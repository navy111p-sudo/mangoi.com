$(function(){
  'use strict';
  
	// Left Sidebar
	$('#menu-left').sideNav({
		menuWidth: 240, // Default is 240
		edge: 'left',
		closeOnClick: false // Closes side-nav on <a> clicks
	});
	// Right Sidebar
	$('#menu-right').sideNav({
		menuWidth: 240, // Default is 240
		edge: 'right',
		closeOnClick: false // Closes side-nav on <a> clicks
	});

	// Featured slider
	$('.featured-slider').slick({
		dots: true,
		arrows: false,
		autoplay: true,
	});

	// Sequence & drawer
	$('.maleo-sequence, .maleo-drawer').slick({
		dots: true,
		arrows: false,
		autoplay: false,
		speed: 200,
	});
	
	// Testimonial slider
	if ($(window).width() > 767) {
		$('.maleo-testimonial_slider').slick({
			slidesToShow: 4,
			slidesToScroll: 1,
			dots: true,
			arrows: false,
		});
	} else {
		$('.maleo-testimonial_slider').slick({
			slidesToShow: 1,
			slidesToScroll: 1,
			dots: true,
			arrows: false,
		});
	}
	
	// Swipebox gallery
	$( '.swipebox' ).swipebox();
	
	// Right sidebar tabs
	$('ul.tabs').tabs();

	// Scroll to top
	var winScroll = $(window).scrollTop();
	if (winScroll > 1) {
		$('#to-top').css({bottom:"10px"});
	} else {
		$('#to-top').css({bottom:"-100px"});
	}
	$(window).on("scroll",function(){
		winScroll = $(window).scrollTop();

		if (winScroll > 1) {
			$('#to-top').css({opacity:1,bottom:"30px"});
		} else {
			$('#to-top').css({opacity:0,bottom:"-100px"});
		}
	});
	$('#to-top').click(function(){
		$('html, body').animate({scrollTop: '0px'}, 800);
		return false;
	});

	$('#mix-wrapper').mixItUp({
		load: {
			sort: 'order:asc'
		},
		selectors: {
			target: '.mix-target',
			filter: '.filter-btn',
			sort: '.sort-btn'
		},
		callbacks: {
			onMixEnd: function(state){
				console.log(state)
			}
		}
	});

});