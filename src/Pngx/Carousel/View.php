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
	 * @param string  $content       Content as an HTML string.
	 * @param array   $args          {
	 *                               List of arguments to override carousel template.
	 *
	 * @type string   $button_id     The ID for the trigger button (optional).
	 *
	 *     Carousel script option overrides.
	 *
	 * @type string   $append_target The carousel will be inserted after the button, you could supply a selector string here to override (optional).
	 * }
	 *
	 * @param string  $id            The unique ID for this carousel. Gets prepended to the data attributes. Generated if not passed (`uniqid()`).
	 * @param boolean $echo          Whether to echo the script or to return it (default: true).
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


	public function render_slider( $content, $args = [], $id = null, $echo = true ) {
		$default_args = [
			'template' => 'slider',
		];

		$args = wp_parse_args( $args, $default_args );

		$this->render_carousel( $content, $args, $id, $echo );
	}

	/**
	 * Factory method for carousel HTML
	 *
	 * @since TBD
	 *
	 * @param string $content       HTML carousel content.
	 * @param string $id            The unique ID for this carousel (`uniqid()`) Gets prepended to the data attributes.
	 * @param array  $args          {
	 *                              List of arguments to override carousel template.
	 *
	 * @type string  $button_id     The ID for the trigger button (optional).
	 *
	 *     Carousel script option overrides.
	 *
	 * @type string  $append_target The carousel will be inserted after the button, you could supply a selector string here to override (optional).
	 * }
	 *
	 * @return string An HTML string of the carousel.
	 */
	private function build_carousel( $content, $id, $args ) {
		$default_args = [
			'template'        => 'carousel',
			'wrapper_classes' => 'carousel',

			// Carousel script options.
			'accessibility'   => true,
			'autoplay'        => true,
			'autoplaySpeed'   => 4000,
			'arrows'          => true,
			'prevArrow'       => '<a class="slick-prev" href="#"><span class="screen-reader-text">Previous</span><i class="fa fa-chevron-left"></i></a>',
			'nextArrow'       => '<a class="slick-next" href="#"><span class="screen-reader-text">Next</span><i class="fa fa-chevron-right"></i></a>',
			'draggable'       => true,
			'focusOnSelect'   => true,
			'infinite'        => true,
			'pauseOnFocus'    => true,
			'pauseOnHover'    => true,
			'responsive'      => [
				[
					'breakpoint' => 768,
					'settings'   => [
						'slidesToShow'   => 3,
						'slidesToScroll' => 3,

					],
				],
				[
					'breakpoint' => 480,
					'settings'   => [
						'slidesToShow'   => 2,
						'slidesToScroll' => 2,

					],
				],
			],
			'slidesToShow'    => 4,
			'slidesToScroll'  => 4,
		];

		$args = wp_parse_args( $args, $default_args );

		$args['content'] = $content;
		$args['id']      = $id;

		/**
		 * Allow us to filter the carousel arguments.
		 *
		 * @since  TBD
		 *
		 * @param array  $args    The carousel arguments.
		 * @param string $content HTML content string.
		 */
		$args = apply_filters( 'pngx_carousel_args', $args, $content );

		$template = $args['template'];
		/**
		 * Allow us to filter the carousel template name.
		 *
		 * @since  TBD
		 *
		 * @param string $template The carousel template name.
		 * @param array  $args     The carousel arguments.
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
		 * @param array  $args The carousel arguments.
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
			'id'             => $args['id'],
			'template'       => $args['template'],
			'wrapperClasses' => $args['wrapper_classes'],

			// Carousel script options.
			'accessibility'   => $args['accessibility'],
			'autoplay'        => $args['autoplay'],
			'autoplaySpeed'   => $args['autoplaySpeed'],
			'arrows'          => $args['arrows'],
			'prevArrow'       => $args['prevArrow'],
			'nextArrow'       => $args['nextArrow'],
			'draggable'       => $args['draggable'],
			'focusOnSelect'   => $args['focusOnSelect'],
			'infinite'        => $args['infinite'],
			'pauseOnFocus'    => $args['pauseOnFocus'],
			'pauseOnHover'    => $args['pauseOnHover'],
			'responsive'      => $args['responsive'],
			'slidesToShow'    => $args['slidesToShow'],
			'slidesToScroll'  => $args['slidesToScroll'],
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
		//$args = array_map( 'esc_html', $args );

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
			do_action( 'pngx_carousel_additional_scripts_' . $args['template'], $args );

			/**
			 * Allows for injecting additional scripts (button actions, etc) by carousel ID.
			 *
			 * @since TBD
			 *
			 * @param array $args List of arguments to override carousel script. See \Pngx\Carousel\View->build_carousel().
			 */
			do_action( 'pngx_carousel_additional_scripts_' . $args['id'], $args );
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
