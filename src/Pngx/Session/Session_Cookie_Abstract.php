<?php
/**
 * Class Session
 *
 * @since   TBD
 *
 * @package Pngx\Session
 */

namespace Pngx\Session;

/**
 * Class Session
 *
 * Based off WooCommerce's WC_Session_Handler.
 *
 * @since   TBD
 *
 * @package Pngx\Session
 */
abstract class Session_Cookie_Abstract extends Session_Abstract implements Session_Cookie_Interface {

	/**
	 * Cookie name for the session.
	 *
	 * @since TBD
	 *
	 * @var string Name of the cookie.
	 */
	protected $cookie_name;

	/**
	 * Whether the session has a cookie.
	 *
	 * @since TBD
	 *
	 * @var boolean True or false if a cookie for the session exists, defaults to false.
	 */
	protected $has_cookie = false;

	/**
	 * Constructor for the abstract session class.
	 *
	 * @since TBD
	 *
	 */
	public function __construct() {
		$this->set_cookie_name();
		parent::__construct();
	}

	/**
	 * {@inheritDoc}
	 */
	public function has_session() {
		return
			isset( $_COOKIE[ $this->cookie ] ) ||
			$this->has_cookie ||
			is_user_logged_in();
	}

	/**
	 * Check if cookie session should be secure.
	 *
	 * @since TBD
	 *
	 * @return bool Whether to use a secure cookie.
	 */
	protected function use_secure_cookie() {
		return apply_filters( 'pngx_session_use_secure_cookie', strstr( get_option( 'home' ), 'https:' ) && is_ssl() );
	}

	/**
	 * {@inheritDoc}
	 */
	public function init_session_cookie() {
		$cookie = $this->get_session_cookie();

		if ( $cookie ) {
			$this->user_id        = $cookie[0];
			$this->session_expiration = $cookie[1];
			$this->session_expiring   = $cookie[2];
			$this->has_cookie         = true;
			$this->data               = $this->get_session_data();

			// If the user logs in, update session.
			if ( is_user_logged_in() && strval( get_current_user_id() ) !== $this->user_id ) {
				$guest_session_id   = $this->user_id;
				$this->user_id = strval( get_current_user_id() );
				$this->unsaved       = true;
				$this->save_data( $guest_session_id );
				$this->set_customer_session_cookie( true );
			}

			// Update session if its close to expiring.
			if ( time() > $this->session_expiring ) {
				$this->set_session_expiration();
				$this->update_session_timestamp( $this->user_id, $this->session_expiration );
			}
		} else {
			$this->set_session_expiration();
			$this->user_id = $this->generate_unique_id();
			$this->data        = $this->get_session_data();
		}
	}

	/**
	 * Set the cookie name with a filter to be able to modify it.
	 *
	 * @since TBD
	 */
	protected function set_cookie_name() {

		/**
		 * Allow filtering of the session cookie name.
		 *
		 * The cookie name is prepended with wp, cache systems like batcache will not cache pages when set.
		 *
		 * @since TBD
		 *
		 * @param string The default cooke name session.
		 * @param Session $this The current session object.
		 */
		$this->cookie_name = apply_filters( 'wp_pngx_session_cookie_name', 'pngx_session_' . COOKIEHASH, $this );
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_customer_session_cookie( $set ) {
		if ( $set ) {
			$to_hash           = $this->user_id . '|' . $this->session_expiration;
			$cookie_hash       = hash_hmac( 'md5', $to_hash, wp_hash( $to_hash ) );
			$cookie_value      = $this->user_id . '||' . $this->session_expiration . '||' . $this->session_expiring . '||' . $cookie_hash;
			$this->has_cookie = true;

			// Cookies set only if called before headers are sent.
			if ( ! isset( $_COOKIE[ $this->cookie ] ) || $_COOKIE[ $this->cookie ] !== $cookie_value ) {
				pngx_setcookie( $this->cookie, $cookie_value, $this->session_expiration, $this->use_secure_cookie(), true );
			}
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_cookie_name() {
		return $this->cookie_name;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_session_cookie() {
		$cookie_value = isset( $_COOKIE[ $this->cookie ] ) ?
			wp_unslash( $_COOKIE[ $this->cookie ] ) :
			false;

		if (
			empty( $cookie_value ) ||
			! is_string( $cookie_value )
		) {
			return false;
		}

		list( $unique_id, $session_expiration, $session_expiring, $cookie_hash ) = explode( '||', $cookie_value );

		if ( empty( $unique_id ) ) {
			return false;
		}

		// Validate hash.
		$to_hash = $unique_id . '|' . $session_expiration;
		$hash    = hash_hmac( 'md5', $to_hash, wp_hash( $to_hash ) );

		if ( empty( $cookie_hash ) || ! hash_equals( $hash, $cookie_hash ) ) {
			return false;
		}

		return [ $unique_id, $session_expiration, $session_expiring, $cookie_hash ];
	}

	/**
	 * {@inheritDoc}
	 */
	public function forget_cookie() {
		pngx_setcookie( $this->cookie, '', time() - YEAR_IN_SECONDS, $this->use_secure_cookie(), true );
	}
}
