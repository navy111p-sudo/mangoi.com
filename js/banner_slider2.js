// JavaScript Document
// 배너 하나의 크기.
var BANNER_WIDTH = 980;
var SHOW_DURATION = 600; 
var AUTO_PLAY_TIME = 4000;
// 우리가 움직이게 될 배너 컨텐츠 엘리먼트.
var $banner_content;
// 배너 전체 개수.
var nBannerLength = 0;
// 현재 화면에 보이고 있는 배너 인덱스 값.
var nCurrentBannerIndex = 0;
//움직임방향 next:0,prev:1;
var play_dirction=0;
// 배너 메뉴의 위치를 표시할 엘리먼트가 담길 변수.
var $banner_dots;
//화면넓이
var $bannerWidth;
// 자동실행 타이머 아이디.
var autoTImerID;
//재생상태확인(1:자동재생중, 0:멈춤상태)
var is_play=1;

$(document).ready(function(){
 initMenu();
 initEventListener();
 startAutoPlay();
});
$(window).resize(function(){
 //화면크기변화시 넓이값 업데이트
 $bannerWidth=$("#banner_content").width();
 
 
 //변화에 따른 요소위치 재정렬
 for(var i=0; i<nBannerLength; i++){
  //요소위치값을 구함
  var offset = $banner_content.eq(i).offset();
  
  if(i != this.nCurrentBannerIndex){//현재 배너위치는 항상 0이므로 무시함.
   //좌측영역에 위치한경우
   if(offset.left<0){
    $banner_content.eq(i).css({left:-$bannerWidth+"px"});
   //우측영역에 위치한경우
   }else{
    $banner_content.eq(i).css({left:$bannerWidth+"px"});
   }
  }
 }
});

// 메뉴 엘리먼트 관련 초기화.
function initMenu(){
 $banner_content = $("#banner_content").children();
 nBannerLength = $banner_content.length;
 //초기위치설정
 $bannerWidth=$("#banner_content").width();
 $banner_content.css({left:$bannerWidth+"px"});
 $banner_content.eq(0).css({left:"0px"});
 
 // 배너 메뉴의 위치를 표시할 엘리먼트가 담길 변수.
 $banner_dots = $("#banner_nav li a");
 // 배너 메뉴의 위치를 0번째로 초기화 시킴.
 showBannerDotAt(0);
 
 // autoPlay의 타이머 ID값.
 autoTImerID = 0;
}
// 이벤트 처리.
function initEventListener(){
 // 이전  배너 보이기
 $("#btn_prev_banner").bind("click", function(){
   prevBanner();
 });
 // 다음 배너 보이기
 $("#btn_next_banner").bind("click", function(){
   nextBanner();
 });
 
 // 배너 메뉴에서 마우스가 오버되는 경우, 오버된 위치에 맞게 배너를 보이도록 하기.
 $banner_dots.bind("click",function(){
  var nIndex = $banner_dots.index(this);
  showBannerAt(nIndex);
  return false;
 });
 
 //마우스오버시 슬라이드컨트롤 상태변화요소
 
 var $over_element = $("#banner_nav, #btn_navi");
 // 배너슬라이더에 마우스 커서가 들어오는 경우 자동실행 기능을 멈춘다.
 $over_element.bind("mouseenter",function(){
  stopAutoPlay();
 });
 // 배너슬라이더에서 마우스 커서가 밖으로 나가는 경우 다시, 자동실행 기능 시작.
 $over_element.bind("mouseleave",function(){
  if(is_play==1){
   startAutoPlay();
  }else{
   stopAutoPlay();
  }
 });
 //STOP, PLAY버튼
 $("#btn_play_stop").toggle(function(){
  stopAutoPlay();
  $(this).text("PLAY");
  is_play=0;
 },function(){
  startAutoPlay();
  $(this).text("STOP");
  is_play=1;
 });
 
}
 
// 이전  배너 보이기
function prevBanner(){
 // 이동할 이전 배너 인덱스 값 구하기.
 var nIndex = this.nCurrentBannerIndex-1;
 // 이전 내용이 없는 경우 마지막 배너 인덱스 값으로 설정하기.
 if(nIndex<0)
  nIndex = this.nBannerLength-1;
  
 // n번째 배너 보이기.
 play_dirction = 1; 
 this.showBannerAt(nIndex);   
}
// 다음 배너 보이기
function nextBanner()
{
 // 이동할 이전 배너 인덱스 값 구하기.
 var nIndex = this.nCurrentBannerIndex+1;
 // 다음 내용이 없는 경우, 첫 번째 배너 인덱스 값으로 설정하기.
 if(nIndex>=nBannerLength)
  nIndex = 0;
 
 // n번째 배너 보이기.
 play_dirction = 0;   
 this.showBannerAt(nIndex); 
}
// nIndex에 해당하는 배너 보이기.
function showBannerAt(nIndex){
 if (nIndex != this.nCurrentBannerIndex) {
  //  n번째 배너 위치 값 구하기.
  //var nPosition = -BANNER_WIDTH * nIndex;
  var nPosition = BANNER_WIDTH;
  // 배너 메뉴의 위치 값을 업데이트 시킴.
  this.showBannerDotAt(nIndex);
  
  //이전배너인덱스
  pIndex = this.nCurrentBannerIndex;
  
  // 슬라이드 시작.
  $banner_content.stop();
  
  for(var i=0; i<nBannerLength; i++){
   
   if(play_dirction==0){//next
    if(i==nIndex){
     $banner_content.eq(i).css({left:$bannerWidth+"px"});
     $banner_content.eq(i).animate({left:"0px"}, SHOW_DURATION, "easeOutQuart");
    }else if(i==pIndex){
     $banner_content.eq(i).animate({left:-$bannerWidth+"px"}, SHOW_DURATION, "easeOutQuart");
    }else{
     $banner_content.eq(i).css({left:$bannerWidth+"px"});
    }
   }else{//prev
    if(i==nIndex){
     $banner_content.eq(i).css({left:-$bannerWidth+"px"});
     $banner_content.eq(i).animate({left:"0px"}, SHOW_DURATION, "easeOutQuart");
    }else if(i==pIndex){
     $banner_content.eq(i).animate({left:$bannerWidth+"px"}, SHOW_DURATION, "easeOutQuart");
    }else{
     $banner_content.eq(i).css({left:-$bannerWidth+"px"});
    }
   }
  }
  //현재 배너 인덱스 업데이트 시키기.
  this.nCurrentBannerIndex = nIndex;
 }
}

// 배너 메뉴의 위치값을 업데이트 시킴.
function showBannerDotAt(nIndex){
 this.$banner_dots.eq(this.nCurrentBannerIndex).removeClass("select");
 this.$banner_dots.eq(nIndex).addClass("select");
}

////////////////////////////////////////////////////////
// 자동 실행 시작
function startAutoPlay(){
 
 if(this.autoTimerID!=0)
  clearInterval(this.autoTimerID);
  
 this.autoTimerID = setInterval(function(){
  nextBanner();
 },this.AUTO_PLAY_TIME);
}
// 자동실행 멈춤.
function stopAutoPlay(){
 if(this.autoTimerID!=0)
  clearInterval(this.autoTimerID);
  
 this.autoTimerID = 0;
}