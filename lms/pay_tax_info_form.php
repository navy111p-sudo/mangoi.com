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
<!-- dropify -->
<link rel="stylesheet" href="assets/skins/dropify/css/dropify.css">
<!-- ============== only this page css ============== -->
<!-- ============== common.css ============== -->
<link rel="stylesheet" type="text/css" href="css/common.css" />
<!-- ============== common.css ============== -->

</head>

<body class="disable_transitions sidebar_main_open sidebar_main_swipe">
<?php
$MainMenuID = 77;
$SubMenuID = 7709;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
?>



<?php
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$Month = date("Ym");
$PayMonth = isset($_REQUEST["PayMonth"]) ? $_REQUEST["PayMonth"] : $Month;


$Sql = "SELECT  
				*
		from PayTaxInfo 
		where PayMonth=:PayMonth";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':PayMonth', $PayMonth);
$Stmt->execute();

if ($Stmt->rowCount() > 0){
	$checkExcute = true;
} else {	
	// 먼저 가장 최근 입력되어 있는 과세항목 정보를 읽어와서 삽입한다.
	
	$Sql1 = "INSERT  into PayTaxInfo (BasePay, SpecialDutyPay, PositionPay, OverTimePay, ReplacePay, IncentivePay, Special1, Special2, PayMonth,
				Add1, Add2, Add3, Add4, Add5, Add6, Add7, Add1Name, Add2Name, Add3Name, Add4Name, Add5Name, Add6Name, Add7Name ) 
			(select BasePay, SpecialDutyPay, PositionPay, OverTimePay, ReplacePay, IncentivePay, Special1, Special2, :PayMonth, 
				Add1, Add2, Add3, Add4, Add5, Add6, Add7, Add1Name, Add2Name, Add3Name, Add4Name, Add5Name, Add6Name, Add7Name 
			 from PayTaxInfo order by PayMonth desc limit 1)";
	$Stmt1 = $DbConn->prepare($Sql1);
	$Stmt1->bindParam(':PayMonth', $PayMonth);
	$Stmt1->execute();
	$checkExcute = false;

}	

// 새로운 항목을 추가했을 때는 다시 한번 sql 문을 실행시킨다. 
if (!$checkExcute) {
	$Stmt->execute();
}


$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TaxInfoID = $Row["TaxInfoID"];
$BasePay = $Row["BasePay"];
$SpecialDutyPay = $Row["SpecialDutyPay"];
$PositionPay = $Row["PositionPay"];
$OverTimePay = $Row["OverTimePay"];
$ReplacePay = $Row["ReplacePay"];
$IncentivePay = $Row["IncentivePay"];
$Special1 = $Row["Special1"];
$Special2 = $Row["Special2"];
$Special3 = $Row["Special3"];
$Special4 = $Row["Special4"];
$Add1 = $Row["Add1"];
$Add1Name = $Row["Add1Name"];
$Add2 = $Row["Add2"];
$Add2Name = $Row["Add2Name"];
$Add3 = $Row["Add3"];
$Add3Name = $Row["Add3Name"];
$Add4 = $Row["Add4"];
$Add4Name = $Row["Add4Name"];
$Add5 = $Row["Add5"];
$Add5Name = $Row["Add5Name"];
$Add6 = $Row["Add6"];
$Add6Name = $Row["Add6Name"];
$Add7 = $Row["Add7"];
$Add7Name = $Row["Add7Name"];
?>
<div id="page_content">
	<div id="page_content_inner">
		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="DepartmentID" value="<?=$DepartmentID?>">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-10-10">
				<div class="md-card">
					<div class="user_content">
						<ul class="uk-margin">
							<div>
								<div class="uk-margin-top">
									<h3 class="full_width_in_card heading_c"> 
										<?=$과세항목설정[$LangID]?>
									</h3>
									<h3 class="heading_b uk-margin-bottom"><?=$귀속년월[$LangID]?> : 
										<select id="PayMonth" name="PayMonth" class="uk-width-2-4" style="min-width:159px" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="귀속년월 선택"/>
											<option value=""><?=$귀속년월선택[$LangID]?></option>
											<?
											for ($yearCount=((int)date("Y")); $yearCount>=2021; $yearCount--) {
												if ($yearCount == date("Y")) $maxMonth = date("m");
													else $maxMonth =12;
												for ($monthCount=$maxMonth; $monthCount>=1; $monthCount--) {
													$optMonth = $yearCount.sprintf('%02d',$monthCount);
											?>
												<option value="<?=$yearCount?><?=sprintf('%02d',$monthCount)?>" <?if ($PayMonth==$optMonth){?>selected<?}?>><?=$yearCount?> 년 <?=$monthCount?> 월</option>
											<?
												}
											}
											?>
										</select>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-2-10">
											<label for="BasePay"><?=$기본급[$LangID]?></label>
											<input type="checkbox" id="BasePay" name="BasePay" value="1" <?php if ($BasePay==1) { echo "checked";}?> data-switchery/>
										</div>
										<div class="uk-width-medium-2-10">
											<label for="SpecialDutyPay"><?=$특무수당[$LangID]?></label>
											<input type="checkbox" id="SpecialDutyPay" name="SpecialDutyPay" value="1" <?php if ($SpecialDutyPay==1) { echo "checked";}?> data-switchery/>
										</div>
										<div class="uk-width-medium-2-10">
											<label for="PositionPay"><?=$직책수당[$LangID]?></label>
											<input type="checkbox" id="PositionPay" name="PositionPay" value="1" <?php if ($PositionPay==1) { echo "checked";}?> data-switchery/>
										</div>											
										<div class="uk-width-medium-2-10">
											<label for="OverTimePay"><?=$초과근무수당[$LangID]?></label>
											<input type="checkbox" id="OverTimePay" name="OverTimePay" value="1" <?php if ($OverTimePay==1) { echo "checked";}?> data-switchery/>	
										</div>											
										<div class="uk-width-medium-2-10">
											<label for="ReplacePay"><?=$대체수당[$LangID]?></label>
											<input type="checkbox" id="ReplacePay" name="ReplacePay" value="1" <?php if ($ReplacePay==1) { echo "checked";}?> data-switchery/>
										</div>											
										<div class="uk-width-medium-2-10">
											<label for="IncentivePay"><?=$인센티브[$LangID]?></label>
											<input type="checkbox" id="IncentivePay" name="IncentivePay" value="1" <?php if ($IncentivePay==1) { echo "checked";}?> data-switchery/>		
											
										</div>
										<div class="uk-width-medium-2-10">
											<label for="Special1"><?=$상여금1[$LangID]?></label>
											<input type="checkbox" id="Special1" name="Special1" value="1" <?php if ($Special1==1) { echo "checked";}?> data-switchery/>
											
										</div>										
										<div class="uk-width-medium-2-10">
											<label for="Special2"><?=$상여금2[$LangID]?></label>
											<input type="checkbox" id="Special2" name="Special2" value="1" <?php if ($Special2==1) { echo "checked";}?> data-switchery/>										
										</div>										
