<?php
/**
 * One-click store setup: Appearance → إعداد متجر Room & Salt.
 * Configures WooCommerce for Jordan (JOD, cash on delivery, shipping by
 * governorate), creates categories, the showroom products and info pages.
 * Safe to run more than once: existing items are skipped.
 *
 * @package room-salt
 */

defined( 'ABSPATH' ) || exit;

add_action( 'admin_menu', function () {
	add_theme_page( 'إعداد متجر Room & Salt', 'إعداد المتجر', 'manage_options', 'rs-setup', 'rs_setup_page' );
} );

add_action( 'after_switch_theme', function () {
	set_transient( 'rs_show_setup_notice', 1, DAY_IN_SECONDS );
} );

add_action( 'admin_notices', function () {
	if ( ! get_transient( 'rs_show_setup_notice' ) || get_option( 'rs_setup_done' ) ) {
		return;
	}
	printf( '<div class="notice notice-info"><p>تم تفعيل قالب Room & Salt. <a href="%s">أكمل إعداد المتجر بخطوة واحدة</a>.</p></div>', esc_url( admin_url( 'themes.php?page=rs-setup' ) ) );
} );

function rs_setup_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$log = array();
	if ( isset( $_POST['rs_run_setup'] ) && check_admin_referer( 'rs_setup' ) ) {
		$log = rs_run_setup( array(
			'whatsapp'  => sanitize_text_field( wp_unslash( $_POST['whatsapp'] ?? '' ) ),
			'instagram' => sanitize_text_field( wp_unslash( $_POST['instagram'] ?? '' ) ),
			'email'     => sanitize_email( wp_unslash( $_POST['email'] ?? '' ) ),
			'cliq'      => sanitize_text_field( wp_unslash( $_POST['cliq'] ?? '' ) ),
			'products'  => ! empty( $_POST['products'] ),
			'sync'      => ! empty( $_POST['sync'] ),
			'stock'     => '' === ( $_POST['stock'] ?? '' ) ? null : absint( wp_unslash( $_POST['stock'] ) ),
			'staff'     => sanitize_email( wp_unslash( $_POST['staff'] ?? '' ) ),
		) );
	}
	$wc = class_exists( 'WooCommerce' );
	$hp = rs_card_plugin_state();
	?>
	<div class="wrap" dir="rtl">
		<h1>إعداد متجر Room & Salt</h1>
		<?php if ( ! $wc ) : ?>
			<div class="notice notice-error"><p>يجب تثبيت إضافة <strong>WooCommerce</strong> وتفعيلها أولاً (الإضافات ← أضف جديد).</p></div>
		<?php endif; ?>
		<?php if ( $log ) : ?>
			<div class="notice notice-success"><p><strong>اكتمل الإعداد:</strong></p><ul style="list-style:disc;padding-inline-start:20px">
				<?php foreach ( $log as $line ) : ?><li><?php echo esc_html( $line ); ?></li><?php endforeach; ?>
			</ul><p><a class="button button-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank">عرض الموقع</a></p></div>
		<?php endif; ?>
		<p>هذه الخطوة تقوم بما يلي:</p>
		<ol>
			<li>ضبط العملة على الدينار الأردني، والبيع داخل الأردن فقط.</li>
			<li>تفعيل <strong>الدفع عند الاستلام</strong> و<strong>التحويل الفوري عبر CliQ</strong>.</li>
			<li>مناطق الشحن: عمّان 2 د.أ، وباقي المحافظات 3 د.أ، ومجاني للطلبات فوق 35 د.أ (عدّلها من إعدادات WooCommerce).</li>
			<li>إضافة الأقسام و<?php echo count( rs_catalog_products() ); ?> منتجاً بأسعار المعرض.</li>
			<li>إنشاء صفحات: التوصيل والاستبدال، من نحن، تواصل معنا، الشروط والأحكام، سياسة الخصوصية.</li>
			<li>تفعيل تتبّع المخزون، وتنبيهك بالبريد عند قرب نفاد أي منتج.</li>
			<li>رسائل البريد للطلبات بالعربية وبألوان المتجر، ونسخة من كل طلب جديد إلى بريد المتجر.</li>
		</ol>
		<form method="post">
			<?php wp_nonce_field( 'rs_setup' ); ?>
			<table class="form-table" role="presentation">
				<tr><th><label for="whatsapp">رقم واتساب المتجر</label></th>
					<td><input id="whatsapp" name="whatsapp" class="regular-text" dir="ltr" placeholder="962791234567" value="<?php echo esc_attr( get_option( 'rs_whatsapp', '' ) ); ?>"><p class="description">بالصيغة الدولية بدون + : الرمز 962 ثم الرقم بدون الصفر الأول.</p></td></tr>
				<tr><th><label for="instagram">حساب إنستغرام</label></th>
					<td><input id="instagram" name="instagram" class="regular-text" dir="ltr" placeholder="roomandsalt" value="<?php echo esc_attr( get_option( 'rs_instagram', '' ) ); ?>"></td></tr>
				<tr><th><label for="email">البريد الإلكتروني</label></th>
					<td><input id="email" name="email" class="regular-text" dir="ltr" value="<?php echo esc_attr( get_option( 'rs_email', get_option( 'admin_email' ) ) ); ?>"></td></tr>
				<tr><th><label for="cliq">الاسم المستعار لـ CliQ</label></th>
					<td><input id="cliq" name="cliq" class="regular-text" dir="ltr" placeholder="ROOMANDSALT" value="<?php echo esc_attr( get_option( 'rs_cliq_alias', '' ) ); ?>"><p class="description">الاسم المستعار المسجّل في البنك لاستقبال التحويلات الفورية.</p></td></tr>
				<tr><th>المنتجات</th>
					<td><label><input type="checkbox" name="products" value="1" checked> أضف منتجات المعرض (يمكن تعديل الأسعار والصور لاحقاً)</label><br>
						<label><input type="checkbox" name="sync" value="1"> حدّث أسماء المنتجات الموجودة وأوصافها وروابطها وترتيبها من القالب (لا يغيّر الأسعار ولا الصور ولا المخزون)</label></td></tr>
					<tr><th><label for="stock">الكمية الافتتاحية</label></th>
						<td><input id="stock" name="stock" type="number" min="0" class="small-text" dir="ltr" value=""><p class="description">اختياري. إذا كتبت رقماً، يُعطى كل منتج له سعر هذه الكمية، وينقص المخزون مع كل طلب ويظهر المنتج «نفد من المخزون» عند الصفر. اتركه فارغاً لتُدخل الكميات لكل منتج بنفسك.</p></td></tr>
					<tr><th><label for="staff">بريد الموظف</label></th>
						<td><input id="staff" name="staff" type="email" class="regular-text" dir="ltr" value=""><p class="description">اختياري. يُنشأ له حساب «مدير متجر»: يرى الطلبات والمنتجات والعملاء ويعدّلها، ولا يستطيع تغيير القالب أو الإضافات أو المستخدمين. يصله بريد لاختيار كلمة المرور.</p></td></tr>
			</table>
			<?php submit_button( 'جهّز المتجر', 'primary', 'rs_run_setup', true, $wc ? array() : array( 'disabled' => 'disabled' ) ); ?>
		</form>

		<h2>الدفع بالبطاقة</h2>
		<p>يتطلب حساب تاجر معتمداً من شركة دفع أردنية. بعد الموافقة تحصل منها على مفاتيح الربط (Entity ID وAccess Token أو ما يماثلها).</p>
		<ol>
			<li><strong>HyperPay:</strong> إضافتها الرسمية «HyperPay Payments» موجودة في مكتبة ووردبريس.
				<?php if ( 'active' === $hp ) : ?>
					<strong style="color:#2D7A78">مثبّتة ومفعّلة.</strong> <a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout' ) ); ?>">أدخل المفاتيح من إعدادات الدفع</a>.
				<?php elseif ( 'installed' === $hp ) : ?>
					مثبّتة وغير مفعّلة. <a href="<?php echo esc_url( admin_url( 'plugins.php' ) ); ?>">فعّلها من صفحة الإضافات</a>.
				<?php else : ?>
					<a href="<?php echo esc_url( admin_url( 'plugin-install.php?s=hyperpay-gateways&tab=search&type=term' ) ); ?>">ثبّتها من هنا</a>.
				<?php endif; ?>
			</li>
			<li><strong>PayTabs:</strong> ترسل الشركة إضافة WooCommerce الخاصة بها للتاجر بعد فتح الحساب. ارفعها من الإضافات ← أضف جديد ← رفع إضافة.</li>
			<li>بعد إدخال المفاتيح، جرّب الدفع في «وضع الاختبار» ببطاقة اختبار من الشركة، ثم حوّل إلى الوضع الفعلي.</li>
		</ol>
		<p>القالب لا يحتاج أي تعديل: تظهر البطاقة تلقائياً في صفحة إتمام الطلب بجانب الدفع عند الاستلام وCliQ.</p>
	</div>
	<?php
}

