<?php

namespace App\Http\Controllers;

use Pdf;
use App\Models\Status;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Models\FundingCategory;
use App\Exports\ProjectDataExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index()
    {
        $selectedFundingCategoryId = "";
        $selectedStatusId = "";

        $projects = Project::all();
        $funding_categories = FundingCategory::pluck('category_name', 'id')->all();
        $statuses = Status::pluck('status_name', 'id')->all();

        return view('reports.index', compact('projects', 'funding_categories', 'statuses', 'selectedFundingCategoryId', 'selectedStatusId'));
    }


    public function searchReport(Request $request) {

        $selectedFundingCategoryId = "";
        $selectedStatusId = "";
        //$projects = Project::all();
        $funding_categories = FundingCategory::pluck('category_name', 'id')->all();
        $statuses = Status::pluck('status_name', 'id')->all();

        $query = Project::query();

        if($request->funding_category_id != "") {
            
            $selectedFundingCategoryId = $request->funding_category_id;
            $query->where('funding_category_id', $selectedFundingCategoryId); 
        }

        if($request->status_id != "") 
        {    
            $selectedStatusId = $request->status_id;
            $query->where('status_id', $selectedStatusId); 
        }

        $projects = $query->get();

        //return redirect()->route('reports.index', compact('projects'));
        return view('reports.index', compact('projects', 'funding_categories', 'statuses', 'selectedFundingCategoryId', 'selectedStatusId'));
    }

    public function generatePdf(Request $request) {
        //dd($request->funding_category_id);

        $query = Project::query();

        if($request->funding_category_id != null) 
        {
            $query->where('funding_category_id', $request->funding_category_id); 
        }

        if($request->status_id != null) 
        {    
            $query->where('status_id', $request->status_id); 
        }

        $projects = $query->get();

        $pdf = Pdf::loadView('reports.pdfReport', ['projects' => $projects]);
        
        return $pdf->setPaper('legal', 'landscape')->setWarnings(false)->download( time(). '_' . 'pdfReport.pdf');
    }

    public function generateExcel(Request $request)
    {

        return Excel::download(new ProjectDataExport($request->funding_category_id, $request->status_id), time() . '_' . 'excelReport.xlsx');
    }
}
