---
mode: 'agent'
description: 'Run a full static analysis pass across the codebase — find bugs, smells, security issues, and tech debt without touching any files'
---

Perform a full **read-only** static analysis of the codebase. Do NOT edit any files.
Produce a prioritised tech debt report I can act on.

## Step 1 — Laravel Static Analysis
Run these and report all findings:
```bash
vendor/bin/phpstan analyse --level=8
vendor/bin/php-cs-fixer fix --dry-run --diff
composer audit
```

## Step 2 — Next.js Static Analysis
```bash
cd frontend && npm run typecheck
cd frontend && npm run lint
cd frontend && npm audit --audit-level=moderate
```

## Step 3 — Manual Pattern Scan
Search the codebase for these anti-patterns and list every occurrence:

**Laravel**
- `env(` called directly in app code (should be `config(`)
- `DB::statement(` or `DB::select(` with string interpolation (SQL injection risk)
- `->get()` without pagination on models with potentially large datasets
- Missing `$fillable` or `$guarded` on Eloquent models
- `Log::info(` or `Log::debug(` containing user data, passwords, or tokens
- Controllers with more than 5 public methods (God controller)
- `sleep(` or `usleep(` in non-test code (blocking queue workers)

**Next.js / TypeScript**
- `any` type usage
- `// @ts-ignore` or `// @ts-nocheck`
- `useEffect` with an API fetch inside
- `localStorage` or `sessionStorage` without SSR guard
- `console.log(` left in production code
- Missing `alt` attributes on `<img>` tags
- `key={index}` in `.map()` calls

## Output Format

Produce a report with these sections:

### 🔴 Security Issues (fix immediately)
| File | Line | Issue | Fix |
|------|------|-------|-----|

### 🟠 Bugs & Breaking Risk
| File | Line | Issue | Fix |
|------|------|-------|-----|

### 🟡 Tech Debt & Code Smells
| File | Line | Issue | Effort |
|------|------|-------|--------|

### 📊 Summary
- Total issues: X (X critical, X major, X minor)
- Most problematic file: ...
- Estimated cleanup effort: ...

### 🗺️ Recommended Fix Order
1. ...
2. ...
