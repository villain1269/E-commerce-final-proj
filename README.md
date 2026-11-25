# Danielle's Crafted Pizza — Laravel Port

This is a **Laravel 11** conversion of the original static HTML/CSS/JS e-commerce
final project for *Danielle's Crafted Pizza* — a fictional pizza-supplies store
selling dough, bases, packaging, equipment, and ready-made pizzas.

The original project lived entirely in the browser: product data was hardcoded
in `js/products.js`, the cart was stored in `localStorage`, and orders were
saved to `localStorage` under `dcp_last_order`. This Laravel port keeps the
same UI and the same flow, but moves all data and state to a real backend —
**and includes a built-in mock payment gateway** that supports GCash, Maya,
credit/debit cards, online banking, and over-the-counter cash payments.

---

## What changed (original → Laravel)

| Aspect | Original (static) | Laravel port |
|---|---|---|
| **Product catalog** | Hardcoded array in `js/products.js` | `products` + `categories` MySQL/SQLite tables, seeded via `ProductSeeder` |
| **Cart storage** | `localStorage['dcp_cart']` | Laravel **session** (server-side), via `App\Services\CartService` |
| **Order storage** | `localStorage['dcp_last_order']` | `orders` + `order_items` tables (DB) |
| **Routing** | Multiple `.html` files | `routes/web.php` named routes |
| **Rendering** | 100% client-side JS DOM building | Blade templates (`resources/views/pages/*.blade.php`) |
| **Forms** | `localStorage` writes + manual validation | Laravel `Validator` with the same regex rules (4-digit postal, PH phone, etc.) |
| **Starter Kit Builder** | `STARTER_KITS` JS object + client-side rendering | `App\Services\StarterKitService` (same data) + server-side Blade render |
| **CSRF protection** | None (no backend) | `@csrf` on every form (Laravel default) |
| **Header / footer** | Built at runtime by `buildLayout()` in `js/cart.js` | `partials/header.blade.php` and `partials/footer.blade.php` |
| **CSS** | `css/style.css` (unchanged) | `public/css/style.css` (copied verbatim) |
| **JS** | `js/products.js`, `js/cart.js`, `js/checkout.js`, `js/starter-kit.js` | Small `public/js/dcp.js` for menu-toggle + search-bar; per-page `<script>` blocks for tabs / carousel / qty steppers / checkout summary updates |

All **12 original products**, **5 categories**, **3 starter-kit business types × 3
budget tiers = 9 kits**, shipping rates (₱150 standard / ₱250 express,
free standard over ₱3,000), and the original CSS palette
(navy `#12151f` / red `#dc3a1e` / amber `#f5a800` / cream `#fbf9f5`) are preserved.

---

## Tech stack

- **PHP 8.3+**
- **Laravel 11.x** (framework)
- **Blade** (templating)
- **SQLite** (default, file: `database/database.sqlite`) — switchable to MySQL via `.env`
- **Eloquent ORM** for models
- Plain CSS + vanilla JavaScript (no build step needed; Vite is included in the skeleton but not required)

---

## Project structure (key files)

