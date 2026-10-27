<?php
/**
 * Title: أسئلة متكررة
 * Slug: room-salt/faq
 * Categories: room-salt, text
 */
$faq = array(
	array( 'هل المنتجات هي نفسها المعروضة في المعرض؟', 'نعم. الأسماء والمقاسات والأسعار في الموقع منقولة من بطاقات المعرض. إذا أردت صوراً إضافية لأي منتج، اطلبها عبر واتساب.' ),
	array( 'كم يستغرق التوصيل؟', 'داخل عمّان خلال 24 – 48 ساعة، وباقي المحافظات خلال 2 – 3 أيام عمل. نتواصل معك عبر واتساب قبل الخروج للتوصيل.' ),
	array( 'كيف أدفع؟', 'نقداً عند الاستلام، أو بتحويل فوري عبر CliQ من تطبيق البنك أو المحفظة. الدفع بالبطاقة قريباً.' ),
	array( 'لماذا يظهر «السعر عند الطلب» على بعض المنتجات؟', 'لأن سعرها يختلف حسب المقاس أو الكمية. راسلنا عبر واتساب ونرسل لك السعر مباشرة.' ),
	array( 'هل يمكنني الاستبدال؟', 'نعم، خلال 7 أيام إذا كان المنتج بتغليفه الأصلي ولم يُفتح. منتجات العناية الشخصية لا تُسترجع بعد فتحها.' ),
);
?>
<!-- wp:group {"className":"rs-faq","layout":{"type":"constrained","contentSize":"820px"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|70"}}}} -->
<div class="wp-block-group rs-faq" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--70)">
<!-- wp:paragraph {"className":"rs-eyebrow"} -->
<p class="rs-eyebrow"><span lang="en" dir="ltr">CONCIERGE</span></p>
<!-- /wp:paragraph -->
<!-- wp:heading -->
<h2 class="wp-block-heading">أسئلة متكررة</h2>
<!-- /wp:heading -->
<?php foreach ( $faq as $q ) : ?>
<!-- wp:details -->
<details class="wp-block-details"><summary><?php echo esc_html( $q[0] ); ?></summary><!-- wp:paragraph {"textColor":"muted"} -->
<p class="has-muted-color has-text-color"><?php echo esc_html( $q[1] ); ?></p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->
<?php endforeach; ?>
<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
<p style="margin-top:var(--wp--preset--spacing--50)">لديك سؤال آخر؟ <?php echo do_shortcode( '[rs_whatsapp label="اسألنا عبر واتساب"]' ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
