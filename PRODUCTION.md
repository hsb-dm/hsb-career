# Production deployment through Cloudflare Tunnel

The production stack uses PHP-FPM, Nginx, and PostgreSQL 17. Only Nginx publishes a host port. PostgreSQL is not exposed, and Docker volumes preserve the database and CV files when containers are replaced.

## Preparation

```bash
cp .env.production.example .env.production
```

Set `APP_URL` to the Tunnel HTTPS hostname, then replace `DB_PASSWORD` and `INITIAL_ADMIN_PASSWORD` with different strong passwords. Generate `APP_KEY` once and store it in `.env.production`:

```bash
php -r 'echo "base64:".base64_encode(random_bytes(32)).PHP_EOL;'
```

Set `MAIL_HOST`, `MAIL_USERNAME`, `MAIL_PASSWORD`, and `MAIL_FROM_ADDRESS` to the values from your mail provider. Send a test rejection email before using the dashboard for candidates. The production example uses SMTP; a log or array mailer cannot deliver rejection emails.

To store new CV uploads in S3, set `AWS_DEFAULT_REGION` and `AWS_BUCKET` in `.env.production`. Attach an IAM role with access to that private bucket to the EC2 instance; leave `AWS_ACCESS_KEY_ID` and `AWS_SECRET_ACCESS_KEY` unset. The app container must be able to reach EC2 instance metadata. With IMDSv2, a metadata response hop limit of 1 can block containers, so check the instance metadata settings if credentials cannot be found. Keep S3 Block Public Access enabled. Existing CVs on the local volume remain readable; keep backing up that volume until they are migrated. S3 storage and request charges apply.

Do not change `APP_KEY` after user data exists because existing sessions and encrypted data may become unreadable. Keep a secure copy of `.env.production` and regularly back up the `postgres-data` and `cv-data` volumes. `.env.production` is excluded from Git and the Docker images. Existing MySQL data is not transferred automatically; preserve its volume until migration is verified.

## Start the stack

After filling `.env.production`, deploy from the repository directory with one command. The script uses `sudo` for Docker, builds the images, starts PostgreSQL, applies pending migrations, creates the initial admin if it does not exist, and starts the app and web server. It does not add demo vacancies:

```bash
./deploy.sh
```

Check `http://127.0.0.1:8181/up` and the home page. A Cloudflare Tunnel running on the same host can use **`http://127.0.0.1:8181`** as its origin service. The Tunnel uses local HTTP while visitors use the HTTPS hostname configured in `APP_URL`. Make sure the public hostname forwarded by the Tunnel matches the hostname in `APP_URL`.

### Host Nginx reverse proxy (alternative to a direct Tunnel origin)

If Nginx runs on the EC2 host, use [docker/nginx-host.example.conf](docker/nginx-host.example.conf) to proxy HTTPS requests to Docker's published `127.0.0.1:8181` port. Replace `career.hsb.co.id` with the hostname in `APP_URL` and set the certificate paths to an existing certificate for that hostname. Keep `APP_BIND_ADDRESS=127.0.0.1` and `APP_PORT=8181`. The file makes this site the host's default server and drops requests for any other hostname, so disable other default sites such as `/etc/nginx/sites-enabled/default`. Install the file as an enabled Nginx site, then run `sudo nginx -t` before reloading Nginx. The example redirects HTTP to HTTPS; obtain the certificate before enabling it. If Cloudflare Tunnel already points directly at `127.0.0.1:8181`, host Nginx is unnecessary.

Verify the running app reports PHP 8.4.27 or newer: `docker compose --env-file .env.production -f compose.production.yaml exec app php -v`.

Change the published port with `APP_PORT` in `.env.production`. The default `APP_BIND_ADDRESS=127.0.0.1` restricts origin access to the local host. If `cloudflared` runs in a container, attach it to the `hsb-jobs-production_frontend` Docker network and use `http://web:8181`. If the Tunnel runs on another host, set `APP_BIND_ADDRESS` to a reachable network address and restrict network access to that port.

Nginx uses Cloudflare's `CF-Connecting-IP` only for requests from Docker's `172.16.0.0/12` bridge range. Keep the origin reachable only through the Tunnel. If your Docker network or Tunnel host uses another range, update `set_real_ip_from` in `docker/nginx.conf` to that specific source before deploying; otherwise all visitors share a submission rate limit.

## UAT demo data

The default seeder creates the administrator account and demo vacancies for UAT:

```bash
sudo docker compose --env-file .env.production -f compose.production.yaml exec app php artisan db:seed --force
```

Use the credentials configured in `INITIAL_ADMIN_EMAIL` and `INITIAL_ADMIN_PASSWORD`. Do not run the demo vacancy seeder on a live production database.

## Updates

Run `./deploy.sh` again after updating the code. The admin seeder leaves an existing account and password unchanged.

Do not run `migrate:fresh` or the demo seeder in production. To inspect status and logs:

```bash
docker compose --env-file .env.production -f compose.production.yaml ps
docker compose --env-file .env.production -f compose.production.yaml logs -f app web postgres
```

The images use multi-stage builds, keeping Node, Composer, development dependencies, and test sources out of the runtime images. PHP OPcache and Laravel caches are enabled at startup. The application runs as a non-root user with a read-only code filesystem. Runtime directories use tmpfs, while CV files use a persistent volume. Nginx serves static assets with browser caching and restricts PHP execution to `public/index.php`.
