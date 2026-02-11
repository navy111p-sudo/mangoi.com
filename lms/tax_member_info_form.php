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
$OrganType = isset($_REQUEST["OrganType"]) ? $_REQUEST["OrganType"] : "";
$OrganID = isset($_REQUEST["OrganID"]) ? $_REQUEST["OrganID"] : "";
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : ""; 

$Sql_99 = "
		select 
				A.*
		from TaxMemberInfos A 
		where A.OrganType=:OrganType and A.OrganID=:OrganID";
$Stmt_99 = $DbConn->prepare($Sql_99);
$Stmt_99->bindParam(':OrganType', $OrganType);
$Stmt_99->bindParam(':OrganID', $OrganID);
$Stmt_99->execute();
$Stmt_99->setFetchMode(PDO::FETCH_ASSOC);
$Row_99 = $Stmt_99->fetch();
$Stmt_99 = null;

$TaxMemberInfoID = $Row_99["TaxMemberInfoID"];

if ($TaxMemberInfoID){

	$CorpName = $Row_99["CorpName"];
	$CorpNum = $Row_99["CorpNum"];
	$TaxRegID = $Row_99["TaxRegID"];
	$CEOName = $Row_99["CEOName"];
	$Addr = $Row_99["Addr"];
	$BizType = $Row_99["BizType"];
	$BizClass = $Row_99["BizClass"];
	$ContactName1 = $Row_99["ContactName1"];
	$Email1 = $Row_99["Email1"];
	$TEL1 = $Row_99["TEL1"];
	$HP1 = $Row_99["HP1"];
	$ContactName2 = $Row_99["ContactName2"];
	$Email2 = $Row_99["Email2"];

}else{

	$TaxMemberInfoID = "";

	$CorpName = "";
	$CorpNum = "";
	$TaxRegID = "";
	$CEOName = "";
	$Addr = "";
	$BizType = "";
	$BizClass = "";
	$ContactName1 = "";
	$Email1 = "";
	$TEL1 = "";
	$HP1 = "";
	$ContactName2 = "";
	$Email2 = "";
}

?>


