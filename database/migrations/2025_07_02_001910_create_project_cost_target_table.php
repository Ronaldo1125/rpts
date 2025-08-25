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
        Schema::create('project_cost_target', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->decimal('target_year_2023', 5, 2)->default(0);
            $table->decimal('target_year_2024', 5, 2)->default(0);
            $table->decimal('target_year_2025', 5, 2)->default(0);
            $table->decimal('target_year_2026', 5, 2)->default(0);
            $table->decimal('target_year_2027', 5, 2)->default(0);
            $table->decimal('target_year_2028', 5, 2)->default(0);
            $table->decimal('target_succeeding_years', 5, 2)->default(0);
            $table->decimal('cost_year_2023', 8, 2)->default(0);
            $table->decimal('cost_year_2024', 8, 2)->default(0);
            $table->decimal('cost_year_2025', 8, 2)->default(0);
            $table->decimal('cost_year_2026', 8, 2)->default(0);
            $table->decimal('cost_year_2027', 8, 2)->default(0);
            $table->decimal('cost_year_2028', 8, 2)->default(0);
            $table->decimal('cost_succeeding_years', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_cost_target');
    }
};
