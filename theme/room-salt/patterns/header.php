<?php
/**
 * Title: الترويسة
 * Slug: room-salt/header
 * Categories: header
 * Block Types: core/template-part/header
 * Inserter: no
 */
?>
<!-- wp:group {"className":"rs-announce","backgroundColor":"deep","textColor":"salt","layout":{"type":"constrained"},"style":{"spacing":{"padding":{"top":"0.5rem","bottom":"0.5rem"}}},"fontSize":"xs"} -->
<div class="wp-block-group rs-announce has-salt-color has-deep-background-color has-text-color has-background has-xs-font-size" style="padding-top:0.5rem;padding-bottom:0.5rem">
<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">توصيل إلى جميع المحافظات <span class="rs-dot">◆</span> الدفع عند الاستلام<span class="rs-hide-sm"> <span class="rs-dot">◆</span> توصيل مجاني للطلبات فوق 35 د.أ</span></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"rs-header","layout":{"type":"constrained"},"style":{"spacing":{"padding":{"top":"0.9rem","bottom":"0.9rem"}}}} -->
<div class="wp-block-group rs-header" style="padding-top:0.9rem;padding-bottom:0.9rem">
<!-- wp:group {"align":"wide","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
<div class="wp-block-group alignwide">
<!-- wp:image {"width":"190px","sizeSlug":"full","linkDestination":"custom","className":"rs-logo"} -->
<figure class="wp-block-image size-full is-resized rs-logo"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo rs_img( 'img/logo-horizontal.svg' ); ?>" alt="Room &amp; Salt" style="width:190px"/></a></figure>
<!-- /wp:image -->

<!-- wp:navigation {"overlayMenu":"mobile","className":"rs-nav","layout":{"type":"flex","justifyContent":"center"},"style":{"spacing":{"blockGap":"1.75rem"}}} -->
<!-- wp:navigation-link {"label":"المتجر","url":"<?php echo esc_url( rs_link( 'shop' ) ); ?>","kind":"custom"} /-->
<!-- wp:navigation-link {"label":"المناشف","url":"<?php echo esc_url( rs_link( 'cat:towels' ) ); ?>","kind":"custom"} /-->
<!-- wp:navigation-link {"label":"المخدات والمفارش","url":"<?php echo esc_url( rs_link( 'cat:bedding' ) ); ?>","kind":"custom"} /-->
<!-- wp:navigation-link {"label":"الفرشات","url":"<?php echo esc_url( rs_link( 'cat:mattresses' ) ); ?>","kind":"custom"} /-->
<!-- wp:navigation-link {"label":"البحر الميت","url":"<?php echo esc_url( rs_link( 'cat:dead-sea' ) ); ?>","kind":"custom"} /-->
<!-- wp:navigation-link {"label":"التوصيل","url":"<?php echo esc_url( rs_link( 'delivery-returns' ) ); ?>","kind":"custom"} /-->
<!-- /wp:navigation -->

<!-- wp:group {"className":"rs-header-tools","layout":{"type":"flex","flexWrap":"nowrap"},"style":{"spacing":{"blockGap":"0.5rem"}}} -->
<div class="wp-block-group rs-header-tools">
<!-- wp:woocommerce/customer-account {"displayStyle":"icon_only","iconStyle":"line","iconClass":"wc-block-customer-account__account-icon"} /-->
<!-- wp:woocommerce/mini-cart {"addToCartBehaviour":"open_drawer"} /-->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:paragraph {"align":"center","className":"rs-strip"} -->
<p class="has-text-align-center rs-strip">توصيل مجاني لكل طلب قيمته 35 ديناراً أو أكثر</p>
<!-- /wp:paragraph -->
