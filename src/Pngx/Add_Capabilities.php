<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class Pngx__Add_Capabilities
 *
 * Adds Capabilities for a CPT
 */
class Pngx__Add_Capabilities {
	/**
	 * Get Defualt Roles.
	 *
	 * @since 4.0.0
	 *
	 * @return array<string> $roles An array of default roles.
	 */
	public function get_default_roles() {
		$roles = [
			get_role( 'administrator' ),
			get_role( 'editor' ),
			get_role( 'author' ),
			get_role( 'contributor' ),
			get_role( 'subscriber' ),
		];

		return $roles;
	}

	/**
	 * Remove Role Capabilities.
	 *
	 * @since 4.0.0
	 *
	 * @param string $capability_type A capability type to remove.
	 *
	 * @return array<string> $caps An array of capability types by role.
	 */
	public function get_capabilities( $capability_type ) {
		$caps = [];

		//Administrator
		$caps['administrator'] = array(
			"read_{$capability_type}",
			"read_private_{$capability_type}s",
			"edit_{$capability_type}",
			"edit_{$capability_type}s",
			"edit_private_{$capability_type}s",
			"edit_published_{$capability_type}s",
			"edit_others_{$capability_type}s",
			"publish_{$capability_type}s",
			"delete_{$capability_type}",
			"delete_{$capability_type}s",
			"delete_private_{$capability_type}s",
			"delete_published_{$capability_type}s",
			"delete_others_{$capability_type}s",
		);
		//Editor
		$caps['editor'] = array(
			"read_{$capability_type}",
			"read_private_{$capability_type}s",
			"edit_{$capability_type}",
			"edit_{$capability_type}s",
			"edit_private_{$capability_type}s",
			"edit_published_{$capability_type}s",
			"edit_others_{$capability_type}s",
			"publish_{$capability_type}s",
			"delete_{$capability_type}",
			"delete_{$capability_type}s",
			"delete_private_{$capability_type}s",
			"delete_published_{$capability_type}s",
			"delete_others_{$capability_type}s",
		);
		//Author
		$caps['author'] = array(
			"edit_{$capability_type}",
			"read_{$capability_type}",
			"delete_{$capability_type}",
			"delete_{$capability_type}s",
			"edit_{$capability_type}s",
			"publish_{$capability_type}s",
			"edit_published_{$capability_type}s",
			"delete_published_{$capability_type}s",
		);
		//Contributor
		$caps['contributor'] = array(
			"edit_{$capability_type}",
			"read_{$capability_type}",
			"delete_{$capability_type}",
			"delete_{$capability_type}s",
			"edit_{$capability_type}s",

		);
		//Subscriber
		$caps['subscriber'] = array(
			"read_{$capability_type}",
		);

		return $caps;
	}

	/**
	 * Add Role Capabilities.
	 *
	 * @since 4.0.0
	 *
	 * @param string $capability_type A capability type to remove.
	 */
	public function add_capabilities( $capability_type ) {
		$caps = $this->get_capabilities( $capability_type );
		$roles = $this->get_default_roles();

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
		update_option( $capability_type . '_capabilities_register', date( 'l jS \of F Y h:i:s A' ) );
	}

	/**
	 * Remove Role Capabilities.
	 *
	 * @since 4.0.0
	 *
	 * @param string $capability_type A capability type to remove.
	 */
	public function remove_capabilities( $capability_type ) {
		$caps = $this->get_capabilities( $capability_type );
		$roles = $this->get_default_roles();

		//Remove Capabilities to Role if Role Exists
		foreach ( $roles as $role ) {
			$role_check = '';
			if ( is_object( $role ) ) {
				$role_check = get_role( $role->name );
			}

			if ( ! empty( $role_check ) ) {
				foreach ( $caps[ $role->name ] as $cap ) {
					$role->remove_cap( $cap );
				}
			}
		}
	}
}