<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AboutController extends Controller
{
    
    public function index()
    {
        return view('about.index');
    }

    public function index_v2()
    {
        return view('about.index_v2');
    }
}
