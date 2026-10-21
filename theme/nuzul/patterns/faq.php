<?php
/**
 * Title: أسئلة متكررة
 * Slug: nuzul/faq
 * Categories: nuzul, text
 */
$faq = array(
	array( 'هل المنتجات نفسها اللي بالفنادق؟', 'بنجيبها من مورّدين بيتعاملوا مع فنادق بالأردن والمنطقة، بنفس المواصفات: قطن 600 غرام للمناشف، قطن 100% للأرواب، وعبوات بحجم الفنادق.' ),
	array( 'كم بياخد التوصيل؟', 'داخل عمّان خلال 24 – 48 ساعة، وباقي المحافظات خلال 2 – 3 أيام عمل. بنتواصل معك عالواتساب قبل ما نطلع.' ),
	array( 'كيف بدفع؟', 'كاش عند الاستلام. ما بنطلب بطاقة ولا تحويل مسبق.' ),
	array( 'بقدر أبدّل؟', 'آه، خلال 7 أيام إذا المنتج بتغليفه وما انفتح. منتجات العناية الشخصية ما بتنرجع بعد فتحها.' ),
	array( 'بتعملوا طلبات كبيرة لشركات أو أعراس؟', 'آه. ابعتلنا عالواتساب الكمية والمنتجات وبنرجعلك بسعر خاص، وبنقدر نطبع اسم أو لوجو على الكروت والعلب.' ),
);
?>
<!-- wp:group {"className":"nuzul-faq","layout":{"type":"constrained","contentSize":"820px"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|70"}}}} -->
<div class="wp-block-group nuzul-faq" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--70)">
<!-- wp:paragraph {"className":"nuzul-eyebrow"} -->
<p class="nuzul-eyebrow">CONCIERGE · أسئلة متكررة</p>
<!-- /wp:paragraph -->
<!-- wp:heading -->
<h2 class="wp-block-heading">قبل ما تسأل</h2>
<!-- /wp:heading -->
<?php foreach ( $faq as $q ) : ?>
<!-- wp:details -->
<details class="wp-block-details"><summary><?php echo esc_html( $q[0] ); ?></summary><!-- wp:paragraph {"textColor":"muted"} -->
<p class="has-muted-color has-text-color"><?php echo esc_html( $q[1] ); ?></p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->
<?php endforeach; ?>
<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
<p style="margin-top:var(--wp--preset--spacing--50)">في سؤال تاني؟ <?php echo do_shortcode( '[nuzul_whatsapp label="اسألنا عالواتساب"]' ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
