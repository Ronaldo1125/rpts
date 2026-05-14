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
        // First, update any existing NULL values to 'Submission'
        DB::table('cpp_submissions')->whereNull('stage')->update(['stage' => 'Submission']);

        Schema::table('cpp_submissions', function (Blueprint $table) {
            $table->string('stage', 50)->default('Submission')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cpp_submissions', function (Blueprint $table) {
            $table->string('stage', 50)->nullable()->default(null)->change();
        });
    }
};
