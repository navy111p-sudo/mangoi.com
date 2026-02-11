<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
?>

<?php

$AddSqlWhere = "1=1";
$AddSqlWhere2 = "1=1";
$ListParam = "1=1";
$PaginationParam = "1=1";


$CurrentPage = isset($_REQUEST["CurrentPage"]) ? $_REQUEST["CurrentPage"] : "";
$PageListNum = isset($_REQUEST["PageListNum"]) ? $_REQUEST["PageListNum"] : "";
$SearchText = isset($_REQUEST["SearchText"]) ? $_REQUEST["SearchText"] : "";
$SearchText2 = isset($_REQUEST["SearchText2"]) ? $_REQUEST["SearchText2"] : "";
$SearchState = isset($_REQUEST["SearchState"]) ? $_REQUEST["SearchState"] : "";
$SearchFranchiseID = isset($_REQUEST["SearchFranchiseID"]) ? $_REQUEST["SearchFranchiseID"] : "";
$SearchCompanyID = isset($_REQUEST["SearchCompanyID"]) ? $_REQUEST["SearchCompanyID"] : "";
$SearchBranchGroupID = isset($_REQUEST["SearchBranchGroupID"]) ? $_REQUEST["SearchBranchGroupID"] : "";
$SearchBranchID = isset($_REQUEST["SearchBranchID"]) ? $_REQUEST["SearchBranchID"] : "";
$SearchCenterID = isset($_REQUEST["SearchCenterID"]) ? $_REQUEST["SearchCenterID"] : "";
$SearchClassOrderEndDateNum = isset($_REQUEST["SearchClassOrderEndDateNum"]) ? $_REQUEST["SearchClassOrderEndDateNum"] : "";





if (!$CurrentPage){
	$CurrentPage = 1;	
}
if (!$PageListNum){
	$PageListNum = 30;
}

if ($PageListNum!=""){
	$ListParam = $ListParam . "&PageListNum=" . $PageListNum;
}

if ($SearchState==""){
	$SearchState = "1";
}	

if ($SearchClassOrderEndDateNum==""){
	$SearchClassOrderEndDateNum = "100";
}
		
if ($SearchState!="100"){
	$ListParam = $ListParam . "&SearchState=" . $SearchState;
	$AddSqlWhere = $AddSqlWhere . " and A.MemberState=$SearchState ";
}
$AddSqlWhere = $AddSqlWhere . " and (A.MemberState<>0 or A.MemberState is null) ";
$AddSqlWhere = $AddSqlWhere . " and (B.CenterState<>0 or B.CenterState is null)";
$AddSqlWhere = $AddSqlWhere . " and (C.BranchState<>0 or C.BranchState is null)";
$AddSqlWhere = $AddSqlWhere . " and (D.BranchGroupState<>0 or D.BranchGroupState is null)";
$AddSqlWhere = $AddSqlWhere . " and (E.CompanyState<>0 or E.CompanyState is null)";
$AddSqlWhere = $AddSqlWhere . " and (F.FranchiseState<>0 or F.FranchiseState is null)";

$AddSqlWhere = $AddSqlWhere . " and A.MemberLevelID=19 ";

if ($SearchText!=""){
	$ListParam = $ListParam . "&SearchText=" . $SearchText;
	$AddSqlWhere = $AddSqlWhere . " and (A.MemberName like '%".$SearchText."%' or A.MemberLoginID like '%".$SearchText."%' or A.MemberNickName like '%".$SearchText."%') ";
}

if ($SearchText2!=""){
	$ListParam = $ListParam . "&SearchText2=" . $SearchText2;
	$AddSqlWhere = $AddSqlWhere . " and A.MemberInviteID=(select MemberID from Members where MemberLoginID='".$SearchText2."') ";
}

if ($SearchFranchiseID!=""){
	$ListParam = $ListParam . "&SearchFranchiseID=" . $SearchFranchiseID;
	$AddSqlWhere = $AddSqlWhere . " and E.FranchiseID=$SearchFranchiseID ";
}

if ($SearchCompanyID!=""){
	$ListParam = $ListParam . "&SearchCompanyID=" . $SearchCompanyID;
	$AddSqlWhere = $AddSqlWhere . " and D.CompanyID=$SearchCompanyID ";
}

