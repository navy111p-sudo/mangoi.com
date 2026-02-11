<?
header('Access-Control-Allow-Origin: *'); 
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');

$ErrNum = 0;
$ErrMsg = "성공";
$AppRegUID = isset($_REQUEST["AppRegUID"]) ? $_REQUEST["AppRegUID"] : "";
$AppID = isset($_REQUEST["AppID"]) ? $_REQUEST["AppID"] : "";
$AppDomain = isset($_REQUEST["AppDomain"]) ? $_REQUEST["AppDomain"] : "";
$AppPath = isset($_REQUEST["AppPath"]) ? $_REQUEST["AppPath"] : "";
$LocalLinkMemberID = isset($_REQUEST["LocalLinkMemberID"]) ? $_REQUEST["LocalLinkMemberID"] : "";
$callback = isset($_REQUEST["callback"]) ? preg_replace('/[^a-z0-9$_]/si', '', $_REQUEST["callback"]) : false;
$ServerPath = $AppDomain.$AppPath."/";

$MemberID = $LocalLinkMemberID;


$PageMyPageHistoryHTML = "";


$Sql = "select 
				A.*, 
				B.TeacherName,
				C.ClassProductID,
				(select count(*) from ClassVideoPlayLogs where ClassID=A.ClassID) as ClassVideoPlayCount,
				(select count(*) from BookQuizResults where ClassID=A.ClassID and BookQuizResultState=2) as ClassQuizCount
		from Classes A 
			inner join Teachers B on A.TeacherID=B.TeacherID 
			inner join ClassOrders C on A.ClassOrderID=C.ClassOrderID 
		where 
			A.MemberID=$MemberID 
			and (A.ClassState=2 or datediff(A.StartDateTime, now())<=0) and C.ClassProductID=1
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
	$ClassQuizCount = $Row["ClassQuizCount"];
	$ClassProductID = $Row["ClassProductID"];
	$BookRegForReason = $Row["BookRegForReason"];

	$StrClassAttendState = "";
	if ($ClassAttendState==1){//1:출석 2:지각 3:결석 4:학생연기 5:강사연기 6:학생취소 7:강사취소
		$StrClassAttendState = "출석";
	}else if ($ClassAttendState==2){
		$StrClassAttendState = "지각";
	}else if ($ClassAttendState==3){
		$StrClassAttendState = "결석";
	}else if ($ClassAttendState==4){
		$StrClassAttendState = "연기";
	}else if ($ClassAttendState==5){
		$StrClassAttendState = "연기";
	}else if ($ClassAttendState==6){
		$StrClassAttendState = "취소";
	}else if ($ClassAttendState==7){
		$StrClassAttendState = "취소";
	}else if ($ClassAttendState==8){
		$StrClassAttendState = "변경";
	}

	$Sql3 = "select AssmtStudentDailyScoreID from AssmtStudentDailyScores where ClassID=:ClassID";
	$Stmt3 = $DbConn->prepare($Sql3);
	$Stmt3->bindParam(':ClassID', $ClassID);
	$Stmt3->execute();
	$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
	$Row3 = $Stmt3->fetch();
	$Stmt3 = null;
	$AssmtStudentDailyScoreID = $Row3["AssmtStudentDailyScoreID"];
	
	
	if ($AssmtStudentDailyScoreID){
		$StrDailyReportLink = "javascript:OpenStudentScoreDailyReport(".$ClassID.", ".$ClassProductID.")";
		$StrDailyReportStyle = "";
	}else{
		$StrDailyReportLink = "javascript:OpenStudentScoreDailyReportErr()";
		$StrDailyReportStyle = "background-color:#cccccc;";
	}

		$PageMyPageHistoryHTML .= "<li>";
		$PageMyPageHistoryHTML .= "	<small>강사명 : <b>".$TeacherName."</b></small>";
		$PageMyPageHistoryHTML .= "	<div class=\"my_study_title\">";
		$PageMyPageHistoryHTML .= "		<h2 class=\"my_study_caption ellipsis\">".str_replace("-",".",substr($StartDateTime,0,10))."</h2>";
		if ($StrClassAttendState!=""){
			$PageMyPageHistoryHTML .= "		<span class=\"my_study_state_before\">".$StrClassAttendState."</span>";
		}
		$PageMyPageHistoryHTML .= "	</div>";
		$PageMyPageHistoryHTML .= "	<div class=\"my_study_date_wrap\">";
		$PageMyPageHistoryHTML .= "		<div class=\"my_study_start\"><trn class='TrnTag'>레스비디오</trn> : <b>".$ClassVideoPlayCount."<trn class='TrnTag'>회 시청</trn></b></div>";
		$PageMyPageHistoryHTML .= "		<div class=\"my_study_finish\"><trn class='TrnTag'>리뷰퀴즈</trn> : <b>".$ClassQuizCount."<trn class='TrnTag'>회 완료</trn></b></div>";
		$PageMyPageHistoryHTML .= "	</div>";
		$PageMyPageHistoryHTML .= "	<div class=\"my_study_btns\">";
		$PageMyPageHistoryHTML .= "		<div class=\"my_study_incorrect\">";
		$PageMyPageHistoryHTML .= "			<h5 class=\"my_study_incorrect_caption TrnTag\">퀴즈결과</h5>";
		$PageMyPageHistoryHTML .= "			<ul class=\"my_study_incorrect_btns\">";


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

				$PageMyPageHistoryHTML .= "				<li><span class=\"incorrect_btn_on TrnTag\" onclick=\"OpenStudyQuiz2(".$BookQuizID.",".$ClassID.",".$QuizStudyNumber.", ".$BookRegForReason.")\">풀기</span></li>";
			
			}else{

				$PageMyPageHistoryHTML .= "				<li><span class=\"incorrect_btn_off\" onclick=\"OpenStudyQuizResult(".$BookQuizResultID.")\">".$ii2."<trn class='TrnTag'>회</trn></span></li>";

			}
			$ii2++;
		}
		$Stmt2 = null;

		if ($BookQuizID!=0){
			if ($BookQuizResultState==2){
				$PageMyPageHistoryHTML .= "				<li><span class=\"incorrect_btn_on TrnTag\" onclick=\"OpenStudyQuiz2(".$BookQuizID.",".$ClassID.",".($QuizStudyNumber+1).", ".$BookRegForReason.")\">재응시</span></li>";
			}
		}

		$PageMyPageHistoryHTML .= "			</ul>";
		$PageMyPageHistoryHTML .= "		</div>";
		$PageMyPageHistoryHTML .= "		<a href=\"".$StrDailyReportLink."\" class=\"my_study_download TrnTag\" style=\"".$StrDailyReportStyle."\"><img src=\"images/icon_bbs_3.png\" class=\"icon\">리포트</a>";
		$PageMyPageHistoryHTML .= "	</div>";
		$PageMyPageHistoryHTML .= "</li>";

	$ii++;
}
$Stmt = null;

$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["PageMyPageHistoryHTML"] = $PageMyPageHistoryHTML;


$ResultValue = my_json_encode($ArrValue);
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>