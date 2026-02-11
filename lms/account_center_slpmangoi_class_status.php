<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');


if ($_LINK_ADMIN_LEVEL_ID_>10){
	header("Location: center_form.php?CenterID=".$_LINK_ADMIN_CENTER_ID_); 
	exit;
}

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
$MainMenuID = 21;
$SubMenuID = 21054;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
?>

 

<?php
$SLP_BranchID_0=42;//gangseo
$SLP_BranchID_1=107;//seodaemoon
$SLP_BranchID_2=113;//slp
$SLP_BranchID_3=114;//soowon


$AddSqlWhere = "1=1";
$ListParam = "1=1";
$PaginationParam = "1=1";



$CurrentPage = isset($_REQUEST["CurrentPage"]) ? $_REQUEST["CurrentPage"] : "";
$PageListNum = isset($_REQUEST["PageListNum"]) ? $_REQUEST["PageListNum"] : "";

$SearchYear = isset($_REQUEST["SearchYear"]) ? $_REQUEST["SearchYear"] : "";
$SearchMonth = isset($_REQUEST["SearchMonth"]) ? $_REQUEST["SearchMonth"] : "";


if (!$CurrentPage){
    $CurrentPage = 1;    
}
if ($PageListNum==""){
    $PageListNum = 30;
}




if ($SearchYear==""){
	$SearchYear = date("Y");
}

if ($SearchMonth==""){
	$SearchMonth = date("m");
}

$ListParam = $ListParam . "&SearchYear=" . $SearchYear;
$ListParam = $ListParam . "&SearchMonth=" . $SearchMonth;


$AddSqlWhere = $AddSqlWhere . " and A.MemberState=1 ";
$AddSqlWhere = $AddSqlWhere . " and A.MemberLevelID=19 ";
$AddSqlWhere = $AddSqlWhere . " and (
										C.BranchID=".$SLP_BranchID_0."
										or 
										C.BranchID=".$SLP_BranchID_1."
										or 
										C.BranchID=".$SLP_BranchID_2."
										or 
										C.BranchID=".$SLP_BranchID_3."
									) ";

$AddSqlWhere = $AddSqlWhere . " and A.MemberID 
										in (
												select 
													AA.MemberID 
												from Classes AA 
													inner join ClassOrders BB on AA.ClassOrderID=BB.ClassOrderID
												where 
													AA.ClassState=2 
													and AA.StartYear=".$SearchYear." 
													and AA.StartMonth=".$SearchMonth." 
													and (AA.ClassAttendState=1 or AA.ClassAttendState=2 or AA.ClassAttendState=3) 
													and BB.ClassProductID=1 
											) 
								";


$PaginationParam = $ListParam;
if ($CurrentPage!=""){
    $ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}
$ListParam = str_replace("&", "^^", $ListParam);



$Sql = "select 
			count(*) as TotalRowCount
		from Members A 
			inner join Centers B on A.CenterID=B.CenterID 
			inner join Members BB on B.CenterID=BB.CenterID and BB.MemberLevelID=12 
			inner join Branches C on B.BranchID=C.BranchID 
			inner join Members CC on C.BranchID=CC.BranchID and CC.MemberLevelID=9
		where 
			".$AddSqlWhere." ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TotalRowCount = $Row["TotalRowCount"];

$TotalPageCount = ceil($TotalRowCount / $PageListNum);
$StartRowNum = $PageListNum * ($CurrentPage - 1 );



$Sql = "select 
			A.*,
			B.CenterName,
			BB.MemberLoginID as CenterLoginID, 
			C.BranchName,
			CC.MemberLoginID as BranchLoginID,
			(select count(*) FROM Classes AA inner join ClassOrders BB on AA.ClassOrderID=BB.ClassOrderID where AA.ClassState=2 and AA.MemberID=A.MemberID and AA.ClassAttendState<>99 and BB.ClassProductID=1 and AA.StartYear=".$SearchYear." and AA.StartMonth=".$SearchMonth.") as TotalClassCount,
			(select count(*) FROM Classes AA inner join ClassOrders BB on AA.ClassOrderID=BB.ClassOrderID where AA.ClassState=2 and AA.MemberID=A.MemberID and AA.ClassAttendState<>99 and BB.ClassProductID=1 and AA.StartYear=".$SearchYear." and AA.StartMonth=".$SearchMonth." and AA.ClassAttendState=3) as AbsentClassCount,
			(select count(*) FROM Classes AA inner join ClassOrders BB on AA.ClassOrderID=BB.ClassOrderID where AA.ClassState=2 and AA.MemberID=A.MemberID and AA.ClassAttendState<>99 and BB.ClassProductID=1 and AA.StartYear=".$SearchYear." and AA.StartMonth=".$SearchMonth." and (AA.ClassAttendState=1 or AA.ClassAttendState=2)) as AttendClassCount

		from Members A 
			inner join Centers B on A.CenterID=B.CenterID 
			inner join Members BB on B.CenterID=BB.CenterID and BB.MemberLevelID=12 
			inner join Branches C on B.BranchID=C.BranchID 
			inner join Members CC on C.BranchID=CC.BranchID and CC.MemberLevelID=9

		where 
			".$AddSqlWhere."
		order by C.BranchName asc, B.CenterName asc limit $StartRowNum, $PageListNum
		";

$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);


