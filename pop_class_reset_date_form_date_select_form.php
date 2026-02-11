<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
include_once('./includes/member_check.php');

$UseMain = 0;
$UseSub = 1;
$SubCode = "sub_04";
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, minimal-ui">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
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

<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="./js/jquery-confirm.min.js"></script>
<link href="./css/jquery-confirm.min.css" rel="stylesheet" type="text/css"/>
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
    // $SubLayoutTop = convertHTML(trim($SubLayoutTop));
    // $SubLayoutBottom = convertHTML(trim($SubLayoutBottom));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_gumiivyleague)}}"));

} else if($DomainSiteID==8){ //engliseed.kr
    $MainLayoutTop = convertHTML(trim("{{Piece(header_engliseed)}}"));
//    $SubLayoutTop = convertHTML(trim("{{Piece(sub_06_engliseed)}}"));
//    $SubLayoutBottom = convertHTML(trim("{{Piece(sub_01_engliseed_bottom)}}"));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_engliseed)}}"));

} else if($DomainSiteID==9){ //live.engedu.kr
    $MainLayoutTop = convertHTML(trim("{{Piece(header_engedu)}}"));
//    $SubLayoutTop = convertHTML(trim("{{Piece(sub_06_engliseed)}}"));
//    $SubLayoutBottom = convertHTML(trim("{{Piece(sub_01_engliseed_bottom)}}"));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_engedu)}}"));

} else {
    $MainLayoutTop = convertHTML(trim($MainLayoutTop));
    $SubLayoutTop = convertHTML(trim($SubLayoutTop));
    $SubLayoutBottom = convertHTML(trim($SubLayoutBottom));
    $MainLayoutBottom = convertHTML(trim($MainLayoutBottom));
  }

echo "\n";
//echo $MainLayoutTop;
echo "\n";
//echo $SubLayoutTop;
echo "\n";
?>

<?
$FromDevice = isset($_REQUEST["FromDevice"]) ? $_REQUEST["FromDevice"] : "";
$ClassID = isset($_REQUEST["ClassID"]) ? $_REQUEST["ClassID"] : "";
$ClassOrderID = isset($_REQUEST["ClassOrderID"]) ? $_REQUEST["ClassOrderID"] : "";
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$TeacherID = isset($_REQUEST["TeacherID"]) ? $_REQUEST["TeacherID"] : "";
$GroupRowCount = isset($_REQUEST["GroupRowCount"]) ? $_REQUEST["GroupRowCount"] : "";
$ClassMemberType = isset($_REQUEST["ClassMemberType"]) ? $_REQUEST["ClassMemberType"] : "";
$SetYear = isset($_REQUEST["SetYear"]) ? $_REQUEST["SetYear"] : "";
$SetMonth = isset($_REQUEST["SetMonth"]) ? $_REQUEST["SetMonth"] : "";
$SetDay = isset($_REQUEST["SetDay"]) ? $_REQUEST["SetDay"] : "";
$SetHour = isset($_REQUEST["SetHour"]) ? $_REQUEST["SetHour"] : "";
$SetMinute = isset($_REQUEST["SetMinute"]) ? $_REQUEST["SetMinute"] : "";
$SetWeek = isset($_REQUEST["SetWeek"]) ? $_REQUEST["SetWeek"] : "";
$ResetType = isset($_REQUEST["ResetType"]) ? $_REQUEST["ResetType"] : "";
$FromPage = isset($_REQUEST["FromPage"]) ? $_REQUEST["FromPage"] : "";
$IframeMode = isset($_REQUEST["IframeMode"]) ? $_REQUEST["IframeMode"] : "";


$Sql = "
	select
		A.*,
		B.ClassOrderPayID,
		B.ClassProductID, 
		B.ClassOrderTimeTypeID,
		B.ClassMemberType,
		D.CenterPayType
	from Classes A 
		inner join ClassOrders B on A.ClassOrderID=B.ClassOrderID 
		inner join Members C on A.MemberID=C.MemberID 
		inner join Centers D on C.CenterID=D.CenterID 
	where ClassID=$ClassID 
