"""Brand product illustrations (SVG) for Room & Salt, laid out on one page for screenshotting."""
import json, sys

CAT, OUT_HTML = sys.argv[1], sys.argv[2]
data = json.load(open(CAT, encoding="utf-8"))

DEEP, MINERAL, MOAB, SALT, SAND, INK = "#12343A", "#2D7A78", "#B4613A", "#FBFAF7", "#E3D6C2", "#0F1B1E"
TINT = {
    "salt": ("#FBFAF7", MINERAL), "deep": (DEEP, SALT), "mineral": (MINERAL, SALT), "sand": ("#E6D8C3", DEEP),
    "moab": (MOAB, SALT), "mud": ("#3B302A", "#E6D8C3"), "berry": ("#6E2F4A", SALT), "honey": ("#D9A441", DEEP),
    "aloe": ("#6F9257", SALT),
}
TOWEL, TOWEL_SH = "#FFFFFF", "#E7E1D6"


def label(x, y, w, h, fg, bg):
    """Small product label: arch mark + two text lines."""
    cx = x + w / 2
    s = h / 80
    return (f'<rect x="{x}" y="{y}" width="{w}" height="{h}" rx="{h*0.06}" fill="{bg}"/>'
            f'<g transform="translate({cx - 9*s} {y + h*0.16}) scale({s*0.45})"><path d="M4 80V32A28 28 0 0 1 60 32V80Z" fill="{fg}"/>'
            f'<path d="M12 80V33A20 20 0 0 1 52 33V80Z" fill="{bg}"/><rect x="12" y="55" width="40" height="25" fill="{fg}" opacity=".55"/></g>'
            f'<rect x="{cx - w*0.3}" y="{y + h*0.66}" width="{w*0.6}" height="{h*0.06}" rx="2" fill="{fg}" opacity=".85"/>'
            f'<rect x="{cx - w*0.2}" y="{y + h*0.78}" width="{w*0.4}" height="{h*0.045}" rx="2" fill="{fg}" opacity=".45"/>')


def jar(t, w=150, h=120, lid=46):
    body, fg = TINT[t]
    lidc = DEEP if t in ("salt", "sand", "honey") else "#F1ECE3"
    x, y = 200 - w / 2, 330 - h
    return (f'<rect x="{x}" y="{y}" width="{w}" height="{h}" rx="{min(22, h/4)}" fill="{body}" stroke="#00000014"/>'
            f'<rect x="{x+6}" y="{y-lid}" width="{w-12}" height="{lid+8}" rx="10" fill="{lidc}"/>'
            f'<rect x="{x+6}" y="{y-lid}" width="{w-12}" height="8" rx="4" fill="#ffffff" opacity=".25"/>'
            + (label(x + w*0.18, y + h*0.2, w*0.64, h*0.62, fg, body if t != "salt" else "#F3EFE7") if h > 70 else
               f'<rect x="{x+w*0.2}" y="{y+h*0.35}" width="{w*0.6}" height="{h*0.12}" rx="3" fill="{fg}" opacity=".8"/>')
            + f'<rect x="{x+10}" y="{y+8}" width="10" height="{h-20}" rx="5" fill="#fff" opacity=".18"/>')


def tube(t):
    body, fg = TINT[t]
    capc = DEEP if t in ("salt", "sand") else "#F1ECE3"
    return (f'<path d="M150 112 L250 112 L238 300 Q200 308 162 300 Z" fill="{body}" stroke="#00000014"/>'
            f'<rect x="146" y="98" width="108" height="18" rx="3" fill="{body}" stroke="#00000014"/>'
            + "".join(f'<line x1="{150+i*10}" y1="100" x2="{150+i*10}" y2="114" stroke="#00000018"/>' for i in range(1, 10))
            + f'<rect x="168" y="298" width="64" height="34" rx="6" fill="{capc}"/>'
            + label(165, 150, 70, 110, fg, body if t != "salt" else "#F3EFE7")
            + '<rect x="158" y="120" width="8" height="170" rx="4" fill="#fff" opacity=".2"/>')


