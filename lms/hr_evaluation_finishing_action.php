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
#-----------------------------------------------------------------------------------------------------------------------------------------#


if ($SearchState) {
        // 먼저 업적평가 리스트를 가져와서 현재 있는 점수를 기반으로 성과평가 테이블에 입력한다.
            $ViweTable = "SELECT AAAA.* 
                            from Hr_OrganLevelTaskMembers AAAA 
                            inner join Members BBBB on AAAA.MemberID=BBBB.MemberID and BBBB.MemberState=1";
            $Sql2 = "SELECT 
                        A.*,
                        B.MemberName,
                        TT.*,
                        AA.MemberID as T_MemberID 
                    from ($ViweTable) A 
                        inner join Members B on A.MemberID=B.MemberID 
                        inner join Hr_Staff_Target TT on TT.MemberID=B.MemberID and TT.Hr_EvaluationID=".$SearchState." and TT.Hr_TargetState='9' 

                        inner join Hr_OrganLevels C on C.Hr_OrganLevelID=A.Hr_OrganLevelID 
                        left outer join Hr_OrganTask2 D on D.Hr_OrganTask2ID=A.Hr_OrganTask2ID 
                        left outer join Hr_OrganTask1 E on E.Hr_OrganTask1ID=D.Hr_OrganTask1ID

                        left outer join ($ViweTable) AA on AA.Hr_OrganLevel < A.Hr_OrganLevel
                        left outer join Members BB on AA.MemberID=BB.MemberID
                    Group By TT.MemberID";
            $Stmt2 = $DbConn->prepare($Sql2);

            $Stmt2->execute();

            $Stmt2->setFetchMode(PDO::FETCH_ASSOC);
            $line_cnt = 0;
            #-------------------------------------------------------------------------------------------------------------#
            while($Row2 = $Stmt2->fetch()) {
            #-------------------------------------------------------------------------------------------------------------#
                    $line_cnt++;
                    //=================== 자기 자신 ======================
                    $MemberID = $Row2["MemberID"];

                    $Hr_SelfTotalPoint = $Row2["Hr_SelfTotalPoint"  ];   // 자기합계
                    $Hr_ChangePoint    = $Row2["Hr_ChangePoint"    ];   // 환산점수
                    $Hr_TargetState    = $Row2["Hr_EvaluationState"];

                    $Hr_FirstTotalPoint   = $Row2["Hr_FirstTotalPoint" ];   // 1차상사 합계
                    $Hr_SecondTotalPoint  = $Row2["Hr_SecondTotalPoint"];   // 2차상사 합계
                    $Hr_EndTotalPoint     = $Row2["Hr_EndTotalPoint"   ];   // 3차상사 합계
                    //====================================================//
                    //=================== 업적평가계산식 ===================//
                    //====================================================//
                    $Hr_EvaluationLevel    = $Row2["Hr_EvaluationLevel"   ];     // 최종 업적 등급
                    $Hr_EndEvaluationPoint = $Row2["Hr_EndEvaluationPoint"];     // 최종 업적 점수

                    // 만약 최종 업적 점수가 null 이면 자기점수를 최종 업적 점수로 대체한다.
                    if ($Hr_EndEvaluationPoint==null || $Hr_EndEvaluationPoint == 0) {
                        $Hr_EvaluationLevel = "A";
                        $Hr_EndEvaluationPoint = $Hr_SelfTotalPoint;
                    }

                    // 성과평가 테이블에 값을 입력한다.
                    $Sql  = "SELECT count(*) TotalRowCount, Hr_EndEvaluationPoint from Hr_Staff_ResultEvaluation  
			                                      where Hr_EvaluationID=:Hr_EvaluationID and 
												        MemberID=:MemberID";
                    $Stmt = $DbConn->prepare($Sql);
                    $Stmt->bindParam(':Hr_EvaluationID', $SearchState);
                    $Stmt->bindParam(':MemberID',        $MemberID);
                    $Stmt->execute();
                    $Row = $Stmt->fetch();
                    $TotalRowCount = $Row["TotalRowCount"];
                    $RowEvaluationPoint = $Row["Hr_EndEvaluationPoint"];
                    if (!$TotalRowCount) {
                            $Sql = " INSERT into Hr_Staff_ResultEvaluation ( Hr_EvaluationID,  
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
                        if ($RowEvaluationPoint== 0 || $RowEvaluationPoint == null || $RowEvaluationPoint == "") {
                            $Sql = "UPDATE Hr_Staff_ResultEvaluation 
                                        set Hr_EvaluationLevel='".$Hr_EvaluationLevel."',
                                            Hr_EndEvaluationPoint=".$Hr_EndEvaluationPoint." 
                                    WHERE Hr_EvaluationID=:Hr_EvaluationID and 
                                            MemberID=:MemberID";
                            $Stmt = $DbConn->prepare($Sql);
                            $Stmt->bindParam(':Hr_EvaluationID', $SearchState);
                            $Stmt->bindParam(':MemberID',        $MemberID);
                            $Stmt->execute();
                            $Stmt = null;
                        }    
                    }
                    #-----------------------------------------------------------------------------------------------------------------#
                    # 최종평가 점수확정
                    #-----------------------------------------------------------------------------------------------------------------#
                    include('hr_inc_endpoint.php');
            }


            // 현재까지의 역량평가점수를 가져와서 성과평가 테이블에 입력
            $Sql3 = "SELECT A.MemberID, sum(A.Hr_EvaluationCompetencyAddTotalPoint) as AddTotalPoint_Hapge
											from Hr_EvaluationCompetencyMembers A 
											inner join Members B on A.MemberID=B.MemberID and B.MemberState=1
											where A.Hr_EvaluationID=:Hr_EvaluationID
											GROUP BY A.MemberID";
            $Stmt3 = $DbConn->prepare($Sql3);
            $Stmt3->bindParam(':Hr_EvaluationID', $SearchState);
            $Stmt3->execute();
            $Stmt3->setFetchMode(PDO::FETCH_ASSOC);
            $line_cnt = 0;
            #-------------------------------------------------------------------------------------------------------------#
            while($Row3 = $Stmt3->fetch()) {
            #-------------------------------------------------------------------------------------------------------------#
                $AddTotalPoint_Hapge   = $Row3["AddTotalPoint_Hapge" ];   // 현재까지의 역량평가 합계
                $MemberID  = $Row3["MemberID"];   // 멤버ID

                if ($AddTotalPoint_Hapge == "" || $AddTotalPoint_Hapge == null) $AddTotalPoint_Hapge = 0;

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
                $Sql = "SELECT count(*) TotalRowCount, Hr_EvaluationCompetencyEndPoint from Hr_Staff_ResultEvaluation  
                                                        where Hr_EvaluationID=:Hr_EvaluationID and 
                                                            MemberID=:MemberID";
                $Stmt = $DbConn->prepare($Sql);
                $Stmt->bindParam(':Hr_EvaluationID', $SearchState);
                $Stmt->bindParam(':MemberID',        $MemberID);
                $Stmt->execute();
                $Row = $Stmt->fetch();
                $TotalRowCount = $Row["TotalRowCount"];
                $RowEvaluationCompetencyEndPoint = $Row["Hr_EvaluationCompetencyEndPoint"];
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
                    if ($RowEvaluationCompetencyEndPoint==0 || $RowEvaluationCompetencyEndPoint == null || $RowEvaluationCompetencyEndPoint == ""){
                        $Sql = "UPDATE Hr_Staff_ResultEvaluation 
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
                }
                #-----------------------------------------------------------------------------------------------------------------#
                # 최종평가 점수확정
                #-----------------------------------------------------------------------------------------------------------------#
                include('hr_inc_endpoint.php');
            }
}    


if ($err_num != 0){
	include_once('./inc_header.php');
?>
<body>
<script>
alert("<?=$err_msg?>");
history.go(-1);
</script>
<?php
	include_once('./inc_footer.php'); 
?>
</body>
</html>
<?
}

include_once('../includes/dbclose.php');


?>

<body>
<script>
    alert("마감 처리했습니다.");
    window.location.href="hr_evaluation_finishing.php"; 
	exit;
</script>
</body>
