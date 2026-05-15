<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_assessment_reports', function (Blueprint $table) {
            $table->json('readiness_data')->nullable()->after('readiness_level');
            $table->json('endorsement_data')->nullable()->after('doc_endorsements');
        });
    }

    public function down(): void
    {
        Schema::table('project_assessment_reports', function (Blueprint $table) {
            $table->dropColumn(['readiness_data', 'endorsement_data']);
        });
    }
};