def bottle(t, head="flip"):
    body, fg = TINT[t]
    capc = DEEP if t in ("salt", "sand") else "#F1ECE3"
    s = (f'<rect x="150" y="130" width="100" height="200" rx="26" fill="{body}" stroke="#00000014"/>'
         f'<rect x="178" y="104" width="44" height="32" rx="6" fill="{body}" stroke="#00000014"/>')
    if head == "flip":
        s += f'<rect x="172" y="76" width="56" height="34" rx="8" fill="{capc}"/>'
    elif head == "pump":
        s += (f'<rect x="184" y="80" width="32" height="28" rx="4" fill="{capc}"/><rect x="190" y="58" width="20" height="24" fill="{capc}"/>'
              f'<rect x="190" y="50" width="62" height="14" rx="6" fill="{capc}"/>')
    elif head == "spray":
        s += (f'<rect x="182" y="78" width="36" height="30" rx="5" fill="{capc}"/><rect x="186" y="56" width="28" height="24" rx="4" fill="{capc}"/>'
              f'<rect x="212" y="62" width="14" height="6" rx="2" fill="{capc}"/>')
    s += label(165, 175, 70, 115, fg, body if t != "salt" else "#F3EFE7")
    s += '<rect x="160" y="146" width="9" height="160" rx="4" fill="#fff" opacity=".2"/>'
    return s


def dropper(t):
    body, fg = TINT[t]
    return (f'<rect x="160" y="190" width="80" height="140" rx="16" fill="{body}" stroke="#00000014"/>'
            f'<rect x="178" y="160" width="44" height="34" rx="4" fill="{DEEP if t != "deep" else "#F1ECE3"}"/>'
            f'<path d="M186 160 V110 Q186 84 200 84 Q214 84 214 110 V160Z" fill="#1B1B1B" opacity=".85"/>'
            + label(170, 220, 60, 90, fg, body if t != "salt" else "#F3EFE7")
            + '<rect x="168" y="200" width="7" height="110" rx="3" fill="#fff" opacity=".22"/>')


def soap(t):
    body, fg = TINT[t]
    sp = ""
    if t == "sand":
        import random
        rnd = random.Random(4)
        sp = "".join(f'<circle cx="{rnd.uniform(120, 280):.1f}" cy="{rnd.uniform(232, 300):.1f}" r="{rnd.uniform(1.2, 2.6):.1f}" fill="#8B6B48" opacity=".55"/>' for _ in range(60))
    return (f'<path d="M100 250 Q100 220 135 214 L265 214 Q300 220 300 250 L300 292 Q300 318 268 320 L132 320 Q100 318 100 292Z" fill="{body}" stroke="#00000018"/>'
            f'<path d="M100 250 Q100 220 135 214 L265 214 Q300 220 300 250 Q300 268 265 270 L135 270 Q100 268 100 250Z" fill="#ffffff" opacity=".14"/>'
            + sp +
            f'<g transform="translate(184 222) scale(.5)" opacity=".35"><path d="M4 80V32A28 28 0 0 1 60 32V80Z" fill="none" stroke="{fg}" stroke-width="5"/></g>')


def pouch(t):
    body, fg = TINT[t]
    window = ""
    if t == "mud":
        window = '<rect x="160" y="190" width="80" height="80" rx="40" fill="#2A221D"/><path d="M168 236 Q200 222 232 236" stroke="#4A3D34" stroke-width="5" fill="none"/>'
    else:
        import random
        rnd = random.Random(7)
        window = '<rect x="160" y="190" width="80" height="80" rx="40" fill="#EEF3F2"/>' + "".join(
            f'<rect x="{rnd.uniform(170, 222):.1f}" y="{rnd.uniform(205, 255):.1f}" width="9" height="9" fill="#C9DEDB" transform="rotate({rnd.randint(0, 80)} 200 230)"/>' for _ in range(16))
    bag = "#FBFAF7" if t == "salt" else "#E6D8C3"
    return (f'<path d="M132 104 L268 104 L276 312 Q200 326 124 312 Z" fill="{bag}" stroke="#00000016"/>'
            f'<rect x="132" y="104" width="136" height="12" fill="#00000010"/>'
            f'<path d="M132 120 L268 120" stroke="#00000020" stroke-dasharray="3 3"/>'
            + window +
            f'<rect x="168" y="286" width="64" height="7" rx="3" fill="{DEEP}" opacity=".8"/>'
            f'<g transform="translate(188 140) scale(.38)"><path d="M4 80V32A28 28 0 0 1 60 32V80Z" fill="{DEEP}"/><path d="M12 80V33A20 20 0 0 1 52 33V80Z" fill="{bag}"/><rect x="12" y="55" width="40" height="25" fill="{MINERAL}"/></g>')


