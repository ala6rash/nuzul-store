# Room & Salt — project guide for Claude Code

## What this is

**Room & Salt** is a B2C online store in Jordan selling, to individuals, the hotel-room items and Dead Sea
products shown in a hotel-lobby showroom: Egyptian-cotton towels, bathrobes, slippers, pillows, pillowcases,
mattress protectors, fitted sheet sets, mattresses, bath amenities, and 27 Dead Sea cosmetics.
The owner is Ahmad Al-Atrash's manager. Ahmad (repo owner, `ala6rash`) builds and maintains it.

- Tagline: **«غرفة الفندق، وملح البحر الميت، في بيتك.»**
- Renamed from **Nuzul (نُزُل)**: that name is a registered Jordanian trademark (2007, hotel services, another
  owner). Room & Salt still needs an official search at the Ministry of Industry and Trade.
- Business model: retail (B2C). Not B2B.
- Payment: **cash on delivery + CliQ instant transfer** now (CliQ = WooCommerce BACS gateway relabelled).
  Owner wants card payments: add a Jordanian gateway (HyperPay / PayTabs / bank) after merchant approval.
  Stripe does not support Jordanian businesses.
- Shipping (placeholders, confirm with owner): Amman 2 JOD, other governorates 3 JOD, free ≥ 35 JOD.
  Aramex's public Amman→Amman 1 kg rate was 4.00 JOD (Oct 2026). Mattresses need their own delivery price.
- Returns (placeholder): 7 days, unopened; personal-care and Dead Sea items not returnable once opened.

Stack: **WordPress 6.6+ (block theme) + WooCommerce**. Hosting + domain not bought yet
(Hostinger Premium $47.88 / Unlimited $59.88 for 12 months ex-VAT, Oct 2026; renewals cost more).

## Repo map

```
theme/room-salt/          The current WordPress block theme (the product)
  inc/catalog.json        SINGLE SOURCE of categories + products: names, sizes, prices (from showroom
                          price cards), art type, real-photo flag. price null = "السعر عند الطلب".
  inc/catalog.php         Loads catalog.json
  inc/setup.php           Appearance → إعداد المتجر: one-click setup (JOD, COD, CliQ, zones, categories,
                          44 products with images + real photo gallery, pages incl. terms/privacy). Idempotent.
  functions.php           WhatsApp float + product button, price-on-request, Arabic button labels,
                          illustration note, Jordan checkout + governorates, "د.أ"
  patterns/               header, hero (arch + Dead Sea SVG), promises, categories, featured, dead-sea,
                          how-to-order, faq, footer
  assets/fonts            Noto Naskh Arabic, IBM Plex Sans Arabic (300–600), Bodoni Moda (+italic) — OFL
  assets/img              Logos (text outlined SVG) + PNG icons
  assets/art              Product + category illustrations (900×900 JPG, generated)
  assets/real             Real showroom photos (second gallery image, 11 products)
brand/room-salt/          Logos, product art, screenshots
tools/room-salt/          brand.py (logo), art.py + split.py (illustrations via headless Edge), real.py
                          (showroom photos), towoff2.py (fonts), run-local.sh + playground-blueprint.json
download/                 room-salt-theme.zip, room-salt-brand-kit.zip (rebuild after theme changes)
theme/nuzul/, docs/, brand/ (old files), tools/*.py|sh   Previous Nuzul version, kept for reference.
```

`docs/` (GitHub Pages) is a static Room & Salt demo (cart → WhatsApp). `docs/data.js` mirrors catalog.json text. GitHub's terms forbid running a store on Pages;
it is a preview only.

## Brand identity

Concept: the hotel room (arch doorway) + the Dead Sea (water, salt crystal, Moab mountains).
Logo mark = arch with mineral water and a floating terracotta salt crystal; wordmark "ROOM & SALT" in
Bodoni Moda, ampersand italic in Moab.

| Token | Hex | Use |
|---|---|---|
| salt | `#F7F4EE` | page background |
| sand | `#ECE5D9` | bands, linen image backgrounds |
| mist | `#E4ECE9` | Dead Sea image backgrounds |
| deep | `#12343A` | primary: buttons, headings, top bar |
| mineral | `#2D7A78` | accent, WhatsApp buttons |
| moab | `#B4613A` | decoration, eyebrows rules |
| moab-deep | `#8E4A2A` | small terracotta text |
| ink | `#0F1B1E` | body text, footer |

