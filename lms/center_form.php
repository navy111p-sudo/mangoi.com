<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
?>
<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="ko"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="ko"> <!--<![endif]-->
<head>
<?
include_once('./includes/common_meta_tag.php');
include_once('./inc_header.php');
include_once('./inc_common_form_css.php');
?>
<!-- ============== only this page css ============== -->

<!-- ============== only this page css ============== -->
<!-- ============== common.css ============== -->
<link rel="stylesheet" type="text/css" href="css/common.css" />
<!-- ============== common.css ============== -->


</head>

<body class="disable_transitions sidebar_main_open sidebar_main_swipe">
<?php
$MainMenuID = 12;
$SubMenuID = 1201;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
?>



<?php
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$CenterID = isset($_REQUEST["CenterID"]) ? $_REQUEST["CenterID"] : "";
$PageTabID = isset($_REQUEST["PageTabID"]) ? $_REQUEST["PageTabID"] : "";



if ($PageTabID==""){
	$PageTabID = "1";
}

if ($CenterID!=""){


	$Sql = "
			select 
					A.*,
					AES_DECRYPT(UNHEX(A.CenterPhone1),:EncryptionKey) as DecCenterPhone1,
					AES_DECRYPT(UNHEX(A.CenterPhone2),:EncryptionKey) as DecCenterPhone2,
					AES_DECRYPT(UNHEX(A.CenterPhone3),:EncryptionKey) as DecCenterPhone3,
					AES_DECRYPT(UNHEX(A.CenterEmail),:EncryptionKey) as DecCenterEmail,
					B.OnlineSiteName,
					C.MemberID,
					C.MemberLoginID,
					C.MemberLoginPW,
					C.MemberTimeZoneID,
					C.MemberLanguageID
			from Centers A 
				left outer join OnlineSites B on A.OnlineSiteID=B.OnlineSiteID 
				inner join Members C on A.CenterID=C.CenterID and C.MemberLevelID=12 
			where A.CenterID=:CenterID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':CenterID', $CenterID);
	$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$BranchID = $Row["BranchID"];
	$OnlineSiteID = $Row["OnlineSiteID"];
	$ManagerID = $Row["ManagerID"];
	$CenterName = $Row["CenterName"];
	$CenterManagerName = $Row["CenterManagerName"];
	$CenterUseMyRank = $Row["CenterUseMyRank"];
	//================ 전화번호 / 이메일 =============
	$CenterPhone1 = $Row["DecCenterPhone1"];
	$CenterPhone2 = $Row["DecCenterPhone2"];
	$CenterPhone3 = $Row["DecCenterPhone3"];
	$CenterEmail = $Row["DecCenterEmail"];
	//================ 전화번호 / 이메일 =============
	$CenterZip = $Row["CenterZip"];
	$CenterAddr1 = $Row["CenterAddr1"];
	$CenterAddr2 = $Row["CenterAddr2"];
	$CenterIntroText = $Row["CenterIntroText"];
	$CenterState = $Row["CenterState"];
	$CenterView = $Row["CenterView"];
	$OnlineSiteName = $Row["OnlineSiteName"];
	$CenterFreeTrialCount = $Row["CenterFreeTrialCount"];
	$CenterPricePerGroup = $Row["CenterPricePerGroup"];
	$CenterPricePerTime = $Row["CenterPricePerTime"];
	$CenterAcceptSms = $Row["CenterAcceptSms"];
	$CenterAcceptJoin = $Row["CenterAcceptJoin"];
	$MemberAcceptCallByTeacher = $Row["MemberAcceptCallByTeacher"];

	$CenterPayType = $Row["CenterPayType"];
	$CenterRenewType = $Row["CenterRenewType"];
	$CenterRenewStartYearMonthNum = $Row["CenterRenewStartYearMonthNum"];
	$CenterStudyEndDate = $Row["CenterStudyEndDate"];

	//Members 
	$MemberID = $Row["MemberID"];
	$MemberLoginID = $Row["MemberLoginID"];
	$MemberLoginPW = $Row["MemberLoginPW"];
	$MemberTimeZoneID = $Row["MemberTimeZoneID"];
	$MemberLanguageID = $Row["MemberLanguageID"];
	$CheckedID = 1;
	$CheckedEmail = 1;
	$CenterPerShVersion = $Row["CenterPerShVersion"];
	$CenterPerShAllow = $Row["CenterPerShAllow"];

}else{
	$BranchID = "";
	$OnlineSiteID = "";
	$ManagerID = "";
	$CenterName = "";
	$CenterManagerName = "";
	$CenterUseMyRank = 1;
	//================ 전화번호 / 이메일 =============
	$CenterPhone1 = "--";
	$CenterPhone2 = "--";
	$CenterPhone3 = "--";
	$CenterEmail = "@";
	//================ 전화번호 / 이메일 =============
	$CenterZip = "";
	$CenterAddr1 = "";
	$CenterAddr2 = "";
	$CenterIntroText = "";
	$CenterState = 1;
	$CenterView = 1;
	$OnlineSiteName = "";
	$CenterFreeTrialCount = 0;
	$CenterPricePerGroup = 0;
	$CenterPricePerTime = 0;
	$CenterAcceptSms = 1;
	$CenterAcceptJoin = 1;
	$MemberAcceptCallByTeacher = 1;

	$CenterPayType = 1;
	$CenterRenewType = 1;
	$CenterRenewStartYearMonthNum = date("Ym");
	$CenterStudyEndDate = date("Y")."-".date("m")."-".substr("0".date("t", strtotime(date("Y-m-d"))),-2);

	//Members 
	$MemberID = "";
	$MemberLoginID = "";
	$MemberLoginPW = "";
	$MemberTimeZoneID = "";
	$MemberLanguageID = "";
	$CheckedID = 0;
	$CheckedEmail = 0; 
	$CenterPerShVersion = 1;
	$CenterPerShAllow = 1;
}

//================ 전화번호 / 이메일 =============
$ArrCenterPhone1 = explode("-", $CenterPhone1);
$ArrCenterPhone2 = explode("-", $CenterPhone2);
$ArrCenterPhone3 = explode("-", $CenterPhone3);
$ArrCenterEmail = explode("@", $CenterEmail);

$CenterPhone1_1 = $ArrCenterPhone1[0];
$CenterPhone1_2 = $ArrCenterPhone1[1];
$CenterPhone1_3 = $ArrCenterPhone1[2];

$CenterPhone2_1 = $ArrCenterPhone2[0];
$CenterPhone2_2 = $ArrCenterPhone2[1];
$CenterPhone2_3 = $ArrCenterPhone2[2];

$CenterPhone3_1 = $ArrCenterPhone3[0];
$CenterPhone3_2 = $ArrCenterPhone3[1];
$CenterPhone3_3 = $ArrCenterPhone3[2];

$CenterEmail_1 = $ArrCenterEmail[0];
$CenterEmail_2 = $ArrCenterEmail[1];
//================ 전화번호 / 이메일 =============


$MemberLoginNewPW = "";
$MemberLoginNewPW2 = "";


$HideOnlineSiteID = 0;
$HideManagerID = 0;
$HideBranchID = 0;

$AddWhere_OnlineSite = "";
$AddWhere_Manager = "";
$AddWhere_Branch = "";

if ($_LINK_ADMIN_LEVEL_ID_>10){
	$BranchID = $_LINK_ADMIN_BRANCH_ID_;

	$HideOnlineSiteID = 1;
	$HideManagerID = 1;
	$HideBranchID = 1;

	$AddWhere_OnlineSite = "";
	$AddWhere_Manager = "";
	$AddWhere_Branch = " and A.BranchID=".$_LINK_ADMIN_BRANCH_ID_." ";
}else if ($_LINK_ADMIN_LEVEL_ID_>7){
	$BranchID = $_LINK_ADMIN_BRANCH_ID_;
	
	$HideOnlineSiteID = 1;
	$HideManagerID = 1;
	$HideBranchID = 1;

	$AddWhere_OnlineSite = "";
	$AddWhere_Manager = "";
	$AddWhere_Branch = " and A.BranchID=".$_LINK_ADMIN_BRANCH_ID_." ";
}else if ($_LINK_ADMIN_LEVEL_ID_>5){// 4(프랜차이즈직원) -> 영업본부 추가로 5로 변경
	$HideOnlineSiteID = 1;
	$HideManagerID = 1;
	$HideBranchID = 0;

	$AddWhere_OnlineSite = "";
	$AddWhere_Manager = "";
	$AddWhere_Branch = " and B.BranchGroupID=".$_LINK_ADMIN_BRANCH_GROUP_ID_." ";
}else if ($_LINK_ADMIN_LEVEL_ID_>1){
	$HideOnlineSiteID = 0;
	$HideManagerID = 0;
	$HideBranchID = 0;

	$AddWhere_OnlineSite = " and A.FranchiseID=".$_LINK_ADMIN_FRANCHISE_ID_." ";
	$AddWhere_Manager = " and A.FranchiseID=".$_LINK_ADMIN_FRANCHISE_ID_." ";
	$AddWhere_Branch = " and C.FranchiseID=".$_LINK_ADMIN_FRANCHISE_ID_." ";
}

// =================================== 수업인원시간구성관리 ===================================

$AddSqlWhere = "1=1";
$ListParam2 = "1=1";
$PaginationParam = "1=1";


$CurrentPage = isset($_REQUEST["CurrentPage"]) ? $_REQUEST["CurrentPage"] : "";
$PageListNum = isset($_REQUEST["PageListNum"]) ? $_REQUEST["PageListNum"] : "";
$SearchText = isset($_REQUEST["SearchText"]) ? $_REQUEST["SearchText"] : "";
$SearchState = isset($_REQUEST["SearchState"]) ? $_REQUEST["SearchState"] : "";


if (!$CurrentPage){
    $CurrentPage = 1;    
}
if (!$PageListNum){
    $PageListNum = 10;
}


if ($PageListNum!=""){
    $ListParam2 = $ListParam2 . "&PageListNum=" . $PageListNum;
}

if ($SearchState==""){
    $SearchState = "1";
}    
        
if ($SearchState!="100"){
    $ListParam2 = $ListParam2 . "&SearchState=" . $SearchState;
    $AddSqlWhere = $AddSqlWhere . " and A.CenterClassState=$SearchState ";
}
$AddSqlWhere = $AddSqlWhere . " and A.CenterClassState<>0 ";
$ListParam2 = str_replace("&", "^^", $ListParam2);


$Sql_2 = "
        select 
            * 

        from CenterClasses A 

        where ".$AddSqlWhere." and CenterID=:CenterID order by A.CenterClassID asc";//." limit $StartRowNum, $PageListNum";
$Stmt_2 = $DbConn->prepare($Sql_2);
$Stmt_2->bindParam(':CenterID', $CenterID);
$Stmt_2->execute();
$Stmt_2->setFetchMode(PDO::FETCH_ASSOC);

$Weekend = array(
	"0"=>"일요일",
	"1"=>"월요일",
	"2"=>"화요일",
	"3"=>"수요일",
	"4"=>"목요일",
	"5"=>"금요일",
	"6"=>"토요일",
);

// =================================== 수업인원시간구성관리 =================================== //

// =========================== 디바이스구성관리 ==============================

$AddSqlWhere = "1=1";
$ListParam3 = "1=1";


$PaginationParam = $ListParam3;
$ListParam3 = str_replace("&", "^^", $ListParam3);


$Sql_3 = "
		select 
			A.*, 
			B.CenterName 
		from CenterDevices A 
		inner join Centers B on A.CenterID = B.CenterID 
		where ".$AddSqlWhere." and A.CenterID=:CenterID order by A.CenterDeviceID asc";//." limit $StartRowNum, $PageListNum";
$Stmt_3 = $DbConn->prepare($Sql_3);
$Stmt_3->bindParam(':CenterID', $CenterID);
$Stmt_3->execute();
$Stmt_3->setFetchMode(PDO::FETCH_ASSOC);

// =========================== 디바이스구성관리 ============================== //

// =========================== 수업인원구성관리 ==============================

$AddSqlWhere = "1=1";
$ListParam4 = "1=1";


$AddSqlWhere = $AddSqlWhere . " and A.CenterClassMemberState<>0 ";
$ListParam4 = str_replace("&", "^^", $ListParam4);

$Sql_4 = "
		select 
			A.*, 
			B.CenterClassName,
			C.MemberName,
			D.CenterDeviceName
		


		from CenterClassMembers A 
		inner join CenterClasses B on A.CenterClassID=B.CenterClassID 
		inner join Members C on A.MemberID=C.MemberID 
		left outer join CenterDevices D on A.CenterDeviceID=D.CenterDeviceID 

		where ".$AddSqlWhere." and B.CenterID=:CenterID order by A.CenterClassMemberID asc";//." limit $StartRowNum, $PageListNum";
