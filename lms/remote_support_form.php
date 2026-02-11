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
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$RemoteSupportMemberID = isset($_REQUEST["RemoteSupportMemberID"]) ? $_REQUEST["RemoteSupportMemberID"] : "";


if ($RemoteSupportMemberID!=""){

	$Sql = "
			select 
					A.*
			from RemoteSupportMembers A 
				left outer join Members B on A.MemberID=B.MemberID 
			where A.RemoteSupportMemberID=:RemoteSupportMemberID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':RemoteSupportMemberID', $RemoteSupportMemberID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$MemberID = $Row["MemberID"];
	$MemberName = $Row["MemberName"];
	$RemoteSupportMemberTitle = $Row["RemoteSupportMemberTitle"];
	$RemoteSupportMemberContent = $Row["RemoteSupportMemberContent"];
	$RemoteSupportMemberRegDateTime = $Row["RemoteSupportMemberRegDateTime"];
	$RemoteSupportMemberState = $Row["RemoteSupportMemberState"];


}else{

	$MemberID = $_LINK_ADMIN_ID_;
	$MemberName = $_LINK_ADMIN_NAME_;
	$RemoteSupportMemberTitle = "";
	$RemoteSupportMemberContent = "";
	$RemoteSupportMemberState = 1;

}

?>


<div id="page_content">
	<div id="page_content_inner">
		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="RemoteSupportMemberID" value="<?=$RemoteSupportMemberID?>">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname">원격지원 문의</span><span class="sub-heading" id="user_edit_position"><!--질문관리--></span></h2>
						</div>
					</div>
					<div class="user_content">
						<h3 class="full_width_in_card heading_c"> 
							<?=$질문[$LangID]?>
						</h3>
						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<label for="MemberName"><?=$이름[$LangID]?></label>
									<input type="hidden" id="MemberID" name="MemberID" value="<?=$MemberID?>"/>
									<input type="text" id="MemberName" name="MemberName" value="<?=$MemberName?>" class="md-input label-fixed"/>
								</div>
							</div>
						</div>

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<label for="RemoteSupportMemberTitle"><?=$제목[$LangID]?></label>
									<input type="text" id="RemoteSupportMemberTitle" name="RemoteSupportMemberTitle" value="<?=$RemoteSupportMemberTitle?>" class="md-input label-fixed"/>
								</div>
							</div>
						</div>

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<label for="RemoteSupportMemberContent"><?=$질문내용[$LangID]?></label>
									<textarea class="md-input" name="RemoteSupportMemberContent" id="RemoteSupportMemberContent" cols="30" rows="4"><?=$RemoteSupportMemberContent?></textarea>
								</div>
							</div>
						</div>
					
						<?if ($RemoteSupportMemberID!=""){?>
						<h3 class="full_width_in_card heading_c"> 
							<?=$처리상황[$LangID]?>
						</h3>
						<input type="hidden" id="AnswerMemberID" name="AnswerMemberID" value="<?=$AnswerMemberID?>" class="md-input label-fixed"/>
						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-2">
									<span class="icheck-inline">
										<input type="radio" name="RemoteSupportMemberState" id="RemoteSupportMemberState1" value="1" <?if ($RemoteSupportMemberState==1){?>checked<?}?> data-md-icheck />
										<label for="RemoteSupportMemberState1" class="inline-label"><?=$처리전[$LangID]?></label>
									</span>
									<span class="icheck-inline">
										<input type="radio" name="RemoteSupportMemberState" id="RemoteSupportMemberState2" value="2" <?if ($RemoteSupportMemberState==2){?>checked<?}?> data-md-icheck />
										<label for="RemoteSupportMemberState2" class="inline-label"><?=$처리완료[$LangID]?></label>
									</span>
								</div>
							</div>
						</div>
						<?}else{?>
						<input type="hidden" name="RemoteSupportMemberState" id="RemoteSupportMemberState" value="1"/>
						<?}?>


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




	UIkit.modal.confirm(
		'저장 하시겠습니까?', 
		function(){ 
			document.RegForm.action = "remote_support_action.php";
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