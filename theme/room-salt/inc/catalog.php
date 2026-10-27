<?php
/**
 * Store catalogue, read from inc/catalog.json. Names and prices come from the
 * showroom price cards; a null price means "price on request" until confirmed.
 *
 * @package room-salt
 */

defined( 'ABSPATH' ) || exit;

function rs_catalog() {
	static $data = null;
	if ( null === $data ) {
		$data = json_decode( (string) file_get_contents( get_theme_file_path( 'inc/catalog.json' ) ), true ); // phpcs:ignore
		if ( ! is_array( $data ) ) {
			$data = array( 'categories' => array(), 'products' => array() );
		}
	}
	return $data;
}

/** Categories flattened to slug => data, children carry their parent slug. */
function rs_catalog_categories() {
	$out = array();
	foreach ( rs_catalog()['categories'] as $c ) {
		$out[ $c['slug'] ] = $c + array( 'parent' => '' );
		foreach ( $c['children'] ?? array() as $ch ) {
			$out[ $ch['slug'] ] = $ch + array( 'parent' => $c['slug'] );
		}
	}
	return $out;
}

/** Top-level categories only, for the home page and menus. */
function rs_catalog_top_categories() {
	return array_filter( rs_catalog_categories(), function ( $c ) {
		return '' === $c['parent'];
	} );
}

function rs_catalog_products() {
	return rs_catalog()['products'];
}
