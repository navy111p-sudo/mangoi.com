<?php
$SearchYear = isset($_REQUEST["SearchYear"]) ? $_REQUEST["SearchYear"] : "";
$SearchMonth = isset($_REQUEST["SearchMonth"]) ? $_REQUEST["SearchMonth"] : "";

header( "Content-type: application/vnd.ms-excel; charset=utf-8");
header( "Content-Disposition: attachment; filename = CLASS-STATUS-".$SearchYear."-".$SearchMonth.".xls" );
header( "Content-Description: PHP4 Generated Data" );

include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');



$AddSqlWhere = "1=1";

$SearchYear = isset($_REQUEST["SearchYear"]) ? $_REQUEST["SearchYear"] : "";
$SearchMonth = isset($_REQUEST["SearchMonth"]) ? $_REQUEST["SearchMonth"] : "";

$SearchFranchiseID = isset($_REQUEST["SearchFranchiseID"]) ? $_REQUEST["SearchFranchiseID"] : "";
$SearchCompanyID = isset($_REQUEST["SearchCompanyID"]) ? $_REQUEST["SearchCompanyID"] : "";
$SearchBranchGroupID = isset($_REQUEST["SearchBranchGroupID"]) ? $_REQUEST["SearchBranchGroupID"] : "";
$SearchBranchID = isset($_REQUEST["SearchBranchID"]) ? $_REQUEST["SearchBranchID"] : "";
$SearchCenterID = isset($_REQUEST["SearchCenterID"]) ? $_REQUEST["SearchCenterID"] : "";


//================== 서치폼 감추기 =================
if ($_LINK_ADMIN_LEVEL_ID_==0 or $_LINK_ADMIN_LEVEL_ID_==1){//마스터
	//모두허용
}else if ($_LINK_ADMIN_LEVEL_ID_==3 or $_LINK_ADMIN_LEVEL_ID_==4){//프랜차이즈 관리자
	$SearchFranchiseID = $_LINK_ADMIN_FRANCHISE_ID_;
}else if ($_LINK_ADMIN_LEVEL_ID_==6 or $_LINK_ADMIN_LEVEL_ID_==7){//대표지사 관리자
	$SearchBranchGroupID = $_LINK_ADMIN_BRANCH_GROUP_ID_;
	$SearchCompanyID = $_LINK_ADMIN_COMPANY_ID_;
	$SearchFranchiseID = $_LINK_ADMIN_FRANCHISE_ID_;
}else if ($_LINK_ADMIN_LEVEL_ID_==9 or $_LINK_ADMIN_LEVEL_ID_==10){//지사 관리자
	$SearchBranchID = $_LINK_ADMIN_BRANCH_ID_;
	$SearchBranchGroupID = $_LINK_ADMIN_BRANCH_GROUP_ID_;
	$SearchCompanyID = $_LINK_ADMIN_COMPANY_ID_;
	$SearchFranchiseID = $_LINK_ADMIN_FRANCHISE_ID_;
}else if ($_LINK_ADMIN_LEVEL_ID_==12 or $_LINK_ADMIN_LEVEL_ID_==13){//대리점 관리자
	$SearchCenterID = $_LINK_ADMIN_CENTER_ID_;
	$SearchBranchID = $_LINK_ADMIN_BRANCH_ID_;
	$SearchBranchGroupID = $_LINK_ADMIN_BRANCH_GROUP_ID_;
	$SearchCompanyID = $_LINK_ADMIN_COMPANY_ID_;
	$SearchFranchiseID = $_LINK_ADMIN_FRANCHISE_ID_;
}else if ($_LINK_ADMIN_LEVEL_ID_==15){//강사 
	//접속불가
}
//================== 서치폼 감추기 =================

if ($SearchBranchID=="" && $SearchCenterID==""){
	$SearchBranchID = -1;
	$SearchCenterID = -1;
}


if ($SearchYear==""){
	$SearchYear = date("Y");
}

if ($SearchMonth==""){
	$SearchMonth = date("m");
}

$AddSqlWhere = $AddSqlWhere . " and A.MemberState=1 ";
$AddSqlWhere = $AddSqlWhere . " and A.MemberLevelID=19 ";

$AddSqlWhere = $AddSqlWhere . " and (A.MemberState<>0 or A.MemberState is null) ";
$AddSqlWhere = $AddSqlWhere . " and (B.CenterState<>0 or B.CenterState is null)";
$AddSqlWhere = $AddSqlWhere . " and (C.BranchState<>0 or C.BranchState is null)";
$AddSqlWhere = $AddSqlWhere . " and (D.BranchGroupState<>0 or D.BranchGroupState is null)";
$AddSqlWhere = $AddSqlWhere . " and (E.CompanyState<>0 or E.CompanyState is null)";
$AddSqlWhere = $AddSqlWhere . " and (F.FranchiseState<>0 or F.FranchiseState is null)";

if ($SearchFranchiseID!=""){
	$AddSqlWhere = $AddSqlWhere . " and E.FranchiseID=$SearchFranchiseID ";
}

if ($SearchCompanyID!=""){
	$AddSqlWhere = $AddSqlWhere . " and D.CompanyID=$SearchCompanyID ";
}

if ($SearchBranchGroupID!=""){
	$AddSqlWhere = $AddSqlWhere . " and C.BranchGroupID=$SearchBranchGroupID ";
}

if ($SearchBranchID!=""){
	$AddSqlWhere = $AddSqlWhere . " and B.BranchID=$SearchBranchID ";
}

if ($SearchCenterID!=""){
	$AddSqlWhere = $AddSqlWhere . " and A.CenterID=$SearchCenterID ";
}


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

			inner join BranchGroups D on C.BranchGroupID=D.BranchGroupID 
			inner join Companies E on D.CompanyID=E.CompanyID 
			inner join Franchises F on E.FranchiseID=F.FranchiseID 
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

			inner join BranchGroups D on C.BranchGroupID=D.BranchGroupID 
			inner join Companies E on D.CompanyID=E.CompanyID 
			inner join Franchises F on E.FranchiseID=F.FranchiseID 

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
			<th nowrap>대리점명</th>
			<th nowrap>대리점아이디</th>
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
			<td><?=$CenterName?><!-- 대리점이름 --></td>
			<td><?=$CenterLoginID?><!-- 대리점아이디 --></td>
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
						

