<?php

/**
 * Query Optimization Configuration
 *
 * Defines pagination limits, cache TTLs, and safe query bounds for performance.
 * All services should reference these constants instead of hardcoding values.
 */

return [
    /**
     * Pagination Configuration
     * Use these defaults when paginating results
     */
    'pagination' => [
        // Default per-page limit for list endpoints
        'default_per_page' => 20,

        // Maximum allowed per_page parameter (prevent abuse)
        'max_per_page' => 100,

        // Minimum per_page
        'min_per_page' => 5,

        // Per-entity defaults
        'exercises' => 20,
        'workouts' => 15,
        'workout_logs' => 15,
        'daily_logs' => 30,
        'meals' => 25,
        'logged_sets' => 50,
        'workout_splits' => 20,
    ],

    /**
     * Cache Configuration
     * Centralized TTL values to avoid magic numbers
     */
    'cache' => [
        // Short-lived cache (5 minutes)
        'short_ttl' => 300,

        // Standard cache (30 minutes)
        'standard_ttl' => 1800,

        // Long-lived cache (1 hour)
        'long_ttl' => 3600,

        // Per-entity cache TTLs
        'user_exercises' => 1800,       // 30 min
        'user_settings' => 1800,        // 30 min
        'user_dashboard_stats' => 3600, // 1 hour
        'recent_users_list' => 3600,    // 1 hour
        'logged_sets' => 3600,          // 1 hour
        'split_exercises' => 3600,      // 1 hour
        'workouts' => 3600,             // 1 hour
        'workout_splits' => 3600,       // 1 hour
    ],

    /**
     * Query Safety Limits
     * Maximum rows returned from unbounded queries (should use pagination instead)
     */
    'limits' => [
        // Maximum rows to fetch without pagination warning
        'large_result_threshold' => 1000,

        // Date range query safe window (days)
        'date_range_max_days' => 365,

        // Chunk size for processing large datasets
        'chunk_size' => 500,
    ],

    /**
     * Cursor Pagination
     * Use for keyset-based pagination on large datasets
     */
    'cursor' => [
        'default_per_page' => 50,
        'max_per_page' => 200,
    ],

    /**
     * Query Timeout Configuration (in seconds)
     */
    'timeout' => [
        'default' => 30,
        'analytics' => 60,
        'export' => 120,
    ],

    /**
     * Rate Limit Analytics
     * Configuration for rate limit violation queries
     */
    'rate_limit' => [
        'recent_minutes' => 60,
        'top_items_limit' => 10,
        'suspicious_threshold' => 50,
    ],
];
