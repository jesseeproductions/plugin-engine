<?php
/**
 * A mocks the abstract class for abstract Session for tests.
 *
 * @since TBD
 *
 * @package Pngx\Session
 */
namespace Pngx\Session;

/**
 * Class SessionTest
 *
 * @since TBD
 *
 * @package Pngx\Session
 */
class SessionForTesting extends Session_Cookie_Abstract {

	/**
	 * Hooks and sets up the session.
	 *
	 * @since TBD
	 */
	public function init() {}

	/**
	 * Cleanup session data.
	 *
	 * @since TBD
	 */
	public function cleanup_sessions() {}

	/**
	 * Return if there is an active session.
	 *
	 * @since TBD
	 */
	public function has_session() {}
}
