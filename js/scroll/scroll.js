// JavaScript Document
   $(document).ready(function(){
	/*carousel*/
	var owl = $("#owl"); 
		owl.owlCarousel({
		items : 4, //10 items above 1000px browser width
		itemsDesktop : [995,3], //5 items between 1000px and 901px
		itemsDesktopSmall : [767, 2], // betweem 900px and 601px
		itemsTablet: [700, 2], //2 items between 600 and 0
		itemsMobile : [479, 1], // itemsMobile disabled - inherit from itemsTablet option
		navigation : true,
		pagination :  false
		});


	 /*Back to Top*/
	$().UItoTop({ easingType: 'easeOutQuart' });

 }); 