<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add missing indexes for performance optimization on heavy-query tables.
     * Focus on foreign keys, date columns, and GROUP BY/ORDER BY columns used in analytics.
     */
    public function up(): void
    {
        // Index for Exercise::where('user_id')
        if (Schema::hasTable('exercises')) {
            Schema::table('exercises', function (Blueprint $table) {
                $table->index('user_id');
            });
        }

        // Index for Workout queries (user + date lookups)
        if (Schema::hasTable('workouts')) {
            Schema::table('workouts', function (Blueprint $table) {
                $table->index(['user_id', 'date']);
                $table->index('user_id');
                $table->index('created_at');
            });
        }

        // Index for WorkoutLog date range queries
        if (Schema::hasTable('workout_logs')) {
            Schema::table('workout_logs', function (Blueprint $table) {
                $table->index(['user_id', 'date']);
                $table->index('exercise_id');
                $table->index('date');
            });
        }

        // Index for DailyLog (user_id already indexed implicitly, add date for range queries)
        if (Schema::hasTable('daily_logs')) {
            Schema::table('daily_logs', function (Blueprint $table) {
                $table->index('date');
                $table->index(['user_id', 'date']);
            });
        }

        // Indexes for Meal queries
        if (Schema::hasTable('meals')) {
            Schema::table('meals', function (Blueprint $table) {
                $table->index('user_id');
                $table->index('daily_log_id');
                $table->index(['user_id', 'created_at']);
            });
        }

        // Index for WorkoutSplitExercise (workout_split_id lookup)
        if (Schema::hasTable('workout_split_exercises')) {
            Schema::table('workout_split_exercises', function (Blueprint $table) {
                $table->index('workout_split_id');
                $table->index('exercise_id');
            });
        }

        // Enhanced indexes for RateLimitViolation analytics
        if (Schema::hasTable('rate_limit_violations')) {
            Schema::table('rate_limit_violations', function (Blueprint $table) {
                // Already has indexes, but ensure created_at is indexed for recent() queries
                if (!Schema::hasColumn('rate_limit_violations', 'created_at')) {
                    $table->index('created_at');
                }
                $table->index(['created_at', 'endpoint']);
                $table->index(['created_at', 'ip_address']);
            });
        }

        // Index for WorkoutSplit queries
        if (Schema::hasTable('workout_splits')) {
            Schema::table('workout_splits', function (Blueprint $table) {
                $table->index('user_id');
                $table->index('created_at');
            });
        }

        // Index for LoggedSet queries (already has some, but add user context)
        if (Schema::hasTable('logged_sets')) {
            Schema::table('logged_sets', function (Blueprint $table) {
                $table->index('created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'exercises' => ['user_id'],
            'workouts' => ['user_id', 'created_at', 'user_id_date'],
            'workout_logs' => ['user_id_date', 'exercise_id', 'date'],
            'daily_logs' => ['date', 'user_id_date'],
            'meals' => ['user_id', 'daily_log_id', 'user_id_created_at'],
            'workout_split_exercises' => ['workout_split_id', 'exercise_id'],
            'workout_splits' => ['user_id', 'created_at'],
            'logged_sets' => ['created_at'],
        ];

        foreach ($tables as $table => $indexNames) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) use ($indexNames) {
                    foreach ($indexNames as $indexName) {
                        try {
                            $table->dropIndex($indexName);
                        } catch (\Exception $e) {
                            // Index may not exist; skip
                        }
                    }
                });
            }
        }
    }
};
