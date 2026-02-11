<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

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

$ClassOrderID = isset($_REQUEST["ClassOrderID"]) ? $_REQUEST["ClassOrderID"] : "";
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";

$ClassProductID = isset($_REQUEST["ClassProductID"]) ? $_REQUEST["ClassProductID"] : "";
$ClassOrderTimeTypeID = isset($_REQUEST["ClassOrderTimeTypeID"]) ? $_REQUEST["ClassOrderTimeTypeID"] : "";
$ClassOrderWeekCountID = isset($_REQUEST["ClassOrderWeekCountID"]) ? $_REQUEST["ClassOrderWeekCountID"] : "";
$ClassOrderRequestText = isset($_REQUEST["ClassOrderRequestText"]) ? $_REQUEST["ClassOrderRequestText"] : "";
$ClassOrderStartDate = isset($_REQUEST["ClassOrderStartDate"]) ? $_REQUEST["ClassOrderStartDate"] : "";
$ClassOrderEndDate = isset($_REQUEST["ClassOrderEndDate"]) ? $_REQUEST["ClassOrderEndDate"] : "";
$ClassOrderState = isset($_REQUEST["ClassOrderState"]) ? $_REQUEST["ClassOrderState"] : "";
$ClassMemberType = isset($_REQUEST["ClassMemberType"]) ? $_REQUEST["ClassMemberType"] : "";
$ClassProgress = isset($_REQUEST["ClassProgress"]) ? $_REQUEST["ClassProgress"] : "";

$ClassOrderLeveltestApplyTypeID = isset($_REQUEST["ClassOrderLeveltestApplyTypeID"]) ? $_REQUEST["ClassOrderLeveltestApplyTypeID"] : "";
$ClassOrderLeveltestApplyLevel = isset($_REQUEST["ClassOrderLeveltestApplyLevel"]) ? $_REQUEST["ClassOrderLeveltestApplyLevel"] : "";
$ClassOrderLeveltestApplyOverseaTypeID = isset($_REQUEST["ClassOrderLeveltestApplyOverseaTypeID"]) ? $_REQUEST["ClassOrderLeveltestApplyOverseaTypeID"] : "";


if ($ClassOrderStartDate==""){
	$ClassOrderStartDate = null;
}
if ($ClassOrderEndDate==""){
	$ClassOrderEndDate = null;
}

if ($ClassOrderLeveltestApplyLevel==""){
	$ClassOrderLeveltestApplyLevel = 1;
}
if ($ClassOrderLeveltestApplyOverseaTypeID==""){
	$ClassOrderLeveltestApplyOverseaTypeID = 1;
}


if ($ClassProductID==1){//일반수업
	$ClassOrderLeveltestApplyTypeID = 1;
}else if ($ClassProductID==2){//레벨테스트
	$ClassMemberType = 1;
	$ClassOrderTimeTypeID = 2;
	$ClassOrderWeekCountID = 1;
	$ClassOrderLeveltestApplyText = $ClassOrderRequestText;
	$ClassOrderRequestText = "";
}else if ($ClassProductID==3){//체험수업
	$ClassOrderLeveltestApplyTypeID = 1;
	$ClassOrderTimeTypeID = 2;
	$ClassOrderWeekCountID = 1;
}


