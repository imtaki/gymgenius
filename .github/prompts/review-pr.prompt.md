---
mode: 'agent'
description: 'Full code review of changed files in the current branch'
---

Review all changed files in this branch against the project's coding standards.

Check for:
- Security vulnerabilities (SQLi, XSS, auth bypass, exposed secrets)
- Laravel anti-patterns (N+1, inline validation, missing authorization)
- Next.js anti-patterns (unnecessary client components, useEffect for data fetching)
- Missing or insufficient tests
- Performance issues
- SOLID violations

Produce a structured review report with 🔴 Critical / 🟠 Major / 🟡 Minor / 🟢 Good sections.
End with a clear recommendation: BLOCK | REQUEST CHANGES | APPROVE WITH COMMENTS | APPROVE
