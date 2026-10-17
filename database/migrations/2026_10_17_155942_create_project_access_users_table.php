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
        Schema::create('project_access_users', function (Blueprint $table) {
            $table->id('ProjectAccessID');
            $table->foreignId('ConProjID')->constrained('construction_projects', 'ConProjID')->cascadeOnDelete();
            $table->foreignId('UserID')->constrained('construction_users', 'UserID')->restrictOnDelete();
            $table->timestamps();
            $table->unique(['ConProjID', 'UserID']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_access_users');
    }
};
