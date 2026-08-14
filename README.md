# Sneakyard

Sneakyard is a responsive, installable sneaker storefront and order-management system built for authentic footwear retail in the Philippines. The storefront follows the selected minimal editorial direction, while the admin area uses the free Flux UI component set.

## What is included

- Responsive storefront, catalog search/filtering, product pages, session cart, checkout, and stock-safe order creation
- Installable PWA for Android and desktop, with an offline fallback and cached storefront assets
- Admin dashboard for products, variants, inventory, orders, fulfillment status, and notification history
- Realtime admin order alerts through Laravel Reverb, database notifications, and optional queued email notifications
- Search metadata, Open Graph cards, JSON-LD product data, sitemap, robots rules, Meta Pixel readiness, and a Meta catalog CSV feed
- Versioned read-only product API at `/api/v1/products`
- PostgreSQL, Redis, queue, scheduler, Reverb, Mailpit, PHP-FPM, and Nginx in Docker Compose

## Brand system

The storefront uses the Facebook Page profile artwork as the source wordmark and a simplified `SY` monogram for PWA and Android icon sizes. Brand source files live in `public/images/brand`, generated install icons live in `public/images/icons`, and the persisted interface direction is documented in `design-system/sneakyard/MASTER.md`.

Typography combines the distressed Special Elite face for short brand labels, Playfair Display for editorial headings, and Instrument Sans for readable commerce and admin controls.

## Start with Docker

1. Copy the environment template:

   ```bash
   cp .env.example .env
   ```

2. Change `APP_KEY`, `ADMIN_PASSWORD`, database passwords, Reverb secrets, and any Meta credentials in `.env`. To create an application key before first startup, run:

   ```bash
   docker compose run --rm app php artisan key:generate --show
   ```

3. Build and start the platform:

   ```bash
   docker compose up -d --build
   docker compose exec app php artisan migrate --seed --force
   ```

4. Open the storefront at `http://localhost:8080`, admin at `http://localhost:8080/admin`, and local email inbox at `http://localhost:8025`.

The development seed creates `admin@sneakyard.ph` with the password supplied through `ADMIN_PASSWORD` (default: `password`). Change it before exposing the app outside a local machine.

## Common commands

```bash
docker compose exec app php artisan test
docker compose exec app php artisan migrate --seed --force
docker compose logs -f app queue reverb
docker compose down
```

Persistent database, Redis, and uploaded-media data use named Docker volumes. `docker compose down` keeps them; adding `--volumes` permanently removes them.

## Notifications

Realtime browser alerts use Reverb. An admin can grant browser notification permission from the notification menu. Order alerts are also stored in the database. Set `ORDER_EMAIL_NOTIFICATIONS=true` and configure the `MAIL_*` variables to send queued order email alerts; local Docker defaults point to Mailpit.

## Facebook and Meta commerce readiness

Set `FACEBOOK_PAGE_URL` to link the storefront to the existing Facebook Page. Add `META_PIXEL_ID` for Pixel page views. For product synchronization, configure `META_CATALOG_ID`, `META_PAGE_ID`, and `META_ACCESS_TOKEN`; product saves enqueue a catalog sync job. Meta permissions and catalog review must still be completed in the business owner's Meta Business account.

The catalog feed is available at `/feeds/meta-products.csv` and can be scheduled from Meta Commerce Manager.

## Production notes

- Replace all example credentials and use a production secret manager.
- Serve the site and Reverb WebSocket endpoint over HTTPS.
- Set `APP_ENV=production`, `APP_DEBUG=false`, production mail, database, Redis, and allowed Reverb origins.
- Back up the PostgreSQL and uploaded-media volumes.
- Run migrations during deployment and keep the queue, scheduler, and Reverb services running.

## Free preview deployment: Render + Neon

The repository includes a free-tier preview configuration in `render.yaml` and `Dockerfile.render`. It runs Nginx and PHP-FPM in one non-root container, uses an external Neon PostgreSQL database, stores sessions and cache in PostgreSQL, runs queued work synchronously, and disables Reverb and outbound email by default.

1. Create a free Neon project in Singapore and copy its pooled connection string.
2. In Render, create a Blueprint from this repository's `development` branch.
3. Supply the requested `APP_KEY`, `DB_URL`, `ADMIN_EMAIL`, and `ADMIN_PASSWORD` values.
4. Set `DB_URL` to the Neon pooled connection string. Render's automatically assigned HTTPS URL is used for Laravel links and assets.

The startup process automatically applies migrations and the idempotent development seed. Render's free filesystem is ephemeral, so admin-uploaded product media is not durable until an S3-compatible object store is configured. Free-tier queues run inline and realtime Reverb notifications are disabled; these services can be restored when moving to paid worker and WebSocket infrastructure.

## Stack

Laravel 13, PHP 8.5, Livewire 4, Flux UI Free, Tailwind CSS 4, PostgreSQL 18, Redis 8, Laravel Reverb, Vite 8, Node.js 24, Nginx 1.28, and Docker Compose.
