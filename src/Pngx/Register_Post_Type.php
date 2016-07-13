<?php
/**
 * Main Plugin Engine class.
 */

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( class_exists( 'Pngx__Main' ) ) {
	return;
}


class Pngx__Register_Post_Type {

	protected $post_type = '';
	protected $title_msg = '';

	public function __construct( $cpt, $title_msg ) {
		if ( ! $cpt ) {
			return;
		}
		$this->post_type = $cpt;
		$this->title_msg = $title_msg;

		add_filter( 'post_updated_messages', array( $this, 'post_update_messages' ) );

		add_filter( 'enter_title_here', array( $this, 'title_placeholders' ) );
	}

	public function post_update_messages( $messages ) {
		global $post, $post_ID;

		$post_types = get_post_types( array( 'show_ui' => true, '_builtin' => false ), 'objects' );

		foreach ( $post_types as $post_type => $post_object ) {

			if ( $this->post_type == $post_type ) {
				$messages[ $post_type ] = array(
					0  => '', // Unused. Messages start at index 1.
					1  => sprintf( __( '%s updated. <a href="%s">View %s</a>' ), $post_object->labels->singular_name, esc_url( get_permalink( $post_ID ) ), $post_object->labels->singular_name ),
					2  => __( 'Custom field updated.' ),
					3  => __( 'Custom field deleted.' ),
					4  => sprintf( __( '%s updated.' ), $post_object->labels->singular_name ),
					5  => isset( $_GET['revision'] ) ? sprintf( __( '%s restored to revision from %s' ), $post_object->labels->singular_name, wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
					6  => sprintf( __( '%s published. <a href="%s">View %s</a>' ), $post_object->labels->singular_name, esc_url( get_permalink( $post_ID ) ), $post_object->labels->singular_name ),
					7  => sprintf( __( '%s saved.' ), $post_object->labels->singular_name ),
					8  => sprintf( __( '%s submitted. <a target="_blank" href="%s">Preview %s</a>' ), $post_object->labels->singular_name, esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ), $post_object->labels->singular_name ),
					9  => sprintf( __( '%s scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview %s</a>' ), $post_object->labels->singular_name, date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ), $post_object->labels->singular_name ),
					10 => sprintf( __( '%s draft updated. <a target="_blank" href="%s">Preview %s</a>' ), $post_object->labels->singular_name, esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ), $post_object->labels->singular_name ),
				);
			}
		}

		return $messages;
	}

	/**
	 * Change Enter Title Here for the Post Type
	 * http://stephanieleary.com/2016/06/wordpress-hidden-gem-enter_title_here-filter/
	 *
	 * @param $placeholder
	 *
	 * @return string
	 */
	public function title_placeholders( $placeholder ) {
		$screen = get_current_screen();
		switch ( $screen->post_type ) {
			case $this->post_type:
				$placeholder = $this->title_msg;
				break;
			default:
				break;

		}

		return $placeholder;
	}

	public static function generate_post_type_labels( $name, $plural_name, $lc_name, $lc_plural_name, $text_domain ) {

		$labels = array(
			'name'                  => sprintf( esc_html__( '%s', $text_domain ), $plural_name ),
			'singular_name'         => sprintf( esc_html__( '%s', $text_domain ), $name ),
			'menu_name'             => sprintf( esc_html__( '%s', $text_domain ), $plural_name ),
			'name_admin_bar'        => sprintf( esc_html__( '%s', $text_domain ), $plural_name ),
			'add_new'               => sprintf( esc_html__( 'Add New', $text_domain ), $name ),
			'add_new_item'          => sprintf( esc_html__( 'Add New %s', $text_domain ), $name ),
			'edit_item'             => sprintf( esc_html__( 'Edit %s', $text_domain ), $name ),
			'new_item'              => sprintf( esc_html__( 'New %s', $text_domain ), $name ),
			'view_item'             => sprintf( esc_html__( 'View %s', $text_domain ), $name ),
			'search_items'          => sprintf( esc_html__( 'Search %s', $text_domain ), $plural_name ),
			'not_found'             => sprintf( esc_html__( 'No %s found', $text_domain ), $lc_plural_name ),
			'not_found_in_trash'    => sprintf( esc_html__( 'No %s found in Trash', $text_domain ), $lc_plural_name ),
			'parent_item_colon'     => sprintf( esc_html__( 'Parent %s', $text_domain ), $name ),
			'all_items'             => sprintf( esc_html__( 'All %s', $text_domain ), $plural_name ),
			'update_item'           => sprintf( esc_html__( 'Update %s', $text_domain ), $name ),
			'archives'              => sprintf( esc_html__( '%s Archives', $text_domain ), $name ),
			'insert_into_item'      => sprintf( esc_html__( 'Insert into %s', $text_domain ), $lc_name ),
			'uploaded_to_this_item' => sprintf( esc_html__( 'Uploaded to this %s', $text_domain ), $lc_name ),
			'items_list'            => sprintf( esc_html__( '%s list', $text_domain ), $plural_name ),
			'items_list_navigation' => sprintf( esc_html__( '%s list navigation', $text_domain ), $plural_name ),
			'filter_items_list'     => sprintf( esc_html__( 'Filter %s list', $text_domain ), $lc_plural_name ),
		);

		return $labels;

	}


	public static function register_post_types( $post_type, $cap_plural, $name, $labels, $slug, $text_domain, $updates ) {

		$args = self::merge_defaults( array(
			'label'               => sprintf( esc_html__( '%s', $text_domain ), $name ),
			'description'         => sprintf( esc_html__( 'Creates a %s Custom Post Type', $text_domain ), $name ),
			'labels'              => $labels,
			'supports'            => '',
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_icon'           => '',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'has_archive'         => false,
			'map_meta_cap'        => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => array( $post_type, $cap_plural ),
			'capabilities'        => array(
				'publish_posts'          => 'publish_' . $cap_plural,
				'edit_post'              => 'edit_' . $post_type,
				'edit_posts'             => 'edit_' . $cap_plural,
				'edit_others_posts'      => 'edit_others_' . $cap_plural,
				'edit_private_posts'     => 'edit_private_' . $cap_plural,
				'edit_published_posts'   => 'edit_published_' . $cap_plural,
				'read_post'              => 'read_' . $post_type,
				'read_private_posts'     => 'read_private_' . $cap_plural,
				'delete_post'            => 'delete_' . $post_type,
				'delete_posts'           => 'delete_' . $cap_plural,
				'delete_private_posts'   => 'delete_private_' . $cap_plural,
				'delete_published_posts' => 'delete_published_' . $cap_plural,
				'delete_others_posts'    => 'delete_others_' . $cap_plural,
			),
			'rewrite'             => array( 'slug' => $slug ),
		), $updates );

		register_post_type( $post_type, $args );

	}

	protected static function merge_defaults( $defualts, $updates ) {

		$updates = (array) $updates;
		$out     = array();
		foreach ( $defualts as $name => $default ) {
			if ( array_key_exists( $name, $updates ) ) {
				$out[ $name ] = $updates[ $name ];
			} else {
				$out[ $name ] = $default;
			}
		}

		return $out;

	}

}