";


$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$ClassOrderID = $Row["ClassOrderID"];
$MemberID = $Row["MemberID"];
$TeacherID = $Row["TeacherID"];
$ClassOrderPayID = $Row["ClassOrderPayID"];
$ClassProductID = $Row["ClassProductID"];
$ClassOrderTimeTypeID = $Row["ClassOrderTimeTypeID"];
$ClassMemberType = $Row["ClassMemberType"];
$CenterPayType = $Row["CenterPayType"];
$StartDateTime = $Row["StartDateTime"];
$StartHour = $Row["StartHour"];
$StartMinute = $Row["StartMinute"];





if ($ResetType=="ChTeacher"){
	$StrResetType = "강사변경";
	$StudyTimeDate = substr($StartDateTime,0,10);
}else if ($ResetType=="PlusClass"){
	$StrResetType = "보강등록";
	$StudyTimeDate = date("Y-m-d", strtotime(substr($StartDateTime,0,10). " + 1 days"));
}else if ($ResetType=="EverChange"){
	$StrResetType = "스케줄변경";
	$StudyTimeDate = substr($StartDateTime,0,10);
	if ($SetWeek == "") $SetWeek = date('w', strtotime($StudyTimeDate));
	if ($SetHour == "") $SetHour = $StartHour;
	if ($SetMinute == "") $SetMinute = $StartMinute;
}else{
	$StrResetType = "연기";
	$StudyTimeDate = date("Y-m-d", strtotime(substr($StartDateTime,0,10). " + 1 days"));
}



?>

<!-- 헤더(앱) 영역 -->
<?if ($FromDevice=="app"){?>
<header class="header_app_wrap">
    <h1 class="header_app_title"><?=$StrResetType?> 신청</h1>
    <a href="javascript:window.Exit=true" class="header_app_close header_app_gray"><img src="images/btn_app_close_black.png" class="icon"></a>
</header>
<?}?>

