<?php

namespace Research\Project\REST;



interface Post_Repository_Interface {

	/**
	 * Retrieves an array representation of the post.
	 *
	 * @param int $id The post ID.
	 *
	 * @return array An array representation of the post.
	 */
	public function get_data( $id );
}