# Farms Marketplace — Roadmap & Feature Analysis

*Generated: 2026-03-13 | KharisERP2025*

---

## Current State Overview

The Farms module includes a fully-functional public Livewire marketplace at `/farm-shop/` with:
- ✅ Complete e-commerce flow (catalog → cart → checkout → payment)
- ✅ Multi-gateway payment via PaymentsChannel (MoMo, Paystack, Flutterwave, etc.)
- ✅ Inventory management (stock deduction, auto-status updates)
- ✅ Public order tracking (ref + phone lookup)
- ✅ Email/SMS notifications via CommunicationCentre
- ✅ Single-company cart enforcement
- ✅ Admin CRUD for marketplace orders and produce inventory
- ✅ FarmMarketplaceCluster in company-admin panel

---

## 1. Fleet / Shipping Integration

### Current State: ZERO integration
The Fleet module has `TripLog` (vehicle_id, driver_id, destination_lat/lng, status) but no FK or event links a `FarmOrder` to a `TripLog`.

### What's needed for Fleet integration:
| Component | Description |
|---|---|
| `FarmOrderDelivery` model | Links `farm_orders.id → trip_logs.id`, stores ETA |
| `DispatchAction` in FarmOrderResource | Creates TripLog when order is "ready", assigns driver/vehicle |
| `DeliveryZone` model | Polygons/radius per area — drives fee calculation |
| `OrderTracking` update | Show driver location on map when "out for delivery" |
| Fleet event listener | `TripLog` completed → auto-set `FarmOrder` delivered |

**Status: Phase 2 feature — foundation exists in Fleet, needs a bridge.**

---

## 2. Missing Features — Priority Matrix

### 🔴 Critical (Pre-launch — IMPLEMENTED)
| Feature | Status |
|---|---|
| FarmShopSetting CMS (logo, colors, fees, contact) | ✅ Implemented 2026-03-13 |
| Product images on FarmProduceInventory | ✅ Implemented 2026-03-13 |
| Landmark-based delivery address field | ✅ Implemented 2026-03-13 |
| Dynamic delivery fee from settings | ✅ Implemented 2026-03-13 |
| Customer accounts (register, login, order history) | ✅ Implemented 2026-03-13 |

### 🔴 Critical (Pre-launch — Implemented 2026-03-13)
| Feature | Status |
|---|---|
| Order cancellation by customer | ✅ Cancel pending orders from My Orders (wire:confirm dialog) |
| Minimum order quantity | ✅ `min_order_quantity` on FarmProduceInventory — validated at add-to-cart |
| Forgot/Reset password for ShopCustomer | ✅ `/forgot-password` + `/reset-password/{token}` routes + broker |
| Edit profile (My Account) | ✅ `/my-account` — name, phone, address, landmark, password change |
| Re-order button | ✅ Re-order from delivered/cancelled orders (cart-merge logic) |

### 🔴 Critical (Pre-launch — Implemented 2026-03-13 batch 2)
| Feature | Status |
|---|---|
| Delivery slot selection | ✅ `preferred_delivery_date` on checkout — slots from `delivery_days` setting |
| Product reviews & ratings | ✅ `farm_product_reviews` table — star rating + text, verified purchase badge |
| Promo/coupon code engine | ✅ `farm_coupons` table — percentage/fixed, cart input, FarmCouponResource |

### 🔴 Critical (Pre-launch — COMPLETE)
| Feature | Notes |
|---|---|
| Refund workflow | ✅ Implemented 2026-03-13 — `farm_return_requests` table, `/orders/{id}/refund` form, FarmReturnRequestResource admin CRUD |
| Sell-by-weight pricing | ✅ Implemented 2026-03-14 — quick-weight preset buttons (250g/500g/1kg/2kg) on product page; live total price display; step=0.01 for weight units |

### 🟡 High Priority (Launch — TODO)
| Feature | Notes |
|---|---|
| Wishlist / saved products | ✅ Implemented 2026-03-13 — `farm_customer_wishlists` table, ♡ toggle on product page, `/my-wishlist` |
| Order receipt PDF | ✅ Implemented 2026-03-13 — printable HTML receipt at `/orders/{id}/receipt` (browser print-to-PDF) |
| "Notify when available" | ✅ Implemented 2026-03-13 — `farm_restock_notifications` table; ♡ subscribe on out-of-stock product pages; `farms:notify-restock` command (hourly schedule) |
| Abandoned cart recovery | ✅ Implemented 2026-03-13 — `farm_abandoned_carts` table; saved on cart add/update for logged-in customers; `farms:abandoned-cart-recovery` command (every 30 min) |
| Bulk / wholesale tier pricing | ✅ Implemented 2026-03-13 — `farm_price_tiers` table; PriceTiersRelationManager in FarmProduceInventoryResource; price shown reactively as qty changes; tier badge in cart |

