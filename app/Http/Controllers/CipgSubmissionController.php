<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CipgSubmissionController extends Controller
{
    
    public function index()
    {
        return view('cipg_submissions.index');
    }
}
