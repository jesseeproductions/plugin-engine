<?php

namespace Pngx\Carousel;

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
	public $template_namespace = 'carousel';

	/**
	 * View constructor
	 *
	 * @since TBD
	 */
	public function __construct() {
		$this->set_template_origin( \Pngx__Main::instance() );
		$this->set_template_folder( 'src/views/carousel' );

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
	 *     List of arguments to override carousel template.
	 *
	 *     @type string  $button_id               The ID for the trigger button (optional).
	 *     @type array   $button_classes          Any desired classes for the trigger button (optional).
	 *     @type string  $button_text             The text for the carousel trigger button ("Open the carousel window").
	 *     @type string  $button_type             The type for the trigger button (optinoal).
	 *     @type string  $button_value            The value for the trigger button (optional).
	 *     @type string  $close_event             The carousel close event hook name (`pngx_carousel_close_carousel`).
	 *     @type string  $content_classes         The carousel content classes ("pngx-carousel__content").
	 *     @type array   $context                 Any additional context data you need to expose to this file (optional).
	 *     @type string  $id                      The unique ID for this carousel (`uniqid()`).
	 *     @type string  $show_event              The carousel event show hook name (`pngx_carousel_show_carousel`).
	 *     @type string  $template                The carousel template name (carousel).
	 *     @type string  $title                   The carousel title (optional).
	 *     @type string  $trigger_classes         Classes for the carousel trigger ("pngx_carousel_trigger").
	 *
	 *     Carousel script option overrides.
	 *
	 *     @type string  $append_target           The carousel will be inserted after the button, you could supply a selector string here to override (optional).
	 *     @type boolean $body_lock               Whether to lock the body while carousel open (false).
	 *     @type string  $close_button_aria_label Aria label for the close button ("Close this carousel window").
	 *     @type string  $close_button_classes    Classes for the close button ("pngx-carousel__close-button").
	 *     @type string  $content_wrapper_classes Carousel content wrapper classes. This wrapper includes the close button ("pngx-carousel__wrapper").
	 *     @type string  $effect                  CSS effect on open. none or fade (optional).
	 *     @type string  $effect_easing           A css easing string to apply ("ease-in-out").
	 *     @type int     $effect_speed            CSS effect speed in milliseconds (optional).
	 *     @type string  $overlay_classes         The carousel overlay classes ("pngx-carousel__overlay").
	 *     @type boolean $overlay_click_closes    If clicking the overlay closes the carousel (false).
	 *     @type string  $wrapper_classes         The wrapper class for the carousel ("pngx-carousel").
	 * }
	 * @param string  $id      The unique ID for this carousel. Gets prepended to the data attributes. Generated if not passed (`uniqid()`).
	 * @param boolean $echo    Whether to echo the script or to return it (default: true).
	 *
	 * @return string An HTML string of the carousel.
	 */
	public function render_carousel( $content, $args = [], $id = null, $echo = true ) {
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
		$assets->enqueue_group( 'pngx-carousel' );

		$html = $this->build_carousel( $content, $id, $args );

		if ( ! $echo ) {
			return $html;
		}

		echo $html;
	}

	/**
	 * Syntactic sugar for `render_carousel()` to make creating modals easier.
	 * Adds sensible defaults for modals.
	 *
	 * @since TBD
	 *
	 * @param string  $content Content as an HTML string.
	 * @param array   $args    {
	 *     List of arguments to override carousel template.
	 *
	 *     @type string  $button_id               The ID for the trigger button (optional).
	 *     @type array   $button_classes          Any desired classes for the trigger button (optional).
	 *     @type string  $button_text             The text for the carousel trigger button ("Open the modal window").
	 *     @type string  $button_type             The type for the trigger button (optional).
	 *     @type string  $button_value            The value for the trigger button (optional).
	 *     @type string  $close_event             The carousel close event hook name (`pngx_carousel_close_modal`).
	 *     @type string  $content_classes         The carousel content classes ("pngx-carousel__content pngx-modal__content").
	 *     @type string  $title_classes           The carousel title classes ("pngx-carousel__title pngx-modal__title").
	 *     @type array   $context                 Any additional context data you need to expose to this file (optional).
	 *     @type string  $id                      The unique ID for this carousel (`uniqid()`).
	 *     @type string  $show_event              The carousel event hook name (`pngx_carousel_show_modal`).
	 *     @type string  $template                The carousel template name (modal).
	 *     @type string  $title                   The carousel title (optional).
	 *     @type string  $trigger_classes         Classes for the carousel trigger ("pngx_carousel_trigger").
	 *
	 *     Carousel script option overrides.
	 *
	 *     @type string  $append_target           The carousel will be inserted after the button, you could supply a selector string here to override ("body").
	 *     @type boolean $body_lock               Whether to lock the body while carousel open (true).
	 *     @type string  $close_button_aria_label Aria label for the close button ("Close this modal window").
	 *     @type string  $close_button_classes    Classes for the close button ("pngx-carousel__close-button pngx-modal__close-button").
	 *     @type string  $content_wrapper_classes Carousel content wrapper classes. This wrapper includes the close button ("pngx-carousel__wrapper pngx-modal__wrapper").
	 *     @type string  $effect                  CSS effect on open. none or fade ("fade").
	 *     @type string  $effect_easing           A css easing string to apply ("ease-in-out").
	 *     @type int     $effect_speed            CSS effect speed in milliseconds (300).
	 *     @type string  $overlay_classes         The carousel overlay classes ("pngx-carousel__overlay pngx-modal__overlay").
	 *     @type boolean $overlay_click_closes    If clicking the overlay closes the carousel (true).
	 *     @type string  $wrapper_classes         The wrapper class for the carousel ("pngx-carousel").
	 * }
	 * @param string  $id      The unique ID for this carousel. Gets prepended to the data attributes. Generated if not passed (`uniqid()`).
	 * @param boolean $echo    Whether to echo the script or to return it (default: true).
	 *
	 * @return string An HTML string of the carousel.
	 */
	public function render_modal( $content, $args = [], $id = null, $echo = true ) {
		$default_args = [
			'append_target'           => '',
			'body_lock'               => true,
			'button_text'             => __( 'Open the modal window', 'pngx-common' ),
			'close_button_aria_label' => __( 'Close this modal window', 'pngx-common' ),
			'close_button_classes'    => 'pngx-carousel__close-button pngx-modal__close-button',
			'close_event'             => 'pngx_carousel_close_modal',
			'content_classes'         => 'pngx-carousel__content pngx-modal__content',
			'content_wrapper_classes' => 'pngx-carousel__wrapper pngx-modal__wrapper',
			'effect'                  => 'fade',
			'effect_speed'            => 300,
			'overlay_classes'         => 'pngx-carousel__overlay pngx-modal__overlay',
			'overlay_click_closes'    => true,
			'show_event'              => 'pngx_carousel_show_modal',
			'template'                => 'modal',
			'title_classes'           => [ 'pngx-carousel__title', 'pngx-modal__title' ],
		];

		$args = wp_parse_args( $args, $default_args );

		$this->render_carousel( $content, $args, $id, $echo );
	}

	/**
	 * Factory method for carousel HTML
	 *
	 * @since TBD
	 *
	 * @param string $content HTML carousel content.
	 * @param string $id      The unique ID for this carousel (`uniqid()`) Gets prepended to the data attributes.
	 * @param array  $args    {
	 *     List of arguments to override carousel template.
	 *
	 *     @type string  $button_id               The ID for the trigger button (optional).
	 *     @type array   $button_classes          Any desired classes for the trigger button (optional).
	 *     @type string  $button_text             The text for the carousel trigger button ("Open the carousel window").
	 *     @type string  $button_type             The type for the trigger button (optional).
	 *     @type string  $button_value            The value for the trigger button (optional).
	 *     @type string  $close_event             The carousel event hook name (`pngx_carousel_close_carousel`).
	 *     @type string  $content_classes         The carousel content classes ("pngx-carousel__content").
	 *     @type string  $title_classes           The carousel title classes ("pngx-carousel__title").
	 *     @type array   $context                 Any additional context data you need to expose to this file (optional).
	 *     @type string  $id                      The unique ID for this carousel (`uniqid()`).
	 *     @type string  $show_event              The carousel event hook name (`pngx_carousel_show_carousel`).
	 *     @type string  $template                The carousel template name (carousel).
	 *     @type string  $title                   The carousel title (optional).
	 *     @type string  $trigger_classes         Classes for the carousel trigger ("pngx_carousel_trigger").
	 *
	 *     Carousel script option overrides.
	 *
	 *     @type string  $append_target           The carousel will be inserted after the button, you could supply a selector string here to override (optional).
	 *     @type boolean $body_lock               Whether to lock the body while carousel open (false).
	 *     @type string  $close_button_aria_label Aria label for the close button ("Close this carousel window").
	 *     @type string  $close_button_classes    Classes for the close button ("pngx-carousel__close-button").
	 *     @type string  $content_wrapper_classes Carousel content wrapper classes. This wrapper includes the close button ("pngx-carousel__wrapper").
	 *     @type string  $effect                  CSS effect on open. none or fade (optional).
	 *     @type string  $effect_easing           A css easing string to apply ("ease-in-out").
	 *     @type int     $effect_speed            CSS effect speed in milliseconds (optional).
	 *     @type string  $overlay_classes         The carousel overlay classes ("pngx-carousel__overlay").
	 *     @type boolean $overlay_click_closes    If clicking the overlay closes the carousel (false).
	 *     @type string  $wrapper_classes         The wrapper class for the carousel ("pngx-carousel").
	 * }
	 *
	 * @return string An HTML string of the carousel.
	 */
	private function build_carousel( $content, $id, $args ) {
		$default_args = [
			'button_classes'          => '',
			'button_id'               => '',
			'button_name'             => '',
			'button_text'             => __( 'Open the carousel window', 'pngx-common' ),
			'button_type'             => '',
			'button_value'            => '',
			'close_event'             => 'pngx_carousel_close_carousel',
			'content_classes'         => 'pngx-carousel__content',
			'context'                 => '',
			'show_event'              => 'pngx_carousel_show_carousel',
			'template'                => 'carousel',
			'title_classes'           => 'pngx-carousel__title',
			'title'                   => '',
			'trigger_classes'         => 'pngx_carousel_trigger',
			// Carousel script options.
			'append_target'           => '', // The carousel will be inserted after the button, you could supply a selector string here to override.
			'body_lock'               => false, // Lock the body while carousel open?
			'close_button_aria_label' => __( 'Close this carousel window', 'pngx-common' ), // Aria label for close button.
			'close_button_classes'    => 'pngx-carousel__close-button', // Classes for close button.
			'content_wrapper_classes' => 'pngx-carousel__wrapper', // Carousel content classes.
			'effect'                  => 'none', // None or fade (for now).
			'effect_speed'            => 0, // Effect speed in milliseconds.
			'effect_easing'           => 'ease-in-out', // A css easing string.
			'overlay_classes'         => 'pngx-carousel__overlay', // Overlay classes.
			'overlay_click_closes'    => false, // Clicking overlay closes carousel.
			'wrapper_classes'         => 'pngx-carousel', // The wrapper class for the carousel.
		];

		$args = wp_parse_args( $args, $default_args );

		$args[ 'content' ] = $content;
		$args[ 'id' ] = $id;

		/**
		 * Allow us to filter the carousel arguments.
		 *
		 * @since  TBD
		 *
		 * @param array $args The carousel arguments.
		 * @param string $content HTML content string.
		 */
		$args = apply_filters( 'pngx_carousel_args', $args, $content );

		$template = $args[ 'template' ];
		/**
		 * Allow us to filter the carousel template name.
		 *
		 * @since  TBD
		 *
		 * @param string $template The carousel template name.
		 * @param array $args The carousel arguments.
		 */
		$template_name = apply_filters( 'pngx_carousel_template', $template, $args );

		ob_start();

		$this->template( $template_name, $args, true );

		$this->get_carousel_script( $args );

		$html = ob_get_clean();

		/**
		 * Allow us to filter the carousel output (HTML string).
		 *
		 * @since  TBD
		 *
		 * @param string $html The carousel HTML string.
		 * @param array $args The carousel arguments.
		 */
		return apply_filters( 'pngx_carousel_html', $html, $args );
	}

	/**
	 * Get carousel <script> to be rendered.
	 *
	 * @since TBD
	 *
	 * @param array   $args List of arguments for the carousel script. See \Pngx\Carousel\View->build_carousel().
	 * @param boolean $echo Whether to echo the script or to return it (default: true).
	 *
	 * @return string|void The carousel <script> HTML or nothing if $echo is true.
	 */
	public function get_carousel_script( $args, $echo = true ) {
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
		 * Allows for modifying the arguments before they are passed to the carousel script.
		 *
		 * @since TBD
		 *
		 * @param array $args List of arguments to override carousel script. See \Pngx\Carousel\View->build_carousel().
		 */
		$args = apply_filters( 'pngx_carousel_script_args', $args );

		// Escape all argument values.
		$args = array_map( 'esc_html', $args );

		$args[ 'trigger' ] = "[data-js='" . esc_attr( 'trigger-carousel-' . $args[ 'id' ] ) . "' ]";

		ob_start();
		?>
		<script>
			var pngx = pngx || {};
			pngx.carousels = pngx.carousels || [];

			pngx.carousels.push( <?php echo json_encode( $args ); ?> );

			<?php
			/**
			 * Allows for injecting additional scripts (button actions, etc).
			 *
			 * @since TBD
			 *
			 * @param array $args List of arguments to override carousel script. See \Pngx\Carousel\View->build_carousel().
			 */
			do_action( 'pngx_carousel_additional_scripts', $args );

			/**
			 * Allows for injecting additional scripts (button actions, etc) by template.
			 *
			 * @since TBD
			 *
			 * @param array $args List of arguments to override carousel script. See \Pngx\Carousel\View->build_carousel().
			 */
			do_action( 'pngx_carousel_additional_scripts_'  . $args[ 'template' ], $args );

			/**
			 * Allows for injecting additional scripts (button actions, etc) by carousel ID.
			 *
			 * @since TBD
			 *
			 * @param array $args List of arguments to override carousel script. See \Pngx\Carousel\View->build_carousel().
			 */
			do_action( 'pngx_carousel_additional_scripts_' . $args[ 'id' ], $args );
			?>
		</script>
		<?php
		$html = ob_get_clean();

		/**
		 * Allows for modifying the HTML before it is echoed or returned.
		 *
		 * @since TBD
		 *
		 * @param array $args List of arguments to override carousel script. See \Pngx\Carousel\View->build_carousel().
		 */
		$html = apply_filters( 'pngx_carousel_script_html', $html );

		if ( $echo ) {
			echo $html;
			return;
		}

		return $html;
	}
}
