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
$SubMenuID = 1205;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
?>



<?php
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$OnlineSiteID = isset($_REQUEST["OnlineSiteID"]) ? $_REQUEST["OnlineSiteID"] : "";

if ($OnlineSiteID!=""){

	$Sql = "
			select 
					A.*,
					AES_DECRYPT(UNHEX(A.OnlineSitePhone1),:EncryptionKey) as DecOnlineSitePhone1,
					AES_DECRYPT(UNHEX(A.OnlineSitePhone2),:EncryptionKey) as DecOnlineSitePhone2,
					AES_DECRYPT(UNHEX(A.OnlineSitePhone3),:EncryptionKey) as DecOnlineSitePhone3,
					AES_DECRYPT(UNHEX(A.OnlineSiteEmail),:EncryptionKey) as DecOnlineSiteEmail
			from OnlineSites A 
			where A.OnlineSiteID=:OnlineSiteID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':OnlineSiteID', $OnlineSiteID);
	$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$FranchiseID = $Row["FranchiseID"];
	$OnlineSiteName = $Row["OnlineSiteName"];
	$OnlineSiteManagerName = $Row["OnlineSiteManagerName"];
	$OnlineSiteDomain = $Row["OnlineSiteDomain"];
	//================ 전화번호 / 이메일 =============
	$OnlineSitePhone1 = $Row["DecOnlineSitePhone1"];
	$OnlineSitePhone2 = $Row["DecOnlineSitePhone2"];
	$OnlineSitePhone3 = $Row["DecOnlineSitePhone3"];
	$OnlineSiteEmail = $Row["DecOnlineSiteEmail"];
	//================ 전화번호 / 이메일 =============

	$OnlineSiteSincerityPayStartDate = $Row["OnlineSiteSincerityPayStartDate"];
	$OnlineSiteSincerityPayEndDate = $Row["OnlineSiteSincerityPayEndDate"];

	$OnlineSiteMemberRegPoint = $Row["OnlineSiteMemberRegPoint"];
	$OnlineSiteMemberLoginPoint = $Row["OnlineSiteMemberLoginPoint"];
	$OnlineSitePaymentPointRatio = $Row["OnlineSitePaymentPointRatio"];
	$OnlineSiteStudyPoint = $Row["OnlineSiteStudyPoint"];
	$OnlineSitePreStudyPoint = $Row["OnlineSitePreStudyPoint"];
	$OnlineSiteReStudyPoint = $Row["OnlineSiteReStudyPoint"];

	$OnlineSitePgCardFeeRatio = $Row["OnlineSitePgCardFeeRatio"];
	$OnlineSitePgDirectFeePrice = $Row["OnlineSitePgDirectFeePrice"];
	$OnlineSitePgDirectFeeRatio = $Row["OnlineSitePgDirectFeeRatio"];
	$OnlineSitePgVBankFeePrice = $Row["OnlineSitePgVBankFeePrice"];

	$OnlineSiteShipPrice = $Row["OnlineSiteShipPrice"];

	$OnlineSiteGuideVideoType = $Row["OnlineSiteGuideVideoType"];
	$OnlineSiteGuideVideoCode = $Row["OnlineSiteGuideVideoCode"];
	$OnlineSiteShVersion = $Row["OnlineSiteShVersion"];
	$OnlineSiteShVersionDemo = $Row["OnlineSiteShVersionDemo"];

	$OnlineSiteZip = $Row["OnlineSiteZip"];
	$OnlineSiteAddr1 = $Row["OnlineSiteAddr1"];
	$OnlineSiteAddr2 = $Row["OnlineSiteAddr2"];
	$OnlineSiteSmsID = $Row["OnlineSiteSmsID"];
	$OnlineSiteSmsPW = $Row["OnlineSiteSmsPW"];
	$OnlineSiteSendNumber = $Row["OnlineSiteSendNumber"];
	$OnlineSiteReceiveNumber = $Row["OnlineSiteReceiveNumber"];
	$OnlineSiteIntroText = $Row["OnlineSiteIntroText"];
	$OnlineSiteState = $Row["OnlineSiteState"];
	$OnlineSiteView = $Row["OnlineSiteView"];

}else{
	$FranchiseID = "";
	$OnlineSiteName = "";
	$OnlineSiteManagerName = "";
	$OnlineSiteDomain = "";
	//================ 전화번호 / 이메일 =============
	$OnlineSitePhone1 = "--";
	$OnlineSitePhone2 = "--";
	$OnlineSitePhone3 = "--";
	$OnlineSiteEmail = "@";
	//================ 전화번호 / 이메일 =============

	$OnlineSiteSincerityPayStartDate = 0;
	$OnlineSiteSincerityPayEndDate = 0;

	$OnlineSiteMemberRegPoint = 0;
	$OnlineSiteMemberLoginPoint = 0;
	$OnlineSitePaymentPointRatio = 0;
	$OnlineSiteStudyPoint = 0;
	$OnlineSitePreStudyPoint = 0;
	$OnlineSiteReStudyPoint = 0;

	$OnlineSitePgCardFeeRatio = 0;
	$OnlineSitePgDirectFeePrice = 0;
	$OnlineSitePgDirectFeeRatio = 0;
	$OnlineSitePgVBankFeePrice = 0;

	$OnlineSiteShipPrice = 0;

	$OnlineSiteGuideVideoType = 1;
	$OnlineSiteGuideVideoCode = "";
	$OnlineSiteShVersion = 1;
	$OnlineSiteShVersionDemo = 1;

	$OnlineSiteZip = "";
	$OnlineSiteAddr1 = "";
	$OnlineSiteAddr2 = "";
	$OnlineSiteSmsID = "";
	$OnlineSiteSmsPW = "";
	$OnlineSiteSendNumber = "";
	$OnlineSiteReceiveNumber = "";
	$OnlineSiteIntroText = "";
	$OnlineSiteState = 1;
	$OnlineSiteView = 1;
}

