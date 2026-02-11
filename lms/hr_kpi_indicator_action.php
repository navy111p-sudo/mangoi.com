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

$Hr_KpiIndicatorID = isset($_REQUEST["Hr_KpiIndicatorID"]) ? $_REQUEST["Hr_KpiIndicatorID"] : "";
$Hr_KpiIndicatorName = isset($_REQUEST["Hr_KpiIndicatorName"]) ? $_REQUEST["Hr_KpiIndicatorName"] : "";
$Hr_KpiIndicatorDefine = isset($_REQUEST["Hr_KpiIndicatorDefine"]) ? $_REQUEST["Hr_KpiIndicatorDefine"] : "";
$Hr_KpiIndicatorFormula = isset($_REQUEST["Hr_KpiIndicatorFormula"]) ? $_REQUEST["Hr_KpiIndicatorFormula"] : "";
$Hr_KpiIndicatorMeasure = isset($_REQUEST["Hr_KpiIndicatorMeasure"]) ? $_REQUEST["Hr_KpiIndicatorMeasure"] : "";
$Hr_KpiIndicatorSource = isset($_REQUEST["Hr_KpiIndicatorSource"]) ? $_REQUEST["Hr_KpiIndicatorSource"] : "";
$Hr_KpiIndicatorPartName = isset($_REQUEST["Hr_KpiIndicatorPartName"]) ? $_REQUEST["Hr_KpiIndicatorPartName"] : "";
$Hr_KpiIndicatorUnitID = isset($_REQUEST["Hr_KpiIndicatorUnitID"]) ? $_REQUEST["Hr_KpiIndicatorUnitID"] : "";
$Hr_KpiIndicatorState = isset($_REQUEST["Hr_KpiIndicatorState"]) ? $_REQUEST["Hr_KpiIndicatorState"] : "";



if ($Hr_KpiIndicatorID==""){


	$Sql = "select ifnull(Max(Hr_KpiIndicatorOrder),0) as Hr_KpiIndicatorOrder from Hr_KpiIndicators";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$Hr_KpiIndicatorOrder = $Row["Hr_KpiIndicatorOrder"]+1;

	$Sql = " insert into Hr_KpiIndicators ( ";
		$Sql .= " Hr_KpiIndicatorName, ";
		$Sql .= " Hr_KpiIndicatorDefine, ";
		$Sql .= " Hr_KpiIndicatorFormula, ";
		$Sql .= " Hr_KpiIndicatorMeasure, ";
		$Sql .= " Hr_KpiIndicatorSource, ";
		$Sql .= " Hr_KpiIndicatorPartName, ";
		$Sql .= " Hr_KpiIndicatorUnitID, ";
		$Sql .= " Hr_KpiIndicatorOrder, ";
		$Sql .= " Hr_KpiIndicatorRegDateTime, ";
		$Sql .= " Hr_KpiIndicatorModiDateTime, ";
		$Sql .= " Hr_KpiIndicatorState ";
	$Sql .= " ) values ( ";
		$Sql .= " :Hr_KpiIndicatorName, ";
		$Sql .= " :Hr_KpiIndicatorDefine, ";
		$Sql .= " :Hr_KpiIndicatorFormula, ";
		$Sql .= " :Hr_KpiIndicatorMeasure, ";
		$Sql .= " :Hr_KpiIndicatorSource, ";
		$Sql .= " :Hr_KpiIndicatorPartName, ";
		$Sql .= " :Hr_KpiIndicatorUnitID, ";
		$Sql .= " :Hr_KpiIndicatorOrder, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :Hr_KpiIndicatorState ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':Hr_KpiIndicatorName', $Hr_KpiIndicatorName);
	$Stmt->bindParam(':Hr_KpiIndicatorDefine', $Hr_KpiIndicatorDefine);
	$Stmt->bindParam(':Hr_KpiIndicatorFormula', $Hr_KpiIndicatorFormula);
	$Stmt->bindParam(':Hr_KpiIndicatorMeasure', $Hr_KpiIndicatorMeasure);
	$Stmt->bindParam(':Hr_KpiIndicatorSource', $Hr_KpiIndicatorSource);
	$Stmt->bindParam(':Hr_KpiIndicatorPartName', $Hr_KpiIndicatorPartName);
	$Stmt->bindParam(':Hr_KpiIndicatorUnitID', $Hr_KpiIndicatorUnitID);
	$Stmt->bindParam(':Hr_KpiIndicatorOrder', $Hr_KpiIndicatorOrder);
	$Stmt->bindParam(':Hr_KpiIndicatorState', $Hr_KpiIndicatorState);
	$Stmt->execute();
	$Hr_KpiIndicatorID = $DbConn->lastInsertId();
	$Stmt = null;



}else{

	$Sql = " update Hr_KpiIndicators set ";
		$Sql .= " Hr_KpiIndicatorName = :Hr_KpiIndicatorName, ";
		$Sql .= " Hr_KpiIndicatorDefine = :Hr_KpiIndicatorDefine, ";
		$Sql .= " Hr_KpiIndicatorFormula = :Hr_KpiIndicatorFormula, ";
		$Sql .= " Hr_KpiIndicatorMeasure = :Hr_KpiIndicatorMeasure, ";
		$Sql .= " Hr_KpiIndicatorSource = :Hr_KpiIndicatorSource, ";
		$Sql .= " Hr_KpiIndicatorPartName = :Hr_KpiIndicatorPartName, ";
		$Sql .= " Hr_KpiIndicatorUnitID = :Hr_KpiIndicatorUnitID, ";
		$Sql .= " Hr_KpiIndicatorModiDateTime = now(), ";
		$Sql .= " Hr_KpiIndicatorState = :Hr_KpiIndicatorState ";
	$Sql .= " where Hr_KpiIndicatorID = :Hr_KpiIndicatorID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':Hr_KpiIndicatorName', $Hr_KpiIndicatorName);
	$Stmt->bindParam(':Hr_KpiIndicatorDefine', $Hr_KpiIndicatorDefine);
	$Stmt->bindParam(':Hr_KpiIndicatorFormula', $Hr_KpiIndicatorFormula);
	$Stmt->bindParam(':Hr_KpiIndicatorMeasure', $Hr_KpiIndicatorMeasure);
	$Stmt->bindParam(':Hr_KpiIndicatorSource', $Hr_KpiIndicatorSource);
	$Stmt->bindParam(':Hr_KpiIndicatorPartName', $Hr_KpiIndicatorPartName);
	$Stmt->bindParam(':Hr_KpiIndicatorUnitID', $Hr_KpiIndicatorUnitID);
	$Stmt->bindParam(':Hr_KpiIndicatorState', $Hr_KpiIndicatorState);
	$Stmt->bindParam(':Hr_KpiIndicatorID', $Hr_KpiIndicatorID);
	$Stmt->execute();
	$Stmt = null;


	$Sql = "delete from Hr_KpiIndicatorTasks where Hr_KpiIndicatorID=:Hr_KpiIndicatorID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':Hr_KpiIndicatorID', $Hr_KpiIndicatorID);
	$Stmt->execute();
	$Stmt = null;
}





