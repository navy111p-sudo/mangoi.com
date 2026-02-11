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
$SubMenuID = 1206;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
?>



<?php
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$CompanyID = isset($_REQUEST["CompanyID"]) ? $_REQUEST["CompanyID"] : "";
$PageTabID = isset($_REQUEST["PageTabID"]) ? $_REQUEST["PageTabID"] : "";

if ($PageTabID==""){
	$PageTabID = "1";
}

if ($CompanyID!=""){

	$Sql = "
			select 
					A.*,
					AES_DECRYPT(UNHEX(A.CompanyPhone1),:EncryptionKey) as DecCompanyPhone1,
					AES_DECRYPT(UNHEX(A.CompanyPhone2),:EncryptionKey) as DecCompanyPhone2,
					AES_DECRYPT(UNHEX(A.CompanyPhone3),:EncryptionKey) as DecCompanyPhone3,
					AES_DECRYPT(UNHEX(A.CompanyEmail),:EncryptionKey) as DecCompanyEmail
			from Companies A 
			where A.CompanyID=:CompanyID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':CompanyID', $CompanyID);
	$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$FranchiseID = $Row["FranchiseID"];
	$CompanyName = $Row["CompanyName"];
	$CompanyManagerName = $Row["CompanyManagerName"];
	//================ 전화번호 / 이메일 =============
	$CompanyPhone1 = $Row["DecCompanyPhone1"];
	$CompanyPhone2 = $Row["DecCompanyPhone2"];
	$CompanyPhone3 = $Row["DecCompanyPhone3"];
	$CompanyEmail = $Row["DecCompanyEmail"];
	//================ 전화번호 / 이메일 =============

	$CompanyPricePerTime = $Row["CompanyPricePerTime"];
	$CompanyZip = $Row["CompanyZip"];
	$CompanyAddr1 = $Row["CompanyAddr1"];
	$CompanyAddr2 = $Row["CompanyAddr2"];
	$CompanyIntroText = $Row["CompanyIntroText"];
	$CompanyState = $Row["CompanyState"];
	$CompanyView = $Row["CompanyView"];

}else{
	$FranchiseID = "";
	$CompanyName = "";
	$CompanyManagerName = "";
	//================ 전화번호 / 이메일 =============
	$CompanyPhone1 = "--";
	$CompanyPhone2 = "--";
	$CompanyPhone3 = "--";
	$CompanyEmail = "@";
	//================ 전화번호 / 이메일 =============

	$CompanyPricePerTime = 0;
	$CompanyZip = "";
	$CompanyAddr1 = "";
	$CompanyAddr2 = "";
	$CompanyIntroText = "";
	$CompanyState = 1;
	$CompanyView = 1;
}


//================ 전화번호 / 이메일 =============
$ArrCompanyPhone1 = explode("-", $CompanyPhone1);
$ArrCompanyPhone2 = explode("-", $CompanyPhone2);
$ArrCompanyPhone3 = explode("-", $CompanyPhone3);
$ArrCompanyEmail = explode("@", $CompanyEmail);

$CompanyPhone1_1 = $ArrCompanyPhone1[0];
$CompanyPhone1_2 = $ArrCompanyPhone1[1];
$CompanyPhone1_3 = $ArrCompanyPhone1[2];

$CompanyPhone2_1 = $ArrCompanyPhone2[0];
$CompanyPhone2_2 = $ArrCompanyPhone2[1];
$CompanyPhone2_3 = $ArrCompanyPhone2[2];

$CompanyPhone3_1 = $ArrCompanyPhone3[0];
$CompanyPhone3_2 = $ArrCompanyPhone3[1];
$CompanyPhone3_3 = $ArrCompanyPhone3[2];

$CompanyEmail_1 = $ArrCompanyEmail[0];
$CompanyEmail_2 = $ArrCompanyEmail[1];
//================ 전화번호 / 이메일 =============


?>


