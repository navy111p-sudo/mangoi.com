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

<body class="disable_transitions sidebar_main_open sidebar_main_swipe" style="padding-top:0px;">

<?php

$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$CenterID = isset($_REQUEST["CenterID"]) ? $_REQUEST["CenterID"] : "";
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : ""; 

// 수정 요망
if ($MemberID!=""){
	$Sql = "
			select 
				A.*, 
				AES_DECRYPT(UNHEX(A.MemberPhone1),:EncryptionKey) as DecMemberPhone1, 
				AES_DECRYPT(UNHEX(A.MemberEmail),:EncryptionKey) as DecMemberEmail 
			from Members A 
			inner join Centers B on A.CenterID=B.CenterID 
			where MemberID=:MemberID"; // limit $StartRowNum, $PageListNum";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);

	$Row = $Stmt->fetch();

	$MemberName = $Row["MemberName"];
	$MemberNickName = $Row["MemberNickName"];
	$MemberLoginID = $Row["MemberLoginID"];
	$MemberPhone1 = $Row["DecMemberPhone1"];
	$MemberEmail = $Row["DecMemberEmail"];
	$MemberState = $Row["MemberState"];
	$MemberView = $Row["MemberView"];

	//================ 전화번호 / 이메일 =============
	$ArrMemberPhone1 = explode("-", $MemberPhone1);
	$ArrMemberEmail = explode("@", $MemberEmail);

	$MemberPhone1_1 = $ArrMemberPhone1[0];
	$MemberPhone1_2 = $ArrMemberPhone1[1];
	$MemberPhone1_3 = $ArrMemberPhone1[2];

	$MemberEmail_1 = $ArrMemberEmail[0];
	$MemberEmail_2 = $ArrMemberEmail[1];
	//================ 전화번호 / 이메일 =============

} else {
	$MemberName = "";
	$MemberNickName = "";
	$MemberLoginID = "";
	$MemberState = 1;
	$MemberView = 1;

	$MemberPhone1_1 = "";
	$MemberPhone1_2 = "";
	$MemberPhone1_3 = "";

	$MemberEmail_1 = "";
	$MemberEmail_2 = "";


}
//
?>


