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
$SubMenuID = 1204;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
?>



<?php
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$ManagerID = isset($_REQUEST["ManagerID"]) ? $_REQUEST["ManagerID"] : "";

if ($ManagerID!=""){

	$Sql = "
			select 
					A.*,
					AES_DECRYPT(UNHEX(A.ManagerPhone1),:EncryptionKey) as DecManagerPhone1,
					AES_DECRYPT(UNHEX(A.ManagerPhone2),:EncryptionKey) as DecManagerPhone2,
					AES_DECRYPT(UNHEX(A.ManagerPhone3),:EncryptionKey) as DecManagerPhone3,
					AES_DECRYPT(UNHEX(A.ManagerEmail),:EncryptionKey) as DecManagerEmail,
					C.MemberID,
					C.MemberLoginID,
					C.MemberLoginPW,
					C.MemberCiTelephone,
					C.MemberLanguageID
			from Managers A 
					inner join Members C on A.ManagerID=C.ManagerID and C.MemberLevelID=5 
			where A.ManagerID=:ManagerID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ManagerID', $ManagerID);
	$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$FranchiseID = $Row["FranchiseID"];
	$ManagerName = $Row["ManagerName"];
	$ManagerCompanyName = $Row["ManagerCompanyName"];
	//================ 전화번호 / 이메일 =============
	$ManagerPhone1 = $Row["DecManagerPhone1"];
	$ManagerPhone2 = $Row["DecManagerPhone2"];
	$ManagerPhone3 = $Row["DecManagerPhone3"];
	$ManagerEmail = $Row["DecManagerEmail"];
	//================ 전화번호 / 이메일 =============
	$ManagerZip = $Row["ManagerZip"];
	$ManagerAddr1 = $Row["ManagerAddr1"];
	$ManagerAddr2 = $Row["ManagerAddr2"];
	$ManagerIntroText = $Row["ManagerIntroText"];
	$ManagerState = $Row["ManagerState"];
	$ManagerView = $Row["ManagerView"];

	//Members 
	$MemberID = $Row["MemberID"];
	$MemberLoginID = $Row["MemberLoginID"];
	$MemberLoginPW = $Row["MemberLoginPW"];
	$MemberCiTelephone = $Row["MemberCiTelephone"];
	$MemberLanguageID = $Row["MemberLanguageID"];	
	$CheckedID = 1;
	$CheckedEmail = 1;

}else{
	$FranchiseID = "";
	$ManagerName = "";
	$ManagerCompanyName = "";
	//================ 전화번호 / 이메일 =============
	$ManagerPhone1 = "--";
	$ManagerPhone2 = "--";
	$ManagerPhone3 = "--";
	$ManagerEmail = "@";
	//================ 전화번호 / 이메일 =============
	$ManagerZip = "";
	$ManagerAddr1 = "";
	$ManagerAddr2 = "";
	$ManagerIntroText = "";
	$ManagerState = 1;
	$ManagerView = 1;

	//Members 
	$MemberID = "";
	$MemberLoginID = "";
	$MemberLoginPW = "";
	$MemberCiTelephone = "";
	$MemberLanguageID = "";
	$CheckedID = 0;
	$CheckedEmail = 0;
}

//================ 전화번호 / 이메일 =============
$ArrManagerPhone1 = explode("-", $ManagerPhone1);
$ArrManagerPhone2 = explode("-", $ManagerPhone2);
$ArrManagerPhone3 = explode("-", $ManagerPhone3);
$ArrManagerEmail = explode("@", $ManagerEmail);

$ManagerPhone1_1 = $ArrManagerPhone1[0];
$ManagerPhone1_2 = $ArrManagerPhone1[1];
$ManagerPhone1_3 = $ArrManagerPhone1[2];

$ManagerPhone2_1 = $ArrManagerPhone2[0];
$ManagerPhone2_2 = $ArrManagerPhone2[1];
$ManagerPhone2_3 = $ArrManagerPhone2[2];

$ManagerPhone3_1 = $ArrManagerPhone3[0];
$ManagerPhone3_2 = $ArrManagerPhone3[1];
$ManagerPhone3_3 = $ArrManagerPhone3[2];

$ManagerEmail_1 = $ArrManagerEmail[0];
$ManagerEmail_2 = $ArrManagerEmail[1];
//================ 전화번호 / 이메일 =============

