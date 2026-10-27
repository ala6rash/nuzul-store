import sys, os
from fontTools.ttLib import TTFont
src, dst = sys.argv[1], sys.argv[2]
m = {"NotoNaskhArabic[wght].ttf":"noto-naskh-arabic.woff2","IBMPlexSansArabic-Light.ttf":"plex-arabic-300.woff2","IBMPlexSansArabic-Regular.ttf":"plex-arabic-400.woff2","IBMPlexSansArabic-Medium.ttf":"plex-arabic-500.woff2","IBMPlexSansArabic-SemiBold.ttf":"plex-arabic-600.woff2","BodoniModa[opsz,wght].ttf":"bodoni-moda.woff2","BodoniModa-Italic[opsz,wght].ttf":"bodoni-moda-italic.woff2"}
for a,b in m.items():
    f = TTFont(os.path.join(src,a)); f.flavor="woff2"; f.save(os.path.join(dst,b)); print(b, os.path.getsize(os.path.join(dst,b))//1024, "KB")
