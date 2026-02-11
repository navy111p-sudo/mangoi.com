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
$SubMenuID = 8825;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
?>


 
<?php

$AddSqlWhere = "1=1";
$ListParam = "1=1";
$PaginationParam = "1=1";


$CurrentPage = isset($_REQUEST["CurrentPage"]) ? $_REQUEST["CurrentPage"] : "";
$PageListNum = isset($_REQUEST["PageListNum"]) ? $_REQUEST["PageListNum"] : "";

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

$PaginationParam = $ListParam;
if ($CurrentPage!=""){
	$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}

$ListParam = str_replace("&", "^^", $ListParam);
?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$역량평가현황[$LangID]?></h3>

		<form name="SearchForm" method="post" ENCTYPE="multipart/form-data">
		<div class="md-card" style="margin-bottom:10px;">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin="">

					<div class="uk-width-medium-3-10">
						<div class="uk-margin-small-top">
                            <?=$평가리스트_선택[$LangID]?>
                            <select id="SearchState" name="SearchState" onchange="SearchSubmit()" data-md-selectize  data-md-selectize-bottom>
                                <option value=""><?=$선택[$LangID]?></option>
                                    <?php
                                    $Hr_EvaluationID       = "";
                                    $Hr_EvaluationTypeID   = "";
                                    $Hr_EvaluationTypeName = "";

                                    $AddSqlWhere = "1=1";
                                    $AddSqlWhere = $AddSqlWhere . " and A.Hr_EvaluationState=1";

                                    $Sql = "
                                            select 
                                                A.*,
                                                ifnull(B.CenterName, '-') as CenterName,
                                                C.Hr_EvaluationTypeName,
                                                D.Hr_EvaluationCycleName
                                            from Hr_Evaluations A 
                                                left outer join Centers B on A.CenterID=B.CenterID 
                                                inner join Hr_EvaluationTypes C on A.Hr_EvaluationTypeID=C.Hr_EvaluationTypeID 
                                                inner join Hr_EvaluationCycles D on A.Hr_EvaluationCycleID=D.Hr_EvaluationCycleID 
                                            where ".$AddSqlWhere." 
                                            order by A.Hr_EvaluationYear desc, A.Hr_EvaluationMonth desc, A.Hr_EvaluationDate desc";

                                    $Stmt = $DbConn->prepare($Sql);
                                    $Stmt->execute();
                                    $Stmt->setFetchMode(PDO::FETCH_ASSOC);
                                    $ListCount = 1;
                                    while($Row = $Stmt->fetch()) {
                                        
                                        $Hr_EvaluationID       = $Row["Hr_EvaluationID"];
                                        $Hr_EvaluationYear     = $Row["Hr_EvaluationYear"];
                                        $Hr_EvaluationMonth    = $Row["Hr_EvaluationMonth"];
                                        $Hr_EvaluationName     = $Row["Hr_EvaluationName"];

                                        $Str_Hr_EvaluationYear = $Hr_EvaluationYear."년 ".substr("0".$Hr_EvaluationMonth,-2)."월 ".$Hr_EvaluationName;
                                        
                                        $Hr_EvaluationTypeID   = $Row["Hr_EvaluationTypeID"];
                                        $Hr_EvaluationTypeName = $Row["Hr_EvaluationTypeName"];
                                        ?>
                                        <option value="<?=$Hr_EvaluationID?>" <? if ($Hr_EvaluationID==$SearchState){?> selected <?}?>><?=$Str_Hr_EvaluationYear?></option>
                                        <?php
                                        $ListCount ++;
                                    }
                                    $Stmt = null;
                                    ?>

                            </select> 
						</div>
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
										<th nowrap colspan="4" style="border-bottom:0px;"><?=$평가대상자[$LangID]?></th>
										<th nowrap colspan="6" style="border-bottom:0px;"><?=$평가자[$LangID]?></th>
									</tr>
									<tr>
										<th nowrap><?=$번호[$LangID]?></th>
										<th nowrap><?=$성명[$LangID]?></th>
										<th nowrap><?=$직급_직책[$LangID]?></th>
										<th nowrap><?=$직무[$LangID]?></th>
										<th nowrap><?=$성명[$LangID]?></th>
										<th nowrap><?=$유형[$LangID]?></th>
										<th nowrap><?=$직무[$LangID]?></th>
										<th nowrap><?=$가중치[$LangID]?>(%)</th>
										<th nowrap><?=$가중치반영점수[$LangID]?></th>
										<th nowrap><?=$비고[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
									
					<?php
                    #---------------------------------------------------------------------------------------------------------------------#
					if ($SearchState) { 
                    #---------------------------------------------------------------------------------------------------------------------#
							$ViewTable2 = "SELECT AAAAA.* 
											from Hr_OrganLevelTaskMembers AAAAA 
											inner join Members BBBBB on AAAAA.MemberID=BBBBB.MemberID and BBBBB.MemberState=1";

							$ViewTable = "SELECT AAAA.* 
											from Hr_EvaluationCompetencyMembers AAAA 
											inner join Members BBBB on AAAA.MemberID=BBBB.MemberID and BBBB.MemberState=1";

							$Sql = "SELECT count(*) TotalRowCount 
									from ($ViewTable) A 
										inner join Members B on A.MemberID=B.MemberID 
										left outer join ($ViewTable2) A_1 on A.MemberID=A_1.MemberID 
										left outer join Hr_OrganLevels C on A_1.Hr_OrganLevelID=C.Hr_OrganLevelID 
										left outer join Hr_OrganTask2 D on A_1.Hr_OrganTask2ID=D.Hr_OrganTask2ID 
										left outer join Hr_OrganTask1 E on D.Hr_OrganTask1ID=E.Hr_OrganTask1ID 

										left outer join ($ViewTable) AA on A.Hr_EvaluationCompetencyMemberID=AA.MemberID 
										left outer join Members BB on AA.MemberID=BB.MemberID and BB.MemberState=1 
										left outer join ($ViewTable2) AA_1 on AA.MemberID=AA_1.MemberID 
										left outer join Hr_OrganLevels CC on AA_1.Hr_OrganLevelID=CC.Hr_OrganLevelID 
										left outer join Hr_OrganTask2 DD on AA_1.Hr_OrganTask2ID=DD.Hr_OrganTask2ID 
										left outer join Hr_OrganTask1 EE on DD.Hr_OrganTask1ID=EE.Hr_OrganTask1ID 
									where A.Hr_EvaluationID=".$SearchState;
							$Stmt = $DbConn->prepare($Sql);
							$Stmt->execute();
							$Stmt->setFetchMode(PDO::FETCH_ASSOC);
							$Row = $Stmt->fetch();
							$Stmt = null;

							$TotalRowCount = $Row["TotalRowCount"];

							$TotalPageCount = ceil($TotalRowCount / $PageListNum);
							$StartRowNum = $PageListNum * ($CurrentPage - 1 );


							$Sql = "SELECT 
										A.*,
										A_1.Hr_OrganLevel,
										A_1.Hr_OrganPositionName,
										A_1.Hr_OrganLevelID,
										A_1.Hr_OrganTask2ID,
										B.MemberName,

										ifnull(D.Hr_OrganTask2Name, '미지정') as Hr_OrganTask2Name,
										ifnull(E.Hr_OrganTask1ID,   ''    ) as Hr_OrganTask1ID,
										ifnull(E.Hr_OrganTask1Name, '미지정') as Hr_OrganTask1Name,

										ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=C.Hr_OrganLevel1ID), '') as Hr_OrganLevelName1, 
										ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=C.Hr_OrganLevel2ID), '') as Hr_OrganLevelName2,
										ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=C.Hr_OrganLevel3ID), '') as Hr_OrganLevelName3,
										ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=C.Hr_OrganLevel4ID), '') as Hr_OrganLevelName4,

										BB.MemberID as T_MemberID,
										AA_1.Hr_OrganLevel as T_Hr_OrganLevel,
										ifnull(AA_1.Hr_OrganPositionName, '미지정') as T_Hr_OrganPositionName,
										BB.MemberName as T_MemberName,

										ifnull(DD.Hr_OrganTask2Name,'미지정') as T_Hr_OrganTask2Name,
										ifnull(EE.Hr_OrganTask1Name,'미지정') as T_Hr_OrganTask1Name,
										ifnull(DD.Hr_OrganTask2ID,  ''    ) as T_Hr_OrganTask2ID,
										ifnull(EE.Hr_OrganTask1ID,  ''    ) as T_Hr_OrganTask1ID,

										ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=CC.Hr_OrganLevel1ID), '') as T_Hr_OrganLevelName1, 
										ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=CC.Hr_OrganLevel2ID), '') as T_Hr_OrganLevelName2,
										ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=CC.Hr_OrganLevel3ID), '') as T_Hr_OrganLevelName3,
										ifnull((select Hr_OrganLevelName from Hr_OrganLevels where Hr_OrganLevelID=CC.Hr_OrganLevel4ID), '') as T_Hr_OrganLevelName4,

										(select count(*) from Members VVVV where VVVV.MemberID in (select VVVVV.Hr_EvaluationCompetencyMemberID from ($ViewTable) VVVVV where MemberID=A.MemberID ) ) as TM_MemberCount,

										( (select count(*) from ($ViewTable) VVVVV where VVVVV.MemberID=A.MemberID and VVVVV.Hr_EvaluationID=".$SearchState." ) ) as T_MemberCount

									FROM ($ViewTable) A 

										inner join Members B on A.MemberID=B.MemberID 
										left outer join ($ViewTable2) A_1 on A.MemberID=A_1.MemberID 
										left outer join Hr_OrganLevels C on A_1.Hr_OrganLevelID=C.Hr_OrganLevelID 
										left outer join Hr_OrganTask2 D on A_1.Hr_OrganTask2ID=D.Hr_OrganTask2ID 
										left outer join Hr_OrganTask1 E on D.Hr_OrganTask1ID=E.Hr_OrganTask1ID 

										left outer join Members BB on A.Hr_EvaluationCompetencyMemberID=BB.MemberID and BB.MemberState=1 
										left outer join ($ViewTable2) AA_1 on BB.MemberID=AA_1.MemberID 
										left outer join Hr_OrganLevels CC on AA_1.Hr_OrganLevelID=CC.Hr_OrganLevelID 
										left outer join Hr_OrganTask2 DD on AA_1.Hr_OrganTask2ID=DD.Hr_OrganTask2ID 
										left outer join Hr_OrganTask1 EE on DD.Hr_OrganTask1ID=EE.Hr_OrganTask1ID 

									where A.Hr_EvaluationID=".$SearchState." 
									order by A.MemberID asc, 
										A_1.Hr_OrganLevel asc, A.Hr_EvaluationCompetencyMemberType desc, C.Hr_OrganLevel1ID asc, C.Hr_OrganLevel2ID asc, C.Hr_OrganLevel3ID asc, C.Hr_OrganLevel4ID asc, AA_1.Hr_OrganLevel asc
									";


								$Stmt = $DbConn->prepare($Sql);
								$Stmt->execute();
								$Stmt->setFetchMode(PDO::FETCH_ASSOC);

								$ListCount   = 0;
								$ListCount2  = 0;
								$OldMemberID = 0;
								$H_AddValue  = 0;
								$H_AddTotalPoint = 0;

								while($Row = $Stmt->fetch()) {
										
										$ListCount ++;

										$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

										//=================== 자기 자신 ======================
										$Hr_EvaluationCompetencyMemberType = $Row["Hr_EvaluationCompetencyMemberType"];
										
										$MemberID = $Row["MemberID"];

										$Hr_OrganLevel = $Row["Hr_OrganLevel"];
										$Hr_OrganLevelID = $Row["Hr_OrganLevelID"];
										$Hr_OrganTask1ID = $Row["Hr_OrganTask1ID"];
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

										//1:부하 2:동료 3:상사 4:고객 5:본인
										if ($Hr_EvaluationCompetencyMemberType==1){
											$Str_Hr_EvaluationCompetencyMemberType = $부하[$LangID];
										}else if ($Hr_EvaluationCompetencyMemberType==2){
											$Str_Hr_EvaluationCompetencyMemberType = $동료[$LangID];
										}else if ($Hr_EvaluationCompetencyMemberType==3){
											$Str_Hr_EvaluationCompetencyMemberType = $상사[$LangID];
										}else if ($Hr_EvaluationCompetencyMemberType==4){
											$Str_Hr_EvaluationCompetencyMemberType = $고객[$LangID];
										}else if ($Hr_EvaluationCompetencyMemberType==5){
											$Str_Hr_EvaluationCompetencyMemberType = $본인[$LangID];
										}
										//=================== 자기 자신 ======================

										//=================== 동 료 ======================
										$T_MemberID = $Row["T_MemberID"];

										$T_Hr_OrganLevel = $Row["T_Hr_OrganLevel"];
										$T_Hr_OrganPositionName = $Row["T_Hr_OrganPositionName"];

										$T_MemberName = $Row["T_MemberName"];

										$T_Hr_OrganTask2Name = $Row["T_Hr_OrganTask2Name"];
										$T_Hr_OrganTask1Name = $Row["T_Hr_OrganTask1Name"];
										$T_Hr_OrganTask2ID   = $Row["T_Hr_OrganTask2ID"];
										$T_Hr_OrganTask1ID   = $Row["T_Hr_OrganTask1ID"];


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
										
										
										//=================== 동 료 ======================

										$T_MemberCount = $Row["T_MemberCount"];
										$TT_MemberCount = $Row["T_MemberCount"];
										if ($T_MemberCount==0){
											  $T_MemberCount = 1;
										} else {
											  $T_MemberCount = $T_MemberCount + 1; 
										}
										$T_AddValue      = $Row["Hr_EvaluationCompetencyAddValue"];
										$T_AddTotalPoint = $Row["Hr_EvaluationCompetencyAddTotalPoint"];
										
										$PrintMember = 0;
										if ($OldMemberID!=$MemberID){
                                            if ($H_AddValue > 0) { 
											?>
											<tr>
												<td colspan="3" class="uk-text-nowrap uk-table-td-right"><?=$평가자_가중치_소계[$LangID]?></td>
												<td class="uk-text-nowrap uk-table-td-center"><b style="color:#7CB342; font-size:1.1em;"><?=$H_AddValue?>%</b></td>
												<td class="uk-text-nowrap uk-table-td-center"><?=iif($H_AddTotalPoint > 0,"".number_format($H_AddTotalPoint,2)."","-")?></td>
										        <td class="uk-text-nowrap uk-table-td-center"></td>
											</tr>
											<?php
                                            }
											$OldMemberID = $MemberID;
											$PrintMember = 1;
											$ListCount2++;

											$H_AddValue  = 0; 
											$H_AddTotalPoint = 0;
										}

										$H_AddValue      = $H_AddValue + $T_AddValue; 
										$H_AddTotalPoint = $H_AddTotalPoint + $T_AddTotalPoint; 
									?>
									<tr>
										<?if ($PrintMember==1){?>
											<td class="uk-text-nowrap uk-table-td-center" rowspan="<?=$T_MemberCount?>"><?=$ListCount2?></td>
											<td class="uk-text-nowrap uk-table-td-left"   rowspan="<?=$T_MemberCount?>"><?=$MemberName?>[<?=$MemberID?>]</td>
											<td class="uk-text-nowrap uk-table-td-center" rowspan="<?=$T_MemberCount?>"><?=$Hr_OrganPositionName?></td>
											<td class="uk-text-nowrap uk-table-td-left"   rowspan="<?=$T_MemberCount?>"><?=$Str_OrganTaskName?></td>
										<?}?>

										<td class="uk-text-nowrap uk-table-td-left"><?=$T_MemberName?>[<?=$T_MemberID?>]</td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$Str_Hr_EvaluationCompetencyMemberType?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$T_Str_OrganTaskName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$T_AddValue?>%</td>
										<td class="uk-text-nowrap uk-table-td-center"><?=iif($T_AddTotalPoint > 0,"".number_format($T_AddTotalPoint,2)."","-")?></td>
										<td class="uk-text-nowrap uk-table-td-center">
										    <a type="button" href="javascript:HRCompetency_ClearOpen(<?=$SearchState?>,<?=$T_MemberID?>,<?=$MemberID?>,'<?=$MemberName?>',<?=$Hr_OrganTask1ID?>,<?=$Hr_OrganTask2ID?>,'<?=$Hr_OrganTask1Name?>','<?=$Hr_OrganTask2Name?>')" class='md-btn md-btn-primary' style='background:#808080;'>*초기화</a>
										</td>
									</tr>

								<?php
								}

								if ($H_AddValue > 0) { 
								?>
									<tr>
										<td colspan="3" class="uk-text-nowrap uk-table-td-right"><?=$평가자_가중치_소계[$LangID]?></td>
										<td class="uk-text-nowrap uk-table-td-center"><b style="color:#7CB342; font-size:1.1em;"><?=$H_AddValue?>%</b></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=iif($H_AddTotalPoint > 0,"".$H_AddTotalPoint."","-")?></td>
									    <td class="uk-text-nowrap uk-table-td-center"></td>
									</tr>
								<?php
								}
								$Stmt = null;

								if ($ListCount==0) {
								?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center" colspan="11" style="height:50px;"><?=$해당_역량평가자료가_없습니다[$LangID]?></td>
									</tr>
								<? 
								}
								?>
					<?php			
                    #---------------------------------------------------------------------------------------------------------------------#
					} else { 
                    #---------------------------------------------------------------------------------------------------------------------#
					?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center" colspan="11" style="height:50px; font-size:1.5em; color:#ddd;"><?=$역량평가를_선택_하세요[$LangID]?></td>
									</tr>
					<?php			
                    #---------------------------------------------------------------------------------------------------------------------#
					}
                    #---------------------------------------------------------------------------------------------------------------------#
					?>
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
<script>
function HRCompetency_ClearOpen(SearchState,My_MemberID,MemberID,MemberName,OrganTask1ID,OrganTask2ID,OrganTask1Name,OrganTask2Name){

    openurl = "hr_staffall_evaluation_competency_clearform.php?SearchState=" + SearchState + "&My_MemberID=" + My_MemberID + "&MemberID="+MemberID + "&MemberName=" + MemberName + "&OrganTask1ID="+OrganTask1ID + "&OrganTask2ID="+OrganTask2ID + "&OrganTask1Name="+OrganTask1Name + "&OrganTask2Name="+OrganTask2Name;

    $.colorbox({    
        href:openurl
        ,width:"95%" 
        ,height:"95%"
        ,maxWidth: "1280"
        ,maxHeight: "750"
        ,title:""
        ,iframe:true 
        ,scrolling:true
        //,onClosed:function(){location.reload(true);}
        //,onComplete:function(){alert(1);}
    }); 
}

