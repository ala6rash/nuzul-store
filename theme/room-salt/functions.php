<?php
/**
 * Room & Salt theme functions.
 *
 * @package room-salt
 */

defined( 'ABSPATH' ) || exit;

define( 'RS_VERSION', '1.0.0' );

require_once get_theme_file_path( 'inc/catalog.php' );
require_once get_theme_file_path( 'inc/setup.php' );

/* -------------------------------------------------------------------------
 * Theme supports and assets
 * ---------------------------------------------------------------------- */
add_action( 'after_setup_theme', function () {
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'responsive-embeds' );
	add_editor_style( 'style.css' );
} );

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'room-salt', get_stylesheet_uri(), array(), RS_VERSION );
} );

add_action( 'init', function () {
	register_block_pattern_category( 'room-salt', array( 'label' => 'Room & Salt' ) );
} );

/** Favicon from the brand icon when no Site Icon was uploaded. */
add_action( 'wp_head', function () {
	if ( ! has_site_icon() ) {
		echo '<link rel="icon" type="image/svg+xml" href="' . esc_url( get_theme_file_uri( 'assets/img/icon-square.svg' ) ) . '">' . "\n";
		echo '<link rel="apple-touch-icon" href="' . esc_url( get_theme_file_uri( 'assets/img/icon-192.png' ) ) . '">' . "\n";
	}
	echo '<meta name="theme-color" content="#12343A">' . "\n";
} );

/* -------------------------------------------------------------------------
 * Store contact settings (Settings → General)
 * ---------------------------------------------------------------------- */
function rs_opt( $key ) {
	$defaults = array(
		'rs_whatsapp'  => '962790000000',
		'rs_instagram' => 'roomandsalt',
		'rs_email'     => 'hello@example.com',
	);
	$v = get_option( $key, '' );
	return '' !== $v ? $v : ( $defaults[ $key ] ?? '' );
}

/** WhatsApp number in international format, digits only (9627XXXXXXXX). */
function rs_wa_number() {
	return preg_replace( '/\D+/', '', rs_opt( 'rs_whatsapp' ) );
}

/** Local display format: 07X XXX XXXX. */
function rs_wa_display() {
	$n = rs_wa_number();
	if ( 0 === strpos( $n, '962' ) ) {
		$n = '0' . substr( $n, 3 );
	}
	return trim( substr( $n, 0, 3 ) . ' ' . substr( $n, 3, 3 ) . ' ' . substr( $n, 6 ) );
}

function rs_wa_url( $text = '' ) {
	$url = 'https://wa.me/' . rs_wa_number();
	return $text ? $url . '?text=' . rawurlencode( $text ) : $url;
}

add_action( 'admin_init', function () {
	$fields = array(
		'rs_whatsapp'  => 'رقم واتساب المتجر (بالصيغة الدولية، مثال 962791234567)',
		'rs_instagram' => 'اسم حساب إنستغرام (بدون @)',
		'rs_email'     => 'البريد الإلكتروني الظاهر في الموقع',
	);
	foreach ( $fields as $key => $label ) {
		register_setting( 'general', $key, array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ) );
		add_settings_field( $key, $label, function () use ( $key ) {
			printf( '<input type="text" class="regular-text" dir="ltr" name="%1$s" id="%1$s" value="%2$s">', esc_attr( $key ), esc_attr( get_option( $key, '' ) ) );
		}, 'general' );
	}
} );

/** Links used by patterns; they keep working if WordPress lives in a sub-folder. */
function rs_link( $what ) {
	if ( 'shop' === $what ) {
		$id = function_exists( 'wc_get_page_id' ) ? wc_get_page_id( 'shop' ) : 0;
		return $id > 0 ? get_permalink( $id ) : home_url( '/shop/' );
	}
	if ( 0 === strpos( $what, 'cat:' ) ) {
		$term = get_term_by( 'slug', substr( $what, 4 ), 'product_cat' );
		return $term ? get_term_link( $term ) : home_url( '/product-category/' . substr( $what, 4 ) . '/' );
	}
	$page = get_page_by_path( $what );
	return $page ? get_permalink( $page ) : home_url( '/' . $what . '/' );
}

function rs_img( $file ) {
	return esc_url( get_theme_file_uri( 'assets/' . $file ) );
}

/* -------------------------------------------------------------------------
 * Small dynamic blocks used inside templates (shortcodes keep them editable)
 * ---------------------------------------------------------------------- */
