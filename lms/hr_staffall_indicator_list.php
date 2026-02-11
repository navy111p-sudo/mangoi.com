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
$SubMenuID = 8833;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
#-----------------------------------------------------------------------------------------------------------------------------------------#
$SearchState = isset($_REQUEST["SearchState"]) ? $_REQUEST["SearchState"] : "";
#-----------------------------------------------------------------------------------------------------------------------------------------#
?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$평가결과[$LangID]?></h3>

		<form name="SearchForm" method="get">
		<div class="md-card" style="margin-bottom:10px;">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin="">

                    <div class="uk-width-medium-1-3">
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
                    <?
					$line_cnt = 0;
                    #---------------------------------------------------------------------------------------------------------------------#
					if ($SearchState) { 
                    #---------------------------------------------------------------------------------------------------------------------#
					       ?>       
                            <h4>▷ 성과평가결과</h4>
							<table class="uk-table uk-table-align-vertical">
                                <tr>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="20%"><?=$성명[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="20%"><?=$업적평가점수[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="20%"><?=$역량평가점수[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="20%"><?=$성과평가종합점수[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="20%"><?=$성과평가_종합평가_등급[$LangID]?></td>
                                </tr>
                                <tbody>
                            <?
                            #-------------------------------------------------------------------------------------------------------------#
						    $Sql = "select count(*) TotalRowCount
							                 from Hr_Staff_ResultEvaluation 
										    where Hr_EvaluationID=".$SearchState." and
												  Hr_ResultTotalPoint is NOT NULL";
							$Stmt = $DbConn->prepare($Sql);
							$Stmt->execute();
							$Stmt->setFetchMode(PDO::FETCH_ASSOC);
							$Row = $Stmt->fetch();
							$Stmt = null;
							$TotalRowCount = $Row["TotalRowCount"];
                            #-------------------------------------------------------------------------------------------------------------#
							$Sql = "select SR.*,MM.MemberName from Hr_Staff_ResultEvaluation as SR
							           inner join Members MM on MM.MemberID=SR.MemberID
										    where SR.Hr_EvaluationID=".$SearchState." and
												  SR.Hr_ResultTotalPoint is NOT NULL";
							$Stmt = $DbConn->prepare($Sql);
							$Stmt->execute();
							$Stmt->setFetchMode(PDO::FETCH_ASSOC);
                            #-------------------------------------------------------------------------------------------------------------#
					        while ($Row = $Stmt->fetch())  {
                            #-------------------------------------------------------------------------------------------------------------#
							       ?>
                                <tr>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$Row["MemberName"]?></td>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$Row["Hr_EndEvaluationPoint"]?></td>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$Row["Hr_EvaluationCompetencyEndPoint"]?></td>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$Row["Hr_ResultTotalPoint"]?></td>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$Row["Hr_ResultLevel"]?></td>
                                </tr>
                                   <?
                            #-------------------------------------------------------------------------------------------------------------#
							}
                            #-------------------------------------------------------------------------------------------------------------#
							$Stmt = null;
                            #-------------------------------------------------------------------------------------------------------------#
                            if (!$TotalRowCount) {
							#-------------------------------------------------------------------------------------------------------------#
									?>
                                <tr>
                                   <td class="uk-text-wrap uk-table-td-center" colspan="5"><?=$성과결과_자료가_없습니다[$LangID]?></td>
                                </tr>
									<?
							#-------------------------------------------------------------------------------------------------------------#
					        }
							#-------------------------------------------------------------------------------------------------------------#
							?>
                                </tbody>
                            </table>
                            <?
                    #---------------------------------------------------------------------------------------------------------------------#
					} else { 
                    #---------------------------------------------------------------------------------------------------------------------#
							?>
                        <div class="uk-form-row" style="text-align:center; padding:30px; color:#BCBCBC; font-size:1.5em;">
                             <?=$먼저_평가리스트를_선택하시면_성과결과를_볼_수_있습니다[$LangID]?>
                        </div>
							<?
                    #---------------------------------------------------------------------------------------------------------------------#
                    } 
                    #---------------------------------------------------------------------------------------------------------------------#
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
function OpenEvaluationForm(Hr_EvaluationID){
	openurl = "hr_evaluation_form.php?Hr_EvaluationID="+Hr_EvaluationID;
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
	document.SearchForm.action = "hr_staffall_indicator_list.php";
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