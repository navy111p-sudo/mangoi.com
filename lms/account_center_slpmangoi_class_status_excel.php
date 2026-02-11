<?php
$SearchYear = isset($_REQUEST["SearchYear"]) ? $_REQUEST["SearchYear"] : "";
$SearchMonth = isset($_REQUEST["SearchMonth"]) ? $_REQUEST["SearchMonth"] : "";

header( "Content-type: application/vnd.ms-excel; charset=utf-8");
header( "Content-Disposition: attachment; filename = SLPMANGOI-CLASS-STATUS-".$SearchYear."-".$SearchMonth.".xls" );
header( "Content-Description: PHP4 Generated Data" );

include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');


$SLP_BranchID_0=42;//gangseo
$SLP_BranchID_1=107;//seodaemoon
$SLP_BranchID_2=113;//slp
$SLP_BranchID_3=114;//soowon


$AddSqlWhere = "1=1";

$SearchYear = isset($_REQUEST["SearchYear"]) ? $_REQUEST["SearchYear"] : "";
$SearchMonth = isset($_REQUEST["SearchMonth"]) ? $_REQUEST["SearchMonth"] : "";

if ($SearchYear==""){
	$SearchYear = date("Y");
}

if ($SearchMonth==""){
	$SearchMonth = date("m");
}

$AddSqlWhere = $AddSqlWhere . " and A.MemberState=1 ";
$AddSqlWhere = $AddSqlWhere . " and A.MemberLevelID=19 ";
$AddSqlWhere = $AddSqlWhere . " and (
										C.BranchID=".$SLP_BranchID_0."
										or 
										C.BranchID=".$SLP_BranchID_1."
										or 
										C.BranchID=".$SLP_BranchID_2."
										or 
										C.BranchID=".$SLP_BranchID_3."
									) ";

$AddSqlWhere = $AddSqlWhere . " and A.MemberID 
										in (
												select 
													AA.MemberID 
												from Classes AA 
													inner join ClassOrders BB on AA.ClassOrderID=BB.ClassOrderID
												where 
													AA.ClassState=2 
													and AA.StartYear=".$SearchYear." 
													and AA.StartMonth=".$SearchMonth." 
													and (AA.ClassAttendState=1 or AA.ClassAttendState=2 or AA.ClassAttendState=3) 
													and BB.ClassProductID=1 
											) 
								";

$Sql = "select 
			count(*) as TotalRowCount
		from Members A 
			inner join Centers B on A.CenterID=B.CenterID 
			inner join Members BB on B.CenterID=BB.CenterID and BB.MemberLevelID=12 
			inner join Branches C on B.BranchID=C.BranchID 
			inner join Members CC on C.BranchID=CC.BranchID and CC.MemberLevelID=9
		where 
			".$AddSqlWhere." ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TotalRowCount = $Row["TotalRowCount"];


$Sql = "select 
			A.*,
			B.CenterName,
			BB.MemberLoginID as CenterLoginID, 
			C.BranchName,
			CC.MemberLoginID as BranchLoginID,
			(select count(*) FROM Classes AA inner join ClassOrders BB on AA.ClassOrderID=BB.ClassOrderID where AA.ClassState=2 and AA.MemberID=A.MemberID and AA.ClassAttendState<>99 and BB.ClassProductID=1 and AA.StartYear=".$SearchYear." and AA.StartMonth=".$SearchMonth.") as TotalClassCount,
			(select count(*) FROM Classes AA inner join ClassOrders BB on AA.ClassOrderID=BB.ClassOrderID where AA.ClassState=2 and AA.MemberID=A.MemberID and AA.ClassAttendState<>99 and BB.ClassProductID=1 and AA.StartYear=".$SearchYear." and AA.StartMonth=".$SearchMonth." and AA.ClassAttendState=3) as AbsentClassCount,
			(select count(*) FROM Classes AA inner join ClassOrders BB on AA.ClassOrderID=BB.ClassOrderID where AA.ClassState=2 and AA.MemberID=A.MemberID and AA.ClassAttendState<>99 and BB.ClassProductID=1 and AA.StartYear=".$SearchYear." and AA.StartMonth=".$SearchMonth." and (AA.ClassAttendState=1 or AA.ClassAttendState=2)) as AttendClassCount

		from Members A 
			inner join Centers B on A.CenterID=B.CenterID 
			inner join Members BB on B.CenterID=BB.CenterID and BB.MemberLevelID=12 
			inner join Branches C on B.BranchID=C.BranchID 
			inner join Members CC on C.BranchID=CC.BranchID and CC.MemberLevelID=9

		where 
			".$AddSqlWhere."
		order by C.BranchName asc, B.CenterName asc 
		";

