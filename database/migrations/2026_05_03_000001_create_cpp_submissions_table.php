<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─────────────────────────────────────────────────────────────
        // MAIN: cpp_submissions
        // ─────────────────────────────────────────────────────────────
        Schema::create('cpp_submissions', function (Blueprint $table) {
            $table->id();

            // ── Ownership ──────────────────────────────────────────
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // Agency is auto-filled from the logged-in user's agency name
            $table->string('agency')->index();

            // ── PAGE 1 · I. Project Information ───────────────────
            $table->foreignId('sector_id')->nullable()->constrained('sectors')->nullOnDelete();
            $table->foreignId('sub_sector_id')->nullable()->constrained('sub_sectors')->nullOnDelete();
            $table->string('project_title', 500);
            // project_type is multi-select (Infrastructure, Non-Infrastructure, etc.)
            $table->json('project_type')->nullable();
            $table->text('components')->nullable();

            // ── PAGE 1 · Location ──────────────────────────────────
            // Regionwide | Inter-Province | Location-Specific
            $table->enum('project_coverage', ['Regionwide', 'Inter-Province', 'Location-Specific'])->nullable();
            // Location-Specific cascade
            $table->foreignId('province_id')->nullable()->constrained('provinces')->nullOnDelete();
            $table->foreignId('district_id')->nullable()->constrained('districts')->nullOnDelete();
            $table->foreignId('municipality_id')->nullable()->constrained('municipalities')->nullOnDelete();
            $table->foreignId('barangay_id')->nullable()->constrained('barangays')->nullOnDelete();

            // ── PAGE 1 · II. Project Status ───────────────────────
            $table->enum('project_status', ['Ongoing', 'Pipeline', 'Proposed'])->nullable();
            // Status of Project Preparation (Site, ROW, DED, etc.)
            $table->json('prep_status')->nullable();

            // ── PAGE 1 · III. Endorsements ────────────────────────
            $table->string('sp_resolution_no')->nullable();
            $table->date('sp_resolution_date')->nullable();
            $table->string('sb_resolution_no')->nullable();
            $table->date('sb_resolution_date')->nullable();
            $table->string('letter_request_ref')->nullable();   // Reference no. / description
            $table->date('letter_transmittal_date')->nullable();
            $table->string('bor_bot_resolution_no')->nullable();
            $table->date('bor_bot_resolution_date')->nullable();

            // ── PAGE 2 · IV. Project Justification ───────────────
            // SDG alignment — comma-separated selected goals stored as JSON
            $table->json('sdg_alignment')->nullable();
            // RDP 2023-2028 alignment — JSON array of selected chapters
            $table->json('rdp_alignment')->nullable();
            $table->text('background')->nullable();         // 1. Project Background
            $table->text('goal')->nullable();               // 2. Goal
            $table->text('purpose')->nullable();            // 3. Purpose
            $table->text('outputs')->nullable();            // 4. Project Output/s
            $table->text('activities')->nullable();         // 5. Project Activities
            $table->text('linkages')->nullable();           // 6. Project Linkages

            // ── PAGE 2 · V. Project Financing ────────────────────
            $table->decimal('total_cost', 18, 2)->nullable();
            $table->text('funding_source')->nullable();
            $table->string('counterpart_funding')->nullable();

            // ── PAGE 2 · VI. Project Benefits and Costs (part 1) ─
            $table->text('beneficiaries')->nullable();
            $table->text('social_benefits')->nullable();
            $table->text('economic_benefits')->nullable();

            // ── PAGE 3 · VI. (continued) ─────────────────────────
            $table->text('social_costs')->nullable();
            $table->text('economic_costs')->nullable();

            // ── PAGE 3 · VII. Project Implementation ─────────────
            $table->text('agencies_involved')->nullable();
            // Implementation schedule rows → stored in cpp_impl_schedule table (see below)
            $table->text('impl_arrangement')->nullable();

            // Environmental clearance
            $table->text('env_clearance_desc')->nullable();

            // Social Acceptability
            $table->text('social_accept')->nullable();
            $table->enum('consultation_status', ['Yes', 'No'])->nullable();
            $table->date('consult_planned_date')->nullable();
            // Multiple dates when Yes → stored in cpp_consultation_dates table
            $table->text('consult_highlights')->nullable();
            $table->text('hgdg')->nullable();               // HGDG score and discussion

            // ── PAGE 4 · VIII. Project Logical Framework ─────────
            // Goal row
            $table->text('lf_goal_narrative')->nullable();
            $table->text('lf_goal_indicators')->nullable();
            $table->text('lf_goal_verification')->nullable();
            $table->text('lf_goal_assumptions')->nullable();
            // Purpose row
            $table->text('lf_purpose_narrative')->nullable();
            $table->text('lf_purpose_indicators')->nullable();
            $table->text('lf_purpose_verification')->nullable();
            $table->text('lf_purpose_assumptions')->nullable();
            // Outputs row
            $table->text('lf_outputs_narrative')->nullable();
            $table->text('lf_outputs_indicators')->nullable();
            $table->text('lf_outputs_verification')->nullable();
            $table->text('lf_outputs_assumptions')->nullable();
            // Inputs / Activities row
            $table->text('lf_inputs_narrative')->nullable();
            $table->text('lf_inputs_indicators')->nullable();
            $table->text('lf_inputs_verification')->nullable();
            $table->text('lf_inputs_assumptions')->nullable();

            // ── PAGE 5 · IX. Geotagged Photo ─────────────────────
            // Stored as a file path (stored in cpp_attachments with type='geo_photo')
            // No column needed here — see cpp_attachments

            // ── PAGE 5 · X. Geolocation Coordinates ──────────────
            $table->decimal('geo_start_lat', 10, 6)->nullable();
            $table->decimal('geo_start_lng', 10, 6)->nullable();
            $table->decimal('geo_end_lat', 10, 6)->nullable();
            $table->decimal('geo_end_lng', 10, 6)->nullable();

            // ── PAGE 5 · Prepared By / Noted By ──────────────────
            $table->string('prepared_by_name')->nullable();
            $table->string('prepared_by_position')->nullable();
            $table->date('prepared_date')->nullable();

            $table->string('noted_by_name')->nullable();
            $table->string('noted_by_position')->nullable();
            $table->date('noted_date')->nullable();

            // ── Workflow Status ────────────────────────────────────
            // Draft | For Review | For Revision | Validated | Approved
            $table->string('status')->default('Draft')->index();
            $table->string('stage')->nullable();
            $table->timestamp('submitted_at')->nullable();

            // ── Promotion ─────────────────────────────────────────
            // Set when CPP is approved and promoted to a tracked project
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();

            $table->timestamps();

            // Common query indexes
            $table->index(['user_id', 'status']);
            $table->index(['agency', 'status']);
            $table->index('project_title');
        });

        // ─────────────────────────────────────────────────────────────
        // CHILD: cpp_impl_schedule  (implementation schedule rows)
        // ─────────────────────────────────────────────────────────────
        Schema::create('cpp_impl_schedule', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cpp_submission_id')
                  ->constrained('cpp_submissions')
                  ->cascadeOnDelete();
            $table->string('year', 10);
            $table->text('physical_target');
            $table->string('indicator');
            $table->decimal('amount', 18, 2)->default(0);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // ─────────────────────────────────────────────────────────────
        // CHILD: cpp_consultation_dates
        // ─────────────────────────────────────────────────────────────
        Schema::create('cpp_consultation_dates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cpp_submission_id')->constrained('cpp_submissions')->cascadeOnDelete();
            $table->date('consultation_date');
        });

        // ─────────────────────────────────────────────────────────────
        // CHILD: cpp_submission_revisions (JSON Snapshots for Version History)
        // ─────────────────────────────────────────────────────────────
        Schema::create('cpp_submission_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cpp_submission_id')
                  ->constrained('cpp_submissions')
                  ->cascadeOnDelete();
            
            $table->unsignedInteger('version');
            $table->string('revision_notes', 500)->nullable();
            $table->foreignId('revised_by')->nullable()->constrained('users')->nullOnDelete();
            
            // The entire state of the submission before the revision
            $table->json('snapshot_data'); 
            
            $table->timestamps();

            $table->unique(['cpp_submission_id', 'version'], 'uq_cpp_revision_version');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cpp_submission_revisions');
        Schema::dropIfExists('cpp_consultation_dates');
        Schema::dropIfExists('cpp_impl_schedule');
        Schema::dropIfExists('cpp_submissions');
    }
};