```
danielle-pizza-laravel/
├── app/
│   ├── Http/Controllers/Web/
│   │   ├── HomeController.php            # / (home page)
│   │   ├── ShopController.php            # /shop (filters + pagination)
│   │   ├── ProductController.php         # /product/{id}
│   │   ├── CartController.php            # /cart + add/qty/remove/clear
│   │   ├── CheckoutController.php        # /checkout GET + POST
│   │   ├── OrderController.php           # /confirmation
│   │   └── StarterKitController.php      # /starter-kit POST
│   ├── Models/
│   │   ├── Category.php
│   │   ├── Product.php                   # has specs/suited/tags as JSON casts
│   │   ├── Order.php
│   │   └── OrderItem.php
│   ├── Services/
│   │   ├── CartService.php               # session cart, subtotal, shipping
│   │   └── StarterKitService.php         # kit data (ported from JS)
│   ├── View/Composers/                    # share $cartCount, $navCategories
│   └── helpers/Format.php                # peso() + stars() + shopPageUrl()
│
├── database/
│   ├── migrations/
│   │   ├── 2025_01_01_000010_create_categories_table.php
│   │   ├── 2025_01_01_000011_create_products_table.php
│   │   ├── 2025_01_01_000012_create_orders_table.php
│   │   └── 2025_01_01_000013_create_order_items_table.php
│   └── seeders/
│       ├── CategorySeeder.php            # 5 categories
│       ├── ProductSeeder.php             # 12 products (full data)
│       └── DatabaseSeeder.php
│
├── resources/views/
│   ├── layouts/
│   │   ├── app.blade.php                 # main layout (header + footer)
│   │   └── checkout.blade.php            # checkout layout (secure bar)
│   ├── partials/
│   │   ├── header.blade.php              # announcement + nav + cart count
│   │   ├── header-checkout.blade.php     # secure-checkout header
│   │   ├── footer.blade.php
│   │   ├── product-card.blade.php        # reusable product card component
│   │   └── toast.blade.php               # session('status') toast
│   └── pages/
│       ├── home.blade.php                # hero + categories + featured + kit builder + wholesale + testimonials + newsletter
│       ├── shop.blade.php                # filters sidebar + grid + pagination
│       ├── product.blade.php             # detail + tabs + related
│       ├── cart.blade.php                # items table + summary + upsells
│       ├── checkout.blade.php            # 3-step form + summary
│       ├── checkout-empty.blade.php      # cart-empty fallback for /checkout
│       ├── confirmation.blade.php        # order thank-you + summary
│       └── product-not-found.blade.php
│
├── public/
│   ├── css/style.css                     # copied verbatim from original
│   └── js/dcp.js                         # menu toggle + search bar (was in cart.js)
│
├── routes/web.php                        # all named routes
└── .env                                   # SQLite by default
```

---

## Setup

