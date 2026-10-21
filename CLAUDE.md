# Nuzul (نُزُل) — project guide for Claude Code

## What this is

**Nuzul** is a B2C online store in Jordan selling hotel-room amenities to individuals for use at home:
towels, bathrobes, slippers, toiletry sets, bathroom accessories, room/pillow sprays, and ready-made gift boxes.
The owner is Ahmad Al-Atrash's manager. Ahmad (repo owner, `ala6rash`) builds and maintains it.

- Tagline: **«رفاهية الفندق، في منزلك.»**
- Business model: retail (B2C). Not B2B. Customers buy themselves.
- Payment: **cash on delivery only** (no card gateway).
- Shipping: Amman 2 JOD, other governorates 3 JOD, free on orders ≥ 35 JOD. Jordan only.
- Returns: 7 days, unopened items; personal-care items not returnable once opened.

Stack: **WordPress 6.6+ (block theme) + WooCommerce**. Everything is free/open-source; the only paid
items are hosting + domain (not bought yet).

## Live links

| | |
|---|---|
| Static preview (GitHub Pages) | https://ala6rash.github.io/nuzul-store/ |
| Brand book + launch guide | https://ala6rash.github.io/nuzul-store/brand/ |
| Repo | https://github.com/ala6rash/nuzul-store |

GitHub Pages serves `docs/` from `main`. It is a **static export** of a local WordPress install:
no cart, no checkout. Buy buttons were rewritten to "عرض المنتج" (view product). The real store works
only after installing WordPress + WooCommerce on a host.

## Repo map

```
theme/nuzul/              The WordPress block theme (the actual product)
  style.css               Theme header + all custom CSS (header, hero, door hanger, grids, Woo tweaks)
  theme.json              Design tokens: palette, fonts (self-hosted woff2), sizes, spacing, element styles
  functions.php           Theme setup, WhatsApp float + product button, Jordan checkout tweaks,
                          governorates (woocommerce_states), JOD symbol "د.أ", helper fns
  inc/catalog.php         6 categories + 19 starter products (Arabic copy, suggested JOD prices)
  inc/setup.php           Admin page Appearance → "إعداد متجر نُزُل": one-click store setup (idempotent)
  patterns/*.php          Header, footer, and home sections (hero, promises, categories, featured,
                          gift-band, how-to-order, faq). PHP patterns so URLs are dynamic.
  templates/, parts/      Block templates; parts just include the header/footer patterns
  assets/fonts            readex-pro.woff2, reem-kufi.woff2, marcellus.woff2
  assets/img              Logos (SVG)
  assets/products         Placeholder product illustrations (PNG, 800×800)
brand/                    Logo SVG/PNG, product illustrations SVG/PNG (source + renders)
docs/                     GENERATED static site for GitHub Pages (+ docs/brand/ = brand book). Don't hand-edit.
download/                 nuzul-theme.zip, nuzul-brand-kit.zip (rebuild after theme changes)
tools/
  local-wp.sh             Local WordPress + WooCommerce on SQLite with the theme symlinked; serves :8080
  build_demo.sh           wget-mirrors the local site → runs make_demo.py → static site folder
  make_demo.py            Post-processes the mirror (strips JS, rewrites buy buttons, relative URLs)
  make_logo.py            Generates logo SVGs with text outlined (needs TTF fonts + uharfbuzz)
  make_products.py        Generates the placeholder product illustrations (SVG)
  render_svgs.js          Renders SVG → PNG with Playwright
```

## Brand identity (keep consistent)

Concept: hotel details at home. Logo = **hotel room key fob on a brass ring with the letter ن engraved**.
Recurring motifs: "Do Not Disturb" door hanger (hero), brass room-number plaque ("ROOM 214"),
category numbers "No. 101–106", section eyebrows = hotel word in Latin + Arabic meaning:
`ROOM SERVICE · خدمة الغرف`, `FLOORS · الأقسام`, `MOST REQUESTED · مختارات`, `LATE CHECKOUT · هدية جاهزة`,
`CHECK-IN · خطوات الطلب`, `CONCIERGE · الأسئلة الشائعة`.

| Token | Hex | Use |
|---|---|---|
| linen | `#F5F6F3` | page background |
| porcelain | `#E4EAE6` | bands, image backgrounds |
| eucalyptus | `#1E4640` | primary: buttons, top bar, logo |
| ink | `#142321` | text, footer |
| brass | `#B3894A` | decoration only (ring, rules) — fails contrast for small text |
| brass-deep | `#86622B` | small brass-coloured text |

Fonts (Google Fonts, OFL, self-hosted in the theme): **Reem Kufi** (h1/h2 only), **Readex Pro** (body, h3,
prices, buttons), **Marcellus** (Latin plaque labels only).

## Copy rules (important — the owner asked for this explicitly)

