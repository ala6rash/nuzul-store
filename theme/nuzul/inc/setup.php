<?php
/**
 * One-click store setup: Appearance → إعداد متجر نُزُل.
 * Configures WooCommerce for Jordan (JOD, cash on delivery, shipping by
 * governorate), creates categories, starter products and info pages.
 * Safe to run more than once: existing items are skipped.
 *
 * @package nuzul
 */

defined( 'ABSPATH' ) || exit;

add_action( 'admin_menu', function () {
	add_theme_page( 'إعداد متجر نُزُل', 'إعداد متجر نُزُل', 'manage_options', 'nuzul-setup', 'nuzul_setup_page' );
} );

add_action( 'after_switch_theme', function () {
	set_transient( 'nuzul_show_setup_notice', 1, DAY_IN_SECONDS );
} );

add_action( 'admin_notices', function () {
	if ( ! get_transient( 'nuzul_show_setup_notice' ) || get_option( 'nuzul_setup_done' ) ) {
		return;
	}
	printf( '<div class="notice notice-info"><p>ثيم نُزُل مفعّل. <a href="%s">كمّل إعداد المتجر بكبسة وحدة</a>.</p></div>', esc_url( admin_url( 'themes.php?page=nuzul-setup' ) ) );
} );

function nuzul_setup_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$log = array();
	if ( isset( $_POST['nuzul_run_setup'] ) && check_admin_referer( 'nuzul_setup' ) ) {
		$log = nuzul_run_setup( array(
			'whatsapp'  => sanitize_text_field( wp_unslash( $_POST['whatsapp'] ?? '' ) ),
			'instagram' => sanitize_text_field( wp_unslash( $_POST['instagram'] ?? '' ) ),
			'email'     => sanitize_email( wp_unslash( $_POST['email'] ?? '' ) ),
			'products'  => ! empty( $_POST['products'] ),
		) );
	}
	$wc = class_exists( 'WooCommerce' );
	?>
	<div class="wrap" dir="rtl">
		<h1>إعداد متجر نُزُل</h1>
		<?php if ( ! $wc ) : ?>
			<div class="notice notice-error"><p>لازم تنزّل وتفعّل إضافة <strong>WooCommerce</strong> أول (الإضافات ← أضف جديد).</p></div>
		<?php endif; ?>
		<?php if ( $log ) : ?>
			<div class="notice notice-success"><p><strong>خلص الإعداد:</strong></p><ul style="list-style:disc;padding-inline-start:20px">
				<?php foreach ( $log as $line ) : ?><li><?php echo esc_html( $line ); ?></li><?php endforeach; ?>
			</ul><p><a class="button button-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank">شوف الموقع</a></p></div>
		<?php endif; ?>
		<p>هالخطوة بتعمل:</p>
		<ol>
			<li>العملة دينار أردني، البلد الأردن، والبيع داخل الأردن فقط.</li>
			<li>تفعيل <strong>الدفع عند الاستلام</strong>.</li>
			<li>مناطق شحن: عمّان 2 د.أ، باقي المحافظات 3 د.أ، ومجاني للطلبات فوق 35 د.أ.</li>
			<li>تصنيفات المنتجات و19 منتج بأسعار مقترحة وصور مؤقتة (اختياري).</li>
			<li>صفحات: التوصيل والاستبدال، من نحن، تواصل معنا.</li>
			<li>روابط الموقع بصيغة مقروءة (/shop/ بدل ?p=12).</li>
		</ol>
		<form method="post">
			<?php wp_nonce_field( 'nuzul_setup' ); ?>
			<table class="form-table" role="presentation">
				<tr><th><label for="whatsapp">رقم واتساب المتجر</label></th>
					<td><input id="whatsapp" name="whatsapp" class="regular-text" dir="ltr" placeholder="962791234567" value="<?php echo esc_attr( get_option( 'nuzul_whatsapp', '' ) ); ?>"><p class="description">بالصيغة الدولية بدون + أو أصفار: 962 وبعدها الرقم بدون الصفر.</p></td></tr>
				<tr><th><label for="instagram">حساب إنستغرام</label></th>
					<td><input id="instagram" name="instagram" class="regular-text" dir="ltr" placeholder="nuzul.jo" value="<?php echo esc_attr( get_option( 'nuzul_instagram', '' ) ); ?>"></td></tr>
				<tr><th><label for="email">إيميل التواصل</label></th>
					<td><input id="email" name="email" class="regular-text" dir="ltr" value="<?php echo esc_attr( get_option( 'nuzul_email', get_option( 'admin_email' ) ) ); ?>"></td></tr>
				<tr><th>المنتجات التجريبية</th>
					<td><label><input type="checkbox" name="products" value="1" checked> أضف المنتجات المقترحة (بتقدر تعدّل أسعارها وصورها بعدين)</label></td></tr>
			</table>
			<?php submit_button( 'جهّز المتجر', 'primary', 'nuzul_run_setup', true, $wc ? array() : array( 'disabled' => 'disabled' ) ); ?>
		</form>
	</div>
	<?php
}

