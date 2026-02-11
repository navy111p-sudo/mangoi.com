<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');


$err_num = 0;
$err_msg = "";

$ProductInterviewID = isset($_REQUEST["ProductInterviewID"]) ? $_REQUEST["ProductInterviewID"] : "";
$CenterID = isset($_REQUEST["CenterID"]) ? $_REQUEST["CenterID"] : "";
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";


$Sql = "select count(*) as RowCount from MemberProductInterviews where ProductInterviewID=:ProductInterviewID and MemberID=:MemberID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ProductInterviewID', $ProductInterviewID);
$Stmt->bindParam(':MemberID', $MemberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$RowCount = $Row["RowCount"];


if ($RowCount==0){

	$Sql = "select ifnull(Max(MemberProductInterviewOrder),0) as MemberProductInterviewOrder from MemberProductInterviews where ProductInterviewID=:ProductInterviewID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ProductInterviewID', $ProductInterviewID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$MemberProductInterviewOrder = $Row["MemberProductInterviewOrder"]+1;

	$Sql = " insert into MemberProductInterviews ( ";
		$Sql .= " ProductInterviewID, ";
		$Sql .= " MemberID, ";
		$Sql .= " MemberProductInterviewState, ";
		$Sql .= " MemberProductInterviewOrder, ";
		$Sql .= " MemberProductInterviewModiDateTime ";
	$Sql .= " ) values ( ";
		$Sql .= " :ProductInterviewID, ";
		$Sql .= " :MemberID, ";
		$Sql .= " 1, ";
		$Sql .= " :MemberProductInterviewOrder, ";
		$Sql .= " now() ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ProductInterviewID', $ProductInterviewID);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->bindParam(':MemberProductInterviewOrder', $MemberProductInterviewOrder);
	$Stmt->execute();
	$Stmt = null;

}else{

	$Sql = " update MemberProductInterviews set ";
		$Sql .= " MemberProductInterviewState=1, ";
		$Sql .= " MemberProductInterviewModiDateTime=now() ";
	$Sql .= " where MemberID=$MemberID and ProductInterviewID=:ProductInterviewID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ProductInterviewID', $ProductInterviewID);
	$Stmt->execute();
	$Stmt = null;
}


header("Location: pop_member_select_form.php?ProductInterviewID=$ProductInterviewID&CenterID=$CenterID"); 
exit;
?>