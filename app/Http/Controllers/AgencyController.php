<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAgencyRequest;
use App\Http\Requests\UpdateAgencyRequest;
use App\Models\Agency;
use App\Models\Sector;
use Illuminate\Http\Request;

class AgencyController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Agency::class);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $agencies = Agency::all();

        $title = 'Delete Agency Record!';
        $text = "Are you sure? This will be deleted permanently.";
        confirmDelete($title, $text);
        
        return view('agencies.index', compact('agencies'));
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
    public function store(StoreAgencyRequest $request)
    {
        Agency::create($request->validated());

         toast('Agency data added successfully!','success');

         return redirect()->route('agencies.index');
    }



    /**
     * Display the specified resource.
     */
    public function show(Agency $agency)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Agency $agency)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreAgencyRequest $request, Agency $agency)
    {
   
        $agency->update($request->validated());

          toast('Agency data updated successfully!','success');

         return redirect()->route('agencies.index');


    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Agency $agency)
    {
        $agency->delete();

        toast('Agency data deleted successfully!', 'success');

        return redirect()->route('agencies.index');
    }




}
