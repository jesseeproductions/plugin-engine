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
 *
 * @property array  post_type
 * @property string user_capability
 * @property array  tabs
 * @property array  fields
 */
class Pngx__Admin__Meta {

	protected static $instance;

	//tabs key and label
	protected static $tabs = array();

	//fields
	protected static $fields = array();

	//post type
	protected $post_type = array( 'pngx' );

	//user capability
	protected $user_capability = 'edit_post';

	/*
	* Construct
	*/
	public function __construct() {

		//Save Meta
		add_action( 'save_post', array( __CLASS__, 'save_meta' ), 10, 2 );

		//JS Error Check
		add_action( 'pngx_meta_message', array( __CLASS__, 'get_js_error_check_msg' ) );
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
	* Set Current Screen Variables
	*/
	public static function get_screen_variables() {

		global $pagenow, $typenow;
		$current_screen['pagenow'] = $pagenow;
		$current_screen['post']    = isset( $_GET['post'] ) ? $_GET['post'] : '';
		$current_screen['type']    = $typenow;

		if ( empty( $current_screen['type'] ) && ! empty( $current_screen['type'] ) ) {
			$current_screen['post_obj'] = get_post( $_GET['post'] );
			$current_screen['type']     = $current_screen['post_obj']->post_type;
		}

		return $current_screen;

	}

	/*
	* Get Post Type
	*/
	public static function get_post_types() {

		return self::instance()->post_type;

	}

	/*
	* Get User Capability
	*/
	public static function get_user_capability() {

		return self::instance()->user_capability;

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
	* Load Meta Box Functions
	*/
	public function set_fields() {

		$this->fields = Pngx__Meta__Fields::get_fields();
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

		<div class="main pngx-tabs" <?php echo Pngx__Admin__Fields::toggle( $tab_data, null ); ?> >

			<ul class="main pngx-tabs-nav">

				<?php //Create Tabs
				foreach ( self::get_tabs() as $tab_slug => $tab ) {
					echo '<li><a href="#' . $tab_slug . '">' . $tab . '</a></li>';
				}
				?>
			</ul>

			<?php foreach ( self::get_tabs() as $tab_slug => $tab ) {

				//set variable for template area
				$template_area = '';
				?>

				<div class="pngx-section-fields form-table">

					<h2 class="pngx-tab-heading-<?php echo $tab_slug; ?>"><?php echo $tab; ?></h2>

					<?php

					/**
					 * Hook to connect help section into a tab
					 *
					 * @parm $tab_slug string of current slug
					 */
					do_action( 'pngx_per_tab_help', $tab_slug );

					$fields = self::get_fields();

					foreach ( $fields as $field ) {

						if ( $field['type'] && $field['section'] === $metabox['id'] && ( isset( $field['tab'] ) && $tab_slug === $field['tab'] ) ) {

							// get value of this field if it exists for this post
							$meta = get_post_meta( $post->ID, $field['id'], true );

							//Wrap Class for Conditionals
							$wrapclass = isset( $field['wrapclass'] ) ? $field['wrapclass'] : '';

							//Template Wrap for AJAX
							if ( "template_start" === $field['type'] ) {
								//Start Template Section Wrap and set value for templates
								$template_select = get_post_meta( $post->ID, $wrapclass, true );
								$template_area   = ! empty( $template_select ) ? $template_select : 'default';
								?>
								<div class="pngx-meta-template-wrap template-wrap-<?php echo esc_html( $wrapclass ); ?>" >
								<?php
								continue;

							} elseif ( "template_end" === $field['type'] ) {
								//End Template Section Wrap
								$template_area = '';
								?>
								</div>
								<?php
								continue;
							}

							//if in template area only get fields with the template value
							if ( $template_area ) {
								$field_template = isset( $field['template'] ) ? $field['template'] : array();
								if ( ! in_array( $template_area, $field_template ) ) {
									continue;
								}

							} elseif ( ! $template_area && isset( $field['template'] ) ) {
								//if not template area set, but there is a template then do not display the field
								continue;
							}
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
									//todo do I need to change the null for repeater here?
									Pngx__Admin__Fields::display_field( $field, false, false, $meta, null );

									// Display admin linked style fields
									Pngx__Admin__Style__Linked::display_styles( $fields, $field, $post->ID );

									?>

								</div>
								<!-- end .pngx-meta-field.field-<?php echo $field['type']; ?>.field-<?php echo $field['id']; ?> -->

							</div> <!-- end .pngx-meta-field-wrap.field-wrap-<?php echo $field['type']; ?>.field-wrap-<?php echo $field['id']; ?>	-->

							<?php
						}//end if in section check

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

			//handle repeatable fields
			if ( 'repeatable' === $option['type'] && isset ( $_POST[ $option['id'] ] ) ) {

				//log_me($option);
				//log_me( $_POST );
				//log_me( count( $_POST[ $option['id'] ] ) );
				//log_me( $_POST[ $option['id'] ] );

				/***
				 * we are in repeater
				 * do we have columns?
				 * if no then set to 0
				 * if yes set number too loop through in this section
				 * loop through columns and save per column
				 * end section is there another
				 * if yes repeat
				 * if no, then finish
				 *
				 *
				 */

				if ( ! isset( ${'repeat_obj' . $option['id']} ) ) {
					${'repeat_obj' . $option['id']} = new Pngx__Admin__Repeater__Main( $option['id'], (int) count( $_POST[ $option['id'] ] ) );
				}

				$repeater_post = $_POST[ $option['id'] ];
				//log_me(${'repeat_obj' . $option['id']});
				/**
				 * Section Loop
				 */
				for ( $section_i = 0; $section_i < ${'repeat_obj' . $option['id']}->get_total_sections(); $section_i ++ ) {

					//log_me('section');
					//log_me(${'repeat_obj' . $option['id']}->get_total_sections() );
					//log_me($section_i);

					//$section_post = $repeater_post[ $section_i ];
/*
					$column_id = isset( $option['columns'] ) ? $option['columns'] . ${'repeat_obj' . $option['id']}->get_current_sec_col() : '';
					if ( $column_id && isset( $_POST[ $column_id ] ) ) {
						${'repeat_obj' . $option['id']}->set_columns( $_POST[ $column_id ] );
					}*/


					self::save_repeatable( $post_id, ${'repeat_obj' . $option['id']}, $repeater_post, $section_i, $option, self::get_fields() );


					/**
					 * Column Loop to Save all Fields in Column under one array
					 *
					 */
					/*for ( $col_i = 0; $col_i < ${'repeat_obj' . $option['id']}->get_total_columns(); $col_i ++ ) {

						//log_me('columns');
						//log_me(${'repeat_obj' . $option['id']}->get_total_columns() );
						//log_me($col_i);

						$column_postfix = ${'repeat_obj' . $option['id']}->get_current_sec_col();

						$col_saving_id = ${'repeat_obj' . $option['id']}->get_id() . $column_postfix;

						$old = get_post_meta( $post_id, $col_saving_id, true );
						$new = array();

						foreach ( $option['repeatable_fields'] as $repeater ) {

							//log_me( $repeater );

							$repeater_id = $repeater['id'] . $column_postfix;

							if ( ! isset( $section_post[ $repeater_id ] ) ) {
								continue;
							}

							$count = count( $section_post[ $repeater_id ] );

							for ( $i = 0; $i < $count; $i ++ ) {
								if ( '' != $section_post[ $repeater_id ][ $i ] ) {

									$sanitized = new Pngx__Sanitize( $repeater['type'], $section_post[ $repeater_id ][ $i ], $repeater );

									$new[ $repeater_id ] = $sanitized->result;

								}
							}

						}

						//log_me( 'old - new' );
						//log_me( $old );
						//log_me( $new );

						if ( ! empty( $new ) && $new != $old ) {
							update_post_meta( $post_id, $col_saving_id, $new );
						} elseif ( empty( $new ) && $old ) {
							delete_post_meta( $post_id, $col_saving_id, $old );
						}

						// Got to next custom field to save as repeatable field is done
						continue;

					} //End For Columns */

					${'repeat_obj' . $option['id']}->update_section_count();

				} // End For Section

				//save oount of sections
				$old = (int) get_post_meta( $post_id, $option['id'], true );
				$new = (int) ${'repeat_obj' . $option['id']}->get_total_sections();

				if ( ! empty( $new ) && $new != $old ) {
					update_post_meta( $post_id, $option['id'], absint( $new ) );
				} elseif ( empty( $new ) && $old ) {
					delete_post_meta( $post_id, $option['id'], absint( $old ) );
				}

				continue;

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


	/*
	* Save Repeatable
	*/
	public static function save_repeatable( $post_id, $repeat_obj, $repeater_post, $section_i, $option, $options ) {

		/**
		 * Section Loop
		 */
		//for ( $section_i = 0; $section_i < $repeat_obj->get_total_sections(); $section_i ++ ) {

			log_me('save_repeatable');
			//log_me($repeat_obj->get_total_sections() );
			//log_me($repeater_post);
			log_me($section_i);

			$section_post = $repeater_post[ $section_i ];

			$column_id = isset( $option['columns'] ) ? $option['columns'] . $repeat_obj->get_current_sec_col() : '';
			if ( $column_id && isset( $_POST[ $column_id ] ) ) {
				$repeat_obj->set_columns( $_POST[ $column_id ] );
			}

			/**
			 * Column Loop to Save all Fields in Column under one array
			 *
			 */
			for ( $col_i = 0; $col_i < $repeat_obj->get_total_columns(); $col_i ++ ) {

				//log_me('columns');
				//log_me($repeat_obj->get_total_columns() );
				//log_me($col_i);

				$column_postfix = $repeat_obj->get_current_sec_col();

				$col_saving_id = $repeat_obj->get_id() . $column_postfix;

				$old = get_post_meta( $post_id, $col_saving_id, true );
				$new = array();

				foreach ( $option['repeatable_fields'] as $repeater ) {

					if ( 'wpe_menu_r_column' ===  $repeater['id'] ) {
						//log_me( $repeater );
					}

					// if repeater in repeater then run through its fields to save
					if ( isset( $repeater['inside_repeating'] ) && $options[ $repeater['inside_repeating'] ] ) {
						// log_me( 'repeater-repeater' );
						//log_me( $options[ $repeater['inside_repeating'] ] );
						self::save_repeatable( $post_id, $repeat_obj, $repeater_post, $section_i, $options[ $repeater['inside_repeating'] ], $options );
					}

					$repeater_id = $repeater['id'] . $column_postfix;

					if ( ! isset( $section_post[ $repeater_id ] ) ) {
						continue;
					}

					$count = count( $section_post[ $repeater_id ] );

					for ( $i = 0; $i < $count; $i ++ ) {
						if ( '' != $section_post[ $repeater_id ][ $i ] ) {

							$sanitized = new Pngx__Sanitize( $repeater['type'], $section_post[ $repeater_id ][ $i ], $repeater );

							$new[ $repeater_id ] = $sanitized->result;

						}
					}

				}

				//log_me( 'old - new' );
				//log_me( $old );
				//log_me( $new );

				if ( ! empty( $new ) && $new != $old ) {
					update_post_meta( $post_id, $col_saving_id, $new );
				} elseif ( empty( $new ) && $old ) {
					delete_post_meta( $post_id, $col_saving_id, $old );
				}

				// Got to next custom field to save as repeatable field is done
				continue;

			} //End For Columns

		/*	$repeat_obj->update_section_count();

		} // End For Section

		//save oount of sections
		$old = (int) get_post_meta( $post_id, $option['id'], true );
		$new = (int) $repeat_obj->get_total_sections();

		if ( ! empty( $new ) && $new != $old ) {
			update_post_meta( $post_id, $option['id'], absint( $new ) );
		} elseif ( empty( $new ) && $old ) {
			delete_post_meta( $post_id, $option['id'], absint( $old ) );
		}*/


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