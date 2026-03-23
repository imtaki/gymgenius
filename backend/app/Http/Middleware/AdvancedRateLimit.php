<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Cache\RateLimiter;

class AdvancedRateLimit
{
    /**
     * The rate limiter instance.
     *
     * @var \Illuminate\Cache\RateLimiter
     */
    protected $limiter;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Cache\RateLimiter  $limiter
     * @return void
     */
    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if rate limiting is enabled
        if (!config('ratelimit.enabled')) {
            return $next($request);
        }

        // Check whitelist
        if ($this->isWhitelisted($request)) {
            return $next($request);
        }

        // Get rate limit key
        $key = $this->getKey($request);

        // Get limit and window
        list($limit, $window) = $this->getLimitAndWindow($request);

        // Check if limit is exceeded
        if ($this->limiter->tooManyAttempts($key, $limit, $window)) {
            return $this->buildResponse($request, $key);
        }

        // Hit the rate limiter
        $this->limiter->hit($key, $window * 60);

        // Continue to next middleware
        $response = $next($request);

        // Add rate limit headers if enabled
        if (config('ratelimit.include_headers')) {
            $response->headers->set('X-RateLimit-Limit', $limit);
            $response->headers->set('X-RateLimit-Remaining', $this->limiter->remaining($key, $limit));
            $response->headers->set('X-RateLimit-Reset', $this->limiter->resetAfter($key));
        }

        return $response;
    }

    /**
     * Get the rate limit key for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function getKey(Request $request): string
    {
        // For authenticated requests, use user ID
        if ($request->user()) {
            return 'rate-limit:' . $request->user()->id;
        }

        // For public requests, use IP address
        return 'rate-limit:' . $request->ip();
    }

    /**
     * Get the limit and window for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array [limit, window_in_minutes]
     */
    protected function getLimitAndWindow(Request $request): array
    {
        // Default limits
        $limit = 60;
        $window = 1;

        // Check if in whitelist
        if ($this->isWhitelisted($request)) {
            return [999999, 1]; // Essentially unlimited
        }

        // Public authentication endpoints
        if ($request->is('api/login') || $request->is('api/register') || $request->is('api/verify-email')) {
            list($limit, $window) = sscanf(config('ratelimit.limits.auth_public'), '%d,%d');
        }
        // Authenticated endpoints
        elseif ($request->user()) {
            // Write operations (POST, PUT, DELETE)
            if (in_array($request->method(), ['POST', 'PUT', 'DELETE', 'PATCH'])) {
                list($limit, $window) = sscanf(config('ratelimit.limits.write'), '%d,%d');
            }
            // Read operations (GET)
            else {
                list($limit, $window) = sscanf(config('ratelimit.limits.read'), '%d,%d');
            }
        }

        return [$limit, $window];
    }

    /**
     * Check if the request is from a whitelisted IP or user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function isWhitelisted(Request $request): bool
    {
        $whitelist = config('ratelimit.whitelist', []);

        if (empty($whitelist)) {
            return false;
        }

        // Check IP whitelist
        if (in_array($request->ip(), $whitelist)) {
            return true;
        }

        // Check user ID whitelist
        if ($request->user() && in_array((string)$request->user()->id, $whitelist)) {
            return true;
        }

        return false;
    }

    /**
     * Build a rate limit exceeded response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $key
     * @return \Illuminate\Http\Response
     */
    protected function buildResponse(Request $request, string $key)
    {
        if ($request->expectsJson()) {
            $retryAfter = $this->limiter->resetAfter($key);
            return response()->json([
                'success' => false,
                'message' => 'Too many requests. Please try again later.',
                'retry_after' => $retryAfter,
            ], 429, [
                'Retry-After' => $retryAfter,
            ]);
        }

        return response('Too Many Requests', 429);
    }
}
