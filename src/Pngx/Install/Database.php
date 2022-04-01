<?php
/**
 * Custom Database Setup.
 *
 * @since   TBD
 *
 * @package Pngx\Session
 */

namespace Pngx\Install;

use Pngx__Main;

/**
 * Class Database
 *
 * @since   TBD
 *
 * @package Pngx\Install
 */
class Database  {

	/**
	 * Create custom tables for Plugin Engine.
	 *
	 * @since TBD
	 *
	 */
	public static function create_tables() {
		global $wpdb;

		$wpdb->hide_errors();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		// dbDelta() cannot handle primary key changes, if there are changes to a primary key, run them here before it.

		dbDelta( self::get_schema() );
	}

	/**
	 * Get custom table schema.
	 *
	 * @since TBD
	 *
	 * Add or remove the table from Install::get_tables().
	 *
	 * @return string The sql to create or update tables.
	 */
	private static function get_schema() {
		global $wpdb;

		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			$collate = $wpdb->get_charset_collate();
		}

		$tables = "
			CREATE TABLE {$wpdb->prefix}pngx_sessions (
			  session_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			  session_key char(32) NOT NULL,
			  session_value longtext NOT NULL,
			  session_expiry BIGINT UNSIGNED NOT NULL,
			  PRIMARY KEY  (session_id),
			  UNIQUE KEY session_key (session_key)
			) $collate;
		";

		return $tables;
	}

	/**
	 * Get a list of Plugin Engine table names.
	 *
	 * @since TBD
	 *
	 * @return array<int|string> $tables An array of Plugin Engine table names.
	 */
	public static function get_tables() {
		global $wpdb;

		$tables = array(
			"{$wpdb->prefix}pngx_sessions",
		);

		/**
		 * Filter the list of Plugin Engine table names.
		 *
		 * @since TBD
		 *
		 * @param array<int|string> $tables An array of Plugin Engine table names.
		 */
		$tables = apply_filters( 'pngx_install_get_tables', $tables );

		return $tables;
	}

	/**
	 * Check if custom tables are created.
	 *
	 * @param bool $modify_notice Whether to modify notice based on if all tables are present.
	 * @param bool $execute       Whether to execute get_schema queries as well.
	 *
	 * @return array List of querues.
	 */
	public static function verify_base_tables( $modify_notice = true, $execute = false ) {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		if ( $execute ) {
			self::create_tables();
		}
		$queries        = dbDelta( self::get_schema(), false );
		$missing_tables = array();
		foreach ( $queries as $table_name => $result ) {
			if ( "Created table $table_name" === $result ) {
				$missing_tables[] = $table_name;
			}
		}

		if ( 0 < count( $missing_tables ) ) {
			if ( $modify_notice ) {
				pngx_notice(
					'missing_tables',
					[ Database::class, 'show_base_tables_missing' ]
				);
			}

			update_option( 'pngx_schema_missing_tables', $missing_tables );
		} else {
			if ( $modify_notice ) {
				\Pngx__Admin__Notices::instance()->remove( 'missing_tables' );
			}

			update_option( Pngx__Main::$db_version_key, Pngx__Main::$db_version );
			delete_option( 'pngx_schema_missing_tables' );
		}
		return $missing_tables;
	}

	/**
	 * Drop tables.
	 *
	 * @since TBD
	 */
	public static function drop_tables() {
		global $wpdb;

		$tables = static::get_tables();

		foreach ( $tables as $table ) {
			$wpdb->query( "DROP TABLE IF EXISTS {$table}" );
		}
	}

	/**
	 * Show Base Table Missing Notice.
	 */
	public function show_base_tables_missing() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		// Set to Coupon Creator Options.
		// todo change this to be the pngx options and use the fitler to change for coupon creator and send to the install tab.
		$base_tables_link = esc_url( get_admin_url() ) . 'edit.php?post_type=cctor_coupon&page=coupon-options';

		/**
		 * Filter the base url for missing tables notice to be able to reinstall.
		 *
		 * @since TBD
		 *
		 * @param array<int|string> $tables An array of Plugin Engine table names.
		 */
		$base_tables_link = apply_filters( 'pngx_missing_tables_notice_link', $base_tables_link );

		printf(
			'<div class="error pngx-notice pngx-dependency-error" data-plugin="%1$s"><p>'
			. esc_html__( 'One or more custom tables are missing from your install. Missing tables: %2$s. <a href="%2$s">Check again.</a>', 'plugin-engine' )
			. '</p></div>',
			'plugin-engine',
			get_option( 'pngx_schema_missing_tables' ),
			pngx_sanitize_url( $base_tables_link )
		);
	}
}