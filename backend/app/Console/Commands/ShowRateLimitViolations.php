<?php

namespace App\Console\Commands;

use App\Models\RateLimitViolation;
use Illuminate\Console\Command;

class ShowRateLimitViolations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ratelimit:show 
                            {--minutes=60 : Number of minutes to look back}
                            {--top=10 : Number of top items to show}
                            {--stats : Show only statistics}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display rate limit violation statistics and patterns';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $minutes = (int)$this->option('minutes');
        $top = (int)$this->option('top');
        $statsOnly = $this->option('stats');

        $this->info("=== Rate Limit Violations (Last {$minutes} minutes) ===\n");

        // Show statistics
        $stats = RateLimitViolation::recent($minutes)->selectRaw('
            COUNT(*) as total_violations,
            COUNT(DISTINCT user_id) as unique_users,
            COUNT(DISTINCT ip_address) as unique_ips,
            COUNT(DISTINCT endpoint) as unique_endpoints,
            COUNT(DISTINCT method) as unique_methods
        ')->first();

        $this->line("📊 Statistics:");
        $this->line("  Total Violations: {$stats->total_violations}");
        $this->line("  Unique Users: {$stats->unique_users}");
        $this->line("  Unique IPs: {$stats->unique_ips}");
        $this->line("  Unique Endpoints: {$stats->unique_endpoints}");
        $this->line("  Unique Methods: {$stats->unique_methods}\n");

        if ($statsOnly) {
            return Command::SUCCESS;
        }

        // Show top endpoints
        $topEndpoints = RateLimitViolation::recent($minutes)
            ->selectRaw('endpoint, COUNT(*) as violation_count, COUNT(DISTINCT identifier) as unique_identifiers')
            ->groupBy('endpoint')
            ->orderByRaw('COUNT(*) DESC')
            ->limit($top)
            ->get();

        if ($topEndpoints->count() > 0) {
            $this->line("🚀 Top Violated Endpoints:");
            $headers = ['Endpoint', 'Violations', 'Unique Users/IPs'];
            $rows = $topEndpoints->map(fn($e) => [
                $e->endpoint,
                $e->violation_count,
                $e->unique_identifiers,
            ])->toArray();
            $this->table($headers, $rows);
            $this->newLine();
        }

        // Show top IPs
        $topIps = RateLimitViolation::recent($minutes)
            ->selectRaw('ip_address, COUNT(*) as violation_count, COUNT(DISTINCT user_id) as authenticated_users')
            ->groupBy('ip_address')
            ->orderByRaw('COUNT(*) DESC')
            ->limit($top)
            ->get();

        if ($topIps->count() > 0) {
            $this->line("🔥 Top Violating IPs:");
            $headers = ['IP Address', 'Violations', 'Authenticated Users'];
            $rows = $topIps->map(fn($i) => [
                $i->ip_address,
                $i->violation_count,
                $i->authenticated_users ?? 0,
            ])->toArray();
            $this->table($headers, $rows);
            $this->newLine();
        }

        // Show top users
        $topUsers = RateLimitViolation::recent($minutes)
            ->whereNotNull('user_id')
            ->selectRaw('user_id, COUNT(*) as violation_count, COUNT(DISTINCT endpoint) as unique_endpoints')
            ->groupBy('user_id')
            ->orderByRaw('COUNT(*) DESC')
            ->limit($top)
            ->get();

        if ($topUsers->count() > 0) {
            $this->line("👤 Top Violating Users:");
            $headers = ['User ID', 'Violations', 'Unique Endpoints'];
            $rows = $topUsers->map(fn($u) => [
                $u->user_id,
                $u->violation_count,
                $u->unique_endpoints,
            ])->toArray();
            $this->table($headers, $rows);
            $this->newLine();
        }

        // Alert for suspicious patterns
        $suspiciousIps = RateLimitViolation::recent($minutes)
            ->selectRaw('ip_address, COUNT(*) as violation_count')
            ->groupBy('ip_address')
            ->havingRaw('COUNT(*) > 50')
            ->get();

        if ($suspiciousIps->count() > 0) {
            $this->warn("\n⚠️  Suspicious Activity Detected:");
            foreach ($suspiciousIps as $ip) {
                $this->warn("  • IP {$ip->ip_address}: {$ip->violation_count} violations");
            }
        }

        return Command::SUCCESS;
    }
}
