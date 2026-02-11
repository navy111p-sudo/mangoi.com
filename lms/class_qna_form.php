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
$ClassID = isset($_REQUEST["ClassID"]) ? $_REQUEST["ClassID"] : "";
$ClassQnaID = isset($_REQUEST["ClassQnaID"]) ? $_REQUEST["ClassQnaID"] : "";


if ($ClassQnaID!=""){

	$Sql = "
			select 
					A.*,
					B.StartYear,
					B.StartMonth,
					B.StartDay,
					D.MemberName,
					E.MemberName as TeacherName 
			from ClassQnas A 
				inner join Classes B on A.ClassID=B.ClassID 
				inner join ClassOrders C on B.ClassOrderID=C.ClassOrderID 
				inner join Members D on B.MemberID=D.MemberID 
				inner join Members E on B.TeacherID=E.MemberID 
			where A.ClassQnaID=:ClassQnaID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ClassQnaID', $ClassQnaID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$ClassID = $Row["ClassID"];
	$ClassQnaID = $Row["ClassQnaID"];
	$ClassQnaTitle = $Row["ClassQnaTitle"];
	$ClassQnaContent = $Row["ClassQnaContent"];
	$ClassQnaAnswer = $Row["ClassQnaAnswer"];
	$ClassQnaRegDateTime = $Row["ClassQnaRegDateTime"];
	$ClassQnaAnswerRegDateTime = $Row["ClassQnaAnswerRegDateTime"];
	$ClassQnaOrder = $Row["ClassQnaOrder"];
	$ClassQnaState = $Row["ClassQnaState"];


	$StartYear = $Row["StartYear"];
	$StartMonth = $Row["StartMonth"];
	$StartDay = $Row["StartDay"];
	$MemberName = $Row["MemberName"];
	$TeacherName = $Row["TeacherName"];


}else{

	$ClassQnaID = "";
	$ClassQnaTitle = "";
	$ClassQnaContent = "";
	$ClassQnaAnswer = "";
	$ClassQnaState = 1;

	$Sql = "
			select 
					A.*,
					C.MemberName,
					D.MemberName as TeacherName
			from Classes A 
				inner join ClassOrders B on A.ClassOrderID=B.ClassOrderID 
				inner join Members C on A.MemberID=C.MemberID 
				inner join Members D on A.TeacherID=D.MemberID 
			where A.ClassID=:ClassID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ClassID', $ClassID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$StartYear = $Row["StartYear"];
	$StartMonth = $Row["StartMonth"];
	$StartDay = $Row["StartDay"];
	$MemberName = $Row["MemberName"];
	$TeacherName = $Row["TeacherName"];

}
?>


<div id="page_content">
	<div id="page_content_inner">
		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="ClassQnaID" value="<?=$ClassQnaID?>">
		<input type="hidden" name="ClassID" value="<?=$ClassID?>">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$StartYear?>.<?=$StartMonth?>.<?=$StartDay?> 수업</span><span class="sub-heading" id="user_edit_position"><?=$요청관리[$LangID]?></span></h2>
						</div>
					</div>
					<div class="user_content">
						
						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-2">
									<label for="TeacherName"><?=$강사명[$LangID]?></label>
									<input type="text" id="TeacherName" name="TeacherName" value="<?=$TeacherName?>" readonly class="md-input label-fixed"/>
								</div>
								<div class="uk-width-medium-1-2">
									<label for="MemberName"><?=$학생명[$LangID]?></label>
									<input type="text" id="MemberName" name="MemberName" value="<?=$MemberName?>" readonly class="md-input label-fixed"/>
								</div>
							</div>
						</div>

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<label for="ClassQnaTitle"><?=$요청제목[$LangID]?></label>
									<input type="text" id="ClassQnaTitle" name="ClassQnaTitle" value="<?=$ClassQnaTitle?>" class="md-input label-fixed"/>
								</div>
							</div>
						</div>

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<label for="ClassQnaContent"><?=$요청내용[$LangID]?></label>
									<textarea class="md-input" name="ClassQnaContent" id="ClassQnaContent" cols="30" rows="4"><?=$ClassQnaContent?></textarea>
								</div>
							</div>
						</div>

						<?if ($ClassQnaID!=""){?>
						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<label for="ClassQnaAnswer"><?=$답변내용[$LangID]?></label>
									<textarea class="md-input" name="ClassQnaAnswer" id="ClassQnaAnswer" cols="30" rows="4"><?=$ClassQnaAnswer?></textarea>
								</div>
							</div>
						</div>
						<?}?>

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-2-10">
								결제수단
								</div>
								<div class="uk-width-medium-8-10">
									<span class="icheck-inline">
										<input type="radio" name="ClassQnaState" id="ClassQnaState1" value="1" <?if ($ClassQnaState==1){?>checked<?}?> data-md-icheck />
										<label for="ClassQnaState1" class="inline-label"><?=$신규[$LangID]?></label>
									</span>
									<span class="icheck-inline">
										<input type="radio" name="ClassQnaState" id="ClassQnaState2" value="2" <?if ($ClassQnaState==2){?>checked<?}?> data-md-icheck />
										<label for="ClassQnaState2" class="inline-label"><?=$진행중[$LangID]?></label>
									</span>
									<span class="icheck-inline">
										<input type="radio" name="ClassQnaState" id="ClassQnaState3" value="3" <?if ($ClassQnaState==3){?>checked<?}?> data-md-icheck />
										<label for="ClassQnaState3" class="inline-label"><?=$위임[$LangID]?></label>
									</span>
									<span class="icheck-inline">
										<input type="radio" name="ClassQnaState" id="ClassQnaState4" value="4" <?if ($ClassQnaState==4){?>checked<?}?> data-md-icheck />
										<label for="ClassQnaState4" class="inline-label"><?=$완료_강사미확인[$LangID]?></label>
									</span>
									<span class="icheck-inline">
										<input type="radio" name="ClassQnaState" id="ClassQnaState5" value="5" <?if ($ClassQnaState==5){?>checked<?}?> data-md-icheck />
										<label for="ClassQnaState5" class="inline-label"><?=$완료_강사확인[$LangID]?></label>
									</span>
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


	obj = document.RegForm.ClassQnaTitle;
	if (obj.value==""){
		UIkit.modal.alert("<?=$요청_제목을_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}

	obj = document.RegForm.ClassQnaContent;
	if (obj.value==""){
		UIkit.modal.alert("<?=$요청_내용을_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}
	

	UIkit.modal.confirm(
		'<?=$저장_하시겠습니까[$LangID]?>?', 
		function(){ 
			document.RegForm.action = "class_qna_action.php";
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