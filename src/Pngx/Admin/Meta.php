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

		$js_msg = '<div class="javascript-conflict pngx-error"><p>' . sprintf( __( 'There maybe a javascript conflict preventing some features from working.  <a href="%s" target="_blank" >Please check this guide to narrow down the cause.</a>', 'coupon-creator' ), esc_url( $js_troubleshoot_url ) ) . '</p></div>';

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
		$tabs['content'] = __( 'Content', 'coupon-creator' ); //set key and tab title

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
		$fields[ $prefix . 'heading_deal' ] = array( //prefix and id
             'id'        => $prefix . 'heading_deal', //prefix and id
             'title'     => '', //Label
             'desc'      => __( 'Coupon Deal', 'coupon-creator' ), //description or header
             'type'      => 'heading', //field type
             'section'   => 'coupon_creator_meta_box', //meta box
             'tab'       => 'content', //tab
             'wrapclass' => 'pngx-img-coupon' //optional class
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
	public static function show_fields( $post, $metabox ) {

		wp_nonce_field( 'pngx_save_fields', 'pngx_nonce' );

		//Set for WP 4.3 and replacing wp_htmledit_pre
		global $wp_version;
		$cctor_required_wp_version = '4.3';

		//Create Array of Tabs and Localize to Meta Script
		$tabs_array = array();

		foreach ( self::get_tabs() as $tab_slug => $tab ) {
			$tabs_array[ $tab ] = $tab_slug;
		}

		$tabs_json_array = json_encode( $tabs_array );

		$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : '';

		//Detect if we saved or tried to save to set the current tab.
		//todo remove coupon mentions from tabs
		//add in css and js to pngx
		global $message;
		$cctor_tabs_variables = array(
			'tabs_arr'             => $tabs_json_array,
			'cctor_coupon_updated' => $message,
			'cctor_coupon_id'      => $post_id,
		);

		wp_localize_script( 'cctor_coupon_meta_js', 'cctor_coupon_meta_js_vars', $cctor_tabs_variables );

		ob_start(); ?>

		<div class="pngx-tabs">

			<ul class="pngx-tabs-nav">

				<?php //Create Tabs
				foreach ( self::get_tabs() as $tab_slug => $tab ) {
					echo '<li><a href="#' . $tab_slug . '">' . $tab . '</a></li>';
				}
				?>
			</ul>

			<?php foreach ( self::get_tabs() as $tab_slug => $tab ) { ?>

				<div class="pngx-section-fields form-table">

					<h3 class="pngx-tab-heading-<?php echo $tab_slug; ?>"><?php echo $tab; ?></h3>

					<?php

					$help_class = new Cctor__Coupon__Admin__Help();
					$help_class->display_help( $tab_slug, false, 'coupon' );

					foreach ( self::get_fields() as $field ) {

						if ( $field['type'] && $field['section'] == $metabox['id'] && $tab_slug == $field['tab'] ) :

							// get value of this field if it exists for this post
							$meta      = get_post_meta( $post->ID, $field['id'], true );

							//Wrap Class for Conditionals
							$wrapclass = isset( $field['wrapclass'] ) ? $field['wrapclass'] : '';
							?>

							<div
								class="pngx-meta-field-wrap field-wrap-<?php echo esc_html( $field['type'] ); ?> field-wrap-<?php echo esc_html( $field['id'] ); ?> <?php echo esc_html( $wrapclass ); ?>">

								<?php if ( isset( $field['label'] ) ) { ?>

									<div
										class="pngx-meta-label label-<?php echo $field['type']; ?> label-<?php echo $field['id']; ?>">
										<label
											for="<?php echo $field['id']; ?>"><?php echo $field['label']; ?></label>
									</div>

								<?php } ?>

								<div
									class="pngx-meta-field field-<?php echo $field['type']; ?> field-<?php echo $field['id']; ?>">

									<?php switch ( $field['type'] ) {

										case 'heading':
											?>

											<h4 class="coupon-heading"><?php echo $field['desc']; ?></h4>

											<?php break;

										case 'message':
											?>

											<span class="description"><?php echo $field['desc']; ?></span>

											<?php break;

										// text
										case 'text':
											?>
											<?php if ( isset( $field['alert'] ) && $field['alert'] != '' && cctor_options( $field['condition'] ) == 1 ) {
											echo '<div class="pngx-error">&nbsp;&nbsp;' . $field['alert'] . '</div>';
										}
											?>
											<input type="text" name="<?php echo $field['id']; ?>"
											       id="<?php echo $field['id']; ?>"
											       value="<?php echo esc_attr( $meta ); ?>" size="30"/>
											<br/><span class="description"><?php echo $field['desc']; ?></span>

											<?php break;
										// url
										case 'url':
											?>
											<input type="text" name="<?php echo $field['id']; ?>"
											       id="<?php echo $field['id']; ?>"
											       value="<?php echo esc_url( $meta ); ?>" size="30"/>
											<br/><span class="description"><?php echo $field['desc']; ?></span>

											<?php break;
										// textarea
										case 'textarea': ?>
											<?php if ( version_compare( $wp_version, $cctor_required_wp_version, '<' ) ) { ?>
												<textarea name="<?php echo $field['id']; ?>"
												          id="<?php echo $field['id']; ?>" cols="60"
												          rows="4"><?php echo wp_htmledit_pre( $meta ); ?></textarea>
												<br/><span class="description"><?php echo $field['desc']; ?></span>
											<?php } else { ?>
												<textarea name="<?php echo $field['id']; ?>"
												          id="<?php echo $field['id']; ?>" cols="60"
												          rows="4"><?php echo format_for_editor( $meta ); ?></textarea>
												<br/><span class="description"><?php echo $field['desc']; ?></span>
											<?php } ?>
											<?php break;

										// checkbox
										case 'checkbox':

											//Check for Default
											global $pagenow;
											$selected = '';
											if ( $meta ) {
												$selected = $meta;
											} elseif ( $pagenow == 'post-new.php' && isset( $field['value'] ) ) {
												$selected = $field['value'];
											}

											?>

											<input type="checkbox" name="<?php echo $field['id']; ?>"
											       id="<?php echo $field['id']; ?>" <?php echo checked( $selected, 1, false ); ?>/>
											<label
												for="<?php echo $field['id']; ?>"><?php echo $field['desc']; ?></label>

											<?php break;

										case 'select':

											//Check for Default
											global $pagenow;
											$selected = '';
											if ( $meta ) {
												$selected = $meta;
											} elseif ( $pagenow == 'post-new.php' ) {
												$selected = isset( $field['value'] ) ? $field['value'] : '';
											}

											?>
											<select id="<?php echo $field['id']; ?>"
											        class="select <?php echo $field['id']; ?>"
											        name="<?php echo $field['id']; ?>">

												<?php foreach ( $field['choices'] as $value => $label ) {

													echo '<option value="' . esc_attr( $value ) . '"' . selected( $value, $selected ) . '>' . $label . '</option>';

												} ?>
											</select>
											<span class="description"><?php echo $field['desc']; ?></span>

											<?php break;
										// image using Media Manager from WP 3.5 and greater
										case 'image': ?>

											<?php //Check existing field and if numeric
											$image = "";

											if ( is_numeric( $meta ) ) {
												$image = wp_get_attachment_image_src( $meta, 'medium' );
												$image = $image[0];
												$image = '<div style="display:none" id="' . $field['id'] . '" class="cctor_coupon_default_image cctor_coupon_box">' . $field['image'] . '</div> <img src="' . $image . '" id="' . $field['id'] . '" class="cctor_coupon_image cctor_coupon_box_img" />';
											} else {
												$image = '<div style="display:block" id="' . $field['id'] . '" class="cctor_coupon_default_image cctor_coupon_box">' . $field['image'] . '</div> <img style="display:none" src="" id="' . $field['id'] . '" class="cctor_coupon_image cctor_coupon_box_img" />';
											} ?>

											<?php echo $image; ?><br/>
											<input name="<?php echo $field['id']; ?>"
											       id="<?php echo $field['id']; ?>" type="hidden"
											       class="upload_coupon_image" type="text" size="36" name="ad_image"
											       value="<?php echo esc_attr( $meta ); ?>"/>
											<input id="<?php echo $field['id']; ?>" class="coupon_image_button"
											       type="button" value="Upload Image"/>
											<small><a href="#" id="<?php echo $field['id']; ?>"
											          class="cctor_coupon_clear_image_button">Remove Image</a>
											</small>
											<br/><span class="description"><?php echo $field['desc']; ?></span>

											<?php break;
										// color
										case 'color': ?>
											<?php //Check if Values and If None, then use default
											if ( ! $meta ) {
												$meta = $field['value'];
											}
											?>
											<input class="color-picker" type="text"
											       name="<?php echo $field['id']; ?>"
											       id="<?php echo $field['id']; ?>"
											       value="<?php echo esc_attr( $meta ); ?>"
											       data-default-color="<?php echo $field['value']; ?>"/>
											<br/><span class="description"><?php echo $field['desc']; ?></span>

											<?php break;
										// date
										case 'date':

											//Blog Time According to WordPress
											$cctor_todays_date = "";
											if ( $field['id'] == "cctor_expiration" ) {
												$cc_blogtime = current_time( 'mysql' );

												list( $today_year, $today_month, $today_day, $hour, $minute, $second ) = preg_split( '([^0-9])', $cc_blogtime );

												if ( cctor_options( 'cctor_default_date_format' ) == 1 || $meta == 1 ) {
													$today_first  = $today_day;
													$today_second = $today_month;
												} else {
													$today_first  = $today_month;
													$today_second = $today_day;
												}

												$cctor_todays_date = '<span class="description">' . __( 'Today\'s Date is ', 'coupon-creator' ) . $today_first . '/' . $today_second . '/' . $today_year . '</span>';
											}
											?>

											<input type="text" class="datepicker" name="<?php echo $field['id']; ?>"
											       id="<?php echo $field['id']; ?>"
											       value="<?php echo esc_attr( $meta ); ?>" size="10"/>
											<br/><span class="description"><?php echo $field['desc']; ?></span>
											<?php echo $cctor_todays_date; ?>

											<?php break;
										// Videos
										case 'cctor_support':

											$help_class->display_help( 'all', false, 'coupon' );
											echo Cctor__Coupon__Admin__Help::get_cctor_support_core_contact();

											break;

										// Videos
										case 'cctor_pro':

											echo ! defined( 'CCTOR_HIDE_UPGRADE' ) || ! CCTOR_HIDE_UPGRADE ? Cctor__Coupon__Admin__Options::display_pro_section() : '';

											break;

									} //end switch

									if ( has_filter( 'cctor_filter_meta_cases' ) ) {
										/**
										 * Filter the cases for Coupon Creator Meta
										 *
										 * @param array $field current coupon meta field being displayed.
										 * @param array $meta  current value of meta saved.
										 * @param obj   $post  object of current post beign edited.
										 */
										echo apply_filters( 'cctor_filter_meta_cases', $field, $meta, $post );
									} ?>

								</div>
								<!-- end .pngx-meta-field.field-<?php echo $field['type']; ?>.field-<?php echo $field['id']; ?> -->

							</div> <!-- end .pngx-meta-field-wrap.field-wrap-<?php echo $field['type']; ?>.field-wrap-<?php echo $field['id']; ?>	-->

							<?php
						endif; //end if in section check

					} // end foreach fields?>

				</div>    <!-- end .coupon-section-fields.form-table -->

			<?php } // end foreach tabs?>

		</div>    <!-- end .pngx-tabs -->

		<?php
		echo ob_get_clean();
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
		if ( isset( $_POST['cctor_ignore_expiration'] ) &&  1 == $_POST['cctor_expiration_option'] ) {
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

}