<?php
/**
 * Title: الواجهة الرئيسية
 * Slug: room-salt/hero
 * Categories: room-salt, banner
 */
?>
<!-- wp:group {"className":"rs-hero","layout":{"type":"constrained"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|70"}}}} -->
<div class="wp-block-group rs-hero" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--70)">
<!-- wp:columns {"verticalAlignment":"center","align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|70"}}}} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center">
<!-- wp:column {"verticalAlignment":"center","width":"54%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:54%">
<!-- wp:paragraph {"className":"rs-eyebrow"} -->
<p class="rs-eyebrow"><span lang="en" dir="ltr">THE ROOM · THE SEA</span></p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":1,"className":"rs-hero-title"} -->
<h1 class="wp-block-heading rs-hero-title">غرفة الفندق،<br>وملح البحر الميت،<br>في بيتك.</h1>
<!-- /wp:heading -->
<!-- wp:paragraph {"fontSize":"lg","textColor":"muted"} -->
<p class="has-muted-color has-text-color has-lg-font-size">مناشف من القطن المصري، وأرواب، ومخدات وفرشات فندقية، ومستحضرات من أملاح البحر الميت وطينه. نوصلها إلى بابك، وتدفع عند الاستلام.</p>
<!-- /wp:paragraph -->
<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--50)">
<!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( rs_link( 'shop' ) ); ?>">تسوّق الآن</a></div>
<!-- /wp:button -->
<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( rs_link( 'cat:dead-sea' ) ); ?>">منتجات البحر الميت</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
</div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center">
<!-- wp:html -->
<div class="rs-hero-art" aria-hidden="true">
<svg viewBox="0 0 400 480" xmlns="http://www.w3.org/2000/svg">
	<defs>
		<clipPath id="rs-arch"><path d="M20 480V200A180 180 0 0 1 380 200V480Z"/></clipPath>
		<linearGradient id="rs-sky" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#F3E7D6"/><stop offset="1" stop-color="#F7F1E7"/></linearGradient>
		<linearGradient id="rs-sea" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#3C8E8A"/><stop offset="1" stop-color="#12343A"/></linearGradient>
	</defs>
	<path d="M8 480V200A192 192 0 0 1 392 200V480Z" fill="#12343A"/>
	<g clip-path="url(#rs-arch)">
		<rect width="400" height="480" fill="url(#rs-sky)"/>
		<circle cx="270" cy="150" r="34" fill="#E9C9A4" opacity=".7"/>
		<path d="M0 262 L40 236 L78 248 L120 214 L160 236 L205 206 L250 230 L290 210 L330 232 L370 214 L400 226 V300 H0Z" fill="#C98962"/>
		<path d="M0 282 L50 262 L96 272 L140 252 L188 270 L236 250 L284 268 L330 254 L400 270 V310 H0Z" fill="#B4613A"/>
		<rect y="296" width="400" height="184" fill="url(#rs-sea)"/>
		<path d="M0 312 H400" stroke="#F7F4EE" stroke-opacity=".35" stroke-width="2"/>
		<path d="M40 340 H150 M210 352 H340 M90 372 H220" stroke="#F7F4EE" stroke-opacity=".18" stroke-width="2" stroke-linecap="round"/>
		<path d="M0 420 Q60 404 120 414 T240 410 T400 416 V480 H0Z" fill="#F7F4EE"/>
		<path d="M30 430 l10 -8 l10 8 z M120 436 l7 -6 l7 6 z M300 428 l9 -7 l9 7 z M230 442 l6 -5 l6 5z" fill="#E4ECE9"/>
		<path d="M196 384 L210 398 L196 412 L182 398Z" fill="#F7F4EE"/>
	</g>
</svg>
<div class="rs-hero-key" lang="en" dir="ltr"><span>THE DEAD SEA</span>−430 m</div>
</div>
<!-- /wp:html -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->
