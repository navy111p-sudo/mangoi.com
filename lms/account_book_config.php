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
$edit_mode       = isset($_REQUEST["edit_mode"      ]) ? $_REQUEST["edit_mode"      ] : "";
$acc_type        = isset($_REQUEST["acc_type"       ]) ? $_REQUEST["acc_type"       ] : "";

$account_id      = isset($_REQUEST["account_id"     ]) ? $_REQUEST["account_id"     ] : "";
$account_subid   = isset($_REQUEST["account_subid"  ]) ? $_REQUEST["account_subid"  ] : "";

$account_type    = isset($_REQUEST["account_type"   ]) ? $_REQUEST["account_type"   ] : "";
$account_name    = isset($_REQUEST["account_name"   ]) ? $_REQUEST["account_name"   ] : "";
$account_subname = isset($_REQUEST["account_subname"]) ? $_REQUEST["account_subname"] : "";
$AccBookConfigSubType = 1;

if ($account_id) {

	$Sql = "SELECT * from account_bookconfig where AccBookConfigID=:AccBookConfigID"; 
	 
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':AccBookConfigID', $account_id);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	
	$account_type = $Row["AccBookConfigType"];
	$account_name = $Row["AccBookConfigName"];
	$AccBookConfigSubType = $Row["AccBookConfigSubType"];

} 

if ($account_subid) {

	$Sql = "SELECT * from account_booksubconfig where AccBookSubConfigID=:AccBookSubConfigID"; 
	 
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':AccBookSubConfigID', $account_subid);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	
	$account_subid   = $Row["AccBookSubConfigID"];
	$account_subname = $Row["AccBookSubConfigName"];
	

}

if ($edit_mode==3) {
	
	$Sql = "SELECT A.*, B.* 
	             from account_booksubconfig A 
			left join account_bookconfig B on A.AccBookConfigID=B.AccBookConfigID 
                where A.AccBookConfigID=:AccBookConfigID
		     order by B.AccBookConfigType asc, B.AccBookConfigID asc, A.AccBookSubConfigID asc"; 
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':AccBookConfigID', $account_id);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);

} else {

	$Sql = "SELECT A.*, ifnull( (select count(*) from account_booksubconfig B where B.AccBookConfigID=A.AccBookConfigID), 0) as SubConfigCounter from account_bookconfig A order by A.AccBookConfigType asc,A.AccBookConfigID asc"; 
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);

}
?>


