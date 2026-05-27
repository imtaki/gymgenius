---
mode: 'agent'
description: 'Analyse test coverage gaps and write missing tests'
---

Analyse the test coverage for this project:

1. Run `php artisan test --coverage` and identify files below 80% coverage
2. Check for untested edge cases: empty inputs, unauthorized access, error states
3. Check for missing E2E tests for critical user journeys
4. Write tests to close the most important gaps

Priority order:
1. Security-critical paths (auth, authorization, input validation)
2. Business-critical paths (payments, data mutations)
3. Happy paths with no existing tests
4. Edge cases for existing features
