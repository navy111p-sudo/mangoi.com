<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$SearchState = isset($_REQUEST["SearchState"]) ? $_REQUEST["SearchState"] : "";
$TargetMenu  = isset($_REQUEST["TargetMenu" ]) ? $_REQUEST["TargetMenu" ] : "";
$Hr_MemberID = isset($_REQUEST["Hr_MemberID"]) ? $_REQUEST["Hr_MemberID"] : "";
$TargetState = 9;
$KpiIndicatorID = "";
#-----------------------------------------------------------------------------------------------------------------------------------------#
$Sql  = "select * from Hr_Staff_Target where MemberID=:Hr_MemberID and Hr_TargetState=:Hr_TargetState 
                 order by Hr_EvaluationID desc, Hr_KpiIndicatorID asc";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':Hr_MemberID',       $Hr_MemberID);
$Stmt->bindParam(':Hr_TargetState',    $TargetState);
$Stmt->execute();
while ($Row = $Stmt->fetch()) {

        $Hr_EvaluationID   = $Row["Hr_EvaluationID"];
		$Hr_KpiIndicatorID = $Row["Hr_KpiIndicatorID"];
		if ($KpiIndicatorID) {
			   $KpiIndicatorID .= "/";
		}
        $KpiIndicatorID .= $Hr_KpiIndicatorID;

}
#-----------------------------------------------------------------------------------------------------------------------------------------#
?>
<form name="RegForm" method="post">
<input type="hidden" name="SearchState"     value="<?=$SearchState?>" />
<input type="hidden" name="TargetMenu"      value="<?=$TargetMenu?>" />
<input type="hidden" name="CallSearchState" value="<?=$Hr_EvaluationID?>" />
<input type="hidden" name="KpiIndicatorID"  value="<?=$KpiIndicatorID?>" />
</form>

<script>
 document.RegForm.target = "_top";
 document.RegForm.action = "hr_staff_target_list.php";
 document.RegForm.submit();
</script>

