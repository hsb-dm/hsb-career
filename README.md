# HSB HR Dashboard

An internal HR dashboard for managing job vacancies and applicants, built with Laravel, Filament, Livewire, Blade, and MySQL.

## Requirements

- PHP 8.4+
- Composer
- Node.js and npm
- Docker for a local MySQL instance, or an existing MySQL 8 installation

## Setup

```bash
composer install
npm install
cp .env.example .env
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

Development credentials:

```text
Email: admin@example.com
Password: password
```

These credentials are for development only and are created by the database seeder.

## Database

The default `.env.example` configuration uses MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hsb_jobs
DB_USERNAME=hsb_jobs
DB_PASSWORD=secret
```

`docker-compose.yml` runs MySQL 8.4 with the persistent `mysql-data` volume.

## Storage

CV files are stored on the `local` disk at `storage/app/private`, so they are not exposed through permanent public URLs.

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
