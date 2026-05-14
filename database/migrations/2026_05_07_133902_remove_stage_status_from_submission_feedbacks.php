<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submission_feedbacks', function (Blueprint $table) {
            $table->dropColumn(['stage_at_feedback', 'status_at_feedback']);
        });
    }

    public function down(): void
    {
        Schema::table('submission_feedbacks', function (Blueprint $table) {
            $table->string('stage_at_feedback')->default('Completeness Test and Validation');
            $table->string('status_at_feedback')->default('Review');
        });
    }
};
