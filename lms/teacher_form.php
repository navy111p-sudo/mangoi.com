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
 <!-- dropify -->
<link rel="stylesheet" href="assets/skins/dropify/css/dropify.css">
<!-- ============== only this page css ============== -->
<!-- ============== common.css ============== -->
<link rel="stylesheet" type="text/css" href="css/common.css" />
<!-- ============== common.css ============== -->


</head>

<body class="disable_transitions sidebar_main_open sidebar_main_swipe">
<?php
$MainMenuID = 13;
$SubMenuID = 1301; 
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
?>



<?php
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$TeacherID = isset($_REQUEST["TeacherID"]) ? $_REQUEST["TeacherID"] : "";
$PageTabID = isset($_REQUEST["PageTabID"]) ? $_REQUEST["PageTabID"] : "";
if ($PageTabID==""){
	$PageTabID = "1";
}


if ($TeacherID!=""){

	$Sql = "
			select 
					A.*,
					AES_DECRYPT(UNHEX(A.TeacherPhone1),:EncryptionKey) as DecTeacherPhone1,
					AES_DECRYPT(UNHEX(A.TeacherPhone2),:EncryptionKey) as DecTeacherPhone2,
					AES_DECRYPT(UNHEX(A.TeacherPhone3),:EncryptionKey) as DecTeacherPhone3,
					AES_DECRYPT(UNHEX(A.TeacherEmail),:EncryptionKey) as DecTeacherEmail,
					C.MemberID,
					C.MemberLoginID,
					C.MemberLoginPW,
					C.MemberLanguageID,
					C.MemberCiTelephone,
					C.MemberTimeZoneID,
					C.MemberBirthday,
					C.MemberSex,
					E.EduCenterHoliday0,
					E.EduCenterHoliday1,
					E.EduCenterHoliday2,
					E.EduCenterHoliday3,
					E.EduCenterHoliday4,
					E.EduCenterHoliday5,
					E.EduCenterHoliday6
			from Teachers A 
				inner join Members C on A.TeacherID=C.TeacherID and C.MemberLevelID=15 
				inner join TeacherGroups D on A.TeacherGroupID=D.TeacherGroupID 
				inner join EduCenters E on D.EduCenterID=E.EduCenterID 
			where A.TeacherID=:TeacherID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TeacherID', $TeacherID);
	$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$TeacherGroupID = $Row["TeacherGroupID"];
	$TeacherPayTypeItemID = $Row["TeacherPayTypeItemID"];
	$TeacherName = $Row["TeacherName"];
	$TeacherNickName = $Row["TeacherNickName"];
	$TeacherImageFileName = $Row["TeacherImageFileName"];
	//================ 전화번호 / 이메일 =============
	$TeacherPhone1 = $Row["DecTeacherPhone1"];
	$TeacherPhone2 = $Row["DecTeacherPhone2"];
	$TeacherPhone3 = $Row["DecTeacherPhone3"];
	$TeacherEmail = $Row["DecTeacherEmail"];
	//================ 전화번호 / 이메일 =============
	$TeacherZip = $Row["TeacherZip"];
	$TeacherAddr1 = $Row["TeacherAddr1"];
	$TeacherAddr2 = $Row["TeacherAddr2"];
	$TeacherVideoType = $Row["TeacherVideoType"];
	$TeacherVideoCode = $Row["TeacherVideoCode"];
	$TeacherIntroText = $Row["TeacherIntroText"];
	$TeacherPayPerTime = $Row["TeacherPayPerTime"];
	$TeacherState = $Row["TeacherState"];
	$TeacherView = $Row["TeacherView"];
	$TeacherIntroEdu = $Row["TeacherIntroEdu"];
	$TeacherIntroSpec = $Row["TeacherIntroSpec"];
	$TeacherIsManager = $Row["TeacherIsManager"];

	$TeacherStartHour = $Row["TeacherStartHour"];
	$TeacherEndHour = $Row["TeacherEndHour"];

	$EduCenterHoliday[0] = $Row["EduCenterHoliday0"];
	$EduCenterHoliday[1] = $Row["EduCenterHoliday1"];
	$EduCenterHoliday[2] = $Row["EduCenterHoliday2"];
	$EduCenterHoliday[3] = $Row["EduCenterHoliday3"];
	$EduCenterHoliday[4] = $Row["EduCenterHoliday4"];
	$EduCenterHoliday[5] = $Row["EduCenterHoliday5"];
	$EduCenterHoliday[6] = $Row["EduCenterHoliday6"];

	$TeacherBlock80Min = $Row["TeacherBlock80Min"];

	$TempTeacherID = $TeacherID;

	//Members 
	$MemberID = $Row["MemberID"];
	$MemberLoginID = $Row["MemberLoginID"];
	$MemberLoginPW = $Row["MemberLoginPW"];
	$MemberLanguageID = $Row["MemberLanguageID"];
	$MemberCiTelephone = $Row["MemberCiTelephone"];
	$MemberTimeZoneID = $Row["MemberTimeZoneID"];
	$MemberBirthday = $Row["MemberBirthday"];
	$MemberSex = $Row["MemberSex"];
	$CheckedID = 1;
	$CheckedEmail = 1;



}else{
	$TeacherGroupID = "";
	$TeacherPayTypeItemID = "";
	$TeacherName = "";
	$TeacherNickName = "";
	$TeacherImageFileName = "";
	//================ 전화번호 / 이메일 =============
	$TeacherPhone1 = "--";
	$TeacherPhone2 = "--";
	$TeacherPhone3 = "--";
	$TeacherEmail = "@";
	//================ 전화번호 / 이메일 =============
	$TeacherZip = "";
	$TeacherAddr1 = "";
	$TeacherAddr2 = "";
	$TeacherVideoType = 1;
	$TeacherVideoCode = "";
	$TeacherIntroText = "";
	$TeacherIntroSpec = "";
	$TeacherPayPerTime = "";
	$TeacherState = 1;
	$TeacherView = 1;
	$TeacherIntroEdu = "";
	$TeacherIsManager = 1;

	$TeacherStartHour = 14;
	$TeacherEndHour = 22;
	
	$TeacherBlock80Min = 1;

	$TempTeacherID = 0;

	//Members 
	$MemberID = "";
	$MemberLoginID = "";
	$MemberLoginPW = "";
	$MemberLanguageID = 1;
	$MemberCiTelephone = "";
	$MemberTimeZoneID = "";
	$MemberBirthday = "";
	$MemberSex = 1;
	$CheckedID = 0;
	$CheckedEmail = 0;
}


//================ 전화번호 / 이메일 =============
$ArrTeacherPhone1 = explode("-", $TeacherPhone1);
$ArrTeacherPhone2 = explode("-", $TeacherPhone2);
$ArrTeacherPhone3 = explode("-", $TeacherPhone3);
$ArrTeacherEmail = explode("@", $TeacherEmail);

$TeacherPhone1_1 = $ArrTeacherPhone1[0];
$TeacherPhone1_2 = $ArrTeacherPhone1[1];
$TeacherPhone1_3 = $ArrTeacherPhone1[2];

$TeacherPhone2_1 = $ArrTeacherPhone2[0];
$TeacherPhone2_2 = $ArrTeacherPhone2[1];
$TeacherPhone2_3 = $ArrTeacherPhone2[2];

$TeacherPhone3_1 = $ArrTeacherPhone3[0];
$TeacherPhone3_2 = $ArrTeacherPhone3[1];
$TeacherPhone3_3 = $ArrTeacherPhone3[2];

$TeacherEmail_1 = $ArrTeacherEmail[0];
$TeacherEmail_2 = $ArrTeacherEmail[1];
//================ 전화번호 / 이메일 =============


$MemberLoginNewPW = "";
$MemberLoginNewPW2 = "";


if ($TeacherImageFileName==""){
	$StrTeacherImageFileName = "images/logo_mangoi.png";
}else{
	$StrTeacherImageFileName = "../uploads/teacher_images/".$TeacherImageFileName;
}



$HideTeacherGroupID = 0;
$AddWhere_TeacherGroup = "";

if ($_LINK_ADMIN_LEVEL_ID_>4){
	$HideTeacherGroupID = 1;
	$AddWhere_TeacherGroup = " and B.FranchiseID=".$_LINK_ADMIN_FRANCHISE_ID_." ";
}else if ($_LINK_ADMIN_LEVEL_ID_>1){
	$FranchiseID = $_LINK_ADMIN_FRANCHISE_ID_;
	$HideTeacherGroupID = 0;
	$AddWhere_TeacherGroup = " and B.FranchiseID=".$_LINK_ADMIN_FRANCHISE_ID_." ";
}
?>


