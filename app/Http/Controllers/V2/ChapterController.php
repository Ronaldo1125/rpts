<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChapterRequest;
use App\Models\Chapter;
use Illuminate\Http\Request;

class ChapterController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Chapter::class);
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $chapters = Chapter::search($search)
            ->latest()
            ->paginate($perPage)
            ->onEachSide(1);

        $chapters->appends(['per_page' => $perPage, 'search' => $search]);
        
        return view('chapters.index_v2', compact('chapters'));
    }

    public function store(StoreChapterRequest $request)
    {
        Chapter::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'RDP Chapter Data Added Successfully!'
        ]);
    }

    public function update(StoreChapterRequest $request, Chapter $chapter)
    {
        $chapter->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'RDP Chapter Data Updated Successfully!'
        ]);
    }

    public function destroy(Chapter $chapter)
    {
        $chapter->delete();

        return response()->json([
            'success' => true,
            'message' => 'RDP Chapter Data Deleted Successfully!'
        ]);
    }

    public function validateField(Request $request)
    {
        $field = $request->field;
        $value = $request->value;
        $ignoreId = $request->ignore_id;

        $query = Chapter::where($field, $value);
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
