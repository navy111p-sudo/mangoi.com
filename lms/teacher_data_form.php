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
$TeacherDataID = isset($_REQUEST["TeacherDataID"]) ? $_REQUEST["TeacherDataID"] : "";
$ReceiveMemberID = isset($_REQUEST["ReceiveMemberID"]) ? $_REQUEST["ReceiveMemberID"] : "";
$SendMemberID = $_LINK_ADMIN_ID_;



if ($TeacherDataID!=""){

	$Sql = "
			select 
					A.*
			from TeacherDatas A 
				left outer join Members B on A.MemberID=B.MemberID 
				left outer join Members C on A.ReceiveMemberID=C.MemberID 
			where A.TeacherDataID=:TeacherDataID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TeacherDataID', $TeacherDataID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$SendMemberID = $Row["MemberID"];
	$SendMemberName = $Row["MemberName"];
	$ReceiveMemberID = $Row["ReceiveMemberID"];
	$ReceiveMemberName = $Row["ReceiveMemberName"];
	$TeacherDataTitle = $Row["TeacherDataTitle"];
	$TeacherDataFileName = $Row["TeacherDataFileName"];
	$TeacherDataFileRealName = $Row["TeacherDataFileRealName"];
	$TeacherDataRegDateTime = $Row["TeacherDataRegDateTime"];
	$TeacherDataReceiveDateTime = $Row["TeacherDataReceiveDateTime"];
	$TeacherDataState = $Row["TeacherDataState"];


}else{

	$Sql = "
			select 
					A.*
			from Members A 
			where A.MemberID=:ReceiveMemberID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ReceiveMemberID', $ReceiveMemberID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$ReceiveMemberName = $Row["MemberName"];


	$Sql = "
			select 
					A.*
			from Members A 
			where A.MemberID=:SendMemberID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':SendMemberID', $SendMemberID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$SendMemberName = $Row["MemberName"];

	$TeacherDataTitle = "";
	$TeacherDataFileName = "";
	$TeacherDataFileRealName = "";
	$TeacherDataState = 1;

}

?>


<div id="page_content">
	<div id="page_content_inner">
		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="TeacherDataID" value="<?=$TeacherDataID?>">
		<input type="hidden" name="SendMemberID" value="<?=$SendMemberID?>">
		<input type="hidden" name="SendMemberName" value="<?=$SendMemberName?>">
		<input type="hidden" name="ReceiveMemberID" id="ReceiveMemberID" value="<?=$ReceiveMemberID?>"/>
		<input type="hidden" name="TeacherDataState" value="<?=$TeacherDataState?>">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname">자료전송</span><span class="sub-heading" id="user_edit_position"><!--질문관리--></span></h2>
						</div>
					</div>
					<div class="user_content">
						<h3 class="full_width_in_card heading_c"> 
							<?=$받는사람[$LangID]?>
						</h3>
						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-3-10">
									<label for="ReceiveMemberName"><?=$이름[$LangID]?></label>
									<input type="text" id="ReceiveMemberName" name="ReceiveMemberName" value="<?=$ReceiveMemberName?>"  readonly class="md-input label-fixed"/>
								</div>
								<div class="uk-width-medium-7-10">
									<label for="TeacherDataTitle"><?=$제목[$LangID]?></label>
									<input type="text" id="TeacherDataTitle" name="TeacherDataTitle" value="<?=$TeacherDataTitle?>" class="md-input label-fixed"/>
								</div>
							</div>
						</div>


						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<input type="file" name="UpFile" id="UpFile"/>
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


	obj = document.RegForm.TeacherDataTitle;
	if (obj.value==""){
		UIkit.modal.alert("<?=$제목을_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}

	obj = document.RegForm.UpFile;
	if (obj.value==""){
		UIkit.modal.alert("<?=$파일을_업로드_하세요[$LangID]?>");
		obj.focus();
		return;
	}
	
	


	UIkit.modal.confirm(
		'<?=$저장_하시겠습니까[$LangID]?>?', 
		function(){ 
			document.RegForm.action = "teacher_data_action.php";
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