?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$SLP_수업현황[$LangID]?></h3>
		<form name="SearchForm" method="get">
		<div class="md-card" style="margin-bottom:10px;">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin="">



					<div class="uk-width-medium-1-10" style="padding-top:7px;">
						<select id="SearchYear" name="SearchYear" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$년도선택[$LangID]?>" style="width:100%;"/>
							<option value=""><?=$년도선택[$LangID]?></option>
							<?
							for ($iiii=$SearchYear-1;$iiii<=$SearchYear+1;$iiii++) {
							?>
							<option value="<?=$iiii?>" <?if ($SearchYear==$iiii){?>selected<?}?>><?=$iiii?> 년</option>
							<?
							}
							?>
						</select>
					</div>
					<div class="uk-width-medium-1-10" style="padding-top:7px;">
						<select id="SearchMonth" name="SearchMonth" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$월선택[$LangID]?>" style="width:100%;"/>
							<option value=""><?=$월선택[$LangID]?></option>
							<?
							for ($iiii=1;$iiii<=12;$iiii++) {
							?>
							<option value="<?=$iiii?>" <?if ($SearchMonth==$iiii){?>selected<?}?>><?=$iiii?> <?=$월월[$LangID]?></option>
							<?
							}
							?>
						</select>
					</div>


					
					<div class="uk-width-medium-3-10 uk-text-center">
						<a href="javascript:SearchSubmit();" class="md-btn md-btn-primary uk-margin-small-top"><?=$검색[$LangID]?></a>
						<a href="javascript:ExcelDown();" class="md-btn md-btn-primary uk-margin-small-top">EXCEL DOWN</a>
					</div>
					
					
				</div>
			</div>
		</div>
		</form>


		<div class="md-card">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin>
					<div class="uk-width-1-1">
						<div class="uk-overflow-container">
							
							
							<table class="uk-table uk-table-align-vertical">
								<thead>
									<tr>
										<th nowrap>No</th>
										<th nowrap><?=$지사명[$LangID]?><br><?=$_지사아이디[$LangID]?></th>
										<th nowrap><?=$학당명[$LangID]?><br><?=$_학당아이디[$LangID]?></th>
										<th nowrap><?=$학생이름[$LangID]?></th>
										<th nowrap><?=$영어이름[$LangID]?></th>
										<th nowrap><?=$학생아이디[$LangID]?></th>
										<th nowrap><?=$결석수[$LangID]?></th>
										<th nowrap><?=$출석수[$LangID]?>/<?=$수업수[$LangID]?><!--<br>(종료수업기준)--></th>
										<!--<th nowrap>출석수/수업수<br>(전체수업기준)</th>-->
										<th nowrap><?=$출석률[$LangID]?><!--<br>(종료수업기준)--></th>
										<!--<th nowrap>출석률<br>(전체수업기준)</th>-->
										<th nowrap><?=$출석률_결과[$LangID]?></th>
										<th nowrap><?=$출석율_그래프[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
									
									<?php
									$ListCount = 1;
									while($Row = $Stmt->fetch()) {

										$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

										$MemberID = $Row["MemberID"];
										$MemberLoginID = $Row["MemberLoginID"];
										$MemberName = $Row["MemberName"];
										$MemberNickName = $Row["MemberNickName"];

										$CenterID = $Row["CenterID"];
										$CenterName = $Row["CenterName"];
										$CenterLoginID = $Row["CenterLoginID"];

										$BranchID = $Row["BranchID"];
										$BranchName = $Row["BranchName"];
										$BranchLoginID = $Row["BranchLoginID"];

										$TotalClassCount = $Row["TotalClassCount"];
										$AbsentClassCount = $Row["AbsentClassCount"];
										$AttendClassCount = $Row["AttendClassCount"];

										$AttendRatio = round($AttendClassCount/$TotalClassCount*100);

										if ($AttendRatio>50){
											$StrResultAttend = "<span style='color:#0000ff;'>Excellent</span>";
										}else{
											$StrResultAttend = "<span style='color:#ff0000;'>Fail</span>";
										}

									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ListNumber?><!-- No --></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$BranchName?><br>(<?=$BranchLoginID?>)<!-- 지사이름 --></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$CenterName?><br>(<?=$CenterLoginID?>)<!-- 학당이름 --></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$MemberName?><!-- 학생이름 --></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$MemberNickName?><!-- 영어이름 --></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$MemberLoginID?><!-- 학생아이디 --></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$AbsentClassCount?><!-- 결석수 --></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$AttendClassCount?>/<?=$TotalClassCount?><!-- 출석수/수업수<br>(종료수업기준) --></td>
										<!--<td class="uk-text-nowrap uk-table-td-center"><?=$ListCount?>!-- 출석수/수업수<br>(전체수업기준) --</td>-->
										<td class="uk-text-nowrap uk-table-td-center"><?=$AttendRatio?> %<!-- 출석률<br>(종료수업기준) --></td>
										<!--<td class="uk-text-nowrap uk-table-td-center"><?=$ListCount?>!-- 출석률<br>(전체수업기준) --</td>-->
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrResultAttend?><!-- 출석률 결과 --></td>
										<td class="uk-text-nowrap uk-table-td-center">
											<div style="display:inline-block;height:20px;background-color:#0CAD4B;width:<?=150*($AttendRatio/100)?>px;margin:0px;"></div><div style="display:inline-block;height:20px;background-color:#FF2D00;width:<?=150*((100-$AttendRatio)/100)?>px;margin:0px;"></div>
											<!-- 출석율 그래프 -->
										</td>
									</tr>
									<?
										$ListCount++;
									}
									$Stmt = null;
									?>
								</tbody>
							</table>
						
						

						
						</div>


						<?php			
						include_once('./inc_pagination.php');
						?>

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


<script>
function SearchSubmit(){
	document.SearchForm.action = "account_center_slpmangoi_class_status.php";
	document.SearchForm.submit();
}

function ExcelDown(){
	location.href = "account_center_slpmangoi_class_status_excel.php?SearchYear=<?=$SearchYear?>&SearchMonth=<?=$SearchMonth?>";
}
</script>

<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>