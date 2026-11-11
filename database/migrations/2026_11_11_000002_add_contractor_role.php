<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('construction_users', function (Blueprint $table) {
            $table->dropForeign(['RoleID']);
        });

        DB::table('construction_users')
            ->where('RoleID', '>=', 2)
            ->update(['RoleID' => DB::raw('RoleID + 1')]);

        DB::statement(
            'UPDATE user_role SET RoleID = RoleID + 1 WHERE RoleID >= 2 ORDER BY RoleID DESC'
        );

        DB::table('user_role')->insert([
            'RoleID' => 2,
            'roleName' => 'Contractor',
        ]);

        Schema::table('construction_users', function (Blueprint $table) {
            $table->foreign('RoleID')
                ->references('RoleID')
                ->on('user_role');
        });
    }

    public function down(): void
    {
        Schema::table('construction_users', function (Blueprint $table) {
            $table->dropForeign(['RoleID']);
        });

        DB::table('construction_users')
            ->where('RoleID', 2)
            ->update(['RoleID' => 1]);

        DB::table('user_role')->where('RoleID', 2)->delete();

        DB::table('construction_users')
            ->where('RoleID', '>=', 3)
            ->update(['RoleID' => DB::raw('RoleID - 1')]);

        DB::statement(
            'UPDATE user_role SET RoleID = RoleID - 1 WHERE RoleID >= 3 ORDER BY RoleID ASC'
        );

        Schema::table('construction_users', function (Blueprint $table) {
            $table->foreign('RoleID')
                ->references('RoleID')
                ->on('user_role');
        });
    }
};
