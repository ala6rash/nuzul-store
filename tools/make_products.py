"""Flat brand illustrations used as placeholder product photos until real photos are shot.
Writes SVGs; PNGs are rendered with a headless browser afterwards."""
import json, os, sys

OUT = sys.argv[1]
os.makedirs(OUT, exist_ok=True)

EUC = "#1E4640"; EUC2 = "#2E6158"; BRASS = "#B3894A"; LINEN = "#F5F6F3"; INK = "#142321"
PORC = "#E4EAE6"; WHITE = "#FFFFFF"; SAGE = "#CBD8D1"; SAND = "#E9E1D2"; BLUSH = "#E8D6D0"; GREY = "#9AA7A2"

BGS = {"toiletries": "#DCE6E1", "robes-slippers": "#E6E3DC", "towels": "#DDE3E6", "bath-accessories": "#E2E6DE",
       "gift-boxes": "#E7DFD6", "fragrances": "#DFE4E8"}


def shadow(cx, cy, rx):
    return f'<ellipse cx="{cx}" cy="{cy}" rx="{rx}" ry="{rx*0.12:.1f}" fill="{INK}" opacity="0.10"/>'


def label(x, y, w, text="NUZUL", color=BRASS, size=None):
    size = size or max(10, w * 0.16)
    return (f'<text x="{x + w/2}" y="{y}" text-anchor="middle" font-family="Marcellus, serif" '
            f'font-size="{size:.1f}" letter-spacing="{size*0.25:.1f}" fill="{color}">{text}</text>')


def bottle(x, base, w, h, body, cap=EUC, pump=False, lbl="NUZUL", lblc=BRASS):
    neck = w * 0.34
    s = shadow(x + w/2, base, w * 0.7)
    s += f'<rect x="{x}" y="{base-h}" width="{w}" height="{h}" rx="{w*0.22:.1f}" fill="{body}"/>'
    s += f'<rect x="{x + (w-neck)/2}" y="{base-h-w*0.22}" width="{neck}" height="{w*0.26}" rx="3" fill="{cap}"/>'
    if pump:
        s += f'<rect x="{x + w/2 - 3}" y="{base-h-w*0.52}" width="6" height="{w*0.32}" fill="{cap}"/>'
        s += f'<rect x="{x + w/2 - 3}" y="{base-h-w*0.56}" width="{w*0.42}" height="9" rx="4" fill="{cap}"/>'
    s += f'<rect x="{x + w*0.14}" y="{base-h*0.66}" width="{w*0.72}" height="{h*0.34}" rx="4" fill="{LINEN}" opacity="0.92"/>'
    s += label(x + w*0.14, base - h*0.66 + h*0.21, w*0.72, lbl, lblc, size=w*0.11)
    return s


def soap(x, base, w, h, color):
    return (shadow(x + w/2, base, w*0.6) + f'<rect x="{x}" y="{base-h}" width="{w}" height="{h}" rx="{h*0.45:.1f}" fill="{color}"/>'
            f'<rect x="{x+w*0.18}" y="{base-h+h*0.22}" width="{w*0.64}" height="{h*0.56}" rx="{h*0.28:.1f}" fill="none" stroke="{WHITE}" stroke-opacity=".6" stroke-width="2"/>')


def slipper(x, y, scale=1.0, color=WHITE, strap=PORC, flip=False):
    t = f'translate({x} {y}) scale({-scale if flip else scale} {scale})'
    return (f'<g transform="{t}">'
            f'<path d="M0 0 C0 -60 30 -150 70 -150 C110 -150 140 -60 140 0 C140 70 110 140 70 140 C30 140 0 70 0 0Z" fill="{color}" stroke="{GREY}" stroke-opacity=".35" stroke-width="2"/>'
            f'<path d="M6 -40 C20 -80 120 -80 134 -40 L134 10 C110 -10 30 -10 6 10Z" fill="{strap}"/>'
            + (f'<text x="70" y="70" text-anchor="middle" font-family="Marcellus, serif" font-size="13" letter-spacing="3" fill="{BRASS}">NUZUL</text>' if not flip else '')
            + f'</g>')


