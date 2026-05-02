<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreIndicatorRequest;
use App\Models\Indicator;
use Illuminate\Http\Request;

class IndicatorController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Indicator::class);
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $indicators = Indicator::search($search)
            ->latest()
            ->paginate($perPage)
            ->onEachSide(1);

        $indicators->appends(['per_page' => $perPage, 'search' => $search]);
        
        return view('indicators.index_v2', compact('indicators'));
    }

    public function store(StoreIndicatorRequest $request)
    {
        $indicator = Indicator::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Indicator Data Added Successfully!',
            'data' => $indicator
        ]);
    }

    public function update(StoreIndicatorRequest $request, Indicator $indicator)
    {
        $indicator->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Indicator Data Updated Successfully!'
        ]);
    }

    public function destroy(Indicator $indicator)
    {
        $indicator->delete();

        return response()->json([
            'success' => true,
            'message' => 'Indicator Data Deleted Successfully!'
        ]);
    }

    public function validateField(Request $request)
    {
        $field = $request->field;
        $value = $request->value;
        $ignoreId = $request->ignore_id;

        $query = Indicator::where($field, $value);
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
