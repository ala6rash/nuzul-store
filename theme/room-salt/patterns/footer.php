<?php
/**
 * Title: التذييل
 * Slug: room-salt/footer
 * Categories: footer
 * Block Types: core/template-part/footer
 * Inserter: no
 */
?>
<!-- wp:group {"className":"rs-footer","backgroundColor":"ink","textColor":"sand","layout":{"type":"constrained"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|50"},"margin":{"top":"0"}},"elements":{"link":{"color":{"text":"var:preset|color|salt"}}}},"fontSize":"sm"} -->
<div class="wp-block-group rs-footer has-sand-color has-ink-background-color has-text-color has-background has-link-color has-sm-font-size" style="margin-top:0;padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--50)">
<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|60"}}}} -->
<div class="wp-block-columns alignwide">
<!-- wp:column {"width":"38%"} -->
<div class="wp-block-column" style="flex-basis:38%">
<!-- wp:image {"width":"200px","sizeSlug":"full"} -->
<figure class="wp-block-image size-full is-resized"><img src="<?php echo rs_img( 'img/logo-horizontal-light.svg' ); ?>" alt="Room &amp; Salt" style="width:200px"/></figure>
<!-- /wp:image -->
<!-- wp:paragraph {"style":{"typography":{"lineHeight":"1.9"}}} -->
<p style="line-height:1.9">مستلزمات غرف الفنادق ومنتجات البحر الميت، من الأردن إلى بيتك. توصيل إلى جميع المحافظات والدفع عند الاستلام.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:heading {"level":3,"className":"rs-foot-h","fontSize":"sm"} -->
<h3 class="wp-block-heading rs-foot-h has-sm-font-size">تسوّق</h3>
<!-- /wp:heading -->
<!-- wp:list {"className":"rs-foot-list"} -->
<ul class="rs-foot-list">
<?php foreach ( rs_catalog_top_categories() as $slug => $c ) : ?>
<li><a href="<?php echo esc_url( rs_link( 'cat:' . $slug ) ); ?>"><?php echo esc_html( $c['name'] ); ?></a></li>
<?php endforeach; ?>
</ul>
<!-- /wp:list -->
</div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:heading {"level":3,"className":"rs-foot-h","fontSize":"sm"} -->
<h3 class="wp-block-heading rs-foot-h has-sm-font-size">مساعدة</h3>
<!-- /wp:heading -->
<!-- wp:list {"className":"rs-foot-list"} -->
<ul class="rs-foot-list">
<li><a href="<?php echo esc_url( rs_link( 'delivery-returns' ) ); ?>">التوصيل والاستبدال</a></li>
<li><a href="<?php echo esc_url( rs_link( 'about' ) ); ?>">من نحن</a></li>
<li><a href="<?php echo esc_url( rs_link( 'contact' ) ); ?>">تواصل معنا</a></li>
<li><a href="<?php echo esc_url( rs_link( 'terms' ) ); ?>">الشروط والأحكام</a></li>
<li><a href="<?php echo esc_url( rs_link( 'privacy' ) ); ?>">سياسة الخصوصية</a></li>
</ul>
<!-- /wp:list -->
</div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:heading {"level":3,"className":"rs-foot-h","fontSize":"sm"} -->
<h3 class="wp-block-heading rs-foot-h has-sm-font-size">تواصل</h3>
<!-- /wp:heading -->
<!-- wp:html -->
<?php echo do_shortcode( '[rs_contact]' ); ?>
<!-- /wp:html -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->

<!-- wp:separator {"align":"wide","className":"is-style-wide","style":{"color":{"background":"#2A3538"}}} -->
<hr class="wp-block-separator alignwide has-text-color has-alpha-channel-opacity has-background is-style-wide" style="background-color:#2A3538;color:#2A3538"/>
<!-- /wp:separator -->

<!-- wp:group {"align":"wide","layout":{"type":"flex","justifyContent":"space-between"},"fontSize":"xs"} -->
<div class="wp-block-group alignwide has-xs-font-size">
<!-- wp:paragraph -->
<p>© <?php echo esc_html( wp_date( 'Y' ) ); ?> Room &amp; Salt. جميع الحقوق محفوظة.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph -->
<p>الدفع عند الاستلام · تحويل فوري عبر CliQ</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
