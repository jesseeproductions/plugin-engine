<?php
/**
 * View: Field wrap for an input.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/pngx/admin-views/components/field.php
 *
 * See more documentation about our views templating system.
 *
 * @since   4.0.0
 *
 * @version 4.0.0
 *
 * @link    https://voltvectors.com/guide/templates/
 *
 * @var string              $id            The ID of the field.
 * @var array<string>       $classes_wrap  The classes for the field wrap.
 * @var string              $label         The label of the field.
 * @var string              $tooltip       The tooltip for the field.
 * @var string              $template_name The name of the template
 * @var array<string|mixed> $template_args The arguments for the template.
 * @var bool                $template_echo Whether to echo the template.
 */
$field_wrap_classes = [ 'pngx-field', 'pngx-field-text' ];
if ( ! empty( $classes_wrap ) ) {
	$field_wrap_classes = array_merge( $field_wrap_classes, $classes_wrap );
}
?>
<fieldset
	<?php echo $id ? 'id="pngx-field-' . esc_attr( $id ) . '"' : ''; ?>
	<?php pngx_classes( $field_wrap_classes ); ?>
>
	<span class="pngx-field-inner-wrap">
		<legend class="pngx-field-label">
			<?php echo esc_html( $label ); ?>
			<?php if ( $tooltip ) :
				$this->template( 'components/tooltip', [ 'message' => $tooltip ] );
			endif; ?>
		</legend>
		<div class="pngx-field-wrap">
			<?php
				$this->template( 'components/' . $template_name, $template_args );
			?>
		</div>
	</span>
</fieldset>
