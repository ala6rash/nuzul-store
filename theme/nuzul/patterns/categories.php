<?php
/**
 * Title: التصنيفات
 * Slug: nuzul/categories
 * Categories: nuzul
 */
$cats = function_exists( 'nuzul_catalog_categories' ) ? nuzul_catalog_categories() : array();
$n    = 100;
?>
<!-- wp:group {"className":"nuzul-cats","layout":{"type":"constrained"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|60"}}}} -->
<div class="wp-block-group nuzul-cats" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--60)">
<!-- wp:group {"align":"wide","layout":{"type":"flex","justifyContent":"space-between","verticalAlignment":"bottom"}} -->
<div class="wp-block-group alignwide">
<!-- wp:group {"layout":{"type":"default"},"style":{"spacing":{"blockGap":"0.25rem"}}} -->
<div class="wp-block-group">
<!-- wp:paragraph {"className":"nuzul-eyebrow"} -->
<p class="nuzul-eyebrow">FLOORS · الأقسام</p>
<!-- /wp:paragraph -->
<!-- wp:heading -->
<h2 class="wp-block-heading">اختار من وين تبلّش</h2>
<!-- /wp:heading -->
</div>
<!-- /wp:group -->
<!-- wp:paragraph -->
<p><a href="<?php echo esc_url( nuzul_link( 'shop' ) ); ?>">كل المنتجات ←</a></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"align":"wide","className":"nuzul-cat-grid","layout":{"type":"grid","minimumColumnWidth":"11rem"}} -->
<div class="wp-block-group alignwide nuzul-cat-grid">
<?php foreach ( $cats as $slug => $c ) : $n++; ?>
<!-- wp:html -->
<a class="nuzul-cat" href="<?php echo esc_url( nuzul_link( 'cat:' . $slug ) ); ?>">
	<span class="nuzul-cat-no">No. <?php echo (int) $n; ?></span>
	<img src="<?php echo nuzul_img( 'products/' . $c['image'] . '.png' ); ?>" alt="" loading="lazy" width="800" height="800">
	<span class="nuzul-cat-name"><?php echo esc_html( $c['name'] ); ?></span>
</a>
<!-- /wp:html -->
<?php endforeach; ?>
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