if ($SearchBranchGroupID!=""){
	$ListParam = $ListParam . "&SearchBranchGroupID=" . $SearchBranchGroupID;
	$AddSqlWhere = $AddSqlWhere . " and C.BranchGroupID=$SearchBranchGroupID ";
}

if ($SearchBranchID!=""){
	$ListParam = $ListParam . "&SearchBranchID=" . $SearchBranchID;
	$AddSqlWhere = $AddSqlWhere . " and B.BranchID=$SearchBranchID ";
}

if ($SearchCenterID!=""){
	$ListParam = $ListParam . "&SearchCenterID=" . $SearchCenterID;
	$AddSqlWhere = $AddSqlWhere . " and A.CenterID=$SearchCenterID ";
}

if ($SearchClassOrderEndDateNum!="100"){
	$ListParam = $ListParam . "&SearchClassOrderEndDateNum=" . $SearchClassOrderEndDateNum;
	$AddSqlWhere2 = $AddSqlWhere2 . " and 
	(
		( V.CenterPayType=1 and V.MemberPayType=0 and datediff(V.CenterStudyEndDate, now())=".$SearchClassOrderEndDateNum.") 
		or 
		(V.CenterPayType=1 and V.MemberPayType=1 and V.MemberID in (select MemberID from ClassOrders where datediff(ClassOrderEndDate, now())=".$SearchClassOrderEndDateNum." and (ClassOrderState=1 or ClassOrderState=2 or ClassOrderState=4)) ) 
		or 
		(V.CenterPayType=2 and V.MemberID in (select MemberID from ClassOrders where datediff(ClassOrderEndDate, now())=".$SearchClassOrderEndDateNum." and (ClassOrderState=1 or ClassOrderState=2 or ClassOrderState=4)) ) 
 
	) ";
}


$PaginationParam = $ListParam;
if ($CurrentPage!=""){
	$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}

$ListParam = str_replace("&", "^^", $ListParam);


$ViewTable = "

		select 
			A.* ,
			AES_DECRYPT(UNHEX(A.MemberPhone1),:EncryptionKey) as DecMemberPhone1,
			AES_DECRYPT(UNHEX(A.MemberPhone2),:EncryptionKey) as DecMemberPhone2,
			AES_DECRYPT(UNHEX(A.MemberPhone3),:EncryptionKey) as DecMemberPhone3,
			B.CenterID as JoinCenterID,
			B.CenterName as JoinCenterName,
			B.CenterPayType,
			B.CenterRenewType,
			B.CenterStudyEndDate,
			C.BranchID as JoinBranchID,
			C.BranchName as JoinBranchName, 
			D.BranchGroupID as JoinBranchGroupID,
			D.BranchGroupName as JoinBranchGroupName,
			E.CompanyID as JoinCompanyID,
			E.CompanyName as JoinCompanyName,
			F.FranchiseName,
			(select sum(AA.MemberPoint) from MemberPoints AA where AA.MemberID=A.MemberID and AA.MemberPointState=1) as MemberPoint,

			ifnull((select BB.ClassOrderWeekCount from ClassOrders AA inner join ClassOrderWeekCounts BB on AA.ClassOrderWeekCountID=BB.ClassOrderWeekCountID where AA.MemberID=A.MemberID and AA.ClassOrderState=1 and AA.ClassProductID=1 order by AA.ClassOrderID desc limit 0,1),'-') as ClassOrderWeekCount
		from Members A 
			left outer join Centers B on A.CenterID=B.CenterID 
			left outer join Branches C on B.BranchID=C.BranchID 
			left outer join BranchGroups D on C.BranchGroupID=D.BranchGroupID 
			left outer join Companies E on D.CompanyID=E.CompanyID 
			left outer join Franchises F on E.FranchiseID=F.FranchiseID 
		where ".$AddSqlWhere." 

";



$Sql = "select 
				count(*) TotalRowCount 
		from ($ViewTable) V 
		where ".$AddSqlWhere2."
";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TotalRowCount = $Row["TotalRowCount"];

$TotalPageCount = ceil($TotalRowCount / $PageListNum);
$StartRowNum = $PageListNum * ($CurrentPage - 1 );