//================ 전화번호 / 이메일 =============
$ArrOnlineSitePhone1 = explode("-", $OnlineSitePhone1);
$ArrOnlineSitePhone2 = explode("-", $OnlineSitePhone2);
$ArrOnlineSitePhone3 = explode("-", $OnlineSitePhone3);
$ArrOnlineSiteEmail = explode("@", $OnlineSiteEmail);

$OnlineSitePhone1_1 = $ArrOnlineSitePhone1[0];
$OnlineSitePhone1_2 = $ArrOnlineSitePhone1[1];
$OnlineSitePhone1_3 = $ArrOnlineSitePhone1[2];

$OnlineSitePhone2_1 = $ArrOnlineSitePhone2[0];
$OnlineSitePhone2_2 = $ArrOnlineSitePhone2[1];
$OnlineSitePhone2_3 = $ArrOnlineSitePhone2[2];

$OnlineSitePhone3_1 = $ArrOnlineSitePhone3[0];
$OnlineSitePhone3_2 = $ArrOnlineSitePhone3[1];
$OnlineSitePhone3_3 = $ArrOnlineSitePhone3[2];

$OnlineSiteEmail_1 = $ArrOnlineSiteEmail[0];
$OnlineSiteEmail_2 = $ArrOnlineSiteEmail[1];
//================ 전화번호 / 이메일 =============


?>


