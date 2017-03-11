<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


/**
 * Plugin Version Validate License from Plugin Page
 *
 */
class Pngx__Admin__Plugin_License_list {

	private $file;
	private $plugin_name;

	/**
	 * Class constructor
	 *
	 */
	public function __construct( $file, $license_key, $plugin_name, $status_name, $options, $shop_url ) {

		$this->basename = plugin_basename( $file );
		$this->file     = basename( dirname( $file ) );

		$this->license_key = $license_key;
		$this->plugin_name = $plugin_name;
		$this->status_name = $status_name;
		$this->options     = $options;
		$this->shop_url    = $shop_url;

	}

	/**
	 * Display license key field
	 *
	 */
	public function license_key_output() {

		wp_enqueue_style( 'pngx-admin' );
		wp_enqueue_script( 'pngx-license' );

		?>
		<tr id="<?php echo esc_attr( $this->file ); ?>-license-key-row" class="active pngx-license-list-key-wrapper" xmlns="http://www.w3.org/1999/html">
			<td class="plugin-update" colspan="3">

				<div class="pngx-license-list-key-wrap">
					<div id="pngx-loading"><span></span></div>
					<?php

					$license_info = get_option( $this->license_key );
					$value        = isset( $license_info['key'] ) ? $license_info['key'] : '';

					$std = __( 'Enter License Key', 'plugin-engine' );

					?>
					<label
							for="<?php echo esc_attr( $this->license_key ); ?>"
					>
						<?php echo esc_html__( 'License Key', 'plugin-engine' ); ?>
					</label>
					<input
							type="text"
							class="pngx-license-field"
							id="<?php echo esc_attr( $this->license_key ); ?>"
							name="<?php echo esc_attr( $this->license_key ); ?>"
							placeholder="<?php echo esc_attr( $std ); ?>"
							value="<?php echo esc_attr( $value ); ?>"
							size="30"
					/>

					<?php


					$expiration_msg = $expiration_date = '';

					if ( isset( $license_info['expires'] ) ) { // Only Display Expiration if Date
						$expiration_date = strtotime( $license_info['expires'] );
						$expiration_date = date( get_option( 'date_format' ), $expiration_date );
						$expiration_msg  = sprintf( __( ' and Expires on %s', 'plugin-engine' ), esc_attr( $expiration_date ) );
					}

					?>

					<input
							type="hidden"
							class="pngx-license-field pngx_license_key"
							name="pngx_license_key"
							value="<?php echo esc_attr( $this->license_key ); ?>"
					/>

					<input
							type="hidden"
							class="pngx-license-field pngx_license_name"
							name="pngx_license_name"
							value="<?php echo esc_attr( $this->plugin_name ); ?>"
					/>

					<input
							type="hidden"
							class="pngx-license-field pngx_shop_url"
							name="pngx_shop_url"
							value="<?php echo esc_url( $this->shop_url ); ?>"
					/>

					<?php

					if ( isset( $license_info['status'] ) && false !== $license_info['status'] && 'valid' == $license_info['status'] ) {

						?>

						<input
								type="hidden"
								class="pngx-license-field pngx_license_action"
								name="pngx_license_action"
								value="deactivate_license"
						/>

						<div class="pngx-license-field pngx-list-license-button">
							<?php echo __( 'Deactivate License', 'plugin-engine' ); ?>
						</div>

						<span class="pngx-license-field-msg">
							<span class="pngx-success-msg">
								<?php echo esc_html( __( 'License is Active', 'plugin-engine' ) . $expiration_msg ); ?>
							</span>
						</span>

						<?php

					} else {

						if ( isset( $license_info['status'] ) && ( 'invalid' == $license_info['status'] || 'missing' == $license_info['status'] ) && ! $license_info['expired'] ) {
							$license_info_valid = __( 'License is Invalid', 'plugin-engine' );
						} elseif ( isset( $license_info['expired'] ) && 'expired' == $license_info['expired'] ) {
							$license_info_valid = sprintf( __( 'License Expired on %s', 'plugin-engine' ), esc_attr( $expiration_date ) );
						} else {
							$license_info_valid = __( 'License is Not Active', 'plugin-engine' );
						}

						?>

						<input
								type="hidden"
								class="pngx-license-field pngx_license_action"
								name="pngx_license_action"
								value="activate_license"
						/>

						<div class="pngx-license-field pngx-list-license-button">
							<?php echo esc_html__( 'Activate License', 'plugin-engine' ); ?>
						</div>
						<span class="pngx-license-field-msg">
							<span style="pngx-error-msg">
								<?php echo esc_html( $license_info_valid ); ?>
							</span>
						</span>
						<?php

					}

					if ( isset( $this->options[ $this->license_key ]['desc'] ) && "" != $this->options[ $this->license_key ]['desc'] ) {
						?>
						<div class="description">
							<?php echo esc_html( $this->options[ $this->license_key ]['desc'] ); ?>
						</div>
						<?php
					}

					?>
					<div class="pngx-license-field-result-msg">

					</div>
				</div>
			</td>
		</tr>
		<?php
	}

}