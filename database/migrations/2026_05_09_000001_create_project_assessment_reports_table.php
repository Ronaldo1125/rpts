<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_assessment_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cpp_submission_id')->constrained('cpp_submissions')->onDelete('cascade');
            // NOTE: referral_id was removed from this table; the link is now on referrals.par_id

            // Workflow Tracking (System User IDs)
            $table->foreignId('assessor_id')->constrained('users');
            $table->foreignId('evaluator_id')->nullable()->constrained('users');
            $table->foreignId('checker_id')->nullable()->constrained('users');
            $table->foreignId('concluder_id')->nullable()->constrained('users');

            // Document Signatories (Text for Reports)
            $table->string('prepared_by')->nullable();
            $table->string('prepared_by_pos')->nullable();
            $table->string('reviewed_by')->nullable();
            $table->string('reviewed_by_pos')->nullable();
            $table->string('approved_by')->nullable();
            $table->string('approved_by_pos')->nullable();

            // Section I: Documentary Requirements
            $table->boolean('doc_request')->default(false);
            $table->boolean('doc_cpp_fs')->default(false);
            $table->boolean('doc_endorsements')->default(false);

            // Section II: Criteria Checklist
            $table->json('typology_data')->nullable();
            $table->json('responsiveness_data')->nullable();
            $table->string('readiness_level')->nullable();

            // Section III: Qualitative Analysis
            $table->text('par_analysis')->nullable();
            $table->text('final_recommendation')->nullable();

            // Section IV: Budget & Annex
            $table->text('annex_description')->nullable();
            $table->json('budget_breakdown')->nullable();
            $table->decimal('total_project_cost', 15, 2)->default(0);

            // Report Status
            $table->string('status')->default('Draft');
            $table->boolean('is_sectoral')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_assessment_reports');
    }
};