<div id="page_content">
	<div id="page_content_inner">

		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="TaxMemberInfoID" value="<?=$TaxMemberInfoID?>">
		<input type="hidden" name="OrganType" value="<?=$OrganType?>">
		<input type="hidden" name="OrganID" value="<?=$OrganID?>">
		<input type="hidden" name="ListParam" value="<?=$ListParam?>">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$세금계산서_정보[$LangID]?></span><span class="sub-heading" id="user_edit_position"></span></h2>
						</div>
					</div>
					<div class="user_content">	
						<ul id="user_edit_tabs" class="uk-tab" data-uk-tab="{connect:'#user_edit_tabs_content'}" style="display:none;">
							<li class="uk-active"><a href="#">Basic</a></li>
							<li><a href="#">Groups</a></li>
							<li><a href="#">Todo</a></li>
						</ul>
						<ul id="user_edit_tabs_content" class="uk-switcher uk-margin">
							<li>
								<div class="uk-margin-top">
									<div class="uk-margin-top">
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-4-10">
											<label for="CorpName"><?=$사업자명[$LangID]?></label>
											<input type="text" id="CorpName" name="CorpName" value="<?=$CorpName?>" class="md-input label-fixed"/>
										</div>
										<div class="uk-width-medium-3-10">
											<label for="CorpNum"><?=$사업자등록번호[$LangID]?></label>
											<input type="text" id="CorpNum" name="CorpNum" value="<?=$CorpNum?>" class="md-input label-fixed allownumericwithoutdecimal"/>
										</div>
										<div class="uk-width-medium-3-10">
											<label for="TaxRegID"><?=$종사업장번호_선택[$LangID]?></label>
											<input type="text" id="TaxRegID" name="TaxRegID" value="<?=$TaxRegID?>" class="md-input label-fixed allownumericwithoutdecimal"/>
										</div>
									</div>
								</div>

								<div class="uk-margin-top">
									<div class="uk-margin-top">
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-3-10">
											<label for="CEOName"><?=$대표자명[$LangID]?></label>
											<input type="text" id="CEOName" name="CEOName" value="<?=$CEOName?>" class="md-input label-fixed"/>
										</div>
										<div class="uk-width-medium-3-10">
											<label for="TEL1"><?=$전화번호[$LangID]?></label>
											<input type="text" id="TEL1" name="TEL1" value="<?=$TEL1?>" class="md-input label-fixed"/>
										</div>
										<div class="uk-width-medium-4-10">
											<label for="HP1"><?=$휴대폰번호[$LangID]?></label>
											<input type="text" id="HP1" name="HP1" value="<?=$HP1?>" class="md-input label-fixed"/>
										</div>
									</div>
								</div>

								<div class="uk-margin-top">
									<div class="uk-margin-top">
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-10-10">
											<label for="Addr"><?=$주소[$LangID]?></label>
											<input type="text" id="Addr" name="Addr" value="<?=$Addr?>" class="md-input label-fixed"/>
										</div>
									</div>
								</div>

								<div class="uk-margin-top">
									<div class="uk-margin-top">
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-5-10">
											<label for="BizType"><?=$업태[$LangID]?></label>
											<input type="text" id="BizType" name="BizType" value="<?=$BizType?>" class="md-input label-fixed"/>
										</div>
										<div class="uk-width-medium-5-10">
											<label for="BizClass"><?=$종목[$LangID]?></label>
											<input type="text" id="BizClass" name="BizClass" value="<?=$BizClass?>" class="md-input label-fixed"/>
										</div>
									</div>
								</div>

								<div class="uk-margin-top">
									<div class="uk-margin-top">
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-5-10">
											<label for="ContactName1"><?=$담당자명[$LangID]?></label>
											<input type="text" id="ContactName1" name="ContactName1" value="<?=$ContactName1?>" class="md-input label-fixed"/>
										</div>
										<div class="uk-width-medium-5-10">
											<label for="Email1"><?=$이메일[$LangID]?></label>
											<input type="text" id="Email1" name="Email1" value="<?=$Email1?>" class="md-input label-fixed"/>
										</div>
									</div>
								</div>
	

								
								<div class="uk-margin-top" style="display:<?if ($OrganType==9){?>none<?}?>;">
									<div class="uk-margin-top">
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-5-10">
											<label for="ContactName2"><?=$담당자명_추가[$LangID]?></label>
											<input type="text" id="ContactName2" name="ContactName2" value="<?=$ContactName2?>" class="md-input label-fixed"/>
										</div>
										<div class="uk-width-medium-5-10">
											<label for="Email2"><?=$이메일_추가[$LangID]?></label>
											<input type="text" id="Email2" name="Email2" value="<?=$Email2?>" class="md-input label-fixed"/>
										</div>
									</div>
								</div>
	
							</li>


						</ul>
					</div>
				</div>
			</div>

			<div class="uk-width-large-10-10">
				<div class="md-card">
					<div class="md-card-content">
						<div class="uk-form-row" style="text-align:center">
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

	obj = document.RegForm.CorpName;
	if (obj.value==""){
		UIkit.modal.alert("사업자명을 입력하세요.");
		obj.focus();
		return;
	}

	obj = document.RegForm.CorpNum;
	if (obj.value==""){
		UIkit.modal.alert("사업자등록번호를 선택하세요.");
		obj.focus();
		return;
	}

	obj = document.RegForm.CEOName;
	if (obj.value==""){
		UIkit.modal.alert("대표자명 입력하세요.");
		obj.focus();
		return;
	}

	obj = document.RegForm.TEL1;
	if (obj.value==""){
		UIkit.modal.alert("전화번호를 입력하세요.");
		obj.focus();
		return;
	}

	obj = document.RegForm.HP1;
	if (obj.value==""){
		UIkit.modal.alert("휴대폰번호를 입력하세요.");
		obj.focus();
		return;
	}

	obj = document.RegForm.Addr;
	if (obj.value==""){
		UIkit.modal.alert("주소를 입력하세요.");
		obj.focus();
		return;
	}

	obj = document.RegForm.BizType;
	if (obj.value==""){
		UIkit.modal.alert("업태를 입력하세요.");
		obj.focus();
		return;
	}

	obj = document.RegForm.BizClass;
	if (obj.value==""){
		UIkit.modal.alert("종목을 입력하세요.");
		obj.focus();
		return;
	}

	obj = document.RegForm.ContactName1;
	if (obj.value==""){
		UIkit.modal.alert("담당자명을 입력하세요.");
		obj.focus();
		return;
	}

	obj = document.RegForm.Email1;
	if (obj.value==""){
		UIkit.modal.alert("이메일을 입력하세요.");
		obj.focus();
		return;
	}

	UIkit.modal.confirm(
		'저장 하시겠습니까?', 
		function(){ 
			document.RegForm.action = "tax_member_info_action.php";
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