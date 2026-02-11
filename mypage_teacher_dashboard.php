<?php
include_once('./includes/dbopen.php');
include_once('./includes/common.php');
$DenyGuest = true;
include_once('./includes/member_check.php');

$UseMain = 0;
$UseSub = 1;
$SubCode = "sub_10";
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
<link href="css/sub_style.css" rel="stylesheet" type="text/css" />
<?php
include_once('./includes/common_header.php');

$CenterClassID = isset($_REQUEST["CenterClassID"]) ? $_REQUEST["CenterClassID"] : "";

$Sql = "select SubID from Subs where SubCode=:SubCode";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':SubCode', $SubCode);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$SubID = $Row["SubID"];


if ($UseMain==1){
	$Sql = "select * from Main limit 0,1";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$MainLayout = $Row["MainLayout"];
	$MainLayoutCss = $Row["MainLayoutCss"];
	$MainLayoutJavascript = $Row["MainLayoutJavascript"];
	list($MainLayoutTop, $MainLayoutBottom) = explode("{{Page}}", $MainLayout);
}else{
	$MainLayoutTop = "";
	$MainLayoutBottom = "";
	$MainLayoutCss = "";
	$MainLayoutJavascript = "";
}


if ($UseSub==1){
	$Sql = "select * from Subs where SubID=:SubID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':SubID', $SubID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$SubLayout = $Row["SubLayout"];
	$SubLayoutCss = $Row["SubLayoutCss"];
	$SubLayoutJavascript = $Row["SubLayoutJavascript"];
	list($SubLayoutTop, $SubLayoutBottom) = explode("{{Page}}", $SubLayout);
}else{
	$SubLayoutTop = "";
	$SubLayoutBottom = "";
	$SubLayoutCss = "";
	$SubLayoutJavascript = "";
}


if (trim($MainLayoutCss)!=""){
	echo "\n";
	echo "<style>";
	echo "\n";
	echo $MainLayoutCss;
	echo "\n";
	echo "</style>";
	echo "\n";
}

if (trim($SubLayoutCss)!=""){
	echo "\n";
	echo "<style>";
	echo "\n";
	echo $SubLayoutCss;
	echo "\n";
	echo "</style>";
	echo "\n";
}

?>
<!-- amchart4 -->
<script src="./amcharts4/core.js"></script>
<script src="./amcharts4/charts.js"></script>
<script src="./amcharts4/themes/animated.js"></script>
<!-- amchart4 -->
</head>
<body>
<?
include_once('./includes/common_body_top.php');
?>
<?php
// $MainLayoutTop = convertHTML(trim($MainLayoutTop));
// $SubLayoutTop = convertHTML(trim($SubLayoutTop));
// $SubLayoutBottom = convertHTML(trim($SubLayoutBottom));
// $MainLayoutBottom = convertHTML(trim($MainLayoutBottom));

if($DomainSiteID==7){
    $MainLayoutTop = convertHTML(trim("{{Piece(header_gumiivyleague)}}"));
    $SubLayoutTop = convertHTML(trim("{{Piece(sub_04_gumiivyleague)}}"));
    $SubLayoutBottom = convertHTML(trim("{{Piece(sub_01_gumiivyleague_bottom)}}"));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_gumiivyleague)}}"));

} else if($DomainSiteID==8){ //engliseed.kr
    $MainLayoutTop = convertHTML(trim("{{Piece(header_engliseed)}}"));
    $SubLayoutTop = convertHTML(trim("{{Piece(sub_06_engliseed)}}"));
    $SubLayoutBottom = convertHTML(trim("{{Piece(sub_01_engliseed_bottom)}}"));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_engliseed)}}"));

} else if($DomainSiteID==9){ //live.engedu.kr
    $MainLayoutTop = convertHTML(trim("{{Piece(header_engedu)}}"));
    $SubLayoutTop = convertHTML(trim("{{Piece(sub_06_engedu)}}"));
    $SubLayoutBottom = convertHTML(trim("{{Piece(sub_01_engedu_bottom)}}"));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_engedu)}}"));


} else {
    $MainLayoutTop = convertHTML(trim($MainLayoutTop));
    $SubLayoutTop = convertHTML(trim($SubLayoutTop));
    $SubLayoutBottom = convertHTML(trim($SubLayoutBottom));
    $MainLayoutBottom = convertHTML(trim($MainLayoutBottom));
  }

