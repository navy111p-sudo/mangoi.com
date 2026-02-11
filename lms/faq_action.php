<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$ListParam = str_replace("^^", "&", $ListParam);

$MemberID = $_LINK_ADMIN_ID_;

$FaqID = isset($_REQUEST["FaqID"]) ? $_REQUEST["FaqID"] : "";
$FaqTitle = isset($_REQUEST["FaqTitle"]) ? $_REQUEST["FaqTitle"] : "";
$FaqContent = isset($_REQUEST["FaqContent"]) ? $_REQUEST["FaqContent"] : "";
$FaqState = isset($_REQUEST["FaqState"]) ? $_REQUEST["FaqState"] : "";
$FaqView = isset($_REQUEST["FaqView"]) ? $_REQUEST["FaqView"] : "";

if ($FaqView!="1"){
	$FaqView = 0;
}

if ($FaqState!="1"){
	$FaqState = 2;
}


if ($FaqID==""){

	$Sql = "select ifnull(Max(FaqOrder),0) as FaqOrder from Faqs";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$FaqOrder = $Row["FaqOrder"]+1;

	$Sql = " insert into Faqs ( ";
		$Sql .= " MemberID, ";
		$Sql .= " FaqTitle, ";
		$Sql .= " FaqContent, ";
		$Sql .= " FaqRegDateTime, ";
		$Sql .= " FaqModiDateTime, ";
		$Sql .= " FaqState, ";
		$Sql .= " FaqView, ";
		$Sql .= " FaqOrder ";
	$Sql .= " ) values ( ";
		$Sql .= " :MemberID, ";
		$Sql .= " :FaqTitle, ";
		$Sql .= " :FaqContent, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :FaqState, ";
		$Sql .= " :FaqView, ";
		$Sql .= " :FaqOrder ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->bindParam(':FaqTitle', $FaqTitle);
	$Stmt->bindParam(':FaqContent', $FaqContent);
	$Stmt->bindParam(':FaqState', $FaqState);
	$Stmt->bindParam(':FaqView', $FaqView);
	$Stmt->bindParam(':FaqOrder', $FaqOrder);
	$Stmt->execute();
	$FaqID = $DbConn->lastInsertId();
	$Stmt = null;


}else{

	$Sql = " update Faqs set ";
		$Sql .= " FaqTitle = :FaqTitle, ";
		$Sql .= " FaqContent = :FaqContent, ";
		$Sql .= " FaqModiDateTime = now(), ";
		$Sql .= " FaqState = :FaqState, ";
		$Sql .= " FaqView = :FaqView ";
	$Sql .= " where FaqID = :FaqID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':FaqTitle', $FaqTitle);
	$Stmt->bindParam(':FaqContent', $FaqContent);
	$Stmt->bindParam(':FaqState', $FaqState);
	$Stmt->bindParam(':FaqView', $FaqView);
	$Stmt->bindParam(':FaqID', $FaqID);
	$Stmt->execute();
	$Stmt = null;

}


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
?>
<script>
parent.$.fn.colorbox.close();
</script>
<?
}
?>


