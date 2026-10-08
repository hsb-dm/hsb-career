# Production deployment through Cloudflare Tunnel

The production stack uses PHP-FPM, Nginx, and MySQL 8.4. Only Nginx publishes a host port. MySQL is not exposed, and Docker volumes preserve the database and CV files when containers are replaced.

## Preparation

```bash
cp .env.production.example .env.production
```

Set `APP_URL` to the Tunnel HTTPS hostname, then replace `DB_PASSWORD` and `DB_ROOT_PASSWORD` with two different random passwords. Generate `APP_KEY` once and store it in `.env.production`:

```bash
php -r 'echo "base64:".base64_encode(random_bytes(32)).PHP_EOL;'
```

Do not change `APP_KEY` after user data exists because existing sessions and encrypted data may become unreadable. Keep a secure copy of `.env.production` and regularly back up the `mysql-data` and `cv-data` volumes. `.env.production` is excluded from Git and the Docker images.

## Start the stack

Run all commands from the repository directory:

```bash
docker compose --env-file .env.production -f compose.production.yaml build
docker compose --env-file .env.production -f compose.production.yaml up -d mysql
docker compose --env-file .env.production -f compose.production.yaml run --rm app php artisan migrate --force
docker compose --env-file .env.production -f compose.production.yaml up -d app web
```

Check `http://127.0.0.1:8181/up` and the home page. A Cloudflare Tunnel running on the same host can use **`http://127.0.0.1:8181`** as its origin service. The Tunnel uses local HTTP while visitors use the HTTPS hostname configured in `APP_URL`. Make sure the public hostname forwarded by the Tunnel matches the hostname in `APP_URL`.

Change the published port with `APP_PORT` in `.env.production`. The default `APP_BIND_ADDRESS=127.0.0.1` restricts origin access to the local host. If `cloudflared` runs in a container, attach it to the `hsb-jobs-production_frontend` Docker network and use `http://web:8181`. If the Tunnel runs on another host, set `APP_BIND_ADDRESS` to a reachable network address and restrict network access to that port.

## Updates

```bash
docker compose --env-file .env.production -f compose.production.yaml build
docker compose --env-file .env.production -f compose.production.yaml run --rm app php artisan migrate --force
docker compose --env-file .env.production -f compose.production.yaml up -d app web
```

Do not run `migrate:fresh` or the demo seeder in production. To inspect status and logs:

```bash
docker compose --env-file .env.production -f compose.production.yaml ps
docker compose --env-file .env.production -f compose.production.yaml logs -f app web mysql
```

The images use multi-stage builds, keeping Node, Composer, development dependencies, and test sources out of the runtime images. PHP OPcache and Laravel caches are enabled at startup. The application runs as a non-root user with a read-only code filesystem. Runtime directories use tmpfs, while CV files use a persistent volume. Nginx serves static assets with browser caching and restricts PHP execution to `public/index.php`.
