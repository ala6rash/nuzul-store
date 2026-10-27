import os, sys
from PIL import Image, ImageOps, ImageEnhance
D, OUT = sys.argv[1], sys.argv[2]; os.makedirs(OUT, exist_ok=True)
m = {"RS-TW-01": "photo_5981198845876048504_y.jpg", "RS-TW-02": "photo_5981198845876048505_y.jpg",
     "RS-TW-03": "5f0337cc-2e27-4952-8f5c-b976427f26ea.jpeg", "RS-TW-04": "photo_5981198845876048502_y.jpg",
     "RS-RB-01": "photo_5981198845876048526_y.jpg", "RS-BD-01": "bd14dfb3-baa9-4bf7-8b75-9a8aacefc77f.jpeg",
     "RS-BD-03": "d0adbc5a-d026-4fd7-814c-d722f0cfb814.jpeg", "RS-BD-06": "b304ccc9-0ca7-40d9-b6d3-07beb4e775e8.jpeg",
     "RS-BD-07": "photo_5981198845876048500_y.jpg", "RS-MT-01": "683e93ce-1715-4492-a7c7-b50ccd7b2bdd.jpeg",
     "RS-AM-01": "7b3f175f-e7fd-4408-99ba-8231657dddde.jpeg"}
for sku, f in m.items():
    im = ImageOps.exif_transpose(Image.open(os.path.join(D, f))).convert("RGB")
    im = ImageEnhance.Brightness(ImageOps.autocontrast(im, cutoff=1)).enhance(1.04)
    im.thumbnail((900, 1200), Image.LANCZOS)
    im.save(os.path.join(OUT, sku + ".jpg"), quality=84, optimize=True, progressive=True)
print(len(m))
