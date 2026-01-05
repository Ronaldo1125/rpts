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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_title')->unique();
            $table->text('description')->nullable();
            $table->foreignId('component_project_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('agency_id')->constrained();
            $table->foreignId('status_id')->constrained();
            $table->decimal('funding_requirement', 9, 2)->default(0);
            $table->foreignId('funding_category_id')->constrained();
            $table->string('location');
            $table->text('remarks')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
