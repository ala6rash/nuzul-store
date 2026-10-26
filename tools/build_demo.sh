#!/usr/bin/env bash
# Mirror the local WordPress store and turn it into the static GitHub Pages preview in $2.
set -e
SITE=${1:-http://localhost:8080/}; OUT=$2; TMP=$(mktemp -d); HERE=$(cd "$(dirname "$0")" && pwd)
cd "$TMP"
wget -q -r -l 4 -p -E -k -nH --restrict-file-names=nocontrol \
 --reject-regex '(/cart/|checkout|my-account|wp-admin|wp-login|wp-json|feed|xmlrpc|add-to-cart|comments|author|/page/[0-9]+/$|oembed|orderby|\?p=)' "$SITE" || true
curl -s "${SITE%/}/wp-json/wc/store/v1/products?per_page=100" > "$TMP.products.json"
python3 "$HERE/make_demo.py" "$TMP" "$TMP.products.json"
find "$TMP" \( -name "*.js" -o -name "*.js?*" -o -name "*.json*" \) -not -path "*/assets/nz/*" -delete
python3 - "$TMP" <<'PY'
import os,re,sys
root=sys.argv[1]
for r,_,F in os.walk(root):
    for f in F:
        if '?' in f: os.rename(os.path.join(r,f),os.path.join(r,re.sub(r'\?ver=[^/]*?\.(css|js)$','',f)))
for r,_,F in os.walk(root):
    for f in F:
        if f.endswith(('.html','.css')):
            p=os.path.join(r,f); s=open(p,encoding='utf-8',errors='ignore').read(); o=s
            s=re.sub(r'(\.(?:css|js))(?:%3F|\?)ver=[^"\'\s)]*?\1',r'\1',s)
            if s!=o: open(p,'w',encoding='utf-8').write(s)
PY
find "$TMP" -type d -empty -delete
rm -rf "$OUT.new"; mv "$TMP" "$OUT.new"
