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
            $table->decimal('nga_funding', 18, 2)->nullable()->after('linkages');
            $table->decimal('lgu_funding', 18, 2)->nullable()->after('nga_funding');
            $table->decimal('oda_funding', 18, 2)->nullable()->after('lgu_funding');
            $table->decimal('others_funding', 18, 2)->nullable()->after('oda_funding');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cpp_submissions', function (Blueprint $table) {
            $table->dropColumn(['nga_funding', 'lgu_funding', 'oda_funding', 'others_funding']);
        });
    }
};
