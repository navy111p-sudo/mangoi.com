<?php
include_once('./includes/dbopen.php');
include_once('./includes/common.php');
include_once('./includes/member_check.php');

$UseMain = 0;
$UseSub = 1;
$SubCode = "sub_06";
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
    $SubLayoutTop = convertHTML(trim("{{Piece(sub_06_gumiivyleague)}}"));
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

$EventID = isset($_REQUEST["EventID"]) ? $_REQUEST["EventID"] : "";

$Sql2 = "select 
			* 
		from Events A 
		where A.EventID=:EventID";

$Stmt2 = $DbConn->prepare($Sql2);
$Stmt2->bindParam(':EventID', $EventID);
$Stmt2->execute();
$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
$Row2 = $Stmt2->fetch();
$Stmt2 = null;

$today = strtotime(date("Y-m-d"));
$EventStatus = "";

$EventImageFileName = $Row2["EventImageFileName"];
$EventTitle = $Row2["EventTitle"];
$EventContent = $Row2["EventContent"];
$TempEventRegDateTime = strtotime($Row2["EventRegDateTime"]);
$TempEventStartDate = strtotime($Row2["EventStartDate"]);
$TempEventEndDate = strtotime($Row2["EventEndDate"]);
$StrEventRegDateTime = date("Y.m.d", $TempEventRegDateTime);

if ( $today > $TempEventEndDate ) {
	$EventStatus = "<span class=\"event_status TrnTag\">종료</span>";
} elseif ($today >= $TempEventStartDate && $today <= $TempEventEndDate) {
	$EventStatus = "<span class=\"event_status ing TrnTag\">진행중</span>";
} elseif ($today < $TempEventStartDate) {
	$EventStatus = "<span class=\"event_status ing TrnTag\">예정</span>";
}

if($EventImageFileName!="") {
	$EventImageFileName = $EventImageFileName;
} else {
	$EventImageFileName = 'no_photo_2.png';
}
?>


<div class="sub_wrap bg_gray">
    
    <div class="sub_title_common_wrap"><h2 class="sub_title_common TrnTag"><b>이벤트</b></h2></div>  

    <!-- 게시판 관련 -->
    <section class="event_wrap">
        <div class="event_area">
            <div class="event_content_wrap">
                <div class="event_content_top">
                    <img src="./uploads/event_images/<?=$EventImageFileName?>" class="event_content_img">
                    <?=$EventStatus?>
					<!--span class="event_status TrnTag">종료</span-->
                </div>
                <div class="event_content_area">
                    <h3 class="event_content_caption ellipsis"><?=$EventTitle?></h3>
					<?=$EventContent?>
                </div>
            </div>
            <div class="button_wrap text_right"><a href="event_list.php" class="button_sea_white TrnTag">목록</a></div>
        </div>
    </section>

</div>


<script language="javascript">
$('.toggle_navi.six .two').addClass('active');
$('.sub_visual_navi .two').addClass('active');

function GetSchoolGradeID(){
	SchoolTypeID = document.RegForm.SchoolTypeID.value;
	
	url = "ajax_get_school_grade_id.php";

	//location.href = url + "?SchoolTypeID="+SchoolTypeID;
	$.ajax(url, {
		data: {
			SchoolTypeID: SchoolTypeID
		},
		success: function (data) {

			ArrOption = data.SchoolGradeIDs.split("{{|}}");
			SelBoxInitOption('SchoolGradeID');

			SelBoxAddOption( 'SchoolGradeID', '학년선택', "", "");
			for (ii=2 ; ii<=ArrOption.length-1 ; ii++ ){
				ArrOptionText     = ArrOption[ii].split("{|}")[0];
				ArrOptionValue    = ArrOption[ii].split("{|}")[1];
				ArrOptionSelected = ArrOption[ii].split("{|}")[2];
				SelBoxAddOption( 'SchoolGradeID', ArrOptionText, ArrOptionValue, ArrOptionSelected );
			}

			if (SchoolTypeID==3){
				document.getElementById("SchoolCourseID").style.display = "";
			}else{
				document.getElementById("SchoolCourseID").style.display = "none";
			}
		},
		error: function () {
			alert('오류가 발생했습니다. 다시 시도해 주세요.');
		}
	});	
}


function FormSubmit(){

	obj = document.RegForm.StudentName;
	if (obj.value==""){
		alert('학생성명을 입력하세요.');
		obj.focus();
		return;
	}


	/*
	obj = document.RegForm.SchoolName;
	if (obj.value==""){
		alert('소속학교를 입력하세요.');
		obj.focus();
		return;
	}

	obj = document.RegForm.SchoolTypeID;
	if (obj.value==""){
		alert('학교구분을 선택하세요.');
		obj.focus();
		return;
	}

	obj = document.RegForm.SchoolGradeID;
	if (obj.value==""){
		alert('학년을 선택하세요.');
		obj.focus();
		return;
	}

	obj = document.RegForm.SchoolCourseID;
	if (document.RegForm.SchoolTypeID.value=="3" && obj.value==""){
		alert('계열을 선택하세요.');
		obj.focus();
		return;
	}

	obj = document.RegForm.WishUniversity1;
	if (obj.value==""){
		alert('1지망 학교/학과를 입력하세요.');
		obj.focus();
		return;
	}

	obj = document.RegForm.WishUniversity2;
	if (obj.value==""){
		alert('2지망 학교/학과를 입력하세요.');
		obj.focus();
		return;
	}
	*/

	obj = document.RegForm.WishDate;
	if (obj.value==""){
		alert('방문 날짜를 입력하세요.');
		obj.focus();
		return;
	}

	obj = document.RegForm.WishTime;
	if (obj.value==""){
		alert('방문 시간를 입력하세요.');
		obj.focus();
		return;
	}

	obj = document.RegForm.PhoneNumber2;
	if (obj.value==""){
		alert('전화번호를 입력하세요.');
		obj.focus();
		return;
	}

	obj = document.RegForm.PhoneNumber3;
	if (obj.value==""){
		alert('전화번호를 입력하세요.');
		obj.focus();
		return;
	}

	obj = document.RegForm.Description;
	if (obj.value==""){
		alert('상담내용을 입력하세요.');
		obj.focus();
		return;
	}

	obj = document.RegForm.Agree;
	if (obj.checked==false){
		alert('개인정보수집에 동의해 주시기 바랍니다.');
		return;
	}

	if (confirm("상담신청을 하시겠습니까?")){
		document.RegForm.action = "reservation_action.php";
		document.RegForm.submit();	
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

</body>
</html>
<?php
include_once('./includes/dbclose.php');
?>





