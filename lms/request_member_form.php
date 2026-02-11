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
$RequestMemberID = isset($_REQUEST["RequestMemberID"]) ? $_REQUEST["RequestMemberID"] : "";


if ($RequestMemberID!=""){

	$Sql = "
			select 
					A.*
			from RequestMembers A 
				left outer join Members B on A.MemberID=B.MemberID 
				left outer join Members C on A.AnswerMemberID=C.MemberID 
			where A.RequestMemberID=:RequestMemberID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':RequestMemberID', $RequestMemberID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$MemberID = $Row["MemberID"];
	$MemberName = $Row["MemberName"];
	$AnswerMemberID = $Row["AnswerMemberID"];
	$AnswerMemberName = $Row["AnswerMemberName"];
	$RequestMemberTitle = $Row["RequestMemberTitle"];
	$RequestMemberContent = $Row["RequestMemberContent"];
	$RequestMemberAnswer = $Row["RequestMemberAnswer"];
	$RequestMemberRegDateTime = $Row["RequestMemberRegDateTime"];
	$RequestMemberAnswerRegDateTime = $Row["RequestMemberAnswerRegDateTime"];
	$RequestMemberState = $Row["RequestMemberState"];

	if ($AnswerMemberID==""){
		$AnswerMemberID = $_LINK_ADMIN_ID_;
	}
	if ($AnswerMemberName==""){
		$AnswerMemberName = $_LINK_ADMIN_NAME_;
	}

}else{

	$MemberID = $_LINK_ADMIN_ID_;
	$MemberName = $_LINK_ADMIN_NAME_;
	$RequestMemberTitle = "";
	$RequestMemberContent = "";
	$RequestMemberState = 1;

}

?>


<div id="page_content">
	<div id="page_content_inner">
		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="RequestMemberID" value="<?=$RequestMemberID?>">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname">건의사항</span><span class="sub-heading" id="user_edit_position"><!--질문관리--></span></h2>
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
									<label for="RequestMemberTitle"><?=$제목[$LangID]?></label>
									<input type="text" id="RequestMemberTitle" name="RequestMemberTitle" value="<?=$RequestMemberTitle?>" class="md-input label-fixed"/>
								</div>
							</div>
						</div>

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<label for="RequestMemberContent"><?=$질문내용[$LangID]?></label>
									<textarea class="md-input" name="RequestMemberContent" id="RequestMemberContent" cols="30" rows="4"><?=$RequestMemberContent?></textarea>
								</div>
							</div>
						</div>
					
						<?if ($RequestMemberID!=""){?>
						<h3 class="full_width_in_card heading_c"> 
							<?=$답변[$LangID]?>
						</h3>
						<input type="hidden" id="AnswerMemberID" name="AnswerMemberID" value="<?=$AnswerMemberID?>" class="md-input label-fixed"/>
						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-2">
									<label for="AnswerMemberName"><?=$답변자[$LangID]?></label>
									<input type="text" id="AnswerMemberName" name="AnswerMemberName" value="<?=$AnswerMemberName?>" class="md-input label-fixed"/>
								</div>
								<div class="uk-width-medium-1-2">
									<span class="icheck-inline">
										<input type="radio" name="RequestMemberState" id="RequestMemberState1" value="1" <?if ($RequestMemberState==1){?>checked<?}?> data-md-icheck />
										<label for="RequestMemberState1" class="inline-label"><?=$답변전[$LangID]?></label>
									</span>
									<span class="icheck-inline">
										<input type="radio" name="RequestMemberState" id="RequestMemberState2" value="2" <?if ($RequestMemberState==2){?>checked<?}?> data-md-icheck />
										<label for="RequestMemberState2" class="inline-label"><?=$답변완료[$LangID]?></label>
									</span>
								</div>
							</div>
						</div>
						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<label for="RequestMemberAnswer"><?=$답변내용[$LangID]?></label>
									<textarea class="md-input" name="RequestMemberAnswer" id="RequestMemberAnswer" cols="30" rows="4"><?=$RequestMemberAnswer?></textarea>
								</div>
							</div>
						</div>
						<?}else{?>
						<input type="hidden" name="RequestMemberState" id="RequestMemberState" value="1"/>
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


	obj = document.RegForm.MemberName;
	if (obj.value==""){
		UIkit.modal.alert("이름을 입력하세요.");
		obj.focus();
		return;
	}

	obj = document.RegForm.RequestMemberTitle;
	if (obj.value==""){
		UIkit.modal.alert("제목을 입력하세요.");
		obj.focus();
		return;
	}

	obj = document.RegForm.RequestMemberContent;
	if (obj.value==""){
		UIkit.modal.alert("내용을 입력하세요.");
		obj.focus();
		return;
	}

	
	


	UIkit.modal.confirm(
		'저장 하시겠습니까?', 
		function(){ 
			document.RegForm.action = "request_member_action.php";
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