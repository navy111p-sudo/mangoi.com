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
    <div class="sub_title_common_wrap"><h2 class="sub_title_common TrnTag"><b>마이</b>페이지</h2></div>

    <section class="mypage_wrap">
        <div class="mypage_area">

			<?
			$HideLinkBtn = 0;
			include_once('mypage_student_info_include.php');
			?>



			<?
			$MemberID = $_LINK_MEMBER_ID_;
			
			$Sql = "
					select 
							count(*) as TotalCount
					from Classes A 
						inner join ClassOrders B on A.ClassOrderID=B.ClassOrderID 
					where A.MemberID=$MemberID and (A.ClassState=2 or datediff(A.StartDateTime, now())<=0) and B.ClassProductID=1";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);
			$Row = $Stmt->fetch();
			$Stmt = null;
			$TotalCount = $Row["TotalCount"];
			?>
            <div class="mypage_inner">
                <h3 class="caption_left_br TrnTag">나의 <b>학습이력</b></h3>
                <div class="overflow_table">
                    <table class="mypage_history_table">
                        <col width="6%">
                        <!--<col width="10.5%">-->
                        <col width="10.5%">
						<col width="10.5%">
						<col width="10.5%">
                        <col width="10.5%">
                        <col width="10.5%">
                        <col width="">
						<!-- <col width="10.5%"> -->
                        <tr>
                            <th class="TrnTag">번호</th>
                            <!--<th>과목</th>-->
                            <th class="TrnTag">강사명</th>
                            <th class="TrnTag">학습일</th>
                            <th class="TrnTag">출석상태</th>
							<th class="TrnTag">리포트</th>
							<th class="TrnTag">레슨비디오</th>
                            <th class="TrnTag">리뷰퀴즈</th>
							<!-- <th>인쇄</th> -->
                        </tr>
						<?	
						
						$Sql = "select 
										A.*, 
										B.TeacherName,
										(select count(*) from ClassVideoPlayLogs where ClassID=A.ClassID) as ClassVideoPlayCount
								from Classes A 
									inner join Teachers B on A.TeacherID=B.TeacherID 
								where 
									A.MemberID=$MemberID 
									and (A.ClassState=2 or datediff(A.StartDateTime, now())<=0) 
								order by A.StartDateTime desc";

						$Stmt = $DbConn->prepare($Sql);
						$Stmt->execute();
						$Stmt->setFetchMode(PDO::FETCH_ASSOC);

						$ii=1;
						while($Row = $Stmt->fetch()) {

							$ClassID = $Row["ClassID"];
							$TeacherName = $Row["TeacherName"];
							$StartDateTime = $Row["StartDateTime"];
							$ClassAttendState = $Row["ClassAttendState"];
							$ClassVideoPlayCount = $Row["ClassVideoPlayCount"];
							$BookRegForReason = $Row["BookRegForReason"];


							$StrClassAttendState = "-";
							if ($ClassAttendState==1){//1:출석 2:지각 3:결석 4:학생연기 5:강사연기 6:학생취소 7:강사취소
								$StrClassAttendState = "<trn class=\"TrnTag\">출석</trn>";
							}else if ($ClassAttendState==2){
								$StrClassAttendState = "<trn class=\"TrnTag\">지각</trn>";
							}else if ($ClassAttendState==3){
								$StrClassAttendState = "<trn class=\"TrnTag\">결석</trn>";
							}else if ($ClassAttendState==4){
								$StrClassAttendState = "<trn class=\"TrnTag\">연기</trn>";
							}else if ($ClassAttendState==5){
								$StrClassAttendState = "<trn class=\"TrnTag\">연기</trn>";
							}else if ($ClassAttendState==6){
								$StrClassAttendState = "<trn class=\"TrnTag\">취소</trn>";
							}else if ($ClassAttendState==7){
								$StrClassAttendState = "<trn class=\"TrnTag\">취소</trn>";
							}else if ($ClassAttendState==8){
								$StrClassAttendState = "<trn class=\"TrnTag\">변경</trn>";
							}

							$Sql3 = "select AssmtStudentDailyScoreID from AssmtStudentDailyScores where ClassID=:ClassID";
							$Stmt3 = $DbConn->prepare($Sql3);
							$Stmt3->bindParam(':ClassID', $ClassID);
							$Stmt3->execute();
							$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
							$Row3 = $Stmt3->fetch();
							$Stmt3 = null;
							$AssmtStudentDailyScoreID = $Row3["AssmtStudentDailyScoreID"];
							
							$StrDailyReport = "-";
							if ($AssmtStudentDailyScoreID){
								$StrDailyReport = "<a href=\"javascript:OpenStudentScoreDailyReport(".$ClassID.")\" class=\"study_repeat_btn_2 TrnTag\">리포트</a>";
							}
						?>
						<tr>
                            <td><?=$TotalCount-$ii+1?></td>
                            <!--<td>망고아이 화상강의</td>-->
                            <td><?=$TeacherName?></td>
                            <!--<td>1단원 1챕터</td>
                            <td><a href="#"><img src="images/btn_download_orange.png" class="study_data_btn"></a></td>
                            <td>2019.03.02</td>-->
                            <td><?=str_replace("-",".",substr($StartDateTime,0,10))?></td>
                            <td><?=$StrClassAttendState?></td>
							<td><?=$StrDailyReport?></td>
                            <td><?=$ClassVideoPlayCount?> <trn class="TrnTag">회 시청</trn></td>
							<td class="study_repeat">
                                <?
								$Sql2 = "select 
												A.*,
												ifnull((select avg(MyScore) from BookQuizResultDetails where BookQuizResultID=A.BookQuizResultID),0) as AvgMyScore
										from BookQuizResults A 
										where 
											A.ClassID=$ClassID 
										order by A.QuizStudyNumber asc";

								$Stmt2 = $DbConn->prepare($Sql2);
								$Stmt2->execute();
								$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

								$ii2=1;
								$QuizStudyNumber=0;
								$BookQuizResultState = 1;
								$BookQuizID = 0;
								while($Row2 = $Stmt2->fetch()) {
									$BookQuizResultID = $Row2["BookQuizResultID"];
									$QuizStudyNumber = $Row2["QuizStudyNumber"];
									$BookQuizResultState = $Row2["BookQuizResultState"];
									$AvgMyScore = round($Row2["AvgMyScore"],0);
									$BookQuizID = $Row2["BookQuizID"];

									if ($BookQuizResultState==1){
								?>
								<a href="javascript:OpenStudyQuiz(<?=$BookQuizID?>,<?=$ClassID?>,<?=$QuizStudyNumber?>, <?=$BookRegForReason?>)" class="study_repeat_btn_2 TrnTag">계속응시</a>
								<?
									}else{
								?>
								<a class="study_repeat_btn_3" href="javascript:OpenStudyQuizResult(<?=$BookQuizResultID?>)"><?=$ii2?>회 - <?=$AvgMyScore?>점</a>
								<?
									}
									$ii2++;
								}
								$Stmt2 = null;
								?>
								
								<?if ($BookQuizID!=0){?>
									<?if ($BookQuizResultState==2){?>
									<a href="javascript:OpenStudyQuiz(<?=$BookQuizID?>,<?=$ClassID?>,<?=$QuizStudyNumber+1?>, <?=$BookRegForReason?>)" class="study_repeat_btn_2 TrnTag">재응시</a>
									<?}?>
									
								<?}else{?>
								-
								<?}?>
                            </td>
							<td>
								<?if ($BookQuizID!=0){?>
									<!-- <a href="javascript:OpenStudyQuizPrint(<?=$BookQuizID?>)" class="study_repeat_btn" style="width:70px;background-color:#FE9147;color:#ffffff;border:0px;">인쇄</a> -->
								<?}else{?>
									<!-- - -->
								<?}?>
							</td>
                        </tr>
						<?
						
							$ii++;
						}
						$Stmt = null;
						?>
						<!--
                        <tr>
                            <td>1</td>
                            <td>영어듣기</td>
                            <td>영어</td>
                            <td>1단원 1챕터</td>
                            <td><a href="#"><img src="images/btn_download_orange.png" class="study_data_btn"></a></td>
                            <td>2019.03.02</td>
                            <td>-</td>
                            <td><a href="#" class="study_start">학습전</a></td>
                            <td>
                                -
                            </td>
                        </tr>

                        <tr>
                            <td>1</td>
                            <td>영어듣기</td>
                            <td>영어</td>
                            <td>1단원 1챕터</td>
                            <td><a href="#"><img src="images/btn_download_orange.png" class="study_data_btn"></a></td>
                            <td>2019.03.02</td>
                            <td>-</td>
                            <td><a href="#" class="study_ing">학습중</a></td>
                            <td>
                                -
                            </td>
                        </tr>
						-->

                    </table>
                </div>
                
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
                    <h3 class="caption_underline">수업 변경 요청</h3>
                    <ul class="mantoman_write_list">
                        <li>변경 사유와 연락처를 적어주시면 전화 연락 후 수업일정을 조정합니다.</li>
                    </ul>
                    <table class="mantoman_write_table">
                        <tr>
                            <th>연락처</th>
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
                            <th>변경사유</th>
                            <td><textarea class="mantoman_textarea"></textarea></td>
                        </tr>
                    </table>
                    <div class="button_wrap"><a href="#" class="button_orange_white mantoman">변경 요청하기</a></div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- 라이트 박스 -->


