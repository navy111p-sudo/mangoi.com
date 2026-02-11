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
<link rel="stylesheet" type="text/css" href="./js/datetimepicker/jquery.datetimepicker.css">



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
if($DomainSiteID==7) {
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
echo $DomainSiteID;

echo "\n";
echo $MainLayoutTop;
echo "\n";
echo $SubLayoutTop;
echo "\n";

$MemberID = $_LINK_MEMBER_ID_;
?>


<div class="sub_wrap bg_gray">

    <div class="sub_title_common_wrap"><h2 class="sub_title_common TrnTag"><b>원격지원</b> 서비스</h2></div>  

    <!-- 원격지원 -->
    <section class="support_remote_wrap">            
        <div class="support_remote_area">
            <a href="http://helpu.kr/mangoi/" target="_blank">
                <table class="remote_wrap">
                    <tr>
                        <th><img src="images/img_remote.png" class="remote_img"></th>
                        <td class="remote_left">
                            <h3 class="TrnTag">원격지원 서비스</h3>
                            <trn class="TrnTag">원하시는 상담시간에 상담을 원하시면<br>아래 PC원격 예약 상담으로 신청해주세요.</trn>
                        </td>
                        <td class="remote_right">
                            <img src="images/bg_remote.png" class="remote_bg">
                            <div class="remote_link">
                                <div class="remote_caption TrnTag">원격지원<br>프로그램</div>
                                <div class="remote_go">GO</div>
                            </div>
                        </td>
                    </tr>
                </table>
            </a>    
            
            <div class="remote_text_box TrnTag">원격지원은 예약제이기 때문에 순차적으로 진행되며, <div class="break">예약일 당일은 지원이 불가할 수 있습니다.</div></div>

            <h3 class="member_join_caption TrnTag">원격지원 예약상담<span>아래의 양식을 작성해 주시면 원하시는 상담시간에 지정해주신 전화로 연락드리겠습니다.</span></h3>
			<form name="RegForm" method="post">
				<input type="hidden" name="MemberID" value="<?=$MemberID?>">
				<table class="member_table">
					<tr>
						<th class="TrnTag">성명</th>
						<td><input type="text" name="MemberName" id="MemberName" class="member_common"></td>
					</tr>
					<tr>
						<th class="TrnTag">예약시간</th>
						<td>
							<span></span>
							<input id="RemoteSupportMemberStartDateTime" name="RemoteSupportMemberStartDateTime" type="text" class="member_input_small">
							
							<span style="display:none;"  class="TrnTag">종료시간 : </span>
							<input style="display:none;" id="RemoteSupportMemberEndDateTime" name="RemoteSupportMemberEndDateTime" type="text" class="member_input_small">
						</td>
					</tr>
					<tr>
						<th class="TrnTag">전화번호</th>
						<td>
							<select class="member_select_1" name="RemoteSupportMemberPhone_1">
								<option value="010">010</option>
								<option value="011">011</option>
								<option value="016">016</option>
								<option value="017">017</option>
								<option value="018">018</option>
								<option value="019">019</option>
								<option value="070">070</option>
								<option value="02">02</option>
								<option value="031">031</option>
								<option value="032">032</option>
								<option value="033">033</option>
								<option value="041">041</option>
								<option value="042">042</option>
								<option value="043">043</option>
								<option value="044">044</option>
								<option value="049">049</option>
								<option value="051">051</option>
								<option value="052">052</option>
								<option value="053">053</option>
								<option value="054">054</option>
								<option value="055">055</option>
								<option value="061">061</option>
								<option value="062">062</option>
								<option value="063">063</option>
								<option value="064">064</option>
							</select>
							<span class="member_space">-</span>
							<input type="text" name="RemoteSupportMemberPhone_2" class="member_input_small" maxlength=4 numberonly>
							<span class="member_space">-</span>
							<input type="text" name="RemoteSupportMemberPhone_3" class="member_input_small" maxlength=4 numberonly >
						</td>
					</tr>
					<tr>
						<th  class="TrnTag">신청사유</th>
						<td><textarea class="support_remote_textarea" name="RemoteSupportMemberContent"></textarea></td>
					</tr>
				</table>
			</form>
            <div class="btn_wrap text_center"><a href="javascript:FormSubmit();;" class="button_black_white">원격지원 예약하기</a></div>
        </div>
    </section>
   
</div>

<!-- 라이트 박스 -->
<!--
<div class="light_box_wrap">
    <div class="light_box_area">
        <a href="#"><img src="images/btn_close_white.png" alt="닫기" class="light_box_close"></a>
        <div class="light_box_box">
            <div class="mantoman_write_wrap">
                <div class="mantoman_write_area">
                    <h3 class="caption_underline">원격지원 예약하기</h3>
                    <table class="level_reserve_table">
                        <tr>
                            <th>신청자</th>
                            <td>남궁영우</td>
                        </tr>
                        <tr>
                            <th>예약시간</th>
                            <td>2019.07.22 PM 03:00</td>
                        </tr>
                        <tr>
                            <th>전화번호</th>
                            <td>010-9753-2468</td>
                        </tr>
                        <tr>
                            <th>신청사유</th>
                            <td>프로그램이 실행되지 않아요</td>
                        </tr>
                    </table>
                    <div class="button_wrap flex_justify">
                        <a href="#" class="button_orange_white mantoman">신청하기</a>
                        <a href="#" class="button_br_black mantoman light_box_cancle">취소하기</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
-->
<!-- 라이트 박스 -->

<script src="./js/datetimepicker/jquery.js"></script>
<script src="./js/datetimepicker/build/jquery.datetimepicker.full.min.js"></script>
<script language="javascript">
$('.toggle_navi.six .six').addClass('active');
$('.sub_visual_navi .six').addClass('active');


$('#RemoteSupportMemberStartDateTime').datetimepicker({
    formatTime:'H:i',
    formatDate:'d.m.Y',
	format:'Y-d-m H:i',
    //defaultDate:'8.12.1986', // it's my birthday
    defaultDate:'+03.01.1970', // it's my birthday
    defaultTime:'10:00',
    step:1,
    timepickerScrollbar:false
});


$('#RemoteSupportMemberEndDateTime').datetimepicker({
    formatTime:'H:i',
    formatDate:'d.m.Y',
	format:'Y-d-m',
    //defaultDate:'8.12.1986', // it's my birthday
    defaultDate:'+03.01.1970', // it's my birthday
    defaultTime:'10:00',
    step:1,
    timepickerScrollbar:false
});



function FormSubmit(){

    obj = document.RegForm.MemberName;
    if (obj.value==""){
        alert('성명을 입력하세요.');
        obj.focus();
        return;
    }

    obj = document.RegForm.RemoteSupportMemberStartDateTime;
    if (obj.value==""){
        alert('시간을 선택하세요.');
        obj.focus();
        return;
    }


    obj = document.RegForm.RemoteSupportMemberPhone_1;
    if (obj.value==""){
        alert('전화번호를 입력하세요.');
        obj.focus();
        return;
    }

	obj = document.RegForm.RemoteSupportMemberPhone_2;
    if (obj.value==""){
        alert('전화번호를 입력하세요.');
        obj.focus();
        return;
    }

	obj = document.RegForm.RemoteSupportMemberPhone_3;
    if (obj.value==""){
        alert('전화번호를 입력하세요.');
        obj.focus();
        return;
    }

	obj = document.RegForm.RemoteSupportMemberContent;
    if (obj.value==""){
        alert('사유를 입력하세요.');
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


    if (confirm("상담신청을 하시겠습니까?")){
        document.RegForm.action = "remote_support_action.php";
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





