<?php

namespace Pngx\Dialog;

/**
 * Class View
 *
 * @since TBD
 */
class View extends \Pngx__Template {
	/**
	 * Where in the themes we will look for templates.
	 *
	 * @since TBD
	 *
	 * @var string
	 */
	public $template_namespace = 'dialogs';

	/**
	 * View constructor
	 *
	 * @since TBD
	 */
	public function __construct() {
		$this->set_template_origin( \Pngx__Main::instance() );
		$this->set_template_folder( 'src/views/dialog' );

		// Configures this templating class to extract variables.
		$this->set_template_context_extract( true );

		// Uses the public folders.
		$this->set_template_folder_lookup( true );
	}

	/**
	 * Public wrapper for build method.
	 * Contains all the logic/validation checks.
	 *
	 * @since TBD
	 *
	 * @param string  $content Content as an HTML string.
	 * @param array   $args    {
	 *     List of arguments to override dialog template.
	 *
	 *     @type string  $button_id               The ID for the trigger button (optional).
	 *     @type array   $button_classes          Any desired classes for the trigger button (optional).
	 *     @type string  $button_text             The text for the dialog trigger button ("Open the dialog window").
	 *     @type string  $button_type             The type for the trigger button (optinoal).
	 *     @type string  $button_value            The value for the trigger button (optional).
	 *     @type string  $close_event             The dialog close event hook name (`pngx_dialog_close_dialog`).
	 *     @type string  $content_classes         The dialog content classes ("pngx-dialog__content").
	 *     @type array   $context                 Any additional context data you need to expose to this file (optional).
	 *     @type string  $id                      The unique ID for this dialog (`uniqid()`).
	 *     @type string  $show_event              The dialog event show hook name (`pngx_dialog_show_dialog`).
	 *     @type string  $template                The dialog template name (dialog).
	 *     @type string  $title                   The dialog title (optional).
	 *     @type string  $trigger_classes         Classes for the dialog trigger ("pngx_dialog_trigger").
	 *
	 *     Dialog script option overrides.
	 *
	 *     @type string  $append_target           The dialog will be inserted after the button, you could supply a selector string here to override (optional).
	 *     @type boolean $body_lock               Whether to lock the body while dialog open (false).
	 *     @type string  $close_button_aria_label Aria label for the close button ("Close this dialog window").
	 *     @type string  $close_button_classes    Classes for the close button ("pngx-dialog__close-button").
	 *     @type string  $content_wrapper_classes Dialog content wrapper classes. This wrapper includes the close button ("pngx-dialog__wrapper").
	 *     @type string  $effect                  CSS effect on open. none or fade (optional).
	 *     @type string  $effect_easing           A css easing string to apply ("ease-in-out").
	 *     @type int     $effect_speed            CSS effect speed in milliseconds (optional).
	 *     @type string  $overlay_classes         The dialog overlay classes ("pngx-dialog__overlay").
	 *     @type boolean $overlay_click_closes    If clicking the overlay closes the dialog (false).
	 *     @type string  $wrapper_classes         The wrapper class for the dialog ("pngx-dialog").
	 * }
	 * @param string  $id      The unique ID for this dialog. Gets prepended to the data attributes. Generated if not passed (`uniqid()`).
	 * @param boolean $echo    Whether to echo the script or to return it (default: true).
	 *
	 * @return string An HTML string of the dialog.
	 */
	public function render_dialog( $content, $args = [], $id = null, $echo = true ) {
		// Check for content to be passed.
		if ( empty( $content ) ) {
			return '';
		}

		// Generate an ID if we weren't passed one.
		if ( is_null( $id ) ) {
			$id = \uniqid();
		}

		/** @var \Pngx__Assets $assets */
		$assets = pngx( 'pngx.assets' );
		$assets->enqueue_group( 'pngx-dialog' );

		$html = $this->build_dialog( $content, $id, $args );

		if ( ! $echo ) {
			return $html;
		}

		echo $html;
	}

