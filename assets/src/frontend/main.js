(function ($) {
	$('body').find('.client-testimonials').each(function (){
		var _CT = $(this);
		if (jQuery().owlCarousel) {
			_CT.owlCarousel({
				loop: _CT.data('loop'),
				nav: _CT.data('nav'),
				autoplay: _CT.data('autoplay'),
				responsive: {
					320: {items: _CT.data('mobile')},
					768: {items: _CT.data('tablet')},
					992: {items: _CT.data('desktop')},
					1192: {items: _CT.data('widescreen')},
					1384: {items: _CT.data('fullhd')}
				}
			});
		}
	});
})(jQuery);