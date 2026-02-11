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
$MainMenuID = 13;
$SubMenuID = 1303;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
?>



<?php
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$EduCenterID = isset($_REQUEST["EduCenterID"]) ? $_REQUEST["EduCenterID"] : "";
$PageTabID = isset($_REQUEST["PageTabID"]) ? $_REQUEST["PageTabID"] : "";
if ($PageTabID==""){
	$PageTabID = "1";
}

if ($EduCenterID!=""){

	$Sql = "
			select 
					A.*,
					AES_DECRYPT(UNHEX(A.EduCenterPhone1),:EncryptionKey) as DecEduCenterPhone1,
					AES_DECRYPT(UNHEX(A.EduCenterPhone2),:EncryptionKey) as DecEduCenterPhone2,
					AES_DECRYPT(UNHEX(A.EduCenterPhone3),:EncryptionKey) as DecEduCenterPhone3,
					AES_DECRYPT(UNHEX(A.EduCenterEmail),:EncryptionKey) as DecEduCenterEmail
			from EduCenters A 
			where A.EduCenterID=:EduCenterID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':EduCenterID', $EduCenterID);
	$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$FranchiseID = $Row["FranchiseID"];
	$EduCenterName = $Row["EduCenterName"];
	$EduCenterManagerName = $Row["EduCenterManagerName"];
	//================ 전화번호 / 이메일 =============
	$EduCenterPhone1 = $Row["DecEduCenterPhone1"];
	$EduCenterPhone2 = $Row["DecEduCenterPhone2"];
	$EduCenterPhone3 = $Row["DecEduCenterPhone3"];
	$EduCenterEmail = $Row["DecEduCenterEmail"];
	//================ 전화번호 / 이메일 =============
	$EduCenterZip = $Row["EduCenterZip"];
	$EduCenterAddr1 = $Row["EduCenterAddr1"];
	$EduCenterAddr2 = $Row["EduCenterAddr2"];
	$EduCenterIntroText = $Row["EduCenterIntroText"];
	$EduCenterHoliday0 = $Row["EduCenterHoliday0"];
	$EduCenterHoliday1 = $Row["EduCenterHoliday1"];
	$EduCenterHoliday2 = $Row["EduCenterHoliday2"];
	$EduCenterHoliday3 = $Row["EduCenterHoliday3"];
	$EduCenterHoliday4 = $Row["EduCenterHoliday4"];
	$EduCenterHoliday5 = $Row["EduCenterHoliday5"];
	$EduCenterHoliday6 = $Row["EduCenterHoliday6"];
	$EduCenterState = $Row["EduCenterState"];
	$EduCenterView = $Row["EduCenterView"];

	$EduCenterStartHour = $Row["EduCenterStartHour"];
	$EduCenterEndHour = $Row["EduCenterEndHour"];


	$EduCenterHoliday[0] = $Row["EduCenterHoliday0"];
	$EduCenterHoliday[1] = $Row["EduCenterHoliday1"];
	$EduCenterHoliday[2] = $Row["EduCenterHoliday2"];
	$EduCenterHoliday[3] = $Row["EduCenterHoliday3"];
	$EduCenterHoliday[4] = $Row["EduCenterHoliday4"];
	$EduCenterHoliday[5] = $Row["EduCenterHoliday5"];
	$EduCenterHoliday[6] = $Row["EduCenterHoliday6"];


}else{
	$FranchiseID = "";
	$EduCenterName = "";
	$EduCenterManagerName = "";
	//================ 전화번호 / 이메일 =============
	$EduCenterPhone1 = "--";
	$EduCenterPhone2 = "--";
	$EduCenterPhone3 = "--";
	$EduCenterEmail = "@";
	//================ 전화번호 / 이메일 =============
	$EduCenterZip = "";
	$EduCenterAddr1 = "";
	$EduCenterAddr2 = "";
	$EduCenterIntroText = "";
	$EduCenterHoliday0 = 0;
	$EduCenterHoliday1 = 0;
	$EduCenterHoliday2 = 0;
	$EduCenterHoliday3 = 0;
	$EduCenterHoliday4 = 0;
	$EduCenterHoliday5 = 0;
	$EduCenterHoliday6 = 0;
	$EduCenterState = 1;
	$EduCenterView = 1;

	$EduCenterStartHour = 14;
	$EduCenterEndHour = 22;
}


//================ 전화번호 / 이메일 =============
$ArrEduCenterPhone1 = explode("-", $EduCenterPhone1);
$ArrEduCenterPhone2 = explode("-", $EduCenterPhone2);
$ArrEduCenterPhone3 = explode("-", $EduCenterPhone3);
$ArrEduCenterEmail = explode("@", $EduCenterEmail);

