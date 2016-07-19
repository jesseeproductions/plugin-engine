<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
if ( class_exists( 'Pngx__Admin__Help' ) ) {
	return;
}


/**
 * Class Pngx__Admin__Help
 * Help Class to display videos and text on meta and option tabs
 */
class Pngx__Admin__Help {

//TODO Set abstract property for fields
//TODO set abstract method set_help_fields
//todo remove all instances of cctor in pngx_admin_help

	//Help Fields
	protected $fields = array();

	public function get_options() {

		return $this->fields;

	}

	/**
	 * Find string in multidimensional array
	 *
	 * Thanks to jwueller http://stackoverflow.com/a/4128377
	 *
	 * @param            $needle
	 * @param            $haystack
	 * @param bool|false $strict
	 *
	 * @return bool
	 */
	public function in_array_r( $needle, $haystack, $strict = false ) {
		foreach ( $haystack as $item ) {
			if ( ( $strict ? $item === $needle : $item == $needle ) || ( is_array( $item ) && $this->in_array_r( $needle, $item, $strict ) ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Display Help Fields in Tab
	 *
	 * @param null $section
	 */
	public function display_help( $section = null ) {

		if ( ! $section ) {
			return;
		}

		if ( 'all' != $section && ! $this->in_array_r( $section, $this->fields ) ) {
			return;
		}

		$screen = get_current_screen();

		if ( 'all' != $section ) {

			if ( 'cctor_coupon_page_coupon-options' == $screen->id ) {
				echo '</td></tr><tr valign="top"><td colspan="2">';
			}

			echo '<div class="cctor-meta-field-wrap cctor-section-help-container">';


			echo '<button aria-expanded="false" class="cctor-section-help-container-toggle" type="button">
					<span class="dashicons-before dashicons-editor-help">Help</span>
					<span class="dashicons toggle__arrow dashicons-arrow-down"></span>
				</button>';

			echo '<div class="cctor-section-help-slideout">';

			echo '<div class="cctor-meta-field-content video">';
			echo '<h4>' . __( 'Video Guides', 'coupon-creator' ) . '</h4>';
			echo '<ul>';
			foreach ( $this->fields as $help_field ) {

				if ( isset( $help_field['type'] ) && 'video' == $help_field['type'] ) {

					if ( $section == $help_field['tab'] || $section == $help_field['section'] ) {
						$this->help_fields_switch( $help_field, $section );
					}
				}

			}
			echo '</ul></div>';

			echo '<div class="cctor-meta-field-content text">';
			echo '<h4>' . __( 'Guides', 'coupon-creator' ) . '</h4>';
			echo '<ul>';
			foreach ( $this->fields as $help_field ) {

				if ( isset( $help_field['type'] ) && 'links' == $help_field['type'] ) {

					if ( $section == $help_field['tab'] || $section == $help_field['section'] ) {
						$this->help_fields_switch( $help_field, $section );
					}
				}

			}
			echo '</ul></div>';

			echo '</div></div>';

			if ( 'cctor_coupon_page_coupon-options' == $screen->id ) {
				echo '</td></tr>';
			}

		} else {

			//All Fields Display for Help Tabs
			foreach ( $this->fields as $help_field ) {

				if ( isset( $help_field['type'] ) ) {

					if ( 'all' == $section ) {
						$this->help_fields_switch( $help_field, $section );
					}

				}
			}
		}

	}

	protected function help_fields_switch( $help_field = array(), $section = null ) {

		switch ( $help_field['type'] ) {

			case 'heading':
				?>

				<h4 class="coupon-heading"><?php echo esc_html( $help_field['text'] ); ?></h4>
				<ul>
				<?php break;

			case 'end_list':
				?>
				</ul>
				<?php break;

			case 'video':
				$rel = '';
				if ( 'all' == $section ) {
					$rel = 'how_to_videos';
				}
				$pro = '';
				if ( isset( $help_field['pro'] ) ) {
					$pro = '<sup class="pro-help-sup">' . esc_html( $help_field['pro'] ) . '</sup>';
				}
				?>
				<li><a class="cctor-support youtube_colorbox"
				       href="http://www.youtube.com/embed/<?php echo esc_html( $help_field['video_id'] ); ?>?hd=1&autohide=1&rel=0&showsearch=0&autoplay=1"
				       rel="<?php echo esc_attr( $rel ); ?>"><?php echo esc_html( $help_field['text'] ); ?></a><?php echo $pro; ?>
				</li>

				<?php break;

			case 'links':
				$pro = '';
				if ( isset( $help_field['pro'] ) ) {
					$pro = '<sup class="pro-help-sup">' . esc_html( $help_field['pro'] ) . '</sup>';
				}
				?>
				<li><a class="cctor-support" target="_blank"
				       href="<?php echo esc_url( $help_field['link'] ); ?>"><?php echo esc_html( $help_field['text'] ); ?></a><?php echo $pro; ?>
				</li>
				<?php break;

		}

	}
}