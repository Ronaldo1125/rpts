<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments_and_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cpp_submission_id')->constrained('cpp_submissions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users');

            $table->text('finding')->nullable();
            $table->text('recommendation')->nullable();
            
            $table->string('stage')->nullable(); // e.g. Assessment, Evaluation, Review
            $table->string('status')->default('Draft'); // e.g. Draft, Pending, Resolved

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments_and_recommendations');
    }
};
