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
$MainMenuID = 11;
$SubMenuID = 1101; 
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
include('./inc_departments.php');
$departments = getDepartments($LangID);
?>

 

<?php
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$StaffID = isset($_REQUEST["StaffID"]) ? $_REQUEST["StaffID"] : "";
$PageTabID = isset($_REQUEST["PageTabID"]) ? $_REQUEST["PageTabID"] : "";
if ($PageTabID==""){
	$PageTabID = "1";
}

if ($StaffID!=""){

	$Sql = "SELECT 
					A.*,
					AES_DECRYPT(UNHEX(A.StaffPhone1),:EncryptionKey) as DecStaffPhone1,
					AES_DECRYPT(UNHEX(A.StaffPhone2),:EncryptionKey) as DecStaffPhone2,
					AES_DECRYPT(UNHEX(A.StaffPhone3),:EncryptionKey) as DecStaffPhone3,
					AES_DECRYPT(UNHEX(A.StaffEmail),:EncryptionKey) as DecStaffEmail,
					C.MemberID,
					C.MemberDprtName,
					C.MemberLoginID,
					C.MemberLoginPW,
					C.MemberLanguageID,
					C.MemberCiTelephone,
					A.Jumin1,
					A.Jumin2,
					D.WorkType
			from Staffs A 
				inner join Members C on A.StaffID=C.StaffID and (C.MemberLevelID=4 OR C.MemberLevelID=15)
				left join PayInfo D on C.MemberID = D.MemberID
			where A.StaffID=:StaffID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':StaffID', $StaffID);
	$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$FranchiseID = $Row["FranchiseID"];
	$StaffName = $Row["StaffName"];
	$StaffNickName = $Row["StaffNickName"];
	$StaffManageMent = $Row["StaffManageMent"];
	//================ 전화번호 / 이메일 =============
	$StaffPhone1 = $Row["DecStaffPhone1"];
	$StaffPhone2 = $Row["DecStaffPhone2"];
	$StaffPhone3 = $Row["DecStaffPhone3"];
	$StaffEmail = $Row["DecStaffEmail"];
	//================ 전화번호 / 이메일 =============
	$StaffZip = $Row["StaffZip"];
	$StaffAddr1 = $Row["StaffAddr1"];
	$StaffAddr2 = $Row["StaffAddr2"];
	$StaffIntroText = $Row["StaffIntroText"];
	$StaffState = $Row["StaffState"];
	$StaffView = $Row["StaffView"];
	//-======== 주민번호와 소득종류 ================
	$Jumin1 = $Row["Jumin1"];
	$Jumin2 = $Row["Jumin2"];
	$WorkType = $Row["WorkType"];

	$RetirementDate = substr($Row["RetirementDate"],0,10);

	//Members 
	$MemberID = $Row["MemberID"];
	$MemberDprtName = $Row["MemberDprtName"];
	$MemberLoginID = $Row["MemberLoginID"];
	$MemberLoginPW = $Row["MemberLoginPW"];
	$MemberLanguageID = $Row["MemberLanguageID"];
	$MemberCiTelephone = $Row["MemberCiTelephone"];
	$CheckedID = 1;
	$CheckedEmail = 1;

}else{
	$FranchiseID = "";
	$StaffName = "";
	$StaffNickName = "";
	$StaffManageMent = "";
	//================ 전화번호 / 이메일 =============
	$StaffPhone1 = "--";
	$StaffPhone2 = "--";
	$StaffPhone3 = "--";
	$StaffEmail = "@";
	//================ 전화번호 / 이메일 =============
	$StaffZip = "";
	$StaffAddr1 = "";
	$StaffAddr2 = "";
	$StaffIntroText = "";
	$StaffState = 1;
	$StaffView = 1;
	//-======== 주민번호와 소득종류 ================
	$Jumin1 = "";
	$Jumin2 = "";
	$WorkType = "";
	//Members 
	$MemberID = "";
	$MemberDprtName = "";
	$MemberLoginID = "";
	$MemberLoginPW = "";
	$MemberLanguageID = 0;
	$MemberCiTelephone = "";
	$CheckedID = 0;
	$CheckedEmail = 0;
}

//================ 전화번호 / 이메일 =============
$ArrStaffPhone1 = explode("-", $StaffPhone1);
$ArrStaffPhone2 = explode("-", $StaffPhone2);
$ArrStaffPhone3 = explode("-", $StaffPhone3);
$ArrStaffEmail = explode("@", $StaffEmail);

$StaffPhone1_1 = $ArrStaffPhone1[0];
$StaffPhone1_2 = isset($ArrStaffPhone1[1])?$ArrStaffPhone1[1]:"";
$StaffPhone1_3 = isset($ArrStaffPhone1[2])?$ArrStaffPhone1[2]:"";

$StaffPhone2_1 = $ArrStaffPhone2[0];
$StaffPhone2_2 = isset($ArrStaffPhone2[1])?$ArrStaffPhone2[1]:"";
$StaffPhone2_3 = isset($ArrStaffPhone2[2])?$ArrStaffPhone2[2]:"";

$StaffPhone3_1 = $ArrStaffPhone3[0];
$StaffPhone3_2 = isset($ArrStaffPhone3[1])?$ArrStaffPhone3[1]:"";
$StaffPhone3_3 = isset($ArrStaffPhone3[2])?$ArrStaffPhone3[2]:"";

$StaffEmail_1 = $ArrStaffEmail[0];
$StaffEmail_2 = isset($ArrStaffEmail[1])?$ArrStaffEmail[1]:"";
//================ 전화번호 / 이메일 =============

$MemberLoginNewPW = "";
$MemberLoginNewPW2 = "";



$HideFranchiseID = 0;
$AddWhere_Franchises = "";
if ($_LINK_ADMIN_LEVEL_ID_>1){
	$FranchiseID = $_LINK_ADMIN_FRANCHISE_ID_;

	$HideFranchiseID = 1;

	$AddWhere_Franchises = " and A.FranchiseID=".$_LINK_ADMIN_FRANCHISE_ID_." ";
}
?>


