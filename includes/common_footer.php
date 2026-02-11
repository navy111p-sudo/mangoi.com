<div class="preloader"><span class="spinner spinner-round"></span></div>
	
<!-- JavaScript -->
<script src="/assets/js/jquery.bundle.js?ver=150"></script>
<script src="/assets/js/scripts.js?ver=150"></script>
<script src="/assets/js/charts.js"></script>
<!--<script src="/js/mvapi.js?ver=6"></script>-->
<script src="/js/mvapi.min.js"></script>


<?
///assets/js/jquery.bundle.js 바로 아래 위치(망고아이)
//include_once('./tms/includes/tms_engine.php');//상대경로
include $_SERVER["DOCUMENT_ROOT"]."/tms/includes/tms_engine.php";//절대경로
///assets/js/jquery.bundle.js 바로 아래 위치(망고아이)
?>


<script>


function OpenCalTable(SelectYear, SelectMonth){
	
	<?if ($SsoDomainSite==1){?>
	var OpenUrl = "../pop_study_calendar.php?SelectYear="+SelectYear+"&SelectMonth="+SelectMonth;
	<?}else{?>
	var OpenUrl = "pop_study_calendar.php?SelectYear="+SelectYear+"&SelectMonth="+SelectMonth;
	<?}?>

    $.colorbox({    
        href:OpenUrl
        ,width:"95%" 
        ,height:"95%"
        ,maxWidth: "1000"
        ,maxHeight: "700"
        ,title:""
        ,iframe:true 
        ,scrolling:true
        //,onClosed:function(){location.reload(true);}
        //,onComplete:function(){alert(1);}
    }); 
}


$(document).ready(function () {

	<?if ($SsoDomainSite==1){?>
		//없음
	<?}else{?>
		url = "ajax_get_main_page_board_list.php";
		//location.href = url + "?NewID=";
		
		$.ajax(url, {
			data: {
				NewID: ""
			},
			success: function (data) {
				ContentHTML1 = data.ContentHTML1;
				ContentHTML2 = data.ContentHTML2;
				document.getElementById("MainFooterNotice").innerHTML = ContentHTML1;
				document.getElementById("MainFooterQna").innerHTML = ContentHTML2;
			},
			error: function () {

			}
		});
	<?}?>

});
</script>



<script>
//float
$(".allownumericwithdecimal").on("keypress keyup blur",function (event) {
	//this.value = this.value.replace(/[^0-9\.]/g,'');
	$(this).val($(this).val().replace(/[^0-9\.]/g,''));
	if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
		event.preventDefault();
	}
});

//int
$(".allownumericwithoutdecimal").on("keypress keyup blur",function (event) {    
	$(this).val($(this).val().replace(/[^\d].+/, ""));
	if ((event.which < 48 || event.which > 57)) {
		event.preventDefault();
	}
});


// numberOnly="true" 와 같이 사용
$(function(){
	$(document).on("keyup", "input:text[numberOnly]", function() {$(this).val( $(this).val().replace(/[^0-9]/gi,"") );});
	$(document).on("keyup", "input:text[datetimeOnly]", function() {$(this).val( $(this).val().replace(/[^0-9:\-]/gi,"") );});
});


// class="numeric-only" 와 같이 사용
$(document).on('keyup', '.numeric-only', function(event) {
   var v = this.value;
   if($.isNumeric(v) === false) {
        //chop off the last char entered
        this.value = this.value.slice(0,-1);
   }
});
</script>


<!-- Floating KakaoTalk Chat Button -->
<style>
  .kakao-chat-float {
    position: fixed;
    right: 16px;
    bottom: 16px;
    width: 68px;
    height: 68px;
    z-index: 9999;
  }
  .kakao-chat-float img {
    display: block;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    box-shadow: 0 6px 16px rgba(0,0,0,0.25);
  }
  @media (min-width: 992px) {
    .kakao-chat-float {
      right: 20px;
      bottom: 20px;
      width: 76px;
      height: 76px;
    }
  }
</style>
<a class="kakao-chat-float" href="https://pf.kakao.com/_xlqnSxd/chat" target="_blank" rel="noopener noreferrer" aria-label="카카오톡 상담센터">
  <img src="/kakao_icon.png" alt="카카오톡 상담하기">
  <!-- 필요 시 아이콘 교체: /images/kakao_floating.png 등 -->
</a>


