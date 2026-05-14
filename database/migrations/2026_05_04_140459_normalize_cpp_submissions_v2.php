<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 0. Drop existing tables to start fresh
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
        
        // 1. Core Submission Table
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
            
            // Prepared/Noted (Moved back)
            $table->string('prepared_by_name')->nullable();
            $table->string('prepared_by_position')->nullable();
            $table->date('prepared_date')->nullable();
            $table->string('noted_by_name')->nullable();
            $table->string('noted_by_position')->nullable();
            $table->date('noted_date')->nullable();

            $table->string('status', 50)->default('Draft');
            $table->string('stage', 50)->nullable();
            $table->timestamp('submitted_at')->nullable();
            
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('set null');
            $table->timestamps();
        });

        // 2. Locations
        Schema::create('cpp_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cpp_submission_id')->constrained('cpp_submissions')->onDelete('cascade');
            $table->foreignId('province_id')->nullable()->constrained('provinces')->onDelete('set null');
            $table->foreignId('district_id')->nullable()->constrained('districts')->onDelete('set null');
            $table->foreignId('municipality_id')->nullable()->constrained('municipalities')->onDelete('set null');
            $table->foreignId('barangay_id')->nullable()->constrained('barangays')->onDelete('set null');
            $table->decimal('geo_start_lat', 10, 6)->nullable();
            $table->decimal('geo_start_lng', 10, 6)->nullable();
            $table->decimal('geo_end_lat', 10, 6)->nullable();
            $table->decimal('geo_end_lng', 10, 6)->nullable();
        });

        // 3. Logframes
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

        // 4. Endorsements
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

        // 5. Benefits & Costs
        Schema::create('cpp_benefits_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cpp_submission_id')->constrained('cpp_submissions')->onDelete('cascade');
            $table->text('beneficiaries')->nullable();
            $table->text('social_benefits')->nullable();
            $table->text('economic_benefits')->nullable();
            $table->text('social_costs')->nullable();
            $table->text('economic_costs')->nullable();
        });

        // 6. Implementation Schedule
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

        // 7. Consultation Dates
        Schema::create('cpp_consultation_dates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cpp_submission_id')->constrained('cpp_submissions')->onDelete('cascade');
            $table->date('consultation_date');
        });

        // 8. Pivot Tables (SDG/RDP)
        if (!Schema::hasTable('sdg_goals')) {
            Schema::create('sdg_goals', function (Blueprint $table) {
                $table->tinyIncrements('id');
                $table->unsignedTinyInteger('number')->unique();
                $table->string('title');
                $table->string('label');
            });
            // Seed SDGs
            DB::table('sdg_goals')->insert([
                ['number' => 1, 'title' => 'No Poverty', 'label' => '1: No Poverty'],
                ['number' => 2, 'title' => 'Zero Hunger', 'label' => '2: Zero Hunger'],
                ['number' => 3, 'title' => 'Good Health and Well-Being', 'label' => '3: Good Health and Well-Being'],
                ['number' => 4, 'title' => 'Quality Education', 'label' => '4: Quality Education'],
                ['number' => 5, 'title' => 'Gender Equality', 'label' => '5: Gender Equality'],
                ['number' => 6, 'title' => 'Clean Water and Sanitation', 'label' => '6: Clean Water and Sanitation'],
                ['number' => 7, 'title' => 'Affordable and Clean Energy', 'label' => '7: Affordable and Clean Energy'],
                ['number' => 8, 'title' => 'Decent Work and Economic Growth', 'label' => '8: Decent Work and Economic Growth'],
                ['number' => 9, 'title' => 'Industry, Innovation, and Infrastructure', 'label' => '9: Industry, Innovation, and Infrastructure'],
                ['number' => 10, 'title' => 'Reduced Inequality', 'label' => '10: Reduced Inequality'],
                ['number' => 11, 'title' => 'Sustainable Cities and Communities', 'label' => '11: Sustainable Cities and Communities'],
                ['number' => 12, 'title' => 'Responsible Consumption and Production', 'label' => '12: Responsible Consumption and Production'],
                ['number' => 13, 'title' => 'Climate Action', 'label' => '13: Climate Action'],
                ['number' => 14, 'title' => 'Life Below Water', 'label' => '14: Life Below Water'],
                ['number' => 15, 'title' => 'Life on Land', 'label' => '15: Life on Land'],
                ['number' => 16, 'title' => 'Peace, Justice, and Strong Institutions', 'label' => '16: Peace, Justice, and Strong Institutions'],
                ['number' => 17, 'title' => 'Partnerships for the Goals', 'label' => '17: Partnerships for the Goals'],
            ]);
        }

        if (!Schema::hasTable('rdp_chapters')) {
            Schema::create('rdp_chapters', function (Blueprint $table) {
                $table->tinyIncrements('id');
                $table->unsignedTinyInteger('number')->unique();
                $table->string('title');
                $table->string('label', 500);
            });
            // Seed RDPs
            DB::table('rdp_chapters')->insert([
                ['number' => 4, 'title' => 'Promote Human and Social Development', 'label' => 'Chapter 4: Promote Human and Social Development'],
                ['number' => 5, 'title' => 'Reduce Vulnerabilities and Protect Purchasing Power', 'label' => 'Chapter 5: Reduce Vulnerabilities and Protect Purchasing Power'],
                ['number' => 6, 'title' => 'Increase Income-Earning Ability', 'label' => 'Chapter 6: Increase Income-Earning Ability'],
                ['number' => 7, 'title' => 'Modernize Agri-Fishery and Agribusiness', 'label' => 'Chapter 7: Modernize Agri-Fishery and Agribusiness'],
                ['number' => 8, 'title' => 'Revitalize Industry', 'label' => 'Chapter 8: Revitalize Industry'],
                ['number' => 9, 'title' => 'Reinvigorate Services', 'label' => 'Chapter 9: Reinvigorate Services'],
                ['number' => 10, 'title' => 'Advance Research and Development, Technology, and Innovation', 'label' => 'Chapter 10: Advance Research and Development, Technology, and Innovation'],
                ['number' => 11, 'title' => 'Promote Trade and Investments', 'label' => 'Chapter 11: Promote Trade and Investments'],
                ['number' => 12, 'title' => 'Promote Financial Inclusion and Improve Public Financial Management', 'label' => 'Chapter 12: Promote Financial Inclusion and Improve Public Financial Management'],
                ['number' => 13, 'title' => 'Expand and Upgrade Infrastructure', 'label' => 'Chapter 13: Expand and Upgrade Infrastructure'],
                ['number' => 14, 'title' => 'Ensure Peace and Security, and Enhance Administration of Justice', 'label' => 'Chapter 14: Ensure Peace and Security, and Enhance Administration of Justice'],
                ['number' => 15, 'title' => 'Practice Good Governance and Improve Bureaucratic Efficiency', 'label' => 'Chapter 15: Practice Good Governance and Improve Bureaucratic Efficiency'],
                ['number' => 16, 'title' => 'Accelerate Climate Action and Strengthen Ecosystem and Disaster Resilience', 'label' => 'Chapter 16: Accelerate Climate Action and Strengthen Ecosystem and Disaster Resilience'],
            ]);
        }

        Schema::create('cpp_sdg_alignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cpp_submission_id')->constrained('cpp_submissions')->onDelete('cascade');
            $table->unsignedTinyInteger('sdg_goal_id');
            $table->foreign('sdg_goal_id')->references('id')->on('sdg_goals')->onDelete('cascade');
            $table->unique(['cpp_submission_id', 'sdg_goal_id'], 'uq_cpp_sdg');
        });

        Schema::create('cpp_rdp_alignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cpp_submission_id')->constrained('cpp_submissions')->onDelete('cascade');
            $table->unsignedTinyInteger('rdp_chapter_id');
            $table->foreign('rdp_chapter_id')->references('id')->on('rdp_chapters')->onDelete('cascade');
            $table->unique(['cpp_submission_id', 'rdp_chapter_id'], 'uq_cpp_rdp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cpp_sdg_alignments');
        Schema::dropIfExists('cpp_rdp_alignments');
        Schema::dropIfExists('cpp_impl_schedule');
        Schema::dropIfExists('cpp_consultation_dates');
        Schema::dropIfExists('cpp_locations');
        Schema::dropIfExists('cpp_logframes');
        Schema::dropIfExists('cpp_endorsements');
        Schema::dropIfExists('cpp_benefits_costs');
        Schema::dropIfExists('cpp_submissions');
    }
};
