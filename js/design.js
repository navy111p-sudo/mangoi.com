// JavaScript Document
$(function(){
 
 //gnb
 setLayout();
 
 //모바일메뉴
 $('.mobile_menu>ul>li>a').toggle(function(){
  $(this).siblings(".submenu").slideDown("fast");
 },function(){
  $(this).siblings(".submenu").slideUp("fast");
 });
 
 //모바일메뉴 현재메뉴 오픈
 open_cur_m_menu();
 
 //모바일메뉴 출력
 $("a.open_m_menu").click(function(){
  $(".mobile_menu").animate({right:"0px"},300,"easeOutQuart");
  return false;
 });
 $("a.close_m_menu").click(function(){
  $(".mobile_menu").animate({right:"-250px"},300,"easeOutQuart");
  return false;
 });
 
 //input file 디자인
// $("input[type=file]").filestyle({ 
//     image: "/img/jquery/btn_inputfile.gif",
//     imageheight : 34,
//     imagewidth : 80,
//     width : 215
// });
});

//win resize
$(window).resize(function(){
 setLayout();
});


//all_menu출력 및 header height 반응형 처리
function setLayout(){  
 var winW = $(window).width();
 var winH = $(window).height();
 
    var hbn_url = document.location.href;
  //alert(hbn_url.search("cate"));
    if(hbn_url.search("index.html")>-1 || hbn_url.search("cate")==-1){//메인일경우
   
   $('#gnb ul.menu>li').hover(function(){
	 $("a", this).addClass('active');
     $(".submenu", this).show();
     $(".submenu", this).stop().animate({right:"-120px"},'fast',"easeOutQuart");
     $("#header_wrap_bg").stop().animate({left:"120px"},'fast',"easeOutQuart");
    },function(){
	 $("a", this).removeClass('active');
     $(".submenu", this).hide();
     $(".submenu", this).stop().animate({right:"-100px"},'fast',"easeOutQuart");
     $("#header_wrap_bg").stop().animate({left:"0"},'fast',"easeOutQuart");
    });
   
  
  }else{//서브일경우
  
   
   if(winW<=1577){
    //초기화
    $('#gnb ul.menu>li').unbind("hover");
    $("#header_wrap_bg").stop().animate({left:"0"},'fast',"easeOutQuart");
    $(".submenu").hide();
    
    $('#gnb ul.menu>li').hover(function(){
     $(".submenu", this).show();
     $(".submenu", this).stop().animate({right:"-120px"},'fast',"easeOutQuart");
     $("#header_wrap_bg").stop().animate({left:"120px"},'fast',"easeOutQuart");
    },function(){
     $(".submenu", this).hide();
     $(".submenu", this).stop().animate({right:"-100px"},'fast',"easeOutQuart");
     $("#header_wrap_bg").stop().animate({left:"0"},'fast',"easeOutQuart");
    });
    
   }else if(winW>1600){
    //초기화
    $('#gnb ul.menu>li').unbind("hover");
    $("#header_wrap_bg").stop().animate({left:"120px"},'fast',"easeOutQuart");
    $("#gnb ul.menu>li a.selected").siblings("div.submenu").show();
    $("#gnb ul.menu>li a.selected").siblings("div.submenu").stop().animate({right:"-120px"},'fast',"easeOutQuart");
    
    $('#gnb ul.menu>li').hover(function(){
     $("#gnb ul.menu>li a.selected").siblings("div.submenu").hide();
     $("#gnb ul.menu>li a.selected").siblings("div.submenu").stop().animate({right:"-100px"},'fast',"easeOutQuart");
     $(".submenu", this).show();
     $(".submenu", this).stop().animate({right:"-120px"},'fast',"easeOutQuart");
     //$("#header_wrap_bg").stop().animate({left:"120px"},'fast',"easeOutQuart");
    },function(){
     $(".submenu", this).hide();
     $(".submenu", this).stop().animate({right:"-100px"},'fast',"easeOutQuart");
     $("#gnb ul.menu>li a.selected").siblings("div.submenu").show();
     $("#gnb ul.menu>li a.selected").siblings("div.submenu").stop().animate({right:"-120px"},'fast',"easeOutQuart");
     //$("#header_wrap_bg").stop().animate({left:"0"},'fast',"easeOutQuart");
    });
   }
  
  
  }
  
  //height 반응형처리
  if(winH>=1000){
   $("#header .third").show();
   $("#header .forth").show();
   $("#quick_icon").show();
   
  }else if(winH<1000 && winH>755){
   $("#phone").show();
   $("#fax").show();
   
   $("#header .third").hide();
   $("#header .forth").hide();
   $("#quick_icon").hide();
  
  }else if(winH<=755){
   $("#phone").hide();
   $("#fax").hide();
   $("#quick_icon").hide();
  }
 
 
}
//모바일메뉴 현재메뉴 오픈
function open_cur_m_menu(){
 var cur_m_menu = $('.mobile_menu>ul>li>a');
 var len_m_menu = cur_m_menu.length;
 for(var i=0; i<len_m_menu; i++){
  if($(cur_m_menu).eq(i).hasClass("selected")){
   $(cur_m_menu).eq(i).siblings(".submenu").show();
  }
 }
}

