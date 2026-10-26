"""Turn a wget mirror of the local WordPress store into a static, JS-free design
preview for GitHub Pages. Cart/checkout need a real WordPress host, so buy
buttons become add-to-cart buttons for a browser-side cart whose checkout sends the
order to the store's WhatsApp (tools/static-cart). Set NUZUL_WHATSAPP=962XXXXXXXXX
to bake in the real number."""
import os, re, sys
from bs4 import BeautifulSoup

SRC = sys.argv[1]
# Optional: WooCommerce Store API dump (wc/store/v1/products) -> enables the WhatsApp cart.
PRODUCTS_JSON = sys.argv[2] if len(sys.argv) > 2 else None
CART_DIR = os.path.join(os.path.dirname(os.path.abspath(__file__)), "static-cart")
WHATSAPP = re.sub(r"\D", "", os.environ.get("NUZUL_WHATSAPP", ""))  # store number, 962XXXXXXXXX; empty = not set yet

GOVERNORATES = {"AM": "عمّان", "IR": "إربد", "AZ": "الزرقاء", "BA": "البلقاء", "MD": "مادبا", "JA": "جرش",
                "AJ": "عجلون", "MA": "المفرق", "KA": "الكرك", "AT": "الطفيلة", "MN": "معان", "AQ": "العقبة"}
SHIPPING = {"AM": 2, "other": 3, "freeOver": 35}  # keep in sync with inc/setup.php shipping zones

PRODUCTS = []
if PRODUCTS_JSON:
    import json, shutil
    for it in json.load(open(PRODUCTS_JSON, encoding="utf-8")):
        minor = it["prices"].get("currency_minor_unit", 2)
        thumb = (it.get("images") or [{}])[0].get("thumbnail", "")
        PRODUCTS.append({
            "id": it["id"], "sku": it["sku"], "name": it["name"],
            "price": int(it["prices"]["price"]) / (10 ** minor),
            "slug": it["permalink"].rstrip("/").split("/")[-1],
            "img": re.sub(r"^https?://[^/]+", "", thumb),
        })
    shutil.copytree(CART_DIR, os.path.join(SRC, "assets", "nz"), dirs_exist_ok=True)
    # Checkout page: clone the contact page (same depth) and swap its content for the checkout mount point.
    contact = os.path.join(SRC, "contact", "index.html")
    if os.path.exists(contact):
        page = BeautifulSoup(open(contact, encoding="utf-8").read(), "html.parser")
        title = page.select_one(".wp-block-post-title")
        if title:
            title.string = "إتمام الطلب"
        content = page.select_one(".wp-block-post-content")
        if content:
            content.clear()
            content.append(BeautifulSoup('<div id="nz-checkout-root" class="alignwide"><p>جارٍ تحميل السلة…</p></div>', "html.parser"))
        if page.title:
            page.title.string = "إتمام الطلب – نُزُل"
        os.makedirs(os.path.join(SRC, "order"), exist_ok=True)
        open(os.path.join(SRC, "order", "index.html"), "w", encoding="utf-8").write(str(page))
ID2SKU = {str(p["id"]): p["sku"] for p in PRODUCTS}

STRINGS = {
    "Add to cart": "أضف إلى السلة",
    "Showing all 19 results": "عرض جميع المنتجات (19)",
    "Description": "الوصف",
    "Related products": "منتجات ذات صلة",
    "Category:": "التصنيف:",
    "SKU:": "رمز المنتج:",
    "Home": "الرئيسية",
    "Shop": "المتجر",
}

BANNER = ('<div class="nuzul-demo-bar" dir="rtl">نسخة عرض للتصميم. السلة والدفع بيشتغلوا بعد تركيب الموقع على '
          'استضافة ووردبريس.</div>')
CSS = ('<style>.nuzul-demo-bar{background:#B3894A;color:#142321;text-align:center;font:500 13px/1.6 "Readex Pro",Tahoma,sans-serif;'
       'padding:6px max(16px,env(safe-area-inset-left,0px))}.wc-block-mini-cart,.woocommerce-ordering,.wp-block-woocommerce-catalog-sorting,'
       '.wc-block-customer-account,.wp-block-woocommerce-customer-account{display:none!important}.woocommerce-product-gallery{opacity:1!important}'
       '.wp-block-navigation__responsive-container.is-menu-open{display:flex}</style>')
NAV_JS = ('<script>document.addEventListener("click",function(e){var o=e.target.closest(".wp-block-navigation__responsive-container-open");'
          'var c=e.target.closest(".wp-block-navigation__responsive-container-close");var nav=e.target.closest(".wp-block-navigation");'
          'if(!nav)return;var r=nav.querySelector(".wp-block-navigation__responsive-container");if(o){r.classList.add("is-menu-open","has-modal-open");}'
          'if(c){r.classList.remove("is-menu-open","has-modal-open");}});</script>')


def product_link(el):
    card = el.find_parent("li")
    if not card:
        return None
    a = card.find("a", href=re.compile(r"product/"))
    return a["href"] if a else None


