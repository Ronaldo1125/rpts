<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddDivisionIdToUsersAndMigrateData extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // add nullable division_id first
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'division_id')) {
                $table->unsignedBigInteger('division_id')->nullable()->after('agency_id');
            }
        });

        // migrate existing string divisions to divisions table
        $users = DB::table('users')->select('id', 'division')->whereNotNull('division')->get();
        foreach ($users as $u) {
            $name = trim($u->division);
            if ($name === '') continue;
            $division = DB::table('divisions')->where('name', $name)->first();
            if (!$division) {
                $id = DB::table('divisions')->insertGetId([
                    'name' => $name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $id = $division->id;
            }
            DB::table('users')->where('id', $u->id)->update(['division_id' => $id]);
        }

        // make FK constraint
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'division_id')) return;
            $table->foreign('division_id')->references('id')->on('divisions')->onDelete('set null');
        });

        // drop old division string column
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'division')) {
                $table->dropColumn('division');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // recreate division string column
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'division')) {
                $table->string('division')->nullable()->after('agency_id');
            }
        });

        // copy back division names
        $users = DB::table('users')->select('id', 'division_id')->whereNotNull('division_id')->get();
        foreach ($users as $u) {
            $div = DB::table('divisions')->where('id', $u->division_id)->first();
            if ($div) {
                DB::table('users')->where('id', $u->id)->update(['division' => $div->name]);
            }
        }

        // drop FK and column
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'division_id')) {
                $table->dropForeign(['division_id']);
                $table->dropColumn('division_id');
            }
        });
    }
}
