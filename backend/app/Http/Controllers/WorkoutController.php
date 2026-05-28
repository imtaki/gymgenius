<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWorkoutRequest;
use App\Http\Requests\UpdateWorkoutRequest;
use App\Http\Resources\WorkoutResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Workout;
use App\Services\WorkoutService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class WorkoutController extends Controller
{
    use ApiResponseTrait, AuthorizesRequests;

    public function __construct(
        private readonly WorkoutService $service
    ) {}

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Workout::class);
        $workouts = $this->service->paginate(Auth::id());
        return $this->successResponse(
            WorkoutResource::collection($workouts),
            meta: ['pagination' => [
                'total' => $workouts->total(),
                'per_page' => $workouts->perPage(),
                'current_page' => $workouts->currentPage(),
                'last_page' => $workouts->lastPage(),
            ]]
        );
    }

    public function store(StoreWorkoutRequest $request): JsonResponse
    {
        $this->authorize('create', Workout::class);
        $workout = $this->service->create(Auth::id(), $request->toDto());
        return $this->createdResponse(
            WorkoutResource::make($workout),
            'Workout created successfully'
        );
    }

    public function show(Workout $workout): JsonResponse
    {
        $this->authorize('view', $workout);
        $workout = $this->service->getById($workout->id);
        return $this->successResponse(
            WorkoutResource::make($workout)
        );
    }

    public function update(UpdateWorkoutRequest $request, Workout $workout): JsonResponse
    {
        $this->authorize('update', $workout);
        $updated = $this->service->update($workout->id, $request->toDto());
        return $this->successResponse(
            WorkoutResource::make($updated),
            'Workout updated successfully'
        );
    }

    public function destroy(Workout $workout): JsonResponse
    {
        $this->authorize('delete', $workout);
        $this->service->delete($workout->id);
        return $this->deletedResponse();
    }
}
