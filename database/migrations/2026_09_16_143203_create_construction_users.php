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
        Schema::create('construction_users', function (Blueprint $table) {
            $table->id('UserID');
            $table->foreignId('RoleID')->constrained('user_role', 'RoleID');
            $table->string('firstName', 50);
            $table->string('lastName', 50);
            $table->string('email', 50);
            $table->string('password');
            $table->string('contactNo', 11);
            $table->timestamps();
            $table->enum('status', ['active', 'inactive'])->default('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('construction_users');
    }
};
