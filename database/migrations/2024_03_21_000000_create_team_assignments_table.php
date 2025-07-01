<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supervisor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('developer_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            // Ensure a developer can only be assigned to one supervisor
            $table->unique('developer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_assignments');
    }
}; 