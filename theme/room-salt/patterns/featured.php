<?php
/**
 * Title: منتجات مختارة
 * Slug: room-salt/featured
 * Categories: room-salt, woo-commerce
 */
?>
<!-- wp:group {"className":"rs-featured","layout":{"type":"constrained"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|70"}}}} -->
<div class="wp-block-group rs-featured" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--70)">
<!-- wp:group {"align":"wide","layout":{"type":"default"},"style":{"spacing":{"blockGap":"0.25rem"}}} -->
<div class="wp-block-group alignwide">
<!-- wp:paragraph {"className":"rs-eyebrow"} -->
<p class="rs-eyebrow"><span lang="en" dir="ltr">FROM THE SHOWROOM</span></p>
<!-- /wp:paragraph -->
<!-- wp:heading -->
<h2 class="wp-block-heading">مختارات من المعرض</h2>
<!-- /wp:heading -->
</div>
<!-- /wp:group -->
<!-- wp:html -->
<div class="alignwide"><?php echo do_shortcode( '[products limit="8" columns="4" visibility="featured" orderby="title" order="ASC" class="rs-products"]' ); ?></div>
<!-- /wp:html -->
</div>
<!-- /wp:group -->