def robe(cx, top, color, trim, scale=1.0):
    s = scale
    return (f'<g transform="translate({cx} {top}) scale({s})">'
            f'<path d="M-30 0 Q0 -26 30 0" fill="none" stroke="{BRASS}" stroke-width="7" stroke-linecap="round"/>'
            f'<path d="M0 -26 L0 -48 Q14 -52 14 -64" fill="none" stroke="{BRASS}" stroke-width="5" stroke-linecap="round"/>'
            f'<path d="M-60 6 L60 6 L150 70 L120 130 L95 112 L100 360 L-100 360 L-95 112 L-120 130 L-150 70 Z" fill="{color}"/>'
            f'<path d="M-60 6 L0 150 L60 6" fill="none" stroke="{trim}" stroke-width="14" stroke-linejoin="round"/>'
            f'<rect x="-98" y="205" width="196" height="16" fill="{trim}"/>'
            f'<path d="M-30 221 L-46 300 M30 221 L42 296" stroke="{trim}" stroke-width="12" stroke-linecap="round"/>'
            f'<rect x="38" y="250" width="44" height="50" rx="6" fill="none" stroke="{trim}" stroke-width="5"/>'
            f'</g>')


def towel(x, base, w, h, color, band=BRASS):
    return (f'<rect x="{x}" y="{base-h}" width="{w}" height="{h}" rx="{h*0.35:.1f}" fill="{color}" stroke="{GREY}" stroke-opacity=".35" stroke-width="2"/>'
            f'<rect x="{x+w*0.82}" y="{base-h}" width="{w*0.05}" height="{h}" fill="{band}" opacity=".7"/>'
            f'<path d="M{x+h*0.3} {base-h*0.5} H{x+w*0.78}" stroke="{WHITE}" stroke-opacity=".45" stroke-width="2" stroke-dasharray="3 5"/>')


def towel_stack(cx, base, colors):
    w, h = 440, 74
    s = shadow(cx, base, 260)
    for i, c in enumerate(colors):
        s += towel(cx - w/2 + (i % 2) * 8, base - i * (h + 2), w, h, c)
    return s


def rolled(cx, base, r, color):
    return (shadow(cx, base, r*1.4) + f'<rect x="{cx-r*1.6}" y="{base-2*r}" width="{r*3.2}" height="{2*r}" rx="{r}" fill="{color}"/>'
            f'<rect x="{cx-r*0.9}" y="{base-2*r}" width="{r*0.12}" height="{2*r}" fill="{BRASS}" opacity=".7"/>'
            f'<circle cx="{cx+r*0.6}" cy="{base-r}" r="{r*0.95}" fill="{color}" stroke="{GREY}" stroke-opacity=".5" stroke-width="3"/>'
            f'<circle cx="{cx+r*0.6}" cy="{base-r}" r="{r*0.62}" fill="none" stroke="{GREY}" stroke-opacity=".45" stroke-width="3"/>'
            f'<circle cx="{cx+r*0.6}" cy="{base-r}" r="{r*0.3}" fill="none" stroke="{GREY}" stroke-opacity=".4" stroke-width="3"/>')


def pouch(x, base, w, h, color, items):
    s = shadow(x + w/2, base, w*0.6)
    s += f'<path d="M{x} {base-h} H{x+w} V{base-12} Q{x+w} {base} {x+w-12} {base} H{x+12} Q{x} {base} {x} {base-12} Z" fill="{color}" opacity=".9"/>'
    s += f'<rect x="{x}" y="{base-h}" width="{w}" height="14" fill="{EUC}" opacity=".85"/>'
    s += items
    s += label(x, base - 24, w, "NUZUL", EUC, size=16)
    return s


