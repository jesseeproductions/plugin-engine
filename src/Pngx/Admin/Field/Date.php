<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
if ( class_exists( 'Pngx__Admin__Field__Date' ) ) {
	return;
}


/**
 * Class Pngx__Admin__Field__Date
 * Date Field
 */
class Pngx__Admin__Field__Date {

	public static function display( $field = array(), $options = array(), $options_id = null, $meta = null ) {

		if ( isset( $options_id ) && ! empty( $options_id ) ) {
			$name  = $options_id;
			$value = $options[ $field['id'] ];
		} else {
			$name  = $field['id'];
			$value = $meta;
		}

		$size  = isset( $field['size'] ) ? $field['size'] : 10;
		$class = isset( $field['class'] ) ? $field['class'] : '';

		echo '<input type="text" class="pngx-datepicker ' . esc_attr( $class ) . '"  id="' . $field['id'] . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '" size="' . absint( $size ) . '" />';

		if ( '' != $field['desc'] ) {
			echo '<br><span class="description">' . $field['desc'] . '</span>';
		}

		//Blog Time According to WordPress
		$todays_date = "";
		if ( isset( $field['condition'] ) && 'show_current_date' == $field['condition'] ) {
			$blogtime = current_time( 'mysql' );

			list( $today_year, $today_month, $today_day, $hour, $minute, $second ) = preg_split( '([^0-9])', $blogtime );

			if ( 1 == $field['format'] ) {
				$today_first  = $today_day;
				$today_second = $today_month;
			} else {
				$today_first  = $today_month;
				$today_second = $today_day;
			}

			$todays_date = '<br><span class="description">' . __( 'Today\'s Date is ', 'plugin-engine' ) . $today_first . '/' . $today_second . '/' . $today_year . '</span>';
		}

		if ( '' != $todays_date ) {
			echo $todays_date;
		}

	}

}