add_shortcode( 'rs_whatsapp', function ( $atts ) {
	$a = shortcode_atts( array( 'label' => 'اطلب عبر واتساب', 'text' => 'مرحباً، أود السؤال عن منتجات Room & Salt', 'class' => '' ), $atts );
	return sprintf( '<a class="rs-wa-link %s" href="%s" target="_blank" rel="noopener">%s</a>', esc_attr( $a['class'] ), esc_url( rs_wa_url( $a['text'] ) ), esc_html( $a['label'] ) );
} );

add_shortcode( 'rs_contact', function () {
	$ig = rs_opt( 'rs_instagram' );
	ob_start();
	?>
	<ul class="rs-contact">
		<li><span>واتساب</span><a href="<?php echo esc_url( rs_wa_url() ); ?>" dir="ltr"><?php echo esc_html( rs_wa_display() ); ?></a></li>
		<li><span>إنستغرام</span><a href="<?php echo esc_url( 'https://instagram.com/' . $ig ); ?>" dir="ltr">@<?php echo esc_html( $ig ); ?></a></li>
		<li><span>البريد الإلكتروني</span><a href="mailto:<?php echo esc_attr( rs_opt( 'rs_email' ) ); ?>" dir="ltr"><?php echo esc_html( rs_opt( 'rs_email' ) ); ?></a></li>
	</ul>
	<?php
	return ob_get_clean();
} );

add_shortcode( 'rs_year', function () {
	return esc_html( wp_date( 'Y' ) );
} );

/* -------------------------------------------------------------------------
 * Floating WhatsApp button + "order on WhatsApp" on product pages
 * ---------------------------------------------------------------------- */
add_action( 'wp_footer', function () {
	if ( function_exists( 'is_checkout' ) && ( is_checkout() || is_cart() ) ) {
		return;
	}
	printf(
		'<a class="rs-wa-float" href="%s" target="_blank" rel="noopener" aria-label="تواصل معنا عبر واتساب"><svg viewBox="0 0 32 32" width="26" height="26" aria-hidden="true"><path fill="currentColor" d="M16 3a13 13 0 0 0-11.2 19.6L3 29l6.6-1.7A13 13 0 1 0 16 3zm0 23.6c-2 0-4-.6-5.7-1.6l-.4-.2-3.9 1 1-3.8-.3-.4A10.6 10.6 0 1 1 16 26.6zm5.8-7.9c-.3-.2-1.9-.9-2.2-1-.3-.1-.5-.2-.7.2l-1 1.2c-.2.2-.4.2-.7.1a8.7 8.7 0 0 1-4.3-3.8c-.3-.5.3-.5.9-1.6.1-.2 0-.4 0-.5l-1-2.4c-.3-.6-.5-.5-.7-.5h-.6c-.2 0-.6.1-.9.4-.3.3-1.1 1.1-1.1 2.7s1.2 3.2 1.3 3.4c.2.2 2.3 3.5 5.6 4.9 2.1.9 2.9 1 4 .8.6-.1 1.9-.8 2.2-1.5.3-.8.3-1.4.2-1.5-.1-.2-.3-.3-.6-.4z"/></svg><span>اسألنا</span></a>',
		esc_url( rs_wa_url( 'مرحباً Room & Salt، لدي سؤال' ) )
	);
} );

function rs_product_wa_button() {
	global $product;
	if ( ! $product instanceof WC_Product ) {
		return '';
	}
	if ( '' === $product->get_price() ) {
		$text  = sprintf( 'مرحباً، أود معرفة سعر: %s (%s)', $product->get_name(), get_permalink( $product->get_id() ) );
		$label = 'اسأل عن السعر عبر واتساب';
		$class = 'is-solid';
	} else {
		$text  = sprintf( 'مرحباً، أود طلب: %s (%s)', $product->get_name(), get_permalink( $product->get_id() ) );
		$label = 'أو اطلب عبر واتساب';
		$class = 'is-outline';
	}
	return sprintf( '<p class="rs-wa-product"><a class="rs-wa-link %s" href="%s" target="_blank" rel="noopener">%s</a></p>', esc_attr( $class ), esc_url( rs_wa_url( $text ) ), esc_html( $label ) );
}

add_filter( 'render_block_woocommerce/add-to-cart-form', function ( $html ) {
	return $html . rs_product_wa_button();
} );

/** Products without a confirmed price say so instead of showing nothing. */
add_filter( 'woocommerce_empty_price_html', function () {
	return '<span class="rs-price-request">السعر عند الطلب</span>';
} );

