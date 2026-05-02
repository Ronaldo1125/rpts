<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSectorRequest;
use App\Models\Sector;
use Illuminate\Http\Request;

class SectorController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Sector::class);
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $sectors = Sector::search($search)
            ->latest()
            ->paginate($perPage)
            ->onEachSide(1);

        $sectors->appends(['per_page' => $perPage, 'search' => $search]);

        return view('sectors.index_v2', compact('sectors'));
    }

    public function store(StoreSectorRequest $request)
    {
        Sector::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Sector data added successfully!'
        ]);
    }

    public function update(StoreSectorRequest $request, Sector $sector)
    {
        $sector->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Sector data updated successfully!'
        ]);
    }

    public function destroy(Sector $sector)
    {
        $sector->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sector data deleted successfully!'
        ]);
    }

    public function validateField(Request $request)
    {
        $field = $request->field;
        $value = $request->value;
        $ignoreId = $request->ignore_id;

        $query = Sector::where($field, $value);
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