<?php
	// 루프를 돌면서 Add1~7 사이에 값이 있으면 출력하고 없으면 그냥 넘어간다. 
	for ($i=1;$i<=7;$i++){
		if (${"Add".$i."Name"}!= ""){
?>			
										<div class="uk-width-medium-2-10">
											<label for="Add<?=$i?>"><?=${"Add".$i."Name"}?></label>
											<input type="checkbox" id="Add<?=$i?>" name="Add<?=$i?>" value="1" <?php if (${"Add".$i}==1) { echo "checked";}?> data-switchery/>
											<a type="button" onClick="DeletePayItem(<?=$i?>);" class="md-btn md-btn-warning">삭제</a>
										</div>		
<?php
		}
	}
?>
									</div>
									<div class="uk-form-row">
								</div>
							</div>
						</ul>
					</div>
				</div>
			</div>
			

		</div>
		</form>
		<div class="uk-form-row" style="text-align:center;margin-top:15px">
			<a type="button" onClick="FormSubmit();" class="md-btn md-btn-primary"><?=$수정[$LangID]?></a>
			<a type="button" onClick="OpenPayItem(<?=$TaxInfoID?>);" class="md-btn md-btn-primary">항목추가</a>
		</div>
	</div>
</div>


<?
include_once('./inc_common_form_js.php');
?>
<!-- ==============  only this page js ============== -->
<!--  dropify -->
<script src="bower_components/dropify/dist/js/dropify.min.js"></script>
<!--  form file input functions -->
<script src="assets/js/pages/forms_file_input.min.js"></script>
<script>
$(function() {
	if(isHighDensity()) {
		$.getScript( "assets/js/custom/dense.min.js", function(data) {
			// enable hires images
			altair_helpers.retina_images();
		});
	}
	if(Modernizr.touch) {
		// fastClick (touch devices)
		FastClick.attach(document.body);
	}
});
$window.load(function() {
	// ie fixes
	altair_helpers.ie_fix();
});
</script>


<!-- ==============  only this page js ============== -->
<!-- ==============  common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ==============  common.js ============== -->

<script language="javascript">

function FormSubmit(){

	UIkit.modal.confirm(
		'<?=$저장하시겠습니까[$LangID]?>?', 
		function(){ 
				document.RegForm.action = "pay_tax_info_action.php";
				document.RegForm.submit();
		}
	);

}

function DeletePayItem(itemNumber){

	UIkit.modal.confirm(
		'<?=$삭제하시겠습니까[$LangID]?>?', 
		function(){ 
			document.RegForm.action = "pay_tax_info_delete_action.php?itemNumber="+itemNumber+"&PayMonth="+<?=$PayMonth?>;
			document.RegForm.submit();
		}
	);

}

function SearchSubmit(){
	document.RegForm.action = "pay_tax_info_form.php";
	document.RegForm.submit();
}

function OpenPayItem(TaxInfoID) {

	var OpenUrl = "./pay_tax_info_write_form.php?TaxInfoID="+TaxInfoID;
	$.colorbox({	
		href:OpenUrl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "850"
		,maxHeight: "536"
		,title:""
		,iframe:true 
		,scrolling:true

		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}


</script>



<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>