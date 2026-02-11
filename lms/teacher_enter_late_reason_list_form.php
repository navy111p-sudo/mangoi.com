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
$ClassTeacherEnterID = isset($_REQUEST["ClassTeacherEnterID"]) ? $_REQUEST["ClassTeacherEnterID"] : "";

$Sql = "
		select 
				A.*
		from ClassTeacherEnters A 
		where A.ClassTeacherEnterID=:ClassTeacherEnterID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassTeacherEnterID', $ClassTeacherEnterID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$ClassEnterLateReason = $Row["ClassEnterLateReason"];

if ( strpos($ClassEnterLateReason, "|||")!==false) {
	// 포함
	$ArrClassEnterLateReasonList= explode("|||", $ClassEnterLateReason);
} else {
	// 사유를 적을 때 마지막에 ||| 붙여 1개의 사유가 2개로 카운트 되기에 빈 공백 추가
	$ArrClassEnterLateReasonList = array($ClassEnterLateReason, "");
}


?>


<div id="page_content" style="overflow:hidden">
	<div id="page_content_inner">
		
		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="ClassTeacherEnterID" value="<?=$ClassTeacherEnterID?>">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="sub-heading" id="user_edit_position">Check late reason</span></h2>
						</div>
					</div>
					<div class="user_content">
						

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<label for="ClassEnterLateReason"></label>
									<? for ($i=0; $i < count($ArrClassEnterLateReasonList)-1; $i++) { // -1 를 해주는 건 처음 기입할 때부터 ||| 붙기 때문 ?>
										<? $ArrClassEnterLateReason= explode("||", $ArrClassEnterLateReasonList[$i]); ?>
										<input type="text" class="md-input" name="ClassEnterLateReason" id="ClassEnterLateReason" value="<?=$ArrClassEnterLateReason[0]?>">
										<? if ( isset($ArrClassEnterLateReason[1])) { ?>
											<span style="float: right; color: #808080">by <?=$ArrClassEnterLateReason[1]?></span>
										<? } ?>
									<? } ?>
								</div>
							</div>
						</div>

						<!--
						<div class="uk-margin-top" style="text-align:center;padding-top:30px;">
							<a type="button" href="javascript:FormSubmit();" class="md-btn md-btn-primary"><?=$저장하기[$LangID]?></a>
						</div>
						-->

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

</script>




<?php
include_once('./inc_footer.php');

include_once('../includes/dbclose.php');
?>
</body>
</html>