<?php

namespace App\Console\Commands;

use App\Models\RateLimitViolation;
use Illuminate\Console\Command;

class CleanupRateLimitViolations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rateline:cleanup {--days=30 : Number of days to keep violation records}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old rate limit violation records from the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');

        // Calculate cutoff date
        $cutoffDate = now()->subDays($days);

        // Count records to be deleted
        $count = RateLimitViolation::where('created_at', '<', $cutoffDate)->count();

        if ($count === 0) {
            $this->info('No rate limit violation records to clean up.');
            return Command::SUCCESS;
        }

        $this->warn("Found {$count} record(s) older than {$days} day(s).");

        if (!$this->confirm('Do you want to delete these records?', true)) {
            $this->info('Cleanup cancelled.');
            return Command::SUCCESS;
        }

        // Delete old records
        RateLimitViolation::where('created_at', '<', $cutoffDate)->delete();

        $this->info("✓ Deleted {$count} rate limit violation record(s).");

        // Show statistics
        $stats = RateLimitViolation::getStatistics(1440); // Last 24 hours
        $this->info("Current violations (last 24h):");
        $this->info("  Total violations: {$stats['total_violations']}");
        $this->info("  Unique users: {$stats['unique_users']}");
        $this->info("  Unique IPs: {$stats['unique_ips']}");
        $this->info("  Unique endpoints: {$stats['unique_endpoints']}");

        return Command::SUCCESS;
    }
}
