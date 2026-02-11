<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
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

<body class="disable_transitions sidebar_main_open sidebar_main_swipe" style="padding-top:0; margin-top:0;">

<div id="page_content">
<?
$PageType = "";

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$PageTabID = isset($_REQUEST["PageTabID"]) ? $_REQUEST["PageTabID"] : "";
$IframeMode = isset($_REQUEST["IframeMode"]) ? $_REQUEST["IframeMode"] : "";

if ($PageTabID==""){
	$PageTabID = "13";
}

if ($MemberID!=""){

	$Sql = "
			select 
					A.*,
					B.CenterPayType,
					AES_DECRYPT(UNHEX(A.MemberPhone1),:EncryptionKey) as DecMemberPhone1,
					AES_DECRYPT(UNHEX(A.MemberPhone2),:EncryptionKey) as DecMemberPhone2,
					AES_DECRYPT(UNHEX(A.MemberPhone3),:EncryptionKey) as DecMemberPhone3,
					AES_DECRYPT(UNHEX(A.MemberEmail),:EncryptionKey) as DecMemberEmail,
					AES_DECRYPT(UNHEX(A.MemberEmail2),:EncryptionKey) as DecMemberEmail2
			from Members A 
				inner join Centers B on A.CenterID=B.CenterID 
			where A.MemberID=:MemberID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	
	$CenterID = $Row["CenterID"];
	$MemberID = $Row["MemberID"];
	$MemberLanguageID = $Row["MemberLanguageID"];
	$MemberLevelID = $Row["MemberLevelID"];
	$MemberNumber = $Row["MemberNumber"];
	$ForceUseClassIn = $Row["ForceUseClassIn"];
	$MemberCiTelephone = $Row["MemberCiTelephone"];
	$MemberLoginID = $Row["MemberLoginID"];
	$MemberLoginPW = $Row["MemberLoginPW"];
	$MemberName = $Row["MemberName"];
	$MemberNickName = $Row["MemberNickName"];
	$MemberParentName = $Row["MemberParentName"];
	$MemberSex = $Row["MemberSex"];
	$MemberCompanyName = $Row["MemberCompanyName"];
	$MemberPhoto = $Row["MemberPhoto"];
	$MemberBirthday = $Row["MemberBirthday"];
	//================ 전화번호 / 이메일 =============
	$MemberPhone1 = $Row["DecMemberPhone1"];
	$MemberPhone2 = $Row["DecMemberPhone2"];
	$MemberPhone3 = $Row["DecMemberPhone3"];
	$MemberEmail = $Row["DecMemberEmail"];
	$MemberEmail2 = $Row["DecMemberEmail2"];
	//================ 전화번호 / 이메일 =============	
	$MemberZip = $Row["MemberZip"];
	$MemberAddr1 = $Row["MemberAddr1"];
	$MemberAddr2 = $Row["MemberAddr2"];
	$MemberTimeZoneID = $Row["MemberTimeZoneID"];
	$SchoolName = $Row["SchoolName"];
	$SchoolGrade = $Row["SchoolGrade"];
	$MemberView = $Row["MemberView"];
	$MemberState = $Row["MemberState"];
	$MemberStateText = $Row["MemberStateText"];
	$WithdrawalText = $Row["WithdrawalText"];
	$MemberStudyAlarmTime = $Row["MemberStudyAlarmTime"];
	$MemberStudyAlarmType = $Row["MemberStudyAlarmType"];
	$MemberChangeTeacher = $Row["MemberChangeTeacher"];

	$CenterPayType = $Row["CenterPayType"];
	$MemberPayType = $Row["MemberPayType"];

	$MemberPricePerTime = $Row["MemberPricePerTime"];



	$CheckedID = 1;
	$CheckedEmail = 1;

}else{

	$CenterID = "";
	$MemberID = "";
	$MemberLanguageID = "";
	$MemberLevelID = 19;
	$MemberNumber = "";
	$ForceUseClassIn = 0;
	$MemberCiTelephone = "";
	$MemberLoginID = "";
	$MemberLoginPW = "";
	$MemberName = "";
	$MemberNickName = "";
	$MemberSex = 1;
	$MemberCompanyName = "";
	$MemberPhoto = "";
	$MemberBirthday = "";
	//================ 전화번호 / 이메일 =============
	$MemberPhone1 = "--";
	$MemberPhone2 = "--";
	$MemberPhone3 = "--";
	$MemberEmail = "@";
	$MemberEmail2 = "@";
	//================ 전화번호 / 이메일 =============
	$MemberZip = "";
	$MemberAddr1 = "";
	$MemberAddr2 = "";
	$MemberTimeZoneID = 1;
	$SchoolName = "";
	$SchoolGrade = "";
	$MemberView = 1;
	$MemberState = 1;
	$MemberStateText = "";
	$WithdrawalText = "";

	$MemberStudyAlarmTime = 30;
	$MemberStudyAlarmType = 1;
	$MemberChangeTeacher = 1;

	$MemberParentName = "";

	$CenterPayType = 1;
	$MemberPayType = 0;

	$MemberPricePerTime = 0;

	$CheckedID = 0;
	$CheckedEmail = 0;


}


//================ 전화번호 / 이메일 =============
$ArrMemberPhone1 = explode("-", $MemberPhone1);
$ArrMemberPhone2 = explode("-", $MemberPhone2);
$ArrMemberPhone3 = explode("-", $MemberPhone3);
$ArrMemberEmail = explode("@", $MemberEmail);
$ArrMemberEmail2 = explode("@", $MemberEmail2);

if (count($ArrMemberPhone1)>=1){
	$MemberPhone1_1 = $ArrMemberPhone1[0];
}else{
	$MemberPhone1_1 = "";
}
if (count($ArrMemberPhone1)>=2){
	$MemberPhone1_2 = $ArrMemberPhone1[1];
}else{
	$MemberPhone1_2 = "";
}
if (count($ArrMemberPhone1)>=3){
	$MemberPhone1_3 = $ArrMemberPhone1[2];
}else{
	$MemberPhone1_3 = "";
}

if (count($ArrMemberPhone2)>=1){
	$MemberPhone2_1 = $ArrMemberPhone2[0];
}else{
	$MemberPhone2_1 = "";
}
if (count($ArrMemberPhone2)>=2){
	$MemberPhone2_2 = $ArrMemberPhone2[1];
}else{
	$MemberPhone2_2 = "";
}
if (count($ArrMemberPhone2)>=3){
	$MemberPhone2_3 = $ArrMemberPhone2[2];
}else{
	$MemberPhone2_3 = "";
}

if (count($ArrMemberPhone3)>=1){
	$MemberPhone3_1 = $ArrMemberPhone3[0];
}else{
	$MemberPhone3_1 = "";
}
if (count($ArrMemberPhone3)>=2){
	$MemberPhone3_2 = $ArrMemberPhone3[1];
}else{
	$MemberPhone3_2 = "";
}
if (count($ArrMemberPhone3)>=3){
	$MemberPhone3_3 = $ArrMemberPhone3[2];
}else{
	$MemberPhone3_3 = "";
}

if (count($ArrMemberEmail)>=1){
	$MemberEmail_1 = $ArrMemberEmail[0];
}else{
	$MemberEmail_1 = "";
}
if (count($ArrMemberEmail)>=2){
	$MemberEmail_2 = $ArrMemberEmail[1];
}else{
	$MemberEmail_2 = "";
}

if (count($ArrMemberEmail2)>=1){
	$MemberEmail2_1 = $ArrMemberEmail2[0];
}else{
	$MemberEmail2_1 = "";
}
if (count($ArrMemberEmail2)>=2){
	$MemberEmail2_2 = $ArrMemberEmail2[1];
}else{
	$MemberEmail2_2 = "";
}
//================ 전화번호 / 이메일 =============



$HideCenterID = 0;
$AddWhere_Center = "";

if ($_LINK_ADMIN_LEVEL_ID_>10){
	$CenterID = $_LINK_ADMIN_CENTER_ID_;
	$HideCenterID = 1;
	$AddWhere_Center = "and A.CenterID=".$_LINK_ADMIN_CENTER_ID_." ";
}else if ($_LINK_ADMIN_LEVEL_ID_>7){
	$HideCenterID = 0;
	$AddWhere_Center = " and A.BranchID=".$_LINK_ADMIN_BRANCH_ID_." ";
}else if ($_LINK_ADMIN_LEVEL_ID_>5){ // 기존 4 ( 프랜차이즈직원) 였으나 영업본부 추가로 5 수정
	$HideCenterID = 0;
	$AddWhere_Center = " and B.BranchGroupID=".$_LINK_ADMIN_BRANCH_GROUP_ID_." ";
}else if ($_LINK_ADMIN_LEVEL_ID_>1){
	$HideCenterID = 0;
	$AddWhere_Center = " and D.FranchiseID=".$_LINK_ADMIN_FRANCHISE_ID_." ";
}

