"""Room & Salt brand assets: SVG logos with text converted to outlines, plus PNG icons."""
import os, sys
import uharfbuzz as hb
from fontTools.ttLib import TTFont
from fontTools.pens.svgPathPen import SVGPathPen
from fontTools.pens.transformPen import TransformPen
from PIL import Image, ImageDraw

SRC, OUT = sys.argv[1], sys.argv[2]
os.makedirs(OUT, exist_ok=True)

C = {
    "salt": "#F7F4EE", "sand": "#ECE5D9", "deep": "#12343A", "mineral": "#2D7A78",
    "moab": "#B4613A", "ink": "#0F1B1E", "line": "#DCD4C6",
}

FONTS = {
    "bodoni": os.path.join(SRC, "BodoniModa[opsz,wght].ttf"),
    "bodoni-it": os.path.join(SRC, "BodoniModa-Italic[opsz,wght].ttf"),
    "naskh": os.path.join(SRC, "NotoNaskhArabic[wght].ttf"),
    "plex": os.path.join(SRC, "IBMPlexSansArabic-Medium.ttf"),
}
_cache = {}


def shape(font_key, text, size, axes=None, tracking=0.0, direction=None):
    """Return (svg path d, width) for text at given size, baseline at y=0."""
    path = FONTS[font_key]
    if path not in _cache:
        blob = hb.Blob.from_file_path(path)
        _cache[path] = (hb.Face(blob), TTFont(path))
    face, tt = _cache[path]
    font = hb.Font(face)
    if axes:
        font.set_variations(axes)
    buf = hb.Buffer()
    buf.add_str(text)
    buf.guess_segment_properties()
    if direction:
        buf.direction = direction
    hb.shape(font, buf, {"kern": True, "liga": True})
    upem = face.upem
    scale = size / upem
    gs = tt.getGlyphSet(location=axes or None)
    order = tt.getGlyphOrder()
    pen = SVGPathPen(gs)
    x = 0.0
    for info, pos in zip(buf.glyph_infos, buf.glyph_positions):
        name = order[info.codepoint]
        tp = TransformPen(pen, (scale, 0, 0, -scale, x + pos.x_offset * scale, -pos.y_offset * scale))
        gs[name].draw(tp)
        x += pos.x_advance * scale + tracking * size
    if tracking:
        x -= tracking * size
    return pen.getCommands(), x


def mark(x, y, h, dark=False):
    """Arch (hotel room door) with Dead Sea water and a floating salt crystal. h = height."""
    s = h / 80.0
    frame = C["salt"] if dark else C["deep"]
    inner = C["deep"] if dark else C["salt"]
    g = f'<g transform="translate({x:.2f} {y:.2f}) scale({s:.4f})">'
    g += f'<path d="M4 80V32A28 28 0 0 1 60 32V80Z" fill="{frame}"/>'
    g += f'<path d="M12 80V33A20 20 0 0 1 52 33V80Z" fill="{inner}"/>'
    g += f'<path d="M32 43.5L38.5 50 32 56.5 25.5 50Z" fill="{C["moab"]}"/>'
    g += f'<rect x="12" y="55" width="40" height="25" fill="{C["mineral"]}"/>'
    g += f'<rect x="12" y="55" width="40" height="1.6" fill="{inner}" opacity=".55"/>'
    return g + "</g>"


def wordmark(size, color, amp_color):
    axes = {"wght": 500, "opsz": 96}
    a, wa = shape("bodoni", "ROOM", size, axes, tracking=0.14)
    b, wb = shape("bodoni-it", "&", size * 1.08, {"wght": 450, "opsz": 96})
    c, wc = shape("bodoni", "SALT", size, axes, tracking=0.14)
    gap = size * 0.34
    parts = [
        f'<path d="{a}" fill="{color}"/>',
        f'<path transform="translate({wa + gap:.2f} 0)" d="{b}" fill="{amp_color}"/>',
        f'<path transform="translate({wa + gap + wb + gap:.2f} 0)" d="{c}" fill="{color}"/>',
    ]
    return "".join(parts), wa + gap + wb + gap + wc


def svg(w, h, body, name):
    with open(os.path.join(OUT, name), "w", encoding="utf-8") as f:
        f.write(f'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {w:.0f} {h:.0f}" width="{w:.0f}" height="{h:.0f}" role="img" aria-label="Room &amp; Salt">{body}</svg>')
    print("wrote", name)


AR_TAG = "مستلزمات الفنادق · منتجات البحر الميت"

for dark in (False, True):
    sfx = "-light" if dark else ""
    ink = C["salt"] if dark else C["deep"]
    # Horizontal: mark + wordmark
    wm, ww = wordmark(40, ink, C["moab"])
    mh = 56
    body = mark(0, 0, mh, dark) + f'<g transform="translate({mh * 0.8 + 18:.2f} {mh * 0.5 + 14:.2f})">{wm}</g>'
    svg(mh * 0.8 + 18 + ww + 2, mh, body, f"logo-horizontal{sfx}.svg")

    # Stacked: mark, wordmark, Arabic descriptor
    wm, ww = wordmark(46, ink, C["moab"])
    ar, aw = shape("plex", AR_TAG, 17, None)
    W = max(ww, aw) + 8
    mh = 96
    body = mark((W - mh * 0.8) / 2, 0, mh, dark)
    body += f'<g transform="translate({(W - ww) / 2:.2f} {mh + 52:.2f})">{wm}</g>'
    body += f'<rect x="{W / 2 - 18:.2f}" y="{mh + 70:.2f}" width="36" height="1.4" fill="{C["moab"]}"/>'
    body += f'<g transform="translate({(W - aw) / 2:.2f} {mh + 102:.2f})"><path d="{ar}" fill="{ink}" opacity=".8"/></g>'
    svg(W, mh + 116, body, f"logo-stacked{sfx}.svg")

# Mark alone + square icon
svg(64, 80, mark(0, 0, 80), "mark.svg")
svg(512, 512, f'<rect width="512" height="512" rx="112" fill="{C["deep"]}"/>' + mark(128, 96, 320, dark=True), "icon-square.svg")


def png_icon(size, name):
    """Raster square icon drawn directly (4x supersampled)."""
    k = 4
    S = size * k
    im = Image.new("RGBA", (S, S), (0, 0, 0, 0))
    d = ImageDraw.Draw(im)
    d.rounded_rectangle([0, 0, S - 1, S - 1], radius=int(S * 0.22), fill=C["deep"])
    sc = S * 0.625 / 80
    ox, oy = S * 0.25, S * 0.1875

    def P(x, y):
        return (ox + x * sc, oy + y * sc)

    def arch(x0, x1, top, r, fill):
        cx = (x0 + x1) / 2
        d.rectangle([P(x0, top)[0], P(x0, top)[1], P(x1, 80)[0], P(x1, 80)[1]], fill=fill)
        d.ellipse([P(cx - r, top - r), P(cx + r, top + r)], fill=fill)

    arch(4, 60, 32, 28, C["salt"])
    arch(12, 52, 33, 20, C["deep"])
    d.polygon([P(32, 43.5), P(38.5, 50), P(32, 56.5), P(25.5, 50)], fill=C["moab"])
    d.rectangle([P(12, 55), P(52, 80)], fill=C["mineral"])
    im.resize((size, size), Image.LANCZOS).save(os.path.join(OUT, name))
    print("wrote", name)


png_icon(512, "icon-512.png")
png_icon(192, "icon-192.png")
png_icon(64, "icon-64.png")
