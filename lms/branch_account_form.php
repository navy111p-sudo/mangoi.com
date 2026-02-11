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
$BranchAccountID = isset($_REQUEST["BranchAccountID"]) ? $_REQUEST["BranchAccountID"] : "";


if ($BranchAccountID!=""){
	$Sql = "
			select 
				A.*
			from BranchAccounts A 

			where A.BranchAccountID=:BranchAccountID ";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BranchAccountID', $BranchAccountID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();

	$BranchID = $Row["BranchID"];
	$BranchAccountName = $Row["BranchAccountName"];
	$BranchAccountPrice = $Row["BranchAccountPrice"];
	$BranchAccountState = $Row["BranchAccountState"];

}else{
	$BranchID = "";
	$BranchAccountName = "";
	$BranchAccountPrice = 0;
	$BranchAccountState = 1;
}
?>


<div id="page_content">
	<div id="page_content_inner">
		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="BranchAccountID" value="<?=$BranchAccountID?>">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$지사미수관리[$LangID]?></span><span class="sub-heading" id="user_edit_position"><!--베스트강사--></span></h2>
						</div>
					</div>
					<div class="user_content">

						<div class="uk-margin-top">
							<div class="uk-margin-top" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<select name="BranchID" style="height:30px;width:100%;">
										<option value=""><?=$지사선택[$LangID]?></option>
										<?
										$Sql2 = "select 
														A.* 
												from Branches A 
												order by A.BranchName asc";
										$Stmt2 = $DbConn->prepare($Sql2);
										$Stmt2->execute();
										$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
										
										while($Row2 = $Stmt2->fetch()) {
										?>
											<option value="<?=$Row2["BranchID"]?>" <?if ($BranchID==$Row2["BranchID"]) {?>selected<?}?>><?=$Row2["BranchName"]?></option>
										<?
										}
										$Stmt2 = null;
										?>
									</select>
								</div>
							</div>
						</div>

						<hr>						

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<label for="BranchAccountName"><?=$적요[$LangID]?></label>
									<input type="text" id="BranchAccountName" name="BranchAccountName" value="<?=$BranchAccountName?>" class="md-input label-fixed"/>
								</div>
							</div>
						</div>



						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-2">
									<label for="CouponPrice"><?=$미수금[$LangID]?></label>
									<input type="text" id="BranchAccountPrice" name="BranchAccountPrice" value="<?=$BranchAccountPrice?>" class="md-input label-fixed"/>
								</div>
								<div class="uk-width-medium-1-2">
									<label for="CouponPrice__"></label>
									※ 지출의 경우 (-) 를 입력해 주세요.
								</div>
							</div>
						</div>


						
						<div class="uk-margin-top" style="display:<?if ($BranchAccountID==""){?>none<?}?>;">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-2 uk-input-group">
									<input type="checkbox" id="BranchAccountState" name="BranchAccountState" value="2" <?php if ($BranchAccountState==2) { echo "checked";}?> data-switchery/>
									<label for="BranchAccountState" class="inline-label"><?=$삭제[$LangID]?></label>
								</div>
							</div>
						</div>

						<div class="uk-margin-top" style="text-align:center;padding-top:30px;">
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


<script language="javascript">

function FormSubmit(){

	obj = document.RegForm.BranchID;
	if (obj.value==""){
		UIkit.modal.alert("<?=$지사를_선택해주세요[$LangID]?>");
		obj.focus();
		return;
	}

	obj = document.RegForm.BranchAccountName;
	if (obj.value==""){
		UIkit.modal.alert("<?=$적요를_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}


	UIkit.modal.confirm(
		'<?=$저장_하시겠습니까[$LangID]?>?', 
		function(){ 
			document.RegForm.action = "branch_account_action.php";
			document.RegForm.submit();
		}
	);

}

</script>




<?php
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>