$Stmt_4 = $DbConn->prepare($Sql_4);
$Stmt_4->bindParam(':CenterID', $CenterID);
$Stmt_4->execute();
$Stmt_4->setFetchMode(PDO::FETCH_ASSOC);

// =========================== 수업인원구성관리 ============================== //

// =========================== 교사 및 직원관리 ==============================
$AddSqlWhere = "1=1";
$ListParam5 = "1=1";


$AddSqlWhere = $AddSqlWhere . " and A.MemberState<>0 ";
$ListParam5 = str_replace("&", "^^", $ListParam5);


$Sql_5 = "
		select 
			A.*, 
			AES_DECRYPT(UNHEX(A.MemberPhone1),:EncryptionKey) as DecMemberPhone1, 
			AES_DECRYPT(UNHEX(A.MemberEmail),:EncryptionKey) as DecMemberEmail 
		from Members A 
		where ".$AddSqlWhere." and A.CenterID=:CenterID and A.MemberLevelID=13 order by A.MemberID asc";//." limit $StartRowNum, $PageListNum";
$Stmt_5 = $DbConn->prepare($Sql_5);
$Stmt_5->bindParam(':CenterID', $CenterID);
$Stmt_5->bindParam(':EncryptionKey', $EncryptionKey);
$Stmt_5->execute();
$Stmt_5->setFetchMode(PDO::FETCH_ASSOC);

// =========================== 교사 및 직원관리 ============================== //

// =========================== 학부모관리 ==============================
$AddSqlWhere = "1=1";
$ListParam6 = "1=1";


$AddSqlWhere = $AddSqlWhere . " and A.MemberState<>0 ";

$ListParam6 = str_replace("&", "^^", $ListParam6);

$Sql_6 = "
		select 
			A.*, 
			(select sum(AA.MemberPoint) from MemberPoints AA where AA.MemberID=A.MemberID and AA.MemberPointState=1) as MemberPoint,
			AES_DECRYPT(UNHEX(A.MemberPhone1),:EncryptionKey) as DecMemberPhone1 
		from Members A 
		where ".$AddSqlWhere." and A.CenterID=:CenterID and A.MemberLevelID=18 order by A.MemberID asc";//." limit $StartRowNum, $PageListNum";
$Stmt_6 = $DbConn->prepare($Sql_6);
$Stmt_6->bindParam(':CenterID', $CenterID);
$Stmt_6->bindParam(':EncryptionKey', $EncryptionKey);
$Stmt_6->execute();
$Stmt_6->setFetchMode(PDO::FETCH_ASSOC);

// =========================== 학부모관리 ============================== //
?>


