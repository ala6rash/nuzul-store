<?php
/**
 * Search and sharing: meta description, Open Graph tags, store details for Google,
 * and WebP thumbnails. Steps aside when an SEO plugin is active.
 *
 * @package room-salt
 */

defined( 'ABSPATH' ) || exit;

/** New uploads get WebP thumbnails (smaller pages); falls back to JPEG where the server can't make WebP. */
add_filter( 'image_editor_output_format', function ( $formats ) {
	$formats['image/jpeg'] = 'image/webp';
	return $formats;
} );

function rs_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' );
}

/** Description and image for the current page. */
function rs_seo_meta() {
	$desc  = get_bloginfo( 'description' );
	$image = get_theme_file_uri( 'assets/img/icon-512.png' );
	$type  = 'website';
	if ( function_exists( 'is_product' ) && is_product() ) {
		$product = wc_get_product( get_queried_object_id() );
		if ( $product ) {
			$desc  = $product->get_short_description();
			$src   = wp_get_attachment_image_url( $product->get_image_id(), 'large' );
			$image = $src ? $src : $image;
			$type  = 'product';
		}
	} elseif ( is_tax( 'product_cat' ) ) {
		$desc = term_description();
	} elseif ( is_singular() ) {
		$desc = get_the_excerpt();
	} elseif ( is_front_page() || is_home() ) {
		$desc = 'مناشف من القطن المصري، وأرواب، ومخدات وفرشات فندقية، ومنتجات البحر الميت من الأردن. توصيل إلى جميع المحافظات.';
	}
	$desc = wp_trim_words( wp_strip_all_tags( (string) $desc ), 30, '…' );
	return array( $desc, $image, $type );
}

add_action( 'wp_head', function () {
	if ( rs_seo_plugin_active() || is_404() ) {
		return;
	}
	list( $desc, $image, $type ) = rs_seo_meta();
	if ( $desc ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $desc ) );
	}
	$tags = array(
		'og:site_name'   => get_bloginfo( 'name' ),
		'og:locale'      => 'ar_JO',
		'og:type'        => $type,
		'og:title'       => wp_get_document_title(),
		'og:description' => $desc,
		'og:url'         => is_singular() ? get_permalink() : ( is_front_page() ? home_url( '/' ) : '' ),
		'og:image'       => $image,
	);
	if ( is_tax() ) {
		$tags['og:url'] = get_term_link( get_queried_object() );
	}
	foreach ( $tags as $k => $v ) {
		if ( $v && ! is_wp_error( $v ) ) {
			printf( '<meta property="%s" content="%s">' . "\n", esc_attr( $k ), esc_attr( $v ) );
		}
	}
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
}, 2 );

/** Store details for Google on the home page. Only facts the store already states on the site. */
add_action( 'wp_head', function () {
	if ( ! is_front_page() || rs_seo_plugin_active() ) {
		return;
	}
	$data = array(
		'@context'           => 'https://schema.org',
		'@type'              => 'OnlineStore',
		'name'               => get_bloginfo( 'name' ),
		'url'                => home_url( '/' ),
		'logo'               => get_theme_file_uri( 'assets/img/icon-512.png' ),
		'description'        => get_bloginfo( 'description' ),
		'areaServed'         => array( '@type' => 'Country', 'name' => 'Jordan' ),
		'currenciesAccepted' => 'JOD',
		'contactPoint'       => array(
			'@type'             => 'ContactPoint',
			'contactType'       => 'customer service',
			'telephone'         => '+' . rs_wa_number(),
			'availableLanguage' => array( 'ar', 'en' ),
		),
	);
	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}, 3 );
