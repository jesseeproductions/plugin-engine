<?php
/**
 * Class Session
 *
 * @since   TBD
 *
 * @package Pngx\Session
 */

namespace Pngx\Session;

use Pngx__Cache;

/**
 * Class Session
 *
 * Based off WooCommerce's WC_Session_Handler.
 *
 *
 * @since   TBD
 *
 * @package Pngx\Session
 */
abstract class Session_Abstract implements Session_Interface {

	/**
	 * Cache Name
	 *
	 * @since TBD
	 *
	 * @var string The cache name.
	 */
	protected static $cache_name = 'pngx_session';

	/**
	 * Session Data.
	 *
	 * @since TBD
	 *
	 * @var array<mixed|mixed> The associative data array for the session.
	 */
	protected $data = [];

	/**
	 * Expiration Date.
	 *
	 * @since TBD
	 *
	 * @var string The expiration timestamp.
	 */
	protected $expiration_timestamp;

	/**
	 * Expiring Soon Date.
	 *
	 * @since TBD
	 *
	 * @var string The expiring soon timestamp.
	 */
	protected $expiring_soon_timestamp;

	/**
	 * Prefix for the nonce.
	 *
	 * @since TBD
	 *
	 * @var string The prefix name of the nonce.
	 */
	protected $nonce_prefix = 'pngx';

	/**
	 * Name of table for the session data.
	 *
	 * @since TBD
	 *
	 * @var string Table name for session data.
	 */
	protected static $table_name;

	/**
	 * User ID.
	 *
	 * @since TBD
	 *
	 * @var int The user id for the session.
	 */
	protected $user_id;

	/**
	 * Unique ID.
	 *
	 * @since TBD
	 *
	 * @var int The unique id for the session.
	 */
	protected $unique_id;

	/**
	 * When the session as data unsaved.
	 *
	 * @since TBD
	 *
	 * @var boolean Whether there is data unsaved.
	 */
	protected $unsaved;

	/**
	 * Pngx cache
	 *
	 * @since TBD
	 *
	 * @var boolean Pngx__Cache The class handler for pngx cache.
	 */
	protected $cache;

	/**
	 * Constructor for the abstract session class.
	 *
	 * @since TBD
	 *
	 * @param Pngx__Cache|null $cache The class handler for pngx cache.
	 */
	public function __construct( Pngx__Cache $cache = null ) {
		$this->set_table_name();
		$this->cache = null !== $cache ? $cache : pngx( 'cache' );
	}

	/**
	 * {@inheritDoc}
	 */
	public abstract function init();

	/**
	 * {@inheritDoc}
	 */
	public abstract function has_session();

	/**
	 * {@inheritDoc}
	 */
	public function __get( $key ) {
		return $this->get( $key );
	}

	/**
	 * {@inheritDoc}
	 */
	public function __set( $key, $value ) {
		$this->set( $key, $value );
	}

	/**
	 * {@inheritDoc}
	 */
	public function __isset( $key ) {
		return isset( $this->data[ sanitize_key( $key ) ] );
	}