if ($ClassOrderID==""){

	$Sql = " insert into ClassOrders ( ";
		$Sql .= " ClassProductID, ";

		$Sql .= " ClassOrderLeveltestApplyTypeID, ";
		$Sql .= " ClassOrderLeveltestApplyLevel, ";
		$Sql .= " ClassOrderLeveltestApplyOverseaTypeID, ";
		$Sql .= " ClassOrderLeveltestApplyText, ";

		$Sql .= " ClassOrderTimeTypeID, ";
		$Sql .= " ClassOrderWeekCountID, ";
		$Sql .= " MemberID, ";
		$Sql .= " ClassOrderRequestText, ";
		$Sql .= " ClassOrderState, ";
		$Sql .= " ClassMemberType, ";
		$Sql .= " ClassProgress, ";
		$Sql .= " ClassOrderRegDateTime, ";
		$Sql .= " ClassOrderModiDateTime ";
	$Sql .= " ) values ( ";
		$Sql .= " :ClassProductID, ";

		$Sql .= " :ClassOrderLeveltestApplyTypeID, ";
		$Sql .= " :ClassOrderLeveltestApplyLevel, ";
		$Sql .= " :ClassOrderLeveltestApplyOverseaTypeID, ";
		$Sql .= " :ClassOrderLeveltestApplyText, ";

		$Sql .= " :ClassOrderTimeTypeID, ";
		$Sql .= " :ClassOrderWeekCountID, ";
		$Sql .= " :MemberID, ";
		$Sql .= " :ClassOrderRequestText, ";
		$Sql .= " :ClassOrderState, ";
		$Sql .= " :ClassMemberType, ";
		$Sql .= " :ClassProgress, ";
		$Sql .= " now(), ";
		$Sql .= " now() ";
	$Sql .= " ) ";


	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ClassProductID', $ClassProductID);

	$Stmt->bindParam(':ClassOrderLeveltestApplyTypeID', $ClassOrderLeveltestApplyTypeID);	
	$Stmt->bindParam(':ClassOrderLeveltestApplyLevel', $ClassOrderLeveltestApplyLevel);
	$Stmt->bindParam(':ClassOrderLeveltestApplyOverseaTypeID', $ClassOrderLeveltestApplyOverseaTypeID);
	$Stmt->bindParam(':ClassOrderLeveltestApplyText', $ClassOrderLeveltestApplyText);

	$Stmt->bindParam(':ClassOrderTimeTypeID', $ClassOrderTimeTypeID);
	$Stmt->bindParam(':ClassOrderWeekCountID', $ClassOrderWeekCountID);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->bindParam(':ClassOrderRequestText', $ClassOrderRequestText);
	$Stmt->bindParam(':ClassOrderState', $ClassOrderState);
	$Stmt->bindParam(':ClassMemberType', $ClassMemberType);
	$Stmt->bindParam(':ClassProgress', $ClassProgress);

	$Stmt->execute();
	$Stmt = null;


}else{


	$Sql = " update ClassOrders set ";

		$Sql .= " ClassOrderLeveltestApplyLevel = :ClassOrderLeveltestApplyLevel, ";
		$Sql .= " ClassOrderLeveltestApplyOverseaTypeID = :ClassOrderLeveltestApplyOverseaTypeID, ";
		$Sql .= " ClassOrderLeveltestApplyText = :ClassOrderLeveltestApplyText, ";
		$Sql .= " ClassOrderRequestText = :ClassOrderRequestText, ";

		if ($ClassProductID==1){
			$Sql .= " ClassOrderStartDate = :ClassOrderStartDate, ";
			$Sql .= " ClassOrderEndDate = :ClassOrderEndDate, ";
		}

		$Sql .= " ClassProgress = :ClassProgress, ";
		$Sql .= " ClassOrderState = :ClassOrderState, ";
		$Sql .= " ClassOrderModiDateTime = now() ";
	$Sql .= " where ClassOrderID = :ClassOrderID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ClassOrderLeveltestApplyLevel', $ClassOrderLeveltestApplyLevel);
	$Stmt->bindParam(':ClassOrderLeveltestApplyOverseaTypeID', $ClassOrderLeveltestApplyOverseaTypeID);
	$Stmt->bindParam(':ClassOrderLeveltestApplyText', $ClassOrderLeveltestApplyText);
	$Stmt->bindParam(':ClassOrderRequestText', $ClassOrderRequestText);
	if ($ClassProductID==1){
		$Stmt->bindParam(':ClassOrderStartDate', $ClassOrderStartDate);
		$Stmt->bindParam(':ClassOrderEndDate', $ClassOrderEndDate);
	}
	$Stmt->bindParam(':ClassProgress', $ClassProgress);
	$Stmt->bindParam(':ClassOrderState', $ClassOrderState);
	$Stmt->bindParam(':ClassOrderID', $ClassOrderID);
	$Stmt->execute();
	$Stmt = null;

	if ($ClassProductID==1){
		//종료일 로그 남기기 =======================================
		$ClassOrderEndDateLogFileQueryNum = 1;
		$Sql_EndDateLog = " insert into ClassOrderEndDateLogs ( ";
			$Sql_EndDateLog .= " ClassOrderID, ";
			$Sql_EndDateLog .= " ClassOrderEndDateLogType, ";
			$Sql_EndDateLog .= " ClassOrderEndDate, ";
			$Sql_EndDateLog .= " ClassOrderEndDateLogFileName, ";
			$Sql_EndDateLog .= " ClassOrderEndDateLogFileQueryNum, ";
			$Sql_EndDateLog .= " ClassOrderEndDateLogRegDateTime ";
		$Sql_EndDateLog .= " ) values ( ";
			$Sql_EndDateLog .= " :ClassOrderID, ";
			$Sql_EndDateLog .= " '강의 신청에 의한 종료일 변경', ";
			$Sql_EndDateLog .= " :ClassOrderEndDate, ";
			$Sql_EndDateLog .= " :ClassOrderEndDateLogFileName, ";
			$Sql_EndDateLog .= " :ClassOrderEndDateLogFileQueryNum, ";
			$Sql_EndDateLog .= " now() ";
		$Sql_EndDateLog .= " ) ";
		$Stmt_EndDateLog = $DbConn->prepare($Sql_EndDateLog);
		$Stmt_EndDateLog->bindParam(':ClassOrderID', $ClassOrderID);
		$Stmt_EndDateLog->bindParam(':ClassOrderEndDate', $ClassOrderEndDate);
		$Stmt_EndDateLog->bindParam(':ClassOrderEndDateLogFileName', $ClassOrderEndDateLogFileName);
		$Stmt_EndDateLog->bindParam(':ClassOrderEndDateLogFileQueryNum', $ClassOrderEndDateLogFileQueryNum);
		$Stmt_EndDateLog->execute();
		$Stmt_EndDateLog = null;
		//종료일 로그 남기기 =======================================
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
<?if ($ClassOrderID==""){?>
	<?if ($ClassProductID==2){?>
		$.confirm({
			title: '',
			content: "저장했습니다.<br>레벨테스트 내역에서 확인하실 수 있습니다.",
			buttons: {
				닫기: function () {
					//parent.$.fn.colorbox.close();
					parent.location.reload();
				},
				레벨테스트목록이동: function () {
					parent.location.href = "leveltest_apply_list.php";
				}
			}
		});

	<?}else{?>
		$.confirm({
			title: '',
			content: "저장했습니다.<br>수강신청 내역에서 확인하실 수 있습니다.",
			buttons: {
				닫기: function () {
					//parent.$.fn.colorbox.close();
					parent.location.reload();
				},
				수강신청목록이동: function () {
					parent.location.href = "class_order_list.php";
				}
			}
		});
	<?}?>
<?}else{?>
	//parent.$.fn.colorbox.close();
	parent.location.reload();
<?}?>
</script>
<?
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>

 