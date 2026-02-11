<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
#-----------------------------------------------------------------------------------------------------------------------------------------#
$err_num = 0;
$err_msg = "";
#-----------------------------------------------------------------------------------------------------------------------------------------#
$SearchState = isset($_REQUEST["SearchState"]) ? $_REQUEST["SearchState"] : "";
$TargetID    = isset($_REQUEST["TargetID"   ]) ? $_REQUEST["TargetID"   ] : "";
#-----------------------------------------------------------------------------------------------------------------------------------------#
if ($TargetID != ""){
#-----------------------------------------------------------------------------------------------------------------------------------------#
	 $Sql  = "update Hr_Staff_Target set Hr_TargetAddValue='',
	                                     Hr_TargetState='',
										 Hr_UseYN='N',
										 Hr_GoodWork=NULL,
                                         Hr_SelfComment=NULL,
										 Hr_FirstBossComment=NULL,
										 Hr_SecondBossComment=NULL,
										 Hr_EndBossComment=NULL,
                                         Hr_SelfPoint=0,
										 Hr_ChangePoint=0,
										 Hr_FirstBossPoint=0,
										 Hr_SecondBossPoint=0,
										 Hr_EndBossPoint=0,
										 Hr_SelfTotalPoint=0,
										 Hr_FirstTotalPoint=0,
										 Hr_SecondTotalPoint=0, 
										 Hr_EndTotalPoint=0, 
                                         Hr_EvaluationState=NULL, 
										 Hr_EvaUseYN='N' 
	                               where Hr_TargetID=:Hr_TargetID";
	 $Stmt = $DbConn->prepare($Sql);
	 $Stmt->bindParam(':Hr_TargetID', $TargetID);
	 $Stmt->execute();
	 $Stmt = null;
#-----------------------------------------------------------------------------------------------------------------------------------------#
}
#-----------------------------------------------------------------------------------------------------------------------------------------#
?>
<form name="RegForm" method="post">
<input type="hidden" name="SearchState"    value="<?=$SearchState?>" />
</form>

<script>
 document.RegForm.target = "_top";
 document.RegForm.action = "hr_staffall_target_list.php";
 document.RegForm.submit();
</script>

