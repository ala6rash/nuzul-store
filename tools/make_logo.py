"""Build NUZUL logo SVGs with text converted to outlines (no font needed to render)."""
import os, sys
import uharfbuzz as hb
from fontTools.ttLib import TTFont
from fontTools.pens.svgPathPen import SVGPathPen
from fontTools.pens.transformPen import TransformPen
from fontTools.varLib.instancer import instantiateVariableFont

FONTS = sys.argv[1]
OUT = sys.argv[2]
os.makedirs(OUT, exist_ok=True)

EUC = "#1E4640"; BRASS = "#B3894A"; LINEN = "#F5F6F3"; INK = "#142321"


def load(path, axes=None):
    tt = TTFont(path)
    if axes and "fvar" in tt:
        tt = instantiateVariableFont(tt, axes)
    blob = hb.Blob.from_file_path(path)
    face = hb.Face(blob)
    font = hb.Font(face)
    if axes:
        font.set_variations(axes)
    return tt, font


def text_path(tt, hbfont, text, size, x, y, tracking=0, direction=None):
    """Return (svg path d, width) for text with baseline at y, starting at x."""
    upem = tt["head"].unitsPerEm
    buf = hb.Buffer()
    buf.add_str(text)
    buf.guess_segment_properties()
    if direction:
        buf.direction = direction
    hb.shape(hbfont, buf, {})
    gs = tt.getGlyphSet()
    order = tt.getGlyphOrder()
    scale = size / upem
    pen = SVGPathPen(gs)
    cx = 0
    for info, pos in zip(buf.glyph_infos, buf.glyph_positions):
        name = order[info.codepoint]
        tp = TransformPen(pen, (scale, 0, 0, -scale, x + (cx + pos.x_offset) * scale, y - pos.y_offset * scale))
        gs[name].draw(tp)
        cx += pos.x_advance + tracking * upem / size
    return pen.getCommands(), cx * scale


ar_tt, ar_hb = load(f"{FONTS}/reemkufi.ttf", {"wght": 700})
la_tt, la_hb = load(f"{FONTS}/marcellus.ttf")

# Key-fob mark: a hotel room key tag on a brass ring, the letter noon (ن) engraved on it.
NOON = None
def mark(x, y, h, fill=EUC, inner=LINEN, ring=BRASS):
    global NOON
    if NOON is None:
        NOON = text_path(ar_tt, ar_hb, "ن", 132, 0, 0)
    d, w = NOON
    s = h / 220.0
    tag = ("M60 36 C88 36 102 54 102 78 L94 176 Q92 206 60 206 Q28 206 26 176 L18 78 C18 54 32 36 60 36 Z "
           "M60 53 A9 9 0 1 0 60.01 53 Z")
    return (f'<g transform="translate({x} {y}) scale({s})">'
            f'<path d="{tag}" fill="{fill}" fill-rule="evenodd"/>'
            f'<circle cx="60" cy="38" r="24" fill="none" stroke="{ring}" stroke-width="7"/>'
            f'<path d="{d}" transform="translate({60 - w/2} 172)" fill="{inner}"/>'
            f'</g>'), 120 * s


def svg(w, h, body, bg=None):
    rect = f'<rect width="{w}" height="{h}" fill="{bg}"/>' if bg else ""
    return (f'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {w:.0f} {h:.0f}" width="{w:.0f}" height="{h:.0f}">'
            f'{rect}{body}</svg>')


def lockup(fg, mark_fill, mark_inner, accent, bg=None, name="logo-horizontal"):
    ar_d, ar_w = text_path(ar_tt, ar_hb, "نزل", 82, 0, 0)
    la_d, la_w = text_path(la_tt, la_hb, "NUZUL", 24, 0, 0, tracking=7)
    textw = max(ar_w, la_w)
    m, mw = mark(textw + 18, 0, 128, mark_fill, mark_inner)
    W = textw + 18 + mw + 2
    body = m
    body += f'<path d="{ar_d}" transform="translate({textw - ar_w} 82)" fill="{fg}"/>'
    body += f'<path d="{la_d}" transform="translate({textw - la_w + 7} 118)" fill="{accent}"/>'
    open(f"{OUT}/{name}.svg", "w").write(svg(W, 130, body, bg))


def stacked(fg, mark_fill, mark_inner, accent, bg=None, name="logo-stacked"):
    W = 300
    m, mw = mark((W - 150*120/220) / 2, 0, 150, mark_fill, mark_inner)
    ar_d, ar_w = text_path(ar_tt, ar_hb, "نزل", 84, 0, 0)
    la_d, la_w = text_path(la_tt, la_hb, "NUZUL", 26, 0, 0, tracking=8)
    body = m
    body += f'<path d="{ar_d}" transform="translate({(W - ar_w) / 2} 252)" fill="{fg}"/>'
    body += f'<path d="{la_d}" transform="translate({(W - la_w) / 2 + 4} 300)" fill="{accent}"/>'
    open(f"{OUT}/{name}.svg", "w").write(svg(W, 312, body, bg))


lockup(INK, EUC, LINEN, BRASS)
lockup(LINEN, LINEN, EUC, BRASS, bg=None, name="logo-horizontal-light")
stacked(INK, EUC, LINEN, BRASS)
stacked(LINEN, LINEN, EUC, BRASS, name="logo-stacked-light")
m, _ = mark(0, 0, 220)
open(f"{OUT}/mark.svg", "w").write(svg(120, 220, m))
# square icon for favicon / social avatar
m, mw = mark(169, 76, 330, LINEN, EUC)
open(f"{OUT}/icon-square.svg", "w").write(svg(512, 512, m, bg=EUC))
print("ok")
