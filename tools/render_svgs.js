// npm i playwright && npx playwright install chromium
// node tools/render_svgs.js <svgdir> <outdir> <fontsdir> [scale]
// fontsdir must contain marcellus.woff2 (theme/nuzul/assets/fonts works).
const { chromium } = require('playwright');
const fs = require('fs'), path = require('path');
(async () => {
  const [, , src, out, fonts] = process.argv;
  fs.mkdirSync(out, { recursive: true });
  const b = await chromium.launch(process.env.CHROMIUM_PATH ? { executablePath: process.env.CHROMIUM_PATH } : {});
  const p = await b.newPage({ viewport: { width: 800, height: 800 }, deviceScaleFactor: +(process.argv[5] || 1) });
  for (const f of fs.readdirSync(src).filter(f => f.endsWith('.svg'))) {
    const svg = fs.readFileSync(path.join(src, f), 'utf8');
    const m = svg.match(/viewBox="0 0 (\d+) (\d+)"/);
    await p.setViewportSize({ width: +m[1], height: +m[2] });
    await p.setContent(`<style>@font-face{font-family:Marcellus;src:url(file://${path.resolve(fonts)}/marcellus.woff2)}body{margin:0}</style>${svg}`);
    await p.evaluate(() => document.fonts.ready);
    await p.waitForTimeout(50);
    await p.screenshot({ path: path.join(out, f.replace('.svg', '.png')), omitBackground: true });
  }
  await b.close();
})();
