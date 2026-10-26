/* Nuzul static cart: cart in the browser, order sent to the store's WhatsApp.
 * Used on the static (no-server) version of the store. Expects window.NUZUL = {
 *   base: relative path to site root ("", "../", "../../"),
 *   products: [{id, sku, name, price, slug, img}],
 *   config: {whatsapp, currency, shipping: {AM, other, freeOver}, governorates: {code: name}, cliq}
 * }
 */
(function () {
  'use strict';
  var N = window.NUZUL || {};
  var C = N.config || {};
  var CUR = C.currency || 'د.أ';
  var KEY = 'nuzul_cart_v1';
  var mem = {};
  var bySku = {};
  var byId = {};
  (N.products || []).forEach(function (p) { bySku[p.sku] = p; byId[String(p.id)] = p; });

  /* ---------- storage ---------- */
  function load() {
    try { var s = localStorage.getItem(KEY); return s ? JSON.parse(s) : {}; } catch (e) { return mem; }
  }
  function save(cart) {
    mem = cart;
    try { localStorage.setItem(KEY, JSON.stringify(cart)); } catch (e) { /* private mode: keep in memory */ }
    render();
  }
  function items() {
    var cart = load();
    return Object.keys(cart).filter(function (k) { return bySku[k] && cart[k] > 0; })
      .map(function (k) { return { p: bySku[k], qty: cart[k] }; });
  }
  function count() { return items().reduce(function (s, i) { return s + i.qty; }, 0); }
  function subtotal() { return items().reduce(function (s, i) { return s + i.qty * i.p.price; }, 0); }
  function add(sku, qty) {
    var cart = load(); cart[sku] = Math.min(99, (cart[sku] || 0) + (qty || 1)); save(cart);
  }
  function setQty(sku, qty) {
    var cart = load(); if (qty <= 0) { delete cart[sku]; } else { cart[sku] = Math.min(99, qty); } save(cart);
  }
  function clear() { save({}); }

  /* ---------- helpers ---------- */
  function money(n) { return n.toFixed(2) + ' ' + CUR; }
  function esc(s) { return String(s).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); }
  function url(path) { return (N.base || '') + path; }
  function img(p) { return p.img ? url(p.img.replace(/^\//, '')) : ''; }
  function shippingFor(gov, sub) {
    if (!gov) { return null; }
    var s = C.shipping || {};
    if (s.freeOver && sub >= s.freeOver) { return 0; }
    return gov === 'AM' ? (s.AM || 0) : (s.other || 0);
  }
  function el(html) { var d = document.createElement('div'); d.innerHTML = html.trim(); return d.firstChild; }
  function toast(msg) {
    var t = document.getElementById('nz-toast');
    if (!t) { t = el('<div id="nz-toast" class="nz-toast" role="status" aria-live="polite"></div>'); document.body.appendChild(t); }
    t.textContent = msg; t.classList.add('is-on');
    clearTimeout(t._h); t._h = setTimeout(function () { t.classList.remove('is-on'); }, 2200);
  }

  /* ---------- header button + drawer ---------- */
  var bagIcon = '<svg viewBox="0 0 24 24" width="26" height="26" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" d="M5 8h14l-1.2 11.2a1 1 0 0 1-1 .8H7.2a1 1 0 0 1-1-.8L5 8Z"/><path fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" d="M9 8V6.5a3 3 0 0 1 6 0V8"/></svg>';

  function mountHeader() {
    var tools = document.querySelector('.nuzul-header-tools') || document.querySelector('.nuzul-header .wp-block-group.alignwide');
    if (!tools || document.getElementById('nz-cart-btn')) { return; }
    var b = el('<button type="button" id="nz-cart-btn" class="nz-cart-btn" aria-label="السلة">' + bagIcon + '<span class="nz-badge" hidden>0</span></button>');
    b.addEventListener('click', openDrawer);
    tools.appendChild(b);
  }

  function mountDrawer() {
    if (document.getElementById('nz-drawer')) { return; }
    var d = el('<div id="nz-drawer" class="nz-drawer" hidden>' +
      '<div class="nz-scrim" data-nz-close></div>' +
      '<aside class="nz-panel" role="dialog" aria-modal="true" aria-labelledby="nz-drawer-title">' +
      '<header class="nz-panel-head"><h2 id="nz-drawer-title">سلة التسوق</h2>' +
      '<button type="button" class="nz-x" data-nz-close aria-label="إغلاق">×</button></header>' +
      '<div class="nz-panel-body" id="nz-drawer-body"></div>' +
      '<footer class="nz-panel-foot" id="nz-drawer-foot"></footer>' +
      '</aside></div>');
    document.body.appendChild(d);
    d.addEventListener('click', function (e) {
      if (e.target.closest('[data-nz-close]')) { closeDrawer(); }
      var q = e.target.closest('[data-nz-qty]');
      if (q) { var it = load(); var sku = q.getAttribute('data-sku'); setQty(sku, (it[sku] || 0) + parseInt(q.getAttribute('data-nz-qty'), 10)); }
      var r = e.target.closest('[data-nz-remove]');
      if (r) { setQty(r.getAttribute('data-sku'), 0); }
    });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { closeDrawer(); } });
  }
  var lastFocus = null;
  function openDrawer() {
    var d = document.getElementById('nz-drawer'); if (!d) { return; }
    lastFocus = document.activeElement; d.hidden = false; document.documentElement.classList.add('nz-lock');
    var x = d.querySelector('.nz-x'); if (x) { x.focus(); }
  }
  function closeDrawer() {
    var d = document.getElementById('nz-drawer'); if (!d || d.hidden) { return; }
    d.hidden = true; document.documentElement.classList.remove('nz-lock');
    if (lastFocus && lastFocus.focus) { lastFocus.focus(); }
  }

  function lineHtml(i, compact) {
    var p = i.p;
    return '<li class="nz-line">' +
      (p.img ? '<img src="' + esc(img(p)) + '" alt="" width="64" height="64" loading="lazy">' : '<span class="nz-noimg"></span>') +
      '<div class="nz-line-main"><a class="nz-line-name" href="' + esc(url('product/' + p.slug + '/index.html')) + '">' + esc(p.name) + '</a>' +
      '<span class="nz-line-unit">' + money(p.price) + '</span>' +
      (compact ? '<span class="nz-line-qty">الكمية: ' + i.qty + '</span>' :
        '<div class="nz-stepper" aria-label="الكمية">' +
        '<button type="button" data-nz-qty="1" data-sku="' + esc(p.sku) + '" aria-label="زيادة">+</button>' +
        '<span>' + i.qty + '</span>' +
        '<button type="button" data-nz-qty="-1" data-sku="' + esc(p.sku) + '" aria-label="إنقاص">−</button></div>') +
      '</div>' +
      '<div class="nz-line-end"><strong>' + money(i.qty * p.price) + '</strong>' +
      (compact ? '' : '<button type="button" class="nz-link" data-nz-remove data-sku="' + esc(p.sku) + '">حذف</button>') +
      '</div></li>';
  }

  function freeHint(sub) {
    var f = (C.shipping || {}).freeOver;
    if (!f) { return ''; }
    if (sub >= f) { return '<p class="nz-free is-ok">طلبك مؤهل للتوصيل المجاني.</p>'; }
    return '<p class="nz-free">أضف ' + money(f - sub) + ' لتحصل على توصيل مجاني.</p>';
  }

  function renderDrawer() {
    var body = document.getElementById('nz-drawer-body'); var foot = document.getElementById('nz-drawer-foot');
    if (!body) { return; }
    var list = items();
    if (!list.length) {
      body.innerHTML = '<div class="nz-empty"><p>سلتك فارغة.</p><a class="nz-btn is-outline" href="' + esc(url('shop/index.html')) + '">تصفّح المنتجات</a></div>';
      foot.innerHTML = '';
      return;
    }
    var sub = subtotal();
    body.innerHTML = '<ul class="nz-lines">' + list.map(function (i) { return lineHtml(i, false); }).join('') + '</ul>';
    foot.innerHTML = freeHint(sub) +
      '<div class="nz-row"><span>المجموع الفرعي</span><strong>' + money(sub) + '</strong></div>' +
      '<p class="nz-note">رسوم التوصيل تُحسب حسب المحافظة في الخطوة التالية.</p>' +
      '<a class="nz-btn" href="' + esc(url('order/index.html')) + '">إتمام الطلب</a>';
  }

  function render() {
    var n = count();
    var b = document.querySelector('#nz-cart-btn .nz-badge');
    if (b) { b.textContent = n; b.hidden = n === 0; }
    renderDrawer();
    renderCheckout();
  }

  /* ---------- add-to-cart buttons ---------- */
  function wireAdds() {
    document.addEventListener('click', function (e) {
      var b = e.target.closest('[data-nz-add]');
      if (!b) { return; }
      e.preventDefault();
      var sku = b.getAttribute('data-nz-add');
      var wrap = b.closest('.nz-buy');
      var qtyEl = wrap ? wrap.querySelector('.nz-buy-qty') : null;
      var qty = qtyEl ? Math.max(1, parseInt(qtyEl.value, 10) || 1) : 1;
      add(sku, qty);
      if (wrap) { openDrawer(); } else { toast('أُضيف إلى السلة'); }
    });
    document.addEventListener('click', function (e) {
      var s = e.target.closest('[data-nz-step]');
      if (!s) { return; }
      var input = s.parentNode.querySelector('.nz-buy-qty');
      input.value = Math.min(99, Math.max(1, (parseInt(input.value, 10) || 1) + parseInt(s.getAttribute('data-nz-step'), 10)));
    });
  }

  /* ---------- checkout ---------- */
  var lastOrder = null;

  function orderNo() {
    var d = new Date();
    var p = function (n) { return (n < 10 ? '0' : '') + n; };
    return 'NZ-' + String(d.getFullYear()).slice(2) + p(d.getMonth() + 1) + p(d.getDate()) + '-' + Math.floor(1000 + Math.random() * 9000);
  }

  function govOptions() {
    var g = C.governorates || {};
    return '<option value="">اختر المحافظة</option>' + Object.keys(g).map(function (k) {
      return '<option value="' + esc(k) + '">' + esc(g[k]) + '</option>';
    }).join('');
  }

  function summaryHtml(gov) {
    var list = items(); var sub = subtotal(); var ship = shippingFor(gov, sub);
    return '<h2 class="nz-h">ملخص الطلب</h2>' +
      '<ul class="nz-lines is-compact">' + list.map(function (i) { return lineHtml(i, true); }).join('') + '</ul>' +
      '<div class="nz-row"><span>المجموع الفرعي</span><span>' + money(sub) + '</span></div>' +
      '<div class="nz-row"><span>التوصيل</span><span>' + (ship === null ? 'اختر المحافظة' : (ship === 0 ? 'مجاني' : money(ship))) + '</span></div>' +
      '<div class="nz-row is-total"><span>الإجمالي</span><strong>' + money(sub + (ship || 0)) + '</strong></div>' +
      freeHint(sub) +
      '<p class="nz-note"><a href="' + esc(url('shop/index.html')) + '">متابعة التسوق</a></p>';
  }

  function renderCheckout() {
    var root = document.getElementById('nz-checkout-root');
    if (!root) { return; }
    if (lastOrder) { return; }
    if (!items().length) {
      root.innerHTML = '<div class="nz-empty"><p>سلتك فارغة. أضف منتجات أولاً.</p><a class="nz-btn" href="' + esc(url('shop/index.html')) + '">تصفّح المنتجات</a></div>';
      return;
    }
    if (!root.querySelector('form')) {
      root.innerHTML = '<div class="nz-checkout">' +
        '<form class="nz-form" id="nz-order-form" novalidate>' +
        '<h2 class="nz-h">بيانات التوصيل</h2>' +
        field('nz-name', 'الاسم الكامل', '<input id="nz-name" name="name" autocomplete="name" required>') +
        field('nz-phone', 'رقم الهاتف', '<input id="nz-phone" name="phone" type="tel" inputmode="tel" dir="ltr" placeholder="07XXXXXXXX" autocomplete="tel" required>') +
        field('nz-gov', 'المحافظة', '<select id="nz-gov" name="gov" required>' + govOptions() + '</select>') +
        field('nz-area', 'المدينة / المنطقة', '<input id="nz-area" name="area" required>') +
        field('nz-address', 'العنوان (الحي، الشارع، رقم البناية)', '<input id="nz-address" name="address" autocomplete="street-address" required>') +
        field('nz-notes', 'ملاحظات (اختياري)', '<textarea id="nz-notes" name="notes" rows="2"></textarea>') +
        '<fieldset class="nz-pay"><legend>طريقة الدفع</legend>' +
        '<label><input type="radio" name="pay" value="cod" checked> الدفع نقداً عند الاستلام</label>' +
        '<label><input type="radio" name="pay" value="cliq"> تحويل فوري عبر CliQ</label>' +
        '<p class="nz-note" id="nz-cliq-note" hidden>نرسل لك تفاصيل التحويل عبر واتساب بعد تأكيد الطلب.</p>' +
        '</fieldset>' +
        '<p class="nz-error" id="nz-error" role="alert" hidden></p>' +
        '<button type="submit" class="nz-btn is-big">إرسال الطلب عبر واتساب</button>' +
        '<p class="nz-note">بالضغط على الزر يُفتح واتساب برسالة فيها تفاصيل طلبك، أرسلها لنا ونتواصل معك لتأكيد الطلب وموعد التوصيل.</p>' +
        (C.whatsapp ? '' : '<p class="nz-note is-warn">رقم واتساب المتجر لم يُضف بعد.</p>') +
        '</form><aside class="nz-summary" id="nz-summary"></aside></div>';
      var form = root.querySelector('form');
      form.addEventListener('change', function () {
        document.getElementById('nz-summary').innerHTML = summaryHtml(form.elements.gov.value);
        document.getElementById('nz-cliq-note').hidden = form.elements.pay.value !== 'cliq';
      });
      form.addEventListener('submit', submitOrder);
    }
    var f = root.querySelector('form');
    document.getElementById('nz-summary').innerHTML = summaryHtml(f.elements.gov.value);
  }

  function field(id, label, control) {
    return '<div class="nz-field"><label for="' + id + '">' + label + '</label>' + control + '</div>';
  }

  function normalizePhone(v) {
    var d = String(v).replace(/[^\d+]/g, '').replace(/^\+?962/, '0').replace(/^00962/, '0');
    if (/^7\d{8}$/.test(d)) { d = '0' + d; }
    return d;
  }

  function submitOrder(e) {
    e.preventDefault();
    var f = e.target; var F = function (k) { return f.elements[k]; }; var err = document.getElementById('nz-error');
    var phone = normalizePhone(F('phone').value);
    var missing = [];
    ['name', 'gov', 'area', 'address'].forEach(function (k) { if (!String(F(k).value).trim()) { missing.push(k); } });
    var msg = '';
    if (missing.length) { msg = 'يرجى تعبئة جميع الحقول المطلوبة.'; F(missing[0]).focus(); }
    else if (!/^07[789]\d{7}$/.test(phone)) { msg = 'رقم الهاتف غير صحيح. اكتبه بالشكل 07XXXXXXXX.'; F('phone').focus(); }
    if (msg) { err.textContent = msg; err.hidden = false; return; }
    err.hidden = true;

    var list = items(); var sub = subtotal(); var ship = shippingFor(F('gov').value, sub) || 0;
    var no = orderNo(); var govName = (C.governorates || {})[F('gov').value] || F('gov').value;
    var pay = F('pay').value === 'cliq' ? 'تحويل فوري عبر CliQ' : 'نقداً عند الاستلام';
    var lines = [
      'طلب جديد من موقع نُزُل',
      'رقم الطلب: ' + no,
      '',
      'المنتجات:'
    ].concat(list.map(function (i) { return '• ' + i.p.name + ' × ' + i.qty + ' = ' + money(i.qty * i.p.price); })).concat([
      '',
      'المجموع الفرعي: ' + money(sub),
      'التوصيل (' + govName + '): ' + (ship === 0 ? 'مجاني' : money(ship)),
      'الإجمالي: ' + money(sub + ship),
      'طريقة الدفع: ' + pay,
      '',
      'الاسم: ' + F('name').value.trim(),
      'الهاتف: ' + phone,
      'المحافظة: ' + govName,
      'المنطقة: ' + F('area').value.trim(),
      'العنوان: ' + F('address').value.trim()
    ]);
    if (F('notes').value.trim()) { lines.push('ملاحظات: ' + F('notes').value.trim()); }
    var text = lines.join('\n');
    var link = 'https://wa.me/' + String(C.whatsapp || '').replace(/\D/g, '') + '?text=' + encodeURIComponent(text);
    lastOrder = { no: no, text: text, link: link, total: sub + ship };
    try { sessionStorage.setItem('nuzul_last_order', JSON.stringify(lastOrder)); } catch (x) { /* ignore */ }
    showConfirmation();
    clear();
    window.open(link, '_blank', 'noopener');
  }

  function showConfirmation() {
    var root = document.getElementById('nz-checkout-root');
    if (!root || !lastOrder) { return; }
    root.innerHTML = '<div class="nz-done">' +
      '<p class="nz-done-no">طلب رقم <strong dir="ltr">' + esc(lastOrder.no) + '</strong></p>' +
      '<h2 class="nz-h">بقيت خطوة واحدة: أرسل الرسالة على واتساب</h2>' +
      '<p>فتحنا لك واتساب برسالة فيها كل تفاصيل الطلب. اضغط «إرسال» هناك، ونتواصل معك لتأكيد الطلب وموعد التوصيل.</p>' +
      '<p class="nz-row is-total"><span>الإجمالي</span><strong>' + money(lastOrder.total) + '</strong></p>' +
      '<div class="nz-actions"><a class="nz-btn" href="' + esc(lastOrder.link) + '" target="_blank" rel="noopener">فتح واتساب مرة أخرى</a>' +
      '<button type="button" class="nz-btn is-outline" id="nz-copy">نسخ تفاصيل الطلب</button></div>' +
      '<pre class="nz-pre" id="nz-order-text">' + esc(lastOrder.text) + '</pre></div>';
    document.getElementById('nz-copy').addEventListener('click', function () {
      var pre = document.getElementById('nz-order-text');
      var done = function () { toast('تم النسخ'); };
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(lastOrder.text).then(done, function () { select(pre); });
      } else { select(pre); }
    });
  }
  function select(node) {
    var r = document.createRange(); r.selectNodeContents(node); var s = window.getSelection(); s.removeAllRanges(); s.addRange(r);
  }

  /* ---------- boot ---------- */
  function boot() {
    mountHeader(); mountDrawer(); wireAdds();
    if (document.getElementById('nz-checkout-root')) { document.body.classList.add('nz-is-checkout'); }
    render();
    window.addEventListener('storage', function (e) { if (e.key === KEY) { render(); } });
  }
  if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', boot); } else { boot(); }
}());
