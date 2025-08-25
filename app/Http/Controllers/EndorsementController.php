<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEndorsementRequest;
use App\Models\Endorsement;
use Illuminate\Http\Request;

class EndorsementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $endorsements = Endorsement::all();

        $title = 'Delete Endorsement Record!';
        $text = "Are you sure? This will be deleted permanently.";
        confirmDelete($title, $text);

        return view('endorsements.index', compact('endorsements'));
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
    public function store(StoreEndorsementRequest $request)
    {
        Endorsement::create($request->validated());

        toast('Endorsement Data Added Successfully!', 'success');

        return redirect()->route('endorsements.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Endorsement $endorsement)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Endorsement $endorsement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreEndorsementRequest $request, Endorsement $endorsement)
    {
        $endorsement->update($request->validated());

        toast('Endorsement Data Updated Successfully!','success');
        
        return redirect()->route('endorsements.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Endorsement $endorsement)
    {
        //
    }
}
