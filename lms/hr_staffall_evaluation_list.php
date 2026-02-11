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
$SubMenuID = 8822;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');

$SearchState       = isset($_REQUEST["SearchState"    ]) ? $_REQUEST["SearchState"    ] : "";
?>

<div id="page_content">
    <div id="page_content_inner">

        <h3 class="heading_b uk-margin-bottom"><?=$업적평가현황[$LangID]?></h3>

        <form name="SearchForm" method="get">
		<input type="hidden" id="TargetMenu" name="TargetMenu" value="<?=$TargetMenu?>" />
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
                                        <option value="<?=$Hr_EvaluationID?>" <? if ($Hr_EvaluationID==$SearchState){?> selected <?}?>><?=$Str_Hr_EvaluationYear?> <?=$Hr_EvaluationTypeName?></option>
                                        <?php
                                        $ListCount ++;
                                    }
                                    $Stmt = null;
                                    ?>

                            </select> 
                        </div>
                    </div>
                    <div class="uk-width-medium-2-3">
					<?
					if ($SearchState) {
					?>
                        <!----a href="javascript:TestData_Delete(9,0,'')" class="md-btn md-btn-primary" style="float:right; margin-right:30px; margin-top:10px; background:#1CC6EA;">테스트데이터 전체삭제</a---->
					<?
                    }
					?>
                    </div>
            </div>
        </div>
        </form>

		<form name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" id="TargetMenu"          name="TargetMenu"          value="<?=$TargetMenu?>" />
		<input type="hidden" id="SearchState"         name="SearchState"         value="<?=$SearchState?>" style="width:10%;" />
        <div class="md-card">
            <div class="md-card-content">
                <div class="uk-grid" data-uk-grid-margin>
                    <div class="uk-width-1-1">
                        <div id="kpi_listup" class="uk-overflow-container">
		            <?
                    #---------------------------------------------------------------------------------------------------------------------#
					if ($SearchState) { 
                    #---------------------------------------------------------------------------------------------------------------------#
							$ViweTable = "select AAAA.* 
											from Hr_OrganLevelTaskMembers AAAA 
											inner join Members BBBB on AAAA.MemberID=BBBB.MemberID and BBBB.MemberState=1";
							$Sql = "select 
										A.*,
										B.MemberName,
										TT.*,

										ifnull(D.Hr_OrganTask2Name, '') as Hr_OrganTask2Name,
										ifnull(E.Hr_OrganTask1Name, '') as Hr_OrganTask1Name,

										ifnull((select Hr_OrganLevelID   from Hr_OrganLevels where Hr_OrganLevelID=C.Hr_OrganLevel1ID), '') as Hr_OrganLevel1ID, 
										ifnull((select Hr_OrganLevelID   from Hr_OrganLevels where Hr_OrganLevelID=C.Hr_OrganLevel2ID), '') as Hr_OrganLevel2ID,
										ifnull((select Hr_OrganLevelID   from Hr_OrganLevels where Hr_OrganLevelID=C.Hr_OrganLevel3ID), '') as Hr_OrganLevel3ID,
										ifnull((select Hr_OrganLevelID   from Hr_OrganLevels where Hr_OrganLevelID=C.Hr_OrganLevel4ID), '') as Hr_OrganLevel4ID,
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
										inner join Hr_Staff_Target TT on TT.MemberID=B.MemberID and TT.Hr_EvaluationID=".$SearchState." and TT.Hr_TargetState='9' 

										inner join Hr_OrganLevels C on C.Hr_OrganLevelID=A.Hr_OrganLevelID 
										left outer join Hr_OrganTask2 D on D.Hr_OrganTask2ID=A.Hr_OrganTask2ID 
										left outer join Hr_OrganTask1 E on E.Hr_OrganTask1ID=D.Hr_OrganTask1ID

										left outer join ($ViweTable) AA on AA.Hr_OrganLevel < A.Hr_OrganLevel
										left outer join Members BB on AA.MemberID=BB.MemberID
										left outer join Hr_OrganLevels CC on AA.Hr_OrganLevelID=CC.Hr_OrganLevelID 
										left outer join Hr_OrganTask2 DD on AA.Hr_OrganTask2ID=DD.Hr_OrganTask2ID 
										left outer join Hr_OrganTask1 EE on DD.Hr_OrganTask1ID=EE.Hr_OrganTask1ID 
									
									Group By TT.MemberID";
							?>
							<table class="uk-table uk-table-align-vertical">
                                <tr>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="5%">NO</td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="8%"><?=$성명[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="15%"><?=$소속부서[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="10%"><?=$직무[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="6%"><?=$본인작성[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="6%"><?=$P1차상사[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="6%"><?=$P2차상사[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="6%"><?=$최종상사[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="6%"><?=$등급[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="6%"><?=$최종점수[$LangID]?></td>
                                   <td class="uk-text-wrap uk-table-td-center" style="background:#F6F6F6;" width="10%"><?=$확인[$LangID]?></td>
                                </tr>
                                <tbody>
							<?
							$Stmt = $DbConn->prepare($Sql);
							$Stmt->execute();
							$Stmt->setFetchMode(PDO::FETCH_ASSOC);
                            $line_cnt = 0;
							#-------------------------------------------------------------------------------------------------------------#
							while($Row = $Stmt->fetch()) {
							#-------------------------------------------------------------------------------------------------------------#
                                    $line_cnt++;

									$Hr_EvaluationID = $Row["Hr_EvaluationID"];
									$Hr_UseYN        = $Row["Hr_UseYN"];
									//=================== 자기 자신 ======================
									$MemberID = $Row["MemberID"];

									$Hr_OrganLevel    = $Row["Hr_OrganLevel"];
									$Hr_OrganLevel1ID = $Row["Hr_OrganLevel1ID"];
									$Hr_OrganLevel2ID = $Row["Hr_OrganLevel2ID"];
									$Hr_OrganLevel3ID = $Row["Hr_OrganLevel3ID"];
									$Hr_OrganLevel4ID = $Row["Hr_OrganLevel4ID"];

									$Hr_OrganTask2ID  = $Row["Hr_OrganTask2ID"];
									$Hr_OrganPositionName = $Row["Hr_OrganPositionName"];

									$MemberName = $Row["MemberName"];

									$Hr_OrganTask2Name = $Row["Hr_OrganTask2Name"];
									$Hr_OrganTask1Name = $Row["Hr_OrganTask1Name"];


									$Hr_OrganLevelName1 = $Row["Hr_OrganLevelName1"];
									$Hr_OrganLevelName2 = $Row["Hr_OrganLevelName2"];
									$Hr_OrganLevelName3 = $Row["Hr_OrganLevelName3"];
									$Hr_OrganLevelName4 = $Row["Hr_OrganLevelName4"];

									$Str_Hr_OrganLevelID = $Hr_OrganLevel1ID;
									$Level_None = "";
									if ($Hr_OrganLevel2ID!=""){
										$Str_Hr_OrganLevelID .= " > " . $Hr_OrganLevel2ID;
									} else {
                                        $Level_None .= "N2"; 
									}
									if ($Hr_OrganLevel3ID!=""){
										$Str_Hr_OrganLevelID .= " > " . $Hr_OrganLevel3ID;
									} else {
										if ($Level_None) {
                                               $Level_None .= "/";
										}
                                        $Level_None .= "N1"; 
									}
									if ($Hr_OrganLevel4ID!=""){
										$Str_Hr_OrganLevelID .= " > " . $Hr_OrganLevel4ID;
									}


									$Str_Hr_OrganLevelName = $Hr_OrganLevelName1;
									if ($Hr_OrganLevelName2!=""){
										 $Str_Hr_OrganLevelName = $Hr_OrganLevelName2;
									}
									if ($Hr_OrganLevelName3!=""){
										$Str_Hr_OrganLevelName = $Hr_OrganLevelName3;
									}
									if ($Hr_OrganLevelName4!=""){
										$Str_Hr_OrganLevelName = $Hr_OrganLevelName4;
									}

                                    $Hr_SelfTotalPoint = $Row["Hr_SelfTotalPoint"  ];   // 자기합계
									$Hr_ChangePoint    = $Row["Hr_ChangePoint"    ];   // 환산점수
									$Hr_TargetState    = $Row["Hr_EvaluationState"];
                                    $Hr_UseYN          = $Row["Hr_EvaUseYN"];
									$SF_State          = "미작성";
									$SF_StateBkcolor   = "#919191";
									$ViewTargetState   = 2;
									if ($Hr_UseYN=='N' and $Hr_TargetState>=9 and $Hr_TargetState<=11) {
                                           $SF_State = "진행중";
										   $SF_StateBkcolor = "#148FB4";
									} else if ($Hr_UseYN=='Y' and $Hr_TargetState==12) {
										   $ViewTargetState = 9;
                                           $SF_State = "평가완료";
										   $SF_StateBkcolor = "#00B2A6";
									}

									$Hr_TargetID = $Row["Hr_TargetID"];

                                    $Hr_FirstTotalPoint   = $Row["Hr_FirstTotalPoint" ];   // 1차상사 합계
                                    $Hr_SecondTotalPoint  = $Row["Hr_SecondTotalPoint"];   // 2차상사 합계
                                    $Hr_EndTotalPoint     = $Row["Hr_EndTotalPoint"   ];   // 3차상사 합계
									//====================================================//
									//=================== 업적평가계산식 ===================//
									//====================================================//
									$Hr_EvaluationLevel    = $Row["Hr_EvaluationLevel"   ];     // 최종 업적 등급
									$Hr_EndEvaluationPoint = $Row["Hr_EndEvaluationPoint"];     // 최종 업적 점수
                                    ?>
                                <tr>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$line_cnt?></td>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$MemberName?></td>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$Str_Hr_OrganLevelName?></td>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$Hr_OrganTask1Name?></td>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$Hr_SelfTotalPoint?></td>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$Hr_FirstTotalPoint?></td>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$Hr_SecondTotalPoint?></td>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$Hr_EndTotalPoint?></td>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$Hr_EvaluationLevel?></td>
                                   <td class="uk-text-wrap uk-table-td-center"><?=$Hr_EndEvaluationPoint?></td>
                                   <td class="uk-text-wrap uk-table-td-center">
							           <a type="button" href="javascript:HRTargetEva_ViewOpen(9,3,<?=$MemberID?>,<?=$Hr_EvaluationID?>,'<?=$Level_None?>')" class="md-btn md-btn-primary" style="background:<?=$SF_StateBkcolor?>;"><?=$SF_State?></a> 
								   </td>
                                </tr>
                                    <?
                            #-------------------------------------------------------------------------------------------------------------#
                            }
                            #-------------------------------------------------------------------------------------------------------------#
						    $Stmt = null;
							if (!$line_cnt) {
							?>
                                <tr>
                                   <td class="uk-text-wrap uk-table-td-center" style="text-align:center; padding:20px; color:#BCBCBC; font-size:1.5em;" colspan=12>
									  <?=$목표설정_대상이_없습니다[$LangID]?>
								   </td>
                                </tr>
							<?
							}
							?>

                                </tbody>
                            </table>
					<? 
                    #---------------------------------------------------------------------------------------------------------------------#
                    }
                    #---------------------------------------------------------------------------------------------------------------------#
					?>
                        </div>
                        <div class="uk-form-row" style="text-align:center; padding:30px; color:#BCBCBC; font-size:1.5em;">
		            <?
					if ($SearchState and $_LINK_ADMIN_LEVEL_ID_==0) { 
                    ?>  
                               <!--a type="button" href="javascript:ChangePointSubmit()" class="md-btn md-btn-primary" style="background:#919191;">평가점수 다시계산</a-->
					<? 
                    }
					?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		</form>

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
//-------------------------------------------------------------------------------------------------------------------------//
// 단문비교함수
//-------------------------------------------------------------------------------------------------------------------------//
function jviif( sw, a, b ) {

      if (sw) {
            return a;
      } else {
            return b;
      }

}
//-------------------------------------------------------------------------------------------------------------------------//
// 평가리스트 선택
//-------------------------------------------------------------------------------------------------------------------------//
function SearchSubmit(){
	   document.SearchForm.action = "hr_staffall_evaluation_list.php";
	   document.SearchForm.submit();

}
//-------------------------------------------------------------------------------------------------------------------------//
// 평가리스트 평가점수 다시 계산해서 DB에 넣기
//-------------------------------------------------------------------------------------------------------------------------//
function ChangePointSubmit(){

	   UIkit.modal.confirm(
			"<?=$평가점수를_재_조정_하시겠습니까[$LangID]?>", 
			function(){ 
				  document.RegForm.action = "hr_staffall_evaluation_changepoint.php";
				  document.RegForm.submit();
			}
	  );  

}

//-------------------------------------------------------------------------------------------------------------------------//
// 부문목표설정
//-------------------------------------------------------------------------------------------------------------------------//
function HRTarget_ViewOpen(vs,MemberID,EvaluationID) {

	var SearchState = document.RegForm.SearchState.value;
    
	openurl = "hr_staff_target_kpi_list.php?ViewSW=" + vs + "&SearchState=" + SearchState + "&TargetMenu=2&MemberID=" + MemberID + "&EvaluationID=" + EvaluationID;

    $.colorbox({    
        href:openurl
        ,width:"98%" 
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
//-------------------------------------------------------------------------------------------------------------------------//
// 부문업적평가 개인별 업적평가확인
//-------------------------------------------------------------------------------------------------------------------------//
function HRTargetEva_ViewOpen(vs,lvchi,MemberID,EvaluationID,LevelNone) {
    
	var SearchState = document.RegForm.SearchState.value;
    
	openurl = "hr_staff_evaluation_form.php?ViewSW=" + vs + "&LevelChi=" + lvchi + "&SearchState=" + SearchState + "&TargetMenu=2&MemberID=" + MemberID + "&EvaluationID=" + EvaluationID + "&LevelNone=" + LevelNone;

    $.colorbox({    
        href:openurl
        ,width:"98%" 
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

//-------------------------------------------------------------------------------------------------------------------------//
// 데이터삭제
//-------------------------------------------------------------------------------------------------------------------------//
function TestData_Delete(s,TargetID,MemberName) {
     
	 var SearchState = document.RegForm.SearchState.value;
	

	 if (s==1) {
	 
		   UIkit.modal.confirm(
				'['+MemberName + "] <?=$목표설정값을_초기화_하시겠습니까[$LangID]?>", 
				function(){ 
					  document.RegForm.action = "hr_staffall_target_reset.php?SearchState=" + SearchState + "&TargetID=" + TargetID;
					  document.RegForm.submit();
				}
		  );  

	 } else {
      
		   UIkit.modal.confirm(
				"<?=$부서_목표설정_데이터를_전체삭제_하시겠습니까[$LangID]?>", 
				function(){ 
					  document.RegForm.action = "hr_staffall_target_alldelete.php?SearchState=" + SearchState;
					  document.RegForm.submit();
				}
		  );  

     } 

}
</script>

<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>