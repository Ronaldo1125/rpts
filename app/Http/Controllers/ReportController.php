<?php

namespace App\Http\Controllers;

use Pdf;
use App\ProjectStatus;
use App\Models\Project;
use Illuminate\Http\Request;
use App\ProjectFundingCategory;
use App\Exports\ProjectDataExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index()
    {
        $selectedFundingCategory = "";
        $selectedStatus = "";

        $projects = Project::all();
        $funding_categories = ProjectFundingCategory::cases();
        $statuses = ProjectStatus::cases();

        return view('reports.index', compact('projects', 'funding_categories', 'statuses', 'selectedFundingCategory', 'selectedStatus'));
    }


    public function searchReport(Request $request) {

        $selectedFundingCategory = "";
        $selectedStatus = "";
        //$projects = Project::all();
        $funding_categories = ProjectFundingCategory::cases();
        $statuses = ProjectStatus::cases();

        $query = Project::query();

        if($request->funding_category != "") {
            
            $selectedFundingCategory = $request->funding_category;
            $query->where('funding_category', $selectedFundingCategory); 
        }

        if($request->status != "") 
        {    
            $selectedStatus = $request->status;
            $query->where('status', $selectedStatus); 
        }

        $projects = $query->get();

        //return redirect()->route('reports.index', compact('projects'));
        return view('reports.index', compact('projects', 'funding_categories', 'statuses', 'selectedFundingCategory', 'selectedStatus'));
    }

    public function generatePdf(Request $request) {
        //dd($request->funding_category_id);

        $query = Project::query();

        if($request->funding_category != null) 
        {
            $query->where('funding_category', $request->funding_category); 
        }

        if($request->status != null) 
        {    
            $query->where('status', $request->status); 
        }

        $projects = $query->get();

        $pdf = Pdf::loadView('reports.pdfReport', ['projects' => $projects]);
        
        return $pdf->setPaper('legal', 'landscape')->setWarnings(false)->download( time(). '_' . 'pdfReport.pdf');
    }

    public function generateExcel(Request $request)
    {

        return Excel::download(new ProjectDataExport($request->funding_category, $request->status), time() . '_' . 'excelReport.xlsx');
    }
}
