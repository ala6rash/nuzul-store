# Builds the home-page scenes (hero, towels, Dead Sea) from real product cut-outs.
# Cut-outs come from studio.py (rembg) or the manufacturer's transparent PNGs.
# Usage: python scene.py <cut-dir> <png-dir> <out-dir>
import os
import sys

from PIL import Image, ImageChops, ImageDraw, ImageFilter

CUT, PNG, OUT = sys.argv[1], sys.argv[2], sys.argv[3]
os.makedirs(OUT, exist_ok=True)


def cutout(sku):
    p = os.path.join(PNG, sku + ".png")
    im = Image.open(p if os.path.exists(p) else os.path.join(CUT, sku + ".png")).convert("RGBA")
    if sku.startswith("RS-TW"):
        # White towels: drop dark threads and shelf edges the cut-out kept, then soften the edge.
        light = im.convert("L").point(lambda v: 255 if v > 95 else 0).filter(ImageFilter.MaxFilter(3)).filter(ImageFilter.MinFilter(3))
        im.putalpha(ImageChops.multiply(im.getchannel("A"), light.filter(ImageFilter.GaussianBlur(0.8))))
    a = im.getchannel("A").point(lambda v: 255 if v > 24 else 0)
    return im.crop(a.getbbox())


def backdrop(w, h, shelf=0.68):
    """Warm wall with soft light from the top-right and a pale shelf surface."""
    bg = Image.new("RGB", (w, h))
    d = ImageDraw.Draw(bg)
    sy = int(h * shelf)
    for y in range(h):
        if y < sy:
            t = y / sy
            c = [int(a + (b - a) * t) for a, b in zip((242, 236, 226), (228, 218, 203))]
        else:
            t = (y - sy) / max(1, h - sy)
            c = [int(a + (b - a) * t) for a, b in zip((236, 228, 216), (222, 211, 195))]
        d.line([(0, y), (w, y)], fill=tuple(c))
    glow = Image.new("L", (w, h), 0)
    ImageDraw.Draw(glow).ellipse([w * 0.45, -h * 0.5, w * 1.3, h * 0.7], fill=60)
    glow = glow.filter(ImageFilter.GaussianBlur(w * 0.08))
    bg = Image.composite(Image.new("RGB", (w, h), (252, 249, 243)), bg, glow)
    edge = Image.new("L", (w, h), 0)
    ImageDraw.Draw(edge).line([(0, sy), (w, sy)], fill=90, width=2)
    return Image.composite(Image.new("RGB", (w, h), (205, 192, 174)), bg, edge.filter(ImageFilter.GaussianBlur(1.5))), sy


def place(bg, items, floor):
    """items: (sku, height_px, center_x); each stands on the shelf line with a contact shadow."""
    w, h = bg.size
    shadow = Image.new("L", (w, h), 0)
    sd = ImageDraw.Draw(shadow)
    placed = []
    for sku, ht, cx, *dy in items:
        im = cutout(sku)
        k = ht / im.height
        im = im.resize((max(1, int(im.width * k)), int(ht)), Image.LANCZOS)
        x = int(cx - im.width / 2)
        y = int(floor - im.height + (dy[0] if dy else 0))
        sd.ellipse([x + im.width * 0.06, y + im.height - 10, x + im.width * 0.94, y + im.height + 18], fill=110)
        placed.append((im, x, y))
    shadow = shadow.filter(ImageFilter.GaussianBlur(14))
    bg = Image.composite(Image.new("RGB", (w, h), (120, 100, 78)), bg, shadow)
    for im, x, y in placed:
        soft = Image.new("L", (w, h), 0)
        soft.paste(im.getchannel("A").point(lambda v: v * 0.22), (x + 18, y + 14))
        bg = Image.composite(Image.new("RGB", (w, h), (120, 100, 78)), bg, soft.filter(ImageFilter.GaussianBlur(22)))
        bg.paste(im, (x, y), im)
    return bg


def save(im, name, q=84):
    im.save(os.path.join(OUT, name), quality=q, optimize=True, progressive=True)
    print(name, im.size)


# Hero: products grouped on the left third so the text panel can sit on the right (RTL start).
bg, sy = backdrop(1920, 1000, 0.74)
hero = place(bg, [
    ("RS-TW-02", 500, 400, 10),
    ("RS-DS-06", 450, 700),  # stands in front of the towels' frayed end
    ("RS-DS-19", 220, 160, 6),
    ("RS-DS-18", 200, 560, 30),
    ("RS-DS-03", 145, 850, 34),
], sy + 40)
save(hero, "scene-hero.jpg")

# Towels: the rolled Egyptian-cotton towels with hand cream and two bath soaps.
bg, sy = backdrop(1200, 1200, 0.72)
save(place(bg, [("RS-TW-02", 600, 520, 20), ("RS-DS-11", 400, 860, 6), ("RS-DS-04", 150, 260, 46), ("RS-DS-05", 160, 700, 56)], sy + 40), "scene-towels.jpg")

# Dead Sea: a family of the manufacturer's packs.
bg, sy = backdrop(1200, 1200, 0.72)
save(place(bg, [
    ("RS-DS-02", 520, 330),
    ("RS-DS-24", 470, 600, 10),
    ("RS-DS-01", 400, 880, 18),
    ("RS-DS-10", 190, 470, 48),
    ("RS-DS-21", 250, 760, 52),
], sy + 40), "scene-sea.jpg")

# Wide Dead Sea banner: products on the right so the text panel can sit on the left.
bg, sy = backdrop(1920, 900, 0.74)
save(place(bg, [
    ("RS-DS-02", 470, 1180),
    ("RS-DS-24", 430, 1420, 10),
    ("RS-DS-01", 360, 1660, 18),
    ("RS-DS-10", 170, 1320, 46),
    ("RS-DS-21", 230, 1560, 50),
], sy + 40), "scene-sea-wide.jpg")

# Square crop of the hero group for the round editorial image.
save(hero.crop((40, 180, 1040, 1000)).resize((900, 738)).crop((0, 0, 900, 738)), "scene-still.jpg")
