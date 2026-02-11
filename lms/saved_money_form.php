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
$CenterID = isset($_REQUEST["CenterID"]) ? $_REQUEST["CenterID"] : "";
$SavedMoneyID = isset($_REQUEST["SavedMoneyID"]) ? $_REQUEST["SavedMoneyID"] : "";
$SearchText = isset($_REQUEST["SearchText"]) ? $_REQUEST["SearchText"] : "";


if ($SavedMoneyID!=""){

	$Sql = "SELECT 
					A.*,
					C.CenterName 
			from SavedMoney A 
				inner join Centers C on A.CenterID=C.CenterID 
			where A.SavedMoneyID=:SavedMoneyID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':SavedMoneyID', $SavedMoneyID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$RegMemberID = $Row["RegMemberID"];
	$CenterID = $Row["CenterID"];
	$SavedMoney = $Row["SavedMoney"];
	$CenterName = $Row["CenterName"];

}else{
	$RegMemberID = $_LINK_ADMIN_ID_;
	
	$SavedMoney = 0;

	// 만약 centerID 검색을 실행했으면 centerID를 가지고 온다.
	if ($SearchText != ''){
		$Sql = "SELECT 	CenterID, CenterName 
				from Centers 
				where CenterName like '%".$SearchText."%'";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;
		$CenterID = $Row["CenterID"];
		$CenterName = $Row["CenterName"];


	} else {
		$CenterName = "";
	}
	
}

?>


<div id="page_content">
	<div id="page_content_inner">
		
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">

			<? if ($SavedMoneyID==""){ ?>
			<form name="SearchForm" method="post">
				<div class="md-card">
					<div class="uk-grid" data-uk-grid-margin>
						<div class="uk-width-medium-3-10">
							<label for="SearchText">대리점명 검색</label>
							<input type="text" class="md-input" id="SearchText" name="SearchText" value="<?=$SearchText?>">

						</div>
						<div class="uk-width-medium-3-10">
							<a href="javascript:SearchSubmit();" class="md-btn md-btn-primary uk-margin-small-top"><?=$검색[$LangID]?></a>
						</div>
					</div>	
				</div>
				<br>
			</form>	
			<?}?>
				<form id="RegForm" name="RegForm" method="get" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
				<input type="hidden" name="SavedMoneyID" value="<?=$SavedMoneyID?>">
				<input type="hidden" name="RegMemberID" value="<?=$RegMemberID?>">
				<input type="hidden" name="CenterID" value="<?=$CenterID?>">

				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname">대리점명 : <?=$CenterName?></span><span class="sub-heading" id="user_edit_position"><?=$충전금[$LangID]?></span></h2>
						</div>
					</div>
					<div class="user_content">
						

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-5-10 uk-input-group">
									<label for="SavedMoney"><?=$충전금[$LangID]?></label>
									<input type="number" id="SavedMoney" name="SavedMoney" value="<?=$SavedMoney?>" class="md-input label-fixed "  />
									<span class="uk-input-group-addon">P</span>
								</div>
							</div>
						</div>

						
						<?if ($SavedMoneyID!=""){?>
						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-2 uk-input-group">
									<input type="checkbox" name="DelSavedMoney" id="DelSavedMoney" value="1" data-md-icheck/>
									<label for="DelSavedMoney" class="inline-label"><?=$삭제[$LangID]?></label>
								</div>
								<div class="uk-width-medium-1-2 uk-input-group">
	
								</div>
							</div>
						</div>
						<?}?>

						<div class="uk-margin-top" style="text-align:center;padding-top:30px;">
							<a type="button" href="javascript:FormSubmit();" class="md-btn md-btn-primary"><?=$저장하기[$LangID]?></a>
						</div>

					</div>
				</div>
				</form>
			</div>

		</div>
		

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

<?php
	//만약 신규등록이면 대리점ID 가 등록되어 있는지 체크한다.
	if ($SavedMoneyID == "") {
?>
		var checkCenterID = document.RegForm.CenterID.value;
		if (checkCenterID == ""){
			alert('대리점을 검색해서 입력해 주세요!');
			return;
		}
<?php
	}
?>

	UIkit.modal.confirm(
		'저장 하시겠습니까?', 
		function(){ 
			document.RegForm.action = "saved_money_action.php";
			document.RegForm.submit();
		}
	);

}

function SearchSubmit(){

	url = "ajax_get_center_id.php";
	var SearchText = document.SearchForm.SearchText.value;
	
	$.ajax(url, {
		data: {
			SearchText: SearchText
		},
		success: function (data) {
			document.RegForm.CenterID.value = data.CenterID;
			$("#user_edit_uname").html("대리점명 : " + data.CenterName);
		},
		error: function () {

		}
	});

	
	
}
</script>



<?php
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>