<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Requests\UserSettingsRequest;
use App\Models\UserSettings;
use App\Models\User;
use App\Services\UserSettingsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class UserSettingsController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private readonly UserSettingsService $userSettingsService)
    {
    }

    public function index($userId): JsonResponse
    {
        Gate::authorize('view', [UserSettings::class, $userId]);
        $settings = UserSettings::where('user_id', $userId)->first();
        return $this->successResponse($settings);
    }

    public function update(UserSettingsRequest $request, $userId): JsonResponse
    {
        $user = User::findOrFail($userId);
        Gate::authorize('update', $user);

        $settings = $this->userSettingsService->updateSettings($request, $user);
        if (!$settings["success"]) {
            return $this->errorResponse($settings["message"], 401);
        }
        return $this->successResponse($settings, $settings['message']);
    }
}
