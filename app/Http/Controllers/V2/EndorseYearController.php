<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Models\EndorseYear;
use App\Http\Requests\StoreEndorseYearRequest;
use Illuminate\Http\Request;

class EndorseYearController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(EndorseYear::class);
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $endorse_years = EndorseYear::search($search)
            ->latest()
            ->paginate($perPage)
            ->onEachSide(1);

        $endorse_years->appends(['per_page' => $perPage, 'search' => $search]);
        
        return view('endorse_years.index_v2', compact('endorse_years'));
    }

    public function store(StoreEndorseYearRequest $request)
    {
        EndorseYear::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Endorse Year Data Added Successfully!'
        ]);
    }

    public function update(StoreEndorseYearRequest $request, EndorseYear $endorseYear)
    {
        $endorseYear->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Endorse Year Data Updated Successfully!'
        ]);
    }

    public function destroy(EndorseYear $endorseYear)
    {
        $endorseYear->delete();

        return response()->json([
            'success' => true,
            'message' => 'Endorse Year Data Deleted Successfully!'
        ]);
    }

    public function validateField(Request $request)
    {
        $field = $request->field;
        $value = $request->value;
        $ignoreId = $request->ignore_id;

        $query = EndorseYear::where($field, $value);
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
