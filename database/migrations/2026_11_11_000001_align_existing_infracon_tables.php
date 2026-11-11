<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('construction_projects', function (Blueprint $table) {
            $table->renameColumn('startedDate', 'actualStartDate');
        });

        Schema::table('construction_projects', function (Blueprint $table) {
            $table->date('targetStartDate')->nullable()->after('location');
        });

        DB::table('construction_projects')
            ->whereNull('targetStartDate')
            ->update(['targetStartDate' => DB::raw('actualStartDate')]);

        Schema::table('construction_projects', function (Blueprint $table) {
            $table->date('targetStartDate')->nullable(false)->change();
        });

        Schema::table('user_role', function (Blueprint $table) {
            $table->unique('roleName');
        });
    }

    public function down(): void
    {
        Schema::table('user_role', function (Blueprint $table) {
            $table->dropUnique(['roleName']);
        });

        Schema::table('construction_projects', function (Blueprint $table) {
            $table->dropColumn('targetStartDate');
            $table->renameColumn('actualStartDate', 'startedDate');
        });
    }
};
