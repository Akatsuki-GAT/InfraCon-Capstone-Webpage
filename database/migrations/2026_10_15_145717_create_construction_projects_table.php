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
        Schema::create('construction_projects', function (Blueprint $table) {
            $table->id('ConProjID');
            $table->foreignId('createdBy')->constrained('construction_users', 'UserID');
            $table->string('projectName', 50);
            $table->string('location', 100);
            $table->date('startedDate');
            $table->date('targetEndDate');
            $table->date('actualEndDate')->nullable();
            $table->enum('status', ['active', 'complete','cancelled','archive'])->default('active');
            $table->timestamps();
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('construction_projects');
    }
};
