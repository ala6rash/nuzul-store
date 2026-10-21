<?php
/**
 * Nuzul theme functions.
 *
 * @package nuzul
 */

defined( 'ABSPATH' ) || exit;

define( 'NUZUL_VERSION', '1.0.0' );

require_once get_theme_file_path( 'inc/catalog.php' );
require_once get_theme_file_path( 'inc/setup.php' );

/* -------------------------------------------------------------------------
 * Theme supports and assets
 * ---------------------------------------------------------------------- */
add_action( 'after_setup_theme', function () {
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'responsive-embeds' );
	add_editor_style( 'style.css' );
} );

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'nuzul', get_stylesheet_uri(), array(), NUZUL_VERSION );
} );

add_action( 'init', function () {
	register_block_pattern_category( 'nuzul', array( 'label' => 'نُزُل' ) );
} );

/** Favicon from the brand icon when no Site Icon was uploaded. */
add_action( 'wp_head', function () {
	if ( ! has_site_icon() ) {
		echo '<link rel="icon" type="image/svg+xml" href="' . esc_url( get_theme_file_uri( 'assets/img/icon-square.svg' ) ) . '">' . "\n";
	}
	echo '<meta name="theme-color" content="#1E4640">' . "\n";
} );

/* -------------------------------------------------------------------------
 * Store contact settings (Settings → General)
 * ---------------------------------------------------------------------- */
function nuzul_opt( $key ) {
	$defaults = array(
		'nuzul_whatsapp'  => '962790000000',
		'nuzul_instagram' => 'nuzul.jo',
		'nuzul_email'     => 'hello@example.com',
	);
	$v = get_option( $key, '' );
	return '' !== $v ? $v : ( $defaults[ $key ] ?? '' );
}

/** WhatsApp number in international format, digits only (9627XXXXXXXX). */
function nuzul_wa_number() {
	return preg_replace( '/\D+/', '', nuzul_opt( 'nuzul_whatsapp' ) );
}

/** Local display format: 07X XXX XXXX. */
function nuzul_wa_display() {
	$n = nuzul_wa_number();
	if ( 0 === strpos( $n, '962' ) ) {
		$n = '0' . substr( $n, 3 );
	}
	return trim( substr( $n, 0, 3 ) . ' ' . substr( $n, 3, 3 ) . ' ' . substr( $n, 6 ) );
}

function nuzul_wa_url( $text = '' ) {
	$url = 'https://wa.me/' . nuzul_wa_number();
	return $text ? $url . '?text=' . rawurlencode( $text ) : $url;
}

add_action( 'admin_init', function () {
	$fields = array(
		'nuzul_whatsapp'  => 'رقم واتساب المتجر (بالصيغة الدولية، مثال 962791234567)',
		'nuzul_instagram' => 'اسم حساب إنستغرام (بدون @)',
		'nuzul_email'     => 'إيميل التواصل الظاهر بالموقع',
	);
	foreach ( $fields as $key => $label ) {
		register_setting( 'general', $key, array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ) );
		add_settings_field( $key, $label, function () use ( $key ) {
			printf( '<input type="text" class="regular-text" dir="ltr" name="%1$s" id="%1$s" value="%2$s">', esc_attr( $key ), esc_attr( get_option( $key, '' ) ) );
		}, 'general' );
	}
} );