/** In product grids, "price on request" items link to the product page instead of a dead button. */
add_filter( 'woocommerce_loop_add_to_cart_link', function ( $html, $product ) {
	if ( '' === $product->get_price() ) {
		return sprintf( '<a class="button rs-ask" href="%s">اسأل عن السعر</a>', esc_url( get_permalink( $product->get_id() ) ) );
	}
	return $html;
}, 10, 2 );

/** Arabic button labels even when WooCommerce's Arabic language pack is missing. */
add_filter( 'woocommerce_product_add_to_cart_text', function ( $text, $product ) {
	return $product->is_purchasable() && $product->is_in_stock() ? 'أضف إلى السلة' : 'عرض المنتج';
}, 10, 2 );
add_filter( 'woocommerce_product_single_add_to_cart_text', function () {
	return 'أضف إلى السلة';
} );

/** Illustration note under the gallery: the first image is a drawing, the real photo follows. */
add_filter( 'render_block_woocommerce/product-image-gallery', function ( $html ) {
	global $product;
	if ( $product instanceof WC_Product && get_post_meta( $product->get_id(), '_rs_illustration', true ) ) {
		$note = count( $product->get_gallery_image_ids() ) ? 'الصورة الأولى توضيحية، والصورة الثانية للمنتج الفعلي في المعرض.' : 'الصورة توضيحية. تواصل معنا عبر واتساب لتصلك صور المنتج الفعلية.';
		$html .= '<p class="rs-illus-note">' . esc_html( $note ) . '</p>';
	}
	return $html;
} );

/* -------------------------------------------------------------------------
 * Checkout tuned for Jordan: no postcode, no company, short fields
 * ---------------------------------------------------------------------- */
add_filter( 'woocommerce_get_country_locale', function ( $locale ) {
	$locale['JO'] = array_merge( $locale['JO'] ?? array(), array(
		'postcode'  => array( 'required' => false, 'hidden' => true ),
		'state'     => array( 'required' => true, 'hidden' => false, 'label' => 'المحافظة' ),
		'company'   => array( 'required' => false, 'hidden' => true ),
		'city'      => array( 'label' => 'المدينة / المنطقة' ),
		'address_1' => array( 'label' => 'العنوان (الحي، الشارع، رقم البناية)' ),
	) );
	return $locale;
} );

/** Jordan's 12 governorates (WooCommerce ships none), used for shipping zones. */
function rs_jo_states() {
	return array(
		'AM' => 'عمّان',
		'IR' => 'إربد',
		'AZ' => 'الزرقاء',
		'BA' => 'البلقاء',
		'MD' => 'مادبا',
		'JA' => 'جرش',
		'AJ' => 'عجلون',
		'MA' => 'المفرق',
		'KA' => 'الكرك',
		'AT' => 'الطفيلة',
		'MN' => 'معان',
		'AQ' => 'العقبة',
	);
}

add_filter( 'woocommerce_states', function ( $states ) {
	$states['JO'] = rs_jo_states();
	return $states;
} );

add_filter( 'woocommerce_checkout_fields', function ( $fields ) {
	unset( $fields['billing']['billing_company'], $fields['billing']['billing_postcode'], $fields['shipping']['shipping_company'], $fields['shipping']['shipping_postcode'] );
	return $fields;
} );

add_filter( 'loop_shop_per_page', function () {
	return 16;
} );

/** Current price of a product by SKU, for marketing blocks. */
function rs_sku_price( $sku, $fallback ) {
	if ( ! function_exists( 'wc_get_product_id_by_sku' ) ) {
		return $fallback;
	}
	$id = wc_get_product_id_by_sku( $sku );
	$p  = $id ? wc_get_product( $id ) : null;
	return $p && '' !== $p->get_price() ? wc_format_localized_price( $p->get_price() + 0 ) : $fallback;
}

/** Write the dinar the way Jordanians read it: د.أ */
add_filter( 'woocommerce_currency_symbol', function ( $symbol, $currency ) {
	return 'JOD' === $currency ? 'د.أ' : $symbol;
}, 10, 2 );

/** The header already has its own account + cart icons; stop WooCommerce auto-adding a second set next to the menu. */
add_filter( 'hooked_block_types', function ( $hooked, $position, $anchor ) {
	if ( 'core/navigation' === $anchor ) {
		$hooked = array_diff( $hooked, array( 'woocommerce/customer-account', 'woocommerce/mini-cart' ) );
	}
	return $hooked;
}, 20, 3 );
