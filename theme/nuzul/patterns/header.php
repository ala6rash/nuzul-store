<?php
/**
 * Title: الهيدر
 * Slug: nuzul/header
 * Categories: header
 * Block Types: core/template-part/header
 * Inserter: no
 */
?>
<!-- wp:group {"className":"nuzul-announce","backgroundColor":"eucalyptus","textColor":"linen","layout":{"type":"constrained"},"style":{"spacing":{"padding":{"top":"0.5rem","bottom":"0.5rem"}}},"fontSize":"xs"} -->
<div class="wp-block-group nuzul-announce has-linen-color has-eucalyptus-background-color has-text-color has-background has-xs-font-size" style="padding-top:0.5rem;padding-bottom:0.5rem">
<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">التوصيل إلى جميع محافظات المملكة <span class="nuzul-dot">·</span> الدفع عند الاستلام<span class="nuzul-hide-sm"> <span class="nuzul-dot">·</span> توصيل مجاني للطلبات فوق 35 د.أ</span></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"nuzul-header","layout":{"type":"constrained"},"style":{"spacing":{"padding":{"top":"1rem","bottom":"1rem"}}}} -->
<div class="wp-block-group nuzul-header" style="padding-top:1rem;padding-bottom:1rem">
<!-- wp:group {"align":"wide","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
<div class="wp-block-group alignwide">
<!-- wp:image {"width":"138px","sizeSlug":"full","linkDestination":"custom","className":"nuzul-logo"} -->
<figure class="wp-block-image size-full is-resized nuzul-logo"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo nuzul_img( 'img/logo-horizontal.svg' ); ?>" alt="نُزُل" style="width:138px"/></a></figure>
<!-- /wp:image -->

<!-- wp:navigation {"overlayMenu":"mobile","className":"nuzul-nav","layout":{"type":"flex","justifyContent":"center"},"style":{"spacing":{"blockGap":"1.75rem"}}} -->
<!-- wp:navigation-link {"label":"المتجر","url":"<?php echo esc_url( nuzul_link( 'shop' ) ); ?>","kind":"custom"} /-->
<!-- wp:navigation-link {"label":"أرواب وشباشب","url":"<?php echo esc_url( nuzul_link( 'cat:robes-slippers' ) ); ?>","kind":"custom"} /-->
<!-- wp:navigation-link {"label":"مناشف","url":"<?php echo esc_url( nuzul_link( 'cat:towels' ) ); ?>","kind":"custom"} /-->
<!-- wp:navigation-link {"label":"صناديق هدايا","url":"<?php echo esc_url( nuzul_link( 'cat:gift-boxes' ) ); ?>","kind":"custom"} /-->
<!-- wp:navigation-link {"label":"التوصيل","url":"<?php echo esc_url( nuzul_link( 'delivery-returns' ) ); ?>","kind":"custom"} /-->
<!-- /wp:navigation -->

<!-- wp:group {"className":"nuzul-header-tools","layout":{"type":"flex","flexWrap":"nowrap"},"style":{"spacing":{"blockGap":"0.5rem"}}} -->
<div class="wp-block-group nuzul-header-tools">
<!-- wp:woocommerce/customer-account {"displayStyle":"icon_only","iconStyle":"line","iconClass":"wc-block-customer-account__account-icon"} /-->
<!-- wp:woocommerce/mini-cart {"addToCartBehaviour":"open_drawer"} /-->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