	/**
	 * Syntactic sugar for `render_dialog()` to make creating modals easier.
	 * Adds sensible defaults for modals.
	 *
	 * @since TBD
	 *
	 * @param string  $content Content as an HTML string.
	 * @param array   $args    {
	 *     List of arguments to override dialog template.
	 *
	 *     @type string  $button_id               The ID for the trigger button (optional).
	 *     @type array   $button_classes          Any desired classes for the trigger button (optional).
	 *     @type string  $button_text             The text for the dialog trigger button ("Open the modal window").
	 *     @type string  $button_type             The type for the trigger button (optional).
	 *     @type string  $button_value            The value for the trigger button (optional).
	 *     @type string  $close_event             The dialog close event hook name (`pngx_dialog_close_modal`).
	 *     @type string  $content_classes         The dialog content classes ("pngx-dialog__content pngx-modal__content").
	 *     @type string  $title_classes           The dialog title classes ("pngx-dialog__title pngx-modal__title").
	 *     @type array   $context                 Any additional context data you need to expose to this file (optional).
	 *     @type string  $id                      The unique ID for this dialog (`uniqid()`).
	 *     @type string  $show_event              The dialog event hook name (`pngx_dialog_show_modal`).
	 *     @type string  $template                The dialog template name (modal).
	 *     @type string  $title                   The dialog title (optional).
	 *     @type string  $trigger_classes         Classes for the dialog trigger ("pngx_dialog_trigger").
	 *
	 *     Dialog script option overrides.
	 *
	 *     @type string  $append_target           The dialog will be inserted after the button, you could supply a selector string here to override ("body").
	 *     @type boolean $body_lock               Whether to lock the body while dialog open (true).
	 *     @type string  $close_button_aria_label Aria label for the close button ("Close this modal window").
	 *     @type string  $close_button_classes    Classes for the close button ("pngx-dialog__close-button pngx-modal__close-button").
	 *     @type string  $content_wrapper_classes Dialog content wrapper classes. This wrapper includes the close button ("pngx-dialog__wrapper pngx-modal__wrapper").
	 *     @type string  $effect                  CSS effect on open. none or fade ("fade").
	 *     @type string  $effect_easing           A css easing string to apply ("ease-in-out").
	 *     @type int     $effect_speed            CSS effect speed in milliseconds (300).
	 *     @type string  $overlay_classes         The dialog overlay classes ("pngx-dialog__overlay pngx-modal__overlay").
	 *     @type boolean $overlay_click_closes    If clicking the overlay closes the dialog (true).
	 *     @type string  $wrapper_classes         The wrapper class for the dialog ("pngx-dialog").
	 * }
	 * @param string  $id      The unique ID for this dialog. Gets prepended to the data attributes. Generated if not passed (`uniqid()`).
	 * @param boolean $echo    Whether to echo the script or to return it (default: true).
	 *
	 * @return string An HTML string of the dialog.
	 */
	public function render_modal( $content, $args = [], $id = null, $echo = true ) {
		$default_args = [
			'append_target'           => '',
			'body_lock'               => true,
			'button_text'             => __( 'Open the modal window', 'pngx-common' ),
			'close_button_aria_label' => __( 'Close this modal window', 'pngx-common' ),
			'close_button_classes'    => 'pngx-dialog__close-button pngx-modal__close-button',
			'close_event'             => 'pngx_dialog_close_modal',
			'content_classes'         => 'pngx-dialog__content pngx-modal__content',
			'content_wrapper_classes' => 'pngx-dialog__wrapper pngx-modal__wrapper',
			'effect'                  => 'fade',
			'effect_speed'            => 300,
			'overlay_classes'         => 'pngx-dialog__overlay pngx-modal__overlay',
			'overlay_click_closes'    => true,
			'show_event'              => 'pngx_dialog_show_modal',
			'template'                => 'modal',
			'title_classes'           => [ 'pngx-dialog__title', 'pngx-modal__title' ],
		];

		$args = wp_parse_args( $args, $default_args );

		$this->render_dialog( $content, $args, $id, $echo );
	}

