---
mode: 'agent'
description: 'Refactor existing code for readability, SOLID principles, and Laravel/Next.js best practices without changing behaviour'
---

Refactor the following file or area of the codebase: ${input:File path or describe what to refactor}

## Rules
- Do NOT change any external behaviour — inputs, outputs, and side effects must stay identical
- Run existing tests after each change to confirm nothing breaks
- Commit in small atomic steps (one concern per change)

## Refactor checklist — Laravel
- [ ] Extract business logic out of controllers into Service classes
- [ ] Replace inline `$request->validate()` with Form Request classes
- [ ] Replace raw model returns with API Resources
- [ ] Break God methods (>20 lines) into smaller private methods with descriptive names
- [ ] Replace magic numbers/strings with named constants or enums
- [ ] Add missing type hints and return types (PHP 8.3 style)
- [ ] Remove dead code, commented-out blocks, unused imports
- [ ] Replace `get()` with `paginate()` on collections that could grow
- [ ] Add `with()` eager loading where N+1 queries exist
- [ ] Move repeated query logic into a Repository or query scope

## Refactor checklist — Next.js / TypeScript
- [ ] Split components over 100 lines into smaller focused components
- [ ] Remove `'use client'` from components that don't need interactivity
- [ ] Replace `useEffect` data fetching with Server Components or TanStack Query
- [ ] Extract repeated logic into custom hooks
- [ ] Replace `any` types with proper TypeScript interfaces
- [ ] Add Zod schemas where API responses are used without validation
- [ ] Replace magic strings with TypeScript enums or const objects
- [ ] Remove unused imports and dead code

## Output format
For each change made, produce a one-line summary:
`[file:line] What was changed → Why it's better`

Then confirm: "All existing tests still pass: ✅ / ❌"
