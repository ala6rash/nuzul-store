import json, os, sys
from PIL import Image, ImageDraw
im = Image.open("arts.png").convert("RGB"); keys = json.load(open("arts.html.json"))
out = sys.argv[1]; os.makedirs(out, exist_ok=True)
prev = Image.new("RGB", (6*200, 200*((len(keys)+5)//6)), "white")
for i, k in enumerate(keys):
    t = im.crop(((i % 6)*900, (i//6)*900, (i % 6)*900+900, (i//6)*900+900))
    t.save(os.path.join(out, k + ".jpg"), quality=88, optimize=True, progressive=True)
    s = t.resize((200, 200)); ImageDraw.Draw(s).text((4, 4), k, fill=(200, 0, 0)); prev.paste(s, ((i % 6)*200, (i//6)*200))
prev.save("arts-preview.jpg", quality=85)