<div class="sub_wrap bg_gray padding_app" style="border:0;<?if ($FromDevice==""){?>margin-top:-60px;<?}?>">   

    <section class="level_application_wrap">
        <div class="level_application_area">
            
			<div class="level_application_text_box" style="display:inline-block;margin-bottom:30px;width:49%;margin-right:1%;cursor:pointer;background-color:#cccccc;" onclick="ChResetSelectType(2)">
				강사먼저 선택
            </div>
			<div class="level_application_text_box" style="display:inline-block;margin-bottom:30px;width:49%;">
				날짜먼저 선택
            </div>
			

			<script>
			function ChResetSelectType(v){
				if (v==1){
					url = "pop_class_reset_date_form_date_select_form.php";
				}else{
					url = "pop_class_reset_date_form_teacher_select_form.php";
				}

				location.href = url+"?FromDevice=<?=$FromDevice?>&ClassID=<?=$ClassID?>&ClassOrderID=<?=$ClassOrderID?>&MemberID=<?=$MemberID?>&TeacherID=<?=$TeacherID?>&GroupRowCount=<?=$GroupRowCount?>&ClassMemberType=<?=$ClassMemberType?>&SetYear=<?=$SetYear?>&SetMonth=<?=$SetMonth?>&SetDay=<?=$SetDay?>&SetHour=<?=$SetHour?>&SetMinute=<?=$SetMinute?>&SetWeek=<?=$SetWeek?>&ResetType=<?=$ResetType?>&FromPage=<?=$FromPage?>&IframeMode=<?=$IframeMode?>";
			}
			</script>
  
			<form name="RegForm" id="RegForm" style="display:none;">

			<input type="hidden" name="FromPage" id="FromPage" value="<?=$FromPage?>">
			<input type="hidden" name="ClassOrderPayID" id="ClassOrderPayID" value="<?=$ClassOrderPayID?>">
			<input type="hidden" name="IframeMode" id="IframeMode" value="<?=$IframeMode?>">
			<input type="text" name="EduCenterID" id="EduCenterID" value="1">

			<input type="text" name="TeacherID" id="TeacherID" value="">
			<input type="text" name="StudyTimeDate" id="StudyTimeDate" value="<?=$StudyTimeDate?>">
			<input type="text" name="StudyTimeHour" id="StudyTimeHour" value="">
			<input type="text" name="StudyTimeMinute" id="StudyTimeMinute" value="">

			<input type="text" name="FromDevice" id="FromDevice" value="<?=$FromDevice?>">
			<input type="text" name="ResetType" id="ResetType" value="<?=$ResetType?>">
			<input type="text" name="ClassID" id="ClassID" value="<?=$ClassID?>">
			<input type="text" name="ClassOrderID" id="ClassOrderID" value="<?=$ClassOrderID?>">
			<input type="text" name="MemberID" id="MemberID" value="<?=$MemberID?>">
			<input type="text" name="ClassProductID" id="ClassProductID" value="<?=$ClassProductID?>">
			<input type="text" name="ClassMemberType" id="ClassMemberType" value="<?=$ClassMemberType?>">
			<input type="text" name="CenterPayType" id="CenterPayType" value="<?=$CenterPayType?>">
			<input type="text" name="ClassOrderTimeTypeID" id="ClassOrderTimeTypeID" value="<?=$ClassOrderTimeTypeID?>">

			<!-- 스케줄 변경의 경우 기존 슬랏 정보 -->
			<input type="text" name="SetTeacherID" id="SetTeacherID" value="<?=$TeacherID?>">
			<input type="text" name="SetYear" id="SetYear" value="<?=$SetYear?>">
			<input type="text" name="SetMonth" id="SetMonth" value="<?=$SetMonth?>">
			<input type="text" name="SetDay" id="SetDay" value="<?=$SetDay?>">
			<input type="text" name="SetHour" id="SetHour" value="<?=$SetHour?>">
			<input type="text" name="SetMinute" id="SetMinute" value="<?=$SetMinute?>">
			<input type="text" name="SetWeek" id="SetWeek" value="<?=$SetWeek?>">
			<!-- 스케줄 변경의 경우 기존 슬랏 정보 -->

			<input type="text" name="GroupRowCount" id="GroupRowCount" value="<?=$GroupRowCount?>">
			</form>

            
			<div class="level_teacher_select_list">
				<div class="lms_teacher_time_wrap">
                    <div class="lms_teacher_time_caption" id="DivSelCalendarHTML">
						<!-- 달력 -->
                    </div>
					<span class="lms_teacher_time_line"></span>
					<table class="lms_teacher_time_table">
						<tr>
							<th id="DivDateTitle"><!-- 선택날짜 --></th>
						</tr>
						<tr>
							<td>
								<form name="SearchForm" id="SearchForm">
								<select name="StudyTimeHour2" id="StudyTimeHour2" class="lms_teacher_time_table_select" style="width:120px;padding-left:15px;padding-right:0px;text-align:center;" onchange="GetTeacherList()" >
									<?
									for ($ii=9;$ii<=23;$ii++){
										$iii=$ii;
										if ($iii<=12){
											$iii = "am ".substr("0".$iii,-2);
										}else{
											$iii = "pm ".substr("0".($iii-12),-2);
										}
									
									?>
									<option value="<?=$ii?>" <?if ($SetHour==$ii){?>selected<?}?>><?=$iii?></option>
									<?
									}
									?>
								</select> 시
								<select name="StudyTimeMinute2" id="StudyTimeMinute2" class="lms_teacher_time_table_select" style="width:80px;padding-left:15px;padding-right:0px;text-align:center;" onchange="GetTeacherList()" style="margin-left:10px;">
									<?for ($ii=0;$ii<=50;$ii=$ii+10){?>
									<option value="<?=$ii?>" <?if ($SetMinute==$ii){?>selected<?}?>><?=substr("0".$ii,-2)?></option>
									<?}?>
								</select> 분
								</form>

								<div style="margin:0px auto;height:50px;width:200px;background-color:#333333;margin-top:20px;border-radius:10px;text-align:center;line-height:50px;color:#F9D353;cursor:pointer;" onclick="GetTeacherListNew()">강사검색</div>
							</td>
						</tr>
					</table>
					<?php //echo ":".$SetWeek.":".$SetHour.":".$SetMinute.":"; ?>
                </div>


                <ul class="lms_teacher_select_list" id="DivTeacherListHTML" style="margin-bottom:80px;">
					<!-- 강사 선택 목록 -->
                </ul>
			
			</div>
			
			

        </div>
    </section>

