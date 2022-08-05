<?php
/**
 * Tabs View Template
 * The base template for Pngx Tabs.
 *
 * Override this template in your own theme by creating a file at [your-theme]/pngx/tabs/tabs.php
 *
 * @since TBD
 *
 * @package Pngx
 * @version TBD
 */

/** @var \Pngx\Tabs\View $tabs_view */
$tabs_view = pngx( 'pngx.tabs.view' );
$vars        = get_defined_vars();
?>
<div id="<?php echo $id; ?>" <?php pngx_classes( $wrapper_classes ) ?>>
	<ul>
	<?php foreach( $content as $key => $tab ) { ?>
		<li>
			<a href="#<?php echo esc_attr( $id .'-'. $key ) ?>>"><?php echo $tab['tab-link'] ?></a>
		</li>
	<?php } ?>
	</ul>

	<?php foreach( $content as $key => $tab ) { ?>
		<section id="<?php echo esc_attr( $id .'-'. $key ) ?>" <?php pngx_classes( $content_classes ) ?>>
			<?php echo $tab['tab-content'] ?>
		</section>
	<?php } ?>
</div>
