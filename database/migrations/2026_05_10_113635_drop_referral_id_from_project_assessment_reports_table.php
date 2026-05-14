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
        Schema::table('project_assessment_reports', function (Blueprint $table) {
            $table->dropForeign(['referral_id']);
            $table->dropColumn('referral_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_assessment_reports', function (Blueprint $table) {
            $table->foreignId('referral_id')->nullable()->constrained('referrals')->onDelete('set null');
        });
    }
};
