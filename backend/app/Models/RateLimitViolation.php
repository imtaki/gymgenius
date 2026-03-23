<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RateLimitViolation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'ip_address',
        'endpoint',
        'method',
        'limit',
        'current_count',
        'window_in_minutes',
        'user_agent',
        'identifier',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that caused the violation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get violations from the last N minutes.
     */
    public function scopeRecent($query, int $minutes = 60)
    {
        return $query->where('created_at', '>=', now()->subMinutes($minutes));
    }

    /**
     * Scope to get violations for a specific user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get violations for a specific IP.
     */
    public function scopeForIp($query, string $ip)
    {
        return $query->where('ip_address', $ip);
    }

    /**
     * Scope to get violations for a specific endpoint.
     */
    public function scopeForEndpoint($query, string $endpoint)
    {
        return $query->where('endpoint', $endpoint);
    }

    /**
     * Scope to get repeated violations from same identifier.
     * Useful for identifying attackers with pattern.
     */
    public function scopeRepeated($query, string $identifier, int $withinMinutes = 60, int $minimumViolations = 5)
    {
        return $query->where('identifier', $identifier)
            ->where('created_at', '>=', now()->subMinutes($withinMinutes))
            ->groupBy('identifier')
            ->having('count', '>=', $minimumViolations)
            ->selectRaw('identifier, count(*) as count, max(created_at) as latest_violation');
    }

    /**
     * Get summary statistics for a period.
     */
    public static function getStatistics(int $minutes = 60)
    {
        return self::recent($minutes)
            ->selectRaw('
                COUNT(*) as total_violations,
                COUNT(DISTINCT user_id) as unique_users,
                COUNT(DISTINCT ip_address) as unique_ips,
                COUNT(DISTINCT endpoint) as unique_endpoints
            ')
            ->first()
            ->toArray();
    }

    /**
     * Get top violating endpoints.
     */
    public static function getTopEndpoints(int $limit = 10, int $minutes = 60)
    {
        return self::recent($minutes)
            ->selectRaw('endpoint, COUNT(*) as violation_count')
            ->groupBy('endpoint')
            ->orderByRaw('COUNT(*) DESC')
            ->limit($limit)
            ->get();
    }

    /**
     * Get top violating IPs.
     */
    public static function getTopIps(int $limit = 10, int $minutes = 60)
    {
        return self::recent($minutes)
            ->selectRaw('ip_address, COUNT(*) as violation_count, COUNT(DISTINCT user_id) as user_count')
            ->groupBy('ip_address')
            ->orderByRaw('COUNT(*) DESC')
            ->limit($limit)
            ->get();
    }
}