/** Links used by patterns; they keep working if WordPress lives in a sub-folder. */
function nuzul_link( $what ) {
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

function nuzul_img( $file ) {
	return esc_url( get_theme_file_uri( 'assets/' . $file ) );
}

/* -------------------------------------------------------------------------
 * Small dynamic blocks used inside templates (shortcodes keep them editable)
 * ---------------------------------------------------------------------- */
add_shortcode( 'nuzul_whatsapp', function ( $atts ) {
	$a = shortcode_atts( array( 'label' => 'اطلب عبر واتساب', 'text' => 'مرحباً، أود الاستفسار عن منتجات نُزُل.', 'class' => '' ), $atts );
	return sprintf( '<a class="nuzul-wa-link %s" href="%s" target="_blank" rel="noopener">%s</a>', esc_attr( $a['class'] ), esc_url( nuzul_wa_url( $a['text'] ) ), esc_html( $a['label'] ) );
} );

add_shortcode( 'nuzul_contact', function () {
	$ig = nuzul_opt( 'nuzul_instagram' );
	ob_start();
	?>
	<ul class="nuzul-contact">
		<li><span>واتساب</span><a href="<?php echo esc_url( nuzul_wa_url() ); ?>" dir="ltr"><?php echo esc_html( nuzul_wa_display() ); ?></a></li>
		<li><span>إنستغرام</span><a href="<?php echo esc_url( 'https://instagram.com/' . $ig ); ?>" dir="ltr">@<?php echo esc_html( $ig ); ?></a></li>
		<li><span>البريد الإلكتروني</span><a href="mailto:<?php echo esc_attr( nuzul_opt( 'nuzul_email' ) ); ?>" dir="ltr"><?php echo esc_html( nuzul_opt( 'nuzul_email' ) ); ?></a></li>
	</ul>
	<?php
	return ob_get_clean();
} );

add_shortcode( 'nuzul_year', function () {
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
		'<a class="nuzul-wa-float" href="%s" target="_blank" rel="noopener" aria-label="تواصل معنا عبر واتساب"><svg viewBox="0 0 32 32" width="28" height="28" aria-hidden="true"><path fill="currentColor" d="M16 3a13 13 0 0 0-11.2 19.6L3 29l6.6-1.7A13 13 0 1 0 16 3zm0 23.6c-2 0-4-.6-5.7-1.6l-.4-.2-3.9 1 1-3.8-.3-.4A10.6 10.6 0 1 1 16 26.6zm5.8-7.9c-.3-.2-1.9-.9-2.2-1-.3-.1-.5-.2-.7.2l-1 1.2c-.2.2-.4.2-.7.1a8.7 8.7 0 0 1-4.3-3.8c-.3-.5.3-.5.9-1.6.1-.2 0-.4 0-.5l-1-2.4c-.3-.6-.5-.5-.7-.5h-.6c-.2 0-.6.1-.9.4-.3.3-1.1 1.1-1.1 2.7s1.2 3.2 1.3 3.4c.2.2 2.3 3.5 5.6 4.9 2.1.9 2.9 1 4 .8.6-.1 1.9-.8 2.2-1.5.3-.8.3-1.4.2-1.5-.1-.2-.3-.3-.6-.4z"/></svg><span>تواصل معنا</span></a>',
		esc_url( nuzul_wa_url( 'مرحباً، لدي استفسار.' ) )
	);
} );

function nuzul_product_wa_button() {
	global $product;
	if ( ! $product instanceof WC_Product ) {
		return '';
	}
	$text = sprintf( 'مرحباً، أود طلب: %s (%s)', $product->get_name(), get_permalink( $product->get_id() ) );
	return sprintf( '<p class="nuzul-wa-product"><a class="nuzul-wa-link is-outline" href="%s" target="_blank" rel="noopener">أو اطلب عبر واتساب</a></p>', esc_url( nuzul_wa_url( $text ) ) );
}

add_filter( 'render_block_woocommerce/add-to-cart-form', function ( $html ) {
	return $html . nuzul_product_wa_button();
} );

/* -------------------------------------------------------------------------
 * Checkout tuned for Jordan: no postcode, no company, short fields
 * ---------------------------------------------------------------------- */
add_filter( 'woocommerce_get_country_locale', function ( $locale ) {
	$locale['JO'] = array_merge( $locale['JO'] ?? array(), array(
		'postcode' => array( 'required' => false, 'hidden' => true ),
		'state'    => array( 'required' => true, 'hidden' => false, 'label' => 'المحافظة' ),
		'company'  => array( 'required' => false, 'hidden' => true ),
		'city'     => array( 'label' => 'المدينة / المنطقة' ),
		'address_1'=> array( 'label' => 'العنوان (الحي، الشارع، رقم البناية)' ),
	) );
	return $locale;
} );

/** Jordan's 12 governorates (WooCommerce ships none), used for shipping zones. */
function nuzul_jo_states() {
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
	$states['JO'] = nuzul_jo_states();
	return $states;
} );

add_filter( 'woocommerce_checkout_fields', function ( $fields ) {
	unset( $fields['billing']['billing_company'], $fields['billing']['billing_postcode'], $fields['shipping']['shipping_company'], $fields['shipping']['shipping_postcode'] );
	return $fields;
} );

/** Show 4 products per row and 12 per page in classic archives. */
add_filter( 'loop_shop_per_page', function () {
	return 12;
} );

/** Current price of a product by SKU, for marketing blocks. */
function nuzul_sku_price( $sku, $fallback ) {
	if ( ! function_exists( 'wc_get_product_id_by_sku' ) ) {
		return $fallback;
	}
	$id = wc_get_product_id_by_sku( $sku );
	$p  = $id ? wc_get_product( $id ) : null;
	return $p ? wc_format_localized_price( $p->get_price() + 0 ) : $fallback;
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
