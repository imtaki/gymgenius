<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $credentials = $request->only('name', 'email', 'password');

        try {
            $user = User::create([
                'name' => $credentials['name'],
                'email' => $credentials['email'],
                'password' => bcrypt($credentials['password']),
                'role' => 'user'
            ]);

            $code = random_int(10000, 99999);
            $user->verification_code = (string)$code;
            $user->is_verified = false;
            $user->save();

            $token = JWTAuth::fromUser($user);


            Mail::to($user->email)->send(new WelcomeMail($user, $code));

        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user
        ])->cookie(
            'jwt_token',
            $token,
            60 * 24,           // 1 day
            '/',
            null,
            true,              // Secure
            true,              // HttpOnly
            false,
            'strict'          // SameSite
        );

    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        $user = auth()->user();

        $response = response()->json([
            'message' => 'Login successful',
            'user' => $user
       ]);

        $response->cookie(
            'jwt_token',
            $token,
            60 * 24,           // 1 day
            '/',
            null,
            true,              // Secure
            true,              // HttpOnly
            false,
            'strict'          // SameSite
        );

        $response->cookie(
            'role',
            $user->role,
            config('jwt.ttl'),
            '/',
            null,
            true,
            true,
            false,
            'strict'
        );

        return $response;
    }

    public function logout(Request $request)
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json([
                'message' => 'Successfully logged out'
            ])->cookie(
                'jwt_token',
                null,
                -1,                // Negative expiration to delete cookie
                '/',
                null,
                true,
                true,
                false,
                'strict'
            );
    }

    public function getUser(Request $request)
    {
        return response()->json($request->user());
    }


    public function verifyEmail(Request $request)
    {
        $email = $request->input('email');
        $code = $request->input('code');

        if (! $email || ! $code) {
            return response()->json(['error' => 'Email and code are required.'], 422);
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        if ($user->is_verified) {
            return response()->json(['message' => 'Email already verified.'], 200);
        }

        if ($user->verification_code === (string)$code) {
            $user->is_verified = true;
            $user->verification_code = null;
            $user->save();

            return response()->json(['message' => 'Email verified successfully.'], 200);
        } else {
            return response()->json(['error' => 'Invalid verification code.'], 400);
        }
    }


}