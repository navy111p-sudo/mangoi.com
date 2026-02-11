<?php
include_once('../includes/dbopen.php');
include_once('./includes/common.php');

$DownMenu       = isset($_REQUEST["DownMenu"   ]) ? $_REQUEST["DownMenu"   ] : "";
$SearchState    = isset($_REQUEST["SearchState"]) ? $_REQUEST["SearchState"] : "";

$excel_filename = "MANGOI_DOWNLOAD-".$DownMenu."-".$SearchState;
#=====================================================================================================================#
if ($DownMenu==1) { 

         $excel_filename = "성과평가_최종결과_";

		 $Sql  = "select * from Hr_Evaluations where Hr_EvaluationID=".$SearchState;
         $Stmt = $DbConn->prepare($Sql);
		 $Stmt->execute();
         $Stmt->setFetchMode(PDO::FETCH_ASSOC);
         $Row = $Stmt->fetch();
		 if ($Row) {
				$excel_filename .= $Row["Hr_EvaluationYear"]."년".substr("0".$Row["Hr_EvaluationMonth"],-2)."월";
		 }   

} else if ($DownMenu==2) { 

         $excel_filename = "원데이터(5점만점)_";
		 $Sql  = "select * from Hr_Evaluations where Hr_EvaluationID=".$SearchState;
         $Stmt = $DbConn->prepare($Sql);
		 $Stmt->execute();
         $Stmt->setFetchMode(PDO::FETCH_ASSOC);
         $Row = $Stmt->fetch();
		 if ($Row) {
				$excel_filename .= $Row["Hr_EvaluationYear"]."년".substr("0".$Row["Hr_EvaluationMonth"],-2)."월";
		 } 
		 
}
#=====================================================================================================================#

// $excelnm = iconv('UTF-8','EUC-KR',$excel_filename.'.xls'); 
// header("Content-type: application/vnd.ms-excel");
// header("Content-type: application/vnd.ms-excel; charset=utf-8");
// header("Content-Disposition: attachment;filename=\"" . $excelnm . "\"");  


// header( "Content-type: application/vnd.ms-excel; charset=euc-kr"); 
// header( "Content-Description: PHP4 Generated Data" ); 
// header( "Content-Disposition: attachment; filename=".$excel_filename.".xls" );
// print("<meta http-equiv=\"Content-Type\" content=\"application/vnd.ms-excel; charset=euc-kr\">");
header("Content-type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=customs_report_list_".$excel_filename.".xls" );
header("Content-Description: PHP4 Generated Data");
header("Pragma: no-cache");
header("Expires: 0");
print("<meta http-equiv=\"Content-Type\" content=\"application/vnd.ms-excel; charset=utf-8\">");

