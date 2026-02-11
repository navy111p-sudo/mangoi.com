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
$SubMenuID = 7705;
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
$PayMonthStateID = $Row["PayMonthStateID"];
?>


<div id="page_content">
	<div id="page_content_inner">
		<form name="SearchForm" method="get">
			<input type="hidden" name="PayInputMode" value="false">
			<h3 class="heading_b uk-margin-bottom"><?=$초과근무수당결재[$LangID]?></h3>
			<h3 class="heading_b uk-margin-bottom"><?=$귀속년월[$LangID]?> : 
			<select id="PayMonth" name="PayMonth" class="uk-width-1-4" onchange="SearchSubmit(false)" data-md-select2 data-allow-clear="true" data-placeholder="귀속년월 선택"/>
					<option value=""><?=$귀속년월선택[$LangID]?></option>
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
			// 자기 부서 부하의 초과근무수당 정보를 가지고 온다.
			$Sql = "SELECT  
						AAAA.MemberID, BBBB.MemberName, AA.Hr_OrganLevelID, AA.Hr_OrganLevel, OverTimePay, Approval
						from Hr_OrganLevelTaskMembers AAAA 
						inner join Members BBBB on AAAA.MemberID=BBBB.MemberID and BBBB.MemberState=1 
						left outer join Hr_OrganLevels C on AAAA.Hr_OrganLevelID=C.Hr_OrganLevelID 
						LEFT JOIN (SELECT A.MemberID, MemberName,Hr_OrganLevelID,Hr_OrganLevel FROM Hr_OrganLevelTaskMembers A 
						INNER JOIN Members B ON A.MemberID=B.MemberID AND B.MemberState=1) AS AA 
						ON (AA.Hr_OrganLevelID=C.Hr_OrganLevel3ID)
						LEFT JOIN PayOverTime P ON AAAA.MemberID = P.MemberID AND P.PayMonthStateID = :PayMonthStateID					
						WHERE AAAA.Hr_OrganLevel=4 AND AA.MemberID=:MemberID";

			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':MemberID', $My_MemberID);
			$Stmt->bindParam(':PayMonthStateID', $PayMonthStateID);
			$Stmt->execute();
			$rowCount2 = $Stmt->rowCount();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);
			

		?>
		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="PayMonth" value="<?=$PayMonth?>">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-9-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$초과근무수당결재[$LangID]?></span></h2>
						</div>
					</div>
					<div class="user_content">
 						<div class="uk-overflow-container">
						<table class="uk-table uk-table-align-vertical">
							<thead>
								<tr>
									<th nowrap>No</th>
									<th nowrap><?=$교사_및_직원명[$LangID]?></th>
									<th nowrap><?=$아이디[$LangID]?></th>
									<th nowrap><?=$초과근무수당[$LangID]?></th>
									<th nowrap><?=$승인[$LangID]?></th>
								</tr>
							</thead>
							<tbody>
								
								<?php
								$ListCount = 1;
								while($Row = $Stmt->fetch()) {
									$MemberName = $Row["MemberName"];
									$MemberID = $Row["MemberID"];
									$Hr_OrganLevelID = $Row["Hr_OrganLevelID"];
									$Hr_OrganLevel = $Row["Hr_OrganLevel"];
									$OverTimePay = $Row["OverTimePay"];
									$Approval = $Row["Approval"];
									
								?>
								<tr>
									<td class="uk-text-nowrap uk-table-td-center"><?=$ListCount?></td>
									<td class="uk-text-nowrap uk-table-td-center"><?=$MemberName?></td>
									<td class="uk-text-nowrap uk-table-td-center"><?=$MemberID?></td>
									<td class="uk-text-nowrap uk-table-td-center"><?=$OverTimePay?></td>
									<td class="uk-text-nowrap uk-table-td-center">
										<? if ($Approval == 0 && $OverTimePay != NULL) { ?>
											<a type="button" href="overtimepay_confirm_action.php?PayMonth=<?=$PayMonth?>&MemberID=<?=$MemberID?>&ApprovalOK=1&OverTimePay=<?=$OverTimePay?>" class="md-btn md-btn-primary">승인하기</a>
										<?} else if ($Approval == 0 && $OverTimePay == NULL) {?>
											초과근무수당 미입력
										<?} else {?>
											<a type="button"  class="md-btn md-btn-worning">승인완료</a>	
										<?}?>
									</td>
								</tr>
								<?php
									$ListCount ++;
								}
								$Stmt = null;
								?>


							</tbody>
						</table>
						</div>

					</div>
				</div>
			</div>

		</div>
		</form>
	<?} else {?>
			<div class="md-card" style="height:500px;text-align:center;">
				<h3 style="padding-top:200px">결재할 초과근무수당이 없습니다. 수당정보가 없거나 이미 정보 입력이 끝난 상태입니다.</h3>	
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

	UIkit.modal.confirm(
		'<?=$저장_하시겠습니까[$LangID]?>?', 
		function(){ 
			document.RegForm.action = "overtimepay_action.php";
			document.RegForm.submit();
		}
	);

}

function SearchSubmit(){
	document.SearchForm.action = "overtimepay_confirm.php";
	document.SearchForm.submit();
}
</script>






<?php
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>