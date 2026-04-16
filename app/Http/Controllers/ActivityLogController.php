<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index()
    {
        $activities = Activity::orderByDesc('id')->get();

        //dd($activities);

        return view('activity_logs.index', compact('activities'));
    }
}
