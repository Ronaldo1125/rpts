<?php

namespace App\Http\Controllers;

use App\Models\FundingCategory;
use App\Http\Requests\StoreFundingCategoryRequest;
use App\Http\Requests\UpdateFundingCategoryRequest;

class FundingCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $funding_categories = FundingCategory::all();

        $title = 'Delete Sector Record!';
        $text = "Are you sure? This will be deleted permanently.";
        confirmDelete($title, $text);

        return view('funding_categories.index', compact('funding_categories'));
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
    public function store(StoreFundingCategoryRequest $request)
    {
        FundingCategory::create($request->validated());

        toast('Funding Category Data Added Successfully!', 'success');

        return redirect()->route('funding_categories.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(FundingCategory $fundingCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FundingCategory $fundingCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreFundingCategoryRequest $request, FundingCategory $fundingCategory)
    {
        $fundingCategory->update($request->validated());

        toast('Funding Category Data Updated Successfully!', 'success');

        return redirect()->route('funding_categories.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FundingCategory $fundingCategory)
    {
        $fundingCategory->delete();

        toast('Funding Category data deleted successfully!', 'success');

        return redirect()->route('funding_categories.index');
    }
}
