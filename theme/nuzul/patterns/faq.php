<?php
/**
 * Title: أسئلة متكررة
 * Slug: nuzul/faq
 * Categories: nuzul, text
 */
$faq = array(
	array( 'ما مواصفات المنتجات؟', 'نختار منتجاتنا بمواصفات غرف الفنادق: مناشف قطنية بوزن 600 غرام/م²، وأرواب من القطن 100%، وعبوات عناية بأحجام الفنادق. تجد المواصفات الكاملة في صفحة كل منتج.' ),
	array( 'كم تستغرق مدة التوصيل؟', 'داخل عمّان خلال 24 – 48 ساعة، وإلى باقي المحافظات خلال 2 – 3 أيام عمل. نتواصل معك قبل التوصيل لتأكيد الموعد.' ),
	array( 'ما طرق الدفع المتاحة؟', 'الدفع نقداً عند الاستلام.' ),
	array( 'هل يمكنني استبدال المنتج؟', 'نعم، خلال 7 أيام من الاستلام إذا كان المنتج في تغليفه الأصلي ولم يُفتح. لا تُستبدل منتجات العناية الشخصية بعد فتحها لأسباب صحية.' ),
	array( 'هل تقبلون طلبات الشركات والمناسبات؟', 'نعم. تواصل معنا عبر واتساب مع تحديد المنتجات والكميات، وسنرسل لك عرض سعر خاصاً. يمكن إضافة اسم أو شعار على بطاقات الإهداء والعلب.' ),
);
?>
<!-- wp:group {"className":"nuzul-faq","layout":{"type":"constrained","contentSize":"820px"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|70"}}}} -->
<div class="wp-block-group nuzul-faq" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--70)">
<!-- wp:paragraph {"className":"nuzul-eyebrow"} -->
<p class="nuzul-eyebrow">CONCIERGE · الأسئلة الشائعة</p>
<!-- /wp:paragraph -->
<!-- wp:heading -->
<h2 class="wp-block-heading">الأسئلة الشائعة</h2>
<!-- /wp:heading -->
<?php foreach ( $faq as $q ) : ?>
<!-- wp:details -->
<details class="wp-block-details"><summary><?php echo esc_html( $q[0] ); ?></summary><!-- wp:paragraph {"textColor":"muted"} -->
<p class="has-muted-color has-text-color"><?php echo esc_html( $q[1] ); ?></p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->
<?php endforeach; ?>
<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
<p style="margin-top:var(--wp--preset--spacing--50)">لديك سؤال آخر؟ <?php echo do_shortcode( '[nuzul_whatsapp label="تواصل معنا عبر واتساب"]' ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
