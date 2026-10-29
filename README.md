# HSB HR Dashboard

An internal HR dashboard for managing job vacancies and applicants, built with Laravel, Filament, Livewire, Blade, and PostgreSQL.

## Requirements

- PHP 8.3.36+, 8.4.27+, or 8.5.12+ within those minor releases
- Composer
- Node.js and npm
- Docker for a local PostgreSQL instance, or an existing PostgreSQL 17 installation

## Setup

```bash
composer install
npm install
cp .env.example .env
# Set INITIAL_ADMIN_PASSWORD in .env before seeding.
php artisan key:generate
docker compose up -d
php artisan migrate:fresh --seed
npm run build
php artisan serve
```

The dashboard is available at:

```text
http://127.0.0.1:8000/admin
```

For production deployment with Docker and Cloudflare Tunnel, see [PRODUCTION.md](PRODUCTION.md).

To share the local application through ngrok, use the compiled assets. Stop `npm run dev` or `php artisan dev`, then run:

```bash
npm run build
rm -f public/hot
```

Run the server and ngrok in separate terminals:

```bash
php artisan serve
```

```bash
ngrok http 8000
```

When `public/hot` exists, Laravel loads CSS and JavaScript from Vite at `localhost:5173`, which visitors cannot access through ngrok. The local application trusts HTTPS headers only from loopback proxies so generated asset URLs use `https://`.

Set `INITIAL_ADMIN_PASSWORD` in `.env` before running the seeder. It creates the admin account at `INITIAL_ADMIN_EMAIL` (default `hr@hsb.co.id`). The password is kept out of tracked files.

## Database

The default `.env.example` configuration uses PostgreSQL:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=hsb_jobs
DB_USERNAME=hsb_jobs
DB_PASSWORD=secret
```

`docker-compose.yml` runs PostgreSQL 17 with the persistent `postgres-data` volume. Existing MySQL data is not migrated automatically; keep the old volume until its data has been transferred and verified.

## Storage

CV files use the `local` disk at `storage/app/private` while any AWS S3 setting (`AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, or `AWS_BUCKET`) is empty. When all four are set, new CV uploads use the `s3` disk. Keep the bucket private.

Existing local CVs remain available to the admin panel. If S3 credentials are removed later, CVs stored on S3 cannot be read until access is restored.

The seeder creates one sample CV file at:

```text
storage/app/private/cvs/sample-cv.pdf
```

Some applicants intentionally reference missing sample paths to exercise the empty state. This application does not require `php artisan storage:link`.

## Features

- Filament admin panel at `/admin`
- Filament authentication
- Jobs resource:
  - create, view, edit, delete
  - duplicate job
  - active/inactive status toggle
  - search, filters, sorting, pagination
- Applicants resource:
  - view applicants
  - status quick actions
  - bulk update status
  - CV upload/download private storage
  - PDF preview when the file is available
  - salary formatting in Indonesian Rupiah
- Dashboard widgets:
  - total active jobs
  - total applicants
  - new applicants
  - applicants in interview
  - accepted applicants
  - applicants by status
  - recent applicants
  - applicants per job
- Seeder:
  - 10 jobs
  - 1 HR admin account
- Users resource:
  - add users and update their names, emails, or passwords

## Tests

```bash
php artisan test
composer test
```

`composer test` also runs Pint and PHPStan through the project scripts.

## Deliberately Not Included

- Public careers page
- Public application form
- Applicant login/profile
- REST API
- Email automation
- Interview scheduling
- AI CV screening
- Resume parsing
- Complex permissions
- Advanced reports
- CSV export
- Full audit log
- Multi-company or multi-tenant support

CSV export is excluded because it is not required by the dashboard workflow.
