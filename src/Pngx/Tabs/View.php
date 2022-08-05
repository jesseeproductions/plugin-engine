<?php

namespace Pngx\Tabs;

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
	public $template_namespace = 'tabs';

	/**
	 * View constructor
	 *
	 * @since TBD
	 */
	public function __construct() {
		$this->set_template_origin( \Pngx__Main::instance() );
		$this->set_template_folder( 'src/views/tabs' );

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
	 *                               List of arguments to override tabs template.
	 *
	 * @type string   $button_id     The ID for the trigger button (optional).
	 *
	 *     Tabs script option overrides.
	 *
	 * @type string   $append_target The tabs will be inserted after the button, you could supply a selector string here to override
	 *
	 * @param string  $id            The unique ID for this tabs. Gets prepended to the data attributes. Generated if not passed (`uniqid()`).
	 * @param boolean $echo          Whether to echo the script or to return it (default: true).
	 *
	 * @return string An HTML string of the tabs.
	 */
	public function render_tabs( $content, $args = [], $id = null, $echo = true ) {
		// Check for content to be passed.
		if ( empty( $content ) ) {
			return '';
		}

		$default_args = [
			'content_classes' => 'pngx-tabs__content',
			'template'        => 'tabs',

			'tabbed'     => '.tabbed',
			'tablist'    => 'ul',
			'tabs'       => 'a',
			'initialTab' => '0',
		];

		$args = wp_parse_args( $args, $default_args );

		// Generate an ID if we weren't passed one.
		if ( is_null( $id ) ) {
			$id = \uniqid();
		}

		/** @var \Pngx__Assets $assets */
		$assets = pngx( 'pngx.assets' );
		$assets->enqueue_group( 'pngx-tabs' );

		$html = $this->build_tabs( $content, $id, $args );

		if ( ! $echo ) {
			return $html;
		}

		echo $html;
	}

	/**
	 * Factory method for tabs HTML
	 *
	 * @since TBD
	 *
	 * @param string $content       HTML tabs content.
	 * @param string $id            The unique ID for this tabs (`uniqid()`) Gets prepended to the data attributes.
	 * @param array  $args          {
	 *                              List of arguments to override tabs template.
	 *
	 * @type string  $button_id     The ID for the trigger button (optional).
	 *
	 *     Tabs script option overrides.
	 *
	 * @type string  $append_target The tabs will be inserted after the button, you could supply a selector string here to override
	 *
	 * @return string An HTML string of the tabs.
	 */
	private function build_tabs( $content, $id, $args ) {
		$default_args = [
			'tabbed'          => $args['tabbed'],
			'tablist'         => $args['tablist'],
			'tabs'            => $args['tabs'],
			'initialTab'      => $args['initialTab'],
			'template'        => $args['template'],
			'wrapper_classes' => 'pngx-tabs tabbed', // The wrapper class for the tabs.
		];

		$args = wp_parse_args( $args, $default_args );

		$args['content'] = $content;
		$args['id']      = $id;

		/**
		 * Allow us to filter the tabs arguments.
		 *
		 * @since  TBD
		 *
		 * @param array  $args    The tabs arguments.
		 * @param string $content HTML content string.
		 */
		$args = apply_filters( 'pngx_tabs_args', $args, $content );

		$template = $args['template'];
		/**
		 * Allow us to filter the tabs template name.
		 *
		 * @since  TBD
		 *
		 * @param string $template The tabs template name.
		 * @param array  $args     The tabs arguments.
		 */
		$template_name = apply_filters( 'pngx_tabs_template', $template, $args );

		ob_start();

		$this->template( $template_name, $args, true );

		$this->get_tabs_script( $args );

		$html = ob_get_clean();

		/**
		 * Allow us to filter the tabs output (HTML string).
		 *
		 * @since  TBD
		 *
		 * @param string $html The tabs HTML string.
		 * @param array  $args The tabs arguments.
		 */
		return apply_filters( 'pngx_tabs_html', $html, $args );
	}

	/**
	 * Get tabs <script> to be rendered.
	 *
	 * @since TBD
	 *
	 * @param array   $args List of arguments for the tabs script. See \Pngx\Tabs\View->build_tabs().
	 * @param boolean $echo Whether to echo the script or to return it (default: true).
	 *
	 * @return string|void The tabs <script> HTML or nothing if $echo is true.
	 */
	public function get_tabs_script( $args, $echo = true ) {
		$args = [
			'id'             => $args['id'],
			'tabbed'         => $args['tabbed'],
			'tablist'        => $args['tablist'],
			'tabs'           => $args['tabs'],
			'initialTab'     => $args['initialTab'],
			'template'       => $args['template'],
			'wrapperClasses' => esc_attr( $args['wrapper_classes'] ),
		];

		/**
		 * Allows for modifying the arguments before they are passed to the tabs script.
		 *
		 * @since TBD
		 *
		 * @param array $args List of arguments to override tabs script. See \Pngx\Tabs\View->build_tabs().
		 */
		$args = apply_filters( 'pngx_tabs_script_args', $args );

		// Escape all argument values.
		$args = array_map( 'esc_html', $args );

		$args['trigger'] = "[data-js='" . esc_attr( 'trigger-tabs-' . $args['id'] ) . "' ]";

		ob_start();
		?>
		<script>
			var pngx = pngx || {};
			pngx.tabs = pngx.tabs || [];

			pngx.tabs.push( <?php echo json_encode( $args ); ?> );

			<?php
			/**
			 * Allows for injecting additional scripts (button actions, etc).
			 *
			 * @since TBD
			 *
			 * @param array $args List of arguments to override tabs script. See \Pngx\Tabs\View->build_tabs().
			 */
			do_action( 'pngx_tabs_additional_scripts', $args );

			/**
			 * Allows for injecting additional scripts (button actions, etc) by template.
			 *
			 * @since TBD
			 *
			 * @param array $args List of arguments to override tabs script. See \Pngx\Tabs\View->build_tabs().
			 */
			do_action( 'pngx_tabs_additional_scripts_' . $args['template'], $args );

			/**
			 * Allows for injecting additional scripts (button actions, etc) by tabs ID.
			 *
			 * @since TBD
			 *
			 * @param array $args List of arguments to override tabs script. See \Pngx\Tabs\View->build_tabs().
			 */
			do_action( 'pngx_tabs_additional_scripts_' . $args['id'], $args );
			?>
		</script>
		<?php
		$html = ob_get_clean();

		/**
		 * Allows for modifying the HTML before it is echoed or returned.
		 *
		 * @since TBD
		 *
		 * @param array $args List of arguments to override tabs script. See \Pngx\Tabs\View->build_tabs().
		 */
		$html = apply_filters( 'pngx_tabs_script_html', $html );

		if ( $echo ) {
			echo $html;

			return;
		}

		return $html;
	}
}
