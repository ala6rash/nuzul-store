<?php
/**
 * Title: طريقة الطلب
 * Slug: nuzul/how-to-order
 * Categories: nuzul
 */
$steps = array(
	array( 'اختر منتجاتك', 'أضف المنتجات إلى سلة التسوق، قطعة واحدة أو صندوقاً كاملاً.' ),
	array( 'أدخل بيانات التوصيل', 'الاسم ورقم الهاتف والمحافظة والعنوان، دون الحاجة إلى إنشاء حساب.' ),
	array( 'تأكيد الطلب', 'نتواصل معك عبر واتساب لتأكيد الطلب وتحديد موعد التوصيل.' ),
	array( 'الاستلام والدفع', 'يصلك الطلب إلى العنوان المحدد، وتدفع نقداً عند الاستلام.' ),
);
?>
<!-- wp:group {"className":"nuzul-how","layout":{"type":"constrained"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|60"}}}} -->
<div class="wp-block-group nuzul-how" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--60)">
<!-- wp:group {"align":"wide","layout":{"type":"default"},"style":{"spacing":{"blockGap":"0.25rem"}}} -->
<div class="wp-block-group alignwide">
<!-- wp:paragraph {"className":"nuzul-eyebrow"} -->
<p class="nuzul-eyebrow">CHECK-IN · خطوات الطلب</p>
<!-- /wp:paragraph -->
<!-- wp:heading -->
<h2 class="wp-block-heading">كيف تطلب؟</h2>
<!-- /wp:heading -->
</div>
<!-- /wp:group -->
<!-- wp:group {"align":"wide","className":"nuzul-steps","layout":{"type":"grid","minimumColumnWidth":"13rem"}} -->
<div class="wp-block-group alignwide nuzul-steps">
<?php foreach ( $steps as $i => $s ) : ?>
<!-- wp:group {"className":"nuzul-step","layout":{"type":"default"},"style":{"spacing":{"blockGap":"0.4rem"}}} -->
<div class="wp-block-group nuzul-step">
<!-- wp:paragraph {"className":"nuzul-step-no"} -->
<p class="nuzul-step-no"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></p>
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
