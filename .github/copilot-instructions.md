# Project Instructions — Laravel + Next.js Fullstack

## Stack
- **Backend**: Laravel 12, PHP 8.3, MySQL 8, Redis 7 (queues + cache)
- **Frontend**: Next.js 15 (App Router), TypeScript 5, Tailwind CSS 4
- **Auth**: Laravel Sanctum (SPA tokens)
- **API**: RESTful JSON — all responses via Laravel API Resources
- **Testing**: PHPUnit + Pest (backend), Vitest + Playwright (frontend)
- **CI/CD**: GitHub Actions

## Universal Rules (apply to all agents)
- Never commit secrets, credentials, or .env files
- All code must have corresponding tests before a PR is opened
- Follow conventional commits: `feat:`, `fix:`, `test:`, `ci:`, `refactor:`
- Branch naming: `feature/`, `fix/`, `test/`, `ci/`
- Every PR must pass all CI checks before merge

## Laravel Conventions
- PSR-12 coding style, PHP 8.3 features (enums, fibers, typed properties)
- Eloquent only — no raw DB queries unless performance-critical (document why)
- Form Requests for ALL validation — never inline `$request->validate()`
- API Resources for ALL responses — never return models directly
- Gates/Policies for authorization — never inline checks in controllers
- Repository pattern for complex data access
- Jobs + Queues (Redis driver) for anything async
- `Cache::tags()` + `Cache::remember()` for all caching strategies
- `paginate()` always — never `get()` on large datasets
- `DB::transaction()` for multi-step writes

## Next.js Conventions
- App Router exclusively — no Pages Router
- Server Components by default; Client Components only when interactivity is needed
- `'use client'` directive must be justified in a comment
- Zod for all input validation and API response typing
- TanStack Query for client-side data fetching and mutations
- Components must stay under 100 lines — split otherwise
- Barrel exports (`index.ts`) for feature folders

## Database
- Every migration must include a `down()` method
- Index all foreign keys and columns used in WHERE/ORDER clauses
- Soft deletes (`SoftDeletes`) where data retention matters
- Never store computed values that can be derived

## Redis
- Queues: three priority levels — `high`, `default`, `low`
- Cache TTLs must be defined as constants, never magic numbers
- Use `Cache::tags()` for grouped invalidation