<div id="page_content">
	<div id="page_content_inner">
		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="CenterID" value="<?=$CenterID?>">
		<input type="hidden" name="ListParam" value="<?=$ListParam?>">
		<input type="hidden" name="PageTabID" value="<?=$PageTabID?>">
		<input type="hidden" name="MemberID" value="<?=$MemberID?>">
		<input type="hidden" name="CheckedID" value="<?=$CheckedID?>">
		<input type="hidden" name="CheckedEmail" value="<?=$CheckedEmail?>">
		<input type="hidden" name="MemberLoginPW" value="<?=$MemberLoginPW?>">
		<input type="hidden" name="CenterStudyEndDate" value="<?=$CenterStudyEndDate?>">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<!--
						<div class="user_heading_avatar fileinput fileinput-new" data-provides="fileinput">
							<div class="fileinput-new thumbnail">
								<img src="assets/img/avatars/user.png" alt="user avatar"/>
							</div>
							<div class="fileinput-preview fileinput-exists thumbnail"></div>
							<div class="user_avatar_controls">
								<span class="btn-file">
									<span class="fileinput-new"><i class="material-icons">&#xE2C6;</i></span>
									<span class="fileinput-exists"><i class="material-icons">&#xE86A;</i></span>
									<input type="file" name="user_edit_avatar_control" id="user_edit_avatar_control">
								</span>
								<a href="#" class="btn-file fileinput-exists" data-dismiss="fileinput"><i class="material-icons">&#xE5CD;</i></a>
							</div>
						</div>
						-->
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$CenterName?></span><span class="sub-heading" id="user_edit_position"><?=$대리점정보[$LangID]?></span></h2>
						</div>
						<!--
						<div class="md-fab-wrapper">
							<div class="md-fab md-fab-toolbar md-fab-small md-fab-accent">
								<i class="material-icons">&#xE8BE;</i>
								<div class="md-fab-toolbar-actions">
									<button type="submit" id="user_edit_save" data-uk-tooltip="{cls:'uk-tooltip-small',pos:'bottom'}" title="Save"><i class="material-icons md-color-white">&#xE161;</i></button>
									<button type="submit" id="user_edit_print" data-uk-tooltip="{cls:'uk-tooltip-small',pos:'bottom'}" title="Print"><i class="material-icons md-color-white">&#xE555;</i></button>
									<button type="submit" id="user_edit_delete" data-uk-tooltip="{cls:'uk-tooltip-small',pos:'bottom'}" title="Delete"><i class="material-icons md-color-white">&#xE872;</i></button>
								</div>
							</div>
						</div>
						-->
					</div>
					<div class="user_content">
						<ul id="user_edit_tabs" class="uk-tab" data-uk-tab="{connect:'#user_edit_tabs_content'}" style="display:<?if ($CenterID==""){?>none<?}?>;">
							<li <?if ($PageTabID=="1"){?>class="uk-active"<?}?>><a href="#"><?=$가맹점정보[$LangID]?></a></li>
							<li style="display:none"><a href="#"><?=$수업시간구성관리[$LangID]?></a></li>
							<li style="display:none"><a href="#"><?=$디바이스구성관리[$LangID]?></a></li>
							<li style="display:none"><a href="#"><?=$수업인원구성관리[$LangID]?></a></li>
							<li <?if ($PageTabID=="5"){?>class="uk-active"<?}?>><a href="#" style="display:<?if ($_LINK_ADMIN_LEVEL_ID_==13){?>none<?}?>"><?=$교사_및_직원관리[$LangID]?></a></li>
							<li <?if ($PageTabID=="6"){?>class="uk-active"<?}?>><a href="#" style="display:<?if ($_LINK_ADMIN_LEVEL_ID_==13){?>none<?}?>"><?=$학부모관리[$LangID]?></a></li>
							<li <?if ($PageTabID=="7"){?>class="uk-active"<?}?>><a href="#"><?=$메시지내역[$LangID]?></a></li>
							<li <?if ($PageTabID=="8"){?>class="uk-active"<?}?>><a href="#"><?=$세금계산서정보[$LangID]?></a></li>
						</ul>
						<ul id="user_edit_tabs_content" class="uk-switcher uk-margin">
							<li>
								<div class="uk-margin-top">
									<h3 class="full_width_in_card heading_c">
										<?=$소속[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										
										<input type="hidden" name="OnlineSiteID" value="1">

										<!--
										<div class="uk-width-medium-4-10" style="padding-top:7px;display:<?if ($HideOnlineSiteID==1){?>none<?}?>;">
											
										
											<select id="OnlineSiteID" name="OnlineSiteID" class="uk-width-1-1" data-md-select2 data-allow-clear="true" data-placeholder="사이트 선택" style="width:100%;"/>
												<option value="0">미지정</option>
												<?
												$Sql2 = "select 
																A.*
														from OnlineSites A 
															inner join Franchises C on A.FranchiseID=C.FranchiseID 
														where A.OnlineSiteState<>0 and C.FranchiseState<>0 ".$AddWhere_OnlineSite."
														order by A.OnlineSiteState asc, A.OnlineSiteName asc";
												
												$Stmt2 = $DbConn->prepare($Sql2);
												$Stmt2->execute();
												$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
												
												$OldSelectOnlineSiteState = -1;
												while($Row2 = $Stmt2->fetch()) {
													$SelectOnlineSiteID = $Row2["OnlineSiteID"];
													$SelectOnlineSiteName = $Row2["OnlineSiteName"];
													$SelectOnlineSiteState = $Row2["OnlineSiteState"];
												
													if ($OldSelectOnlineSiteState!=$SelectOnlineSiteState){
														if ($OldSelectOnlineSiteState!=-1){
															echo "</optgroup>";
														}
														
														if ($SelectOnlineSiteState==1){
															echo "<optgroup label=\"$사이트[$LangID]($운영중[$LangID])\">";
														}else if ($SelectOnlineSiteState==2){
															echo "<optgroup label=\"$사이트[$LangID]($미운영[$LangID])\">";
														}
													}
													$OldSelectOnlineSiteState = $SelectOnlineSiteState;
												?>

												<option value="<?=$SelectOnlineSiteID?>" <?if ($OnlineSiteID==$SelectOnlineSiteID){?>selected<?}?>><?=$SelectOnlineSiteName?></option>
												<?
												}
												$Stmt2 = null;
												?>
											</select>

											
										</div>
										-->

										<div class="uk-width-medium-2-10" style="padding-top:7px;display:<?if ($HideManagerID==1){?>none<?}?>;">
											<select id="ManagerID" name="ManagerID" class="uk-width-1-1" data-md-select2 data-allow-clear="true" data-placeholder="<?=$영업본부선택[$LangID]?>" style="width:100%;"/>
												<option value="0"><?=$미지정[$LangID]?></option>
												<?
												$Sql2 = "select 
																A.*
														from Managers A 
															inner join Franchises B on A.FranchiseID=B.FranchiseID 
														where A.ManagerState<>0 and B.FranchiseState<>0 ".$AddWhere_Manager."
														order by A.ManagerState asc, A.ManagerName asc";
												
												$Stmt2 = $DbConn->prepare($Sql2);
												$Stmt2->execute();
												$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
												
												$OldSelectManagerState = -1;
												while($Row2 = $Stmt2->fetch()) {
													$SelectManagerID = $Row2["ManagerID"];
													$SelectManagerName = $Row2["ManagerName"];
													$SelectManagerState = $Row2["ManagerState"];
												
													if ($OldSelectManagerState!=$SelectManagerState){
														if ($OldSelectManagerState!=-1){
															echo "</optgroup>";
														}
														
														if ($SelectManagerState==1){
															echo "<optgroup label=\"$영업본부[$LangID]($운영중[$LangID])\">";
														}else if ($SelectManagerState==2){
															echo "<optgroup label=\"$영업본부[$LangID]($미운영[$LangID])\">";
														}
													}
													$OldSelectManagerState = $SelectManagerState;
												?>

												<option value="<?=$SelectManagerID?>" <?if ($ManagerID==$SelectManagerID){?>selected<?}?>><?=$SelectManagerName?></option>
												<?
												}
												$Stmt2 = null;
												?>
											</select>
										</div>
										<div class="uk-width-medium-2-10" style="padding-top:7px;display:<?if ($HideBranchID==1){?>none<?}?>;">
											<select id="BranchID" name="BranchID" class="uk-width-1-1" data-md-select2 data-allow-clear="true" data-placeholder="<?=$미지정[$LangID]?>" style="width:100%;"/>
												<option value=""></option>
												<?
												$Sql2 = "select 
																A.*,
																B.BranchGroupName,
																C.CompanyName,
																D.FranchiseName 
														from Branches A 
															inner join BranchGroups B on A.BranchGroupID=B.BranchGroupID 
															inner join Companies C on B.CompanyID=C.CompanyID 
															inner join Franchises D on C.FranchiseID=D.FranchiseID 
														where A.BranchState<>0 and B.BranchGroupState<>0 and C.CompanyState<>0 and D.FranchiseState<>0 ".$AddWhere_Branch."
														order by A.BranchState asc, D.FranchiseName asc, C.CompanyName asc, B.BranchGroupName asc, A.BranchName asc";
												
												$Stmt2 = $DbConn->prepare($Sql2);
												$Stmt2->execute();
												$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
												
												$OldSelectBranchState = -1;
												while($Row2 = $Stmt2->fetch()) {
													$SelectBranchID = $Row2["BranchID"];
													$SelectBranchName = $Row2["BranchName"];
													$SelectBranchState = $Row2["BranchState"];
													$SelectBranchGroupName = $Row2["BranchGroupName"];
													$SelectCompanyName = $Row2["CompanyName"];
													$SelectFranchiseName = $Row2["FranchiseName"];
													if ($_LINK_ADMIN_LEVEL_ID_ <=2){
														$StrSelectFranchiseName = " (".$SelectFranchiseName.")";
													}else{
														$StrSelectFranchiseName = "";
													}
												
													if ($OldSelectBranchState!=$SelectBranchState){
														if ($OldSelectBranchState!=-1){
															echo "</optgroup>";
														}
														
														if ($SelectBranchState==1){
															echo "<optgroup label=\"$지사[$LangID]($운영중[$LangID])\">";
														}else if ($SelectBranchState==2){
															echo "<optgroup label=\"$지사[$LangID]($미운영[$LangID])\">";
														}
													}
													$OldSelectBranchState = $SelectBranchState;
												?>

												<option value="<?=$SelectBranchID?>" <?if ($BranchID==$SelectBranchID){?>selected<?}?>><?=$SelectBranchName?><?=$StrSelectFranchiseName?></option>
												<?
												}
												$Stmt2 = null;
												?>
											</select>
										</div>
										<div class="uk-width-medium-2-10" style="padding-top:7px;">
											<select name="MemberTimeZoneID" class="uk-width-1-1" data-md-select2 data-allow-clear="true" style="width:100%;">
											<option value=""><?=$활동지역[$LangID]?></option>
											<?php

												//  멤버와 타임존을 조인하여 값을 가져올 것. id 가 매칭이 된다면 default 로 지정
												$Sql3 = "select Z.MemberTimeZoneName, Z.MemberTimeZoneID from MemberTimeZones Z";

												$Stmt3 = $DbConn->prepare($Sql3);
												$Stmt3->bindParam(':CenterID', $CenterID);
												$Stmt3->execute();
												$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
												while($Row3 = $Stmt3->fetch()) {
											?>
											<option value="<?=$Row3['MemberTimeZoneID']?>" <?if ($MemberTimeZoneID==$Row3['MemberTimeZoneID']){?>selected<?}?>><?=$Row3['MemberTimeZoneName']?></option>
											<?php
												}
											?>
											</select>
										</div>
									</div>
									<h3 class="full_width_in_card heading_c">
										<?=$대리점명_또는_관리자명[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-1-5">
											<label for="CenterName"><?=$대리점명[$LangID]?></label>
											<input type="text" id="CenterName" name="CenterName" value="<?=$CenterName?>" class="md-input label-fixed"/>
										</div>
										<div class="uk-width-medium-1-5">
											<label for="CenterManagerName"><?=$관리자[$LangID]?></label>
											<input type="text" id="CenterManagerName" name="CenterManagerName" value="<?=$CenterManagerName?>" class="md-input label-fixed"/>
										</div>
										<div class="uk-width-medium-1-5">
											<label for="CenterPricePerGroup"><?=$그룹_수업료_그룹당_주1회[$LangID]?></label>
											<input type="number" id="CenterPricePerGroup" name="CenterPricePerGroup" value="<?=$CenterPricePerGroup?>" class="md-input label-fixed allownumericwithoutdecimal" <?if ($_LINK_ADMIN_LEVEL_ID_>=12){?>readonly<?}?>/>
										</div>
										<div class="uk-width-medium-1-5">
											<label for="CenterPricePerTime"><?=$수업료_10분[$LangID]?></label>
											<input type="number" id="CenterPricePerTime" name="CenterPricePerTime" value="<?=$CenterPricePerTime?>" class="md-input label-fixed allownumericwithoutdecimal" <?if ($_LINK_ADMIN_LEVEL_ID_>=12){?>readonly<?}?>/>
										</div>
										<div class="uk-width-medium-1-5">
											<label for="CenterFreeTrialCount"><?=$무료체험횟수[$LangID]?></label>
											<input type="number" id="CenterFreeTrialCount" name="CenterFreeTrialCount" value="<?=$CenterFreeTrialCount?>" class="md-input label-fixed allownumericwithoutdecimal" <?if ($_LINK_ADMIN_LEVEL_ID_>5){?>readonly<?}?>/>
										</div>
									</div>

									<div style="margin-top:30px;" class="uk-width-medium-3-5 uk-form-row" >
											<span class="icheck-inline">
												<input type="radio" id="MemberLanguageID0" name="MemberLanguageID" value="0" <?php if ($MemberLanguageID==0) { echo "checked";}?> data-md-icheck/>
												<label for="MemberLanguageID0" class="inline-label"><?=$한국어[$LangID]?></label>
											</span>
											<span class="icheck-inline">
												<input type="radio" id="MemberLanguageID1" name="MemberLanguageID" value="1" <?php if ($MemberLanguageID==1) { echo "checked";}?> data-md-icheck/>
												<label for="MemberLanguageID1" class="inline-label"><?=$영어[$LangID]?></label>
											</span>
											<span class="icheck-inline">
												<input type="radio" id="MemberLanguageID2" name="MemberLanguageID" value="2" <?php if ($MemberLanguageID==2) { echo "checked";}?> data-md-icheck/>
												<label for="MemberLanguageID2" class="inline-label"><?=$중국어[$LangID]?></label>
											</span>
											※ LMS 사용언어 입니다.
									</div>

									<div class="uk-width-medium-3-5 uk-input-group">
										<span class="icheck-inline">
											<input type="radio" id="MemberAcceptCallByTeacher_1" value="1" name="MemberAcceptCallByTeacher" <?if ($MemberAcceptCallByTeacher==1){?>checked<?}?> data-md-icheck/>
											<label class="inline-label" for="MemberAcceptCallByTeacher_1" ><?=$전화거부[$LangID]?></label>
										</span>
										<span class="icheck-inline">
											<input type="radio" id="MemberAcceptCallByTeacher_2" value="2" name="MemberAcceptCallByTeacher" <?if ($MemberAcceptCallByTeacher==2){?>checked<?}?> data-md-icheck/>
											<label class="inline-label" for="MemberAcceptCallByTeacher_2"><?=$전화수신[$LangID]?></label>
										</span>
										※ 수업지각 시, 강사로부터 전화수신여부
									</div>

									<h3 class="full_width_in_card heading_c" style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>4){?>none<?}?>;">
										<?=$새하_버전[$LangID]?>
									</h3>
									
									<div class="uk-width-medium-3-5 uk-input-group" style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>4){?>none<?}?>;">
										<span class="icheck-inline">
											<input type="radio" id="CenterPerShAllow1" name="CenterPerShAllow" value="1" <?php if ($CenterPerShAllow==1) { echo "checked";}?> data-md-icheck/>
											<label for="CenterPerShAllow1" class="inline-label"><?=$본사선택버전에_따름[$LangID]?></label>
										</span>
										<span class="icheck-inline">
											<input type="radio" id="CenterPerShAllow2" name="CenterPerShAllow" value="2" <?php if ($CenterPerShAllow==2) { echo "checked";}?> data-md-icheck/>
											<label for="CenterPerShAllow2" class="inline-label"><?=$대리점선택버전에_따름[$LangID]?></label>
										</span>
										※ 새하버전 선택 타입
									</div>

									<div class="uk-width-medium-3-5 uk-input-group" style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>4){?>none<?}?>;">
										<span class="icheck-inline">
											<input type="radio" id="CenterPerShVersion1" name="CenterPerShVersion" value="1" <?php if ($CenterPerShVersion==1) { echo "checked";}?> data-md-icheck/>
											<label for="CenterPerShVersion1" class="inline-label"><?=$신버전[$LangID]?></label>
										</span>
										<span class="icheck-inline">
											<input type="radio" id="CenterPerShVersion2" name="CenterPerShVersion" value="2" <?php if ($CenterPerShVersion==2) { echo "checked";}?> data-md-icheck/>
											<label for="CenterPerShVersion2" class="inline-label"><?=$구버전[$LangID]?></label>
										</span>
										※ 대리점 선택 버전
									</div>

									<h3 class="full_width_in_card heading_c">
										<?=$아이디_및_비밀번호[$LangID]?><?if ($MemberID!="") {?>(비밀번호는 변경을 원할때 입력하세요)<?}?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-4-10 uk-input-group">
											<label for="MemberLoginID"><?=$아이디[$LangID]?></label>
											<?if ($MemberID!="") {?>
											<input type="email" id="MemberLoginID" name="MemberLoginID" value="<?=$MemberLoginID?>" readonly class="md-input label-fixed" />
											<?}else{?>
											<input type="email" id="MemberLoginID" name="MemberLoginID" value="<?=$MemberLoginID?>" onkeyup="EnNewID()" class="md-input label-fixed" />
											<span class="uk-input-group-addon" id="BtnCheckID" style="display:<?if ($MemberID!="") {?>none<?}?>;"><a class="md-btn" href="javascript:CheckID();"><?=$중복확인[$LangID]?></a></span>
											<?}?>
										</div>
										<div class="uk-width-medium-3-10 uk-input-group">
											<label for="MemberLoginNewPW"><?=$비밀번호[$LangID]?></label>
											<input type="password" id="MemberLoginNewPW" name="MemberLoginNewPW" value="<?=$MemberLoginNewPW?>" class="md-input label-fixed" />
										</div>
										<div class="uk-width-medium-3-10 uk-input-group">
											<label for="MemberLoginNewPW2"><?=$비밀번호확인[$LangID]?></label>
											<input type="password" id="MemberLoginNewPW2" name="MemberLoginNewPW2" value="<?=$MemberLoginNewPW2?>" class="md-input label-fixed" />
										</div>
									</div>
									<h3 class="full_width_in_card heading_c">
										<?=$연락처[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-1-2 uk-input-group">
											<label for="CenterPhone1"><?=$전화번호[$LangID]?></label>
											<div class="md-input label-fixed" style="padding-top:10px;margin-bottom:10px;">
												<select name="CenterPhone1_1" class="Select" style="width:15%;height:30px;">
													<option value="010" <?if ($CenterPhone1_1=="010") {?>selected<?}?>>010</option>
													<option value="011" <?if ($CenterPhone1_1=="011") {?>selected<?}?>>011</option>
													<option value="016" <?if ($CenterPhone1_1=="016") {?>selected<?}?>>016</option>
													<option value="017" <?if ($CenterPhone1_1=="017") {?>selected<?}?>>017</option>
													<option value="018" <?if ($CenterPhone1_1=="018") {?>selected<?}?>>018</option>
													<option value="019" <?if ($CenterPhone1_1=="019") {?>selected<?}?>>019</option>
													<option value="070" <?if ($CenterPhone1_1=="070") {?>selected<?}?>>070</option>
													<option value="02"  <?if ($CenterPhone1_1=="02")  {?>selected<?}?>>02</option>
													<option value="031" <?if ($CenterPhone1_1=="031") {?>selected<?}?>>031</option>
													<option value="032" <?if ($CenterPhone1_1=="032") {?>selected<?}?>>032</option>
													<option value="033" <?if ($CenterPhone1_1=="033") {?>selected<?}?>>033</option>
													<option value="041" <?if ($CenterPhone1_1=="041") {?>selected<?}?>>041</option>
													<option value="042" <?if ($CenterPhone1_1=="042") {?>selected<?}?>>042</option>
													<option value="043" <?if ($CenterPhone1_1=="043") {?>selected<?}?>>043</option>
													<option value="044" <?if ($CenterPhone1_1=="044") {?>selected<?}?>>044</option>
													<option value="049" <?if ($CenterPhone1_1=="049") {?>selected<?}?>>049</option>
													<option value="051" <?if ($CenterPhone1_1=="051") {?>selected<?}?>>051</option>
													<option value="052" <?if ($CenterPhone1_1=="052") {?>selected<?}?>>052</option>
													<option value="053" <?if ($CenterPhone1_1=="053") {?>selected<?}?>>053</option>
													<option value="054" <?if ($CenterPhone1_1=="054") {?>selected<?}?>>054</option>
													<option value="055" <?if ($CenterPhone1_1=="055") {?>selected<?}?>>055</option>
													<option value="061" <?if ($CenterPhone1_1=="061") {?>selected<?}?>>061</option>
													<option value="062" <?if ($CenterPhone1_1=="062") {?>selected<?}?>>062</option>
													<option value="063" <?if ($CenterPhone1_1=="063") {?>selected<?}?>>063</option>
													<option value="064" <?if ($CenterPhone1_1=="064") {?>selected<?}?>>064</option>
													<option value="0505" <?if ($CenterPhone1_1=="0505") {?>selected<?}?>>0505</option>
													<option value="0502" <?if ($CenterPhone1_1=="0502") {?>selected<?}?>>0502</option>
												</select>
												<input type="text" name="CenterPhone1_2" style="width:15%;height:30px;padding-left:10px;" placeholder="" value="<?=$CenterPhone1_2?>" class="allownumericwithoutdecimal" maxlength="4"> 
												<input type="text" name="CenterPhone1_3" style="width:15%;height:30px;padding-left:10px;"  placeholder="" value="<?=$CenterPhone1_3?>" class="allownumericwithoutdecimal" maxlength="4">
											</div>
										</div>
										<div class="uk-width-medium-1-2 uk-input-group">
											<label for="CenterPhone2"><?=$휴대폰[$LangID]?></label>
											<div class="md-input label-fixed" style="padding-top:10px;margin-bottom:10px;">
												<select name="CenterPhone2_1" class="Select" style="width:15%;height:30px;">
													<option value="010" <?if ($CenterPhone2_1=="010") {?>selected<?}?>>010</option>
													<option value="011" <?if ($CenterPhone2_1=="011") {?>selected<?}?>>011</option>
													<option value="016" <?if ($CenterPhone2_1=="016") {?>selected<?}?>>016</option>
													<option value="017" <?if ($CenterPhone2_1=="017") {?>selected<?}?>>017</option>
													<option value="018" <?if ($CenterPhone2_1=="018") {?>selected<?}?>>018</option>
													<option value="019" <?if ($CenterPhone2_1=="019") {?>selected<?}?>>019</option>
													<option value="070" <?if ($CenterPhone2_1=="070") {?>selected<?}?>>070</option>
												</select>
												<input type="text" name="CenterPhone2_2" style="width:15%;height:30px;padding-left:10px;" placeholder="" value="<?=$CenterPhone2_2?>" class="allownumericwithoutdecimal" maxlength="4"> 
												<input type="text" name="CenterPhone2_3" style="width:15%;height:30px;padding-left:10px;"  placeholder="" value="<?=$CenterPhone2_3?>" class="allownumericwithoutdecimal" maxlength="4">
											</div>
										</div>
									</div>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-1-2 uk-input-group">
											<label for="CenterPhone3"><?=$팩스번호[$LangID]?></label>
											<div class="md-input label-fixed" style="padding-top:10px;margin-bottom:10px;">
												<select name="CenterPhone3_1" class="Select" style="width:15%;height:30px;">
													<option value="02"  <?if ($CenterPhone3_1=="02")  {?>selected<?}?>>02</option>
													<option value="031" <?if ($CenterPhone3_1=="031") {?>selected<?}?>>031</option>
													<option value="032" <?if ($CenterPhone3_1=="032") {?>selected<?}?>>032</option>
													<option value="033" <?if ($CenterPhone3_1=="033") {?>selected<?}?>>033</option>
													<option value="041" <?if ($CenterPhone3_1=="041") {?>selected<?}?>>041</option>
													<option value="042" <?if ($CenterPhone3_1=="042") {?>selected<?}?>>042</option>
													<option value="043" <?if ($CenterPhone3_1=="043") {?>selected<?}?>>043</option>
													<option value="044" <?if ($CenterPhone3_1=="044") {?>selected<?}?>>044</option>
													<option value="049" <?if ($CenterPhone3_1=="049") {?>selected<?}?>>049</option>
													<option value="051" <?if ($CenterPhone3_1=="051") {?>selected<?}?>>051</option>
													<option value="052" <?if ($CenterPhone3_1=="052") {?>selected<?}?>>052</option>
													<option value="053" <?if ($CenterPhone3_1=="053") {?>selected<?}?>>053</option>
													<option value="054" <?if ($CenterPhone3_1=="054") {?>selected<?}?>>054</option>
													<option value="055" <?if ($CenterPhone3_1=="055") {?>selected<?}?>>055</option>
													<option value="061" <?if ($CenterPhone3_1=="061") {?>selected<?}?>>061</option>
													<option value="062" <?if ($CenterPhone3_1=="062") {?>selected<?}?>>062</option>
													<option value="063" <?if ($CenterPhone3_1=="063") {?>selected<?}?>>063</option>
													<option value="064" <?if ($CenterPhone3_1=="064") {?>selected<?}?>>064</option>
													<option value="010" <?if ($CenterPhone3_1=="010") {?>selected<?}?>>010</option>
													<option value="011" <?if ($CenterPhone3_1=="011") {?>selected<?}?>>011</option>
													<option value="016" <?if ($CenterPhone3_1=="016") {?>selected<?}?>>016</option>
													<option value="017" <?if ($CenterPhone3_1=="017") {?>selected<?}?>>017</option>
													<option value="018" <?if ($CenterPhone3_1=="018") {?>selected<?}?>>018</option>
													<option value="019" <?if ($CenterPhone3_1=="019") {?>selected<?}?>>019</option>
													<option value="070" <?if ($CenterPhone3_1=="070") {?>selected<?}?>>070</option>
													<option value="0505" <?if ($CenterPhone1_1=="0505") {?>selected<?}?>>0505</option>
													<option value="0502" <?if ($CenterPhone1_1=="0502") {?>selected<?}?>>0502</option>
												</select>
												<input type="text" name="CenterPhone3_2" style="width:15%;height:30px;padding-left:10px;" placeholder="" value="<?=$CenterPhone3_2?>" class="allownumericwithoutdecimal" maxlength="4"> 
												<input type="text" name="CenterPhone3_3" style="width:15%;height:30px;padding-left:10px;"  placeholder="" value="<?=$CenterPhone3_3?>" class="allownumericwithoutdecimal" maxlength="4">
											</div>
										</div>
										<div class="uk-width-medium-1-2 uk-input-group">
											<label for="CenterEmail"><?=$이메일[$LangID]?></label>
											<div class="md-input label-fixed" style="padding-top:10px;margin-bottom:10px;">
												<input type="text" name="CenterEmail_1" id="CenterEmail_1" style="width:20%;height:30px;padding-left:10px;" value="<?=$CenterEmail_1?>" onkeyup="EnNewEmail()"> 
												<div style="display:inline-block;width:20px;margin-top:8px;">@</div> 
												<input type="text" name="CenterEmail_2" id="CenterEmail_2" style="width:20%;height:30px;padding-left:10px;" value="<?=$CenterEmail_2?>" onkeyup="EnNewEmail()">
												<select name="CenterEmail_3" class="Select" style="width:22%;height:30px;margin-bottom:0px;" onchange="SetEmailName()">
													<option value=""><?=$직접입력[$LangID]?></option>
													<option value="naver.com">naver.com</option>
													<option value="empal.com">empal.com</option>
													<option value="empas.com">empas.com</option>
													<option value="daum.net">daum.net</option>
													<option value="hanmail.net">hanmail.net</option>
													<option value="hotmail.com">hotmail.com</option>
													<option value="dreamwiz.com">dreamwiz.com</option>
													<option value="korea.com">korea.com</option>
													<option value="paran.com">paran.com</option>
													<option value="nate.com">nate.com</option>
													<option value="lycos.co.kr">lycos.co.kr</option>
													<option value="yahoo.co.kr">yahoo.co.kr</option>
												</select>
												<span class="uk-input-group-addon" id="BtnCheckEmail" style="display:<?if ($MemberID!="") {?>none<?}else{?>inline<?}?>;"><a class="md-btn" href="javascript:CheckEmail();">중복확인</a></span>
											</div>
										</div>
									</div>
									<h3 class="full_width_in_card heading_c">
										<?=$주소[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-3-10 uk-input-group">
											<label for="CenterZip"><?=$우편번호[$LangID]?></label>
											<input type="text" id="CenterZip" name="CenterZip" value="<?=$CenterZip?>" class="md-input label-fixed" />
											<span class="uk-input-group-addon"><a class="md-btn" href="javascript:ExecDaumPostcode();"><?=$검색[$LangID]?></a></span>

										</div>
										<div class="uk-width-medium-3-10 uk-input-group">
											<label for="CenterAddr1"><?=$주소[$LangID]?></label>
											<input type="text" id="CenterAddr1" name="CenterAddr1" value="<?=$CenterAddr1?>" class="md-input label-fixed" />
										</div>
										<div class="uk-width-medium-4-10 uk-input-group">
											<label for="CenterAddr2"><?=$상세주소[$LangID]?></label>
											<input type="text" id="CenterAddr2" name="CenterAddr2" value="<?=$CenterAddr2?>" class="md-input label-fixed" />
										</div>
									</div>

									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-1-1">
											<label for="CenterIntroText"><?=$메모[$LangID]?></label>
											<textarea class="md-input" name="CenterIntroText" id="CenterIntroText" cols="30" rows="4"><?=$CenterIntroText?></textarea>
										</div>
									</div>


								</div>
							</li>
							<!-- =========================== 수업시간구성관리 ============================== -->
							<li>
								<!--
								<form name="SearchForm" method="get">
								<div class="md-card" style="margin-bottom:10px;">
									<div class="md-card-content">
										<div class="uk-grid" data-uk-grid-margin="">
											
											<div class="uk-width-medium-6-10">
											</div>

											<div class="uk-width-medium-2-10">
												<label for="SearchText">항목명</label>
												<input type="text" class="md-input" id="SearchText" name="SearchText" value="<?=$SearchText?>">
											</div>

											<div class="uk-width-medium-1-10">
												<div class="uk-margin-small-top">
													<select id="SearchState" name="SearchState" onchange="SearchSubmit()" data-md-selectize  data-md-selectize-bottom>
														<option value="100" <?if ($SearchState=="100"){?>selected<?}?>>전체</option>
														<option value="1" <?if ($SearchState=="1"){?>selected<?}?>>운영중</option>
														<option value="2" <?if ($SearchState=="2"){?>selected<?}?>>미운영</option>
													</select>
												</div>
											</div>


											<div class="uk-width-medium-1-10 uk-text-center">
												<a href="javascript:SearchSubmit();" class="md-btn md-btn-primary uk-margin-small-top">검색</a>
											</div>
											
										</div>
									</div>
								</div>
								</form>
								-->
								<div class="uk-grid" data-uk-grid-margin>
									<div class="uk-width-1-1">
										<div class="uk-overflow-container">
											<table class="uk-table uk-table-align-vertical">
												<thead>
													<tr>
														<th style="width:10%;" nowrap>No</th>
														<th style="width:20%;" nowrap><?=$수업명[$LangID]?></th>
														<th style="width:10%;" nowrap><?=$수업요일[$LangID]?></th>
														<th style="width:10%;" nowrap><?=$수업시작시간[$LangID]?></th>
														<th style="width:10%;" nowrap><?=$수업종료시간[$LangID]?></th>
														<th style="width:10%;" nowrap><?=$수업등록일자[$LangID]?></th>
														<th style="width:10%" nowrap><?=$수업진행상태[$LangID]?></th>
													</tr>
												</thead>
												<tbody>
													
													<?php
													$ListCount = 1;
													while($Row_2 = $Stmt_2->fetch()) {
														$CenterClassID = $Row_2["CenterClassID"];
														$CenterClassName = $Row_2["CenterClassName"];
														$CenterClassWeekNum = $Row_2["CenterClassWeekNum"];
														$CenterClassStartTime = $Row_2["CenterClassStartTime"];
														$CenterClassEndTime = $Row_2["CenterClassEndTime"];
														$CenterClassState = $Row_2["CenterClassState"];
														$CenterClassRegDateTime = $Row_2["CenterClassRegDateTime"];
														
														if ($CenterClassState==1){
															$StrCenterClassState = "<span class=\"ListState_1\"><?=$운영중[$LangID]?></span>";
														}else if ($CenterClassState==2){
															$StrCenterClassState = "<span class=\"ListState_2\"><?=$미운영[$LangID]?></span>";
														}
													?>
													<tr>
														<td class="uk-text-nowrap uk-table-td-center"><?=$ListCount?></td>
														<td class="uk-text-nowrap uk-table-td-center"><a href="javascript:OpenCenterClassForm(<?=$CenterClassID?>)"><?=$CenterClassName?></a></td>
														<td class="uk-text-nowrap uk-table-td"><?=$Weekend[$CenterClassWeekNum]?></td>
														<td class="uk-text-nowrap uk-table-td"><?=$CenterClassStartTime?></td>
														<td class="uk-text-nowrap uk-table-td"><?=$CenterClassEndTime?></td>
														<td class="uk-text-nowrap uk-table-td-center"><?=$CenterClassRegDateTime?></td>
														<td class="uk-text-nowrap uk-table-td-center"><?=$StrCenterClassState?></td>
														<!--
														<td class="uk-text-nowrap uk-table-td-center">
														<?php
															if($SearchText=="" && $SearchState!="100") {
														?>
															<div class="uk-text-nowrap uk-table-td-center">
																<a href="javascript:CenterClassListOrder(<?=$CenterClassID?>, 1)" class="top_menu_toggle"><i class="material-icons md-24" style="display:inline-block" >arrow_drop_up</i></a>
																<a href="javascript:CenterClassListOrder(<?=$CenterClassID?>, 0)" class="top_menu_toggle"><i class="material-icons md-24" style="display:inline-block">arrow_drop_down</i></a>
															</div>
														<?php
															} else {
														?>
															-
														<?php
															}
														?>
															
														</td>
														-->
													</tr>
													<?php
														$ListCount ++;
													}
													$Stmt_2 = null;
													?>
												</tbody>
											</table>
										</div>
										

										<?php			
										//include_once('./inc_pagination.php');
										?>

										<div class="uk-form-row" style="text-align:center;margin-top:20px;">
											<a type="button" href="javascript:OpenCenterClassForm('')" class="md-btn md-btn-primary"><?=$신규등록[$LangID]?></a>
										</div>

									</div>
								</div>
							</li>
							<!-- =========================== 수업시간구성관리 ============================== -->
							<li>
							<!-- =========================== 디바이스구성관리 ============================== -->
								<!--
								<form name="SearchForm" method="get">
								<div class="md-card" style="margin-bottom:10px;">
									<div class="md-card-content">
										<div class="uk-grid" data-uk-grid-margin="">
											
											<div class="uk-width-medium-6-10">
											</div>

											<div class="uk-width-medium-2-10">
												<label for="SearchText">항목명</label>
												<input type="text" class="md-input" id="SearchText" name="SearchText" value="<?=$SearchText?>">
											</div>

											<div class="uk-width-medium-1-10">
												<div class="uk-margin-small-top">
													<select id="SearchState" name="SearchState" onchange="SearchSubmit()" data-md-selectize  data-md-selectize-bottom>
														<option value="100" <?if ($SearchState=="100"){?>selected<?}?>>전체</option>
														<option value="1" <?if ($SearchState=="1"){?>selected<?}?>>운영중</option>
														<option value="2" <?if ($SearchState=="2"){?>selected<?}?>>미운영</option>
													</select>
												</div>
											</div>


											<div class="uk-width-medium-1-10 uk-text-center">
												<a href="javascript:SearchSubmit();" class="md-btn md-btn-primary uk-margin-small-top">검색</a>
											</div>
											
										</div>
									</div>
								</div>
								</form>
								-->

								<div class="uk-grid" data-uk-grid-margin>
									<div class="uk-width-1-1">
										<div class="uk-overflow-container">
											<table class="uk-table uk-table-align-vertical">
												<thead>
													<tr>
														<th style="width:8%;" nowrap>No</th>
														<th style="width:10%;" nowrap><?=$센터명[$LangID]?></th>
														<th style="width:20%;" nowrap><?=$이름[$LangID]?></th>
														<th style="width:10%;" nowrap><?=$타입[$LangID]?></th>
														<th style="width:10%;" nowrap><?=$등록일자[$LangID]?></th>
														<th style="width:10%" nowrap><?=$상태[$LangID]?></th>
													</tr>
												</thead>
												<tbody>
													
													<?php
													$ListCount = 1;
													while($Row_3 = $Stmt_3->fetch()) {
														$CenterDeviceID = $Row_3["CenterDeviceID"];
														$CenterID = $Row_3["CenterID"];
														$CenterName = $Row_3["CenterName"];
														$CenterDeviceName = $Row_3["CenterDeviceName"];
														$CenterDeviceType = $Row_3["CenterDeviceType"];
														$CenterDeviceRegDateTime = $Row_3["CenterDeviceRegDateTime"];
														$CenterDeviceState = $Row_3["CenterDeviceState"];
														
														if ($CenterDeviceState==1){
															$StrCenterDeviceState = "<span class=\"ListState_1\">운영중</span>";
														}else if ($CenterDeviceState==2){
															$StrCenterDeviceState = "<span class=\"ListState_2\">미운영</span>";
														}

														if ($CenterDeviceType==1) {
															$StrCenterDeviceType = "PC";
														} else if ($CenterDeviceType==2) {
															$StrCenterDeviceType = "PHONE";
														}
													?>
													<tr>
														<td class="uk-text-nowrap uk-table-td-center"><?=$ListCount?></td>
														<td class="uk-text-nowrap uk-table-td-center"><?=$CenterName?></td>
														<td class="uk-text-nowrap uk-table-td-center"><a href="javascript:OpenCenterDeviceForm(<?=$CenterDeviceID?>)"><?=$CenterDeviceName?></a></td>
														<td class="uk-text-nowrap uk-table-td-center"><?=$StrCenterDeviceType?></td>
														<td class="uk-text-nowrap uk-table-td-center"><?=$CenterDeviceRegDateTime?></td>
														<td class="uk-text-nowrap uk-table-td-center"><?=$StrCenterDeviceState?></td>
														<!--
														<td class="uk-text-nowrap uk-table-td-center">
														<?php
															if($SearchText=="" && $SearchState!="100") {
														?>
															<div class="uk-text-nowrap uk-table-td-center">
																<a href="javascript:CenterDeviceListOrder(<?=$CenterDeviceID?>, 1)" class="top_menu_toggle"><i class="material-icons md-24" style="display:inline-block" >arrow_drop_up</i></a>
																<a href="javascript:CenterDeviceListOrder(<?=$CenterDeviceID?>, 0)" class="top_menu_toggle"><i class="material-icons md-24" style="display:inline-block">arrow_drop_down</i></a>
															</div>
														<?php
															} else {
														?>
															-
														<?php
															}
														?>
															
														</td>
														-->
													</tr>
													<?php
														$ListCount ++;
													}
													$Stmt_3 = null;
													?>
												</tbody>
											</table>
										</div>
										

										<?php			
										//include_once('./inc_pagination.php');
										?>

										<div class="uk-form-row" style="text-align:center;margin-top:20px;">
											<a type="button" href="javascript:OpenCenterDeviceForm('')" class="md-btn md-btn-primary"><?=$신규등록[$LangID]?></a>
										</div>

									</div>
								</div>
							<!-- =========================== 디바이스구성관리 ============================== -->
							</li>
							<li>
							<!-- =========================== 수업인원구성관리 ============================== -->
							<!--
								<form name="SearchForm" method="get">
								<div class="md-card" style="margin-bottom:10px;">
									<div class="md-card-content">
										<div class="uk-grid" data-uk-grid-margin="">
											
											<div class="uk-width-medium-6-10">
											</div>

											<div class="uk-width-medium-2-10">
												<label for="SearchText">항목명</label>
												<input type="text" class="md-input" id="SearchText" name="SearchText" value="<?=$SearchText?>">
											</div>

											<div class="uk-width-medium-1-10">
												<div class="uk-margin-small-top">
													<select id="SearchState" name="SearchState" onchange="SearchSubmit()" data-md-selectize  data-md-selectize-bottom>
														<option value="100" <?if ($SearchState=="100"){?>selected<?}?>>전체</option>
														<option value="1" <?if ($SearchState=="1"){?>selected<?}?>>운영중</option>
														<option value="2" <?if ($SearchState=="2"){?>selected<?}?>>미운영</option>
													</select>
												</div>
											</div>


											<div class="uk-width-medium-1-10 uk-text-center">
												<a href="javascript:SearchSubmit();" class="md-btn md-btn-primary uk-margin-small-top">검색</a>
											</div>
											
										</div>
									</div>
								</div>
								</form>
							-->

								<div class="uk-grid" data-uk-grid-margin>
									<div class="uk-width-1-1">
										<div class="uk-overflow-container">
											<table class="uk-table uk-table-align-vertical">
												<thead>
													<tr>
														<th style="width:8%;" nowrap>No</th>
														<th style="width:20%;" nowrap><?=$수업명[$LangID]?></th>
														<th style="width:10%;" nowrap><?=$학생명[$LangID]?></th>
														<th style="width:10%" nowrap><?=$등록일자[$LangID]?></th>
														<th style="width:10%" nowrap><?=$상태[$LangID]?></th>
													</tr>
												</thead>
												<tbody>
													
													<?php
													$ListCount = 1;
													while($Row_4 = $Stmt_4->fetch()) {
														$CenterClassMemberID = $Row_4["CenterClassMemberID"];
														$CenterClassName = $Row_4["CenterClassName"];
														$MemberName = $Row_4["MemberName"];
														$CenterClassMemberRegDateTime = $Row_4["CenterClassMemberRegDateTime"];
														$CenterClassMemberState = $Row_4["CenterClassMemberState"];
														
														if ($CenterClassMemberState==1){
															$StrCenterClassMemberState = "<span class=\"ListState_1\">운영중</span>";
														}else if ($CenterClassMemberState==2){
															$StrCenterClassMemberState = "<span class=\"ListState_2\">미운영</span>";
														}

													?>
													<tr>
														<td class="uk-text-nowrap uk-table-td-center"><?=$ListCount?></td>
														<td class="uk-text-nowrap uk-table-td-center"><?=$CenterClassName?></td>
														<td class="uk-text-nowrap uk-table-td-center"><a href="javascript:OpenCenterClassMemberForm(<?=$CenterClassMemberID?>)"><?=$MemberName?></a></td>
														<td class="uk-text-nowrap uk-table-td-center"><?=$CenterClassMemberRegDateTime?></td>
														<td class="uk-text-nowrap uk-table-td-center"><?=$StrCenterClassMemberState?></td>
														<!--
														<td class="uk-text-nowrap uk-table-td-center">
														<?php
															if($SearchText=="" && $SearchState!="100") {
														?>
															<div class="uk-text-nowrap uk-table-td-center">
																<a href="javascript:CenterClassMemberListOrder(<?=$CenterClassMemberID?>, 1)" class="top_menu_toggle"><i class="material-icons md-24" style="display:inline-block" >arrow_drop_up</i></a>
																<a href="javascript:CenterClassMemberListOrder(<?=$CenterClassMemberID?>, 0)" class="top_menu_toggle"><i class="material-icons md-24" style="display:inline-block">arrow_drop_down</i></a>
															</div>
														<?php
															} else {
														?>
															-
														<?php
															}
														?>
															
														</td>
														-->
													</tr>
													<?php
														$ListCount ++;
													}
													$Stmt = null;
													?>
												</tbody>
											</table>
										</div>
										

										<?php			
										//include_once('./inc_pagination.php');
										?>

										<div class="uk-form-row" style="text-align:center;margin-top:20px;">
											<a type="button" href="javascript:OpenCenterClassMemberForm('')" class="md-btn md-btn-primary"><?=$신규등록[$LangID]?></a>
										</div>

									</div>
								</div>
							<!-- =========================== 수업인원구성관리 ============================== -->
							</li>

							<li>
							<!-- =========================== 교사 및 직원관리 ============================== -->

								<div class="uk-grid" data-uk-grid-margin>
									<div class="uk-width-1-1">
										<div class="uk-overflow-container">
											<table class="uk-table uk-table-align-vertical">
												<thead>
													<tr>
														<th style="width:8%;" nowrap>No</th>
														<th style="width:20%;" nowrap><?=$이름[$LangID]?></th>
														<th style="width:10%;" nowrap><?=$아이디[$LangID]?></th>
														<th style="width:10%;" nowrap><?=$연락처[$LangID]?></th>
														<th style="width:10%" nowrap><?=$등록일자[$LangID]?></th>
														<th style="width:10%" nowrap><?=$상태[$LangID]?></th>
													</tr>
												</thead>
												<tbody>
													
													<?php
													$ListCount = 1;
													while($Row_5 = $Stmt_5->fetch()) {
														$TempMemberID = $Row_5["MemberID"];
														$MemberName = $Row_5["MemberName"];
														$MemberLoginID = $Row_5["MemberLoginID"];
														$DecMemberPhone1 = $Row_5["DecMemberPhone1"];
														$MemberRegDateTime = $Row_5["MemberRegDateTime"];
														$MemberState = $Row_5["MemberState"];
														
														if ($MemberState==1){
															$StrMemberState = "<span class=\"ListState_1\"><?=$운영중[$LangID]?></span>";
														}else if ($MemberState==2){
															$StrMemberState = "<span class=\"ListState_2\"><?=$미운영[$LangID]?></span>";
														}

													?>
													<tr>
														<td class="uk-text-nowrap uk-table-td-center"><?=$ListCount?></td>
														<td class="uk-text-nowrap uk-table-td-center"><a href="javascript:OpenCenterStaffForm(<?=$TempMemberID?>)"><?=$MemberName?></a></td>
														<td class="uk-text-nowrap uk-table-td-center"><?=$MemberLoginID?></td>
														<td class="uk-text-nowrap uk-table-td-center"><?=$DecMemberPhone1?></td>
														<td class="uk-text-nowrap uk-table-td-center"><?=$MemberRegDateTime?></td>
														<td class="uk-text-nowrap uk-table-td-center"><?=$StrMemberState?></td>
														<!--
														<td class="uk-text-nowrap uk-table-td-center">
														<?php
															if($SearchText=="" && $SearchState!="100") {
														?>
															<div class="uk-text-nowrap uk-table-td-center">
																<a href="javascript:CenterClassMemberListOrder(<?=$CenterClassMemberID?>, 1)" class="top_menu_toggle"><i class="material-icons md-24" style="display:inline-block" >arrow_drop_up</i></a>
																<a href="javascript:CenterClassMemberListOrder(<?=$CenterClassMemberID?>, 0)" class="top_menu_toggle"><i class="material-icons md-24" style="display:inline-block">arrow_drop_down</i></a>
															</div>
														<?php
															} else {
														?>
															-
														<?php
															}
														?>
															
														</td>
														-->
													</tr>
													<?php
														$ListCount ++;
													}
													$Stmt = null;
													?>
												</tbody>
											</table>
										</div>
										

										<?php			
										//include_once('./inc_pagination.php');
										?>

										<div class="uk-form-row" style="text-align:center;margin-top:20px;">
											<a type="button" href="javascript:OpenCenterStaffForm('')" class="md-btn md-btn-primary"><?=$신규등록[$LangID]?></a>
										</div>

									</div>
								</div>
							<!-- =========================== 교사 및 직원관리 ============================== -->
							</li>

							<li>
							<!-- =========================== 학부모관리 ============================== -->

								<div class="uk-grid" data-uk-grid-margin>
									<div class="uk-width-1-1">
										<div class="uk-overflow-container">
											<table class="uk-table uk-table-align-vertical">
												<thead>
													<tr>
														<th style="width:8%;" nowrap>No</th>
														<th style="" nowrap><?=$학부모명[$LangID]?></th>
														<th style="" nowrap><?=$아이디[$LangID]?></th>
														<th style="width:10%;" nowrap><?=$포인트[$LangID]?></th>
														<th style="width:15%;" nowrap><?=$연락처[$LangID]?></th>
														<th style="width:10%" nowrap><?=$등록일자[$LangID]?></th>
														<th style="width:10%" nowrap><?=$상태[$LangID]?></th>
													</tr>
												</thead>
												<tbody>
													
													<?php
													$ListCount = 1;
													while($Row_6 = $Stmt_6->fetch()) {

														$MemberID = $Row_6["MemberID"];
														$MemberName = $Row_6["MemberName"];
														$MemberLoginID = $Row_6["MemberLoginID"];
														$DecMemberPhone1 = $Row_6["DecMemberPhone1"];
														$MemberRegDateTime = $Row_6["MemberRegDateTime"];
														$MemberState = $Row_6["MemberState"];

														$MemberPoint = $Row_6["MemberPoint"];
														
														if ($MemberState==1){
															$StrMemberState = "<span class=\"ListState_1\"><?=$운영중[$LangID]?></span>";
														}else if ($MemberState==2){
															$StrMemberState = "<span class=\"ListState_2\"><?=$미운영[$LangID]?></span>";
														}

													?>
													<tr>
														<td class="uk-text-nowrap uk-table-td-center"><?=$ListCount?></td>
														<td class="uk-text-nowrap uk-table-td-center"><a href="javascript:OpenCenterParentForm(<?=$MemberID?>)"><?=$MemberName?></a></td>
														<td class="uk-text-nowrap uk-table-td-center"><?=$MemberLoginID?></td>

														<td class="uk-text-nowrap uk-table-td-center">
															<span style="color:#0000ff;"><?=number_format($MemberPoint,0)?></span>&nbsp;P
															<?if ($_LINK_ADMIN_LEVEL_ID_<=4){?>
															&nbsp;
															<a href="javascript:OpenMemberPointForm(<?=$MemberID?>);"><i class="material-icons">monetization_on</i></a>
															<?}?>
														</td>

														<td class="uk-text-nowrap uk-table-td-center"><?=$DecMemberPhone1?></td>
														<td class="uk-text-nowrap uk-table-td-center"><?=$MemberRegDateTime?></td>
														<td class="uk-text-nowrap uk-table-td-center"><?=$StrMemberState?></td>
														<!--
														<td class="uk-text-nowrap uk-table-td-center">
														<?php
															if($SearchText=="" && $SearchState!="100") {
														?>
															<div class="uk-text-nowrap uk-table-td-center">
																<a href="javascript:CenterClassMemberListOrder(<?=$CenterClassMemberID?>, 1)" class="top_menu_toggle"><i class="material-icons md-24" style="display:inline-block" >arrow_drop_up</i></a>
																<a href="javascript:CenterClassMemberListOrder(<?=$CenterClassMemberID?>, 0)" class="top_menu_toggle"><i class="material-icons md-24" style="display:inline-block">arrow_drop_down</i></a>
															</div>
														<?php
															} else {
														?>
															-
														<?php
															}
														?>
															
														</td>
														-->
													</tr>
													<?php
														$ListCount ++;
													}
													$Stmt = null;
													?>
												</tbody>
											</table>
										</div>
										

										<?php			
										//include_once('./inc_pagination.php');
										?>

										<div class="uk-form-row" style="text-align:center;margin-top:20px;">
											<a type="button" href="javascript:OpenCenterParentForm('')" class="md-btn md-btn-primary"><?=$신규등록[$LangID]?></a>
										</div>

									</div>
								</div>
							<!-- =========================== 학부모관리 ============================== -->
							</li>

							<!-- =========================== 메시지내역 ============================== -->
							<li>
								<table class="uk-table uk-table-align-vertical">
									<thead>
										<tr>
											<th width="15%" nowrap><?=$메시지_전송시간[$LangID]?></th>
											<th nowrap><?=$제목[$LangID]?></th>
											<th width="50%" nowrap><?=$내용[$LangID]?></th>
											<th width="10%">PUSH 전송</th>
											<th width="10%">SMS 전송</th>
											<!--<th width="10%"><?=$카카오_전송[$LangID]?></th>-->
										</tr>
									</thead>
							<tbody>
								<?
								$Sql = "
										select 
											A.*,
											B.MemberName,
											B.MemberLoginID, 
											ifnull(H.MemberName,'<?=$시스템[$LangID]?>') as RegMemberName,
											ifnull(H.MemberLoginID, '-') as RegMemberLoginID 
										from SendMessageLogs A
											inner join Members B on A.MemberID=B.MemberID 
											left outer join Members H on A.SendMemberID=H.MemberID 
										where A.MemberID=:MemberID 
										order by A.SendMessageLogRegDateTime desc";
								$Stmt = $DbConn->prepare($Sql);
								$Stmt->bindParam(':MemberID', $MemberID);
								$Stmt->execute();
								$Stmt->setFetchMode(PDO::FETCH_ASSOC);


								$ListCount = 1;
								while($Row = $Stmt->fetch()) {

									$MemberID = $Row["MemberID"];
									$SendMemberID = $Row["SendMemberID"];
									$SendTitle = $Row["SendTitle"];
									$SendMessage = $Row["SendMessage"];
									$SendMemo = $Row["SendMemo"];
									$SendMessageDateTime = $Row["SendMessageDateTime"];
									$SendMessageLogRegDateTime = $Row["SendMessageLogRegDateTime"];
									$SendMessageLogModiDateTime = $Row["SendMessageLogModiDateTime"];

									$UseSendPush = $Row["UseSendPush"];
									$UseSendSms = $Row["UseSendSms"];
									$UseSendKakao = $Row["UseSendKakao"];

									$DeviceToken = $Row["DeviceToken"];
									$DeviceType = $Row["DeviceType"];
									$PushMessageResult = $Row["PushMessageResult"];
									$PushMessageState = $Row["PushMessageState"];
									$PushMessageSendDateTime = $Row["PushMessageSendDateTime"];

									$SmsMessagePhoneNumber = $Row["SmsMessagePhoneNumber"];
									$SmsMessageResult = $Row["SmsMessageResult"];
									$SmsMessageState = $Row["SmsMessageState"];
									$SmsMessageSendDateTime = $Row["SmsMessageSendDateTime"];

									$KakaoMessagePhoneNumber = $Row["KakaoMessagePhoneNumber"];
									$KakaoMessageResult = $Row["KakaoMessageResult"];
									$KakaoMessageState = $Row["KakaoMessageState"];
									$KakaoMessageSendDateTime = $Row["KakaoMessageSendDateTime"];

									
									$MemberName = $Row["MemberName"];
									$MemberLoginID = $Row["MemberLoginID"];
									$RegMemberName = $Row["RegMemberName"];
									$RegMemberLoginID = $Row["RegMemberLoginID"];
									

									if ($UseSendPush==1){
										$StrUseSendPush = "<?=$요청[$LangID]?>";
									}else{
										$StrUseSendPush = "-";
									}
									if ($UseSendSms==1){
										$StrUseSendSms = "<?=$요청[$LangID]?>";
									}else{
										$StrUseSendSms = "-";
									}
									if ($UseSendKakao==1){
										$StrUseSendKakao = "<?=$요청[$LangID]?>";
									}else{
										$StrUseSendKakao = "-";
									}


									if ($PushMessageState==2){
										$StrPushMessageState = "<?=$완료[$LangID]?>";
									}else{
										$StrPushMessageState = "-";
									}
									if ($SmsMessageState==2){
										$StrSmsMessageState = "<?=$완료[$LangID]?>";
									}else{
										$StrSmsMessageState = "-";
									}
									if ($KakaoMessageState==2){
										$StrKakaoMessageState = "<?=$완료[$LangID]?>";
									}else{
										$StrKakaoMessageState = "-";
									}
									
								?>
								<tr>
									<td class="uk-text-nowrap uk-table-td-center"><?=$SendMessageDateTime?></td>
									<td class="uk-text-nowrap uk-table-td-center"><?=$SendTitle?></td>
									<td class="uk-text-nowrap uk-table-td-center"><?=$SendMessage?></td>
									<td class="uk-text-nowrap uk-table-td-center"><?=$StrUseSendPush?>(<?=$StrPushMessageState?>)</td>
									<td class="uk-text-nowrap uk-table-td-center"><?=$StrUseSendSms?>(<?=$StrSmsMessageState?>)</td>
									<!--<td class="uk-text-nowrap uk-table-td-center"><?=$StrUseSendKakao?>(<?=$StrKakaoMessageState?>)</td>-->
								</tr>
								<?php
									$ListCount ++;
								}
								$Stmt = null;
								?>


								</tbody>
								</table>
							</li>
							<!-- =========================== 메시지내역 ============================== -->

							<!-- =========================== 세금계산서 정보 ============================== -->
							<li>

								<?

								$OrganType = 1;
								$OrganID = $CenterID;
								
								$Sql_99 = "
										select 
												A.*
										from TaxMemberInfos A 
										where A.OrganType=:OrganType and A.OrganID=:OrganID";
								$Stmt_99 = $DbConn->prepare($Sql_99);
								$Stmt_99->bindParam(':OrganType', $OrganType);
								$Stmt_99->bindParam(':OrganID', $OrganID);
								$Stmt_99->execute();
								$Stmt_99->setFetchMode(PDO::FETCH_ASSOC);
								$Row_99 = $Stmt_99->fetch();
								$Stmt_99 = null;

								$TaxMemberInfoID = $Row_99["TaxMemberInfoID"];

								if ($TaxMemberInfoID){

									$CorpName = $Row_99["CorpName"];
									$CorpNum = $Row_99["CorpNum"];
									$TaxRegID = $Row_99["TaxRegID"];
									$CEOName = $Row_99["CEOName"];
									$Addr = $Row_99["Addr"];
									$BizType = $Row_99["BizType"];
									$BizClass = $Row_99["BizClass"];
									$ContactName1 = $Row_99["ContactName1"];
									$Email1 = $Row_99["Email1"];
									$TEL1 = $Row_99["TEL1"];
									$HP1 = $Row_99["HP1"];
									$ContactName2 = $Row_99["ContactName2"];
									$Email2 = $Row_99["Email2"];

								}else{

									$CorpName = "";
									$CorpNum = "";
									$TaxRegID = "";
									$CEOName = "";
									$Addr = "";
									$BizType = "";
									$BizClass = "";
									$ContactName1 = "";
									$Email1 = "";
									$TEL1 = "";
									$HP1 = "";
									$ContactName2 = "";
									$Email2 = "";
								}

								?>

								<table class="uk-table uk-table-align-vertical">
								<tbody>
									<tr>
										<th width="15%" nowrap><?=$사업자명[$LangID]?></th>
										<td class="uk-text-nowrap"><?=$CorpName?></td>
									</tr>
									<tr>
										<th width="15%" nowrap><?=$사업자등록번호[$LangID]?></th>
										<td class="uk-text-nowrap"><?=$CorpNum?></td>
									</tr>
									<tr>
										<th width="15%" nowrap><?=$종사업장번호[$LangID]?></th>
										<td class="uk-text-nowrap"><?=$TaxRegID?></td>
									</tr>
									<tr>
										<th width="15%" nowrap><?=$대표자명[$LangID]?></th>
										<td class="uk-text-nowrap"><?=$CEOName?></td>
									</tr>
									<tr>
										<th width="15%" nowrap><?=$전화번호[$LangID]?></th>
										<td class="uk-text-nowrap"><?=$TEL1?></td>
									</tr>
									<tr>
										<th width="15%" nowrap><?=$휴대폰번호[$LangID]?></th>
										<td class="uk-text-nowrap"><?=$HP1?></td>
									</tr>
									<tr>
										<th width="15%" nowrap><?=$주소[$LangID]?></th>
										<td class="uk-text-nowrap"><?=$Addr?></td>
									</tr>
									<tr>
										<th width="15%" nowrap><?=$업태[$LangID]?></th>
										<td class="uk-text-nowrap"><?=$BizType?></td>
									</tr>
									<tr>
										<th width="15%" nowrap><?=$종목[$LangID]?></th>
										<td class="uk-text-nowrap"><?=$BizClass?></td>
									</tr>
									<tr>
										<th width="15%" nowrap><?=$담당자명[$LangID]?></th>
										<td class="uk-text-nowrap"><?=$ContactName1?></td>
									</tr>
									<tr>
										<th width="15%" nowrap><?=$이메일[$LangID]?></th>
										<td class="uk-text-nowrap"><?=$Email1?></td>
									</tr>
									<tr>
										<th width="15%" nowrap><?=$_추가담당자명[$LangID]?></th>
										<td class="uk-text-nowrap"><?=$ContactName2?></td>
									</tr>
									<tr>
										<th width="15%" nowrap><?=$_추가이메일[$LangID]?></th>
										<td class="uk-text-nowrap"><?=$Email2?></td>
									</tr>


								</tbody>
								</table>

								<div class="uk-form-row" style="text-align:center;margin-top:20px;">
									<a type="button" href="javascript:OpenTaxMemberInfo(<?=$OrganType?>,<?=$OrganID?>)" class="md-btn md-btn-primary"><?=$정보수정[$LangID]?></a>
								</div>

							</li>
							<!-- =========================== 세금계산서 정보 ============================== -->



						</ul>
					</div>
				</div>
			</div>
			<div class="uk-width-large-3-10">
				<div class="md-card">
					<div class="md-card-content">
						<h3 class="heading_c uk-margin-medium-bottom"><?=$기타설정[$LangID]?></h3>
						<div class="uk-form-row" style="display:none;">
							<input type="checkbox" id="CenterView" name="CenterView" value="1" <?php if ($CenterView==1) { echo "checked";}?> data-switchery/>
							<label for="CenterView" class="inline-label"><?=$활성화[$LangID]?></label>
						</div>
						<hr class="md-hr" style="display:none;">

						<div class="uk-form-row">
							<input type="checkbox" id="CenterUseMyRank" name="CenterUseMyRank" value="1" <?php if ($CenterUseMyRank==1) { echo "checked";}?> data-switchery/>
							<label for="CenterUseMyRank" class="inline-label"><?=$내랭킹_보여주기[$LangID]?></label>
						</div>
						<hr class="md-hr">

			
						<div class="uk-form-row" style="display:<?if ($CenterPayType==2 && $_LINK_ADMIN_LEVEL_ID_>5) {?>none<?}?>;">
							<div class="uk-width-medium-2-5">
							대리점 수강종료일
							</div>
							<div class="uk-width-medium-2-5"> 
								<?if ($_LINK_ADMIN_LEVEL_ID_<=5){?>
									<input type="text" name="CenterStudyEndDate" value="<?=$CenterStudyEndDate?>" class="md-input label-fixed" style="text-align:left;" data-uk-datepicker="{format:'YYYY-MM-DD', weekstart:0}">
								<?}else{?>
									<br>
									<?=$CenterStudyEndDate?> 
									<input type="hidden" name="CenterStudyEndDate" value="<?=$CenterStudyEndDate?>">
									<?if ($CenterPayType==1) {?>
									<div style='display:inline-block;width:80px;text-align:center;padding:5px;border-radius:3px;background-color:#046AC6;color:#ffffff;font-size:10px;cursor:pointer;' onclick="location.href='class_order_renew_center_form.php';"><?=$수강연장[$LangID]?></div>
									<?}?>
								
								<?}?>
							</div>
						</div>
						<hr class="md-hr" style="display:<?if ($CenterPayType==2 && $_LINK_ADMIN_LEVEL_ID_>5) {?>none<?}?>;">

						
						
						<div class="uk-form-row" style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>7){?>none<?}?>;">
							<span class="icheck-inline">
								<input type="radio" id="CenterPayType1" name="CenterPayType" <?php if ($CenterPayType==1) { echo "checked";}?> value="1" data-md-icheck/>
								<label for="CenterPayType1" class="inline-label"><?=$B2B_결제[$LangID]?></label>
							</span>
							<span class="icheck-inline">
								<input type="radio" id="CenterPayType2" name="CenterPayType" <?php if ($CenterPayType==2) { echo "checked";}?> value="2" data-md-icheck/>
								<label for="CenterPayType2" class="inline-label"><?=$B2C_결제[$LangID]?></label>
							</span>
							<div style="color:#ff0000;">※ B2B -> B2C 로 변경시 수강신청관리에서 해당 대리점 수강생의 종료일을 설정해 주셔야 합니다.</div>
						</div>

						<div class="uk-form-row" style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>7){?>none<?}?>;">
							<span class="icheck-inline">
								<input type="radio" id="CenterRenewType1" name="CenterRenewType" <?php if ($CenterRenewType==1) { echo "checked";}?> value="1" data-md-icheck/>
								<label for="CenterRenewType1" class="inline-label"><?=$일반_B2B[$LangID]?></label>
							</span>
							<span class="icheck-inline">
								<input type="radio" id="CenterRenewType2" name="CenterRenewType" <?php if ($CenterRenewType==2) { echo "checked";}?> value="2" data-md-icheck/>
								<label for="CenterRenewType2" class="inline-label"><?=$무결제_B2B[$LangID]?></label>
							</span>
							<div style="color:#ff0000;">※ 무결제 B2B는 수강연장을 하지 않아도 계속해서 수업이 가능합니다. (B2B결제 일때만 유효합니다)</div>
						</div>

						
						<div class="uk-form-row" style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>7){?>none<?}?>;">
							<div class="uk-width-medium-5-5">
							B2B 선불결제 시작년월 (형식 : 202003)
							</div>
							<div class="uk-width-medium-2-5"> 
								<input type="text" name="CenterRenewStartYearMonthNum" value="<?=$CenterRenewStartYearMonthNum?>" class="md-input label-fixed" style="text-align:left;" readonly data-uk-datepicker="{format:'YYYYMM', weekstart:0}">
							</div>
						</div>
						<div class="uk-form-row" style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>7){?>none<?}?>;">

							<div style="color:#ff0000;">※ 시작년월에는 이전달 차액을 계산하지 않습니다. (B2B결제, 일반 B2B 일때만 유효합니다)</div>
						</div>

						<hr class="md-hr" style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>7){?>none<?}?>;">

						<div class="uk-width-medium-5-5" >
							<span class="icheck-inline">
								<input type="radio" id="CenterAcceptSms1" name="CenterAcceptSms" value="1" <?php if ($CenterAcceptSms==1) { echo "checked";}?> data-md-icheck/>
								<label for="CenterAcceptSms1" class="inline-label"><?=$설정[$LangID]?></label>
							</span>
							<span class="icheck-inline">
								<input type="radio" id="CenterAcceptSms0" name="CenterAcceptSms" value="0" <?php if ($CenterAcceptSms==0) { echo "checked";}?> data-md-icheck/>
								<label for="CenterAcceptSms0" class="inline-label"><?=$미설정[$LangID]?></label>
							</span>
							※ 수업전 알림문자 전송 
						</div>

						<div class="uk-width-medium-5-5" style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>7){?>none<?}?>;">
							<span class="icheck-inline">
								<input type="radio" id="CenterAcceptJoin1" name="CenterAcceptJoin" value="1" <?php if ($CenterAcceptJoin==1) { echo "checked";}?> data-md-icheck/>
								<label for="CenterAcceptJoin1" class="inline-label"><?=$설정[$LangID]?></label>
							</span>
							<span class="icheck-inline">
								<input type="radio" id="CenterAcceptJoin0" name="CenterAcceptJoin" value="0" <?php if ($CenterAcceptJoin==0) { echo "checked";}?> data-md-icheck/>
								<label for="CenterAcceptJoin0" class="inline-label"><?=$미설정[$LangID]?></label>
							</span>
							※ 회원가입시 대리점 선택 가능
						</div>

						<hr class="md-hr" style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>7){?>none<?}?>;">

						<div class="uk-form-row" style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>7){?>none<?}?>;">
							<span class="icheck-inline">
								<input type="radio" id="CenterState1" name="CenterState" <?php if ($CenterState==1) { echo "checked";}?> value="1" data-md-icheck/>
								<label for="CenterState1" class="inline-label"><?=$운영[$LangID]?></label>
							</span>
							<span class="icheck-inline">
								<input type="radio" id="CenterState2" name="CenterState" <?php if ($CenterState==2) { echo "checked";}?> value="2" data-md-icheck/>
								<label for="CenterState2" class="inline-label"><?=$휴원[$LangID]?></label>
							</span>
							<span class="icheck-inline">
								<input type="radio" id="CenterState3" name="CenterState" <?php if ($CenterState==3) { echo "checked";}?> value="3" data-md-icheck/>
								<label for="CenterState3" class="inline-label"><?=$미운영[$LangID]?></label>
							</span>
						</div>

						<hr class="md-hr" style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>7){?>none<?}?>;">


						<div class="uk-form-row">
							<a type="button" href="javascript:FormSubmit();" class="md-btn md-btn-primary"><?=$저장하기[$LangID]?></a>
						</div>
					</div>
				</div>
			</div>
		</div>
		</form>

	</div>
