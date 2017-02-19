<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
if ( class_exists( 'Pngx__Admin__Field__Repeatable' ) ) {
	return;
}


/**
 * Class Pngx__Admin__Field__Repeatable
 * Repeatable Field
 */
class Pngx__Admin__Field__Repeatable {

	public static function display( $field = array(), $options = array(), $options_id = null, $meta = null, $repeat_obj = null ) {

		if ( ! isset( $field['repeatable_fields'] ) || ! is_array( $field['repeatable_fields'] ) ) {
			return;
		}

		global $post;
		if ( ! $repeat_obj ) {
			$repeat_obj = new Pngx__Admin__Repeater__Main( $field['id'], (int) $meta );
		}

		$class = isset( $field['class'] ) ? $field['class'] : '';

		?>
		<ul
				id="wpe_menu_section-repeatable"
				class="pngx-repeatable repeating-section"
				data-name_id="wpe_menu_section"
				data-ajax_field_id="wpe_menu_section"
				data-ajax_action="pngx_repeatable"
				data-repeat-type="section"
				data-section=0
				data-column=0
		>
			<li class="repeatable-item repeatable-section 0">
				<span class="sort hndle">|||</span>
				<h5>Section 0</h5>




				<div class="pngx-meta-field-wrap field-repeating-column">
					<ul
							id="wpe_menu_r_column-repeatable"
							class="pngx-repeatable repeating-column"
							data-name_id="wpe_menu_r_column"
							data-ajax_field_id="wpe_menu_r_column"
							data-ajax_action="pngx_repeatable"
							data-repeat-type="column"
							data-section=0
							data-column=0
					>

						<li class="repeatable-item repeatable-column 0">
							<span class="sort hndle">|||</span>
							<h5>Column 0</h5>



									<div class="pngx-meta-field-wrap field-repeating-section">

										<ul
												id="wpe_menu_items-repeatable"
												class="pngx-repeatable repeating-column"
												data-name_id="wpe_menu_items"
												data-ajax_field_id="wpe_menu_items"
												data-ajax_action="pngx_repeatable"
												data-repeat-type="section"
												data-section=0
												data-column=0
										>
											<li class="repeatable-item repeatable-section 0">
												<span class="sort hndle">|||</span>
												<h5>Child Section 0</h5>


												<div class="pngx-meta-field-wrap field-wrap-text field-wrap-cctor_highlight_title ">
													<div class="pngx-meta-field field-text field-cctor_highlight_title">
														<input id="cctor_highlight_title" class="regular-text" name="wpe_section[0][column][0][items][0][title]" value="Field 1 0 col 0" size="30" type="text">
													</div>
												</div>

												<div class="pngx-meta-field-wrap field-wrap-text field-wrap-cctor_highlight_title ">
													<div class="pngx-meta-field field-text field-cctor_highlight_title">
														<input id="cctor_highlight_title" class="regular-text" name="wpe_section[0][column][0][items][0][place]" value="Field 2 0 col 0" size="30" type="text">
													</div>
												</div>

												<div class="pngx-meta-field-wrap field-repeating-field">
													<ul
															id="wpe_menu_items-repeatable"
															class="pngx-repeatable repeating-column"
															data-name_id="wpe_menu_items"
															data-ajax_field_id="wpe_menu_items"
															data-ajax_action="pngx_repeatable"
															data-repeat-type="section"
															data-section=0
															data-column=0
													>
														<li class="repeatable-item repeatable-field 0">
															<span class="sort hndle">|||</span>
															<h5>Field Repeat 0</h5>


															<div class="pngx-meta-field-wrap field-wrap-text field-wrap-cctor_highlight_title ">
																<div class="pngx-meta-field field-text field-cctor_highlight_title">
																	<input id="cctor_highlight_title" class="regular-text" name="wpe_section[0][column][0][items][0][price][]" value="Price 1" size="30" type="text">
																</div>
															</div>

															<div class="pngx-meta-field-wrap field-wrap-text field-wrap-cctor_highlight_title ">
																<div class="pngx-meta-field field-text field-cctor_highlight_title">
																	<input id="cctor_highlight_title" class="regular-text" name="wpe_section[0][column][0][items][0][price][]" value="Price 2" size="30" type="text">
																</div>
															</div>


															<h5>Field Repeat 0</h5>
															<a class="add-repeatable button"
															   data-repeater="<?php echo esc_attr( $field['id'] ); ?>-repeatable"
															   href="#"
															>+</a>
															<a class="remove-repeatable button"
															   data-repeater="<?php echo esc_attr( $field['id'] ); ?>-repeatable"
															   href="#"
															>X</a>
														</li>
													</ul>
												</div>


												<h5>Child Section 0</h5>
												<a class="add-repeatable button"
												   data-repeater="<?php echo esc_attr( $field['id'] ); ?>-repeatable"
												   href="#"
												>+</a>
												<a class="remove-repeatable button"
												   data-repeater="<?php echo esc_attr( $field['id'] ); ?>-repeatable"
												   href="#"
												>X</a>
											</li>
										</ul>
									</div>





							<h5>Column 0</h5>
							<a class="add-repeatable button"
							   data-repeater="<?php echo esc_attr( $field['id'] ); ?>-repeatable"
							   href="#"
							>+</a>
							<a class="remove-repeatable button"
							   data-repeater="<?php echo esc_attr( $field['id'] ); ?>-repeatable"
							   href="#"
							>X</a>
						</li>

						<li class="repeatable-item repeatable-column 1">
							<span class="sort hndle">|||</span>
							<h5>Column 1</h5>



									<div class="pngx-meta-field-wrap field-repeating-section">
										<ul
												id="wpe_menu_items-repeatable"
												class="pngx-repeatable repeating-column"
												data-name_id="wpe_menu_items"
												data-ajax_field_id="wpe_menu_items"
												data-ajax_action="pngx_repeatable"
												data-repeat-type="section"
												data-section=0
												data-column=0
										>
											<li class="repeatable-item repeatable-section 0">
												<span class="sort hndle">|||</span>
												<h5>Child Section 1</h5>


												<div class="pngx-meta-field-wrap field-wrap-text field-wrap-cctor_highlight_title ">
													<div class="pngx-meta-field field-text field-cctor_highlight_title">
														<input id="cctor_highlight_title" class="regular-text" name="wpe_section[0][column][1][items][0][title]" value="Field 1 0 col 1" size="30" type="text">
													</div>
												</div>

												<div class="pngx-meta-field-wrap field-wrap-text field-wrap-cctor_highlight_title ">
													<div class="pngx-meta-field field-text field-cctor_highlight_title">
														<input id="cctor_highlight_title" class="regular-text" name="wpe_section[0][column][1][items][0][place]" value="Field 2 0 col 1" size="30" type="text">
													</div>
												</div>

													<div class="pngx-meta-field-wrap field-repeating-section">

														<ul
																id="wpe_menu_items-repeatable"
																class="pngx-repeatable repeating-column"
																data-name_id="wpe_menu_items"
																data-ajax_field_id="wpe_menu_items"
																data-ajax_action="pngx_repeatable"
																data-repeat-type="section"
																data-section=0
																data-column=0
														>
															<li class="repeatable-item repeatable-section 0">
																<span class="sort hndle">|||</span>
																<h5>Sub Child Section 0</h5>


																<div class="pngx-meta-field-wrap field-wrap-text field-wrap-cctor_highlight_title ">
																	<div class="pngx-meta-field field-text field-cctor_highlight_title">
																		<input id="cctor_highlight_title" class="regular-text" name="wpe_section[0][column][0][items][0][items][0][title]" value="Nested Field 1 0 col 1" size="30" type="text">
																	</div>
																</div>

																<div class="pngx-meta-field-wrap field-wrap-text field-wrap-cctor_highlight_title ">
																	<div class="pngx-meta-field field-text field-cctor_highlight_title">
																		<input id="cctor_highlight_title" class="regular-text" name="wpe_section[0][column][0][items][0][items][0][place]" value="Nested Field 2 0 col 1" size="30" type="text">
																	</div>
																</div>

																<h5>Sub Child Section 0</h5>
																<a class="add-repeatable button"
																   data-repeater="<?php echo esc_attr( $field['id'] ); ?>-repeatable"
																   href="#"
																>+</a>
																<a class="remove-repeatable button"
																   data-repeater="<?php echo esc_attr( $field['id'] ); ?>-repeatable"
																   href="#"
																>X</a>
															</li>
														</ul>
													</div>

												<h5>Child Section 1</h5>
												<a class="add-repeatable button"
												   data-repeater="<?php echo esc_attr( $field['id'] ); ?>-repeatable"
												   href="#"
												>+</a>
												<a class="remove-repeatable button"
												   data-repeater="<?php echo esc_attr( $field['id'] ); ?>-repeatable"
												   href="#"
												>X</a>
											</li>
										</ul>
									</div>





							<h5>Column 1</h5>
							<a class="add-repeatable button"
							   data-repeater="<?php echo esc_attr( $field['id'] ); ?>-repeatable"
							   href="#"
							>+</a>
							<a class="remove-repeatable button"
							   data-repeater="<?php echo esc_attr( $field['id'] ); ?>-repeatable"
							   href="#"
							>X</a>
						</li>

				<h5>Section 0</h5>
				<a class="add-repeatable button"
				   data-repeater="<?php echo esc_attr( $field['id'] ); ?>-repeatable"
				   href="#"
				>+</a>
				<a class="remove-repeatable button"
				   data-repeater="<?php echo esc_attr( $field['id'] ); ?>-repeatable"
				   href="#"
				>X</a>
			</li>
		</ul>

		<?php

	}

