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

    public function index_v2()
    {
        $user = auth()->user();

    if ($user->hasRole('administrator')) {
        return redirect()->route('admin.dashboard');
    }

    if ($user->hasRole('staff')) {
        return redirect()->route('staff.dashboard');
    }

    if ($user->hasRole('implementing_agency') || $user->hasRole('agency')) {
        return redirect()->route('agency.dashboard');
    }

    if ($user->hasRole('division_head') || $user->hasRole('chief')) {
        return redirect()->route('chief.dashboard');
    }

    if ($user->hasRole('pdipbd_staff')) {
        return redirect()->route('admin.dashboard');
    }

    return redirect('/');
    }

    public function admin()
    {
        return view('home.dashboards');
    }

    public function agency()
    {
        return view('home.agency-dashboard');
    }

    public function staff()
    {
        return view('home.staff-dashboard');
    }

    public function chief()
    {
        return view('home.division-head-dashboard');
    }
}
