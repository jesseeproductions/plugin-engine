<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


/**
 * Allowed Tags in Fields
 *
 *  Used with strip_tags();
 */
class Pngx__Allowed_Tags {

	/*
	* Allowed Tags for content without links support
	*/
	public static function title() {

		$cctor_terms_tags = '<span><br><b><strong><em><sub><sup><i>';

		return $cctor_terms_tags;

	}

	/*
	* Allowed Tags for content
	*/
	public static function content() {

		$cctor_terms_tags = '<h1><h2><h3><h4><h5><h6><p><blockquote><div><pre><code><span><br><b><strong><em><img><del><ins><sub><sup><ul><ol><li><hr><i><button><caption><cite><datalist><dd><dl><dt><figcaption><figure><a>';

		return $cctor_terms_tags;

	}

	/*
	* Allowed Tags for content without link support
	*/
	public static function content_no_link() {

		$cctor_terms_tags = '<h1><h2><h3><h4><h5><h6><p><blockquote><div><pre><code><span><br><b><strong><em><img><del><ins><sub><sup><ul><ol><li><hr><i><button><caption><cite><datalist><dd><dl><dt><figcaption><figure>';

		return $cctor_terms_tags;

	}

}