<div id="page_content">
	<div id="page_content_inner">

		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="StaffID" value="<?=$StaffID?>">
		<input type="hidden" name="ListParam" value="<?=$ListParam?>">

		<input type="hidden" name="MemberID" value="<?=$MemberID?>">
		<input type="hidden" name="CheckedID" value="<?=$CheckedID?>">
		<input type="hidden" name="CheckedEmail" value="<?=$CheckedEmail?>">
		<input type="hidden" name="MemberLoginPW" value="<?=$MemberLoginPW?>">
		<input type="hidden" name="PageTabID" value="<?=$PageTabID?>">
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
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$StaffName?></span><span class="sub-heading" id="user_edit_position"><?=$교사_및_직원관리[$LangID]?></span></h2>
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
						
						<ul id="user_edit_tabs" class="uk-tab" data-uk-tab="{connect:'#user_edit_tabs_content'}" style="display:<?if ($StaffID==""){?>none<?}?>;">
							<li <?if ($PageTabID=="1"){?>class="uk-active"<?}?>><a href="#"><?=$직원정보[$LangID]?></a></li>
							<li <?if ($PageTabID=="2"){?>class="uk-active"<?}?> style="display:none;"><a href="#"><?=$권한설정[$LangID]?></a></li>
							<!----li <?if ($PageTabID=="3"){?>class="uk-active"<?}?> style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>0){?>none<?}?>;"><a href="#">역량평가대상</a></li---->
						</ul>
						<ul id="user_edit_tabs_content" class="uk-switcher uk-margin">
							<li>
								<div class="uk-margin-top">
									<h3 class="full_width_in_card heading_c">
										<?=$교사_및_직원정보[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-3-10" style="padding-top:7px;">
											<select id="FranchiseID" name="FranchiseID" class="uk-width-1-1" data-md-select2 data-allow-clear="true" data-placeholder="프랜차이즈선택" style="width:100%;"/>
												<option value=""></option>
												<?
												$Sql2 = "select 
																A.*,
																B.FranchiseName 
														from Franchises A 
															inner join Franchises B on A.FranchiseID=B.FranchiseID 
														where A.FranchiseState<>0 and B.FranchiseState<>0 ".$AddWhere_Franchises."
														order by A.FranchiseState asc, B.FranchiseName asc";
												
												$Stmt2 = $DbConn->prepare($Sql2);
												$Stmt2->execute();
												$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
												
												$OldSelectFranchiseState = -1;
												while($Row2 = $Stmt2->fetch()) {
													$SelectFranchiseID = $Row2["FranchiseID"];
													$SelectFranchiseName = $Row2["FranchiseName"];
													$SelectFranchiseState = $Row2["FranchiseState"];
													$SelectEduCenterName = $Row2["EduCenterName"];
													$SelectFranchiseName = $Row2["FranchiseName"];
													if ($_LINK_ADMIN_LEVEL_ID_ <=2){
														$StrSelectFranchiseName = " (".$SelectFranchiseName.")";
													}else{
														$StrSelectFranchiseName = "";
													}
												
													if ($OldSelectFranchiseState!=$SelectFranchiseState){
														if ($OldSelectFranchiseState!=-1){
															echo "</optgroup>";
														}
														
														if ($SelectFranchiseState==1){
															echo "<optgroup label=\"".$프랜차이즈_운영중[$LangID]."\">";
														}else if ($SelectFranchiseState==2){
															echo "<optgroup label=\"".$프랜차이즈_미운영[$LangID]."\">";
														}
													}
													$OldSelectFranchiseState = $SelectFranchiseState;
												?>

												<option value="<?=$SelectFranchiseID?>" <?if ($FranchiseID==$SelectFranchiseID){?>selected<?}?>><?=$SelectFranchiseName?><?=$StrSelectFranchiseName?></option>
												<?
												}
												$Stmt2 = null;
												?>
											</select>
										</div>
										<div class="uk-width-medium-3-10">
											<div class="uk-width-medium-8-10" style="padding-top:7px;">
												<select id="StaffManageMent" name="StaffManageMent" class="uk-width-1-1" data-md-select2 data-allow-clear="true" data-placeholder="관리부서선택" style="width:100%;"/>
													<option></option>
													<?		
															foreach($departments as $key => $value){
																echo "<option value='{$key}' ".($StaffManageMent==$key?"selected":"").">{$value}</option>";
															}
													?>	
												</select>
											</div>
										</div>

										
										<div class="uk-width-medium-4-10">
											<label for="MemberDprtName"><?=$부서_상세[$LangID]?></label>
											<input type="text" id="MemberDprtName" name="MemberDprtName" value="<?=$MemberDprtName?>" class="md-input label-fixed"/>
										</div>

										<div class="uk-width-medium-2-10">
											<label for="StaffName"><?=$교사_및_직원명[$LangID]?></label>
											<input type="text" id="StaffName" name="StaffName" value="<?=$StaffName?>" class="md-input label-fixed"/>
										</div>
										<div class="uk-width-medium-2-10">
											<label for="StaffNickName"><?=$닉네임[$LangID]?></label>
											<input type="text" id="StaffNickName" name="StaffNickName" value="<?=$StaffNickName?>" class="md-input label-fixed"/>
										</div>
										<div class="uk-width-medium-6-10 uk-form-row">
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
										<div class="uk-width-medium-2-10">
											<label for="Jumin1"><?=$주민번호앞자리[$LangID]?></label>
											<input type="text" id="Jumin1" name="Jumin1" value="<?=$Jumin1?>" class="md-input label-fixed" maxlength='6'/>
										</div>
										<div class="uk-width-medium-2-10">
											<label for="Jumin2"><?=$주민번호뒷자리[$LangID]?></label>
											<input type="text" id="Jumin2" name="Jumin2" value="<?=$Jumin2?>" class="md-input label-fixed" maxlength='7'/>
										</div>
										<div class="uk-width-medium-6-10 uk-form-row">
											<span class="icheck-inline">
												<input type="radio" id="WorkType" name="WorkType" value="0" <?php if ($WorkType==0) { echo "checked";}?> data-md-icheck/>
												<label for="WorkType" class="inline-label"><?=$근로소득자[$LangID]?></label>
											</span>
											<span class="icheck-inline">
												<input type="radio" id="WorkType" name="WorkType" value="1" <?php if ($WorkType==1) { echo "checked";}?> data-md-icheck/>
												<label for="WorkType" class="inline-label"><?=$사업소득자[$LangID]?></label>
											</span>
											
										</div>
										<div class="uk-width-medium-4-10 uk-form-row">
											퇴사일자 <input type="input" id="RetirementDate" name="RetirementDate" value="<?=$RetirementDate?>" class="draft_input" style="width:40%;display:inline" data-uk-datepicker="{format:'YYYY-MM-DD', weekstart:0}" > 
										</div>
									</div>
									<h3 class="full_width_in_card heading_c">
										<?=$아이디_및_비밀번호[$LangID]?> <?if ($MemberID!="") {?>(비밀번호는 변경을 원할때 입력하세요)<?}?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-4-10 uk-input-group">
											<label for="MemberLoginID">아이디</label>
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
											<label for="StaffPhone1"><?=$전화번호[$LangID]?></label>
											<div class="md-input label-fixed" style="padding-top:10px;margin-bottom:10px;">
												<select name="StaffPhone1_1" class="Select" style="width:15%;height:30px;">
													<option value="010" <?if ($StaffPhone1_1=="010") {?>selected<?}?>>010</option>
													<option value="011" <?if ($StaffPhone1_1=="011") {?>selected<?}?>>011</option>
													<option value="016" <?if ($StaffPhone1_1=="016") {?>selected<?}?>>016</option>
													<option value="017" <?if ($StaffPhone1_1=="017") {?>selected<?}?>>017</option>
													<option value="018" <?if ($StaffPhone1_1=="018") {?>selected<?}?>>018</option>
													<option value="019" <?if ($StaffPhone1_1=="019") {?>selected<?}?>>019</option>
													<option value="070" <?if ($StaffPhone1_1=="070") {?>selected<?}?>>070</option>
													<option value="02"  <?if ($StaffPhone1_1=="02")  {?>selected<?}?>>02</option>
													<option value="031" <?if ($StaffPhone1_1=="031") {?>selected<?}?>>031</option>
													<option value="032" <?if ($StaffPhone1_1=="032") {?>selected<?}?>>032</option>
													<option value="033" <?if ($StaffPhone1_1=="033") {?>selected<?}?>>033</option>
													<option value="041" <?if ($StaffPhone1_1=="041") {?>selected<?}?>>041</option>
													<option value="042" <?if ($StaffPhone1_1=="042") {?>selected<?}?>>042</option>
													<option value="043" <?if ($StaffPhone1_1=="043") {?>selected<?}?>>043</option>
													<option value="044" <?if ($StaffPhone1_1=="044") {?>selected<?}?>>044</option>
													<option value="049" <?if ($StaffPhone1_1=="049") {?>selected<?}?>>049</option>
													<option value="051" <?if ($StaffPhone1_1=="051") {?>selected<?}?>>051</option>
													<option value="052" <?if ($StaffPhone1_1=="052") {?>selected<?}?>>052</option>
													<option value="053" <?if ($StaffPhone1_1=="053") {?>selected<?}?>>053</option>
													<option value="054" <?if ($StaffPhone1_1=="054") {?>selected<?}?>>054</option>
													<option value="055" <?if ($StaffPhone1_1=="055") {?>selected<?}?>>055</option>
													<option value="061" <?if ($StaffPhone1_1=="061") {?>selected<?}?>>061</option>
													<option value="062" <?if ($StaffPhone1_1=="062") {?>selected<?}?>>062</option>
													<option value="063" <?if ($StaffPhone1_1=="063") {?>selected<?}?>>063</option>
													<option value="064" <?if ($StaffPhone1_1=="064") {?>selected<?}?>>064</option>
													<option value="0505" <?if ($CenterPhone1_1=="0505") {?>selected<?}?>>0505</option>
													<option value="0502" <?if ($CenterPhone1_1=="0502") {?>selected<?}?>>0502</option>
												</select>
												<input type="text" name="StaffPhone1_2" style="width:15%;height:30px;padding-left:10px;" placeholder="" value="<?=$StaffPhone1_2?>" class="allownumericwithoutdecimal" maxlength="4"> 
												<input type="text" name="StaffPhone1_3" style="width:15%;height:30px;padding-left:10px;"  placeholder="" value="<?=$StaffPhone1_3?>" class="allownumericwithoutdecimal" maxlength="4">
											</div>
										</div>
										<div class="uk-width-medium-1-2 uk-input-group">
											<label for="StaffPhone2"><?=$휴대폰[$LangID]?></label>
											<div class="md-input label-fixed" style="padding-top:10px;margin-bottom:10px;">
												<select name="StaffPhone2_1" class="Select" style="width:15%;height:30px;">
													<option value="010" <?if ($StaffPhone2_1=="010") {?>selected<?}?>>010</option>
													<option value="011" <?if ($StaffPhone2_1=="011") {?>selected<?}?>>011</option>
													<option value="016" <?if ($StaffPhone2_1=="016") {?>selected<?}?>>016</option>
													<option value="017" <?if ($StaffPhone2_1=="017") {?>selected<?}?>>017</option>
													<option value="018" <?if ($StaffPhone2_1=="018") {?>selected<?}?>>018</option>
													<option value="019" <?if ($StaffPhone2_1=="019") {?>selected<?}?>>019</option>
												</select>
												<input type="text" name="StaffPhone2_2" style="width:15%;height:30px;padding-left:10px;" placeholder="" value="<?=$StaffPhone2_2?>" class="allownumericwithoutdecimal" maxlength="4"> 
												<input type="text" name="StaffPhone2_3" style="width:15%;height:30px;padding-left:10px;"  placeholder="" value="<?=$StaffPhone2_3?>" class="allownumericwithoutdecimal" maxlength="4">
											</div>
										</div>
									</div>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-1-2 uk-input-group">
											<label for="StaffPhone3"><?=$팩스번호[$LangID]?></label>
											<div class="md-input label-fixed" style="padding-top:10px;margin-bottom:10px;">
												<select name="StaffPhone3_1" class="Select" style="width:15%;height:30px;">
													<option value="02"  <?if ($StaffPhone3_1=="02")  {?>selected<?}?>>02</option>
													<option value="031" <?if ($StaffPhone3_1=="031") {?>selected<?}?>>031</option>
													<option value="032" <?if ($StaffPhone3_1=="032") {?>selected<?}?>>032</option>
													<option value="033" <?if ($StaffPhone3_1=="033") {?>selected<?}?>>033</option>
													<option value="041" <?if ($StaffPhone3_1=="041") {?>selected<?}?>>041</option>
													<option value="042" <?if ($StaffPhone3_1=="042") {?>selected<?}?>>042</option>
													<option value="043" <?if ($StaffPhone3_1=="043") {?>selected<?}?>>043</option>
													<option value="044" <?if ($StaffPhone3_1=="044") {?>selected<?}?>>044</option>
													<option value="049" <?if ($StaffPhone3_1=="049") {?>selected<?}?>>049</option>
													<option value="051" <?if ($StaffPhone3_1=="051") {?>selected<?}?>>051</option>
													<option value="052" <?if ($StaffPhone3_1=="052") {?>selected<?}?>>052</option>
													<option value="053" <?if ($StaffPhone3_1=="053") {?>selected<?}?>>053</option>
													<option value="054" <?if ($StaffPhone3_1=="054") {?>selected<?}?>>054</option>
													<option value="055" <?if ($StaffPhone3_1=="055") {?>selected<?}?>>055</option>
													<option value="061" <?if ($StaffPhone3_1=="061") {?>selected<?}?>>061</option>
													<option value="062" <?if ($StaffPhone3_1=="062") {?>selected<?}?>>062</option>
													<option value="063" <?if ($StaffPhone3_1=="063") {?>selected<?}?>>063</option>
													<option value="064" <?if ($StaffPhone3_1=="064") {?>selected<?}?>>064</option>
													<option value="010" <?if ($StaffPhone3_1=="010") {?>selected<?}?>>010</option>
													<option value="011" <?if ($StaffPhone3_1=="011") {?>selected<?}?>>011</option>
													<option value="016" <?if ($StaffPhone3_1=="016") {?>selected<?}?>>016</option>
													<option value="017" <?if ($StaffPhone3_1=="017") {?>selected<?}?>>017</option>
													<option value="018" <?if ($StaffPhone3_1=="018") {?>selected<?}?>>018</option>
													<option value="019" <?if ($StaffPhone3_1=="019") {?>selected<?}?>>019</option>
													<option value="070" <?if ($StaffPhone3_1=="070") {?>selected<?}?>>070</option>
													<option value="0505" <?if ($StaffPhone3_1=="0505") {?>selected<?}?>>0505</option>
													<option value="0502" <?if ($StaffPhone3_1=="0502") {?>selected<?}?>>0502</option>
												</select>
												<input type="text" name="StaffPhone3_2" style="width:15%;height:30px;padding-left:10px;" placeholder="" value="<?=$StaffPhone3_2?>" class="allownumericwithoutdecimal" maxlength="4"> 
												<input type="text" name="StaffPhone3_3" style="width:15%;height:30px;padding-left:10px;"  placeholder="" value="<?=$StaffPhone3_3?>" class="allownumericwithoutdecimal" maxlength="4">
											</div>
										</div>
										<div class="uk-width-medium-1-2 uk-input-group">
											<label for="StaffEmail"><?=$이메일[$LangID]?></label>
											<div class="md-input label-fixed" style="padding-top:10px;margin-bottom:10px;">
												<input type="text" name="StaffEmail_1" id="StaffEmail_1" style="width:20%;height:30px;padding-left:10px;" value="<?=$StaffEmail_1?>" onkeyup="EnNewEmail()"> 
												<div style="display:inline-block;width:20px;margin-top:8px;">@</div> 
												<input type="text" name="StaffEmail_2" id="StaffEmail_2" style="width:20%;height:30px;padding-left:10px;" value="<?=$StaffEmail_2?>" onkeyup="EnNewEmail()">
												<select name="StaffEmail_3" class="Select" style="width:22%;height:30px;margin-bottom:0px;" onchange="SetEmailName()">
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
											<label for="StaffZip"><?=$우편번호[$LangID]?></label>
											<input type="text" id="StaffZip" name="StaffZip" value="<?=$StaffZip?>" class="md-input label-fixed" />
											<span class="uk-input-group-addon"><a class="md-btn" href="javascript:ExecDaumPostcode();"><?=$검색[$LangID]?></a></span>

										</div>
										<div class="uk-width-medium-3-10 uk-input-group">
											<label for="StaffAddr1"><?=$주소[$LangID]?></label>
											<input type="text" id="StaffAddr1" name="StaffAddr1" value="<?=$StaffAddr1?>" class="md-input label-fixed" />
										</div>
										<div class="uk-width-medium-4-10 uk-input-group">
											<label for="StaffAddr2"><?=$상세주소[$LangID]?></label>
											<input type="text" id="StaffAddr2" name="StaffAddr2" value="<?=$StaffAddr2?>" class="md-input label-fixed" />
										</div>
									</div>

									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-1-1">
											<label for="StaffIntroText"><?=$메모[$LangID]?></label>
											<textarea class="md-input" name="StaffIntroText" id="StaffIntroText" cols="30" rows="4"><?=$StaffIntroText?></textarea>
										</div>
									</div>

									<?
									$Sql2 = "select 
												A.*,
												B.Hr_OrganTask1ID 
											 from Hr_OrganLevelTaskMembers A 
												inner join Hr_OrganTask2 B on A.Hr_OrganTask2ID=B.Hr_OrganTask2ID 
											 where A.MemberID=:MemberID";
									$Stmt2 = $DbConn->prepare($Sql2);
									$Stmt2->bindParam(':MemberID', $MemberID);
									$Stmt2->execute();
									$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
									$Row2 = $Stmt2->fetch();

									$Hr_OrganLevel = $Row2["Hr_OrganLevel"];
									$Hr_OrganLevelID = $Row2["Hr_OrganLevelID"];
									$Hr_OrganTask2ID = $Row2["Hr_OrganTask2ID"];
									$Hr_OrganPositionName = $Row2["Hr_OrganPositionName"];

									$Hr_OrganTask1ID = $Row2["Hr_OrganTask1ID"];

									
									if ($Hr_OrganLevel==""){
										$Hr_OrganLevel = 0;
									}
									if ($Hr_OrganLevelID==""){
										$Hr_OrganLevelID = 0;
									}
									if ($Hr_OrganTask1ID==""){
										$Hr_OrganTask1ID = 0;
									}
									if ($Hr_OrganTask2ID==""){
										$Hr_OrganTask2ID = 0;
									}

							
									?>
									
									
									<h3 class="full_width_in_card heading_c" style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>0){?>none<?}?>;">
										<?=$인사평가_설정[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>0){?>none<?}?>;">
										<div class="uk-width-medium-10-10 uk-input-group">
											<label for="StaffPhone1"><?=$소속부서[$LangID]?></label>
											<div class="md-input label-fixed" style="padding-top:10px;margin-bottom:10px;">
												<select id="Hr_OrganLevelID" name="Hr_OrganLevelID" class="Select" style="width:100%;height:30px;">
													
													<option value="0"><?=$선택[$LangID]?></option>
													<?
													$Sql2 = "select 
																	A.*,
																	ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=A.Hr_OrganLevel1ID), '') as Hr_OrganLevelName1, 
																	ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=A.Hr_OrganLevel2ID), '') as Hr_OrganLevelName2,
																	ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=A.Hr_OrganLevel3ID), '') as Hr_OrganLevelName3,
																	ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=A.Hr_OrganLevel4ID), '') as Hr_OrganLevelName4
															from Hr_OrganLevels A 

															where 
																A.Hr_OrganLevelState=1 or A.Hr_OrganLevelID=:Hr_OrganLevelID
															order by A.Hr_OrganLevel, A.Hr_OrganLevel1ID asc, A.Hr_OrganLevel2ID asc, A.Hr_OrganLevel3ID asc, A.Hr_OrganLevel4ID asc";
													$Stmt2 = $DbConn->prepare($Sql2);
													$Stmt2->bindParam(':Hr_OrganLevelID', $Hr_OrganLevelID);
													$Stmt2->execute();
													$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

													while($Row2 = $Stmt2->fetch()) {
														
														$Db_Hr_OrganLevelID = $Row2["Hr_OrganLevelID"];
														$Db_Hr_OrganLevel = $Row2["Hr_OrganLevel"];
														$Db_Hr_OrganLevelName1 = $Row2["Hr_OrganLevelName1"];
														$Db_Hr_OrganLevelName2 = $Row2["Hr_OrganLevelName2"];
														$Db_Hr_OrganLevelName3 = $Row2["Hr_OrganLevelName3"];
														$Db_Hr_OrganLevelName4 = $Row2["Hr_OrganLevelName4"];

														$Str_Db_Hr_OrganLevelName = $Db_Hr_OrganLevelName1;
														if ($Db_Hr_OrganLevelName2!=""){
															$Str_Db_Hr_OrganLevelName .= " > " . $Db_Hr_OrganLevelName2;
														}
														if ($Db_Hr_OrganLevelName3!=""){
															$Str_Db_Hr_OrganLevelName .= " > " . $Db_Hr_OrganLevelName3;
														}
														if ($Db_Hr_OrganLevelName4!=""){
															$Str_Db_Hr_OrganLevelName .= " > " . $Db_Hr_OrganLevelName4;
														}
													?>
													<option value="<?=$Db_Hr_OrganLevelID?>" <?if ($Db_Hr_OrganLevelID==$Hr_OrganLevelID) {?>selected<?}?>>[레벨 <?=$Db_Hr_OrganLevel?>] <?=$Str_Db_Hr_OrganLevelName?></option>
													<?
													}
													$Stmt2 = null;
													?>
												</select>
											</div>
										</div>
										<div class="uk-width-medium-6-10 uk-input-group">
											<label for="StaffPhone1"><?=$직무[$LangID]?></label>
											<div class="md-input label-fixed" style="padding-top:10px;margin-bottom:10px;">
												<select id="Hr_OrganTask1ID" name="Hr_OrganTask1ID" class="Select" style="width:47%;height:30px;" onchange="Ch_Hr_OrganTask1ID(this.value, <?=$Hr_OrganTask2ID?>)">
													<option value="0"><?=$선택[$LangID]?></option>
													<?
													$Sql2 = "select 
																	A.*
															from Hr_OrganTask1 A 

															where 
																A.Hr_OrganTask1State=1 or A.Hr_OrganTask1ID=:Hr_OrganTask1ID
															order by A.Hr_OrganTask1Name asc";
													$Stmt2 = $DbConn->prepare($Sql2);
													$Stmt2->bindParam(':Hr_OrganTask1ID', $Hr_OrganTask1ID);
													$Stmt2->execute();
													$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

													while($Row2 = $Stmt2->fetch()) {
														$Db_Hr_OrganTask1ID = $Row2["Hr_OrganTask1ID"];
														$Db_Hr_OrganTask1Name = $Row2["Hr_OrganTask1Name"];
													?>
													<option value="<?=$Db_Hr_OrganTask1ID?>" <?if ($Db_Hr_OrganTask1ID==$Hr_OrganTask1ID) {?>selected<?}?>><?=$Db_Hr_OrganTask1Name?></option>
													<?
													}
													$Stmt2 = null;
													?>
												</select>

												<select id="Hr_OrganTask2ID" name="Hr_OrganTask2ID" class="Select" style="width:47%;height:30px;">
													<option value="0"><?=$선택[$LangID]?></option>
													<?
													if ($Hr_OrganTask1ID!=0){
														$Sql2 = "select 
																		A.*
																from Hr_OrganTask2 A 

																where 
																	(A.Hr_OrganTask2State=1 or A.Hr_OrganTask2ID=:Hr_OrganTask2ID)
																	and A.Hr_OrganTask1ID=:Hr_OrganTask1ID 
																order by A.Hr_OrganTask2Name asc";
														$Stmt2 = $DbConn->prepare($Sql2);
														$Stmt2->bindParam(':Hr_OrganTask2ID', $Hr_OrganTask2ID);
														$Stmt2->bindParam(':Hr_OrganTask1ID', $Hr_OrganTask1ID);
														$Stmt2->execute();
														$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

														while($Row2 = $Stmt2->fetch()) {
															$Db_Hr_OrganTask2ID = $Row2["Hr_OrganTask2ID"];
															$Db_Hr_OrganTask2Name = $Row2["Hr_OrganTask2Name"];
													?>
													<option value="<?=$Db_Hr_OrganTask2ID?>" <?if ($Db_Hr_OrganTask2ID==$Hr_OrganTask2ID) {?>selected<?}?>><?=$Db_Hr_OrganTask2Name?></option>
													<?
														}
														$Stmt2 = null;
													}
													?>
												</select>
											</div>
										</div>
										<div class="uk-width-medium-4-10 uk-input-group">
											<label for="Hr_OrganPositionName"><?=$직급_직책[$LangID]?></label>
											<input type="text" id="Hr_OrganPositionName" name="Hr_OrganPositionName" value="<?=$Hr_OrganPositionName?>" class="md-input label-fixed" />
										</div>
										
										
										<div class="uk-width-medium-2-10 uk-input-group" style="display:none;">
											<label for="Hr_OrganLevel"><?=$권한[$LangID]?></label>
											<div class="md-input label-fixed" style="padding-top:10px;margin-bottom:10px;">
												<select id="Hr_OrganLevel" name="Hr_OrganLevel" class="Select" style="width:100%;height:30px;">
														<option value="0"><?=$선택[$LangID]?></option>
														<option value="1" <?if ($Hr_OrganLevel==1) {?>selected<?}?>>LEVEL 1(<?=$경영진[$LangID]?>)</option>
														<option value="2" <?if ($Hr_OrganLevel==2) {?>selected<?}?>>LEVEL 2(<?=$부문[$LangID]?>)</option>
														<option value="3" <?if ($Hr_OrganLevel==3) {?>selected<?}?>>LEVEL 3(<?=$부서[$LangID]?>)</option>
														<option value="4" <?if ($Hr_OrganLevel==4) {?>selected<?}?>>LEVEL 4(<?=$파트[$LangID]?>)</option>
													</select>
												</div>
										</div>


									</div>
								</div>
							</li>
							<?
							if ($StaffID!=""){
								
							?>
							<li style="display:none;">
								<!--권한관리-->
								<table class="uk-table uk-table-align-vertical">
									<thead>
										<tr>
											<th>대메뉴</th>
											<th>소메뉴</th>
											<th width="10%">읽기</th>
											<th width="10%">쓰기</th>
											<th width="10%">수정</th>
											<th width="10%">삭제</th>
										</tr>
									</thead>
									<tbody>
									<?
									$Sql2 = "select 
													A.LmsSubMenuID,
													A.LmsSubMenuName,
													B.LmsMainMenuID,
													B.LmsMainMenuName
											from LmsSubMenus A 
												inner join LmsMainMenus B on A.LmsMainMenuID=B.LmsMainMenuID 
											where B.LmsMainMenuState=1 and A.LmsSubMenuState=1 
											order by B.LmsMainMenuOrder asc, A.LmsSubMenuOrder asc";
									
									$Stmt2 = $DbConn->prepare($Sql2);
									$Stmt2->execute();
									$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
									
									while($Row2 = $Stmt2->fetch()) {
										$LmsMainMenuID = $Row2["LmsMainMenuID"];
										$LmsSubMenuID = $Row2["LmsSubMenuID"];
										$LmsMainMenuName = $Row2["LmsMainMenuName"];
										$LmsSubMenuName = $Row2["LmsSubMenuName"];
									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><?=$LmsMainMenuName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$LmsSubMenuName?></td>
										<td class="uk-text-nowrap uk-table-td-center">
											<input type="checkbox" id="LmsSubMenu_<?=$LmsSubMenuID?>_1" name="LmsSubMenu_<?=$LmsSubMenuID?>_1" value="1" checked data-switchery/>
											<label for="LmsSubMenu_<?=$LmsSubMenuID?>_1" class="inline-label">읽기</label>
										</td>
										<td class="uk-text-nowrap uk-table-td-center">
											<input type="checkbox" id="LmsSubMenu_<?=$LmsSubMenuID?>_2" name="LmsSubMenu_<?=$LmsSubMenuID?>_2" value="1" checked data-switchery/>
											<label for="LmsSubMenu_<?=$LmsSubMenuID?>_2" class="inline-label">쓰기</label>
										</td>
										<td class="uk-text-nowrap uk-table-td-center">
											<input type="checkbox" id="LmsSubMenu_<?=$LmsSubMenuID?>_3" name="LmsSubMenu_<?=$LmsSubMenuID?>_3" value="1" checked data-switchery/>
											<label for="LmsSubMenu_<?=$LmsSubMenuID?>_3" class="inline-label">수정</label>
										</td>
										<td class="uk-text-nowrap uk-table-td-center">
											<input type="checkbox" id="LmsSubMenu_<?=$LmsSubMenuID?>_4" name="LmsSubMenu_<?=$LmsSubMenuID?>_4" value="1" checked data-switchery/>
											<label for="LmsSubMenu_<?=$LmsSubMenuID?>_4" class="inline-label">삭제</label>
										</td>
									</tr>
									<?
									}
									?>
								</tbody>
							</table>
							</li>
							<li style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>0){?>none<?}?>;">
							<!-- =========================== 역량평가대상 ============================== -->
								<div class="uk-grid" data-uk-grid-margin>
									<div class="uk-width-1-1">
										<div class="uk-overflow-container">
											<table class="uk-table uk-table-align-vertical">
												<thead>
													<tr>
														<th style="width:8%;" nowrap>No</th>
														<th nowrap><?=$이름[$LangID]?></th>
														<th nowrap><?=$아이디[$LangID]?></th>
														<th nowrap><?=$관계[$LangID]?></th>
														<th nowrap><?=$직급_직책[$LangID]?></th>
														<th nowrap><?=$직무군[$LangID]?></th>
														<th nowrap><?=$직무[$LangID]?></th>
														<th nowrap><?=$소속[$LangID]?></th>
														<th nowrap><?=$삭제[$LangID]?></th>
													</tr>
												</thead>
												<tbody>
													
													<?php
													$Sql_5 = "
															select 
																A.*,
																B.MemberName,
																B.MemberLoginID,
																ifnull(C.Hr_OrganPositionName, '-') as Hr_OrganPositionName,
																ifnull(E.Hr_OrganTask2Name, '-') as Hr_OrganTask2Name,
																ifnull(F.Hr_OrganTask1Name, '-') as Hr_OrganTask1Name,

																ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=G.Hr_OrganLevel1ID), '') as Hr_OrganLevelName1, 
																ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=G.Hr_OrganLevel2ID), '') as Hr_OrganLevelName2,
																ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=G.Hr_OrganLevel3ID), '') as Hr_OrganLevelName3,
																ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=G.Hr_OrganLevel4ID), '') as Hr_OrganLevelName4

															from Hr_EvaluationCompetencyMembers A 
																inner join Members B on A.Hr_EvaluationCompetencyMemberID=B.MemberID 
																left outer join Hr_OrganLevelTaskMembers C on A.Hr_EvaluationCompetencyMemberID=C.MemberID 
																left outer join Hr_OrganLevels D on C.Hr_OrganLevelID=D.Hr_OrganLevelID
																left outer join Hr_OrganTask2 E on C.Hr_OrganTask2ID=E.Hr_OrganTask2ID
																left outer join Hr_OrganTask1 F on E.Hr_OrganTask1ID=F.Hr_OrganTask1ID
																left outer join Hr_OrganLevels G on C.Hr_OrganLevelID=G.Hr_OrganLevelID 
															where A.MemberID=:MemberID order by A.Hr_EvaluationCompetencyMemberRegDateTime asc";//." limit $StartRowNum, $PageListNum";
													$Stmt_5 = $DbConn->prepare($Sql_5);
													$Stmt_5->bindParam(':MemberID', $MemberID);
													$Stmt_5->execute();
													$Stmt_5->setFetchMode(PDO::FETCH_ASSOC);


													$ListCount = 1;
													while($Row_5 = $Stmt_5->fetch()) {
														$Hr_EvaluationCompetencyMemberID = $Row_5["Hr_EvaluationCompetencyMemberID"];
														$Hr_EvaluationCompetencyMemberType = $Row_5["Hr_EvaluationCompetencyMemberType"];

														$Hr_EvaluationCompetencyMemberName = $Row_5["MemberName"];
														$Hr_EvaluationCompetencyMemberLoginID = $Row_5["MemberLoginID"];

														$Hr_OrganPositionName = $Row_5["Hr_OrganPositionName"];
														$Hr_OrganTask2Name = $Row_5["Hr_OrganTask2Name"];
														$Hr_OrganTask1Name = $Row_5["Hr_OrganTask1Name"];

														$Db_Hr_OrganLevelName1 = $Row_5["Hr_OrganLevelName1"];
														$Db_Hr_OrganLevelName2 = $Row_5["Hr_OrganLevelName2"];
														$Db_Hr_OrganLevelName3 = $Row_5["Hr_OrganLevelName3"];
														$Db_Hr_OrganLevelName4 = $Row_5["Hr_OrganLevelName4"];

														
														if ($Hr_EvaluationCompetencyMemberType==1){
															$Str_Hr_EvaluationCompetencyMemberType = $부하[$LangID];
														}else if ($Hr_EvaluationCompetencyMemberType==2){
															$Str_Hr_EvaluationCompetencyMemberType = $동료[$LangID];
														}else if ($Hr_EvaluationCompetencyMemberType==3){
															$Str_Hr_EvaluationCompetencyMemberType = $상사[$LangID];
														}else if ($Hr_EvaluationCompetencyMemberType==4){
															$Str_Hr_EvaluationCompetencyMemberType = $고객[$LangID];
														}else if ($Hr_EvaluationCompetencyMemberType==5){
															$Str_Hr_EvaluationCompetencyMemberType = $본인[$LangID];
														}


														$Str_Db_Hr_OrganLevelName = $Db_Hr_OrganLevelName1;
														if ($Db_Hr_OrganLevelName2!=""){
															$Str_Db_Hr_OrganLevelName .= " > " . $Db_Hr_OrganLevelName2;
														}
														if ($Db_Hr_OrganLevelName3!=""){
															$Str_Db_Hr_OrganLevelName .= " > " . $Db_Hr_OrganLevelName3;
														}
														if ($Db_Hr_OrganLevelName4!=""){
															$Str_Db_Hr_OrganLevelName .= " > " . $Db_Hr_OrganLevelName4;
														}

														if ($Str_Db_Hr_OrganLevelName==""){
															$Str_Db_Hr_OrganLevelName = "-";
														}

													?>
													<tr>
														<td class="uk-text-nowrap uk-table-td-center"><?=$ListCount?></td>
														<td class="uk-text-nowrap uk-table-td-center"><?=$Hr_EvaluationCompetencyMemberName?></td>
														<td class="uk-text-nowrap uk-table-td-center"><?=$Hr_EvaluationCompetencyMemberLoginID?></td>
														<td class="uk-text-nowrap uk-table-td-center"><?=$Str_Hr_EvaluationCompetencyMemberType?></td>
														<td class="uk-text-nowrap uk-table-td-center"><?=$Hr_OrganPositionName?></td>
														<td class="uk-text-nowrap uk-table-td-center"><?=$Hr_OrganTask1Name?></td>
														<td class="uk-text-nowrap uk-table-td-center"><?=$Hr_OrganTask2Name?></td>
														<td class="uk-text-nowrap uk-table-td-center"><?=$Str_Db_Hr_OrganLevelName?></td>
														<td class="uk-text-nowrap uk-table-td-center">
															<a class="md-btn md-btn-success md-btn-mini md-btn-wave-light" href="javascript:DeleteStaffCompetencyMember(<?=$MemberID?>, <?=$Hr_EvaluationCompetencyMemberID?>)"><?=$삭제[$LangID]?></a>
														</td>
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
											<a type="button" href="javascript:OpenStaffCompetencyMemberForm(<?=$MemberID?>,'')" class="md-btn md-btn-primary"><?=$신규등록[$LangID]?></a>
										</div>

									</div>
								</div>
							
							<!-- =========================== 역량평가대상 ============================== -->
							</li>
							<?
							}
							?>

						</ul>
					</div>
				</div>
			</div>
			<div class="uk-width-large-3-10">
				<div class="md-card">
					<div class="md-card-content">
						<h3 class="heading_c uk-margin-medium-bottom"><?=$기타설정[$LangID]?></h3>
						<div class="uk-form-row" style="display:none;">
							<input type="checkbox" id="StaffView" name="StaffView" value="1" <?php if ($StaffView==1) { echo "checked";}?> data-switchery/>
							<label for="StaffView" class="inline-label"><?=$활성화[$LangID]?></label>
						</div>
						<hr class="md-hr" style="display:none;">

						<div class="uk-form-row">
							<input type="checkbox" id="StaffState" name="StaffState" value="1" <?php if ($StaffState==1) { echo "checked";}?> data-switchery/>
							<label for="StaffState" class="inline-label"><?=$활동중[$LangID]?></label>
						</div>
						<hr class="md-hr">
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
                document.getElementById('StaffZip').value = data.zonecode;
                document.getElementById("StaffAddr1").value = addr;
                // 커서를 상세주소 필드로 이동한다.
                document.getElementById("StaffAddr2").focus();

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