- All customer-facing text is **Modern Standard Arabic, professional tone** (not Jordanian dialect).
- **Only accurate, verifiable statements.** No "same suppliers as hotels", no "lasts for hours", no
  superlatives. Product descriptions = specs (gsm, cotton %, ml, sizes, piece counts).
- **Never add fake reviews, testimonials, ratings or customer counts.** Reviews are disabled.
- The **"من نحن" (About) page is intentionally empty** (`nuzul_page_about()` returns `''`). Leave it empty
  until the owner provides text.
- Prices in `inc/catalog.php` are **suggestions**; the owner sets real prices.

## Open items / placeholders

- **WhatsApp number is not set.** Theme default `962790000000` is a placeholder. The real number is entered
  on the setup page or Settings → General (`nuzul_whatsapp`, international format, digits only).
  In the static preview, `make_demo.py` strips the number (`wa.me/` with no number) and shows `07X XXX XXXX`
  — so no real person gets messaged. Once the owner gives the real number, set it locally, rebuild the demo,
  and remove that stripping.
- Instagram handle `nuzul.jo` and email are placeholders.
- Product images are illustrations; replace with real square photos (same filenames or via WooCommerce).
- Name/trademark availability (Jordan trade registry, domain, Instagram) not checked yet.
- Hosting + domain not purchased.

## How the theme works

- `functions.php`
  - `nuzul_opt()`, `nuzul_wa_number()`, `nuzul_wa_url($text)`: contact settings.
  - Floating WhatsApp button (`wp_footer`, hidden on cart/checkout); "أو اطلب عبر واتساب" appended to the
    product add-to-cart block via `render_block_woocommerce/add-to-cart-form`.
  - `woocommerce_states` adds Jordan's 12 governorates (Woo ships none) → used by shipping zones.
  - `woocommerce_get_country_locale` (JO): postcode/company hidden, state = "المحافظة" required.
  - `hooked_block_types`: stops Woo auto-inserting a second account/cart icon next to the nav.
  - `nuzul_link()` builds URLs (shop / `cat:slug` / page slug) so patterns survive sub-folder installs.
- `inc/setup.php` → `nuzul_run_setup()`: JOD currency, Jordan-only selling, COD on (Arabic texts),
  phone required, reviews off, shipping zones (Amman / other governorates, flat + free ≥ 35),
  categories with images, products (slug = image key, English slugs share better on WhatsApp),
  pages (delivery-returns, about [empty], contact), pretty permalinks. Skips anything that already exists.
- Home page = `templates/front-page.html` → patterns. Featured products use the `[products visibility="featured"]`
  shortcode rendered via `do_shortcode` inside the pattern.
- Shop/category/product pages use WooCommerce's own block templates + our header/footer + CSS.

## Common tasks

```bash
# Run a local store (first run installs everything into .local-wp/, gitignored)
tools/local-wp.sh            # http://localhost:8080, admin/admin
tools/local-wp.sh serve      # restart server later

# Lint PHP after edits
for f in theme/nuzul/functions.php theme/nuzul/inc/*.php theme/nuzul/patterns/*.php; do php -l "$f"; done

# Changed catalog/pages copy? setup skips existing items, so delete then re-run:
W="php .local-wp/wp-cli.phar --allow-root --path=.local-wp/site"
$W post delete $($W post list --post_type=product --format=ids) --force
$W eval 'nuzul_run_setup(["whatsapp"=>"","instagram"=>"","email"=>"","products"=>true]);'

# Rebuild the GitHub Pages preview (local site must be running)
tools/build_demo.sh http://localhost:8080/ /tmp/nuzul-demo
rm -rf docs && mv /tmp/nuzul-demo.new docs && touch docs/.nojekyll
#   then restore docs/brand/index.html and docs/preview.png from git if needed:
git checkout HEAD -- docs/brand docs/preview.png

# Rebuild downloadable zips
(cd theme && zip -qr ../download/nuzul-theme.zip nuzul)
```

Before committing UI changes: check the home, shop, a product, cart and checkout at **390px and 1366px**
width, and make sure nothing overflows horizontally (RTL layouts overflow to the left/right easily —
e.g. absolutely positioned hero elements, fixed-width images).

## Deploying the real store (for when hosting is bought)

1. Hosting with WooCommerce support + domain.
2. Install WordPress, Settings → General → Site language: العربية.
3. Install + activate WooCommerce (skip its onboarding wizard).
4. Appearance → Themes → Upload `download/nuzul-theme.zip` → Activate.
5. Appearance → **إعداد متجر نُزُل** → enter WhatsApp number → "جهّز المتجر".
6. Set real prices and photos; place a test order from a phone; cancel it.

## Git

- Default branch `main`; Pages builds from `main:/docs` automatically on push.
- Commit messages in English, imperative.