<div id="page_content">
	<div id="page_content_inner">

		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="ListParam" value="<?=$ListParam?>">
		<input type="hidden" name="MemberID" value="<?=$MemberID?>">
		<input type="hidden" name="CenterID" value="<?=$CenterID?>">
		<input type="hidden" name="CheckedID" value="<?=$CheckedID?>">
		<input type="hidden" name="CheckedEmail" value="<?=$CheckedEmail?>">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b">
							<!-- <span class="uk-text-truncate" id="user_edit_uname"><?=$CenterClassName?></span> -->
							<span class="sub-heading" id="user_edit_position">교사 및 직원관리</span></h2>
						</div>
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
									<?=$교사_및_직원명[$LangID]?>
								</h3>
								<div class="uk-grid" data-uk-grid-margin>
									<div class="uk-width-medium-3-10">
										<label for="MemberName"><?=$교사_및_직원명[$LangID]?></label>
										<input type="text" id="MemberName" name="MemberName" value="<?=$MemberName?>" class="md-input label-fixed"/>
									</div>
									<div class="uk-width-medium-3-10">
										<label for="MemberNickName"><?=$영문표기이름[$LangID]?></label>
										<input type="text" id="MemberNickName" name="MemberNickName" value="<?=$MemberNickName?>" class="md-input label-fixed"/>
									</div>
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
									<div class="uk-width-medium-2-10 uk-input-group">
										<label for="MemberLoginNewPW"><?=$비밀번호[$LangID]?></label>
										<input type="password" id="MemberLoginNewPW" name="MemberLoginNewPW" class="md-input label-fixed" />
									</div>
									<div class="uk-width-medium-2-10 uk-input-group">
										<label for="MemberLoginNewPW2"><?=$비밀번호확인[$LangID]?></label>
										<input type="password" id="MemberLoginNewPW2" name="MemberLoginNewPW2" class="md-input label-fixed" />
									</div>
								</div>

								<h3 class="full_width_in_card heading_c">
									<?=$연락처[$LangID]?>
								</h3>
								<div class="uk-grid" data-uk-grid-margin>
									<div class="uk-width-medium-4-5 uk-input-group">
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
												<option value="0505" <?if ($CenterPhone1_1=="0505") {?>selected<?}?>>0505</option>
												<option value="0502" <?if ($CenterPhone1_1=="0502") {?>selected<?}?>>0502</option>
											</select>
											<input type="text" name="MemberPhone1_2" style="width:15%;height:30px;padding-left:10px;" placeholder="" value="<?=$MemberPhone1_2?>" class="allownumericwithoutdecimal" maxlength="4"> 
											<input type="text" name="MemberPhone1_3" style="width:15%;height:30px;padding-left:10px;"  placeholder="" value="<?=$MemberPhone1_3?>" class="allownumericwithoutdecimal" maxlength="4">
										</div>
									</div>

									<div class="uk-width-medium-5-5 uk-input-group">
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
											<span class="uk-input-group-addon" id="BtnCheckEmail" style="display:<?if ($MemberID!="") {?>none<?}else{?>inline<?}?>;"><a class="md-btn" href="javascript:CheckEmail();"><?=$중복확인[$LangID]?></a></span>
										</div>
									</div>
								</div>

							</div>
							</li>

						</ul>
					</div>
				</div>
			</div>
			<div class="uk-width-large-3-10">
				<div class="md-card">
					<div class="md-card-content">
						<h3 class="heading_c uk-margin-medium-bottom"><?=$상태설정[$LangID]?></h3>
						
						<div class="uk-form-row" style="display:none;">
							<input type="checkbox" id="MemberView" name="MemberView" value="1" <?php if ($MemberView==1) { echo "checked";}?> data-switchery/>
							<label for="MemberView" class="inline-label"><?=$활성화[$LangID]?></label>
						</div>
						<hr class="md-hr" style="display:none;">
						
						
						<div class="uk-form-row">
							<input type="checkbox" id="MemberState" name="MemberState" value="1" <?php if ($MemberState==1) { echo "checked";}?> data-switchery/>
							<label for="MemberState" class="inline-label"><?=$운영중[$LangID]?></label>
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


<script>

function EnNewID() {
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
    var MemberEmail_1 = $.trim($('#MemberEmail_1').val());
	var MemberEmail_2 = $.trim($('#MemberEmail_2').val());

    if (MemberEmail_1 == "" || MemberEmail_2 == "") {
        alert('<?=$이메일을_입력하세요[$LangID]?>');
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

//================ 이메일 =============



function FormSubmit(){
	obj = document.RegForm.MemberName;
	if (obj.value==""){
		UIkit.modal.alert("<?=$센터직원명을_입력해주세요[$LangID]?>");
		obj.focus();
		return;
	}

	obj = document.RegForm.MemberNickName;
	if (obj.value==""){
		UIkit.modal.alert("<?=$영문표기이름을_입력해주세요[$LangID]?>");
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
	//obj = document.getElementById("BtnCheckID");
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


	obj = document.RegForm.MemberPhone1_2;
	if (obj.value==""){
		UIkit.modal.alert("<?=$학생_휴대전화를_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}

	obj = document.RegForm.MemberPhone1_3;
	if (obj.value==""){
		UIkit.modal.alert("<?=$학생_휴대전화를_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}

	obj = document.RegForm.MemberEmail_1;
	if (obj.value==""){
		UIkit.modal.alert("<?=$이메일을_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}

	obj = document.RegForm.MemberEmail_2;
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

	UIkit.modal.confirm(
		'<?=$저장_하시겠습니까[$LangID]?>?', 
		function(){ 
			document.RegForm.action = "center_staff_action.php";
			document.RegForm.submit();
		}
	);


}

</script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">