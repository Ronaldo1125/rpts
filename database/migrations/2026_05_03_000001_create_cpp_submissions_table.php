<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─────────────────────────────────────────────────────────────
        // Drop any stale tables from previous runs
        // ─────────────────────────────────────────────────────────────
        Schema::dropIfExists('cpp_sdg_alignments');
        Schema::dropIfExists('cpp_rdp_alignments');
        Schema::dropIfExists('cpp_impl_schedule');
        Schema::dropIfExists('cpp_consultation_dates');
        Schema::dropIfExists('cpp_locations');
        Schema::dropIfExists('cpp_logframes');
        Schema::dropIfExists('cpp_endorsements');
        Schema::dropIfExists('cpp_benefits_costs');
        Schema::dropIfExists('cpp_submission_revisions');
        Schema::dropIfExists('cpp_submissions');
        Schema::dropIfExists('rdp_chapters'); // replaced by the existing 'chapters' table

        // ─────────────────────────────────────────────────────────────
        // 1. Core Submission Table
        // ─────────────────────────────────────────────────────────────
        Schema::create('cpp_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('agency_id')->constrained()->onDelete('restrict');
            $table->foreignId('sector_id')->nullable()->constrained('sectors')->onDelete('set null');
            $table->foreignId('sub_sector_id')->nullable()->constrained('sub_sectors')->onDelete('set null');

            $table->text('project_title');
            $table->json('project_type')->nullable();
            $table->text('components')->nullable();
            $table->enum('project_coverage', ['Regionwide', 'Inter-Province', 'Location-Specific'])->nullable();

            // Geo-coordinates live directly on the submission (not a child table)
            $table->decimal('geo_start_lat', 10, 8)->nullable();
            $table->decimal('geo_start_lng', 11, 8)->nullable();
            $table->decimal('geo_end_lat', 10, 8)->nullable();
            $table->decimal('geo_end_lng', 11, 8)->nullable();

            $table->enum('project_status', ['Ongoing', 'Pipeline', 'Proposed'])->nullable();
            $table->json('prep_status')->nullable();

            $table->text('background')->nullable();
            $table->text('goal')->nullable();
            $table->text('purpose')->nullable();
            $table->text('outputs')->nullable();
            $table->text('activities')->nullable();
            $table->text('linkages')->nullable();

            $table->decimal('total_cost', 18, 2)->nullable();
            $table->text('funding_source')->nullable();
            $table->string('counterpart_funding', 500)->nullable();

            $table->text('agencies_involved')->nullable();
            $table->text('impl_arrangement')->nullable();
            $table->text('env_clearance_desc')->nullable();
            $table->text('social_accept')->nullable();
            $table->enum('consultation_status', ['Yes', 'No'])->nullable();
            $table->date('consult_planned_date')->nullable();
            $table->text('consult_highlights')->nullable();
            $table->text('hgdg')->nullable();

            // Prepared / Noted signatories
            $table->string('prepared_by_name')->nullable();
            $table->string('prepared_by_position')->nullable();
            $table->date('prepared_date')->nullable();
            $table->string('noted_by_name')->nullable();
            $table->string('noted_by_position')->nullable();
            $table->date('noted_date')->nullable();

            // Workflow
            $table->string('status', 50)->default('Draft')->index();
            $table->string('stage', 50)->default('Submission');
            $table->timestamp('submitted_at')->nullable();

            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('set null');
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['agency_id', 'status']);
            $table->index('project_title');
        });

        // ─────────────────────────────────────────────────────────────
        // 2. Locations  (province / district / municipality / barangay)
        // ─────────────────────────────────────────────────────────────
        Schema::create('cpp_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cpp_submission_id')->constrained('cpp_submissions')->onDelete('cascade');
            $table->foreignId('province_id')->nullable()->constrained('provinces')->onDelete('set null');
            $table->foreignId('district_id')->nullable()->constrained('districts')->onDelete('set null');
            $table->foreignId('municipality_id')->nullable()->constrained('municipalities')->onDelete('set null');
            $table->foreignId('barangay_id')->nullable()->constrained('barangays')->onDelete('set null');
        });

        // ─────────────────────────────────────────────────────────────
        // 3. Logical Framework
        // ─────────────────────────────────────────────────────────────
        Schema::create('cpp_logframes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cpp_submission_id')->constrained('cpp_submissions')->onDelete('cascade');
            $table->text('lf_goal_narrative')->nullable();
            $table->text('lf_goal_indicators')->nullable();
            $table->text('lf_goal_verification')->nullable();
            $table->text('lf_goal_assumptions')->nullable();
            $table->text('lf_purpose_narrative')->nullable();
            $table->text('lf_purpose_indicators')->nullable();
            $table->text('lf_purpose_verification')->nullable();
            $table->text('lf_purpose_assumptions')->nullable();
            $table->text('lf_outputs_narrative')->nullable();
            $table->text('lf_outputs_indicators')->nullable();
            $table->text('lf_outputs_verification')->nullable();
            $table->text('lf_outputs_assumptions')->nullable();
            $table->text('lf_inputs_narrative')->nullable();
            $table->text('lf_inputs_indicators')->nullable();
            $table->text('lf_inputs_verification')->nullable();
            $table->text('lf_inputs_assumptions')->nullable();
        });

        // ─────────────────────────────────────────────────────────────
        // 4. Endorsements
        // ─────────────────────────────────────────────────────────────
        Schema::create('cpp_endorsements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cpp_submission_id')->constrained('cpp_submissions')->onDelete('cascade');
            $table->string('sp_resolution_no')->nullable();
            $table->date('sp_resolution_date')->nullable();
            $table->string('sb_resolution_no')->nullable();
            $table->date('sb_resolution_date')->nullable();
            $table->string('letter_request_ref')->nullable();
            $table->date('letter_transmittal_date')->nullable();
            $table->string('bor_bot_resolution_no')->nullable();
            $table->date('bor_bot_resolution_date')->nullable();
        });

        // ─────────────────────────────────────────────────────────────
        // 5. Benefits & Costs
        // ─────────────────────────────────────────────────────────────
        Schema::create('cpp_benefits_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cpp_submission_id')->constrained('cpp_submissions')->onDelete('cascade');
            $table->text('beneficiaries')->nullable();
            $table->text('social_benefits')->nullable();
            $table->text('economic_benefits')->nullable();
            $table->text('social_costs')->nullable();
            $table->text('economic_costs')->nullable();
        });

        // ─────────────────────────────────────────────────────────────
        // 6. Implementation Schedule
        // ─────────────────────────────────────────────────────────────
        Schema::create('cpp_impl_schedule', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cpp_submission_id')->constrained('cpp_submissions')->onDelete('cascade');
            $table->string('year', 10);
            $table->text('physical_target');
            $table->string('indicator', 500);
            $table->decimal('amount', 18, 2)->default(0);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // ─────────────────────────────────────────────────────────────
        // 7. Consultation Dates
        // ─────────────────────────────────────────────────────────────
        Schema::create('cpp_consultation_dates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cpp_submission_id')->constrained('cpp_submissions')->onDelete('cascade');
            $table->date('consultation_date');
        });

        // ─────────────────────────────────────────────────────────────
        // 8. Revision Snapshots
        // ─────────────────────────────────────────────────────────────
        Schema::create('cpp_submission_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cpp_submission_id')->constrained('cpp_submissions')->onDelete('cascade');
            $table->unsignedInteger('version');
            $table->string('revision_notes', 500)->nullable();
            $table->foreignId('revised_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('snapshot_data');
            $table->timestamps();

            $table->unique(['cpp_submission_id', 'version'], 'uq_cpp_revision_version');
        });

        // ─────────────────────────────────────────────────────────────
        // 9. SDG Goals reference table + pivot
        // ─────────────────────────────────────────────────────────────
        if (!Schema::hasTable('sdg_goals')) {
            Schema::create('sdg_goals', function (Blueprint $table) {
                $table->tinyIncrements('id');
                $table->unsignedTinyInteger('number')->unique();
                $table->string('title');
                $table->string('label');
            });

            DB::table('sdg_goals')->insert([
                ['number' => 1,  'title' => 'No Poverty',                                        'label' => '1: No Poverty'],
                ['number' => 2,  'title' => 'Zero Hunger',                                       'label' => '2: Zero Hunger'],
                ['number' => 3,  'title' => 'Good Health and Well-Being',                         'label' => '3: Good Health and Well-Being'],
                ['number' => 4,  'title' => 'Quality Education',                                  'label' => '4: Quality Education'],
                ['number' => 5,  'title' => 'Gender Equality',                                    'label' => '5: Gender Equality'],
                ['number' => 6,  'title' => 'Clean Water and Sanitation',                         'label' => '6: Clean Water and Sanitation'],
                ['number' => 7,  'title' => 'Affordable and Clean Energy',                        'label' => '7: Affordable and Clean Energy'],
                ['number' => 8,  'title' => 'Decent Work and Economic Growth',                    'label' => '8: Decent Work and Economic Growth'],
                ['number' => 9,  'title' => 'Industry, Innovation, and Infrastructure',           'label' => '9: Industry, Innovation, and Infrastructure'],
                ['number' => 10, 'title' => 'Reduced Inequality',                                 'label' => '10: Reduced Inequality'],
                ['number' => 11, 'title' => 'Sustainable Cities and Communities',                 'label' => '11: Sustainable Cities and Communities'],
                ['number' => 12, 'title' => 'Responsible Consumption and Production',             'label' => '12: Responsible Consumption and Production'],
                ['number' => 13, 'title' => 'Climate Action',                                     'label' => '13: Climate Action'],
                ['number' => 14, 'title' => 'Life Below Water',                                   'label' => '14: Life Below Water'],
                ['number' => 15, 'title' => 'Life on Land',                                       'label' => '15: Life on Land'],
                ['number' => 16, 'title' => 'Peace, Justice, and Strong Institutions',            'label' => '16: Peace, Justice, and Strong Institutions'],
                ['number' => 17, 'title' => 'Partnerships for the Goals',                         'label' => '17: Partnerships for the Goals'],
            ]);
        }

        Schema::create('cpp_sdg_alignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cpp_submission_id')->constrained('cpp_submissions')->onDelete('cascade');
            $table->unsignedTinyInteger('sdg_goal_id');
            $table->foreign('sdg_goal_id')->references('id')->on('sdg_goals')->onDelete('cascade');
            $table->unique(['cpp_submission_id', 'sdg_goal_id'], 'uq_cpp_sdg');
        });

        // ─────────────────────────────────────────────────────────────
        // 10. RDP alignment pivot — uses the existing 'chapters' table
        // ─────────────────────────────────────────────────────────────
        Schema::create('cpp_rdp_alignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cpp_submission_id')->constrained('cpp_submissions')->onDelete('cascade');
            $table->unsignedBigInteger('chapter_id');
            $table->foreign('chapter_id')->references('id')->on('chapters')->onDelete('cascade');
            $table->unique(['cpp_submission_id', 'chapter_id'], 'uq_cpp_chapter');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cpp_sdg_alignments');
        Schema::dropIfExists('cpp_rdp_alignments');
        Schema::dropIfExists('cpp_submission_revisions');
        Schema::dropIfExists('cpp_consultation_dates');
        Schema::dropIfExists('cpp_impl_schedule');
        Schema::dropIfExists('cpp_benefits_costs');
        Schema::dropIfExists('cpp_endorsements');
        Schema::dropIfExists('cpp_logframes');
        Schema::dropIfExists('cpp_locations');
        Schema::dropIfExists('cpp_submissions');
    }
};
