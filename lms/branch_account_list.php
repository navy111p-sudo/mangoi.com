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
$SubMenuID = 1124;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
?>


 
<?php

$AddSqlWhere = "1=1";

$SearchText = isset($_REQUEST["SearchText"]) ? $_REQUEST["SearchText"] : "";
$SearchState = isset($_REQUEST["SearchState"]) ? $_REQUEST["SearchState"] : "";



if ($SearchText!=""){
	$AddSqlWhere = $AddSqlWhere . " and B.BranchName like '%".$SearchText."%' ";
}

$AddSqlWhere = $AddSqlWhere . " and A.BranchAccountState=1 ";


$Sql = "
		select 
			A.BranchID,
			sum(A.BranchAccountPrice) as TotalBranchAccountPrice,
			B.BranchName,
			C.MemberID 
		from BranchAccounts A 
			inner join Branches B on A.BranchID=B.BranchID 
			inner join Members C on B.BranchID=C.BranchID and C.MemberLevelID=9 
		where ".$AddSqlWhere." 
		group by A.BranchID 
		order by B.BranchName asc";// limit $StartRowNum, $PageListNum";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$지사[$LangID]?> </h3>

		<form name="SearchForm" method="get">
		<div class="md-card" style="margin-bottom:10px;">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin="">


					<div class="uk-width-medium-2-10">
						<label for="SearchText"><?=$지사명[$LangID]?></label>
						<input type="text" class="md-input" id="SearchText" name="SearchText" value="<?=$SearchText?>">
					</div>


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
										<th nowrap><input name="CheckBoxAll" type="checkbox" onclick="CheckListAll(this)"></th>
										<th nowrap>No</th>
										<th nowrap><?=$지사명[$LangID]?></th>
										<th nowrap><?=$미수금[$LangID]?></th>
										<th nowrap><?=$상세목록[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
									
									<?php
									$ListCount = 1;
									while($Row = $Stmt->fetch()) {
										//$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

										$BranchID = $Row["BranchID"];
										$TotalBranchAccountPrice = $Row["TotalBranchAccountPrice"];
										$BranchName = $Row["BranchName"];
										$MemberID = $Row["MemberID"];
									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><input name="CheckBox_<?=$ListCount?>" id="CheckBox_<?=$ListCount?>" type="checkbox" value="<?=$MemberID?>"></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ListCount?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$BranchName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($TotalBranchAccountPrice,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center">
											<a class="md-btn md-btn-success md-btn-mini md-btn-wave-light" href="branch_account_detail_list.php?BranchID=<?=$BranchID?>"><?=$상세목록[$LangID]?></a>
										</td>
									</tr>
									<?php
										$ListCount ++;
									}
									$Stmt = null;
									?>


								</tbody>
							</table>
						</div>
						

						<div class="uk-form-row" style="text-align:left;margin-top:20px;">
                            <a type="button" href="javascript:SendMessageForm()" class="md-btn md-btn-primary"><?=$메시지전송[$LangID]?></a>
                        </div>

						<?php			
						//include_once('./inc_pagination.php');
						?>

						<div class="uk-form-row" style="text-align:center;margin-top:30px;">
							<a type="button" href="javascript:OpenBranchAccountForm('')" class="md-btn md-btn-primary"><?=$신규등록[$LangID]?></a>
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
var ListCount = <?=$ListCount-1?>;
function CheckListAll(obj){

	for (ii=1;ii<=ListCount;ii++){
		if (obj.checked){
			document.getElementById("CheckBox_"+ii).checked = true;
		}else{
			document.getElementById("CheckBox_"+ii).checked = false;
		}	
	}
}

function SendMessageForm(){

	if (ListCount==0){
		alert("<?=$선택한_목록이_없습니다[$LangID]?>");
	}else{
		
		MemberIDs = "|";
		for (ii=1;ii<=ListCount;ii++){
			if (document.getElementById("CheckBox_"+ii).checked){
				MemberIDs = MemberIDs + document.getElementById("CheckBox_"+ii).value + "|";
			}	
		}

	
		if (MemberIDs=="|"){
			alert("<?=$선택한_목록이_없습니다[$LangID]?>");
		}else{

			openurl = "send_message_log_branch_accoount_form.php?MemberIDs="+MemberIDs;
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
	}
}
</script>



<script>
function OpenBranchAccountForm(BranchAccountID){
	openurl = "branch_account_form.php?BranchAccountID="+BranchAccountID;
	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "850"
		,maxHeight: "750"
		,title:""
		,iframe:true 
		,scrolling:true
		,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}


function SearchSubmit(){
	document.SearchForm.action = "branch_account_list.php";
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