	/**
	 * Syntactic sugar for `render_dialog()` to make creating custom confirmation dialogs easier.
	 * Adds sensible defaults for confirmation dialogs.
	 *
	 * @since TBD
	 *
	 * @param string  $content Content as an HTML string.
	 * @param array   $args    {
	 *     List of arguments to override dialog template.
	 *
	 *     @type string  $button_id               The ID for the trigger button (optional).
	 *     @type array   $button_classes          Any desired classes for the trigger button (optional).
	 *     @type string  $button_text             The text for the dialog trigger button ("Open the dialog window").
	 *     @type string  $button_type             The type for the trigger button (optional).
	 *     @type string  $button_value            The value for the trigger button (optional).
	 *     @type string  $cancel_button_text      Text for the "Cancel" button ("Cancel").
	 *     @type string  $content_classes         The dialog content classes ("pngx-dialog__content pngx-confirm__content").
	 *     @type string  $continue_button_text    Text for the "Continue" button ("Confirm").
	 *     @type array   $context                 Any additional context data you need to expose to this file (optional).
	 *     @type string  $id                      The unique ID for this dialog (`uniqid()`).
	 *     @type string  $template                The dialog template name (confirm).
	 *     @type string  $title                   The dialog title (optional).
	 *     @type string  $trigger_classes         Classes for the dialog trigger ("pngx_dialog_trigger").
	 *
	 *     Dialog script option overrides.
	 *
	 *     @type string  $append_target           The dialog will be inserted after the button, you could supply a selector string here to override (optional).
	 *     @type boolean $body_lock               Whether to lock the body while dialog open (true).
	 *     @type string  $close_button_aria_label Aria label for the close button (optional).
	 *     @type string  $close_button_classes    Classes for the close button ("pngx-dialog__close-button--hidden").
	 *     @type string  $close_event             The dialog close event hook name (`pngx_dialog_close_confirm`).
	 *     @type string  $content_wrapper_classes Dialog content wrapper classes. This wrapper includes the close button ("pngx-dialog__wrapper pngx-confirm__wrapper").
	 *     @type string  $effect                  CSS effect on open. none or fade (optional).
	 *     @type string  $effect_easing           A css easing string to apply ("ease-in-out").
	 *     @type int     $effect_speed            CSS effect speed in milliseconds (optional).
	 *     @type string  $overlay_classes         The dialog overlay classes ("pngx-dialog__overlay pngx-confirm__overlay").
	 *     @type boolean $overlay_click_closes    If clicking the overlay closes the dialog (false).
	 *     @type string  $show_event              The dialog event hook name (`pngx_dialog_show_confirm`).
	 *     @type string  $wrapper_classes         The wrapper class for the dialog ("pngx-dialog").
	 * }
	 * @param string  $id      The unique ID for this dialog. Gets prepended to the data attributes. Generated if not passed (`uniqid()`).
	 * @param boolean $echo    Whether to echo the script or to return it (default: true).
	 *
	 * @return string An HTML string of the dialog.
	 */
	public function render_confirm( $content, $args = [], $id = null, $echo = true ) {
		$default_args = [
			'body_lock'               => true,
			'cancel_button_text'      => __( 'Cancel', 'pngx-common' ),
			'continue_button_text'    => __( 'Confirm', 'pngx-common' ),
			'close_button_aria_label' => '',
			'close_button_classes'    => 'pngx-dialog__close-button--hidden',
			'close_event'             => 'pngx_dialog_close_confirm',
			'content_classes'         => 'pngx-dialog__content pngx-confirm__content',
			'content_wrapper_classes' => 'pngx-dialog__wrapper pngx-confirm__wrapper',
			'overlay_classes'         => 'pngx-dialog__overlay pngx-confirm__overlay',
			'show_event'              => 'pngx_dialog_show_confirm',
			'template'                => 'confirm',
			'title_classes'           => [ 'pngx-dialog__title', 'pngx-confirm__title' ],
		];

		$args = wp_parse_args( $args, $default_args );

		$this->render_dialog( $content, $args, $id, $echo );
	}

