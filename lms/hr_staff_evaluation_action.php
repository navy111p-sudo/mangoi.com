<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$SearchState         = isset($_REQUEST["SearchState"    ]) ? $_REQUEST["SearchState"    ] : "";
$TargetMenu          = isset($_REQUEST["TargetMenu"     ]) ? $_REQUEST["TargetMenu"     ] : "";
$MemberID            = isset($_REQUEST["MemberID"       ]) ? $_REQUEST["MemberID"       ] : "";
$LevelChi            = isset($_REQUEST["LevelChi"       ]) ? $_REQUEST["LevelChi"       ] : "";
$MyOrgan             = isset($_REQUEST["MyOrgan"        ]) ? $_REQUEST["MyOrgan"        ] : "";
$LevelNone           = isset($_REQUEST["LevelNone"      ]) ? $_REQUEST["LevelNone"      ] : "";
$EvaluationState     = isset($_REQUEST["EvaluationState"]) ? $_REQUEST["EvaluationState"] : "";
$Hr_EvaUseYN         = "N";
if ($EvaluationState == 12) {
       $Hr_EvaUseYN = "Y";
}

$line_cnt = isset($_REQUEST["line_cnt"]) ? $_REQUEST["line_cnt"] : "";

if ($line_cnt != ""){
     
     $Hr_EvaComment = isset($_REQUEST["EVA_COMMENT"]) ? $_REQUEST["EVA_COMMENT"] : "";                     // 상사평가
     $Hr_TotalPoint = isset($_REQUEST["EVA_TOTAL"  ]) ? $_REQUEST["EVA_TOTAL"  ] : "";                     // 총점수
	 #-------------------------------------------------------------------------------------------------------------------------#
     for ($k=1; $k <= $line_cnt; $k++) {
	 #-------------------------------------------------------------------------------------------------------------------------#
           $Hr_TargetID        = isset($_REQUEST["Hr_TargetID_" .  $k]) ? $_REQUEST["Hr_TargetID_" .  $k] : "";     
           
           $Hr_GoodWork        = isset($_REQUEST["KPI_TARGET6_" .  $k]) ? $_REQUEST["KPI_TARGET6_" .  $k] : "";      // 자기산출물
           $Hr_SelfPoint       = isset($_REQUEST["KPI_TARGET7_" .  $k]) ? $_REQUEST["KPI_TARGET7_" .  $k] : "";      // 자기평가점수
           $Hr_ChangePoint     = isset($_REQUEST["KPI_TARGET8_" .  $k]) ? $_REQUEST["KPI_TARGET8_" .  $k] : "";      // 환산자기점수
           $Hr_FirstBossPoint  = isset($_REQUEST["KPI_TARGET9_" .  $k]) ? $_REQUEST["KPI_TARGET9_" .  $k] : "";      // 1차상사점수
           $Hr_SecondBossPoint = isset($_REQUEST["KPI_TARGET10_" . $k]) ? $_REQUEST["KPI_TARGET10_" . $k] : "";      // 2차상사점수
           $Hr_EndBossPoint    = isset($_REQUEST["KPI_TARGET11_" . $k]) ? $_REQUEST["KPI_TARGET11_" . $k] : "";      // 최종상사점수
           
           if ($Hr_TargetID) {
                
                $Hr_EvaluationLevel    = "";           // 최종 업적 등급
                $Hr_EndEvaluationPoint = "";           // 최종 업적 점수
                if ($LevelChi == 3 || $MyOrgan == 1) {  
					// $MyOrgan은 자신의 레벨등급으로 1은 대표자이다. 대표자는 최종보스이므로 그에 맞는 처리를 한다.
					// $Hr_EndBossPoint 에 점수를 대입하고, $Hr_EvaluateState 및 $Hr_EvaUseYN 도 최종 처리한다.
					  if ($LevelChi == 1) $Hr_EndBossPoint = $Hr_FirstBossPoint;
					  if ($LevelChi == 2) $Hr_EndBossPoint = $Hr_SecondBossPoint;
					  $EvaluationState = 12;
					  $Hr_EvaUseYN = "Y";

                      $Hr_EndTotalPoint = $Hr_TotalPoint;
                      if ($Hr_EndTotalPoint > 0 and $Hr_EndTotalPoint >= 110) {
                             $Hr_EvaluationLevel    = "S";
                             $Hr_EndEvaluationPoint = 110;
                      } else if ($Hr_EndTotalPoint > 0 and $Hr_EndTotalPoint >= 100 and $Hr_EndTotalPoint <= 109) {
                             $Hr_EvaluationLevel    = "A";
                             $Hr_EndEvaluationPoint = 105;
                      } else if ($Hr_EndTotalPoint > 0 and $Hr_EndTotalPoint >= 90 and $Hr_EndTotalPoint <= 99) {
                             $Hr_EvaluationLevel    = "B";
                             $Hr_EndEvaluationPoint = 95;
                      } else if ($Hr_EndTotalPoint > 0 and $Hr_EndTotalPoint >= 80 and $Hr_EndTotalPoint <= 89) {
                             $Hr_EvaluationLevel    = "C";
                             $Hr_EndEvaluationPoint = 85;
                      } else if ($Hr_EndTotalPoint > 0 and $Hr_EndTotalPoint <= 70) {
                             $Hr_EvaluationLevel    = "D";
                             $Hr_EndEvaluationPoint = 75;
                      } 
                }
                
                $Sql = " update Hr_Staff_Target set ";
				if ($LevelChi==3 || $MyOrgan == 1) {
					  $Sql .= " Hr_EndBossPoint=:Hr_EndBossPoint, ";
					  $Sql .= " Hr_EndBossComment=:Hr_EndBossComment, ";
					  $Sql .= " Hr_EndTotalPoint=:Hr_EndTotalPoint, ";
					  $Sql .= " Hr_EvaluationLevel=:Hr_EvaluationLevel, ";
					  $Sql .= " Hr_EndEvaluationPoint=:Hr_EndEvaluationPoint, ";
				} else if ($LevelChi==1) {
                      $Sql .= " Hr_FirstBossPoint=:Hr_FirstBossPoint, ";
                      $Sql .= " Hr_FirstBossComment=:Hr_FirstBossComment, ";
                      $Sql .= " Hr_FirstTotalPoint=:Hr_FirstTotalPoint, ";
                } else if ($LevelChi==2) {
                      $Sql .= " Hr_SecondBossPoint=:Hr_SecondBossPoint, ";
                      $Sql .= " Hr_SecondBossComment=:Hr_SecondBossComment, ";
                      $Sql .= " Hr_SecondTotalPoint=:Hr_SecondTotalPoint, ";
                } else {
                      $Sql .= " Hr_GoodWork=:Hr_GoodWork, ";
                      $Sql .= " Hr_SelfPoint=:Hr_SelfPoint, ";
                      $Sql .= " Hr_ChangePoint=:Hr_ChangePoint, ";
                      $Sql .= " Hr_SelfComment=:Hr_SelfComment, ";
                      $Sql .= " Hr_SelfTotalPoint=:Hr_SelfTotalPoint, ";
                }
                $Sql .= " Hr_EvaluationState=:Hr_EvaluationState, ";
                $Sql .= " Hr_EvaUseYN=:Hr_EvaUseYN, ";
                $Sql .= " edate = now() ";
                $Sql .= " where Hr_TargetID=:Hr_TargetID ";
                $Stmt = $DbConn->prepare($Sql);
			    if ($LevelChi==3 || $MyOrgan == 1) {
					  $Stmt->bindParam(':Hr_EndBossPoint',      $Hr_EndBossPoint);
					  $Stmt->bindParam(':Hr_EndBossComment',    $Hr_EvaComment);
					  $Stmt->bindParam(':Hr_EndTotalPoint',     $Hr_TotalPoint);
					  $Stmt->bindParam(':Hr_EvaluationLevel',   $Hr_EvaluationLevel);
					  $Stmt->bindParam(':Hr_EndEvaluationPoint',$Hr_EndEvaluationPoint);
				} else if ($LevelChi==1) {
                      $Stmt->bindParam(':Hr_FirstBossPoint',    $Hr_FirstBossPoint);
                      $Stmt->bindParam(':Hr_FirstBossComment',  $Hr_EvaComment);
                      $Stmt->bindParam(':Hr_FirstTotalPoint',   $Hr_TotalPoint);
                } else if ($LevelChi==2) {
                      $Stmt->bindParam(':Hr_SecondBossPoint',   $Hr_SecondBossPoint);
                      $Stmt->bindParam(':Hr_SecondBossComment', $Hr_EvaComment);
                      $Stmt->bindParam(':Hr_SecondTotalPoint',  $Hr_TotalPoint);
                } else {
                      $Stmt->bindParam(':Hr_GoodWork',          $Hr_GoodWork);
                      $Stmt->bindParam(':Hr_SelfPoint',         $Hr_SelfPoint);
                      $Stmt->bindParam(':Hr_ChangePoint',       $Hr_ChangePoint);
                      $Stmt->bindParam(':Hr_SelfComment',       $Hr_EvaComment);
                      $Stmt->bindParam(':Hr_SelfTotalPoint',    $Hr_TotalPoint);
                } 
                $Stmt->bindParam(':Hr_EvaluationState', $EvaluationState);
                $Stmt->bindParam(':Hr_EvaUseYN', $Hr_EvaUseYN);
                $Stmt->bindParam(':Hr_TargetID', $Hr_TargetID);
                $Stmt->execute();
                $Stmt = null;

           } 
	 #-------------------------------------------------------------------------------------------------------------------------#
     }
	 #-------------------------------------------------------------------------------------------------------------------------#
	 if ($LevelChi == 3 || $MyOrgan == 1) {             // 최종 성과평가 데이터등록(최종 결재자인 대표자인 경우)
	 #-------------------------------------------------------------------------------------------------------------------------#
			$Sql  = "select count(*) TotalRowCount from Hr_Staff_ResultEvaluation  
			                                      where Hr_EvaluationID=:Hr_EvaluationID and 
												        MemberID=:MemberID";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':Hr_EvaluationID', $SearchState);
			$Stmt->bindParam(':MemberID',        $MemberID);
			$Stmt->execute();
			$Row = $Stmt->fetch();
			$TotalRowCount = $Row["TotalRowCount"];
			if (!$TotalRowCount) {
					 $Sql = " insert into Hr_Staff_ResultEvaluation ( Hr_EvaluationID,  
						                                              MemberID, 
						                                              Hr_EvaluationLevel,  
						                                              Hr_EndEvaluationPoint 
					                                                ) values ( 
						                                             ".$SearchState.", 
						                                             ".$MemberID.", 
						                                            '".$Hr_EvaluationLevel."', 
						                                             ".$Hr_EndEvaluationPoint." 
					                                                ) ";
					$Stmt = $DbConn->prepare($Sql);
					$Stmt->execute();
					$Stmt = null;
			} else {
					$Sql = " update Hr_Staff_ResultEvaluation 
								set Hr_EvaluationLevel='".$Hr_EvaluationLevel."',
									Hr_EndEvaluationPoint=".$Hr_EndEvaluationPoint." 
							  where Hr_EvaluationID=:Hr_EvaluationID and 
								    MemberID=:MemberID";
					$Stmt = $DbConn->prepare($Sql);
					$Stmt->bindParam(':Hr_EvaluationID', $SearchState);
					$Stmt->bindParam(':MemberID',        $MemberID);
					$Stmt->execute();
					$Stmt = null;
			}
			#-----------------------------------------------------------------------------------------------------------------#
			# 최종평가 점수확정
			#-----------------------------------------------------------------------------------------------------------------#
			include('hr_inc_endpoint.php');
	 #-------------------------------------------------------------------------------------------------------------------------#
	 }
	 #-------------------------------------------------------------------------------------------------------------------------#


}
?>
<form name="RegForm" method="post">
<input type="hidden" name="SearchState"     value="<?=$SearchState?>" />
<input type="hidden" name="EvaluationState" value="<?=$EvaluationState?>" />
<input type="hidden" name="TargetMenu"      value="<?=$TargetMenu?>" />
</form>

<script>
 document.RegForm.target = "_top";
 document.RegForm.action = "hr_staff_evaluation_list.php";
 document.RegForm.submit();
</script>

