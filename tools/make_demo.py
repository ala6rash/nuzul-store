"""Turn a wget mirror of the local WordPress store into a static, JS-free design
preview for GitHub Pages. Cart/checkout need a real WordPress host, so buy
buttons become "view product" links and a banner says it's a preview."""
import os, re, sys
from bs4 import BeautifulSoup

SRC = sys.argv[1]

STRINGS = {
    "Add to cart": "أضف للسلة",
    "Showing all 19 results": "كل المنتجات (19)",
    "Description": "الوصف",
    "Related products": "منتجات ممكن تعجبك",
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
        # Buy buttons -> product page links
        for el in soup.select(".add_to_cart_button, .wc-block-components-product-button__button, .wc-block-components-product-button a, .wc-block-components-product-button button"):
            href = product_link(el)
            if not href:
                el.decompose()
                continue
            a = soup.new_tag("a", href=href)
            a["class"] = ["button", "wp-element-button", "wp-block-button__link", "wc-block-components-product-button__button"]
            a.string = "شوف المنتج"
            el.replace_with(a)
        for form in soup.select("form.cart"):
            form.decompose()
        for a in soup.select(".nuzul-wa-product a"):
            a.string = "اطلب عالواتساب"
            a["class"] = ["nuzul-wa-link", "is-solid"]
        # Placeholder WhatsApp number must not reach a real person from the demo.
        for a in soup.find_all("a", href=re.compile(r"wa\.me/")):
            a["href"] = re.sub(r"wa\.me/\d+", "wa.me/", a["href"])
        html = str(soup)
        prefix = os.path.relpath(SRC, root).replace(os.sep, "/") + "/"
        prefix = "" if prefix == "./" else prefix
        html = re.sub(r"http://localhost:8080/my-account/", "#", html)
        html = html.replace("http://localhost:8080/", prefix)
        html = re.sub(r"%20%28http%3A%2F%2Flocalhost%3A8080[^%]*(%2F[^%\"]*)*%29", "", html)
        html = html.replace("079 123 4567", "07X XXX XXXX").replace("962791234567", "")
        for en, ar in STRINGS.items():
            html = re.sub(r">(\s*)" + re.escape(en) + r"(\s*)<", lambda m: ">" + m.group(1) + ar + m.group(2) + "<", html)
        html = html.replace("</head>", CSS + "</head>", 1)
        html = re.sub(r"(<body[^>]*>)", r"\1" + BANNER, html, count=1)
        html = html.replace("</body>", NAV_JS + "</body>", 1)
        open(p, "w", encoding="utf-8").write(html)
print("done")