<script>
function OpenStudentScoreDailyReport(ClassID){
	var OpenUrl = "./report_daily.php?ClassID="+ClassID;

	$.colorbox({	
		href:OpenUrl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "900"
		,maxHeight: "900"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}

function OpenStudyQuizPrint(BookQuizID){
	var OpenUrl = "pop_book_quiz_print.php?BookQuizID="+BookQuizID;

	$.colorbox({	
		href:OpenUrl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "1000"
		,maxHeight: "850"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}


function OpenStudyQuiz(BookQuizID, ClassID, QuizStudyNumber, BookRegForReason){
	var OpenUrl = "pop_quiz_study_preset.php?BookQuizID="+BookQuizID+"&ClassID="+ClassID+"&QuizStudyNumber="+QuizStudyNumber;

	$.colorbox({	
		href:OpenUrl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "1000"
		,maxHeight: "850"
		,title:""
		,iframe:true 
		,scrolling:true
		,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 

}

function OpenStudyQuizResult(BookQuizResultID) {
	var OpenUrl = "pop_quiz_study_result.php?BookQuizResultID="+BookQuizResultID;

	$.colorbox({	
		href:OpenUrl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "1000"
		,maxHeight: "850"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}

function OpenStudyQuizErr(){
	alert("준비된 리뷰 퀴즈가 없습니다.");
}

</script>



<script language="javascript">
$('.sub_visual_navi .one').addClass('active');
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





