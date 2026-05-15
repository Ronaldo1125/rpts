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
            // Soft reference — no FK because referrals may not exist yet
            $table->unsignedBigInteger('referral_id')->nullable();
            $table->foreignId('staff_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submission_feedbacks');
    }
};
