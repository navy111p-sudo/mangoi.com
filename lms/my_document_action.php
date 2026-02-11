<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$ListParam = str_replace("^^", "&", $ListParam);


$DocumentReportID = isset($_REQUEST["DocumentReportID"]) ? $_REQUEST["DocumentReportID"] : "";
$DocumentID = isset($_REQUEST["DocumentID"]) ? $_REQUEST["DocumentID"] : "";
$DocumentReportName = isset($_REQUEST["DocumentReportName"]) ? $_REQUEST["DocumentReportName"] : "";
$DocumentReportContent = isset($_REQUEST["DocumentReportContent"]) ? $_REQUEST["DocumentReportContent"] : "";
$DocumentReportState = isset($_REQUEST["DocumentReportState"]) ? $_REQUEST["DocumentReportState"] : "";


 
if ($DocumentReportID=="0"){

	$MemberID = $_LINK_ADMIN_ID_;

	$Sql = " insert into DocumentReports ( ";
		$Sql .= " DocumentID, ";
		$Sql .= " MemberID, ";
		$Sql .= " DocumentReportName, ";
		$Sql .= " DocumentReportContent, ";
		$Sql .= " DocumentReportRegDateTime, ";
		$Sql .= " DocumentReportModiDateTime, ";
		$Sql .= " DocumentReportState ";
	$Sql .= " ) values ( ";
		$Sql .= " :DocumentID, ";
		$Sql .= " :MemberID, ";
		$Sql .= " :DocumentReportName, ";
		$Sql .= " :DocumentReportContent, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :DocumentReportState ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':DocumentID', $DocumentID);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->bindParam(':DocumentReportName', $DocumentReportName);
	$Stmt->bindParam(':DocumentReportContent', $DocumentReportContent);
	$Stmt->bindParam(':DocumentReportState', $DocumentReportState);
	$Stmt->execute();
	$DocumentReportID = $DbConn->lastInsertId();
	$Stmt = null;



}else{

	$Sql = " update DocumentReports set ";
		$Sql .= " DocumentReportName = :DocumentReportName, ";
		$Sql .= " DocumentReportContent = :DocumentReportContent, ";
		$Sql .= " DocumentReportState = :DocumentReportState, ";
		$Sql .= " DocumentReportModiDateTime = now() ";
	$Sql .= " where DocumentReportID = :DocumentReportID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':DocumentReportName', $DocumentReportName);
	$Stmt->bindParam(':DocumentReportContent', $DocumentReportContent);
	$Stmt->bindParam(':DocumentReportState', $DocumentReportState);
	$Stmt->bindParam(':DocumentReportID', $DocumentReportID);
	$Stmt->execute();
	$Stmt = null;


	$Sql = "delete from DocumentReportMembers where DocumentReportID=:DocumentReportID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':DocumentReportID', $DocumentReportID);
	$Stmt->execute();
	$Stmt = null;

}



$Sql3 = "select A.* from Members A where A.MemberLevelID<=4 and A.MemberID<>".$_LINK_ADMIN_ID_." order by A.MemberName asc";
$Stmt3 = $DbConn->prepare($Sql3);
$Stmt3->execute();
$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
while($Row3 = $Stmt3->fetch()) {

	$CheckMemberID = isset($_REQUEST["CheckMemberID_".$Row3["MemberID"]]) ? $_REQUEST["CheckMemberID_".$Row3["MemberID"]] : "";
	if ($CheckMemberID=="1"){

		$Sql = " insert into DocumentReportMembers ( ";
			$Sql .= " DocumentReportID, ";
			$Sql .= " MemberID, ";
			$Sql .= " DocumentReportMemberRegDateTime, ";
			$Sql .= " DocumentReportMemberModiDateTime, ";
			$Sql .= " DocumentReportMemberState ";
		$Sql .= " ) values ( ";
			$Sql .= " :DocumentReportID, ";
			$Sql .= " :MemberID, ";
			$Sql .= " now(), ";
			$Sql .= " now(), ";
			$Sql .= " 0 ";
		$Sql .= " ) ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':DocumentReportID', $DocumentReportID);
		$Stmt->bindParam(':MemberID', $Row3["MemberID"]);
		$Stmt->execute();
		$Stmt = null;

	}
}
$Stmt3 = null;



if ($err_num != 0){
	include_once('./inc_header.php');
?>
<body>
<script>
alert("<?=$err_msg?>");
history.go(-1);
</script>
<?php
	include_once('./inc_footer.php'); 
?>
</body>
</html>
<?
}

include_once('../includes/dbclose.php');


if ($err_num == 0){
	header("Location: my_document_list.php?$ListParam"); 
	exit;
}
?>