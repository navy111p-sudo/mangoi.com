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
?>

<?
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
$SubMenuID = 1188;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');


$Sql = "SELECT 
			count(*) TotalRowCount 
		from Departments ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TotalRowCount = $Row["TotalRowCount"];


$Sql = "SELECT 
			* 
		from Departments";

$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$부서관리[$LangID]?></h3>

		<div class="md-card">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin>
					<div class="uk-width-1-1">
						<div class="uk-overflow-container">
							<table class="uk-table uk-table-align-vertical">
								<thead>
									<tr>
										<th style="width:10%;" nowrap>ID</th>
										<th nowrap><?=$부서명[$LangID]?></th>
										<th style="width:40%;" nowrap><?=$부서영문명[$LangID]?></th>
										<th style="width:10%;" nowrap>비고</th>
									</tr>
								</thead>
								<tbody>
									
									<?php
									while($Row = $Stmt->fetch()) {

										$DepartmentID = $Row["DepartmentID"];
										$DepartmentName = $Row["DepartmentName"];
										$DepartmentNameEng = $Row["DepartmentNameEng"];
										$InUse = $Row["InUse"];

									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><a href="departments_form.php?DepartmentID=<?=$DepartmentID?>"><?=$DepartmentID?></a></td>
										<td class="uk-text-nowrap uk-table-td-center"><a href="departments_form.php?DepartmentID=<?=$DepartmentID?>"><?=$DepartmentName?></a></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$DepartmentNameEng?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=($InUse==0?"미사용중":"사용중")?></td>
									</tr>
									<?php
										$ListCount ++;
									}
									$Stmt = null;
									?>


								</tbody>
							</table>
						</div>
						
						<div class="uk-form-row" style="text-align:center;margin-top:10px">
							<a type="button" href="departments_form.php" class="md-btn md-btn-primary"><?=$신규등록[$LangID]?></a>
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