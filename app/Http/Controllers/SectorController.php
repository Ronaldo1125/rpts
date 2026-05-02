<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSectorRequest;
use App\Models\Sector;
use Illuminate\Http\Request;

class SectorController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Sector::class);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sectors = Sector::all();

        $title = 'Delete Sector Record!';
        $text = "Are you sure? This will be deleted permanently.";
        confirmDelete($title, $text);

        return view('sectors.index', compact('sectors'));
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
    public function store(StoreSectorRequest $request)
    {  
        Sector::create($request->validated());

        toast('Sector data added successfully!','success');
        //alert()->success('SuccessAlert','Sector data added successfully!');

        return redirect()->route('sectors.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sector $sector)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sector $sector)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreSectorRequest $request, Sector $sector)
    {
        $sector->update($request->validated());

        toast('Sector data updated successfully!','success');
        //alert()->success('SuccessAlert','Sector data added successfully!');

        return redirect()->route('sectors.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sector $sector)
    {
        $sector->delete();

        toast('Sector data deleted successfully!', 'success');

        return redirect()->route('sectors.index');
    }


}
