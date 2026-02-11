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

<body class="disable_transitions sidebar_main_open sidebar_main_swipe" style="padding-top:0px;">

<?php
$WorkSW          = isset($_REQUEST["WorkSW"         ]) ? $_REQUEST["WorkSW"         ] : "";
#------------------------------------------------------------------------------------------------------#
$YearNumber      = isset($_REQUEST["YearNumber"     ]) ? $_REQUEST["YearNumber"     ] : "";
$MonthNumber     = isset($_REQUEST["MonthNumber"    ]) ? $_REQUEST["MonthNumber"    ] : "";
$DayNumber       = isset($_REQUEST["DayNumber"      ]) ? $_REQUEST["DayNumber"      ] : "";
#------------------------------------------------------------------------------------------------------#
$edit_mode       = isset($_REQUEST["edit_mode"      ]) ? $_REQUEST["edit_mode"      ] : "";
$edit_accid      = isset($_REQUEST["edit_accid"     ]) ? $_REQUEST["edit_accid"     ] : "";
#------------------------------------------------------------------------------------------------------#
$account_id      = isset($_REQUEST["account_id"     ]) ? $_REQUEST["account_id"     ] : "";
$account_subid   = isset($_REQUEST["account_subid"  ]) ? $_REQUEST["account_subid"  ] : "";
#------------------------------------------------------------------------------------------------------#
$account_type    = isset($_REQUEST["account_type"   ]) ? $_REQUEST["account_type"   ] : "";
$account_subname = isset($_REQUEST["account_subname"]) ? $_REQUEST["account_subname"] : "";
$account_money   = isset($_REQUEST["account_money"  ]) ? $_REQUEST["account_money"  ] : "";
#------------------------------------------------------------------------------------------------------#
if (!$account_type) {
      $account_type = 2;
} 
$SearchDate   = $YearNumber . "년 " . $MonthNumber . "월 " . $DayNumber . "일";
$account_date = $YearNumber . "-" . iif($MonthNumber < 10,"0","") . $MonthNumber . "-" . iif($DayNumber < 10,"0","") . $DayNumber;
#------------------------------------------------------------------------------------------------------#
if ($WorkSW == 3) {      // 월간 일자별 매출 추출하기
    $SearchDate   = $YearNumber . "년 " . $MonthNumber . "월";
    $account_date = $YearNumber . "-" . iif($MonthNumber < 10,"0","") . $MonthNumber;
    $account_type = 1;
}
#------------------------------------------------------------------------------------------------------#
if ($edit_mode==1) {
#------------------------------------------------------------------------------------------------------#
    $Sql = "select * from account_book where AccBookID=:AccBookID"; 
     
    $Stmt = $DbConn->prepare($Sql);
    $Stmt->bindParam(':AccBookID', $edit_accid);
    $Stmt->execute();
    $Stmt->setFetchMode(PDO::FETCH_ASSOC);
    $Row = $Stmt->fetch();
    $Stmt = null;
    
    $account_id      = $Row["AccBookConfigID"];
    $account_subid   = $Row["AccBookSubConfigID"];
    $account_subname = $Row["AccBookSubject"];
    $account_money   = $Row["AccBookMoney"];
#------------------------------------------------------------------------------------------------------#
} 
#------------------------------------------------------------------------------------------------------#
?>

