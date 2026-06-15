<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goal_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goal_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['goal', 'stop', 'start', 'continue']);
            $table->text('content')->nullable();
            $table->timestamps();

            $table->unique(['goal_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goal_sections');
    }
};