	/**
	 * Syntactic sugar for `render_dialog()` to make creating custom alerts easier.
	 * Adds sensible defaults for alerts.
	 *
	 * @since TBD
	 *
	 * @param string  $content Content as an HTML string.
	 * @param array   $args    {
	 *     List of arguments to override dialog template.
	 *
	 *     @type string  $alert_button_text       Text for the "OK" button ("OK").
	 *     @type string  $button_id               The ID for the trigger button (optional).
	 *     @type array   $button_classes          Any desired classes for the trigger button (optional).
	 *     @type string  $button_text             The text for the dialog trigger button ("Open the dialog window").
	 *     @type string  $button_type             The type for the trigger button (optional).
	 *     @type string  $button_value            The value for the trigger button (optional).
	 *     @type string  $content_classes         The dialog content classes ("pngx-dialog__content pngx-alert__content").
	 *     @type string  $title_classes           The dialog title classes ("pngx-dialog__title pngx-alert__title").
	 *     @type array   $context                 Any additional context data you need to expose to this file (optional).
	 *     @type string  $id                      The unique ID for this dialog (`uniqid()`).
	 *     @type string  $template                The dialog template name (alert).
	 *     @type string  $title                   The dialog title (optional).
	 *     @type string  $trigger_classes         Classes for the dialog trigger ("pngx_dialog_trigger").
	 *
	 *     Dialog script option overrides.
	 *
	 *     @type string  $append_target           The dialog will be inserted after the button, you could supply a selector string here to override (optional).
	 *     @type boolean $body_lock               Whether to lock the body while dialog open (true).
	 *     @type string  $close_button_aria_label Aria label for the close button (optional).
	 *     @type string  $close_button_classes    Classes for the close button ("pngx-dialog__close-button--hidden").
	 *     @type string  $close_event             The dialog close event hook name (`pngx_dialog_close_alert`).
	 *     @type string  $content_wrapper_classes Dialog content wrapper classes. This wrapper includes the close button ("pngx-dialog__wrapper pngx-alert__wrapper").
	 *     @type string  $effect                  CSS effect on open. none or fade (optional).
	 *     @type string  $effect_easing           A css easing string to apply ("ease-in-out").
	 *     @type int     $effect_speed            CSS effect speed in milliseconds (optional).
	 *     @type string  $overlay_classes         The dialog overlay classes ("pngx-dialog__overlay pngx-alert__overlay").
	 *     @type boolean $overlay_click_closes    If clicking the overlay closes the dialog (false).
	 *     @type string  $show_event              The dialog event hook name (`pngx_dialog_show_alert`).
	 *     @type string  $wrapper_classes         The wrapper class for the dialog ("pngx-dialog").
	 * }
	 * @param string  $id      The unique ID for this dialog. Gets prepended to the data attributes. Generated if not passed (`uniqid()`).
	 * @param boolean $echo    Whether to echo the script or to return it (default: true).
	 *
	 * @return string An HTML string of the dialog.
	 */
	public function render_alert( $content, $args = [], $id = null, $echo = true ) {
		$default_args = [
			'alert_button_text'       => __( 'OK', 'pngx-common' ),
			'body_lock'               => true,
			'close_button_aria_label' => '',
			'close_button_classes'    => 'pngx-dialog__close-button--hidden',
			'close_event'             => 'pngx_dialog_close_alert',
			'content_classes'         => 'pngx-dialog__content pngx-alert__content',
			'content_wrapper_classes' => 'pngx-dialog__wrapper pngx-alert__wrapper',
			'overlay_classes'         => 'pngx-dialog__overlay pngx-alert__overlay',
			'show_event'              => 'pngx_dialog_show_alert',
			'template'                => 'alert',
			'title_classes'           => [ 'pngx-dialog__title', 'pngx-alert__title' ],
		];

		$args = wp_parse_args( $args, $default_args );

		$this->render_dialog( $content, $args, $id, $echo );
	}

