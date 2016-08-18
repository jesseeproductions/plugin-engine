<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
if ( class_exists( 'Pngx__Admin__Meta' ) ) {
	return;
}


/**
 * Class Pngx__Admin__Meta
 * Create, Save, Display Custom Fields for a CPT
 */
class Pngx__Admin__Meta {

	protected static $instance;

	//tabs key and label
	protected static $tabs = array();

	//fields id prefix
	protected $fields_prefix = 'pngx_';

	//fields
	protected static $fields = array();

	//post type
	protected static $post_type = array();

	//user capability
	protected static $user_capability = array();

	/*
	* Construct
	*/
	public function __construct() {

		//Save Meta
		add_action( 'save_post', array( __CLASS__, 'save_meta' ), 10, 2 );

		//JS Error Check
		add_action( 'cctor_meta_message', array( __CLASS__, 'get_js_error_check_msg' ) );
	}

	/**
	 * Javascript Conflict Message
	 *
	 * @return string
	 */
	public static function get_js_error_check_msg() {

		$js_troubleshoot_url = 'http://cctor.link/R7KRa';

		$js_msg = '<div class="javascript-conflict pngx-error"><p>' . sprintf( __( 'There maybe a javascript conflict preventing some features from working.  <a href="%s" target="_blank" >Please check this guide to narrow down the cause.</a>', 'plugin-engine' ), esc_url( $js_troubleshoot_url ) ) . '</p></div>';

		return $js_msg;

	}

	/*
	* Set Post Type
	*/
	protected function set_post_type() {

		$post_type[] = 'pngx';

		$this->post_type = $post_type;

	}

	/*
	* Get Post Type
	*/
	public static function get_post_types() {

		return self::$post_type;

	}

	/*
	* Set User Capability
	*/
	protected function set_user_capability() {

		$user_capability = 'edit_post';

		$this->user_capability = $user_capability;

	}

	/*
	* Get User Capability
	*/
	public static function get_user_capability() {

		return self::$user_capability;

	}

	/*
	* Set Tabs
	*/
	protected function set_tabs() {

		//CPT Fields Tabs
		$tabs['content'] = __( 'Content', 'plugin-engine' ); //set key and tab title

		$this->tabs = $tabs;

	}

	/*
	* Get Tabs
	*/
	public static function get_tabs() {

		return self::$tabs;

	}

	/*
	* Get Fields ID Prefix
	*/
	public function get_fields_prefix() {

		return $this->fields_prefix;

	}

	/*
	* Load Meta Box Functions
	*/
	public function set_fields() {

		//Prefix for fields id
		$prefix = self::get_fields_prefix();

		//Sample Field Array
		$fields[ $prefix . 'heading_deal' ] = array(
             'id'        => $prefix . 'heading_deal', //id
             'title'     => '', //Label
             'desc'      => __( 'Coupon Deal', 'plugin-engine' ),//description or header
             'type'      => 'heading',//field type
             'section'   => 'plugin_engine_meta_box',//meta box
             'tab'       => 'content',//tab
             'condition' => 'pngx-img',//optional condition used in some fields
             'class'     => 'pngx-img',//optional field class
             'wrapclass' => 'pngx-img',//optional wrap css class
             'toggle'    => array()//field toggle infomation based on value or selection
		);

		$this->fields = $fields;
	}

	/*
	* Get Fields
	*/
	public static function get_fields() {

		return self::$fields;

	}

