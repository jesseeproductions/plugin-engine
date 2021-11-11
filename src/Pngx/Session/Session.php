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
	 * Hooks and sets up the session.
	 *
	 * @since TBD
	 *
	 */
	protected function init() {

	}
}