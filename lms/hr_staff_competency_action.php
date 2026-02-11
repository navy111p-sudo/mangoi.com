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
$Compsw       = isset($_REQUEST["Compsw"        ]) ? $_REQUEST["Compsw"        ] : "";
$My_MemberID  = isset($_REQUEST["My_MemberID"   ]) ? $_REQUEST["My_MemberID"   ] : "";
$MemberID     = isset($_REQUEST["MemberID"      ]) ? $_REQUEST["MemberID"      ] : "";
$MemberName   = isset($_REQUEST["MemberName"    ]) ? $_REQUEST["MemberName"    ] : "";
$OrganTask1ID = isset($_REQUEST["OrganTask1ID"  ]) ? $_REQUEST["OrganTask1ID"  ] : "";
$OrganTask2ID = isset($_REQUEST["OrganTask2ID"  ]) ? $_REQUEST["OrganTask2ID"  ] : "";
$COMP_COMMENT = isset($_REQUEST["COMP_COMMENT"  ]) ? $_REQUEST["COMP_COMMENT"  ] : "";
$line_cnt     = isset($_REQUEST["line_cnt"      ]) ? $_REQUEST["line_cnt"      ] : "";
#-----------------------------------------------------------------------------------------------------------------------------------------#
if ($SearchState){
#-----------------------------------------------------------------------------------------------------------------------------------------#
         $Sql = "delete from Hr_Staff_Compentency 
                        where MyMemberID=:MyMemberID and 
                              MemberID=:MemberID and 
                              Hr_EvaluationID=:Hr_EvaluationID and 
                              Hr_OrganTask1ID=:Hr_OrganTask1ID and
                              Hr_OrganTask2ID=:Hr_OrganTask2ID";
         $Stmt = $DbConn->prepare($Sql);
         $Stmt->bindParam(':MyMemberID',      $My_MemberID);
         $Stmt->bindParam(':MemberID',        $MemberID);
         $Stmt->bindParam(':Hr_EvaluationID', $SearchState);
         $Stmt->bindParam(':Hr_OrganTask1ID', $OrganTask1ID);
         $Stmt->bindParam(':Hr_OrganTask2ID', $OrganTask2ID);
         $Stmt->execute();
         $Stmt = null;
         #--------------------------------------------------------------------------------------------------------------------------------#
         $UseYN = "N";
         $CPoint_Total   = 0;         // 역량평가점수합계
         $CIndicator_Cnt = 0;         // 역량평가 품항수
         if ($Compsw==9) {
                 $UseYN = "Y";
         }
         #--------------------------------------------------------------------------------------------------------------------------------#
         for ($k=1; $k <= $line_cnt; $k++) {
         #--------------------------------------------------------------------------------------------------------------------------------#
               $CompetencyIndicatorID = isset($_REQUEST["CompetencyIndicatorID_" . $k]) ? $_REQUEST["CompetencyIndicatorID_" . $k] : "";
               $CPoint_Val            = isset($_REQUEST["CPoint_" . $k]) ? $_REQUEST["CPoint_" . $k] : "";
               
               $CPoint_Total = $CPoint_Total + $CPoint_Val;
			   $CIndicator_Cnt++;
               #--------------------------------------------------------------------------------------------------------------------------#
               if ($CompetencyIndicatorID and $CPoint_Val) {     
               #--------------------------------------------------------------------------------------------------------------------------#
                     $Sql = " insert into Hr_Staff_Compentency ( ";
                        $Sql .= " MyMemberID, ";
                        $Sql .= " MemberID, ";
                        $Sql .= " Hr_EvaluationID, ";
                        $Sql .= " Hr_OrganTask1ID, ";
                        $Sql .= " Hr_OrganTask2ID, ";
                        $Sql .= " Hr_CompetencyIndicatorID, ";
                        $Sql .= " Hr_CompetencyIndicatorPoint, ";
                        $Sql .= " Hr_CompetencyIndicatorComment, ";
                        $Sql .= " Hr_CompetencyIndicatorState, ";
                        $Sql .= " Hr_CompetencyIndicatorUseYN, ";
                        $Sql .= " wdate ";
                     $Sql .= " ) values ( ";
                        $Sql .= " ".$My_MemberID.", ";
                        $Sql .= " ".$MemberID.", ";
                        $Sql .= " ".$SearchState.", ";
                        $Sql .= " ".$OrganTask1ID.", ";
                        $Sql .= " ".$OrganTask2ID.", ";
                        $Sql .= " ".$CompetencyIndicatorID.", ";
                        $Sql .= " ".$CPoint_Val.", ";
                        $Sql .= " '".$COMP_COMMENT."', ";
                        $Sql .= " '".$Compsw."',";  
                        $Sql .= " '".$UseYN."',";   
                        $Sql .= " now() ";
                    $Sql .= " ) ";
                    $Stmt = $DbConn->prepare($Sql);
                    $Stmt->execute();
                    $Stmt = null;
               #--------------------------------------------------------------------------------------------------------------------------#
               } 
         #--------------------------------------------------------------------------------------------------------------------------------#
         }
         #--------------------------------------------------------------------------------------------------------------------------------#
         # 역량평가 저장하기 
         #--------------------------------------------------------------------------------------------------------------------------------#
         if ($Compsw==9 and $CPoint_Total > 0) {
         #--------------------------------------------------------------------------------------------------------------------------------#
			    if ($CPoint_Total > 0) {
                       $CPoint_Total = $CPoint_Total / $CIndicator_Cnt;
                }
                #-------------------------------------------------------------------------------------------------------------------------#
				$Sql = " select Hr_EvaluationCompetencyAddValue 
				              from Hr_EvaluationCompetencyMembers 
							 where Hr_EvaluationID=:Hr_EvaluationID and 
								   MemberID=:MemberID and 
								   Hr_EvaluationCompetencyMemberID=:Hr_EvaluationCompetencyMemberID";
                $Stmt = $DbConn->prepare($Sql);
                $Stmt->bindParam(':Hr_EvaluationID',                      $SearchState);
                $Stmt->bindParam(':MemberID',                             $MemberID);
                $Stmt->bindParam(':Hr_EvaluationCompetencyMemberID',      $My_MemberID);
                $Stmt->execute();
				$Row = $Stmt->fetch();
                $Stmt = null;
				$CPoint_addValue = $Row["Hr_EvaluationCompetencyAddValue"];  // 가중치(%)
				$CPoint_addTotal = ($CPoint_addValue / 100) * $CPoint_Total;
                #-------------------------------------------------------------------------------------------------------------------------#
                $Sql = " update Hr_EvaluationCompetencyMembers 
                            set Hr_EvaluationCompetencyAddTotalPoint=:Hr_EvaluationCompetencyAddTotalPoint
                         where Hr_EvaluationID=:Hr_EvaluationID and 
                               MemberID=:MemberID and 
                               Hr_EvaluationCompetencyMemberID=:Hr_EvaluationCompetencyMemberID";
                $Stmt = $DbConn->prepare($Sql);
                $Stmt->bindParam(':Hr_EvaluationCompetencyAddTotalPoint', $CPoint_addTotal);
                $Stmt->bindParam(':Hr_EvaluationID',                      $SearchState);
                $Stmt->bindParam(':MemberID',                             $MemberID);
                $Stmt->bindParam(':Hr_EvaluationCompetencyMemberID',      $My_MemberID);
                $Stmt->execute();
                $Stmt = null;
                #-------------------------------------------------------------------------------------------------------------------------#
                $Sql = " select * from Hr_EvaluationCompetencyMembers where Hr_EvaluationID=:Hr_EvaluationID and MemberID=:MemberID";
                $Stmt = $DbConn->prepare($Sql);
                $Stmt->bindParam(':Hr_EvaluationID', $SearchState);
                $Stmt->bindParam(':MemberID',        $MemberID);
                $Stmt->execute();
				
                $AddTotalPoint_NoneCnt = 0;
                $AddTotalPoint_Hapge   = 0;
				while($Row = $Stmt->fetch()) {
					   if (!$Row["Hr_EvaluationCompetencyAddTotalPoint"]) {
                              $AddTotalPoint_NoneCnt++;
                       }
                       $AddTotalPoint_Hapge = $AddTotalPoint_Hapge + $Row["Hr_EvaluationCompetencyAddTotalPoint"];
				}
				#-------------------------------------------------------------------------------------------------------------------------#
				if ($AddTotalPoint_NoneCnt==0 and $AddTotalPoint_Hapge > 0) {       // 최종 성과평가 데이터등록
                #-------------------------------------------------------------------------------------------------------------------------#
						if ($AddTotalPoint_Hapge >= 4.5) {
								 $Hr_EvaluationCompetencyLevel    = "S";
								 $Hr_EvaluationCompetencyEndPoint = 110;
						} else if ($AddTotalPoint_Hapge >= 4 and $AddTotalPoint_Hapge < 4.5) {
								 $Hr_EvaluationCompetencyLevel    = "A";
								 $Hr_EvaluationCompetencyEndPoint = 105;
						} else if ($AddTotalPoint_Hapge >= 3.5 and $AddTotalPoint_Hapge < 4) {
								 $Hr_EvaluationCompetencyLevel    = "B";
								 $Hr_EvaluationCompetencyEndPoint = 95;
						} else if ($AddTotalPoint_Hapge >= 3 and $AddTotalPoint_Hapge < 3.5) {
								 $Hr_EvaluationCompetencyLevel    = "C";
								 $Hr_EvaluationCompetencyEndPoint = 85;
						} else if ($AddTotalPoint_Hapge < 3) {
								 $Hr_EvaluationCompetencyLevel    = "D";
								 $Hr_EvaluationCompetencyEndPoint = 75;
						} 
						#-----------------------------------------------------------------------------------------------------------------#
						$Sql = " select count(*) TotalRowCount from Hr_Staff_ResultEvaluation  
						                                      where Hr_EvaluationID=:Hr_EvaluationID and 
															        MemberID=:MemberID";
						$Stmt = $DbConn->prepare($Sql);
						$Stmt->bindParam(':Hr_EvaluationID', $SearchState);
						$Stmt->bindParam(':MemberID',        $MemberID);
						$Stmt->execute();
						$Row = $Stmt->fetch();
						$TotalRowCount = $Row["TotalRowCount"];
						if (!$TotalRowCount) {
								 $Sql = " insert into Hr_Staff_ResultEvaluation ( ";
									$Sql .= " Hr_EvaluationID, ";
									$Sql .= " MemberID, ";
									$Sql .= " Hr_EvaluationCompetencyAddTotalPoint, ";
									$Sql .= " Hr_EvaluationCompetencyLevel, ";
									$Sql .= " Hr_EvaluationCompetencyEndPoint ";
								 $Sql .= " ) values ( ";
									$Sql .= " ".$SearchState.", ";
									$Sql .= " ".$MemberID.", ";
									$Sql .= " ".$AddTotalPoint_Hapge.", ";
									$Sql .= " '".$Hr_EvaluationCompetencyLevel."', ";
									$Sql .= " ".$Hr_EvaluationCompetencyEndPoint." ";
								$Sql .= " ) ";
								$Stmt = $DbConn->prepare($Sql);
								$Stmt->execute();
								$Stmt = null;
						} else {
								$Sql = " update Hr_Staff_ResultEvaluation 
											set Hr_EvaluationCompetencyAddTotalPoint=".$AddTotalPoint_Hapge.",
												Hr_EvaluationCompetencyLevel='".$Hr_EvaluationCompetencyLevel."',
												Hr_EvaluationCompetencyEndPoint=".$Hr_EvaluationCompetencyEndPoint." 
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
         #--------------------------------------------------------------------------------------------------------------------------------#
         }
#-----------------------------------------------------------------------------------------------------------------------------------------#
}
#-----------------------------------------------------------------------------------------------------------------------------------------#
?>
<form name="RegForm" method="post">
<input type="hidden" name="SearchState"  value="<?=$SearchState?>" />
</form>

<script>
 document.RegForm.target = "_top";
 document.RegForm.action = "hr_staff_competency_list.php";
 document.RegForm.submit();
</script>

