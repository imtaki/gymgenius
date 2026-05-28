<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use App\Jobs\SendRateLimitedEmail;
use App\Http\Requests\Auth\RegisterRequest;
use App\Enums\UserType;
use App\Http\Resources\UserResource;
use App\Http\Traits\ApiResponseTrait;

class AuthController extends Controller
{
    use ApiResponseTrait;
    public function register(RegisterRequest $request)
    {
        $credentials = $request->validated();

        try {
            $user = User::create([
                'name' => $credentials['name'],
                'email' => $credentials['email'],
                'password' => bcrypt($credentials['password']),
                'role' => UserType::USER,
                'is_verified' => false
            ]);

            $code = random_int(10000, 99999);

            Cache::put('verification_code_' . $user->email, $code, now()->addMinutes(10));

            $token = JWTAuth::fromUser($user);

            SendRateLimitedEmail::dispatch($user, $code);

        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        return response()->json([
            'message' => 'User registered successfully',
            'user' => new UserResource($user),
            'token' => $token
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return $this->errorResponse('invalid_credentials', 401);
            }
        } catch (JWTException $e) {
            return $this->errorResponse('could_not_create_token', 500);
        }

        $user = auth()->user();

        return response()->json([
            'message' => 'Login successful',
            'id' => $user->id,
            'token' => $token
        ]);
    }

    public function logout(Request $request)
    {
        try {
            $user = auth()->user();

            if($user) {
                Cache::forget('user_profile_' . $user->id);
            }

            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json(['message' => 'Successfully logged out']);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Failed to logout, token invalid'], 500);
        }
    }

    public function getUser(Request $request)
    {
        try {
            $payload = JWTAuth::parseToken()->getPayload();
            $userId = $payload->get('sub');

            $user = Cache::remember('user_profile_' . $userId, 3600, function () {
                return JWTAuth::parseToken()->authenticate();
            });

            return response()->json([
                'success' => true,
                'data' => ['user' => new UserResource($user)]
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Token is invalid or expired'], 401);
        }
    }

    public function verifyEmail(Request $request)
    {
        $email = $request->input('email');
        $code = $request->input('code');

        if (! $email || ! $code) {
            return response()->json(['error' => 'Email and code are required.'], 422);
        }

        $cachedCode = Cache::get('verification_code_' . $email);

        if (!$cachedCode) {
            return response()->json(['error' => 'Verification code expired or not found.'], 404);
        }

        if ((string)$cachedCode === (string)$code) {
            $user = User::where('email', $email)->first();

            if ($user) {
                $user->is_verified = true;
                $user->save();

                Cache::forget('verification_code_' . $email);

                return response()->json(['message' => 'Email verified successfully.'], 200);
            }
        }

        return response()->json(['error' => 'Invalid verification code.'], 400);
    }
}
