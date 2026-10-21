<?php
/**
 * Title: طريقة الطلب
 * Slug: nuzul/how-to-order
 * Categories: nuzul
 */
$steps = array(
	array( 'اختار منتجاتك', 'حط اللي بدك إياه بالسلة. بتقدر تطلب حبة وحدة أو صندوق كامل.' ),
	array( 'اكتب عنوانك', 'الاسم، رقم التلفون، المحافظة والعنوان. بدون حساب وبدون بطاقة.' ),
	array( 'بنأكد عالواتساب', 'بنبعتلك رسالة نأكد فيها الطلب وموعد التوصيل.' ),
	array( 'استلم وادفع', 'المندوب بيوصلك الطلب وبتدفع كاش.' ),
);
?>
<!-- wp:group {"className":"nuzul-how","layout":{"type":"constrained"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|60"}}}} -->
<div class="wp-block-group nuzul-how" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--60)">
<!-- wp:group {"align":"wide","layout":{"type":"default"},"style":{"spacing":{"blockGap":"0.25rem"}}} -->
<div class="wp-block-group alignwide">
<!-- wp:paragraph {"className":"nuzul-eyebrow"} -->
<p class="nuzul-eyebrow">CHECK-IN · طريقة الطلب</p>
<!-- /wp:paragraph -->
<!-- wp:heading -->
<h2 class="wp-block-heading">أربع خطوات وبيوصلك</h2>
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
