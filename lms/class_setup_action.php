<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
?>
<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="ko"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="ko"> <!--<![endif]-->
<head>
<?
include_once('./includes/common_meta_tag.php');
include_once('./inc_header.php');
include_once('./inc_common_form_css.php');
?>
<link rel="stylesheet" type="text/css" href="css/common.css" />
</head>
</body>
<?

$err_num = 0;
$err_msg = "";


$ClassID = isset($_REQUEST["ClassID"]) ? $_REQUEST["ClassID"] : "";

$ClassLinkType = isset($_REQUEST["ClassLinkType"]) ? $_REQUEST["ClassLinkType"] : "";
$CommonCiTelephoneTeacher = isset($_REQUEST["CommonCiTelephoneTeacher"]) ? $_REQUEST["CommonCiTelephoneTeacher"] : "";
$CommonCiTelephoneStudent = isset($_REQUEST["CommonCiTelephoneStudent"]) ? $_REQUEST["CommonCiTelephoneStudent"] : "";

$ClassState = isset($_REQUEST["ClassState"]) ? $_REQUEST["ClassState"] : "";
$ClassAttendState = isset($_REQUEST["ClassAttendState"]) ? $_REQUEST["ClassAttendState"] : "";

$BookSystemType = isset($_REQUEST["BookSystemType"]) ? $_REQUEST["BookSystemType"] : "";
$BookVideoID = isset($_REQUEST["BookVideoID"]) ? $_REQUEST["BookVideoID"] : "";
$BookQuizID = isset($_REQUEST["BookQuizID"]) ? $_REQUEST["BookQuizID"] : "";
$BookScanID = isset($_REQUEST["BookScanID"]) ? $_REQUEST["BookScanID"] : "";

$ClassLevel = isset($_REQUEST["ClassLevel"]) ? $_REQUEST["ClassLevel"] : "";
$BookRegForReason = isset($_REQUEST["BookRegForReason"]) ? $_REQUEST["BookRegForReason"] : "";
// webook variables
$BookSystemType = isset($_REQUEST["BookSystemType"]) ? $_REQUEST["BookSystemType"] : ""; // 0 : 망고아이, 1 : 웹북시스템
$unit = isset($_REQUEST["unit"]) ? $_REQUEST["unit"] : "";

$BookWebookUnitID = isset($_REQUEST["BookWebookUnitID"]) ? $_REQUEST["BookWebookUnitID"] : "";
$BookWebookUnitName = isset($_REQUEST["BookWebookUnitName"]) ? $_REQUEST["BookWebookUnitName"] : "";

if ($BookWebookUnitID!="" && $unit==""){
	$unit = $BookWebookUnitID;
}

if($BookRegForReason=="") {
	$BookRegForReason = 0;
}


$Sql = " update Classes set ";
	$Sql .= " ClassLinkType = :ClassLinkType, ";
	$Sql .= " ClassLevel = :ClassLevel, ";
	$Sql .= " CommonCiTelephoneTeacher = :CommonCiTelephoneTeacher, ";
	$Sql .= " CommonCiTelephoneStudent = :CommonCiTelephoneStudent, ";
	$Sql .= " ClassState = :ClassState, ";
	$Sql .= " ClassAttendState = :ClassAttendState, ";
	$Sql .= " BookSystemType = :BookSystemType, ";
	$Sql .= " BookVideoID = :BookVideoID, ";
	$Sql .= " BookQuizID = :BookQuizID, ";
	if($BookSystemType==0) {
		$Sql .= " BookScanID = :BookScanID, ";
	} else {
		$Sql .= " BookWebookUnitID = :BookWebookUnitID, ";
		$Sql .= " BookWebookUnitName = :BookWebookUnitName, ";
	}
	$Sql .= " BookRegForReason = :BookRegForReason, ";
	$Sql .= " ClassModiDateTime = now() ";
$Sql .= " where ClassID = :ClassID ";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassLinkType', $ClassLinkType);
$Stmt->bindParam(':ClassLevel', $ClassLevel);
$Stmt->bindParam(':CommonCiTelephoneTeacher', $CommonCiTelephoneTeacher);
$Stmt->bindParam(':CommonCiTelephoneStudent', $CommonCiTelephoneStudent);
$Stmt->bindParam(':ClassState', $ClassState);
$Stmt->bindParam(':BookSystemType', $BookSystemType);
$Stmt->bindParam(':BookVideoID', $BookVideoID);
$Stmt->bindParam(':BookQuizID', $BookQuizID);
if($BookSystemType==0) {
	$Stmt->bindParam(':BookScanID', $BookScanID);
} else {
	$Stmt->bindParam(':BookWebookUnitID', $unit);
	$Stmt->bindParam(':BookWebookUnitName', $BookWebookUnitName);
}
$Stmt->bindParam(':BookRegForReason', $BookRegForReason);
$Stmt->bindParam(':ClassAttendState', $ClassAttendState);
$Stmt->bindParam(':ClassID', $ClassID);
$Stmt->execute();
$Stmt = null;


// 만약 연속 결석 횟수가 3회이면 알림톡 보낸다.
// classAttendState : 3은 결석을 의미
if($ClassAttendState==3){

	$Sql = "SELECT count(*) AS AbsentCount FROM Classes WHERE MemberID = :MemberID AND ClassAttendState = 3 AND date_add(StartDateTime,interval 21 day) >= NOW();";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$AbsentCount = $Row["AbsentCount"];


	if ($AbsentCount >= 3){
		// 3회 이상 연속 결석시 완료 알림톡 보내기
		$RowSms = GetMemberSmsInfo($MemberID);

		$MemberName = $RowSms["MemberName"];
		$DecMemberPhone1 = $RowSms["DecMemberPhone1"];
			
		$msg = "$MemberName 학생이 3회이상 결석중입니다. 수업 참여 여부 확인 부탁드립니다. 감사합니다.";
			
		$tmplId="mangoi_005";  //카카오 알림톡 템플릿 아이디(문자 메시지를 바꿀려면 비즈엠에서 템플릿 등록하고 그 메시지를 위에 복사후 템플릿id도 바꿔야 함)
		
		SendAlimtalk($DecMemberPhone1, $msg,$tmplId);
	}	

}

?>


<?
include_once('./inc_common_form_js.php');
?>
<script type="text/javascript" src="js/common.js"></script>
<link rel="stylesheet" type="text/css" href="css/jquery-confirm.min.css" />
<script type="text/javascript" src="js/jquery-confirm.min.js"></script>
<script>
parent.location.reload();
</script>
<?
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>

