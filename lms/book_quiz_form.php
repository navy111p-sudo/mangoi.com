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
$BookID = isset($_REQUEST["BookID"]) ? $_REQUEST["BookID"] : "";
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$BookQuizID = isset($_REQUEST["BookQuizID"]) ? $_REQUEST["BookQuizID"] : "";


if ($BookQuizID!=""){

	$Sql = "
			select 
					A.*,
					B.BookName 
			from BookQuizs A 
				inner join Books B on A.BookID=B.BookID 
			where A.BookQuizID=:BookQuizID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BookQuizID', $BookQuizID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$BookQuizName = $Row["BookQuizName"];
	$BookQuizMemo = $Row["BookQuizMemo"];
	$BookQuizView = $Row["BookQuizView"];
	$BookQuizState = $Row["BookQuizState"];

	$BookName = $Row["BookName"];

}else{
	$BookQuizName = "";
	$BookQuizMemo = "";
	$BookQuizView = 1;
	$BookQuizState = 1;

	$Sql = "
			select 
					A.*
			from Books A 
			where A.BookID=:BookID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BookID', $BookID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;	
	
	$BookName = $Row["BookName"];
}
?>


<div id="page_content">
	<div id="page_content_inner">
		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="BookID" value="<?=$BookID?>">
		<input type="hidden" name="BookQuizID" value="<?=$BookQuizID?>">
		<input type="hidden" name="ListParam" value="<?=$ListParam?>">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$BookName?></span><span class="sub-heading" id="user_edit_position"><?=$퀴즈그룹관리[$LangID]?></span></h2>
						</div>
					</div>
					<div class="user_content">
						
						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<label for="BookQuizName"><?=$퀴즈그룹제목[$LangID]?></label>
									<input type="text" id="BookQuizName" name="BookQuizName" value="<?=$BookQuizName?>" class="md-input label-fixed"/>
								</div>
							</div>
						</div>



						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<label for="BookQuizMemo"><?=$퀴즈그룹내용[$LangID]?></label>
									<textarea class="md-input" name="BookQuizMemo" id="BookQuizMemo" cols="30" rows="4"><?=$BookQuizMemo?></textarea>
								</div>
							</div>
						</div>

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-2 uk-input-group">
									<input type="checkbox" id="BookQuizState" name="BookQuizState" value="1" <?php if ($BookQuizState==1) { echo "checked";}?> data-switchery/>
									<label for="BookQuizState" class="inline-label"><?=$사용[$LangID]?></label>
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

	obj = document.RegForm.BookQuizName;
	if (obj.value==""){
		UIkit.modal.alert("<?=$퀴즈그룹_제목을_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}


	UIkit.modal.confirm(
		'<?=$저장_하시겠습니까[$LangID]?>?', 
		function(){ 
			document.RegForm.action = "book_quiz_action.php";
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