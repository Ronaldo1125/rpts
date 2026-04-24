<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePermissionRequest;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permissions = Permission::all();

        //dd($permissions);

        $title = 'Delete Permission Record!';
        $text = "Are you sure? This will be deleted permanently.";
        confirmDelete($title, $text);
        
        

        return view('permissions.index', compact('permissions'));
    }

    public function index_v2(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $permissions = Permission::latest()->paginate($perPage)->onEachSide(1);
        $permissions->appends(['per_page' => $perPage]);

        return view('permissions.index_v2', compact('permissions'));
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
    public function store(StorePermissionRequest $request)
    {
        $request->validated();
        $permissionName = $request->name;

        Permission::create([
            'name' => $permissionName 
        ]);

        toast('Permission data added successfully!','success');

        return redirect()->route('permissions.index');
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
    public function update(StorePermissionRequest $request, Permission $permission)
    {
        $permission->update($request->validated());

        toast('Permission data updated successfully!','success');
        
        return redirect()->route('permissions.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();

        toast('Permission data deleted successfully!', 'success');

        return redirect()->route('permissions.index');
    }
}