//================ 이메일 =============
function EnNewEmail(){
	document.RegForm.CheckedEmail.value = "0";
	document.getElementById('BtnCheckEmail').style.display = "inline";
}



function CheckEmail(){
    var StaffEmail_1 = $.trim($('#StaffEmail_1').val());
	var StaffEmail_2 = $.trim($('#StaffEmail_2').val());

    if (StaffEmail_1 == "" || StaffEmail_2 == "") {
        alert('이메일을 입력하세요.');
        document.RegForm.CheckedEmail.value = "0";
	} else {
        url = "ajax_check_email.php";

		//location.href = url + "?MemberEmail_1="+StaffEmail_1+"&MemberEmail_2="+StaffEmail_2+"&MemberID=<?=$MemberID?>";
        $.ajax(url, {
            data: {
                MemberEmail_1: StaffEmail_1,
				MemberEmail_2: StaffEmail_2,
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



function SetEmailName(){
	StaffEmail_3 = document.RegForm.StaffEmail_3.value;
	if (StaffEmail_3==""){
		document.RegForm.StaffEmail_2.value = "";
		document.RegForm.StaffEmail_2.readOnly = false;
	}else{
		document.RegForm.StaffEmail_2.value = StaffEmail_3;
		document.RegForm.StaffEmail_2.readOnly = true;
	}

	EnNewEmail();
}
//================ 이메일 =============


function FormSubmit(){
	obj = document.RegForm.FranchiseID;
	if (obj.value==""){
		UIkit.modal.alert("소속 프랜차이즈를 선택하세요.");
		obj.focus();
		return;
	}

	obj = document.RegForm.StaffManageMent;
	if (obj.value==""){
		UIkit.modal.alert("관리부서를 선택하세요.");
		obj.focus();
		return;
	}

	obj = document.RegForm.StaffName;
	if (obj.value==""){
		UIkit.modal.alert("직원명을 입력하세요.");
		obj.focus();
		return;
	}

	obj = document.RegForm.StaffNickName;
	if (obj.value==""){
		UIkit.modal.alert("직원 닉네임을 입력하세요.");
		obj.focus();
		return;
	}

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
			
			if (obj.value.length<4){
				UIkit.modal.alert("비밀번호는 4자 이상 입력하세요.");
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

		if (obj.value.length<4){
			UIkit.modal.alert("비밀번호는 4자 이상 입력하세요.");
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


	<?
	if ($_LINK_ADMIN_LEVEL_ID_==0){
	?>

		obj = document.RegForm.Hr_OrganLevelID;
		if (obj.value=="0"){
			UIkit.modal.alert("소속부서를 선택하세요.");
			obj.focus();
			return;
		}

		obj = document.RegForm.Hr_OrganTask2ID;
		if (obj.value=="0"){
			UIkit.modal.alert("직무를 선택하세요.");
			obj.focus();
			return;
		}

		obj = document.RegForm.Hr_OrganPositionName;
		if (obj.value==""){
			UIkit.modal.alert("직급/직책을 입력하세요.");
			obj.focus();
			return;
		}

		obj = document.RegForm.Hr_OrganLevel;
		if (obj.value=="0"){
			UIkit.modal.alert("권한을 입력하세요.");
			obj.focus();
			return;
		}

	<?
	}
	?>

	UIkit.modal.confirm(
		'저장 하시겠습니까?', 
		function(){ 
			document.RegForm.action = "staff_action.php";
			document.RegForm.submit();
		}
	);

}


//======================================================== 역량평가 ========================================================
function OpenStaffCompetencyMemberForm(MemberID, Hr_EvaluationCompetencyMemberID){
	openurl = "hr_evaluation_competency_member_form.php?MemberID="+MemberID+"&Hr_EvaluationCompetencyMemberID="+Hr_EvaluationCompetencyMemberID;
	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "850"
		,maxHeight: "750"
		,title:""
		,iframe:true 
		,scrolling:true
		,onClosed:function(){location.href = "staff_form.php?ListParam=<?=$ListParam?>&StaffID=<?=$StaffID?>&PageTabID=3";}
		//,onComplete:function(){alert(1);}
	}); 
}

function DeleteStaffCompetencyMember(MemberID, Hr_EvaluationCompetencyMemberID){

	UIkit.modal.confirm(
		'삭제 하시겠습니까?', 
		function(){ 

			url = "hr_ajax_set_evaluation_competency_member_delete.php";

			//location.href = url + "?MemberID="+MemberID+"&Hr_EvaluationCompetencyMemberID="+Hr_EvaluationCompetencyMemberID;
			$.ajax(url, {
				data: {
					MemberID: MemberID,
					Hr_EvaluationCompetencyMemberID: Hr_EvaluationCompetencyMemberID
				},
				success: function (data) {
					location.href = "staff_form.php?ListParam=<?=$ListParam?>&StaffID=<?=$StaffID?>&PageTabID=3";
				},
				error: function () {
					alert('Error while contacting server, please try again');
				}
			});


		}
	);
}

function Ch_Hr_OrganTask1ID(Hr_OrganTask1ID, Hr_OrganTask2ID){
	
	Hr_OrganLevelID = document.RegForm.Hr_OrganLevelID.value;

	if (Hr_OrganLevelID=="0"){
		alert("먼저 소속 부서를 선택해 주세요.");
		document.RegForm.Hr_OrganTask1ID.value ="0";
	}else{
		url = "hr_ajax_get_organ_task_2_id.php";
		//window.open(url + "?BookVideoBookGroupID="+BookVideoBookGroupID);
		$.ajax(url, {
			data: {
				Hr_OrganLevelID: Hr_OrganLevelID,
				Hr_OrganTask1ID: Hr_OrganTask1ID,
				Hr_OrganTask2ID: Hr_OrganTask2ID
			},
			success: function (data) {

				Hr_OrganLevel = data.Hr_OrganLevel;
				document.RegForm.Hr_OrganLevel.value = Hr_OrganLevel;


				ArrOption = data.Hr_OrganTask2IDs.split("{{|}}");
				SelBoxInitOption('Hr_OrganTask2ID');

				SelBoxAddOption( 'Hr_OrganTask2ID', '선택', "0", "");
				for (ii=2 ; ii<=ArrOption.length-1 ; ii++ ){
					ArrOptionText     = ArrOption[ii].split("{|}")[0];
					ArrOptionValue    = ArrOption[ii].split("{|}")[1];
					ArrOptionSelected = ArrOption[ii].split("{|}")[2];
					SelBoxAddOption( 'Hr_OrganTask2ID', ArrOptionText, ArrOptionValue, ArrOptionSelected );
				}

			},
			error: function () {
				//alert('오류가 발생했습니다. 다시 시도해 주세요.');
			}
		});
	}

}
//======================================================== 역량평가 ========================================================
</script>

<script>
/** ===================================== 기본함수 ===================================== **/
// Option객체를 생성해서 Return
function SelBoxCreateOption( text, value, selected )
{
	var oOption = document.createElement("OPTION"); // Option 객체를 생성
	oOption.text = text; // Text(Keyword)를 입력
	oOption.value = value; // Value를 입력
	if (selected=="selected"){
		oOption.selected = true;
	}
	return oOption;
}

// SelectBox의 Option을 초기화
function SelBoxInitOption( ObjId ){
	var SelectObj = document.getElementById( ObjId );
	if ( SelectObj == null ) return; // 객체가 존재하지 않으면 취소

	SelectObj.options.length = 0; // 길이를 0으로 하면 초기화
}

// Option을 추가
function SelBoxAddOption( ObjId, text, value, selected ){
	var SelectObj = document.getElementById( ObjId );

	SelectObj.add( SelBoxCreateOption( text , value, selected ) );
	text     = "";
	value    = "";
	selected = "";
}
/** ===================================== 기본함수 ===================================== **/
</script>




<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>