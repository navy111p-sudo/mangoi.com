// JavaScript Document

$(document).ready(function(){

		
	// 네비게이션 토글
	$('.navi_mobile').click(function(e){
		e.preventDefault();
		$('.navi').slideToggle(200);
	});	
	// 원페이지 스크롤
	var navi=$('.navi li');
	var contents=$('.wrap > section');
	navi.click(function(e){
		e.preventDefault();
		
		var k=$(this).index();
		var section=contents.eq(k);
		var off_top=section.offset().top;
		var Width=$(window).width();
		
		$('html, body').stop().animate({scrollTop:off_top});
		if(Width <= 1024){
			$('.navi').slideUp(300);
		}
	});
	$(window).on('scroll',function(){
		var scrollTop=$(this).scrollTop();
		
		if(scrollTop==0){			
			$('header').removeClass('change');
		}else{
			$('header').addClass('change');
		}
		
		contents.each(function(k){
			if(scrollTop >= $(this).offset().top - 10){
				navi.find('a').removeClass('active');
				navi.find('a').eq(k).addClass('active');
			}
		});
	});
	
	// 하단 고정
	$('.search').each(function(){
		var $window=$(window);
		var $search=$(this);
		var whiteOffsetTop=$('.white').offset().top;
		
		$window.on('scroll',function(){
			if($window.scrollTop() > whiteOffsetTop){
				$search.addClass('change');	
			}else{
				$search.removeClass('change');	
			}
		});
	});		

	
	// 탑 스크롤
	$('.scroll').click(function(e){
		e.preventDefault();
		$('html, body').animate({scrollTop:0});
	});
	$(window).scroll(function(){
		var scrollTop=$(this).scrollTop();
		if(scrollTop > 500){
			$('.scroll').fadeIn(200);	
		}else{
			$('.scroll').fadeOut(200);
		}
	});
	$(window).trigger('scroll');

	// 로드맵 슬라이드
	var winWidth=$(window).width();
	var n;

	if(winWidth>=1024){
		n=5;
	}else if(winWidth>=768){
		n=4;
	}else if(winWidth>=640){
		n=3;
	}else{
		n=2;	
	}
	
	var q=0;
	var W=$('.road_list > li').width();
	var count=$('.road_list > li').length - n;
	$('.arrow_right').click(function(e){
		e.preventDefault();
		if(q==count){
			q=count;
		}else{
			q++;
		}
		$('.road_list').animate({marginLeft:-W*q+'px'},200);
	});
	$('.arrow_left').click(function(e){
		e.preventDefault();
		if(q==0){
			q=0;
		}else{
			q--;	
		}
		$('.road_list').animate({marginLeft:-W*q+'px'},200);
	});
	
});
