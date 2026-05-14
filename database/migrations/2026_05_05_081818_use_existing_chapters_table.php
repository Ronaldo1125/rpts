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
        // 1. Drop the temporary alignment table that points to rdp_chapters
        Schema::dropIfExists('cpp_rdp_alignments');

        // 2. Drop the rdp_chapters table we created earlier
        Schema::dropIfExists('rdp_chapters');

        // 3. Recreate the alignment table pointing to the existing 'chapters' table
        Schema::create('cpp_rdp_alignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cpp_submission_id')->constrained('cpp_submissions')->onDelete('cascade');
            $table->unsignedBigInteger('chapter_id');
            // Assuming the existing table is named 'chapters'
            $table->foreign('chapter_id')->references('id')->on('chapters')->onDelete('cascade');
            $table->unique(['cpp_submission_id', 'chapter_id'], 'uq_cpp_chapter');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cpp_rdp_alignments');
    }
};