	/**
	 * {@inheritDoc}
	 */
	public function __unset( $key ) {
		if ( isset( $this->data[ $key ] ) ) {
			unset( $this->data[ $key ] );
			$this->unsaved = true;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function get( $key, $default = null ) {
		$key = sanitize_key( $key );

		return isset( $this->data[ $key ] ) ? maybe_unserialize( $this->data[ $key ] ) : $default;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set( $key, $value ) {
		if ( $value !== $this->get( $key ) ) {
			$key = sanitize_key( $key );
			$this->data[ $key ] = maybe_serialize( $value );
			$this->unsaved      = true;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	protected function set_table_name() {
		$this->table_name = $GLOBALS['wpdb']->prefix . 'pngx_sessions';
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_table_name() {
		return static::$table_name;
	}

	/**
	 * Gets the cache prefix.
	 *
	 * @return string The cache prefix set in the class.
	 */
	protected function get_cache_prefix() {
		return $this->cache->get_cache_prefix( static::$cache_name );
	}

	/**
	 * {@inheritDoc}
	 */
	public function generate_unique_id() {
		$unique_id = '';

		if ( is_user_logged_in() ) {
			$unique_id = strval( get_current_user_id() );
		}

		if ( empty( $unique_id ) ) {
			require_once( ABSPATH . 'wp-includes/class-phpass.php' );
			$hasher    = new \PasswordHash( 8, false );
			$unique_id = md5( $hasher->get_random_bytes( 32 ) );
		}

		return $unique_id;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_user_id() {
		return $this->user_id;
	}

	/**
	 * Update nonce for logged out to ensure they have a unique nonce to manage a cart and more using the unique ID.
	 * Runs on 'wp_verify_nonce()' and 'wp_create_nonce()'.
	 *
	 * @since TBD
	 *
	 * @param int    $uid    User ID.
	 * @param string $action The nonce action.
	 *
	 * @return int|string A unique ID.
	 */
	public function maybe_update_nonce_of_logged_out_user( $uid, $action ) {
		if ( str_starts_with( $action, $this->nonce_prefix ) ) {
			return $this->has_session() && $this->user_id ? $this->user_id : $uid;
		}

		return $uid;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_session_id() {
		$unique_id = '';

		if ( $this->has_session() && $this->unique_id ) {
			return $this->unique_id;
		}

		if ( is_user_logged_in() ) {
			return (string) get_current_user_id();
		}

		return $unique_id;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_session_data() {
		return $this->has_session() ?
			(array) $this->get_session( $this->user_id, [] ) :
			[];
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_session( $user_id, $default = false ) {
		global $wpdb;

		if ( defined( 'WP_SETUP_CONFIG' ) ) {
			return false;
		}

		// Use cache class to attempt to get session.
		$value = $this->cache->get( $this->get_cache_prefix() . $user_id, static::$cache_name );

		if ( false === $value ) {
			$query = $wpdb->prepare( "
				SELECT session_value 
				FROM {$this->get_table_name()} 
				WHERE session_key = %s",
				$user_id
			);
			$value = $wpdb->get_var( $query );

			if ( is_null( $value ) ) {
				$value = $default;
			}

			$cache_duration = $this->session_expiration - time();
			if ( 0 < $cache_duration ) {
				$this->cache->get( $this->get_cache_prefix() . $user_id, $value, static::$cache_name, $cache_duration );
			}
		}

		return maybe_unserialize( $value );
	}

	/**
	 * {@inheritDoc}
	 */
	public function save_data( $logged_out_key = 0 ) {
		// Only save if there is something to save.
		if ( ! $this->unsaved && ! $this->has_session() ) {
			return;
		}

		global $wpdb;

		// Prepare session data for saving.
		$session_query = $wpdb->prepare( "
			INSERT INTO {$this->get_table_name()} (`session_key`, `session_value`, `session_expiry`) 
			VALUES (%s, %s, %d)
            ON DUPLICATE KEY 
            UPDATE `session_value` = VALUES(`session_value`), `session_expiry` = VALUES(`session_expiry`)
            ",
			$this->user_id,
			maybe_serialize( $this->data ),
			$this->session_expiration
		);

		$wpdb->query( $session_query );

		$this->cache->set( $this->get_cache_prefix() . $this->user_id, $this->data, static::$cache_name, $this->session_expiration - time() );

		$this->unsaved = false;
		if ( get_current_user_id() != $logged_out_key && ! is_object( get_user_by( 'id', $logged_out_key ) ) ) {
			$this->delete_session( $logged_out_key );
		}

	}

	/**
	 * {@inheritDoc}
	 */
	public function set_session_expiration() {
		/**
		 * Filter the Session Expiration Date.
		 *
		 * @since TBD
		 *
		 * @param int The time in seconds to set the expiring soon date, default 48 hours.
		 */
		$expiration_timestamp       = (int) apply_filters( 'pngx_expiration_timestamp', HOUR_IN_SECONDS * 48 );
		$this->expiration_timestamp = time() + $expiration_timestamp;

		/**
		 * Filter the Session Expiring Soon Date.
		 *
		 * @since TBD
		 *
		 * @param int The time in seconds to set the expiring soon date, default 47 hours.
		 */
		$expiring_soon_timestamp       = (int) apply_filters( 'pngx_expiring_soon_timestamp', $expiration_timestamp - HOUR_IN_SECONDS );
		$this->expiring_soon_timestamp = time() + $expiring_soon_timestamp;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_expiration_timestamp() {
		return $this->expiration_timestamp;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_expiring_soon_timestamp() {
		return $this->expiring_soon_timestamp;
	}

	/**
	 * {@inheritDoc}
	 */
	public function update_session_timestamp( $user_id, $timestamp ) {
		global $wpdb;

		$wpdb->update(
			$this->get_table_name(),
			[ 'session_expiry' => $timestamp, ],
			[ 'session_key' => $user_id, ],
			[ '%d', ]
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function cleanup_sessions() {
		global $wpdb;

		$query = $wpdb->prepare( "
			DELETE FROM {$this->get_table_name()} 
			WHERE session_expiry < %d",
			time()
		);
		$wpdb->query( $query );

		$this->cache->invalidate_cache_group( static::$cache_name );
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete_session( $user_id ) {
		global $wpdb;

		$this->cache->delete( $this->get_cache_prefix() . $user_id, static::$cache_name );

		$wpdb->delete(
			$this->get_table_name(),
			array(
				'session_key' => $user_id,
			)
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function forget_session() {
		do_action( 'pngx_forget_session', $this );

		// todo - add a function here to clear a cart? it should have a method that hooks into this, the abstract should force it.

		$this->data    = [];
		$this->unsaved = false;
		$this->user_id = $this->generate_unique_id();
	}

	/**
	 * {@inheritDoc}
	 */
	public function destroy_session() {
		$this->delete_session( $this->user_id );
		$this->forget_session();
	}
}