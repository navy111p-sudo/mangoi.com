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
$SearchState = isset($_REQUEST["SearchState"]) ? $_REQUEST["SearchState"] : "";

$Sql = "select * from Hr_Evaluations where Hr_EvaluationID=".$SearchState;

$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$ListCount = 1;
$Row = $Stmt->fetch();
if ($Row) {	
	$Hr_EvaluationID       = $Row["Hr_EvaluationID"];
	$Hr_EvaluationYear     = $Row["Hr_EvaluationYear"];
	$Hr_EvaluationMonth    = $Row["Hr_EvaluationMonth"];
	$Hr_EvaluationName     = $Row["Hr_EvaluationName"];

	$SearchStateName = $Hr_EvaluationYear."<?=$년[$LangID]?>".substr("0".$Hr_EvaluationMonth,-2)."<?=$월월[$LangID]?>";

}
$Stmt = null;
?>


<div id="page_content">
	<div id="page_content_inner">
		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="SearchState" value="<?=$SearchState?>">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$SearchStateName?><?=$역량평가대상자_추가[$LangID]?></span></h2>
						</div>
					</div>
					<div class="user_content">

						<div class="uk-margin-top" id="Div_Hr_Evaluation_1">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-10-10">
									<label style="display:inline-block;width:150px;"><?=$평가대상자[$LangID]?></label>

									<div style="display:inline-block;">
										<select id="MemberID" name="MemberID" style="width:300px;height:30px;border:1px solid #cccccc;">
										<option value=""><?=$선택[$LangID]?></option>
										<?
										$Sql = "select B.MemberID,B.MemberName 
												          from Staffs A 
										            inner join Members B on A.StaffID=B.StaffID
												where A.StaffState<>2 
												order by A.StaffOrder desc";
										$Stmt = $DbConn->prepare($Sql);
										$Stmt->execute();
										$Stmt->setFetchMode(PDO::FETCH_ASSOC);
										while($Row = $Stmt->fetch()) {

											$MemberID   = $Row["MemberID"];
											$MemberName = $Row["MemberName"];
										?>
										<option value="<?=$MemberID?>"><?=$MemberName?> (<?=$MemberID?>)</option>
										<?
										}
										?>
										</select>
									</div>

								</div>
							</div>
						</div>
						<hr>

						<div class="uk-margin-top" id="Div_Hr_Evaluation_1">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-10-10">
									<label style="display:inline-block;width:150px;"><?=$평가자[$LangID]?></label>

									<div style="display:inline-block;">
										<select id="Hr_EvaluationCompetencyMemberID" name="Hr_EvaluationCompetencyMemberID" style="width:300px;height:30px;border:1px solid #cccccc;">
										<option value=""><?=$선택[$LangID]?></option>
										<?
										$Sql = "select B.MemberID,B.MemberName 
												          from Staffs A 
													inner join Members B on A.StaffID=B.StaffID
												where A.StaffState<>2 
												order by A.StaffOrder desc";
										$Stmt = $DbConn->prepare($Sql);
										$Stmt->execute();
										$Stmt->setFetchMode(PDO::FETCH_ASSOC);
										while($Row = $Stmt->fetch()) {

											$MemberID = $Row["MemberID"];
											$MemberName = $Row["MemberName"];
										?>
										<option value="<?=$MemberID?>"><?=$MemberName?> (<?=$MemberID?>)</option>
										<?
										}
										?>
										</select>
									</div>
								</div>
							</div>
						</div>
						<hr>

						<div class="uk-margin-top">
							<div class="uk-margin-top" data-uk-grid-margin>
								<label style="display:inline-block;width:150px;"><?=$평가자_유형[$LangID]?></label>
								<span class="icheck-inline">
									<input type="radio" class="radio_input" name="Hr_EvaluationCompetencyMemberType" id="Hr_EvaluationCompetencyMemberType1" value="1" />
									<label for="Hr_EvaluationCompetencyMemberType1" class="radio_label"><span class="radio_bullet"></span><?=$부하[$LangID]?></label>
								</span>
								<span class="icheck-inline">
									<input type="radio" class="radio_input" name="Hr_EvaluationCompetencyMemberType" id="Hr_EvaluationCompetencyMemberType2" value="2" />
									<label for="Hr_EvaluationCompetencyMemberType2" class="radio_label"><span class="radio_bullet"></span><?=$동료[$LangID]?></label>
								</span>
								<span class="icheck-inline">
									<input type="radio" class="radio_input" name="Hr_EvaluationCompetencyMemberType" id="Hr_EvaluationCompetencyMemberType3" value="3" />
									<label for="Hr_EvaluationCompetencyMemberType3" class="radio_label"><span class="radio_bullet"></span><?=$상사[$LangID]?></label>
								</span>
							</div>
						</div>
						<hr>
						<div class="uk-margin-top" id="Div_Hr_Evaluation_1">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-10-10">
									<label style="display:inline-block;width:150px;"><?=$가중치[$LangID]?></label>
								    <span class="icheck-inline">
									     <input type="text" name="Hr_EvaluationCompetencyAddValue" id="Hr_EvaluationCompetencyAddValue" style="height:25px;width:20%;border:1px solid #cccccc;padding-left:10px;padding-right:10px; text-align:right;"/>%
								    </span>
								</div>
							</div>
						</div>
						<hr>
						<div class="uk-margin-top" id="Div_Hr_Evaluation_1">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-10-10 uk-table-td-center">
									<?=$평가대상자에_대한_평가자_계속_추가는_평가대상자를_먼저_저장하고_리스트에서_추가작업_하시기_바랍니다[$LangID]?>!
								</div>
							</div>
						</div>
						<hr>

						<div class="uk-margin-top" style="text-align:center;padding-top:30px;">
							<a type="button" href="javascript:EvaluationCompetencyAdd_Act();" class="md-btn md-btn-primary"><?=$저장하기[$LangID]?></a>
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

