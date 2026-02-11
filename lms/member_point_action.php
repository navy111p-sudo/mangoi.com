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

$MemberPointID = isset($_REQUEST["MemberPointID"]) ? $_REQUEST["MemberPointID"] : "";
$RegMemberID = isset($_REQUEST["RegMemberID"]) ? $_REQUEST["RegMemberID"] : "";
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$MemberPointTypeID = isset($_REQUEST["MemberPointTypeID"]) ? $_REQUEST["MemberPointTypeID"] : "";
$MemberPoint = isset($_REQUEST["MemberPoint"]) ? $_REQUEST["MemberPoint"] : "";
$MemberPointName = isset($_REQUEST["MemberPointName"]) ? $_REQUEST["MemberPointName"] : "";
$MemberPointText = isset($_REQUEST["MemberPointText"]) ? $_REQUEST["MemberPointText"] : "";
$DelMemberPoint = isset($_REQUEST["DelMemberPoint"]) ? $_REQUEST["DelMemberPoint"] : "";


if ($DelMemberPoint=="1"){
	$MemberPointState = 0;
}else{
	$MemberPointState = 1;
}

InsertNewTypePoint2($MemberPointTypeID, $RegMemberID, $MemberID, $MemberPointName, $MemberPointText, $MemberPoint);

if($MemberPointTypeID==15) {
	InsertNewTypePoint2($MemberPointTypeID, 0, $RegMemberID, $MemberPointName, $MemberID."에게 ".$MemberPoint." 포인트 전달", "-".$MemberPoint);
}
/*
if ($MemberPointID==""){

	$Sql = " insert into MemberPoints ( ";
		$Sql .= " MemberPointTypeID, ";
		$Sql .= " RegMemberID, ";
		$Sql .= " MemberID, ";
		$Sql .= " MemberPoint, ";
		$Sql .= " MemberPointName, ";
		$Sql .= " MemberPointText, ";
		$Sql .= " MemberPointRegDateTime, ";
		$Sql .= " MemberPointModiDateTime, ";
		$Sql .= " MemberPointState ";
	$Sql .= " ) values ( ";
		$Sql .= " :MemberPointTypeID, ";
		$Sql .= " :RegMemberID, ";
		$Sql .= " :MemberID, ";
		$Sql .= " :MemberPoint, ";
		$Sql .= " :MemberPointName, ";
		$Sql .= " :MemberPointText, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " 1 ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':MemberPointTypeID', $MemberPointTypeID);
	$Stmt->bindParam(':RegMemberID', $RegMemberID);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->bindParam(':MemberPoint', $MemberPoint);
	$Stmt->bindParam(':MemberPointName', $MemberPointName);
	$Stmt->bindParam(':MemberPointText', $MemberPointText);
	$Stmt->execute();
	$Stmt = null;

}else{

	$Sql = " update MemberPoints set ";
		$Sql .= " MemberPointTypeID = :MemberPointTypeID, ";
		$Sql .= " MemberPoint = :MemberPoint, ";
		$Sql .= " MemberPointName = :MemberPointName, ";
		$Sql .= " MemberPointText = :MemberPointText, ";
		$Sql .= " MemberPointModiDateTime = now(), ";
		$Sql .= " MemberPointState = :MemberPointState ";
	$Sql .= " where MemberPointID = :MemberPointID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':MemberPoint', $MemberPoint);
	$Stmt->bindParam(':MemberPointTypeID', $MemberPointTypeID);
	$Stmt->bindParam(':MemberPointName', $MemberPointName);
	$Stmt->bindParam(':MemberPointText', $MemberPointText);
	$Stmt->bindParam(':MemberPointState', $MemberPointState);
	$Stmt->bindParam(':MemberPointID', $MemberPointID);
	$Stmt->execute();
	$Stmt = null;

}

*/
?>


<?
include_once('./inc_common_form_js.php');
?>
<script type="text/javascript" src="js/common.js"></script>
<link rel="stylesheet" type="text/css" href="css/jquery-confirm.min.css" />
<script type="text/javascript" src="js/jquery-confirm.min.js"></script>
<script>
<?if ($MemberPointID==""){?>
	<?if ($_ADMIN_LEVEL_ID_<=4){?>
	
		$.confirm({
			title: '',
			content: "포인트 내용을 저장했습니다.<br>포이트내역에서 확인하실 수 있습니다.",
			buttons: {
				닫기: function () {
					parent.$.fn.colorbox.close();
				},
				포인트내역이동: function () {
					parent.location.href = "member_point_list.php";
				}
			}
		});
	
	<?}else{?>

		$.confirm({
			title: '',
			content: "포인트 내용을 저장했습니다.",
			buttons: {
				닫기: function () {
					parent.$.fn.colorbox.close();
				}
			}
		});

	<?}?>

<?}else{?>
	parent.$.fn.colorbox.close();
<?}?>
</script>
<?
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>