/** Copy a bundled theme image into the media library once; returns attachment ID. */
function nuzul_import_image( $slug, $title ) {
	$existing = get_posts( array( 'post_type' => 'attachment', 'meta_key' => '_nuzul_asset', 'meta_value' => $slug, 'posts_per_page' => 1, 'fields' => 'ids' ) );
	if ( $existing ) {
		return (int) $existing[0];
	}
	$src = get_theme_file_path( "assets/products/{$slug}.png" );
	if ( ! file_exists( $src ) ) {
		return 0;
	}
	$upload = wp_upload_bits( "nuzul-{$slug}.png", null, file_get_contents( $src ) ); // phpcs:ignore
	if ( ! empty( $upload['error'] ) ) {
		return 0;
	}
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$id = wp_insert_attachment( array( 'post_mime_type' => 'image/png', 'post_title' => $title, 'post_status' => 'inherit' ), $upload['file'] );
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );
	update_post_meta( $id, '_wp_attachment_image_alt', $title );
	update_post_meta( $id, '_nuzul_asset', $slug );
	return $id;
}

function nuzul_run_setup( $args ) {
	$log = array();

	foreach ( array( 'whatsapp' => 'nuzul_whatsapp', 'instagram' => 'nuzul_instagram', 'email' => 'nuzul_email' ) as $k => $opt ) {
		if ( '' !== $args[ $k ] ) {
			update_option( $opt, 'whatsapp' === $k ? preg_replace( '/\D+/', '', $args[ $k ] ) : ltrim( $args[ $k ], '@' ) );
		}
	}
	$log[] = 'حفظت بيانات التواصل.';

	if ( in_array( get_option( 'blogdescription' ), array( '', 'Just another WordPress site' ), true ) ) {
		update_option( 'blogdescription', 'رفاهية الفندق، في منزلك' );
	}

	if ( ! get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
		$log[] = 'غيرت صيغة الروابط لـ /اسم-الصفحة/.';
	}

	if ( ! class_exists( 'WooCommerce' ) ) {
		$log[] = 'ووكومرس مش مفعّل، فوقفت هون.';
		return $log;
	}

	// Store basics.
	$opts = array(
		'woocommerce_currency'                    => 'JOD',
		'woocommerce_currency_pos'                => 'right_space',
		'woocommerce_price_num_decimals'          => '2',
		'woocommerce_price_decimal_sep'           => '.',
		'woocommerce_price_thousand_sep'          => ',',
		'woocommerce_default_country'             => 'JO:AM',
		'woocommerce_store_city'                  => 'عمّان',
		'woocommerce_allowed_countries'           => 'specific',
		'woocommerce_specific_allowed_countries'  => array( 'JO' ),
		'woocommerce_ship_to_countries'           => '',
		'woocommerce_default_customer_address'    => 'base',
		'woocommerce_calc_taxes'                  => 'no',
		'woocommerce_enable_guest_checkout'       => 'yes',
		'woocommerce_enable_checkout_login_reminder' => 'yes',
		'woocommerce_enable_signup_and_login_from_checkout' => 'yes',
		'woocommerce_coming_soon'                 => 'no',
		'woocommerce_weight_unit'                 => 'kg',
		'woocommerce_dimension_unit'              => 'cm',
		'woocommerce_onboarding_profile'          => array( 'skipped' => true ),
		'woocommerce_enable_reviews'              => 'no',
		'woocommerce_checkout_phone_field'        => 'required',
		'woocommerce_checkout_company_field'      => 'hidden',
		'woocommerce_checkout_address_2_field'    => 'hidden',
	);
	foreach ( $opts as $k => $v ) {
		update_option( $k, $v );
	}
	$log[] = 'العملة دينار أردني والبيع داخل الأردن.';

	// Cash on delivery.
	$cod = array_merge( (array) get_option( 'woocommerce_cod_settings', array() ), array(
		'enabled'            => 'yes',
		'title'              => 'الدفع عند الاستلام',
		'description'        => 'ادفع نقداً عند استلام طلبك.',
		'instructions'       => 'شكراً لطلبك. سنتواصل معك عبر واتساب أو الهاتف لتأكيد الطلب وتحديد موعد التوصيل.',
		'enable_for_methods' => array(),
		'enable_for_virtual' => 'yes',
	) );
	update_option( 'woocommerce_cod_settings', $cod );
	$log[] = 'فعّلت الدفع عند الاستلام.';

	// Shipping zones by governorate.
	if ( class_exists( 'WC_Shipping_Zones' ) ) {
		$names = wp_list_pluck( WC_Shipping_Zones::get_zones(), 'zone_name' );
		$zones = array(
			'عمّان'          => array( 'states' => array( 'AM' ), 'cost' => '2' ),
			'باقي المحافظات' => array( 'states' => array_diff( array_keys( nuzul_jo_states() ), array( 'AM' ) ), 'cost' => '3' ),
		);
		foreach ( $zones as $name => $z ) {
			if ( in_array( $name, $names, true ) ) {
				continue;
			}
			$zone = new WC_Shipping_Zone();
			$zone->set_zone_name( $name );
			foreach ( $z['states'] as $st ) {
				$zone->add_location( 'JO:' . $st, 'state' );
			}
			$zone->save();
			$flat = $zone->add_shipping_method( 'flat_rate' );
			update_option( "woocommerce_flat_rate_{$flat}_settings", array( 'title' => 'توصيل', 'tax_status' => 'none', 'cost' => $z['cost'] ) );
			$free = $zone->add_shipping_method( 'free_shipping' );
			update_option( "woocommerce_free_shipping_{$free}_settings", array( 'title' => 'توصيل مجاني', 'requires' => 'min_amount', 'min_amount' => '35', 'ignore_discounts' => 'no' ) );
		}
		$log[] = 'مناطق الشحن: عمّان 2 د.أ، باقي المحافظات 3 د.أ، مجاني فوق 35 د.أ.';
	}

	// Categories.
	$cat_ids = array();
	foreach ( nuzul_catalog_categories() as $slug => $c ) {
		$term = get_term_by( 'slug', $slug, 'product_cat' );
		if ( ! $term ) {
			$res = wp_insert_term( $c['name'], 'product_cat', array( 'slug' => $slug, 'description' => $c['desc'] ) );
			if ( is_wp_error( $res ) ) {
				continue;
			}
			$tid = $res['term_id'];
		} else {
			$tid = $term->term_id;
		}
		$cat_ids[ $slug ] = $tid;
		if ( ! get_term_meta( $tid, 'thumbnail_id', true ) ) {
			update_term_meta( $tid, 'thumbnail_id', nuzul_import_image( $c['image'], $c['name'] ) );
		}
	}
	$log[] = 'أنشأت ' . count( $cat_ids ) . ' تصنيفات.';

	// Products.
	if ( $args['products'] ) {
		$made = 0;
		foreach ( nuzul_catalog_products() as $p ) {
			if ( wc_get_product_id_by_sku( $p['sku'] ) ) {
				continue;
			}
			$prod = new WC_Product_Simple();
			$prod->set_name( $p['name'] );
			$prod->set_sku( $p['sku'] );
			$prod->set_slug( $p['img'] ); // short English URLs share better on WhatsApp
			$prod->set_regular_price( $p['price'] );
			$prod->set_short_description( $p['short'] );
			$prod->set_description( wpautop( $p['desc'] ) );
			$prod->set_status( 'publish' );
			$prod->set_featured( ! empty( $p['featured'] ) );
			$prod->set_stock_status( 'instock' );
			if ( isset( $cat_ids[ $p['cat'] ] ) ) {
				$prod->set_category_ids( array( $cat_ids[ $p['cat'] ] ) );
			}
			$prod->set_image_id( nuzul_import_image( $p['img'], $p['name'] ) );
			$prod->save();
			$made++;
		}
		$log[] = "أضفت {$made} منتج (الأسعار مقترحة، عدّلها من المنتجات).";
	}

	// Info pages.
	$pages = array(
		'delivery-returns' => array( 'التوصيل والاستبدال', nuzul_page_delivery() ),
		'about'            => array( 'من نحن', nuzul_page_about() ),
		'contact'          => array( 'تواصل معنا', nuzul_page_contact() ),
	);
	foreach ( $pages as $slug => $pg ) {
		if ( ! get_page_by_path( $slug ) ) {
			wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_name' => $slug, 'post_title' => $pg[0], 'post_content' => $pg[1] ) );
		}
	}
	$log[] = 'أنشأت صفحات التوصيل والاستبدال، من نحن، وتواصل معنا.';

	// Arabic title for the shop page.
	$shop = wc_get_page_id( 'shop' );
	if ( $shop > 0 ) {
		wp_update_post( array( 'ID' => $shop, 'post_title' => 'المتجر' ) );
	}

	flush_rewrite_rules();
	update_option( 'nuzul_setup_done', 1 );
	delete_transient( 'nuzul_show_setup_notice' );
	return $log;
}