/** Copy a bundled theme image into the media library once; returns attachment ID. */
function rs_import_image( $rel, $title ) {
	$existing = get_posts( array( 'post_type' => 'attachment', 'meta_key' => '_rs_asset', 'meta_value' => $rel, 'posts_per_page' => 1, 'fields' => 'ids' ) );
	if ( $existing ) {
		return (int) $existing[0];
	}
	$src = get_theme_file_path( 'assets/' . $rel );
	if ( ! file_exists( $src ) ) {
		return 0;
	}
	$upload = wp_upload_bits( 'rs-' . str_replace( '/', '-', $rel ), null, file_get_contents( $src ) ); // phpcs:ignore
	if ( ! empty( $upload['error'] ) ) {
		return 0;
	}
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$id = wp_insert_attachment( array( 'post_mime_type' => 'image/jpeg', 'post_title' => $title, 'post_status' => 'inherit' ), $upload['file'] );
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );
	update_post_meta( $id, '_wp_attachment_image_alt', $title );
	update_post_meta( $id, '_rs_asset', $rel );
	return $id;
}

function rs_product_description( $p ) {
	$html = '<p>' . esc_html( ! empty( $p['desc'] ) ? $p['desc'] : $p['short'] ) . '</p>';
	if ( ! empty( $p['specs'] ) ) {
		$html .= '<ul>';
		foreach ( $p['specs'] as $s ) {
			$html .= '<li>' . esc_html( $s ) . '</li>';
		}
		$html .= '</ul>';
	}
	$html .= '<p class="rs-en" lang="en" dir="ltr">' . esc_html( $p['en'] ) . '</p>';
	return $html;
}

