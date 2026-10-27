# Go live (real main domain)

Demo was: `https://minimarvels.com.pk/federalmanpower` (subfolder hacks).  
Live should be: main domain → document root = **`public/`**.

## Do this

1. Point domain docroot to `…/public` (not project root).
2. New `.env`:
   - `APP_URL=https://your-domain.com` (no `/federalmanpower`, no `/public`)
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - real DB credentials
3. `composer install --no-dev --optimize-autoloader` (or upload `vendor`)
4. `php artisan key:generate`
5. `php artisan migrate --force`
6. `chmod -R 775 storage bootstrap/cache`
7. `php artisan config:cache` / `route:cache` / `view:cache`

## Remove demo-only bits

| File | Action |
|------|--------|
| Root `.htaccess` | Delete (or stop using — demo subfolder rewrite) |
| Root `index.php` | Delete (shim into `public/`; not needed if docroot is `public/`) |
| `public/.htaccess` | Remove line `RewriteBase /federalmanpower/` |

## Keep

- `AppServiceProvider` `URL::forceRootUrl()` — OK; follows `APP_URL`
- Private client files under `storage/app/private` — never public disk

## After deploy — smoke test

Login, clients, document download, portal apply, employer jobs, payments receipt PDF.
