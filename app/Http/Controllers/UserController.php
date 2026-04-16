<?php

namespace App\Http\Controllers;

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

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        $agencies = Agency::pluck('agency_name', 'id')->all();
        $roles = Role::pluck('name')->all();

        $title = 'Delete Role Record!';
        $text = "Are you sure? This will be deleted permanently.";
        confirmDelete($title, $text);
        
        return view('users.index', compact('users', 'agencies', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $request->validated();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'agency_id' => $request->agency_id,
        ]);


        activity('user')
        ->performedOn($user)
        ->withProperties([
            'name' => $request->name,
            'email' => $request->email
            ])
        ->event('created')
        ->log('created');

        $profile = Profile::create([
            'user_id' => $user->id,
        ]);

        $user->assignRole($request->input('role'));

        toast('User and Profile data added successfully!','success');

         return redirect()->route('users.index');


        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreUserRequest $request, User $user)
    {
        $request->validated();

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'agency_id' => $request->agency_id,
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

        toast('User data updated successfully!','success');

         return redirect()->route('users.index');
    }

    /**
     * Remove the specified resource from storage.
     */
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

        toast('User data deleted successfully!', 'success');

        return redirect()->route('users.index');
    }
}
