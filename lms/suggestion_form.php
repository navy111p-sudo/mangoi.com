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
$SuggestionID = isset($_REQUEST["SuggestionID"]) ? $_REQUEST["SuggestionID"] : "";


if ($SuggestionID!=""){

	$Sql = "
			select 
					A.*
			from Suggestions A 
				left outer join Members B on A.MemberID=B.MemberID 
				left outer join Members C on A.AnswerMemberID=C.MemberID 
			where A.SuggestionID=:SuggestionID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':SuggestionID', $SuggestionID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$MemberID = $Row["MemberID"];
	$MemberName = $Row["MemberName"];
	$AnswerMemberID = $Row["AnswerMemberID"];
	$AnswerMemberName = $Row["AnswerMemberName"];
	$SuggestionTitle = $Row["SuggestionTitle"];
	$SuggestionContent = $Row["SuggestionContent"];
	$SuggestionAnswer = $Row["SuggestionAnswer"];
	$SuggestionRegDateTime = $Row["SuggestionRegDateTime"];
	$SuggestionAnswerRegDateTime = $Row["SuggestionAnswerRegDateTime"];
	$SuggestionState = $Row["SuggestionState"];

	if ($AnswerMemberID==""){
		$AnswerMemberID = $_LINK_ADMIN_ID_;
	}
	if ($AnswerMemberName==""){
		$AnswerMemberName = $_LINK_ADMIN_NAME_;
	}

}else{

	$MemberID = $_LINK_ADMIN_ID_;
	$MemberName = $_LINK_ADMIN_NAME_;
	$SuggestionTitle = "";
	$SuggestionContent = "";
	$SuggestionState = 1;

}

?>


<div id="page_content">
	<div id="page_content_inner">
		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="SuggestionID" value="<?=$SuggestionID?>">
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
									<label for="SuggestionTitle"><?=$제목[$LangID]?></label>
									<input type="text" id="SuggestionTitle" name="SuggestionTitle" value="<?=$SuggestionTitle?>" class="md-input label-fixed"/>
								</div>
							</div>
						</div>

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<label for="SuggestionContent"><?=$질문내용[$LangID]?></label>
									<textarea class="md-input" name="SuggestionContent" id="SuggestionContent" cols="30" rows="4"><?=$SuggestionContent?></textarea>
								</div>
							</div>
						</div>
					
						<?if ($SuggestionID!=""){?>
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
										<input type="radio" name="SuggestionState" id="SuggestionState1" value="1" <?if ($SuggestionState==1){?>checked<?}?> data-md-icheck />
										<label for="SuggestionState1" class="inline-label"><?=$답변전[$LangID]?></label>
									</span>
									<span class="icheck-inline">
										<input type="radio" name="SuggestionState" id="SuggestionState2" value="2" <?if ($SuggestionState==2){?>checked<?}?> data-md-icheck />
										<label for="SuggestionState2" class="inline-label"><?=$답변완료[$LangID]?></label>
									</span>
								</div>
							</div>
						</div>
						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<label for="SuggestionAnswer"><?=$답변내용[$LangID]?></label>
									<textarea class="md-input" name="SuggestionAnswer" id="SuggestionAnswer" cols="30" rows="4"><?=$SuggestionAnswer?></textarea>
								</div>
							</div>
						</div>
						<?}else{?>
						<input type="hidden" name="SuggestionState" id="SuggestionState" value="1"/>
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

	obj = document.RegForm.SuggestionTitle;
	if (obj.value==""){
		UIkit.modal.alert("제목을 입력하세요.");
		obj.focus();
		return;
	}

	obj = document.RegForm.SuggestionContent;
	if (obj.value==""){
		UIkit.modal.alert("내용을 입력하세요.");
		obj.focus();
		return;
	}

	
	


	UIkit.modal.confirm(
		'저장 하시겠습니까?', 
		function(){ 
			document.RegForm.action = "suggestion_action.php";
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