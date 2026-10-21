<?php
/**
 * Title: الواجهة الرئيسية
 * Slug: nuzul/hero
 * Categories: nuzul, banner
 */
?>
<!-- wp:group {"className":"nuzul-hero","layout":{"type":"constrained"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|70"}}}} -->
<div class="wp-block-group nuzul-hero" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--70)">
<!-- wp:columns {"verticalAlignment":"center","align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|70"}}}} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center">
<!-- wp:column {"verticalAlignment":"center","width":"52%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:52%">
<!-- wp:paragraph {"className":"nuzul-eyebrow"} -->
<p class="nuzul-eyebrow">ROOM SERVICE · خدمة الغرف</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":1,"className":"nuzul-hero-title"} -->
<h1 class="wp-block-heading nuzul-hero-title">رفاهية الفندق،<br>ببيتك.</h1>
<!-- /wp:heading -->
<!-- wp:paragraph {"fontSize":"lg","textColor":"muted"} -->
<p class="has-muted-color has-text-color has-lg-font-size">مناشف ثقيلة، أرواب قطن، شباشب، وأطقم عناية بنفس الجودة اللي بتلاقيها بغرف الفنادق. بتوصلك لباب البيت وبتدفع عند الاستلام.</p>
<!-- /wp:paragraph -->
<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--50)">
<!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( nuzul_link( 'shop' ) ); ?>">تسوّق المنتجات</a></div>
<!-- /wp:button -->
<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( nuzul_link( 'cat:gift-boxes' ) ); ?>">صناديق الهدايا</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
</div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center">
<!-- wp:html -->
<div class="nuzul-hero-art" aria-hidden="true">
	<img class="nuzul-hero-robe" src="<?php echo nuzul_img( 'products/robe-waffle.png' ); ?>" alt="" width="800" height="800">
	<div class="nuzul-hanger">
		<span class="nuzul-hanger-hole"></span>
		<span class="nuzul-hanger-ar">لا تزعجني</span>
		<span class="nuzul-hanger-en">PLEASE DO<br>NOT DISTURB</span>
	</div>
	<div class="nuzul-roomno"><span>ROOM</span>214</div>
</div>
<!-- /wp:html -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->
