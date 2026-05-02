<?php

namespace App\Http\Controllers;

use App\Models\EndorseYear;
use App\Http\Requests\StoreEndorseYearRequest;
use App\Http\Requests\UpdateEndorseYearRequest;
use Illuminate\Http\Request;

class EndorseYearController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(EndorseYear::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $endorse_years = EndorseYear::all();

         $title = 'Delete Endorse Year Record!';
        $text = "Are you sure? This will be deleted permanently.";
        confirmDelete($title, $text);

        return view('endorse_years.index', compact('endorse_years'));
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
    public function store(StoreEndorseYearRequest $request)
    {
        EndorseYear::create($request->validated());

        toast('Endorse Year Data Added Successfully!', 'success');

        return redirect()->route('endorse_years.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(EndorseYear $endorseYear)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EndorseYear $endorseYear)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreEndorseYearRequest $request, EndorseYear $endorseYear)
    {
         $endorseYear->update($request->validated());

        toast('Endorse Year Data Updated Successfully!', 'success');

        return redirect()->route('endorse_years.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EndorseYear $endorseYear)
    {
        $endorseYear->delete();

        toast('Endorse Year Data Deleted Successfully!', 'success');

        return redirect()->route('endorse_years.index');
    }


}
