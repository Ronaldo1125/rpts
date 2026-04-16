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
        // $statusGroupNameCounts = [
        //     'proposed' => 0,
        //     'terminated' => 0,
        //     'suspended' => 0
        // ];
        // $totalProjectCost = Project::sum('funding_requirement');
        // $statusGroupCounts = Project::selectRaw('count(status) as status_total, status')
        //     ->groupBy('status')
        //     ->get();

        //    // dd($statusGroupCounts);

        // if(!empty($statusGroupCounts)) 
        // {
        //     foreach($statusGroupCounts as $statusGroupCount) {
        //         if($statusGroupCount->status == 'proposed') {
        //             $statusGroupNameCounts['proposed'] = $statusGroupCount->status_total;
        //         } elseif($statusGroupCount->status == 'terminated') {
        //             $statusGroupNameCounts['terminated'] = $statusGroupCount->status_total;
        //         } elseif($statusGroupCount->status == 'suspended') {
        //             $statusGroupNameCounts['suspended'] = $statusGroupCount->status_total;
        //         }
        //     }
        // }

        //$statusGroupCounts = Project::with('status', 'status_name')->get();

           //dd($statusNameCount);
        $totalProjectCost = 100.00;
        $statusGroupNameCounts = '';
        
        return view('home', compact('totalProjectCost', 'statusGroupNameCounts'));
        //return view('home');
    }
}