	/**
	 * Factory method for dialog HTML
	 *
	 * @since TBD
	 *
	 * @param string $content HTML dialog content.
	 * @param string $id      The unique ID for this dialog (`uniqid()`) Gets prepended to the data attributes.
	 * @param array  $args    {
	 *     List of arguments to override dialog template.
	 *
	 *     @type string  $button_id               The ID for the trigger button (optional).
	 *     @type array   $button_classes          Any desired classes for the trigger button (optional).
	 *     @type string  $button_text             The text for the dialog trigger button ("Open the dialog window").
	 *     @type string  $button_type             The type for the trigger button (optional).
	 *     @type string  $button_value            The value for the trigger button (optional).
	 *     @type string  $close_event             The dialog event hook name (`pngx_dialog_close_dialog`).
	 *     @type string  $content_classes         The dialog content classes ("pngx-dialog__content").
	 *     @type string  $title_classes           The dialog title classes ("pngx-dialog__title").
	 *     @type array   $context                 Any additional context data you need to expose to this file (optional).
	 *     @type string  $id                      The unique ID for this dialog (`uniqid()`).
	 *     @type string  $show_event              The dialog event hook name (`pngx_dialog_show_dialog`).
	 *     @type string  $template                The dialog template name (dialog).
	 *     @type string  $title                   The dialog title (optional).
	 *     @type string  $trigger_classes         Classes for the dialog trigger ("pngx_dialog_trigger").
	 *
	 *     Dialog script option overrides.
	 *
	 *     @type string  $append_target           The dialog will be inserted after the button, you could supply a selector string here to override (optional).
	 *     @type boolean $body_lock               Whether to lock the body while dialog open (false).
	 *     @type string  $close_button_aria_label Aria label for the close button ("Close this dialog window").
	 *     @type string  $close_button_classes    Classes for the close button ("pngx-dialog__close-button").
	 *     @type string  $content_wrapper_classes Dialog content wrapper classes. This wrapper includes the close button ("pngx-dialog__wrapper").
	 *     @type string  $effect                  CSS effect on open. none or fade (optional).
	 *     @type string  $effect_easing           A css easing string to apply ("ease-in-out").
	 *     @type int     $effect_speed            CSS effect speed in milliseconds (optional).
	 *     @type string  $overlay_classes         The dialog overlay classes ("pngx-dialog__overlay").
	 *     @type boolean $overlay_click_closes    If clicking the overlay closes the dialog (false).
	 *     @type string  $wrapper_classes         The wrapper class for the dialog ("pngx-dialog").
	 * }
	 *
	 * @return string An HTML string of the dialog.
	 */
	private function build_dialog( $content, $id, $args ) {
		$default_args = [
			'button_classes'          => '',
			'button_id'               => '',
			'button_name'             => '',
			'button_text'             => __( 'Open the dialog window', 'pngx-common' ),
			'button_type'             => '',
			'button_value'            => '',
			'close_event'             => 'pngx_dialog_close_dialog',
			'content_classes'         => 'pngx-dialog__content',
			'context'                 => '',
			'show_event'              => 'pngx_dialog_show_dialog',
			'template'                => 'dialog',
			'title_classes'           => 'pngx-dialog__title',
			'title'                   => '',
			'trigger_classes'         => 'pngx_dialog_trigger',
			// Dialog script options.
			'append_target'           => '', // The dialog will be inserted after the button, you could supply a selector string here to override.
			'body_lock'               => false, // Lock the body while dialog open?
			'close_button_aria_label' => __( 'Close this dialog window', 'pngx-common' ), // Aria label for close button.
			'close_button_classes'    => 'pngx-dialog__close-button', // Classes for close button.
			'content_wrapper_classes' => 'pngx-dialog__wrapper', // Dialog content classes.
			'effect'                  => 'none', // None or fade (for now).
			'effect_speed'            => 0, // Effect speed in milliseconds.
			'effect_easing'           => 'ease-in-out', // A css easing string.
			'overlay_classes'         => 'pngx-dialog__overlay', // Overlay classes.
			'overlay_click_closes'    => false, // Clicking overlay closes dialog.
			'wrapper_classes'         => 'pngx-dialog', // The wrapper class for the dialog.
		];

		$args = wp_parse_args( $args, $default_args );

		$args[ 'content' ] = $content;
		$args[ 'id' ] = $id;

		/**
		 * Allow us to filter the dialog arguments.
		 *
		 * @since  TBD
		 *
		 * @param array $args The dialog arguments.
		 * @param string $content HTML content string.
		 */
		$args = apply_filters( 'pngx_dialog_args', $args, $content );

		$template = $args[ 'template' ];
		/**
		 * Allow us to filter the dialog template name.
		 *
		 * @since  TBD
		 *
		 * @param string $template The dialog template name.
		 * @param array $args The dialog arguments.
		 */
		$template_name = apply_filters( 'pngx_dialog_template', $template, $args );

		ob_start();

		$this->template( $template_name, $args, true );

		$this->get_dialog_script( $args );

		$html = ob_get_clean();

		/**
		 * Allow us to filter the dialog output (HTML string).
		 *
		 * @since  TBD
		 *
		 * @param string $html The dialog HTML string.
		 * @param array $args The dialog arguments.
		 */
		return apply_filters( 'pngx_dialog_html', $html, $args );
	}

