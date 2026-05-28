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
        Schema::create('logged_sets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_id')->constrained()->onDelete('cascade');
            $table->foreignId('workout_split_exercise_id')->constrained('workout_split_exercises')->onDelete('cascade');
            $table->integer('set_number');
            $table->integer('reps')->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->integer('rpe')->nullable(); // Rate of Perceived Exertion (1-10)
            $table->timestamps();
            
            // Indexes for efficient queries
            $table->index('workout_id');
            $table->index('workout_split_exercise_id');
            $table->index(['workout_id', 'set_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logged_sets');
    }
};
