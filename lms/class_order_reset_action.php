<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

// -----------------------------------------------------------------
// 삭제 권한을 갖는지 여부 확인  // 추가
// 세션에 저장돼 있는 로그인 ID, MemberID 변수명은
$loginID   = isset($_LINK_ADMIN_LOGIN_ID_)  ? $_LINK_ADMIN_LOGIN_ID_  : '';
$loginMID  = isset($_LINK_ADMIN_MEMBER_ID_) ? $_LINK_ADMIN_MEMBER_ID_ : 0;
$canDelete = ($loginID === '장지웅1'
    || $loginID === 'maiskd'
//    || $loginID === 'master'
    || $loginMID == 22055
//    || $loginMID == 1
//    || $loginID === '정우영1'
//    || $loginID === 22050
);
// -----------------------------------------------------------------

$ClassOrderEndDateLogFileName = $_SERVER["PHP_SELF"];
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

$ClassProductID = isset($_REQUEST["ClassProductID"]) ? $_REQUEST["ClassProductID"] : "";
$ClassOrderID = isset($_REQUEST["ClassOrderID"]) ? $_REQUEST["ClassOrderID"] : "";
$ClassMemberTypeGroupID = isset($_REQUEST["ClassMemberTypeGroupID"]) ? $_REQUEST["ClassMemberTypeGroupID"] : "";
$ClassOrderEndDate = isset($_REQUEST["ClassOrderEndDate"]) ? $_REQUEST["ClassOrderEndDate"] : "";
$ClassOrderState = isset($_REQUEST["ClassOrderState"]) ? $_REQUEST["ClassOrderState"] : "";
$IframeMode = isset($_REQUEST["IframeMode"]) ? $_REQUEST["IframeMode"] : "0";


// -----------------------------------------------------------------
// 삭제 권한 없는 계정이 삭제(0) 시도 시 차단  // 검증
if ($ClassOrderState == "0" && !$canDelete) {
    echo "<script>alert('삭제 기능 사용이 제한되었습니다. 필요 시, 관리자에게 문의해 주시기 바랍니다.');history.back();</script>";
    include_once('../includes/dbclose.php');
    exit;
}
// -----------------------------------------------------------------


$Sql = " update ClassOrders set ";
	if ($ClassProductID==1 && $ClassOrderEndDate!=""){
		$Sql .= " ClassOrderEndDate = :ClassOrderEndDate, ";
	}

	$Sql .= " ClassOrderState = :ClassOrderState, ";
	$Sql .= " ClassOrderModiDateTime = now() ";

if ($ClassMemberTypeGroupID!="0"){
	$Sql .= " where ClassMemberTypeGroupID = :ClassMemberTypeGroupID ";
}else{
	$Sql .= " where ClassOrderID = :ClassOrderID ";
}


$Stmt = $DbConn->prepare($Sql);
if ($ClassProductID==1 && $ClassOrderEndDate!=""){
	$Stmt->bindParam(':ClassOrderEndDate', $ClassOrderEndDate);
}
$Stmt->bindParam(':ClassOrderState', $ClassOrderState);

if ($ClassMemberTypeGroupID!="0"){
	$Stmt->bindParam(':ClassMemberTypeGroupID', $ClassMemberTypeGroupID);
}else{
	$Stmt->bindParam(':ClassOrderID', $ClassOrderID);
}
$Stmt->execute();
$Stmt = null;



if ($ClassProductID==1){
	//종료일 로그 남기기 =======================================
	$ClassOrderEndDateLogFileQueryNum = 1;
	$Sql_EndDateLog = " insert into ClassOrderEndDateLogs ( ";
		$Sql_EndDateLog .= " ClassOrderID, ";
		if ($ClassOrderEndDate!=""){
			$Sql_EndDateLog .= " ClassOrderEndDate, ";
		}
		$Sql_EndDateLog .= " ClassOrderEndDateLogFileName, ";
		$Sql_EndDateLog .= " ClassOrderEndDateLogFileQueryNum, ";
		$Sql_EndDateLog .= " ClassOrderEndDateLogRegDateTime ";
	$Sql_EndDateLog .= " ) values ( ";
		$Sql_EndDateLog .= " :ClassOrderID, ";
		if ($ClassOrderEndDate!=""){
			$Sql_EndDateLog .= " :ClassOrderEndDate, ";
		}
		$Sql_EndDateLog .= " :ClassOrderEndDateLogFileName, ";
		$Sql_EndDateLog .= " :ClassOrderEndDateLogFileQueryNum, ";
		$Sql_EndDateLog .= " now() ";
	$Sql_EndDateLog .= " ) ";
	$Stmt_EndDateLog = $DbConn->prepare($Sql_EndDateLog);
	$Stmt_EndDateLog->bindParam(':ClassOrderID', $ClassOrderID);
	if ($ClassOrderEndDate!=""){
		$Stmt_EndDateLog->bindParam(':ClassOrderEndDate', $ClassOrderEndDate);
	}
	$Stmt_EndDateLog->bindParam(':ClassOrderEndDateLogFileName', $ClassOrderEndDateLogFileName);
	$Stmt_EndDateLog->bindParam(':ClassOrderEndDateLogFileQueryNum', $ClassOrderEndDateLogFileQueryNum);
	$Stmt_EndDateLog->execute();
	$Stmt_EndDateLog = null;
	//종료일 로그 남기기 =======================================
}
?>


<?
include_once('./inc_common_form_js.php');
?>
<script type="text/javascript" src="js/common.js"></script>
<link rel="stylesheet" type="text/css" href="css/jquery-confirm.min.css" />
<script type="text/javascript" src="js/jquery-confirm.min.js"></script>
<script>
if(<?=$IframeMode?>==1) {
	parent.window.close();
} else {
	parent.$.fn.colorbox.close();
	//parent.location.reload();
}
</script>
<?
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>

