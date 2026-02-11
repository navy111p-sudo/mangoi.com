<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$OrganType = isset($_REQUEST["OrganType"]) ? $_REQUEST["OrganType"] : "";
$OrganID = isset($_REQUEST["OrganID"]) ? $_REQUEST["OrganID"] : "";
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : ""; 

$TaxMemberInfoID = isset($_REQUEST["TaxMemberInfoID"]) ? $_REQUEST["TaxMemberInfoID"] : "";

$CorpName = isset($_REQUEST["CorpName"]) ? $_REQUEST["CorpName"] : "";
$CorpNum = isset($_REQUEST["CorpNum"]) ? $_REQUEST["CorpNum"] : "";
$TaxRegID = isset($_REQUEST["TaxRegID"]) ? $_REQUEST["TaxRegID"] : "";
$CEOName = isset($_REQUEST["CEOName"]) ? $_REQUEST["CEOName"] : "";
$TEL1 = isset($_REQUEST["TEL1"]) ? $_REQUEST["TEL1"] : "";
$HP1 = isset($_REQUEST["HP1"]) ? $_REQUEST["HP1"] : "";
$Addr = isset($_REQUEST["Addr"]) ? $_REQUEST["Addr"] : "";
$BizType = isset($_REQUEST["BizType"]) ? $_REQUEST["BizType"] : "";
$BizClass = isset($_REQUEST["BizClass"]) ? $_REQUEST["BizClass"] : "";
$ContactName1 = isset($_REQUEST["ContactName1"]) ? $_REQUEST["ContactName1"] : "";
$Email1 = isset($_REQUEST["Email1"]) ? $_REQUEST["Email1"] : "";
$ContactName2 = isset($_REQUEST["ContactName2"]) ? $_REQUEST["ContactName2"] : "";
$Email2 = isset($_REQUEST["Email2"]) ? $_REQUEST["Email2"] : "";



if ($TaxMemberInfoID==""){

	$Sql = " insert into TaxMemberInfos ( ";
		$Sql .= " OrganType, ";
		$Sql .= " OrganID, ";
		$Sql .= " CorpName, ";
		$Sql .= " CorpNum, ";
		$Sql .= " TaxRegID, ";
		$Sql .= " CEOName, ";
		$Sql .= " TEL1, ";
		$Sql .= " HP1, ";
		$Sql .= " Addr, ";
		$Sql .= " BizType, ";
		$Sql .= " BizClass, ";
		$Sql .= " ContactName1, ";
		$Sql .= " Email1, ";
		$Sql .= " ContactName2, ";
		$Sql .= " Email2, ";
		$Sql .= " TaxMemberInfoRegDateTime, ";
		$Sql .= " TaxMemberInfoModiDateTime ";

	$Sql .= " ) values ( ";

		$Sql .= " :OrganType, ";
		$Sql .= " :OrganID, ";
		$Sql .= " :CorpName, ";
		$Sql .= " :CorpNum, ";
		$Sql .= " :TaxRegID, ";
		$Sql .= " :CEOName, ";
		$Sql .= " :TEL1, ";
		$Sql .= " :HP1, ";
		$Sql .= " :Addr, ";
		$Sql .= " :BizType, ";
		$Sql .= " :BizClass, ";
		$Sql .= " :ContactName1, ";
		$Sql .= " :Email1, ";
		$Sql .= " :ContactName2, ";
		$Sql .= " :Email2, ";
		$Sql .= " now(), ";
		$Sql .= " now() ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':OrganType', $OrganType);
	$Stmt->bindParam(':OrganID', $OrganID);
	$Stmt->bindParam(':CorpName', $CorpName);
	$Stmt->bindParam(':CorpNum', $CorpNum);
	$Stmt->bindParam(':TaxRegID', $TaxRegID);
	$Stmt->bindParam(':CEOName', $CEOName);
	$Stmt->bindParam(':TEL1', $TEL1);
	$Stmt->bindParam(':HP1', $HP1);
	$Stmt->bindParam(':Addr', $Addr);
	$Stmt->bindParam(':BizType', $BizType);
	$Stmt->bindParam(':BizClass', $BizClass);
	$Stmt->bindParam(':ContactName1', $ContactName1);
	$Stmt->bindParam(':Email1', $Email1);
	$Stmt->bindParam(':ContactName2', $ContactName2);
	$Stmt->bindParam(':Email2', $Email2);
	$Stmt->execute();
	$Stmt = null;
	
}else{

	$Sql = " update TaxMemberInfos set ";
		$Sql .= " CorpName = :CorpName, ";
		$Sql .= " CorpNum = :CorpNum, ";
		$Sql .= " TaxRegID = :TaxRegID, ";
		$Sql .= " CEOName = :CEOName, ";
		$Sql .= " TEL1 = :TEL1, ";
		$Sql .= " HP1 = :HP1, ";
		$Sql .= " Addr = :Addr, ";
		$Sql .= " BizType = :BizType, ";
		$Sql .= " BizClass = :BizClass, ";
		$Sql .= " ContactName1 = :ContactName1, ";
		$Sql .= " Email1 = :Email1, ";
		$Sql .= " ContactName2 = :ContactName2, ";
		$Sql .= " Email2 = :Email2, ";
		$Sql .= " TaxMemberInfoModiDateTime = now() ";
	$Sql .= " where TaxMemberInfoID = :TaxMemberInfoID ";


	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':CorpName', $CorpName);
	$Stmt->bindParam(':CorpNum', $CorpNum);
	$Stmt->bindParam(':TaxRegID', $TaxRegID);
	$Stmt->bindParam(':CEOName', $CEOName);
	$Stmt->bindParam(':TEL1', $TEL1);
	$Stmt->bindParam(':HP1', $HP1);
	$Stmt->bindParam(':Addr', $Addr);
	$Stmt->bindParam(':BizType', $BizType);
	$Stmt->bindParam(':BizClass', $BizClass);
	$Stmt->bindParam(':ContactName1', $ContactName1);
	$Stmt->bindParam(':Email1', $Email1);
	$Stmt->bindParam(':ContactName2', $ContactName2);
	$Stmt->bindParam(':Email2', $Email2);

	$Stmt->bindParam(':TaxMemberInfoID', $TaxMemberInfoID);
	$Stmt->execute();
	$Stmt = null;


}


if ($err_num != 0){
	include_once('./inc_header.php');
?>
<body>
<script>
alert("<?=$err_msg?>");
//history.go(-1);
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
//parent.$.fn.colorbox.close();
<?if ($OrganType=="1"){?>
	parent.location.href = "center_form.php?CenterID=<?=$OrganID?>&PageTabID=8&<?=$ListParam?>";
<?}else if ($OrganType=="9"){?>
	parent.location.href = "company_form.php?CompanyID=<?=$OrganID?>&PageTabID=8&<?=$ListParam?>";
<?}?>
</script>
<?
}
?>


