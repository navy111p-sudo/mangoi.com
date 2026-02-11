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
$SubMenuID = 7706;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');

?>


<div id="page_content">
	<div id="page_content_inner">
		<h3 class="heading_b uk-margin-bottom"><?=$급여열람권한설정[$LangID]?></h3>
		<?
			// 3레벨 이상의 간부급의 급여 열람 권한을 가지고 온다.
			$Sql = "SELECT A.MemberID AS MID, A.StaffID, A.MemberName, B.Hr_OrganPositionName, C.* FROM Members A
						INNER JOIN Hr_OrganLevelTaskMembers B ON A.MemberID = B.MemberID 
						LEFT JOIN PayAuth C ON A.MemberID = C.MemberID
						WHERE B.Hr_OrganLevel <= 4";

			$Stmt = $DbConn->prepare($Sql);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);

		?>
		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="PayMonth" value="<?=$PayMonth?>">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-10-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$급여열람권한설정[$LangID]?></span></h2>
						</div>
					</div>
					<div class="user_content">
 						<div class="uk-overflow-container">
						<table class="uk-table uk-table-align-vertical">
							<thead>
								<tr>
									<th nowrap>No</th>
									<th nowrap><?=$교사_및_직원명[$LangID]?></th>
									<th nowrap><?=$직무[$LangID]?></th>
									<th nowrap><?=$기본급[$LangID]?></th>
									<th nowrap><?=$특무수당[$LangID]?></th>
									<th nowrap><?=$직책수당[$LangID]?></th>
									<th nowrap><?=$초과근무수당[$LangID]?></th>
									<th nowrap><?=$대체수당[$LangID]?></th>
									<th nowrap><?=$인센티브[$LangID]?></th>
									<th nowrap><?=$상여금1[$LangID]?></th>
									<th nowrap><?=$상여금2[$LangID]?></th>
								</tr>
							</thead>
							<tbody>
								
								<?php
								$ListCount = 1;
								while($Row = $Stmt->fetch()) {
									$MemberName = $Row["MemberName"];
									$MemberID = $Row["MID"];
									$Hr_OrganPositionName = $Row["Hr_OrganPositionName"];
									$BasePayAuth = $Row["BasePayAuth"];
									$SpecialDutyPayAuth = $Row["SpecialDutyPayAuth"];
									$PositionPayAuth = $Row["PositionPayAuth"];
									$OverTimePayAuth = $Row["OverTimePayAuth"];
									$ReplacePayAuth = $Row["ReplacePayAuth"];
									$IncentivePayAuth = $Row["IncentivePayAuth"];
									$Special1Auth = $Row["Special1Auth"];
									$Special2Auth = $Row["Special2Auth"];
									// 만약 PayAuth 에 레코드가 없으면 새로 하나 생성해 준다.
									if (!isset($Row["BasePayAuth"])){
										$Sql2 = "INSERT INTO PayAuth (MemberID) VALUES ('".$MemberID."')";

										$Stmt2 = $DbConn->prepare($Sql2);
										$Stmt2->execute();
									}	
									
								?>
								<tr>
									<td class="uk-text-nowrap uk-table-td-center"><?=$ListCount?></td>
									<td class="uk-text-nowrap uk-table-td-center"><?=$MemberName?></td>
									<td class="uk-text-nowrap uk-table-td-center"><?=$Hr_OrganPositionName?></td>
									<td class="uk-text-nowrap uk-table-td-center">
										<? if ($BasePayAuth == 0) { ?>
											<a type="button" id="btnBasePayAuth<?=$MemberID?>" href="javascript:changeAuth('BasePayAuth',<?=$MemberID?>,1);" class="md-btn " style="background-color:white;color:black;">열람불가</a>
										<?} else {?>
											<a type="button" id="btnBasePayAuth<?=$MemberID?>" href="javascript:changeAuth('BasePayAuth',<?=$MemberID?>,0);" class="md-btn"  style="background-color:#556BAC;color:white;">열람가능</a>
										<?}?>
									</td>
									<td class="uk-text-nowrap uk-table-td-center">
										<? if ($SpecialDutyPayAuth == 0) { ?>
											<a type="button" id="btnSpecialDutyPayAuth<?=$MemberID?>" href="javascript:changeAuth('SpecialDutyPayAuth',<?=$MemberID?>,1);" class="md-btn " style="background-color:white;color:black;">열람불가</a>
										<?} else {?>
											<a type="button" id="btnSpecialDutyPayAuth<?=$MemberID?>" href="javascript:changeAuth('SpecialDutyPayAuth',<?=$MemberID?>,0);" class="md-btn"  style="background-color:#556BAC;color:white;">열람가능</a>
										<?}?>
									</td>
									<td class="uk-text-nowrap uk-table-td-center">
										<? if ($PositionPayAuth == 0) { ?>
											<a type="button" id="btnPositionPayAuth<?=$MemberID?>" href="javascript:changeAuth('PositionPayAuth',<?=$MemberID?>,1);" class="md-btn " style="background-color:white;color:black;">열람불가</a>
										<?} else {?>
											<a type="button" id="btnPositionPayAuth<?=$MemberID?>" href="javascript:changeAuth('PositionPayAuth',<?=$MemberID?>,0);" class="md-btn"  style="background-color:#556BAC;color:white;">열람가능</a>
										<?}?>
									</td>
									<td class="uk-text-nowrap uk-table-td-center">
										<? if ($OverTimePayAuth == 0) { ?>
											<a type="button" id="btnOverTimePayAuth<?=$MemberID?>" href="javascript:changeAuth('OverTimePayAuth',<?=$MemberID?>,1);" class="md-btn " style="background-color:white;color:black;">열람불가</a>
										<?} else {?>
											<a type="button" id="btnOverTimePayAuth<?=$MemberID?>" href="javascript:changeAuth('OverTimePayAuth',<?=$MemberID?>,0);" class="md-btn"  style="background-color:#556BAC;color:white;">열람가능</a>
										<?}?>
									</td>
									<td class="uk-text-nowrap uk-table-td-center">
										<? if ($ReplacePayAuth == 0) { ?>
											<a type="button" id="btnReplacePayAuth<?=$MemberID?>" href="javascript:changeAuth('ReplacePayAuth',<?=$MemberID?>,1);" class="md-btn " style="background-color:white;color:black;">열람불가</a>
										<?} else {?>
											<a type="button" id="btnReplacePayAuth<?=$MemberID?>" href="javascript:changeAuth('ReplacePayAuth',<?=$MemberID?>,0);" class="md-btn"  style="background-color:#556BAC;color:white;">열람가능</a>
										<?}?>
									</td>
									<td class="uk-text-nowrap uk-table-td-center">
										<? if ($IncentivePayAuth == 0) { ?>
											<a type="button" id="btnIncentivePayAuth<?=$MemberID?>" href="javascript:changeAuth('IncentivePayAuth',<?=$MemberID?>,1);" class="md-btn " style="background-color:white;color:black;">열람불가</a>
										<?} else {?>
											<a type="button" id="btnIncentivePayAuth<?=$MemberID?>" href="javascript:changeAuth('IncentivePayAuth',<?=$MemberID?>,0);" class="md-btn"  style="background-color:#556BAC;color:white;">열람가능</a>
										<?}?>
									</td>
									<td class="uk-text-nowrap uk-table-td-center">
										<? if ($Special1Auth == 0) { ?>
											<a type="button" id="btnSpecial1Auth<?=$MemberID?>" href="javascript:changeAuth('Special1Auth',<?=$MemberID?>,1);" class="md-btn " style="background-color:white;color:black;">열람불가</a>
										<?} else {?>
											<a type="button" id="btnSpecial1Auth<?=$MemberID?>" href="javascript:changeAuth('Special1Auth',<?=$MemberID?>,0);" class="md-btn"  style="background-color:#556BAC;color:white;">열람가능</a>
										<?}?>
									</td>
									<td class="uk-text-nowrap uk-table-td-center">
										<? if ($Special2Auth == 0) { ?>
											<a type="button" id="btnSpecial2Auth<?=$MemberID?>" href="javascript:changeAuth('Special2Auth',<?=$MemberID?>,1);" class="md-btn " style="background-color:white;color:black;" style="backgroundColor:white">열람불가</a>
										<?} else {?>
											<a type="button" id="btnSpecial2Auth<?=$MemberID?>" href="javascript:changeAuth('Special2Auth',<?=$MemberID?>,0);" class="md-btn"  style="background-color:#556BAC;color:white;" style="backgroundColor:#556BAC">열람가능</a>
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


<script>
function changeAuth(auth,MID,value){

	url = "ajax_set_pay_auth.php";
			
	$.ajax(url, {
		data: {
			AuthType: auth,
			AuthValue: value,
			MemberID: MID
		},
		success: function (data) {
			if (value == 1)	{
				document.getElementById("btn"+auth+MID).style.backgroundColor = "#556BAC";
				document.getElementById("btn"+auth+MID).style.color = "white";
				document.getElementById("btn"+auth+MID).innerHTML = "열람가능";
				document.getElementById("btn"+auth+MID).href = "javascript:changeAuth('"+auth+"',"+MID+",0)";
			} else {
				document.getElementById("btn"+auth+MID).style.backgroundColor = "white";
				document.getElementById("btn"+auth+MID).style.color = "black";
				document.getElementById("btn"+auth+MID).innerHTML = "열람불가";
				document.getElementById("btn"+auth+MID).href = "javascript:changeAuth('"+auth+"',"+MID+",1)";
			}
		},
		error: function () {
			alert('에러 발생. 다시 시도하세요.~');
		}

	

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