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
$MainMenuID = 88;
$SubMenuID = 8804;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
?>


 
<?php

$AddSqlWhere = "1=1";
$ListParam = "1=1";
$PaginationParam = "1=1";


$CurrentPage = isset($_REQUEST["CurrentPage"]) ? $_REQUEST["CurrentPage"] : "";
$PageListNum = isset($_REQUEST["PageListNum"]) ? $_REQUEST["PageListNum"] : "";
//$SearchText = isset($_REQUEST["SearchText"]) ? $_REQUEST["SearchText"] : "";
$SearchState = isset($_REQUEST["SearchState"]) ? $_REQUEST["SearchState"] : "";


if (!$CurrentPage){
	$CurrentPage = 1;	
}
if (!$PageListNum){
	$PageListNum = 30;
}


if ($PageListNum!=""){
	$ListParam = $ListParam . "&PageListNum=" . $PageListNum;
}

if ($SearchState==""){
	$SearchState = "1";
}	
		
//if ($SearchState!="100"){
//	$ListParam = $ListParam . "&SearchState=" . $SearchState;
//	$AddSqlWhere = $AddSqlWhere . " and A.Hr_OrganLevelState=$SearchState ";
//}
//$AddSqlWhere = $AddSqlWhere . " and A.Hr_OrganLevelState<>0 ";

//if ($SearchText!=""){
//	$ListParam = $ListParam . "&SearchText=" . $SearchText;
//	$AddSqlWhere = $AddSqlWhere . " and A.CouponName like '%".$SearchText."%' ";
//}



$PaginationParam = $ListParam;
if ($CurrentPage!=""){
	$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}

$ListParam = str_replace("&", "^^", $ListParam);

// 현재 있는 조직원들의 직무레벨과 레벨이름 가져오는 뷰
$ViweTable = "
				select 
					AAAA.* 
				from Hr_OrganLevelTaskMembers AAAA 
				inner join Members BBBB on AAAA.MemberID=BBBB.MemberID and BBBB.MemberState=1 
		";




$Sql = "select 
				count(*) TotalRowCount 
		from ($ViweTable) A 
			inner join Members B on A.MemberID=B.MemberID 
			left outer join Hr_OrganLevels C on A.Hr_OrganLevelID=C.Hr_OrganLevelID 
			left outer join Hr_OrganTask2 D on A.Hr_OrganTask2ID=D.Hr_OrganTask2ID 
			left outer join Hr_OrganTask1 E on D.Hr_OrganTask1ID=E.Hr_OrganTask1ID 

			left outer join ($ViweTable) AA on AA.Hr_OrganLevel<A.Hr_OrganLevel and (AA.Hr_OrganLevelID=C.Hr_OrganLevel1ID or AA.Hr_OrganLevelID=C.Hr_OrganLevel2ID or AA.Hr_OrganLevelID=C.Hr_OrganLevel3ID)
			left outer join Members BB on AA.MemberID=BB.MemberID and BB.MemberState=1 
			left outer join Hr_OrganLevels CC on AA.Hr_OrganLevelID=CC.Hr_OrganLevelID 
			left outer join Hr_OrganTask2 DD on AA.Hr_OrganTask2ID=DD.Hr_OrganTask2ID 
			left outer join Hr_OrganTask1 EE on DD.Hr_OrganTask1ID=EE.Hr_OrganTask1ID 
		where ".$AddSqlWhere." ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TotalRowCount = $Row["TotalRowCount"];

$TotalPageCount = ceil($TotalRowCount / $PageListNum);
$StartRowNum = $PageListNum * ($CurrentPage - 1 );


