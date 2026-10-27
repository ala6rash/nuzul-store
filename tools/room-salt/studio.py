import os, sys, json, io, urllib.request
from PIL import Image, ImageOps, ImageFilter, ImageEnhance, ImageDraw, ImageChops
from rembg import remove, new_session
HERE = os.path.dirname(os.path.abspath(__file__)); S = os.path.join(HERE, "studio")
DL = r"C:\Users\AHMAD\Downloads"
sys.path.insert(0, HERE)
SRC = json.load(open(os.path.join(HERE, "room-salt/assets/photos/_sources.json"), encoding="utf-8"))
exec(open(os.path.join(HERE, "realcrop.py"), encoding="utf-8").read().split("for sku")[0].split("D, OUT")[0])  # imports
C = {}
exec("C = {" + open(os.path.join(HERE, "realcrop.py"), encoding="utf-8").read().split("C = {",1)[1].split("}\n",1)[0] + "}")
N = 1200
def backdrop():
    bg = Image.new("RGB", (N, N), "#F4EFE7"); d = ImageDraw.Draw(bg)
    for y in range(N):  # soft vertical light falloff
        t = y / N; c = tuple(int(a + (b - a) * t**1.6) for a, b in zip((248, 245, 239), (233, 225, 213)))
        d.line([(0, y), (N, y)], fill=c)
    return bg
def fetch(sku):
    p = os.path.join(S, "src", sku + ".png")
    if not os.path.exists(p):
        if sku in SRC:
            req = urllib.request.Request(SRC[sku]["image"], headers={"User-Agent": "Mozilla/5.0"})
            Image.open(io.BytesIO(urllib.request.urlopen(req, timeout=60).read())).convert("RGB").save(p)
        else:
            f, box = C[sku]
            ImageOps.exif_transpose(Image.open(os.path.join(DL, f))).convert("RGB").crop(box).save(p)
    return Image.open(p).convert("RGB")
def compose(sku, cut, fill=0.74):
    a = cut.getchannel("A"); bb = a.point(lambda v: 255 if v > 24 else 0).getbbox(); cut = cut.crop(bb)
    w, h = cut.size; k = fill * N / max(w, h); cut = cut.resize((max(1, int(w*k)), max(1, int(h*k))), Image.LANCZOS)
    w, h = cut.size; x = (N - w)//2; y = int(N*0.53 - h/2); y = min(y, int(N*0.9) - h)
    bg = backdrop()
    sh = Image.new("L", (N, N), 0); sd = ImageDraw.Draw(sh)  # contact shadow
    sd.ellipse([x + w*0.08, y + h - h*0.035, x + w*0.92, y + h + h*0.05], fill=120)
    sh = sh.filter(ImageFilter.GaussianBlur(N*0.022))
    drop = Image.new("L", (N, N), 0); drop.paste(cut.getchannel("A").point(lambda v: v*0.28), (x + 10, y + 22))
    sh = ImageChops.lighter(sh, drop.filter(ImageFilter.GaussianBlur(26)))
    bg = Image.composite(Image.new("RGB", (N, N), (92, 78, 62)), bg, sh)
    bg.paste(cut, (x, y), cut)
    return bg
sess = new_session(sys.argv[1] if len(sys.argv) > 1 else "isnet-general-use")
skus = sys.argv[2].split(",") if len(sys.argv) > 2 else sorted(set(SRC) | set(C))
for sku in skus:
    im = fetch(sku)
    if sku in C:
        im = ImageOps.autocontrast(im, cutoff=0.5); im = ImageEnhance.Brightness(im).enhance(1.05)
    big = im.resize((im.width*2, im.height*2), Image.LANCZOS) if max(im.size) < 1000 else im
    cut = remove(big, session=sess, post_process_mask=True)
    cut.save(os.path.join(S, "cut", sku + ".png"))
    out = compose(sku, cut, 0.70 if sku.startswith("RS-DS") else 0.80)
    out = out.filter(ImageFilter.UnsharpMask(1.2, 40, 2))
    out.save(os.path.join(S, "out", sku + ".jpg"), quality=88, optimize=True, progressive=True)
    print(sku, im.size, flush=True)
