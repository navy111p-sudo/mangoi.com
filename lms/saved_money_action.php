<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
include_once('./includes/common_meta_tag.php');
include_once('./inc_header.php');
include_once('./inc_common_form_css.php');

$err_num = 0;
$err_msg = "";

$SavedMoneyID = isset($_REQUEST["SavedMoneyID"]) ? $_REQUEST["SavedMoneyID"] : "";
$RegCenterID = isset($_REQUEST["RegCenterID"]) ? $_REQUEST["RegCenterID"] : "";
$CenterID = isset($_REQUEST["CenterID"]) ? $_REQUEST["CenterID"] : "";

$SavedMoney = isset($_REQUEST["SavedMoney"]) ? $_REQUEST["SavedMoney"] : "";

$DelSavedMoney = isset($_REQUEST["DelSavedMoney"]) ? $_REQUEST["DelSavedMoney"] : "";


if ($DelSavedMoney=="1"){
	$SavedMoneyState = 0;
}else{
	$SavedMoneyState = 1;
}

//$SavedMoneyID가 있으면 수정이나 삭제를 없으면 신규 생성을
if ($SavedMoneyID ==""){

	$Sql = " INSERT INTO SavedMoney 
				(SavedMoneyType, CenterID, SavedMoney, SavedMoneyRegDateTime, RegMemberID, SavedMoneyState)
			 VALUES (
				 1, 
				 :CenterID,
				 :SavedMoney,
				 now(),
				 :RegMemberID,
				 :SavedMoneyState
			 )";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':CenterID', $CenterID);
	$Stmt->bindParam(':SavedMoney', $SavedMoney);
	$Stmt->bindParam(':RegMemberID', $_ADMIN_ID_);
	$Stmt->bindParam(':SavedMoneyState', $SavedMoneyState);
			 
	$Stmt->execute();
	$Stmt = null;
			 

}else {
	$Sql = " UPDATE SavedMoney set  
				SavedMoney = :SavedMoney,
				SavedMoneyModiDateTime = now(),
				SavedMoneyState = :SavedMoneyState 
 			WHERE SavedMoneyID = :SavedMoneyID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':SavedMoneyID', $SavedMoneyID);
	$Stmt->bindParam(':SavedMoney', $SavedMoney);
	$Stmt->bindParam(':SavedMoneyState', $SavedMoneyState);
			 
	$Stmt->execute();
	$Stmt = null;
			 
}


?>
<script>
	parent.$.fn.colorbox.close();

</script>
<?
include_once('../includes/dbclose.php');
?>
</body>
</html>