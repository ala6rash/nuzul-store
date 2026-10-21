<?php
/**
 * Title: وعود المتجر
 * Slug: nuzul/promises
 * Categories: nuzul
 */
$items = array(
	array( 'توصيل لكل المملكة', 'عمّان خلال يومين، والمحافظات خلال 2 – 3 أيام.' ),
	array( 'الدفع عند الاستلام', 'ما في بطاقة ولا تحويل. بتدفع كاش لما يوصلك.' ),
	array( 'مجاني فوق 35 د.أ', 'التوصيل علينا إذا طلبك 35 دينار أو أكثر.' ),
	array( 'تبديل خلال 7 أيام', 'إذا المنتج مش مفتوح، بنبدّله بدون أسئلة.' ),
);
?>
<!-- wp:group {"className":"nuzul-promises","backgroundColor":"porcelain","layout":{"type":"constrained"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}}} -->
<div class="wp-block-group nuzul-promises has-porcelain-background-color has-background" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)">
<!-- wp:group {"align":"wide","className":"nuzul-promise-grid","layout":{"type":"grid","minimumColumnWidth":"14rem"}} -->
<div class="wp-block-group alignwide nuzul-promise-grid">
<?php foreach ( $items as $it ) : ?>
<!-- wp:group {"className":"nuzul-promise","layout":{"type":"default"},"style":{"spacing":{"blockGap":"0.25rem"}}} -->
<div class="wp-block-group nuzul-promise">
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
