<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\ProjectAssessmentReport;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('project_assessment_reports', 'par_background')) {
            Schema::table('project_assessment_reports', function (Blueprint $table) {
                $table->text('par_background')->nullable()->after('par_analysis');
                $table->text('par_components')->nullable()->after('par_background');
                $table->text('par_spatial')->nullable()->after('par_components');
                $table->text('par_qualitative')->nullable()->after('par_spatial');
                $table->text('par_recommendations')->nullable()->after('par_qualitative');
            });
        }

        // Migrate existing data
        $reports = ProjectAssessmentReport::all();
        foreach ($reports as $report) {
            // Note: If par_analysis is present as text, we might need to json_decode it
            // if the model cast is not working as expected.
            $analysis = $report->par_analysis;
            if (is_string($analysis)) {
                $analysis = json_decode($analysis, true);
            }
            
            if (is_array($analysis)) {
                $report->update([
                    'par_background' => $analysis['background'] ?? null,
                    'par_components' => $analysis['components'] ?? null,
                    'par_spatial' => $analysis['spatial'] ?? null,
                    'par_qualitative' => $analysis['qualitative'] ?? null,
                    'par_recommendations' => $analysis['recommendations'] ?? null,
                ]);
            }
        }

        if (Schema::hasColumn('project_assessment_reports', 'par_analysis')) {
            Schema::table('project_assessment_reports', function (Blueprint $table) {
                $table->dropColumn('par_analysis');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('project_assessment_reports', 'par_analysis')) {
            Schema::table('project_assessment_reports', function (Blueprint $table) {
                $table->text('par_analysis')->nullable()->after('readiness_level');
            });
        }

        $reports = ProjectAssessmentReport::all();
        foreach ($reports as $report) {
            $report->update([
                'par_analysis' => [
                    'background' => $report->par_background,
                    'components' => $report->par_components,
                    'spatial' => $report->par_spatial,
                    'qualitative' => $report->par_qualitative,
                    'recommendations' => $report->par_recommendations,
                ]
            ]);
        }

        Schema::table('project_assessment_reports', function (Blueprint $table) {
            $table->dropColumn(['par_background', 'par_components', 'par_spatial', 'par_qualitative', 'par_recommendations']);
        });
    }
};
