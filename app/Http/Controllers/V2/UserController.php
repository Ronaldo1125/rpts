<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Agency;
use App\Models\Profile;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUserRequest;

class UserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class);
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $users = User::search($search)
            ->latest()
            ->paginate($perPage)
            ->onEachSide(1);

        $users->appends(['per_page' => $perPage, 'search' => $search]);

        $agencies = Agency::pluck('agency_name', 'id')->all();
        $roles = Role::pluck('name')->all();
        $divisions = \App\Models\Division::pluck('name', 'id')->all();

        return view('users.index_v2', compact('users', 'agencies', 'roles', 'divisions'));
    }

    public function store(StoreUserRequest $request)
    {
        $request->validated();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'agency_id' => $request->agency_id,
            'division_id' => $request->division_id,
        ]);

        activity('user')
            ->performedOn($user)
            ->withProperties([
                'name' => $request->name,
                'email' => $request->email
            ])
            ->event('created')
            ->log('created');

        Profile::create([
            'user_id' => $user->id,
        ]);

        $user->assignRole($request->input('role'));

        return response()->json([
            'success' => true,
            'message' => 'User and Profile data added successfully!'
        ]);
    }

    public function update(StoreUserRequest $request, User $user)
    {
        $request->validated();

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'agency_id' => $request->agency_id,
            'division_id' => $request->division_id,
        ]);

        activity('user')
            ->performedOn($user)
            ->withProperties([
                'name' => $request->name,
                'email' => $request->email
            ])
            ->event('updated')
            ->log('updated');

        $user->syncRoles($request->input('role'));

        return response()->json([
            'success' => true,
            'message' => 'User data updated successfully!'
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();

        activity('user')
            ->performedOn($user)
            ->withProperties([
                'name' => $user->name,
                'email' => $user->email
            ])
            ->event('deleted')
            ->log('deleted');

        return response()->json([
            'success' => true,
            'message' => 'User data deleted successfully!'
        ]);
    }

    public function validateField(Request $request)
    {
        $field = $request->field;
        $value = $request->value;
        $ignoreId = $request->ignore_id;

        $query = User::where($field, $value);
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
