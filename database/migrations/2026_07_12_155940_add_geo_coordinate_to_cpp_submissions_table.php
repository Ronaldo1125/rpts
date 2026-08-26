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
        Schema::table('cpp_submissions', function (Blueprint $table) {
            $table->enum('geo_coordinate', ['geo-lineal', 'geo-building'])->nullable()->after('project_coverage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cpp_submissions', function (Blueprint $table) {
            $table->dropColumn(['geo_coordinate']);
        });
    }
};