function nuzul_page_delivery() {
	return <<<HTML
<!-- wp:paragraph {"fontSize":"lg"} -->
<p class="has-lg-font-size">نوصل الطلبات إلى جميع محافظات المملكة الأردنية الهاشمية، ويتم الدفع نقداً عند الاستلام.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">رسوم ومدة التوصيل</h2>
<!-- /wp:heading -->

<!-- wp:table {"className":"nuzul-table"} -->
<figure class="wp-block-table nuzul-table"><table><thead><tr><th>المنطقة</th><th>الرسوم</th><th>المدة المتوقعة</th></tr></thead><tbody><tr><td>عمّان</td><td>2 د.أ</td><td>24 – 48 ساعة</td></tr><tr><td>باقي المحافظات</td><td>3 د.أ</td><td>2 – 3 أيام عمل</td></tr><tr><td>الطلبات التي تبلغ 35 د.أ أو أكثر</td><td>مجاناً</td><td>حسب المنطقة</td></tr></tbody></table></figure>
<!-- /wp:table -->

<!-- wp:paragraph -->
<p>بعد استلام طلبك، نتواصل معك عبر واتساب أو الهاتف لتأكيد الطلب والعنوان وموعد التوصيل. أيام العمل من الأحد إلى الخميس.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">سياسة الاستبدال والإرجاع</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul><li>يمكن استبدال المنتج أو إرجاعه خلال 7 أيام من تاريخ الاستلام، بشرط أن يكون في تغليفه الأصلي وغير مستخدم.</li><li>لا تُستبدل منتجات العناية الشخصية (الشامبو، واللوشن، وأطقم الأسنان، وأغطية الاستحمام) بعد فتحها، حفاظاً على الصحة العامة.</li><li>في حال وصول منتج تالف أو مختلف عن الطلب، يرجى إرسال صورة له عبر واتساب خلال 48 ساعة من الاستلام، وسنستبدله دون أي رسوم إضافية.</li><li>يتحمّل العميل رسوم التوصيل في حالات الاستبدال لأسباب غير متعلقة بعيب في المنتج.</li></ul>
<!-- /wp:list -->
HTML;
}

function nuzul_page_about() {
	return '';
}

function nuzul_page_contact() {
	return <<<HTML
<!-- wp:paragraph {"fontSize":"lg"} -->
<p class="has-lg-font-size">يسعدنا الرد على استفساراتكم من الأحد إلى الخميس، من الساعة 10 صباحاً حتى 6 مساءً.</p>
<!-- /wp:paragraph -->

<!-- wp:shortcode -->
[nuzul_contact]
<!-- /wp:shortcode -->

<!-- wp:paragraph -->
<p>للطلبات الخاصة بالشركات والمناسبات، يرجى ذكر المنتجات والكميات المطلوبة وموعد التسليم، وسنرسل لكم عرض سعر.</p>
<!-- /wp:paragraph -->

<!-- wp:shortcode -->
[nuzul_whatsapp label="راسلنا عبر واتساب" class="is-solid"]
<!-- /wp:shortcode -->
HTML;
}
