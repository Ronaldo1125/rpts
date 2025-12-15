<?php

namespace App\Exports;

use App\Models\Project;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
//use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ProjectDataExport implements FromView, ShouldAutoSize, WithStyles, WithColumnWidths, WithEvents
{
    private $selectedFundingCategoryId;
    private $selectedStatusId;

    public function __construct($selectedFundingCategoryId, $selectedStatusId)
    {
        $this->selectedFundingCategoryId = $selectedFundingCategoryId;
        $this->selectedStatusId = $selectedStatusId;

    }
   
    public function view(): View
    {
        $query = Project::query();

        if($this->selectedFundingCategoryId != null) 
        {
            $query->where('funding_category_id', $this->selectedFundingCategoryId); 
        }

        if($this->selectedStatusId != null) 
        {    
            $query->where('status_id', $this->selectedStatusId); 
        }

        $projects = $query->get();

        //dd($projects);

        return view('reports.excelReport',['projects' => $projects]);
    }

   public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A:V')->getAlignment()->setWrapText(true);

        return [
            // Center align the entire first row horizontally and vertically
            '1:2' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],

            // Right align a specific cell (e.g., B2)
            // 'B2' => [
            //     'alignment' => [
            //         'horizontal' => Alignment::HORIZONTAL_RIGHT,
            //     ],
            // ],

            // Left align an entire column (e.g., C)
            // 'C' => [
            //     'alignment' => [
            //         'horizontal' => Alignment::HORIZONTAL_LEFT,
            //     ],
            // ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 45,
            'B' => 55,            
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Get the highest row and column to dynamically set range
                $highestRow = $event->sheet->getHighestRow();
                $highestColumn = $event->sheet->getHighestColumn();
                $range = 'A1:' . $highestColumn . $highestRow;

                // Apply all borders to the entire used range
                $event->sheet->getStyle($range)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000'], // Black color
                        ],
                    ],
                ]);

                // Example for just headers (Row 1)
                // $event->sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([ ... ]);
            },
        ];
    }
}