<?if ($SsoDomainSite==1){?>
	<!-- ====    kendo -->
	<link href="../kendo/styles/kendo.common.min.css" rel="stylesheet">
	<link href="../kendo/styles/kendo.default.min.css" rel="stylesheet">
	<script src="../kendo/js/kendo.web.min.js"></script>
	<!-- ====    kendo   === -->


	<!-- ====   Color Box -->
	<?
	$ColorBox = isset($ColorBox) ? $ColorBox : "";
	if ($ColorBox==""){
		$ColorBox = "example2";
	}
	?>
	<link rel="stylesheet" href="../js/colorbox/<?=$ColorBox?>/colorbox.css" />
	<script src="../js/colorbox/jquery.colorbox.js"></script>
	<script>
	jQuery(document).ready(function(){
	$(document).bind('cbox_open', function() {
		$('html').css({ overflow: 'hidden' });
	}).bind('cbox_closed', function() {
		$('html').css({ overflow: '' });
	});
	});
	</script>
	<!-- ====   Color Box   === -->
<?}else{?>
	<!-- ====    kendo -->
	<link href="./kendo/styles/kendo.common.min.css" rel="stylesheet">
	<link href="./kendo/styles/kendo.default.min.css" rel="stylesheet">
	<script src="./kendo/js/kendo.web.min.js"></script>
	<!-- ====    kendo   === -->


	<!-- ====   Color Box -->
	<?
	$ColorBox = isset($ColorBox) ? $ColorBox : "";
	if ($ColorBox==""){
		$ColorBox = "example2";
	}
	?>
	<link rel="stylesheet" href="./js/colorbox/<?=$ColorBox?>/colorbox.css" />
	<script src="./js/colorbox/jquery.colorbox.js"></script>
	<script>
	jQuery(document).ready(function(){
	$(document).bind('cbox_open', function() {
		$('html').css({ overflow: 'hidden' });
	}).bind('cbox_closed', function() {
		$('html').css({ overflow: '' });
	});
	});
	</script>
	<!-- ====   Color Box   === -->
<?}?>

