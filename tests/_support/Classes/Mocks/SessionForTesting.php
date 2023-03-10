<?php
/**
 * A mocks the abstract class for abstract Session for tests.
 *
 * By Default Plugin Engine Does not run code controlled by this class, this is for testing it and can be used as the base in a plugin that uses this feature.
 *
 * @since 4.0.0
 *
 * @package Pngx\Session
 */
namespace Pngx\Tests\Classes\Mocks;

use Pngx\Session\Session_Cookie_Abstract;
use Pngx__Cache;

/**
 * Class SessionForTesting
 *
 * @since 4.0.0
 *
 * @package Pngx\Tests\Classes\Mocks
 */
class SessionForTesting extends Session_Cookie_Abstract {

	/**
	 * Constructor for the session class.
	 *
	 * @since 4.0.0
	 *
	 * @param Pngx__Cache|null $cache The class handler for pngx cache.
	 */
	public function __construct( Pngx__Cache $cache ) {
		$this->set_cookie_name();
		$this->set_table_name();
		$this->cache = $cache;
	}
}
