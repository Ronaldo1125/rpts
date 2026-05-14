<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropCodeFromDivisionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('divisions', function (Blueprint $table) {
            if (Schema::hasColumn('divisions', 'code')) {
                $table->dropColumn('code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('divisions', function (Blueprint $table) {
            if (!Schema::hasColumn('divisions', 'code')) {
                $table->string('code')->nullable()->after('name');
            }
        });
    }
}
