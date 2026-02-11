$(document).ready(function () {

	
    // 네비게이션 토글
    $('.navi_mobile_bar').click(function (e) {
        e.preventDefault();
        $('.navi_lnb').slideToggle(200);
    });
	$('.toggle_navi').mouseenter(function(e){
		e.preventDefault();
		$(this).find('.sub_navi').slideDown(200);
	});
	$('.toggle_navi').mouseleave(function(e){
		e.preventDefault();
		$(this).find('.sub_navi').slideUp(200);
	});
    
    // 서브 비주얼
	$('.sub_visual_area').each(function(){
		var $slides=$(this).find('li');
		var count=$slides.length;
		var current=0;
		
		$slides.eq(current).fadeIn(600);
		
		setInterval(nextslide, 3000);
		
		function nextslide(){
			var next=(current+1)%count
			$slides.eq(current).fadeOut(600);
			$slides.eq(next).fadeIn(600);
			current=next;	
		}
	});
	
	// 메인 코스 페이드인/아웃 
	var k=0;
	var size=$('.main_banner_fade .main_banner_area').length-1;
	var move;
	$('.banner_next').click(function(e){
		e.preventDefault();
		if(k==size){
			k=0;
		}else{
			k++;	
		}
		$('.main_banner_fade .main_banner_area').stop().fadeOut(500);
		$('.main_banner_fade .main_banner_area').eq(k).stop().fadeIn(500);
	});
	$('.banner_prev').click(function(e){
		e.preventDefault();
		if(k==0){
			k=size;
		}else{
			k--;	
		}
		$('.main_banner_fade .main_banner_area').stop().fadeOut(500);
		$('.main_banner_fade .main_banner_area').eq(k).stop().fadeIn(500);
	});	
	
	timer();
	function timer(){
		move=setInterval(function(){
			$('.banner_next').trigger('click');
		},3500);
	}
	
	// 레벨테스트 강사 선택
	$('.level_teacher_select_btn').click(function(e){		
		e.preventDefault();
		$('.level_teacher_select_btn').removeClass('active');		

		$('.level_teacher_time_wrap').stop().slideUp(200);		

		if(!$(this).parent().next().is(":visible"))
		{
			$(this).parent().next().stop().slideDown(200);
			$(this).addClass('active');	
		}	
	});
	
	// 서브 강사 선택
	$('.teacher_select_btn').click(function(e){		
		e.preventDefault();
		$('.teacher_select_btn').removeClass('active');		

		$('.teacher_time_wrap').stop().slideUp(200);		

		if(!$(this).parent().next().is(":visible"))
		{
			$(this).parent().next().stop().slideDown(200);
			$(this).addClass('active');	
		}	
	});
	
	// 자주 묻는 질문
	$('.faq_sub_button').click(function(e){		
		e.preventDefault();
		$('.faq_arrow').removeClass('active');		

		$('.faq_a').stop().slideUp(200);		

		if(!$(this).next().is(":visible"))
		{
			$(this).next().stop().slideDown(200);
			$(this).find('.faq_arrow').addClass('active');	
		}	
	});	
	
	// 1:1 문의
	$('.mantoman_btn').click(function(e){		
		e.preventDefault();
		$('.mantoman_btn').removeClass('active');		

		$('.mantoman_a').stop().slideUp(200);		

		if(!$(this).parent().parent().next().is(":visible"))
		{
			$(this).parent().parent().next().stop().slideDown(200);
			$(this).addClass('active');	
		}	
	});	
	
	// 라이트박스
	$('.light_box_btn').click(function(e){	
		e.preventDefault();	
		$('.light_box_wrap').fadeIn(200);
		$('body').css('overflow','hidden');
	});
	$('.light_box_close').click(function(e){
		e.preventDefault();		
		$('.light_box_wrap').fadeOut(200);
		$('body').css('overflow','auto');
    });
    $('.light_box_cancle').click(function (e) {
        e.preventDefault();
        $('.light_box_wrap').fadeOut(200);
        $('body').css('overflow', 'auto');
    });
	
	$('.light_box_btn_photo').click(function(e){	
		e.preventDefault();	
		$('.light_box_wrap.photo_change').fadeIn(200);
		$('body').css('overflow','hidden');
	});
	$('.light_box_close').click(function(e){
		e.preventDefault();		
		$('.light_box_wrap.photo_change').fadeOut(200);
		$('body').css('overflow','auto');
    });	
    $('.light_box_cancle').click(function (e) {
        e.preventDefault();
        $('.light_box_wrap.photo_change').fadeOut(200);
        $('body').css('overflow', 'auto');
    });	
	
	$('.light_box_btn_youtube').click(function(e){	
		e.preventDefault();	
		$('.light_box_wrap_youtube').fadeIn(200);
		$('body').css('overflow','hidden');
	});
	$('.light_box_close').click(function(e){
		e.preventDefault();		
		$('.light_box_wrap_youtube').fadeOut(200);
		$('body').css('overflow','auto');
    });	

});
