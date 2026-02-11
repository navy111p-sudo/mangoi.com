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
$QnaNoMemberID = isset($_REQUEST["QnaNoMemberID"]) ? $_REQUEST["QnaNoMemberID"] : "";


if ($QnaNoMemberID!=""){

	$Sql = "
			select 
					A.*
			from QnaNoMembers A 
				left outer join Members B on A.MemberID=B.MemberID 
				left outer join Members C on A.AnswerMemberID=C.MemberID 
			where A.QnaNoMemberID=:QnaNoMemberID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':QnaNoMemberID', $QnaNoMemberID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$MemberID = $Row["MemberID"];
	$MemberName = $Row["MemberName"];
	$AnswerMemberID = $Row["AnswerMemberID"];
	$AnswerMemberName = $Row["AnswerMemberName"];
	$QnaNoMemberTitle = $Row["QnaNoMemberTitle"];
	$QnaNoMemberContent = $Row["QnaNoMemberContent"];
	$QnaNoMemberAnswer = $Row["QnaNoMemberAnswer"];
	$QnaNoMemberRegDateTime = $Row["QnaNoMemberRegDateTime"];
	$QnaNoMemberAnswerRegDateTime = $Row["QnaNoMemberAnswerRegDateTime"];
	$QnaNoMemberState = $Row["QnaNoMemberState"];

	if ($AnswerMemberID==""){
		$AnswerMemberID = $_LINK_ADMIN_ID_;
	}
	if ($AnswerMemberName==""){
		$AnswerMemberName = $_LINK_ADMIN_NAME_;
	}

}else{

	$MemberID = 0;
	$MemberName = "";
	$QnaNoMemberTitle = "";
	$QnaNoMemberContent = "";
	$QnaNoMemberState = 1;

}

?>


<div id="page_content">
	<div id="page_content_inner">
		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="QnaNoMemberID" value="<?=$QnaNoMemberID?>">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$비회원문의[$LangID]?></span><span class="sub-heading" id="user_edit_position"><!--질문관리--></span></h2>
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
									<label for="QnaNoMemberTitle"><?=$제목[$LangID]?></label>
									<input type="text" id="QnaNoMemberTitle" name="QnaNoMemberTitle" value="<?=$QnaNoMemberTitle?>" class="md-input label-fixed"/>
								</div>
							</div>
						</div>

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<label for="QnaNoMemberContent"><?=$질문내용[$LangID]?></label>
									<textarea class="md-input" name="QnaNoMemberContent" id="QnaNoMemberContent" cols="30" rows="4"><?=$QnaNoMemberContent?></textarea>
								</div>
							</div>
						</div>
					
						<?if ($QnaNoMemberID!=""){?>
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
										<input type="radio" name="QnaNoMemberState" id="QnaNoMemberState1" value="1" <?if ($QnaNoMemberState==1){?>checked<?}?> data-md-icheck />
										<label for="QnaNoMemberState1" class="inline-label"><?=$답변전[$LangID]?></label>
									</span>
									<span class="icheck-inline">
										<input type="radio" name="QnaNoMemberState" id="QnaNoMemberState2" value="2" <?if ($QnaNoMemberState==2){?>checked<?}?> data-md-icheck />
										<label for="QnaNoMemberState2" class="inline-label"><?=$답변완료[$LangID]?></label>
									</span>
								</div>
							</div>
						</div>
						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<label for="QnaNoMemberAnswer"><?=$답변내용[$LangID]?></label>
									<textarea class="md-input" name="QnaNoMemberAnswer" id="QnaNoMemberAnswer" cols="30" rows="4"><?=$QnaNoMemberAnswer?></textarea>
								</div>
							</div>
						</div>
						<?}else{?>
						<input type="hidden" name="QnaNoMemberState" id="QnaNoMemberState" value="1"/>
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

	obj = document.RegForm.QnaNoMemberTitle;
	if (obj.value==""){
		UIkit.modal.alert("제목을 입력하세요.");
		obj.focus();
		return;
	}

	obj = document.RegForm.QnaNoMemberContent;
	if (obj.value==""){
		UIkit.modal.alert("내용을 입력하세요.");
		obj.focus();
		return;
	}

	
	


	UIkit.modal.confirm(
		'저장 하시겠습니까?', 
		function(){ 
			document.RegForm.action = "qna_nomember_action.php";
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