### 🟢 Growth (Post-launch)
| Feature | Notes |
|---|---|
| Subscription/recurring orders | ✅ Implemented 2026-03-13 — `farm_subscriptions` table; weekly/biweekly/monthly; `/my-subscriptions`; pause/resume/cancel; `farms:process-subscriptions` daily command; FarmSubscriptionResource admin |
| Loyalty points system | ✅ Implemented 2026-03-13 — `farm_loyalty_points` table; earn on payment; redeem at checkout; balance shown in My Orders |
| Referral program | ✅ Implemented 2026-03-13 — `farm_referrals` + `referral_code` on ShopCustomer; `?ref=CODE` on register URL; 50 pts awarded to referrer on referred customer's first paid order; referral stats in My Account |
| Bundle deals | ✅ Implemented 2026-03-13 — `farm_bundles` + `farm_bundle_items`; % discount applied per item; `/bundles/{bundle}` detail page; bundle cards on shop index; FarmBundleResource admin CRUD |
| Saved delivery addresses | ✅ Implemented 2026-03-13 — `farm_saved_addresses` table; managed in My Account; selectable cards in checkout step 1 |
| Flash sale scheduler | ✅ Implemented 2026-03-13 — `sale_price`/`sale_starts_at`/`sale_ends_at` on inventory; countdown timer on product page; SALE badge on index |
| Harvest calendar (public) | ✅ Implemented 2026-03-13 — `/farm-shop/harvest-calendar`; groups produce by harvest month; Notify Me for upcoming |
| Blog / recipe content | ✅ Implemented 2026-03-14 — `farm_shop_blog_posts` table (migration 300027); FarmShopBlogPostResource admin CRUD; public `/farm-shop/blog` list + `/farm-shop/blog/{slug}` article; categories: blog/recipe; tags, ingredients (recipes), cover image, reading time; `Blog` link in public nav |
| "Meet the Farm" pages | ✅ Implemented 2026-03-14 — `about`, `cover_image`, `gallery_images`, `video_url`, `established_year` added to `farms` table (migration 300028); Farm model + FarmResource admin form updated; public `/farm-shop/farms/{slug}` FarmProfile page with story, gallery, video embed, and available produce cards |
| WhatsApp Business API | Existing: wa.me link in nav/footer from `whatsapp_number` shop setting — full Business API is a Phase 3 feature |
| B2B/wholesale portal | ✅ Implemented 2026-03-14 — `farm_b2b_accounts` table (migration 300030) with approval workflow, discount_percent, credit_limit, payment_terms (prepay/net7/net14/net30); `is_b2b`+`b2b_account_id` added to `shop_customers` (300031) and `farm_orders` (300032); `FarmB2bAccountResource` admin CRUD with Approve/Reject actions; public `/farm-shop/b2b/apply` multi-step registration form; wholesale price shown on product page; B2B discount + PO number field in checkout; credit-term orders skip payment gateway (status=on_account); `🏢 For Business` link in public nav |
| Fleet dispatch bridge | ✅ Implemented 2026-03-14 — `farm_order_deliveries` table (migration 300029) + `FarmOrderDelivery` model; `DispatchOrderAction` on FarmOrderResource (visible on ready orders) creates TripLog + delivery record; FarmsServiceProvider `saved` hook auto-marks FarmOrder delivered when TripLog→completed |
| PWA (Progressive Web App) | ✅ Implemented 2026-03-14 — web manifest at `/farm-shop/manifest.json` (dynamic, uses shop settings); service worker at `/farm-shop-sw.js` (network-first with cache fallback); PWA meta tags + `<link rel="manifest">` in public layout; `theme-color` from shop primary_color |

---

## 3. Ghana-Specific Priorities

1. **MoMo-First UX** — ✅ Implemented 2026-03-14 — `OrderPayment.php` now sorts `groupedPaymentMethods` so `mobile_money`/`momo` groups are displayed first.
2. **Landmark address field** — ✅ Implemented 2026-03-13.
3. **Delivery day scheduling** — ✅ Implemented 2026-03-13 — `preferred_delivery_date` at checkout.
4. **WhatsApp Business API** — `wa.me` link in nav/footer from shop settings; full Business API is Phase 3.
5. **Price vs. Market comparison** — ✅ Implemented 2026-03-14 — `market_price` column on `farm_produce_inventories` (migration 300026); amber comparison panel on product show page shows "Market (Makola): GHS X → X% cheaper here!".
6. **USSD ordering** — Phase 3 (requires telecom operator integration).
7. **Agent/reseller accounts** — Phase 3 — local agents with commission tracking.