function DeleteStaffCompetencyMember(MemberID, Hr_EvaluationCompetencyMemberID,MemberName) {

    var SearchState = document.SearchForm.SearchState.value;
    var evaluation_confirm = MemberName+" <?=$평가자를_삭제_하시겠습니까[$LangID]?>";
	if (MemberID==Hr_EvaluationCompetencyMemberID) {
         var evaluation_confirm = MemberName + " <?=$평가자를_삭제_하시겠습니까[$LangID]?>";
	}
	
	UIkit.modal.confirm(
		evaluation_confirm, 
		function(){ 

			url = "hr_ajax_set_evaluation_competency_member_delete.php";

			$.ajax(url, {
				data: {
					SearchState: SearchState,
					MemberID: MemberID,
					Hr_EvaluationCompetencyMemberID: Hr_EvaluationCompetencyMemberID
				},
				success: function (data) {
					location.reload();
				},
				error: function () {
					alert('Error while contacting server, please try again');
				}
			});


		}
	);
}
// 엑셀자료 등록
function EvaluationCompetencyTable_Upload() {

	obj = document.SearchForm.SearchState;
	if (obj.value==""){
		UIkit.modal.alert("<?=$역량평가를_선택해_주세요[$LangID]?>");
		obj.focus();
		return;
	}
     
    var selected_index = obj.selectedIndex;
    var selected_text  = obj.options[selected_index].text;

	obj = document.SearchForm.EvaluationCompetencyData;
	if (obj.value==""){
		UIkit.modal.alert(selected_text+" <?=$역량평가_자료엑셀파일을_선택해_주세요[$LangID]?>");
		obj.focus();
		return;
	}

	UIkit.modal.confirm(
		'역량평가 자료를 업로드 하시겠습니까?', 
		function(){ 
			document.SearchForm.action = "hr_evaluation_competency_excell_upload.php";
			document.SearchForm.submit();
		}
	);

}
// 평가대상자 개별등록
function EvaluationCompetencyTable_DataIn() {

    var SearchState = document.SearchForm.SearchState.value;
	if (SearchState==""){
		UIkit.modal.alert("<?=$역량평가를_선택해_주세요[$LangID]?>");
		return;
	}

	openurl = "hr_evaluation_competency_member_form.php?SearchState="+SearchState;
	
	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "850"
		,maxHeight: "700"
		,title:""
		,iframe:true 
		,scrolling:true
	}); 

}
// 평가대상자의 평가자 추가등록
function InsertStaffCompetencyMember(MemberID,MemberName){
    
    var SearchState = document.SearchForm.SearchState.value;

	openurl = "hr_evaluation_competency_member_insertform.php?SearchState="+SearchState+"&MemberID="+MemberID+"&MemberName="+MemberName;
	
	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"80%"
		,maxWidth: "850"
		,maxHeight: "500"
		,title:""
		,iframe:true 
		,scrolling:true
	}); 
}


function SearchSubmit(){
	document.SearchForm.action = "hr_staffall_evaluation_competency_list.php";
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