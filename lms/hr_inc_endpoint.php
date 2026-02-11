<?
            $Sql = " SELECT Hr_EndEvaluationPoint, Hr_EvaluationCompetencyEndPoint 
						   from Hr_Staff_ResultEvaluation  
						  where Hr_EvaluationID=:Hr_EvaluationID and 
								MemberID=:MemberID and
								(Hr_EndEvaluationPoint is NOT NULL or 
								Hr_EvaluationCompetencyEndPoint is NOT NULL)";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':Hr_EvaluationID', $SearchState);
			$Stmt->bindParam(':MemberID',        $MemberID);
			$Stmt->execute();
			$Row = $Stmt->fetch();
			$Hr_EndEvaluationPoint           = $Row["Hr_EndEvaluationPoint"          ];   // 업적최종점수
			$Hr_EvaluationCompetencyEndPoint = $Row["Hr_EvaluationCompetencyEndPoint"];   // 역량최종점수

			if ($Hr_EndEvaluationPoint > 0 || $Hr_EvaluationCompetencyEndPoint > 0) {

					$Hr_ResultPoint1     = round($Hr_EndEvaluationPoint * 0.7);           // 업적최종점수 * 70% 가중치 
					$Hr_ResultPoint2     = round($Hr_EvaluationCompetencyEndPoint * 0.3); // 역량최종점수 * 30% 가중치
					$Hr_ResultTotalPoint = $Hr_ResultPoint1 + $Hr_ResultPoint2;
					$Hr_ResultLevel      = "";
					if ($Hr_ResultTotalPoint >= 110) {
						   $Hr_ResultLevel = "S";
					} else if ($Hr_ResultTotalPoint >= 100 and $Hr_ResultTotalPoint < 110) {
						   $Hr_ResultLevel = "A";
					} else if ($Hr_ResultTotalPoint >= 90 and $Hr_ResultTotalPoint < 100) {
						   $Hr_ResultLevel = "B";
					} else if ($Hr_ResultTotalPoint >= 80 and $Hr_ResultTotalPoint < 90) {
						   $Hr_ResultLevel = "C";
					} else if ($Hr_ResultTotalPoint > 0 and $Hr_ResultTotalPoint < 80) {
						   $Hr_ResultLevel = "D";
					} 
						 
					$Sql = " UPDATE Hr_Staff_ResultEvaluation 
								set Hr_ResultPoint1=".$Hr_ResultPoint1.",
									Hr_ResultPoint2=".$Hr_ResultPoint2.",
									Hr_ResultTotalPoint=".$Hr_ResultTotalPoint.",
									Hr_ResultLevel='".$Hr_ResultLevel."' 
							  where Hr_EvaluationID=:Hr_EvaluationID and 
									MemberID=:MemberID";
					$Stmt = $DbConn->prepare($Sql);
					$Stmt->bindParam(':Hr_EvaluationID', $SearchState);
					$Stmt->bindParam(':MemberID',        $MemberID);
					$Stmt->execute();
					$Stmt = null;
			}
?>            