def towel_stack(stripe=None, n=3):
    s = ""
    for i in range(n):
        y = 300 - i * 52
        w = 250 - i * 6
        x = 200 - w / 2
        s += (f'<path d="M{x} {y} h{w} q14 0 14 -22 q0 -24 -14 -24 h-{w} q-14 0 -14 24 q0 22 14 22z" fill="{TOWEL}" stroke="{TOWEL_SH}" stroke-width="2"/>'
              f'<path d="M{x+4} {y-10} h{w-8}" stroke="{TOWEL_SH}" stroke-width="3"/>')
        if stripe:
            s += f'<rect x="{x + w*0.18}" y="{y-46}" width="{w*0.12}" height="46" fill="{stripe}"/><rect x="{x + w*0.62}" y="{y-46}" width="{w*0.12}" height="46" fill="{stripe}"/>'
        s += "".join(f'<circle cx="{x + 12 + k*11}" cy="{y-30 + (k % 3)*7}" r="1.4" fill="{TOWEL_SH}"/>' for k in range(int(w/11) - 1))
    return s


def towel_roll():
    def roll(cx, cy, r):
        return (f'<rect x="{cx-150}" y="{cy-r}" width="150" height="{2*r}" fill="{TOWEL}" stroke="{TOWEL_SH}" stroke-width="2"/>'
                f'<circle cx="{cx}" cy="{cy}" r="{r}" fill="{TOWEL}" stroke="{TOWEL_SH}" stroke-width="2"/>'
                f'<path d="M{cx} {cy} m-6 0 a6 6 0 1 1 12 0 a14 14 0 1 1 -28 0 a22 22 0 1 1 44 0 a30 30 0 1 1 -60 0" fill="none" stroke="{TOWEL_SH}" stroke-width="3"/>')
    return roll(250, 284, 44) + roll(330, 284, 44) + roll(290, 206, 44)


def robe():
    return (f'<path d="M140 92 L200 140 L260 92 L318 120 L340 252 L300 262 L292 186 L290 330 L110 330 L108 186 L100 262 L60 252 L82 120Z" fill="{TOWEL}" stroke="{TOWEL_SH}" stroke-width="3" stroke-linejoin="round"/>'
            f'<path d="M140 92 L200 140 L260 92 L240 92 L200 128 L160 92Z" fill="{TOWEL_SH}"/>'
            f'<path d="M200 140 L200 330" stroke="{TOWEL_SH}" stroke-width="3"/>'
            f'<rect x="108" y="218" width="184" height="16" rx="5" fill="{TOWEL}" stroke="{TOWEL_SH}" stroke-width="2"/>'
            f'<path d="M190 234 L176 286 M206 234 L222 280" stroke="{TOWEL_SH}" stroke-width="10" stroke-linecap="round"/>'
            f'<rect x="124" y="250" width="44" height="40" rx="6" fill="none" stroke="{TOWEL_SH}" stroke-width="2"/>')


def slippers():
    def one(cx, rot):
        return (f'<g transform="rotate({rot} {cx} 220)"><ellipse cx="{cx}" cy="220" rx="52" ry="118" fill="{TOWEL}" stroke="{TOWEL_SH}" stroke-width="3"/>'
                f'<path d="M{cx-50} 170 Q{cx} 128 {cx+50} 170 L{cx+48} 214 Q{cx} 196 {cx-48} 214Z" fill="#F4F0E9" stroke="{TOWEL_SH}" stroke-width="2"/></g>')
    return one(150, -8) + one(250, 8)


