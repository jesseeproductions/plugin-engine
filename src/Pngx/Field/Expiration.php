<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
if ( class_exists( 'Pngx__Field__Expiration' ) ) {
	return;
}


/**
 * Class Pngx__Field__Expiration
 * Text Field
 */
class Pngx__Field__Expiration {

	public static function display( $field = array(), $coupon_id = null, $meta = null, $template_fields = array(), $var = array() ) {

		// Set Coupon Expiration Obj
		$coupon_expiration = isset( $var['expiration'] ) ? $var['expiration'] : '';

		if ( ! is_object( $coupon_expiration ) ) {
			return false;
		}

		if ( 1 == $coupon_expiration->get_expiration_option() ) {
			return false;
		}


		$expiration_date = $coupon_expiration->get_display_expiration();
		if ( empty( $expiration_date ) ) {
			return false;
		}

		$class = $field['display']['class'] ? ' class="' . $field['display']['class'] . ' " ' : ' ';
		$style = Pngx__Style__Linked::get_styles( $field, $coupon_id );
		$tags  = isset( $field['display']['tags'] ) ? $field['display']['tags'] : 'title';
		$wrap  = isset( $field['display']['wrap'] ) ? $field['display']['wrap'] : 'div';

		$expires_text = $counter_text = '';

		if ( 'expiration-date' === $field['display']['class'] || 'expiration-counter' === $field['display']['class'] ) {

			if ( 5 == $coupon_expiration->get_expiration_option() ) {

				$start_date = $coupon_expiration->get_display_start();
				//Set Expires On Text
				$custom_expires_text = cctor_options( 'coupon-valid-name' );
				if ( $custom_expires_text ) {
					$expires_text = sprintf( $custom_expires_text, $start_date, $expiration_date );
				} else {
					$expires_text = sprintf( __( 'Valid %s thru %s', 'coupon-creator-pro' ), $start_date, $expiration_date );
				}

			} else {

				//Set Expires On Text
				$custom_expires_text = cctor_options( 'coupon-expires-name' );
				if ( $custom_expires_text ) {
					$expires_text = $custom_expires_text;
				} else {
					$expires_text = __( 'Expires on:', 'coupon-creator-pro' );
				}

				$expires_text = $expires_text . ' ' . $expiration_date;

			}

		}

		if ( 'counter' === $field['display']['class'] || 'expiration-counter' === $field['display']['class'] ) {

			if ( "counter-show" != $coupon_expiration->get_counter_show_status() ) {
				return false;
			}

			if ( 'activate-counter' == $coupon_expiration->get_counter_status() ) {
				$counter_text = '<div class="counter">' . sprintf( esc_html_x( '%d of %d', 'counter views', 'coupon-creator-pro' ), esc_attr( $coupon_expiration->get_coupon_views() ), esc_attr( $coupon_expiration->get_coupon_views_limit() ) ) . '</div>';
			} elseif ( 'unlimited-counter' == $coupon_expiration->get_counter_status() ) {
				$counter_text = '<div class="counter">' . sprintf( esc_html_x( '%d', 'unlimited counter views', 'coupon-creator-pro' ), esc_attr( $coupon_expiration->get_coupon_views() ) ) . '</div>';
			}

		}


		if ( $expires_text || $counter_text ) {
			?>

			<div class="cctor_expiration" <?php echo $class; ?> >
				<?php echo esc_html( $expires_text ) . $counter_text; ?>
			</div>

			<?php
		}

	}

}