echo "\n";
echo $MainLayoutTop;
echo "\n";
echo $SubLayoutTop;
echo "\n";

?>

<!-- 비밀번호 입력 파일 : mypage_teacher_password.php -->

<div class="sub_wrap">   
    <div class="tab_teacher_wrap">
        <ul class="tab_teacher">
            <li class="one"><a href="mypage_teacher_mode.php">학생모드</a></li>
            <li class="two"><a href="mypage_teacher_dashboard.php">대시보드</a></li>
            <li class="three"><a href="mypage_teacher_device.php">디바이스 설정</a></li>
        </ul>
    </div>
    <div class="sub_title_common_wrap"><h2 class="sub_title_common"><b>대시</b>보드</h2></div>

    <section class="mypage_teacher_wrap">
        <div class="mypage_teacher_area">
            <div class="mypage_teacher_dash_box">
                <div class="mypage_teacher_dash_inner">
                    <select class="mypage_teacher_dash_select" onchange="getClassInfo()" name="CenterClassID" id="CenterClassID">
                        <option>클래스를 선택하세요.</option>
						<?
						$Sql2 = "select * from CenterClasses where CenterID=:_LINK_MEMBER_CENTER_ID_";
						$Stmt2 = $DbConn->prepare($Sql2);
						$Stmt2->bindParam(':_LINK_MEMBER_CENTER_ID_', $_LINK_MEMBER_CENTER_ID_);
						$Stmt2->execute();
						$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

						while($Row2 = $Stmt2->fetch()){
							$TempCenterClassID = $Row2["CenterClassID"];
							$TempCenterClassName = $Row2["CenterClassName"];
						?>
						<option value="<?=$TempCenterClassID?>" <?if($TempCenterClassID==$CenterClassID){?>selected<?}?> ><?=$TempCenterClassName?></option>
						<?
						}
						$Stmt2 = null;
						?>
                    </select>
                    <select class="mypage_teacher_dash_select" id="CenterDeviceID" name="CenterDeviceID">
                        <option>디바이스를 선택하세요.</option>
						<?php
							$Sql3 = "select A.CenterDeviceID, A.CenterDeviceName from CenterDevices A where A.CenterID=:_LINK_MEMBER_CENTER_ID_";

							$Stmt3 = $DbConn->prepare($Sql3);
							$Stmt3->bindParam(':_LINK_MEMBER_CENTER_ID_', $_LINK_MEMBER_CENTER_ID_);
							$Stmt3->execute();
							$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
							while($Row3 = $Stmt3->fetch()) {
								$CenterDeviceID = $Row3["CenterDeviceID"];
								$CenterDeviceName = $Row3["CenterDeviceName"];
						?>
						<option value="<?=$CenterDeviceID?>">[<?=$CenterDeviceID?>] <?=$CenterDeviceName?></option>
						<?php
							}
						?>
                    </select>
                    <select class="mypage_teacher_dash_select" name="MemberID" id="MemberID">
                        <option value="">학생을 선택하세요.</option>
						<?php

							//  멤버와 타임존을 조인하여 값을 가져올 것. id 가 매칭이 된다면 default 로 지정
							$Sql4 = "select A.CenterClassMemberLoginStatus, B.MemberName, B.MemberID, B.MemberLoginID, B.CenterID from CenterClassMembers A inner join Members B on A.MemberID=B.MemberID  where A.CenterClassID=:CenterClassID and B.CenterID=:_LINK_MEMBER_CENTER_ID_";

							$Stmt4 = $DbConn->prepare($Sql4);
							$Stmt4->bindParam(':CenterClassID', $CenterClassID);
							$Stmt4->bindParam(':_LINK_MEMBER_CENTER_ID_', $_LINK_MEMBER_CENTER_ID_);
							$Stmt4->execute();
							$Stmt4->setFetchMode(PDO::FETCH_ASSOC);
							while($Row4 = $Stmt4->fetch()) {
								$CenterClassMemberLoginStatus = $Row4["CenterClassMemberLoginStatus"];
								$MemberName = $Row4["MemberName"];
								$MemberLoginID = $Row4["MemberLoginID"];
								$MemberID = $Row4["MemberID"];
								$CenterID = $Row4["CenterID"];
						?>
						<option value="<?=$MemberID?>"><?=$MemberName?></option>
						<?php
							}
						?>
                    </select>
                    <a href="javascript:SendLoginCookie();" class="button_yellow_black">연결하기</a>
                </div>
                <table class="mypage_teacher_dash_table">
                    <tr>
                        <th>디바이스</th>
                        <th>학생명</th>
                        <th>설정</th>
                    </tr>
					<?php
					/*
					$Sql5 = "select A.CenterDeviceID, A.CenterDeviceName, A.CenterDeviceLastLoginMember, C.MemberName
					from CenterDevices A 
					inner join CenterClassMembers B on A.CenterDeviceID=B.CenterDeviceID
					inner join Members C on C.MemberID=B.MemberID 
					
					where A.CenterDeviceLastLoginMember != '' and A.CenterID=:_LINK_MEMBER_CENTER_ID_ and A.CenterClassID=:CenterClassID"; // 링크멤버센터 where 구문절 지워도 될듯.
					*/
					$Sql5 = "SELECT A.*, B.MemberName 
					FROM CenterDevices A
					INNER JOIN Members B ON A.CenterDeviceLastLoginMember=B.MemberID 
					INNER JOIN CenterClassMembers C ON B.MemberID=C.MemberID
					WHERE C.CenterClassID=:CenterClassID and A.CenterID=:_LINK_MEMBER_CENTER_ID_";
					$Stmt5 = $DbConn->prepare($Sql5);
					$Stmt5->bindParam(':CenterClassID', $CenterClassID);
					$Stmt5->bindParam(':_LINK_MEMBER_CENTER_ID_', $_LINK_MEMBER_CENTER_ID_);
					$Stmt5->execute();
					$Stmt5->setFetchMode(PDO::FETCH_ASSOC);

					while($Row5 = $Stmt5->fetch()) {
						$CenterDeviceID = $Row5["CenterDeviceID"];
						$CenterDeviceName = $Row5["CenterDeviceName"];
						$CenterDeviceLastLoginMember = $Row5["CenterDeviceLastLoginMember"];
						$MemberName = $Row5["MemberName"];

						if ($CenterDeviceLastLoginMember) {
							$CookieStatus = "로그아웃";
						} else {
							$CookieStatus = "";
						}
					?>
					<tr>
						<td>[<?=$CenterDeviceID?>] <?=$CenterDeviceName?></td>
						<td><?=$MemberName?></td>
						<td><a class="button_br_black" href="javascript:SendLogoutCookie(<?=$CenterDeviceID?>);"><?=$CookieStatus?></a></td>
					</tr>
					<?php } ?>
                </table>
                <!--
                <div class="mypage_teacher_dash_text">
                    <img src="images/img_no_link.png" alt="" class="mypage_teacher_dash_img">
                    연결된 학생이 없습니다.
                </div>
                -->
            </div>
        </div>
    </section>
