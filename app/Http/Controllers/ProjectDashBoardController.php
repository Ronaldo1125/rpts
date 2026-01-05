<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProjectDashBoardController extends Controller
{
    public function index()
    {

        return view('projectDashboard.index');
    }
}
