---
description: >
  Activate when creating or modifying Laravel API endpoints, controllers, routes,
  Form Requests, API Resources, Sanctum auth, or middleware. Triggers on:
  "create endpoint", "add route", "API resource", "auth middleware", "form request".
---

# Laravel API Skill

## Route Registration
```php
// routes/api.php
Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('products', ProductController::class);
    // Nested: Route::apiResource('orders.items', OrderItemController::class)->shallow();
});
```

## Controller Checklist
- Constructor-inject the Service class (readonly)
- Call `$this->authorize()` as first line of each action
- Return `ApiResource::collection()` for lists, `new ApiResource()` for single items
- Use `201 Created` for store, `200 OK` for show/update, `204 No Content` for destroy

## Form Request Checklist
```php
public function authorize(): bool { return $this->user()->can('create', Product::class); }
public function rules(): array { return ['name' => ['required', 'string', 'max:255']]; }
public function messages(): array { return ['name.required' => 'Product name is required.']; }
```

## API Resource Checklist
```php
public function toArray(Request $request): array {
    return [
        'id' => $this->id,
        'name' => $this->name,
        'category' => new CategoryResource($this->whenLoaded('category')),
        'created_at' => $this->created_at->toIso8601String(),
    ];
}
```
