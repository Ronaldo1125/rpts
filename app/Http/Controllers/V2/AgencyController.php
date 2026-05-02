<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAgencyRequest;
use App\Http\Requests\UpdateAgencyRequest;
use App\Models\Agency;
use Illuminate\Http\Request;

class AgencyController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Agency::class);
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $agencies = Agency::search($search)
            ->latest()
            ->paginate($perPage)
            ->onEachSide(1);

        $agencies->appends(['per_page' => $perPage, 'search' => $search]);

        return view('agencies.index_v2', compact('agencies'));
    }

    public function store(StoreAgencyRequest $request)
    {
        Agency::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Agency data added successfully!'
        ]);
    }

    public function update(UpdateAgencyRequest $request, Agency $agency)
    {
        $agency->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Agency data updated successfully!'
        ]);
    }

    public function destroy(Agency $agency)
    {
        $agency->delete();

        return response()->json([
            'success' => true,
            'message' => 'Agency data deleted successfully!'
        ]);
    }

    public function validateField(Request $request)
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
