<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class CipgGuideController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('can:cipg_submission-view');
    }
    
    public function index()
    {
        return view('cipg_guide.index');
    }
}