</div>


<?
include_once('./inc_common_form_js.php');
?>
<!-- ==============  only this page js ============== -->

<!-- ==============  only this page js ============== -->
<!-- ==============  common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ==============  common.js ============== -->


<!-- iOS에서는 position:fixed 버그가 있음, 적용하는 사이트에 맞게 position:absolute 등을 이용하여 top,left값 조정 필요 -->
<div id="layer" style="display:none;position:fixed;overflow:hidden;z-index:1;-webkit-overflow-scrolling:touch;">
<img src="//t1.daumcdn.net/postcode/resource/images/close.png" id="btnCloseLayer" style="cursor:pointer;position:absolute;right:-3px;top:-3px;z-index:1" onclick="closeDaumPostcode()" alt="<?=$닫기버튼[$LangID]?>">
</div>

<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
<script>
    // 우편번호 찾기 화면을 넣을 element
    var element_layer = document.getElementById('layer');

    function closeDaumPostcode() {
        // iframe을 넣은 element를 안보이게 한다.
        element_layer.style.display = 'none';
    }

    function ExecDaumPostcode() {
        new daum.Postcode({
            oncomplete: function(data) {
                // 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

                // 각 주소의 노출 규칙에 따라 주소를 조합한다.
                // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
                var addr = ''; // 주소 변수
                var extraAddr = ''; // 참고항목 변수

                //사용자가 선택한 주소 타입에 따라 해당 주소 값을 가져온다.
                if (data.userSelectedType === 'R') { // 사용자가 도로명 주소를 선택했을 경우
                    addr = data.roadAddress;
                } else { // 사용자가 지번 주소를 선택했을 경우(J)
                    addr = data.jibunAddress;
                }

                // 사용자가 선택한 주소가 도로명 타입일때 참고항목을 조합한다.
                if(data.userSelectedType === 'R'){
                    // 법정동명이 있을 경우 추가한다. (법정리는 제외)
                    // 법정동의 경우 마지막 문자가 "동/로/가"로 끝난다.
                    if(data.bname !== '' && /[동|로|가]$/g.test(data.bname)){
                        extraAddr += data.bname;
                    }
                    // 건물명이 있고, 공동주택일 경우 추가한다.
                    if(data.buildingName !== '' && data.apartment === 'Y'){
                        extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                    }
                    // 표시할 참고항목이 있을 경우, 괄호까지 추가한 최종 문자열을 만든다.
                    if(extraAddr !== ''){
                        extraAddr = ' (' + extraAddr + ')';
                    }
                    // 조합된 참고항목을 해당 필드에 넣는다.
                    //document.getElementById("sample2_extraAddress").value = extraAddr;
                
                } else {
                    //document.getElementById("sample2_extraAddress").value = '';
                }

                // 우편번호와 주소 정보를 해당 필드에 넣는다.
                document.getElementById('CenterZip').value = data.zonecode;
                document.getElementById("CenterAddr1").value = addr;
                // 커서를 상세주소 필드로 이동한다.
                document.getElementById("CenterAddr2").focus();

                // iframe을 넣은 element를 안보이게 한다.
                // (autoClose:false 기능을 이용한다면, 아래 코드를 제거해야 화면에서 사라지지 않는다.)
                element_layer.style.display = 'none';
            },
            width : '100%',
            height : '100%',
            maxSuggestItems : 5
        }).embed(element_layer);

        // iframe을 넣은 element를 보이게 한다.
        element_layer.style.display = 'block';

        // iframe을 넣은 element의 위치를 화면의 가운데로 이동시킨다.
        initLayerPosition();
    }

    // 브라우저의 크기 변경에 따라 레이어를 가운데로 이동시키고자 하실때에는
    // resize이벤트나, orientationchange이벤트를 이용하여 값이 변경될때마다 아래 함수를 실행 시켜 주시거나,
    // 직접 element_layer의 top,left값을 수정해 주시면 됩니다.
    function initLayerPosition(){
        var width = 300; //우편번호서비스가 들어갈 element의 width
        var height = 400; //우편번호서비스가 들어갈 element의 height
        var borderWidth = 5; //샘플에서 사용하는 border의 두께

        // 위에서 선언한 값들을 실제 element에 넣는다.
        element_layer.style.width = width + 'px';
        element_layer.style.height = height + 'px';
        element_layer.style.border = borderWidth + 'px solid';
        // 실행되는 순간의 화면 너비와 높이 값을 가져와서 중앙에 뜰 수 있도록 위치를 계산한다.
        element_layer.style.left = (((window.innerWidth || document.documentElement.clientWidth) - width)/2 - borderWidth) + 'px';
        element_layer.style.top = (((window.innerHeight || document.documentElement.clientHeight) - height)/2 - borderWidth) + 'px';
    }
