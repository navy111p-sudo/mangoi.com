
<!--
 $(document).ready(function() {
  var selBox1 = {
   "divSelectBox":"div.selectBox",
   "divSelectOptions":"div.selectOptions",
   "spanSelectOption":"span.selectOption",
   "spanSelected":"span.selected",
   "spanSelectArrow":"span.selectArrow"
  };
  var selBox2 = {
   "divSelectBox":"div.menu_Big_selectBox",
   "divSelectOptions":"div.selectOptions",
   "spanSelectOption":"span.selectOption",
   "spanSelected":"span.selected",
   "spanSelectArrow":"span.selectArrow"
  };
  var selBox3 = {
   "divSelectBox":"div.subsidiary_selectBox",
   "divSelectOptions":"div.selectOptions",
   "spanSelectOption":"span.selectOption",
   "spanSelected":"span.selected",
   "spanSelectArrow":"span.selectArrow"
  };1
  enableSelectBoxes(selBox1);
  enableSelectBoxes(selBox2);
  enableSelectBoxes(selBox3);
  function enableSelectBoxes(selBox){
  $(selBox.divSelectBox).each(function(){
   //$(this).children('span.selected').html(   $(this).children('div.selectOptions').children('span.selectOption:first').html()   );
   $(this).attr('value',$(this).children(selBox.divSelectOptions).children(selBox.spanSelectOption+':first').attr('value'));

   $(this).children(selBox.spanSelected+','+selBox.spanSelectArrow).click(function(){
    if($(this).parent().children(selBox.divSelectOptions).css('display') == 'none'){
     $(this).parent().children(selBox.divSelectOptions).css('display','block');
    }
    else
    {
     $(this).parent().children(selBox.divSelectOptions).css('display','none');
    }
   });

   $(this).find(selBox.spanSelectOption).click(function(){
    $(this).parent().css('display','none');
    $(this).closest(selBox.divSelectBox).attr('value',$(this).attr('value'));
    $(this).parent().siblings(selBox.spanSelected).html($(this).html());
   });
  });
 }//-->
 });
// 서브페이지 공통 함수 
$(function () {

    // 서브 비주얼 이미지 페이드 변수 정의 
    var sIndex = 0; var sTimer; var sInterval = 3000;

    // 서브 비주얼 이미지 초기셋팅 
    function subImgInit() { $("#sub_visual ul li:last").after($("#sub_visual ul li[data-idx=" + sIndex + "]")); }


    // 1차 드롭다운 메뉴 활성화 
    function majorActive() { var currIndex = $("#container").data('menu') - 1; var currMenu = $("#nav .depth1 ul.mnuList li").eq(currIndex).text(); $("#nav .depth1 > a > span").text(currMenu); }

    // 2차 드롭다운 메뉴 활성화 
    function minorActive() { var currIndex = $("#container").data('sub') - 1; var currMenu = $("#nav .depth2 ul.mnuList li").eq(currIndex).text(); $("#nav .depth2 > a > span").text(currMenu); }

    // 드롭박스 메뉴 클릭 처리 
    $("#nav .dropdown > a").click(function () { if (!$(this).parent('.dropdown').hasClass('open')) { $("#nav .dropdown").removeClass('open'); $("#nav .dropdown > ul.mnuList").stop(true, false).fadeOut(200); $(this).parent('.dropdown').addClass('open'); $(this).siblings('ul.mnuList').stop(true, false).fadeIn(200); } else { $(this).parent('.dropdown').removeClass('open'); $(this).siblings('ul.mnuList').stop(true, false).fadeOut(200); } });

    // 모바일 드롭박스 메뉴 클릭 처리 
    $("#leftBar a.dropdown").click(function (e) { e.preventDefault(); if (!$(this).hasClass('open')) { $(this).addClass('open'); $("#lnb").stop(true, false).slideDown(300); } else { $(this).removeClass('open'); $("#lnb").stop(true, false).slideUp(300); } });

    // 상단메뉴 (태블릿 이하 좌측메뉴) 숨김 
    function gnbHide() { $("#gnb > li > ul.submnu").css('display', 'none'); $("#gnb > li").removeClass('open'); }

    // 상단메뉴 (태블릿 이하 좌측메뉴) 표시 
    function gnbShow() { $("#gnb > li > ul.submnu").css('display', 'block'); $("#gnb > li").removeClass('open'); }

    // 서브 페이드 비주얼 및 드롭박스 메뉴 동기화 
    $(window).load(function () { subImgInit(); majorActive(); minorActive(); });

    // 윈도우 리사이즈시 사이즈별로 메뉴 숨김/보임 처리 
    $(window).resize(function () {
        var wSize = $(this).width();

        // 
        if (wSize > 1024) {
            // 
            gnbShow();
            // 
        } else {
            // 
            gnbHide();
            // 
        }
    });
});