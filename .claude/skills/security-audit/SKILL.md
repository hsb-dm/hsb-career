
---
name: security-audit
description: Audit this Laravel/Filament app for security vulnerabilities and potential (hardening) issues, verify each finding has a real exploit path, then optionally fix them with regression tests. Use when asked to "security audit", "security review", "check for vulnerabilities", "harden", "pentest the code", "is this safe", or "fix security issues". Args - mode (audit|fix, default audit) and scope (diff|full|<path>, default diff if the branch has changes else full).
argument-hint: "[audit|fix] [diff|full|<path>]"
---

# Security audit & fix

Find real vulnerabilities and hardening gaps in this app, prove each one, and fix the ones the user approves. The stack is Laravel 13, Filament 5 (admin panel at `/admin`), Livewire 4, Pest, PostgreSQL, Docker. Public users are unauthenticated job applicants who upload CVs. Admins are HR staff who view applicant PII.

Arguments: `$ARGUMENTS`

- **mode**: `audit` (default) only reports. `fix` audits, then fixes the confirmed findings the user approves.
- **scope**: `diff` audits the changes on this branch. `full` audits the whole codebase. `<path>` audits one file or directory. If no scope is given, use `diff` when step 1 finds changes, otherwise `full`.

## 1. Scope

Work out which code you are auditing. `origin/HEAD` is often unset in this repo, so don't rely on it:

```bash
git fetch origin master 2>/dev/null
BASE=$(git merge-base HEAD origin/master 2>/dev/null || git merge-base HEAD master)
git diff --stat "$BASE"...HEAD; git status --short
```

Include uncommitted changes in a `diff` audit. For `diff` scope you still need to read the code around each change (callers, routes, policies, models), because most vulnerabilities live at the boundary between new code and existing code.

## 2. Map the attack surface

Before you hunt, build a short map. Read these files, don't guess:

- **Entry points**: `routes/web.php` (public apply and talent-pool POSTs, CV preview/download routes), every controller in `app/Http/Controllers/`, Livewire components, and Filament resources, pages, actions and widgets in `app/Filament/`.
- **AuthN/AuthZ**: `app/Providers/Filament/AdminPanelProvider.php`, `app/Models/User.php` (`canAccessPanel`, roles), policies in `app/Policies/` (if any), `config/auth.php`, and `bootstrap/app.php` (middleware, trusted proxies, exception handling).
- **Sensitive data**: models `Applicant`, `TalentPoolEntry` and `User` (`$fillable`/`$guarded`, `$hidden`, casts), plus `app/Support/CvStorage.php` and `config/filesystems.php`.
- **Outbound**: `app/Mail/*`, the mail views in `resources/views/`, `config/mail.php` and `config/services.php`.
- **Deploy**: `Dockerfile`, `compose.production.yaml`, `docker/`, `deploy.sh`, `.env*.example`, `PRODUCTION.md`, `.github/workflows/`, `config/session.php`, `config/app.php` and `config/logging.php`.

For every entry point, write down who can reach it (anonymous, any authenticated user, or admin), what input it takes, and what data or side effects it touches.

## 3. Hunt

Work through every category in [checklist.md](checklist.md) against the map. Also run the tooling:

```bash
composer audit                      # known CVEs in PHP deps
npm audit --omit=dev                # known CVEs in JS deps
php artisan route:list --except-vendor   # confirm middleware on each route
```

For `full` scope, run the hunt in parallel. If the session has workflow orchestration enabled (ultracode, or the user asked for a workflow), use the Workflow tool: one finder agent per checklist category, then 3 adversarial verifiers per finding (step 4), then a completeness critic that asks "which entry point or category wasn't checked?" Otherwise, spawn one Agent per group of related categories. Give each agent the attack-surface map and its checklist section, and ask for structured findings. For `diff` or `<path>` scope, do the hunt inline.

## 4. Verify: no unproven findings

