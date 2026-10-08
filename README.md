# HSB HR Dashboard

MVP dashboard internal untuk HR mengelola lowongan dan applicant menggunakan Laravel, Filament, Livewire, Blade, dan MySQL.

## Requirements

- PHP 8.4+
- Composer
- Node.js dan npm
- Docker untuk MySQL lokal, atau MySQL 8 yang sudah tersedia

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

Dashboard tersedia di:

```text
http://127.0.0.1:8000/admin
```

Untuk deployment Docker production melalui Cloudflare Tunnel, lihat [PRODUCTION.md](PRODUCTION.md).

Untuk membagikan aplikasi lokal melalui ngrok, gunakan asset hasil build. Hentikan `npm run dev` / `php artisan dev`, lalu jalankan:

```bash
npm run build
rm -f public/hot
```

Jalankan server dan ngrok di dua terminal terpisah:

```bash
php artisan serve
```

```bash
ngrok http 8000
```

File `public/hot` membuat Laravel mengambil CSS/JS dari Vite di `localhost:5173`, yang tidak dapat diakses pengunjung melalui ngrok. Aplikasi lokal mempercayai header HTTPS hanya dari proxy loopback agar URL asset yang dihasilkan memakai `https://`.

Development credentials:

```text
Email: admin@example.com
Password: password
```

Credential ini hanya untuk development dan dibuat melalui database seeder.

## Database

Default `.env.example` menggunakan MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hsb_jobs
DB_USERNAME=hsb_jobs
DB_PASSWORD=secret
```

`docker-compose.yml` hanya menjalankan MySQL 8.4 dengan persistent volume `mysql-data`.

## Storage

CV disimpan di disk `local`, yaitu `storage/app/private`, sehingga file tidak diekspos sebagai public URL permanen.

Seeder menyediakan satu dummy CV fisik di:

```text
storage/app/private/cvs/sample-cv.pdf
```

Beberapa applicant sengaja memakai dummy path yang tidak ada untuk menguji empty state. Tidak perlu menjalankan `php artisan storage:link` untuk MVP ini.

## Features

- Filament admin panel di `/admin`
- Authentication bawaan Filament
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
  - PDF preview jika file tersedia
  - salary formatting sebagai Rupiah
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
  - 100 applicants
  - 1 HR admin account

## Tests

```bash
php artisan test
composer test
```

`composer test` juga menjalankan Pint dan PHPStan sesuai script project.

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

CSV export dilewati di MVP ini karena tidak diperlukan untuk dashboard-first flow dan akan menambah surface area yang belum penting.