</script>

<!-- ================================= 수업시간구성관리 스크립트 모음 ================================= -->
<script> 

var CenterID = "<?=$CenterID?>";
var ListParam2 = "<?=$ListParam2?>";
var ListParam3 = "<?=$ListParam3?>";
var ListParam4 = "<?=$ListParam4?>";
var ListParam5 = "<?=$ListParam5?>";


function OpenCenterClassForm(CenterClassID){
	openurl = "center_class_form.php?CenterClassID="+CenterClassID+"&CenterID="+CenterID+"&ListParam="+ListParam2;
	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "850"
		,maxHeight: "750"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}

function OpenCenterDeviceForm(CenterDeviceID){
	openurl = "center_device_form.php?CenterDeviceID="+CenterDeviceID+"&CenterID="+CenterID+"&ListParam="+ListParam3;
	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "850"
		,maxHeight: "750"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}

function OpenCenterClassMemberForm(CenterClassMemberID){
	openurl = "center_class_member_form.php?CenterClassMemberID="+CenterClassMemberID+"&CenterID="+CenterID+"&ListParam="+ListParam4;
	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "850"
		,maxHeight: "750"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}

function OpenCenterStaffForm(MemberID) {
	openurl = "center_staff_form.php?MemberID="+MemberID+"&CenterID="+CenterID+"&ListParam="+ListParam5;
	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "850"
		,maxHeight: "750"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}