$EduCenterPhone1_1 = $ArrEduCenterPhone1[0];
$EduCenterPhone1_2 = $ArrEduCenterPhone1[1];
$EduCenterPhone1_3 = $ArrEduCenterPhone1[2];

$EduCenterPhone2_1 = $ArrEduCenterPhone2[0];
$EduCenterPhone2_2 = $ArrEduCenterPhone2[1];
$EduCenterPhone2_3 = $ArrEduCenterPhone2[2];

$EduCenterPhone3_1 = $ArrEduCenterPhone3[0];
$EduCenterPhone3_2 = $ArrEduCenterPhone3[1];
$EduCenterPhone3_3 = $ArrEduCenterPhone3[2];

$EduCenterEmail_1 = $ArrEduCenterEmail[0];
$EduCenterEmail_2 = $ArrEduCenterEmail[1];
//================ 전화번호 / 이메일 =============


?>


<div id="page_content">
	<div id="page_content_inner">

		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="EduCenterID" value="<?=$EduCenterID?>">
		<input type="hidden" name="ListParam" value="<?=$ListParam?>">
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
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$EduCenterName?></span><span class="sub-heading" id="user_edit_position">교육센터정보</span></h2>
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
						
						<ul id="user_edit_tabs" class="uk-tab" data-uk-tab="{connect:'#user_edit_tabs_content'}" style="display:<?if ($EduCenterID==""){?>none<?}?>;">
							<li <?if ($PageTabID=="1"){?>class="uk-active"<?}?>><a href="#"><?=$교육센터정보[$LangID]?></a></li>
							<li <?if ($PageTabID=="5"){?>class="uk-active"<?}?>><a href="#"><?=$운영시간관리[$LangID]?></a></li>
							<li <?if ($PageTabID=="3"){?>class="uk-active"<?}?> onclick="SelectPageTab(3);"><a href="#"><?=$휴일관리[$LangID]?></a></li>
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
															echo "<optgroup label=\"대표지사(운영중)\">";
														}else if ($SelectFranchiseState==2){
															echo "<optgroup label=\"대표지사(미운영)\">";
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
										<?=$교육센터명_및_관리자[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-1-2">
											<label for="EduCenterName"><?=$교육센터명[$LangID]?></label>
											<input type="text" id="EduCenterName" name="EduCenterName" value="<?=$EduCenterName?>" class="md-input label-fixed"/>
										</div>
										<div class="uk-width-medium-1-2">
											<label for="EduCenterManagerName"><?=$관리자[$LangID]?></label>
											<input type="text" id="EduCenterManagerName" name="EduCenterManagerName" value="<?=$EduCenterManagerName?>" class="md-input label-fixed"/>
										</div>
									</div>
									
									<h3 class="full_width_in_card heading_c">
										<?=$연락처[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-1-2 uk-input-group">
											<label for="EduCenterPhone1"><?=$전화번호[$LangID]?></label>
											<div class="md-input label-fixed" style="padding-top:10px;margin-bottom:10px;">
												<select name="EduCenterPhone1_1" class="Select" style="width:15%;height:30px;">
													<option value="010" <?if ($EduCenterPhone1_1=="010") {?>selected<?}?>>010</option>
													<option value="011" <?if ($EduCenterPhone1_1=="011") {?>selected<?}?>>011</option>
													<option value="016" <?if ($EduCenterPhone1_1=="016") {?>selected<?}?>>016</option>
													<option value="017" <?if ($EduCenterPhone1_1=="017") {?>selected<?}?>>017</option>
													<option value="018" <?if ($EduCenterPhone1_1=="018") {?>selected<?}?>>018</option>
													<option value="019" <?if ($EduCenterPhone1_1=="019") {?>selected<?}?>>019</option>
													<option value="070" <?if ($EduCenterPhone1_1=="070") {?>selected<?}?>>070</option>
													<option value="02"  <?if ($EduCenterPhone1_1=="02")  {?>selected<?}?>>02</option>
													<option value="031" <?if ($EduCenterPhone1_1=="031") {?>selected<?}?>>031</option>
													<option value="032" <?if ($EduCenterPhone1_1=="032") {?>selected<?}?>>032</option>
													<option value="033" <?if ($EduCenterPhone1_1=="033") {?>selected<?}?>>033</option>
													<option value="041" <?if ($EduCenterPhone1_1=="041") {?>selected<?}?>>041</option>
													<option value="042" <?if ($EduCenterPhone1_1=="042") {?>selected<?}?>>042</option>
													<option value="043" <?if ($EduCenterPhone1_1=="043") {?>selected<?}?>>043</option>
													<option value="044" <?if ($EduCenterPhone1_1=="044") {?>selected<?}?>>044</option>
													<option value="049" <?if ($EduCenterPhone1_1=="049") {?>selected<?}?>>049</option>
													<option value="051" <?if ($EduCenterPhone1_1=="051") {?>selected<?}?>>051</option>
													<option value="052" <?if ($EduCenterPhone1_1=="052") {?>selected<?}?>>052</option>
													<option value="053" <?if ($EduCenterPhone1_1=="053") {?>selected<?}?>>053</option>
													<option value="054" <?if ($EduCenterPhone1_1=="054") {?>selected<?}?>>054</option>
													<option value="055" <?if ($EduCenterPhone1_1=="055") {?>selected<?}?>>055</option>
													<option value="061" <?if ($EduCenterPhone1_1=="061") {?>selected<?}?>>061</option>
													<option value="062" <?if ($EduCenterPhone1_1=="062") {?>selected<?}?>>062</option>
													<option value="063" <?if ($EduCenterPhone1_1=="063") {?>selected<?}?>>063</option>
													<option value="064" <?if ($EduCenterPhone1_1=="064") {?>selected<?}?>>064</option>
													<option value="0505" <?if ($CenterPhone1_1=="0505") {?>selected<?}?>>0505</option>
													<option value="0502" <?if ($CenterPhone1_1=="0502") {?>selected<?}?>>0502</option>
												</select>
												<input type="text" name="EduCenterPhone1_2" style="width:15%;height:30px;padding-left:10px;" placeholder="" value="<?=$EduCenterPhone1_2?>" class="allownumericwithoutdecimal" maxlength="4"> 
												<input type="text" name="EduCenterPhone1_3" style="width:15%;height:30px;padding-left:10px;"  placeholder="" value="<?=$EduCenterPhone1_3?>" class="allownumericwithoutdecimal" maxlength="4">
											</div>
										</div>
										<div class="uk-width-medium-1-2 uk-input-group">
											<label for="CenterPhone2"><?=$휴대폰[$LangID]?></label>
											<div class="md-input label-fixed" style="padding-top:10px;margin-bottom:10px;">
												<select name="EduCenterPhone2_1" class="Select" style="width:15%;height:30px;">
													<option value="010" <?if ($EduCenterPhone2_1=="010") {?>selected<?}?>>010</option>
													<option value="011" <?if ($EduCenterPhone2_1=="011") {?>selected<?}?>>011</option>
													<option value="016" <?if ($EduCenterPhone2_1=="016") {?>selected<?}?>>016</option>
													<option value="017" <?if ($EduCenterPhone2_1=="017") {?>selected<?}?>>017</option>
													<option value="018" <?if ($EduCenterPhone2_1=="018") {?>selected<?}?>>018</option>
													<option value="019" <?if ($EduCenterPhone2_1=="019") {?>selected<?}?>>019</option>
												</select>
												<input type="text" name="EduCenterPhone2_2" style="width:15%;height:30px;padding-left:10px;" placeholder="" value="<?=$EduCenterPhone2_2?>" class="allownumericwithoutdecimal" maxlength="4"> 
												<input type="text" name="EduCenterPhone2_3" style="width:15%;height:30px;padding-left:10px;"  placeholder="" value="<?=$EduCenterPhone2_3?>" class="allownumericwithoutdecimal" maxlength="4">
											</div>
										</div>
									</div>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-1-2 uk-input-group">
											<label for="CenterPhone3"><?=$팩스번호[$LangID]?></label>
											<div class="md-input label-fixed" style="padding-top:10px;margin-bottom:10px;">
												<select name="EduCenterPhone3_1" class="Select" style="width:15%;height:30px;">
													<option value="02"  <?if ($EduCenterPhone3_1=="02")  {?>selected<?}?>>02</option>
													<option value="031" <?if ($EduCenterPhone3_1=="031") {?>selected<?}?>>031</option>
													<option value="032" <?if ($EduCenterPhone3_1=="032") {?>selected<?}?>>032</option>
													<option value="033" <?if ($EduCenterPhone3_1=="033") {?>selected<?}?>>033</option>
													<option value="041" <?if ($EduCenterPhone3_1=="041") {?>selected<?}?>>041</option>
													<option value="042" <?if ($EduCenterPhone3_1=="042") {?>selected<?}?>>042</option>
													<option value="043" <?if ($EduCenterPhone3_1=="043") {?>selected<?}?>>043</option>
													<option value="044" <?if ($EduCenterPhone3_1=="044") {?>selected<?}?>>044</option>
													<option value="049" <?if ($EduCenterPhone3_1=="049") {?>selected<?}?>>049</option>
													<option value="051" <?if ($EduCenterPhone3_1=="051") {?>selected<?}?>>051</option>
													<option value="052" <?if ($EduCenterPhone3_1=="052") {?>selected<?}?>>052</option>
													<option value="053" <?if ($EduCenterPhone3_1=="053") {?>selected<?}?>>053</option>
													<option value="054" <?if ($EduCenterPhone3_1=="054") {?>selected<?}?>>054</option>
													<option value="055" <?if ($EduCenterPhone3_1=="055") {?>selected<?}?>>055</option>
													<option value="061" <?if ($EduCenterPhone3_1=="061") {?>selected<?}?>>061</option>
													<option value="062" <?if ($EduCenterPhone3_1=="062") {?>selected<?}?>>062</option>
													<option value="063" <?if ($EduCenterPhone3_1=="063") {?>selected<?}?>>063</option>
													<option value="064" <?if ($EduCenterPhone3_1=="064") {?>selected<?}?>>064</option>
													<option value="010" <?if ($EduCenterPhone3_1=="010") {?>selected<?}?>>010</option>
													<option value="011" <?if ($EduCenterPhone3_1=="011") {?>selected<?}?>>011</option>
													<option value="016" <?if ($EduCenterPhone3_1=="016") {?>selected<?}?>>016</option>
													<option value="017" <?if ($EduCenterPhone3_1=="017") {?>selected<?}?>>017</option>
													<option value="018" <?if ($EduCenterPhone3_1=="018") {?>selected<?}?>>018</option>
													<option value="019" <?if ($EduCenterPhone3_1=="019") {?>selected<?}?>>019</option>
													<option value="070" <?if ($EduCenterPhone3_1=="070") {?>selected<?}?>>070</option>
													<option value="0505" <?if ($CenterPhone1_1=="0505") {?>selected<?}?>>0505</option>
													<option value="0502" <?if ($CenterPhone1_1=="0502") {?>selected<?}?>>0502</option>
												</select>
												<input type="text" name="EduCenterPhone3_2" style="width:15%;height:30px;padding-left:10px;" placeholder="" value="<?=$EduCenterPhone3_2?>" class="allownumericwithoutdecimal" maxlength="4"> 
												<input type="text" name="EduCenterPhone3_3" style="width:15%;height:30px;padding-left:10px;"  placeholder="" value="<?=$EduCenterPhone3_3?>" class="allownumericwithoutdecimal" maxlength="4">
											</div>
										</div>
										<div class="uk-width-medium-1-2 uk-input-group">
											<label for="EduCenterEmail"><?=$이메일[$LangID]?></label>
											<div class="md-input label-fixed" style="padding-top:10px;margin-bottom:10px;">
												<input type="text" name="EduCenterEmail_1" id="EduCenterEmail_1" style="width:20%;height:30px;padding-left:10px;" value="<?=$EduCenterEmail_1?>"> 
												<div style="display:inline-block;width:20px;margin-top:8px;">@</div> 
												<input type="text" name="EduCenterEmail_2" id="EduCenterEmail_2" style="width:20%;height:30px;padding-left:10px;" value="<?=$EduCenterEmail_2?>">
												<select name="EduCenterEmail_3" class="Select" style="width:22%;height:30px;margin-bottom:0px;" onchange="SetEmailName()">
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
									<h3 class="full_width_in_card heading_c">
										<?=$주소[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-3-10 uk-input-group">
											<label for="EduCenterZip"><?=$우편번호[$LangID]?></label>
											<input type="text" id="EduCenterZip" name="EduCenterZip" value="<?=$EduCenterZip?>" class="md-input label-fixed" />
											<span class="uk-input-group-addon"><a class="md-btn" href="javascript:ExecDaumPostcode();"><?=$검색[$LangID]?></a></span>

										</div>
										<div class="uk-width-medium-3-10 uk-input-group">
											<label for="EduCenterAddr1"><?=$주소[$LangID]?></label>
											<input type="text" id="EduCenterAddr1" name="EduCenterAddr1" value="<?=$EduCenterAddr1?>" class="md-input label-fixed" />
										</div>
										<div class="uk-width-medium-4-10 uk-input-group">
											<label for="EduCenterAddr2"><?=$상세주소[$LangID]?></label>
											<input type="text" id="EduCenterAddr2" name="EduCenterAddr2" value="<?=$EduCenterAddr2?>" class="md-input label-fixed" />
										</div>
									</div>

									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-1-1">
											<label for="EduCenterIntroText"><?=$메모[$LangID]?></label>
											<textarea class="md-input" name="EduCenterIntroText" id="EduCenterIntroText" cols="30" rows="4"><?=$EduCenterIntroText?></textarea>
										</div>
									</div>


								</div>
							</li>
							<?if ($EduCenterID!=""){?>
							<li>
								
								<h3 class="full_width_in_card heading_c">
									<?=$운영시간[$LangID]?>
								</h3>
								<div class="uk-grid" data-uk-grid-margin style="padding-left:50px;">
									<span class="uk-form-help-block" style="font-style:normal;"><?=$시작[$LangID]?></span>
									<div class="uk-width-medium-1-10 uk-input-group">
										<select name="EduCenterStartHour" onchange="ChEduCenterHour()" data-md-selectize>
											<?for ($ii=0;$ii<=24;$ii++){?>
											<option value="<?=$ii?>" <?if ($EduCenterStartHour==$ii) {?>selected<?}?>><?=substr("0".$ii,-2)?> 시</option>	
											<?}?>
										</select> 
									</div>
									<div class="uk-width-medium-1-10 uk-input-group">
									
									</div>
									<span class="uk-form-help-block" style="font-style:normal;"><?=$종료[$LangID]?></span>
									<div class="uk-width-medium-1-10 uk-input-group">
										<select name="EduCenterEndHour" onchange="ChEduCenterHour()" data-md-selectize>
											<?for ($ii=0;$ii<=24;$ii++){?>
											<option value="<?=$ii?>" <?if ($EduCenterEndHour==$ii) {?>selected<?}?>><?=substr("0".$ii,-2)?> 시</option>	
											<?}?>
										</select> 
									</div>
									<div class="uk-width-medium-6-10 uk-input-group" id="DivEduCenterHourMsg">
										※ 운영시간을 수정하면 자동 적용 됩니다.
									</div>
								</div>
								<script>
								function ChEduCenterHour(){
									url = "ajax_set_edu_center_hour.php";
									EduCenterStartHour = document.RegForm.EduCenterStartHour.value;
									EduCenterEndHour = document.RegForm.EduCenterEndHour.value;
									//location.href = url + "?NewID="+NewID;
									$.ajax(url, {
										data: {
											EduCenterStartHour: EduCenterStartHour,
											EduCenterEndHour: EduCenterEndHour,
											EduCenterID: <?=$EduCenterID?>
										},
										success: function (data) {
											json_data = data;
											location.href = "edu_center_form.php?ListParam=<?=$ListParam?>&EduCenterID=<?=$EduCenterID?>&PageTabID=5";
										},
										error: function () {

										}
									});
								}
								</script>


								<h3 class="full_width_in_card heading_c">
									<?=$휴식시간관리[$LangID]?>
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
										for ($ii=$EduCenterStartHour;$ii<=$EduCenterEndHour-1;$ii++){
											for ($jj=0;$jj<=50;$jj=$jj+10){
												for ($kk=0;$kk<=6;$kk++){
													if ($EduCenterHoliday[$kk]==0) {
														$EduCenterBreak[$kk][$ii][$jj] = 1;
													}
												}
											}
										}

								
								
										$Sql2 = "select 
														A.* 
												from EduCenterBreakTimes A 
												where A.EduCenterID=$EduCenterID and A.EduCenterBreakTimeState=1 
												order by A.EduCenterBreakTimeWeek asc, A.EduCenterBreakTimeHour asc, A.EduCenterBreakTimeMinute asc";
										$Stmt2 = $DbConn->prepare($Sql2);
										$Stmt2->execute();
										$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
										
										

										while($Row2 = $Stmt2->fetch()) {
											$EduCenterBreakTimeWeek = $Row2["EduCenterBreakTimeWeek"];
											$EduCenterBreakTimeHour = $Row2["EduCenterBreakTimeHour"];
											$EduCenterBreakTimeMinute = $Row2["EduCenterBreakTimeMinute"];
											$EduCenterBreakTimeType = $Row2["EduCenterBreakTimeType"];
											
											$EduCenterBreak[$EduCenterBreakTimeWeek][$EduCenterBreakTimeHour][$EduCenterBreakTimeMinute] = $EduCenterBreakTimeType;
										}

								
								
										for ($ii=$EduCenterStartHour;$ii<=$EduCenterEndHour-1;$ii++){
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
														if ($EduCenterBreak[$kk][$ii][$jj]==1) {
															$BgColor = "#FBFBFB";
														}else if ($EduCenterBreak[$kk][$ii][$jj]==2) {
															$BgColor = "#FFCC00";
														}else if ($EduCenterBreak[$kk][$ii][$jj]==3) {
															$BgColor = "#CC9933";
														}else if ($EduCenterBreak[$kk][$ii][$jj]==4) {
															$BgColor = "#CC6666";
														}
												?>
												<td style="padding-bottom:0px;text-align:center;background-color:<?=$BgColor?>;" id="DivEduCenterBreak_<?=$kk?>_<?=$ii?>_<?=$jj?>">
													
														<input type="radio" id="EduCenterBreak_<?=$kk?>_<?=$ii?>_<?=$jj?>_1" name="EduCenterBreak_<?=$kk?>_<?=$ii?>_<?=$jj?>" <?php if ($EduCenterBreak[$kk][$ii][$jj]==1) { echo "checked";}?> onclick="CheckEduCenterBreak(<?=$kk?>,<?=$ii?>,<?=$jj?>,1);" value="1"/>
														<label for="EduCenterBreak_<?=$kk?>_<?=$ii?>_<?=$jj?>_1" class="inline-label">수업</label>
														<input type="radio" id="EduCenterBreak_<?=$kk?>_<?=$ii?>_<?=$jj?>_2" name="EduCenterBreak_<?=$kk?>_<?=$ii?>_<?=$jj?>" <?php if ($EduCenterBreak[$kk][$ii][$jj]==2) { echo "checked";}?> onclick="CheckEduCenterBreak(<?=$kk?>,<?=$ii?>,<?=$jj?>,2);" value="2"/>
														<label for="EduCenterBreak_<?=$kk?>_<?=$ii?>_<?=$jj?>_2" class="inline-label">식사</label>
														<br>
														<input type="radio" id="EduCenterBreak_<?=$kk?>_<?=$ii?>_<?=$jj?>_3" name="EduCenterBreak_<?=$kk?>_<?=$ii?>_<?=$jj?>" <?php if ($EduCenterBreak[$kk][$ii][$jj]==3) { echo "checked";}?> onclick="CheckEduCenterBreak(<?=$kk?>,<?=$ii?>,<?=$jj?>,3);" value="3"/>
														<label for="EduCenterBreak_<?=$kk?>_<?=$ii?>_<?=$jj?>_3" class="inline-label">휴식</label>
														<input type="radio" id="EduCenterBreak_<?=$kk?>_<?=$ii?>_<?=$jj?>_4" name="EduCenterBreak_<?=$kk?>_<?=$ii?>_<?=$jj?>" <?php if ($EduCenterBreak[$kk][$ii][$jj]==4) { echo "checked";}?> onclick="CheckEduCenterBreak(<?=$kk?>,<?=$ii?>,<?=$jj?>,4);" value="4"/>
														<label for="EduCenterBreak_<?=$kk?>_<?=$ii?>_<?=$jj?>_4" class="inline-label">블락</label>
													
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
								function CheckEduCenterBreak(EduCenterBreakTimeWeek,EduCenterBreakTimeHour,EduCenterBreakTimeMinute,EduCenterBreakTimeType){
									url = "ajax_set_edu_center_break_time.php";

									//location.href = url + "?NewID="+NewID;
									$.ajax(url, {
										data: {
											EduCenterBreakTimeWeek: EduCenterBreakTimeWeek,
											EduCenterBreakTimeHour: EduCenterBreakTimeHour,
											EduCenterBreakTimeMinute: EduCenterBreakTimeMinute,
											EduCenterBreakTimeType: EduCenterBreakTimeType,
											EduCenterID: <?=$EduCenterID?>
										},
										success: function (data) {
											json_data = data;

											BgColor = "#FBFBFB";
											if (EduCenterBreakTimeType==1) {
												BgColor = "#FBFBFB";
											}else if (EduCenterBreakTimeType==2) {
												BgColor = "#FFCC00";
											}else if (EduCenterBreakTimeType==3) {
												BgColor = "#CC9933";
											}else if (EduCenterBreakTimeType==4) {
												BgColor = "#CC6666";
											}
											document.getElementById("DivEduCenterBreak_"+EduCenterBreakTimeWeek+"_"+EduCenterBreakTimeHour+"_"+EduCenterBreakTimeMinute).style.backgroundColor = BgColor;
										},
										error: function () {

										}
									});
								}
								</script>

								
								
							</li>
							<li>
								<h3 class="full_width_in_card heading_c">
									<?=$정기휴일[$LangID]?>
								</h3>
								<div class="uk-grid" data-uk-grid-margin>
									<div class="uk-width-medium-1-1 uk-input-group" style="padding-left:50px;">
										<span class="icheck-inline">
											<input type="checkbox" name="EduCenterHoliday0" id="EduCenterHoliday0" value="1" class="check_input" onclick="ChEduCenterHoliday(0,this)" <?if ($EduCenterHoliday0==1){?>checked<?}?>/>
											<label for="EduCenterHoliday0" class="check_label" style="color:#ff0000;"><span class="check_bullet"></span><?=$일[$LangID]?></label>
										</span>
										<span class="icheck-inline">
											<input type="checkbox" name="EduCenterHoliday1" id="EduCenterHoliday1" value="1" class="check_input" onclick="ChEduCenterHoliday(1,this)" <?if ($EduCenterHoliday1==1){?>checked<?}?>/>
											<label for="EduCenterHoliday1" class="check_label"><span class="check_bullet"></span><?=$월[$LangID]?></label>
										</span>
										<span class="icheck-inline">
											<input type="checkbox" name="EduCenterHoliday2" id="EduCenterHoliday2" value="1" class="check_input" onclick="ChEduCenterHoliday(2,this)" <?if ($EduCenterHoliday2==1){?>checked<?}?>/>
											<label for="EduCenterHoliday2" class="check_label"><span class="check_bullet"></span><?=$화[$LangID]?></label>
										</span>
										<span class="icheck-inline">
											<input type="checkbox" name="EduCenterHoliday3" id="EduCenterHoliday3" value="1" class="check_input" onclick="ChEduCenterHoliday(3,this)" <?if ($EduCenterHoliday3==1){?>checked<?}?>/>
											<label for="EduCenterHoliday3" class="check_label"><span class="check_bullet"></span><?=$수[$LangID]?></label>
										</span>
										<span class="icheck-inline">
											<input type="checkbox" name="EduCenterHoliday4" id="EduCenterHoliday4" value="1" class="check_input" onclick="ChEduCenterHoliday(4,this)" <?if ($EduCenterHoliday4==1){?>checked<?}?>/>
											<label for="EduCenterHoliday4" class="check_label"><span class="check_bullet"></span><?=$목[$LangID]?></label>
										</span>
										<span class="icheck-inline">
											<input type="checkbox" name="EduCenterHoliday5" id="EduCenterHoliday5" value="1" class="check_input" onclick="ChEduCenterHoliday(5,this)" <?if ($EduCenterHoliday5==1){?>checked<?}?>/>
											<label for="EduCenterHoliday5" class="check_label"><span class="check_bullet"></span><?=$금[$LangID]?></label>
										</span>
										<span class="icheck-inline">
											<input type="checkbox" name="EduCenterHoliday6" id="EduCenterHoliday6" value="1" class="check_input" onclick="ChEduCenterHoliday(6,this)" <?if ($EduCenterHoliday6==1){?>checked<?}?>/>
											<label for="EduCenterHoliday6" class="check_label" style="color:#0000ff;"><span class="check_bullet"></span><?=$토[$LangID]?></label>
										</span>
									</div>
									<div class="uk-width-medium-1-1 uk-input-group" style="padding-left:50px;">
									※ 정기휴일을 수정하면 자동 적용 됩니다.
									</div>
								</div>
								<script>
								function ChEduCenterHoliday(WeekDayNum, obj){
									url = "ajax_set_edu_center_holiday.php";
									if (obj.checked){
										WeekDayCheked = 1;
									}else{
										WeekDayCheked = 0;
									}


									//location.href = url + "?NewID="+NewID;
									$.ajax(url, {
										data: {
											WeekDayNum: WeekDayNum,
											WeekDayCheked: WeekDayCheked,
											EduCenterID: <?=$EduCenterID?>
										},
										success: function (data) {
											json_data = data;
											location.href = "edu_center_form.php?ListParam=<?=$ListParam?>&EduCenterID=<?=$EduCenterID?>&PageTabID=3";
										},
										error: function () {

										}
									});

								}
								</script>




								<h3 class="full_width_in_card heading_c">
									<?=$비정기_휴일[$LangID]?>
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
							<input type="checkbox" id="EduCenterView" name="EduCenterView" value="1" <?php if ($EduCenterView==1) { echo "checked";}?> data-switchery/>
							<label for="EduCenterView" class="inline-label"><?=$활성화[$LangID]?></label>
						</div>
						<hr class="md-hr" style="display:none;">

						<div class="uk-form-row">
							<input type="checkbox" id="EduCenterState" name="EduCenterState" value="1" <?php if ($EduCenterState==1) { echo "checked";}?> data-switchery/>
							<label for="EduCenterState" class="inline-label"><?=$운영중[$LangID]?></label>
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

<?if ($EduCenterID!=""){?>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
			  integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
			integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
			crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
			integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
			crossorigin="anonymous"></script>
	<!-- Project Files -->
	<link rel="stylesheet" href="./js/bootstrap_year_calendar/jquery.bootstrap.year.calendar.css">
	<script src="./js/bootstrap_year_calendar/jquery.bootstrap.year.calendar.js"></script>
	<script>
	function appendToCalendar() {
		<?
		$Sql = "
				select 
					A.*
				from EduCenterHolidays A 
					where A.EduCenterID=$EduCenterID and EduCenterHolidayState<>0
				order by A.EduCenterHolidayDate asc";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		while($Row = $Stmt->fetch()) {
			$EduCenterHolidayDate = $Row["EduCenterHolidayDate"];
			$ArrEduCenterHolidayDate = explode("-",$EduCenterHolidayDate);
		?>
		$('.calendar').calendar('appendText', '■', <?=$ArrEduCenterHolidayDate[0]?>, <?=intval($ArrEduCenterHolidayDate[1])?>, <?=intval($ArrEduCenterHolidayDate[2])?>);
		<?
		}
		?>
		
	}

	 $('.calendar').on('jqyc.dayChoose', function (event) {
		var choosenYear = $(this).data('year');
		var choosenMonth = $(this).data('month');
		var choosenDay = $(this).data('day-of-month');
		date = choosenYear+"-"+choosenMonth+"-"+choosenDay;

		openurl = "edu_center_holiday_form.php?EduCenterID=<?=$EduCenterID?>&ListParam=<?=$ListParam?>&EduCenterHolidayDate="+date;
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



<!-- ==============  only this page js ============== -->
<!-- ==============  common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ==============  common.js ============== -->


<!-- iOS에서는 position:fixed 버그가 있음, 적용하는 사이트에 맞게 position:absolute 등을 이용하여 top,left값 조정 필요 -->
<div id="layer" style="display:none;position:fixed;overflow:hidden;z-index:1;-webkit-overflow-scrolling:touch;">
<img src="//t1.daumcdn.net/postcode/resource/images/close.png" id="btnCloseLayer" style="cursor:pointer;position:absolute;right:-3px;top:-3px;z-index:1" onclick="closeDaumPostcode()" alt="<?=$닫기_버튼[$LangID]?>">
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
                document.getElementById('EduCenterZip').value = data.zonecode;
                document.getElementById("EduCenterAddr1").value = addr;
                // 커서를 상세주소 필드로 이동한다.
                document.getElementById("EduCenterAddr2").focus();

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

//================ 이메일 =============
function SetEmailName(){
	EduCenterEmail_3 = document.RegForm.EduCenterEmail_3.value;
	if (EduCenterEmail_3==""){
		document.RegForm.EduCenterEmail_2.value = "";
		document.RegForm.EduCenterEmail_2.readOnly = false;
	}else{
		document.RegForm.EduCenterEmail_2.value = EduCenterEmail_3;
		document.RegForm.EduCenterEmail_2.readOnly = true;
	}
}
//================ 이메일 =============


function FormSubmit(){
	obj = document.RegForm.FranchiseID;
	if (obj.value==""){
		UIkit.modal.alert('<?=$프랜차이즈를_선택하세요[$LangID]?>');
		obj.focus();
		return;
	}

	obj = document.RegForm.EduCenterName;
	if (obj.value==""){
		UIkit.modal.alert("<?=$교육센터명을_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}

	obj = document.RegForm.EduCenterManagerName;
	if (obj.value==""){
		UIkit.modal.alert("<?=$교육센터_관리자명을_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}

	<?if ($EduCenterID!=""){?>
		obj1 = document.RegForm.EduCenterStartHour;
		obj2 = document.RegForm.EduCenterEndHour;

		if (Number(obj1.value)>=Number(obj2.value)){
			UIkit.modal.alert("<?=$종료시간을_시작시간보다_늦게_설정해_주세요[$LangID]?>");
			obj.focus();
			return;
		}
	<?}?>

	UIkit.modal.confirm(
		'<?=$저장_하시겠습니까[$LangID]?>?', 
		function(){ 
			document.RegForm.action = "edu_center_action.php";
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