<div id="page_content">
	<div id="page_content_inner">

		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="CompanyID" value="<?=$CompanyID?>">
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
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$CompanyName?></span><span class="sub-heading" id="user_edit_position">본사정보</span></h2>
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
						
						<ul id="user_edit_tabs" class="uk-tab" data-uk-tab="{connect:'#user_edit_tabs_content'}">
							<li <?if ($PageTabID=="1"){?>class="uk-active"<?}?>><a href="#"><?=$본사정보[$LangID]?></a></li>
							<li <?if ($PageTabID=="8"){?>class="uk-active"<?}?>><a href="#"><?=$세금계산서정보[$LangID]?></a></li>
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
															echo "<optgroup label=\"$대표지사[$LangID]( $운영중[$LangID])\">";
														}else if ($SelectFranchiseState==2){
															echo "<optgroup label=\"$대표지사[$LangID]( $미운영[$LangID])\">";
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
										<?=$본사명_및_관리자[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-4-10">
											<label for="CompanyName"><?=$본사명[$LangID]?></label>
											<input type="text" id="CompanyName" name="CompanyName" value="<?=$CompanyName?>" class="md-input label-fixed"/>
										</div>
										<div class="uk-width-medium-4-10">
											<label for="CompanyManagerName"><?=$관리자[$LangID]?></label>
											<input type="text" id="CompanyManagerName" name="CompanyManagerName" value="<?=$CompanyManagerName?>" class="md-input label-fixed"/>
										</div>
										<div class="uk-width-medium-1-5">
											<label for="CompanyPricePerTime"><?=$수업료_10분[$LangID]?></label>
											<input type="number" id="CompanyPricePerTime" name="CompanyPricePerTime" value="<?=$CompanyPricePerTime?>" class="md-input label-fixed allownumericwithoutdecimal"/>
										</div>
									</div>
									<h3 class="full_width_in_card heading_c">
										<?=$연락처[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-1-2 uk-input-group">
											<label for="CompanyPhone1"><?=$전화번호[$LangID]?></label>
											<div class="md-input label-fixed" style="padding-top:10px;margin-bottom:10px;">
												<select name="CompanyPhone1_1" class="Select" style="width:15%;height:30px;">
													<option value="010" <?if ($CompanyPhone1_1=="010") {?>selected<?}?>>010</option>
													<option value="011" <?if ($CompanyPhone1_1=="011") {?>selected<?}?>>011</option>
													<option value="016" <?if ($CompanyPhone1_1=="016") {?>selected<?}?>>016</option>
													<option value="017" <?if ($CompanyPhone1_1=="017") {?>selected<?}?>>017</option>
													<option value="018" <?if ($CompanyPhone1_1=="018") {?>selected<?}?>>018</option>
													<option value="019" <?if ($CompanyPhone1_1=="019") {?>selected<?}?>>019</option>
													<option value="070" <?if ($CompanyPhone1_1=="070") {?>selected<?}?>>070</option>
													<option value="02"  <?if ($CompanyPhone1_1=="02")  {?>selected<?}?>>02</option>
													<option value="031" <?if ($CompanyPhone1_1=="031") {?>selected<?}?>>031</option>
													<option value="032" <?if ($CompanyPhone1_1=="032") {?>selected<?}?>>032</option>
													<option value="033" <?if ($CompanyPhone1_1=="033") {?>selected<?}?>>033</option>
													<option value="041" <?if ($CompanyPhone1_1=="041") {?>selected<?}?>>041</option>
													<option value="042" <?if ($CompanyPhone1_1=="042") {?>selected<?}?>>042</option>
													<option value="043" <?if ($CompanyPhone1_1=="043") {?>selected<?}?>>043</option>
													<option value="044" <?if ($CompanyPhone1_1=="044") {?>selected<?}?>>044</option>
													<option value="049" <?if ($CompanyPhone1_1=="049") {?>selected<?}?>>049</option>
													<option value="051" <?if ($CompanyPhone1_1=="051") {?>selected<?}?>>051</option>
													<option value="052" <?if ($CompanyPhone1_1=="052") {?>selected<?}?>>052</option>
													<option value="053" <?if ($CompanyPhone1_1=="053") {?>selected<?}?>>053</option>
													<option value="054" <?if ($CompanyPhone1_1=="054") {?>selected<?}?>>054</option>
													<option value="055" <?if ($CompanyPhone1_1=="055") {?>selected<?}?>>055</option>
													<option value="061" <?if ($CompanyPhone1_1=="061") {?>selected<?}?>>061</option>
													<option value="062" <?if ($CompanyPhone1_1=="062") {?>selected<?}?>>062</option>
													<option value="063" <?if ($CompanyPhone1_1=="063") {?>selected<?}?>>063</option>
													<option value="064" <?if ($CompanyPhone1_1=="064") {?>selected<?}?>>064</option>
													<option value="0505" <?if ($CenterPhone1_1=="0505") {?>selected<?}?>>0505</option>
													<option value="0502" <?if ($CenterPhone1_1=="0502") {?>selected<?}?>>0502</option>
												</select>
												<input type="text" name="CompanyPhone1_2" style="width:15%;height:30px;padding-left:10px;" placeholder="" value="<?=$CompanyPhone1_2?>" class="allownumericwithoutdecimal" maxlength="4"> 
												<input type="text" name="CompanyPhone1_3" style="width:15%;height:30px;padding-left:10px;"  placeholder="" value="<?=$CompanyPhone1_3?>" class="allownumericwithoutdecimal" maxlength="4">
											</div>
										</div>
										<div class="uk-width-medium-1-2 uk-input-group">
											<label for="CenterPhone2"><?=$휴대폰[$LangID]?></label>
											<div class="md-input label-fixed" style="padding-top:10px;margin-bottom:10px;">
												<select name="CompanyPhone2_1" class="Select" style="width:15%;height:30px;">
													<option value="010" <?if ($CompanyPhone2_1=="010") {?>selected<?}?>>010</option>
													<option value="011" <?if ($CompanyPhone2_1=="011") {?>selected<?}?>>011</option>
													<option value="016" <?if ($CompanyPhone2_1=="016") {?>selected<?}?>>016</option>
													<option value="017" <?if ($CompanyPhone2_1=="017") {?>selected<?}?>>017</option>
													<option value="018" <?if ($CompanyPhone2_1=="018") {?>selected<?}?>>018</option>
													<option value="019" <?if ($CompanyPhone2_1=="019") {?>selected<?}?>>019</option>
												</select>
												<input type="text" name="CompanyPhone2_2" style="width:15%;height:30px;padding-left:10px;" placeholder="" value="<?=$CompanyPhone2_2?>" class="allownumericwithoutdecimal" maxlength="4"> 
												<input type="text" name="CompanyPhone2_3" style="width:15%;height:30px;padding-left:10px;"  placeholder="" value="<?=$CompanyPhone2_3?>" class="allownumericwithoutdecimal" maxlength="4">
											</div>
										</div>
									</div>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-1-2 uk-input-group">
											<label for="CenterPhone3"><?=$팩스번호[$LangID]?></label>
											<div class="md-input label-fixed" style="padding-top:10px;margin-bottom:10px;">
												<select name="CompanyPhone3_1" class="Select" style="width:15%;height:30px;">
													<option value="02"  <?if ($CompanyPhone3_1=="02")  {?>selected<?}?>>02</option>
													<option value="031" <?if ($CompanyPhone3_1=="031") {?>selected<?}?>>031</option>
													<option value="032" <?if ($CompanyPhone3_1=="032") {?>selected<?}?>>032</option>
													<option value="033" <?if ($CompanyPhone3_1=="033") {?>selected<?}?>>033</option>
													<option value="041" <?if ($CompanyPhone3_1=="041") {?>selected<?}?>>041</option>
													<option value="042" <?if ($CompanyPhone3_1=="042") {?>selected<?}?>>042</option>
													<option value="043" <?if ($CompanyPhone3_1=="043") {?>selected<?}?>>043</option>
													<option value="044" <?if ($CompanyPhone3_1=="044") {?>selected<?}?>>044</option>
													<option value="049" <?if ($CompanyPhone3_1=="049") {?>selected<?}?>>049</option>
													<option value="051" <?if ($CompanyPhone3_1=="051") {?>selected<?}?>>051</option>
													<option value="052" <?if ($CompanyPhone3_1=="052") {?>selected<?}?>>052</option>
													<option value="053" <?if ($CompanyPhone3_1=="053") {?>selected<?}?>>053</option>
													<option value="054" <?if ($CompanyPhone3_1=="054") {?>selected<?}?>>054</option>
													<option value="055" <?if ($CompanyPhone3_1=="055") {?>selected<?}?>>055</option>
													<option value="061" <?if ($CompanyPhone3_1=="061") {?>selected<?}?>>061</option>
													<option value="062" <?if ($CompanyPhone3_1=="062") {?>selected<?}?>>062</option>
													<option value="063" <?if ($CompanyPhone3_1=="063") {?>selected<?}?>>063</option>
													<option value="064" <?if ($CompanyPhone3_1=="064") {?>selected<?}?>>064</option>
													<option value="010" <?if ($CompanyPhone3_1=="010") {?>selected<?}?>>010</option>
													<option value="011" <?if ($CompanyPhone3_1=="011") {?>selected<?}?>>011</option>
													<option value="016" <?if ($CompanyPhone3_1=="016") {?>selected<?}?>>016</option>
													<option value="017" <?if ($CompanyPhone3_1=="017") {?>selected<?}?>>017</option>
													<option value="018" <?if ($CompanyPhone3_1=="018") {?>selected<?}?>>018</option>
													<option value="019" <?if ($CompanyPhone3_1=="019") {?>selected<?}?>>019</option>
													<option value="070" <?if ($CompanyPhone3_1=="070") {?>selected<?}?>>070</option>
													<option value="0505" <?if ($CenterPhone1_1=="0505") {?>selected<?}?>>0505</option>
													<option value="0502" <?if ($CenterPhone1_1=="0502") {?>selected<?}?>>0502</option>
												</select>
												<input type="text" name="CompanyPhone3_2" style="width:15%;height:30px;padding-left:10px;" placeholder="" value="<?=$CompanyPhone3_2?>" class="allownumericwithoutdecimal" maxlength="4"> 
												<input type="text" name="CompanyPhone3_3" style="width:15%;height:30px;padding-left:10px;"  placeholder="" value="<?=$CompanyPhone3_3?>" class="allownumericwithoutdecimal" maxlength="4">
											</div>
										</div>
										<div class="uk-width-medium-1-2 uk-input-group">
											<label for="CompanyEmail"><?=$이메일[$LangID]?></label>
											<div class="md-input label-fixed" style="padding-top:10px;margin-bottom:10px;">
												<input type="text" name="CompanyEmail_1" id="CompanyEmail_1" style="width:20%;height:30px;padding-left:10px;" value="<?=$CompanyEmail_1?>"> 
												<div style="display:inline-block;width:20px;margin-top:8px;">@</div> 
												<input type="text" name="CompanyEmail_2" id="CompanyEmail_2" style="width:20%;height:30px;padding-left:10px;" value="<?=$CompanyEmail_2?>">
												<select name="CompanyEmail_3" class="Select" style="width:22%;height:30px;margin-bottom:0px;" onchange="SetEmailName()">
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
										주소
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-3-10 uk-input-group">
											<label for="CompanyZip"><?=$우편번호[$LangID]?></label>
											<input type="text" id="CompanyZip" name="CompanyZip" value="<?=$CompanyZip?>" class="md-input label-fixed" />
											<span class="uk-input-group-addon"><a class="md-btn" href="javascript:ExecDaumPostcode();"><?=$검색[$LangID]?></a></span>

										</div>
										<div class="uk-width-medium-3-10 uk-input-group">
											<label for="CompanyAddr1"><?=$주소[$LangID]?></label>
											<input type="text" id="CompanyAddr1" name="CompanyAddr1" value="<?=$CompanyAddr1?>" class="md-input label-fixed" />
										</div>
										<div class="uk-width-medium-4-10 uk-input-group">
											<label for="CompanyAddr2"><?=$상세주소[$LangID]?></label>
											<input type="text" id="CompanyAddr2" name="CompanyAddr2" value="<?=$CompanyAddr2?>" class="md-input label-fixed" />
										</div>
									</div>

									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-1-1">
											<label for="CompanyIntroText"><?=$메모[$LangID]?></label>
											<textarea class="md-input" name="CompanyIntroText" id="CompanyIntroText" cols="30" rows="4"><?=$CompanyIntroText?></textarea>
										</div>
									</div>


								</div>
							</li>

							<!-- =========================== 세금계산서 정보 ============================== -->
							<li>
								<?

								$OrganType = 9;
								$OrganID = $CompanyID;
								
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
									<!--
									<tr>
										<th width="15%" nowrap>(추가)담당자명</th>
										<td class="uk-text-nowrap"><?=$ContactName2?></td>
									</tr>
									<tr>
										<th width="15%" nowrap>(추가)이메일</th>
										<td class="uk-text-nowrap"><?=$Email2?></td>
									</tr>
									-->


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
							<input type="checkbox" id="CompanyView" name="CompanyView" value="1" <?php if ($CompanyView==1) { echo "checked";}?> data-switchery/>
							<label for="CompanyView" class="inline-label"><?=$활성화[$LangID]?></label>
						</div>
						<hr class="md-hr" style="display:none;">

						<div class="uk-form-row">
							<input type="checkbox" id="CompanyState" name="CompanyState" value="1" <?php if ($CompanyState==1) { echo "checked";}?> data-switchery/>
							<label for="CompanyState" class="inline-label"><?=$운영중[$LangID]?></label>
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
                document.getElementById('CompanyZip').value = data.zonecode;
                document.getElementById("CompanyAddr1").value = addr;
                // 커서를 상세주소 필드로 이동한다.
                document.getElementById("CompanyAddr2").focus();

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
	CompanyEmail_3 = document.RegForm.CompanyEmail_3.value;
	if (CompanyEmail_3==""){
		document.RegForm.CompanyEmail_2.value = "";
		document.RegForm.CompanyEmail_2.readOnly = false;
	}else{
		document.RegForm.CompanyEmail_2.value = CompanyEmail_3;
		document.RegForm.CompanyEmail_2.readOnly = true;
	}
}
//================ 이메일 =============



function FormSubmit(){

	obj = document.RegForm.FranchiseID;
	if (obj.value==""){
		UIkit.modal.alert("<?=$프랜차이즈를_선택하세요[$LangID]?>");
		obj.focus();
		return;
	}

	obj = document.RegForm.CompanyName;
	if (obj.value==""){
		UIkit.modal.alert("<?=$본사명을_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}

	obj = document.RegForm.CompanyManagerName;
	if (obj.value==""){
		UIkit.modal.alert("<?=$본사_관리자명을_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}

	UIkit.modal.confirm(
		'<?=$저장_하시겠습니까[$LangID]?>?', 
		function(){ 
			document.RegForm.action = "company_action.php";
			document.RegForm.submit();
		}
	);


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






<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>