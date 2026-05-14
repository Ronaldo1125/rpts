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
            $table->decimal('geo_start_lat', 10, 8)->nullable()->after('project_coverage');
            $table->decimal('geo_start_lng', 11, 8)->nullable()->after('geo_start_lat');
            $table->decimal('geo_end_lat', 10, 8)->nullable()->after('geo_start_lng');
            $table->decimal('geo_end_lng', 11, 8)->nullable()->after('geo_end_lat');
        });

        Schema::table('cpp_locations', function (Blueprint $table) {
            $table->dropColumn(['geo_start_lat', 'geo_start_lng', 'geo_end_lat', 'geo_end_lng']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cpp_locations', function (Blueprint $table) {
            $table->decimal('geo_start_lat', 10, 8)->nullable();
            $table->decimal('geo_start_lng', 11, 8)->nullable();
            $table->decimal('geo_end_lat', 10, 8)->nullable();
            $table->decimal('geo_end_lng', 11, 8)->nullable();
        });

        Schema::table('cpp_submissions', function (Blueprint $table) {
            $table->dropColumn(['geo_start_lat', 'geo_start_lng', 'geo_end_lat', 'geo_end_lng']);
        });
    }
};
