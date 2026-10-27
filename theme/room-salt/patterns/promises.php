<?php
/**
 * Title: وعود المتجر
 * Slug: room-salt/promises
 * Categories: room-salt
 */
$rs_icons = array(
	'truck'  => '<path d="M3 6h11v9H3zM14 9h4l3 3v3h-7"/><circle cx="7" cy="17" r="1.8"/><circle cx="17" cy="17" r="1.8"/>',
	'wallet' => '<rect x="3" y="6" width="18" height="13" rx="2"/><path d="M3 10h18M16 14.5h2"/>',
	'gift'   => '<rect x="4" y="10" width="16" height="10"/><path d="M3 7h18v3H3zM12 7v13M12 7c-1.5-3-5-3-5-1s3 1 5 1c2 0 5 1 5-1s-3.5-2-5 1"/>',
	'swap'   => '<path d="M4 8h13l-3-3M20 16H7l3 3"/>',
);
$items = array(
	array( 'truck', 'توصيل إلى جميع المحافظات', 'عمّان خلال 24 – 48 ساعة، وباقي المحافظات خلال 2 – 3 أيام عمل.' ),
	array( 'wallet', 'الدفع عند الاستلام أو CliQ', 'نقداً عند وصول الطلب، أو بتحويل فوري عبر CliQ.' ),
	array( 'gift', 'توصيل مجاني فوق 35 د.أ', 'التوصيل علينا إذا بلغت قيمة طلبك 35 ديناراً أو أكثر.' ),
	array( 'swap', 'استبدال خلال 7 أيام', 'للمنتجات غير المفتوحة وبتغليفها الأصلي.' ),
);
?>
<!-- wp:html -->
<section class="rs-trust" aria-label="خدمات المتجر">
	<ul class="rs-trust-list">
		<?php foreach ( $items as $it ) : ?>
		<li>
			<svg viewBox="0 0 24 24" aria-hidden="true"><?php echo $rs_icons[ $it[0] ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed SVG markup above. ?></svg>
			<span><strong><?php echo esc_html( $it[1] ); ?></strong><small><?php echo esc_html( $it[2] ); ?></small></span>
		</li>
		<?php endforeach; ?>
	</ul>
</section>
<!-- /wp:html -->
