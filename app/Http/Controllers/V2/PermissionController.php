<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePermissionRequest;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:permission-view')->only(['index']);
        $this->middleware('can:permission-create')->only(['store']);
        $this->middleware('can:permission-update')->only(['update']);
        $this->middleware('can:permission-delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $query = Permission::latest();
        if ($search) {
            $query->where('name', 'LIKE', "%{$search}%");
        }

        $permissions = $query->paginate($perPage)->onEachSide(1);
        $permissions->appends(['per_page' => $perPage, 'search' => $search]);

        return view('permissions.index_v2', compact('permissions'));
    }

    public function store(StorePermissionRequest $request)
    {
        $request->validated();
        $permissionName = $request->name;

        Permission::create([
            'name' => $permissionName 
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permission data added successfully!'
        ]);
    }

    public function update(StorePermissionRequest $request, Permission $permission)
    {
        $permission->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Permission data updated successfully!'
        ]);
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();

        return response()->json([
            'success' => true,
            'message' => 'Permission data deleted successfully!'
        ]);
    }

    public function validateField(Request $request)
    {
        $field = $request->field;
        $value = $request->value;
        $ignoreId = $request->ignore_id;

        $query = Permission::where($field, $value);
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