<div id="page_content">
	<div id="page_content_inner">

		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="TeacherID" value="<?=$TeacherID?>">
		<input type="hidden" name="ListParam" value="<?=$ListParam?>">

		<input type="hidden" name="MemberID" value="<?=$MemberID?>">
		<input type="hidden" name="CheckedID" value="<?=$CheckedID?>">
		<input type="hidden" name="CheckedEmail" value="<?=$CheckedEmail?>">
		<input type="hidden" name="MemberLoginPW" value="<?=$MemberLoginPW?>">
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
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$TeacherName?></span><span class="sub-heading" id="user_edit_position"><?=$강사정보[$LangID]?></span></h2>
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
						
						<ul id="user_edit_tabs" class="uk-tab" data-uk-tab="{connect:'#user_edit_tabs_content'}" style="display:<?if ($TeacherID==""){?>none<?}?>;">
							<li <?if ($PageTabID=="1"){?>class="uk-active"<?}?>><a href="#"><?=$강사정보[$LangID]?></a></li>
							<li <?if ($PageTabID=="4"){?>class="uk-active"<?}?>><a href="#"><?=$받은자료[$LangID]?></a></li>
							<li <?if ($PageTabID=="2"){?>class="uk-active"<?}?> style="display:none;"><a href="#"><?=$수업교재관리[$LangID]?></a></li>
							<li <?if ($PageTabID=="5"){?>class="uk-active"<?}?> style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>4){?>none<?}?>;"><a href="#"><?=$근무시간관리[$LangID]?></a></li>
							<li <?if ($PageTabID=="6"){?>class="uk-active"<?}?> style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>4){?>none<?}?>;"><a href="#"><?=$휴식시간관리_기간[$LangID]?></a></li>
							<li <?if ($PageTabID=="3"){?>class="uk-active"<?}?> style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>4){?>none<?}?>;" onclick="SelectPageTab(3);"><a href="#"><?=$휴일관리[$LangID]?></a></li>
						</ul>
						<ul id="user_edit_tabs_content" class="uk-switcher uk-margin">
							<li>
								<div class="uk-margin-top">
									<h3 class="full_width_in_card heading_c">
										<?=$강사명[$LangID]?> / <?=$닉네임[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>

										<div class="uk-width-medium-3-10">
											<label for="TeacherName"><?=$강사명[$LangID]?></label>
											<input type="text" id="TeacherName" name="TeacherName" value="<?=$TeacherName?>" class="md-input label-fixed"/>
										</div>
										<div class="uk-width-medium-3-10">
											<label for="TeacherNickName"><?=$닉네임[$LangID]?></label>
											<input type="text" id="TeacherNickName" name="TeacherNickName" value="<?=$TeacherNickName?>" class="md-input label-fixed"/>
										</div>


										<div class="uk-width-medium-2-10" style="padding-top:7px;display:<?if ($HideTeacherGroupID==1){?>none<?}?>;">
											<select id="TeacherGroupID" name="TeacherGroupID" class="uk-width-1-1" data-md-select2 data-allow-clear="true" data-placeholder="<?=$강사그룹선택[$LangID]?>" style="width:100%;"/>
												<option value=""></option>
												<?
												$Sql2 = "select 
																A.*,
																B.EduCenterName,
																C.FranchiseName 
														from TeacherGroups A 
															inner join EduCenters B on A.EduCenterID=B.EduCenterID 
															inner join Franchises C on B.FranchiseID=C.FranchiseID 
														where A.TeacherGroupState<>0 and B.EduCenterState<>0 and C.FranchiseState<>0 ".$AddWhere_TeacherGroup."
														order by A.TeacherGroupState asc, C.FranchiseName asc, B.EduCenterName asc, A.TeacherGroupName asc";
												
												$Stmt2 = $DbConn->prepare($Sql2);
												$Stmt2->execute();
												$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
												
												$OldSelectTeacherGroupState = -1;
												while($Row2 = $Stmt2->fetch()) {
													$SelectTeacherGroupID = $Row2["TeacherGroupID"];
													$SelectTeacherGroupName = $Row2["TeacherGroupName"];
													$SelectTeacherGroupState = $Row2["TeacherGroupState"];
													$SelectEduCenterName = $Row2["EduCenterName"];
													$SelectFranchiseName = $Row2["FranchiseName"];
													if ($_LINK_ADMIN_LEVEL_ID_ <=2){
														$StrSelectFranchiseName = " (".$SelectFranchiseName.")";
													}else{
														$StrSelectFranchiseName = "";
													}
												
													if ($OldSelectTeacherGroupState!=$SelectTeacherGroupState){
														if ($OldSelectTeacherGroupState!=-1){
															echo "</optgroup>";
														}
														
														if ($SelectTeacherGroupState==1){
															echo "<optgroup label=\"강사그룹(운영중)\">";
														}else if ($SelectTeacherGroupState==2){
															echo "<optgroup label=\"강사그룹(미운영)\">";
														}
													}
													$OldSelectTeacherGroupState = $SelectTeacherGroupState;
												?>

												<option value="<?=$SelectTeacherGroupID?>" <?if ($TeacherGroupID==$SelectTeacherGroupID){?>selected<?}?>><?=$SelectTeacherGroupName?><?=$StrSelectFranchiseName?></option>
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
												$Stmt3->execute();
												$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
												while($Row3 = $Stmt3->fetch()) {
											?>

											<option value="<?=$Row3['MemberTimeZoneID']?>" 
												<?if ($MemberTimeZoneID==$Row3['MemberTimeZoneID']){?>selected<?}?>>
												<?
												$MemberTimeZoneName = $Row3['MemberTimeZoneName'];
												switch($MemberTimeZoneName){
													case "<?=$한국[$LangID]?>" : $MemberTimeZoneName=$한국[$LangID];
																  break;
													case "<?=$필리핀[$LangID]?>" : $MemberTimeZoneName=$필리핀[$LangID];
																  break;													
												}
												?>
												<?=$MemberTimeZoneName?>
													
												</option>
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
										<?=$아이디[$LangID]?> / <?=$비밀번호[$LangID]?> <?if ($MemberID!="") {?>(<?=$비밀번호_변경을_원할때_입력하세요[$LangID]?>)<?}?>
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
											<label for="TeacherPhone1"><?=$전화번호[$LangID]?></label>
											<div class="md-input label-fixed" style="padding-top:10px;margin-bottom:10px;">
												<select name="TeacherPhone1_1" class="Select" style="width:15%;height:30px;">
													<option value="010" <?if ($TeacherPhone1_1=="010") {?>selected<?}?>>010</option>
													<option value="011" <?if ($TeacherPhone1_1=="011") {?>selected<?}?>>011</option>
													<option value="016" <?if ($TeacherPhone1_1=="016") {?>selected<?}?>>016</option>
													<option value="017" <?if ($TeacherPhone1_1=="017") {?>selected<?}?>>017</option>
													<option value="018" <?if ($TeacherPhone1_1=="018") {?>selected<?}?>>018</option>
													<option value="019" <?if ($TeacherPhone1_1=="019") {?>selected<?}?>>019</option>
													<option value="070" <?if ($TeacherPhone1_1=="070") {?>selected<?}?>>070</option>
													<option value="02"  <?if ($TeacherPhone1_1=="02")  {?>selected<?}?>>02</option>
													<option value="031" <?if ($TeacherPhone1_1=="031") {?>selected<?}?>>031</option>
													<option value="032" <?if ($TeacherPhone1_1=="032") {?>selected<?}?>>032</option>
													<option value="033" <?if ($TeacherPhone1_1=="033") {?>selected<?}?>>033</option>
													<option value="041" <?if ($TeacherPhone1_1=="041") {?>selected<?}?>>041</option>
													<option value="042" <?if ($TeacherPhone1_1=="042") {?>selected<?}?>>042</option>
													<option value="043" <?if ($TeacherPhone1_1=="043") {?>selected<?}?>>043</option>
													<option value="044" <?if ($TeacherPhone1_1=="044") {?>selected<?}?>>044</option>
													<option value="049" <?if ($TeacherPhone1_1=="049") {?>selected<?}?>>049</option>
													<option value="051" <?if ($TeacherPhone1_1=="051") {?>selected<?}?>>051</option>
													<option value="052" <?if ($TeacherPhone1_1=="052") {?>selected<?}?>>052</option>
													<option value="053" <?if ($TeacherPhone1_1=="053") {?>selected<?}?>>053</option>
													<option value="054" <?if ($TeacherPhone1_1=="054") {?>selected<?}?>>054</option>
													<option value="055" <?if ($TeacherPhone1_1=="055") {?>selected<?}?>>055</option>
													<option value="061" <?if ($TeacherPhone1_1=="061") {?>selected<?}?>>061</option>
													<option value="062" <?if ($TeacherPhone1_1=="062") {?>selected<?}?>>062</option>
													<option value="063" <?if ($TeacherPhone1_1=="063") {?>selected<?}?>>063</option>
													<option value="064" <?if ($TeacherPhone1_1=="064") {?>selected<?}?>>064</option>
													<option value="0505" <?if ($CenterPhone1_1=="0505") {?>selected<?}?>>0505</option>
													<option value="0502" <?if ($CenterPhone1_1=="0502") {?>selected<?}?>>0502</option>
												</select>
												<input type="text" name="TeacherPhone1_2" style="width:15%;height:30px;padding-left:10px;" placeholder="" value="<?=$TeacherPhone1_2?>" class="allownumericwithoutdecimal" maxlength="4"> 
												<input type="text" name="TeacherPhone1_3" style="width:15%;height:30px;padding-left:10px;"  placeholder="" value="<?=$TeacherPhone1_3?>" class="allownumericwithoutdecimal" maxlength="4">
											</div>
										</div>
										<div class="uk-width-medium-1-2 uk-input-group">
											<label for="CenterPhone2"><?=$휴대폰[$LangID]?></label>
											<div class="md-input label-fixed" style="padding-top:10px;margin-bottom:10px;">
												<select name="TeacherPhone2_1" class="Select" style="width:15%;height:30px;">
													<option value="010" <?if ($TeacherPhone2_1=="010") {?>selected<?}?>>010</option>
													<option value="011" <?if ($TeacherPhone2_1=="011") {?>selected<?}?>>011</option>
													<option value="016" <?if ($TeacherPhone2_1=="016") {?>selected<?}?>>016</option>
													<option value="017" <?if ($TeacherPhone2_1=="017") {?>selected<?}?>>017</option>
													<option value="018" <?if ($TeacherPhone2_1=="018") {?>selected<?}?>>018</option>
													<option value="019" <?if ($TeacherPhone2_1=="019") {?>selected<?}?>>019</option>
												</select>
												<input type="text" name="TeacherPhone2_2" style="width:15%;height:30px;padding-left:10px;" placeholder="" value="<?=$TeacherPhone2_2?>" class="allownumericwithoutdecimal" maxlength="4"> 
												<input type="text" name="TeacherPhone2_3" style="width:15%;height:30px;padding-left:10px;"  placeholder="" value="<?=$TeacherPhone2_3?>" class="allownumericwithoutdecimal" maxlength="4">
											</div>
										</div>
									</div>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-1-2 uk-input-group">
											<label for="CenterPhone3"><?=$팩스번호[$LangID]?></label>
											<div class="md-input label-fixed" style="padding-top:10px;margin-bottom:10px;">
												<select name="TeacherPhone3_1" class="Select" style="width:15%;height:30px;">
													<option value="02"  <?if ($TeacherPhone3_1=="02")  {?>selected<?}?>>02</option>
													<option value="031" <?if ($TeacherPhone3_1=="031") {?>selected<?}?>>031</option>
													<option value="032" <?if ($TeacherPhone3_1=="032") {?>selected<?}?>>032</option>
													<option value="033" <?if ($TeacherPhone3_1=="033") {?>selected<?}?>>033</option>
													<option value="041" <?if ($TeacherPhone3_1=="041") {?>selected<?}?>>041</option>
													<option value="042" <?if ($TeacherPhone3_1=="042") {?>selected<?}?>>042</option>
													<option value="043" <?if ($TeacherPhone3_1=="043") {?>selected<?}?>>043</option>
													<option value="044" <?if ($TeacherPhone3_1=="044") {?>selected<?}?>>044</option>
													<option value="049" <?if ($TeacherPhone3_1=="049") {?>selected<?}?>>049</option>
													<option value="051" <?if ($TeacherPhone3_1=="051") {?>selected<?}?>>051</option>
													<option value="052" <?if ($TeacherPhone3_1=="052") {?>selected<?}?>>052</option>
													<option value="053" <?if ($TeacherPhone3_1=="053") {?>selected<?}?>>053</option>
													<option value="054" <?if ($TeacherPhone3_1=="054") {?>selected<?}?>>054</option>
													<option value="055" <?if ($TeacherPhone3_1=="055") {?>selected<?}?>>055</option>
													<option value="061" <?if ($TeacherPhone3_1=="061") {?>selected<?}?>>061</option>
													<option value="062" <?if ($TeacherPhone3_1=="062") {?>selected<?}?>>062</option>
													<option value="063" <?if ($TeacherPhone3_1=="063") {?>selected<?}?>>063</option>
													<option value="064" <?if ($TeacherPhone3_1=="064") {?>selected<?}?>>064</option>
													<option value="010" <?if ($TeacherPhone3_1=="010") {?>selected<?}?>>010</option>
													<option value="011" <?if ($TeacherPhone3_1=="011") {?>selected<?}?>>011</option>
													<option value="016" <?if ($TeacherPhone3_1=="016") {?>selected<?}?>>016</option>
													<option value="017" <?if ($TeacherPhone3_1=="017") {?>selected<?}?>>017</option>
													<option value="018" <?if ($TeacherPhone3_1=="018") {?>selected<?}?>>018</option>
													<option value="019" <?if ($TeacherPhone3_1=="019") {?>selected<?}?>>019</option>
													<option value="070" <?if ($TeacherPhone3_1=="070") {?>selected<?}?>>070</option>
													<option value="0505" <?if ($CenterPhone1_1=="0505") {?>selected<?}?>>0505</option>
													<option value="0502" <?if ($CenterPhone1_1=="0502") {?>selected<?}?>>0502</option>
												</select>
												<input type="text" name="TeacherPhone3_2" style="width:15%;height:30px;padding-left:10px;" placeholder="" value="<?=$TeacherPhone3_2?>" class="allownumericwithoutdecimal" maxlength="4"> 
												<input type="text" name="TeacherPhone3_3" style="width:15%;height:30px;padding-left:10px;"  placeholder="" value="<?=$TeacherPhone3_3?>" class="allownumericwithoutdecimal" maxlength="4">
											</div>
										</div>
										<div class="uk-width-medium-1-2 uk-input-group">
											<label for="TeacherEmail"><?=$이메일[$LangID]?></label>
											<div class="md-input label-fixed" style="padding-top:10px;margin-bottom:10px;">
												<input type="text" name="TeacherEmail_1" id="TeacherEmail_1" style="width:20%;height:30px;padding-left:10px;" value="<?=$TeacherEmail_1?>" onkeyup="EnNewEmail()"> 
												<div style="display:inline-block;width:20px;margin-top:8px;">@</div> 
												<input type="text" name="TeacherEmail_2" id="TeacherEmail_2" style="width:20%;height:30px;padding-left:10px;" value="<?=$TeacherEmail_2?>" onkeyup="EnNewEmail()">
												<select name="TeacherEmail_3" class="Select" style="width:22%;height:30px;margin-bottom:0px;" onchange="SetEmailName()">
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
												<span class="uk-input-group-addon" id="BtnCheckEmail" style="display:<?if ($MemberID!="") {?>none<?}else{?>inline<?}?>;"><a class="md-btn" href="javascript:CheckEmail();"><?=$중복확인[$LangID]?></a></span>
											</div>
										</div>
									</div>
									<h3 class="full_width_in_card heading_c">
										<?=$주소[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-3-10 uk-input-group">
											<label for="TeacherZip"><?=$우편번호[$LangID]?></label>
											<input type="text" id="TeacherZip" name="TeacherZip" value="<?=$TeacherZip?>" class="md-input label-fixed" />
											<span class="uk-input-group-addon"><a class="md-btn" href="javascript:ExecDaumPostcode();"><?=$검색[$LangID]?></a></span>

										</div>
										<div class="uk-width-medium-3-10 uk-input-group">
											<label for="TeacherAddr1"><?=$주소[$LangID]?></label>
											<input type="text" id="TeacherAddr1" name="TeacherAddr1" value="<?=$TeacherAddr1?>" class="md-input label-fixed" />
										</div>
										<div class="uk-width-medium-4-10 uk-input-group">
											<label for="TeacherAddr2"><?=$상세주소[$LangID]?></label>
											<input type="text" id="TeacherAddr2" name="TeacherAddr2" value="<?=$TeacherAddr2?>" class="md-input label-fixed" />
										</div>
									</div>
									<h3 class="full_width_in_card heading_c">
										<?=$강사료[$LangID]?> / <?=$출신지역[$LangID]?>(<?=$수강료기준[$LangID]?>)
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-2-10 uk-input-group">
											<label for="TeacherPayPerTime"><?=$강사수수료_10분[$LangID]?></label>
											<input type="text" id="TeacherPayPerTime" name="TeacherPayPerTime" value="<?=$TeacherPayPerTime?>" class="md-input label-fixed allownumericwithoutdecimal" />
										</div>
										<div class="uk-width-medium-2-10 uk-input-group">
											※ 강사료 수수료는<br/><?=$단위시간당_강사_수수료_입니다[$LangID]?>
										</div>
										<div class="uk-width-medium-3-10 uk-input-group">
											<select name="TeacherPayTypeItemID" class="uk-width-1-1" data-md-select2 data-allow-clear="true" style="width:100%;">
											<option value=""><?=$출신지역[$LangID]?></option>
											<?php

												//  멤버와 타임존을 조인하여 값을 가져올 것. id 가 매칭이 된다면 default 로 지정
												$Sql3 = "select T.* from TeacherPayTypeItems T order by T.TeacherPayTypeItemTitle asc";
											
												$Stmt3 = $DbConn->prepare($Sql3);
												$Stmt3->execute();
												$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
												while($Row3 = $Stmt3->fetch()) {
											?>
											<option value="<?=$Row3['TeacherPayTypeItemID']?>" 
												<?if ($TeacherPayTypeItemID==$Row3['TeacherPayTypeItemID']){?>selected<?}?>>
												<?
												$TeacherPayTypeItemTitle = $Row3['TeacherPayTypeItemTitle'];
												switch($TeacherPayTypeItemTitle){
													case "미국/캐나다 강사" : $TeacherPayTypeItemTitle=$미국_캐나다강사[$LangID];
																  break;
													case "필리핀 강사" : $TeacherPayTypeItemTitle=$필리핀강사[$LangID];
																  break;													
												}
												?>
												<?=$TeacherPayTypeItemTitle?>
													
											</option>
											<?php
												}
											?>
											</select>
										</div>
										<div class="uk-width-medium-3-10 uk-input-group">
											※ <?=$수강료기준[$LangID]?><br/><?=$출신지역에_따라_대리점_및_B2C회원_수강료가_결정됩니다[$LangID]?>
										</div>



									</div>

									<h3 class="full_width_in_card heading_c">
										<?=$강사사진[$LangID]?> / <?=$개인정보[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-5-10 uk-input-group">
											<input type="file" name="UpFile" id="UpFile" class="dropify" data-default-file="<?=$StrTeacherImageFileName?>"/>
										</div>
										
										<div class="uk-width-medium-3-10 uk-input-group">
											<label for="MemberBirthday"><?=$생년월일[$LangID]?></label>
											<input type="text" id="MemberBirthday" name="MemberBirthday" value="<?=$MemberBirthday?>" class="md-input label-fixed" data-uk-datepicker="{format:'YYYY-MM-DD', weekstart:0}">
											<span class="uk-input-group-addon"><i class="uk-input-group-icon uk-icon-calendar"></i></span>
										</div>
										<div class="uk-width-medium-2-10 uk-input-group" style="padding-top:10px;">
											<span class="icheck-inline">
												<input type="radio" id="MemberSex1" name="MemberSex" <?php if ($MemberSex==1) { echo "checked";}?> value="1" data-md-icheck/>
												<label for="MemberSex1" class="inline-label"><?=$남자[$LangID]?></label>
											</span>
											<span class="icheck-inline">
												<input type="radio" id="MemberSex2" name="MemberSex" <?php if ($MemberSex==2) { echo "checked";}?> value="2" data-md-icheck/>
												<label for="MemberSex2" class="inline-label"><?=$여자[$LangID]?></label>
											</span>
										</div>
										<div class="uk-width-medium-2-10 uk-input-group">
											<label for="TeacherIntroEdu"><?=$소속기관[$LangID]?></label>
											<input type="text" id="TeacherIntroEdu" name="TeacherIntroEdu" value="<?=$TeacherIntroEdu?>" class="md-input label-fixed" />
										</div>
										<div class="uk-width-medium-4-10 uk-form-row">
											<span class="icheck-inline">
												<input type="radio" id="TeacherIsManager2" name="TeacherIsManager" value="2" <?php if ($TeacherIsManager==2) { echo "checked";}?> data-md-icheck/>
												<label for="TeacherIsManager2" class="inline-label"><?=$매니저_강사[$LangID]?></label>
											</span>
											<span class="icheck-inline">
												<input type="radio" id="TeacherIsManager1" name="TeacherIsManager" value="1" <?php if ($TeacherIsManager==1) { echo "checked";}?> data-md-icheck/>
												<label for="TeacherIsManager1" class="inline-label"><?=$일반_강사[$LangID]?></label>
											</span>
										</div>
										<!--
										<div class="uk-width-medium-3-10 uk-form-row">
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
										</div>
										<div class="uk-width-medium-3-10 uk-input-group">
											※ 사용언어<br/><?=$LMS_사용언어_입니다[$LangID]?>
										</div>
										-->
									</div>

									<h3 class="full_width_in_card heading_c">
										<?=$소개영상[$LangID]?> / <?=$소개글[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-2-10 uk-form-row">
											<span class="icheck-inline">
												<input type="radio" id="TeacherVideoType1" name="TeacherVideoType" value="1" <?php if ($TeacherVideoType==1) { echo "checked";}?> data-md-icheck/>
												<label for="TeacherVideoType1" class="inline-label">Youtube</label>
											</span>
											<span class="icheck-inline">
												<input type="radio" id="TeacherVideoType2" name="TeacherVideoType" value="2" <?php if ($TeacherVideoType==2) { echo "checked";}?> data-md-icheck/>
												<label for="TeacherVideoType2" class="inline-label">Vimeo</label>
											</span>
										</div>
										<div class="uk-width-medium-3-10 uk-input-group">
											<label for="TeacherVideoCode"><?=$영상코드[$LangID]?></label>
											<input type="text" id="TeacherVideoCode" name="TeacherVideoCode" value="<?=$TeacherVideoCode?>" class="md-input label-fixed" />
											<span class="uk-input-group-addon"><a class="md-btn" href="javascript:OpenVideoPlayer();"><?=$영상확인[$LangID]?></a></span>
										</div>
										<div class="uk-width-medium-5-10 uk-input-group">
											※ Youtube 또는 Vimeo 코드를 입력하세요.(아래 예제의 <span style='color:#ff0000;'>빨간색</span> 코드 부분)<br>
											※ 예) https://www.youtube.com/watch?v=<span style='color:#ff0000;'>LDPt7XLrbks</span> , https://vimeo.com/<span style='color:#ff0000;'>159328419</span> 
										</div>
									</div>
									
									
									<hr style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>4) {?>none<?}?>;">
									<div class="uk-grid" data-uk-grid-margin  style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>4) {?>none<?}?>;">
										<div class="uk-width-medium-1-1 uk-input-group">
										
										<span id="TeacherCharacterItemID_All" class="inline-label" style="display:inline-block;padding:5px;background-color:#1D76CE;color:#ffffff;margin-right:20px;" onclick="TeacherCharacterItemIDAll()"><?=$전체선택[$LangID]?></span> 
										
										<?php

											$Sql3 = "select 
															A.*,
															ifnull(B.TeacherID,0) as TeacherID  
													 from TeacherCharacterItems A 
														left outer join TeacherCharacters B on A.TeacherCharacterItemID=B.TeacherCharacterItemID and B.TeacherID=$TempTeacherID 
													 where A.TeacherCharacterItemState=1
													 order by A.TeacherCharacterItemOrder asc ";
										
											$Stmt3 = $DbConn->prepare($Sql3);
											$Stmt3->execute();
											$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
											
											$TeacherCharacterItemIDs = "|";
											while($Row3 = $Stmt3->fetch()) {
										?>
										<input type="checkbox" name="TeacherCharacterItemID_<?=$Row3['TeacherCharacterItemID']?>" id="TeacherCharacterItemID_<?=$Row3['TeacherCharacterItemID']?>" value="1" <?if ($Row3["TeacherID"]!=0){?>checked<?}?>/>
										<label for="TeacherCharacterItemID_<?=$Row3['TeacherCharacterItemID']?>" class="inline-label" style="margin-right:20px;"><?=$Row3['TeacherCharacterItemTitle']?></label> 
										<?php
												$TeacherCharacterItemIDs = $TeacherCharacterItemIDs . $Row3['TeacherCharacterItemID'] ."|";
											}
										?>
										</div>

										<script>
										var TeacherCharacterItemCheckState = 0;
										function TeacherCharacterItemIDAll(){
											TeacherCharacterItemIDs = "<?=$TeacherCharacterItemIDs?>";
											if (TeacherCharacterItemIDs!="|"){
												ArrTeacherCharacterItemID = TeacherCharacterItemIDs.split("|");
												for(ii=1;ii<=ArrTeacherCharacterItemID.length-1;ii++){
													if (TeacherCharacterItemCheckState==1){
														$("input:checkbox[id='TeacherCharacterItemID_"+ArrTeacherCharacterItemID[ii]+"']").prop("checked", false);
													}else{
														$("input:checkbox[id='TeacherCharacterItemID_"+ArrTeacherCharacterItemID[ii]+"']").prop("checked", true);
													}
												}
												if (TeacherCharacterItemCheckState==1){
													TeacherCharacterItemCheckState=0;
													document.getElementById("TeacherCharacterItemID_All").innerHTML = "<?=$전체선택[$LangID]?>";
												}else{
													TeacherCharacterItemCheckState=1;
													document.getElementById("TeacherCharacterItemID_All").innerHTML = "<?=$전체해제[$LangID]?>";
												}
											}
										}
										</script>
									</div>


									<hr>
								    <div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-1-1">
											<label for="TeacherIntroSpec"><?=$강사경력소개[$LangID]?> <span style="color:red; "><?=$_소개란은_한줄_출력_됩니다[$LangID]?></span></label>
											<textarea class="md-input" name="TeacherIntroSpec" id="TeacherIntroSpec" cols="30" rows="4"><?=$TeacherIntroSpec?></textarea>
										</div>
									</div>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-1-1">
											<label for="TeacherIntroText"><?=$강사소개[$LangID]?></label>
											<textarea class="md-input" name="TeacherIntroText" id="TeacherIntroText" cols="30" rows="4"><?=$TeacherIntroText?></textarea>
										</div>
									</div>
								</div>

							</li>
							<?if ($TeacherID!=""){?>
							<li>

								<table class="uk-table uk-table-align-vertical">
									<thead>
										<tr>
											<th style="width:10%" nowrap>No</th>
											<th nowrap><?=$자료다운로드[$LangID]?> </th>
											<th style="width:15%" nowrap><?=$보낸시간[$LangID]?></th>
											<th style="width:15%" nowrap><?=$받은시간[$LangID]?></th>
											<th style="width:10%" nowrap><?=$보낸사람[$LangID]?></th>
											<th style="width:10%" nowrap><?=$상태[$LangID]?></th>
										</tr>
									</thead>
									<tbody>
										
										<?php


										$Sql = "select 
														count(*) TotalRowCount 
												from TeacherDatas A 
												where A.ReceiveMemberID=".$MemberID."";
										$Stmt = $DbConn->prepare($Sql);
										$Stmt->execute();
										$Stmt->setFetchMode(PDO::FETCH_ASSOC);
										$Row = $Stmt->fetch();
										$Stmt = null;
										$TotalRowCount = $Row["TotalRowCount"];


										$Sql = "
												select 
													A.*
												from TeacherDatas A 

												where A.ReceiveMemberID=".$MemberID."
												order by A.TeacherDataRegDateTime desc";// limit $StartRowNum, $PageListNum";
										$Stmt = $DbConn->prepare($Sql);
										$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
										$Stmt->execute();
										$Stmt->setFetchMode(PDO::FETCH_ASSOC);

										$ListCount = 1;
										while($Row = $Stmt->fetch()) {
											$ListNumber = $TotalRowCount - $ListCount + 1;

											$TeacherDataID = $Row["TeacherDataID"];
											$SendMemberID = $Row["SendMemberID"];
											$SendMemberName = $Row["SendMemberName"];
											$ReceiveMemberID = $Row["ReceiveMemberID"];
											$ReceiveMemberName = $Row["ReceiveMemberName"];
											$TeacherDataTitle = $Row["TeacherDataTitle"];
											$TeacherDataRegDateTime = $Row["TeacherDataRegDateTime"];
											$TeacherDataReceiveDateTime = $Row["TeacherDataReceiveDateTime"];
											$TeacherDataState = $Row["TeacherDataState"];
											
											
											if ($TeacherDataState==1){
												$StrTeacherDataState = "<?=$확인전[$LangID]?>";
											}else if ($TeacherDataState==2){
												$StrTeacherDataState = "<?=$확인완료[$LangID]?>";
											}

								
										?>
										<tr>
											<td class="uk-text-nowrap uk-table-td-center"><?=$ListNumber?></td>
											<td class="uk-text-nowrap"><a href="javascript:DownTeacherData('<?=$TeacherDataID?>')"><i class="material-icons">save_alt</i><?=$TeacherDataTitle?></a></td>
											<td class="uk-text-nowrap uk-table-td-center"><?=$TeacherDataRegDateTime?></td>
											<td class="uk-text-nowrap uk-table-td-center"><?=$TeacherDataReceiveDateTime?></td>
											<td class="uk-text-nowrap uk-table-td-center"><?=$SendMemberName?></td>
											<td class="uk-text-nowrap uk-table-td-center"><?=$StrTeacherDataState?></td>
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
								<h3 class="full_width_in_card heading_c">
									<?=$교재관리[$LangID]?>
								</h3>
								<!--<div class="uk-width-medium-1-1 uk-input-group">-->
								<div>
								<?
								$Sql2 = "select 
											A.*,
											B.BookGroupID,
											B.BookGroupName,
											ifnull(C.TeacherID,0) as TeacherID 
										from Books A inner join BookGroups B on A.BookGroupID=B.BookGroupID 
											left outer join TeacherUseBooks C on A.BookID=C.BookID and C.TeacherID=$TeacherID 
										where A.BookState<>0 and B.BookGroupState<>0 
										order by B.BookGroupOrder asc, A.BookOrder asc
										";
								
								
								$Stmt2 = $DbConn->prepare($Sql2);
								$Stmt2->execute();
								$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
								
								$OldBookGroupID = -1;
								while($Row2 = $Stmt2->fetch()) {
								
									$BookID = $Row2["BookID"];
									$BookName = $Row2["BookName"];
									$BookGroupID = $Row2["BookGroupID"];
									$BookGroupName  = $Row2["BookGroupName"];


								?>
	
									<?if ($OldBookGroupID!=$BookGroupID){?>
									<div style="padding:10px 0px 10px 0px;border-bottom:1px dotted #888888;margin-bottom:20px;margin-top:30px;font-weight:bold;"><?=$BookGroupName?></div>
									<?}?>
									
										<input type="checkbox" class="check_input" name="BookID_<?=$BookID?>" id="BookID_<?=$BookID?>" value="1" <?if ($Row2["TeacherID"]!=0){?>checked<?}?> onclick="SetTeacherUseBook(<?=$BookID?>)">
										<label for="BookID_<?=$BookID?>" class="check_label" style="margin-right:20px;"><span class="check_bullet"></span><?=$BookName?></label> 

								<?
									$OldBookGroupID = $BookGroupID;
								}
								?>
								</div>


							</li>
							<li style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>4){?>none<?}?>;">
								<h3 class="full_width_in_card heading_c">
									<?=$근무시간관리[$LangID]?>
								</h3>
								<div class="uk-grid" data-uk-grid-margin style="padding-left:50px;">
									<span class="uk-form-help-block" style="font-style:normal;">시작</span>
									<div class="uk-width-medium-1-10 uk-input-group" style="width:12%;">
										<select name="TeacherStartHour" onchange="ChTeacherHour()" data-md-selectize>
											<?for ($ii=0;$ii<=24;$ii++){?>
											<option value="<?=$ii?>" <?if ($TeacherStartHour==$ii) {?>selected<?}?>><?=substr("0".$ii,-2)?><?=$시[$LangID]?></option>	
											<?}?>
										</select> 
									</div>
									<div class="uk-width-medium-1-10 uk-input-group">
									
									</div>
									<span class="uk-form-help-block" style="font-style:normal;"><?=$종료[$LangID]?></span>
									<div class="uk-width-medium-1-10 uk-input-group" style="width:12%;">
										<select name="TeacherEndHour" onchange="ChTeacherHour()" data-md-selectize>
											<?for ($ii=0;$ii<=24;$ii++){?>
											<option value="<?=$ii?>" <?if ($TeacherEndHour==$ii) {?>selected<?}?>><?=substr("0".$ii,-2)?><?=$시[$LangID]?></option>	
											<?}?>
										</select> 
									</div>
									<div class="uk-width-medium-6-10 uk-input-group" id="DivTeacherHourMsg">
										※ 근무시간을 수정 하면 자동 적용 됩니다.
									</div>
								</div>

								<script>
								function ChTeacherHour(){
									url = "ajax_set_teahcer_hour.php";
									TeacherStartHour = document.RegForm.TeacherStartHour.value;
									TeacherEndHour = document.RegForm.TeacherEndHour.value;
									//location.href = url + "?NewID="+NewID;
									$.ajax(url, {
										data: {
											TeacherStartHour: TeacherStartHour,
											TeacherEndHour: TeacherEndHour,
											TeacherID: <?=$TeacherID?>
										},
										success: function (data) {
											json_data = data;
											location.href = "teacher_form.php?ListParam=<?=$ListParam?>&TeacherID=<?=$TeacherID?>&PageTabID=5";
										},
										error: function () {

										}
									});
								}
								</script>

								<h3 class="full_width_in_card heading_c">
									<?=$휴식시간관리_고정[$LangID]?>
								</h3>

								<table class="uk-table uk-table-align-vertical">
									<thead>
										<tr>
											<th style="width:20%" nowrap><?=$시[$LangID]?></th>
											<th style="width:20%" nowrap><?=$분[$LangID]?></th>
											<?if ($EduCenterHoliday[0]==0) {?>
											<th nowrap><?=$일[$LangID]?></th>
											<?}?>
											<?if ($EduCenterHoliday[1]==0) {?>
											<th nowrap><?=$월[$LangID]?></th>
											<?}?>
											<?if ($EduCenterHoliday[2]==0) {?>
											<th nowrap><?=$화[$LangID]?></th>
											<?}?>
											<?if ($EduCenterHoliday[3]==0) {?>
											<th nowrap><?=$수[$LangID]?></th>
											<?}?>
											<?if ($EduCenterHoliday[4]==0) {?>
											<th nowrap><?=$목[$LangID]?></th>
											<?}?>
											<?if ($EduCenterHoliday[5]==0) {?>
											<th nowrap><?=$금[$LangID]?></th>
											<?}?>
											<?if ($EduCenterHoliday[6]==0) {?>
											<th nowrap><?=$토[$LangID]?></th>
											<?}?>
										</tr>
									</thead>
									<tbody>
										<?
										for ($ii=$TeacherStartHour;$ii<=$TeacherEndHour-1;$ii++){
											for ($jj=0;$jj<=50;$jj=$jj+10){
												for ($kk=0;$kk<=6;$kk++){
													if ($EduCenterHoliday[$kk]==0) {
														$TeacherBreak[$kk][$ii][$jj] = 1;
													}
												}
											}
										}

										$Sql2 = "select 
														A.* 
												from TeacherBreakTimes A 
												where A.TeacherID=$TeacherID and A.TeacherBreakTimeState=1 
												order by A.TeacherBreakTimeWeek asc, A.TeacherBreakTimeHour asc, A.TeacherBreakTimeMinute asc";
										$Stmt2 = $DbConn->prepare($Sql2);
										$Stmt2->execute();
										$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
									
										while($Row2 = $Stmt2->fetch()) {
											$TeacherBreakTimeWeek = $Row2["TeacherBreakTimeWeek"];
											$TeacherBreakTimeHour = $Row2["TeacherBreakTimeHour"];
											$TeacherBreakTimeMinute = $Row2["TeacherBreakTimeMinute"];
											$TeacherBreakTimeType = $Row2["TeacherBreakTimeType"];
											
											$TeacherBreak[$TeacherBreakTimeWeek][$TeacherBreakTimeHour][$TeacherBreakTimeMinute] = $TeacherBreakTimeType;
										}


										for ($ii=$TeacherStartHour;$ii<=$TeacherEndHour-1;$ii++){
										?>
										<tr>
											<td class="uk-text-nowrap uk-table-td-center" rowspan="6"><?=$ii?></td>
										<?
											for ($jj=0;$jj<=50;$jj=$jj+10){
												if ($jj>0){
										?>
										<tr>
										<?
												}
										?>
											<td class="uk-text-nowrap uk-table-td-center"><?=$jj?></td>
												<?
												for ($kk=0;$kk<=6;$kk++){
													if ($EduCenterHoliday[$kk]==0) {

														$BgColor = "#FBFBFB";
														if ($TeacherBreak[$kk][$ii][$jj]==1) {
															$BgColor = "#FBFBFB";
														}else if ($TeacherBreak[$kk][$ii][$jj]==2) {
															$BgColor = "#FFCC00";
														}else if ($TeacherBreak[$kk][$ii][$jj]==3) {
															$BgColor = "#CC9933";
														}else if ($TeacherBreak[$kk][$ii][$jj]==4) {
															$BgColor = "#CC6666";
														}
												?>
												<td style="padding-bottom:0px;text-align:center;background-color:<?=$BgColor?>;" id="DivTeacherBreak_<?=$kk?>_<?=$ii?>_<?=$jj?>">
													
														<input type="radio" id="TeacherBreak_<?=$kk?>_<?=$ii?>_<?=$jj?>_1" name="TeacherBreak_<?=$kk?>_<?=$ii?>_<?=$jj?>" <?php if ($TeacherBreak[$kk][$ii][$jj]==1) { echo "checked";}?> onclick="CheckTeacherBreak(<?=$kk?>,<?=$ii?>,<?=$jj?>,1);" value="1"/>
														<label for="TeacherBreak_<?=$kk?>_<?=$ii?>_<?=$jj?>_1" class="inline-label">수업</label>
														<input type="radio" id="TeacherBreak_<?=$kk?>_<?=$ii?>_<?=$jj?>_2" name="TeacherBreak_<?=$kk?>_<?=$ii?>_<?=$jj?>" <?php if ($TeacherBreak[$kk][$ii][$jj]==2) { echo "checked";}?> onclick="CheckTeacherBreak(<?=$kk?>,<?=$ii?>,<?=$jj?>,2);" value="2"/>
														<label for="TeacherBreak_<?=$kk?>_<?=$ii?>_<?=$jj?>_2" class="inline-label">식사</label>
														<br>
														<input type="radio" id="TeacherBreak_<?=$kk?>_<?=$ii?>_<?=$jj?>_3" name="TeacherBreak_<?=$kk?>_<?=$ii?>_<?=$jj?>" <?php if ($TeacherBreak[$kk][$ii][$jj]==3) { echo "checked";}?> onclick="CheckTeacherBreak(<?=$kk?>,<?=$ii?>,<?=$jj?>,3);" value="3"/>
														<label for="TeacherBreak_<?=$kk?>_<?=$ii?>_<?=$jj?>_3" class="inline-label">휴식</label>
														<input type="radio" id="TeacherBreak_<?=$kk?>_<?=$ii?>_<?=$jj?>_4" name="TeacherBreak_<?=$kk?>_<?=$ii?>_<?=$jj?>" <?php if ($TeacherBreak[$kk][$ii][$jj]==4) { echo "checked";}?> onclick="CheckTeacherBreak(<?=$kk?>,<?=$ii?>,<?=$jj?>,4);" value="4"/>
														<label for="TeacherBreak_<?=$kk?>_<?=$ii?>_<?=$jj?>_4" class="inline-label">블락</label>
													
												</td>
												<?
													}
												}
												?>
										</tr>
										<?
											}
										}
										?>

									</tbody>
								</table>
								<script>
								function CheckTeacherBreak(TeacherBreakTimeWeek,TeacherBreakTimeHour,TeacherBreakTimeMinute,TeacherBreakTimeType){
									url = "ajax_set_teacher_break_time.php";



									//location.href = url + "?NewID="+NewID;
									$.ajax(url, {
										data: {
											TeacherBreakTimeWeek: TeacherBreakTimeWeek,
											TeacherBreakTimeHour: TeacherBreakTimeHour,
											TeacherBreakTimeMinute: TeacherBreakTimeMinute,
											TeacherBreakTimeType: TeacherBreakTimeType,
											TeacherID: <?=$TeacherID?>
										},
										success: function (data) {
											json_data = data;

											BgColor = "#FBFBFB";
											if (TeacherBreakTimeType==1) {
												BgColor = "#FBFBFB";
											}else if (TeacherBreakTimeType==2) {
												BgColor = "#FFCC00";
											}else if (TeacherBreakTimeType==3) {
												BgColor = "#CC9933";
											}else if (TeacherBreakTimeType==4) {
												BgColor = "#CC6666";
											}
											document.getElementById("DivTeacherBreak_"+TeacherBreakTimeWeek+"_"+TeacherBreakTimeHour+"_"+TeacherBreakTimeMinute).style.backgroundColor = BgColor;
										},
										error: function () {

										}
									});
								}
								</script>
							</li>
							<li style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>4){?>none<?}?>;">
								<h3 class="full_width_in_card heading_c">
									<?=$휴식시간관리_기간[$LangID]?>
								</h3>
								<div style="padding:20px;">
									<table class="uk-table uk-table-align-vertical">
										<thead>
											<tr>
												<th style="width:10%" nowrap>No</th>
												<th nowrap>타입</th>
												<th style="width:15%" nowrap><?=$시작날짜[$LangID]?></th>
												<th style="width:15%" nowrap><?=$종료날짜[$LangID]?></th>
												<th style="width:10%" nowrap><?=$요일[$LangID]?></th>
												<th style="width:10%" nowrap><?=$시작시간[$LangID]?></th>
												<th style="width:10%" nowrap><?=$종료시간[$LangID]?></th>
											</tr>
										</thead>
										<tbody>
											
											<?php


											$Sql = "select 
															count(*) TotalRowCount 
													from TeacherBreakTimeTemps A 
													where A.TeacherID=".$TeacherID." and A.TeacherBreakTimeTempState=1 ";
											$Stmt = $DbConn->prepare($Sql);
											$Stmt->execute();
											$Stmt->setFetchMode(PDO::FETCH_ASSOC);
											$Row = $Stmt->fetch();
											$Stmt = null;
											$TotalRowCount = $Row["TotalRowCount"];


											$Sql = "
													select 
														A.*
													from TeacherBreakTimeTemps A 
													where A.TeacherID=".$TeacherID." and A.TeacherBreakTimeTempState=1 
													order by A.TeacherBreakTimeTempEndDate desc, A.TeacherBreakTimeTempStartDate desc ";// limit $StartRowNum, $PageListNum";
											$Stmt = $DbConn->prepare($Sql);
											$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
											$Stmt->execute();
											$Stmt->setFetchMode(PDO::FETCH_ASSOC);

											$ListCount = 1;
											while($Row = $Stmt->fetch()) {
												$ListNumber = $TotalRowCount - $ListCount + 1;

												$TeacherBreakTimeTempID = $Row["TeacherBreakTimeTempID"];
												$TeacherBreakTimeTempStartDate = $Row["TeacherBreakTimeTempStartDate"];
												$TeacherBreakTimeTempEndDate = $Row["TeacherBreakTimeTempEndDate"];
												$TeacherBreakTimeTempWeek = $Row["TeacherBreakTimeTempWeek"];
												$TeacherBreakTimeTempStartHour = $Row["TeacherBreakTimeTempStartHour"];
												$TeacherBreakTimeTempStartMinute = $Row["TeacherBreakTimeTempStartMinute"];
												$TeacherBreakTimeTempEndHour = $Row["TeacherBreakTimeTempEndHour"];
												$TeacherBreakTimeTempEndMinute = $Row["TeacherBreakTimeTempEndMinute"];
												$TeacherBreakTimeTempType = $Row["TeacherBreakTimeTempType"];

												
												
												if ($TeacherBreakTimeTempType==1){
													$StrTeacherBreakTimeTempType = "수업";//사용안함
												}else if ($TeacherBreakTimeTempType==2){
													$StrTeacherBreakTimeTempType = "식사";
												}else if ($TeacherBreakTimeTempType==3){
													$StrTeacherBreakTimeTempType = "휴식";
												}else if ($TeacherBreakTimeTempType==4){
													$StrTeacherBreakTimeTempType = "블락";
												}

												if ($TeacherBreakTimeTempWeek==0){
													$StrTeacherBreakTimeTempWeek = "일요일";
												}else if ($TeacherBreakTimeTempWeek==1){
													$StrTeacherBreakTimeTempWeek = "월요일";
												}else if ($TeacherBreakTimeTempWeek==2){
													$StrTeacherBreakTimeTempWeek = "화요일";
												}else if ($TeacherBreakTimeTempWeek==3){
													$StrTeacherBreakTimeTempWeek = "수요일";
												}else if ($TeacherBreakTimeTempWeek==4){
													$StrTeacherBreakTimeTempWeek = "목요일";
												}else if ($TeacherBreakTimeTempWeek==5){
													$StrTeacherBreakTimeTempWeek = "금요일";
												}else if ($TeacherBreakTimeTempWeek==6){
													$StrTeacherBreakTimeTempWeek = "토요일";
												}


									
											?>
											<tr>
												<td class="uk-text-nowrap uk-table-td-center"><?=$ListNumber?></td>
												<td class="uk-text-nowrap"><a href="javascript:OpenTeacherBreakTimeTempForm('<?=$TeacherBreakTimeTempID?>')"><?=$StrTeacherBreakTimeTempType?></td>
												<td class="uk-text-nowrap uk-table-td-center"><?=$TeacherBreakTimeTempStartDate?></td>
												<td class="uk-text-nowrap uk-table-td-center"><?=$TeacherBreakTimeTempEndDate?></td>
												<td class="uk-text-nowrap uk-table-td-center"><?=$StrTeacherBreakTimeTempWeek?></td>
												<td class="uk-text-nowrap uk-table-td-center"><?=substr("0".$TeacherBreakTimeTempStartHour,-2)?>:<?=substr("0".$TeacherBreakTimeTempStartMinute,-2)?></td>
												<td class="uk-text-nowrap uk-table-td-center"><?=substr("0".$TeacherBreakTimeTempEndHour,-2)?>:<?=substr("0".$TeacherBreakTimeTempEndMinute,-2)?></td>
											</tr>
											<?php
												$ListCount ++;
											}
											$Stmt = null;
											?>


										</tbody>
									</table>


									<div class="uk-form-row" style="text-align:center;">
										<a type="button" href="javascript:OpenTeacherBreakTimeTempForm('')" class="md-btn md-btn-primary"><?=$신규등록[$LangID]?></a>
									</div>

								</div>
							</li>
							<li style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>4){?>none<?}?>;">
								<h3 class="full_width_in_card heading_c">
									<?=$휴일관리[$LangID]?>
								</h3>
								<div style="padding:20px;">
									<div class="calendar"></div>
								</div>
							</li>
							<?}?>
						</ul>

					</div>
				</div>
			</div>
			<div class="uk-width-large-3-10">
				<div class="md-card">
					<div class="md-card-content">
						<h3 class="heading_c uk-margin-medium-bottom"><?=$기타설정[$LangID]?></h3>
						<div class="uk-form-row" style="display:none;">
							<input type="checkbox" id="TeacherView" name="TeacherView" value="1" <?php if ($TeacherView==1) { echo "checked";}?> data-switchery/>
							<label for="TeacherView" class="inline-label"><?=$활성화[$LangID]?></label>
						</div>
						<hr style="display:none;">

						<div class="uk-form-row">
							<input type="checkbox" id="TeacherBlock80Min" name="TeacherBlock80Min" value="1" <?php if ($TeacherBlock80Min==1) { echo "checked";}?> data-switchery/>
							<label for="TeacherBlock80Min" class="inline-label"><?=$P80분_연속강의_제한[$LangID]?></label>
						</div>
						<hr>


						<div class="uk-form-row">
							<input type="checkbox" id="TeacherState" name="TeacherState" value="1" <?php if ($TeacherState==1) { echo "checked";}?> data-switchery/>
							<label for="TeacherState" class="inline-label"><?=$활동중[$LangID]?></label>
						</div>
						<hr>
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


<!--  dropify -->
<script src="bower_components/dropify/dist/js/dropify.min.js"></script>
<!--  form file input functions -->
<script src="assets/js/pages/forms_file_input.min.js"></script>
<script>
$(function() {
	if(isHighDensity()) {
		$.getScript( "assets/js/custom/dense.min.js", function(data) {
			// enable hires images
			altair_helpers.retina_images();
		});
	}
	if(Modernizr.touch) {
		// fastClick (touch devices)
		FastClick.attach(document.body);
	}
});
$window.load(function() {
	// ie fixes
	altair_helpers.ie_fix();
});
</script>

<?if ($TeacherID!=""){?>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
			  integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
			integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
			crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
			integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
			crossorigin="anonymous"></script>
	<!-- Project Files -->
	<link rel="stylesheet" href="./js/bootstrap_year_calendar/jquery.bootstrap.year.calendar.min.css">
	<script src="./js/bootstrap_year_calendar/jquery.bootstrap.year.calendar.min.js"></script>
	<script>
	function appendToCalendar() {
		<?
		$Sql = "
				select 
					A.*
				from TeacherHolidays A 
					where A.TeacherID=$TeacherID and A.TeacherHolidayState=1
				order by A.TeacherHolidayDate asc";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		while($Row = $Stmt->fetch()) {
			$TeacherHolidayDate = $Row["TeacherHolidayDate"];
			$ArrTeacherHolidayDate = explode("-",$TeacherHolidayDate);
		?>
		$('.calendar').calendar('appendText', '■', <?=$ArrTeacherHolidayDate[0]?>, <?=intval($ArrTeacherHolidayDate[1])?>, <?=intval($ArrTeacherHolidayDate[2])?>);
		<?
		}
		?>
		
	}

	 $('.calendar').on('jqyc.dayChoose', function (event) {
		var choosenYear = $(this).data('year');
		var choosenMonth = $(this).data('month');
		var choosenDay = $(this).data('day-of-month');
		date = choosenYear+"-"+choosenMonth+"-"+choosenDay;

		openurl = "teacher_holiday_form.php?TeacherID=<?=$TeacherID?>&ListParam=<?=$ListParam?>&TeacherHolidayDate="+date;
		$.colorbox({	
			href:openurl
			,width:"500" 
			,height:"450"
			,maxWidth: "500"
			,maxHeight: "450"
			,title:""
			,iframe:true 
			,scrolling:true
			//,onClosed:function(){location.reload(true);}   
		}); 


	});

	
	var DisplayCalendar = 0;
	function SelectPageTab(PageTabID){
		if (PageTabID==3){
			if (DisplayCalendar==0){
			
				setTimeout(function(){

					$('.calendar').calendar();

					appendToCalendar();
					$('.calendar').on('jqyc.changeYear', function (event) {
						appendToCalendar();
					});

					DisplayCalendar = 1;

				}, 500);
			}
		}
	}

	<?if ($PageTabID=="3"){?>
		SelectPageTab(3);
	<?}?>
	</script>

<?}?>

