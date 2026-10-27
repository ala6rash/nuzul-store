import os, sys, zipfile
src, dst, root = sys.argv[1], sys.argv[2], sys.argv[3]
with zipfile.ZipFile(dst, "w", zipfile.ZIP_DEFLATED) as z:
    for d, _, fs in os.walk(src):
        for f in fs:
            p = os.path.join(d, f)
            z.write(p, root + "/" + os.path.relpath(p, src).replace("\\", "/"))
print(dst, os.path.getsize(dst)//1024, "KB")