	public static function display_old( $field = array(), $options = array(), $options_id = null, $meta = null, $repeat_obj = null ) {

		//log_me($field['id']);
		// log_me($meta);
		if ( ! isset( $field['repeatable_fields'] ) || ! is_array( $field['repeatable_fields'] ) ) {
			return;
		}

		global $post;

		if ( ! $repeat_obj ) {
			$repeat_obj = new Pngx__Admin__Repeater__Main( $field['id'], (int) $meta );
		}

		//log_me( 'repeat meta' );
		//log_me( count( $meta ) );
		//log_me( $repeat_obj );
		//log_me( $meta );

		$class = isset( $field['class'] ) ? $field['class'] : '';

		$options[] = array(
			'wrap'  => 'li',
			'class' => 'repeatable-item',
		);

		$repeating_type = '';
		if ( $repeat_obj->get_repeating_sections_status() ) {
			$repeating_type = 'section';
		} elseif ( $repeat_obj->get_repeating_columns_status() ) {
			$repeating_type = 'column';
		}

		?>
		<ul
				id="<?php echo esc_attr( $field['id'] ); ?>-repeatable"
				class="pngx-repeatable <?php echo esc_attr( $class ); ?>"
				data-clone="<?php echo esc_attr( json_encode( $options ) ); ?>"
				data-name_id="<?php echo esc_attr( $repeat_obj->get_id() ); ?>"
				data-ajax_field_id="<?php echo esc_attr( $field['id'] ); ?>"
				data-ajax_action="pngx_repeatable"
				data-repeat-type="<?php echo esc_attr( $repeating_type ); ?>"
		>

			<?php


			$count = $repeat_obj->get_total_sections();
			if ( $repeat_obj->get_repeating_columns_status() ) {
				$count = $repeat_obj->get_total_columns();
			}

			for ( $i = 0; $i < $count; $i ++ ) {
				//log_me( 'repeat values' );
				//log_me( $repeat_obj->get_meta_id() );
				$section = get_post_meta( $post->ID, $repeat_obj->get_meta_id(), true );

				if ( empty( $section ) ) {
					self::display_repeat_section( $field['repeatable_fields'], $field, null, $repeat_obj, $meta );
					continue;
				}

				//log_me( $section );

				if ( ! is_array( $section ) ) {
					self::display_repeat_section( $field['repeatable_fields'], $field, $section, $repeat_obj, $meta );
					continue;
				}

				self::display_repeat_section( $field['repeatable_fields'], $field, $section, $repeat_obj, $meta );

				/*foreach ( $section as $row ) {
					//log_me( 'row' );
					//log_me( $row );
					if ( ! is_array( $row ) ) {
						self::display_repeat_section( $field['repeatable_fields'], $field, $row, $repeat_obj, $meta );
						continue;
					}

					//foreach( $row as $value ) {
					//log_me('$value');
					//log_me($value);
					self::display_repeat_section( $field['repeatable_fields'], $field, $row, $repeat_obj, $meta );
					// }
				}*/

				if ( $repeat_obj->get_repeating_sections_status() ) {
					$repeat_obj->update_section_count();
				} elseif ( $repeat_obj->get_repeating_columns_status() ) {
					$repeat_obj->update_column_count();
				}

			}

			?>
		</ul>

		<?php

	}

