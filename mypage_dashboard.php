<?php
include_once('./includes/dbopen.php');
include_once('./includes/common.php');
$DenyGuest = true;
include_once('./includes/member_check.php');

$UseMain = 0;
$UseSub = 1;
$SubCode = "sub_07";
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
    $SubLayoutTop = convertHTML(trim("{{Piece(sub_07_gumiivyleague)}}"));
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


$Sql = "
		select 
				sum(MemberPoint) as MemberPoint
		from MemberPoints A 
		where A.MemberID=:MemberID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $_LINK_MEMBER_ID_);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$MemberPoint = $Row["MemberPoint"];

$Sql = "
		select 
				A.MemberPhoto,
				DATE_FORMAT(A.MemberRegDateTime,'%Y년 %m월 %d일') as MemberRegDateTime
		from Members A 
		where A.MemberID=:MemberID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $_LINK_MEMBER_ID_);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$MemberRegDateTime = $Row["MemberRegDateTime"];

$MemberPhoto = $Row["MemberPhoto"];

if ($MemberPhoto==""){
	$StrMemberPhoto = "../images/no_photo.png";
}else{
	$StrMemberPhoto = "../uploads/member_photos/".$MemberPhoto;
}



$NowYear = date("Y");
$NowMonth = date("n");
$NowDay = date("j");


$Sql = "select 
				A.*,
				B.TeacherName

		from Classes A 
			inner join Teachers B on A.TeacherID=B.TeacherID 
		where 
			A.MemberID=:MemberID 
			and A.StartYear=:StartYear 
			and A.StartMonth=:StartMonth 
			and A.StartDay=:StartDay 
		order by A.StartHour asc, A.StartMinute asc limit 0,1";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $_LINK_MEMBER_ID_);
$Stmt->bindParam(':StartYear', $NowYear);
$Stmt->bindParam(':StartMonth', $NowMonth);
$Stmt->bindParam(':StartDay', $NowDay);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$StartHour = $Row["StartHour"];
$StartMinute = $Row["StartMinute"];
$TeacherName = $Row["TeacherName"];


?>


