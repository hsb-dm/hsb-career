# Deploy production lewat Cloudflare Tunnel

Stack production menggunakan PHP-FPM, Nginx, dan MySQL 8.4. Hanya Nginx yang membuka port host. MySQL tidak dipublikasikan; CV dan database memakai volume Docker agar tetap ada saat container dibuat ulang.

## Persiapan

```bash
cp .env.production.example .env.production
```

Isi `APP_URL` dengan hostname HTTPS Tunnel, lalu ganti `DB_PASSWORD` dan `DB_ROOT_PASSWORD` dengan dua password acak yang berbeda. Buat `APP_KEY` satu kali dan simpan nilainya di `.env.production`:

```bash
php -r 'echo "base64:".base64_encode(random_bytes(32)).PHP_EOL;'
```

Jangan ganti `APP_KEY` setelah ada data pengguna: sesi dan data terenkripsi lama dapat menjadi tidak terbaca. Simpan salinan aman `.env.production`, serta cadangkan volume `mysql-data` dan `cv-data` secara berkala. File `.env.production` tidak masuk Git maupun image Docker.

## Jalankan

Jalankan semua perintah dari direktori repo:

```bash
docker compose --env-file .env.production -f compose.production.yaml build
docker compose --env-file .env.production -f compose.production.yaml up -d mysql
docker compose --env-file .env.production -f compose.production.yaml run --rm app php artisan migrate --force
docker compose --env-file .env.production -f compose.production.yaml up -d app web
```

Periksa `http://127.0.0.1:8080/up` dan halaman utama. Cloudflare Tunnel yang berjalan di host yang sama dapat diarahkan ke **`http://127.0.0.1:8080`**. URL layanan Tunnel memakai HTTP lokal; pengunjung tetap memakai hostname HTTPS pada `APP_URL`. Pastikan hostname publik yang dikirim Tunnel sama dengan hostname `APP_URL`.

Port host dapat diganti lewat `APP_PORT` di `.env.production`. Default `APP_BIND_ADDRESS=127.0.0.1` membuat origin hanya dapat diakses dari host itu. Jika `cloudflared` berjalan di container, hubungkan ke jaringan Docker `hsb-jobs-production_frontend` dan arahkan ke `http://web:8080`. Jika Tunnel berada di host lain, atur `APP_BIND_ADDRESS` ke alamat jaringan yang dapat dicapai Tunnel dan batasi akses jaringan ke port tersebut.

## Pembaruan

```bash
docker compose --env-file .env.production -f compose.production.yaml build
docker compose --env-file .env.production -f compose.production.yaml run --rm app php artisan migrate --force
docker compose --env-file .env.production -f compose.production.yaml up -d app web
```

Jangan jalankan `migrate:fresh` atau seeder demo di production. Untuk melihat status dan log:

```bash
docker compose --env-file .env.production -f compose.production.yaml ps
docker compose --env-file .env.production -f compose.production.yaml logs -f app web mysql
```

Image menggunakan build bertahap sehingga Node, Composer, dependency development, dan source test tidak masuk image runtime. PHP OPcache dan cache Laravel aktif saat startup. Aplikasi berjalan sebagai user non-root dengan filesystem kode read-only; direktori runtime memakai tmpfs, dan hanya CV yang memakai volume persisten. Nginx melayani aset statis dengan cache browser dan membatasi eksekusi PHP ke `public/index.php`.
