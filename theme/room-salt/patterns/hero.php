<?php
/**
 * Title: الواجهة الرئيسية
 * Slug: room-salt/hero
 * Categories: room-salt, banner
 */
$rs_from = function_exists( 'rs_sku_price' ) ? rs_sku_price( 'RS-AM-01', '1' ) : '1';
?>
<!-- wp:html -->
<section class="rs-stage rs-stage--hero">
	<img class="rs-stage-img" src="<?php echo rs_img( 'scenes/scene-hero.jpg' ); ?>" alt="مناشف بيضاء من القطن المصري مع منتجات البحر الميت" width="1920" height="1000" fetchpriority="high">
	<div class="rs-stage-panel">
		<p class="rs-kicker"><span lang="en" dir="ltr">ROOM &amp; SALT</span></p>
		<h1 class="rs-stage-title">غرفة الفندق، وملح البحر الميت، في بيتك.</h1>
		<p class="rs-stage-text">مناشف من القطن المصري، وأرواب ومخدات وفرشات فندقية، ومستحضرات من أملاح البحر الميت وطينه، بأسعار معرضنا نفسها.</p>
		<div class="rs-pill-cta">
			<span class="rs-pill-price">من <?php echo esc_html( $rs_from ); ?> د.أ</span>
			<a class="rs-pill-link" href="<?php echo esc_url( rs_link( 'shop' ) ); ?>">تسوّق الآن</a>
		</div>
	</div>
</section>
<!-- /wp:html -->
