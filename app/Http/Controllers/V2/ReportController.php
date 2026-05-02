<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;

use Pdf;
use App\ProjectStatus;
use App\Models\Project;
use Illuminate\Http\Request;
use App\ProjectFundingCategory;
use App\Exports\ProjectDataExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:report-view');
    }

    public function index(Request $request)
    {
        $selectedFundingCategory = $request->funding_category ?? "";
        $selectedStatus = $request->status ?? "";
        $search = $request->search ?? "";
        $perPage = $request->per_page ?? 10;

        $query = Project::with(['project_cost_target', 'project_indicator.indicator', 'agency']);

        if($selectedFundingCategory != "") {
            $query->where('funding_category', $selectedFundingCategory); 
        }

        if($selectedStatus != "") {    
            $query->where('status', $selectedStatus); 
        }

        if($search != "") {
            $query->where('project_title', 'like', "%{$search}%");
        }

        $projects = $query->latest()->paginate($perPage)->onEachSide(1)->withQueryString();
            
        $funding_categories = ProjectFundingCategory::cases();
        $statuses = ProjectStatus::cases();

        return view('reports.index_v2', compact('projects', 'funding_categories', 'statuses', 'selectedFundingCategory', 'selectedStatus'));
    }

    public function searchReport(Request $request) {
        return $this->index($request);
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
