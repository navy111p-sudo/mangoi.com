<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

// 역량평가 점수를 다시 계산해서 등록하고, 최종 성과평가 데이터도 등록하기 위한 임시 프로그램

$Hr_EvaluationID = 18;
$SearchState = 18;

$Sql2 = "SELECT DISTINCT B.MemberID, B.MemberName 
				FROM Hr_EvaluationCompetencyMembers  A
				LEFT JOIN Members B ON A.Hr_EvaluationCompetencyMemberID = B.MemberID
				WHERE A.Hr_EvaluationID = :Hr_EvaluationID";
$Stmt2 = $DbConn->prepare($Sql2);
$Stmt2->bindParam(':Hr_EvaluationID', $Hr_EvaluationID);
$Stmt2->execute();

while($Row2 = $Stmt2->fetch()) {
#-------------------------------------------------------------------------------------------------------------------------#
                $MemberID = $Row2["MemberID"];
                $MemberName = $Row2["MemberName"];
                $Sql = " select * from Hr_EvaluationCompetencyMembers where Hr_EvaluationID=:Hr_EvaluationID and MemberID=:MemberID";
                $Stmt = $DbConn->prepare($Sql);
                $Stmt->bindParam(':Hr_EvaluationID', $Hr_EvaluationID);
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
						$Sql = "SELECT count(*) TotalRowCount from Hr_Staff_ResultEvaluation  
						                                      where Hr_EvaluationID=:Hr_EvaluationID and 
															        MemberID=:MemberID";
						$Stmt = $DbConn->prepare($Sql);
						$Stmt->bindParam(':Hr_EvaluationID', $SearchState);
						$Stmt->bindParam(':MemberID',        $MemberID);
						$Stmt->execute();
						$Row = $Stmt->fetch();
						$TotalRowCount = $Row["TotalRowCount"];
						if (!$TotalRowCount) {
								 $Sql = "INSERT into Hr_Staff_ResultEvaluation ( ";
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
								$Sql = "UPDATE Hr_Staff_ResultEvaluation 
											set Hr_EvaluationCompetencyAddTotalPoint=".$AddTotalPoint_Hapge.",
												Hr_EvaluationCompetencyLevel='".$Hr_EvaluationCompetencyLevel."',
												Hr_EvaluationCompetencyEndPoint=".$Hr_EvaluationCompetencyEndPoint." 
										  where Hr_EvaluationID=:Hr_EvaluationID and 
											    MemberID=:MemberID";
								$Stmt = $DbConn->prepare($Sql);
								$Stmt->bindParam(':Hr_EvaluationID', $Hr_EvaluationID);
								$Stmt->bindParam(':MemberID',        $MemberID);
								$Stmt->execute();
								$Stmt = null;
						}
						#-----------------------------------------------------------------------------------------------------------------#
						# 최종평가 점수확정
						#-----------------------------------------------------------------------------------------------------------------#
						include('hr_inc_endpoint.php');

                        echo "업데이트완료:".$MemberName."(".$MemberID.")\n";
           }
}
#-----------------------------------------------------------------------------------------------------------------------------------------#
?>
