<?php

namespace Pngx\Install;

use Pngx\Tests\Traits\With_Uopz;


/**
 * Test Pngx Install Scripts
 *
 * @group   core
 *
 * @package Pngx\Install
 */
class DatabaseTest extends \Codeception\TestCase\WPTestCase {

	use With_Uopz;

	/**
	 * @return Database
	 */
	protected function make_instance() {
		return new Database();
	}

	/**
	 * @test
	 */
	public function be_instantiatable() {
		$this->assertInstanceOf( Database::class, $this->make_instance() );
	}

	//https://github.com/woocommerce/woocommerce/blob/trunk/plugins/woocommerce/tests/legacy/unit-tests/woocommerce-admin/install.php

	/**
	 * Integration test for database table creation.
	 *
	 * @group database
	 */
	public function test_create_tables() {
		global $wpdb;

		$Database = $this->make_instance();

		// Remove the Test Suite’s use of temporary tables https://wordpress.stackexchange.com/a/220308.
		remove_filter( 'query', array( $this, '_create_temporary_tables' ) );
		remove_filter( 'query', array( $this, '_drop_temporary_tables' ) );

		// List of tables created by Database::create_tables.
		$tables = array(
			"{$wpdb->prefix}pngx_sessions",
		);

		// Remove any existing tables in the environment.
		$query = 'DROP TABLE IF EXISTS ' . implode( ',', $tables );
		$wpdb->query( $query ); // phpcs:ignore.

		$Database::create_tables();
		$result = $wpdb->get_col( "SHOW TABLES LIKE '{$wpdb->prefix}%'" );

		// Check all the tables exist.
		foreach ( $tables as $table ) {
			$this->assertContains( $table, $result );
		}
	}


	/**
	 * Run maybe_update_db_version and confirm the expected jobs are pushed to the queue.
	 *
	 * @dataProvider db_update_version_provider
	 *
	 * @param string $db_update_version WC version to test.
	 * @param int    $expected_jobs_count # of expected jobs.
	 *
	 * @return void
	 */
	public function test_running_db_updates( $db_update_version, $expected_jobs_count ) {
		update_option( 'woocommerce_db_version', $db_update_version );
		add_filter(
			'woocommerce_enable_auto_update_db',
			function() {
				return true;
			}
		);

		$class  = new ReflectionClass( WC_Install::class );
		$method = $class->getMethod( 'maybe_update_db_version' );
		$method->setAccessible( true );
		$method->invoke( $class );

		$pending_jobs = WC_Helper_Queue::get_all_pending();
		$pending_jobs = array_filter(
			$pending_jobs,
			function( $pending_job ) {
				return $pending_job->get_hook() === 'woocommerce_run_update_callback';
			}
		);

		$this->assertCount( $expected_jobs_count, $pending_jobs );
	}


	/**
	 * Ensure that a DB version callback is defined when there are updates.
	 */
	public function test_db_update_callbacks_exist() {
		$all_callbacks = \WC_Install::get_db_update_callbacks();

		foreach ( $all_callbacks as $version => $version_callbacks ) {
			// Verify all callbacks have been defined.
			foreach ( $version_callbacks as $version_callback ) {
				if ( strpos( $version_callback, 'wc_admin_update' ) === 0 ) {
					$this->assertTrue(
						function_exists( $version_callback ),
						"Callback {$version_callback}() is not defined."
					);
				}
			}
		}
	}

	/**
	 * By the time we hit this test method, we should have the following cron jobs.
	 * - wc_admin_daily
	 * - generate_category_lookup_table
	 *
	 * @return void
	 */
	public function test_cron_job_creation() {
		$this->assertNotFalse( wp_next_scheduled( 'wc_admin_daily' ) );
		$this->assertNotFalse( wp_next_scheduled( 'generate_category_lookup_table' ) );
	}

	/**
	 * Data provider that returns DB Update version string and # of expected pending jobs.
	 *
	 * @return array[]
	 */
	public function db_update_version_provider() {
		return array(
			// [DB Update version string, # of expected pending jobs]
			array( '3.9.0', 33 ),
			array( '4.0.0', 26 ),
			array( '4.4.0', 22 ),
			array( '4.5.0', 20 ),
			array( '5.0.0', 16 ),
			array( '5.6.0', 14 ),
			array( '6.0.0', 7 ),
			array( '6.3.0', 4 ),
			array( '6.4.0', 1 ),
		);
	}