def toothbrush(x, y, l, color):
    return (f'<rect x="{x}" y="{y}" width="{l}" height="22" rx="11" fill="{color}"/>'
            f'<rect x="{x+l-50}" y="{y-18}" width="40" height="18" rx="3" fill="{WHITE}" stroke="{GREY}" stroke-opacity=".4"/>')


def tube(x, y, l, color):
    return (f'<path d="M{x} {y} H{x+l-20} L{x+l} {y+8} V{y+20} L{x+l-20} {y+28} H{x} Z" fill="{color}"/>'
            f'<rect x="{x-14}" y="{y+6}" width="14" height="16" rx="2" fill="{EUC}"/>')


def gift_box(cx, base, w, h, color, ribbon, tag=True):
    lid = h * 0.22
    s = shadow(cx, base, w*0.62)
    s += f'<rect x="{cx-w/2}" y="{base-h}" width="{w}" height="{h}" rx="6" fill="{color}"/>'
    s += f'<rect x="{cx-w/2-8}" y="{base-h-8}" width="{w+16}" height="{lid}" rx="6" fill="{color}" stroke="{INK}" stroke-opacity=".08"/>'
    s += f'<rect x="{cx-14}" y="{base-h-8}" width="28" height="{h+8}" fill="{ribbon}"/>'
    s += f'<path d="M{cx} {base-h-8} C{cx-70} {base-h-80} {cx-90} {base-h-10} {cx} {base-h-8} Z M{cx} {base-h-8} C{cx+70} {base-h-80} {cx+90} {base-h-10} {cx} {base-h-8} Z" fill="{ribbon}"/>'
    if tag:
        s += f'<path d="M{cx+14} {base-h+30} Q{cx+60} {base-h+40} {cx+76} {base-h+70}" stroke="{BRASS}" stroke-width="3" fill="none"/>'
        s += f'<g transform="translate({cx+62} {base-h+64}) rotate(-18)"><rect width="56" height="80" rx="24" fill="{EUC}"/><circle cx="28" cy="14" r="5" fill="{color}"/></g>'
    return s


def spray(x, base, w, h, body, cap=EUC):
    s = shadow(x + w/2, base, w*0.7)
    s += f'<rect x="{x}" y="{base-h}" width="{w}" height="{h}" rx="{w*0.12}" fill="{body}"/>'
    s += f'<rect x="{x+w*0.28}" y="{base-h-36}" width="{w*0.44}" height="38" rx="4" fill="{cap}"/>'
    s += f'<rect x="{x+w*0.72}" y="{base-h-30}" width="18" height="8" fill="{cap}"/>'
    s += f'<rect x="{x+w*0.12}" y="{base-h*0.7}" width="{w*0.76}" height="{h*0.4}" rx="4" fill="{LINEN}" opacity=".92"/>'
    s += label(x + w*0.12, base - h*0.7 + h*0.24, w*0.76, "NUZUL", BRASS, size=w*0.1)
    return s


def shower_cap(cx, cy, r, color):
    return (shadow(cx, cy + r*0.55, r) + f'<ellipse cx="{cx}" cy="{cy}" rx="{r}" ry="{r*0.55}" fill="{color}" stroke="{GREY}" stroke-opacity=".5" stroke-width="3"/>'
            f'<ellipse cx="{cx}" cy="{cy+r*0.3}" rx="{r*0.98}" ry="{r*0.22}" fill="none" stroke="{GREY}" stroke-opacity=".6" stroke-width="4" stroke-dasharray="2 6"/>')


def comb(x, y, w):
    teeth = "".join(f'<rect x="{x+6+i*9}" y="{y+16}" width="4" height="26" fill="{EUC2}"/>' for i in range(int((w-12)/9)))
    return f'<rect x="{x}" y="{y}" width="{w}" height="18" rx="6" fill="{EUC2}"/>' + teeth


def razor(x, y):
    return (f'<rect x="{x}" y="{y}" width="140" height="14" rx="7" fill="{EUC}"/>'
            f'<rect x="{x+136}" y="{y-16}" width="18" height="46" rx="4" fill="{GREY}"/>')