</div>




<script>
function GetCalendar(SelDate){

	url = "ajax_get_class_reset_date_calendar.php";
	//window.open(url + "?DefaultDate=<?=$StudyTimeDate?>", 'ajax_get_class_reset_date_calendar');

	
	$.ajax(url, {
		data: {
			SelDate: SelDate
		},
		success: function (data) {

			document.getElementById("DivDateTitle").innerHTML = data.DateTitle;
			document.getElementById("DivSelCalendarHTML").innerHTML = data.SelCalendarHTML;

			document.RegForm.StudyTimeDate.value = SelDate;

			GetTeacherList();
		},
		error: function () {
			alert('err1');
		}
	});
} 

function GetTeacherList(){
	//기능 없앰
}


function GetTeacherListNew(){

	document.getElementById("DivTeacherListHTML").innerHTML = "<div style=\"width:100%;text-align:center;margin-top:50px;\"><img src=\"images/loading.gif\"><br><span style=\"font-weight: 500; font-size: 18px;\">강사 목록을 불러오는 중입니다.</span></div>";
	document.RegForm.TeacherID.value = "";

	document.RegForm.StudyTimeHour.value = document.SearchForm.StudyTimeHour2.value;
	document.RegForm.StudyTimeMinute.value = document.SearchForm.StudyTimeMinute2.value;

	url = "ajax_get_class_reset_date_form_date_teacher_list.php";

	EduCenterID = document.RegForm.EduCenterID.value;
	StudyTimeDate = document.RegForm.StudyTimeDate.value;
	StudyTimeHour = document.RegForm.StudyTimeHour.value;
	StudyTimeMinute = document.RegForm.StudyTimeMinute.value;
		
	//if (StudyTimeHour==9){
		//window.open(url + "?EduCenterID="+EduCenterID+"&StudyTimeDate="+StudyTimeDate+"&StudyTimeHour="+StudyTimeHour+"&StudyTimeMinute="+StudyTimeMinute+"&ClassOrderTimeTypeID=<?=$ClassOrderTimeTypeID?>&ResetType=<?=$ResetType?>&ClassOrderID=<?=$ClassOrderID?>", 'ajax_get_class_reset_date_form_date_teacher_list');
	//}
	
	$.ajax(url, {
		data: {
			EduCenterID: EduCenterID,
			StudyTimeDate: StudyTimeDate,
			StudyTimeHour: StudyTimeHour,
			StudyTimeMinute: StudyTimeMinute,
			ClassOrderTimeTypeID: "<?=$ClassOrderTimeTypeID?>",
			ResetType: "<?=$ResetType?>",
			ClassOrderID: "<?=$ClassOrderID?>"
		},
		success: function (data) {

			TeacherListHTML = data.TeacherListHTML;
			
			document.getElementById("DivTeacherListHTML").innerHTML = TeacherListHTML;
		
		},
		error: function () {
			alert('err2');
		}
	});



}


