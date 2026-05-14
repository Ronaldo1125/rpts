<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('referrals', function (Blueprint $table) {
            $table->foreignId('par_id')
                ->nullable()
                ->after('cipg_submission_id')
                ->constrained('project_assessment_reports')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('referrals', function (Blueprint $table) {
            $table->dropForeign(['par_id']);
            $table->dropColumn('par_id');
        });
    }
};