$ListParam = $ListParam . "&IframeMode=".$IframeMode;

$MemberLoginNewPW = "";
$MemberLoginNewPW2 = "";
?>

<div id="page_content_inner">

	<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
	<input type="hidden" name="MemberID" value="<?=$MemberID?>">
	<input type="hidden" name="ListParam" value="<?=$ListParam?>">
	<input type="hidden" name="IframeMode" value="<?=$IframeMode?>">
	
	<input type="hidden" name="CheckedID" value="<?=$CheckedID?>">
	<input type="hidden" name="CheckedEmail" value="<?=$CheckedEmail?>">
	<input type="hidden" name="MemberLoginPW" value="<?=$MemberLoginPW?>">
	<input type="hidden" name="PageType" value="<?=$PageType?>">
	<input type="hidden" name="PageTabID" value="<?=$PageTabID?>">

	<input type="hidden" name="CenterPayType" value="<?=$CenterPayType?>">

	<div class="uk-grid" data-uk-grid-margin>
		<div class="uk-width-large-10-10">
			<div class="md-card">
				<div class="user_heading" data-uk-sticky="{ top: 0, media: 960 }">
					<div class="user_heading_content">
						<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$MemberName?></span><span class="sub-heading" id="user_edit_position"><?=$학생정보[$LangID]?></span></h2>
					</div>
				</div>
				<div class="user_content">
					<ul id="user_edit_tabs" class="uk-tab" data-uk-tab="{connect:'#user_edit_tabs_content'}" style="display:<?if ($MemberID==""){?>none<?}?>;">
						<li <?if ($PageTabID=="1"){?>class="uk-active"<?}?>><a href="#"><?=$학생정보[$LangID]?></a></li>
						<li <?if ($PageTabID=="2"){?>class="uk-active"<?}?> style="display:none;"><a href="#"><?=$수업관리[$LangID]?></a></li>
						<li <?if ($PageTabID=="3"){?>class="uk-active"<?}?> style="display:none;"><a href="#"><?=$평가보고서[$LangID]?></a></li>
						<li <?if ($PageTabID=="4"){?>class="uk-active"<?}?> style="display:none;"><a href="#"><?=$수강신청[$LangID]?></a></li>
						<li <?if ($PageTabID=="6"){?>class="uk-active"<?}?>><a href="#"><?=$수강신청[$LangID]?></a></li>
						<li <?if ($PageTabID=="7"){?>class="uk-active"<?}?>><a href="#"><?=$수강신청[$LangID]?></a></li>
						<li <?if ($PageTabID=="8"){?>class="uk-active"<?}?>><a href="#"><?=$수강신청[$LangID]?></a></li>
						<li <?if ($PageTabID=="11"){?>class="uk-active"<?}?>><a href="#"><?=$수강신청[$LangID]?></a></li>
						<li <?if ($PageTabID=="9"){?>class="uk-active"<?}?>><a href="#"><?=$수강신청[$LangID]?></a></li>
						<li <?if ($PageTabID=="10"){?>class="uk-active"<?}?>><a href="#"><?=$수강신청[$LangID]?></a></li>
						<li <?if ($PageTabID=="12"){?>class="uk-active"<?}?>><a href="#"><?=$수강신청[$LangID]?></a></li>
						<li <?if ($PageTabID=="13"){?>class="uk-active"<?}?>><a href="#"><?=$스케쥴[$LangID]?></a></li>
					</ul>
					<ul id="user_edit_tabs_content" class="uk-switcher uk-margin">
						<li>
							<div class="uk-margin-top">
								<h3 class="full_width_in_card heading_c">
									<?=$학생명_및_영어이름[$LangID]?>
								</h3>
								<div class="uk-grid" data-uk-grid-margin>
									<div class="uk-width-medium-4-10" style="padding-top:7px;display:<?if ($HideCenterID==1){?>none<?}?>;">
										<select id="CenterID" name="CenterID" class="uk-width-1-1" data-md-select2 data-allow-clear="true" data-placeholder="대리점선택" style="width:100%;"/>
											<?
											$Sql2 = "select 
															A.*,
															B.BranchName,
															C.BranchGroupName,
															D.CompanyName,
															E.FranchiseName 
													from Centers A 
														inner join Branches B on A.BranchID=B.BranchID 
														inner join BranchGroups C on B.BranchGroupID=C.BranchGroupID 
														inner join Companies D on C.CompanyID=D.CompanyID 
														inner join Franchises E on D.FranchiseID=E.FranchiseID 
													where A.CenterState<>0 and B.BranchState<>0 and C.BranchGroupState<>0 and D.CompanyState<>0  and E.FranchiseState<>0 ".$AddWhere_Center."
													order by A.CenterState asc, E.FranchiseName asc, D.CompanyName asc, C.BranchGroupName asc, B.BranchName asc";
											
											$Stmt2 = $DbConn->prepare($Sql2);
											$Stmt2->execute();
											$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
											
											$OldSelectCenterState = -1;
											while($Row2 = $Stmt2->fetch()) {
												$SelectCenterID = $Row2["CenterID"];
												$SelectCenterName = $Row2["CenterName"];
												$SelectCenterState = $Row2["CenterState"];
												$SelectBranchName = $Row2["BranchName"];
												$SelectBranchGroupName = $Row2["BranchGroupName"];
												$SelectCompanyName = $Row2["CompanyName"];
												$SelectFranchiseName = $Row2["FranchiseName"];

												if ($_LINK_ADMIN_LEVEL_ID_ <=2){
													$StrSelectFranchiseName = " (".$SelectFranchiseName.")";
												}else{
													$StrSelectFranchiseName = "";
												}
											
												if ($OldSelectCenterState!=$SelectCenterState){
													if ($OldSelectCenterState!=-1){
														echo "</optgroup>";
													}
													
													if ($SelectCenterState==1){
														echo "<optgroup label=\"".$대리점_운영중[$LangID]."\">";
													}else if ($SelectCenterState==2){
														echo "<optgroup label=\"".$대리점_미운영[$LangID]."\">";
													}
												} 
												$OldSelectCenterState = $SelectCenterState;
											?>

											<option value="<?=$SelectCenterID?>" <?if ($CenterID==$SelectCenterID){?>selected<?}?>><?=$SelectCenterName?><?=$StrSelectFranchiseName?></option>
											<?
											}
											$Stmt2 = null;
											?>
										</select>
									</div>
									<div class="uk-width-medium-2-10">
										<label for="MemberName"><?=$학생명[$LangID]?></label>
										<input type="text" id="MemberName" name="MemberName" value="<?=$MemberName?>" class="md-input label-fixed"/>
									</div>
									<div class="uk-width-medium-2-10">
										<label for="MemberNickName"><?=$영문표기이름[$LangID]?></label>
										<input type="text" id="MemberNickName" name="MemberNickName" value="<?=$MemberNickName?>" class="md-input label-fixed"/>
									</div>
									<div class="uk-width-medium-2-10" style="padding-top:7px;">
										<select name="MemberTimeZoneID" class="uk-width-1-1" data-md-select2 data-allow-clear="true" style="width:100%;">
										<option value=""><?=$활동지역[$LangID]?></option>
										<?php

										//  멤버와 타임존을 조인하여 값을 가져올 것. id 가 매칭이 된다면 default 로 지정
											$Sql3 = "select Z.MemberTimeZoneName, Z.MemberTimeZoneID from MemberTimeZones Z";
											
											$Stmt3 = $DbConn->prepare($Sql3);
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
								<div style="margin-top:30px;" class="uk-width-medium-3-5 uk-form-row">
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
								<h3 class="full_width_in_card heading_c">
									<?=$아이디_및_비밀번호[$LangID]?> <?if ($MemberID!="") {?>(비밀번호는 변경을 원할때 입력하세요)<?}?>
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

									<!-- 사용안함 -->
									<div class="uk-width-medium-2-10 uk-input-group" style="display:none;">
										<label for="MemberCiTelephone"><?=$클래스인ID[$LangID]?></label>
										<input type="text" id="MemberCiTelephone" name="MemberCiTelephone" value="<?=$MemberCiTelephone?>" class="md-input label-fixed" />
									</div>
									<!-- 사용안함 -->
								</div>

								<h3 class="full_width_in_card heading_c">
									<?=$연락처[$LangID]?>
								</h3>
								<div class="uk-grid" data-uk-grid-margin>
									<div class="uk-width-medium-1-2 uk-input-group">
										<label for="MemberPhone1"><?=$전화번호[$LangID]?></label>
										<div class="md-input label-fixed" style="padding-top:10px;margin-bottom:10px;">
											<select name="MemberPhone1_1" class="Select" style="width:15%;height:30px;">
												<option value="010" <?if ($MemberPhone1_1=="010") {?>selected<?}?>>010</option>
												<option value="011" <?if ($MemberPhone1_1=="011") {?>selected<?}?>>011</option>
												<option value="016" <?if ($MemberPhone1_1=="016") {?>selected<?}?>>016</option>
												<option value="017" <?if ($MemberPhone1_1=="017") {?>selected<?}?>>017</option>
												<option value="018" <?if ($MemberPhone1_1=="018") {?>selected<?}?>>018</option>
												<option value="019" <?if ($MemberPhone1_1=="019") {?>selected<?}?>>019</option>
												<option value="070" <?if ($MemberPhone1_1=="070") {?>selected<?}?>>070</option>
												<option value="02"  <?if ($MemberPhone1_1=="02")  {?>selected<?}?>>02</option>
												<option value="031" <?if ($MemberPhone1_1=="031") {?>selected<?}?>>031</option>
												<option value="032" <?if ($MemberPhone1_1=="032") {?>selected<?}?>>032</option>
												<option value="033" <?if ($MemberPhone1_1=="033") {?>selected<?}?>>033</option>
												<option value="041" <?if ($MemberPhone1_1=="041") {?>selected<?}?>>041</option>
												<option value="042" <?if ($MemberPhone1_1=="042") {?>selected<?}?>>042</option>
												<option value="043" <?if ($MemberPhone1_1=="043") {?>selected<?}?>>043</option>
												<option value="044" <?if ($MemberPhone1_1=="044") {?>selected<?}?>>044</option>
												<option value="049" <?if ($MemberPhone1_1=="049") {?>selected<?}?>>049</option>
												<option value="051" <?if ($MemberPhone1_1=="051") {?>selected<?}?>>051</option>
												<option value="052" <?if ($MemberPhone1_1=="052") {?>selected<?}?>>052</option>
												<option value="053" <?if ($MemberPhone1_1=="053") {?>selected<?}?>>053</option>
												<option value="054" <?if ($MemberPhone1_1=="054") {?>selected<?}?>>054</option>
												<option value="055" <?if ($MemberPhone1_1=="055") {?>selected<?}?>>055</option>
												<option value="061" <?if ($MemberPhone1_1=="061") {?>selected<?}?>>061</option>
												<option value="062" <?if ($MemberPhone1_1=="062") {?>selected<?}?>>062</option>
												<option value="063" <?if ($MemberPhone1_1=="063") {?>selected<?}?>>063</option>
												<option value="064" <?if ($MemberPhone1_1=="064") {?>selected<?}?>>064</option>
												<option value="0505" <?if ($MemberPhone1_1=="0505") {?>selected<?}?>>0505</option>
												<option value="0502" <?if ($MemberPhone1_1=="0502") {?>selected<?}?>>0502</option>
											</select>
											<input type="text" name="MemberPhone1_2" style="width:15%;height:30px;padding-left:10px;" placeholder="" value="<?=$MemberPhone1_2?>" class="allownumericwithoutdecimal" maxlength="4"> 
											<input type="text" name="MemberPhone1_3" style="width:15%;height:30px;padding-left:10px;"  placeholder="" value="<?=$MemberPhone1_3?>" class="allownumericwithoutdecimal" maxlength="4">
										</div>
									</div>
									<div class="uk-width-medium-1-2 uk-input-group">
										<label for="CenterPhone2"><?=$부모님_전화번호[$LangID]?></label>
										<div class="md-input label-fixed" style="padding-top:10px;margin-bottom:10px;">
											<select name="MemberPhone2_1" class="Select" style="width:15%;height:30px;">
												<option value="010" <?if ($MemberPhone2_1=="010") {?>selected<?}?>>010</option>
												<option value="011" <?if ($MemberPhone2_1=="011") {?>selected<?}?>>011</option>
												<option value="016" <?if ($MemberPhone2_1=="016") {?>selected<?}?>>016</option>
												<option value="017" <?if ($MemberPhone2_1=="017") {?>selected<?}?>>017</option>
												<option value="018" <?if ($MemberPhone2_1=="018") {?>selected<?}?>>018</option>
												<option value="019" <?if ($MemberPhone2_1=="019") {?>selected<?}?>>019</option>
												<option value="070" <?if ($MemberPhone2_1=="070") {?>selected<?}?>>070</option>
												<option value="02"  <?if ($MemberPhone2_1=="02")  {?>selected<?}?>>02</option>
												<option value="031" <?if ($MemberPhone2_1=="031") {?>selected<?}?>>031</option>
												<option value="032" <?if ($MemberPhone2_1=="032") {?>selected<?}?>>032</option>
												<option value="033" <?if ($MemberPhone2_1=="033") {?>selected<?}?>>033</option>
												<option value="041" <?if ($MemberPhone2_1=="041") {?>selected<?}?>>041</option>
												<option value="042" <?if ($MemberPhone2_1=="042") {?>selected<?}?>>042</option>
												<option value="043" <?if ($MemberPhone2_1=="043") {?>selected<?}?>>043</option>
												<option value="044" <?if ($MemberPhone2_1=="044") {?>selected<?}?>>044</option>
												<option value="049" <?if ($MemberPhone2_1=="049") {?>selected<?}?>>049</option>
												<option value="051" <?if ($MemberPhone2_1=="051") {?>selected<?}?>>051</option>
												<option value="052" <?if ($MemberPhone2_1=="052") {?>selected<?}?>>052</option>
												<option value="053" <?if ($MemberPhone2_1=="053") {?>selected<?}?>>053</option>
												<option value="054" <?if ($MemberPhone2_1=="054") {?>selected<?}?>>054</option>
												<option value="055" <?if ($MemberPhone2_1=="055") {?>selected<?}?>>055</option>
												<option value="061" <?if ($MemberPhone2_1=="061") {?>selected<?}?>>061</option>
												<option value="062" <?if ($MemberPhone2_1=="062") {?>selected<?}?>>062</option>
												<option value="063" <?if ($MemberPhone2_1=="063") {?>selected<?}?>>063</option>
												<option value="064" <?if ($MemberPhone2_1=="064") {?>selected<?}?>>064</option>
												<option value="0505" <?if ($MemberPhone2_1=="0505") {?>selected<?}?>>0505</option>
												<option value="0502" <?if ($MemberPhone2_1=="0502") {?>selected<?}?>>0502</option>
											</select>
											<input type="text" name="MemberPhone2_2" style="width:15%;height:30px;padding-left:10px;" placeholder="" value="<?=$MemberPhone2_2?>" class="allownumericwithoutdecimal" maxlength="4"> 
											<input type="text" name="MemberPhone2_3" style="width:15%;height:30px;padding-left:10px;"  placeholder="" value="<?=$MemberPhone2_3?>" class="allownumericwithoutdecimal" maxlength="4">
										</div>
									</div>
								</div>
								<div class="uk-grid" data-uk-grid-margin>
									<div class="uk-width-medium-1-2 uk-input-group">
										<label for="CenterPhone3"><?=$관리교사_전화번호[$LangID]?></label>
										<div class="md-input label-fixed" style="padding-top:10px;margin-bottom:10px;">
											<select name="MemberPhone3_1" class="Select" style="width:15%;height:30px;">
												<option value="010" <?if ($MemberPhone3_1=="010") {?>selected<?}?>>010</option>
												<option value="011" <?if ($MemberPhone3_1=="011") {?>selected<?}?>>011</option>
												<option value="016" <?if ($MemberPhone3_1=="016") {?>selected<?}?>>016</option>
												<option value="017" <?if ($MemberPhone3_1=="017") {?>selected<?}?>>017</option>
												<option value="018" <?if ($MemberPhone3_1=="018") {?>selected<?}?>>018</option>
												<option value="019" <?if ($MemberPhone3_1=="019") {?>selected<?}?>>019</option>
												<option value="070" <?if ($MemberPhone3_1=="070") {?>selected<?}?>>070</option>
												<option value="02"  <?if ($MemberPhone3_1=="02")  {?>selected<?}?>>02</option>
												<option value="031" <?if ($MemberPhone3_1=="031") {?>selected<?}?>>031</option>
												<option value="032" <?if ($MemberPhone3_1=="032") {?>selected<?}?>>032</option>
												<option value="033" <?if ($MemberPhone3_1=="033") {?>selected<?}?>>033</option>
												<option value="041" <?if ($MemberPhone3_1=="041") {?>selected<?}?>>041</option>
												<option value="042" <?if ($MemberPhone3_1=="042") {?>selected<?}?>>042</option>
												<option value="043" <?if ($MemberPhone3_1=="043") {?>selected<?}?>>043</option>
												<option value="044" <?if ($MemberPhone3_1=="044") {?>selected<?}?>>044</option>
												<option value="049" <?if ($MemberPhone3_1=="049") {?>selected<?}?>>049</option>
												<option value="051" <?if ($MemberPhone3_1=="051") {?>selected<?}?>>051</option>
												<option value="052" <?if ($MemberPhone3_1=="052") {?>selected<?}?>>052</option>
												<option value="053" <?if ($MemberPhone3_1=="053") {?>selected<?}?>>053</option>
												<option value="054" <?if ($MemberPhone3_1=="054") {?>selected<?}?>>054</option>
												<option value="055" <?if ($MemberPhone3_1=="055") {?>selected<?}?>>055</option>
												<option value="061" <?if ($MemberPhone3_1=="061") {?>selected<?}?>>061</option>
												<option value="062" <?if ($MemberPhone3_1=="062") {?>selected<?}?>>062</option>
												<option value="063" <?if ($MemberPhone3_1=="063") {?>selected<?}?>>063</option>
												<option value="064" <?if ($MemberPhone3_1=="064") {?>selected<?}?>>064</option>
												<option value="0505" <?if ($MemberPhone3_1=="0505") {?>selected<?}?>>0505</option>
												<option value="0502" <?if ($MemberPhone3_1=="0502") {?>selected<?}?>>0502</option>	
											</select>
											<input type="text" name="MemberPhone3_2" style="width:15%;height:30px;padding-left:10px;" placeholder="" value="<?=$MemberPhone3_2?>" class="allownumericwithoutdecimal" maxlength="4"> 
											<input type="text" name="MemberPhone3_3" style="width:15%;height:30px;padding-left:10px;"  placeholder="" value="<?=$MemberPhone3_3?>" class="allownumericwithoutdecimal" maxlength="4">
										</div>
									</div>
									<div class="uk-width-medium-1-2 uk-input-group">
										<label for="MemberEmail"><?=$이메일[$LangID]?></label>
										<div class="md-input label-fixed" style="padding-top:10px;margin-bottom:10px;">
											<input type="text" name="MemberEmail_1" id="MemberEmail_1" style="width:20%;height:30px;padding-left:10px;" value="<?=$MemberEmail_1?>" onkeyup="EnNewEmail()"> 
											<div style="display:inline-block;width:20px;margin-top:8px;">@</div> 
											<input type="text" name="MemberEmail_2" id="MemberEmail_2" style="width:20%;height:30px;padding-left:10px;" value="<?=$MemberEmail_2?>" onkeyup="EnNewEmail()">
											<select name="MemberEmail_3" class="Select" style="width:22%;height:30px;margin-bottom:0px;" onchange="SetEmailName()">
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
									<?=$생년월일_및_성별_학교[$LangID]?>
								</h3>
								<div class="uk-grid" data-uk-grid-margin>
									<div class="uk-width-medium-3-10 uk-input-group">
										<label for="MemberBirthday"><?=$생년월일[$LangID]?></label>
										<input type="text" id="MemberBirthday" name="MemberBirthday" value="<?=$MemberBirthday?>" class="md-input label-fixed" data-uk-datepicker="{format:'YYYY-MM-DD', weekstart:0}">
										<span class="uk-input-group-addon"><i class="uk-input-group-icon uk-icon-calendar"></i></span>
									</div>
									<div class="uk-width-medium-3-10 uk-input-group" style="padding-top:10px;">
										<span class="icheck-inline">
											<input type="radio" id="MemberSex1" name="MemberSex" <?php if ($MemberSex==1) { echo "checked";}?> value="1" data-md-icheck/>
											<label for="MemberSex1" class="inline-label"><?=$남자[$LangID]?></label>
										</span>
										<span class="icheck-inline">
											<input type="radio" id="MemberSex2" name="MemberSex" <?php if ($MemberSex==2) { echo "checked";}?> value="2" data-md-icheck/>
											<label for="MemberSex2" class="inline-label"><?=$여자[$LangID]?></label>
										</span>
									</div>
									<div class="uk-width-medium-4-10 uk-input-group">
										<label for="SchoolName"><?=$학교명[$LangID]?></label>
										<input type="text" id="SchoolName" name="SchoolName" value="<?=$SchoolName?>" class="md-input label-fixed" />
									</div>
								</div>

								<h3 class="full_width_in_card heading_c" style="display:;">
									<?=$알림_강사_대체_정책[$LangID]?>
								</h3>
								<div class="uk-grid" data-uk-grid-margin style="display:;">

									<div class="uk-width-medium-1-6 uk-input-group">
										<select class="uk-width-1-1" data-md-select2 data-allow-clear="true" style="width:100%;" id="MemberStudyAlarmTime" name="MemberStudyAlarmTime">
											<option value="10" <?if ($MemberStudyAlarmTime==10){?>selected<?}?>>10분전</option>
											<option value="30" <?if ($MemberStudyAlarmTime==30){?>selected<?}?>>30분전</option>
											<option value="60" <?if ($MemberStudyAlarmTime==60){?>selected<?}?>>1시간전</option>
										</select>
									</div>
									<div class="uk-width-medium-2-6 uk-input-group">
										<span class="icheck-inline">
											<input type="radio" id="MemberStudyAlarmType_1" value="1" name="MemberStudyAlarmType" <?if ($MemberStudyAlarmType==1){?>checked<?}?> data-md-icheck/>
											<label class="inline-label" for="MemberStudyAlarmType_1" ><?=$수신[$LangID]?></label>
										</span>
										<span class="icheck-inline">
											<input type="radio" id="MemberStudyAlarmType_2" value="2" name="MemberStudyAlarmType" <?if ($MemberStudyAlarmType==2){?>checked<?}?> data-md-icheck/>
											<label class="inline-label" for="MemberStudyAlarmType_2"><?=$거부[$LangID]?></label>
										</span>
									</div>
									<div class="uk-width-medium-3-6 uk-input-group">
										<input type="radio" id="MemberChangeTeacher_1" value="1" name="MemberChangeTeacher" <?if ($MemberChangeTeacher==1){?>checked<?}?> data-md-icheck/>
										<label class="inline-label" for="MemberChangeTeacher_1" style="margin-right: 7px;"><?=$다른_강사_대체[$LangID]?></label>
										<input type="radio" id="MemberChangeTeacher_2" value="2" name="MemberChangeTeacher" <?if ($MemberChangeTeacher==2){?>checked<?}?> data-md-icheck/>
										<label class="inline-label" for="MemberChangeTeacher_2"><?=$강사_대체없이_수업_취소[$LangID]?></label>
									</div>
								</div>


								<h3 class="full_width_in_card heading_c">
									<?=$주소[$LangID]?>
								</h3>
								<div class="uk-grid" data-uk-grid-margin>
									<div class="uk-width-medium-3-10 uk-input-group">
										<label for="MemberZip"><?=$우편번호[$LangID]?></label>
										<input type="text" id="MemberZip" name="MemberZip" value="<?=$MemberZip?>" class="md-input label-fixed" />
										<span class="uk-input-group-addon"><a class="md-btn" href="javascript:ExecDaumPostcode();"><?=$검색[$LangID]?></a></span>

									</div>
									<div class="uk-width-medium-3-10 uk-input-group">
										<label for="MemberAddr1"><?=$주소[$LangID]?></label>
										<input type="text" id="MemberAddr1" name="MemberAddr1" value="<?=$MemberAddr1?>" class="md-input label-fixed " />
									</div>
									<div class="uk-width-medium-4-10 uk-input-group">
										<label for="MemberAddr2"><?=$상세주소[$LangID]?></label>
										<input type="text" id="MemberAddr2" name="MemberAddr2" value="<?=$MemberAddr2?>" class="md-input label-fixed" />
									</div>
								</div>

								<div class="uk-grid" data-uk-grid-margin>
									<div class="uk-width-1-1">
										<label for="MemberStateText"><?=$메모[$LangID]?></label>
										<textarea class="md-input" name="MemberStateText" id="MemberStateText" cols="30" rows="4"><?=$MemberStateText?></textarea>
									</div>
								</div>

								<h3 class="full_width_in_card heading_c">
									<?=$학부모_이름[$LangID]?> / <?=$이메일[$LangID]?>
								</h3>
								<div class="uk-grid" data-uk-grid-margin>
									<div class="uk-width-medium-2-4 uk-input-group">
										<label for="MemberParentName"><?=$보호자_이름[$LangID]?></label>
										<input type="text" id="MemberParentName" name="MemberParentName" value="<?=$MemberParentName?>" class="md-input label-fixed" />
									</div>
									<div class="uk-width-medium-2-4 uk-input-group">
										<label for="MemberEmail2"><?=$학부모_이메일[$LangID]?></label>
										<div class="md-input label-fixed" style="padding-top:10px;margin-bottom:10px;">
											<input type="text" name="MemberEmail2_1" id="MemberEmail2_1" style="width:25%;height:30px;padding-left:10px;" value="<?=$MemberEmail2_1?>"> 
											<div style="display:inline-block;width:20px;margin-top:8px;">@</div> 
											<input type="text" name="MemberEmail2_2" id="MemberEmail2_2" style="width:25%;height:30px;padding-left:10px;" value="<?=$MemberEmail2_2?>">
											<select name="MemberEmail2_3" class="Select" style="width:30%;height:30px;margin-bottom:0px;" onchange="SetEmailName2()">
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
										</div>
									</div>
								</div>

							</div>
						</li>
						<?
						if ($MemberID!=""){
							
						?>
						
						<li style="display:none;"><!--수업관리--></li>
						<li style="display:none;"><!--평가보고서--></li>
						<li style="display:none;"><!--수강신청--></li>

						<li>
							<!--상담내역-->
							<table class="uk-table uk-table-align-vertical">
								<thead>
									<tr>
										<th width="15%" nowrap><?=$상담시간[$LangID]?></th>
										<th nowrap><?=$제목[$LangID]?></th>
										<th width="40%" nowrap><?=$내용[$LangID]?></th>
										<th width="10%" nowrap><?=$강사명[$LangID]?></th>
										<th width="10%" nowrap>SMS</th>
										<th width="10%" nowrap><?=$상태[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
									<?
									$Sql = "
											select 
												A.*,
												B.MemberName,
												B.MemberLoginID, 
												ifnull(H.MemberName,'시스템') as RegMemberName,
												ifnull(H.MemberLoginID, '-') as RegMemberLoginID
											from Counsels A
												inner join Members B on A.MemberID=B.MemberID 
												left outer join Members H on A.RegMemberID=H.MemberID 
											where A.MemberID=:MemberID and A.CounselState=1 
											order by A.CounselRegDateTime desc";
									$Stmt = $DbConn->prepare($Sql);
									$Stmt->bindParam(':MemberID', $MemberID);
									$Stmt->execute();
									$Stmt->setFetchMode(PDO::FETCH_ASSOC);

									$ListCount = 1;
									while($Row = $Stmt->fetch()) {
										$CounselID = $Row["CounselID"];
										$MemberID = $Row["MemberID"];
										$RegMemberID = $Row["RegMemberID"];
										$MemberWriteName = $Row["MemberWriteName"];
										$TeacherWriteName = $Row["TeacherWriteName"];
										$CounselTitle = $Row["CounselTitle"];
										$CounselDate = $Row["CounselDate"];
										$CounselTime = $Row["CounselTime"];
										$CounselContent = $Row["CounselContent"];
										$CounselSms = $Row["CounselSms"];
										$CounselState = $Row["CounselState"];
										
										$MemberName = $Row["MemberName"];
										$MemberLoginID = $Row["MemberLoginID"];
										$RegMemberName = $Row["RegMemberName"];
										$RegMemberLoginID = $Row["RegMemberLoginID"];
										
										if ($CounselState==1){
											$StrCounselState = "<span class=\"ListState_1\">공개</span>";
										}else if ($CounselState==2){
											$StrCounselState = "<span class=\"ListState_2\">미공개</span>";
										}

										if ($CounselSms==1){
											$StrCounselSms = "전송";
										}else{
											$StrCounselSms = "-";
										}
							
									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><?=$CounselDate?> <?=$CounselTime?></td>
										<td class="uk-text-nowrap"><?=$CounselTitle?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$CounselContent?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$TeacherWriteName?><br>(<?=$RegMemberLoginID?>)</td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrCounselSms?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrCounselState?></td>
									</tr>
									<?php
										$ListCount ++;
									}
									$Stmt = null;
									?>


								</tbody>
							</table>

						</li>
						<li>
							<!--SMS내역-->
							<table class="uk-table uk-table-align-vertical">
								<thead>
									<tr>
										<th width="15%" nowrap><?=$메시지_전송시간[$LangID]?></th>
										<th nowrap><?=$제목[$LangID]?></th>
										<th width="50%" nowrap><?=$내용[$LangID]?></th>
										<th width="10%">PUSH <?=$전송[$LangID]?></th>
										<th width="10%">SMS <?=$전송[$LangID]?></th>
										<!--<th width="10%">Kakao <?=$전송[$LangID]?></th>-->
									</tr>
								</thead>
								<tbody>
								<?
								$Sql = "
										select 
											A.*,
											B.MemberName,
											B.MemberLoginID, 
											ifnull(H.MemberName,'시스템') as RegMemberName,
											ifnull(H.MemberLoginID, '-') as RegMemberLoginID 
										from SendMessageLogs A
											inner join Members B on A.MemberID=B.MemberID 
											left outer join Members H on A.SendMemberID=H.MemberID 
										where A.MemberID=:MemberID and A.SendMemberParentCheck=1 
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
										$StrUseSendPush = "요청";
									}else{
										$StrUseSendPush = "-";
									}
									if ($UseSendSms==1){
										$StrUseSendSms = "요청";
									}else{
										$StrUseSendSms = "-";
									}
									if ($UseSendKakao==1){
										$StrUseSendKakao = "요청";
									}else{
										$StrUseSendKakao = "-";
									}


									if ($PushMessageState==2){
										$StrPushMessageState = "완료";
									}else{
										$StrPushMessageState = "-";
									}
									if ($SmsMessageState==2){
										$StrSmsMessageState = "완료";
									}else{
										$StrSmsMessageState = "-";
									}
									if ($KakaoMessageState==2){
										$StrKakaoMessageState = "완료";
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
						<li>
							<!--포인트내역-->
							<table class="uk-table uk-table-align-vertical">
								<thead>
									<tr>
										<th width="15%" nowrap><?=$적립일시[$LangID]?></th>
										<th nowrap><?=$제목[$LangID]?></th>
										<th width="40%" nowrap><?=$내용[$LangID]?></th>
										<th width="10%" nowrap><?=$포인트[$LangID]?></th>
										<th width="10%" nowrap><?=$누적포인트[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
								<?
								$Sql = "
										select 
											A.*,
											B.MemberName,
											B.MemberLoginID, 
											ifnull(H.MemberName,'시스템') as RegMemberName,
											ifnull(H.MemberLoginID, '-') as RegMemberLoginID, 
											(select sum(AA.MemberPoint) from MemberPoints AA where AA.MemberID=A.MemberID and AA.MemberPointState=1 and AA.MemberPointID<=A.MemberPointID) as TotalMemberPoint 
										from MemberPoints A
											inner join Members B on A.MemberID=B.MemberID 
											left outer join Members H on A.RegMemberID=H.MemberID 
										where A.MemberID=:MemberID and A.MemberPointState=1 
										order by A.MemberPointRegDateTime desc";
								$Stmt = $DbConn->prepare($Sql);
								$Stmt->bindParam(':MemberID', $MemberID);
								$Stmt->execute();
								$Stmt->setFetchMode(PDO::FETCH_ASSOC);

								$ListCount = 1;
								while($Row = $Stmt->fetch()) {

									$MemberPointID = $Row["MemberPointID"];
									$MemberID = $Row["MemberID"];
									$RegMemberID = $Row["RegMemberID"];
									$MemberPointName = $Row["MemberPointName"];
									$MemberPointText = $Row["MemberPointText"];
									$MemberPoint = $Row["MemberPoint"];
									$MemberPointRegDateTime = $Row["MemberPointRegDateTime"];

									$MemberName = $Row["MemberName"];
									$MemberLoginID = $Row["MemberLoginID"];
									$RegMemberName = $Row["RegMemberName"];
									$RegMemberLoginID = $Row["RegMemberLoginID"];									

									$TotalMemberPoint = $Row["TotalMemberPoint"];
								?>
								<tr>
									<td class="uk-text-nowrap uk-table-td-center"><?=$MemberPointRegDateTime?></td>
									<td class="uk-text-nowrap"><?=$MemberPointName?></td>
									<td class="uk-text-nowrap"><?=$MemberPointText?></td>
									<td class="uk-text-nowrap uk-table-td-right"><?=number_format($MemberPoint,0)?> P</td>
									<td class="uk-text-nowrap uk-table-td-right"><?=number_format($TotalMemberPoint,0)?> P</td>
								</tr>
								<?php
									$ListCount ++;
								}
								$Stmt = null;
								?>
							
								</tbody>
							</table>
						</li>
						<li>

							<!--결제내역-->
							<table class="uk-table uk-table-align-vertical">
								<thead>
									<tr>
										<th nowrap><?=$상태[$LangID]?></th>
										<th nowrap><?=$결제번호[$LangID]?></th>
										<th nowrap><?=$결제일[$LangID]?></th>
										<th nowrap><?=$대리점[$LangID]?></th>
										<th nowrap><?=$결제수단[$LangID]?></th>
										<th nowrap><?=$판매금액[$LangID]?></th>
										<th nowrap><?=$할인금액[$LangID]?></th>
										<th nowrap><?=$결제금액[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
								<?
								$AddSqlWhere = "1=1";
								$AddSqlWhere = $AddSqlWhere . " and A.ClassOrderPayType=0 ";
								$AddSqlWhere = $AddSqlWhere . " and A.PayResultMsg<>'' ";

								$AddSqlWhere = $AddSqlWhere . " and B.MemberState<>0 ";
								$AddSqlWhere = $AddSqlWhere . " and C.CenterState<>0 ";
								$AddSqlWhere = $AddSqlWhere . " and D.BranchState<>0 ";
								$AddSqlWhere = $AddSqlWhere . " and E.BranchGroupState<>0 ";
								$AddSqlWhere = $AddSqlWhere . " and F.CompanyState<>0 ";
								$AddSqlWhere = $AddSqlWhere . " and G.FranchiseState<>0 ";
								$AddSqlWhere = $AddSqlWhere . " and A.ClassOrderPayPaymentMemberID=".$MemberID."";

								$Sql = "
									select 
										A.*,
										B.MemberName,
										B.MemberLoginID, 
										B.MemberLevelID,
										C.CenterID as JoinCenterID,
										C.CenterName as JoinCenterName,
										D.BranchID as JoinBranchID,
										D.BranchName as JoinBranchName, 
										E.BranchGroupID as JoinBranchGroupID,
										E.BranchGroupName as JoinBranchGroupName,
										F.CompanyID as JoinCompanyID,
										F.CompanyName as JoinCompanyName,
										G.FranchiseName,
										K.MemberLevelName
									from ClassOrderPays A

										inner join Members B on A.ClassOrderPayPaymentMemberID=B.MemberID 
										inner join Centers C on B.CenterID=C.CenterID 
										inner join Branches D on C.BranchID=D.BranchID 
										inner join BranchGroups E on D.BranchGroupID=E.BranchGroupID 
										inner join Companies F on E.CompanyID=F.CompanyID 
										inner join Franchises G on F.FranchiseID=G.FranchiseID 
										inner join MemberLevels K on B.MemberLevelID=K.MemberLevelID 
									where ".$AddSqlWhere." 
									order by A.ClassOrderPayID desc ";
								?> <!-- <? echo $Sql ?> --> <?
								$Stmt = $DbConn->prepare($Sql);
								$Stmt->bindParam(':MemberID', $MemberID);
								$Stmt->execute();
								$Stmt->setFetchMode(PDO::FETCH_ASSOC);

								$ListCount = 1;
								while($Row = $Stmt->fetch()) {
										$res_msg = $Row["res_msg"];
										$tno = $Row["tno"];
										$PayResultMsg = $Row["PayResultMsg"];

										$ClassOrderPayNumber = $Row["ClassOrderPayNumber"];
										$ClassOrderPaySellingPrice = $Row["ClassOrderPaySellingPrice"];
										$ClassOrderPayDiscountPrice = $Row["ClassOrderPayDiscountPrice"];
										$ClassOrderPayPaymentPrice = $Row["ClassOrderPayPaymentPrice"];
										$ClassOrderPayUseSavedMoneyPrice = $Row["ClassOrderPayUseSavedMoneyPrice"];
										$ClassOrderPayUseCashPrice = $Row["ClassOrderPayUseCashPrice"];
										$ClassOrderPayUseCashPaymentType = $Row["ClassOrderPayUseCashPaymentType"];
										
										$ClassOrderPayPgFeeRatio = $Row["ClassOrderPayPgFeeRatio"];
										$ClassOrderPayPgFeePrice = $Row["ClassOrderPayPgFeePrice"];
										$ClassOrderPayProgress = $Row["ClassOrderPayProgress"];

										$ClassOrderPayDateTime = $Row["ClassOrderPayDateTime"];
										$ClassOrderPayPaymentDateTime = $Row["ClassOrderPayPaymentDateTime"];
										$ClassOrderPayCencelRequestDateTime = $Row["ClassOrderPayCencelRequestDateTime"];
										$ClassOrderPayCencelDateTime = $Row["ClassOrderPayCencelDateTime"];
										$ClassOrderPayRefundRequestDateTime = $Row["ClassOrderPayRefundRequestDateTime"];
										$ClassOrderPayRefundDateTime = $Row["ClassOrderPayRefundDateTime"];
										$ClassOrderPayRegDateTime = $Row["ClassOrderPayRegDateTime"];
										$ClassOrderPayModiDateTime = $Row["ClassOrderPayModiDateTime"];

										$MemberName = $Row["MemberName"];
										$MemberLoginID = $Row["MemberLoginID"];
										
										$CenterID = $Row["JoinCenterID"];
										$CenterName = $Row["JoinCenterName"];
										$BranchID = $Row["JoinBranchID"];
										$BranchName = $Row["JoinBranchName"];
										$BranchGroupID = $Row["JoinBranchGroupID"];
										$BranchGroupName = $Row["JoinBranchGroupName"];
										$CompanyID = $Row["JoinCompanyID"];
										$CompanyName = $Row["JoinCompanyName"];
										$FranchiseName = $Row["FranchiseName"];

										$StrClassOrderPayUseCashPaymentType="-";
										if ($ClassOrderPayUseCashPaymentType==1){
											$StrClassOrderPayUseCashPaymentType="카드";
										}else if ($ClassOrderPayUseCashPaymentType==2){
											$StrClassOrderPayUseCashPaymentType="실시간이체";
										}else if ($ClassOrderPayUseCashPaymentType==3){
											$StrClassOrderPayUseCashPaymentType="가상계좌";
										}else if ($ClassOrderPayUseCashPaymentType==4){
											$StrClassOrderPayUseCashPaymentType="계좌입금";
										}else if ($ClassOrderPayUseCashPaymentType==5){
											$StrClassOrderPayUseCashPaymentType="오프라인";
										}else if ($ClassOrderPayUseCashPaymentType==9){
											$StrClassOrderPayUseCashPaymentType="기타";
										}
							
									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><?=$PayResultMsg?></td>
										<td class="uk-text-nowrap"><?=$ClassOrderPayNumber?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ClassOrderPayPaymentDateTime?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$CenterName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrClassOrderPayUseCashPaymentType?></td>
										<td class="uk-text-nowrap uk-table-td-right"><?=number_format($ClassOrderPaySellingPrice,0)?></td>
										<td class="uk-text-nowrap uk-table-td-right"><?=number_format($ClassOrderPayDiscountPrice,0)?></td>
										<td class="uk-text-nowrap uk-table-td-right"><?=number_format($ClassOrderPayPaymentPrice,0)?></td>
									</tr>
								<?php
									$ListCount ++;
								}
								$Stmt = null;
								?>
							
								</tbody>
							</table>
						</li>
						<li>

							<!--정기평가보고서-->
							<table class="uk-table uk-table-align-vertical">
								<thead>
									<tr>
										<th width="10%" nowrap><?=$번호[$LangID]?></th>
										<th width="70%" nowrap><?=$평가서[$LangID]?></th>
										<th nowrap>보기</th>
									</tr>
								</thead>
								<tbody>
								<?
								$Sql = "select 
											A.*
										from AssmtStudentMonthlyScores A 
										where A.ClassID in (select ClassID from Classes where MemberID=:MemberID )  
										order by A.AssmtStudentMonthlyScoreRegDateTime desc";

								$Stmt = $DbConn->prepare($Sql);
								$Stmt->bindParam(':MemberID', $MemberID);
								$Stmt->execute();
								$Stmt->setFetchMode(PDO::FETCH_ASSOC);

								$ListCount = 1;
								while($Row = $Stmt->fetch()) {

									$AssmtStudentMonthlyScoreID = $Row["AssmtStudentMonthlyScoreID"];
									$AssmtStudentMonthlyScoreSubject = $Row["AssmtStudentMonthlyScoreSubject"];
									$AssmtStudentMonthlyScoreYear = $Row["AssmtStudentMonthlyScoreYear"];
									$AssmtStudentMonthlyScoreMonth = $Row["AssmtStudentMonthlyScoreMonth"];
								?>
								<tr>
									<td class="uk-text-nowrap uk-table-td-center"><?=$ListCount?></td>
									<td class="uk-text-nowrap"><?=$AssmtStudentMonthlyScoreSubject?>(<?=$AssmtStudentMonthlyScoreYear?>. <?=substr("0".$AssmtStudentMonthlyScoreMonth,-2)?>)</a></td>
									<td class="uk-text-nowrap uk-table-td-center"><a href="javascript:OpenStudentScoreMonthlyReport(<?=$AssmtStudentMonthlyScoreID?>);"><img src="../images/btn_report.png" class="mypage_report_btn "></a></td>
								</tr>
								<?php
									$ListCount ++;
								}
								$Stmt = null;
								?>
							
								</tbody>
							</table>
						</li>
						<li>
							<!--레벨테스트-->
							<table class="uk-table uk-table-align-vertical">
								<thead>
									<tr>
										<th width="10%"><?=$번호[$LangID]?></th>
										<th><?=$레벨테스트[$LangID]?></th>
										<th width="15%"><?=$평가일[$LangID]?></th>
										<th width="15%"><?=$평가서보기[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
								<?

								$Sql = "select 
											A.*
										from AssmtStudentLeveltestScores A 
										where A.ClassID in (select ClassID from Classes where MemberID=:MemberID )  
										order by A.AssmtStudentLeveltestScoreRegDateTime desc";

								$Stmt = $DbConn->prepare($Sql);
								$Stmt->bindParam(':MemberID', $MemberID);
								$Stmt->execute();
								$Stmt->setFetchMode(PDO::FETCH_ASSOC);

								$ListCount=1;
								while($Row = $Stmt->fetch()) {
									$AssmtStudentLeveltestScoreID = $Row["AssmtStudentLeveltestScoreID"];
									$AssmtStudentLeveltestScoreYear = $Row["AssmtStudentLeveltestScoreYear"];
									$AssmtStudentLeveltestScoreMonth = $Row["AssmtStudentLeveltestScoreMonth"];
									$AssmtStudentLeveltestScoreDay = $Row["AssmtStudentLeveltestScoreDay"];
									$AssmtStudentLeveltestScoreLevel = $Row["AssmtStudentLeveltestScoreLevel"];

								?>
								<tr>
									<td class="uk-text-nowrap uk-table-td-center"><?=$ListCount?></td>
									<td class="uk-text-nowrap">레벨테스트 (LEVEL <?=$AssmtStudentLeveltestScoreLevel?>)</td>
									<td class="uk-text-nowrap"><?=$AssmtStudentLeveltestScoreYear?>. <?=substr("0".$AssmtStudentLeveltestScoreMonth,-2)?>. <?=substr("0".$AssmtStudentLeveltestScoreDay,-2)?></td>
									<td class="uk-text-nowrap uk-table-td-center"><a href="javascript:OpenStudentScoreLeveltestReport(<?=$AssmtStudentLeveltestScoreID?>);"><img src="../images/btn_report.png" class="mypage_report_btn"></a></td>
								</tr>
								<?php
									$ListCount ++;
								}
								$Stmt = null;
								?>
							
								</tbody>
							</table>
						</li>


						<li>
							<!--교재구매바구니-->
							<table class="uk-table uk-table-align-vertical">
								<thead>
									<tr>
										<th width="10%"><?=$번호[$LangID]?></th>
										<th><?=$바구니명[$LangID]?></th>
										<th width="10%"><?=$교재종류수[$LangID]?></th>
										<th width="10%"><?=$총교재수[$LangID]?></th>
										<th width="10%"><?=$금액[$LangID]?></th>
										<th width="10%"><?=$등록일[$LangID]?></th>
										<th width="10%"><?=$상태[$LangID]?></th>
										<th width="10%"><?=$상세보기[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
								<?

								$Sql = "select 
											count(*) as TotalCount
										from ProductOrderCarts A 
										where A.MemberID=:MemberID and (A.ProductOrderCartState=1 or A.ProductOrderCartState=2) ";

								$Stmt = $DbConn->prepare($Sql);
								$Stmt->bindParam(':MemberID', $MemberID);
								$Stmt->execute();
								$Stmt->setFetchMode(PDO::FETCH_ASSOC);
								$Row = $Stmt->fetch();
								$TotalCount = $Row["TotalCount"];


								$Sql = "select 
											A.*,
											(select count(*) from ProductOrderCartDetails where ProductOrderCartID=A.ProductOrderCartID) as ProductOrderCartDetailCount,
											(select sum(ProductCount) from ProductOrderCartDetails where ProductOrderCartID=A.ProductOrderCartID) as ProductOrderCartDetailProductCount,
											(select sum(AA.ProductCount*BB.ProductPrice) from ProductOrderCartDetails AA inner join Products BB on AA.ProductID=BB.ProductID where AA.ProductOrderCartID=A.ProductOrderCartID) as ProductOrderCartDetailProductPrice
										from ProductOrderCarts A 
										where A.MemberID=:MemberID and (A.ProductOrderCartState=1 or A.ProductOrderCartState=2)
										order by A.ProductOrderCartOrder desc";

								$Stmt = $DbConn->prepare($Sql);
								$Stmt->bindParam(':MemberID', $MemberID);
								$Stmt->execute();
								$Stmt->setFetchMode(PDO::FETCH_ASSOC);

								$ListCount=1;
								while($Row = $Stmt->fetch()) {

									$ListNumber = $TotalCount - $ListCount + 1;

									$ProductOrderCartID = $Row["ProductOrderCartID"];
									$ProductOrderCartName = $Row["ProductOrderCartName"];
									$ProductOrderCartState = $Row["ProductOrderCartState"];
									$ProductOrderCartRegDateTime = $Row["ProductOrderCartRegDateTime"];
									$ProductOrderCartModiDateTime = $Row["ProductOrderCartModiDateTime"];

									$StrProductOrderCartRegDateTime = substr($ProductOrderCartRegDateTime, 0,10);

									$ProductOrderCartDetailCount = $Row["ProductOrderCartDetailCount"];
									$ProductOrderCartDetailProductCount = $Row["ProductOrderCartDetailProductCount"];
									$ProductOrderCartDetailProductPrice = $Row["ProductOrderCartDetailProductPrice"];

									if ($ProductOrderCartState==1){
										$StrProductOrderCartState = "숨김";
									}else if ($ProductOrderCartState==2){
										$StrProductOrderCartState = "노출";
									}

								?>
								<tr>
									<td class="uk-text-nowrap uk-table-td-center"><?=$ListNumber?></td>
									<td class="uk-text-nowrap"><?=$ProductOrderCartName?></td>
									<td class="uk-text-nowrap uk-table-td-center"><?=number_format($ProductOrderCartDetailCount,0)?></td>
									<td class="uk-text-nowrap uk-table-td-center"><?=number_format($ProductOrderCartDetailProductCount,0)?></td>
									<td class="uk-text-nowrap uk-table-td-center"><?=number_format($ProductOrderCartDetailProductPrice,0)?></td>
									<td class="uk-text-nowrap uk-table-td-center"><?=$StrProductOrderCartRegDateTime?></td>
									<td class="uk-text-nowrap uk-table-td-center"><?=$StrProductOrderCartState?></td>
									<td class="uk-text-nowrap uk-table-td-center"><a href="javascript:OpenProductOrderCartForm(<?=$ProductOrderCartID?>);">상세보기</a></td>
								</tr>
								<?php
									$ListCount ++;
								}
								$Stmt = null;
								?>
							
								</tbody>
							</table>

							<div class="uk-form-row" style="text-align:center;">
								<a type="button" href="javascript:OpenProductOrderCartForm('')" class="md-btn md-btn-primary">신규등록</a>
							</div>
						</li>

						<li>
							<!--스케쥴-->
							<iframe src="../pop_study_calendar.php?MemberID=<?=$MemberID?>&IframeMode=<?=$IframeMode?>" scrolling="no" frameborder="1" id="iframe_calendar" height="600" width="100%"></iframe>
						</li>

						<?
						}
						?>
					</ul>
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
<img src="//t1.daumcdn.net/postcode/resource/images/close.png" id="btnCloseLayer" style="cursor:pointer;position:absolute;right:-3px;top:-3px;z-index:1" onclick="closeDaumPostcode()" alt="닫기 버튼">
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
				document.getElementById('MemberZip').value = data.zonecode;
				document.getElementById("MemberAddr1").value = addr;
				// 커서를 상세주소 필드로 이동한다.
				document.getElementById("MemberAddr2").focus();

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


<script language="javascript">

function EnNewID(){
	document.RegForm.CheckedID.value = "0";
	document.getElementById('BtnCheckID').style.display = "";
}

function CheckID(){
	var NewID = $.trim($('#MemberLoginID').val());

	if (NewID == "") {
		UIkit.modal.alert("아이디를 입력하세요.");
		document.RegForm.CheckedID.value = "0";
	} else if (NewID.length<2)  {
		UIkit.modal.alert("아이디는 2자 이상 입력하세요.");
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
					UIkit.modal.alert("사용 가능한 아이디 입니다.");
					document.RegForm.CheckedID.value = "1";
					document.getElementById('BtnCheckID').style.display = "none";
				}
				else {
					UIkit.modal.alert("이미 사용중인 아이디 입니다.");
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


function OpenProductOrderCartForm(ProductOrderCartID){
	openurl = "product_order_cart_form.php?MemberID=<?=$MemberID?>&ProductOrderCartID="+ProductOrderCartID;
	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "1000"
		,maxHeight: "750"
		,title:""
		,iframe:true 
		,scrolling:true
		,onClosed:function(){
			//location.reload();
			location.href='popup_favorite_student_menu.php?MemberID=<?=$MemberID?>&IframeMode=<?=$IframeMode?>&PageTabID=12';
		}
		//,onComplete:function(){alert(1);}
	}); 
}

function OpenStudentScoreMonthlyReport(AssmtStudentMonthlyScoreID){

	var OpenUrl = "../report_monthly.php?AssmtStudentMonthlyScoreID="+AssmtStudentMonthlyScoreID;

	$.colorbox({	
		href:OpenUrl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "1200"
		,maxHeight: "700"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}

function OpenStudentScoreLeveltestReport(AssmtStudentLeveltestScoreID){
	var OpenUrl = "../report_leveltest.php?AssmtStudentLeveltestScoreID="+AssmtStudentLeveltestScoreID;

	$.colorbox({	
		href:OpenUrl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "1200"
		,maxHeight: "700"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}


//================ 이메일 =============
function EnNewEmail(){
	document.RegForm.CheckedEmail.value = "0";
	document.getElementById('BtnCheckEmail').style.display = "inline";
}



function CheckEmail(){
    var MemberEmail_1 = $.trim($('#MemberEmail_1').val());
	var MemberEmail_2 = $.trim($('#MemberEmail_2').val());

    if (MemberEmail_1 == "" || MemberEmail_2 == "") {
        alert('이메일을 입력하세요.');
        document.RegForm.CheckedEmail.value = "0";
	} else {
        url = "ajax_check_email.php";

		//location.href = url + "?MemberEmail_1="+MemberEmail_1+"&MemberEmail_2="+MemberEmail_2+"&MemberID=<?=$MemberID?>";
        $.ajax(url, {
            data: {
                MemberEmail_1: MemberEmail_1,
				MemberEmail_2: MemberEmail_2,
				MemberID: "<?=$MemberID?>"
            },
            success: function (data) {
				json_data = data;
				CheckResult = json_data.CheckResult;
                if (CheckResult == 1) {
                    alert('사용 가능한 이메일 입니다.');
                    document.RegForm.CheckedEmail.value = "1";
					document.getElementById('BtnCheckEmail').style.display = "none";
                }
                else {
                    alert('이미 등록된 이메일 입니다.');
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


function addLoadEvent(func){
	var oldonload = window.onload;

	if(typeof window.onload != "function"){
		window.onload = func;
	}else{
		window.onload = function(){
			oldonload();
			func();
		}
	}
}

function resize(){
	//var height = document.body.scrollHeight;
	//var height = document.getElementById("iframe_calendar").contentWindow.document.body.scrollHeight
	//document.getElementById("iframe_calendar").style.height = height + 50 + "px";
}

//setTimeout(function() {
	addLoadEvent(resize);
//}, 1000);

/*
$("#iframe_calendar").parent().height($("#iframe_calendar").height());
$("#iframe_calendar").height(0);
$("#iframe_calendar").height($("#iframe_calendar").contents().height());
$("#iframe_calendar").parent().height("");
*/

function SetEmailName(){
	MemberEmail_3 = document.RegForm.MemberEmail_3.value;
	if (MemberEmail_3==""){
		document.RegForm.MemberEmail_2.value = "";
		document.RegForm.MemberEmail_2.readOnly = false;
	}else{
		document.RegForm.MemberEmail_2.value = MemberEmail_3;
		document.RegForm.MemberEmail_2.readOnly = true;
	}

	EnNewEmail();
}

function SetEmailName2(){
	MemberEmail2_3 = document.RegForm.MemberEmail2_3.value;
	if (MemberEmail2_3==""){
		document.RegForm.MemberEmail2_2.value = "";
		document.RegForm.MemberEmail2_2.readOnly = false;
	}else{
		document.RegForm.MemberEmail2_2.value = MemberEmail_3;
		document.RegForm.MemberEmail2_2.readOnly = true;
	}
}
//================ 이메일 =============



function FormSubmit(){
	obj = document.RegForm.CenterID;
	if (obj.value==""){
		UIkit.modal.alert("소속 대리점를 선택하세요.");
		obj.focus();
		return;
	}

	obj = document.RegForm.MemberName;
	if (obj.value==""){
		UIkit.modal.alert("학생명을 입력하세요.");
		obj.focus();
		return;
	}

	/*
	obj = document.RegForm.MemberTimeZoneID;
	if (obj.value==""){
		UIkit.modal.alert("활동지역을 선택하세요.");
		obj.focus();
		return;
	}
	*/


	obj = document.RegForm.MemberLoginID;
	if (obj.value==""){
		UIkit.modal.alert("아이디를 입력하세요.");
		obj.focus();
		return;
	}

	obj = document.RegForm.MemberLoginID;
	if (obj.value.length<2){
		UIkit.modal.alert("아이디는 2자 이상 입력하세요.");
		obj.focus();
		return;
	}


	obj = document.RegForm.CheckedID;
	if (obj.value=="0"){
		UIkit.modal.alert("아이디 중복확인 버튼을 클릭하세요.");
		return;
	}


	<?
	if ($MemberID!=""){ 
	?>	
		obj = document.RegForm.MemberLoginNewPW;
		obj2 = document.RegForm.MemberLoginNewPW2;

		if (obj.value!="" || obj2.value!=""){
			
			if (obj.value.length<2){
				UIkit.modal.alert("비밀번호는 2자 이상 입력하세요.");
				obj.focus();
				return;
			}			
			
			if (obj.value!=obj2.value){
				UIkit.modal.alert("비밀번호와 비밀번호 확인이 일치하지 않습니다.");
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
			UIkit.modal.alert("비밀번호를 입력하세요.");
			obj.focus();
			return;
		}

		if (obj.value.length<2){
			UIkit.modal.alert("비밀번호는 2자 이상 입력하세요.");
			obj.focus();
			return;
		}	

		if (obj.value!=obj2.value){
			UIkit.modal.alert("비밀번호와 비밀번호 확인이 일치하지 않습니다.");
			obj.focus();
			return;
		}
	<?
	}
	?>


	obj = document.RegForm.MemberPhone1_2;
	if (obj.value==""){
		UIkit.modal.alert("학생 휴대전화를 입력하세요.");
		obj.focus();
		return;
	}

	obj = document.RegForm.MemberPhone1_2;
	if (obj.value==""){
		UIkit.modal.alert("학생 휴대전화를 입력하세요.");
		obj.focus();
		return;
	}


	/*

	obj = document.RegForm.MemberEmail;
	if (obj.value==""){
		UIkit.modal.alert("이메일을 입력하세요.");
		obj.focus();
		return;
	}

	obj = document.RegForm.CheckedEmail;
	if (obj.value=="0"){
		UIkit.modal.alert("이메일 중복확인 버튼을 클릭하세요.");
		return;
	}
	*/

	obj = document.RegForm.ForceUseClassIn;
	obj2 = document.RegForm.MemberCiTelephone;
	if (obj.checked && obj2.value==""){
		UIkit.modal.alert("클래스인만 사용할 경우 클래스인 아이디를 입력해 주세요.");
		return;
	}


	UIkit.modal.confirm(
		'저장 하시겠습니까?', 
		function(){ 
			document.RegForm.action = "student_action.php";
			document.RegForm.submit();
		}
	);


}

</script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">


<link rel="stylesheet" href="../js/colorbox/example2/colorbox.css" />
<script src="../js/colorbox/jquery.colorbox.js"></script>
<script>
jQuery(document).ready(function(){
$(document).bind('cbox_open', function() {
	$('html').css({ overflow: 'hidden' });
}).bind('cbox_closed', function() {
	$('html').css({ overflow: '' });
});
});
</script>
</body>
</html>
