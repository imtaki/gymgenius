---
description: >
  Activate when modifying CI/CD pipelines, GitHub Actions workflows, Docker configs,
  or deployment scripts. Triggers on: "CI", "pipeline", "workflow", "deploy",
  "GitHub Actions", "Docker", "build", "staging", "production".
---

# CI/CD Skill

## Caching in Pipelines (Always Apply)
```yaml
- uses: actions/cache@v4
  with:
    path: vendor
    key: composer-${{ hashFiles('**/composer.lock') }}
    restore-keys: composer-

- uses: actions/cache@v4
  with:
    path: frontend/.next/cache
    key: nextjs-${{ hashFiles('frontend/package-lock.json') }}-${{ hashFiles('frontend/**/*.ts') }}
```

## Service Containers for Tests
```yaml
services:
  mysql:
    image: mysql:8.0
    env: { MYSQL_ROOT_PASSWORD: secret, MYSQL_DATABASE: testing }
    ports: ['3306:3306']
    options: --health-cmd="mysqladmin ping" --health-interval=10s --health-retries=3
  redis:
    image: redis:7-alpine
    ports: ['6379:6379']
    options: --health-cmd="redis-cli ping" --health-interval=10s --health-retries=3
```

## Concurrency — Cancel Stale Runs
```yaml
concurrency:
  group: ${{ github.workflow }}-${{ github.ref }}
  cancel-in-progress: true
```

## Parallel Test Execution
```bash
php artisan test --parallel --coverage --min=80
```

## Environment Setup Order
```bash
cp .env.ci .env && php artisan key:generate && php artisan migrate --force
```