function OpenCenterParentForm(MemberID) {
	openurl = "center_parent_form.php?MemberID="+MemberID+"&CenterID="+CenterID+"&ListParam="+ListParam5;
	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "850"
		,maxHeight: "750"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}



function OpenMemberPointForm(MemberID){
	openurl = "member_point_form.php?MemberID="+MemberID;
	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "850"
		,maxHeight: "750"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}


function OpenTaxMemberInfo(OrganType,OrganID){

	openurl = "tax_member_info_form.php?OrganType="+OrganType+"&OrganID="+OrganID+"&ListParam=<?=$ListParam?>";
	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "850"
		,maxHeight: "750"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 

}
</script>

<!-- ================================= 수업시간구성관리 스크립트 모음 ================================= -->

<script language="javascript">
function EnNewID(){
	document.RegForm.CheckedID.value = "0";
	document.getElementById('BtnCheckID').style.display = "";
}

function CheckID(){
    var NewID = $.trim($('#MemberLoginID').val());

    if (NewID == "") {
		UIkit.modal.alert("<?=$아이디를_입력하세요[$LangID]?>");
        document.RegForm.CheckedID.value = "0";
    } else if (NewID.length<2)  {
		UIkit.modal.alert("<?=$아이디는_2자_이상_입력하세요[$LangID]?>");
        document.RegForm.CheckedID.value = "0";
	} else {
        url = "ajax_check_id.php";

		//location.href = url + "?NewID="+NewID;
        $.ajax(url, {
            data: {
                NewID: NewID
            },
            success: function (data) {
				json_data = data;
				CheckResult = json_data.CheckResult;
                if (CheckResult == 1) {
					UIkit.modal.alert("<?=$사용_가능한_아이디_입니다[$LangID]?>");
                    document.RegForm.CheckedID.value = "1";
					document.getElementById('BtnCheckID').style.display = "none";
                }
                else {
					UIkit.modal.alert("<?=$이미_사용중인_아이디_입니다[$LangID]?>");
                    document.RegForm.CheckedID.value = "0";
					document.getElementById('BtnCheckID').style.display = "";
                }
            },
            error: function () {
				UIkit.modal.alert("Error while contacting server, please try again");
                document.RegForm.CheckedID.value = "0";
				document.getElementById('BtnCheckID').style.display = "";
            }
        });

    }

}