$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);


?>


							

<table border="1">
	<thead>
		<tr>
			<th nowrap>No</th>
			<th nowrap>지사명</th>
			<th nowrap>지사아이디</th>
			<th nowrap>학당명</th>
			<th nowrap>학당아이디</th>
			<th nowrap>학생이름</th>
			<th nowrap>영어이름</th>
			<th nowrap>학생아이디</th>
			<th nowrap>결석수</th>
			<th nowrap>출석수<!--<br>(종료수업기준)--></th>
			<th nowrap>수업수<!--<br>(종료수업기준)--></th>
			<!--<th nowrap>출석수/수업수<br>(전체수업기준)</th>-->
			<th nowrap>출석률<!--<br>(종료수업기준)--></th>
			<!--<th nowrap>출석률<br>(전체수업기준)</th>-->
			<th nowrap>출석률 결과</th>
		</tr>
	</thead>
	<tbody>
		
		<?php
		$ListCount = 1;

		while($Row = $Stmt->fetch()) {

			$ListNumber = $TotalRowCount - ($ListCount-1);

			$MemberID = $Row["MemberID"];
			$MemberLoginID = $Row["MemberLoginID"];
			$MemberName = $Row["MemberName"];
			$MemberNickName = $Row["MemberNickName"];

			$CenterID = $Row["CenterID"];
			$CenterName = $Row["CenterName"];
			$CenterLoginID = $Row["CenterLoginID"];

			$BranchID = $Row["BranchID"];
			$BranchName = $Row["BranchName"];
			$BranchLoginID = $Row["BranchLoginID"];

			$TotalClassCount = $Row["TotalClassCount"];
			$AbsentClassCount = $Row["AbsentClassCount"];
			$AttendClassCount = $Row["AttendClassCount"];

			$AttendRatio = round($AttendClassCount/$TotalClassCount*100);

			if ($AttendRatio>50){
				$StrResultAttend = "<span style='color:#0000ff;'>Excellent</span>";
			}else{
				$StrResultAttend = "<span style='color:#ff0000;'>Fail</span>";
			}



		?>
		<tr>
			<td><?=$ListNumber?><!-- No --></td>
			<td><?=$BranchName?><!-- 지사이름 --></td>
			<td><?=$BranchLoginID?><!-- 지사아이디 --></td>
			<td><?=$CenterName?><!-- 학당이름 --></td>
			<td><?=$CenterLoginID?><!-- 학당아이디 --></td>
			<td><?=$MemberName?><!-- 학생이름 --></td>
			<td><?=$MemberNickName?><!-- 영어이름 --></td>
			<td><?=$MemberLoginID?><!-- 학생아이디 --></td>
			<td><?=$AbsentClassCount?><!-- 결석수 --></td>
			<td><?=$AttendClassCount?><!-- 출석수<br>(종료수업기준) --></td>
			<td><?=$TotalClassCount?><!-- 수업수<br>(종료수업기준) --></td>
			<!--<td><?=$ListCount?>!-- 출석수/수업수<br>(전체수업기준) --</td>-->
			<td><?=$AttendRatio?> %<!-- 출석률<br>(종료수업기준) --></td>
			<!--<td><?=$ListCount?>!-- 출석률<br>(전체수업기준) --</td>-->
			<td><?=$StrResultAttend?><!-- 출석률 결과 --></td>
		</tr>
		<?
			$ListCount++;
		}
		$Stmt = null;
		?>
	</tbody>
</table>
						

