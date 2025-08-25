<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStatusRequest;
use App\Models\Status;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $statuses = Status::all();

         $title = 'Delete Status Record!';
        $text = "Are you sure? This will be deleted permanently.";
        confirmDelete($title, $text);

        return view('statuses.index', compact('statuses'));
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
    public function store(StoreStatusRequest $request)
    {
        Status::create($request->validated());

       toast('Status data added successfully!','success');

         return redirect()->route('statuses.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Status $status)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Status $status)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreStatusRequest $request, Status $status)
    {
        $status->update($request->validated());

        toast('Status data updated successfully!', 'success');

        return redirect()->route('statuses.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Status $status)
    {
        $status->delete();

        toast('Status data deleted successfully!', 'success');

        return redirect()->route('statuses.index');
    }
}