#=====================================================================================================================#
if ($DownMenu==1 and $SearchState) { 
#=====================================================================================================================#
		?>       
		<table border="1">
			<thead>
				<tr>
					<th colspan=6>성과평가 최종결과</th>
				</tr>
				<tr>
					<th>No</th>
					<th>성명</th>
					<th>업적평가 점수</th>
					<th>역량평가 점수</th>
					<th>성과평가 종합점수</th>
					<th>성과평가 종합평가 등급</th>
				</tr>
			</thead>
			<tbody>
        <?php
		$List_Cnt = 0;
		#-------------------------------------------------------------------------------------------------------------#
		$Sql = "select SR.*,MM.MemberName from Hr_Staff_ResultEvaluation as SR
				   inner join Members MM on MM.MemberID=SR.MemberID
						where SR.Hr_EvaluationID=".$SearchState." and
							  SR.Hr_ResultTotalPoint is NOT NULL";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		#-------------------------------------------------------------------------------------------------------------#
		while ($Row = $Stmt->fetch())  {
		#-------------------------------------------------------------------------------------------------------------#
			   $List_Cnt++;
			   ?>
			<tr>
			   <td><?=$List_Cnt?></td>
			   <td><?=$Row["MemberName"]?></td>
			   <td><?=$Row["Hr_EndEvaluationPoint"]?></td>
			   <td><?=$Row["Hr_EvaluationCompetencyEndPoint"]?></td>
			   <td><?=$Row["Hr_ResultTotalPoint"]?></td>
			   <td><?=$Row["Hr_ResultLevel"]?></td>
			</tr>
			   <?php
		#-------------------------------------------------------------------------------------------------------------#
		}
		#-------------------------------------------------------------------------------------------------------------#
		$Stmt = null;
		#-------------------------------------------------------------------------------------------------------------#
        ?>
			</tbody>
		</table>
<?php
#=====================================================================================================================#
} else if ($DownMenu==2 and $SearchState) { 
#=====================================================================================================================#
		$Sql  = "select count(*) TotalRowCount from Hr_CompetencyIndicatorCate1 A  where A.Hr_CompetencyIndicatorCate1State=1";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;

		$TotalRowCount = $Row["TotalRowCount"];
		?>
		<table border=1>
			<thead>
				<tr>
					<th nowrap colspan="4">평가대상자</th>
					<th nowrap colspan="5">평가자</th>
					<?
					if ($TotalRowCount > 1) {

							$Sql = "select A.* from Hr_CompetencyIndicatorCate1 A 
											  where A.Hr_CompetencyIndicatorCate1State=1  
										   order by A.Hr_CompetencyIndicatorCate1Order asc"; 
							$Stmt = $DbConn->prepare($Sql);
							$Stmt->execute();
							$Stmt->setFetchMode(PDO::FETCH_ASSOC);
							while($Row = $Stmt->fetch()) {
								$Hr_CompetencyIndicatorCate1ID    = $Row["Hr_CompetencyIndicatorCate1ID"];
								$Hr_CompetencyIndicatorCate1Name  = $Row["Hr_CompetencyIndicatorCate1Name"];

								$Sql2 = "select A.* from Hr_CompetencyIndicatorCate2 A 
													where A.Hr_CompetencyIndicatorCate1ID=:Hr_CompetencyIndicatorCate1ID
													order by A.Hr_CompetencyIndicatorCate2Order asc";
								$Stmt2 = $DbConn->prepare($Sql2);
								$Stmt2->bindParam(':Hr_CompetencyIndicatorCate1ID', $Hr_CompetencyIndicatorCate1ID);
								$Stmt2->execute();
								$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
								$TotalRowCount2 = 0;
								while($Row2 = $Stmt2->fetch()) {
									  $Hr_CompetencyIndicatorCate2ID   = $Row2["Hr_CompetencyIndicatorCate2ID"];

									  $Sql3  = "select count(*) TotalRowCount3 from Hr_CompetencyIndicators A 
																			  where A.Hr_CompetencyIndicatorCate2ID=:Hr_CompetencyIndicatorCate2ID "; 
									  $Stmt3 = $DbConn->prepare($Sql3);
									  $Stmt3->bindParam(':Hr_CompetencyIndicatorCate2ID', $Hr_CompetencyIndicatorCate2ID);
									  $Stmt3->execute();
									  $Stmt3->setFetchMode(PDO::FETCH_ASSOC);
									  $Row3  = $Stmt3->fetch();
									  $Stmt3 = null;
									  $TotalRowCount2 = $TotalRowCount2 + $Row3["TotalRowCount3"] + 1;
											 
								}
								?> 
					<th nowrap colspan="<?=$TotalRowCount2?>"><?=$Hr_CompetencyIndicatorCate1Name?></th>
								<?
							} 
								
					} else {

							?>
					<th nowrap style="border-bottom:0px;">비고</th>
							<?

					}
					?>
					<th rowspan=3>전체역량평균</th>
				</tr>
				<tr>
					<th rowspan=2>번호</th>
					<th rowspan=2>성명</th>
					<th rowspan=2>직급_직책</th>
					<th rowspan=2>직무</th>
					<th rowspan=2>성명</th>
					<th rowspan=2>유형</th>
					<th rowspan=2>직무</th>
					<th rowspan=2>가중치(%)</th>
					<th rowspan=2>가중치반영점수</th>
					<?
					$Sql = "select A.* from Hr_CompetencyIndicatorCate1 A 
									  where A.Hr_CompetencyIndicatorCate1State=1  
								   order by A.Hr_CompetencyIndicatorCate1Order asc"; 
					$Stmt = $DbConn->prepare($Sql);
					$Stmt->execute();
					$Stmt->setFetchMode(PDO::FETCH_ASSOC);
					while($Row = $Stmt->fetch()) {

						$Hr_CompetencyIndicatorCate1ID    = $Row["Hr_CompetencyIndicatorCate1ID"];
						
						$Sql2 = "select A.* from Hr_CompetencyIndicatorCate2 A 
											where A.Hr_CompetencyIndicatorCate1ID=:Hr_CompetencyIndicatorCate1ID
											order by A.Hr_CompetencyIndicatorCate2Order asc";
						$Stmt2 = $DbConn->prepare($Sql2);
						$Stmt2->bindParam(':Hr_CompetencyIndicatorCate1ID', $Hr_CompetencyIndicatorCate1ID);
						$Stmt2->execute();
						$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
						while($Row2 = $Stmt2->fetch()) {
							  $Hr_CompetencyIndicatorCate2ID   = $Row2["Hr_CompetencyIndicatorCate2ID"];
							  $Hr_CompetencyIndicatorCate2Name = $Row2["Hr_CompetencyIndicatorCate2Name"];

							  $Sql3  = "select count(*) TotalRowCount3 from Hr_CompetencyIndicators A 
																	  where A.Hr_CompetencyIndicatorCate2ID=:Hr_CompetencyIndicatorCate2ID "; 
							  $Stmt3 = $DbConn->prepare($Sql3);
							  $Stmt3->bindParam(':Hr_CompetencyIndicatorCate2ID', $Hr_CompetencyIndicatorCate2ID);
							  $Stmt3->execute();
							  $Stmt3->setFetchMode(PDO::FETCH_ASSOC);
							  $Row3  = $Stmt3->fetch();
							  $Stmt3 = null;
							  $TotalRowCount3 = $Row3["TotalRowCount3"] + 1;
							  ?> 
					<th nowrap colspan="<?=$TotalRowCount3?>"><?=$Hr_CompetencyIndicatorCate2Name?></th>
							  <?
						}
					} 
					?>
				</tr>
				<tr>
					<?
					$Snno = 0;

					$Sql = "select A.* from Hr_CompetencyIndicatorCate1 A 
									  where A.Hr_CompetencyIndicatorCate1State=1  
								   order by A.Hr_CompetencyIndicatorCate1Order asc"; 
					$Stmt = $DbConn->prepare($Sql);
					$Stmt->execute();
					$Stmt->setFetchMode(PDO::FETCH_ASSOC);
					while($Row = $Stmt->fetch()) {

						$Hr_CompetencyIndicatorCate1ID    = $Row["Hr_CompetencyIndicatorCate1ID"];
						
						$Sql2 = "select A.* from Hr_CompetencyIndicatorCate2 A 
											where A.Hr_CompetencyIndicatorCate1ID=:Hr_CompetencyIndicatorCate1ID
											order by A.Hr_CompetencyIndicatorCate2Order asc";
						$Stmt2 = $DbConn->prepare($Sql2);
						$Stmt2->bindParam(':Hr_CompetencyIndicatorCate1ID', $Hr_CompetencyIndicatorCate1ID);
						$Stmt2->execute();
						$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
						while($Row2 = $Stmt2->fetch()) {

							  $Hr_CompetencyIndicatorCate2ID   = $Row2["Hr_CompetencyIndicatorCate2ID"];
							  $Hr_CompetencyIndicatorCate2Name = $Row2["Hr_CompetencyIndicatorCate2Name"];

							  $Sql3  = "select A.* from Hr_CompetencyIndicators A 
												  where A.Hr_CompetencyIndicatorCate2ID=:Hr_CompetencyIndicatorCate2ID
												  order by A.Hr_CompetencyIndicatorOrder asc"; 
							  $Stmt3 = $DbConn->prepare($Sql3);
							  $Stmt3->bindParam(':Hr_CompetencyIndicatorCate2ID', $Hr_CompetencyIndicatorCate2ID);
							  $Stmt3->execute();
							  while($Row3 = $Stmt3->fetch()) {
								   $Snno++;         
								   $Hr_CompetencyIndicatorID3 = $Row3["Hr_CompetencyIndicatorID"];
								   ?>
					<th nowrap><?=$Snno?></th>
								   <? 
							  }
							  ?>
					<th nowrap>평균</th>
							  <?
						}
					}
					?>
				</tr>
			</thead>
			<tbody>
		<?
		#-------------------------------------------------------------------------------------------------------------#
		$ViweTable2 = "select AAAAA.* from Hr_OrganLevelTaskMembers AAAAA 
									inner join Members BBBBB on AAAAA.MemberID=BBBBB.MemberID and BBBBB.MemberState=1";
		$ViweTable = "select AAAA.* from Hr_EvaluationCompetencyMembers AAAA 
									inner join Members BBBB on AAAA.MemberID=BBBB.MemberID and BBBB.MemberState=1";
		$Sql = "select count(*) TotalRowCount from ($ViweTable) A 
										inner join Members B on A.MemberID=B.MemberID 
										left outer join ($ViweTable2) A_1 on A.MemberID=A_1.MemberID 
										left outer join Hr_OrganLevels C on A_1.Hr_OrganLevelID=C.Hr_OrganLevelID 
										left outer join Hr_OrganTask2 D on A_1.Hr_OrganTask2ID=D.Hr_OrganTask2ID 
										left outer join Hr_OrganTask1 E on D.Hr_OrganTask1ID=E.Hr_OrganTask1ID 

										left outer join ($ViweTable) AA on A.Hr_EvaluationCompetencyMemberID=AA.MemberID 
										left outer join Members BB on AA.MemberID=BB.MemberID and BB.MemberState=1 
										left outer join ($ViweTable2) AA_1 on AA.MemberID=AA_1.MemberID 
										left outer join Hr_OrganLevels CC on AA_1.Hr_OrganLevelID=CC.Hr_OrganLevelID 
										left outer join Hr_OrganTask2 DD on AA_1.Hr_OrganTask2ID=DD.Hr_OrganTask2ID 
										left outer join Hr_OrganTask1 EE on DD.Hr_OrganTask1ID=EE.Hr_OrganTask1ID 
												  where A.Hr_EvaluationID=".$SearchState;
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;

		$TotalRowCount = $Row["TotalRowCount"];

		$Sql = "select  A.*,
						A_1.Hr_OrganLevel,
						A_1.Hr_OrganPositionName,
						A_1.Hr_OrganLevelID,
						A_1.Hr_OrganTask2ID,
						B.MemberName,

						ifnull(D.Hr_OrganTask2Name, '미지정') as Hr_OrganTask2Name,
						ifnull(E.Hr_OrganTask1ID,   ''    ) as Hr_OrganTask1ID,
						ifnull(E.Hr_OrganTask1Name, '미지정') as Hr_OrganTask1Name,

						ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=C.Hr_OrganLevel1ID), '') as Hr_OrganLevelName1, 
						ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=C.Hr_OrganLevel2ID), '') as Hr_OrganLevelName2,
						ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=C.Hr_OrganLevel3ID), '') as Hr_OrganLevelName3,
						ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=C.Hr_OrganLevel4ID), '') as Hr_OrganLevelName4,

						BB.MemberID as T_MemberID,
						AA_1.Hr_OrganLevel as T_Hr_OrganLevel,
						ifnull(AA_1.Hr_OrganPositionName, '미지정') as T_Hr_OrganPositionName,
						BB.MemberName as T_MemberName,

						ifnull(DD.Hr_OrganTask2Name,'미지정') as T_Hr_OrganTask2Name,
						ifnull(EE.Hr_OrganTask1Name,'미지정') as T_Hr_OrganTask1Name,
						ifnull(DD.Hr_OrganTask2ID,  ''    ) as T_Hr_OrganTask2ID,
						ifnull(EE.Hr_OrganTask1ID,  ''    ) as T_Hr_OrganTask1ID,

						ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=CC.Hr_OrganLevel1ID), '') as T_Hr_OrganLevelName1, 
						ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=CC.Hr_OrganLevel2ID), '') as T_Hr_OrganLevelName2,
						ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=CC.Hr_OrganLevel3ID), '') as T_Hr_OrganLevelName3,
						ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=CC.Hr_OrganLevel4ID), '') as T_Hr_OrganLevelName4,

						(select count(*) from Members VVVV where VVVV.MemberID in (select VVVVV.Hr_EvaluationCompetencyMemberID from ($ViweTable) VVVVV where MemberID=A.MemberID ) ) as TM_MemberCount,

						( (select count(*) from ($ViweTable) VVVVV where VVVVV.MemberID=A.MemberID and VVVVV.Hr_EvaluationID=".$SearchState." ) ) as T_MemberCount

					from ($ViweTable) A 

						inner join Members B on A.MemberID=B.MemberID 
						left outer join ($ViweTable2) A_1 on A.MemberID=A_1.MemberID 
						left outer join Hr_OrganLevels C on A_1.Hr_OrganLevelID=C.Hr_OrganLevelID 
						left outer join Hr_OrganTask2 D on A_1.Hr_OrganTask2ID=D.Hr_OrganTask2ID 
						left outer join Hr_OrganTask1 E on D.Hr_OrganTask1ID=E.Hr_OrganTask1ID 

						left outer join Members BB on A.Hr_EvaluationCompetencyMemberID=BB.MemberID and BB.MemberState=1 
						left outer join ($ViweTable2) AA_1 on BB.MemberID=AA_1.MemberID 
						left outer join Hr_OrganLevels CC on AA_1.Hr_OrganLevelID=CC.Hr_OrganLevelID 
						left outer join Hr_OrganTask2 DD on AA_1.Hr_OrganTask2ID=DD.Hr_OrganTask2ID 
						left outer join Hr_OrganTask1 EE on DD.Hr_OrganTask1ID=EE.Hr_OrganTask1ID 

					where A.Hr_EvaluationID=".$SearchState." 
					order by A.MemberID asc, 
						A_1.Hr_OrganLevel asc, A.Hr_EvaluationCompetencyMemberType desc, C.Hr_OrganLevel1ID asc, C.Hr_OrganLevel2ID asc, C.Hr_OrganLevel3ID asc, C.Hr_OrganLevel4ID asc, AA_1.Hr_OrganLevel asc";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);

			$ListCount   = 0;
			$ListCount2  = 0;
			$OldMemberID = 0;
			$H_AddValue  = 0;
			$H_AddTotalPoint = 0;

			while($Row = $Stmt->fetch()) {
					
					$ListCount ++;

					//=================== 자기 자신 ======================
					$Hr_EvaluationCompetencyMemberType = $Row["Hr_EvaluationCompetencyMemberType"];
					
					$MemberID = $Row["MemberID"];

					$Hr_OrganLevel = $Row["Hr_OrganLevel"];
					$Hr_OrganLevelID = $Row["Hr_OrganLevelID"];
					$Hr_OrganTask1ID = $Row["Hr_OrganTask1ID"];
					$Hr_OrganTask2ID = $Row["Hr_OrganTask2ID"];
					$Hr_OrganPositionName = $Row["Hr_OrganPositionName"];

					$MemberName = $Row["MemberName"];

					$Hr_OrganTask2Name = $Row["Hr_OrganTask2Name"];
					$Hr_OrganTask1Name = $Row["Hr_OrganTask1Name"];


					$Hr_OrganLevelName1 = $Row["Hr_OrganLevelName1"];
					$Hr_OrganLevelName2 = $Row["Hr_OrganLevelName2"];
					$Hr_OrganLevelName3 = $Row["Hr_OrganLevelName3"];
					$Hr_OrganLevelName4 = $Row["Hr_OrganLevelName4"];

					$Str_Hr_OrganLevelName = $Hr_OrganLevelName1;
					if ($Hr_OrganLevelName2!=""){
						$Str_Hr_OrganLevelName .= " > " . $Hr_OrganLevelName2;
					}
					if ($Hr_OrganLevelName3!=""){
						$Str_Hr_OrganLevelName .= " > " . $Hr_OrganLevelName3;
					}
					if ($Hr_OrganLevelName4!=""){
						$Str_Hr_OrganLevelName .= " > " . $Hr_OrganLevelName4;
					}

					$Str_OrganTaskName = $Hr_OrganTask1Name;
					if ($Hr_OrganTask2Name!=""){
						$Str_OrganTaskName .= " > " . $Hr_OrganTask2Name;
					}

					//1:부하 2:동료 3:상사 4:고객 5:본인
					if ($Hr_EvaluationCompetencyMemberType==1){
						$Str_Hr_EvaluationCompetencyMemberType = "부하";
					}else if ($Hr_EvaluationCompetencyMemberType==2){
						$Str_Hr_EvaluationCompetencyMemberType = "동료";
					}else if ($Hr_EvaluationCompetencyMemberType==3){
						$Str_Hr_EvaluationCompetencyMemberType = "상사";
					}else if ($Hr_EvaluationCompetencyMemberType==4){
						$Str_Hr_EvaluationCompetencyMemberType = "고객";
					}else if ($Hr_EvaluationCompetencyMemberType==5){
						$Str_Hr_EvaluationCompetencyMemberType = "본인";
					}
					//=================== 자기 자신 ======================

					//=================== 동 료 ======================
					$T_MemberID = $Row["T_MemberID"];

					$T_Hr_OrganLevel = $Row["T_Hr_OrganLevel"];
					$T_Hr_OrganPositionName = $Row["T_Hr_OrganPositionName"];

					$T_MemberName = $Row["T_MemberName"];

					$T_Hr_OrganTask2Name = $Row["T_Hr_OrganTask2Name"];
					$T_Hr_OrganTask1Name = $Row["T_Hr_OrganTask1Name"];
					$T_Hr_OrganTask2ID   = $Row["T_Hr_OrganTask2ID"];
					$T_Hr_OrganTask1ID   = $Row["T_Hr_OrganTask1ID"];


					$T_Hr_OrganLevelName1 = $Row["T_Hr_OrganLevelName1"];
					$T_Hr_OrganLevelName2 = $Row["T_Hr_OrganLevelName2"];
					$T_Hr_OrganLevelName3 = $Row["T_Hr_OrganLevelName3"];
					$T_Hr_OrganLevelName4 = $Row["T_Hr_OrganLevelName4"];

					$T_Str_Hr_OrganLevelName = $T_Hr_OrganLevelName1;
					if ($T_Hr_OrganLevelName2!=""){
						$T_Str_Hr_OrganLevelName .= " > " . $T_Hr_OrganLevelName2;
					}
					if ($T_Hr_OrganLevelName3!=""){
						$T_Str_Hr_OrganLevelName .= " > " . $T_Hr_OrganLevelName3;
					}
					if ($T_Hr_OrganLevelName4!=""){
						$T_Str_Hr_OrganLevelName .= " > " . $T_Hr_OrganLevelName4;
					}


					$T_Str_OrganTaskName = $T_Hr_OrganTask1Name;
					if ($T_Hr_OrganTask2Name!=""){
						$T_Str_OrganTaskName .= " > " . $T_Hr_OrganTask2Name;
					}
					
					
					//=================== 동 료 ======================

					$T_MemberCount = $Row["T_MemberCount"];
					$TT_MemberCount = $Row["T_MemberCount"];
					/*
					if ($T_MemberCount==0){
						  $T_MemberCount = 1;
					} else {
						  $T_MemberCount = $T_MemberCount + 1; 
					}
					*/
					$T_AddValue      = $Row["Hr_EvaluationCompetencyAddValue"];
					$T_AddTotalPoint = $Row["Hr_EvaluationCompetencyAddTotalPoint"];
					
					$PrintMember = 0;
					if ($OldMemberID!=$MemberID){
						/*
						if ($H_AddValue > 0) { 
						?>
				<tr>
					<td colspan="3" class="uk-text-nowrap uk-table-td-right"><?=$평가자_가중치_소계</td>
					<td ><b style="color:#7CB342; font-size:1.1em;"><?=$H_AddValue?>%</b></td>
					<td ><?=iif($H_AddTotalPoint > 0,"".number_format($H_AddTotalPoint,2)."","-")?></td>
					<td ></td>
				</tr>
						<?php
						}
						*/
						$OldMemberID = $MemberID;
						$PrintMember = 1;
						$ListCount2++;

						$H_AddValue  = 0; 
						$H_AddTotalPoint = 0;
					}

					$H_AddValue      = $H_AddValue + $T_AddValue; 
					$H_AddTotalPoint = $H_AddTotalPoint + $T_AddTotalPoint; 
				?>
				<tr>
					<?if ($PrintMember==1){?>
						<td rowspan="<?=$T_MemberCount?>"><?=$ListCount2?></td>
						<td rowspan="<?=$T_MemberCount?>"><?=$MemberName?></td>
						<td rowspan="<?=$T_MemberCount?>"><?=$Hr_OrganPositionName?></td>
						<td rowspan="<?=$T_MemberCount?>"><?=$Str_OrganTaskName?></td>
					<?}?>

					<td ><?=$T_MemberName?></td>
					<td ><?=$Str_Hr_EvaluationCompetencyMemberType?></td>
					<td ><?=$T_Str_OrganTaskName?></td>
					<td ><?=$T_AddValue?>%</td>
					<td ><?=iif($T_AddTotalPoint > 0,"".number_format($T_AddTotalPoint,2)."","-")?></td>

					<?
					$CPTOT_CNT = 0;
					$CPTOT_HAP = 0;

					$Sql1 = "select A.* from Hr_CompetencyIndicatorCate1 A 
									  where A.Hr_CompetencyIndicatorCate1State=1  
								   order by A.Hr_CompetencyIndicatorCate1Order asc"; 
					$Stmt1 = $DbConn->prepare($Sql1);
					$Stmt1->execute();
					$Stmt1->setFetchMode(PDO::FETCH_ASSOC);
					while($Row1 = $Stmt1->fetch()) {

						$Hr_CompetencyIndicatorCate1ID = $Row1["Hr_CompetencyIndicatorCate1ID"];
						
						$Sql2 = "select A.* from Hr_CompetencyIndicatorCate2 A 
											where A.Hr_CompetencyIndicatorCate1ID=:Hr_CompetencyIndicatorCate1ID
											order by A.Hr_CompetencyIndicatorCate2Order asc";
						$Stmt2 = $DbConn->prepare($Sql2);
						$Stmt2->bindParam(':Hr_CompetencyIndicatorCate1ID', $Hr_CompetencyIndicatorCate1ID);
						$Stmt2->execute();
						$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
						while($Row2 = $Stmt2->fetch()) {

							  $Hr_CompetencyIndicatorCate2ID   = $Row2["Hr_CompetencyIndicatorCate2ID"];

							  $Sql3  = "select A.* from Hr_CompetencyIndicators A 
												  where A.Hr_CompetencyIndicatorCate2ID=:Hr_CompetencyIndicatorCate2ID
												  order by A.Hr_CompetencyIndicatorOrder asc"; 
							  $Stmt3 = $DbConn->prepare($Sql3);
							  $Stmt3->bindParam(':Hr_CompetencyIndicatorCate2ID', $Hr_CompetencyIndicatorCate2ID);
							  $Stmt3->execute();
							  $CP_CNT = 0;
							  $CP_HAP = 0;
							  while($Row3 = $Stmt3->fetch()) {
										
									$Hr_CompetencyIndicatorID3 = $Row3["Hr_CompetencyIndicatorID"];
								   
									#-------------------------------------------------------------------------------------------------------------#
									$Sql4 = "select A.Hr_CompetencyIndicatorPoint as CPPoint 
												 from Hr_Staff_Compentency A 
												where A.Hr_CompetencyIndicatorID=".$Hr_CompetencyIndicatorID3." and
													  A.MyMemberID=".$T_MemberID." and 
													  A.MemberID=".$MemberID." and
													  A.Hr_EvaluationID=".$SearchState." and 
													  A.Hr_OrganTask1ID=".$Hr_OrganTask1ID." and 
													  A.Hr_OrganTask2ID=".$Hr_OrganTask2ID;

									$Stmt4 = $DbConn->prepare($Sql4);
									$Stmt4->execute();
									$Stmt4->setFetchMode(PDO::FETCH_ASSOC);
									$Row4 = $Stmt4->fetch();
									$CPPoint = 0;
									if ($Row4) {
										  $CPPoint   = $Row4["CPPoint"];
										  $CP_HAP    = $CP_HAP + $Row4["CPPoint"];
										  $CPTOT_HAP = $CPTOT_HAP + $Row4["CPPoint"];
										  $CPTOT_CNT++;
										  $CP_CNT++;
									}
									#-------------------------------------------------------------------------------------------------------------#
									?>
					<td align="center"><?=iif($CPPoint>0,$CPPoint,"")?></td>
								   <? 
							  }
							  $CP_AVG = 0;
							  if ($CP_HAP > 0) {
								   $CP_AVG = $CP_HAP / $CP_CNT;
							  }
							  ?>
					<td align="center"><?=iif($CP_AVG>0,"".number_format($CP_AVG,2)."","")?></td>
							  <?
						}
					}
					$CPTOT_AVG = 0;
					if ($CPTOT_HAP > 0) {
						   $CPTOT_AVG = $CPTOT_HAP / $CPTOT_CNT;
					}
					?>
					<td align="center"><?=iif($CPTOT_AVG>0,"".number_format($CPTOT_AVG,2)."","")?></td>
					
				</tr>

			<?php
			}
			/*
			if ($H_AddValue > 0) { 
			?>
				<tr>
					<td colspan="3" class="uk-text-nowrap uk-table-td-right"><?=$평가자_가중치_소계</td>
					<td ><b style="color:#7CB342; font-size:1.1em;"><?=$H_AddValue?>%</b></td>
					<td ><?=iif($H_AddTotalPoint > 0,"".$H_AddTotalPoint."","-")?></td>
				</tr>
			<?php
			}
			*/
			$Stmt = null;
			?>
			</tbody>
		</table>
<?
#=====================================================================================================================#
} 
#=====================================================================================================================#
?>