	/**
	 * Test missed DB version number update.
	 * See: https:// github.com/woocommerce/woocommerce-admin/issues/5058
	 */
	public function test_missed_version_number_update() {
		$this->markTestSkipped( 'We no longer update WooCommerce Admin versions' );
		$old_version = '1.6.0'; // This should get updated to later versions as we add more migrations.

		// Simulate an upgrade from an older version.
		update_option( self::VERSION_OPTION, '1.6.0' );
		WC_Install::install();
		WC_Helper_Queue::run_all_pending();

		// Simulate a collision/failure in version updating.
		update_option( self::VERSION_OPTION, '1.6.0' );

		// The next update check should force update the skipped version number.
		WC_Install::install();
		$this->assertTrue( version_compare( $old_version, get_option( self::VERSION_OPTION ), '<' ) );

		// The following update check should bump the version to the current (no migrations left).
		WC_Install::install();
		$this->assertEquals( get_option( self::VERSION_OPTION ), WC_ADMIN_VERSION_NUMBER );
	}

	/**
	 * Test the following options are created.
	 *
	 * - woocommerce_admin_install_timestamp
	 *
	 * @return void
	 */
	public function test_options_are_set() {
		delete_transient( 'wc_installing' );
		WC_Install::install();
		$options = array( 'woocommerce_admin_install_timestamp' );
		foreach ( $options as $option ) {
			$this->assertNotFalse( get_option( $option ) );
		}
	}

	/**
	 * Test woocommerce_admin_installed action.
	 * @return void
	 */
	public function test_woocommerce_admin_installed_action() {
		delete_transient( 'wc_installing' );
		WC_Install::install();
		$this->assertTrue( did_action( 'woocommerce_admin_installed' ) > 0 );
	}

	/**
	 * Test woocommerce_updated action gets fired.
	 *
	 * @return void
	 */
	public function test_woocommerce_updated_action() {
		$versions     = array_keys( WC_Install::get_db_update_callbacks() );
		$prev_version = $versions[ count( $versions ) - 2 ];
		update_option( 'woocommerce_version', $prev_version );
		WC_Install::check_version();
		$this->assertTrue( did_action( 'woocommerce_updated' ) > 0 );
	}

	/**
	 * Test woocommerce_newly_installed action gets fired.
	 * @return void
	 */
	public function test_woocommerce_newly_installed_action() {
		delete_option( 'woocommerce_version' );
		WC_Install::check_version();
		$this->assertTrue( did_action( 'woocommerce_newly_installed' ) > 0 );
	}

	/**
	 * Test migrate_options();
	 * @return void
	 */
	public function test_migrate_options() {
		delete_transient( 'wc_installing' );
		WC_Install::install();
		$this->assertTrue( defined( 'WC_ADMIN_MIGRATING_OPTIONS' ) );
		$migrated_options = array(
			'woocommerce_onboarding_profile'           => 'wc_onboarding_profile',
			'woocommerce_admin_install_timestamp'      => 'wc_admin_install_timestamp',
			'woocommerce_onboarding_opt_in'            => 'wc_onboarding_opt_in',
			'woocommerce_admin_import_stats'           => 'wc_admin_import_stats',
			'woocommerce_admin_version'                => 'wc_admin_version',
			'woocommerce_admin_last_orders_milestone'  => 'wc_admin_last_orders_milestone',
			'woocommerce_admin-wc-helper-last-refresh' => 'wc-admin-wc-helper-last-refresh',
			'woocommerce_admin_report_export_status'   => 'wc_admin_report_export_status',
			'woocommerce_task_list_complete'           => 'woocommerce_task_list_complete',
			'woocommerce_task_list_hidden'             => 'woocommerce_task_list_hidden',
			'woocommerce_extended_task_list_complete'  => 'woocommerce_extended_task_list_complete',
			'woocommerce_extended_task_list_hidden'    => 'woocommerce_extended_task_list_hidden',
		);

		foreach ( $migrated_options as $new_option => $old_option ) {
			$old_option_value = get_option( $old_option );
			if ( false === $old_option_value ) {
				continue;
			}
			$this->assertNotFalse( get_option( $new_option ), $new_option );
		}
	}
}