<div class="sub_wrap">       
    <div class="sub_title_common_wrap"><h2 class="sub_title_common"><b>DashBoard</b></h2></div>

		<section class="mypage_wrap" style="border-bottom:0;">


			<div class="uk-width-medium-2-10" style="padding-top:7px;">
				<select name="CenterClassID" id="CenterClassID" class="Select" onchange="getClassInfo(this)" style="">
					<option value="">수업명</option>
					<?
					$Sql2 = "select * from CenterClasses where CenterID=:_LINK_MEMBER_CENTER_ID_";
					$Stmt2 = $DbConn->prepare($Sql2);
					$Stmt2->bindParam(':_LINK_MEMBER_CENTER_ID_', $_LINK_MEMBER_CENTER_ID_);
					echo $Sql2;
					echo $_LINK_MEMBER_CENTER_ID_;
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
			</div>
			<div class="uk-width-medium-2-10" style="padding-top:7px;">
				<select name="CenterDeviceID" id="CenterDeviceID" class="uk-width-1-1" data-md-select2 data-allow-clear="true" style="width:100%;">
				<option value="">디바이스명</option>
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
				<option value="<?=$CenterDeviceID?>"><?=$CenterDeviceName?></option>
				<?php
					}
				?>
				</select>
			</div>
			<div class="uk-width-medium-2-10" style="padding-top:7px;">
				<select name="MemberID" id="MemberID" class="uk-width-1-1" data-md-select2 data-allow-clear="true" style="width:100%;">
				<option value="">학생명</option>
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
				<option value="<?=$MemberID?>" id="<?=$MemberLoginID?>" ><?=$MemberName?></option>
				<?php
					}
				?>
				</select>
			</div>
			<div>
				<a href="javascript:SendLoginCookie();">등록하기</a>
			</div>
		</div>
		<br/>
		<div>
			<table>
				<thead>
					<th width=15%>디바이스명</th>
					<th width=10%>학생명</th>
					<th width=5%>상태</th>
					<th width=5%></th>
				</thead>
				<tbody>
				<?php
					$Sql5 = "select A.CenterDeviceID, A.CenterDeviceName from CenterDevices A where A.CenterID=:_LINK_MEMBER_CENTER_ID_";
					$Stmt5 = $DbConn->prepare($Sql5);
					$Stmt5->bindParam(':_LINK_MEMBER_CENTER_ID_', $_LINK_MEMBER_CENTER_ID_);
					$Stmt5->execute();
					$Stmt5->setFetchMode(PDO::FETCH_ASSOC);
					while($Row5 = $Stmt5->fetch()) {
						$CenterDeviceID = $Row5["CenterDeviceID"];
						$CenterDeviceName = $Row5["CenterDeviceName"];
						$CookieName = 'D_'.$CenterDeviceID;  // 디바이스 별로 쿠키 이름을 정함
						$GetCookie = isset($_COOKIE[$CookieName]) ? $_COOKIE[$CookieName] : "";  // 받아온 쿠키 체크

						if ($GetCookie) {
							$CookieStatus = "사용 중";
						} else {
							$CookieStatus = "미사용";
						}
					?>
					<tr>
						<td><?=$CenterDeviceName?></td>
						<td><?=$GetCookie?></td>
						<td><?=$CookieStatus?></td>
						<td><a href="javascript:SendLogoutCookie(<?=$CenterDeviceID?>);">연결해제</a></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</section>
	</div>
</div>

<script>
// 웹소켓

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
    }

    function onClose(Evt)
    {
    }

    function onMessage(Evt)
    {
		alert(Evt.data);
		var json = JSON.parse(Evt.data);

		alert(json);
		if(json.Type == "Link") {
			var Member = json.Member;
			var Device = json.Device;
			var Action = json.Action;
			var Url = "ajax_set_cookie.php";

			$.ajax({
				url: Url,
				data: {
					MemberID: Member,
					DeviceID: Device,
					Action: Action
				},
				success: function(res) {
					var cookie = res.cookie;
			
					if(cookie) {
						location.reload(true);
					} else {
						alert(Device+" 의 쿠키생성에 실패했습니다.");
					}
				}
			});
		}
    }

 

	function doSend(Message) {
		var Strjson = JSON.stringify(Message);
		// 소켓에서 받을 때 "Object: Object 로 받게 되어 String 으로 변환 발송
		websocket.send(Strjson);
	}

	function onError(Evt)
	{
		websocket.close();
	}

	window.addEventListener("load", init, false);


	function doDisconnect() {
		websocket.close();
	}

</script>

<script language="javascript">
$('.sub_visual_navi .one').addClass('active');


function SendLoginCookie() {
	var MemberID = document.getElementById("MemberID").value;
	var MemberLoginIDIndex = document.getElementById("MemberID").selectedIndex;
	var MemberLoginID = document.getElementById("MemberID").options[MemberLoginIDIndex].id;
	var CenterID = <?=$_LINK_MEMBER_CENTER_ID_?>;
	var DeviceID = document.getElementById("CenterDeviceID").value;

	var json = {
		Type: "cookie",
		CenterID: CenterID,
		MemberID: MemberID,
		MemberLoginID: MemberLoginID,
		DeviceID: DeviceID
	};
	doSend(json);
}

function SendLogoutCookie(DeviceID) {
	var MemberLoginID = getCookie("LinkLoginAdminID");
	var CenterID = <?=$_LINK_MEMBER_CENTER_ID_?>;

	var json = {
		Type: "cookie",
		CenterID: CenterID,
		MemberID: MemberLoginID,
		DeviceID: DeviceID
	};
	doSend(json);
}

function getClassInfo(CenterClassID) {
	var CenterClassID = CenterClassID.value;
	location.href = "mypage_dashboard.php?CenterClassID="+CenterClassID;
}

function setCookie(name, value, domain) {
  //date.setTime(date.getTime() + exp*24*60*60*1000);
  //document.cookie = namae + '=' + value + ';expires=' + date.toUTCString() + ';path=/';
  document.cookie = name + '=' + value+';domain='+domain;
}

function getCookie(name) {
  var value = document.cookie.match('(^|;) ?' + name + '=([^;]*)(;|$)');
  return value? value[2] : null;
};

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
?>


<?php
include_once('./includes/common_footer.php');

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