<div id="page_content">
    <div id="page_content_inner">

        <form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
        <input type="hidden" id="edit_mode"   name="edit_mode"   value="<?=$edit_mode?>">
        <input type="hidden" id="YearNumber"  name="YearNumber"  value="<?=$YearNumber?>">
        <input type="hidden" id="MonthNumber" name="MonthNumber" value="<?=$MonthNumber?>">
        <input type="hidden" id="DayNumber"   name="DayNumber"   value="<?=$DayNumber?>">
        <input type="hidden" id="edit_accid"  name="edit_accid"  value="<?=$edit_accid?>">
        <input type="hidden" id="WorkSW"      name="WorkSW"      value="<?=$WorkSW?>">
        <div class="uk-grid" data-uk-grid-margin>
            <div class="uk-width-large-7-10">
                <div class="md-card">
                    <div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
                        <div class="user_heading_content">
                            <h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$SearchDate?> <?=iif($WorkSW == 3 && $account_type==1,"매출","")?>장부작성</span></h2>
                        </div>
                    </div>
                    <div class="user_content">

                        <div class="uk-margin-top" style="display:<?if ($Hr_KpiIndicatorID==""){?>none<?}?>;">
                            <div class="uk-grid" data-uk-grid-margin>
                                <label style="display:inline-block;width:120px;"><?=$계정구분[$LangID]?></label>
                                <span class="icheck-inline">
                                    <input type="radio" class="radio_input" name="account_type" id="account_type2" onClick="Account_TypeCho(2)" value="2" <?=iif(!$account_type || $account_type==2,"checked","")?> />
                                    <label for="account_type2" class="radio_label"><span class="radio_bullet"></span><?=$비용[$LangID]?></label>
                                </span>
                                <span class="icheck-inline">
                                    <input type="radio" class="radio_input" name="account_type" id="account_type1" onClick="Account_TypeCho(1)" value="1" <?=iif($account_type==1,"checked","")?> />
                                    <label for="account_type1" class="radio_label"><span class="radio_bullet"></span><?=$매출[$LangID]?></label>
                                </span>
                         <?php
                         if($WorkSW == 3 && $account_type==1) {
                         ?>
                                <span class="icheck-inline" style="color:#d80000; font-weight:500;">※ 주의 : 매출 일괄등록은 중복체크 하지 않습니다! 미리 등록여부를 체크 하신 후 작업하시기 바랍니다!</span>
                         <?php
                         }
                         ?>
                            </div>
                        </div>
                        <div class="uk-margin-top">
                            <div class="uk-grid" data-uk-grid-margin>

                                <div class="uk-width-medium-1-2" style="padding-top:7px;">
                                    <select id="account_id" name="account_id" onchange="SubAccount_Search(1)" class="uk-width-1-1" data-md-select2 data-allow-clear="true" data-placeholder="<?=$과목선택[$LangID]?>" style="width:100%;"/>
                                        <option value="0"></option>
                                        <?
                                        $Sql = "select A.*, 
                                                      ifnull( (select count(*) from account_booksubconfig B where B.AccBookConfigID=A.AccBookConfigID), 0) as SubConfigCounter 
                                                     from account_bookconfig A 
                                                     where AccBookConfigType=$account_type
                                                     order by A.AccBookConfigType asc,A.AccBookConfigID asc"; 
                                        $Stmt = $DbConn->prepare($Sql);
                                        $Stmt->execute();
                                        $Stmt->setFetchMode(PDO::FETCH_ASSOC);
                                        while($Row = $Stmt->fetch()) {

                                                 $AccBookConfigID   = $Row["AccBookConfigID"];
                                                 $AccBookConfigType = $Row["AccBookConfigType"];
                                                 $AccBookConfigName = $Row["AccBookConfigName"];
                                                 $SubConfigCounter  = $Row["SubConfigCounter"];
                                                 if ($AccBookConfigType==1) {
                                                         $AccBookConfigTypeName = "매출";
                                                 } else {
                                                         $AccBookConfigTypeName = "비용";
                                                 }
                                                ?>
                                        <option value="<?=$AccBookConfigID?>" <?if ($AccBookConfigID==$account_id){?>selected<?}?>><?=$AccBookConfigName?></option>
                                                <?
                                        }
                                        $Stmt2 = null;
                                        ?>
                                    </select>
                                </div>

                                <div class="uk-width-medium-1-2" style="padding-top:7px;">
                                    <select id="account_subid" name="account_subid" onchange="SubAccount_Search(2)" class="uk-width-1-1" data-md-select2 data-allow-clear="true" data-placeholder="<?=$세목선택[$LangID]?>" style="width:100%;"/>
                                        <option value="0"></option>
                                        <?php
                                        if ($account_id) {
                                                $Sql = "select * from account_booksubconfig where AccBookConfigID=:AccBookConfigID"; 
                                                $Stmt = $DbConn->prepare($Sql);
                                                $Stmt->bindParam(':AccBookConfigID', $account_id);
                                                $Stmt->execute();
                                                $Stmt->setFetchMode(PDO::FETCH_ASSOC);
                                                while($Row = $Stmt->fetch()) {
                                                         $AccBookSubConfigID    = $Row["AccBookSubConfigID"];
                                                         $AccBookSubConfigName  = $Row["AccBookSubConfigName"];
                                                        ?>
                                                <option value="<?=$AccBookSubConfigID?>" <?if ($AccBookSubConfigID==$account_subid){?>selected<?}?>><?=$AccBookSubConfigName?></option>
                                                        <?
                                                }
                                                $Stmt2 = null;
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="uk-margin-top">
                            <div class="uk-grid" data-uk-grid-margin>

                                <div class="uk-width-medium-1-2" style="padding-top:7px;">
                                    <label for="account_subname" style="display:inline-block; font-size:0.9em; color:#000;"><?=$상세_세목명[$LangID]?></label>
                                    <input type="text" id="account_subname" name="account_subname" value="<?=$account_subname?>" class="md-input label-fixed" style="<?=iif($account_type==1,"color:#0000ff;","color:#E80000;")?> font-weight:600;"/>
                                </div>
                                <div class="uk-width-medium-1-2" style="padding-top:7px;">
                                    <label for="account_money" style="display:inline-block; font-size:0.9em; color:#000;"><?=$금액_원[$LangID]?></label>
                                    <input type="text" id="account_money" name="account_money" value="<?=$account_money?>" onKeyup="formattedMoney(this);"  class="md-input label-fixed" style="<?=iif($account_type==1,"color:#0000ff;","color:#E80000;")?> font-weight:600; text-align:center;"/>
                                </div>

                            </div>
                        </div>

                        <div class="uk-margin-top" style="text-align:center;padding-top:30px;">
                            <a type="button" href="javascript:FormSubmit(<?=iif($edit_mode,"1","0")?>);" class="md-btn md-btn-primary" style="<?=iif($edit_mode,"background:#006CD8;","")?><?=iif($WorkSW == 3 && $account_type==1,"display:none;","")?>"><?=iif($edit_mode,"수정하기","저장하기")?></a>
                            <?
                            if ($account_type==1) {
                                 ?>
                                 <a type="button" href="javascript:Account_Auto(<?=$WorkSW?>,'<?=$account_date?>');" class="md-btn md-btn-primary" style="background:#8080c0;"><?=$SearchDate?><?=$매출액_추출[$LangID]?></a>
                                 <?
                            } 
                            ?>

                            <?
                            if ($edit_mode) {
                            ?>
                                 <a type="button" href="javascript:CancleSubmit();" class="md-btn md-btn-primary" style="background:#919191;"><?=$취소[$LangID]?></a>
                            <? 
                            } 
                            ?>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        </form>
        <form id="RegListForm" name="RegListForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
        <div class="md-card">
            <div class="md-card-content">
                <div class="uk-grid" data-uk-grid-margin>
                    <div class="uk-width-1-1">
                        <div class="uk-overflow-container">
                            <table id="account_booklist" class="uk-table uk-table-align-vertical" style="width:100%;">
                <?php
                #---------------------------------------------------------------------------------------------------------------------------#
                # 일자별 합계 추출 
                #---------------------------------------------------------------------------------------------------------------------------#
                if ($WorkSW == 3) {
                #---------------------------------------------------------------------------------------------------------------------------#
                ?>
                                <thead>
                                    <tr>
                                        <th nowrap style="width:5%;">No</th>
                                        <th nowrap style="width:15%;"><?=$매출일자[$LangID]?></th>
                                        <th nowrap style="width:8%;"><?=$계정구분[$LangID]?></th>
                                        <th nowrap style="width:15%;"><?=$과목명[$LangID]?></th>
                                        <th nowrap style="width:20%;"><?=$세목_적요[$LangID]?></th>
                                        <th nowrap style="width:15%;"><?=$금액[$LangID]?></th>
                                        <th nowrap style="width:5%;"><?=$선택[$LangID]?><input type="checkbox" id="allcheck" name="allcheck" onClick="msgChkAll('RegListForm')"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                  
                <?php
                #---------------------------------------------------------------------------------------------------------------------------#
                # 일자별 합계 추출 
                #---------------------------------------------------------------------------------------------------------------------------#
                } else if ($WorkSW == 1) {
                #---------------------------------------------------------------------------------------------------------------------------#
                ?>
                                <thead>
                                    <tr>
                                        <th nowrap style="width:5%;">No</th>
                                        <th nowrap style="width:8%;"><?=$계정구분[$LangID]?></th>
                                        <th nowrap style="width:15%;"><?=$과목명[$LangID]?></th>
                                        <th nowrap style="width:30%;"><?=$세목_적요[$LangID]?></th>
                                        <th nowrap style="width:15%;"><?=$금액[$LangID]?></th>
                                        <th nowrap style="width:22%;"><?=$수정[$LangID]?></th>
                                    </tr>
                                </thead>
                                <tbody>
                            <?php
                            $Sql = "select A.*, B.* 
                                         from account_book A 
                                    left join account_bookconfig B on A.AccBookConfigID=B.AccBookConfigID 
                                        where A.AccBookDate=:AccBookDate
                                     order by B.AccBookConfigType asc, B.AccBookConfigID asc, A.AccBookSubConfigID asc"; 
                            $Stmt = $DbConn->prepare($Sql);
                            $Stmt->bindParam(':AccBookDate', $account_date);
                            $Stmt->execute();
                            $Stmt->setFetchMode(PDO::FETCH_ASSOC);

                            $ListCount    = 0;
                            $Total_Money1 = 0;
                            $Total_Money2 = 0;
                            #------------------------------------------------------------------------------------------------------#
                            while($Row = $Stmt->fetch()) {
                            #------------------------------------------------------------------------------------------------------#
                                 $ListCount ++;
                                 $AccBookConfigID   = $Row["AccBookConfigID"];
                                 $AccBookConfigType = $Row["AccBookConfigType"];
                                 $AccBookConfigName = $Row["AccBookConfigName"];
                                 $AccBookSubConfigID = $Row["AccBookSubConfigID"];
                                 $AccBookSubject     = $Row["AccBookSubject"];
                                 $AccBookMoney       = $Row["AccBookMoney"];
                                 $AccBookID          = $Row["AccBookID"];
                                 if ($AccBookConfigType==1) {
                                         $AccBookConfigTypeName = "매출";
                                         $Total_Money1 = $Total_Money1 + $AccBookMoney;
                                 } else {
                                         $AccBookConfigTypeName = "비용";
                                         $Total_Money2 = $Total_Money2 + $AccBookMoney;
                                 }
                                 ?> 
                            <tr>
                                <td style="text-align:center; color:<?=iif($AccBookConfigType==1,"#006CD8","#E80000")?>;"><?=$ListCount?></td>
                                <td style="text-align:center; color:<?=iif($AccBookConfigType==1,"#006CD8","#E80000")?>;"><?=$AccBookConfigTypeName?></td>
                                <td style="text-align:center; color:<?=iif($AccBookConfigType==1,"#006CD8","#E80000")?>;"><?=$AccBookConfigName?></td>
                                <td style="text-align:center; color:<?=iif($AccBookConfigType==1,"#0057ae","#c40000")?>; font-weight:bold;"><?=$AccBookSubject?></td>
                                <td style="text-align:right;  color:<?=iif($AccBookConfigType==1,"#0057ae","#c40000")?>; font-weight:bold;"><?=number_format($AccBookMoney)?></td>
                                <td style="text-align:center;"> 
                                    <a class="md-btn md-btn-success md-btn-mini md-btn-wave-light" href="javascript:FormEdit(1,<?=$AccBookID?>,<?=$AccBookConfigType?>)"><?=$수정[$LangID]?></a>
                                    <a class="md-btn md-btn-success md-btn-mini md-btn-wave-light" href="javascript:FormEdit(2,<?=$AccBookID?>,<?=$AccBookConfigType?>)" style="background:#919191;"><?=$삭제[$LangID]?></a>
                                </td>
                            </tr>
                                 <?php
                            #------------------------------------------------------------------------------------------------------#
                            }
                            #------------------------------------------------------------------------------------------------------#
                            $Stmt = null;
                            #------------------------------------------------------------------------------------------------------#
                            if ($ListCount==0) {
                            #------------------------------------------------------------------------------------------------------#
                            ?>
                            <tr>
                                <td class="uk-text-wrap uk-table-td-center" colspan=6><?=$등록된_자료가_없습니다[$LangID]?></td>
                            </tr>
                            <?php
                            #------------------------------------------------------------------------------------------------------#
                            } 
                            #------------------------------------------------------------------------------------------------------#
                            if ($ListCount > 0) {
                            #------------------------------------------------------------------------------------------------------#
                                    $Total_Money = $Total_Money1 - $Total_Money2;
                            ?>
                            <tr>
                                <td class="uk-text-wrap uk-table-td-center" colspan=4><?=$매출_합계[$LangID]?></td>
                                <td style="text-align:right;  color:#006CD8; font-size:1.1em; font-weight:bold;"><?=number_format($Total_Money1)?></td>
                                <td class="uk-text-wrap uk-table-td-center"></td>
                            </tr>
                            <tr>
                                <td class="uk-text-wrap uk-table-td-center" colspan=4><?=$비용_합계[$LangID]?></td>
                                <td style="text-align:right;  color:#E80000; font-size:1.1em; font-weight:bold;"><?=number_format($Total_Money2)?></td>
                                <td class="uk-text-wrap uk-table-td-center"></td>
                            </tr>
                            <tr>
                                <td class="uk-text-wrap uk-table-td-center" colspan=4><?=$손익합계[$LangID]?></td>
                                <td style="text-align:right;  color:<?=iif($Total_Money > 0,"#0000FF","#FF0000")?>; font-size:1.1em; font-weight:bold;"><?=number_format($Total_Money)?></td>
                                <td class="uk-text-wrap uk-table-td-center"></td>
                            </tr>

                            <?php
                            #------------------------------------------------------------------------------------------------------#
                            } 
                            #------------------------------------------------------------------------------------------------------#
                            ?>
                                </tbody>


                <?php
                #---------------------------------------------------------------------------------------------------------------------------#
                }
                #---------------------------------------------------------------------------------------------------------------------------#
                ?>

                            </table>
                        </div>

                        <div class="uk-margin-top" style="text-align:center;padding-top:30px;">
                            <a type="button" id="Return_But"  href="javascript:HomeSubmit();" class="md-btn md-btn-primary" style="background:#909090;"><?=$돌아가기[$LangID]?></a>
                            <a type="button" id="Paysave_But" href="javascript:MonthPay_Save();" class="md-btn md-btn-primary" style="background:#006CD8; display:none;"><?=$매출_저장하기[$LangID]?></a>
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


<script language="javascript">
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

function Account_TypeCho(s){

    document.RegForm.account_type.value    = s;
    document.RegForm.account_subname.value = "";
    document.RegForm.account_money.value   = "";
    
    SelBoxInitOption('account_subid');
    SelBoxAddOption( 'account_subid', '<?=$세목선택[$LangID]?>', "", "");
    
    document.RegForm.submit();

}

function SubAccount_Search(s) {

    var account_id = document.RegForm.account_id.value;
    
    if (s == 1) {

        url = "ajax_get_account_subname.php";

        $.ajax(url, {
            data: {
                account_id: account_id
            },
            success: function (data) {
                 //alert(data);  
                 var data_list  = data.split("＾");
                 var data_len   = data_list.length;

                 if (data_list[0]=='9') {

                    SelBoxInitOption('account_subid');
                    SelBoxAddOption( 'account_subid', '', "0", "");

                    for (i=1; i < data_len; i++) {

                        var state_array = data_list[i].split("|");

                        ArrOptionText     = state_array[1];
                        ArrOptionValue    = state_array[0];
                        ArrOptionSelected = "";

                        SelBoxAddOption( 'account_subid', ArrOptionText, ArrOptionValue, ArrOptionSelected );
                    
                    }

                }

            },
            error: function () {

            }
        });

    } else {
        
        var account_obj     = document.getElementById("account_subid");
        var account_subid   = account_obj.options[account_obj.selectedIndex].value;
        var account_subname = account_obj.options[account_obj.selectedIndex].text;

        document.getElementById("account_subname").value = account_subname;

    }

}
// 매출 자동 추출해 오기
function Account_Auto(s,account_date) {

    obj = document.RegForm.account_id;
    if (obj.selectedIndex==0){
        UIkit.modal.alert("<?=$과목을_선택해_주세요[$LangID]?>");
        obj.focus();
        return;
    }
    obj = document.RegForm.account_subname;
    if (obj.value==""){
        UIkit.modal.alert("<?=$세목명을_입력해_주세요[$LangID]?>");
        obj.focus();
        return;
    }

    var account_obj     = document.getElementById("account_id");
    var account_id      = account_obj.options[account_obj.selectedIndex].value;
    var account_name    = account_obj.options[account_obj.selectedIndex].text;
    var account_obj     = document.getElementById("account_subid");
    var account_subid   = account_obj.options[account_obj.selectedIndex].value;
    var account_subname = document.getElementById("account_subname").value;

    UIkit.modal.confirm(
        account_date + '<?=$매출_금액을_추출해_오시겠습니까[$LangID]?>?', 
        function(){ 

            url = "ajax_get_account_autopay.php";

            $.ajax(url, {
                data: {
                    work_sw: s,
                    account_date: account_date
                },
                success: function (data) {
                     // alert(data);  
                     var data_list  = data.split("＾");
                     var data_len   = data_list.length;

                     if (data_list[0]=='9' && s==1) {          // 해당일자 매출 

                            document.RegForm.account_money.value = numberWithCommas(data_list[1],3);
   
                     } else if (data_list[0]=='9' && s==3) {   // 해당월 일자별 매출
                          
                            $('#account_booklist > tbody').remove();
                            var total_accpay = 0;
                            for (i=1; i<data_len; i++) {

                                  var row_data = data_list[i].split("|");

                                  var acc_date = row_data[0]; // 일자
                                  var acc_pay  = row_data[1]; // 결제금액
                                  var acc_val  = acc_date+"/"+account_id+"/"+account_subid+"/"+account_name+"/"+account_subname+"/"+acc_pay;
                                  total_accpay = total_accpay + parseInt(acc_pay);

                                  bd_listval  = "<tr>";
                                  bd_listval  = bd_listval + "<td style='text-align:center; color:#006CD8;'>"+i+"</td>";
                                  bd_listval  = bd_listval + "<td style='text-align:center; color:#006CD8;'>"+acc_date+"</td>";
                                  bd_listval  = bd_listval + "<td style='text-align:center; color:#006CD8;'><?=$매출[$LangID]?></td>";
                                  bd_listval  = bd_listval + "<td style='text-align:center; color:#006CD8;'>"+account_name+"</td>";
                                  bd_listval  = bd_listval + "<td style='text-align:center; color:#006CD8;'>"+account_subname+"</td>";
                                  bd_listval  = bd_listval + "<td style='text-align:right;  color:#006CD8;'>"+numberWithCommas(acc_pay,3)+"</td>";
                                  bd_listval  = bd_listval + "<td style='text-align:center;'>";
                                  bd_listval  = bd_listval + "<input type='checkbox' id='check[]' name='check[]' value='"+acc_val+"' onClick=\"AccPay_ChkToggle('RegListForm')\">";
                                  bd_listval  = bd_listval + "</td>";
                                  bd_listval  = bd_listval + "</tr>";

                                  $('#account_booklist').append(bd_listval);
                          
                            }
							 
                            bd_listval  = "<tr>";
                            bd_listval  = bd_listval + "<td style='text-align:center; color:#006CD8;' colspan=5><?=$합계[$LangID]?></td>";
                            bd_listval  = bd_listval + "<td style='text-align:right;  color:#0000cc; font-weight:bold;'>"+numberWithCommas(total_accpay,3)+"</td>";
                            bd_listval  = bd_listval + "<td style='text-align:center;'> </td>";
                            bd_listval  = bd_listval + "</tr>";
                            $('#account_booklist').append(bd_listval);

                            //document.RegForm.account_money.value = numberWithCommas(total_accpay,3);
                            

                            $('#Paysave_But').show();
                     
                     }

                },
                error: function () {

                }
            });

        }
    );

}
//---------------------------------------------------------------------------------------------------------------------------//
// 리스트 체크처리
//---------------------------------------------------------------------------------------------------------------------------//
function MonthPay_Save() {

     var chk_cnt      = 0;
     var account_val  = "";
     for(i = 0; i < document.RegListForm.elements.length; ++i) {
          if(document.RegListForm.elements[i].name == 'check[]' && document.RegListForm.elements[i].checked == true) {
               chk_cnt++;
               account_val = account_val + jviif(account_val,"＾","") + document.RegListForm.elements[i].value;
          } 
     }
     if (!chk_cnt) {
          alert("<?=$P1개_이상_체크_하세요[$LangID]?>");
          return;
     }

     UIkit.modal.confirm(
        '선택하신 매출금액('+chk_cnt+')들을 저장 하시겠습니까?', 
        function(){ 

            url = "ajax_get_account_autopay.php";

            $.ajax(url, {
                data: {
                     work_sw: 31,
                     account_val: account_val 
                },
                success: function (data) {

                     var data_list  = data.split("＾");
                     var data_len   = data_list.length;

                     if (data_list[0]=='9') {  

                            UIkit.modal.alert("성공적으로 매출정보("+data_list[1]+")를 일괄 저장 했습니다!");
                            $('#Paysave_But').hide();

                     } else {

                            UIkit.modal.alert(data);

                     }

                },
                error: function () {

                }
            });

        }

    );

}

function FormSubmit(s) {

    obj = document.RegForm.account_id;
    if (obj.selectedIndex==0){
        UIkit.modal.alert("<?=$과목을_선택해_주세요[$LangID]?>");
        obj.focus();
        return;
    }
    obj = document.RegForm.account_subname;
    if (obj.value==""){
        UIkit.modal.alert("<?=$세목명을_입력해_주세요[$LangID]?>");
        obj.focus();
        return;
    }
    obj = document.RegForm.account_money;
    if (obj.value==""){
        UIkit.modal.alert("<?=$금액을_입력해_주세요[$LangID]?>");
        obj.focus();
        return;
    }

    var modal_msg = jviif(s > 0,"<?=$수정_하시겠습니까[$LangID]?>?","<?=$저장_하시겠습니까[$LangID]?>?")

    UIkit.modal.confirm(
        modal_msg, 
        function(){ 
            document.RegForm.edit_mode.value = s;
            document.RegForm.action = "account_book_action.php";
            document.RegForm.submit();
        }
    );

}

function FormEdit(s,edit_accid, acc_type) {

    document.RegForm.edit_mode.value    = s;
    document.RegForm.edit_accid.value   = edit_accid;
    document.RegForm.account_type.value = acc_type;

    if (s == 1) {
          
          document.RegForm.submit();

    } else {
         
          UIkit.modal.confirm(
                '<?=$자료를_삭제_하시겠습니까[$LangID]?>?', 
                function(){ 
                    document.RegForm.action = "account_book_action.php";
                    document.RegForm.submit();
                }
         );

    }


}

function CancleSubmit() {

     document.RegForm.edit_accid.value         = "";
     document.RegForm.edit_mode.value          = "";
     document.RegForm.account_subname.value    = "";
     document.RegForm.account_money.value      = "";
     document.RegForm.account_id.selectedIndex    = 0;
     document.RegForm.account_subid.selectedIndex = 0;

     document.RegForm.submit();

}

function HomeSubmit() {

     var SearchStartYear  = document.RegForm.YearNumber.value;
     var SearchStartMonth = document.RegForm.MonthNumber.value;
     var SearchStartDay   = document.RegForm.DayNumber.value;

     document.RegForm.target = "_top";
     document.RegForm.action = "account_book.php?SearchStartYear=" + SearchStartYear + "&SearchStartMonth=" + SearchStartMonth + "&SearchStartDay=" + SearchStartDay;
     document.RegForm.submit();

}
//---------------------------------------------------------------------------------------------------------------------------//
//   Input type="Text"를 Money 에 관련된 내용으로 사용
//---------------------------------------------------------------------------------------------------------------------------//
function formattedMoney(oTa) {

      var vm     = oTa.value;
      var a      = removeFormat(vm,',');
      var money  = reverse(a);
      var format = "";
      for(var i = money.length-1; i > -1; i--) {
            if((i+1)%3 == 0 && money.length-1 != i) format += ",";
            format += money.charAt(i);
      }
      oTa.value = format;

}
//---------------------------------------------------------------------------------------------------------------------------//
//  String을 꺼꾸로 만들어 준다.
//---------------------------------------------------------------------------------------------------------------------------//
function reverse(s) {

      var rev = "";

      for(var i = s.length-1; i >= 0 ; i--) {
            rev += s.charAt(i);
      }

      return rev;

}
//---------------------------------------------------------------------------------------------------------------------------//
//   형식화된 내용의 심볼들을 없애고 원래의 내용만을 보여준다.
//---------------------------------------------------------------------------------------------------------------------------//
function removeFormat(content, sep) {

      var real = "";
      var contents = content.split(sep);

      for(var i = 0; i < contents.length; i++) {
              real += contents[i];
      }
      var real_val = "";
      for(var i = 0; i < real.length; i++) {
            var chr = real.substr(i,1);
            if(chr >= '0' && chr <= '9') {
                   real_val += chr;
            }
      }

      return real_val;

}
//---------------------------------------------------------------------------------------------------------------------------//
// 숫자인지 체크
//---------------------------------------------------------------------------------------------------------------------------//
function IsNumberData(sMsg) {

      for(var i = 0; i < sMsg.length; i++) {
             var chr = sMsg.substr(i,1);
             if(chr < '0' || chr > '9') {
                  return false;
             }
      }
      return true;

}

//---------------------------------------------------------------------------------------------------------------------------//
// 화폐단위
//---------------------------------------------------------------------------------------------------------------------------//
function numberWithCommas(x) {

      return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

}
//---------------------------------------------------------------------------------------------------------------------------//
// 전체선택
//---------------------------------------------------------------------------------------------------------------------------//
function msgChkAll(formname) {

        var checkbox_obj = document.forms[formname]["allcheck"];
        if (checkbox_obj.checked == true) {
              var checkFlag = true;
        } else {
              var checkFlag = false;
        }
        var checkbox_obj = document.forms[formname];
        var total_accpay = 0;
        for(i = 0; i < checkbox_obj.elements.length; ++i) {
              if(checkbox_obj.elements[i].name == 'check[]') {
                      checkbox_obj.elements[i].checked = checkFlag;
                      if (checkFlag == true) {
                           var acc_pay  = checkbox_obj.elements[i].value.split("/")[5];
                           total_accpay = total_accpay + parseInt(acc_pay);
                      }
              }
        }
		if (total_accpay == 0)	{
               document.RegForm.account_money.value = "";
		} else {
               document.RegForm.account_money.value = numberWithCommas(total_accpay,3);
		}

}

function AccPay_ChkToggle(formname) {

        var checkbox_obj = document.forms[formname];
        var total_accpay = 0;
        for(i = 0; i < checkbox_obj.elements.length; ++i) {
              if(checkbox_obj.elements[i].name == 'check[]') {
                    if (checkbox_obj.elements[i].checked == true) {
                           var acc_pay  = checkbox_obj.elements[i].value.split("/")[5];
                           total_accpay = total_accpay + parseInt(acc_pay);
                    }
              }
        }
		if (total_accpay == 0)	{
               document.RegForm.account_money.value = "";
		} else {
               document.RegForm.account_money.value = numberWithCommas(total_accpay,3);
		}

}
/** ===================================== 기본함수 ===================================== **/
// Option객체를 생성해서 Return
function SelBoxCreateOption( text, value, selected )
{
    var oOption = document.createElement("OPTION"); // Option 객체를 생성
    oOption.text = text; // Text(Keyword)를 입력
    oOption.value = value; // Value를 입력
    if (selected=="selected"){
        oOption.selected = true;
    }
    return oOption;
}

// SelectBox의 Option을 초기화
function SelBoxInitOption( ObjId ){
    var SelectObj = document.getElementById( ObjId );
    if ( SelectObj == null ) return; // 객체가 존재하지 않으면 취소

    SelectObj.options.length = 0; // 길이를 0으로 하면 초기화
}

// Option을 추가
function SelBoxAddOption( ObjId, text, value, selected ){
    var SelectObj = document.getElementById( ObjId );

    SelectObj.add( SelBoxCreateOption( text , value, selected ) );
    text     = "";
    value    = "";
    selected = "";
}
/** ===================================== 기본함수 ===================================== **/

</script>

<?php
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>