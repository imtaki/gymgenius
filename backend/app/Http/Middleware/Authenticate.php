<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }

    /**
     * Handle an incoming request.
     */
    public function handle($request, $next, ...$guards)
    {
        // Only apply JWT validation for API requests
        if ($request->is('api/*')) {
            try {
                // Check if Authorization header is present
                if (! $request->hasHeader('Authorization')) {
                    return response()->json([
                        'error' => 'Unauthorized',
                        'message' => 'Missing access token. Please provide a valid JWT token in the Authorization header.'
                    ], 401);
                }

                // Extract and validate token
                $token = JWTAuth::getToken();
                
                if (! $token) {
                    return response()->json([
                        'error' => 'Unauthorized',
                        'message' => 'Invalid or missing access token format. Use: Authorization: Bearer YOUR_TOKEN'
                    ], 401);
                }

                // Attempt to authenticate the token
                $user = JWTAuth::authenticate($token);

                if (! $user) {
                    return response()->json([
                        'error' => 'Unauthorized',
                        'message' => 'Invalid access token. User not found.'
                    ], 401);
                }

            } catch (TokenExpiredException $e) {
                return response()->json([
                    'error' => 'Unauthorized',
                    'message' => 'Access token has expired. Please login again.'
                ], 401);

            } catch (TokenInvalidException $e) {
                return response()->json([
                    'error' => 'Unauthorized',
                    'message' => 'Access token is invalid. Please provide a valid token.'
                ], 401);

            } catch (JWTException $e) {
                return response()->json([
                    'error' => 'Unauthorized',
                    'message' => 'Token verification failed. ' . $e->getMessage()
                ], 401);

            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'Unauthorized',
                    'message' => 'Authentication failed. ' . $e->getMessage()
                ], 401);
            }
        }

        return $next($request);
    }
}
