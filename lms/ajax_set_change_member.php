<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');

$ChangeMemberID = isset($_REQUEST["ChangeMemberID"]) ? $_REQUEST["ChangeMemberID"] : "";
$ChangeMemberID = trim($ChangeMemberID);

$ErrNum = 0;
$ErrMsg = "";


if ($_ADMIN_LEVEL_ID_==0 or $_ADMIN_LEVEL_ID_==1){//마스터
	$Sql = "select count(*) as ExistCount from Members A where A.MemberLoginID=:ChangeMemberID and A.MemberLevelID<=15 and A.MemberLevelID>=".$_ADMIN_LEVEL_ID_." and A.MemberState=1 ";
}else if ($_ADMIN_LEVEL_ID_==3 or $_ADMIN_LEVEL_ID_==4){//프랜차이즈 관리자
	$Sql = "select 
				count(*) as ExistCount 
			from Members A 

				left outer join Centers B on A.CenterID=B.CenterID and (A.MemberLevelID=12 or A.MemberLevelID=13)
				left outer join Branches C on B.BranchID=C.BranchID 
				left outer join BranchGroups D on C.BranchGroupID=D.BranchGroupID 
				left outer join Companies E on D.CompanyID=E.CompanyID 
				
				left outer join Branches BB on A.BranchID=BB.BranchID and (A.MemberLevelID=9 or A.MemberLevelID=10)
				left outer join BranchGroups CC on BB.BranchGroupID=CC.BranchGroupID 
				left outer join Companies DD on CC.CompanyID=DD.CompanyID
				
				left outer join BranchGroups EE on A.BranchGroupID=EE.BranchGroupID and (A.MemberLevelID=6 or A.MemberLevelID=7)
				left outer join Companies FF on EE.CompanyID=FF.CompanyID 

				left outer join Teachers AAA on A.TeacherID=AAA.TeacherID and A.MemberLevelID=15 
				left outer join TeacherGroups BBB on AAA.TeacherGroupID=BBB.TeacherGroupID 
				
				left outer join Staffs CCC on A.StaffID=CCC.StaffID and (A.MemberLevelID=3 or A.MemberLevelID=4) 

			where A.MemberLoginID=:ChangeMemberID and A.MemberLevelID<=15 and A.MemberLevelID>=".$_ADMIN_LEVEL_ID_." and A.MemberState=1 
				and (
						(E.FranchiseID=".$_ADMIN_FRANCHISE_ID_." or DD.FranchiseID=".$_ADMIN_FRANCHISE_ID_." or FF.FranchiseID=".$_ADMIN_FRANCHISE_ID_." or CCC.FranchiseID=".$_ADMIN_FRANCHISE_ID_.") 
						or 
						(A.MemberLevelID=15)
					)
			
			";
}else if ($_ADMIN_LEVEL_ID_==6 or $_ADMIN_LEVEL_ID_==7){//대표지사 관리자
	$Sql = "select 
				count(*) as ExistCount 
			from Members A 

				left outer join Centers B on A.CenterID=B.CenterID and (A.MemberLevelID=12 or A.MemberLevelID=13)
				left outer join Branches C on B.BranchID=C.BranchID 
				left outer join BranchGroups D on C.BranchGroupID=D.BranchGroupID 
				left outer join Companies E on D.CompanyID=E.CompanyID 
				
				left outer join Branches BB on A.BranchID=BB.BranchID and (A.MemberLevelID=9 or A.MemberLevelID=10)
				left outer join BranchGroups CC on BB.BranchGroupID=CC.BranchGroupID 
				left outer join Companies DD on CC.CompanyID=DD.CompanyID
				
				left outer join BranchGroups EE on A.BranchGroupID=EE.BranchGroupID and (A.MemberLevelID=6 or A.MemberLevelID=7)
				left outer join Companies FF on EE.CompanyID=FF.CompanyID 

				left outer join Teachers AAA on A.TeacherID=AAA.TeacherID and A.MemberLevelID=15 
				left outer join TeacherGroups BBB on AAA.TeacherGroupID=BBB.TeacherGroupID 
				
				left outer join Staffs CCC on A.StaffID=CCC.StaffID and (A.MemberLevelID=3 or A.MemberLevelID=4) 

			where A.MemberLoginID=:ChangeMemberID and A.MemberLevelID<=15 and A.MemberLevelID>=".$_ADMIN_LEVEL_ID_." and A.MemberState=1 
				and (A.BranchGroupID=".$_ADMIN_BRANCH_GROUP_ID_." or C.BranchGroupID=".$_ADMIN_BRANCH_GROUP_ID_." or BB.BranchGroupID=".$_ADMIN_BRANCH_GROUP_ID_." )
			
			";
}else if ($_ADMIN_LEVEL_ID_==9 or $_ADMIN_LEVEL_ID_==10){//지사 관리자
	$Sql = "select 
				count(*) as ExistCount 
			from Members A 

				left outer join Centers B on A.CenterID=B.CenterID and (A.MemberLevelID=12 or A.MemberLevelID=13)
				left outer join Branches C on B.BranchID=C.BranchID 
				left outer join BranchGroups D on C.BranchGroupID=D.BranchGroupID 
				left outer join Companies E on D.CompanyID=E.CompanyID 
				
				left outer join Branches BB on A.BranchID=BB.BranchID and (A.MemberLevelID=9 or A.MemberLevelID=10)
				left outer join BranchGroups CC on BB.BranchGroupID=CC.BranchGroupID 
				left outer join Companies DD on CC.CompanyID=DD.CompanyID
				
				left outer join BranchGroups EE on A.BranchGroupID=EE.BranchGroupID and (A.MemberLevelID=6 or A.MemberLevelID=7)
				left outer join Companies FF on EE.CompanyID=FF.CompanyID 

				left outer join Teachers AAA on A.TeacherID=AAA.TeacherID and A.MemberLevelID=15 
				left outer join TeacherGroups BBB on AAA.TeacherGroupID=BBB.TeacherGroupID 
				
				left outer join Staffs CCC on A.StaffID=CCC.StaffID and (A.MemberLevelID=3 or A.MemberLevelID=4) 

			where A.MemberLoginID=:ChangeMemberID and A.MemberLevelID<=15 and A.MemberLevelID>=".$_ADMIN_LEVEL_ID_." and A.MemberState=1 
				and (A.BranchID=".$_ADMIN_BRANCH_ID_." or B.BranchID=".$_ADMIN_BRANCH_ID_." )
			
			";
}else if ($_ADMIN_LEVEL_ID_==12 or $_ADMIN_LEVEL_ID_==13){//대리점 관리자
	$Sql = "select 
				count(*) as ExistCount 
			from Members A 

				left outer join Centers B on A.CenterID=B.CenterID and (A.MemberLevelID=12 or A.MemberLevelID=13)
				left outer join Branches C on B.BranchID=C.BranchID 
				left outer join BranchGroups D on C.BranchGroupID=D.BranchGroupID 
				left outer join Companies E on D.CompanyID=E.CompanyID 
				
				left outer join Branches BB on A.BranchID=BB.BranchID and (A.MemberLevelID=9 or A.MemberLevelID=10)
				left outer join BranchGroups CC on BB.BranchGroupID=CC.BranchGroupID 
				left outer join Companies DD on CC.CompanyID=DD.CompanyID
				
				left outer join BranchGroups EE on A.BranchGroupID=EE.BranchGroupID and (A.MemberLevelID=6 or A.MemberLevelID=7)
				left outer join Companies FF on EE.CompanyID=FF.CompanyID 

				left outer join Teachers AAA on A.TeacherID=AAA.TeacherID and A.MemberLevelID=15 
				left outer join TeacherGroups BBB on AAA.TeacherGroupID=BBB.TeacherGroupID 
				
				left outer join Staffs CCC on A.StaffID=CCC.StaffID and (A.MemberLevelID=3 or A.MemberLevelID=4) 

			where A.MemberLoginID=:ChangeMemberID and A.MemberLevelID<=15 and A.MemberLevelID>=".$_ADMIN_LEVEL_ID_." and A.MemberState=1 
				and A.CenterID=".$_ADMIN_CENTER_ID_." 
			
			";
}



$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ChangeMemberID', $ChangeMemberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$ExistCount = $Row["ExistCount"];

if ($ExistCount==0){
	$ErrNum = 1;
	$ErrMsg = "입력하신 아이디는 존재하지 않거나 전활할 권한이 없는 아이디 입니다.";
}else{

	
	$Sql = "
			select 
					A.*
			from Members A 
			where A.MemberLoginID=:ChangeMemberID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ChangeMemberID', $ChangeMemberID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$MemberName = $Row["MemberName"];

	
	$ErrMsg = "입력하신 [".$MemberName."] 아이디 권한으로 전환했습니다. 원래대로 돌아가실 경우 본인 아이디로 전환하시기 바랍니다.";

	setcookie("LinkLoginMemberID", $ChangeMemberID, 0, "/");
	setcookie("LinkLoginAdminID", $ChangeMemberID,  0, "/");
}

$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;

$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('../includes/dbclose.php');
?>