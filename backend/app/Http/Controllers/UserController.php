<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;


class UserController extends Controller
{
    public function __construct(UserService $userService) 
    {
        $this->userService = $userService;
    }
    
    /**
     * Get User count and for admin dashboard, Cached
     */
    public function indexUserCount()
    {
        return response()->json([
            'user_count' => $this->userService->getUserCount(),
        ], 200);
    }

     /**
     * Get meal logs count and for admin dashboard, Cached
     */
    public function indexMealLogs() 
    {
        return response()->json([
            'meal_logs_count' => $this->userService->getUserMealLogsCount(),
        ], 200);
    }
}