// node render_svgs.js <svgdir> <outdir> <fontsdir>
const { chromium } = require('/tmp/claude-0/-home-claude/29f69107-8c4b-547c-a634-44b3e647d3d6/scratchpad/node_modules/playwright');
const fs = require('fs'), path = require('path');
(async () => {
  const [, , src, out, fonts] = process.argv;
  fs.mkdirSync(out, { recursive: true });
  const b = await chromium.launch({ executablePath: '/opt/pw-browsers/chromium_headless_shell-1194/chrome-linux/headless_shell' });
  const p = await b.newPage({ viewport: { width: 800, height: 800 }, deviceScaleFactor: +(process.argv[5] || 1) });
  for (const f of fs.readdirSync(src).filter(f => f.endsWith('.svg'))) {
    const svg = fs.readFileSync(path.join(src, f), 'utf8');
    const m = svg.match(/viewBox="0 0 (\d+) (\d+)"/);
    await p.setViewportSize({ width: +m[1], height: +m[2] });
    await p.setContent(`<style>@font-face{font-family:Marcellus;src:url(file://${fonts}/marcellus.woff2)}body{margin:0}</style>${svg}`);
    await p.evaluate(() => document.fonts.ready);
    await p.waitForTimeout(50);
    await p.screenshot({ path: path.join(out, f.replace('.svg', '.png')), omitBackground: true });
  }
  await b.close();
})();