def pillow(soft=False):
    d = "M78 150 Q72 112 112 112 Q200 98 288 112 Q328 112 322 150 Q330 220 322 290 Q328 320 288 318 Q200 330 112 318 Q72 320 78 290 Q70 220 78 150Z"
    s = f'<path d="{d}" fill="{TOWEL}" stroke="{TOWEL_SH}" stroke-width="3"/>'
    s += f'<path d="M96 134 Q200 120 304 134 Q312 215 304 298 Q200 312 96 298 Q88 215 96 134Z" fill="none" stroke="{"#E8DFD0" if soft else "#C9B89A"}" stroke-width="2" stroke-dasharray="{"0" if not soft else "6 5"}"/>'
    s += '<path d="M120 170 Q200 196 280 170" stroke="#EFE9DF" stroke-width="6" fill="none"/>'
    return s


def pillowcase(striped=False):
    s = f'<rect x="80" y="130" width="240" height="170" rx="6" fill="{TOWEL}" stroke="{TOWEL_SH}" stroke-width="3"/>'
    if striped:
        s += "".join(f'<line x1="{92+i*9}" y1="134" x2="{92+i*9}" y2="296" stroke="#EAE5DC" stroke-width="3"/>' for i in range(26))
    s += f'<rect x="80" y="130" width="54" height="170" fill="none" stroke="{TOWEL_SH}" stroke-width="2"/>'
    return s


def protector():
    s = f'<rect x="70" y="120" width="260" height="190" rx="10" fill="{TOWEL}" stroke="{TOWEL_SH}" stroke-width="3"/>'
    for i in range(-6, 12):
        s += f'<line x1="{70+i*26}" y1="120" x2="{70+i*26+190}" y2="310" stroke="#ECE6DB" stroke-width="2"/><line x1="{330-i*26}" y1="120" x2="{330-i*26-190}" y2="310" stroke="#ECE6DB" stroke-width="2"/>'
    s = f'<clipPath id="pc"><rect x="70" y="120" width="260" height="190" rx="10"/></clipPath><g clip-path="url(#pc)">{s}</g>'
    s += f'<rect x="70" y="120" width="260" height="190" rx="10" fill="none" stroke="{TOWEL_SH}" stroke-width="3"/>'
    s += f'<path d="M70 150 Q70 120 100 120 M330 150 Q330 120 300 120" stroke="{MINERAL}" stroke-width="6" fill="none"/>'
    s += f'<g transform="translate(296 268)"><circle r="20" fill="{MINERAL}"/><path d="M0 -10 Q8 2 0 10 Q-8 2 0 -10Z" fill="#fff"/></g>'
    return s


def sheet_set():
    s = ""
    for i in range(3):
        y = 300 - i * 44
        s += f'<rect x="{90+i*4}" y="{y-40}" width="{220-i*8}" height="40" rx="6" fill="{TOWEL}" stroke="{TOWEL_SH}" stroke-width="2"/><path d="M{96+i*4} {y-12} h{208-i*8}" stroke="{TOWEL_SH}" stroke-width="2"/>'
    s += f'<rect x="186" y="164" width="28" height="140" fill="{MOAB}" opacity=".9"/>'
    s += label(150, 196, 100, 80, DEEP, SALT)
    return s


def mattress(wide=False, band=MINERAL):
    w = 300 if wide else 220
    x = 200 - w / 2 - 20
    top = f'M{x} 200 L{x+w} 200 L{x+w+50} 150 L{x+50} 150Z'
    s = f'<path d="{top}" fill="{TOWEL}" stroke="{TOWEL_SH}" stroke-width="3" stroke-linejoin="round"/>'
    for i in range(1, 8):
        s += f'<line x1="{x + i*w/8}" y1="200" x2="{x + i*w/8 + 50}" y2="150" stroke="#ECE6DB" stroke-width="2"/>'
    s += f'<path d="M{x} 200 L{x+w} 200 L{x+w} 290 L{x} 290Z" fill="#F6F2EB" stroke="{TOWEL_SH}" stroke-width="3"/>'
    s += f'<path d="M{x+w} 200 L{x+w+50} 150 L{x+w+50} 240 L{x+w} 290Z" fill="#EDE7DD" stroke="{TOWEL_SH}" stroke-width="3"/>'
    s += f'<rect x="{x}" y="226" width="{w}" height="22" fill="{band}" opacity=".9"/><path d="M{x+w} 248 L{x+w+50} 198 L{x+w+50} 176 L{x+w} 226Z" fill="{band}" opacity=".75"/>'
    return s