<script>

function OpenTeacherBreakTimeTempForm(TeacherBreakTimeTempID){
	openurl = "teacher_break_time_temp_form.php?TeacherID=<?=$TeacherID?>&ListParam=<?=$ListParam?>&TeacherBreakTimeTempID="+TeacherBreakTimeTempID;
	$.colorbox({	
		href:openurl
		,width:"100%" 
		,height:"100%"
		,maxWidth: "800"
		,maxHeight: "800"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}   
	}); 
}


function SetTeacherUseBook(BookID){
	url = "ajax_set_teacher_use_book.php";

	//location.href = url + "?BookID="+BookID+"&TeacherID=<?=$TeacherID?>";
	$.ajax(url, {
		data: {
			BookID: BookID,
			TeacherID : "<?=$TeacherID?>"
		},
		success: function (data) {

		},
		error: function () {

		}
	});
}

</script>

<script>
function DownTeacherData(TeacherDataID){
	location.href = "teacher_data_file_down.php?TeacherDataID="+TeacherDataID;
	setTimeout(function(){
		location.href = "teacher_form.php?ListParam=<?=$ListParam?>&TeacherID=<?=$TeacherID?>&PageTabID=4";
	}, 2000);
}
</script>
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
                document.getElementById('TeacherZip').value = data.zonecode;
                document.getElementById("TeacherAddr1").value = addr;
                // 커서를 상세주소 필드로 이동한다.
                document.getElementById("TeacherAddr2").focus();

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
    var TeacherEmail_1 = $.trim($('#TeacherEmail_1').val());
	var TeacherEmail_2 = $.trim($('#TeacherEmail_2').val());

    if (TeacherEmail_1 == "" || TeacherEmail_2 == "") {
        alert('<?=$이메일을_입력하세요[$LangID]?>');
        document.RegForm.CheckedEmail.value = "0";
	} else {
        url = "ajax_check_email.php";

		//location.href = url + "?MemberEmail_1="+TeacherEmail_1+"&MemberEmail_2="+TeacherEmail_2+"&MemberID=<?=$MemberID?>";
        $.ajax(url, {
            data: {
                MemberEmail_1: TeacherEmail_1,
				MemberEmail_2: TeacherEmail_2,
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
	TeacherEmail_3 = document.RegForm.TeacherEmail_3.value;
	if (TeacherEmail_3==""){
		document.RegForm.TeacherEmail_2.value = "";
		document.RegForm.TeacherEmail_2.readOnly = false;
	}else{
		document.RegForm.TeacherEmail_2.value = TeacherEmail_3;
		document.RegForm.TeacherEmail_2.readOnly = true;
	}

	EnNewEmail();
}
//================ 이메일 =============

function FormSubmit(){
	obj = document.RegForm.TeacherGroupID;
	if (obj.value==""){
		UIkit.modal.alert("<?=$소속_강사그룹를_선택하세요[$LangID]?>");
		obj.focus();
		return;
	}

	obj = document.RegForm.TeacherName;
	if (obj.value==""){
		UIkit.modal.alert("<?=$강사명을_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}

	obj = document.RegForm.TeacherNickName;
	if (obj.value==""){
		UIkit.modal.alert("<?=$강사_닉네임을_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}

	obj = document.RegForm.MemberTimeZoneID;
	if (obj.value==""){
		UIkit.modal.alert("<?=$활동지역을_선택하세요[$LangID]?>");
		obj.focus();
		return;
	}

	TeacherPayPerTime
	obj = document.RegForm.TeacherPayPerTime;
	if (obj.value==""){
		UIkit.modal.alert("<?=$강사_수수료를_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}

	obj = document.RegForm.TeacherPayTypeItemID;
	if (obj.value==""){
		UIkit.modal.alert("<?=$출신지역을_선택하세요[$LangID]?>");
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


	<?
	if ($MemberID!=""){ 
	?>	
		obj = document.RegForm.MemberLoginNewPW;
		obj2 = document.RegForm.MemberLoginNewPW2;

		if (obj.value!="" || obj2.value!=""){
			
			if (obj.value.length<4){
				UIkit.modal.alert("<?=$비밀번호는_4자_이상_입력하세요[$LangID]?>");
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

		if (obj.value.length<4){
			UIkit.modal.alert("<?=$비밀번호는_4자_이상_입력하세요[$LangID]?>");
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

	obj = document.RegForm.TeacherEmail;
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

	<?if ($TeacherID!=""){?>
		obj1 = document.RegForm.TeacherStartHour;
		obj2 = document.RegForm.TeacherEndHour;

		if (Number(obj1.value)>=Number(obj2.value)){
			UIkit.modal.alert("<?=$종료시간을_시작시간보다_늦게_설정해_주세요[$LangID]?>");
			obj.focus();
			return;
		}
	<?}?>


	UIkit.modal.confirm(
		'<?=$저장_하시겠습니까[$LangID]?>?', 
		function(){ 
			document.RegForm.action = "teacher_action.php";
			document.RegForm.submit();
		}
	);

}

function OpenVideoPlayer() {

	
	var TeacherVideoCode = document.RegForm.TeacherVideoCode.value;

	if (TeacherVideoCode==""){
		UIkit.modal.alert("<?=$동영상_코드를_입력하세요[$LangID]?>");
	}else{
		var TeacherVideoTypeForm = document.RegForm.TeacherVideoType;
		if (TeacherVideoTypeForm[0].checked){
			TeacherVideoType = TeacherVideoTypeForm[0].value
		} else {
			TeacherVideoType = TeacherVideoTypeForm[1].value
		}

		openurl = "video_player.php?VideoCode="+TeacherVideoCode+"&VideoType="+TeacherVideoType;

		$.colorbox({	
			href:openurl
			,width:"95%" 
			,height:"95%"
			,maxWidth: "850"
			,maxHeight: "750"
			,title:""
			,iframe:true 
			,scrolling:false
			//,onClosed:function(){location.reload(true);}
			//,onComplete:function(){alert(1);}
		}); 
	}

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