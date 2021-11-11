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
 * @since   TBD
 *
 * @package Pngx\Session
 */
abstract class Session {

	/**
	 * Session Data.
	 *
	 * @since TBD
	 *
	 * @var array<mixed|mixed> The associative data array for the session.
	 */
	protected $data = [];

	/**
	 * Cookie name for the session.
	 *
	 * @since TBD
	 *
	 * @var string Name of the cookie.
	 */
	protected $cookie_name;

	/**
	 * Expiration Date.
	 *
	 * @since TBD
	 *
	 * @var string The expiration timestamp.
	 */
	protected $expiration_date;

	/**
	 * Expiring Soon Date.
	 *
	 * @since TBD
	 *
	 * @var string The expiring soon timestamp.
	 */
	protected $expiring_soon_date;

	/**
	 * Whether the session has a cookie.
	 *
	 * @since TBD
	 *
	 * @var boolean True or false if a cookie for the session exists, defaults to false.
	 */
	protected $has_cookie = false;

	/**
	 * Name of table for the session data.
	 *
	 * @since TBD
	 *
	 * @var string Table name for session data.
	 */
	protected $table;

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
	 * Constructor for the abstract session class.
	 *
	 * @since TBD
	 *
	 */
	public function __construct() {
		$this->set_cookie_name();
		$this->set_table();
	}

	/**
	 * Set the cookie name for the session.
	 *
	 * @since TBD
	 *
	 */
	protected function set_cookie_name() {

		/**
		 * Allow filtering of the session cookie name.
		 *
		 * @since TBD
		 *
		 * @param string The default cooke name session.
		 * @param Session $this The current session object.
		 */
		$this->_cookie = apply_filters( 'pngx_session_cookie_name', 'pngx_session_' . COOKIEHASH, $this );
	}

	/**
	 * Set the custom table name.
	 *
	 * @since TBD
	 *
	 */
	protected function set_table() {
		$this->_table = $GLOBALS['wpdb']->prefix . 'pngx_sessions';
	}

	/**
	 * Get the unique id of the session.
	 *
	 * @since TBD
	 *
	 * @return int
	 */
	public function get_id() {
		return $this->unique_id;
	}

	/**
	 * Hooks and sets up the session.
	 *
	 * @since TBD
	 *
	 */
	public function init() {
	}

	/**
	 * Cleanup session data.
	 *
	 * @since TBD
	 *
	 */
	public function cleanup_sessions() {
	}

	/**
	 * Magic get method.
	 *
	 * @since TBD
	 *
	 * @param mixed $key Key to get.
	 *
	 * @return mixed
	 */
	public function __get( $key ) {
		return $this->get( $key );
	}

	/**
	 * Magic set method.
	 *
	 * @since TBD
	 *
	 * @param mixed $key   Key to set.
	 * @param mixed $value Value to set.
	 */
	public function __set( $key, $value ) {
		$this->set( $key, $value );
	}

	/**
	 * Magic isset method.
	 *
	 * @since TBD
	 *
	 * @param mixed $key Name of the key to check.
	 *
	 * @return bool Whether the key is set or not.
	 */
	public function __isset( $key ) {
		return isset( $this->data[ sanitize_title( $key ) ] );
	}

	/**
	 * Magic unset method.
	 *
	 * @since TBD
	 *
	 * @param mixed $key Name of the key to unset.
	 */
	public function __unset( $key ) {
		if ( isset( $this->data[ $key ] ) ) {
			unset( $this->data[ $key ] );
			$this->unsaved = true;
		}
	}

	/**
	 * Get a session variable.
	 *
	 * @since TBD
	 *
	 * @param string $key     Name of the key to get.
	 * @param mixed  $default Optional default variable if it is not set.
	 *
	 * @return array<string|mixed> The requested value of the session data or maybe a default.
	 */
	public function get( $key, $default = null ) {
		$key = sanitize_key( $key );

		return isset( $this->data[ $key ] ) ? maybe_unserialize( $this->data[ $key ] ) : $default;
	}

	/**
	 * Set a session variable.
	 *
	 * @since TBD
	 *
	 * @param string $key   Name of the key to set.
	 * @param mixed  $value Value to set.
	 */
	public function set( $key, $value ) {
		if ( $value !== $this->get( $key ) ) {
			$this->data[ sanitize_key( $key ) ] = maybe_serialize( $value );
			$this->unsaved                      = true;
		}
	}
}