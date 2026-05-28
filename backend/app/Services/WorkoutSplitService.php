<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\CreateWorkoutSplitData;
use App\Data\UpdateWorkoutSplitData;
use App\Models\WorkoutSplit;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;

class WorkoutSplitService
{
    private const CACHE_TTL_SPLITS = 3600;

    /**
     * Get paginated workout splits for a user
     */
    public function paginate(int $userId, int $perPage = 20)
    {
        return Cache::tags(['splits'])->remember(
            "splits.user.$userId",
            self::CACHE_TTL_SPLITS,
            function () use ($userId, $perPage) {
                return WorkoutSplit::where('user_id', $userId)
                    ->with('exercises')
                    ->paginate($perPage);
            }
        );
    }

    /**
     * Get a single workout split by ID
     */
    public function getById(int $id): WorkoutSplit
    {
        return Cache::tags(['splits'])->remember(
            "split.$id",
            self::CACHE_TTL_SPLITS,
            function () use ($id) {
                return WorkoutSplit::with('exercises')
                    ->findOrFail($id);
            }
        );
    }

    /**
     * Create a new workout split
     */
    public function create(int $userId, CreateWorkoutSplitData $data): WorkoutSplit
    {
        $split = WorkoutSplit::create([
            'user_id' => $userId,
            'name' => $data->name,
            'description' => $data->description,
        ]);

        Cache::tags(['splits'])->flush();

        return $split;
    }

    /**
     * Update a workout split
     */
    public function update(int $id, UpdateWorkoutSplitData $data): WorkoutSplit
    {
        $split = WorkoutSplit::findOrFail($id);

        $updateData = [];
        if ($data->name !== null) {
            $updateData['name'] = $data->name;
        }
        if ($data->description !== null) {
            $updateData['description'] = $data->description;
        }

        if (!empty($updateData)) {
            $split->update($updateData);
        }

        Cache::tags(['splits'])->forget("split.$id");
        Cache::tags(['splits'])->forget("splits.user." . $split->user_id);

        return $split;
    }

    /**
     * Delete a workout split
     */
    public function delete(int $id): bool
    {
        $split = WorkoutSplit::findOrFail($id);
        $userId = $split->user_id;

        $result = $split->delete();

        Cache::tags(['splits'])->forget("split.$id");
        Cache::tags(['splits'])->forget("splits.user.$userId");

        return $result;
    }
}