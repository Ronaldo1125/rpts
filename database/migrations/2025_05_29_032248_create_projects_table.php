<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_title')->unique();
            $table->text('description')->nullable();
            $table->foreignId('component_project_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('agency_id')->constrained();
            $table->string('fund_source');
            $table->string('other_fund_source')->nullable();
            $table->string('status');
            $table->decimal('funding_requirement', 15, 2)->default(0);
            $table->string('funding_category');
            $table->string('location');
            $table->string('latitude')->nullable();
            $table->string('longtitude')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
