<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( ! class_exists( 'Pngx__Admin__Support' ) ) {

	class Pngx__Admin__Support {

		public static $support;

		/**
		 * @var Tribe__Support__Obfuscator
		 */
		protected $obfuscator;

		/**
		 * Fields listed here contain HTML and should be escaped before being
		 * printed.
		 *
		 * @var array
		 */
		protected $must_escape = array(
			'tribeEventsAfterHTML',
			'tribeEventsBeforeHTML',
		);

		/**
		 * Field prefixes here should be partially obfuscated before being printed.
		 *
		 * @var array
		 */
		protected $must_obfuscate_prefixes = array(
			'pue_install_key_',
		);


		private function __construct() {
			$this->must_escape = (array) apply_filters( 'tribe_help_must_escape_fields', $this->must_escape );

		}

		public function get_system_info_copy() {
			echo '<div class="system-info-copy" >
						<button data-clipboard-action = "copy" class="system-info-copy-btn" data-clipboard-target = ".pngx-support-stats" >
							<span class="dashicons dashicons-clipboard license-btn" ></span > ' . __( 'Copy to clipboard', 'tribe - common' ) . ' 
						</button >
					</div >';
		}

		/**
		 * Collect system information for support
		 *
		 * @return array of system data for support
		 */
		public function get_support_stats() {
			global $wpdb;
			$user = wp_get_current_user();

			$plugins = array();
			if ( function_exists( 'get_plugin_data' ) ) {
				$plugins_raw = wp_get_active_and_valid_plugins();
				foreach ( $plugins_raw as $k => $v ) {
					$plugin_details = get_plugin_data( $v );
					$plugin         = $plugin_details['Name'];
					if ( ! empty( $plugin_details['Version'] ) ) {
						$plugin .= sprintf( ' version %s', $plugin_details['Version'] );
					}
					if ( ! empty( $plugin_details['Author'] ) ) {
						$plugin .= sprintf( ' by %s', $plugin_details['Author'] );
					}
					if ( ! empty( $plugin_details['AuthorURI'] ) ) {
						$plugin .= sprintf( '(%s)', $plugin_details['AuthorURI'] );
					}
					$plugins[] = $plugin;
				}
			}

			$network_plugins = array();
			if ( is_multisite() && function_exists( 'get_plugin_data' ) ) {
				$plugins_raw = wp_get_active_network_plugins();
				foreach ( $plugins_raw as $k => $v ) {
					$plugin_details = get_plugin_data( $v );
					$plugin         = $plugin_details['Name'];
					if ( ! empty( $plugin_details['Version'] ) ) {
						$plugin .= sprintf( ' version %s', $plugin_details['Version'] );
					}
					if ( ! empty( $plugin_details['Author'] ) ) {
						$plugin .= sprintf( ' by %s', $plugin_details['Author'] );
					}
					if ( ! empty( $plugin_details['AuthorURI'] ) ) {
						$plugin .= sprintf( '(%s)', $plugin_details['AuthorURI'] );
					}
					$network_plugins[] = $plugin;
				}
			}

			$mu_plugins = array();
			if ( function_exists( 'get_mu_plugins' ) ) {
				$mu_plugins_raw = get_mu_plugins();
				foreach ( $mu_plugins_raw as $k => $v ) {
					$plugin = $v['Name'];
					if ( ! empty( $v['Version'] ) ) {
						$plugin .= sprintf( ' version %s', $v['Version'] );
					}
					if ( ! empty( $v['Author'] ) ) {
						$plugin .= sprintf( ' by %s', $v['Author'] );
					}
					if ( ! empty( $v['AuthorURI'] ) ) {
						$plugin .= sprintf( '(%s)', $v['AuthorURI'] );
					}
					$mu_plugins[] = $plugin;
				}
			}

			$keys = apply_filters( 'tribe-pue-install-keys', array() );
			//Obfuscate the License Keys for Security
			if ( is_array( $keys ) && ! empty( $keys ) ) {
				$secure_keys = array();
				foreach ( $keys as $plugin => $license ) {
					$secure_keys[ $plugin ] = preg_replace( '/^(.{4}).*(.{4})$/', '$1' . str_repeat( '#', 32 ) . '$2', $license );
				}
				$keys = $secure_keys;
			}

			//Server
			$server = explode( ' ', $_SERVER['SERVER_SOFTWARE'] );
			$server = explode( '/', reset( $server ) );

			//PHP Information
			$php_info = array();
			$php_vars = array(
				'max_execution_time',
				'memory_limit',
				'upload_max_filesize',
				'post_max_size',
				'display_errors',
				'log_errors',
			);

			foreach ( $php_vars as $php_var ) {
				if ( isset( $wpdb->qm_php_vars ) && isset( $wpdb->qm_php_vars[ $php_var ] ) ) {
					$val = $wpdb->qm_php_vars[ $php_var ];
				} else {
					$val = ini_get( $php_var );
				}
				$php_info[ $php_var ] = $val;
			}

			$systeminfo = array(
				'Home URL'               => get_home_url(),
				'Site URL'               => get_site_url(),
				'Site Language'          => get_option( 'WPLANG' ) ? get_option( 'WPLANG' ) : esc_html__( 'English', 'tribe-common' ),
				'Character Set'          => get_option( 'blog_charset' ),
				'Name'                   => $user->display_name,
				'Email'                  => $user->user_email,
				'Install keys'           => $keys,
				'WordPress version'      => get_bloginfo( 'version' ),
				'PHP version'            => phpversion(),
				'PHP'                    => $php_info,
				'Server'                 => $server[0],
				'SAPI'                   => php_sapi_name(),
				'Plugins'                => $plugins,
				'Network Plugins'        => $network_plugins,
				'MU Plugins'             => $mu_plugins,
				'Theme'                  => wp_get_theme()->get( 'Name' ),
				'Multisite'              => is_multisite(),
				//'Settings'               => Tribe__Settings_Manager::get_options(),
				'WP Timezone'            => get_option( 'timezone_string' ) ? get_option( 'timezone_string' ) : esc_html__( 'Unknown or not set', 'tribe-common' ),
				'WP GMT Offset'          => get_option( 'gmt_offset' ) ? ' ' . get_option( 'gmt_offset' ) : esc_html__( 'Unknown or not set', 'tribe-common' ),
				'Server Timezone'        => date_default_timezone_get(),
				'WP Date Format'         => get_option( 'date_format' ),
				'WP Time Format'         => get_option( 'time_format' ),
				'Week Starts On'         => get_option( 'start_of_week' ),
				'Common Library Dir'     => $GLOBALS['plugin-engine-info']['dir'],
				'Common Library Version' => $GLOBALS['plugin-engine-info']['version'],
			);

			//	if ( $this->rewrite_rules_purged ) {
			//	$systeminfo['rewrite rules purged'] = esc_html__( 'Rewrite rules were purged on load of this help page. Chances are there is a rewrite rule flush occurring in a plugin or theme!', 'tribe-common' );
			//}

			$systeminfo = apply_filters( 'pngx-support-info', $systeminfo );

			return $systeminfo;
		}

		/**
		 * Render system information into a pretty output
		 *
		 * @return string pretty HTML
		 */
		public function formatted_support_stats() {
			$systeminfo = $this->get_support_stats();
			$output     = '';
			$output .= '<dl class="pngx-support-stats">';
			foreach ( $systeminfo as $k => $v ) {

				switch ( $k ) {
					case 'name' :
					case 'email' :
						continue 2;
						break;
					case 'url' :
						$v = sprintf( '<a href="%s">%s</a>', $v, $v );
						break;
				}

				if ( is_array( $v ) ) {
					$keys             = array_keys( $v );
					$key              = array_shift( $keys );
					$is_numeric_array = is_numeric( $key );
					unset( $keys );
					unset( $key );
				}

				$output .= sprintf( '<dt>%s</dt>', $k );
				if ( empty( $v ) ) {
					$output .= '<dd class="support-stats-null">-</dd>';
				} elseif ( is_bool( $v ) ) {
					$output .= sprintf( '<dd class="support-stats-bool">%s</dd>', $v );
				} elseif ( is_string( $v ) ) {
					$output .= sprintf( '<dd class="support-stats-string">%s</dd>', $v );
				} elseif ( is_array( $v ) && $is_numeric_array ) {
					$output .= sprintf( '<dd class="support-stats-array"><ul><li>%s</li></ul></dd>', join( '</li><li>', $v ) );
				} else {
					$formatted_v = array();
					foreach ( $v as $obj_key => $obj_val ) {
						if ( in_array( $obj_key, $this->must_escape ) ) {
							$obj_val = esc_html( $obj_val );
						}

						//$obj_val = $this->obfuscator->obfuscate( $obj_key, $obj_val );

						if ( is_array( $obj_val ) ) {
							$formatted_v[] = sprintf( '<li>%s = <pre>%s</pre></li>', $obj_key, print_r( $obj_val, true ) );
						} else {
							$formatted_v[] = sprintf( '<li>%s = %s</li>', $obj_key, $obj_val );
						}
					}
					$v = join( "\n", $formatted_v );
					$output .= sprintf( '<dd class="support-stats-object"><ul>%s</ul></dd>', print_r( $v, true ) );
				}
			}
			$output .= '</dl>';

			return $output;
		}

		/**
		 * Sets the obfuscator to be used.
		 *
		 * @param Tribe__Support__Obfuscator $obfuscator
		 */
		//public function set_obfuscator( Tribe__Support__Obfuscator $obfuscator ) {
		//	$this->obfuscator = $obfuscator;
		//}

		/****************** SINGLETON GUTS ******************/

		/**
		 * Enforce Singleton Pattern
		 */
		private static $instance;


		public static function getInstance() {
			if ( null == self::$instance ) {
				$instance = new self;
				//$instance->set_obfuscator( new Tribe__Support__Obfuscator( $instance->must_obfuscate_prefixes ) );
				self::$instance = $instance;
			}

			return self::$instance;
		}
	}


}
