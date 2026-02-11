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
$MainMenuID = 77;
$SubMenuID = 7708;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');


$Sql = "SELECT 
			count(*) TotalRowCount 
		from PayInsuranceRate ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TotalRowCount = $Row["TotalRowCount"];


$Sql = "SELECT 
			* 
		from PayInsuranceRate order by year desc";

$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$사대보험요율관리[$LangID]?></h3>

		<div class="md-card">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin>
					<div class="uk-width-1-1">
						<div class="uk-overflow-container">
							<table class="uk-table uk-table-align-vertical">
								<thead>
									<tr>
										<th style="width:10%;" nowrap>ID</th>
										<th style="width:18%;"nowrap><?=$귀속년도[$LangID]?></th>
										<th style="width:18%;" nowrap><?=$고용보험[$LangID]?></th>
										<th style="width:18%;" nowrap><?=$건강보험[$LangID]?></th>
										<th style="width:18%;" nowrap><?=$장기요양보험[$LangID]?></th>
										<th style="width:18%;" nowrap><?=$국민연금[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
									
									<?php
									while($Row = $Stmt->fetch()) {

										$InsuranceID = $Row["InsuranceID"];
										$Year = $Row["Year"];
										$EmploymentInsurance = $Row["EmploymentInsurance"];
										$HealthInsurance = $Row["HealthInsurance"];
										$CareInsurance = $Row["CareInsurance"];
										$NationalPension = $Row["NationalPension"];
									
									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><a href="pay_insurance_rate_form.php?InsuranceID=<?=$InsuranceID?>"><?=$InsuranceID?></a></td>
										<td class="uk-text-nowrap uk-table-td-center"><a href="pay_insurance_rate_form.php?InsuranceID=<?=$InsuranceID?>"><?=$Year?></a></td>
										<td class="uk-text-nowrap uk-table-td-center"><a href="pay_insurance_rate_form.php?InsuranceID=<?=$InsuranceID?>"><?=$EmploymentInsurance?></a></td>
										<td class="uk-text-nowrap uk-table-td-center"><a href="pay_insurance_rate_form.php?InsuranceID=<?=$InsuranceID?>"><?=$HealthInsurance?></a></td>
										<td class="uk-text-nowrap uk-table-td-center"><a href="pay_insurance_rate_form.php?InsuranceID=<?=$InsuranceID?>"><?=$CareInsurance?></a></td>
										<td class="uk-text-nowrap uk-table-td-center"><a href="pay_insurance_rate_form.php?InsuranceID=<?=$InsuranceID?>"><?=$NationalPension?></a></td>
										
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
							<a type="button" href="pay_insurance_rate_form.php" class="md-btn md-btn-primary"><?=$신규등록[$LangID]?></a>
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