<div id="page_content">
	<div id="page_content_inner">
		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" id="Oldaccount_id"    name="Oldaccount_id" value="<?=$account_id?>">
		<input type="hidden" id="account_id"       name="account_id"    value="<?=$account_id?>">
		<input type="hidden" id="account_subid"    name="account_subid" value="<?=$account_subid?>">
		<input type="hidden" id="edit_mode"        name="edit_mode"     value="<?=$edit_mode?>">
		<input type="hidden" id="Oldacc_type"      name="Oldacc_type"   value="<?=$acc_type?>">
		<input type="hidden" id="acc_type"         name="acc_type"      value="<?=$acc_type?>">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$계정과목설정[$LangID]?></span><span class="sub-heading" id="user_edit_position"></span></h2>
						</div>
					</div>
					<div class="user_content">

						<div class="uk-margin-top" style="display:<?if ($Hr_KpiIndicatorID==""){?>none<?}?>;">
							<div class="uk-grid" data-uk-grid-margin>
								<label style="display:inline-block;width:120px;"><?=$계정구분[$LangID]?></label>
								<span class="icheck-inline">
									<input type="radio" class="radio_input" name="account_type" id="account_type1" value="1" <?=iif(!$account_type || $account_type==1,"checked","")?> />
									<label for="account_type1" class="radio_label"><span class="radio_bullet"></span><?=$수익[$LangID]?></label>
								</span>
								<span class="icheck-inline">
									<input type="radio" class="radio_input" name="account_type" id="account_type2" value="2" <?=iif($account_type==2,"checked","")?> />
									<label for="account_type2" class="radio_label"><span class="radio_bullet"></span><?=$비용[$LangID]?></label>
								</span>
								
							</div>
						</div>

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-2-2">
									<label style="display:inline-block;width:120px;"><?=$구분[$LangID]?></label>
									<span class="icheck-inline">
										<input type="radio" class="radio_input" name="AccBookConfigSubType" id="AccBookConfigSubType1" value="1" <?=iif(!$AccBookConfigSubType || $AccBookConfigSubType==1,"checked","")?> />
										<label for="AccBookConfigSubType1" class="radio_label"><span class="radio_bullet"></span><?=$매출[$LangID]?></label>
									</span>
									<span class="icheck-inline">
										<input type="radio" class="radio_input" name="AccBookConfigSubType" id="AccBookConfigSubType2" value="2" <?=iif(!$AccBookConfigSubType || $AccBookConfigSubType==2,"checked","")?> />
										<label for="AccBookConfigSubType2" class="radio_label"><span class="radio_bullet"></span><?=$영업외수익[$LangID]?></label>
									</span>
									<span class="icheck-inline">
										<input type="radio" class="radio_input" name="AccBookConfigSubType" id="AccBookConfigSubType3" value="3" <?=iif(!$AccBookConfigSubType || $AccBookConfigSubType==3,"checked","")?> />
										<label for="AccBookConfigSubType3" class="radio_label"><span class="radio_bullet"></span><?=$판매비와관리비[$LangID]?></label>
									</span>
									<span class="icheck-inline">
										<input type="radio" class="radio_input" name="AccBookConfigSubType" id="AccBookConfigSubType4" value="4" <?=iif(!$AccBookConfigSubType || $AccBookConfigSubType==4,"checked","")?> />
										<label for="AccBookConfigSubType4" class="radio_label"><span class="radio_bullet"></span><?=$영업외비용[$LangID]?></label>
									</span>
								</div>
							</div>
						</div>

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-2-2">
									<label for="account_name" style="display:inline-block;width:120px; font-size:1.0em; color:#000;"><?=$과목명[$LangID]?></label>
									<input type="text" id="account_name" name="account_name" value="<?=$account_name?>" class="md-input label-fixed" style="<?=iif($account_id && $acc_type==1,"color:#0000ff;","color:#E80000;")?> font-weight:600;"/>
								</div>
							</div>
						</div>

               <?php
			   if ($edit_mode==3) {
			   ?>

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-2-2">
									<label for="account_subname" style="display:inline-block;width:120px; font-size:1.0em; color:#000;"><?=$세목명[$LangID]?></label>
									<input type="text" id="account_subname" name="account_subname" value="<?=$account_subname?>" class="md-input label-fixed" style="<?=iif($account_id && $acc_type==1,"color:#0000ff;","color:#d80000;")?> font-weight:600;"/>
								</div>
							</div>
						</div>

						<div class="uk-margin-top" style="text-align:center;padding-top:30px;">
							<a type="button" href="javascript:SubFormSubmit(<?=iif($account_id and $account_subid,"31","33")?>);" class="md-btn md-btn-primary" style="<?=iif($account_id and $account_subid,"background:#006CD8;","")?>">세목 <?=iif($account_id and $account_subid,"수정하기","저장하기")?></a>
							<?
							if ($account_subid) {
							?>
							     <a type="button" href="javascript:SubCancleSubmit();" class="md-btn md-btn-primary" style="background:#8080c0;"><?=$세목_수정취소[$LangID]?></a>
							<? 
							} 
							?>
						    <a type="button" href="javascript:CancleSubmit();" class="md-btn md-btn-primary" style="background:#919191;"><?=$세목_등록취소[$LangID]?></a>
						</div>

             
               <?php
               } else {
			   ?>

						<div class="uk-margin-top" style="text-align:center;padding-top:30px;">
							<a type="button" href="javascript:FormSubmit(<?=iif($account_id,"1","")?>);" class="md-btn md-btn-primary" style="<?=iif($account_id,"background:#006CD8;","")?>"><?=iif($account_id,"수정하기","저장하기")?></a>
							<?
							if ($account_id) {
							?>
							     <a type="button" href="javascript:CancleSubmit();" class="md-btn md-btn-primary" style="background:#919191;">취소</a>
							<? 
							} 
							?>
						</div>

               <?php
               } 
			   ?>
					</div>
				</div>
			</div>
		</div>
		</form>

		<div class="md-card">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin>
					<div class="uk-width-1-1">
						<div style="color:#E80000">* 과목명옆 괄호 안의 숫자는 세목의 수를 나타냅니다.</div>
						<div class="uk-overflow-container">
							<table class="uk-table uk-table-align-vertical" style="width:100%;">
								<thead>
									<tr>
										<th nowrap style="width:8%;">No</th>
										<th nowrap style="width:15%;"><?=$과목구분[$LangID]?></th>
										<th nowrap>과목명</th>
										<th nowrap style="width:<?=iif($edit_mode==3,"30%","15%")?>;"><?=$세목[$LangID]?></th>
										<th nowrap style="width:22%;"><?=$수정[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
                  
							<?php
                            $ListCount = 0;
							#------------------------------------------------------------------------------------------------------#
							if ($edit_mode < 3) {
							#------------------------------------------------------------------------------------------------------#
									while($Row = $Stmt->fetch()) {
							        #----------------------------------------------------------------------------------------------#
										 $ListCount ++;
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
									<tr>
										<td style="text-align:center; color:<?=iif($AccBookConfigType==1,"#006CD8","#E80000")?>;"><?=$ListCount?></td>
										<td style="text-align:center; color:<?=iif($AccBookConfigType==1,"#006CD8","#E80000")?>;"><?=$AccBookConfigTypeName?></td>
										<td style="text-align:center; color:<?=iif($AccBookConfigType==1,"#006CD8","#E80000")?>;"><?=$AccBookConfigName?> <?=iif($SubConfigCounter,"<font color=grey>(".$SubConfigCounter.")</font>","")?></td>
										<td style="text-align:center;"> 
											<a class="md-btn md-btn-success md-btn-mini md-btn-wave-light" href="javascript:FormEdit(3,<?=$AccBookConfigID?>,<?=$AccBookConfigType?>)" style="background:#2BBDE8;"><?=$세목설정[$LangID]?></a>
										</td>
										<td style="text-align:center;"> 
											<a class="md-btn md-btn-success md-btn-mini md-btn-wave-light" href="javascript:FormEdit(1,<?=$AccBookConfigID?>,<?=$AccBookConfigType?>)"><?=$수정[$LangID]?></a>
											<a class="md-btn md-btn-success md-btn-mini md-btn-wave-light" href="javascript:FormEdit(2,<?=$AccBookConfigID?>,<?=$AccBookConfigType?>)" style="background:#919191;"><?=$삭제[$LangID]?></a>
										</td>
									</tr>
                                         <?php
									}
									$Stmt = null;
									if ($ListCount==0) {
									?>
									<tr>
										<td class="uk-text-wrap uk-table-td-center" colspan=5><?=$등록된_자료가_없습니다[$LangID]?></td>
									</tr>
									<?php
							        #----------------------------------------------------------------------------------------------#
									}
							#------------------------------------------------------------------------------------------------------#
							} else {
							#------------------------------------------------------------------------------------------------------#
									while($Row = $Stmt->fetch()) {
							        #----------------------------------------------------------------------------------------------#
										 $ListCount ++;
										 $AccBookConfigID   = $Row["AccBookConfigID"];
										 $AccBookConfigType = $Row["AccBookConfigType"];
										 $AccBookConfigName = $Row["AccBookConfigName"];
										 if ($AccBookConfigType==1) {
										         $AccBookConfigTypeName = "매출";
										 } else {
                                                 $AccBookConfigTypeName = "비용";
										 }
										 $AccBookSubConfigID   = $Row["AccBookSubConfigID"];
										 $AccBookSubConfigName = $Row["AccBookSubConfigName"];
                                         ?> 
									<tr>
										<td style="text-align:center; color:<?=iif($AccBookConfigType==1,"#006CD8","#E80000")?>;"><?=$ListCount?></td>
										<td style="text-align:center; color:<?=iif($AccBookConfigType==1,"#006CD8","#E80000")?>;"><?=$AccBookConfigTypeName?></td>
										<td style="text-align:center; color:<?=iif($AccBookConfigType==1,"#006CD8","#E80000")?>;"><?=$AccBookConfigName?></td>
										<td style="text-align:center; color:<?=iif($AccBookConfigType==1,"#0057ae","#c40000")?>; font-weight:bold;"><?=$AccBookSubConfigName?></td>
										<td style="text-align:center;"> 
											<a class="md-btn md-btn-success md-btn-mini md-btn-wave-light" href="javascript:SubFormEdit(31,<?=$AccBookConfigID?>,<?=$AccBookSubConfigID?>,<?=$AccBookConfigType?>)"><?=$수정[$LangID]?></a>
											<a class="md-btn md-btn-success md-btn-mini md-btn-wave-light" href="javascript:SubFormEdit(32,<?=$AccBookConfigID?>,<?=$AccBookSubConfigID?>,<?=$AccBookConfigType?>)" style="background:#919191;"><?=$삭제[$LangID]?></a>
										</td>
									</tr>
                                         <?php
									}
									$Stmt = null;
									if ($ListCount==0) {
									?>
									<tr>
										<td class="uk-text-wrap uk-table-td-center" colspan=5><?=$등록된_자료가_없습니다[$LangID]?></td>
									</tr>
									<?php
							        #----------------------------------------------------------------------------------------------#
									}
							#------------------------------------------------------------------------------------------------------#
							} 
							#------------------------------------------------------------------------------------------------------#
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

function FormSubmit(s){

	obj = document.RegForm.account_name;
	if (obj.value==""){
		UIkit.modal.alert("<?=$과목명을_입력해_주세요[$LangID]?>");
		obj.focus();
		return;
	}
    var modal_msg = jviif(s > 0,"<?=$수정_하시겠습니까[$LangID]?>?","<?=$저장_하시겠습니까[$LangID]?>?")

	obj2 = document.RegForm.AccBookConfigSubType;
	obj3 = document.RegForm.account_type;

	if (obj2.value=="1" || obj2.value== "2"){
		obj3.value = "1";
	} else {
		obj3.value = "2";
	}

	UIkit.modal.confirm(
		modal_msg, 
		function(){ 
			document.RegForm.action = "account_book_config_action.php?edit_mode=" + s;
			document.RegForm.submit();
		}
	);

}

function CancleSubmit() {
     
	 document.RegForm.account_name.value  = "";
     document.RegForm.account_id.value    = "";
     document.RegForm.account_subid.value = "";
	 document.RegForm.edit_mode.value     = "";
	 document.RegForm.acc_type.value      = "";
     document.RegForm.submit();

}

function FormEdit(s,edit_id, acc_type) {

    document.RegForm.account_id.value = edit_id;
	document.RegForm.edit_mode.value  = s;
	document.RegForm.acc_type.value   = acc_type;

    if (s == 1 || s == 3) {
	      
          document.RegForm.submit();

	} else {
	     
		  var edit_msg = "";
		  UIkit.modal.confirm(
				'<?=$삭제_하시겠습니까[$LangID]?>?', 
				function(){ 
					document.RegForm.action = "account_book_config_action.php";
					document.RegForm.submit();
				}
		 );

	}


}


function SubFormSubmit(s){

	obj = document.RegForm.account_subname;
	if (obj.value==""){
		UIkit.modal.alert("<?=$세목명을_입력해_주세요[$LangID]?>");
		obj.focus();
		return;
	}
    var modal_msg = jviif(s==31,"<?=$세목을_수정_하시겠습니까[$LangID]?>?","<?=$세목을_저장_하시겠습니까[$LangID]?>?");

	document.RegForm.edit_mode.value = s;

	UIkit.modal.confirm(
		modal_msg, 
		function(){ 
			document.RegForm.action = "account_book_config_action.php";
			document.RegForm.submit();
		}
	);

}
function SubCancleSubmit() {

     document.RegForm.account_id.value = document.RegForm.Oldaccount_id.value;
	 document.RegForm.acc_type.value   = document.RegForm.Oldacc_type.value;

	 document.RegForm.account_subname.value = "";
     document.RegForm.account_subid.value   = "";
	 document.RegForm.edit_mode.value       = 3;
     document.RegForm.submit();

}

function SubFormEdit(s,edit_id, edit_subid, acc_type) {

    document.RegForm.account_id.value    = edit_id;
    document.RegForm.account_subid.value = edit_subid;
	document.RegForm.edit_mode.value     = s;
	document.RegForm.acc_type.value      = acc_type;
    if (s == 31) {
	      
		  document.RegForm.edit_mode.value = 3;
          document.RegForm.submit();

	} else {
	     
		  var edit_msg = "";
		  UIkit.modal.confirm(
				'<?=$세목을_삭제_하시겠습니까[$LangID]?>?', 
				function(){ 
					document.RegForm.action = "account_book_config_action.php";
					document.RegForm.submit();
				}
		 );

	}


}

</script>




<?php
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>