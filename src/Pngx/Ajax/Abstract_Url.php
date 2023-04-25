<?php
/**
 * Plugin Engine AJAX Urls.
 *
 * @since 4.0.0
 */

namespace Pngx\Ajax;

/**
 * Class Abstract_Url
 *
 * @since   4.0.0
 *
 * @package Pngx\Ajax
 */
class Abstract_Url {

	/**
	 * The internal id of the API integration.
	 *
	 * @since 4.0.0
	 *
	 * @var string
	 */
	public static $api_id = '';

	/**
	 * The current Actions handler instance.
	 *
	 * @since 4.0.0
	 *
	 * @var \Pngx\Volt_Vectors\OpenAI\Actions
	 */
	protected $actions;

	/**
	 * Get the admin ajax url with parameters to enable an API action.
	 *
	 * @since 4.0.0
	 *
	 * @param string               $request_slug   The request slug.
	 * @param string               $action         The name of the action to add to the url.
	 * @param string               $nonce          The nonce to verify for the action.
	 * @param array<string|string> $additional_arg An array of arugments to add to the query string of the admin ajax url.
	 *
	 * @return string
	 */
	public function get_admin_ajax_url_with_parameters( string $request_slug, string $action, string $nonce, array $additional_arg ) {
		$args = [
			'action'      => $action,
			$request_slug => $nonce,
			'_ajax_nonce' => $nonce,
		];

		$query_args = array_merge( $args, $additional_arg );

		return add_query_arg( $query_args, admin_url( 'admin-ajax.php' ) );
	}
}
