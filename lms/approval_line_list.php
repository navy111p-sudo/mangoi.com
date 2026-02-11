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
include_once('./inc_common_list_css.php');
?>
<!-- ============== only this page css ============== -->

<!-- ============== only this page css ============== -->
<!-- ============== common.css ============== -->
<link rel="stylesheet" type="text/css" href="./css/common.css" />
<!-- ============== common.css ============== -->
</head>

<body class="disable_transitions sidebar_main_open sidebar_main_swipe">
<?php
$MainMenuID = 11;
$SubMenuID = 1199;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');

?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom">결재라인 관리</h3>

		<div class="md-card">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin>
					<div class="uk-width-1-1">
						<div class="uk-overflow-container">
							<table class="uk-table uk-table-align-vertical">
								<thead>
									<tr>
										<th style="width:10%;" nowrap>ID</th>
										<th nowrap>문서명</th>
										<th style="width:10%;" nowrap>비고</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><a href="approval_line_form.php?DocumentType=0">1</a></td>
										<td class="uk-text-nowrap uk-table-td-center"><a href="approval_line_form.php?DocumentType=0">지출품의서</a></td>
										<td class="uk-text-nowrap uk-table-td-center"></td>
									</tr>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><a href="approval_line_form.php?DocumentType=1">2</a></td>
										<td class="uk-text-nowrap uk-table-td-center"><a href="approval_line_form.php?DocumentType=1">휴가계획서</a></td>
										<td class="uk-text-nowrap uk-table-td-center"></td>
									</tr>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><a href="approval_line_form.php?DocumentType=2">3</a></td>
										<td class="uk-text-nowrap uk-table-td-center"><a href="approval_line_form.php?DocumentType=2">필리핀 강사 지출품의서</a></td>
										<td class="uk-text-nowrap uk-table-td-center"></td>
									</tr>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><a href="approval_line_form.php?DocumentType=3">4</a></td>
										<td class="uk-text-nowrap uk-table-td-center"><a href="approval_line_form.php?DocumentType=3">필리핀 강사 휴가계획서</a></td>
										<td class="uk-text-nowrap uk-table-td-center"></td>
									</tr>


								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>

<?
include_once('./inc_common_list_js.php');
?>
<!-- ============== only this page js ============== -->

<!-- ============== only this page js ============== -->
<!-- ============== common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ============== common.js ============== -->

<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>