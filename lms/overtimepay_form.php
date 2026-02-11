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

<body class="disable_transitions sidebar_main_open sidebar_main_swipe">
<?php
$MainMenuID = 77;
$SubMenuID = 7704;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');

$MemberLoginID     = isset($_COOKIE["LoginMemberID"   ]) ? $_COOKIE["LoginMemberID"   ] : "";
#-----------------------------------------------------------------------------------------------------------------------------------------#
# 회원 고유아이디(번호, 조직아이디) 찾기
#-----------------------------------------------------------------------------------------------------------------------------------------#
$Sql = "select T.*,M.* from Members as M 
			  left join Hr_OrganLevelTaskMembers T on T.MemberID=M.MemberID
			      where M.MemberLoginID=:MemberLoginID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberLoginID', $MemberLoginID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$My_MemberID     = $Row["MemberID"];
$My_MemberName   = $Row["MemberName"];
$My_OrganLevel   = $Row["Hr_OrganLevel"];    
$My_OrganLevelID = $Row["Hr_OrganLevelID"];


?>


<?php
// $PayMonth 급여 귀속년월 기본적으로 지난달을 귀속년월로 잡아주고
// 조건을 바꾸면 거기에 맞게 귀속년월이 바뀐다. 매월 1일로 설정한다.(혼선 방지)
$Month = date("Y-m-01");
$PayMonth = isset($_REQUEST["PayMonth"]) ? $_REQUEST["PayMonth"] : $Month;


// 현재 급여정보가 입력중인지 확인. 만약 입력된 정보가 없거나, 이미 결재요청중이거나 지급완료인 경우에는 진행하지 않는다.
$Sql = "SELECT *
			FROM PayMonthState 
			WHERE PayMonth = :PayMonth AND PayState = 0";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':PayMonth', $PayMonth);
$Stmt->execute();
$rowCount = $Stmt->rowCount();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();

?>


<div id="page_content">
	<div id="page_content_inner">
		<form name="SearchForm" method="get">
			<input type="hidden" name="PayInputMode" value="false">
			<h3 class="heading_b uk-margin-bottom">초과근무수당 입력</h3>
			<h3 class="heading_b uk-margin-bottom">귀속년월 : 
			<select id="PayMonth" name="PayMonth" class="uk-width-1-4" onchange="SearchSubmit(false)" data-md-select2 data-allow-clear="true" data-placeholder="귀속년월 선택"/>
					<option value="">귀속년월 선택</option>
					<?
					for ($yearCount=2021; $yearCount<=((int)date("Y")); $yearCount++) {
						if ($yearCount == date("Y")) $maxMonth = date("m");
							else $maxMonth =12;
						for ($monthCount=1; $monthCount<=$maxMonth; $monthCount++) {
							$optMonth = $yearCount."-".sprintf('%02d',$monthCount)."-01";
					?>
						<option value="<?=$yearCount?>-<?=sprintf('%02d',$monthCount)?>-01" <?if ($PayMonth==$optMonth){?>selected<?}?>><?=$yearCount?> 년 <?=$monthCount?> 월</option>
					<?
						}
					}
					?>
				</select>
			</h3>
		</form>
		<? if ($rowCount > 0) { 
				$PayMonthStateID = $Row["PayMonthStateID"];	

				$Sql = "SELECT *
					FROM PayOverTime
					WHERE PayMonthStateID = :PayMonthStateID AND MemberID = :MemberID";

				$Stmt = $DbConn->prepare($Sql);
				$Stmt->bindParam(':PayMonthStateID', $PayMonthStateID);
				$Stmt->bindParam(':MemberID', $My_MemberID);
				$Stmt->execute();
				$Stmt->setFetchMode(PDO::FETCH_ASSOC);
				$Row = $Stmt->fetch();
				$OverTimePay = $Row["OverTimePay"];	
		?>

		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="PayMonth" value="<?=$PayMonth?>">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname">초과 근무수당 등록</span></h2>
						</div>
					</div>
					<div class="user_content">
						
						<div class="uk-margin-top">
							<div class="uk-margin-top" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<label for="OverTimePay">초과 근무 수당</label>
									<input type="text" id="OverTimePay" name="OverTimePay" value="<?=$OverTimePay?>" class="md-input label-fixed"/>
								</div>
							</div>
						</div>

						<hr>

						<div class="uk-margin-top" style="text-align:center;padding-top:30px;">
							<a type="button" href="javascript:FormSubmit();" class="md-btn md-btn-primary"><?=$제출하기[$LangID]?></a>
						</div>

					</div>
				</div>
			</div>

		</div>
		</form>
		<?} else {?>
			<div class="md-card" style="height:500px;text-align:center;">
				<h3 style="padding-top:200px">초과근무수당을 입력할 수 없습니다. 급여정보가 없거나 이미 정보 입력이 끝난 상태입니다.</h3>	
			</div>

		<?}?>

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

	obj = document.RegForm.OverTimePay;
	if (obj.value==""){
		UIkit.modal.alert("초과근무수당을 입력하세요!");
		obj.focus();
		return;
	}

	UIkit.modal.confirm(
		'<?=$저장_하시겠습니까[$LangID]?>?', 
		function(){ 
			document.RegForm.action = "overtimepay_action.php";
			document.RegForm.submit();
		}
	);

}

function SearchSubmit(){
	document.SearchForm.action = "overtimepay_form.php";
	document.SearchForm.submit();
}
</script>






<?php
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>