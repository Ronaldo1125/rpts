<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChapterRequest;
use App\Models\Chapter;
use Illuminate\Http\Request;

class ChapterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $chapters = Chapter::all();

        $title = 'Delete RDP Chapter Record!';
        $text = "Are you sure? This will be deleted permanently.";
        confirmDelete($title, $text);

        return view('chapters.index', compact('chapters'));
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
    public function store(StoreChapterRequest $request)
    {
        Chapter::create($request->validated());

        toast('RDP Chapter Data Added Successfully!', 'success');

        return redirect()->route('chapters.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Chapter $chapter)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Chapter $chapter)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreChapterRequest $request, Chapter $chapter)
    {
        $chapter->update($request->validated());

        toast('RDP Chapter Data Updated Successfully!', 'success');

        return redirect()->route('chapters.index');
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Chapter $chapter)
    {
        $chapter->delete();

        toast('RDP Chapter data deleted successfully!', 'success');

        return redirect()->route('chapters.index');
    }
}
