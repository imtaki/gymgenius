<?php

namespace App\Services;
use App\Models\User;
use App\Models\UserSettings;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserSettingsService {

    public function getSettingsByUser($userId) {
        try {
            $user = User::findOrFail($userId); // Fixed capitalization
            return $user->settings;
        } catch (ModelNotFoundException $e) {
            throw $e; // Let controller handle the response, don't return response from service
        }
    }

    public function updateSettings($userId, $data) {
        try {
            $settings = UserSettings::where('user_id', $userId)->firstOrFail();
            $settings->update($data);
            return $settings->fresh(); // Return fresh data from DB
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException("Settings not found for User ID: {$userId}");
        } catch (\Exception $e) {
            throw new \Exception("Failed to update settings: {$e->getMessage()}");
        }
    }

    public function createSettings($userId, $data): UserSettings
    {
        try {
            $user = User::findOrFail($userId);
            
            // Check if settings already exist
            if ($user->settings) {
                throw new \Exception("Settings already exist for this user. Use update instead.");
            }
            
            return $user->settings()->create($data);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException("User not found with ID: {$userId}");
        } catch (\Exception $e) {
            throw new \Exception("Failed to create settings: {$e->getMessage()}");
        }
    }
}