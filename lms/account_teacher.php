<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

if ($_LINK_ADMIN_LEVEL_ID_>7){
	header("Location: branch_form.php?BranchID=".$_LINK_ADMIN_BRANCH_ID_); 
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
$SubMenuID = 2104;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
?>

 

<?php

$AddSqlWhere = "1=1";
$ListParam = "1=1";
$PaginationParam = "1=1";


$CurrentPage = isset($_REQUEST["CurrentPage"]) ? $_REQUEST["CurrentPage"] : "";
$PageListNum = isset($_REQUEST["PageListNum"]) ? $_REQUEST["PageListNum"] : "";
$SearchText = isset($_REQUEST["SearchText"]) ? $_REQUEST["SearchText"] : "";
$SearchState = isset($_REQUEST["SearchState"]) ? $_REQUEST["SearchState"] : "";

$SearchTeacherGroupID = isset($_REQUEST["SearchTeacherGroupID"]) ? $_REQUEST["SearchTeacherGroupID"] : "";
$SearchStartDate = isset($_REQUEST["SearchStartDate"]) ? $_REQUEST["SearchStartDate"] : "";
$SearchEndDate = isset($_REQUEST["SearchEndDate"]) ? $_REQUEST["SearchEndDate"] : "";

$CellID = isset($_REQUEST["CellID"]) ? $_REQUEST["CellID"] : "";
$CellOrder = isset($_REQUEST["CellOrder"]) ? $_REQUEST["CellOrder"] : "";
$OldCellID = isset($_REQUEST["OldCellID"]) ? $_REQUEST["OldCellID"] : "";
$OldCellOrder = isset($_REQUEST["OldCellOrder"]) ? $_REQUEST["OldCellOrder"] : "";


if ($CellID==""){
	$CellID = "4";
	$OldCellID = "4";
}

if ($CellOrder==""){
	$CellOrder = "1";
}

if ($SearchStartDate==""){
	$SearchStartDate = date("Y-m-01");
}	
if ($SearchEndDate==""){
	$SearchEndDate = date("Y-m-").date("t",strtotime($SearchStartDate));
}
$ListParam = $ListParam . "&SearchStartDate=" . $SearchStartDate;
$ListParam = $ListParam . "&SearchEndDate=" . $SearchEndDate;

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
		
if ($SearchState!="100"){
	$ListParam = $ListParam . "&SearchState=" . $SearchState;
	$AddSqlWhere = $AddSqlWhere . " and A.TeacherState=$SearchState ";
}


if ($SearchTeacherGroupID!=""){
	$ListParam = $ListParam . "&SearchTeacherGroupID=" . $SearchTeacherGroupID;
	$AddSqlWhere = $AddSqlWhere . " and A.TeacherGroupID=$SearchTeacherGroupID ";
}


if ($SearchText!=""){
	$ListParam = $ListParam . "&SearchText=" . $SearchText;
	$AddSqlWhere = $AddSqlWhere . " and A.TeacherName like '%".$SearchText."%' ";
}


$AddSqlWhere2 = " 1=1 ";
$AddSqlWhere2 = $AddSqlWhere2 . " and datediff(AAA.StartDateTime, '".$SearchStartDate."')>=0 and datediff(AAA.StartDateTime, '".$SearchEndDate."')<=0 ";
$AddSqlWhere2 = $AddSqlWhere2 . " and (AAA.ClassAttendState=1 or AAA.ClassAttendState=2 or AAA.ClassAttendState=3) ";





$PaginationParam = $ListParam;
if ($CurrentPage!=""){
	$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}

$ListParam = str_replace("&", "^^", $ListParam);

$Sql = "select 
				count(*) TotalRowCount 
		from Teachers A 
			inner join Members G on A.TeacherID=G.TeacherID and G.MemberLevelID=15 
		where ".$AddSqlWhere." ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TotalRowCount = $Row["TotalRowCount"];

$TotalPageCount = ceil($TotalRowCount / $PageListNum);
$StartRowNum = $PageListNum * ($CurrentPage - 1 );


$ViewTable = "
		select 
			A.* ,
			
			ifnull((select count(*) from Classes AAA inner join ClassOrders BBB on AAA.ClassOrderID=BBB.ClassOrderID where ".$AddSqlWhere2." and AAA.TeacherID=A.TeacherID and AAA.ClassState=2 and (AAA.ClassAttendState=1 or AAA.ClassAttendState=2 or AAA.ClassAttendState=3)),0) as TotalTeacherClass,
			
			ifnull((select sum(BBB.ClassOrderTimeTypeID) from Classes AAA inner join ClassOrders BBB on AAA.ClassOrderID=BBB.ClassOrderID where ".$AddSqlWhere2." and AAA.TeacherID=A.TeacherID and AAA.ClassState=2 and (AAA.ClassAttendState=1 or AAA.ClassAttendState=2 or AAA.ClassAttendState=3)),0) as TotalTeacherTime,

			ifnull((select sum(BBB.ClassOrderTimeTypeID) from Classes AAA inner join ClassOrders BBB on AAA.ClassOrderID=BBB.ClassOrderID where ".$AddSqlWhere2." and AAA.TeacherID=A.TeacherID and AAA.ClassState=2 and AAA.ClassAttendState=1),0) as TotalTeacherTime1,
			
			ifnull((select sum(BBB.ClassOrderTimeTypeID) from Classes AAA inner join ClassOrders BBB on AAA.ClassOrderID=BBB.ClassOrderID where ".$AddSqlWhere2." and AAA.TeacherID=A.TeacherID and AAA.ClassState=2 and AAA.ClassAttendState=2),0) as TotalTeacherTime2,
			
			ifnull((select sum(BBB.ClassOrderTimeTypeID) from Classes AAA inner join ClassOrders BBB on AAA.ClassOrderID=BBB.ClassOrderID where ".$AddSqlWhere2." and AAA.TeacherID=A.TeacherID and AAA.ClassState=2 and AAA.ClassAttendState=3),0) as TotalTeacherTime3,

			(
				select 
					case when AAA.ClassAttendState=3 then 
						sum((AAA.TeacherPayPerTime*0.5)*BBB.ClassOrderTimeTypeID) 
					else
						sum(AAA.TeacherPayPerTime*BBB.ClassOrderTimeTypeID) 
					end TotalTeacherPay 
				from Classes AAA 
					inner join ClassOrders BBB on AAA.ClassOrderID=BBB.ClassOrderID 
				where 
					".$AddSqlWhere2." 
					and AAA.TeacherID=A.TeacherID 
					and AAA.ClassState=2 
					and (AAA.ClassAttendState=1 or AAA.ClassAttendState=2 or AAA.ClassAttendState=3)
			) as TotalTeacherPay,
			(select count(*) from ClassOrders AAA where ClassOrderID in (select ClassOrderID from Classes where TeacherID=A.TeacherID)) as TotalTeachreClassOrder,
			(select count(*) from ClassOrders AAA where AAA.ClassOrderID in (select ClassOrderID from Classes where TeacherID=A.TeacherID) and AAA.ClassOrderState=3) as TotalTeachreEndClassOrder,

			(select sum(MemberPoint) from MemberPoints where MemberID=G.MemberID and MemberPointState=1) as SumMemberPoint
		from Teachers A 
			inner join Members G on A.TeacherID=G.TeacherID and G.MemberLevelID=15 
		where ".$AddSqlWhere." 
		order by A.TeacherName asc ";// limit $StartRowNum, $PageListNum";

$AddSqlWhere3 = ($CellOrder==1)? "desc":"asc";
if ($CellID=="1"){
	$Sql = "select * from ($ViewTable) V order by V.SumMemberPoint ".$AddSqlWhere3;
}else if ($CellID=="2"){
	$Sql = "select * from ($ViewTable) V order by V.TotalTeacherClass ".$AddSqlWhere3;
}else if ($CellID=="3"){
	$Sql = "select * from ($ViewTable) V order by V.TotalTeacherTime ".$AddSqlWhere3;
}else if ($CellID=="4"){
	$Sql = "select * from ($ViewTable) V order by V.TotalTeachreClassOrder ".$AddSqlWhere3;
}else if ($CellID=="5"){
	$Sql = "select * from ($ViewTable) V order by V.TotalTeachreEndClassOrder ".$AddSqlWhere3;
}else if ($CellID=="6"){
	$Sql = "select * from ($ViewTable) V order by V.TotalTeacherPay ".$AddSqlWhere3;
}

$Stmt = $DbConn->prepare($Sql);
//$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$강사별정산[$LangID]?></h3>

		<form name="SearchForm" method="get">
		<input type="hidden" name="CellID" id="CellID" value="<?=$CellID?>"/>
		<input type="hidden" name="CellOrder" id="CellOrder" value="<?=$CellOrder?>"/>
		<input type="hidden" name="OldCellID" id="OldCellID" value="<?=$OldCellID?>"/>
		<input type="hidden" name="OldCellOrder" id="OldCellOrder" value="<?=$OldCellOrder?>"/>

		<div class="md-card" style="margin-bottom:10px;">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin="">
					

					<div class="uk-width-medium-2-10" style="padding-top:7px;">
						<select id="SearchTeacherGroupID" name="SearchTeacherGroupID" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$강사그룹선택[$LangID]?>" style="width:100%;"/>
							<option value=""></option>
							<?
							$Sql2 = "select 
											A.* 
									from TeacherGroups A 
										inner join EduCenters B on A.EduCenterID=B.EduCenterID 
										inner join Franchises C on B.FranchiseID=C.FranchiseID 
									where A.TeacherGroupState<>0 and B.EduCenterState<>0 and C.FranchiseState<>0 and B.EduCenterID=1 
									order by A.TeacherGroupState asc, A.TeacherGroupName asc";
							
							$Stmt2 = $DbConn->prepare($Sql2);
							$Stmt2->execute();
							$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
							
							$OldSelectTeacherGroupState = -1;
							while($Row2 = $Stmt2->fetch()) {
								$SelectTeacherGroupID = $Row2["TeacherGroupID"];
								$SelectTeacherGroupName = $Row2["TeacherGroupName"];
								$SelectTeacherGroupState = $Row2["TeacherGroupState"];
							
								if ($OldSelectTeacherGroupState!=$SelectTeacherGroupState){
									if ($OldSelectTeacherGroupState!=-1){
										echo "</optgroup>";
									}
									
									if ($SelectTeacherGroupState==1){
										echo "<optgroup label=\"강사그룹(운영중)\">";
									}else if ($SelectTeacherGroupState==2){
										echo "<optgroup label=\"강사그룹(미운영)\">";
									}
								}
								$OldSelectTeacherGroupState = $SelectTeacherGroupState;
							?>

							<option value="<?=$SelectTeacherGroupID?>" <?if ($SearchTeacherGroupID==$SelectTeacherGroupID){?>selected<?}?>><?=$SelectTeacherGroupName?></option>
							<?
							}
							$Stmt2 = null;
							?>
						</select>
					</div>


					<div class="uk-width-medium-2-10" style="padding-top:7px;">
						<label for="uk_dp_start"><?=$시작일[$LangID]?></label>
						<input type="text" id="SearchStartDate" name="SearchStartDate" value="<?=$SearchStartDate?>" class="md-input label-fixed" data-uk-datepicker="{format:'YYYY-MM-DD', weekstart:0}">
					</div>
					<div class="uk-width-medium-2-10" style="padding-top:7px;">
						<label for="uk_dp_end"><?=$종료일[$LangID]?></label>
						<input type="text" id="SearchEndDate" name="SearchEndDate" value="<?=$SearchEndDate?>" class="md-input label-fixed" data-uk-datepicker="{format:'YYYY-MM-DD', weekstart:0}">
					</div>

					<div class="uk-width-medium-2-10">
						<label for="SearchText"><?=$강사명[$LangID]?></label>
						<input type="text" class="md-input" id="SearchText" name="SearchText" value="<?=$SearchText?>">
					</div>

					<!--
					<div class="uk-width-medium-1-10">
						<label for="product_search_price">Price</label>
						<input type="text" class="md-input" id="product_search_price">
					</div>
					-->

					<div class="uk-width-medium-1-10">
						<div class="uk-margin-small-top">
							<select id="SearchState" name="SearchState" onchange="SearchSubmit()" data-md-selectize  data-md-selectize-bottom>
								<option value="100" <?if ($SearchState=="100"){?>selected<?}?>><?=$전체[$LangID]?></option>
								<option value="1" <?if ($SearchState=="1"){?>selected<?}?>><?=$운영중[$LangID]?></option>
								<option value="2" <?if ($SearchState=="2"){?>selected<?}?>><?=$미운영[$LangID]?></option>
							</select>
						</div>
					</div>

					<!--
					<div class="uk-width-medium-1-10">
						<div class="uk-margin-top uk-text-nowrap">
							<input type="checkbox" name="product_search_active" id="product_search_active" data-md-icheck/>
							<label for="product_search_active" class="inline-label">Active</label>
						</div>
					</div>
					-->

					<div class="uk-width-medium-1-10 uk-text-center">
						<a href="javascript:SearchSubmit();" class="md-btn md-btn-primary uk-margin-small-top"><?=$검색[$LangID]?></a>
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
										<th nowrap><?=$강사명[$LangID]?></th>
										<th nowrap><a href="javascript:SetOrderList(1);" ><?=$포인트[$LangID]?> <?if ($CellOrder=="1" && $CellID=="1"){?>▼<?} else if($CellOrder=="2" && $CellID=="1") {?>▲<?}?></a></th>
										<th nowrap><a href="javascript:SetOrderList(2);" >수업수 <?if ($CellOrder=="1" && $CellID=="2"){?>▼<?} else if($CellOrder=="2" && $CellID=="2") {?>▲<?}?></a>
										<th nowrap><a href="javascript:SetOrderList(3);" >티수 <?if ($CellOrder=="1" && $CellID=="3"){?>▼<?} else if($CellOrder=="2" && $CellID=="3") {?>▲<?}?></a>
										<th nowrap>티수 (출석/지각/결석)</a>
										<th nowrap><a href="javascript:SetOrderList(4);" ><?=$누적전체강의[$LangID]?> <?if ($CellOrder=="1" && $CellID=="4"){?>▼<?} else if($CellOrder=="2" && $CellID=="4") {?>▲<?}?></a>
										<th nowrap><a href="javascript:SetOrderList(5);" ><?=$누적종료강의[$LangID]?> <?if ($CellOrder=="1" && $CellID=="5"){?>▼<?} else if($CellOrder=="2" && $CellID=="5") {?>▲<?}?></a>
										<th nowrap><?=$종료율[$LangID]?></th>
										<th nowrap><a href="javascript:SetOrderList(6);" ><?=$HomeT급여[$LangID]?> <?if ($CellOrder=="1" && $CellID=="6"){?>▼<?} else if($CellOrder=="2" && $CellID=="6") {?>▲<?}?></a>
									</tr>
								</thead>
								<tbody>
									
									<?php
									$ListCount = 1;
									$SumTotalTeacherPay = 0;
									$SumTotalTeacherClass = 0;
									$SumTotalTeacherTime = 0;
									$SumTotalTeachreClassOrder = 0;
									$SumTotalTeachreEndClassOrder = 0;
									$SumTotalEndClassOrder = 0;
									$SumSumMemberPoint = 0;
									while($Row = $Stmt->fetch()) {
										$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

										$TeacherID = $Row["TeacherID"];
										$TeacherName = $Row["TeacherName"];
										$TotalTeacherPay = $Row["TotalTeacherPay"];
										$TotalTeacherTime = $Row["TotalTeacherTime"];
										$TotalTeacherTime1 = $Row["TotalTeacherTime1"];
										$TotalTeacherTime2 = $Row["TotalTeacherTime2"];
										$TotalTeacherTime3 = $Row["TotalTeacherTime3"];
										$TotalTeacherClass = $Row["TotalTeacherClass"];

										$TotalTeachreClassOrder = $Row["TotalTeachreClassOrder"];
										$TotalTeachreEndClassOrder = $Row["TotalTeachreEndClassOrder"];

										$SumMemberPoint = $Row["SumMemberPoint"];
										
										$SumTotalTeacherPay = $SumTotalTeacherPay + $TotalTeacherPay;
										$SumTotalTeacherClass = $SumTotalTeacherClass + $TotalTeacherPay;
										$SumTotalTeacherTime = $SumTotalTeacherTime + $TotalTeacherTime;
										$SumTotalTeachreClassOrder = $SumTotalTeachreClassOrder + $TotalTeachreClassOrder;
										$SumTotalTeachreEndClassOrder = $SumTotalTeachreEndClassOrder + $TotalTeachreEndClassOrder;

										if($TotalTeachreClassOrder>0) {
											$TotalEndClassOrder = ($TotalTeachreEndClassOrder/$TotalTeachreClassOrder)*100;
											$TotalEndClassOrder = number_format($TotalEndClassOrder,0);
											$TotalEndClassOrderRatio = $TotalEndClassOrder."%";
										} else {
											$TotalEndClassOrderRatio = "-";
											$TotalEndClassOrder = 0;
										}
										$SumTotalEndClassOrder= $SumTotalEndClassOrder + $TotalEndClassOrder;
										$SumSumMemberPoint = $SumSumMemberPoint + $SumMemberPoint;
									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ListNumber?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$TeacherName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($SumMemberPoint,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($TotalTeacherClass,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($TotalTeacherTime,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($TotalTeacherTime1,0)?> / <?=number_format($TotalTeacherTime2,0)?> / <?=number_format($TotalTeacherTime3,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($TotalTeachreClassOrder,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($TotalTeachreEndClassOrder,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$TotalEndClassOrderRatio?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($TotalTeacherPay,0)?></td>
									</tr>
									<?php
										$ListCount ++;
									}
									$Stmt = null;

									if ($ListCount==1){
										$AvgTotalTeacherPay = 0;
										$AvgTotalTeacherClass = 0;
										$AvgTotalTeacherTime = 0;
										$AvgTotalTeachreClassOrder = 0;
										$AvgTotalTeachreEndClassOrder = 0;
										$AvgSumMemberPoint = 0;
									}else{
										$AvgTotalTeacherPay = $SumTotalTeacherPay / ($ListCount-1);
										$AvgTotalTeacherClass = $SumTotalTeacherClass / ($ListCount-1);
										$AvgTotalTeacherTime = $SumTotalTeacherTime / ($ListCount-1);
										$AvgTotalTeachreClassOrder = $SumTotalTeachreClassOrder / ($ListCount-1);
										$AvgTotalTeachreEndClassOrder = $SumTotalTeachreEndClassOrder / ($ListCount-1);
										$AvgTotalEndClassOrder = $SumTotalEndClassOrder / ($ListCount-1);
										$AvgSumMemberPoint = $SumSumMemberPoint / ($ListCount-1);
									}
									?>

									<tr style="background-color:#f1f1f1;">
										<td class="uk-text-nowrap uk-table-td-center" colspan="2"><?=$합계[$LangID]?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($SumSumMemberPoint,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($SumTotalTeacherClass,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($SumTotalTeacherTime,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($SumTotalTeachreClassOrder,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($SumTotalTeachreEndClassOrder,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($SumTotalTeacherPay,0)?></td>
									</tr>
									<tr style="background-color:#f1f1f1;">
										<td class="uk-text-nowrap uk-table-td-center" colspan="2"><?=$평균[$LangID]?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($AvgSumMemberPoint,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($AvgTotalTeacherClass,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($AvgTotalTeacherTime,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($AvgTotalTeachreClassOrder,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($AvgTotalTeachreEndClassOrder,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($AvgTotalEndClassOrder,0)?>%</td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($SumTotalTeacherPay,0)?></td>
									</tr>
								</tbody>
							</table>
						</div>
						

						<?php			
						//include_once('./inc_pagination.php');
						?>
						<!--
						<div class="uk-form-row" style="text-align:center;">
							<a type="button" href="branch_form.php?ListParam=<?=$ListParam?>" class="md-btn md-btn-primary">신규등록</a>
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
function SetOrderList(ID) {
	// .value 를 붙이면 단순 문자열 또는 숫자로 인식.
	var CellID = document.SearchForm.CellID;
	var CellOrder = document.SearchForm.CellOrder;
	var OldCellID = document.SearchForm.OldCellID;
	var OldCellOrder = document.SearchForm.OldCellOrder;

	// 클릭했었던 값은 Old 에 대입
	OldCellOrder.value = CellOrder.value;
	OldCellID.value = CellID.value;
	CellID.value = ID;

	//alert("CellID : "+CellID.value);
	//alert("CellOrder : "+CellOrder.value);
	//alert("OldCellID : "+OldCellID.value);
	//alert("OldCellOrder : "+OldCellOrder.value);
	//alert(document.SearchForm.OldCellOrder.value);

	// 동일한 CellID 를 눌렀다면 
	if (CellID.value==OldCellID.value) {
		// 기존값이 1,2 인지 확인 후 2 또는 1 대입
		CellOrder.value = (OldCellOrder.value==1)? 2:1;
		//alert("after if : "+CellOrder.value);
	} else { // 기존 Cell 과 누른 Cell 이 같지 않다면
		CellOrder.value = 1;
		//alert("after if : "+CellOrder.value);
	}




	SearchSubmit();
}

function SearchSubmit(){
	document.SearchForm.action = "account_teacher.php";
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