	/**
	 * Show Fields
	 *
	 * @param $post
	 * @param $metabox
	 */
	public static function display_fields( $post, $metabox ) {

		global $wp_version;

		wp_nonce_field( 'pngx_save_fields', 'pngx_nonce' );

		//Create Array of Tabs and Localize to Meta Script
		$tabs_array = array();

		foreach ( self::get_tabs() as $tab_slug => $tab ) {
			$tabs_array[ $tab ] = $tab_slug;
		}

		//Detect if we saved or tried to save to set the current tab.
		global $message;

		$tab_data = array(
			'tabs'           => $tabs_array,
			'update_message' => $message,
			'id'             => isset( $_GET['post'] ) ? absint( $_GET['post'] ) : '',
			'wp_version'     => $wp_version,
		);

		ob_start(); ?>

		<div class="pngx-tabs" <?php echo Pngx__Admin__Fields::toggle( $tab_data, null ); ?> >

			<ul class="pngx-tabs-nav">

				<?php //Create Tabs
				foreach ( self::get_tabs() as $tab_slug => $tab ) {
					echo '<li><a href="#' . $tab_slug . '">' . $tab . '</a></li>';
				}
				?>
			</ul>

			<?php foreach ( self::get_tabs() as $tab_slug => $tab ) { ?>

				<div class="pngx-section-fields form-table">

					<h2 class="pngx-tab-heading-<?php echo $tab_slug; ?>"><?php echo $tab; ?></h2>

					<?php

					/**
					 * Hook to connect help section into a tab
					 *
					 * @parm $tab_slug string of current slug
					 */
					do_action( 'pngx-per-tab-help', $tab_slug );

					foreach ( self::get_fields() as $field ) {

						if ( $field['type'] && $field['section'] == $metabox['id'] && $tab_slug == $field['tab'] ) :

							// get value of this field if it exists for this post
							$meta      = get_post_meta( $post->ID, $field['id'], true );

							//Wrap Class for Conditionals
							$wrapclass = isset( $field['wrapclass'] ) ? $field['wrapclass'] : '';

							?>

							<div class="pngx-meta-field-wrap field-wrap-<?php echo esc_html( $field['type'] ); ?> field-wrap-<?php echo esc_html( $field['id'] ); ?> <?php echo esc_html( $wrapclass ); ?>"
								<?php echo isset( $field['toggle'] ) ? Pngx__Admin__Fields::toggle( $field['toggle'], $field['id'] ) : null; ?> >

								<?php if ( isset( $field['label'] ) ) { ?>

									<div class="pngx-meta-label label-<?php echo $field['type']; ?> label-<?php echo $field['id']; ?>">
										<label for="<?php echo $field['id']; ?>"><?php echo $field['label']; ?></label>
									</div>

								<?php } ?>

								<div class="pngx-meta-field field-<?php echo $field['type']; ?> field-<?php echo $field['id']; ?>">

									<?php

									Pngx__Admin__Fields::display_field( $field, false, false, $meta, $wp_version );

									?>

								</div>
								<!-- end .pngx-meta-field.field-<?php echo $field['type']; ?>.field-<?php echo $field['id']; ?> -->

							</div> <!-- end .pngx-meta-field-wrap.field-wrap-<?php echo $field['type']; ?>.field-wrap-<?php echo $field['id']; ?>	-->

							<?php
						endif; //end if in section check

					} // end foreach fields?>

				</div>    <!-- end .pngx-section-fields.form-table -->

			<?php } // end foreach tabs
			?>

		</div>    <!-- end .pngx-tabs -->

		<?php echo ob_get_clean();
	}

	/*
	* Save Meta Fields
	*/
	public static function save_meta( $post_id, $post ) {

		//Autosave or no past variable then kick out
		if ( empty( $_POST ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
			return;
		}

		//Check if on the right post type
		if ( isset( $post->post_type ) && ! in_array( $post->post_type, self::get_post_types() ) ) {
			return;
		}

		//Check if the user can make edits
		if ( ! current_user_can( self::get_user_capability(), $post->ID ) ) {
			return;
		}

		//Verify Nonce
		if ( isset( $_POST['pngx_nonce'] ) && ! wp_verify_nonce( $_POST['pngx_nonce'], 'pngx_save_fields' ) && ( isset( $_POST['_inline_edit'] ) && ! wp_verify_nonce( $_POST['_inline_edit'], 'inlineeditnonce' ) ) ) {
			return;
		}

		// Save data

		//todo this is used to save the ignore expiration with the new expriation options
		//Expiration Option Auto Check Ignore Input
		if ( isset( $_POST['cctor_ignore_expiration'] ) && 1 == $_POST['cctor_expiration_option'] ) {
			$_POST['cctor_ignore_expiration'] = 'on';
		} elseif ( isset( $_POST['cctor_ignore_expiration'] ) && 'on' == $_POST['cctor_ignore_expiration'] && 1 != $_POST['cctor_expiration_option'] ) {
			unset( $_POST['cctor_ignore_expiration'] );
		}

		/**
		 * Before Save Meta Fields
		 *
		 * @param array $_POST
		 *
		 */
		do_action( 'pngx_before_save_meta_fields', $_POST );

		//Save Date for each file
		foreach ( self::get_fields() as $option ) {

			/**
			 * Save Meta Fields
			 *
			 *
			 * @param int   $post_id
			 * @param array $option
			 *
			 */
			do_action( 'pngx_save_meta_fields', $post_id, $option );

			//handle check box saving
			if ( $option['type'] == 'checkbox' ) {

				$checkbox = get_post_meta( $post_id, $option['id'], true );

				if ( $checkbox && ! isset( $_POST[ $option['id'] ] ) ) {
					delete_post_meta( $post_id, $option['id'] );
				}

			}

			// Final Check if value should be saved then sanitize and save
			if ( isset( $_POST[ $option['id'] ] ) ) {

				//Send Input to Sanitize Class, will return sanitized input or no input if no sanitization method
				$sanitized = new Pngx__Sanitize( $option['type'], $_POST[ $option['id'] ], $option );

				$old = get_post_meta( $post_id, $option['id'], true );

				$new = $_POST[ $option['id'] ];

				if ( ! is_null( $new ) && $new != $old ) {
					update_post_meta( $post_id, $option['id'], $sanitized->result );
				} elseif ( '' == $new && $old ) {
					delete_post_meta( $post_id, $option['id'], $old );
				}

			}
		}

		/**
		 * After Save Meta Fields
		 *
		 * @param array $_POST
		 *
		 */
		do_action( 'pngx_after_save_meta_fields', $_POST );

	}

	/**
	 * Static Singleton Factory Method
	 *
	 * @return Pngx__Admin__Meta
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			$className      = __CLASS__;
			self::$instance = new $className;
		}

		return self::$instance;
	}
}