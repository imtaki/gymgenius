# Pagination Implementation Example

This file shows how to update controllers to use the new pagination methods from refactored services.

## Example 1: ExerciseController — Pagination Support

**Before** (unbounded):
```php
public function index(): JsonResponse
{
    $exercises = $this->exerciseService->getExercisesByUser(Auth::id());
    return $this->successResponse(
        ExerciseResource::collection($exercises)
    );
}
```

**After** (with pagination support):
```php
use Illuminate\Http\Request;

public function index(Request $request): JsonResponse
{
    // Validate and constrain per_page parameter
    $perPage = min(
        $request->integer('per_page', config('query.pagination.exercises')),
        config('query.pagination.max_per_page')
    );

    // Get paginated results
    $exercises = $this->exerciseService->getExercisesByUserPaginated(
        Auth::id(),
        $perPage
    );

    return $this->successResponse(
        ExerciseResource::collection($exercises)
    );
}
```

**API Usage:**
```bash
# Default pagination
GET /api/v1/exercises

# Custom page size
GET /api/v1/exercises?per_page=50

# Next page
GET /api/v1/exercises?page=2&per_page=20

# Cursor pagination (keyset-based)
GET /api/v1/exercises?cursor=eyJpZCI6OTksIl9wb2ludHNUb1d..."
```

---

## Example 2: WorkoutLogController — Date Range with Pagination

**Before** (unbounded get):
```php
public function getByDateRange(Request $request): JsonResponse
{
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    
    $logs = $this->workoutLogService->getWorkoutLogsByDateRange(
        auth()->user(),
        $startDate,
        $endDate
    );
    
    return $this->successResponse(ExerciseResource::collection($logs));
}
```

**After** (paginated with validation):
```php
use Illuminate\Validation\ValidationException;

public function getByDateRange(Request $request): JsonResponse
{
    // Validate date range
    $validated = $request->validate([
        'start_date' => 'required|date',
        'end_date' => 'required|date|after:start_date',
        'per_page' => 'integer|min:5|max:' . config('query.pagination.max_per_page'),
    ]);

    $perPage = $validated['per_page'] ?? config('query.pagination.workout_logs');

    // Use paginated method
    $logs = $this->workoutLogService->getWorkoutLogsByDateRangePaginated(
        auth()->user(),
        $validated['start_date'],
        $validated['end_date'],
        $perPage
    );

    return $this->successResponse(WorkoutLogResource::collection($logs));
}
```

---

## Example 3: Cursor Pagination (Large Datasets)

For very large datasets or mobile apps with incremental scrolling:

```php
public function indexCursor(Request $request): JsonResponse
{
    // Cursor pagination doesn't support per_page > max
    $perPage = min(
        $request->integer('per_page', config('query.cursor.default_per_page')),
        config('query.cursor.max_per_page')
    );

    $exercises = $this->exerciseService->getExercisesByUserCursor(
        Auth::id(),
        $perPage
    );

    return $this->successResponse([
        'data' => ExerciseResource::collection($exercises),
        'next_page_url' => $exercises->nextPageUrl(),
        'path' => $exercises->path(),
    ]);
}
```

**API Usage (Cursor):**
```bash
# First page
GET /api/v1/exercises/cursor?per_page=50

# Next page (client gets cursor from response.next_page_url)
GET /api/v1/exercises/cursor?cursor=eyJpZCI6OTksIl9wb2ludHNUb1d..."
```

---

## Example 4: Chunking for Batch Operations (Non-API)

For background jobs or bulk exports, use chunking:

```php
// In a Job or Command
use App\Services\ExerciseService;

class ExportExercisesJob implements ShouldQueue
{
    public function handle(ExerciseService $service)
    {
        $service->processAllUserExercises(auth()->id(), function ($exercise) {
            // Export one exercise
            $this->exportExercise($exercise);
        });
    }
}
```

Service method:
```php
public function processAllUserExercises(int $userId, callable $callback)
{
    Exercise::where('user_id', $userId)
        ->chunk(config('query.limits.chunk_size'), function ($exercises) use ($callback) {
            foreach ($exercises as $exercise) {
                $callback($exercise);
            }
        });
}
```

---

## Response Format

All paginated endpoints return Laravel's standard pagination structure:

```json
{
  "data": [
    {
      "id": 1,
      "name": "Barbell Squat",
      "created_at": "2026-05-28T10:00:00Z"
    },
    ...
  ],
  "links": {
    "first": "https://api.example.com/api/v1/exercises?page=1",
    "last": "https://api.example.com/api/v1/exercises?page=5",
    "prev": "https://api.example.com/api/v1/exercises?page=1",
    "next": "https://api.example.com/api/v1/exercises?page=3"
  },
  "meta": {
    "current_page": 2,
    "from": 21,
    "last_page": 5,
    "path": "https://api.example.com/api/v1/exercises",
    "per_page": 20,
    "to": 40,
    "total": 100
  }
}
```

---

## Cursor Pagination Response

```json
{
  "data": [
    { "id": 50, "name": "Deadlift", ... },
    ...
  ],
  "links": {
    "first": "https://api.example.com/api/v1/exercises?cursor=...",
    "last": null,
    "prev": "https://api.example.com/api/v1/exercises?cursor=...",
    "next": "https://api.example.com/api/v1/exercises?cursor=eyJpZCI6NTAsIl9wb..."
  },
  "path": "https://api.example.com/api/v1/exercises"
}
```

---

## Migration Checklist

- [ ] Update `ExerciseController::index()` to call `getExercisesByUserPaginated()`
- [ ] Update `WorkoutLogController::getByDateRange()` with pagination support
- [ ] Add validation for `per_page` parameter (max from config)
- [ ] Test with various page sizes: 5, 20, 50, 100, 150 (should constrain to max)
- [ ] Add cursor pagination example in docs
- [ ] Update OpenAPI spec with pagination parameters and responses
- [ ] Test pagination with 1000+ records
- [ ] Verify indexes are helping (use `EXPLAIN` on queries)

---

## Testing Example

```php
// tests/Feature/ExerciseTest.php

public function test_exercises_index_paginated()
{
    // Create 50 exercises for user
    $user = User::factory()->create();
    Exercise::factory(50)->create(['user_id' => $user->id]);

    // Default pagination (20 per page)
    $response = $this->actingAs($user)->getJson('/api/v1/exercises');
    $response->assertStatus(200)
        ->assertJsonCount(20, 'data')
        ->assertJsonPath('meta.total', 50)
        ->assertJsonPath('meta.per_page', 20);

    // Custom page size (50)
    $response = $this->actingAs($user)->getJson('/api/v1/exercises?per_page=50');
    $response->assertJsonCount(50, 'data');

    // Page 2
    $response = $this->actingAs($user)->getJson('/api/v1/exercises?page=2');
    $response->assertJsonCount(20, 'data')
        ->assertJsonPath('meta.current_page', 2);

    // Max page size capped at config
    $maxAllowed = config('query.pagination.max_per_page');
    $response = $this->actingAs($user)->getJson("/api/v1/exercises?per_page=999");
    $response->assertJsonPath('meta.per_page', $maxAllowed);
}
```

