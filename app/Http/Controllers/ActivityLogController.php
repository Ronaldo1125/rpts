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

    public function index_v2(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $activities = Activity::orderByDesc('id')->paginate($perPage)->onEachSide(1);
        $activities->appends(['per_page' => $perPage]);

        return view('activity_logs.index_v2', compact('activities'));
    }
}