	/**
	 * Get dialog <script> to be rendered.
	 *
	 * @since TBD
	 *
	 * @param array   $args List of arguments for the dialog script. See \Pngx\Dialog\View->build_dialog().
	 * @param boolean $echo Whether to echo the script or to return it (default: true).
	 *
	 * @return string|void The dialog <script> HTML or nothing if $echo is true.
	 */
	public function get_dialog_script( $args, $echo = true ) {
		$args = [
			'appendTarget'         => $args[ 'append_target' ],
			'bodyLock'             => $args[ 'body_lock' ],
			'closeButtonAriaLabel' => $args[ 'close_button_aria_label' ],
			'closeButtonClasses'   => $args[ 'close_button_classes' ],
			'closeEvent'           => $args[ 'close_event' ],
			'contentClasses'       => $args[ 'content_wrapper_classes' ],
			'effect'               => $args[ 'effect' ],
			'effectEasing'         => $args[ 'effect_easing' ],
			'effectSpeed'          => $args[ 'effect_speed' ],
			'id'                   => $args[ 'id' ],
			'overlayClasses'       => $args[ 'overlay_classes' ],
			'overlayClickCloses'   => $args[ 'overlay_click_closes' ],
			'showEvent'            => $args[ 'show_event' ],
			'closeEvent'           => $args[ 'close_event' ],
			'template'             => $args[ 'template' ],
			'wrapperClasses'       => esc_attr( $args[ 'wrapper_classes' ] ),
		];

		/**
		 * Allows for modifying the arguments before they are passed to the dialog script.
		 *
		 * @since TBD
		 *
		 * @param array $args List of arguments to override dialog script. See \Pngx\Dialog\View->build_dialog().
		 */
		$args = apply_filters( 'pngx_dialog_script_args', $args );

		// Escape all argument values.
		$args = array_map( 'esc_html', $args );

		$args[ 'trigger' ] = "[data-js='" . esc_attr( 'trigger-dialog-' . $args[ 'id' ] ) . "' ]";

		ob_start();
		?>
		<script>
			var pngx = pngx || {};
			pngx.dialogs = pngx.dialogs || [];

			pngx.dialogs.push( <?php echo json_encode( $args ); ?> );

			<?php
			/**
			 * Allows for injecting additional scripts (button actions, etc).
			 *
			 * @since TBD
			 *
			 * @param array $args List of arguments to override dialog script. See \Pngx\Dialog\View->build_dialog().
			 */
			do_action( 'pngx_dialog_additional_scripts', $args );

			/**
			 * Allows for injecting additional scripts (button actions, etc) by template.
			 *
			 * @since TBD
			 *
			 * @param array $args List of arguments to override dialog script. See \Pngx\Dialog\View->build_dialog().
			 */
			do_action( 'pngx_dialog_additional_scripts_'  . $args[ 'template' ], $args );

			/**
			 * Allows for injecting additional scripts (button actions, etc) by dialog ID.
			 *
			 * @since TBD
			 *
			 * @param array $args List of arguments to override dialog script. See \Pngx\Dialog\View->build_dialog().
			 */
			do_action( 'pngx_dialog_additional_scripts_' . $args[ 'id' ], $args );
			?>
		</script>
		<?php
		$html = ob_get_clean();

		/**
		 * Allows for modifying the HTML before it is echoed or returned.
		 *
		 * @since TBD
		 *
		 * @param array $args List of arguments to override dialog script. See \Pngx\Dialog\View->build_dialog().
		 */
		$html = apply_filters( 'pngx_dialog_script_html', $html );

		if ( $echo ) {
			echo $html;
			return;
		}

		return $html;
	}
}
