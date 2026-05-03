<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Requests\UserSettingsRequest;
use App\Http\Resources\UserSettingsResource;
use App\Models\UserSettings;
use App\Models\User;
use App\Services\UserSettingsService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;

class UserSettingsController extends Controller
{
    use ApiResponseTrait, AuthorizesRequests;

    public function __construct(private readonly UserSettingsService $userSettingsService)
    {
    }

    public function index($userId): JsonResponse
    {
        $this->authorize('view', [UserSettings::class, $userId]);
        $settings = UserSettings::where('user_id', $userId)->first();
        return $this->successResponse(new UserSettingsResource($settings));
    }

    public function update(UserSettingsRequest $request, $userId): JsonResponse
    {
        $this->authorize('update', [UserSettings::class, $userId]);

        $settings = $this->userSettingsService->updateSettings($userId, $request->validated());
        return $this->successResponse(new UserSettingsResource($settings), 'Settings updated successfully');
    }
}
