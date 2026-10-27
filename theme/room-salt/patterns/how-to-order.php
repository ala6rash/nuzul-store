<?php
/**
 * Title: طريقة الطلب
 * Slug: room-salt/how-to-order
 * Categories: room-salt
 */
$steps = array(
	array( 'اختر منتجاتك', 'أضف ما تريد إلى السلة، قطعة واحدة أو أكثر.' ),
	array( 'أدخل عنوانك واختر طريقة الدفع', 'نقداً عند الاستلام أو تحويل فوري عبر CliQ، دون الحاجة إلى إنشاء حساب.' ),
	array( 'نؤكد عبر واتساب', 'نرسل لك رسالة لتأكيد الطلب وموعد التوصيل.' ),
	array( 'استلم طلبك', 'يصلك الطلب إلى بابك في الموعد المتفق عليه.' ),
);
?>
<!-- wp:group {"className":"rs-how","layout":{"type":"constrained"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|60"}}}} -->
<div class="wp-block-group rs-how" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--60)">
<!-- wp:group {"align":"wide","layout":{"type":"default"},"style":{"spacing":{"blockGap":"0.25rem"}}} -->
<div class="wp-block-group alignwide">
<!-- wp:paragraph {"className":"rs-eyebrow"} -->
<p class="rs-eyebrow"><span lang="en" dir="ltr">CHECK-IN</span></p>
<!-- /wp:paragraph -->
<!-- wp:heading -->
<h2 class="wp-block-heading">أربع خطوات حتى باب بيتك</h2>
<!-- /wp:heading -->
</div>
<!-- /wp:group -->
<!-- wp:group {"align":"wide","className":"rs-steps","layout":{"type":"grid","minimumColumnWidth":"13rem"}} -->
<div class="wp-block-group alignwide rs-steps">
<?php foreach ( $steps as $i => $s ) : ?>
<!-- wp:group {"className":"rs-step","layout":{"type":"default"},"style":{"spacing":{"blockGap":"0.4rem"}}} -->
<div class="wp-block-group rs-step">
<!-- wp:paragraph {"className":"rs-step-no"} -->
<p class="rs-step-no"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":3,"fontSize":"md"} -->
<h3 class="wp-block-heading has-md-font-size"><?php echo esc_html( $s[0] ); ?></h3>
<!-- /wp:heading -->
<!-- wp:paragraph {"fontSize":"sm","textColor":"muted"} -->
<p class="has-muted-color has-text-color has-sm-font-size"><?php echo esc_html( $s[1] ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
<?php endforeach; ?>
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
