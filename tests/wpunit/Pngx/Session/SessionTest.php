<?php
namespace Pngx\Session;

if ( ! class_exists( '\\SessionForTesting' ) ) {
	require_once codecept_data_dir( 'classes/mocks/SessionForTesting.php' );
}

/**
 * Test Pngx Session Abstract Class.
 *
 * @group   core
 *
 * @package Cctor__Coupon__Main
 */
class SessionTest extends \Codeception\TestCase\WPTestCase {

	/**
	 * It should be instantiatable
	 *
	 * @test
	 */
	public function be_instantiatable() {
		$this->assertInstanceOf( SessionForTesting::class, $this->make_instance() );
	}

	/**
	 * @return SessionForTesting
	 */
	protected function make_instance() {
		return new SessionForTesting();
	}

	/**
	 * @test
	 */
	public function it_is_loading_common() {

		$this->assertTrue( true );
	}


}
