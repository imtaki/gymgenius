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
        Schema::create('workout_split_exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_split_id')->constrained('workout_splits')->onDelete('cascade');
            $table->foreignId('exercise_id')->constrained()->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->integer('target_sets')->nullable();
            $table->integer('target_reps')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes for efficient queries
            $table->index('workout_split_id');
            $table->index('exercise_id');
            $table->unique(['workout_split_id', 'exercise_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workout_split_exercises');
    }
};
