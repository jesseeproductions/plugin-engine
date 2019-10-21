<?php
/**
 * Carousel View Template
 * The base template for Pngx Carousels.
 *
 * Override this template in your own theme by creating a file at [your-theme]/pngx/carousels/carousel.php
 *
 * @since TBD
 *
 * @package Pngx
 * @version TBD
 */

// grab allthevars!
$vars        = get_defined_vars();
?>
<div id="carousel_obj_<?php echo esc_html( $vars['id'] ) ?>" <?php pngx_classes(  $wrapper_classes ) ?>>
	<?php echo $content; ?>
</div>