function SelTeacherID(TeacherID){
	document.RegForm.TeacherID.value = TeacherID;

	TeacherID = document.RegForm.TeacherID.value;
	StudyTimeDate = document.RegForm.StudyTimeDate.value;
	StudyTimeHour = document.RegForm.StudyTimeHour.value;
	StudyTimeMinute = document.RegForm.StudyTimeMinute.value;

	<?if ($FromDevice=="app"){?>
		if (TeacherID=="" || StudyTimeHour=="" || StudyTimeMinute==""){
			$.alert({title: "안내", content: "날짜와 시간을 선택해 주세요."});
		}else{
			
			<?if ($_LINK_MEMBER_ID_==""){?>
				$.alert({title: "안내", content: "먼저 로그인 해주세요."});
			<?}else{?>
				$.confirm({
					title: '안내',
					content: '선택한 교사의 날짜와 시간으로 <?=$StrResetType?> 하시겠습니까?',
					buttons: {
						확인: function () {
							document.RegForm.action = "pop_class_reset_date_form_teacher_select_action.php";//강사먼저 선택과 동일
							document.RegForm.submit();
						},
						취소: function () {

						}
					}
				});

			
			<?}?>	
		}
	<?}else{?>
		if (TeacherID=="" || StudyTimeHour=="" || StudyTimeMinute==""){
			alert("날짜와 시간을 선택해 주세요.");
		}else{
			
			<?if ($_LINK_MEMBER_ID_==""){?>
				alert("먼저 로그인 해주세요.");
			<?}else{?>
				if (confirm("선택한 교사의 날짜와 시간으로 <?=$StrResetType?> 하시겠습니까?")){
					document.RegForm.action = "pop_class_reset_date_form_teacher_select_action.php";//강사먼저 선택과 동일
					document.RegForm.submit();
				}
			<?}?>	
		}

	<?}?>

}


function OpenResetReserveSubmit(){

	TeacherID = document.RegForm.TeacherID.value;
	StudyTimeDate = document.RegForm.StudyTimeDate.value;
	StudyTimeHour = document.RegForm.StudyTimeHour.value;
	StudyTimeMinute = document.RegForm.StudyTimeMinute.value;

	<?if ($FromDevice=="app"){?>
		if (TeacherID=="" || StudyTimeHour=="" || StudyTimeMinute==""){
			$.alert({title: "안내", content: "날짜와 시간을 선택해 주세요."});
		}else{
			
			<?if ($_LINK_MEMBER_ID_==""){?>
				$.alert({title: "안내", content: "먼저 로그인 해주세요."});
			<?}else{?>
				$.confirm({
					title: '안내',
					content: '선택한 교사의 날짜와 시간으로 <?=$StrResetType?> 하시겠습니까?',
					buttons: {
						확인: function () {
							document.RegForm.action = "pop_class_reset_date_form_teacher_select_action.php";
							document.RegForm.submit();
						},
						취소: function () {

						}
					}
				});

			
			<?}?>	
		}
	<?}else{?>
		if (TeacherID=="" || StudyTimeHour=="" || StudyTimeMinute==""){
			alert("날짜와 시간을 선택해 주세요.");
		}else{
			
			<?if ($_LINK_MEMBER_ID_==""){?>
				alert("먼저 로그인 해주세요.");
			<?}else{?>
				if (confirm("선택한 교사의 날짜와 시간으로 <?=$StrResetType?> 하시겠습니까?")){
					document.RegForm.action = "pop_class_reset_date_form_date_select_action.php";
					document.RegForm.submit();
				}
			<?}?>	
		}

	<?}?>

}



window.onload = function(){
	SelDate = "<?=$StudyTimeDate?>";
	GetCalendar(SelDate);
}


history.pushState(null, null, location.href);
window.onpopstate = function(event) {

     history.go(1);
	 window.Exit=true;
     //alert("뒤로가기 버튼은 사용할 수 없습니다!");
};
</script>


<script language="javascript">
$('.toggle_navi.four .one').addClass('active');
$('.sub_visual_navi .three').addClass('active');

</script>


<?php
echo "\n";
//echo $SubLayoutBottom;
echo "\n";
//echo $MainLayoutBottom;
echo "\n";
?>

<?
include_once('./includes/common_analytics.php');
?>


<?php
//include_once('./includes/common_footer.php');

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

</body>
</html>
<?php
include_once('./includes/dbclose.php');
?>





