<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
#-----------------------------------------------------------------------------------------------------------------------------------------#
$err_num = 0;
$err_msg = "";
#-----------------------------------------------------------------------------------------------------------------------------------------#
$SearchState         = isset($_REQUEST["SearchState"        ]) ? $_REQUEST["SearchState"        ] : "";
$KpiIndicatorID      = isset($_REQUEST["KpiIndicatorID"     ]) ? $_REQUEST["KpiIndicatorID"     ] : "";
$TargetMenu          = isset($_REQUEST["TargetMenu"         ]) ? $_REQUEST["TargetMenu"         ] : "";
// 개인목표에서....
$Hr_MemberID         = isset($_REQUEST["Hr_MemberID"        ]) ? $_REQUEST["Hr_MemberID"        ] : "";
$Hr_EvaluationID     = isset($_REQUEST["Hr_EvaluationID"    ]) ? $_REQUEST["Hr_EvaluationID"    ] : "";
$Hr_EvaluationTypeID = isset($_REQUEST["Hr_EvaluationTypeID"]) ? $_REQUEST["Hr_EvaluationTypeID"] : "";
$Hr_OrganLevelID     = isset($_REQUEST["Hr_OrganLevelID"    ]) ? $_REQUEST["Hr_OrganLevelID"    ] : "";
// 부분목표에서....
$MemberID            = isset($_REQUEST["MemberID"           ]) ? $_REQUEST["MemberID"           ] : "";
$EvaluationID        = isset($_REQUEST["EvaluationID"       ]) ? $_REQUEST["EvaluationID"       ] : "";
$Eva_Pass            = isset($_REQUEST["Eva_Pass"           ]) ? $_REQUEST["Eva_Pass"           ] : "";

$line_cnt            = isset($_REQUEST["line_cnt"           ]) ? $_REQUEST["line_cnt"           ] : "";
$TargetState         = isset($_REQUEST["TargetState"        ]) ? $_REQUEST["TargetState"        ] : "";
$TargetState_imsi    = 9;
#-----------------------------------------------------------------------------------------------------------------------------------------#
if ($TargetMenu==1 and $line_cnt != ""){
#-----------------------------------------------------------------------------------------------------------------------------------------#
	 $Sql  = " delete from Hr_Staff_Target where MemberID=:Hr_MemberID and Hr_EvaluationID=:Hr_EvaluationID and Hr_TargetState < :Hr_TargetState";
	 $Stmt = $DbConn->prepare($Sql);
	 $Stmt->bindParam(':Hr_MemberID',       $Hr_MemberID);
	 $Stmt->bindParam(':Hr_EvaluationID',   $Hr_EvaluationID);
	 $Stmt->bindParam(':Hr_TargetState',    $TargetState_imsi);
	 $Stmt->execute();
	 $Stmt = null;
     
	 for ($k=1; $k <= $line_cnt; $k++) {
	       
		   $Hr_KpiIndicatorID = isset($_REQUEST["KPI_CHECKID_" . $k]) ? $_REQUEST["KPI_CHECKID_" . $k] : "";
		   if ($Hr_KpiIndicatorID) {
				 
				$Hr_TargetName     = isset($_REQUEST["KPI_TARGET1_" . $k]) ? $_REQUEST["KPI_TARGET1_" . $k] : "";
				$Hr_TargetAddValue = isset($_REQUEST["KPI_TARGET5_" . $k]) ? $_REQUEST["KPI_TARGET5_" . $k] : "";
                 
				$Sql = " insert into Hr_Staff_Target ( ";
					$Sql .= " MemberID, ";
					$Sql .= " Hr_OrganLevelID, ";
					$Sql .= " Hr_EvaluationID, ";
					$Sql .= " Hr_EvaluationTypeID, ";
					$Sql .= " Hr_KpiIndicatorID, ";
					$Sql .= " Hr_TargetName, ";
					$Sql .= " Hr_TargetAddValue, ";
					$Sql .= " Hr_TargetState, ";
					$Sql .= " wdate ";
				$Sql .= " ) values ( ";
					$Sql .= " ".$Hr_MemberID.", ";
					$Sql .= " ".$Hr_OrganLevelID.", ";
					$Sql .= " ".$Hr_EvaluationID.", ";
					$Sql .= " ".$Hr_EvaluationTypeID.", ";
					$Sql .= " ".$Hr_KpiIndicatorID.", ";
					$Sql .= " '".$Hr_TargetName."', ";
					$Sql .= " '".$Hr_TargetAddValue."', ";
					$Sql .= " '".$TargetState."', ";
					$Sql .= " now() ";
				$Sql .= " ) ";
				$Stmt = $DbConn->prepare($Sql);
				$Stmt->execute();
				$Stmt = null;

		   } 
	 
	 }
#-----------------------------------------------------------------------------------------------------------------------------------------#
} else if ($TargetMenu==2 and $Eva_Pass){
#-----------------------------------------------------------------------------------------------------------------------------------------#
     if ($Eva_Pass==1) {     // 반려
 	        $Sql  = "update Hr_Staff_Target set Hr_TargetState='8' where MemberID=:MemberID and Hr_EvaluationID=:Hr_EvaluationID and Hr_TargetState=:Hr_TargetState";
     } else {
 	        $Sql  = "update Hr_Staff_Target set Hr_UseYN='Y' where MemberID=:MemberID and Hr_EvaluationID=:Hr_EvaluationID and Hr_TargetState=:Hr_TargetState";
	 }
	 $Stmt = $DbConn->prepare($Sql);
	 $Stmt->bindParam(':MemberID',        $MemberID);
	 $Stmt->bindParam(':Hr_EvaluationID', $EvaluationID);
	 $Stmt->bindParam(':Hr_TargetState',  $TargetState_imsi);
	 $Stmt->execute();
	 $Stmt = null;
#-----------------------------------------------------------------------------------------------------------------------------------------#
}
#-----------------------------------------------------------------------------------------------------------------------------------------#
?>
<form name="RegForm" method="post">
<input type="hidden" name="SearchState"    value="<?=$SearchState?>" />
<input type="hidden" name="KpiIndicatorID" value="<?=$KpiIndicatorID?>" />
<input type="hidden" name="TargetState"    value="<?=$TargetState?>" />
<input type="hidden" name="TargetMenu"     value="<?=$TargetMenu?>" />
</form>

<script>
 document.RegForm.target = "_top";
 document.RegForm.action = "hr_staff_target_list.php";
 document.RegForm.submit();
</script>

