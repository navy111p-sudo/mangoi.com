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


$LinkLoginDeviceID = isset($_COOKIE["LinkLoginDeviceID"]) ? $_COOKIE["LinkLoginDeviceID"] : "";
?>


<div class="sub_wrap">       
    <div class="sub_title_common_wrap"><h2 class="sub_title_common"><b>디바이스</b>상태</h2></div>

    <section class="mypage_wrap" style="border-bottom:0;">
        <div class="mypage_area">
            <!-- 마이페이지 상단 -->
            <div class="mypage_top_wrap">
				<a style="font-size:3em;"><?=$LinkLoginDeviceID?> 기기 대기 중</a>
				<div class="uk-width-medium-2-10" style="padding-top:7px; display:<?if($LinkLoginDeviceID!=null) {?>none<?}?>">
					<div>
						<select name="CenterDeviceID" id="CenterDeviceID" class="uk-width-1-1" data-md-select2 data-allow-clear="true" style="width:100%;">
						디바이스세팅 :<option value="">디바이스명</option>
						<?php

							//  멤버와 타임존을 조인하여 값을 가져올 것. id 가 매칭이 된다면 default 로 지정
							$Sql2 = "select A.* from CenterDevices A where A.CenterID=:_LINK_MEMBER_CENTER_ID_";

							$Stmt2 = $DbConn->prepare($Sql2);
							$Stmt2->bindParam(':_LINK_MEMBER_CENTER_ID_', $_LINK_MEMBER_CENTER_ID_);
							$Stmt2->execute();
							$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
							while($Row2 = $Stmt2->fetch()) {
								$CenterDeviceID = $Row2["CenterDeviceID"];
								$CenterDeviceName = $Row2["CenterDeviceName"];
						?>
						<option value="<?=$CenterDeviceID?>"><?=$CenterDeviceName?></option>
						<?php
							}
						?>
						</select>
					</div>
				<a href="javascript:AddDeviceCookie();">등록하기</a>
				</div>
			<a href="javascript:ChangeDeviceCookie();">변경하기</a>
			
			<a href="javascript:OpenUpPopUp();">입장</a>
			</div>
        </div>
    </section>
</div>

<script>

	function AddDeviceCookie(CenterDeviceID) {
		var CenterDeviceID = document.getElementById("CenterDeviceID").value;
		setCookie('LinkLoginDeviceID', CenterDeviceID, '.mangoidev.hihome.kr', 365);
		location.reload(true);
	}

	function ChangeDeviceCookie(CenterDeviceID) {
		setCookie('LinkLoginDeviceID', "", '.mangoidev.hihome.kr', -3600);
		location.reload(true);
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

	}

	function onClose(Evt)
	{

	}

	function onMessage(Evt)
	{
		var json = JSON.parse(Evt.data);
		var TempDeviceID = json.DeviceID; // 특정 기기만 받게 하게 위한 변수
		var OriDeviceID = getCookie('LinkLoginDeviceID');
		alert("type : "+json.Type);
		alert("center :"+json.CenterID);
		alert("device : "+json.DeviceID);
		alert("member : "+json.MemberID);
		alert("oriDeviceID : "+OriDeviceID);

		if(TempDeviceID==OriDeviceID) {
			if(json.Type=="cookie") {
				var TempCenterID = json.CenterID;
				var TempMemberID = json.MemberID;
				alert("center11111");
				var OriAdminMemberID = getCookie('LinkLoginAdminID');
				var OriMemberID = getCookie("LinkLoginMemberID");
				var OriCenterID = "<?=$_LINK_MEMBER_CENTER_ID_?>";
				alert("oricenterid : "+OriCenterID);
				alert("tempCenterID : "+TempCenterID);
				if(OriCenterID==TempCenterID) {
					alert("center2222");
					if(TempMemberID != OriAdminMemberID) {
						alert("기존 ID "+OriMemberID+" 에서 ID "+TempMemberID+"로 변환되었습니다.");
						alert("로그인 되셨습니다");
						setCookie("LinkLoginMemberID", TempMemberID, ".mangoidev.hihome.kr");
						var send = { Type: "Link", Device: OriDeviceID, Member: TempMemberID, Action: "add" };
						doSend(send);
						location.href = "mypage_study_room.php";
					} else {
						alert("center3333");
						setCookie("LinkLoginMemberID", TempMemberID, ".mangoidev.hihome.kr");
						alert("로그아웃 되셨습니다.");
						location.href = "mypage_center.php";
						var send = { Type: "Link", Device: OriDeviceID, Member: "", Action: "del" };
						doSend(send);
					}
				}
			}
		}
	}

	function onError(Evt)
	{
		writeToScreen('error: ' + Evt.data + '\n');
		websocket.close();

	}

	function doSend(Message) {
		var Strjson = JSON.stringify(Message);
		// 소켓에서 받을 때 "Object: Object 로 받게 되어 String 으로 변환 발송
		websocket.send(Strjson);
	}

	window.addEventListener("load", init, false);


	function doDisconnect() {
		websocket.close();
	}

	// 쿠키 함수 !
	function setCookie(name, value, domain, expire) {
	  var date = new Date();
	  date.setTime(date.getTime() + (expire * 24 * 60 * 60 * 1000));
	  var expire = date.toGMTString();
	  //document.cookie = namae + '=' + value + ';expires=' + date.toUTCString() + ';path=/';
	  document.cookie = name + '=' + value+';domain='+domain+'; expires='+expire;
	}

	function getCookie(name) {
	  var value = document.cookie.match('(^|;) ?' + name + '=([^;]*)(;|$)');
	  return value? value[2] : null;
	};
</script>



<script language="javascript">
$('.sub_visual_navi .one').addClass('active');

</script>
<link rel="stylesheet" href="../js/colorbox/example2/colorbox.css" />
<script src="../js/colorbox/jquery.colorbox.js"></script>

// 패스워드 입력창 띄우기
function OpenUpPopUp() {
	openurl = "mypage_center_popup_form.php";
	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "850"
		,maxHeight: "750"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
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





