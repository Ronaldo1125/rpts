<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:activity_log-view');
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $query = Activity::orderByDesc('id');
        
        if ($search) {
            $query->where('log_name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
        }

        $activities = $query->paginate($perPage)->onEachSide(1);
        $activities->appends(['per_page' => $perPage, 'search' => $search]);

        return view('activity_logs.index_v2', compact('activities'));
    }
}
