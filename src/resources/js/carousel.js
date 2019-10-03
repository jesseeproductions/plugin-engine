var pngx = pngx || {};
pngx.carousels = pngx.carousels || {};

(function ( $, obj ) {
	'use strict';

	document.addEventListener(
		'DOMContentLoaded',
		function () {

			pngx.carousels.forEach( function ( carousel ) {
				var objName = '#carousel_obj_' + carousel.id;

				console.log(objName, carousel);

				$( objName ).slick( {
					accessibility: carousel.accessibility,
					autoplay: carousel.autoplay,
					autoplaySpeed: carousel.autoplaySpeed,
					arrows: carousel.arrows,
					prevArrow: carousel.prevArrow,
					nextArrow: carousel.nextArrow,
					draggable: carousel.draggable,
					focusOnSelect: carousel.focusOnSelect,
					infinite: carousel.infinite,
					pauseOnFocus: carousel.pauseOnFocus,
					pauseOnHover: carousel.pauseOnHover,
					responsive: carousel.responsive,
					slidesToShow: carousel.slidesToShow,
					slidesToScroll: carousel.slidesToScroll
				} );
			} );
		}
	)
})( jQuery, pngx.carousel );