<!-- ===================== 웹소켓 및 쿠키 ====================== -->
<script>
window.addEventListener('DOMContentLoaded', function()
{
	/*
	// 디바이스 정보 쿠키에 등록
	function AddDeviceCookie(CenterDeviceID) {
		var CenterDeviceID = document.getElementById("CenterDeviceID").value;
		setCookie('LinkLoginDeviceID', CenterDeviceID, '.mangoidev.hihome.kr', 365);
		location.reload(true);
	}
	*/

	// 디바이스 정보 쿠키 삭제
	function DeleteCookie() {

		// 디바이스 - 학생 연결을 끊기 위한 변수
		var OriDeviceID = getCookie('LinkLoginDeviceID');

		var send = { Type: "Link", DeviceID: OriDeviceID, MemberID: "" };
		doSend(send);
	}


	function init()
	{
		websocket = new WebSocket("ws://211.117.60.181:8090/");
		websocket.onopen = function(Evt) { onOpen(Evt) };
		websocket.onclose = function(Evt) { onClose(Evt) };
		websocket.onmessage = function(Evt) { onMessage(Evt) };
		websocket.onerror = function(Evt) { onError(Evt) };
	}

	function onOpen(Evt)
	{
		// 로그아웃 후 들어온 케이스에 해당
		var ResponseLogoutCookie = getCookie('ResponseLogoutCookie') ? getCookie('ResponseLogoutCookie') : 0;

		// 로그아웃 하였다면 학생계정만 로그아웃하기 위한 분기
		if(ResponseLogoutCookie == 1) {
			setCookie("ResponseLogoutCookie", 0, ".mangoidev.hihome.kr", 0);
			DeleteCookie();
		}
	}

	function onClose(Evt)
	{
	}

	function onMessage(Evt)
	{
		var isJson = checkJson(Evt.data);

		if( isJson) {
			// json 타입 체크통과.

			var json = JSON.parse(Evt.data);
			console.log(json);
			// 작업 하기 전에 확인용 변수들
			var TempCenterID = json.CenterID;
			var CurrentCenterID = <?=$_LINK_MEMBER_CENTER_ID_?>;
	
			// 센터가 동일하다면 실행
			if(TempCenterID==CurrentCenterID) {
				var TempDeviceID = json.DeviceID; // 특정 기기만 받게 하게 위한 변수
				var CurrentDeviceID = getCookie('LinkLoginDeviceID');

				if(TempDeviceID==CurrentDeviceID) {
					var TempType = json.Type;
					var CurrentPage = window.location.href;

					if(TempType=="cookie") {
						// values from websocket message
						var TempMemberID = json.MemberID;
						var TempAction = json.Action;


						var GetMemberInfoData = GetMemberInfo(TempMemberID);
						var TempMemberLevelID = GetMemberInfoData.MemberLevelID;
						var TempMemberLoginID = GetMemberInfoData.MemberLoginID;
						var TempMemberName = GetMemberInfoData.MemberName;

						if(TempAction=="login") {
							// 웹 쪽에선 MemberLoginID 기준으로 member_check.php 에서 가져오기 때문에
							setCookie("LinkLoginMemberID", TempMemberLoginID, ".mangoidev.hihome.kr", 1);

							alert("안녕하세요.\n"+TempMemberName+" 님 로그인 되셨습니다.");
							location.href = "index.php";
							var send = { Type: "Link", DeviceID: CurrentDeviceID, MemberID: TempMemberID, CenterID: TempCenterID };
							doSend(send);

						} else if(TempAction=="logout") {
							setCookie("LinkLoginMemberID", TempMemberLoginID, ".mangoidev.hihome.kr", 8760); // 1 Year

							alert("로그아웃 되셨습니다.");
							location.href = "mypage_teacher_mode.php";
							var send = { Type: "Link", DeviceID: CurrentDeviceID, MemberID: "", CenterID: TempCenterID };
							doSend(send);

						}
					}
				}
			}
		} // Evt.data 가 json이 아닐 경우
	}

	function GetMemberInfo(MemberID) {
		var Url = "ajax_get_member_info.php";
		var Result;
		$.ajax({
			url: Url,
			async: false,
			data: {
				MemberID: MemberID
			},
			success: function(data) {
				Result = data;
			}
		});
		return Result;
	}

	function SendLoginCookie() {
		// 검증완료
		// 확인용
		var CenterClassID = document.getElementById("CenterClassID").value;
		var MemberID = document.getElementById("MemberID").value;
		var DeviceID = document.getElementById("CenterDeviceID").value;
		var CenterID = <?=$_LINK_MEMBER_CENTER_ID_?>;

		if (CenterClassID == "") {
			alert('클래스를 선택해주세요.');
			CenterClassID.focus();
			return;
		}

		if (DeviceID == "") {
			alert('디바이스를 선택해주세요.');
			DeviceID.focus();
			return;
		}
		
		if (MemberID == "") {
			alert('학생을 선택해주세요.');
			MemberID.focus();
			return;
		}

		var json = {
			Type: "cookie",
			CenterClassID: CenterClassID,
			MemberID: MemberID,
			DeviceID: DeviceID,
			CenterID: CenterID,
			Action: "login"
		};
		doSend(json);
	}
 
	function SendLogoutCookie(DeviceID) {
		var LoginMemberID = getCookie("MemberLoginID");
		var CenterID = <?=$_LINK_MEMBER_CENTER_ID_?>;

		var json = {
			Type: "cookie",
			CenterID: CenterID,
			MemberID: LoginMemberID,
			Action: "logout",
			LoginMemberID: "", // undefined 막기 위함
			DeviceID: DeviceID
		};
		doSend(json);
	}

	function onError(Evt)
	{
		websocket.close();

	}

	function doSend(Message) {
		var Strjson = JSON.stringify(Message);
		// 소켓에서 받을 때 "Object: Object 로 받게 되어 String 으로 변환 발송
		websocket.send(Strjson);
	}

	if(getCookie("LinkLoginDeviceID")) {
		// 디바이스값을 가지는 학생들 또는 선생님들은 웹소켓 연결
		window.addEventListener("load", init, false);
	}

	function doDisconnect() {
		websocket.close();
	}

	// 쿠키 함수 !
	function setCookie(name, value, domain, expire) {
	  var date = new Date();
	  date.setTime(date.getTime() + (expire * 60 * 60 * 1000));
	  var expire = date.toGMTString();
	  //document.cookie = namae + '=' + value + ';expires=' + date.toUTCString() + ';path=/';
	  document.cookie = name + '=' + value+';domain='+domain+'; expires='+expire;
	}

	function getCookie(name) {
	  var value = document.cookie.match('(^|;) ?' + name + '=([^;]*)(;|$)');
	  return value? value[2] : null;
	}

	function checkJson(response) { 
		try {
			var json = JSON.parse(response);
			if(typeof json === 'object') {
				return true;
			}
		} catch (e) {
			return false;
		}
	}
});
</script>
<!-- ===================== 웹소켓 및 쿠키 ====================== -->