---

## 4. CMS / Website Management (FarmShopSetting)

### Implemented: `farm_shop_settings` table (2026-03-13)

Fields managed from company-admin panel:
- **Branding**: shop_name, tagline, logo_path, favicon_path, primary_color, secondary_color
- **Contact**: phone, whatsapp_number, email, address
- **Delivery**: delivery_fee, free_delivery_above, delivery_days (JSON), order_cutoff_time
- **Homepage**: hero_heading, hero_subheading, hero_image_path, announcement_bar_text, announcement_bar_active
- **SEO**: meta_title, meta_description, og_image_path
- **Social**: facebook_url, instagram_url, twitter_url
- **Footer**: footer_about_text

### Settings flow:
1. Company admin navigates to Farms → Shop Settings in company-admin panel
2. Edits branding, delivery fee, contact info, homepage content
3. Settings cached per company (10-min TTL)
4. All public shop pages read from `ShopSettingsService::get($companyId)`

### CMS — Implemented 2026-03-14:
- ✅ Homepage banner/slider manager — `farm_shop_banners` table; `FarmShopBannerResource` CRUD; Alpine.js auto-play slider on shop index; scheduled start/end dates
- ✅ Navigation menu builder — `farm_shop_nav_items` table; `FarmShopNavItemResource` CRUD; custom items injected in public nav
- ✅ Static page editor — `farm_shop_pages` table; `FarmShopPageResource` CRUD (rich editor); `/farm-shop/pages/{slug}` public route; About Us/Terms/Privacy auto-linked in footer
- ✅ Pop-up / announcement bar with scheduling — popup_active/title/body/cta/starts_at/ends_at fields on `farm_shop_settings`; Alpine.js modal (localStorage dismiss); announcement bar also supports start/end scheduling

---

## 5. Customer Accounts (ShopCustomer)

### Implemented: `shop_customers` table + `shop_customer` auth guard (2026-03-13)

Features:
- Register with name, email, phone, password
- Login with email + password
- My Orders page — full order history with status/payment badges
- Optional link at checkout — if logged in, orders are linked to account
- Guest checkout still works (no forced login)
- Pre-fill checkout fields from account

### Implemented 2026-03-13:
- ✅ Forgot password / reset password flow (`/forgot-password`, `/reset-password/{token}`)
- ✅ Edit profile — name, phone, default address, default landmark, password change (`/my-account`)
- ✅ Re-order button on order history (cart-merge with stock validation)

### TODO for customer accounts:
- ✅ Saved delivery addresses — `farm_saved_addresses`; managed in My Account; selectable in checkout
- ✅ Wishlist / saved products — `farm_customer_wishlists`; toggle on product page; `/my-wishlist`

---

## 6. Technical Notes

### Key files:
- `Modules/Farms/app/Models/FarmShopSetting.php` — shop configuration model
- `Modules/Farms/app/Services/ShopSettingsService.php` — cached settings getter
- `Modules/Farms/app/Filament/Pages/FarmShopSettingsPage.php` — admin settings UI
- `Modules/Farms/app/Models/ShopCustomer.php` — customer authenticatable model
- `Modules/Farms/app/Http/Livewire/Shop/Auth/Register.php` — registration
- `Modules/Farms/app/Http/Livewire/Shop/Auth/Login.php` — login/logout
- `Modules/Farms/app/Http/Livewire/Shop/MyOrders.php` — order history

### Auth guard:
- Guard: `shop_customer`
- Provider: `shop_customers` → `ShopCustomer::class`
- Session-based, separate from ERP `web` guard
- Check: `auth('shop_customer')->check()`

### Payment flow (unchanged):
Checkout → FarmOrder (pending) → PaymentsChannel → PayIntent → Gateway redirect →
OrderPaymentReturn → Verify → FarmOrder (confirmed, paid) → OrderConfirmation

### Integration matrix:
| Module | Integration |
|---|---|
| CommunicationCentre | Order confirmation email/SMS via FarmOrderPlaced event |
| PaymentsChannel | Multi-gateway payments via HasPayments trait |
| Fleet | NOT YET — planned Phase 2 |
| Finance | NOT YET — no Invoice/Receivable created on paid orders |
