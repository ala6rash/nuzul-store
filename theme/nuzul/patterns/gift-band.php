<?php
/**
 * Title: شريط صندوق الهدية
 * Slug: nuzul/gift-band
 * Categories: nuzul, call-to-action
 */
?>
<!-- wp:group {"className":"nuzul-gift","backgroundColor":"eucalyptus","textColor":"linen","layout":{"type":"constrained"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}},"elements":{"heading":{"color":{"text":"var:preset|color|linen"}}}}} -->
<div class="wp-block-group nuzul-gift has-linen-color has-eucalyptus-background-color has-text-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)">
<!-- wp:columns {"verticalAlignment":"center","align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|70"}}}} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center">
<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center">
<!-- wp:image {"sizeSlug":"full","className":"nuzul-gift-img"} -->
<figure class="wp-block-image size-full nuzul-gift-img"><img src="<?php echo nuzul_img( 'products/box-night.png' ); ?>" alt="صندوق ليلة بالفندق" width="800" height="800"/></figure>
<!-- /wp:image -->
</div>
<!-- /wp:column -->
<!-- wp:column {"verticalAlignment":"center","width":"55%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:55%">
<!-- wp:paragraph {"className":"nuzul-eyebrow is-light"} -->
<p class="nuzul-eyebrow is-light">LATE CHECKOUT · هدية جاهزة</p>
<!-- /wp:paragraph -->
<!-- wp:heading -->
<h2 class="wp-block-heading">صندوق «ليلة بالفندق»</h2>
<!-- /wp:heading -->
<!-- wp:paragraph {"fontSize":"lg"} -->
<p class="has-lg-font-size">روب حمّام قطني، وشبشب، وطقم عناية، ومنشفة حمّام، في علبة هدايا أنيقة مع بطاقة إهداء مكتوبة بخط اليد. هدية مناسبة لأعياد الميلاد والتخرّج والمناسبات الخاصة.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"className":"nuzul-price-tag"} -->
<p class="nuzul-price-tag"><?php echo esc_html( nuzul_sku_price( 'NZ-GB-01', '39' ) ); ?> د.أ <span>شامل التوصيل المجاني إلى جميع المحافظات</span></p>
<!-- /wp:paragraph -->
<!-- wp:buttons -->
<div class="wp-block-buttons">
<!-- wp:button {"backgroundColor":"linen","textColor":"eucalyptus"} -->
<div class="wp-block-button"><a class="wp-block-button__link has-eucalyptus-color has-linen-background-color has-text-color has-background wp-element-button" href="<?php echo esc_url( nuzul_link( 'cat:gift-boxes' ) ); ?>">تصفّح الصناديق</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->