$MemberLoginNewPW = "";
$MemberLoginNewPW2 = "";
?>


<div id="page_content"> 
	<div id="page_content_inner">

		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="ManagerID" value="<?=$ManagerID?>">
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
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$ManagerName?></span><span class="sub-heading" id="user_edit_position"><?=$영업본부정보[$LangID]?></span></h2>
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
						
						<ul id="user_edit_tabs" class="uk-tab" data-uk-tab="{connect:'#user_edit_tabs_content'}" style="display:none;">
							<li class="uk-active"><a href="#">Basic</a></li>
							<li><a href="#">Groups</a></li>
							<li><a href="#">Todo</a></li>
						</ul>
						<ul id="user_edit_tabs_content" class="uk-switcher uk-margin">
							<li>
								<div class="uk-margin-top">
									<h3 class="full_width_in_card heading_c">
										<?=$프랜차이즈[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-4-10">
											<select id="FranchiseID" name="FranchiseID" class="uk-width-1-1" data-md-select2 data-allow-clear="true" data-placeholder="<?=$프랜차이즈선택[$LangID]?>" style="width:100%;"/>
												<option value=""></option>
												<?
												$Sql2 = "select 
																A.*
														from Franchises A 
														where A.FranchiseState<>0 
														order by A.FranchiseState asc, A.FranchiseName asc";
												
												$Stmt2 = $DbConn->prepare($Sql2);
												$Stmt2->execute();
												$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
												
												$OldSelectFranchiseState = -1;
												while($Row2 = $Stmt2->fetch()) {
													$SelectFranchiseID = $Row2["FranchiseID"];
													$SelectFranchiseName = $Row2["FranchiseName"];
													$SelectFranchiseState = $Row2["FranchiseState"];
												
													if ($OldSelectFranchiseState!=$SelectFranchiseState){
														if ($OldSelectFranchiseState!=-1){
															echo "</optgroup>";
														}
														
														if ($SelectFranchiseState==1){
															echo "<optgroup label=\"프랜차이즈(운영중)\">";
														}else if ($SelectFranchiseState==2){
															echo "<optgroup label=\"프랜차이즈(미운영)\">";
														}
													}
													$OldSelectFranchiseState = $SelectFranchiseState;
												?>

												<option value="<?=$SelectFranchiseID?>" <?if ($FranchiseID==$SelectFranchiseID){?>selected<?}?>><?=$SelectFranchiseName?></option>
												<?
												}
												$Stmt2 = null;
												?>
											</select>
										</div>
										<div class="uk-width-medium-6-10">
										</div>
									</div>
									<h3 class="full_width_in_card heading_c">
										<?=$영업본부명_및_소속기관[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-1-2">
											<label for="ManagerName"><?=$영업본부명[$LangID]?></label>
											<input type="text" id="ManagerName" name="ManagerName" value="<?=$ManagerName?>" class="md-input label-fixed"/>
										</div>
										<div class="uk-width-medium-1-2">
											<label for="ManagerCompanyName"><?=$소속기관[$LangID]?></label>
											<input type="text" id="ManagerCompanyName" name="ManagerCompanyName" value="<?=$ManagerCompanyName?>" class="md-input label-fixed"/>
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
									</div>
									<h3 class="full_width_in_card heading_c">
										<?=$연락처[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-1-2 uk-input-group">
											<label for="ManagerPhone1"><?=$전화번호[$LangID]?></label>
											<div class="md-input label-fixed" style="padding-top:10px;margin-bottom:10px;">
												<select name="ManagerPhone1_1" class="Select" style="width:15%;height:30px;">
													<option value="010" <?if ($ManagerPhone1_1=="010") {?>selected<?}?>>010</option>
													<option value="011" <?if ($ManagerPhone1_1=="011") {?>selected<?}?>>011</option>
													<option value="016" <?if ($ManagerPhone1_1=="016") {?>selected<?}?>>016</option>
													<option value="017" <?if ($ManagerPhone1_1=="017") {?>selected<?}?>>017</option>
													<option value="018" <?if ($ManagerPhone1_1=="018") {?>selected<?}?>>018</option>
													<option value="019" <?if ($ManagerPhone1_1=="019") {?>selected<?}?>>019</option>
													<option value="070" <?if ($ManagerPhone1_1=="070") {?>selected<?}?>>070</option>
													<option value="02"  <?if ($ManagerPhone1_1=="02")  {?>selected<?}?>>02</option>
													<option value="031" <?if ($ManagerPhone1_1=="031") {?>selected<?}?>>031</option>
													<option value="032" <?if ($ManagerPhone1_1=="032") {?>selected<?}?>>032</option>
													<option value="033" <?if ($ManagerPhone1_1=="033") {?>selected<?}?>>033</option>
													<option value="041" <?if ($ManagerPhone1_1=="041") {?>selected<?}?>>041</option>
													<option value="042" <?if ($ManagerPhone1_1=="042") {?>selected<?}?>>042</option>
													<option value="043" <?if ($ManagerPhone1_1=="043") {?>selected<?}?>>043</option>
													<option value="044" <?if ($ManagerPhone1_1=="044") {?>selected<?}?>>044</option>
													<option value="049" <?if ($ManagerPhone1_1=="049") {?>selected<?}?>>049</option>
													<option value="051" <?if ($ManagerPhone1_1=="051") {?>selected<?}?>>051</option>
													<option value="052" <?if ($ManagerPhone1_1=="052") {?>selected<?}?>>052</option>
													<option value="053" <?if ($ManagerPhone1_1=="053") {?>selected<?}?>>053</option>
													<option value="054" <?if ($ManagerPhone1_1=="054") {?>selected<?}?>>054</option>
													<option value="055" <?if ($ManagerPhone1_1=="055") {?>selected<?}?>>055</option>
													<option value="061" <?if ($ManagerPhone1_1=="061") {?>selected<?}?>>061</option>
													<option value="062" <?if ($ManagerPhone1_1=="062") {?>selected<?}?>>062</option>
													<option value="063" <?if ($ManagerPhone1_1=="063") {?>selected<?}?>>063</option>
													<option value="064" <?if ($ManagerPhone1_1=="064") {?>selected<?}?>>064</option>
													<option value="0505" <?if ($CenterPhone1_1=="0505") {?>selected<?}?>>0505</option>
													<option value="0502" <?if ($CenterPhone1_1=="0502") {?>selected<?}?>>0502</option>
												</select>
												<input type="text" name="ManagerPhone1_2" style="width:15%;height:30px;padding-left:10px;" placeholder="" value="<?=$ManagerPhone1_2?>" class="allownumericwithoutdecimal" maxlength="4"> 
												<input type="text" name="ManagerPhone1_3" style="width:15%;height:30px;padding-left:10px;"  placeholder="" value="<?=$ManagerPhone1_3?>" class="allownumericwithoutdecimal" maxlength="4">
											</div>
										</div>
										<div class="uk-width-medium-1-2 uk-input-group">
											<label for="CenterPhone2"><?=$휴대폰[$LangID]?></label>
											<div class="md-input label-fixed" style="padding-top:10px;margin-bottom:10px;">
												<select name="ManagerPhone2_1" class="Select" style="width:15%;height:30px;">
													<option value="010" <?if ($ManagerPhone2_1=="010") {?>selected<?}?>>010</option>
													<option value="011" <?if ($ManagerPhone2_1=="011") {?>selected<?}?>>011</option>
													<option value="016" <?if ($ManagerPhone2_1=="016") {?>selected<?}?>>016</option>
													<option value="017" <?if ($ManagerPhone2_1=="017") {?>selected<?}?>>017</option>
													<option value="018" <?if ($ManagerPhone2_1=="018") {?>selected<?}?>>018</option>
													<option value="019" <?if ($ManagerPhone2_1=="019") {?>selected<?}?>>019</option>
												</select>
												<input type="text" name="ManagerPhone2_2" style="width:15%;height:30px;padding-left:10px;" placeholder="" value="<?=$ManagerPhone2_2?>" class="allownumericwithoutdecimal" maxlength="4"> 
												<input type="text" name="ManagerPhone2_3" style="width:15%;height:30px;padding-left:10px;"  placeholder="" value="<?=$ManagerPhone2_3?>" class="allownumericwithoutdecimal" maxlength="4">
											</div>
										</div>
									</div>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-1-2 uk-input-group">
											<label for="CenterPhone3"><?=$팩스번호[$LangID]?></label>
											<div class="md-input label-fixed" style="padding-top:10px;margin-bottom:10px;">
												<select name="ManagerPhone3_1" class="Select" style="width:15%;height:30px;">
													<option value="02"  <?if ($ManagerPhone3_1=="02")  {?>selected<?}?>>02</option>
													<option value="031" <?if ($ManagerPhone3_1=="031") {?>selected<?}?>>031</option>
													<option value="032" <?if ($ManagerPhone3_1=="032") {?>selected<?}?>>032</option>
													<option value="033" <?if ($ManagerPhone3_1=="033") {?>selected<?}?>>033</option>
													<option value="041" <?if ($ManagerPhone3_1=="041") {?>selected<?}?>>041</option>
													<option value="042" <?if ($ManagerPhone3_1=="042") {?>selected<?}?>>042</option>
													<option value="043" <?if ($ManagerPhone3_1=="043") {?>selected<?}?>>043</option>
													<option value="044" <?if ($ManagerPhone3_1=="044") {?>selected<?}?>>044</option>
													<option value="049" <?if ($ManagerPhone3_1=="049") {?>selected<?}?>>049</option>
													<option value="051" <?if ($ManagerPhone3_1=="051") {?>selected<?}?>>051</option>
													<option value="052" <?if ($ManagerPhone3_1=="052") {?>selected<?}?>>052</option>
													<option value="053" <?if ($ManagerPhone3_1=="053") {?>selected<?}?>>053</option>
													<option value="054" <?if ($ManagerPhone3_1=="054") {?>selected<?}?>>054</option>
													<option value="055" <?if ($ManagerPhone3_1=="055") {?>selected<?}?>>055</option>
													<option value="061" <?if ($ManagerPhone3_1=="061") {?>selected<?}?>>061</option>
													<option value="062" <?if ($ManagerPhone3_1=="062") {?>selected<?}?>>062</option>
													<option value="063" <?if ($ManagerPhone3_1=="063") {?>selected<?}?>>063</option>
													<option value="064" <?if ($ManagerPhone3_1=="064") {?>selected<?}?>>064</option>
													<option value="010" <?if ($ManagerPhone3_1=="010") {?>selected<?}?>>010</option>
													<option value="011" <?if ($ManagerPhone3_1=="011") {?>selected<?}?>>011</option>
													<option value="016" <?if ($ManagerPhone3_1=="016") {?>selected<?}?>>016</option>
													<option value="017" <?if ($ManagerPhone3_1=="017") {?>selected<?}?>>017</option>
													<option value="018" <?if ($ManagerPhone3_1=="018") {?>selected<?}?>>018</option>
													<option value="019" <?if ($ManagerPhone3_1=="019") {?>selected<?}?>>019</option>
													<option value="070" <?if ($ManagerPhone3_1=="070") {?>selected<?}?>>070</option>
													<option value="0505" <?if ($CenterPhone1_1=="0505") {?>selected<?}?>>0505</option>
													<option value="0502" <?if ($CenterPhone1_1=="0502") {?>selected<?}?>>0502</option>
												</select>
												<input type="text" name="ManagerPhone3_2" style="width:15%;height:30px;padding-left:10px;" placeholder="" value="<?=$ManagerPhone3_2?>" class="allownumericwithoutdecimal" maxlength="4"> 
												<input type="text" name="ManagerPhone3_3" style="width:15%;height:30px;padding-left:10px;"  placeholder="" value="<?=$ManagerPhone3_3?>" class="allownumericwithoutdecimal" maxlength="4">
											</div>
										</div>
										<div class="uk-width-medium-1-2 uk-input-group">
											<label for="ManagerEmail"><?=$이메일[$LangID]?></label>
											<div class="md-input label-fixed" style="padding-top:10px;margin-bottom:10px;">
												<input type="text" name="ManagerEmail_1" id="ManagerEmail_1" style="width:20%;height:30px;padding-left:10px;" value="<?=$ManagerEmail_1?>" onkeyup="EnNewEmail()"> 
												<div style="display:inline-block;width:20px;margin-top:8px;">@</div> 
												<input type="text" name="ManagerEmail_2" id="ManagerEmail_2" style="width:20%;height:30px;padding-left:10px;" value="<?=$ManagerEmail_2?>" onkeyup="EnNewEmail()">
												<select name="ManagerEmail_3" class="Select" style="width:22%;height:30px;margin-bottom:0px;" onchange="SetEmailName()">
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
											<label for="ManagerZip"><?=$우편번호[$LangID]?></label>
											<input type="text" id="ManagerZip" name="ManagerZip" value="<?=$ManagerZip?>" class="md-input label-fixed" />
											<span class="uk-input-group-addon"><a class="md-btn" href="javascript:ExecDaumPostcode();"><?=$검색[$LangID]?></a></span>

										</div>
										<div class="uk-width-medium-3-10 uk-input-group">
											<label for="ManagerAddr1"><?=$주소[$LangID]?></label>
											<input type="text" id="ManagerAddr1" name="ManagerAddr1" value="<?=$ManagerAddr1?>" class="md-input label-fixed" />
										</div>
										<div class="uk-width-medium-4-10 uk-input-group">
											<label for="ManagerAddr2"><?=$상세주소[$LangID]?></label>
											<input type="text" id="ManagerAddr2" name="ManagerAddr2" value="<?=$ManagerAddr2?>" class="md-input label-fixed" />
										</div>
									</div>

									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-1-1">
											<label for="ManagerIntroText"><?=$메모[$LangID]?></label>
											<textarea class="md-input" name="ManagerIntroText" id="ManagerIntroText" cols="30" rows="4"><?=$ManagerIntroText?></textarea>
										</div>
									</div>


								</div>
							</li>
							<li>

							</li>
							<li>

							</li>
						</ul>
					</div>
				</div>
			</div>
			<div class="uk-width-large-3-10">
				<div class="md-card">
					<div class="md-card-content">
						<h3 class="heading_c uk-margin-medium-bottom"><?=$기타설정[$LangID]?></h3>
						<div class="uk-form-row" style="display:none;">
							<input type="checkbox" id="ManagerView" name="ManagerView" value="1" <?php if ($ManagerView==1) { echo "checked";}?> data-switchery/>
							<label for="ManagerView" class="inline-label"><?=$활성화[$LangID]?></label>
						</div>
						<hr class="md-hr" style="display:none;">

						<div class="uk-form-row">
							<input type="checkbox" id="ManagerState" name="ManagerState" value="1" <?php if ($ManagerState==1) { echo "checked";}?> data-switchery/>
							<label for="ManagerState" class="inline-label"><?=$운영중[$LangID]?></label>
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
                document.getElementById('ManagerZip').value = data.zonecode;
                document.getElementById("ManagerAddr1").value = addr;
                // 커서를 상세주소 필드로 이동한다.
                document.getElementById("ManagerAddr2").focus();

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
    } else if (NewID.length<4)  {
		UIkit.modal.alert("아이디는 4자 이상 입력하세요.");
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
    var ManagerEmail_1 = $.trim($('#ManagerEmail_1').val());
	var ManagerEmail_2 = $.trim($('#ManagerEmail_2').val());

    if (ManagerEmail_1 == "" || ManagerEmail_2 == "") {
        alert('이메일을 입력하세요.');
        document.RegForm.CheckedEmail.value = "0";
	} else {
        url = "ajax_check_email.php";

		//location.href = url + "?MemberEmail_1="+ManagerEmail_1+"&MemberEmail_2="+ManagerEmail_2+"&MemberID=<?=$MemberID?>";
        $.ajax(url, {
            data: {
                MemberEmail_1: ManagerEmail_1,
				MemberEmail_2: ManagerEmail_2,
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
	ManagerEmail_3 = document.RegForm.ManagerEmail_3.value;
	if (ManagerEmail_3==""){
		document.RegForm.ManagerEmail_2.value = "";
		document.RegForm.ManagerEmail_2.readOnly = false;
	}else{
		document.RegForm.ManagerEmail_2.value = ManagerEmail_3;
		document.RegForm.ManagerEmail_2.readOnly = true;
	}

	EnNewEmail();
}
//================ 이메일 =============


function FormSubmit(){

	obj = document.RegForm.FranchiseID;
	if (obj.value==""){
		UIkit.modal.alert("프랜차이즈를 선택하세요.");
		obj.focus();
		return;
	}

	obj = document.RegForm.ManagerName;
	if (obj.value==""){
		UIkit.modal.alert("영업본부명을 입력하세요.");
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

	UIkit.modal.confirm(
		'저장 하시겠습니까?', 
		function(){ 
			document.RegForm.action = "manager_action.php";
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