def amenities():
    s = '<rect x="70" y="282" width="260" height="30" rx="10" fill="#FBFAF7" stroke="#00000014"/>'
    cols = [("#E8C547", 92), ("#F2E6A6", 142), ("#B9D07A", 192), ("#E8C547", 242)]
    for c, x in cols:
        s += f'<rect x="{x}" y="186" width="40" height="98" rx="10" fill="{c}" stroke="#00000014"/><rect x="{x+10}" y="168" width="20" height="22" rx="4" fill="#F6F3EC" stroke="#00000014"/><rect x="{x+6}" y="214" width="28" height="30" rx="3" fill="#ffffffaa"/>'
    s += '<rect x="290" y="250" width="40" height="34" rx="12" fill="#FFFFFF" stroke="#00000016"/>'
    return s


def art(kind, tint="salt"):
    return {
        "jar": lambda: jar(tint), "jar-wide": lambda: jar(tint, 190, 104, 40), "jar-low": lambda: jar(tint, 190, 62, 34),
        "tube": lambda: tube(tint), "bottle": lambda: bottle(tint), "pump": lambda: bottle(tint, "pump"),
        "spray": lambda: bottle(tint, "spray"), "dropper": lambda: dropper(tint), "soap": lambda: soap(tint),
        "pouch": lambda: pouch(tint), "towel-stack": towel_stack, "towel-roll": towel_roll,
        "towel-striped": lambda: towel_stack("#1F2D5A"), "robe": robe, "slippers": slippers,
        "pillow": pillow, "pillow-soft": lambda: pillow(True), "pillowcase": pillowcase,
        "pillowcase-striped": lambda: pillowcase(True), "protector": protector, "sheet-set": sheet_set,
        "mattress": mattress, "mattress-wide": lambda: mattress(True), "mattress-elite": lambda: mattress(False, "#E2B93B"),
        "amenities": amenities,
        "pouch-towel": lambda: ('<path d="M120 90 L280 90 L286 320 L114 320Z" fill="#F4ECDC" stroke="#00000016"/><circle cx="200" cy="112" r="9" fill="#E3D6C2"/>'
                                + label(150, 160, 100, 110, DEEP, "#F4ECDC")),
    }[kind]()


def tile(kind, tint, bg):
    return (f'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400" width="900" height="900">'
            f'<defs><radialGradient id="g" cx="50%" cy="38%" r="70%"><stop offset="0" stop-color="#ffffff" stop-opacity=".75"/><stop offset="1" stop-color="#ffffff" stop-opacity="0"/></radialGradient></defs>'
            f'<rect width="400" height="400" fill="{bg}"/><rect width="400" height="400" fill="url(#g)"/>'
            f'<ellipse cx="200" cy="336" rx="150" ry="14" fill="#000" opacity=".07"/>'
            f'<g transform="translate(0 6)">{art(kind, tint)}</g></svg>')


items = []
for p in data["products"]:
    face = p["cat"].startswith("dead-sea")
    bg = "#E9EFEC" if face else "#EFE9DF"
    items.append((p["sku"], tile(p["art"], p.get("tint", "salt"), bg)))
cats = [(c, False) for c in data["categories"]] + [(ch, True) for c in data["categories"] for ch in c.get("children", [])]
for c, _ in cats:
    tint = {"dead-sea": "deep", "dead-sea-face": "salt", "dead-sea-body": "mud"}.get(c["slug"], "salt")
    bg = "#E9EFEC" if c["slug"].startswith("dead-sea") else "#EFE9DF"
    items.append(("cat-" + c["slug"], tile(c["art"], tint, bg)))

html = ['<html><head><style>body{margin:0;display:grid;grid-template-columns:repeat(6,900px)}svg{display:block}</style></head><body>']
html += [s for _, s in items]
html.append("</body></html>")
open(OUT_HTML, "w", encoding="utf-8").write("".join(html))
json.dump([k for k, _ in items], open(OUT_HTML + ".json", "w"))
print(len(items), "tiles; rows:", (len(items) + 5) // 6)
