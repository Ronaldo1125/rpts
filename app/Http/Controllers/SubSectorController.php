<?php

namespace App\Http\Controllers;

use App\Models\Sector;
use App\Models\SubSector;
use App\Http\Requests\StoreSubSectorRequest;
use App\Http\Requests\UpdateSubSectorRequest;
use Illuminate\Http\Request;

class SubSectorController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(SubSector::class);
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sub_sectors = SubSector::all();
        $sectors = Sector::pluck('sector_name', 'id')->all();

        $title = 'Delete Sector Record!';
        $text = "Are you sure? This will be deleted permanently.";
        confirmDelete($title, $text);

        return view('sub_sectors.index', compact('sub_sectors', 'sectors'));
    }

    public function index_v2(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $sub_sectors = SubSector::latest()->paginate($perPage)->onEachSide(1);
        $sub_sectors->appends(['per_page' => $perPage]);

        $sectors = Sector::pluck('sector_name', 'id')->all();
        return view('sub_sectors.index_v2', compact('sub_sectors', 'sectors'));
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
    public function store(StoreSubSectorRequest $request)
    {
        SubSector::create($request->validated());

        toast('Sub-Sector Data Added Successfully!', 'success');

        return redirect()->route('sub_sectors.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(SubSector $subSector)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SubSector $subSector)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreSubSectorRequest $request, SubSector $subSector)
    {
          $subSector->update($request->validated());

        toast('Sub-Sector Data Updated Successfully!', 'success');

        return redirect()->route('sub_sectors.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubSector $subSector)
    {
        $subSector->delete();

        toast('Sub-Sector data deleted successfully!', 'success');

        return redirect()->route('sub_sectors.index');
    }
}
