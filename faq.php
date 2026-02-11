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

$Sql2 = "
		select 
			* 
		from Faqs A 
		order by A.FaqOrder asc";//." limit $StartRowNum, $PageListNum";
$Stmt2 = $DbConn->prepare($Sql2);
$Stmt2->execute();
$Stmt2->setFetchMode(PDO::FETCH_ASSOC);


?>


<div class="sub_wrap">

    <div class="sub_title_common_wrap"><h2 class="sub_title_common TrnTag">자주 <b>묻는</b> 질문</h2></div>  

    <!-- 자주 묻는 질문 -->
    <section class="faq_sub_wrap">            
        <div class="faq_sub_area">
            <ul class="faq_sub_list">
			<?php
			while($Row2 = $Stmt2->fetch()) {
				$FaqTitle = $Row2["FaqTitle"];
				$FaqContent = $Row2["FaqContent"];
			?>
                <li>
                    <a href="#" class="faq_sub_button">
                        <div class="faq_q">
                            <div class="faq_icon"><img src="images/icon_q.png" alt="질문" class="icon"></div>
                            <div class="faq_question">
                                <?=$FaqTitle?>
                            </div>
                            <div class="faq_arrow"></div>
                        </div>
                    </a>
                    <div class="faq_a">
                        <div class="faq_icon"><img src="images/icon_a_2.png" alt="답변" class="icon"></div>
                        <div class="faq_answer">
							<?=$FaqContent?>
                        </div>
                    </div>                    
                </li>
			<?php } ?>
				<!--
                <li>
                    <a href="#" class="faq_sub_button">
                        <div class="faq_q">
                            <div class="faq_icon"><img src="images/icon_q.png" alt="질문" class="icon"></div>
                            <div class="faq_question">
                                [수업진행관련] 수업 전화가 오지 않았어요! (화상 수업 시 강사님이 입장하지 않았어요)
                            </div>
                            <div class="faq_arrow"></div>
                        </div>
                    </a>
                    <div class="faq_a">
                        <div class="faq_icon"><img src="images/icon_a_2.png" alt="답변" class="icon"></div>
                        <div class="faq_answer">
                            전화가 오지 않았을 경우, [마이페이지] - [학습캘린더]에 가셔서 해당 수업 피드백에 출결 여부를 먼저 확인해 주시기 바랍니다.<br><br>
                            1. 전화가 오지 않았는데 해당일에 수업 연결 녹취가 있고, 결석처리가 되었다면, 인터넷이나 전화 연결이 강사님 쪽이나, 회원님 쪽에서 간혹 오류가 발생할 수 있습니다. 문제가 발생했던 수업 일정을 게시판에 문의해주시면 상담원이 현지 교육 센터 측에 확인한 후 상담전화를 드리고 있습니다.<br>
                            2. 출결 여부에 아무 표시가 없다면, 강사님이 수업을 조금 늦게 진행하실 수도 있고, 현지 센터(태풍, 정전 등), 강사님 문제(병결), 수업 번호 문제 등으로 인해 수업 연결이 되지 않을 경우가 있습니다. 현지에서 수업을 진행하는 부분이라 간혹 문제가 생기는 경우가 있으므로 너그러이 양해해 주시고, 해당 수업 내용을 게시판에 남겨주시면 상담원이 센터 측에 확인하여 보강 수업을 등록해 드리고 사유에 대한 상담전화를 드리겠습니다.<br><br>
                            게시판 처리 고객센터 상담은 평일 오전 9시 ~ 오후 6시 사이에 드리고 있으니, 미리 게시판에 올려주시고 기다려 주시면, 가능한 빠르게 처리하여 연락드리겠습니다. 
                        </div>
                    </div>
                </li>
                <li>
                    <a href="#" class="faq_sub_button">
                        <div class="faq_q">
                            <div class="faq_icon"><img src="images/icon_q.png" alt="질문" class="icon"></div>
                            <div class="faq_question">
                                [스케쥴 관련] 여행이나 출장으로 3일 이상 수업을 연기해야 하는데 3일 이후에는 연기처리가 되지 않아요!
                            </div>
                            <div class="faq_arrow"></div>
                        </div>
                    </a>
                    <div class="faq_a">
                        <div class="faq_icon"><img src="images/icon_a_2.png" alt="답변" class="icon"></div>
                        <div class="faq_answer">
                            3일 이상 수업이 어려우신 경우에는 장기 홀드를 신청하실 수 있습니다. 장기 홀드는 수업을 일정 기간동안 중단하는 것이며, 이를 위해서는 고객센터로 전화주시거나, 1:1 문의 게시판에 요청해 주셔야 합니다.<br>
                            고객센터의 상담은 평일 오전 9시 ~ 오후 6시 사이에 이루어지므로 그 이전 수업에 대해서는 미리 연기를 하시고, 수업을 받기 힘드신 기간을 고객센터에 알려주시면 상담후 처리해 드리겠습니다. 고객센터로 전화하기 힘드신 경우에는 1:1 문의 게시판을 통해 요청하셔도 됩니다.<br>
                            장기 홀드 후 수업 재시작 시 강사님과 수업 시간이 변경될 수 있으니 이점을 반드시 유의해 주시기 바랍니다.
                        </div>
                    </div>
                </li>
				-->
            </ul>            

			<!--
            <div class="bbs_page">
                <span class="arrow_left_2"><img src="images/arrow_bbs_left_2.png"></span>
                <span class="arrow_left_1"><img src="images/arrow_bbs_left_1.png"></span>
                <span class="active">1</span>
                <a href="#">2</a>
                <a href="#">3</a>
                <a href="#" class="arrow_right_1"><img src="images/arrow_bbs_right_1.png"></a>
                <a href="#" class="arrow_right_2"><img src="images/arrow_bbs_right_2.png"></a>
            </div>
			-->

        </div>
    </section>
   
</div>



<script language="javascript">
$('.toggle_navi.six .three').addClass('active');
$('.sub_visual_navi .three').addClass('active');


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





