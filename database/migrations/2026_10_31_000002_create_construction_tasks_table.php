<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('construction_tasks', function (Blueprint $table) {
            $table->id('TaskID');
            $table->foreignId('ConProgressID')
                ->constrained('construction_progress', 'ConProgressID')
                ->cascadeOnDelete();
            $table->foreignId('createdBy')
                ->constrained('construction_users', 'UserID')
                ->restrictOnDelete();
            $table->foreignId('updatedBy')
                ->nullable()
                ->constrained('construction_users', 'UserID')
                ->restrictOnDelete();
            $table->string('taskName', 55);
            $table->string('taskDesc', 255)->nullable();
            $table->date('startDate');
            $table->enum('status', ['active', 'complete', 'archive'])->default('active');
            $table->timestamps();

            $table->unique(['ConProgressID', 'taskName']);
            $table->index(['ConProgressID', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('construction_tasks');
    }
};
