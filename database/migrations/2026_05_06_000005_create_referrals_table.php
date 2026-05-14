<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cipg_submission_id')->nullable()->constrained('cpp_submissions')->nullOnDelete();
            $table->foreignId('from_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('from_division_id')->nullable()->constrained('divisions')->nullOnDelete();
            $table->foreignId('to_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('to_division_id')->nullable()->constrained('divisions')->nullOnDelete();
            $table->string('stage', 100)->nullable();
            $table->string('status', 50)->default('Pending')->index();
            $table->text('notes')->nullable();
            $table->timestamp('referred_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['cipg_submission_id', 'status']);
            $table->index(['from_user_id', 'status']);
            $table->index(['to_division_id', 'status']);
            $table->index('stage');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
