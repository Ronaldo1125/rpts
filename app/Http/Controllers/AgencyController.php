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

    public function index_v2(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $agencies = Agency::latest()->paginate($perPage)->onEachSide(1);
        $agencies->appends(['per_page' => $perPage]);
        
        return view('agencies.index_v2', compact('agencies'));
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

    public function store_v2(StoreAgencyRequest $request)
    {
        Agency::create($request->validated());

        toast('Agency data added successfully!', 'success');

        return response()->json([
            'success' => true,
            'message' => 'Agency data added successfully!'
        ]);
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

    public function update_v2(UpdateAgencyRequest $request, Agency $agency)
    {
        $agency->update($request->validated());

        toast('Agency data updated successfully!', 'success');

        return response()->json([
            'success' => true,
            'message' => 'Agency data updated successfully!'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Agency $agency)
    {
        $agency->delete();

        toast('Agency data deleted successfully!', 'success');

        return redirect()->route('agencies.index_v2');
    }

    public function destroy_v2(Agency $agency)
    {
        $agency->delete();

        toast('Agency data deleted successfully!', 'success');

        return response()->json([
            'success' => true,
            'message' => 'Agency data deleted successfully!'
        ]);
    }

    public function validate_v2(Request $request)
    {
        $field = $request->field;
        $value = $request->value;
        $ignoreId = $request->ignore_id;

        $query = Agency::where($field, $value);
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        $exists = $query->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'This ' . str_replace('_', ' ', $field) . ' is already taken.' : ''
        ]);
    }
}