for root, _, files in os.walk(SRC):
    for f in files:
        if not f.endswith(".html"):
            continue
        p = os.path.join(root, f)
        soup = BeautifulSoup(open(p, encoding="utf-8").read(), "html.parser")
        for s in soup.find_all("script"):
            s.decompose()
        for l in soup.find_all("link", rel=lambda v: v and ("modulepreload" in v or "EditURI" in v or "shortlink" in v or "alternate" in v)):
            l.decompose()
        # Buy buttons -> add-to-cart (WhatsApp cart) or, without product data, product page links
        done_cards = set()
        for el in soup.select(".add_to_cart_button, .wc-block-components-product-button__button, .wc-block-components-product-button a, .wc-block-components-product-button button"):
            card = el.find_parent("li")
            if card is not None and id(card) in done_cards:
                el.decompose()  # Woo's hidden "View cart" link etc. — one button per card
                continue
            if card is not None:
                done_cards.add(id(card))
            m = re.search(r"\bpost-(\d+)\b", " ".join(card.get("class", []))) if card else None
            sku = ID2SKU.get(m.group(1)) if m else None
            if sku:
                b = soup.new_tag("button", type="button")
                b["class"] = ["button", "wp-element-button", "wp-block-button__link", "wc-block-components-product-button__button", "nz-add"]
                b["data-nz-add"] = sku
                b.string = "أضف إلى السلة"
                el.replace_with(b)
                continue
            href = product_link(el)
            if not href:
                el.decompose()
                continue
            a = soup.new_tag("a", href=href)
            a["class"] = ["button", "wp-element-button", "wp-block-button__link", "wc-block-components-product-button__button"]
            a.string = "عرض المنتج"
            el.replace_with(a)
        body_m = re.search(r"\bpostid-(\d+)\b", " ".join(soup.body.get("class", []))) if soup.body else None
        page_sku = ID2SKU.get(body_m.group(1)) if body_m else None
        for form in soup.select("form.cart"):
            if page_sku:
                box = BeautifulSoup(
                    '<div class="nz-buy"><div class="nz-buy-qtywrap">'
                    '<button type="button" class="nz-buy-step" data-nz-step="1" aria-label="زيادة">+</button>'
                    '<input class="nz-buy-qty" type="number" min="1" max="99" value="1" aria-label="الكمية">'
                    '<button type="button" class="nz-buy-step" data-nz-step="-1" aria-label="إنقاص">−</button></div>'
                    f'<button type="button" class="nz-btn nz-add" data-nz-add="{page_sku}">أضف إلى السلة</button></div>',
                    "html.parser")
                form.replace_with(box)
            else:
                form.decompose()
        for a in soup.select(".nuzul-wa-product a"):
            a.string = "أو اطلب مباشرة عبر واتساب" if page_sku else "اطلب عبر واتساب"
            a["class"] = ["nuzul-wa-link", "is-outline" if page_sku else "is-solid"]
        # Placeholder WhatsApp number must not reach a real person from the demo.
        for a in soup.find_all("a", href=re.compile(r"wa\.me/")):
            a["href"] = re.sub(r"wa\.me/\d+", "wa.me/" + WHATSAPP, a["href"])
        html = str(soup)
        prefix = os.path.relpath(SRC, root).replace(os.sep, "/") + "/"
        prefix = "" if prefix == "./" else prefix
        html = re.sub(r"http://localhost:8080/my-account/", "#", html)
        html = html.replace("http://localhost:8080/", prefix)
        html = re.sub(r"%20%28http%3A%2F%2Flocalhost%3A8080[^%]*(%2F[^%\"]*)*%29", "", html)
        shown = ("0" + WHATSAPP[3:6] + " " + WHATSAPP[6:9] + " " + WHATSAPP[9:]) if WHATSAPP.startswith("962") else "07X XXX XXXX"
        html = html.replace("079 123 4567", shown).replace("962791234567", WHATSAPP)
        html = re.sub(r"Showing all (\d+) results", r"عرض جميع المنتجات (\1)", html)
        html = html.replace("Showing the single result", "منتج واحد")
        for en, ar in STRINGS.items():
            html = re.sub(r">(\s*)" + re.escape(en) + r"(\s*)<", lambda m: ">" + m.group(1) + ar + m.group(2) + "<", html)
        html = html.replace("</head>", CSS + "</head>", 1)
        cart = ""
        if PRODUCTS:
            import json as _j
            data = {"base": prefix, "products": PRODUCTS,
                    "config": {"whatsapp": WHATSAPP, "currency": "د.أ", "shipping": SHIPPING, "governorates": GOVERNORATES}}
            html = html.replace("</head>", f'<link rel="stylesheet" href="{prefix}assets/nz/nuzul-cart.css"></head>', 1)
            cart = ("<script>window.NUZUL=" + _j.dumps(data, ensure_ascii=False).replace("</", "<\\/") + ";</script>"
                    f'<script src="{prefix}assets/nz/nuzul-cart.js" defer></script>')
        html = html.replace("</body>", NAV_JS + cart + "</body>", 1)
        open(p, "w", encoding="utf-8").write(html)
print("done")
