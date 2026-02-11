<?php
if (isset($_COOKIE["LoginAdminID"])){

	
	
	$Sql = "select 
				A.MemberID, 
				A.MemberLoginID, 
				A.MemberName, 
				A.MemberLevelID,
				A.MemberLanguageID,

				A.CenterID as Center_CenterID,
				ifnull(B.BranchID, 0) as Center_BranchID,
				ifnull(B.CenterPayType, 0) as Center_CenterPayType,
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

			where A.MemberLoginID=:LoginAdminID and A.MemberState=1";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':LoginAdminID', $_COOKIE["LoginAdminID"]);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$_ADMIN_ID_ = $Row["MemberID"];
	$_ADMIN_LOGIN_ID_ = $Row["MemberLoginID"];
	$_ADMIN_NAME_ = $Row["MemberName"];
	$_ADMIN_LEVEL_ID_ = $Row["MemberLevelID"];
	$_ADMIN_LANG_ID_ = $Row["MemberLanguageID"];

	$_ADMIN_CENTER_PAY_TYPE_ = $Row["Center_CenterPayType"];

	if ($_ADMIN_LEVEL_ID_==0 || $_ADMIN_LEVEL_ID_==1 || $_ADMIN_LEVEL_ID_==2){//마스터
		$_ADMIN_CENTER_ID_ = 0;
		$_ADMIN_BRANCH_ID_ = 0;
		$_ADMIN_BRANCH_GROUP_ID_ = 0;
		$_ADMIN_COMPANY_ID_ = 0;
		$_ADMIN_FRANCHISE_ID_ = 0;
		$_ADMIN_MANAGER_ID_ = 0;
		$_ADMIN_EDUCENTER_ID_ = 0;
		$_ADMIN_TEACHER_ID_ = 0;
		$_ADMIN_STAFF_ID_ = 0;
	}else if ($_ADMIN_LEVEL_ID_==3 || $_ADMIN_LEVEL_ID_==4){//프랜차이즈 관리자
		$_ADMIN_CENTER_ID_ = 0;
		$_ADMIN_BRANCH_ID_ = 0;
		$_ADMIN_BRANCH_GROUP_ID_ = 0;
		$_ADMIN_COMPANY_ID_ = 0;
		$_ADMIN_FRANCHISE_ID_ = $Row["Staff_FranchiseID"];
		$_ADMIN_MANAGER_ID_ = 0;
		$_ADMIN_EDUCENTER_ID_ = 0;
		$_ADMIN_TEACHER_ID_ = 0;
		$_ADMIN_STAFF_ID_ = $Row["Staff_StaffID"];
	}else if ($_ADMIN_LEVEL_ID_==5){//영업본부 관리자
		$_ADMIN_CENTER_ID_ = 0;
		$_ADMIN_BRANCH_ID_ = 0;
		$_ADMIN_BRANCH_GROUP_ID_ = 0;
		$_ADMIN_COMPANY_ID_ = 0;
		$_ADMIN_FRANCHISE_ID_ = $Row["Manager_FranchiseID"];
		$_ADMIN_MANAGER_ID_ = $Row["Manager_ManagerID"];
		$_ADMIN_EDUCENTER_ID_ = 0;
		$_ADMIN_TEACHER_ID_ = 0;
		$_ADMIN_STAFF_ID_ = 0;
	}else if ($_ADMIN_LEVEL_ID_==6 || $_ADMIN_LEVEL_ID_==7){//대표지사 관리자
		$_ADMIN_CENTER_ID_ = 0;
		$_ADMIN_BRANCH_ID_ = 0;
		$_ADMIN_BRANCH_GROUP_ID_ = $Row["BranchGroup_BranchGroupID"];
		$_ADMIN_COMPANY_ID_ = $Row["BranchGroup_CompanyID"];
		$_ADMIN_FRANCHISE_ID_ = $Row["BranchGroup_FranchiseID"];
		$_ADMIN_MANAGER_ID_ = 0;
		$_ADMIN_EDUCENTER_ID_ = 0;
		$_ADMIN_TEACHER_ID_ = 0;
		$_ADMIN_STAFF_ID_ = 0;
	}else if ($_ADMIN_LEVEL_ID_==9 || $_ADMIN_LEVEL_ID_==10){//지사 관리자
		$_ADMIN_CENTER_ID_ = 0;
		$_ADMIN_BRANCH_ID_ = $Row["Branch_BranchID"];
		$_ADMIN_BRANCH_GROUP_ID_ = $Row["Branch_BranchGroupID"];
		$_ADMIN_COMPANY_ID_ = $Row["Branch_CompanyID"];
		$_ADMIN_FRANCHISE_ID_ = $Row["Branch_FranchiseID"];
		$_ADMIN_MANAGER_ID_ = 0;
		$_ADMIN_EDUCENTER_ID_ = 0;
		$_ADMIN_TEACHER_ID_ = 0;
		$_ADMIN_STAFF_ID_ = 0;
	}else if ($_ADMIN_LEVEL_ID_==12 || $_ADMIN_LEVEL_ID_==13 || $_ADMIN_LEVEL_ID_==19){//대리점 관리자 //학생
		$_ADMIN_CENTER_ID_ = $Row["Center_CenterID"];
		$_ADMIN_BRANCH_ID_ = $Row["Center_BranchID"];
		$_ADMIN_BRANCH_GROUP_ID_ = $Row["Center_BranchGroupID"];
		$_ADMIN_COMPANY_ID_ = $Row["Center_CompanyID"];
		$_ADMIN_FRANCHISE_ID_ = $Row["Center_FranchiseID"];
		$_ADMIN_MANAGER_ID_ = 0;
		$_ADMIN_EDUCENTER_ID_ = 0;
		$_ADMIN_TEACHER_ID_ = 0;
		$_ADMIN_STAFF_ID_ = 0;
	}else if ($_ADMIN_LEVEL_ID_==15){//강사 
		$_ADMIN_CENTER_ID_ = 0;
		$_ADMIN_BRANCH_ID_ = 0;
		$_ADMIN_BRANCH_GROUP_ID_ = 0;
		$_ADMIN_COMPANY_ID_ = 0;
		$_ADMIN_FRANCHISE_ID_ = $Row["Teacher_FranchiseID"];
		$_ADMIN_MANAGER_ID_ = 0;
		$_ADMIN_EDUCENTER_ID_ = $Row["Teacher_EduCenterID"];
		$_ADMIN_TEACHER_ID_ = $Row["Teacher_TeacherID"];
		$_ADMIN_STAFF_ID_ = 0;
	}


	if (isset($_COOKIE["LinkLoginAdminID"])){ // 상위 관리자가 하위 관리자 권한으로 접속


		$Sql = "select 
					A.MemberID, 
					A.MemberLoginID, 
					A.MemberName, 
					A.MemberLevelID,
					A.MemberLanguageID,

					A.CenterID as Center_CenterID,
					ifnull(B.BranchID, 0) as Center_BranchID,
					ifnull(B.CenterPayType, 0) as Center_CenterPayType,
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

				where A.MemberLoginID=:LinkLoginAdminID and A.MemberState=1";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':LinkLoginAdminID', $_COOKIE["LinkLoginAdminID"]);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;

		$_LINK_ADMIN_ID_ = $Row["MemberID"];
		$_LINK_ADMIN_LOGIN_ID_ = $Row["MemberLoginID"];
		$_LINK_ADMIN_NAME_ = $Row["MemberName"];
		$_LINK_ADMIN_LEVEL_ID_ = $Row["MemberLevelID"];
		$_LINK_ADMIN_LANG_ID_ = $Row["MemberLanguageID"];

		$_LINK_ADMIN_CENTER_PAY_TYPE_ = $Row["Center_CenterPayType"];

		if ($_LINK_ADMIN_LEVEL_ID_==0 || $_LINK_ADMIN_LEVEL_ID_==1 || $_ADMIN_LEVEL_ID_==2){//마스터
			$_LINK_ADMIN_CENTER_ID_ = 0;
			$_LINK_ADMIN_BRANCH_ID_ = 0;
			$_LINK_ADMIN_BRANCH_GROUP_ID_ = 0;
			$_LINK_ADMIN_COMPANY_ID_ = 0;
			$_LINK_ADMIN_FRANCHISE_ID_ = 0;
			$_LINK_ADMIN_MANAGER_ID_ = 0;
			$_LINK_ADMIN_EDUCENTER_ID_ = 0;
			$_LINK_ADMIN_TEACHER_ID_ = 0;
			$_LINK_ADMIN_STAFF_ID_ = 0;
		}else if ($_LINK_ADMIN_LEVEL_ID_==3 || $_LINK_ADMIN_LEVEL_ID_==4){//프랜차이즈 관리자
			$_LINK_ADMIN_CENTER_ID_ = 0;
			$_LINK_ADMIN_BRANCH_ID_ = 0;
			$_LINK_ADMIN_BRANCH_GROUP_ID_ = 0;
			$_LINK_ADMIN_COMPANY_ID_ = 0;
			$_LINK_ADMIN_FRANCHISE_ID_ = $Row["Staff_FranchiseID"];
			$_LINK_ADMIN_MANAGER_ID_ = 0;
			$_LINK_ADMIN_EDUCENTER_ID_ = 0;
			$_LINK_ADMIN_TEACHER_ID_ = 0;
			$_LINK_ADMIN_STAFF_ID_ = $Row["Staff_StaffID"];
		}else if ($_LINK_ADMIN_LEVEL_ID_==5){//영업본부 관리자
			$_LINK_ADMIN_CENTER_ID_ = 0;
			$_LINK_ADMIN_BRANCH_ID_ = 0;
			$_LINK_ADMIN_BRANCH_GROUP_ID_ = 0;
			$_LINK_ADMIN_COMPANY_ID_ = 0;
			$_LINK_ADMIN_FRANCHISE_ID_ = $Row["Manager_FranchiseID"];
			$_LINK_ADMIN_MANAGER_ID_ = $Row["Manager_ManagerID"];
			$_LINK_ADMIN_EDUCENTER_ID_ = 0;
			$_LINK_ADMIN_TEACHER_ID_ = 0;
			$_LINK_ADMIN_STAFF_ID_ = 0;
		}else if ($_LINK_ADMIN_LEVEL_ID_==6 || $_LINK_ADMIN_LEVEL_ID_==7){//대표지사 관리자
			$_LINK_ADMIN_CENTER_ID_ = 0;
			$_LINK_ADMIN_BRANCH_ID_ = 0;
			$_LINK_ADMIN_BRANCH_GROUP_ID_ = $Row["BranchGroup_BranchGroupID"];
			$_LINK_ADMIN_COMPANY_ID_ = $Row["BranchGroup_CompanyID"];
			$_LINK_ADMIN_FRANCHISE_ID_ = $Row["BranchGroup_FranchiseID"];
			$_LINK_ADMIN_MANAGER_ID_ = 0;
			$_LINK_ADMIN_EDUCENTER_ID_ = 0;
			$_LINK_ADMIN_TEACHER_ID_ = 0;
			$_LINK_ADMIN_STAFF_ID_ = 0;
		}else if ($_LINK_ADMIN_LEVEL_ID_==9 || $_LINK_ADMIN_LEVEL_ID_==10){//지사 관리자
			$_LINK_ADMIN_CENTER_ID_ = 0;
			$_LINK_ADMIN_BRANCH_ID_ = $Row["Branch_BranchID"];
			$_LINK_ADMIN_BRANCH_GROUP_ID_ = $Row["Branch_BranchGroupID"];
			$_LINK_ADMIN_COMPANY_ID_ = $Row["Branch_CompanyID"];
			$_LINK_ADMIN_FRANCHISE_ID_ = $Row["Branch_FranchiseID"];
			$_LINK_ADMIN_MANAGER_ID_ = 0;
			$_LINK_ADMIN_EDUCENTER_ID_ = 0;
			$_LINK_ADMIN_TEACHER_ID_ = 0;
			$_LINK_ADMIN_STAFF_ID_ = 0;
		}else if ($_LINK_ADMIN_LEVEL_ID_==12 || $_LINK_ADMIN_LEVEL_ID_==13 || $_LINK_ADMIN_LEVEL_ID_==19){//대리점 관리자 //학생
			$_LINK_ADMIN_CENTER_ID_ = $Row["Center_CenterID"];
			$_LINK_ADMIN_BRANCH_ID_ = $Row["Center_BranchID"];
			$_LINK_ADMIN_BRANCH_GROUP_ID_ = $Row["Center_BranchGroupID"];
			$_LINK_ADMIN_COMPANY_ID_ = $Row["Center_CompanyID"];
			$_LINK_ADMIN_FRANCHISE_ID_ = $Row["Center_FranchiseID"];
			$_LINK_ADMIN_MANAGER_ID_ = 0;
			$_LINK_ADMIN_EDUCENTER_ID_ = 0;
			$_LINK_ADMIN_TEACHER_ID_ = 0;
			$_LINK_ADMIN_STAFF_ID_ = 0;
		}else if ($_LINK_ADMIN_LEVEL_ID_==15){//강사 
			$_LINK_ADMIN_CENTER_ID_ = 0;
			$_LINK_ADMIN_BRANCH_ID_ = 0;
			$_LINK_ADMIN_BRANCH_GROUP_ID_ = 0;
			$_LINK_ADMIN_COMPANY_ID_ = 0;
			$_LINK_ADMIN_FRANCHISE_ID_ = $Row["Teacher_FranchiseID"];
			$_LINK_ADMIN_MANAGER_ID_ = 0;
			$_LINK_ADMIN_EDUCENTER_ID_ = $Row["Teacher_EduCenterID"];
			$_LINK_ADMIN_TEACHER_ID_ = $Row["Teacher_TeacherID"];
			$_LINK_ADMIN_STAFF_ID_ = 0;
		}


	}

}else{

	$_ADMIN_ID_ = "";
	$_ADMIN_LOGIN_ID_ = "";
	$_ADMIN_NAME_ = "";
	$_ADMIN_LEVEL_ID_ = 20;
	$_ADMIN_LANG_ID_ = 0;
	$_ADMIN_CENTER_ID_ = 0;
	$_ADMIN_BRANCH_ID_ = 0;
	$_ADMIN_BRANCH_GROUP_ID_ = 0;
	$_ADMIN_COMPANY_ID_ = 0;
	$_ADMIN_FRANCHISE_ID_ = 0;
	$_ADMIN_MANAGER_ID_ = 0;
	$_ADMIN_EDUCENTER_ID_ = 0;
	$_ADMIN_TEACHER_ID_ = 0;
	$_ADMIN_STAFF_ID_ = 0;
	$_ADMIN_CENTER_PAY_TYPE_ = 0;


	$_LINK_ADMIN_ID_ = "";
	$_LINK_ADMIN_LOGIN_ID_ = "";
	$_LINK_ADMIN_NAME_ = "";
	$_LINK_ADMIN_LEVEL_ID_ = 20;
	$_LINK_ADMIN_LANG_ID_ = 0;
	$_LINK_ADMIN_CENTER_ID_ = 0;
	$_LINK_ADMIN_BRANCH_ID_ = 0;
	$_LINK_ADMIN_BRANCH_GROUP_ID_ = 0;
	$_LINK_ADMIN_COMPANY_ID_ = 0;
	$_LINK_ADMIN_FRANCHISE_ID_ = 0;
	$_LINK_ADMIN_MANAGER_ID_ = 0;
	$_LINK_ADMIN_EDUCENTER_ID_ = 0;
	$_LINK_ADMIN_TEACHER_ID_ = 0;
	$_LINK_ADMIN_STAFF_ID_ = 0;
	$_LINK_ADMIN_CENTER_PAY_TYPE_ = 0;

	header("Location: login_form.php"); 
	exit;
}

$LangID = $_LINK_ADMIN_LANG_ID_;


$ShLanguage = "ko";
if ($LangID!=0){
	$ShLanguage = "en";
}

include_once($_SERVER['DOCUMENT_ROOT'].'/includes/language_lms.php');
?>