Arabic typography rules: never letter-spacing on Arabic; line-height ≥ 1.3 headings / ≥ 1.5 body; Latin
accents wrapped in `<span lang="en" dir="ltr">` (only those get tracking).

## Copy rules (owner asked explicitly)

- Customer-facing text in **Modern Standard Arabic**, professional tone (not dialect).
- **Only accurate statements**: names/sizes/prices come from the showroom cards. No invented specs.
- **No fake reviews, ratings or customer counts.** Reviews disabled.
- **About page stays empty** (`rs_page_about()` returns `''`) until the owner provides text.
- Terms and privacy pages are drafts — must be reviewed by a lawyer before launch.
- Supplier certificate (Beauty Secret GMP) shown in photos **expired Jan 2024** — do not display it.

## Store controls (inc/)

- `setup.php` also: stock tracking + low-stock email, optional opening quantity, Arabic order-email subjects in brand colours
  (`assets/img/logo-email.png`), optional Shop Manager account, "sync" option that rewrites existing product names,
  descriptions, slugs (from `en`) and order from catalog.json. Card section links the official HyperPay plugin (`hyperpay-gateways`).
- `security.php`: 5 failed logins → 15-min lock, generic login error, no XML-RPC, no author/user enumeration, DISALLOW_FILE_EDIT, headers.
- `seo.php`: meta description + Open Graph (skipped if an SEO plugin is active), OnlineStore JSON-LD on home, WebP thumbnails.
  WooCommerce itself outputs Product JSON-LD (price, stock, SKU).
- `functions.php`: free shipping hides paid rates; Arabic fallback for WooCommerce labels when its language pack is missing.
- `LAUNCH.md`: Arabic owner guide (install, caching, backups, updates, daily orders, cards).

Testing in Playground: SQLite can't run WooCommerce's stock-reservation SQL; the test blueprint adds a mu-plugin returning 0
from `woocommerce_order_hold_stock_minutes`. Not needed on MySQL hosting. Playground auto-login resets the login counter
between HTTP requests; the lockout was verified inside one request.

## Home page layout (boutique style)

Front page order: hero (full-bleed scene + curved panel, price pill) → trust row → `linen` (towels split + slider) →
`dead-sea` (wide banner) → `sea-care` (arch art + Dead Sea slider) → `showroom` (round image) → how-to-order → FAQ.
Sliders: `[rs_carousel cats="…" limit=""]` in functions.php, live WooCommerce data, `assets/js/carousel.js` (RTL-aware).
Scenes in `assets/scenes/` are built by `tools/room-salt/scene.py` from real product cut-outs (no stock or AI photos).
The docs/ demo mirrors the same sections. Layout inspired by hotel-boutique stores; no third-party brand assets or copy.

## Product copy

`tools/room-salt/copy_catalog.py` holds the edited copy (MSA). Dead Sea effects are attributed to the manufacturer
(«تقول الشركة المصنّعة…») and come from b-secrets.com product pages; sizes only where printed on the pack.

## Open items

- WhatsApp number, CliQ alias, Instagram, email: placeholders (set on the setup page).
- Prices missing: slippers, plain pillowcase, fitted sheet set. "burberry" mask on the price list read as berry — confirm.
- Product photos: illustrations now; get supplier photos (Rest One, Astoria, Beauty Secret) or shoot on white.
- Card gateway: needs commercial registration, company bank account, live https site, policy pages.

## Common tasks

```bash
bash tools/room-salt/run-local.sh      # local store at http://127.0.0.1:9400 (fresh each run)
for f in theme/room-salt/functions.php theme/room-salt/inc/*.php theme/room-salt/patterns/*.php; do php -l "$f"; done
python tools/room-salt/zipdir.py theme/room-salt download/room-salt-theme.zip room-salt   # rebuild theme zip
```

On Git Bash, Playground mounts need `MSYS_NO_PATHCONV=1` (otherwise `/wordpress/...` becomes a Windows path).
Check pages at ~390px and 1366px; headless Edge can't go below ~496px viewport.

## Git

- Default branch `main`; Pages builds from `main:/docs`. Commit messages in English, imperative.
