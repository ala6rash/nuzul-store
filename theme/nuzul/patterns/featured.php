<?php
/**
 * Title: منتجات مختارة
 * Slug: nuzul/featured
 * Categories: nuzul, woo-commerce
 */
?>
<!-- wp:group {"className":"nuzul-featured","layout":{"type":"constrained"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|70"}}}} -->
<div class="wp-block-group nuzul-featured" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--70)">
<!-- wp:group {"align":"wide","layout":{"type":"default"},"style":{"spacing":{"blockGap":"0.25rem"}}} -->
<div class="wp-block-group alignwide">
<!-- wp:paragraph {"className":"nuzul-eyebrow"} -->
<p class="nuzul-eyebrow">MOST REQUESTED · مختارات</p>
<!-- /wp:paragraph -->
<!-- wp:heading -->
<h2 class="wp-block-heading">الأكثر طلباً</h2>
<!-- /wp:heading -->
</div>
<!-- /wp:group -->
<!-- wp:html -->
<div class="alignwide"><?php echo do_shortcode( '[products limit="8" columns="4" visibility="featured" orderby="date" order="ASC" class="nuzul-products"]' ); ?></div>
<!-- /wp:html -->
</div>
<!-- /wp:group -->
