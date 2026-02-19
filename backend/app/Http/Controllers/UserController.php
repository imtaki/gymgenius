<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\UserService;


class UserController extends Controller
{
    public function __construct(UserService $userService) 
    {
        $this->userService = $userService;
    }
    
    /**
     * Get User data and for admin dashboard, Cached, User count + Meal logs count
     */
    public function indexUserData()
    {
        return response()->json($this->userService->getUserDataCount(), 200);
    }

    /**
     * Get Recent users, SELECT id, name, email, created_at FROM users ORDER BY created_at DESC LIMIT 5
     * TODO: Implement this
     */
        public function indexRecentUsers()
        {
            return response()->json($this->userService->getRecentUsers(), 200);
        }


}