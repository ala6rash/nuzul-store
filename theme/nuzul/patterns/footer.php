<?php
/**
 * Title: الفوتر
 * Slug: nuzul/footer
 * Categories: footer
 * Block Types: core/template-part/footer
 * Inserter: no
 */
?>
<!-- wp:group {"className":"nuzul-footer","backgroundColor":"ink","textColor":"porcelain","layout":{"type":"constrained"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|50"},"margin":{"top":"0"}},"elements":{"link":{"color":{"text":"var:preset|color|linen"}}}},"fontSize":"sm"} -->
<div class="wp-block-group nuzul-footer has-porcelain-color has-ink-background-color has-text-color has-background has-link-color has-sm-font-size" style="margin-top:0;padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--50)">
<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|60"}}}} -->
<div class="wp-block-columns alignwide">
<!-- wp:column {"width":"40%"} -->
<div class="wp-block-column" style="flex-basis:40%">
<!-- wp:image {"width":"150px","sizeSlug":"full"} -->
<figure class="wp-block-image size-full is-resized"><img src="<?php echo nuzul_img( 'img/logo-horizontal-light.svg' ); ?>" alt="نُزُل" style="width:150px"/></figure>
<!-- /wp:image -->
<!-- wp:paragraph {"style":{"typography":{"lineHeight":"1.9"}}} -->
<p style="line-height:1.9">مستلزمات غرف الفنادق لمنزلك: مناشف، وأرواب حمّام، وشباشب، وأطقم عناية، ومعطرات. من عمّان إلى جميع محافظات المملكة.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:heading {"level":3,"className":"nuzul-foot-h","fontSize":"sm"} -->
<h3 class="wp-block-heading nuzul-foot-h has-sm-font-size">المتجر</h3>
<!-- /wp:heading -->
<!-- wp:list {"className":"nuzul-foot-list"} -->
<ul class="nuzul-foot-list">
<li><a href="<?php echo esc_url( nuzul_link( 'cat:toiletries' ) ); ?>">أطقم العناية</a></li>
<li><a href="<?php echo esc_url( nuzul_link( 'cat:robes-slippers' ) ); ?>">أرواب وشباشب</a></li>
<li><a href="<?php echo esc_url( nuzul_link( 'cat:towels' ) ); ?>">مناشف</a></li>
<li><a href="<?php echo esc_url( nuzul_link( 'cat:gift-boxes' ) ); ?>">صناديق الهدايا</a></li>
</ul>
<!-- /wp:list -->
</div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:heading {"level":3,"className":"nuzul-foot-h","fontSize":"sm"} -->
<h3 class="wp-block-heading nuzul-foot-h has-sm-font-size">معلومات</h3>
<!-- /wp:heading -->
<!-- wp:list {"className":"nuzul-foot-list"} -->
<ul class="nuzul-foot-list">
<li><a href="<?php echo esc_url( nuzul_link( 'delivery-returns' ) ); ?>">التوصيل والاستبدال</a></li>
<li><a href="<?php echo esc_url( nuzul_link( 'about' ) ); ?>">من نحن</a></li>
<li><a href="<?php echo esc_url( nuzul_link( 'contact' ) ); ?>">تواصل معنا</a></li>
</ul>
<!-- /wp:list -->
</div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:heading {"level":3,"className":"nuzul-foot-h","fontSize":"sm"} -->
<h3 class="wp-block-heading nuzul-foot-h has-sm-font-size">تواصل</h3>
<!-- /wp:heading -->
<!-- wp:html -->
<?php echo do_shortcode( '[nuzul_contact]' ); ?>
<!-- /wp:html -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->

<!-- wp:separator {"align":"wide","className":"is-style-wide","style":{"color":{"background":"#2b3b38"}}} -->
<hr class="wp-block-separator alignwide has-text-color has-alpha-channel-opacity has-background is-style-wide" style="background-color:#2b3b38;color:#2b3b38"/>
<!-- /wp:separator -->

<!-- wp:group {"align":"wide","layout":{"type":"flex","justifyContent":"space-between"},"fontSize":"xs"} -->
<div class="wp-block-group alignwide has-xs-font-size">
<!-- wp:paragraph -->
<p>© <?php echo esc_html( wp_date( 'Y' ) ); ?> نُزُل. جميع الحقوق محفوظة.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"className":"nuzul-pay-note"} -->
<p class="nuzul-pay-note">الدفع نقداً عند الاستلام</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