</div>

<style>
/** 마이 선생님 탭메뉴 **/
.tab_teacher li.one{border-right:0; border-left:1px solid #e3e3e3;}
.tab_teacher li.two{border:0; border:2px solid #333; border-bottom:0; position:relative; line-height:39px;}
.tab_teacher li.two:after{content:''; position:absolute; background-color:#fff; bottom:-1px; left:0; right:0; height:1px;}

@media all and (min-width:640px){
.tab_teacher li.two{line-height:43px;}
}
@media all and (min-width:768px){
.tab_teacher li.two{line-height:43px;}
}
@media all and (min-width:1024px){
.tab_teacher li.two{line-height:48px;}
}
@media all and (min-width:1280px){
.tab_teacher li.two{line-height:48px;}
}
</style>


<script language="javascript">

$('.sub_visual_navi .two').addClass('active');

websocket = new WebSocket("ws://211.117.60.181:8090/");
websocket.onopen = function(Evt) { onOpen(Evt) };
websocket.onclose = function(Evt) { onClose(Evt) };
websocket.onmessage = function(Evt) { onMessage(Evt) };
websocket.onerror = function(Evt) { onError(Evt) };



function onOpen(Evt) {
		// 로그아웃 후 들어온 케이스에 해당
		var ResponseLogoutCookie = getCookie('ResponseLogoutCookie') ? getCookie('ResponseLogoutCookie') : 0;

		// 로그아웃 하였다면 학생계정만 로그아웃하기 위한 분기
		if(ResponseLogoutCookie == 1) {
			setCookie("ResponseLogoutCookie", 0, ".mangoidev.hihome.kr", 0);
			DeleteCookie();
		}
	}

function onClose(Evt) {
}

function onMessage(Evt) {
	var isJson = checkJson(Evt.data);

	if( isJson) {
		// json 타입 체크통과.
		var json = JSON.parse(Evt.data);
		// 작업 하기 전에 확인용 변수들
		var TempCenterID = json.CenterID;
		var CurrentCenterID = <?=$_LINK_MEMBER_CENTER_ID_?>;

		// 센터가 동일하다면 실행
		if(TempCenterID==CurrentCenterID) {
			var TempType = json.Type;

			if(TempType=="Link") {
				var TempMemberID = json.MemberID;
				var TempDeviceID = json.DeviceID;
				var Url = "ajax_set_cookie.php";

				$.ajax({
					url: Url,
					data: {
						MemberID: TempMemberID,
						DeviceID: TempDeviceID
					},
					success: function(res) {
						var Result = res.Result;
				
						if(Result=="update") {
							location.reload(true);
						} else {
							alert("데이터베이스 작업에 실패하였습니다.");
						}
					}
				});
			}
		}
	} // Evt.data 가 json이 아닐 경우
}

function getClassInfo() {
	var CenterClassID = document.getElementById("CenterClassID").value;
	location.href = "mypage_teacher_dashboard.php?CenterClassID="+CenterClassID;
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

function doDisconnect() {
	websocket.close();
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
	// 선생님 아이디와 그 센터의 값을 전달
	var CenterID = <?=$_LINK_MEMBER_CENTER_ID_?>;
	var MemberID = <?=$_LINK_MEMBER_ID_?>;

	var json = {
		Type: "cookie",
		CenterID: CenterID,
		MemberID: MemberID,
		Action: "logout",
		LoginMemberID: "", // undefined 막기 위함
		DeviceID: DeviceID
	};
	doSend(json);
	setInterval(CheckConnectWithStudent, 2000, MemberID, CenterID);
}

function CheckConnectWithStudent(TempMemberID, TempCenterID) {
	var Url = "ajax_set_cookie.php";

	$.ajax({
		url: Url,
		data: {
			MemberID: TempMemberID,
			DeviceID: TempDeviceID
		},
		success: function(res) {
			var Result = res.Result;

			if(Result=="update") {
				alert(TempMemberID+" 와의 연결이 끊겼습니다.");
				location.reload(true);
			} else if(Result=="exist") {

			} else {
				alert("데이터베이스 작업에 실패하였습니다.");
			}
		}
	});
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

</script>

<?php
echo "\n";
echo $SubLayoutBottom;
echo "\n";
echo $MainLayoutBottom;
echo "\n";
?>

<?
include_once('./includes/common_analytics.php');
include_once('./includes/common_footer.php');
?>


<?php


if (trim($SubLayoutJavascript)!=""){
	echo "\n";
	echo "<script>";
	echo "\n";
	echo $SubLayoutJavascript;
	echo "\n";
	echo "</script>";
	echo "\n";
}

if (trim($MainLayoutJavascript)!=""){
	echo "\n";
	echo "<script>";
	echo "\n";
	echo $MainLayoutJavascript;
	echo "\n";
	echo "</script>";
	echo "\n";
}
?>
<script src="js/animatescroll.min.js"></script>
</body>
</html>
<?php
include_once('./includes/dbclose.php');
?>





