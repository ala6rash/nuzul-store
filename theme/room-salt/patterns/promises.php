<?php
/**
 * Title: وعود المتجر
 * Slug: room-salt/promises
 * Categories: room-salt
 */
$items = array(
	array( 'توصيل إلى جميع المحافظات', 'عمّان خلال يومين، وباقي المحافظات خلال 2 – 3 أيام عمل.' ),
	array( 'ادفع كما يناسبك', 'نقداً عند وصول الطلب، أو بتحويل فوري عبر CliQ.' ),
	array( 'مجاني فوق 35 د.أ', 'التوصيل علينا إذا بلغت قيمة طلبك 35 ديناراً أو أكثر.' ),
	array( 'استبدال خلال 7 أيام', 'للمنتجات غير المفتوحة وبتغليفها الأصلي.' ),
);
?>
<!-- wp:group {"className":"rs-promises","backgroundColor":"sand","layout":{"type":"constrained"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}}} -->
<div class="wp-block-group rs-promises has-sand-background-color has-background" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)">
<!-- wp:group {"align":"wide","layout":{"type":"grid","minimumColumnWidth":"14rem"}} -->
<div class="wp-block-group alignwide">
<?php foreach ( $items as $it ) : ?>
<!-- wp:group {"className":"rs-promise","layout":{"type":"default"},"style":{"spacing":{"blockGap":"0.25rem"}}} -->
<div class="wp-block-group rs-promise">
<!-- wp:heading {"level":3,"fontSize":"md"} -->
<h3 class="wp-block-heading has-md-font-size"><?php echo esc_html( $it[0] ); ?></h3>
<!-- /wp:heading -->
<!-- wp:paragraph {"fontSize":"sm","textColor":"muted"} -->
<p class="has-muted-color has-text-color has-sm-font-size"><?php echo esc_html( $it[1] ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
<?php endforeach; ?>
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
