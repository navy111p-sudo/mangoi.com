<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
#-----------------------------------------------------------------------------------------------------------------------------------------#
$err_num = 0;
$err_msg = "";
#-----------------------------------------------------------------------------------------------------------------------------------------#
$SearchState  = isset($_REQUEST["SearchState"   ]) ? $_REQUEST["SearchState"   ] : "";
$My_MemberID  = isset($_REQUEST["My_MemberID"   ]) ? $_REQUEST["My_MemberID"   ] : "";
$MemberID     = isset($_REQUEST["MemberID"      ]) ? $_REQUEST["MemberID"      ] : "";
$OrganTask1ID = isset($_REQUEST["OrganTask1ID"  ]) ? $_REQUEST["OrganTask1ID"  ] : "";
$OrganTask2ID = isset($_REQUEST["OrganTask2ID"  ]) ? $_REQUEST["OrganTask2ID"  ] : "";
#-----------------------------------------------------------------------------------------------------------------------------------------#
if ($SearchState){
#-----------------------------------------------------------------------------------------------------------------------------------------#
         //평가해 놓은 항목들을 삭제하고
         $Sql = "delete from Hr_Staff_Compentency 
                        where MyMemberID=:MyMemberID and 
                              MemberID=:MemberID and 
                              Hr_EvaluationID=:Hr_EvaluationID ";
         $Stmt = $DbConn->prepare($Sql);
         $Stmt->bindParam(':MyMemberID',      $My_MemberID);
         $Stmt->bindParam(':MemberID',        $MemberID);
         $Stmt->bindParam(':Hr_EvaluationID', $SearchState);
         //$Stmt->bindParam(':Hr_OrganTask1ID', $OrganTask1ID);
         //$Stmt->bindParam(':Hr_OrganTask2ID', $OrganTask2ID);
         $Stmt->execute();
         $Stmt = null;

         //평가해 놓은 평균점수를 초기화한다.
         $Sql = "UPDATE Hr_EvaluationCompetencyMembers SET  Hr_EvaluationCompetencyAddTotalPoint = 0 
                        WHERE Hr_EvaluationCompetencyMemberID=:MyMemberID and 
                              MemberID=:MemberID and 
                              Hr_EvaluationID=:Hr_EvaluationID";
         $Stmt = $DbConn->prepare($Sql);
         $Stmt->bindParam(':MyMemberID',      $My_MemberID);
         $Stmt->bindParam(':MemberID',        $MemberID);
         $Stmt->bindParam(':Hr_EvaluationID', $SearchState);
         $Stmt->execute();
         $Stmt = null;
#-----------------------------------------------------------------------------------------------------------------------------------------#
}
#-----------------------------------------------------------------------------------------------------------------------------------------#
?>
<form name="RegForm" method="post">
<input type="hidden" name="SearchState"  value="<?=$SearchState?>" />
</form>

<script>
 document.RegForm.target = "_top";
 document.RegForm.action = "hr_staffall_evaluation_competency_list.php";
 document.RegForm.submit();
</script>

