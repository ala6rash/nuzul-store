<?php
/**
 * Title: منتجات البحر الميت
 * Slug: room-salt/sea-care
 * Categories: room-salt, woo-commerce
 */
?>
<!-- wp:html -->
<section class="rs-split rs-split--flip">
	<span class="rs-blob rs-blob--b" aria-hidden="true"></span>
	<div class="rs-split-inner">
		<figure class="rs-split-media rs-split-media--arch" aria-hidden="true">
			<svg viewBox="0 0 400 480" xmlns="http://www.w3.org/2000/svg">
				<defs>
					<clipPath id="rs-arch2"><path d="M20 480V200A180 180 0 0 1 380 200V480Z"/></clipPath>
					<linearGradient id="rs-sky2" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#F3E7D6"/><stop offset="1" stop-color="#F7F1E7"/></linearGradient>
					<linearGradient id="rs-sea2" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#3C8E8A"/><stop offset="1" stop-color="#12343A"/></linearGradient>
				</defs>
				<path d="M8 480V200A192 192 0 0 1 392 200V480Z" fill="#12343A"/>
				<g clip-path="url(#rs-arch2)">
					<rect width="400" height="480" fill="url(#rs-sky2)"/>
					<circle cx="270" cy="150" r="34" fill="#E9C9A4" opacity=".7"/>
					<path d="M0 262 L40 236 L78 248 L120 214 L160 236 L205 206 L250 230 L290 210 L330 232 L370 214 L400 226 V300 H0Z" fill="#C98962"/>
					<path d="M0 282 L50 262 L96 272 L140 252 L188 270 L236 250 L284 268 L330 254 L400 270 V310 H0Z" fill="#B4613A"/>
					<rect y="296" width="400" height="184" fill="url(#rs-sea2)"/>
					<path d="M40 340 H150 M210 352 H340 M90 372 H220" stroke="#F7F4EE" stroke-opacity=".18" stroke-width="2" stroke-linecap="round"/>
					<path d="M0 420 Q60 404 120 414 T240 410 T400 416 V480 H0Z" fill="#F7F4EE"/>
					<path d="M196 384 L210 398 L196 412 L182 398Z" fill="#F7F4EE"/>
				</g>
			</svg>
		</figure>
		<div class="rs-split-body">
			<p class="rs-kicker"><span lang="en" dir="ltr">FACE &amp; BODY</span></p>
			<h2 class="rs-split-title">عناية البحر الميت</h2>
			<p class="rs-split-text">27 منتجاً من Beauty Secrets: كريمات الوجه، وماسكات الطين، والصابون، والأملاح، بأسعار من 3 إلى 7 دنانير.</p>
			<?php echo do_shortcode( '[rs_carousel cats="dead-sea" limit="16" label="منتجات البحر الميت"]' ); ?>
		</div>
	</div>
</section>
<!-- /wp:html -->
