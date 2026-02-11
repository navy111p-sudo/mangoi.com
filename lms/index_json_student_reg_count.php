<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');

$AddSqlWhere = " 1=1 "; 
if ($_LINK_ADMIN_LEVEL_ID_==6 || $_LINK_ADMIN_LEVEL_ID_==7){//대표 지사
	$AddSqlWhere .= " and E.BranchGroupID=".$_LINK_ADMIN_BRANCH_GROUP_ID_." ";
}else if ($_LINK_ADMIN_LEVEL_ID_==9 || $_LINK_ADMIN_LEVEL_ID_==10){//지사
	$AddSqlWhere .= " and D.BranchID=".$_LINK_ADMIN_BRANCH_ID_." ";
}else if ($_ADMIN_LEVEL_ID_==12 || $_ADMIN_LEVEL_ID_==13){//대리점
	$AddSqlWhere .= " and C.CenterID=".$_LINK_ADMIN_CENTER_ID_." ";
}else{//대표지사 이상
	
}

$AddSqlWhere = $AddSqlWhere . " and B.MemberState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and C.CenterState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and D.BranchState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and E.BranchGroupState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and F.CompanyState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and G.FranchiseState<>0 ";


$Sql = "
SELECT MemberRegYearMonth, COUNT(*) as MemberRegCount FROM 		
		(select 
				DATE_FORMAT(B.MemberRegDateTime,'%Y') AS MemberRegYear,
				DATE_FORMAT(B.MemberRegDateTime,'%m') AS MemberRegMonth,
				DATE_FORMAT(B.MemberRegDateTime,'%Y-%m-01') AS MemberRegYearMonth
		from Members B 
			inner join Centers C on B.CenterID=C.CenterID 
			inner join Branches D on C.BranchID=D.BranchID
			inner join BranchGroups E on D.BranchGroupID=E.BranchGroupID 
			inner join Companies F on E.CompanyID=F.CompanyID 
			inner join Franchises G on F.FranchiseID=G.FranchiseID 
		where ".$AddSqlWhere." and B.MemberLevelID=19
			
		) AA
GROUP BY MemberRegYearMonth
ORDER BY MemberRegYearMonth asc
";

$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
?>

[

<?
$ListCount = 1;
while($Row = $Stmt->fetch()) {
	if ($ListCount>1){
		echo ",";
	}
?>
    {
        "date": "<?=$Row["MemberRegYearMonth"]?>", 
        "value": <?=$Row["MemberRegCount"]?>
    } 
<?
	$ListCount++;
}
?>

]

<?
include_once('../includes/dbclose.php');
?>