Every candidate finding must survive an attempt to refute it. For each one:

1. **Trace the path** from an attacker-controlled input to the dangerous sink, through the actual code, including middleware, validation, policies, casts and framework defaults. If you find a guard that blocks the path, the finding is refuted.
2. **State the attacker and the precondition**: anonymous user, applicant, or authenticated admin. If exploiting it requires an admin account, say so. For a single-tenant HR panel that usually lowers the severity, but it doesn't erase it.
3. **Write a concrete reproduction**: the HTTP request, Livewire call or input, and what goes wrong. When you can, prove it with a failing Pest test (see step 6) or with `php artisan tinker --execute`.
4. **Classify it**:
   - **Confirmed vulnerability**: an exploit path exists today.
   - **Potential / hardening**: no exploit path today, but the issue is one change away, is defense in depth missing, or depends on deploy config you can't see (for example `APP_DEBUG` in prod). Label these honestly. Never inflate them into vulnerabilities.
   - **Refuted**: drop it, but list it in the report in one line so the user knows it was checked.

Severity: **Critical** means unauthenticated RCE, auth bypass, or a mass PII leak. **High** means authenticated privilege escalation, IDOR on CVs or PII, stored XSS in the admin panel, or SQL injection. **Medium** means CSRF on a state change, missing rate limits on sensitive actions, or information disclosure. **Low** covers hardening and headers.

Don't report: theoretical issues in vendor code without a reachable path, missing features (such as 2FA) presented as vulnerabilities, or DoS through brute volume.

## 5. Report

Lead with a summary line, for example "2 confirmed (1 High, 1 Medium), 4 hardening, 7 refuted". Then give a table:

| # | Severity | Class | Location | Issue | Exploit / precondition | Fix |
|---|---|---|---|---|---|---|

Use markdown links `[file.php:42](app/...#L42)` for locations. Write the exploit column in plain words. For example: "anonymous user uploads `cv.html` with `<script>`, admin opens the preview, the script runs in the admin session". If the scope was capped or a category was skipped, say so.

In `audit` mode, stop here and offer to fix specific findings.

## 6. Fix (only in `fix` mode, or once the user approves)

Fix confirmed findings first, ordered by severity, then the hardening items the user picks. For each finding:

1. **Write a regression test first** in `tests/Feature/` in Pest style, matching the existing tests (see `tests/Feature/VacancyApplicationTest.php` and `UserManagementTest.php`). Run it and confirm it fails for the right reason.
2. **Make the smallest correct fix**, using the framework mechanism rather than ad-hoc checks: Form Request or `validate()` rules, policies or `canAccessPanel`/`canViewAny`, `$fillable`, Eloquent bindings, `e()`/`{{ }}`, `Storage` with server-generated paths, `throttle`, signed routes, and the `Content-Disposition: attachment` and `X-Content-Type-Options: nosniff` headers. Match the surrounding code style.
3. **Rerun the test** and confirm it passes.
4. Move on to the next finding.

After all fixes, run the full gate and report the real output:

```bash
vendor/bin/pint --parallel --test
php -d memory_limit=1G vendor/bin/phpstan analyse
php artisan test
```

Rules while fixing:

- **Never** read, print or edit a real `.env`, and never paste secret values into output. If a secret is committed (check with `git log -p -S` on the key name), the fix is to **rotate** it. Tell the user that, because deleting the secret from the code doesn't revoke it.
- Don't change migrations that have already run. Add a new migration instead.
- Deploy config changes (Dockerfile, compose, nginx/php config) can break production. Show the diff and explain the impact before applying.
- Don't commit or push unless the user asks.
- If a fix needs a product decision (for example, who may download CVs, or how long to keep applicant data), stop and ask instead of picking for the user.

Finish with a list of what was fixed, what was skipped and why, the test and phpstan results, and any manual follow-ups (secret rotation, prod env settings, infrastructure changes).
