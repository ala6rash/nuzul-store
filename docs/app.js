/* Room & Salt storefront: catalogue, cart, checkout → WhatsApp order. */
(() => {
	'use strict';

	// Store settings: set the real WhatsApp number (962XXXXXXXXX) and CliQ alias before launch.
	const STORE = { whatsapp: '', cliq: 'ROOMANDSALT', freeOver: 35, shipAmman: 2, shipOther: 3 };
	const GOVS = ['عمّان', 'إربد', 'الزرقاء', 'البلقاء', 'مادبا', 'جرش', 'عجلون', 'المفرق', 'الكرك', 'الطفيلة', 'معان', 'العقبة'];
	const TOP_CATS = ['towels', 'robes-slippers', 'bedding', 'mattresses', 'bath-amenities', 'dead-sea'];
	const CAT_IMG = { towels: 'RS-TW-02', 'robes-slippers': 'RS-RB-01', bedding: 'RS-BD-07', mattresses: 'RS-MT-01', 'bath-amenities': 'RS-AM-01', 'dead-sea': 'RS-DS-01' };
	const SEA_PICKS = ['RS-DS-01', 'RS-DS-06', 'RS-DS-03', 'RS-DS-21', 'RS-DS-18'];
	const SRC_NOTE = {
		supplier: 'صورة المنتج من الشركة المصنّعة Beauty Secrets.',
		showroom: 'صورة المنتج الفعلي من معرضنا.',
		illustration: 'صورة توضيحية. اطلب صورة المنتج الفعلي عبر واتساب.',
	};

	const { categories, products } = window.RS_DATA;
	const byId = Object.fromEntries(products.map((p) => [p.id, p]));
	const catBy = Object.fromEntries(categories.map((c) => [c.slug, c]));
	const $ = (s, el = document) => el.querySelector(s);
	const $$ = (s, el = document) => [...el.querySelectorAll(s)];
	const esc = (s) => String(s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
	const money = (n) => `${n.toFixed(2)} د.أ`;
	const inCat = (p, slug) => slug === 'all' || p.cat === slug || catBy[p.cat]?.parent === slug;
	const brandOf = (p) => (p.src === 'supplier' ? 'Beauty Secrets' : /Rest One/.test(p.specs.join(' ')) ? 'Rest One' : /Astoria/.test(p.short) ? 'Astoria' : '');

	const waUrl = (text) => `https://wa.me/${STORE.whatsapp}?text=${encodeURIComponent(text)}`;
	const store = {
		get() { try { return JSON.parse(localStorage.getItem('rs-cart') || '{}'); } catch { return {}; } },
		set(c) { try { localStorage.setItem('rs-cart', JSON.stringify(c)); } catch { /* private mode */ } },
	};
	let cart = Object.fromEntries(Object.entries(store.get()).filter(([id, q]) => byId[id] && byId[id].price !== null && q > 0));

	/* ---------- Toast ---------- */
	let toastT;
	function toast(msg) {
		const t = $('[data-toast]');
		t.textContent = msg; t.classList.add('show');
		clearTimeout(toastT); toastT = setTimeout(() => t.classList.remove('show'), 2200);
	}

	/* ---------- Categories + chips ---------- */
	function renderCats() {
		$('[data-cats]').innerHTML = TOP_CATS.map((slug) => {
			const c = catBy[slug];
			const n = products.filter((p) => inCat(p, slug)).length;
			return `<a class="cat" href="#shop" data-cat="${slug}"><img src="img/p/${CAT_IMG[slug]}.jpg" alt="" loading="lazy" width="900" height="900"><span class="cat-txt"><span lang="en">${esc(c.en)}</span><b>${esc(c.name)}</b><small>${n} منتجاً</small></span></a>`;
		}).join('');
		$('[data-foot-cats]').innerHTML = TOP_CATS.map((s) => `<li><a href="#shop" data-cat="${s}">${esc(catBy[s].name)}</a></li>`).join('');
		const chipCats = ['all', ...TOP_CATS, 'dead-sea-face', 'dead-sea-body'];
		$('[data-chips]').innerHTML = chipCats.map((s) => {
			const n = products.filter((p) => inCat(p, s)).length;
			const name = s === 'all' ? 'الكل' : (catBy[s].parent ? `البحر الميت: ${catBy[s].name}` : catBy[s].name);
			return `<button class="chip" role="tab" type="button" data-chip="${s}" aria-selected="${s === 'all'}">${esc(name)}<small>${n}</small></button>`;
		}).join('');
	}

	/* ---------- Grid ---------- */
	let filter = 'all';
	let query = '';
	function card(p, i) {
		const brand = brandOf(p);
		const price = p.price !== null ? `<span class="price">${money(p.price)}</span>` : '<span class="price ask">السعر عند الطلب</span>';
		const btn = p.price !== null
			? `<button class="add" type="button" data-add="${p.id}">أضف إلى السلة</button>`
			: `<a class="add ask" href="${waUrl(`مرحباً Room & Salt، أود معرفة سعر: ${p.name}`)}" target="_blank" rel="noopener">اسأل عن السعر</a>`;
		return `<article class="card" style="animation-delay:${Math.min(i, 12) * 45}ms">
			<button class="card-img" type="button" data-open="${p.id}" aria-label="${esc(p.name)}"><img src="${p.img}" alt="${esc(p.name)}" loading="lazy" width="900" height="900">${brand ? `<span class="badge brand" lang="en">${brand}</span>` : ''}</button>
			<h3 data-open="${p.id}">${esc(p.name)}</h3>
			<p class="en" lang="en">${esc(p.en)}</p>
			<div class="card-foot">${price}${btn}</div>
		</article>`;
	}
	function renderGrid() {
		const q = query.trim().toLowerCase();
		const list = products.filter((p) => inCat(p, filter) && (!q || (p.name + ' ' + p.en + ' ' + p.short).toLowerCase().includes(q)));
		$('[data-grid]').innerHTML = list.map(card).join('');
		$('[data-empty]').hidden = list.length > 0;
		$('[data-shop-title]').textContent = filter === 'all' ? 'جميع المنتجات' : (catBy[filter].parent ? `البحر الميت: ${catBy[filter].name}` : catBy[filter].name);
		$$('[data-chip]').forEach((b) => b.setAttribute('aria-selected', String(b.dataset.chip === filter)));
	}
	function setFilter(slug) { filter = catBy[slug] || slug === 'all' ? slug : 'all'; query = ''; $('[data-search]').value = ''; renderGrid(); }

	function renderSea() {
		$('[data-sea]').innerHTML = SEA_PICKS.map((id) => `<button type="button" data-open="${id}" aria-label="${esc(byId[id].name)}"><img src="${byId[id].img}" alt="" loading="lazy" width="900" height="900"></button>`).join('');
	}

	/* ---------- Product modal ---------- */
	const pmDlg = $('[data-product-modal]');
	function openProduct(id) {
		const p = byId[id];
		if (!p) return;
		const imgs = [p.img, p.real].filter(Boolean);
		let qty = 1;
		const buy = p.price !== null
			? `<div class="qty"><button type="button" data-q="1" aria-label="زيادة">+</button><output data-qv>1</output><button type="button" data-q="-1" aria-label="إنقاص">−</button></div>
			   <button class="btn btn-dark" type="button" data-pm-add>أضف إلى السلة</button>`
			: '';
		$('[data-pm]').innerHTML = `
			<div class="pm-gal">
				<img class="pm-main" src="${imgs[0]}" alt="${esc(p.name)}" width="900" height="900">
				${imgs.length > 1 ? `<div class="pm-thumbs">${imgs.map((s, i) => `<button type="button" data-img="${s}" aria-pressed="${i === 0}"><img src="${s}" alt=""></button>`).join('')}</div>` : ''}
				<p class="pm-src">${SRC_NOTE[p.src]}${p.real ? ' الصورة الثانية من معرضنا.' : ''}</p>
			</div>
			<div class="pm-info">
				<p class="eyebrow"><span>${esc(catBy[p.cat].parent ? 'البحر الميت · ' + catBy[p.cat].name : catBy[p.cat].name)}</span></p>
				<h2>${esc(p.name)}</h2>
				<p class="en" lang="en">${esc(p.en)}</p>
				${p.price !== null ? `<p class="price">${money(p.price)}</p>` : '<p class="price ask">السعر عند الطلب</p>'}
				<p>${esc(p.short)}</p>
				${p.specs.length ? `<ul>${p.specs.map((s) => `<li>${esc(s)}</li>`).join('')}</ul>` : ''}
				<div class="pm-buy">${buy}
					<a class="btn btn-wa" href="${waUrl(`مرحباً Room & Salt، ${p.price !== null ? 'أود طلب' : 'أود معرفة سعر'}: ${p.name}`)}" target="_blank" rel="noopener">${p.price !== null ? 'اطلب عبر واتساب' : 'اسأل عن السعر عبر واتساب'}</a>
				</div>
			</div>`;
		const pm = $('[data-pm]');
		pm.onclick = (e) => {
			const t = e.target.closest('button');
			if (!t) return;
			if (t.dataset.img) { $('.pm-main', pm).src = t.dataset.img; $$('.pm-thumbs button', pm).forEach((b) => b.setAttribute('aria-pressed', String(b === t))); }
			if (t.dataset.q) { qty = Math.max(1, Math.min(99, qty + Number(t.dataset.q))); $('[data-qv]', pm).textContent = qty; }
			if (t.hasAttribute('data-pm-add')) { add(p.id, qty); pmDlg.close(); }
		};
		pmDlg.showModal();
	}

	/* ---------- Cart ---------- */
	const drawer = $('[data-drawer]');
	const back = $('[data-drawer-back]');
	const count = () => Object.values(cart).reduce((a, b) => a + b, 0);
	const subtotal = () => Object.entries(cart).reduce((a, [id, q]) => a + byId[id].price * q, 0);
	function add(id, q = 1) {
		cart[id] = Math.min(99, (cart[id] || 0) + q);
		saveCart();
		toast(`أضيف إلى السلة: ${byId[id].name}`);
		const c = $('[data-cart-count]'); c.classList.remove('bump'); void c.offsetWidth; c.classList.add('bump');
	}
	function saveCart() { store.set(cart); renderCart(); }
	function renderCart() {
		const n = count();
		const c = $('[data-cart-count]'); c.textContent = n; c.hidden = n === 0;
		const items = Object.entries(cart);
		$('[data-cart-items]').innerHTML = items.length ? items.map(([id, q]) => {
			const p = byId[id];
			return `<div class="line"><img src="${p.img}" alt=""><div><h4>${esc(p.name)}</h4><span class="price">${money(p.price * q)}</span><br><button class="rm" type="button" data-rm="${id}">إزالة</button></div>
				<div class="qty"><button type="button" data-cq="${id}" data-d="1" aria-label="زيادة">+</button><output>${q}</output><button type="button" data-cq="${id}" data-d="-1" aria-label="إنقاص">−</button></div></div>`;
		}).join('') : '<p class="cart-empty">سلتك فارغة.<br><a href="#shop" data-close-cart>تصفّح المنتجات</a></p>';
		const s = subtotal();
		$('[data-cart-subtotal]').textContent = money(s);
		$('[data-free-note]').textContent = s === 0 ? '' : s >= STORE.freeOver ? 'طلبك مؤهل للتوصيل المجاني.' : `أضف ${money(STORE.freeOver - s)} للحصول على توصيل مجاني.`;
		$('[data-checkout]').disabled = items.length === 0;
	}
	function openCart() { drawer.classList.add('open'); drawer.setAttribute('aria-hidden', 'false'); back.hidden = false; $('[data-close-cart]', drawer).focus(); }
	function closeCart() { drawer.classList.remove('open'); drawer.setAttribute('aria-hidden', 'true'); back.hidden = true; }

	/* ---------- Checkout ---------- */
	const coDlg = $('[data-checkout-modal]');
	const form = $('[data-checkout-form]');
	const ship = (gov, s) => (s >= STORE.freeOver ? 0 : gov === 'عمّان' ? STORE.shipAmman : STORE.shipOther);
	function renderSummary() {
		const s = subtotal();
		const gov = form.gov.value;
		const sh = gov ? ship(gov, s) : null;
		$('[data-co-items]').innerHTML = Object.entries(cart).map(([id, q]) => `<li><span>${esc(byId[id].name)} × ${q}</span><span>${money(byId[id].price * q)}</span></li>`).join('');
		$('[data-co-sub]').textContent = money(s);
		$('[data-co-ship]').textContent = sh === null ? 'اختر المحافظة' : sh === 0 ? 'مجاني' : money(sh);
		$('[data-co-total]').textContent = money(s + (sh || 0));
	}
	function openCheckout() {
		closeCart();
		form.hidden = false; $('[data-done]').hidden = true; $('[data-co-err]').hidden = true;
		renderSummary();
		coDlg.showModal();
	}
	function submitOrder(e) {
		e.preventDefault();
		const f = form;
		const phone = f.phone.value.replace(/\D+/g, '').replace(/^962/, '0');
		const bad = [];
		['name', 'city', 'address'].forEach((k) => { if (f[k].value.trim().length < 2) bad.push(f[k]); });
		if (!/^07[789]\d{7}$/.test(phone)) bad.push(f.phone);
		if (!f.gov.value) bad.push(f.gov);
		$$('.bad', f).forEach((x) => x.classList.remove('bad'));
		bad.forEach((x) => x.classList.add('bad'));
		const err = $('[data-co-err]');
		if (bad.length) {
			err.textContent = bad.includes(f.phone) && bad.length === 1 ? 'رقم الهاتف غير صحيح. اكتبه بصيغة 07XXXXXXXX.' : 'يرجى تعبئة الحقول المطلوبة.';
			err.hidden = false; bad[0].focus(); return;
		}
		err.hidden = true;
		const s = subtotal();
		const sh = ship(f.gov.value, s);
		const pay = f.pay.value;
		const no = 'RS-' + Date.now().toString(36).toUpperCase().slice(-6);
		const lines = Object.entries(cart).map(([id, q]) => `• ${byId[id].name} × ${q} = ${money(byId[id].price * q)}`);
		const msg = [
			`طلب جديد من موقع Room & Salt`, `رقم الطلب: ${no}`, '', ...lines, '',
			`المنتجات: ${money(s)}`, `التوصيل: ${sh === 0 ? 'مجاني' : money(sh)}`, `الإجمالي: ${money(s + sh)}`,
			`طريقة الدفع: ${pay === 'cliq' ? 'تحويل فوري عبر CliQ' : 'الدفع عند الاستلام'}`, '',
			`الاسم: ${f.name.value.trim()}`, `الهاتف: ${phone}`, `المحافظة: ${f.gov.value}`, `المدينة: ${f.city.value.trim()}`, `العنوان: ${f.address.value.trim()}`,
			f.notes.value.trim() ? `ملاحظات: ${f.notes.value.trim()}` : '',
		].filter((x, i, a) => x !== '' || a[i - 1] !== '').join('\n');
		const url = waUrl(msg);
		window.open(url, '_blank', 'noopener');
		form.hidden = true;
		$('[data-done]').innerHTML = `<div class="tick">✓</div><h2>تم تسجيل طلبك</h2><p>رقم الطلب</p><p class="ord">${no}</p>
			<p class="muted">فُتح واتساب برسالة فيها تفاصيل طلبك. اضغط «إرسال» لنستلمه ونتواصل معك لتأكيد موعد التوصيل.</p>
			${pay === 'cliq' ? `<div class="cliq-box">حوّل <b>${money(s + sh)}</b> عبر CliQ إلى الاسم المستعار <b lang="en">${esc(STORE.cliq)}</b>، واكتب رقم الطلب <b lang="en">${no}</b> في ملاحظة التحويل.</div>` : ''}
			<a class="btn btn-wa" href="${url}" target="_blank" rel="noopener">فتح واتساب مرة أخرى</a>
			<button class="btn btn-line" type="button" data-close>متابعة التسوق</button>`;
		$('[data-done]').hidden = false;
		cart = {}; saveCart(); f.reset(); renderSummary();
	}

	/* ---------- Wiring ---------- */
	function init() {
		renderCats(); renderGrid(); renderSea(); renderCart();
		$('[data-gov]').innerHTML = '<option value="">اختر المحافظة</option>' + GOVS.map((g) => `<option>${g}</option>`).join('');
		$$('[data-cliq]').forEach((el) => { el.textContent = STORE.cliq; });
		$$('[data-year]').forEach((el) => { el.textContent = new Date().getFullYear(); });
		$$('[data-wa]').forEach((a) => { a.href = waUrl('مرحباً Room & Salt، لدي سؤال'); a.target = '_blank'; a.rel = 'noopener'; });
		$$('.hero-copy > *').forEach((el, i) => { el.classList.add('reveal'); el.style.animationDelay = `${120 + i * 110}ms`; });

		document.addEventListener('click', (e) => {
			const t = e.target.closest('[data-cat],[data-chip],[data-open],[data-add],[data-open-cart],[data-close-cart],[data-checkout],[data-close],[data-rm],[data-cq]');
			if (!t) return;
			if (t.dataset.cat) setFilter(t.dataset.cat);
			if (t.dataset.chip) setFilter(t.dataset.chip);
			if (t.dataset.open) openProduct(t.dataset.open);
			if (t.dataset.add) { add(t.dataset.add); t.classList.add('done'); t.textContent = 'أضيف ✓'; setTimeout(() => { t.classList.remove('done'); t.textContent = 'أضف إلى السلة'; }, 1400); }
			if (t.hasAttribute('data-open-cart')) openCart();
			if (t.hasAttribute('data-close-cart')) closeCart();
			if (t.hasAttribute('data-checkout')) openCheckout();
			if (t.hasAttribute('data-close')) t.closest('dialog')?.close();
			if (t.dataset.rm) { delete cart[t.dataset.rm]; saveCart(); }
			if (t.dataset.cq) { const q = (cart[t.dataset.cq] || 0) + Number(t.dataset.d); if (q <= 0) delete cart[t.dataset.cq]; else cart[t.dataset.cq] = Math.min(99, q); saveCart(); }
		});
		back.addEventListener('click', closeCart);
		document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && drawer.classList.contains('open')) closeCart(); });
		$$('dialog').forEach((d) => d.addEventListener('click', (e) => { if (e.target === d) d.close(); }));
		$('[data-search]').addEventListener('input', (e) => { query = e.target.value; filter = 'all'; renderGrid(); });
		form.addEventListener('submit', submitOrder);
		form.gov.addEventListener('change', renderSummary);
	}
	init();
})();
