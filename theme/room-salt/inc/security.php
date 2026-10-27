<?php
/**
 * Basic hardening that a small shop needs on shared hosting:
 * login attempt limit, no XML-RPC, no user-name discovery, no file editor, safe headers.
 *
 * @package room-salt
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
	define( 'DISALLOW_FILE_EDIT', true );
}

const RS_LOGIN_MAX_TRIES = 5;
const RS_LOGIN_LOCK_MIN  = 15;

function rs_login_key() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	return 'rs_login_' . md5( $ip );
}

/** Refuse to check the password while this address is locked out. */
add_filter( 'authenticate', function ( $user ) {
	if ( (int) get_transient( rs_login_key() ) >= RS_LOGIN_MAX_TRIES ) {
		/* translators: %d: minutes */
		return new WP_Error( 'rs_locked', sprintf( 'محاولات دخول كثيرة. حاول مرة أخرى بعد %d دقيقة.', RS_LOGIN_LOCK_MIN ) );
	}
	return $user;
}, 30 );

add_action( 'wp_login_failed', function () {
	$key = rs_login_key();
	set_transient( $key, (int) get_transient( $key ) + 1, RS_LOGIN_LOCK_MIN * MINUTE_IN_SECONDS );
} );

add_action( 'wp_login', function () {
	delete_transient( rs_login_key() );
} );

/** Same message for a wrong user name and a wrong password; other login errors keep their own message. */
add_filter( 'authenticate', function ( $user ) {
	if ( is_wp_error( $user ) && array_intersect( $user->get_error_codes(), array( 'invalid_username', 'invalid_email', 'incorrect_password' ) ) ) {
		return new WP_Error( 'rs_bad_login', 'اسم المستخدم أو كلمة المرور غير صحيحة.' );
	}
	return $user;
}, 99 );

add_filter( 'xmlrpc_enabled', '__return_false' );

/** Stop ?author=1, author archives and the public users endpoint from revealing login names. Runs before WordPress's canonical redirect. */
add_action( 'template_redirect', function () {
	if ( ( isset( $_GET['author'] ) || is_author() ) && ! is_user_logged_in() ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
}, 1 );

add_filter( 'rest_endpoints', function ( $endpoints ) {
	if ( ! is_user_logged_in() ) {
		unset( $endpoints['/wp/v2/users'], $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
	}
	return $endpoints;
} );

add_action( 'send_headers', function () {
	header( 'X-Content-Type-Options: nosniff' );
	header( 'X-Frame-Options: SAMEORIGIN' );
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );
	header( 'Permissions-Policy: camera=(), microphone=(), geolocation=()' );
} );