$Req_Hr_OrganTask2ID_0 = isset($_REQUEST["Hr_OrganTask2ID_0"]) ? $_REQUEST["Hr_OrganTask2ID_0"] : "";

if ($Req_Hr_OrganTask2ID_0!=""){

	$Sql3 = " insert into Hr_KpiIndicatorTasks ( ";
		$Sql3 .= " Hr_KpiIndicatorID, ";
		$Sql3 .= " Hr_OrganTask2ID, ";
		$Sql3 .= " Hr_KpiIndicatorTaskRegDateTime, ";
		$Sql3 .= " Hr_KpiIndicatorTaskModiDateTime, ";
		$Sql3 .= " Hr_KpiIndicatorTaskState ";
	$Sql3 .= " ) values ( ";
		$Sql3 .= " :Hr_KpiIndicatorID, ";
		$Sql3 .= " 0, ";
		$Sql3 .= " now(), ";
		$Sql3 .= " now(), ";
		$Sql3 .= " 1 ";
	$Sql3 .= " ) ";

	$Stmt3 = $DbConn->prepare($Sql3);
	$Stmt3->bindParam(':Hr_KpiIndicatorID', $Hr_KpiIndicatorID);
	$Stmt3->execute();
	$Stmt3 = null;

}else{

	$Sql2 = "
		select 
			A.Hr_OrganTask2ID
		from Hr_OrganTask2 A 
			inner join Hr_OrganTask1 B on A.Hr_OrganTask1ID=B.Hr_OrganTask1ID 
		where A.Hr_OrganTask2State=1 and B.Hr_OrganTask1State=1 
		order by B.Hr_OrganTask1ID asc, A.Hr_OrganTask2ID asc
	";
	$Stmt2 = $DbConn->prepare($Sql2);
	$Stmt2->execute();
	$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
	$ListCount2 = 1;
	while($Row2 = $Stmt2->fetch()) {
		$Hr_OrganTask2ID = $Row2["Hr_OrganTask2ID"];
		
		$Req_Hr_OrganTask2ID = isset($_REQUEST["Hr_OrganTask2ID_".$Hr_OrganTask2ID]) ? $_REQUEST["Hr_OrganTask2ID_".$Hr_OrganTask2ID] : "";

		if ($Req_Hr_OrganTask2ID!=""){
			$Sql3 = " insert into Hr_KpiIndicatorTasks ( ";
				$Sql3 .= " Hr_KpiIndicatorID, ";
				$Sql3 .= " Hr_OrganTask2ID, ";
				$Sql3 .= " Hr_KpiIndicatorTaskRegDateTime, ";
				$Sql3 .= " Hr_KpiIndicatorTaskModiDateTime, ";
				$Sql3 .= " Hr_KpiIndicatorTaskState ";
			$Sql3 .= " ) values ( ";
				$Sql3 .= " :Hr_KpiIndicatorID, ";
				$Sql3 .= " :Hr_OrganTask2ID, ";
				$Sql3 .= " now(), ";
				$Sql3 .= " now(), ";
				$Sql3 .= " 1 ";
			$Sql3 .= " ) ";

			$Stmt3 = $DbConn->prepare($Sql3);
			$Stmt3->bindParam(':Hr_KpiIndicatorID', $Hr_KpiIndicatorID);
			$Stmt3->bindParam(':Hr_OrganTask2ID', $Req_Hr_OrganTask2ID);
			$Stmt3->execute();
			$Stmt3 = null;
		}

		$ListCount2++;
	}
	$Stmt2 = null;

}
?>


<?
include_once('./inc_common_form_js.php');
?>
<script type="text/javascript" src="js/common.js"></script>
<link rel="stylesheet" type="text/css" href="css/jquery-confirm.min.css" />
<script type="text/javascript" src="js/jquery-confirm.min.js"></script>
<script>
//parent.$.fn.colorbox.close();
parent.location.reload();
</script>
<?
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>