	public static function display_repeat_section_old( $fields = array(), $parent = array(), $section = null, $repeat_obj, $meta = null ) {

		if ( ! is_object( $repeat_obj ) ) {
			return;
		}

		?>

		<li class="repeatable-item repeatable-item<?php echo esc_attr( $repeat_obj->get_current_sec_col() ); ?>">

			<span class="sort hndle">|||</span>
			<?php

			$field_count = isset( $parent['repeatable_fields'] ) ? count( $parent['repeatable_fields'] ) : 0;
			log_me( 'field count' );
			log_me( $field_count );
			//log_me($fields);
			//for ( $i = 0; $i < $field_count; $i ++ ) {
			foreach ( $fields as $repeater ) {
				log_me( ' ' );
				log_me( $repeater );
				log_me( $section );
				log_me( $repeater['id'] );

				$repeat_field_val = isset( $section[ $repeater['id'] . $repeat_obj->get_current_sec_col() ] ) ? $section[ $repeater['id'] . $repeat_obj->get_current_sec_col() ] : '';
				log_me( $repeat_field_val );
				self::display_repeat_row( $repeater, $parent, $repeat_field_val, $repeat_obj, null );

			}
			//}

			?>
			<a class="add-repeatable button"
			   data-repeater="<?php echo esc_attr( $parent['id'] ); ?>-repeatable"
			   data-section="<?php echo absint( $repeat_obj->get_current_section() ); ?>"
			   data-column="<?php echo esc_attr( $repeat_obj->get_current_column() ); ?>"
			   href="#"
			>+</a>
			<a class="remove-repeatable button"
			   data-repeater="<?php echo esc_attr( $parent['id'] ); ?>-repeatable"
			   data-section="<?php echo absint( $repeat_obj->get_current_section() ); ?>"
			   data-column="<?php echo esc_attr( $repeat_obj->get_current_column() ); ?>"
			   href="#"
			>X</a>
		</li>
		<?php

	}

