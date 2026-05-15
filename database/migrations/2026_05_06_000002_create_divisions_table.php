<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create divisions table (no 'code' column — was added then dropped)
        Schema::create('divisions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // 2. Add division_id to users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'division_id')) {
                $table->unsignedBigInteger('division_id')->nullable()->after('agency_id');
            }
        });

        // 3. Migrate any existing string 'division' values into the new divisions table
        if (Schema::hasColumn('users', 'division')) {
            $users = DB::table('users')->select('id', 'division')->whereNotNull('division')->get();
            foreach ($users as $u) {
                $name = trim($u->division);
                if ($name === '') continue;
                $division = DB::table('divisions')->where('name', $name)->first();
                $id = $division
                    ? $division->id
                    : DB::table('divisions')->insertGetId(['name' => $name, 'created_at' => now(), 'updated_at' => now()]);
                DB::table('users')->where('id', $u->id)->update(['division_id' => $id]);
            }
        }

        // 4. Add the FK constraint on division_id
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'division_id')) {
                $table->foreign('division_id')->references('id')->on('divisions')->onDelete('set null');
            }
        });

        // 5. Drop the legacy string column
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'division')) {
                $table->dropColumn('division');
            }
        });
    }

    public function down(): void
    {
        // Restore string column
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'division')) {
                $table->string('division')->nullable()->after('agency_id');
            }
        });

        // Copy names back
        $users = DB::table('users')->select('id', 'division_id')->whereNotNull('division_id')->get();
        foreach ($users as $u) {
            $div = DB::table('divisions')->where('id', $u->division_id)->first();
            if ($div) {
                DB::table('users')->where('id', $u->id)->update(['division' => $div->name]);
            }
        }

        // Drop FK and division_id
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'division_id')) {
                $table->dropForeign(['division_id']);
                $table->dropColumn('division_id');
            }
        });

        Schema::dropIfExists('divisions');
    }
};
