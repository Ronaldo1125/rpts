<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Requests\StoreRoleRequest;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:role-view')->only(['index']);
        $this->middleware('can:role-create')->only(['store']);
        $this->middleware('can:role-update')->only(['update']);
        $this->middleware('can:role-delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $query = Role::latest();
        if ($search) {
            $query->where('name', 'LIKE', "%{$search}%");
        }

        $roles = $query->paginate($perPage)->onEachSide(1);
        $roles->appends(['per_page' => $perPage, 'search' => $search]);

        $permissions = Permission::pluck('name')->all();

        return view('roles.index_v2', compact('roles', 'permissions'));
    }

    public function store(StoreRoleRequest $request)
    {
        $request->validated();

        $role = Role::create(['name' => $request->input('name') ]);
        $role->syncPermissions($request->input('permission'));

        return response()->json([
            'success' => true,
            'message' => 'Role data added successfully!'
        ]);
    }

    public function update(StoreRoleRequest $request, Role $role)
    {
        $role->update($request->validated());
        $role->syncPermissions($request->input('permission'));

        return response()->json([
            'success' => true,
            'message' => 'Role data updated successfully!'
        ]);
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Role data deleted successfully!'
        ]);
    }

    public function validateField(Request $request)
    {
        $field = $request->field;
        $value = $request->value;
        $ignoreId = $request->ignore_id;

        $query = Role::where($field, $value);
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        $exists = $query->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'This ' . str_replace('_', ' ', $field) . ' is already taken.' : ''
        ]);
    }
}
