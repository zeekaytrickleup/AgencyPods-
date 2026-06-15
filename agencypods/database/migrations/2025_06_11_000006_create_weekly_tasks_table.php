<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weekly_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('task');
            $table->enum('status', ['done', 'pending'])->default('pending');
            $table->date('week_start');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_tasks');
    }
};
