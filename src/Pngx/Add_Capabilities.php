<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( class_exists( 'Pngx__Add_Capabilities' ) ) {
	return;
}


/**
 * Class Pngx__Add_Capabilities
 *
 * Adds Capabilities for a CPT
 */
class Pngx__Add_Capabilities {

	/**
	 * constructor
	 */
	public function __construct( $post_type, $cap_plural ) {

		//Administrator
		$caps['administrator'] = array(
			'read_' . $post_type,
			'read_private_' . $cap_plural,
			'edit_' . $post_type,
			'edit_' . $cap_plural,
			'edit_private_' . $cap_plural,
			'edit_published_' . $cap_plural,
			'edit_others_' . $cap_plural,
			'publish_' . $cap_plural,
			'delete_' . $post_type,
			'delete_' . $cap_plural,
			'delete_private_' . $cap_plural,
			'delete_published_' . $cap_plural,
			'delete_others_' . $cap_plural,
		);
		//Editor
		$caps['editor'] = array(
			'read_' . $post_type,
			'read_private_' . $cap_plural,
			'edit_' . $post_type,
			'edit_' . $cap_plural,
			'edit_private_' . $cap_plural,
			'edit_published_' . $cap_plural,
			'edit_others_' . $cap_plural,
			'publish_' . $cap_plural,
			'delete_' . $post_type,
			'delete_' . $cap_plural,
			'delete_private_' . $cap_plural,
			'delete_published_' . $cap_plural,
			'delete_others_' . $cap_plural,
		);
		//Author
		$caps['author'] = array(
			'edit_' . $post_type,
			'read_' . $post_type,
			'delete_' . $post_type,
			'delete_' . $cap_plural,
			'edit_' . $cap_plural,
			'publish_' . $cap_plural,
			'edit_published_' . $cap_plural,
			'delete_published_' . $cap_plural,
		);
		//Contributor
		$caps['contributor'] = array(
			'edit_' . $post_type,
			'read_' . $post_type,
			'delete_' . $post_type,
			'delete_' . $cap_plural,
			'edit_' . $cap_plural,

		);
		//Subscriber
		$caps['subscriber'] = array(
			'read_' . $post_type,
		);

		$roles = array(
			get_role( 'administrator' ),
			get_role( 'editor' ),
			get_role( 'author' ),
			get_role( 'contributor' ),
			get_role( 'subscriber' ),
		);

		//Add Capabilities to Role if Role Exists
		foreach ( $roles as $role ) {

			$role_check = '';
			if ( is_object( $role ) ) {
				$role_check = get_role( $role->name );
			}

			if ( ! empty( $role_check ) ) {
				foreach ( $caps[ $role->name ] as $cap ) {
					$role->add_cap( $cap );
				}
			}
		}

		//Set Option to Prevent this from Running Again
		update_option( $post_type . '_capabilities_register', date( 'l jS \of F Y h:i:s A' ) );

	}

}