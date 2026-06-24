# copilot-instructions for Mian Traders

This repository is a Laravel 12 web application for a multi‑shop trading/pos system.  Agents should treat it as a classic MVC Laravel app with a few domain‑specific helpers and conventions.

## Big Picture & architecture

* **Framework**: Laravel 12, PHP 8.2.  Frontend uses Blade templates in `resources/views/mt`; minimal JavaScript handled by Vite.
* **Multi‑shop context**: a single installation hosts many shops.  The currently selected shop is stored in session (`shop_id`) and accessed via `App\Support\ShopContext`.  Most controllers either filter by `shop_id` or call `ShopContext::requireSingleShopId()` before mutating data.
* **Authentication & roles**: Laravel Breeze out of box.  Custom middleware `App\Http\Middleware\RoleMiddleware` guards routes with `role:admin,manager` etc.  `EnsureActiveShop` enforces a valid shop choice and pre‑sets the session.
* **Domain models** live under `app/Models` (Product, Shop, Sale, Purchase, StockMovement, etc.).  Common relationships use Eloquent conventions; look at `Shop::users()` for the many‑to‑many `user_shops` pivot.
* **Services & support classes**:
  * `App\Services\PricingService` calculates sell/purchase prices (shell formula, discount rules).  `App\Services\DiscountEngine` is a lighter engine used elsewhere.
  * `App\Support\Inventory` manages per‑shop stock via `ShopProduct`, updating weighted average costs inside transactions.
  * `App\Support\ShopContext` centralises helpers for active/allowed shops.
* **Routes** are defined in `routes/web.php` using a `mt.` name prefix.  Most are inside a middleware group `['auth','activeShop']`.  Admin‑only routes add `role:admin`.
* **Views**: use simple inline styling, `@selected`/`@foreach` Blade helpers and a layout `mt.layouts.app` which builds the sidebar based on available routes and the active shop.
* **Session cart** for POS logic is stored under `mt_cart` as an associative array of rows.  Cart manipulation occurs in `PosController` (see methods `add`, `addCut`, `recalcCart` etc.).
* **Checkout actions**: the POS uses a hidden `checkout_action` field with values `save`, `print`, `sms`, `whatsapp` or `all`.  The new `all` option triggers print, SMS and WhatsApp together.  A similar **All** button is also exposed on the sale detail page.  SMS is sent via `LocalSmsService` (now exposes `configured()` to check env settings); if the provider isn’t set an explanatory message is flashed rather than throwing.  WhatsApp opens a chat window (popup handling is required), and a PDF backup is generated automatically on every sale.
* **Eloquent queries** often use `when($condition,...)` for optional filters and `compact()` when passing variables to views.

## Project‑specific conventions

* **Route naming**: all names use the `mt.` prefix and dot notation (e.g. `mt.products.index`).  Add new routes there to keep sidebar generation working.
* **Shop selection**: always call `ShopContext::activeShopId()` or `ShopContext::requireSingleShopId()` instead of reading from session directly.
* **Cart row IDs**: prefixes `p` for normal products, `lf` for leftovers, and include cut dimensions when relevant.  Keep them stable so client code can merge rows.
* **Discounts**: scope hierarchy is product &gt; category &gt; company; see `DiscountEngine`/`PricingService` for usage examples.
* **Leftovers & sheets**: some products allow "sheet" pricing; see `PosController::addSheetViaAddRoute` and leftover methods for calculations.
* **No Eloquent accessors in most models**; logic lives in services or controllers.
* **Minimal tests**: the `tests` folder currently only contains a base `TestCase`.  When writing new features include feature tests under `tests/Feature`.

## Development workflows

1. **Bootstrap a new instance**
   ```bash
   composer run-script setup          # installs deps, copies .env, migrates, builds assets
   ```
2. **Development**
   ```bash
   npm run dev                       # compile assets with vite
   composer run-script dev           # runs serve, queue listener, logs watcher and vite concurrently
   php artisan migrate               # apply DB schema
   php artisan db:seed               # seed sample data if available
   php artisan serve                 # start web server on :8000
   ```
3. **Tests & linting**
   ```bash
   php artisan test                   # PHPUnit
   vendor/bin/pint                   # PHP coding style
   ```
4. **Building for production**
   ```bash
   npm run build                     # compile assets
   php artisan migrate --force       # run migrations on deploy
   ```

> _Note:_ `composer.json` includes helpful scripts (`setup`, `dev`, `test`) that wrap common commands.

## External integrations & configuration

* Uses `barryvdh/laravel-dompdf` for PDF generation (sales receipts).  The POS checkout now automatically generates and stores a PDF copy of every invoice under the `public/invoices` disk, so bills are backed up without needing to hit the download link.
* No 3rd‑party APIs; WhatsApp export is server‑side CSV generation.
* .env keys follow standard Laravel patterns; there are no hidden custom env variables beyond the default.

## What an agent should pay attention to

* **Middleware ordering**: `auth` comes before `activeShop` so HTTP requests assume a logged‑in user.  Add new middleware in `app/Http/Kernel.php`.
* **Session keys**: `shop_id` and `mt_cart`.  Never rename them without updating all access points.
* **Controller patterns**: data is validated via `$request->validate([...])` and then processed.  For form reuse between create/edit, controllers often pass a `$product` or `$item` variable that may be `null`.
* **View helpers**: `@selected`, `@checked`, and manual inline styles instead of CSS files.
* **Calendar logic**: `DashboardController` auto‑generates recurring expenses and computes week/month ranges with Carbon.

## Editing or extending

* Add new models under `app/Models` and register migrations in `database/migrations` with timestamp order.
* Seeders live under `database/seeders` and can attach relations (see `UserSeeder` for `shops()` sync logic).
* To expose new data to the frontend, update the corresponding controller method and view.  Keep parameter names consistent with other forms (e.g. `['g','q','c']` query strings in POS).
* When writing queries for multi‑shop data, use `ShopContext::activeShopId()` or scope by `$sid`.
* JavaScript is rare; any new frontend interactions should use simple Alpine/Vue or raw JS loaded via Vite.

---

If you (the human) notice missing context or conventions, please update this file.  After creating the first version, ask for feedback so we can iterate further.