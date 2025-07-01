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
        Schema::create('change_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users');
            $table->foreignId('developer_id')->nullable()->constrained('users');
            $table->string('title');
            $table->text('description');
            $table->string('status')->default('pending'); // pending, validated, approved, in_progress, completed, rejected
            $table->text('validation_notes')->nullable();
            $table->text('approval_notes')->nullable();
            $table->text('implementation_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('change_requests');
    }
};