//input file 디자인
//function designInputfile(){
// $("input[type=file]").filestyle({ 
//     image: "/img/jquery/btn_inputfile.gif",
//     imageheight : 34,
//     imagewidth : 80,
//     width : 215
// });
//}

//quick메뉴 스크롤
$(window).scroll(
 function() {
  var npos = $(window).scrollTop();
  //퀵메뉴
  if(npos>=376) {
    $('#quick_menu').stop();
    $('#quick_menu').animate({top:(npos-376)+"px"},500,"easeOutQuart");
  }else{
    $('#quick_menu').stop();
    $('#quick_menu').animate({top:"24px"},500,"easeOutQuart");
  }
 }
);

//팝업
var win= null;
function NewWindow(mypage,myname,w,h,scroll){
   var winl = (screen.width-w)/2;
   var wint = (screen.height-h)/2;
   var settings ='height='+h+',';
      settings +='width='+w+',';
      settings +='top='+wint+',';
      settings +='left='+winl+',';
      settings +='scrollbars='+scroll+',';
      settings +='resizable=yes';
   win=window.open(mypage,myname,settings);
   if(parseInt(navigator.appVersion) >= 4){win.window.focus();}
}


function gnbController(mainClass, subClass){
 var mainChk = $("#listId>li>a"); // 1st for 문에서 length 를 체크할 항목 기준.
 
 for(var i = 0; i < mainChk.length ; i++){
  
  var subChk = $("#listId>li").eq(i); //변수 i 에 따라 대분류 li가 달라진다
   
  if(subChk.find("a").eq(0).text() == mainClass) { //체크할 항목 "투자정보" 에 도달하면 조건을 건다
    
   //subChk.eq(0).addClass("selected");
   subChk.find("a").eq(0).addClass("selected");
   
   subChk.find(".submenu").eq(0).show();
   subChk.find(".submenu").eq(0).stop().animate({right:"-120px"},'fast',"easeOutQuart");
   $("#header_wrap_bg").stop().animate({left:"120px"},'fast',"easeOutQuart");
   
   for (var z = 0; z < subChk.find("li>a").length; z++){ // 대분류i의 위치에 따른 소분류 length
    
    if(subChk.find("li>a").eq(z).text() == subClass){
     //subChk.find("li").eq(z).addClass("selected");
     subChk.find("li>a").eq(z).addClass("selected");
      
    } else {
     subChk.find("li>a").eq(z).removeClass("selected");
    }
    
   }; 
    
  } else {
   subChk.find("a").eq(0).removeClass("selected");
  }
    
 };
};