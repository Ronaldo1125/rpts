<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $statusNameCount = [
            'proposed' => 0,
            'terminated' => 0,
            'suspended' => 0
        ];
        $totalProjectCost = Project::sum('funding_requirement');
        $statusGroupCounts = Project::selectRaw('count(status_id) as status_total, status_id')
            ->groupBy('status_id')
            ->get();

        if(!empty($statusGroupCounts)) 
        {
            foreach($statusGroupCounts as $statusGroupCount) {
                if($statusGroupCount->status_id == 1) {
                    $statusNameCount['proposed'] = $statusGroupCount->status_total;
                } elseif($statusGroupCount->status_id == 4) {
                    $statusNameCount['terminated'] = $statusGroupCount->status_total;
                } elseif($statusGroupCount->status_id == 5) {
                    $statusNameCount['suspended'] = $statusGroupCount->status_total;
                }
            }
        }

        //$statusGroupCounts = Project::with('status', 'status_name')->get();

           //dd($statusNameCount);
        
        return view('home', compact('totalProjectCost', 'statusNameCount'));
    }
}