//================ 이메일 =============
function EnNewEmail(){
	document.RegForm.CheckedEmail.value = "0";
	document.getElementById('BtnCheckEmail').style.display = "inline";
}



function CheckEmail(){
    var CenterEmail_1 = $.trim($('#CenterEmail_1').val());
	var CenterEmail_2 = $.trim($('#CenterEmail_2').val());

    if (CenterEmail_1 == "" || CenterEmail_2 == "") {
        alert('<?=$이메일을_입력하세요[$LangID]?>');
        document.RegForm.CheckedEmail.value = "0";
	} else {
        url = "ajax_check_email.php";

		//location.href = url + "?MemberEmail_1="+CenterEmail_1+"&MemberEmail_2="+CenterEmail_2+"&MemberID=<?=$MemberID?>";
        $.ajax(url, {
            data: {
                MemberEmail_1: CenterEmail_1,
				MemberEmail_2: CenterEmail_2,
				MemberID: "<?=$MemberID?>"
            },
            success: function (data) {
				json_data = data;
				CheckResult = json_data.CheckResult;
                if (CheckResult == 1) {
                    alert('<?=$사용_가능한_이메일_입니다[$LangID]?>');
                    document.RegForm.CheckedEmail.value = "1";
					document.getElementById('BtnCheckEmail').style.display = "none";
                }
                else {
                    alert('<?=$이미_등록된_이메일_입니다[$LangID]?>');
                    document.RegForm.CheckedEmail.value = "0";
					document.getElementById('BtnCheckEmail').style.display = "inline";
                }
            },
            error: function () {
                alert('Error while contacting server, please try again');
                document.RegForm.CheckedEmail.value = "0";
				document.getElementById('BtnCheckEmail').style.display = "inline";
            }
        });

    }

}