	public static function display_repeat_row_old( $field = array(), $parent = array(), $repeat_field_val = null, $repeat_obj, $i ) {

		if ( isset( $field['child_repeater'] ) ) {
			log_me( 'child repeater' );
			self::display_repeat_fields( $field, false, $repeat_obj, null );

			return;

		}


		// if no values then display empty fields
		if ( empty( $repeat_field_val ) ) {
			log_me( 'empty row' );
			self::display_repeat_fields( $field, false, $repeat_obj, null );

			return;
		}


		//$repeat_field_val = isset( $section[ $field['id'] . $repeat_obj->get_current_sec_col() ] ) ? $section[ $field['id'] . $repeat_obj->get_current_sec_col() ] : '';

		// if we have a repeating field of values display
		if ( is_array( $repeat_field_val ) && ( isset( $field['repeating_type'] ) && 'field' === $field['repeating_type'] ) ) {

			foreach ( $repeat_field_val as $row ) {
				log_me( 'row single repeating field' );
				///log_me( $row );
				self::display_repeat_fields( $field, $row, $repeat_obj, null );
			}

			return;

		}


		/*
				$value_count = is_array( $repeat_field_val ) ? count( $repeat_field_val ) : 0;

				log_me( 'count' );
				log_me( $value_count );
				log_me( $repeat_field_val );

				//we need to get all values for a section of repeating fields and nothing more

				if ( 0 < $value_count ) {
					for ( $i = 0; $i < $value_count; $i ++ ) {
						log_me( 'value for' );
						log_me( $repeat_field_val );
						log_me( $repeat_field_val[ $i ] );
						self::display_repeat_fields( $field, $repeat_field_val[ $i ], $repeat_obj, null );

					}

					//continue;
				} elseif ( 0 < $value_count ) {

					self::display_repeat_fields( $field, false, $repeat_obj, null );

				}*/

		/*for ( $i = 0; $i < $count; $i ++ ) {

			if ( is_array( $repeat_field_val ) && ( isset( $repeater['repeating_type'] ) && 'field' === $repeater['repeating_type'] ) ) {

				foreach ( is_array( $repeat_field_val ) as $row ) {
					log_me( 'row1' );
					log_me( $row );
					self::display_repeat_fields( $repeater, $row, $repeat_obj, $meta );
				}

			} elseif ( is_array( $repeat_field_val ) ) {
				// what can I use here to signal this should be a section and not a repeating of a field?
				foreach ( $repeat_field_val as $row ) {
					log_me( 'row2' );
					log_me( $row );
					// self:: display_repeat_section( $repeater, $parent, $row, $repeat_obj, null );
					self::display_repeat_fields( $repeater, $row, $repeat_obj, $meta );
				}

			}
		}*/

	}

	public static function display_repeat_fields_old( $field = array(), $row = null, $repeat_obj, $meta = null ) {

		?>
		<div class="field-wrap-repeatable field-wrap-<?php echo esc_html( $field['type'] ); ?>
  	                field-wrap-repeatable-<?php echo esc_html( $field['id'] ); ?>"
			<?php echo isset( $field['toggle'] ) ? Pngx__Admin__Fields::toggle( $field['toggle'], $field['id'] ) : null; ?>
		>
			<?php if ( isset( $field['label'] ) ) { ?>

				<div class="pngx-meta-label label-<?php echo $field['type']; ?> label-<?php echo $field['id'] . $repeat_obj->get_current_sec_col(); ?>">
					<label for="<?php echo $field['id'] . $repeat_obj->get_current_sec_col(); ?>"><?php echo $field['label']; ?></label>
				</div>

			<?php } ?>

			<div class="pngx-meta-field field-<?php echo $field['type']; ?> field-<?php echo $field['id']; ?>">

				<?php

				/*if ( is_array( $row ) && 'field' === $field['repeating_type'] ) {
				    log_me('display_field1');
					foreach ( $row as $value ) {
                        log_me('display_field2');
						Pngx__Admin__Fields::display_field( $field, false, false, $value, $repeat_obj );
					}
				} else {*/
				log_me( 'display_field3' );
				Pngx__Admin__Fields::display_field( $field, false, false, $row, $repeat_obj );
				//}


				?>

			</div>

		</div>
		<?php

	}

}