$Sql = "
		select 
			V.*
		from ($ViewTable) V 
		where ".$AddSqlWhere2." 
		order by V.MemberRegDateTime desc";// limit $StartRowNum, $PageListNum";


$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

header( "Content-type: application/vnd.ms-excel" );
header( "Content-type: application/vnd.ms-excel; charset=utf-8");
header( "Content-Disposition: attachment; filename = 학생목록.xls" );
print("<meta http-equiv=\"Content-Type\" content=\"application/vnd.ms-excel; charset=utf-8\">");
?>



<table border="1">
	<thead>
		<tr>
			<th>No</th>
			<th><?=$학생명[$LangID]?></th>
			<th><?=$아이디[$LangID]?></th>
			<th>결제타입</th>
			<th>수강종료일</th>
			<th>가입일</th>
			<th>수업회수(주)</th>
			<th><?=$포인트[$LangID]?></th>
			<?if ($_LINK_ADMIN_LEVEL_ID_!=9 && $_LINK_ADMIN_LEVEL_ID_!=10){?>
				<th><?=$학생번호[$LangID]?></th>
				<th><?=$부모님번호[$LangID]?></th>
				<th>관리교사번호</th>
			<?}?>

			<th><?=$대리점명[$LangID]?></th>
			<?if ($_LINK_ADMIN_LEVEL_ID_<6){?>
				<th><?=$본사명[$LangID]?></th>
				<th><?=$대표지사명[$LangID]?></th>
				<th><?=$지사명[$LangID]?></th>
				<th><?=$프랜차이즈[$LangID]?></th>
			<?}?>
			<th><?=$상태[$LangID]?></th>
		</tr>
	</thead>
	<tbody>
		
		<?php
		$ListCount = 1;
		while($Row = $Stmt->fetch()) {
			$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

			$MemberID = $Row["MemberID"];
			$MemberPayType = $Row["MemberPayType"];
			$MemberLevelID = $Row["MemberLevelID"];
			$MemberNumber = $Row["MemberNumber"];
			$MemberLoginID = $Row["MemberLoginID"];
			$MemberLoginPW = $Row["MemberLoginPW"];
			$MemberName = $Row["MemberName"];
			$MemberNickName = $Row["MemberNickName"];
			$MemberSex = $Row["MemberSex"];
			$MemberCompanyName = $Row["MemberCompanyName"];
			$MemberPhoto = $Row["MemberPhoto"];
			$MemberBirthday = $Row["MemberBirthday"];
			$MemberPhone1 = $Row["DecMemberPhone1"];
			$MemberPhone2 = $Row["DecMemberPhone2"];
			$MemberPhone3 = $Row["DecMemberPhone3"];
			$MemberEmail = $Row["MemberEmail"];
			$MemberZip = $Row["MemberZip"];
			$MemberAddr1 = $Row["MemberAddr1"];
			$MemberAddr2 = $Row["MemberAddr2"];
			$SchoolName = $Row["SchoolName"];
			$SchoolGrade = $Row["SchoolGrade"];
			$MemberView = $Row["MemberView"];
			$MemberState = $Row["MemberState"];
			$MemberStateText = $Row["MemberStateText"];
			$WithdrawalText = $Row["WithdrawalText"];
			$LastLoginDateTime = $Row["LastLoginDateTime"];
			$LastAppLoginDateTime = $Row["LastAppLoginDateTime"];
			$MemberRegDateTime = $Row["MemberRegDateTime"];
			$MemberModiDateTime = $Row["MemberModiDateTime"];
			$WithdrawalDateTime = $Row["WithdrawalDateTime"];
			

			$CenterID = $Row["JoinCenterID"];
			$CenterName = $Row["JoinCenterName"];
			$CenterPayType = $Row["CenterPayType"];
			$CenterRenewType = $Row["CenterRenewType"];
			$CenterStudyEndDate = $Row["CenterStudyEndDate"];

			$BranchID = $Row["JoinBranchID"];
			$BranchName = $Row["JoinBranchName"];
			$BranchGroupID = $Row["JoinBranchGroupID"];
			$BranchGroupName = $Row["JoinBranchGroupName"];
			$CompanyID = $Row["JoinCompanyID"];
			$CompanyName = $Row["JoinCompanyName"];
			$FranchiseName = $Row["FranchiseName"];

			$MemberPoint = $Row["MemberPoint"];

			$ClassOrderWeekCount = $Row["ClassOrderWeekCount"];
			
			if ($MemberState==1){
				$StrCenterState = "정상";
			}else if ($MemberState==2){
				$StrCenterState = "휴면";
			}else if ($MemberState==3){
				$StrCenterState = "탈퇴";
			}

			$StrClassOrderEndDateGroup = 0;
			if ($CenterPayType==1){//B2B결제
				if ($MemberPayType==0){
					$StrCenterPayType = "B2B 결제";
				}else{
					$StrCenterPayType = "B2B 개인결제";
					$StrClassOrderEndDateGroup = 1;
				}
			}else{
				$StrCenterPayType = "B2C 결제";
				$StrClassOrderEndDateGroup = 1;
			}


			$StrClassOrderEndDateGroup=1;//20200320 수강신청 종료날짜로 보여준다.

			if ($StrClassOrderEndDateGroup==1){
				$Sql2 = "select 
								A.* 
						from ClassOrders A 
						where 
							A.MemberID=$MemberID 
							and (A.ClassOrderState=1 or A.ClassOrderState=2 or A.ClassOrderState=4) 
							and A.ClassProgress=11 
							and A.ClassProductID=1 
						order by A.ClassOrderEndDate asc";	
				$Stmt2 = $DbConn->prepare($Sql2);
				$Stmt2->execute();
				$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

				$kkk=0;
				$StrClassOrderEndDate = "";
				while($Row2 = $Stmt2->fetch()) {
					if ($kkk>0){
						$StrClassOrderEndDate = $StrClassOrderEndDate . "<br>";
					}

					$ClassOrderEndDate = $Row2["ClassOrderEndDate"];
					
					if ($ClassOrderEndDate=="0000-00-00" || $ClassOrderEndDate==""){
						$ClassOrderEndDate = "[미설정]";
					}else{
						$ClassOrderEndDateDiff = (strtotime($ClassOrderEndDate) - strtotime(date("Y-m-d"))) / 86400;
						if ($ClassOrderEndDateDiff<=7){
							$ClassOrderEndDate = "<span style='color:#ff0000;'>".$ClassOrderEndDate." (".$ClassOrderEndDateDiff."일)</span>";
						}
					}
					$StrClassOrderEndDate .= $ClassOrderEndDate;
					
					
					$kkk++;
				}
				$Stmt2 = null;
			}else{
				$StrClassOrderEndDate = $CenterStudyEndDate;
			}

			if ($CenterPayType==1 && $CenterRenewType==2 && $MemberPayType==0){
				$StrClassOrderEndDate = "무결제B2B";
			}

		?>
		<tr>
			<td><?=$ListNumber?></td>
			<td><?=$MemberName?></td>
			<td><?=$MemberLoginID?></td>
			<td><?=$StrCenterPayType?></td>
			<td><?=$StrClassOrderEndDate?></td>
			<td><?=substr($MemberRegDateTime,0,10)?></td>
			<td><?=$ClassOrderWeekCount?></td>
			<td><?=number_format($MemberPoint,0)?></td>
			<?if ($_LINK_ADMIN_LEVEL_ID_!=9 && $_LINK_ADMIN_LEVEL_ID_!=10){?>
				<td><?=$MemberPhone1?></td>
				<td><?=$MemberPhone2?></td>
				<td><?=$MemberPhone3?></td>
			<?}?>
			<td><?=$CenterName?></td>
			<?if ($_LINK_ADMIN_LEVEL_ID_<6){?>
				<td><?=$CompanyName?></td>
				<td><?=$BranchGroupName?></td>
				<td><?=$BranchName?></td>
				<td><?=$FranchiseName?></td>
			<?}?>
			<td><?=$StrCenterState?></td>
		</tr>
		<?php
			$ListCount ++;
		}
		$Stmt = null;
		?>


	</tbody>
</table>


<?php
include_once('../includes/dbclose.php');
?>
