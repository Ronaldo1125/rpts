<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submission_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cpp_submission_id')->constrained('cpp_submissions')->cascadeOnDelete();
            $table->unsignedBigInteger('referral_id')->nullable();  // soft reference — no FK since project_referrals may not exist yet
            $table->foreignId('staff_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes');
            $table->string('stage_at_feedback')->default('Completeness Test and Validation');
            $table->string('status_at_feedback')->default('Review');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submission_feedbacks');
    }
};
