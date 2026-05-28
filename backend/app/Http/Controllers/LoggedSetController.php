<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLoggedSetRequest;
use App\Http\Resources\LoggedSetResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Workout;
use App\Models\LoggedSet;
use App\Services\LoggedSetService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoggedSetController extends Controller
{
    use ApiResponseTrait, AuthorizesRequests;

    public function __construct(
        private readonly LoggedSetService $service
    ) {}

    /**
     * Get logged sets for a specific workout
     */
    public function index(Workout $workout): JsonResponse
    {
        $this->authorize('view', $workout);
        $sets = $this->service->getByWorkout($workout->id);
        return $this->successResponse(
            LoggedSetResource::collection($sets)
        );
    }

    /**
     * Log a new set in a workout
     */
    public function store(Workout $workout, StoreLoggedSetRequest $request): JsonResponse
    {
        $this->authorize('update', $workout);
        $set = $this->service->create($workout->id, $request->toDto());
        return $this->createdResponse(
            LoggedSetResource::make($set),
            'Set logged successfully'
        );
    }

    /**
     * Get a specific logged set
     */
    public function show(LoggedSet $set): JsonResponse
    {
        $this->authorize('view', $set);
        $set = $this->service->getById($set->id);
        return $this->successResponse(
            LoggedSetResource::make($set)
        );
    }

    /**
     * Update a logged set
     */
    public function update(Request $request, LoggedSet $set): JsonResponse
    {
        $this->authorize('update', $set);
        
        $updateData = [];
        if ($request->has('reps')) {
            $updateData['reps'] = $request->integer('reps');
        }
        if ($request->has('weight')) {
            $updateData['weight'] = $request->float('weight');
        }
        if ($request->has('rpe')) {
            $updateData['rpe'] = $request->integer('rpe');
        }

        $updated = $this->service->update($set->id, $updateData);
        return $this->successResponse(
            LoggedSetResource::make($updated),
            'Set updated successfully'
        );
    }

    /**
     * Delete a logged set
     */
    public function destroy(LoggedSet $set): JsonResponse
    {
        $this->authorize('delete', $set);
        $this->service->delete($set->id);
        return $this->deletedResponse();
    }
}
