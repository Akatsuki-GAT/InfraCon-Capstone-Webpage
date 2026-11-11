<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('construction_progress', function (Blueprint $table) {
            $table->id('ConProgressID');
            $table->foreignId('ConProjID')
                ->constrained('construction_projects', 'ConProjID')
                ->cascadeOnDelete();
            $table->foreignId('createdBy')
                ->constrained('construction_users', 'UserID')
                ->restrictOnDelete();
            $table->foreignId('updatedBy')
                ->nullable()
                ->constrained('construction_users', 'UserID')
                ->restrictOnDelete();
            $table->string('progressName', 255);
            $table->string('progressDesc', 255)->nullable();
            $table->date('startDate');
            $table->date('finishDate');
            $table->enum('status', ['ongoing', 'complete', 'archive'])->default('ongoing');
            $table->timestamps();

            $table->unique(['ConProjID', 'progressName']);
            $table->index(['ConProjID', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('construction_progress');
    }
};