$Sql = "
		select 
			A.*,
			B.MemberName,

			ifnull(D.Hr_OrganTask2Name, '') as Hr_OrganTask2Name,
			ifnull(E.Hr_OrganTask1Name, '') as Hr_OrganTask1Name,

			ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=C.Hr_OrganLevel1ID), '') as Hr_OrganLevelName1, 
			ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=C.Hr_OrganLevel2ID), '') as Hr_OrganLevelName2,
			ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=C.Hr_OrganLevel3ID), '') as Hr_OrganLevelName3,
			ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=C.Hr_OrganLevel4ID), '') as Hr_OrganLevelName4,

			AA.MemberID as T_MemberID,
			AA.Hr_OrganLevel as T_Hr_OrganLevel,
			AA.Hr_OrganPositionName as T_Hr_OrganPositionName,
			BB.MemberName as T_MemberName,

			ifnull(DD.Hr_OrganTask2Name,'') as T_Hr_OrganTask2Name,
			ifnull(EE.Hr_OrganTask1Name,'') as T_Hr_OrganTask1Name,

			ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=CC.Hr_OrganLevel1ID), '') as T_Hr_OrganLevelName1, 
			ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=CC.Hr_OrganLevel2ID), '') as T_Hr_OrganLevelName2,
			ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=CC.Hr_OrganLevel3ID), '') as T_Hr_OrganLevelName3,
			ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=CC.Hr_OrganLevel4ID), '') as T_Hr_OrganLevelName4,

			(select count(*) from ($ViweTable) VVVV where VVVV.Hr_OrganLevel<A.Hr_OrganLevel and (VVVV.Hr_OrganLevelID=C.Hr_OrganLevel1ID or VVVV.Hr_OrganLevelID=C.Hr_OrganLevel2ID or VVVV.Hr_OrganLevelID=C.Hr_OrganLevel3ID)) as T_BossCount


		from ($ViweTable) A 

			inner join Members B on A.MemberID=B.MemberID 
			left outer join Hr_OrganLevels C on A.Hr_OrganLevelID=C.Hr_OrganLevelID 
			left outer join Hr_OrganTask2 D on A.Hr_OrganTask2ID=D.Hr_OrganTask2ID 
			left outer join Hr_OrganTask1 E on D.Hr_OrganTask1ID=E.Hr_OrganTask1ID 

			left outer join ($ViweTable) AA on AA.Hr_OrganLevel<A.Hr_OrganLevel and (AA.Hr_OrganLevelID=C.Hr_OrganLevel1ID or AA.Hr_OrganLevelID=C.Hr_OrganLevel2ID or AA.Hr_OrganLevelID=C.Hr_OrganLevel3ID)
			left outer join Members BB on AA.MemberID=BB.MemberID 
			left outer join Hr_OrganLevels CC on AA.Hr_OrganLevelID=CC.Hr_OrganLevelID 
			left outer join Hr_OrganTask2 DD on AA.Hr_OrganTask2ID=DD.Hr_OrganTask2ID 
			left outer join Hr_OrganTask1 EE on DD.Hr_OrganTask1ID=EE.Hr_OrganTask1ID 

		where ".$AddSqlWhere." 
		order by 
			A.Hr_OrganLevel asc, C.Hr_OrganLevel1ID asc, C.Hr_OrganLevel2ID asc, C.Hr_OrganLevel3ID asc, C.Hr_OrganLevel4ID asc, A.MemberID asc , AA.Hr_OrganLevel asc
		";// limit $StartRowNum, $PageListNum";

$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

//echo $Sql;

