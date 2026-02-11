<?php
if (empty($DenyGuest)) {
	$DenyGuest = false;
}

$LoginMemberID =  isset($_COOKIE["LoginMemberID"]) ? $_COOKIE["LoginMemberID"] : "";
$LinkLoginMemberID = isset($_COOKIE["LinkLoginMemberID"]) ? $_COOKIE["LinkLoginMemberID"] : "";
$TempLinkLoginMemberID = isset($_REQUEST["TempLinkLoginMemberID"]) ? $_REQUEST["TempLinkLoginMemberID"] : "";
$LocalLinkLoginMemberID = isset($_REQUEST["LocalLinkLoginMemberID"]) ? $_REQUEST["LocalLinkLoginMemberID"] : "";

if($TempLinkLoginMemberID!="") {
	$LoginMemberID = $TempLinkLoginMemberID; 
}

if ($LinkLoginMemberID==""){
	$LinkLoginMemberID = $LoginMemberID;
}

if(!$LinkLoginMemberID && $LocalLinkLoginMemberID!=""){
	$LinkLoginMemberID = $LocalLinkLoginMemberID; 
	if(!$LoginMemberID) $LoginMemberID = $LocalLinkLoginMemberID; 
}

if ($LoginMemberID!=""){
	
	$Sql = "select 
				A.MemberID, 
				A.MemberLoginID, 
				A.MemberName, 
				A.MemberLevelID,
				A.MemberLanguageID,

				A.CenterID as Center_CenterID,
				ifnull(B.BranchID, 0) as Center_BranchID,
				ifnull(C.BranchGroupID, 0) as Center_BranchGroupID,
				ifnull(D.CompanyID, 0) as Center_CompanyID,
				ifnull(E.FranchiseID, 0) as Center_FranchiseID,
				
				A.BranchID as Branch_BranchID,
				ifnull(BB.BranchGroupID, 0) as Branch_BranchGroupID, 
				ifnull(CC.CompanyID, 0) as Branch_CompanyID,
				ifnull(DD.FranchiseID, 0) as Branch_FranchiseID,

				A.BranchGroupID as BranchGroup_BranchGroupID,
				ifnull(EE.CompanyID, 0) as BranchGroup_CompanyID,
				ifnull(FF.FranchiseID, 0) as BranchGroup_FranchiseID,

				A.ManagerID as Manager_ManagerID,
				ifnull(GG.FranchiseID, 0) as Manager_FranchiseID, 

				A.TeacherID as Teacher_TeacherID,
				ifnull(BBB.EduCenterID, 0) as Teacher_EduCenterID,
				ifnull(CCC.FranchiseID, 0 ) as Teacher_FranchiseID,

				A.StaffID as Staff_StaffID,
				ifnull(DDD.FranchiseID, 0) as Staff_FranchiseID

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
				left outer join Managers GG on A.ManagerID=GG.ManagerID and A.MemberLevelID=5 

				left outer join Teachers AAA on A.TeacherID=AAA.TeacherID and A.MemberLevelID=15 
				left outer join TeacherGroups BBB on AAA.TeacherGroupID=BBB.TeacherGroupID 
				left outer join EduCenters CCC on BBB.EduCenterID=CCC.EduCenterID 
				
				left outer join Staffs DDD on A.StaffID=DDD.StaffID and (A.MemberLevelID=3 or A.MemberLevelID=4) 

			where A.MemberLoginID=:LoginMemberID and A.MemberState=1";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':LoginMemberID', $LoginMemberID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$_MEMBER_ID_ = $Row["MemberID"];
	$_MEMBER_LOGIN_ID_ = $Row["MemberLoginID"];
	$_MEMBER_NAME_ = $Row["MemberName"];
	$_MEMBER_LEVEL_ID_ = $Row["MemberLevelID"];
	$_MEMBER_LANG_ID_ = $Row["MemberLanguageID"];

	if ($_MEMBER_LEVEL_ID_==0 or $_MEMBER_LEVEL_ID_==1){//마스터
		$_MEMBER_CENTER_ID_ = 0;
		$_MEMBER_BRANCH_ID_ = 0;
		$_MEMBER_BRANCH_GROUP_ID_ = 0;
		$_MEMBER_COMPANY_ID_ = 0;
		$_MEMBER_FRANCHISE_ID_ = 0;
		$_MEMBER_MANAGER_ID_ = 0;
		$_MEMBER_EDUCENTER_ID_ = 0;
		$_MEMBER_TEACHER_ID_ = 0;
		$_MEMBER_STAFF_ID_ = 0;
	}else if ($_MEMBER_LEVEL_ID_==3 or $_MEMBER_LEVEL_ID_==4){//프랜차이즈 관리자
		$_MEMBER_CENTER_ID_ = 0;
		$_MEMBER_BRANCH_ID_ = 0;
		$_MEMBER_BRANCH_GROUP_ID_ = 0;
		$_MEMBER_COMPANY_ID_ = 0;
		$_MEMBER_FRANCHISE_ID_ = $Row["Staff_FranchiseID"];
		$_MEMBER_MANAGER_ID_ = 0;
		$_MEMBER_EDUCENTER_ID_ = 0;
		$_MEMBER_TEACHER_ID_ = 0;
		$_MEMBER_STAFF_ID_ = $Row["Staff_StaffID"];
	}else if ($_MEMBER_LEVEL_ID_==5){//영업본부 관리자
		$_MEMBER_CENTER_ID_ = 0;
		$_MEMBER_BRANCH_ID_ = 0;
		$_MEMBER_BRANCH_GROUP_ID_ = 0;
		$_MEMBER_COMPANY_ID_ = 0;
		$_MEMBER_FRANCHISE_ID_ = $Row["Manager_FranchiseID"];
		$_MEMBER_MANAGER_ID_ = $Row["Manager_ManagerID"];
		$_MEMBER_EDUCENTER_ID_ = 0;
		$_MEMBER_TEACHER_ID_ = 0;
		$_MEMBER_STAFF_ID_ = 0;
	}else if ($_MEMBER_LEVEL_ID_==6 or $_MEMBER_LEVEL_ID_==7){//대표지사 관리자
		$_MEMBER_CENTER_ID_ = 0;
		$_MEMBER_BRANCH_ID_ = 0;
		$_MEMBER_BRANCH_GROUP_ID_ = $Row["BranchGroup_BranchGroupID"];
		$_MEMBER_COMPANY_ID_ = $Row["BranchGroup_CompanyID"];
		$_MEMBER_FRANCHISE_ID_ = $Row["BranchGroup_FranchiseID"];
		$_MEMBER_MANAGER_ID_ = 0;
		$_MEMBER_EDUCENTER_ID_ = 0;
		$_MEMBER_TEACHER_ID_ = 0;
		$_MEMBER_STAFF_ID_ = 0;
	}else if ($_MEMBER_LEVEL_ID_==9 or $_MEMBER_LEVEL_ID_==10){//지사 관리자
		$_MEMBER_CENTER_ID_ = 0;
		$_MEMBER_BRANCH_ID_ = $Row["Branch_BranchID"];
		$_MEMBER_BRANCH_GROUP_ID_ = $Row["Branch_BranchGroupID"];
		$_MEMBER_COMPANY_ID_ = $Row["Branch_CompanyID"];
		$_MEMBER_FRANCHISE_ID_ = $Row["Branch_FranchiseID"];
		$_MEMBER_MANAGER_ID_ = 0;
		$_MEMBER_EDUCENTER_ID_ = 0;
		$_MEMBER_TEACHER_ID_ = 0;
		$_MEMBER_STAFF_ID_ = 0;
	}else if ($_MEMBER_LEVEL_ID_==12 or $_MEMBER_LEVEL_ID_==13 or $_MEMBER_LEVEL_ID_==19){//대리점 관리자 //학생
		$_MEMBER_CENTER_ID_ = $Row["Center_CenterID"];
		$_MEMBER_BRANCH_ID_ = $Row["Center_BranchID"];
		$_MEMBER_BRANCH_GROUP_ID_ = $Row["Center_BranchGroupID"];
		$_MEMBER_COMPANY_ID_ = $Row["Center_CompanyID"];
		$_MEMBER_FRANCHISE_ID_ = $Row["Center_FranchiseID"];
		$_MEMBER_MANAGER_ID_ = 0;
		$_MEMBER_EDUCENTER_ID_ = 0;
		$_MEMBER_TEACHER_ID_ = 0;
		$_MEMBER_STAFF_ID_ = 0;
	}else if ($_MEMBER_LEVEL_ID_==15){//강사 
		$_MEMBER_CENTER_ID_ = 0;
		$_MEMBER_BRANCH_ID_ = 0;
		$_MEMBER_BRANCH_GROUP_ID_ = 0;
		$_MEMBER_COMPANY_ID_ = 0;
		$_MEMBER_FRANCHISE_ID_ = $Row["Teacher_FranchiseID"];
		$_MEMBER_MANAGER_ID_ = 0;
		$_MEMBER_EDUCENTER_ID_ = $Row["Teacher_EduCenterID"];
		$_MEMBER_TEACHER_ID_ = $Row["Teacher_TeacherID"];
		$_MEMBER_STAFF_ID_ = 0;
	}


	if ($LinkLoginMemberID!=""){ // 상위 관리자가 하위 관리자 권한으로 접속


		$Sql = "select 
					A.MemberID, 
					A.MemberLoginID, 
					A.MemberName, 
					A.MemberLevelID,
					A.MemberLanguageID,

					A.CenterID as Center_CenterID,
					ifnull(B.BranchID, 0) as Center_BranchID,
					ifnull(C.BranchGroupID, 0) as Center_BranchGroupID,
					ifnull(D.CompanyID, 0) as Center_CompanyID,
					ifnull(E.FranchiseID, 0) as Center_FranchiseID,
					
					A.BranchID as Branch_BranchID,
					ifnull(BB.BranchGroupID, 0) as Branch_BranchGroupID, 
					ifnull(CC.CompanyID, 0) as Branch_CompanyID,
					ifnull(DD.FranchiseID, 0) as Branch_FranchiseID,

					A.BranchGroupID as BranchGroup_BranchGroupID,
					ifnull(EE.CompanyID, 0) as BranchGroup_CompanyID,
					ifnull(FF.FranchiseID, 0) as BranchGroup_FranchiseID,

					A.ManagerID as Manager_ManagerID,
					ifnull(GG.FranchiseID, 0) as Manager_FranchiseID,

					A.TeacherID as Teacher_TeacherID,
					ifnull(BBB.EduCenterID, 0) as Teacher_EduCenterID,
					ifnull(CCC.FranchiseID, 0 ) as Teacher_FranchiseID,

					A.StaffID as Staff_StaffID,
					ifnull(DDD.FranchiseID, 0) as Staff_FranchiseID

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
					left outer join Managers GG on A.ManagerID=GG.ManagerID and A.MemberLevelID=5 

					left outer join Teachers AAA on A.TeacherID=AAA.TeacherID and A.MemberLevelID=15 
					left outer join TeacherGroups BBB on AAA.TeacherGroupID=BBB.TeacherGroupID 
					left outer join EduCenters CCC on BBB.EduCenterID=CCC.EduCenterID 
					
					left outer join Staffs DDD on A.StaffID=DDD.StaffID and (A.MemberLevelID=3 or A.MemberLevelID=4)

				where A.MemberLoginID=:LinkLoginMemberID and A.MemberState=1";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':LinkLoginMemberID', $LinkLoginMemberID);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;

		$_LINK_MEMBER_ID_ = $Row["MemberID"];
		$_LINK_MEMBER_LOGIN_ID_ = $Row["MemberLoginID"];
		$_LINK_MEMBER_NAME_ = $Row["MemberName"];
		$_LINK_MEMBER_LEVEL_ID_ = $Row["MemberLevelID"];
		$_LINK_MEMBER_LANG_ID_ = $Row["MemberLanguageID"];

		if ($_LINK_MEMBER_LEVEL_ID_==0 or $_LINK_MEMBER_LEVEL_ID_==1){//마스터
			$_LINK_MEMBER_CENTER_ID_ = 0;
			$_LINK_MEMBER_BRANCH_ID_ = 0;
			$_LINK_MEMBER_BRANCH_GROUP_ID_ = 0;
			$_LINK_MEMBER_COMPANY_ID_ = 0;
			$_LINK_MEMBER_FRANCHISE_ID_ = 0;
			$_LINK_MEMBER_MANAGER_ID_ = 0;
			$_LINK_MEMBER_EDUCENTER_ID_ = 0;
			$_LINK_MEMBER_TEACHER_ID_ = 0;
			$_LINK_MEMBER_STAFF_ID_ = 0;
		}else if ($_LINK_MEMBER_LEVEL_ID_==3 or $_LINK_MEMBER_LEVEL_ID_==4){//프랜차이즈 관리자
			$_LINK_MEMBER_CENTER_ID_ = 0;
			$_LINK_MEMBER_BRANCH_ID_ = 0;
			$_LINK_MEMBER_BRANCH_GROUP_ID_ = 0;
			$_LINK_MEMBER_COMPANY_ID_ = 0;
			$_LINK_MEMBER_FRANCHISE_ID_ = $Row["Staff_FranchiseID"];
			$_LINK_MEMBER_MANAGER_ID_ = 0;
			$_LINK_MEMBER_EDUCENTER_ID_ = 0;
			$_LINK_MEMBER_TEACHER_ID_ = 0;
			$_LINK_MEMBER_STAFF_ID_ = $Row["Staff_StaffID"];
		}else if ($_LINK_MEMBER_LEVEL_ID_==5){//영업본부 관리자
			$_LINK_MEMBER_CENTER_ID_ = 0;
			$_LINK_MEMBER_BRANCH_ID_ = 0;
			$_LINK_MEMBER_BRANCH_GROUP_ID_ = 0;
			$_LINK_MEMBER_COMPANY_ID_ = 0;
			$_LINK_MEMBER_FRANCHISE_ID_ = $Row["Manager_FranchiseID"];
			$_LINK_MEMBER_MANAGER_ID_ = $Row["Manager_ManagerID"];
			$_LINK_MEMBER_EDUCENTER_ID_ = 0;
			$_LINK_MEMBER_TEACHER_ID_ = 0;
			$_LINK_MEMBER_STAFF_ID_ = $Row["Staff_StaffID"];
		}else if ($_LINK_MEMBER_LEVEL_ID_==6 or $_LINK_MEMBER_LEVEL_ID_==7){//대표지사 관리자
			$_LINK_MEMBER_CENTER_ID_ = 0;
			$_LINK_MEMBER_BRANCH_ID_ = 0;
			$_LINK_MEMBER_BRANCH_GROUP_ID_ = $Row["BranchGroup_BranchGroupID"];
			$_LINK_MEMBER_COMPANY_ID_ = $Row["BranchGroup_CompanyID"];
			$_LINK_MEMBER_FRANCHISE_ID_ = $Row["BranchGroup_FranchiseID"];
			$_LINK_MEMBER_MANAGER_ID_ = 0;
			$_LINK_MEMBER_EDUCENTER_ID_ = 0;
			$_LINK_MEMBER_TEACHER_ID_ = 0;
			$_LINK_MEMBER_STAFF_ID_ = 0;
		}else if ($_LINK_MEMBER_LEVEL_ID_==9 or $_LINK_MEMBER_LEVEL_ID_==10){//지사 관리자
			$_LINK_MEMBER_CENTER_ID_ = 0;
			$_LINK_MEMBER_BRANCH_ID_ = $Row["Branch_BranchID"];
			$_LINK_MEMBER_BRANCH_GROUP_ID_ = $Row["Branch_BranchGroupID"];
			$_LINK_MEMBER_COMPANY_ID_ = $Row["Branch_CompanyID"];
			$_LINK_MEMBER_FRANCHISE_ID_ = $Row["Branch_FranchiseID"];
			$_LINK_MEMBER_MANAGER_ID_ = 0;
			$_LINK_MEMBER_EDUCENTER_ID_ = 0;
			$_LINK_MEMBER_TEACHER_ID_ = 0;
			$_LINK_MEMBER_STAFF_ID_ = 0;
		}else if ($_LINK_MEMBER_LEVEL_ID_==12 or $_LINK_MEMBER_LEVEL_ID_==13 or $_LINK_MEMBER_LEVEL_ID_==19){//대리점 관리자 //학생
			$_LINK_MEMBER_CENTER_ID_ = $Row["Center_CenterID"];
			$_LINK_MEMBER_BRANCH_ID_ = $Row["Center_BranchID"];
			$_LINK_MEMBER_BRANCH_GROUP_ID_ = $Row["Center_BranchGroupID"];
			$_LINK_MEMBER_COMPANY_ID_ = $Row["Center_CompanyID"];
			$_LINK_MEMBER_FRANCHISE_ID_ = $Row["Center_FranchiseID"];
			$_LINK_MEMBER_MANAGER_ID_ = 0;
			$_LINK_MEMBER_EDUCENTER_ID_ = 0;
			$_LINK_MEMBER_TEACHER_ID_ = 0;
			$_LINK_MEMBER_STAFF_ID_ = 0;
		}else if ($_LINK_MEMBER_LEVEL_ID_==15){//강사 
			$_LINK_MEMBER_CENTER_ID_ = 0;
			$_LINK_MEMBER_BRANCH_ID_ = 0;
			$_LINK_MEMBER_BRANCH_GROUP_ID_ = 0;
			$_LINK_MEMBER_COMPANY_ID_ = 0;
			$_LINK_MEMBER_FRANCHISE_ID_ = $Row["Teacher_FranchiseID"];
			$_LINK_MEMBER_MANAGER_ID_ = 0;
			$_LINK_MEMBER_EDUCENTER_ID_ = $Row["Teacher_EduCenterID"];
			$_LINK_MEMBER_TEACHER_ID_ = $Row["Teacher_TeacherID"];
			$_LINK_MEMBER_STAFF_ID_ = 0;
		}


	}

}else{

	$_MEMBER_ID_ = "";
	$_MEMBER_LOGIN_ID_ = "";
	$_MEMBER_NAME_ = "";
	$_MEMBER_LEVEL_ID_ = 20;
	$_MEMBER_LANG_ID_ = 0;
	$_MEMBER_CENTER_ID_ = 0;
	$_MEMBER_BRANCH_ID_ = 0;
	$_MEMBER_BRANCH_GROUP_ID_ = 0;
	$_MEMBER_COMPANY_ID_ = 0;
	$_MEMBER_FRANCHISE_ID_ = 0;
	$_MEMBER_MANAGER_ID_ = 0;
	$_MEMBER_EDUCENTER_ID_ = 0;
	$_MEMBER_TEACHER_ID_ = 0;
	$_MEMBER_STAFF_ID_ = 0;


	$_LINK_MEMBER_ID_ = "";
	$_LINK_MEMBER_LOGIN_ID_ = "";
	$_LINK_MEMBER_NAME_ = "";
	$_LINK_MEMBER_LEVEL_ID_ = 20;
	$_LINK_MEMBER_LANG_ID_ = 0;
	$_LINK_MEMBER_CENTER_ID_ = 0;
	$_LINK_MEMBER_BRANCH_ID_ = 0;
	$_LINK_MEMBER_BRANCH_GROUP_ID_ = 0;
	$_LINK_MEMBER_COMPANY_ID_ = 0;
	$_LINK_MEMBER_FRANCHISE_ID_ = 0;
	$_LINK_MEMBER_MANAGER_ID_ = 0;
	$_LINK_MEMBER_EDUCENTER_ID_ = 0;
	$_LINK_MEMBER_TEACHER_ID_ = 0;
	$_LINK_MEMBER_STAFF_ID_ = 0;
	
	
	if ($DenyGuest == true){
		
		$StrLoginPage = "login_form.php";
		
		$RedirectUrl = urlencode(basename($_SERVER['REQUEST_URI']));
		if ($RedirectUrl!=""){
			header("Location: ".$StrLoginPage."?RedirectUrl=$RedirectUrl"); 
			exit;
		}else{
			header("Location: ".$StrLoginPage); 
			exit;
		}

	}



}

$LangID = $_LINK_MEMBER_LANG_ID_;


$ShLanguage = "ko";
if ($LangID!=0){
	$ShLanguage = "en";
}


if ( ($DomainSiteID==4 || $DomainSiteID==5) && strpos($url, "/mypage/") != false){ //잉글리시텔, 토마스
	include_once('../includes/language_home.php');
}else{
	include_once('./includes/language_home.php');
}
?>