<?php
/**
 * View: Read only field.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/pngx/admin-views/components/read-only.php
 *
 * See more documentation about our views templating system.
 *
 * @since   4.0.0
 *
 * @version 4.0.0
 *
 * @link    https://pngx.ink/RYIOh
 *
 * @var array<string,string> $classes_wrap    An array of classes for the read only field.
 * @var array<string,string> $classes_input   An array of classes for the input.
 * @var string               $label           The label for the hidden input.
 * @var string               $screen_reader   The screen reader instructions for the text input.
 * @var string               $id              ID of the hidden input.
 * @var string               $name            The name for the hidden input.
 * @var string               $value           The value of the ready only field.
 * @var string               $display_value   The display value of the ready only field.
 * @var string               $repeater_fields The display value of the ready only field.
 * @var Template             $template        An instance of the admin template
 */

use Pngx\Admin\Field\V2\Repeater;

?>
<div class="repeater-container">
	<div data-repeater-list="<?php echo esc_attr( $name ); ?>">
		<?php foreach ( $value as $index => $item ) : ?>
			<div data-repeater-item>
				<?php foreach ( $repeater_fields as $repeater_field ) : ?>
					<?php
					$repeater_field_name  = "{$name}[{$index}][{$repeater_field['id']}]";
					$repeater_field_value = isset( $item[ $repeater_field['id'] ] ) ? $item[ $repeater_field['id'] ] : '';
					echo Repeater::display_repeater_field( $repeater_field, $repeater_field_name, $repeater_field_value, $this );
					?>
				<?php endforeach; ?>
				<button data-repeater-delete type="button">Delete</button>
			</div>
		<?php endforeach; ?>
		<div data-repeater-item style="display: none;">
			<?php foreach ( $repeater_fields as $repeater_field ) : ?>
				<?php
				$repeater_field_name  = "{$name}[{{{index}}}][{$repeater_field['id']}]";
				$repeater_field_value = '';
				echo Repeater::display_repeater_field( $repeater_field, $repeater_field_name, $repeater_field_value, $this );
				?>
			<?php endforeach; ?>
			<button data-repeater-delete type="button">Delete</button>
		</div>
	</div>
	<button data-repeater-create type="button">Add</button>
</div>

<style>
	.repeater-container > [data-repeater-item]:first-child {
		display: none;
	}
</style>

<script>
	jQuery( document ).ready( function( $ ) {
		$( '.repeater-container' ).repeater( {
			initEmpty: false,
			defaultValues: {},
			show: function() {
				$( this ).slideDown( function() {
					// Initialize dropdowns in the new row
					$( this ).find( '.pngx-dropdown' ).pngx_dropdowns();

					// Trigger dependencies in the new row
					$( this ).find( '.pngx-dependency' ).trigger( 'change' );
					$( document ).trigger( 'pngx.dependencies-run' );
				} );
			},
			hide: function( deleteElement ) {
				$( this ).slideUp( deleteElement );
			}
		} );
	} );
</script>