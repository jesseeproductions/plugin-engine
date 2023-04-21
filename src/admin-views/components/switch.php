<?php
/**
 * View: Switch Input.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/pngx/admin-views/components/switch.php
 *
 * See more documentation about our views templating system.
 *
 * @since 4.0.0
 *
 * @version 4.0.0
 *
 * @link    https://voltvectors.com/guide/templates/
 *
 * @var string               $label         Label for the switch input.
 * @var string               $id            ID of the switch input.
 * @var array<string,string> $classes_wrap  An array of classes for the switch wrap.
 * @var array<string,string> $classes_input An array of classes for the switch input.
 * @var array<string,string> $classes_label An array of classes for the switch label.
 * @var string               $name          Name attribute for the switch input.
 * @var string|int           $value         The value of the switch.
 * @var string|int           $checked       Whether the switch is enabled or not.
 * @var array<string,string> $attrs         Associative array of attributes of the switch.
 */
$switch_wrap_classes = [ 'pngx-engine', 'pngx-control--switch' ];
if ( ! empty( $classes_wrap ) ) {
	$switch_wrap_classes = array_merge( $switch_wrap_classes, $classes_wrap );
}

$switch_input_classes = [ 'pngx-engine-switch__input' ];
if ( ! empty( $classes_input ) ) {
	$switch_input_classes = array_merge( $switch_input_classes, $classes_input );
}

$switch_label_classes = [ 'pngx-engine-switch__label' ];
if ( ! empty( $classes_label ) ) {
	$switch_label_classes = array_merge( $switch_label_classes, $classes_label );
}
?>
<div
	<?php pngx_classes( $switch_wrap_classes ); ?>
>
	<input
		<?php pngx_classes( $switch_input_classes ); ?>
		id="<?php echo esc_attr( $id ); ?>"
		name="<?php echo esc_attr( $name ); ?>"
		type="checkbox"
		value="<?php echo esc_attr( $value ); ?>"
		<?php checked( true, pngx_is_truthy( $checked ) ); ?>
		<?php pngx_attributes( $attrs ) ?>
	/>

	<label <?php pngx_classes( $switch_label_classes ); ?> for="<?php echo esc_attr( $id ); ?>">
		<span class="screen-reader-text">
			<?php echo esc_html( $label ); ?>
		</span>
	</label>
</div>
