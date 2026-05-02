<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubSectorRequest;
use App\Models\Sector;
use App\Models\SubSector;
use Illuminate\Http\Request;

class SubSectorController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(SubSector::class);
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $sub_sectors = SubSector::search($search)
            ->latest()
            ->paginate($perPage)
            ->onEachSide(1);

        $sub_sectors->appends(['per_page' => $perPage, 'search' => $search]);

        $sectors = Sector::pluck('sector_name', 'id')->all();
        return view('sub_sectors.index_v2', compact('sub_sectors', 'sectors'));
    }

    public function store(StoreSubSectorRequest $request)
    {
        SubSector::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Sub-Sector data added successfully!'
        ]);
    }

    public function update(StoreSubSectorRequest $request, SubSector $subSector)
    {
        $subSector->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Sub-Sector data updated successfully!'
        ]);
    }

    public function destroy(SubSector $subSector)
    {
        $subSector->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sub-Sector data deleted successfully!'
        ]);
    }

    public function validateField(Request $request)
    {
        $field = $request->field;
        $value = $request->value;
        $ignoreId = $request->ignore_id;

        $query = SubSector::where($field, $value);
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
