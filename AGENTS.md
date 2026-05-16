# AGENTS.md -- Artpedia Printshop (Sistem Informasi Percetakan)

## Stack

- **Laravel 8** / PHP ^8.0 / MySQL (`artpedia_db`) on XAMPP
- **Blade** + **Bootstrap 5.3 CDN** (no compiled assets -- `public/css/` and `public/js/` are empty)
- Midtrans Snap sandbox for cashless payment, dompdf for PDF

## Setup & run

```bash
composer install && npm install
cp .env.example .env                    # .env is committed; update if needed
php artisan key:generate
php artisan storage:link
php artisan migrate && php artisan db:seed
php artisan serve                       # http://localhost:8000
```

## Demo accounts (from DatabaseSeeder)

| Role              | Email                         | Password    |
|-------------------|-------------------------------|-------------|
| Kasir             | kasirartpedia@gmail.com       | kasir123    |
| Operator Produksi | operatorproduksi@gmail.com    | operator123 |
| Pelanggan         | ifan@gmail.com                | ifan123     |

## Tests

```bash
./vendor/bin/phpunit                  # only ExampleTests exist
```

## Architecture

### Roles & routing

Three roles: `pelanggan` / `kasir` / `operator_produksi`. All web routes are grouped by role with `auth` + `role:xxx` middleware in `routes/web.php`.

### Cart

Session-based. Customer: `cart` key. Cashier walk-in: `kasir_walkin_cart` key. Checkout creates a `Transaksi` group; multiple `Pesanan` share one `nomor_pesanan`.

### Payment

- **Cash**: immediate, status → `dalam_antrian`
- **Cashless (Midtrans)**: Snap token → settlement/capture callback → status → `dalam_antrian`

Midtrans sandbox: `MIDTRANS_IS_PRODUCTION=false`. Local callback testing requires **ngrok** (`ngrok.exe` in repo root). Callback URL: `POST /api/midtrans/callback` (CSRF exempt in `VerifyCsrfToken`).

### Production queue (SJF)

Operator sees queue sorted by `estimasi_waktu ASC`, then `dikonfirmasi_at ASC`, then `id ASC`. Only one order claimable at a time.

### Storage

Design uploads → `storage/app/public/desain/` (moved from `desain_temp/` on checkout).

## Conventions

- **Language**: Indonesian throughout (UI, comments, routes, variables)
- **Locale**: `id`
- **Views**: Blade, extend `layouts.app`
- **nomor_pesanan**: NO unique constraint (intentional -- multi-item checkout shares one number)
- **CSRF**: enabled for web; `api/midtrans/callback` exempted in `VerifyCsrfToken` middleware

## ── ⚠️ Warnings ──────────────────────────────────

- **.env is committed** with real Midtrans sandbox keys -- do not push to public repos.
- Do NOT add unique constraint on `nomor_pesanan`.
- Do NOT `git reset --hard` or large revert (many manual changes).
- For production: update `APP_URL`, Midtrans production keys, and Midtrans dashboard URL.

## Deeper context

See `RINGKASAN_PROJECT.md` for the full project narrative, order flows, and product/pricing specs.
