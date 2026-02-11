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
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$CounselID = isset($_REQUEST["CounselID"]) ? $_REQUEST["CounselID"] : "";


if ($CounselID!=""){

	$Sql = "
			select 
					A.*,
					B.MemberName,
					C.MemberName as RegMemberName 
			from Counsels A 
				inner join Members B on A.MemberID=B.MemberID 
				inner join Members C on A.RegMemberID=C.MemberID 
			where A.CounselID=:CounselID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':CounselID', $CounselID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$RegMemberID = $Row["RegMemberID"];
	$MemberID = $Row["MemberID"];
	$MemberWriteName = $Row["MemberWriteName"];
	$TeacherWriteName = $Row["TeacherWriteName"];
	$CounselTitle = $Row["CounselTitle"];
	$CounselDate = $Row["CounselDate"];
	$CounselTime = $Row["CounselTime"];
	$CounselContent = $Row["CounselContent"];
	$CounselAnswerContent = $Row["CounselAnswerContent"];
	$CounselSms = $Row["CounselSms"];
	$CounselView = $Row["CounselView"];
	$CounselState = $Row["CounselState"];

	$MemberName = $Row["MemberName"];
	$RegMemberName = $Row["RegMemberName"];

	$CounselTime = substr($CounselTime,0,5);

}else{
	$RegMemberID = $_LINK_ADMIN_ID_;
	$CounselTitle = "";
	$CounselDate = date("Y-m-d");
	$CounselTime = date("H:i");
	$CounselContent = "";
	$CounselAnswerContent = "";
	$CounselSms = 0;
	$CounselView = 1;
	$CounselState = 1;

	$Sql = "
			select 
					A.*
			from Members A 
			where A.MemberID=:MemberID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$MemberName = $Row["MemberName"];


	$Sql = "
			select 
					A.*
			from Members A 
			where A.MemberID=:MemberID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':MemberID', $RegMemberID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$RegMemberName = $Row["MemberName"];


	$MemberWriteName = $MemberName;
	$TeacherWriteName = $RegMemberName;
}
?>


<div id="page_content">
	<div id="page_content_inner">
		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="CounselID" value="<?=$CounselID?>">
		<input type="hidden" name="RegMemberID" value="<?=$RegMemberID?>">
		<input type="hidden" name="MemberID" value="<?=$MemberID?>">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$MemberName?></span><span class="sub-heading" id="user_edit_position"><?=$상담관리[$LangID]?></span></h2>
						</div>
					</div>
					<div class="user_content">
						
						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-2">
									<label for="TeacherWriteName"><?=$상담자[$LangID]?></label>
									<input type="text" id="TeacherWriteName" name="TeacherWriteName" value="<?=$TeacherWriteName?>" class="md-input label-fixed" <?if ($CounselID!=""){?>disabled<?}?>/>
								</div>
								<div class="uk-width-medium-1-2">
									<label for="MemberWriteName"><?=$학생명[$LangID]?></label>
									<input type="text" id="MemberWriteName" name="MemberWriteName" value="<?=$MemberWriteName?>" class="md-input label-fixed" <?if ($CounselID!=""){?>disabled<?}?>/>
								</div>
							</div>
						</div>

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-2 uk-input-group">
									<label for="CounselDate"><?=$상담일자[$LangID]?></label>
									<input type="text" id="CounselDate" name="CounselDate" value="<?=$CounselDate?>" class="md-input label-fixed" data-uk-datepicker="{format:'YYYY-MM-DD', weekstart:0}" <?if ($CounselID!=""){?>disabled<?}?>>
									<span class="uk-input-group-addon"><i class="uk-input-group-icon uk-icon-calendar"></i></span>
								</div>
								<div class="uk-width-medium-1-2 uk-input-group">
									<label for="CounselTime"><?=$상담시간[$LangID]?></label>
									<input type="text" id="CounselTime" name="CounselTime" value="<?=$CounselTime?>" class="md-input label-fixed" data-uk-timepicker="{start:8, end:22, interval:10}" <?if ($CounselID!=""){?>disabled<?}?>>
									<span class="uk-input-group-addon"><i class="uk-input-group-icon uk-icon-clock-o"></i></span>
								</div>
							</div>
						</div>

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<label for="CounselTitle"><?=$상담제목[$LangID]?></label>
									<input type="text" id="CounselTitle" name="CounselTitle" value="<?=$CounselTitle?>" class="md-input label-fixed" <?if ($CounselID!=""){?>disabled<?}?>/>
								</div>
							</div>
						</div>

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<label for="CounselContent"><?=$상담내용[$LangID]?></label>
									<textarea class="md-input" name="CounselContent" id="CounselContent" cols="30" rows="4" <?if ($CounselID!=""){?>disabled<?}?>><?=$CounselContent?></textarea>
								</div>
							</div>
						</div>

						<?if ($CounselID!=""){?>
						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<label for="CounselContent"><?=$메모[$LangID]?></label>
									<textarea class="md-input" name="CounselAnswerContent" id="CounselAnswerContent" cols="30" rows="4"><?=$CounselAnswerContent?></textarea>
								</div>
							</div>
						</div>

						<?}?>

						<div class="uk-margin-top" style="display:none;">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-2 uk-input-group" style="display:none;">
									<input type="checkbox" name="CounselSms" id="CounselSms" value="1" data-md-icheck/>
									<label for="CounselSms" class="inline-label"><?=$SMS전송[$LangID]?></label>
								</div>
								<div class="uk-width-medium-1-2 uk-input-group">
									<input type="checkbox" id="CounselState" name="CounselState" value="1" <?php if ($CounselState==1) { echo "checked";}?> data-switchery/>
									<label for="CounselState" class="inline-label"><?=$공개[$LangID]?></label>
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

	obj = document.RegForm.TeacherWriteName;
	if (obj.value==""){
		UIkit.modal.alert("<?=$상담자_이름을_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}

	obj = document.RegForm.MemberWriteName;
	if (obj.value==""){
		UIkit.modal.alert("<?=$학생_이름을_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}

	obj = document.RegForm.CounselDate;
	if (obj.value==""){
		UIkit.modal.alert("<?=$상담_날짜를_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}

	obj = document.RegForm.CounselTime;
	if (obj.value==""){
		UIkit.modal.alert("<?=$상담_시간을_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}

	obj = document.RegForm.CounselTitle;
	if (obj.value==""){
		UIkit.modal.alert("<?=$상담_제목을_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}

	obj = document.RegForm.CounselContent;
	if (obj.value==""){
		UIkit.modal.alert("<?=$상담_내용을_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}
	

	UIkit.modal.confirm(
		'<?=$저장_하시겠습니까[$LangID]?>?', 
		function(){ 
			document.RegForm.action = "counsel_action.php";
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