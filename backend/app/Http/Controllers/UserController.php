<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Requests\UpdateSubscriptionRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private readonly UserService $userService)
    {
    }

    /**
     * Get User data and for admin dashboard, Cached, User count + Meal logs count
     */
    public function indexUserData(): JsonResponse
    {
        return $this->successResponse($this->userService->getUserDataCount());
    }

    /**
     * Get Recent users, SELECT id, name, email, created_at FROM users ORDER BY created_at DESC LIMIT 5
     */
    public function indexRecentUsers(): JsonResponse
    {
        return $this->successResponse($this->userService->getRecentUsers());
    }

    /**
     * Update the authenticated user's subscription tier
     */
    public function updateSubscription(UpdateSubscriptionRequest $request): UserResource
    {
        $user = Auth::user();
        $user->update(['subscription_tier' => $request->validated('tier')]);
        return new UserResource($user);
    }
}