def page(slug, cat, body):
    svg = (f'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 800" width="800" height="800">'
           f'<rect width="800" height="800" fill="{BGS[cat]}"/>'
           f'<circle cx="400" cy="400" r="290" fill="{WHITE}" opacity=".35"/>'
           f'{body}</svg>')
    open(f"{OUT}/{slug}.svg", "w").write(svg)


B = 640  # base line for objects

page("trio-set", "toiletries",
     bottle(200, B, 120, 280, EUC) + bottle(340, B, 120, 320, SAGE, EUC, pump=True, lblc=EUC) + bottle(480, B, 120, 280, SAND))
page("travel-kit", "toiletries",
     pouch(190, B, 420, 300, LINEN,
           bottle(230, B - 40, 60, 140, EUC) + bottle(305, B - 40, 60, 160, SAGE, EUC) + bottle(380, B - 40, 60, 140, SAND)
           + bottle(455, B - 40, 60, 120, BLUSH) + soap(530, B - 40, 60, 36, SAGE)))
page("soap-set", "toiletries",
     "".join(soap(180 + (i % 3) * 150, B - (i // 3) * 70, 130, 60, [SAGE, SAND, BLUSH][i % 3]) for i in range(6)))
page("body-lotion", "toiletries", bottle(320, B, 160, 380, SAND, EUC, pump=True))
page("slippers-pair", "robes-slippers", slipper(230, 430, 1.05) + slipper(570, 470, 1.05, flip=True))
page("slippers-box", "robes-slippers",
     gift_box(400, B, 420, 200, EUC, BRASS, tag=False) + slipper(250, 330, 0.7) + slipper(550, 350, 0.7, flip=True)
     + label(190, B - 70, 420, "10 × PAIRS", LINEN, size=26))
page("robe-waffle", "robes-slippers", robe(400, 220, WHITE, PORC))
page("robe-plush", "robes-slippers", robe(400, 220, SAGE, EUC2))
page("towel-set", "towels", towel_stack(400, B, [WHITE, PORC, WHITE]))
page("bath-towel", "towels", rolled(380, B, 130, WHITE))
page("dental-kit", "bath-accessories",
     "".join(toothbrush(220, 250 + i * 66, 360, [EUC, SAGE, SAND, BLUSH, EUC2][i]) for i in range(5)) + tube(250, 600, 320, WHITE))
page("shaving-kit", "bath-accessories",
     pouch(200, B, 400, 280, LINEN, razor(260, 440) + tube(270, 500, 200, WHITE)))
page("vanity-kit", "bath-accessories",
     pouch(200, B, 400, 280, LINEN, comb(260, 420, 170) + f'<circle cx="500" cy="460" r="34" fill="{WHITE}"/><circle cx="470" cy="500" r="26" fill="{WHITE}"/>'
           + f'<rect x="260" y="500" width="150" height="10" rx="5" fill="{SAND}"/>'))
page("shower-caps", "bath-accessories",
     shower_cap(320, 470, 130, WHITE) + shower_cap(480, 520, 120, PORC))
page("box-night", "gift-boxes", gift_box(400, B, 380, 300, LINEN, EUC))
page("box-guest", "gift-boxes", gift_box(400, B, 340, 260, SAGE, BRASS))
page("box-bride", "gift-boxes", gift_box(400, B, 400, 310, BLUSH, LINEN))
page("pillow-mist", "fragrances", spray(330, B, 140, 300, SAGE))
page("room-spray", "fragrances", spray(260, B, 130, 330, EUC, BRASS) + spray(420, B, 130, 270, SAND))

# category covers reuse the most characteristic product
covers = {"toiletries": "trio-set", "robes-slippers": "robe-waffle", "towels": "towel-set",
          "bath-accessories": "dental-kit", "gift-boxes": "box-night", "fragrances": "pillow-mist"}
json.dump(covers, open(f"{OUT}/covers.json", "w"))
print(len([f for f in os.listdir(OUT) if f.endswith('.svg')]), "svgs")
