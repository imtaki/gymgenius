<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UserSettingsRequest;
use App\Models\UserSettings;
use App\Services\UserSettingsService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class UserSettingsController extends Controller
{

    protected UserSettingsService $userSettingsService;

    public function __construct(UserSettingsService $userSettingsService) {
        $this->userSettingsService = $userSettingsService;
    }

    public function index($userId)
    {
        return response()->json(UserSettings::where('user_id', $userId)->first(), 200);
    }

    public function store(UserSettingsRequest $request) {
        Gate::authorize('create', UserSettings::class);
        $settings = $this->userSettingsService->createSettings($request);
        return response()->json(['message' => $settings['message'], 'data' => $settings]);
    }

    public function update(UserSettingsRequest $request, User $user) {
        Gate::authorize('update', $user);
        $settings = $this->userSettingsService->updateSettings($request, $user);
        if (!$settings["success"]) {
            return response()->json(['error' => $settings["message"]], 401);
        }
        return response()->json(['message' => $settings['message']]);
    }

}