function SetEmailName(){
	CenterEmail_3 = document.RegForm.CenterEmail_3.value;
	if (CenterEmail_3==""){
		document.RegForm.CenterEmail_2.value = "";
		document.RegForm.CenterEmail_2.readOnly = false;
	}else{
		document.RegForm.CenterEmail_2.value = CenterEmail_3;
		document.RegForm.CenterEmail_2.readOnly = true;
	}

	EnNewEmail();
}
//================ 이메일 =============



function FormSubmit(){

	obj = document.RegForm.OnlineSiteID;
	if (obj.value==""){
		UIkit.modal.alert("<?=$소속_사이트를_선택하세요[$LangID]?>");
		obj.focus();
		return;
	}

	obj = document.RegForm.ManagerID;
	if (obj.value==""){
		UIkit.modal.alert("<?=$소속_영업본부를_선택하세요[$LangID]?>");
		obj.focus();
		return;
	}

	obj = document.RegForm.BranchID;
	if (obj.value==""){
		UIkit.modal.alert("<?=$소속_지사를_선택하세요[$LangID]?>");
		obj.focus();
		return;
	}

	obj = document.RegForm.MemberTimeZoneID;
	if (obj.value==""){
		UIkit.modal.alert("<?=$활동지역을_선택하세요[$LangID]?>");
		obj.focus();
		return;
	}


	obj = document.RegForm.CenterName;
	if (obj.value==""){
		UIkit.modal.alert("<?=$대리점명을_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}

	obj = document.RegForm.CenterPricePerGroup;
	if (obj.value==""){
		UIkit.modal.alert("<?=$그룹수업_수업료_그룹당를_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}

	obj = document.RegForm.CenterPricePerTime;
	if (obj.value==""){
		UIkit.modal.alert("<?=$P10분당_수업료를_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}

	obj = document.RegForm.CenterFreeTrialCount;
	if (obj.value==""){
		UIkit.modal.alert("<?=$무료체험_회수를_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}

	

	obj = document.RegForm.CenterManagerName;
	if (obj.value==""){
		UIkit.modal.alert("<?=$대리점_관리자명을_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}


	obj = document.RegForm.MemberLoginID;
	if (obj.value==""){
		UIkit.modal.alert("<?=$아이디를_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}

	obj = document.RegForm.MemberLoginID;
	if (obj.value.length<2){
		UIkit.modal.alert("<?=$아이디는_2자_이상_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}


	obj = document.RegForm.CheckedID;
	if (obj.value=="0"){
		UIkit.modal.alert("<?=$아이디_중복확인_버튼을_클릭하세요[$LangID]?>");
		return;
	}

    var CenterPricePerGroup = document.getElementById("CenterPricePerGroup").value;
    var CenterPricePerTime = document.getElementById("CenterPricePerTime").value;

    if (CenterPricePerGroup == "" || CenterPricePerGroup == "0" || CenterPricePerTime == "" || CenterPricePerTime == "0") {
        UIkit.modal.alert("그룹 수업료(그룹당 주 1회) 또는 수업료(10분) 값은 0원 보다 커야 합니다.<br>수업료를 올바르게 입력해주시기 바랍니다.");
        return;
    }


	<?
	if ($MemberID!=""){ 
	?>	
		obj = document.RegForm.MemberLoginNewPW;
		obj2 = document.RegForm.MemberLoginNewPW2;

		if (obj.value!="" || obj2.value!=""){
			
			if (obj.value.length<2){
				UIkit.modal.alert("<?=$비밀번호는_2자_이상_입력하세요[$LangID]?>");
				obj.focus();
				return;
			}			
			
			if (obj.value!=obj2.value){
				UIkit.modal.alert("<?=$비밀번호와_비밀번호_확인이_일치하지_않습니다[$LangID]?>");
				obj.focus();
				return;
			}
		}
	<?
	}else{
	?>
		obj = document.RegForm.MemberLoginNewPW;
		obj2 = document.RegForm.MemberLoginNewPW2;
		if (obj.value==""){
			UIkit.modal.alert("<?=$비밀번호를_입력하세요[$LangID]?>");
			obj.focus();
			return;
		}

		if (obj.value.length<2){
			UIkit.modal.alert("<?=$비밀번호는_2자_이상_입력하세요[$LangID]?>");
			obj.focus();
			return;
		}	

		if (obj.value!=obj2.value){
			UIkit.modal.alert("<?=$비밀번호와_비밀번호_확인이_일치하지_않습니다[$LangID]?>");
			obj.focus();
			return;
		}
	<?
	}
	?>

/*
	obj = document.RegForm.CenterEmail;
	if (obj.value==""){
		UIkit.modal.alert("<?=$이메일을_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}

	obj = document.RegForm.CheckedEmail;
	if (obj.value=="0"){
		UIkit.modal.alert("<?=$이메일_중복확인_버튼을_클릭하세요[$LangID]?>");
		return;
	}
*/

	UIkit.modal.confirm(
		'<?=$저장_하시겠습니까[$LangID]?>?', 
		function(){ 
			document.RegForm.action = "center_action.php";
			document.RegForm.submit();
		}
	);


}

</script>








<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>
