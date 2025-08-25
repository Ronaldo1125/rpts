<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Requests\StoreRoleRequest;
use Spatie\Permission\Models\Permission;


class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::all();
        $permissions = Permission::pluck('name')->all();

        $title = 'Delete Role Record!';
        $text = "Are you sure? This will be deleted permanently.";
        confirmDelete($title, $text);

        return view('roles.index', compact('roles','permissions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request)
    {
        $request->validated();

        //dd($request->input('permission'));

        $role = Role::create(['name' => $request->input('name') ]);
        $role->syncPermissions($request->input('permission'));

        toast('Role data added successfully!','success');

        return redirect()->route('roles.index'); 
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
    public function update(StoreRoleRequest $request, Role $role)
    {

        $role->update($request->validated());
        $role->syncPermissions($request->input('permission'));

        toast('Role data updated successfully!','success');

        return redirect()->route('roles.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        $role->delete();

        toast('Role data deleted successfully!', 'success');

        return redirect()->route('roles.index');
    }
}