//---------------------------------------------------------------------------------------------------------------------------//
// 숫자인지 체크
//---------------------------------------------------------------------------------------------------------------------------//
function IsNumberCalc(indata) {

      for(var i = 0; i < indata.length; i++) {
             var chr = indata.substr(i,1);
             if(chr < '0' || chr > '9') {
                  return false;
             }
      }
      return true;

}

function EvaluationCompetencyAdd_Act(){

	obj = document.RegForm.MemberID;
	if (obj.value==""){
		UIkit.modal.alert("<?=$역량평가_대상자를_선택해_주세요[$LangID]?>");
		obj.focus();
		return;
	}
	obj = document.RegForm.Hr_EvaluationCompetencyMemberID;
	if (obj.value==""){
		UIkit.modal.alert("<?=$역량평가자를_선택해_주세요[$LangID]?>");
		obj.focus();
		return;
	}
	obj1 = document.getElementById("Hr_EvaluationCompetencyMemberType1");
	obj2 = document.getElementById("Hr_EvaluationCompetencyMemberType2");
	obj3 = document.getElementById("Hr_EvaluationCompetencyMemberType3");
	if (obj1.checked==false && obj2.checked==false && obj3.checked==false){
		UIkit.modal.alert("<?=$역량평가자_유형을_선택해_주세요[$LangID]?>");
		return;
	}

	obj = document.RegForm.Hr_EvaluationCompetencyAddValue;
	if (obj.value==""){
		UIkit.modal.alert("<?=$가중치를_입력해_주세요[$LangID]?>");
		obj.focus();
		return;
	}
    if ( !IsNumberCalc(obj.value) ) {
		UIkit.modal.alert("<?=$가중치는_숫자로만_입력해_주세요[$LangID]?>");
		obj.value = "";
		obj.focus();
		return;
    }

	UIkit.modal.confirm(
		'<?=$평가대상자를_추가_하시겠습니까[$LangID]?>?', 
		function(){ 
			 document.RegForm.action = "hr_evaluation_competency_member_insert_action.php";
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