?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$업적평가_조직도[$LangID]?></h3>

		<!--
		<form name="SearchForm" method="get">
		<div class="md-card" style="margin-bottom:10px;">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin="">

					
					<div class="uk-width-medium-2-10">
						<label for="SearchText"><?=$쿠폰명[$LangID]?> </label>
						<input type="text" class="md-input" id="SearchText" name="SearchText" value="<?=$SearchText?>">
					</div>

					<div class="uk-width-medium-1-10">
						<div class="uk-margin-small-top">
							<select id="SearchState" name="SearchState" onchange="SearchSubmit()" data-md-selectize  data-md-selectize-bottom>
								<option value="100" <?if ($SearchState=="100"){?>selected<?}?>><?=$전체[$LangID]?></option>
								<option value="1" <?if ($SearchState=="1"){?>selected<?}?>><?=$사용중[$LangID]?></option>
								<option value="2" <?if ($SearchState=="2"){?>selected<?}?>><?=$미사용[$LangID]?></option>
							</select>
						</div>
					</div>

					<div class="uk-width-medium-1-10 uk-text-center">
						<a href="javascript:SearchSubmit();" class="md-btn md-btn-primary uk-margin-small-top"><?=$검색[$LangID]?> </a>
					</div>
					
					
				</div>
			</div>
		</div>
		</form>
		-->

		<div class="md-card">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin>
					<div class="uk-width-1-1">
						<div class="uk-overflow-container">
							<table class="uk-table uk-table-align-vertical">
								<thead>
									<tr>
										<th nowrap colspan="4" style="border-bottom:0px;"><?=$평가대상자[$LangID]?></th>
										<th nowrap colspan="4" style="border-bottom:0px;"><?=$평가자[$LangID]?></th>
									</tr>
									<tr>
										<th nowrap><?=$번호[$LangID]?></th>
										<th nowrap><?=$평가대상자_성명[$LangID]?></th>
										<th nowrap><?=$직급[$LangID]?>/<?=$직책[$LangID]?></th>
										<th nowrap><?=$직무[$LangID]?></th>

										<th nowrap><?=$평가자성명[$LangID]?></th>
										<th nowrap><?=$상사구분[$LangID]?></th>
										<th nowrap><?=$직급[$LangID]?>/<?=$직책[$LangID]?></th>
										<th nowrap><?=$직무[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
									
									<?php
									$ListCount = 1;
									$ListCount2 = 0;
									$OldMemberID = 0;
									while($Row = $Stmt->fetch()) {
										$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

										
										//=================== 자기 자신 ======================
										$MemberID = $Row["MemberID"];

										$Hr_OrganLevel = $Row["Hr_OrganLevel"];
										$Hr_OrganLevelID = $Row["Hr_OrganLevelID"];
										$Hr_OrganTask2ID = $Row["Hr_OrganTask2ID"];
										$Hr_OrganPositionName = $Row["Hr_OrganPositionName"];

										$MemberName = $Row["MemberName"];

										$Hr_OrganTask2Name = $Row["Hr_OrganTask2Name"];
										$Hr_OrganTask1Name = $Row["Hr_OrganTask1Name"];


										$Hr_OrganLevelName1 = $Row["Hr_OrganLevelName1"];
										$Hr_OrganLevelName2 = $Row["Hr_OrganLevelName2"];
										$Hr_OrganLevelName3 = $Row["Hr_OrganLevelName3"];
										$Hr_OrganLevelName4 = $Row["Hr_OrganLevelName4"];

										$Str_Hr_OrganLevelName = $Hr_OrganLevelName1;
										if ($Hr_OrganLevelName2!=""){
											$Str_Hr_OrganLevelName .= " > " . $Hr_OrganLevelName2;
										}
										if ($Hr_OrganLevelName3!=""){
											$Str_Hr_OrganLevelName .= " > " . $Hr_OrganLevelName3;
										}
										if ($Hr_OrganLevelName4!=""){
											$Str_Hr_OrganLevelName .= " > " . $Hr_OrganLevelName4;
										}

										$Str_OrganTaskName = $Hr_OrganTask1Name;
										if ($Hr_OrganTask2Name!=""){
											$Str_OrganTaskName .= " > " . $Hr_OrganTask2Name;
										}
										//=================== 자기 자신 ======================

										//=================== 상 사 ======================
										$T_MemberID = $Row["T_MemberID"];

										$T_Hr_OrganLevel = $Row["T_Hr_OrganLevel"];
										$T_Hr_OrganPositionName = $Row["T_Hr_OrganPositionName"];

										$T_MemberName = $Row["T_MemberName"];

										$T_Hr_OrganTask2Name = $Row["T_Hr_OrganTask2Name"];
										$T_Hr_OrganTask1Name = $Row["T_Hr_OrganTask1Name"];


										$T_Hr_OrganLevelName1 = $Row["T_Hr_OrganLevelName1"];
										$T_Hr_OrganLevelName2 = $Row["T_Hr_OrganLevelName2"];
										$T_Hr_OrganLevelName3 = $Row["T_Hr_OrganLevelName3"];
										$T_Hr_OrganLevelName4 = $Row["T_Hr_OrganLevelName4"];

										$T_Str_Hr_OrganLevelName = $T_Hr_OrganLevelName1;
										if ($T_Hr_OrganLevelName2!=""){
											$T_Str_Hr_OrganLevelName .= " > " . $T_Hr_OrganLevelName2;
										}
										if ($T_Hr_OrganLevelName3!=""){
											$T_Str_Hr_OrganLevelName .= " > " . $T_Hr_OrganLevelName3;
										}
										if ($T_Hr_OrganLevelName4!=""){
											$T_Str_Hr_OrganLevelName .= " > " . $T_Hr_OrganLevelName4;
										}


										$T_Str_OrganTaskName = $T_Hr_OrganTask1Name;
										if ($T_Hr_OrganTask2Name!=""){
											$T_Str_OrganTaskName .= " > " . $T_Hr_OrganTask2Name;
										}
										
										
										$T_Str_BossTitle = "";
										if ($T_Hr_OrganLevel==1){
											$T_Str_BossTitle = "최종 상사";
										}else{
											if ($Hr_OrganLevel==3){
												$T_Str_BossTitle = "1차 상사";
											}else if ($Hr_OrganLevel==4){
												if ($Hr_OrganLevelName3!=""){
													if ($T_Hr_OrganLevel==3){
														$T_Str_BossTitle = "1차 상사";
													}else if ($T_Hr_OrganLevel==2){
														$T_Str_BossTitle = "2차 상사";
													}
												}else{
													$T_Str_BossTitle = "1차 상사";
												}
											}

										}
										//=================== 상 사 ======================

										
										$PrintMember = 0;
										if ($OldMemberID!=$MemberID){
											$T_BossCount = $Row["T_BossCount"];
											$OldMemberID = $MemberID;
											$PrintMember = 1;
											$ListCount2++;

											if ($T_BossCount==0){
												$T_BossCount = 1;
											}
										}
									?>
									<tr>
										<?if ($PrintMember==1){?>
											<td class="uk-text-nowrap uk-table-td-center" rowspan="<?=$T_BossCount?>"><?=$ListCount2?></td>
											<td class="uk-text-nowrap uk-table-td-center" rowspan="<?=$T_BossCount?>"><?=$MemberName?></td>
											<td class="uk-text-nowrap uk-table-td-center" rowspan="<?=$T_BossCount?>"><?=$Hr_OrganPositionName?></td>
											<td class="uk-text-nowrap uk-table-td-left" rowspan="<?=$T_BossCount?>"><?=$Str_OrganTaskName?></td>
											<!---td class="uk-text-nowrap uk-table-td-left" rowspan="<?=$T_BossCount?>"><?=$Str_Hr_OrganLevelName?></td---->
										<?}?>

										<td class="uk-text-nowrap uk-table-td-center"><?=$T_MemberName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$T_Str_BossTitle?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$T_Hr_OrganPositionName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$T_Str_OrganTaskName?></td>
										<!---td class="uk-text-nowrap uk-table-td-center"><?=$T_Str_Hr_OrganLevelName?></td---->
									</tr>
									<?php
										$ListCount ++;
									}
									$Stmt = null;
									?>


								</tbody>
							</table>
						</div>
						

						<?php			
						//include_once('./inc_pagination.php');
						?>

						<!--
						<div class="uk-form-row" style="text-align:center; margin-top:20px;">
							<a type="button" href="javascript:OpenOrganlevelForm('')" class="md-btn md-btn-primary"><?=$신규등록[$LangID]?></a>
						</div>
						-->

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
function OpenOrganlevelForm(Hr_OrganLevelID){
	openurl = "hr_organ_level_form.php?Hr_OrganLevelID="+Hr_OrganLevelID;
	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "850"
		,maxHeight: "750"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}


function SearchSubmit(){
	document.SearchForm.action = "hr_evaluation_organ_table.php";
	document.SearchForm.submit();
}
</script>

<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>