<?php

namespace App\Http\Controllers;

use App\Models\Indicator;
use App\Http\Requests\StoreIndicatorRequest;
use App\Http\Requests\UpdateIndicatorRequest;

class IndicatorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       $indicators = Indicator::all();
      
        $title = 'Delete Indicator Record!';
        $text = "Are you sure? This will be deleted permanently.";
        confirmDelete($title, $text);

        return view('indicators.index', compact('indicators'));
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
    public function store(StoreIndicatorRequest $request)
    {
        Indicator::create($request->validated());

        toast('Indicator Data Added Successfully!', 'success');

        return redirect()->route('indicators.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Indicator $indicator)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Indicator $indicator)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreIndicatorRequest $request, Indicator $indicator)
    {
        $indicator->update($request->validated());

        toast('Indicator Data Updated Successfully!', 'success');

        return redirect()->route('indicators.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Indicator $indicator)
    {
        $indicator->delete();

        toast('Indicator Data Deleted Successfully!', 'success');

        return redirect()->route('indicators.index');
    }
}
