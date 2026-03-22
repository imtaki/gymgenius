<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

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
}