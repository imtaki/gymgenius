<?php

namespace App\Jobs;

use App\Models\RateLimitViolation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class LogRateLimitViolation implements ShouldQueue
{
    use Queueable;

    /**
     * The violation data to log.
     */
    protected array $data;

    /**
     * Create a new job instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Avoid logging duplicate violations within a short window
        $recentViolation = RateLimitViolation::where('identifier', $this->data['identifier'])
            ->where('endpoint', $this->data['endpoint'])
            ->where('created_at', '>=', now()->subSeconds(5))
            ->first();

        if ($recentViolation) {
            // Skip logging duplicate violations within 5 seconds
            return;
        }

        RateLimitViolation::create($this->data);
    }
}
