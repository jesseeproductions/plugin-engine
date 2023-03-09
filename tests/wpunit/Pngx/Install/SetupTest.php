<?php

namespace Pngx\Install;

use Pngx\Tests\Traits\With_Uopz;


/**
 * Test Pngx Setup Scripts
 *
 * @group   core
 *
 * @package Pngx\Install
 */
class SetupTest extends \Codeception\TestCase\WPTestCase {

	use With_Uopz;

	/**
	 * @return Setup
	 */
	protected function make_instance() {
		return new Setup();
	}

	/**
	 * @test
	 */
	public function be_instantiatable() {
		$this->assertInstanceOf( Setup::class, $this->make_instance() );
	}

}