### Requirements
- **PHP 8.2+** (with `pdo_sqlite` or `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`, `curl`, `gd`, `zip`)
- **Composer** — *only needed for the small zip; the ready-to-run zip already has vendor/*

> **Using XAMPP / Laragon / MAMP?** Their PHP is fine — just make sure `php` is on your PATH
> so the `php artisan ...` commands below work from any terminal. On XAMPP, the PHP binary
> lives at `C:\xampp\php\php.exe`; on Laragon at `C:\laragon\bin\php\php-8.x\php.exe`.
> On Mac with Homebrew: `brew install php`. Verify with `php -v`.

---

### Option A — Ready-to-run zip (no Composer needed) ✅ recommended

1. Download **`danielle-pizza-laravel-with-vendor.zip`** (≈17 MB).
2. Unzip it anywhere — Desktop, Documents, `C:\projects\`, etc.
3. Open a terminal **inside** the unzipped folder (the one containing `artisan`).
   - Windows: shift+right-click in File Explorer → "Open PowerShell window here"
   - Mac: right-click the folder in Finder → "New Terminal at Folder"
4. Run these four commands:

```bash
# 1. Create your local .env (Laravel needs this to run)
cp .env.example .env          # Windows PowerShell: copy .env.example .env

# 2. Generate the encryption key (writes to .env)
php artisan key:generate

# 3. Create the empty SQLite database file + run migrations + seed 12 products
touch database/database.sqlite  # Windows PowerShell: New-Item database/database.sqlite -ItemType File
php artisan migrate:fresh --seed

# 4. Start the dev server
php artisan serve
```

5. Open <http://127.0.0.1:8000/> in your browser.

That's it — no Composer, no database server, no config files to edit.

---

### Option B — Small zip + `composer install` (the "proper" way)

Use this if you want a clean install, want to add Laravel packages later, or are setting up
for production. The download is **`danielle-pizza-laravel.zip`** (≈130 KB).

1. Make sure Composer is installed: <https://getcomposer.org/download/>
   - Verify with `composer --version` in a terminal.
2. Unzip the file.
3. In a terminal **inside** the unzipped folder:

```bash
# 1. Install PHP dependencies (downloads vendor/)
composer install

# 2. Copy .env.example to .env and generate the app key
cp .env.example .env          # Windows PowerShell: copy .env.example .env
php artisan key:generate

# 3. Create the SQLite database file (or skip if using MySQL — see below)
touch database/database.sqlite  # Windows PowerShell: New-Item database/database.sqlite -ItemType File

# 4. Run migrations + seeders
php artisan migrate:fresh --seed

# 5. Start the dev server
php artisan serve
```

Open <http://127.0.0.1:8000/> in your browser.

---

### Common `composer install` problems and fixes

| Symptom | Likely cause | Fix |
|---|---|---|
| `composer: command not found` / `'composer' is not recognized` | Composer isn't installed or not on PATH | Install from <https://getcomposer.org/download/> and reopen the terminal |
| `php: command not found` | PHP isn't on PATH | Add your PHP folder (e.g. `C:\xampp\php`) to PATH, or call PHP by full path: `C:\xampp\php\php.exe artisan ...` |
| `Your requirements could not be resolved to an installable set of packages` | PHP version too old (need 8.2+) | `php -v` to check; upgrade PHP if older |
| `Script @php artisan package:discover handling the post-autoload-dump event returned with error code 255` | App key missing or .env not created yet | Run `cp .env.example .env` then `php artisan key:generate` before `composer install` |
| `SQLSTATE[HY000] [14] unable to open database file` (when running migrations) | `database/database.sqlite` doesn't exist | Create it: `touch database/database.sqlite` (Mac/Linux) or `New-Item database/database.sqlite -ItemType File` (PowerShell) |
| Migrations prompt "Application in production" and abort | `.env` has `APP_ENV=production` | Either answer "yes" when prompted, or run `php artisan migrate:fresh --seed --force` |

If none of the above match what you're seeing, paste the exact error text in chat and I'll help.

---

### Switching to MySQL (optional)
Edit `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=danielle_pizza
DB_USERNAME=root
DB_PASSWORD=
```
Then re-run `php artisan migrate:fresh --seed`.

---

## Routes

| Method | Path | Route name | Description |
|---|---|---|---|
| GET | `/` | `home` | Home page (hero, categories, featured carousel, starter kit builder, wholesale, testimonials, newsletter) |
| GET | `/shop` | `shop` | Shop page (filters: `category[]`, `suit[]`, `min`, `max`, `q`, `sort`, `page`) |
| GET | `/product/{id}` | `product.show` | Product detail page (id = `p1`–`p12`) |
| GET | `/cart` | `cart.index` | Cart page |
| POST | `/cart/add/{id}` | `cart.add` | Add product to cart (body: `qty`) |
| POST | `/cart/qty/{id}` | `cart.qty` | Set exact qty (body: `qty`) |
| POST | `/cart/inc/{id}` | `cart.inc` | Increment qty by 1 |
| POST | `/cart/dec/{id}` | `cart.dec` | Decrement qty by 1 |
| POST | `/cart/remove/{id}` | `cart.remove` | Remove item from cart |
| POST | `/cart/clear` | `cart.clear` | Empty the cart |
| GET | `/checkout` | `checkout.index` | Checkout form (redirects to empty-state if cart is empty) |
| POST | `/checkout` | `checkout.place` | Place order → saves to DB, clears cart, redirects to `/confirmation` |
| GET | `/confirmation` | `confirmation.show` | Order confirmation page (reads `last_order_ref` from session) |
| POST | `/starter-kit` | `starter-kit.recommend` | Recommend a kit (`businessType` + `budget`, optional `add_to_cart`) |
| POST | `/newsletter` | `newsletter.subscribe` | Newsletter form (just flashes a thank-you toast) |

All `POST` routes are CSRF-protected (Laravel default).

---

## Notes on intentional behaviors

- **Image placeholders** (`[HERO BANNER]`, `[PRODUCT IMAGE]`, `[IMAGE 2]`, `[IMAGE 3]`, `[IMAGE 4]`, `[IMG]`, `[WHOLESALE IMAGE]`): preserved from the original — the original project is a school assignment and never shipped real images.
- **Cart badge**: server-rendered with the page; it updates on the next page load (no AJAX). This matches the visual style of the original, which also rebuilt the header on every load.
- **Demo checkout**: the "no real payment processed" notice is preserved verbatim from `checkout.html`. The form's `payment` field is validated but not used to charge anything.
- **`dcp.js`**: the only client-side JS shipped globally. The original `cart.js` did header/footer rendering, mobile menu, search bar, toast, and cart-page rendering — the first three are now in `dcp.js`; the cart page is now Blade-rendered.

---

## Testing the full flow (manual smoke test)

1. Visit `/` — verify hero, 5 category cards, featured carousel arrows work, starter-kit form renders.
2. Click "Shop" — verify 12 products paginate 9 + 3, filters narrow results, sort changes order.
3. Click a product — verify tabs switch (Description / Specs / Reviews), qty stepper updates the "Add to Cart" button label, related products appear.
4. Click "Add to Order" on a product card — verify redirect back with "Added to cart" toast.
5. Open `/cart` — verify line items, qty +/−, remove (×), subtotal, shipping, total, "free shipping" progress.
6. Click "Proceed to Secure Checkout" — verify the 3-step form, shipping-method radios update the order summary, payment validation requires a choice (Cash on Delivery OR Pay Online).
7. Submit the form:
   - **COD path**: order is created with `payment_status=pending_cod`, redirects straight to confirmation page.
   - **Online path**: order is created with `payment_status=pending_payment`, redirects to `/pay/{ref}` — the dedicated payment screen showing 5 method tiles.
8. On the payment screen, click any method tile and the "Pay" button. The flow:
   - **Card** (test `4242 4242 4242 4242`, any future expiry, any CVC) → processes inline → redirects to confirmation with `payment_status=paid`.
   - **GCash/Maya/bank/OTC** → redirects to a realistic mock gateway page that looks like the real wallet/bank checkout. Click "Approve" → redirects to confirmation with `payment_status=paid`. Click "Cancel payment" → goes back to the method picker.
9. Check the DB: `SELECT * FROM orders ORDER BY id DESC LIMIT 1;` — should show `payment_status='paid'`, `payment_method_type`, `payment_ref` populated.

---

## Payment integration — mock gateway + optional Xendit test mode

This project ships with **two payment modes** that auto-switch based on your `.env` configuration:

### Default: built-in mock gateway

Out of the box, with no API keys configured, the project uses a **mock payment gateway** at `/mock/{ref}` that looks like a real GCash/Maya/Stripe checkout but doesn't charge money and doesn't require any signup. It requires:

- **No signup**
- **No API keys**
- **No BIR / DTI / business documents**
- **No external accounts of any kind**

### Optional: real Xendit test mode

If you have a Xendit test account (free signup at <https://dashboard.xendit.co>), you can wire it up by adding your sandbox keys to `.env`:

```env
XENDIT_SECRET_KEY=xnd_development_your_secret_key_here
XENDIT_PUBLIC_KEY=xnd_public_development_your_public_key_here
```

When both keys are present and non-empty, the payment screen automatically:

1. Shows a "Xendit test mode active" banner instead of "Mock mode"
2. Tags each payment method with a `Xendit live` or `Mock` badge
3. For supported methods (GCash, Maya, OTC): makes a real API call to Xendit to create a PaymentRequest → redirects the customer to Xendit's real sandbox GCash/Maya/OTC page (e.g. `ewallet-mock-connector.xendit.co/...`) → on approval, Xendit redirects back to `/payment/return/{ref}` → we verify the status with Xendit → mark the order as `paid`
4. For unsupported methods on Xendit PH (Card, Online Banking): falls back to the mock gateway automatically

The switch is **automatic and code-free** — the same Blade templates and routes work for both modes.

### Supported methods

| Method | Mock gateway | Xendit test mode (when configured) |
|---|---|---|
| **GCash** | ✅ Mock page with "Approve via GCash" button | ✅ Real Xendit PaymentRequest, redirect to `ewallet-mock-connector.xendit.co` |
| **Maya** | ✅ Mock page with "Approve via Maya" button | ✅ Real Xendit PaymentRequest via PAYMAYA channel |
| **Credit / Debit Card** | ✅ Inline card form, test card `4242 4242 4242 4242` | ⚠️ Mock fallback (Xendit.js card tokenization not wired up yet) |
| **Online Banking** | ✅ Bank picker (BPI/BDO/UnionBank etc.) | ⚠️ Mock fallback (Xendit PH bank channels need pre-activation) |
| **Over-the-Counter** | ✅ Outlet picker (7-Eleven / MLhuillier / Cebuana / Palawan) | ✅ Real Xendit PaymentRequest |

### Test cards (mock gateway only — Xendit test cards are different)

| Card number | Result |
|---|---|
| `4242 4242 4242 4242` | Success (Visa test card) |
| `5555 5555 5555 4444` | Success (Mastercard test card) |
| Any 15-16 digit number | Success (mock doesn't actually validate card networks) |

Any future expiry date and any 3-digit CVC will work.

### Payment flow (Shopee-style)

Both flows start the same way: customer picks a method, clicks "Pay", and is shown a brief **"Connecting to {wallet}…"** intermediate screen — branded with the wallet's color (GCash blue, Maya green, etc.), showing the amount, merchant name, a spinner, and a "Cancel and choose another method" link. After ~1.6 seconds, this screen auto-redirects to either Xendit's real sandbox or the mock gateway page.

This mirrors how real Shopee/Lazada checkouts work — they don't teleport you to the wallet; they show a branded "connecting" screen first.

#### Default flow (no Xendit keys — mock only)

1. Customer fills delivery info on `/checkout` and picks **"Pay Online"**.
2. Clicks **Place Order** → order saved to DB with `payment_status=pending_payment`, redirected to `/pay/{ref}` (the method picker).
3. Customer clicks a method tile (GCash, Maya, Card, Online Banking, OTC). Sub-options appear for bank/OTC.
4. Clicks **"Pay ₱X with {method}"** → backend updates the order with the chosen method + generates a `payment_ref` (`MOCK-XXXXXXXXXXXX`).
5. **Connecting screen** renders for ~1.6s with the wallet's branding, then auto-redirects to `/mock/{ref}?method=...`.
6. The mock page opens — looks like a real GCash/Maya/Stripe checkout with branded colors, sandbox tag, merchant info, big amount, "Approve" / "Cancel payment" buttons.
7. Click **Approve** → 1.5-second processing spinner → POST to `/mock/{ref}/complete?result=approve` → order status updated to `paid` → redirect to `/confirmation?ref=...`.
8. Click **Cancel payment** → POST to `/mock/{ref}/complete?result=decline` → order status updated to `failed` → redirect back to `/pay/{ref}` so the customer can try a different method.
9. Confirmation page shows "Payment status: Paid" in green with a checkmark badge.

#### Xendit flow (when keys are configured — for GCash/Maya/OTC)

1. Customer fills delivery info on `/checkout` and picks **"Pay Online"**.
2. Clicks **Place Order** → order saved to DB with `payment_status=pending_payment`, redirected to `/pay/{ref}`.
3. Customer picks GCash/Maya/OTC → clicks **"Pay ₱X with {method}"**.
4. Backend calls `XenditService::createPaymentRequest()` which POSTs to `https://api.xendit.co/payment_requests`. Order saved with `payment_intent_id=pr-xxxx` and `payment_status=REQUIRES_ACTION`.
5. **Connecting screen** renders for ~1.6s with the wallet's branding (footer says "Powered by Xendit · Sandbox mode" + the Xendit PaymentRequest ID), then auto-redirects to Xendit's sandbox wallet page (e.g. `ewallet-mock-connector.xendit.co/v1/ewallet_connector/checkouts?token=...`).
6. On Xendit's sandbox page, customer clicks "Proceed to Pay" — Xendit simulates a successful payment.
7. Xendit redirects back to `/payment/return/{ref}?method=gcash` — our handler retrieves the PaymentRequest status from Xendit → updates `payment_status` to `paid` (if `SUCCEEDED`) → redirects to `/confirmation?ref=...`.
8. Confirmation page shows "Payment status: Paid" + "GCash via Xendit" + the Xendit PaymentRequest ID as the payment reference.

### Getting Xendit sandbox API keys

1. Sign up at <https://dashboard.xendit.co> (free, no credit card, takes 2 minutes).
2. Go to **Settings → API Keys**.
3. You'll see the **Public Key** (`xnd_public_development_...`) — copy it.
4. Click **"Generate secret key"** to create a new secret key. Note: Xendit's 2FA flow is sometimes buggy — if it keeps prompting you to "enable 2FA" even though you have it on, try a fresh incognito window, different browser, or contact Xendit support.
5. Copy the **Secret Key** (`xnd_development_...` for older accounts, or `xnd_secret_development_...` for newer accounts).
6. Paste both into your `.env`:

```env
XENDIT_SECRET_KEY=xnd_development_your_actual_secret_key
XENDIT_PUBLIC_KEY=xnd_public_development_your_actual_public_key
```

7. Restart `php artisan serve`. The payment page banner should change from "Mock mode" to "Xendit test mode active".

### (Optional) Webhook verification token

For production-grade payment status updates (in case the customer closes the tab without returning), set up a webhook:

1. Go to **Settings → Webhooks** in the Xendit dashboard.
2. Click **"Add webhook endpoint"** and enter:
   - URL: `https://your-ngrok-or-deployed-url.com/payment/webhook` (Xendit needs a publicly reachable URL — use `ngrok http 8000` for local development)
   - Events: `payment.succeeded`, `payment.failed`
3. Xendit shows you a **Callback Verification Token**. Paste it into `.env`:

```env
XENDIT_WEBHOOK_TOKEN=your_callback_token_here
```

Without this, the `/payment/return/{ref}` endpoint still works (it verifies the status by retrieving the PaymentRequest). The webhook is only needed for async methods where the customer might not return (e.g., OTC cash payments that take hours/days to complete at the branch).

### Files that make up the payment integration

```
app/Http/Controllers/Web/PaymentController.php    # /pay/{ref}, /pay/{ref}/pay, /mock/{ref}, /mock/{ref}/complete, /payment/return/{ref}, /payment/webhook
app/Services/XenditService.php                     # Xendit API wrapper (createPaymentRequest, retrievePaymentRequest, verifyWebhook)
app/Models/Order.php                                # payment_status, payment_method_type, payment_ref columns + isPaid(), statusBadge() helpers
resources/views/pages/payment.blade.php             # Method picker screen (auto-detects Xendit vs mock mode)
resources/views/pages/mock-gateway.blade.php        # The realistic mock payment page (per-method branded UI)
resources/views/pages/confirmation.blade.php        # Shows payment status (paid / failed / pending)
routes/web.php                                      # /pay/*, /mock/*, /payment/return/*, /payment/webhook routes
database/migrations/2025_01_02_000010_add_payment_fields_to_orders_table.php
```

### How to swap in a different real gateway (Stripe, PayMongo, etc.) later

The order schema is already compatible with any real PaymentIntent-based gateway. To switch:

1. Create a new service class (e.g. `app/Services/StripeService.php`) that wraps the gateway's API.
2. In `PaymentController::pay()`, replace the `$this->xendit->createPaymentRequest(...)` call with `$stripeService->createIntent(...)` and redirect to the gateway's hosted URL.
3. Add a return URL handler that verifies the intent status with the gateway and updates `payment_status` accordingly.
4. Add your real keys to `.env` (e.g. `STRIPE_SECRET_KEY=sk_test_...`).

The Blade views don't need to change — the method picker and confirmation page already render based on `payment_status` and `payment_method_type` from the database.

---

## Product images

The project ships with `[PRODUCT IMAGE]` text placeholders for all 12 products (matching the original school-project aesthetic). You can drop in real product photos and they'll automatically replace the placeholders — no code changes needed.

### Setup (one-time)

```bash
php artisan storage:link
```

This creates a symlink at `public/storage` → `storage/app/public` so images stored in `storage/app/public/` are web-accessible.

### Where to put the images

Put image files in:

```
storage/app/public/products/
```

### File naming convention

The `Product::imageUrl()` method looks for files in this order (first match wins):

| Priority | Pattern | Example | Notes |
|---|---|---|---|
| 1 | `{public_id}.{ext}` | `p1.jpg`, `p1.png`, `p1.webp` | **Recommended** — matches the IDs in the database |
| 2 | `{sku}.{ext}` | `DG-CLS-10.jpg` | Useful if you have product photos named by supplier SKU |
| 3 | `{slug}.{ext}` | `classic-pizza-dough-balls.jpg` | Slug of the product name |

Supported extensions: `.jpg`, `.jpeg`, `.png`, `.webp`, `.avif` (whichever exists first is used).

### Thumbnails (product detail page)

The product detail page shows 1 main image + 3 thumbnails. The thumbnails are looked up by:

| Priority | Pattern | Example |
|---|---|---|
| 1 | `{public_id}-{N}.{ext}` | `p1-2.jpg`, `p1-3.jpg`, `p1-4.jpg` |
| 2 | `{sku}-{N}.{ext}` | `DG-CLS-10-2.jpg` |
| 3 | `{slug}-{N}.{ext}` | `classic-pizza-dough-balls-2.jpg` |

Where `{N}` is 2, 3, or 4.

### Example folder structure

```
storage/app/public/products/
├── p1.jpg                  # Classic Pizza Dough Balls — main
├── p1-2.jpg                # Classic Pizza Dough Balls — thumbnail 2
├── p1-3.jpg                # Classic Pizza Dough Balls — thumbnail 3
├── p1-4.jpg                # Classic Pizza Dough Balls — thumbnail 4
├── p2.jpg                  # Whole Wheat Dough Balls
├── p3.jpg
├── ...
└── p12.jpg                 # Hawaiian Pizza
```

### Recommended image specs

- **Format**: JPG (photographic) or WebP (smaller, modern browsers)
- **Size**: 800×800 px (square) for main product images, 400×400 px for thumbnails
- **File size**: under 200 KB per image for fast loading
- **Background**: white or transparent

### If images aren't showing up

1. **Run `php artisan storage:link`** — the symlink must exist (only need to do this once per deployment)
2. **Check `APP_URL` in `.env`** — should match your dev server URL, e.g. `APP_URL=http://localhost:8000`. If it says just `http://localhost` (no port), image URLs will be wrong.
3. **Check the filename matches a known product ID** (`p1` through `p12`) or SKU
4. **Check the file is in `storage/app/public/products/`** (not `public/products/` — that folder is for files you committed to git, but `storage/app/public/` is the conventional place for runtime assets)
5. **Clear the view cache**: `php artisan view:clear`

### How the image URL is determined

The `App\Models\Product::imageUrl()` method:

1. Looks for a file matching `{public_id}.{ext}` in `storage/app/public/products/`
2. If not found, tries `{sku}.{ext}`
3. If not found, tries `{slug-of-name}.{ext}`
4. If nothing matches, returns `null` and the Blade view renders the original `[PRODUCT IMAGE]` text placeholder

The `partials/product-card.blade.php` partial and the `pages/product.blade.php` view both use this method, so adding an image file on disk automatically updates the home page carousel, shop grid, product detail page, cart upsells, and starter-kit builder — everywhere a product card appears.

---

## Email — order confirmation

When a customer places an order (COD or online), an HTML order confirmation email is automatically sent to the email address they entered in the delivery form.

### Default mode: log (no real email sent)

Out of the box, `MAIL_MAILER=log` in `.env`. This means emails are written to `storage/logs/laravel.log` instead of being actually sent. This is the safest setting for development — you can see the full email content in the log file without needing an SMTP server.

To check if the email was "sent":
```bash
# After placing an order, look in the log file:
# Windows: open storage\logs\laravel.log in Notepad
# Mac/Linux: tail -100 storage/logs/laravel.log
```

You'll see the full email including headers, HTML body, order items, totals, and delivery details.

### Sending real emails via Gmail SMTP

For a school project demo where you want the customer to actually receive the email:

1. **Enable 2-Step Verification** on your Google account:
   - Go to <https://myaccount.google.com/security>
   - Turn on "2-Step Verification" (required for App Passwords)

2. **Create an App Password**:
   - Go to <https://myaccount.google.com/apppasswords>
   - Create a new app password for "Mail"
   - Copy the 16-character password (e.g. `abcd efgh ijkl mnop`)

3. **Update `.env`**:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=yourgmail@gmail.com
MAIL_PASSWORD=abcd efgh ijkl mnop    ← your 16-char App Password (spaces are fine)
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=yourgmail@gmail.com
MAIL_FROM_NAME="Danielle's Crafted Pizza"
```

4. **Restart the server**:
```cmd
php artisan serve
```

5. Place an order with a real email address — the confirmation email will arrive in the customer's inbox within a few seconds.

### What the email contains

The order confirmation email includes:

- **Green checkmark** + "Thank you for your order, {firstName}!" heading
- Order number (e.g. `DCP-95513521`)
- Payment status badge (Paid / Cash on Delivery / Pending / Failed)
- Full list of items ordered with quantities and prices
- Subtotal, shipping cost, and total
- Delivery address (name, street, city, postal code, phone)
- Payment method (Cash on Delivery / Online Payment via GCash / etc.)
- Order notes (if the customer entered any)
- For COD orders: a note to prepare cash for the rider
- For pending payments: a note that payment is being processed

### Files that make up the email feature

```
app/Mail/OrderConfirmation.php                    # Mailable class (builds the email)
resources/views/emails/order-confirmation.blade.php  # HTML email template
app/Http/Controllers/Web/CheckoutController.php   # Sends the email after order placement
config/mail.php                                    # Mail driver configuration
.env                                               # MAIL_* settings
```

### Troubleshooting

| Issue | Fix |
|---|---|
| Email not showing up in `storage/logs/laravel.log` | Check that `MAIL_MAILER=log` is set in `.env`. Restart the server. |
| `Failed to send order confirmation email` in the log | If using SMTP, check that `MAIL_USERNAME` and `MAIL_PASSWORD` are correct. For Gmail, make sure you're using an App Password, not your regular password. |
| Gmail blocks the email with "Less secure app" error | You must use an App Password (2-Step Verification required). Regular passwords don't work anymore. |
| Email goes to spam folder | Use a real domain for `MAIL_FROM_ADDRESS` instead of `@gmail.com` for production. For school projects, just mark it as "Not spam" in the recipient's inbox. |
| Order goes through but no email | The email sending is wrapped in a try-catch — check `storage/logs/laravel.log` for `Failed to send order confirmation email` warnings. |

### Sending email on payment success (optional enhancement)

Currently, the email is sent when the order is **placed** (before payment for online orders). If you want a second email after successful payment, add this to `PaymentController::return()` after `$order->update(['payment_status' => 'paid'])`:

```php
use App\Mail\OrderConfirmation;
use Illuminate\Support\Facades\Mail;

if ($order->payment_status === 'paid' && !$order->email_sent) {
    Mail::to($order->email)->send(new OrderConfirmation($order));
    $order->update(['email_sent' => true]);
}
```

(This would require adding an `email_sent` boolean column to the orders table.)

---

## Original project credit

Original static HTML/CSS/JS project by `villain1269`:
https://github.com/villain1269/E-commerce-final-proj

This Laravel port preserves all UI, copy, and product data 1:1, and adds a full
e-commerce payment flow on top (mock gateway by default, optional real Xendit
test mode when keys are configured).
