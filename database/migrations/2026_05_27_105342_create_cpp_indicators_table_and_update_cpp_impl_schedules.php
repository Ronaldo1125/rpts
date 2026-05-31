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
        if (Schema::hasColumn('cpp_impl_schedule', 'indicator')) {
            Schema::table('cpp_impl_schedule', function (Blueprint $table) {
                $table->dropColumn('indicator');
            });
        }

        Schema::create('cpp_indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cpp_impl_schedule_id')->constrained('cpp_impl_schedule')->onDelete('cascade');
            $table->foreignId('indicator_id')->constrained('indicators')->onDelete('cascade');
            $table->string('indicator_quantity')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cpp_indicators');

        Schema::table('cpp_impl_schedule', function (Blueprint $table) {
            $table->string('indicator')->nullable();
        });
    }
};
