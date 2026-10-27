<?php
/**
 * Title: الأقسام
 * Slug: room-salt/categories
 * Categories: room-salt
 */
$cats = function_exists( 'rs_catalog_top_categories' ) ? rs_catalog_top_categories() : array();
?>
<!-- wp:group {"className":"rs-cats","layout":{"type":"constrained"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|60"}}}} -->
<div class="wp-block-group rs-cats" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--60)">
<!-- wp:group {"align":"wide","layout":{"type":"flex","justifyContent":"space-between","verticalAlignment":"bottom"}} -->
<div class="wp-block-group alignwide">
<!-- wp:group {"layout":{"type":"default"},"style":{"spacing":{"blockGap":"0.25rem"}}} -->
<div class="wp-block-group">
<!-- wp:paragraph {"className":"rs-eyebrow"} -->
<p class="rs-eyebrow"><span lang="en" dir="ltr">COLLECTIONS</span></p>
<!-- /wp:paragraph -->
<!-- wp:heading -->
<h2 class="wp-block-heading">تسوّق حسب القسم</h2>
<!-- /wp:heading -->
</div>
<!-- /wp:group -->
<!-- wp:paragraph -->
<p><a href="<?php echo esc_url( rs_link( 'shop' ) ); ?>">جميع المنتجات ←</a></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"align":"wide","className":"rs-cat-grid","layout":{"type":"grid","minimumColumnWidth":"11rem"}} -->
<div class="wp-block-group alignwide rs-cat-grid">
<?php foreach ( $cats as $slug => $c ) : ?>
<!-- wp:html -->
<a class="rs-cat" href="<?php echo esc_url( rs_link( 'cat:' . $slug ) ); ?>">
	<img src="<?php echo rs_img( 'art/cat-' . $slug . '.jpg' ); ?>" alt="" loading="lazy" width="900" height="900">
	<span class="rs-cat-name"><?php echo esc_html( $c['name'] ); ?></span>
	<span class="rs-cat-en" lang="en" dir="ltr"><?php echo esc_html( $c['en'] ); ?></span>
</a>
<!-- /wp:html -->
<?php endforeach; ?>
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