<div id="page_content">
	<div id="page_content_inner">

		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="OnlineSiteID" value="<?=$OnlineSiteID?>">
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
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$OnlineSiteName?></span><span class="sub-heading" id="user_edit_position"><?=$독립사이트정보[$LangID]?></span></h2>
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
															echo "<optgroup label=\"".$프랜차이즈_운영중[$LangID]."\">";
														}else if ($SelectFranchiseState==2){
															echo "<optgroup label=\"".$프랜차이즈_미운영[$LangID]."\">";
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
										<?=$사이트명_및_관리자[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-4-10">
											<label for="OnlineSiteName"><?=$사이트명[$LangID]?></label>
											<input type="text" id="OnlineSiteName" name="OnlineSiteName" value="<?=$OnlineSiteName?>" class="md-input label-fixed"/>
										</div>
										<div class="uk-width-medium-3-10">
											<label for="OnlineSiteDomain"><?=$도메인[$LangID]?></label>
											<input type="text" id="OnlineSiteDomain" name="OnlineSiteDomain" value="<?=$OnlineSiteDomain?>" class="md-input label-fixed"/>
										</div>
										<div class="uk-width-medium-3-10">
											<label for="OnlineSiteManagerName"><?=$관리자[$LangID]?></label>
											<input type="text" id="OnlineSiteManagerName" name="OnlineSiteManagerName" value="<?=$OnlineSiteManagerName?>" class="md-input label-fixed"/>
										</div>
									</div>
									<h3 class="full_width_in_card heading_c" style="display:none;">
										<?=$사이트_이용_포인트[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin  style="display:none;">
										<div class="uk-width-medium-4-10">
											<label for="OnlineSiteMemberRegPoint"><?=$회원가입_포인트[$LangID]?></label>
											<input type="text" id="OnlineSiteMemberRegPoint" name="OnlineSiteMemberRegPoint" value="<?=$OnlineSiteMemberRegPoint?>" class="md-input label-fixed allownumericwithoutdecimal"/>
										</div>
										<div class="uk-width-medium-3-10">
											<label for="OnlineSiteMemberLoginPoint"><?=$로그인_포인트[$LangID]?></label>
											<input type="text" id="OnlineSiteMemberLoginPoint" name="OnlineSiteMemberLoginPoint" value="<?=$OnlineSiteMemberLoginPoint?>" class="md-input label-fixed allownumericwithoutdecimal"/>
										</div>
										<div class="uk-width-medium-3-10">
											<label for="OnlineSitePaymentPointRatio"><?=$구매_포인트[$LangID]?> (%)</label>
											<input type="text" id="OnlineSitePaymentPointRatio" name="OnlineSitePaymentPointRatio" value="<?=$OnlineSitePaymentPointRatio?>" class="md-input label-fixed allownumericwithdecimal"/>
										</div>
									</div>
									<div class="uk-grid" data-uk-grid-margin  style="display:none;">
										<div class="uk-width-medium-4-10">
											<label for="OnlineSitePreStudyPoint"><?=$예습동영상시청_포인트[$LangID]?></label>
											<input type="text" id="OnlineSitePreStudyPoint" name="OnlineSitePreStudyPoint" value="<?=$OnlineSitePreStudyPoint?>" class="md-input label-fixed allownumericwithoutdecimal"/>
										</div>
										<div class="uk-width-medium-3-10">
											<label for="OnlineSiteStudyPoint"><?=$수업출석_포인트[$LangID]?></label>
											<input type="text" id="OnlineSiteStudyPoint" name="OnlineSiteStudyPoint" value="<?=$OnlineSiteStudyPoint?>" class="md-input label-fixed allownumericwithoutdecimal"/>
										</div>
										<div class="uk-width-medium-3-10">
											<label for="OnlineSiteReStudyPoint"><?=$복습퀴즈풀기_포인트[$LangID]?></label>
											<input type="text" id="OnlineSiteReStudyPoint" name="OnlineSiteReStudyPoint" value="<?=$OnlineSiteReStudyPoint?>" class="md-input label-fixed allownumericwithoutdecimal"/>
										</div>
									</div>
									<h3 class="full_width_in_card heading_c">
										<?=$성실납부_기한_설정[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-3-10">
											<select id="OnlineSiteSincerityPayStartDate" name="OnlineSiteSincerityPayStartDate" class="uk-width-1-1" data-md-select2 data-allow-clear="true" data-placeholder="성실납부 시작일" style="width:100%;"/>
												<option value="0"></option>
												<?
													$Start = 1;
													$End = 31;
													for(;$Start<=$End;$Start++) {
														$StrDay = $Start." 일";
												?>

												<option value="<?=$Start?>" <?if ($Start==$OnlineSiteSincerityPayStartDate){?>selected<?}?>><?=$StrDay?></option>
												<?
												}
												?>
											</select>
										</div>
										<div class="uk-width-medium-3-10">
											<select id="OnlineSiteSincerityPayEndDate" name="OnlineSiteSincerityPayEndDate" class="uk-width-1-1" data-md-select2 data-allow-clear="true" data-placeholder="성실납부 종료일" style="width:100%;"/>
												<option value="0"></option>
												<?
													$Start = 1;
													$End = 31;
													for(;$Start<=$End;$Start++) {
														$StrDay = $Start." 일";
												?>

												<option value="<?=$Start?>" <?if ($Start==$OnlineSiteSincerityPayEndDate){?>selected<?}?>><?=$StrDay?></option>
												<?
												}
												$Stmt2 = null;
												?>
											</select>
										</div>
									</div>
									<h3 class="full_width_in_card heading_c">
										<?=$전자결제_수수료[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-4-10">
											<label for="OnlineSitePgCardFeeRatio"><?=$카드수수료_퍼센트[$LangID]?></label>
											<input type="text" id="OnlineSitePgCardFeeRatio" name="OnlineSitePgCardFeeRatio" value="<?=$OnlineSitePgCardFeeRatio?>" class="md-input label-fixed allownumericwithdecimal"/>
										</div>
										<div class="uk-width-medium-3-10">
											<label for="OnlineSitePgDirectFeePrice"><?=$실시간이체[$LangID]?>(원, 1만원 이하)</label>
											<input type="text" id="OnlineSitePgDirectFeePrice" name="OnlineSitePgDirectFeePrice" value="<?=$OnlineSitePgDirectFeePrice?>" class="md-input label-fixed allownumericwithoutdecimal"/>
										</div>
										<div class="uk-width-medium-3-10">
											<label for="OnlineSitePgDirectFeePrice"><?=$실시간이체[$LangID]?>(%, 1만원 이상)</label>
											<input type="text" id="OnlineSitePgDirectFeeRatio" name="OnlineSitePgDirectFeeRatio" value="<?=$OnlineSitePgDirectFeeRatio?>" class="md-input label-fixed allownumericwithdecimal"/>
										</div>
										<div class="uk-width-medium-3-10">
											<label for="OnlineSitePgVBankFeePrice"><?=$가상계좌[$LangID]?>(원)</label>
											<input type="text" id="OnlineSitePgVBankFeePrice" name="OnlineSitePgVBankFeePrice" value="<?=$OnlineSitePgVBankFeePrice?>" class="md-input label-fixed allownumericwithoutdecimal"/>
										</div>
										<div class="uk-width-medium-3-10">
											<label for="OnlineSiteShipPrice"><?=$교재배송료[$LangID]?>(원)</label>
											<input type="text" id="OnlineSiteShipPrice" name="OnlineSiteShipPrice" value="<?=$OnlineSiteShipPrice?>" class="md-input label-fixed allownumericwithoutdecimal"/>
										</div>
									</div>
									<h3 class="full_width_in_card heading_c">
										<?=$학습_가이드_영상[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-2-10 uk-form-row">
											<span class="icheck-inline">
												<input type="radio" id="OnlineSiteGuideVideoType1" name="OnlineSiteGuideVideoType" value="1" <?php if ($OnlineSiteGuideVideoType==1) { echo "checked";}?> data-md-icheck/>
												<label for="OnlineSiteGuideVideoType1" class="inline-label">Youtube</label>
											</span>
											<span class="icheck-inline">
												<input type="radio" id="OnlineSiteGuideVideoType2" name="OnlineSiteGuideVideoType" value="2" <?php if ($OnlineSiteGuideVideoType==2) { echo "checked";}?> data-md-icheck/>
												<label for="OnlineSiteGuideVideoType2" class="inline-label">Vimeo</label>
											</span>
										</div>
										<div class="uk-width-medium-3-10 uk-input-group">
											<label for="OnlineSiteGuideVideoCode"><?=$영상코드[$LangID]?></label>
											<input type="text" id="OnlineSiteGuideVideoCode" name="OnlineSiteGuideVideoCode" value="<?=$OnlineSiteGuideVideoCode?>" class="md-input label-fixed" />
											<span class="uk-input-group-addon"><a class="md-btn" href="javascript:OpenVideoPlayer();"><?=$영상확인[$LangID]?></a></span>
										</div>
										<div class="uk-width-medium-5-10 uk-input-group">
											※ Youtube 또는 Vimeo 코드를 입력하세요.(아래 예제의 <span style='color:#ff0000;'>빨간색</span> 코드 부분)<br>
											※ 예) https://www.youtube.com/watch?v=<span style='color:#ff0000;'>LDPt7XLrbks</span> , https://vimeo.com/<span style='color:#ff0000;'>159328419</span> 
										</div>
									</div>
									<h3 class="full_width_in_card heading_c">
										새하 버전
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-2-10 uk-form-row">
											<span class="icheck-inline">
												<input type="radio" id="OnlineSiteShVersion1" name="OnlineSiteShVersion" value="1" <?php if ($OnlineSiteShVersion==1) { echo "checked";}?> data-md-icheck/>
												<label for="OnlineSiteShVersion1" class="inline-label">신버전</label>
											</span>
											<span class="icheck-inline">
												<input type="radio" id="OnlineSiteShVersion2" name="OnlineSiteShVersion" value="2" <?php if ($OnlineSiteShVersion==2) { echo "checked";}?> data-md-icheck/>
												<label for="OnlineSiteShVersion2" class="inline-label">구버전</label>
											</span>
										</div>
									</div>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-2-10 uk-form-row">
											<span class="icheck-inline">
												<input type="radio" id="OnlineSiteShVersionDemo1" name="OnlineSiteShVersionDemo" value="1" <?php if ($OnlineSiteShVersionDemo==1) { echo "checked";}?> data-md-icheck/>
												<label for="OnlineSiteShVersionDemo1" class="inline-label">DEMO 신버전</label>
											</span>
											<span class="icheck-inline">
												<input type="radio" id="OnlineSiteShVersionDemo2" name="OnlineSiteShVersionDemo" value="2" <?php if ($OnlineSiteShVersionDemo==2) { echo "checked";}?> data-md-icheck/>
												<label for="OnlineSiteShVersionDemo2" class="inline-label">DEMO 구버전</label>
											</span>
										</div>
									</div>
									<h3 class="full_width_in_card heading_c">
										SMS
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-1-2 uk-input-group">
											<label for="OnlineSiteSendNumber"><?=$발신번호[$LangID]?></label>
											<input type="text" id="OnlineSiteSendNumber" name="OnlineSiteSendNumber" value="<?=$OnlineSiteSendNumber?>" class="md-input label-fixed" />
										</div>
										<div class="uk-width-medium-1-2 uk-input-group">
											<label for="OnlineSiteReceiveNumber"><?=$수신번호[$LangID]?>(<?=$콤마로_구분[$LangID]?>)</label>
											<input type="text" id="OnlineSiteReceiveNumber" name="OnlineSiteReceiveNumber" value="<?=$OnlineSiteReceiveNumber?>" class="md-input label-fixed" />
										</div>
									</div>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-1-2 uk-input-group"> 
											<label for="OnlineSiteSmsID">문자114 아이디</label>
											<input type="text" id="OnlineSiteSmsID" name="OnlineSiteSmsID" value="<?=$OnlineSiteSmsID?>" class="md-input label-fixed" />
										</div>
										<div class="uk-width-medium-1-2 uk-input-group">
											<label for="OnlineSiteSmsPW">문자114 비밀번호</label>
											<input type="text" id="OnlineSiteSmsPW" name="OnlineSiteSmsPW" value="" class="md-input label-fixed" placeholder="변경시 입력"/>
										</div>
									</div>
									<h3 class="full_width_in_card heading_c">
										<?=$연락처[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-1-2 uk-input-group">
											<label for="OnlineSitePhone1"><?=$전화번호[$LangID]?></label>
											<div class="md-input label-fixed" style="padding-top:10px;margin-bottom:10px;">
												<select name="OnlineSitePhone1_1" class="Select" style="width:15%;height:30px;">
													<option value="010" <?if ($OnlineSitePhone1_1=="010") {?>selected<?}?>>010</option>
													<option value="011" <?if ($OnlineSitePhone1_1=="011") {?>selected<?}?>>011</option>
													<option value="016" <?if ($OnlineSitePhone1_1=="016") {?>selected<?}?>>016</option>
													<option value="017" <?if ($OnlineSitePhone1_1=="017") {?>selected<?}?>>017</option>
													<option value="018" <?if ($OnlineSitePhone1_1=="018") {?>selected<?}?>>018</option>
													<option value="019" <?if ($OnlineSitePhone1_1=="019") {?>selected<?}?>>019</option>
													<option value="070" <?if ($OnlineSitePhone1_1=="070") {?>selected<?}?>>070</option>
													<option value="02"  <?if ($OnlineSitePhone1_1=="02")  {?>selected<?}?>>02</option>
													<option value="031" <?if ($OnlineSitePhone1_1=="031") {?>selected<?}?>>031</option>
													<option value="032" <?if ($OnlineSitePhone1_1=="032") {?>selected<?}?>>032</option>
													<option value="033" <?if ($OnlineSitePhone1_1=="033") {?>selected<?}?>>033</option>
													<option value="041" <?if ($OnlineSitePhone1_1=="041") {?>selected<?}?>>041</option>
													<option value="042" <?if ($OnlineSitePhone1_1=="042") {?>selected<?}?>>042</option>
													<option value="043" <?if ($OnlineSitePhone1_1=="043") {?>selected<?}?>>043</option>
													<option value="044" <?if ($OnlineSitePhone1_1=="044") {?>selected<?}?>>044</option>
													<option value="049" <?if ($OnlineSitePhone1_1=="049") {?>selected<?}?>>049</option>
													<option value="051" <?if ($OnlineSitePhone1_1=="051") {?>selected<?}?>>051</option>
													<option value="052" <?if ($OnlineSitePhone1_1=="052") {?>selected<?}?>>052</option>
													<option value="053" <?if ($OnlineSitePhone1_1=="053") {?>selected<?}?>>053</option>
													<option value="054" <?if ($OnlineSitePhone1_1=="054") {?>selected<?}?>>054</option>
													<option value="055" <?if ($OnlineSitePhone1_1=="055") {?>selected<?}?>>055</option>
													<option value="061" <?if ($OnlineSitePhone1_1=="061") {?>selected<?}?>>061</option>
													<option value="062" <?if ($OnlineSitePhone1_1=="062") {?>selected<?}?>>062</option>
													<option value="063" <?if ($OnlineSitePhone1_1=="063") {?>selected<?}?>>063</option>
													<option value="064" <?if ($OnlineSitePhone1_1=="064") {?>selected<?}?>>064</option>
													<option value="0505" <?if ($CenterPhone1_1=="0505") {?>selected<?}?>>0505</option>
													<option value="0502" <?if ($CenterPhone1_1=="0502") {?>selected<?}?>>0502</option>
												</select>
												<input type="text" name="OnlineSitePhone1_2" style="width:15%;height:30px;padding-left:10px;" placeholder="" value="<?=$OnlineSitePhone1_2?>" class="allownumericwithoutdecimal" maxlength="4"> 
												<input type="text" name="OnlineSitePhone1_3" style="width:15%;height:30px;padding-left:10px;"  placeholder="" value="<?=$OnlineSitePhone1_3?>" class="allownumericwithoutdecimal" maxlength="4">
											</div>
										</div>
										<div class="uk-width-medium-1-2 uk-input-group">
											<label for="CenterPhone2"><?=$휴대폰[$LangID]?></label>
											<div class="md-input label-fixed" style="padding-top:10px;margin-bottom:10px;">
												<select name="OnlineSitePhone2_1" class="Select" style="width:15%;height:30px;">
													<option value="010" <?if ($OnlineSitePhone2_1=="010") {?>selected<?}?>>010</option>
													<option value="011" <?if ($OnlineSitePhone2_1=="011") {?>selected<?}?>>011</option>
													<option value="016" <?if ($OnlineSitePhone2_1=="016") {?>selected<?}?>>016</option>
													<option value="017" <?if ($OnlineSitePhone2_1=="017") {?>selected<?}?>>017</option>
													<option value="018" <?if ($OnlineSitePhone2_1=="018") {?>selected<?}?>>018</option>
													<option value="019" <?if ($OnlineSitePhone2_1=="019") {?>selected<?}?>>019</option>
												</select>
												<input type="text" name="OnlineSitePhone2_2" style="width:15%;height:30px;padding-left:10px;" placeholder="" value="<?=$OnlineSitePhone2_2?>" class="allownumericwithoutdecimal" maxlength="4"> 
												<input type="text" name="OnlineSitePhone2_3" style="width:15%;height:30px;padding-left:10px;"  placeholder="" value="<?=$OnlineSitePhone2_3?>" class="allownumericwithoutdecimal" maxlength="4">
											</div>
										</div>
									</div>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-1-2 uk-input-group">
											<label for="CenterPhone3"><?=$팩스번호[$LangID]?></label>
											<div class="md-input label-fixed" style="padding-top:10px;margin-bottom:10px;">
												<select name="OnlineSitePhone3_1" class="Select" style="width:15%;height:30px;">
													<option value="02"  <?if ($OnlineSitePhone3_1=="02")  {?>selected<?}?>>02</option>
													<option value="031" <?if ($OnlineSitePhone3_1=="031") {?>selected<?}?>>031</option>
													<option value="032" <?if ($OnlineSitePhone3_1=="032") {?>selected<?}?>>032</option>
													<option value="033" <?if ($OnlineSitePhone3_1=="033") {?>selected<?}?>>033</option>
													<option value="041" <?if ($OnlineSitePhone3_1=="041") {?>selected<?}?>>041</option>
													<option value="042" <?if ($OnlineSitePhone3_1=="042") {?>selected<?}?>>042</option>
													<option value="043" <?if ($OnlineSitePhone3_1=="043") {?>selected<?}?>>043</option>
													<option value="044" <?if ($OnlineSitePhone3_1=="044") {?>selected<?}?>>044</option>
													<option value="049" <?if ($OnlineSitePhone3_1=="049") {?>selected<?}?>>049</option>
													<option value="051" <?if ($OnlineSitePhone3_1=="051") {?>selected<?}?>>051</option>
													<option value="052" <?if ($OnlineSitePhone3_1=="052") {?>selected<?}?>>052</option>
													<option value="053" <?if ($OnlineSitePhone3_1=="053") {?>selected<?}?>>053</option>
													<option value="054" <?if ($OnlineSitePhone3_1=="054") {?>selected<?}?>>054</option>
													<option value="055" <?if ($OnlineSitePhone3_1=="055") {?>selected<?}?>>055</option>
													<option value="061" <?if ($OnlineSitePhone3_1=="061") {?>selected<?}?>>061</option>
													<option value="062" <?if ($OnlineSitePhone3_1=="062") {?>selected<?}?>>062</option>
													<option value="063" <?if ($OnlineSitePhone3_1=="063") {?>selected<?}?>>063</option>
													<option value="064" <?if ($OnlineSitePhone3_1=="064") {?>selected<?}?>>064</option>
													<option value="010" <?if ($OnlineSitePhone3_1=="010") {?>selected<?}?>>010</option>
													<option value="011" <?if ($OnlineSitePhone3_1=="011") {?>selected<?}?>>011</option>
													<option value="016" <?if ($OnlineSitePhone3_1=="016") {?>selected<?}?>>016</option>
													<option value="017" <?if ($OnlineSitePhone3_1=="017") {?>selected<?}?>>017</option>
													<option value="018" <?if ($OnlineSitePhone3_1=="018") {?>selected<?}?>>018</option>
													<option value="019" <?if ($OnlineSitePhone3_1=="019") {?>selected<?}?>>019</option>
													<option value="070" <?if ($OnlineSitePhone3_1=="070") {?>selected<?}?>>070</option>
													<option value="0505" <?if ($CenterPhone1_1=="0505") {?>selected<?}?>>0505</option>
													<option value="0502" <?if ($CenterPhone1_1=="0502") {?>selected<?}?>>0502</option>
												</select>
												<input type="text" name="OnlineSitePhone3_2" style="width:15%;height:30px;padding-left:10px;" placeholder="" value="<?=$OnlineSitePhone3_2?>" class="allownumericwithoutdecimal" maxlength="4"> 
												<input type="text" name="OnlineSitePhone3_3" style="width:15%;height:30px;padding-left:10px;"  placeholder="" value="<?=$OnlineSitePhone3_3?>" class="allownumericwithoutdecimal" maxlength="4">
											</div>
										</div>
										<div class="uk-width-medium-1-2 uk-input-group">
											<label for="OnlineSiteEmail"><?=$이메일[$LangID]?></label>
											<div class="md-input label-fixed" style="padding-top:10px;margin-bottom:10px;">
												<input type="text" name="OnlineSiteEmail_1" id="OnlineSiteEmail_1" style="width:20%;height:30px;padding-left:10px;" value="<?=$OnlineSiteEmail_1?>"> 
												<div style="display:inline-block;width:20px;margin-top:8px;">@</div> 
												<input type="text" name="OnlineSiteEmail_2" id="OnlineSiteEmail_2" style="width:20%;height:30px;padding-left:10px;" value="<?=$OnlineSiteEmail_2?>">
												<select name="OnlineSiteEmail_3" class="Select" style="width:22%;height:30px;margin-bottom:0px;" onchange="SetEmailName()">
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
											<label for="OnlineSiteZip"><?=$우편번호[$LangID]?></label>
											<input type="text" id="OnlineSiteZip" name="OnlineSiteZip" value="<?=$OnlineSiteZip?>" class="md-input label-fixed" />
											<span class="uk-input-group-addon"><a class="md-btn" href="javascript:ExecDaumPostcode();"><?=$검색[$LangID]?></a></span>

										</div>
										<div class="uk-width-medium-3-10 uk-input-group">
											<label for="OnlineSiteAddr1"><?=$주소[$LangID]?></label>
											<input type="text" id="OnlineSiteAddr1" name="OnlineSiteAddr1" value="<?=$OnlineSiteAddr1?>" class="md-input label-fixed" />
										</div>
										<div class="uk-width-medium-4-10 uk-input-group">
											<label for="OnlineSiteAddr2"><?=$상세주소[$LangID]?></label>
											<input type="text" id="OnlineSiteAddr2" name="OnlineSiteAddr2" value="<?=$OnlineSiteAddr2?>" class="md-input label-fixed" />
										</div>
									</div>

									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-1-1">
											<label for="OnlineSiteIntroText"><?=$메모[$LangID]?></label>
											<textarea class="md-input" name="OnlineSiteIntroText" id="OnlineSiteIntroText" cols="30" rows="4"><?=$OnlineSiteIntroText?></textarea>
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
							<input type="checkbox" id="OnlineSiteView" name="OnlineSiteView" value="1" <?php if ($OnlineSiteView==1) { echo "checked";}?> data-switchery/>
							<label for="OnlineSiteView" class="inline-label"><?=$활성화[$LangID]?></label>
						</div>
						<hr class="md-hr" style="display:none;">

						<div class="uk-form-row">
							<input type="checkbox" id="OnlineSiteState" name="OnlineSiteState" value="1" <?php if ($OnlineSiteState==1) { echo "checked";}?> data-switchery/>
							<label for="OnlineSiteState" class="inline-label"><?=$운영중[$LangID]?></label>
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
                document.getElementById('OnlineSiteZip').value = data.zonecode;
                document.getElementById("OnlineSiteAddr1").value = addr;
                // 커서를 상세주소 필드로 이동한다.
                document.getElementById("OnlineSiteAddr2").focus();

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
	OnlineSiteEmail_3 = document.RegForm.OnlineSiteEmail_3.value;
	if (OnlineSiteEmail_3==""){
		document.RegForm.OnlineSiteEmail_2.value = "";
		document.RegForm.OnlineSiteEmail_2.readOnly = false;
	}else{
		document.RegForm.OnlineSiteEmail_2.value = OnlineSiteEmail_3;
		document.RegForm.OnlineSiteEmail_2.readOnly = true;
	}
}
//================ 이메일 =============


function FormSubmit(){

	obj = document.RegForm.FranchiseID;
	if (obj.value==""){
		UIkit.modal.alert("프랜차이즈를 선택하세요.");
		obj.focus();
		return;
	}

	obj = document.RegForm.OnlineSiteName;
	if (obj.value==""){
		UIkit.modal.alert("사이트명을 입력하세요.");
		obj.focus();
		return;
	}

	obj = document.RegForm.OnlineSiteManagerName;
	if (obj.value==""){
		UIkit.modal.alert("사이트 관리자명을 입력하세요.");
		obj.focus();
		return;
	}

	UIkit.modal.confirm(
		'저장 하시겠습니까?', 
		function(){ 
				document.RegForm.action = "online_site_action.php";
				document.RegForm.submit();
		}
	);

}




function OpenVideoPlayer() {

	
	var OnlineSiteGuideVideoCode = document.RegForm.OnlineSiteGuideVideoCode.value;

	if (OnlineSiteGuideVideoCode==""){
		UIkit.modal.alert("동영상 코드를 입력하세요.");
	}else{
		var OnlineSiteGuideVideoTypeForm = document.RegForm.OnlineSiteGuideVideoType;
		if (OnlineSiteGuideVideoTypeForm[0].checked){
			OnlineSiteGuideVideoType = OnlineSiteGuideVideoTypeForm[0].value
		} else {
			OnlineSiteGuideVideoType = OnlineSiteGuideVideoTypeForm[1].value
		}

		openurl = "video_player.php?VideoCode="+OnlineSiteGuideVideoCode+"&VideoType="+OnlineSiteGuideVideoType;

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