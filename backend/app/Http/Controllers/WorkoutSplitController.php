<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWorkoutSplitRequest;
use App\Http\Requests\UpdateWorkoutSplitRequest;
use App\Http\Resources\WorkoutSplitResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\WorkoutSplit;
use App\Services\WorkoutSplitService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class WorkoutSplitController extends Controller
{
    use ApiResponseTrait, AuthorizesRequests;

    public function __construct(
        private readonly WorkoutSplitService $service
    ) {}

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', WorkoutSplit::class);
        $splits = $this->service->paginate(Auth::id());
        return $this->successResponse(
            WorkoutSplitResource::collection($splits),
            meta: ['pagination' => [
                'total' => $splits->total(),
                'per_page' => $splits->perPage(),
                'current_page' => $splits->currentPage(),
                'last_page' => $splits->lastPage(),
            ]]
        );
    }

    public function store(StoreWorkoutSplitRequest $request): JsonResponse
    {
        $this->authorize('create', WorkoutSplit::class);
        $split = $this->service->create(Auth::id(), $request->toDto());
        return $this->createdResponse(
            WorkoutSplitResource::make($split),
            'Workout split created successfully'
        );
    }

    public function show(WorkoutSplit $split): JsonResponse
    {
        $this->authorize('view', $split);
        $split = $this->service->getById($split->id);
        return $this->successResponse(
            WorkoutSplitResource::make($split)
        );
    }

    public function update(UpdateWorkoutSplitRequest $request, WorkoutSplit $split): JsonResponse
    {
        $this->authorize('update', $split);
        $updated = $this->service->update($split->id, $request->toDto());
        return $this->successResponse(
            WorkoutSplitResource::make($updated),
            'Workout split updated successfully'
        );
    }

    public function destroy(WorkoutSplit $split): JsonResponse
    {
        $this->authorize('delete', $split);
        $this->service->delete($split->id);
        return $this->deletedResponse();
    }
}