function rs_run_setup( $args ) {
	$args = wp_parse_args( $args, array( 'whatsapp' => '', 'instagram' => '', 'email' => '', 'cliq' => '', 'products' => true, 'sync' => false, 'stock' => null, 'staff' => '' ) );
	$log  = array();

	foreach ( array( 'whatsapp' => 'rs_whatsapp', 'instagram' => 'rs_instagram', 'email' => 'rs_email' ) as $k => $opt ) {
		if ( '' !== $args[ $k ] ) {
			update_option( $opt, 'whatsapp' === $k ? preg_replace( '/\D+/', '', $args[ $k ] ) : ltrim( $args[ $k ], '@' ) );
		}
	}
	$log[] = 'تم حفظ بيانات التواصل.';

	if ( in_array( get_option( 'blogname' ), array( '', 'My WordPress Website', 'My WordPress Site' ), true ) ) {
		update_option( 'blogname', 'Room & Salt' );
	}
	if ( in_array( get_option( 'blogdescription' ), array( '', 'Just another WordPress site' ), true ) ) {
		update_option( 'blogdescription', 'مستلزمات الفنادق ومنتجات البحر الميت' );
	}

	if ( ! get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
		$log[] = 'تم تغيير صيغة الروابط إلى /اسم-الصفحة/.';
	}

	if ( ! class_exists( 'WooCommerce' ) ) {
		$log[] = 'إضافة WooCommerce غير مفعّلة، لذلك توقف الإعداد هنا.';
		return $log;
	}

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
	$log[] = 'العملة الدينار الأردني، والبيع داخل الأردن.';

	$cod = array_merge( (array) get_option( 'woocommerce_cod_settings', array() ), array(
		'enabled'            => 'yes',
		'title'              => 'الدفع عند الاستلام',
		'description'        => 'تدفع نقداً لمندوب التوصيل عند استلام الطلب.',
		'instructions'       => 'سنتواصل معك عبر واتساب لتأكيد الطلب وموعد التوصيل.',
		'enable_for_methods' => array(),
		'enable_for_virtual' => 'yes',
	) );
	update_option( 'woocommerce_cod_settings', $cod );
	$log[] = 'تم تفعيل الدفع عند الاستلام.';

	// CliQ instant transfer, built on WooCommerce's bank-transfer method: the order waits "on hold" until the transfer is confirmed.
	if ( ! empty( $args['cliq'] ) ) {
		update_option( 'rs_cliq_alias', $args['cliq'] );
	}
	$alias = get_option( 'rs_cliq_alias', '' );
	$bacs  = array_merge( (array) get_option( 'woocommerce_bacs_settings', array() ), array(
		'enabled'      => 'yes',
		'title'        => 'تحويل فوري عبر CliQ',
		'description'  => 'حوّل قيمة الطلب من تطبيق البنك أو المحفظة عبر CliQ' . ( $alias ? ' إلى الاسم المستعار: ' . $alias : '' ) . '، واكتب رقم الطلب في ملاحظة التحويل. نشحن الطلب بعد وصول التحويل.',
		'instructions' => 'الاسم المستعار لـ CliQ: ' . ( $alias ? $alias : '(يُضاف من إعداد المتجر)' ) . '. اكتب رقم الطلب في ملاحظة التحويل، ثم أرسل صورة الإيصال عبر واتساب لنؤكد طلبك.',
	) );
	update_option( 'woocommerce_bacs_settings', $bacs );
	update_option( 'woocommerce_bacs_accounts', array() );
	$log[] = $alias ? "تم تفعيل الدفع عبر CliQ على الاسم المستعار {$alias}." : 'تم تفعيل الدفع عبر CliQ. أضف الاسم المستعار من صفحة الإعداد.';

	if ( class_exists( 'WC_Shipping_Zones' ) ) {
		$names = wp_list_pluck( WC_Shipping_Zones::get_zones(), 'zone_name' );
		$zones = array(
			'عمّان'          => array( 'states' => array( 'AM' ), 'cost' => '2' ),
			'باقي المحافظات' => array( 'states' => array_diff( array_keys( rs_jo_states() ), array( 'AM' ) ), 'cost' => '3' ),
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

	// Categories (parents first, so children can attach).
	$cat_ids = array();
	foreach ( rs_catalog_categories() as $slug => $c ) {
		$term = get_term_by( 'slug', $slug, 'product_cat' );
		if ( ! $term ) {
			$res = wp_insert_term( $c['name'], 'product_cat', array(
				'slug'        => $slug,
				'description' => $c['desc'],
				'parent'      => $c['parent'] && isset( $cat_ids[ $c['parent'] ] ) ? $cat_ids[ $c['parent'] ] : 0,
			) );
			if ( is_wp_error( $res ) ) {
				continue;
			}
			$tid = $res['term_id'];
		} else {
			$tid = $term->term_id;
		}
		$cat_ids[ $slug ] = $tid;
		// A real product photo represents each category; the drawing only if that photo is missing.
		$thumb = (int) get_term_meta( $tid, 'thumbnail_id', true );
		if ( ! $thumb || 0 === strpos( (string) get_post_meta( $thumb, '_rs_asset', true ), 'art/' ) ) {
			$sku = rs_category_photo_sku( $slug );
			$rel = $sku && file_exists( get_theme_file_path( "assets/photos/{$sku}.jpg" ) ) ? "photos/{$sku}.jpg" : "art/cat-{$slug}.jpg";
			update_term_meta( $tid, 'thumbnail_id', rs_import_image( $rel, $c['name'] ) );
		}
	}
	$log[] = 'تم إنشاء ' . count( $cat_ids ) . ' أقسام.';

	if ( $args['products'] ) {
		$made = 0;
		$ask  = 0;
		foreach ( array_values( rs_catalog_products() ) as $order => $p ) {
			if ( wc_get_product_id_by_sku( $p['sku'] ) ) {
				continue;
			}
			$cats = array( $cat_ids[ $p['cat'] ] ?? 0 );
			$parent = rs_catalog_categories()[ $p['cat'] ]['parent'] ?? '';
			if ( $parent && isset( $cat_ids[ $parent ] ) ) {
				$cats[] = $cat_ids[ $parent ];
			}
			$prod = new WC_Product_Simple();
			$prod->set_name( $p['name'] );
			$prod->set_slug( sanitize_title( $p['en'] ) );
			$prod->set_sku( $p['sku'] );
			if ( null !== $p['price'] ) {
				$prod->set_regular_price( $p['price'] );
			} else {
				$ask++;
			}
			$prod->set_short_description( $p['short'] );
			$prod->set_description( rs_product_description( $p ) );
			$prod->set_status( 'publish' );
			$prod->set_featured( ! empty( $p['featured'] ) );
			$prod->set_menu_order( $order ); // Shop lists products in catalog order: towels first, Dead Sea last.
			$prod->set_stock_status( 'instock' );
			$prod->set_category_ids( array_filter( $cats ) );
			// Real photo first (manufacturer or showroom); the drawing only where no photo exists yet.
			$has_photo = file_exists( get_theme_file_path( "assets/photos/{$p['sku']}.jpg" ) );
			$prod->set_image_id( rs_import_image( ( $has_photo ? 'photos/' : 'art/' ) . "{$p['sku']}.jpg", $p['name'] ) );
			if ( file_exists( get_theme_file_path( "assets/real/{$p['sku']}.jpg" ) ) ) {
				$prod->set_gallery_image_ids( array( rs_import_image( "real/{$p['sku']}.jpg", $p['name'] . ' (صورة من المعرض)' ) ) );
			}
			if ( ! $has_photo ) {
				$prod->update_meta_data( '_rs_illustration', 1 );
			}
			$prod->save();
			$made++;
		}
		$log[] = "تمت إضافة {$made} منتجاً. منها {$ask} بدون سعر مؤكد وتظهر «السعر عند الطلب».";
	}

	if ( $args['sync'] ) {
		$log[] = sprintf( 'تم تحديث أسماء %d منتجاً وأوصافها.', rs_sync_product_copy() );
	}

	$log = array_merge( $log, rs_setup_store_controls( $args ) );

	$pages = array(
		'delivery-returns' => array( 'التوصيل والاستبدال', rs_page_delivery() ),
		'about'            => array( 'من نحن', rs_page_about() ),
		'contact'          => array( 'تواصل معنا', rs_page_contact() ),
		'terms'            => array( 'الشروط والأحكام', rs_page_terms() ),
		'privacy'          => array( 'سياسة الخصوصية', rs_page_privacy() ),
	);
	foreach ( $pages as $slug => $pg ) {
		$page = get_page_by_path( $slug );
		$pid  = $page ? $page->ID : wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_name' => $slug, 'post_title' => $pg[0], 'post_content' => $pg[1] ) );
		if ( 'terms' === $slug ) {
			update_option( 'woocommerce_terms_page_id', $pid );
		} elseif ( 'privacy' === $slug ) {
			update_option( 'wp_page_for_privacy_policy', $pid );
		}
	}
	$log[] = 'تم إنشاء صفحات التوصيل والاستبدال، ومن نحن، وتواصل معنا، والشروط والأحكام، وسياسة الخصوصية.';

	// WooCommerce names its pages in English when its Arabic language pack is missing.
	foreach ( array( 'shop' => 'المتجر', 'cart' => 'السلة', 'checkout' => 'إتمام الطلب', 'myaccount' => 'حسابي' ) as $wc_page => $title ) {
		$pid = wc_get_page_id( $wc_page );
		if ( $pid > 0 ) {
			wp_update_post( array( 'ID' => $pid, 'post_title' => $title ) );
		}
	}

	flush_rewrite_rules();
	update_option( 'rs_setup_done', 1 );
	delete_transient( 'rs_show_setup_notice' );
	return $log;
}

/** Rewrite names and descriptions of existing catalog products from catalog.json; prices, images and stock stay as the owner set them. */
function rs_sync_product_copy() {
	$n = 0;
	foreach ( array_values( rs_catalog_products() ) as $order => $p ) {
		$prod = wc_get_product( wc_get_product_id_by_sku( $p['sku'] ) );
		if ( ! $prod ) {
			continue;
		}
		$prod->set_name( $p['name'] );
		$prod->set_slug( sanitize_title( $p['en'] ) );
		$prod->set_short_description( $p['short'] );
		$prod->set_description( rs_product_description( $p ) );
		$prod->set_menu_order( $order );
		$prod->save();
		$n++;
	}
	return $n;
}

function rs_category_photo_sku( $slug ) {
	$map = array(
		'towels'         => 'RS-TW-02',
		'robes-slippers' => 'RS-RB-01',
		'bedding'        => 'RS-BD-07',
		'mattresses'     => 'RS-MT-01',
		'bath-amenities' => 'RS-AM-01',
		'dead-sea'       => 'RS-DS-01',
		'dead-sea-face'  => 'RS-DS-06',
		'dead-sea-body'  => 'RS-DS-03',
	);
	return $map[ $slug ] ?? '';
}

/** 'active', 'installed' or '' for the official HyperPay plugin. */
function rs_card_plugin_state() {
	$file = 'hyperpay-gateways/hyperpay-gateways.php';
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	$found = '';
	foreach ( array_keys( get_plugins() ) as $f ) {
		if ( 0 === strpos( $f, 'hyperpay-gateways/' ) ) {
			$found = $f;
			break;
		}
	}
	if ( ! $found ) {
		return '';
	}
	return is_plugin_active( $found ) ? 'active' : 'installed';
}

/** Stock tracking, Arabic order emails in the brand colours, and an optional Shop Manager account. */
function rs_setup_store_controls( $args ) {
	$log   = array();
	$email = rs_opt( 'rs_email' );
	$email = is_email( $email ) && false === strpos( $email, 'example.com' ) ? $email : get_option( 'admin_email' );

	$stock = array(
		'woocommerce_manage_stock'               => 'yes',
		'woocommerce_notify_low_stock'           => 'yes',
		'woocommerce_notify_no_stock'            => 'yes',
		'woocommerce_notify_low_stock_amount'    => '2',
		'woocommerce_notify_no_stock_amount'     => '0',
		'woocommerce_stock_email_recipient'      => $email,
		'woocommerce_hide_out_of_stock_items'    => 'no',
		'woocommerce_hold_stock_minutes'         => '60',
		'woocommerce_stock_format'               => '',
	);
	foreach ( $stock as $k => $v ) {
		update_option( $k, $v );
	}
	$log[] = 'تم تفعيل تتبّع المخزون، ويصلك بريد عندما تبقى قطعتان من أي منتج وعند نفاده.';

	if ( null !== $args['stock'] ) {
		$n = 0;
		foreach ( rs_catalog_products() as $p ) {
			$prod = null === $p['price'] ? null : wc_get_product( wc_get_product_id_by_sku( $p['sku'] ) );
			if ( ! $prod ) {
				continue;
			}
			$prod->set_manage_stock( true );
			$prod->set_stock_quantity( $args['stock'] );
			$prod->save();
			$n++;
		}
		$log[] = sprintf( 'تم ضبط كمية %d لـ %d منتجاً. عدّل كمية كل منتج من صفحته عند الحاجة.', $args['stock'], $n );
	}

	$mail = array(
		'woocommerce_email_from_name'             => 'Room & Salt',
		'woocommerce_email_from_address'          => $email,
		'woocommerce_email_header_image'          => get_theme_file_uri( 'assets/img/logo-email.png' ),
		'woocommerce_email_footer_text'           => 'Room & Salt · للاستفسار راسلنا عبر واتساب ' . rs_wa_display(),
		'woocommerce_email_base_color'            => '#12343A',
		'woocommerce_email_background_color'      => '#F7F4EE',
		'woocommerce_email_body_background_color' => '#FFFFFF',
		'woocommerce_email_text_color'            => '#0F1B1E',
	);
	foreach ( $mail as $k => $v ) {
		update_option( $k, $v );
	}
	$texts = array(
		'new_order'                 => array( 'طلب جديد رقم {order_number}', 'طلب جديد: {order_number}', $email ),
		'cancelled_order'           => array( 'أُلغي الطلب رقم {order_number}', 'طلب ملغى: {order_number}', $email ),
		'failed_order'              => array( 'تعذّر الدفع للطلب رقم {order_number}', 'دفع غير مكتمل: {order_number}', $email ),
		'customer_on_hold_order'    => array( 'استلمنا طلبك رقم {order_number} من Room & Salt', 'شكراً لطلبك', null ),
		'customer_processing_order' => array( 'طلبك رقم {order_number} قيد التجهيز', 'شكراً لطلبك', null ),
		'customer_completed_order'  => array( 'طلبك رقم {order_number} في الطريق إليك', 'طلبك في الطريق', null ),
		'customer_refunded_order'   => array( 'أعدنا مبلغ الطلب رقم {order_number}', 'تمت إعادة المبلغ', null ),
		'customer_note'             => array( 'ملاحظة على طلبك رقم {order_number}', 'ملاحظة على طلبك', null ),
	);
	foreach ( $texts as $id => $t ) {
		$s = array_merge( (array) get_option( "woocommerce_{$id}_settings", array() ), array( 'enabled' => 'yes', 'subject' => $t[0], 'heading' => $t[1] ) );
		if ( $t[2] ) {
			$s['recipient'] = $t[2];
		}
		update_option( "woocommerce_{$id}_settings", $s );
	}
	$log[] = sprintf( 'رسائل الطلبات بالعربية، ويصل كل طلب جديد إلى %s.', $email );

	if ( $args['staff'] ) {
		if ( email_exists( $args['staff'] ) ) {
			$log[] = 'بريد الموظف مسجّل مسبقاً، فلم يُنشأ حساب جديد.';
		} else {
			$login = sanitize_user( strtok( $args['staff'], '@' ), true );
			while ( username_exists( $login ) ) {
				$login .= wp_rand( 1, 9 );
			}
			$uid = wp_insert_user( array(
				'user_login' => $login,
				'user_email' => $args['staff'],
				'user_pass'  => wp_generate_password( 24 ),
				'role'       => 'shop_manager',
				'locale'     => 'ar',
			) );
			if ( is_wp_error( $uid ) ) {
				$log[] = 'تعذّر إنشاء حساب الموظف: ' . $uid->get_error_message();
			} else {
				wp_new_user_notification( $uid, null, 'user' );
				$log[] = sprintf( 'تم إنشاء حساب «مدير متجر» باسم %s، وأُرسل له بريد لاختيار كلمة المرور.', $login );
			}
		}
	}
	return $log;
}

function rs_page_delivery() {
	return <<<HTML
<!-- wp:paragraph {"fontSize":"lg"} -->
<p class="has-lg-font-size">نوصل الطلبات إلى جميع محافظات المملكة، والدفع نقداً عند الاستلام.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">التوصيل</h2>
<!-- /wp:heading -->

<!-- wp:table {"className":"rs-table"} -->
<figure class="wp-block-table rs-table"><table><thead><tr><th>المنطقة</th><th>السعر</th><th>المدة</th></tr></thead><tbody><tr><td>عمّان</td><td>2 د.أ</td><td>خلال 24 – 48 ساعة</td></tr><tr><td>باقي المحافظات</td><td>3 د.أ</td><td>خلال 2 – 3 أيام عمل</td></tr><tr><td>الطلبات فوق 35 د.أ</td><td>مجاني</td><td>حسب المنطقة</td></tr></tbody></table></figure>
<!-- /wp:table -->

<!-- wp:paragraph -->
<p>بعد إتمام الطلب نتواصل معك عبر واتساب لتأكيد الطلب والعنوان وموعد التوصيل. توصيل الفرشات يُتفق عليه بشكل منفصل حسب الحجم والمنطقة.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">الاستبدال والإرجاع</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul><li>يمكن استبدال المنتج خلال 7 أيام من الاستلام إذا كان بتغليفه الأصلي ولم يُفتح.</li><li>منتجات العناية الشخصية ومنتجات البحر الميت لا تُسترجع بعد فتحها لأسباب صحية.</li><li>إذا وصلك منتج فيه عيب، أرسل لنا صورته عبر واتساب ونستبدله على حسابنا.</li></ul>
<!-- /wp:list -->
HTML;
}

/** Left empty on purpose until the owner provides the About text. */
function rs_page_about() {
	return '';
}

function rs_page_terms() {
	return <<<HTML
<!-- wp:paragraph -->
<p>باستخدامك موقع Room & Salt وإتمامك طلب شراء، فإنك توافق على الشروط التالية. يُرجى مراجعة هذه الصفحة مع مستشار قانوني قبل إطلاق المتجر.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">الطلبات والأسعار</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul><li>الأسعار بالدينار الأردني، وتشمل ما هو مذكور في صفحة المنتج فقط.</li><li>يُعدّ الطلب مؤكداً بعد تواصلنا معك عبر واتساب أو الهاتف.</li><li>نحتفظ بحق إلغاء الطلب إذا نفد المنتج أو ظهر خطأ في السعر، ونعيد المبلغ كاملاً إذا كان مدفوعاً.</li></ul>
<!-- /wp:list -->

<!-- wp:heading -->
<h2 class="wp-block-heading">الدفع</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul><li>الدفع نقداً عند الاستلام، أو بالتحويل الفوري عبر CliQ قبل الشحن.</li><li>في حال الدفع عبر CliQ يُشحن الطلب بعد وصول التحويل.</li></ul>
<!-- /wp:list -->

<!-- wp:heading -->
<h2 class="wp-block-heading">التوصيل والاستبدال</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>تفاصيل مدة التوصيل وتكلفته وشروط الاستبدال والإرجاع موضحة في صفحة «التوصيل والاستبدال».</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">صور المنتجات</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>بعض صور المنتجات رسوم توضيحية، وهذا مذكور في صفحة كل منتج. يمكنك طلب صور فعلية لأي منتج عبر واتساب قبل الشراء.</p>
<!-- /wp:paragraph -->
HTML;
}

function rs_page_privacy() {
	return <<<HTML
<!-- wp:paragraph -->
<p>توضح هذه الصفحة البيانات التي نجمعها عند الطلب من Room & Salt وكيف نستخدمها. يُرجى مراجعتها مع مستشار قانوني قبل إطلاق المتجر.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">البيانات التي نجمعها</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>الاسم، ورقم الهاتف، والعنوان، والبريد الإلكتروني إن أدخلته، وتفاصيل الطلب.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">كيف نستخدمها</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul><li>لتجهيز طلبك وتوصيله والتواصل معك بشأنه.</li><li>نشارك الاسم والهاتف والعنوان مع شركة التوصيل فقط بالقدر اللازم لإيصال الطلب.</li><li>لا نبيع بياناتك ولا نشاركها لأغراض إعلانية.</li></ul>
<!-- /wp:list -->

<!-- wp:heading -->
<h2 class="wp-block-heading">حقوقك</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>يمكنك طلب الاطلاع على بياناتك أو تعديلها أو حذفها بالتواصل معنا عبر واتساب أو البريد الإلكتروني.</p>
<!-- /wp:paragraph -->
HTML;
}

function rs_page_contact() {
	return <<<HTML
<!-- wp:paragraph {"fontSize":"lg"} -->
<p class="has-lg-font-size">أسرع طريقة للتواصل معنا هي واتساب.</p>
<!-- /wp:paragraph -->

<!-- wp:shortcode -->
[rs_contact]
<!-- /wp:shortcode -->

<!-- wp:shortcode -->
[rs_whatsapp label="راسلنا عبر واتساب" class="is-solid"]
<!-- /wp:shortcode -->
HTML;
}
