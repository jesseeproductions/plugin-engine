<?php

class Pngx__REST__Post_Repository {

	/**
	 * Returns the data representing an image object.
	 *
	 * @since 4.7.19
	 *
	 * @param int $image_id
	 *
	 * @return array|false An array of image information or `false` on failure.
	 */
	protected function get_image_data( $image_id ) {
		$full_url = get_attachment_link( $image_id );
		$file     = get_attached_file( $image_id );

		$data = array(
			'url'       => $full_url,
			'id'        => $image_id,
			'extension' => pathinfo( $file, PATHINFO_EXTENSION ),
		);

		$metadata = wp_get_attachment_metadata( $image_id );

		if (
			false !== $metadata
			&& isset( $metadata['image_meta'], $metadata['file'], $metadata['sizes'] )
		) {
			unset( $metadata['image_meta'], $metadata['file'] );

			foreach ( $metadata['sizes'] as $size => &$meta ) {
				$size_image_src = wp_get_attachment_image_src( $image_id, $size );
				$meta['url']    = ! empty( $size_image_src[0] ) ? $size_image_src[0] : '';
				unset( $meta['file'] );
			}
			unset( $meta );

			$data = array_filter( array_merge( $data, $metadata ) );
		}

		return $data;
	}
}