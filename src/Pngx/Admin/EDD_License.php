<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


/**
 * License Methods
 *
 */
class Pngx__Admin__EDD_License {

	protected $update_url = '';
	protected $options_id = Pngx__Main::OPTIONS_ID;
	protected $license    = '';

	public function __construct( $shop_url, $options_id, $license ) {

		$this->update_url = trailingslashit( $shop_url );

		$this->options_id = $options_id;

		$this->license = $license;

	}

	/**
	 * Get the update API endpoint url
	 *
	 * @return string
	 */
	public function get_update_url() {
		return apply_filters( 'pngx_update_url', $this->update_url );
	}

	/*
	* Register and Enqueue Style and Scripts on Options Screens
	*
	*/
	public function activate_license() {

		// listen for our activate button to be clicked
		if ( isset( $_POST[ 'pngx_license_activate_' . $this->license ] ) ) {

			// run a quick security check
			if ( ! check_admin_referer( 'pngx_license_nonce_' . $this->license, 'pngx_license_nonce_' . $this->license ) ) {
				return false; // get out if we didn't click the Activate button
			}

			if ( $_POST[ 'pngx_license_key_' . $this->license ] != $this->license ) {
				return false; //not this plugins license
			}

			//Set WordPress Option Name
			$license_option_name = esc_attr( $_POST[ 'pngx_license_key_' . $this->license ] );

			// retrieve the license from the database
			$license_info = get_option( $license_option_name );

			//Check if the License has changed and deactivate
			if ( $_POST[ $this->options_id ][ $license_option_name ] != $license_info['key'] ) {

				$license_info['key'] = esc_attr( trim( $_POST[ $this->options_id ][ $license_option_name ] ) );

				delete_option( $license_option_name );

				update_option( $license_option_name, $license_info );

			}

			// data to send in our API request
			$api_params = array(
				'edd_action' => 'activate_license',
				'license'    => esc_attr( trim( $license_info['key'] ) ),
				'item_name'  => urlencode( esc_attr( $_POST[ 'pngx_license_name_' . $this->license ] ) ), // the name of our product in EDD
				'url'        => home_url()
			);

			// Call the custom API.
			$response = wp_remote_get( esc_url_raw( add_query_arg( $api_params, self::get_update_url() ) ), array(
				'timeout'   => 15,
				'sslverify' => false
			) );

			// make sure the response came back okay
			if ( is_wp_error( $response ) ) {
				return false;
			}
			// decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			//Remove Current Expiration
			$license_info['status'] = "nostatus";

			//Get Status of Key
			$license_info['status'] = esc_html( $license_data->license );

			//Remove Current Expiration
			unset( $license_info['expires'] );

			//Set Expiration Date  for This License
			$license_info['expires'] = esc_html( $license_data->expires );

			//if Expired Add that to the option.
			if ( isset( $license_data->error ) && "expired" == $license_data->error ) {
				$license_info['expired'] = esc_html( $license_data->error );
			}

			//if Expired Add that to the option.
			if ( isset( $license_data->error ) && "missing" == $license_data->error ) {
				unset( $license_info['expires'] );
				unset( $license_info['expired'] );
				$license_info['status'] = esc_html( $license_data->error );
			}

			//Update License Object
			update_option( $license_option_name, $license_info );

		}

		return true;
	}

	/*
	* Deactivate a license key.
	*
	*/
	public function deactivate_license() {

		// listen for our activate button to be clicked
		if ( isset( $_POST[ 'pngx_license_deactivate_' . $this->license ] ) ) {

			// run a quick security check
			if ( ! check_admin_referer( 'pngx_license_nonce_' . $this->license, 'pngx_license_nonce_' . $this->license ) ) {
				return false; // get out if we didn't click the Activate button
			}

			if ( $_POST[ 'pngx_license_key_' . $this->license ] != $this->license ) {
				return false; //not this plugins license
			}

			$license_option_name = esc_attr( $_POST[ 'pngx_license_key_' . $this->license ] );

			// retrieve the license from the database
			$license_info = get_option( $license_option_name );

			// data to send in our API request
			$api_params = array(
				'edd_action' => 'deactivate_license',
				'license'    => esc_attr( trim( $license_info['key'] ) ),
				'item_name'  => urlencode( esc_attr( $_POST[ 'pngx_license_name_' . $this->license ] ) ),
				// the name of our product in EDD
				'url'        => home_url()
			);

			// Call the custom API.
			$response = wp_remote_get( esc_url_raw( add_query_arg( $api_params, self::get_update_url() ) ), array(
				'timeout'   => 15,
				'sslverify' => false
			) );

			// make sure the response came back okay
			if ( is_wp_error( $response ) ) {
				return false;
			}

			// decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			// $license_data->license will be either "deactivated" or "failed"
			if ( 'deactivated' == $license_data->license || 'failed' == $license_data->license ) {

				unset( $license_info['status'] );
				unset( $license_info['expires'] );

				//Update License Object
				update_option( $license_option_name, $license_info );
			}

		}

		return true;
	}
}