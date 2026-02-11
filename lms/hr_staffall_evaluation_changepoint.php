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
$TargetState         = isset($_REQUEST["TargetState"        ]) ? $_REQUEST["TargetState"        ] : "";
$TargetMenu          = isset($_REQUEST["TargetMenu"         ]) ? $_REQUEST["TargetMenu"         ] : "";
#-----------------------------------------------------------------------------------------------------------------------------------------#
$Sql = "select * from Hr_Staff_Target where Hr_EvaluationID=$SearchState and Hr_TargetState='9' group by MemberID, Hr_EvaluationID order by MemberID asc, Hr_TargetID asc limit 1";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$imsi_MemberID     = $Row["MemberID"];
$Stmt = null;
echo $imsi_MemberID . "<br>";
#-----------------------------------------------------------------------------------------------------------------------------------------#
$Sql = "select *, sum(Hr_ChangePoint) as Hap_ChangePoint from Hr_Staff_Target 
                                  where Hr_EvaluationID=$SearchState and Hr_TargetState='9'
                                  group by MemberID, Hr_EvaluationID order by MemberID asc, Hr_TargetID asc";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
#-----------------------------------------------------------------------------------------------------------------------------------------#
while ($Row = $Stmt->fetch()) {
#-----------------------------------------------------------------------------------------------------------------------------------------#
		$MemberID          = $Row["MemberID"          ];
		$Hr_EvaluationID   = $Row["Hr_EvaluationID"   ];
		$Hap_ChangePoint   = $Row["Hap_ChangePoint"   ];   // 환산점수
		$Hr_SelfTotalPoint = $Row["Hr_SelfTotalPoint" ];
		$Hr_FirstTotalPoint= $Row["Hr_FirstTotalPoint"];

	    echo $MemberID . " - " . $Hr_EvaluationID . " | " . $Hap_ChangePoint . " | " . $Hr_SelfTotalPoint . " | " . $Hr_FirstTotalPoint . "<br>";
		
		$Re_Sql  = "update Hr_Staff_Target set Hr_SelfTotalPoint=:Hr_SelfTotalPoint ".iif($Hr_FirstTotalPoint > 0,", Hr_FirstTotalPoint=".$Hap_ChangePoint,"")." 
		                                 where MemberID=:MemberID and Hr_EvaluationID=:Hr_EvaluationID";
		$Re_Stmt = $DbConn->prepare($Re_Sql);
        $Re_Stmt->bindParam(':Hr_SelfTotalPoint', $Hap_ChangePoint);
		$Re_Stmt->bindParam(':MemberID',          $MemberID);
		$Re_Stmt->bindParam(':Hr_EvaluationID',   $Hr_EvaluationID);
		$Re_Stmt->execute();
		$Re_Stmt = null;

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
// document.RegForm.submit();
</script>