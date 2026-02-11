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
?>


<div class="sub_wrap">       
    <div class="sub_title_common_wrap"><h2 class="sub_title_common"><b>마이</b>페이지</h2></div>

    <section class="mypage_wrap">
        <div class="mypage_area">

            
			<?
			$HideLinkBtn = 0;
			include_once('mypage_student_info_include.php');
			?>


            <!-- 나의 포인트 -->
            <div class="mypage_inner">
                <h3 class="caption_left_br TrnTag">나의 <b>포인트 내역</b><span class="text">※ 포인트는 <b>1,000점 이상일 경우 현금</b>처럼 사용할 수 있습니다.</span></h3>
                <table class="mypage_point_table">
                    <col width="21%">
                    <col width="">
                    <col width="21%">
                    <col width="21%">
                    <tr>
                        <th class="TrnTag">날짜</th>
                        <th class="TrnTag">포인트내역</th>
                        <th class="TrnTag">적립포인트</th>
                        <th class="TrnTag">누적포인트</th>
                    </tr>
					<?
					$Sql = "
							select 
								A.*,
								(select sum(AA.MemberPoint) from MemberPoints AA where AA.MemberID=A.MemberID and AA.MemberPointState=1 and AA.MemberPointID<=A.MemberPointID) as TotalMemberPoint 
							from MemberPoints A
								inner join Members B on A.MemberID=B.MemberID 
							where A.MemberID=".$_LINK_MEMBER_ID_." and A.MemberPointState=1  
							order by A.MemberPointRegDateTime desc";// limit $StartRowNum, $PageListNum";
					$Stmt = $DbConn->prepare($Sql);
					$Stmt->execute();
					$Stmt->setFetchMode(PDO::FETCH_ASSOC);					

					$ListCount = 1;
					while($Row = $Stmt->fetch()) {
						//$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;


						$MemberPointID = $Row["MemberPointID"];
						$MemberID = $Row["MemberID"];
						$RegMemberID = $Row["RegMemberID"];
						$MemberPointName = $Row["MemberPointName"];
						$MemberPointText = $Row["MemberPointText"];
						$MemberPoint = $Row["MemberPoint"];
						$MemberPointRegDateTime = $Row["MemberPointRegDateTime"];
						$TotalMemberPoint = $Row["TotalMemberPoint"];

					?>
					
					<tr>
                        <td><?=str_replace("-",".",substr($MemberPointRegDateTime,0,10))?></td>
                        <td><?=$MemberPointName?></td>
                        <?if ($MemberPoint<0){?>
						<td class="point_minus">
							<?=number_format($MemberPoint,0)?>P
						</td>
						<?}else{?>
						<td>
							<?=number_format($MemberPoint,0)?>P
						</td>
						<?}?>
						<td><?=number_format($TotalMemberPoint,0)?>P</td>
                    </tr>

					<?
						$ListCount ++;
					}
					$Stmt = null;
					?>
                    <!--
					<tr>
                        <td>2019.06.28</td>
                        <td>주5회 결제(300,000)</td>
                        <td class="point_minus">- 3,000P</td>
                        <td>0P</td>
                    </tr>
                    <tr>
                        <td>2019.05.28</td>
                        <td>주5회 결제(300,000)</td>
                        <td>3,000P</td>
                        <td>3,000P</td>
                    </tr>
					-->


                </table>
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

        </div>
    </section>

</div>

<!-- 사진 변경 라이트 박스 -->
<div class="light_box_wrap photo_change">
    <div class="light_box_area">
        <a href="#"><img src="images/btn_close_white.png" alt="닫기" class="light_box_close"></a>
        <div class="light_box_box">
            <div class="phto_change_wrap">
                <div class="phto_change_area">
                    <h3 class="caption_underline TrnTag">사진 업로드</h3>
                    <div class="photo_change_box" style="background-image:url(images/no_photo.png)"></div>
                    <input type="file" class="photo_change_file">
                    <div class="button_wrap"><a href="#" class="button_orange_white photo_change TrnTag">업로드</a></div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- 사진 변경 라이트 박스 -->

<!-- 변경 사유 라이트 박스 -->
<div class="light_box_wrap">
    <div class="light_box_area">
        <a href="#"><img src="images/btn_close_white.png" alt="닫기" class="light_box_close"></a>
        <div class="light_box_box">
            <div class="mantoman_write_wrap">
                <div class="mantoman_write_area">
                    <h3 class="caption_underline TrnTag">수업 변경 요청</h3>
                    <ul class="mantoman_write_list">
                        <li class="TrnTag">변경 사유와 연락처를 적어주시면 전화 연락 후 수업일정을 조정합니다.</li>
                    </ul>
                    <table class="mantoman_write_table">
                        <tr>
                            <th class="TrnTag">연락처</th>
                            <td>
                                <select name="MemberPhone1_1" class="member_select_1 change">
                                    <option value="010">010</option>
                                    <option value="011">011</option>
                                </select>
                                <span class="member_space">-</span>
                                <input type="text" name="MemberPhone1_2" class="member_input_small change">
                                <span class="member_space">-</span>
                                <input type="text" name="MemberPhone1_3" class="member_input_small change">
                            </td>
                        </tr>
                        <tr>
                            <th class="TrnTag">변경사유</th>
                            <td><textarea class="mantoman_textarea"></textarea></td>
                        </tr>
                    </table>
                    <div class="button_wrap"><a href="#" class="button_orange_white mantoman TrnTag">변경 요청하기</a></div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- 라이트 박스 -->


<script language="javascript">
$('.sub_visual_navi .one').addClass('active');

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





