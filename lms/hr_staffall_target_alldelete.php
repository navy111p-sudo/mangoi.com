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
#-----------------------------------------------------------------------------------------------------------------------------------------#
if ($SearchState != ""){
#-----------------------------------------------------------------------------------------------------------------------------------------#
	 $Sql  = "delete from Hr_Staff_Target where Hr_EvaluationID=:Hr_EvaluationID";
	 $Stmt = $DbConn->prepare($Sql);
	 $Stmt->bindParam(':Hr_EvaluationID', $SearchState);
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

