<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

if ($_LINK_ADMIN_LEVEL_ID_>13){
	header("Location: teacher_form.php?TeacherID=".$_LINK_ADMIN_TEACHER_ID_); 
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
$MainMenuID = 13;
$SubMenuID = 1305;
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
$SearchFranchiseID = isset($_REQUEST["SearchFranchiseID"]) ? $_REQUEST["SearchFranchiseID"] : "";
$SearchEduCenterID = isset($_REQUEST["SearchEduCenterID"]) ? $_REQUEST["SearchEduCenterID"] : "";
$SearchTeacherGroupID = isset($_REQUEST["SearchTeacherGroupID"]) ? $_REQUEST["SearchTeacherGroupID"] : "";


//================== 서치폼 감추기 =================
$HideSearchFranchiseID = 0;

if ($_LINK_ADMIN_LEVEL_ID_==0 or $_LINK_ADMIN_LEVEL_ID_==1){//마스터
	//모두허용
}else if ($_LINK_ADMIN_LEVEL_ID_==3 or $_LINK_ADMIN_LEVEL_ID_==4){//프랜차이즈 관리자
	$SearchFranchiseID = $_LINK_ADMIN_FRANCHISE_ID_;
	
	$HideSearchFranchiseID = 1;
}else if ($_LINK_ADMIN_LEVEL_ID_==6 or $_LINK_ADMIN_LEVEL_ID_==7){//대표지사 관리자
	//접속불가
}else if ($_LINK_ADMIN_LEVEL_ID_==9 or $_LINK_ADMIN_LEVEL_ID_==10){//지사 관리자
	//접속불가
}else if ($_LINK_ADMIN_LEVEL_ID_==12 or $_LINK_ADMIN_LEVEL_ID_==13){//대리점 관리자
	//접속불가
}else if ($_LINK_ADMIN_LEVEL_ID_==15){//강사 
	//폼으로 넘김
}
//================== 서치폼 감추기 =================


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
$AddSqlWhere = $AddSqlWhere . " and A.TeacherState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and B.TeacherGroupState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and C.EduCenterState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and D.FranchiseState<>0 ";

if ($SearchText!=""){
	$ListParam = $ListParam . "&SearchText=" . $SearchText;
	$AddSqlWhere = $AddSqlWhere . " and (A.TeacherName like '%".$SearchText."%' or A.TeacherNickName like '%".$SearchText."%' or G.MemberLoginID like '".$SearchText."') ";
}

if ($SearchFranchiseID!=""){
	$ListParam = $ListParam . "&SearchFranchiseID=" . $SearchFranchiseID;
	$AddSqlWhere = $AddSqlWhere . " and C.FranchiseID=$SearchFranchiseID ";
}

if ($SearchEduCenterID!=""){
	$ListParam = $ListParam . "&SearchEduCenterID=" . $SearchEduCenterID;
	$AddSqlWhere = $AddSqlWhere . " and B.EduCenterID=$SearchEduCenterID ";
}

if ($SearchTeacherGroupID!=""){
	$ListParam = $ListParam . "&SearchTeacherGroupID=" . $SearchTeacherGroupID;
	$AddSqlWhere = $AddSqlWhere . " and A.TeacherGroupID=$SearchTeacherGroupID ";
}


$PaginationParam = $ListParam;
if ($CurrentPage!=""){
	$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}

$ListParam = str_replace("&", "^^", $ListParam);

$Sql = "SELECT  
				count(*) TotalRowCount 
		from Teachers A 
			inner join TeacherGroups B on A.TeacherGroupID=B.TeacherGroupID 
			inner join EduCenters C on B.EduCenterID=C.EduCenterID 
			inner join Franchises D on C.FranchiseID=D.FranchiseID 
			inner join Members G on A.TeacherID=G.TeacherID and G.MemberLevelID=15 
			left outer join MemberTimeZones Z on G.MemberTimeZoneID=Z.MemberTimeZoneID 
			left outer join TeacherPayTypeItems I on A.TeacherPayTypeItemID=I.TeacherPayTypeItemID
		where ".$AddSqlWhere." ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TotalRowCount = $Row["TotalRowCount"];

$TotalPageCount = ceil($TotalRowCount / $PageListNum);
$StartRowNum = $PageListNum * ($CurrentPage - 1 );



$Sql = "SELECT 
			A.* ,
			AES_DECRYPT(UNHEX(A.TeacherPhone1),:EncryptionKey) as DecTeacherPhone1,
			AES_DECRYPT(UNHEX(A.TeacherPhone2),:EncryptionKey) as DecTeacherPhone2,
			B.TeacherGroupName,
			C.EduCenterName,
			G.MemberLoginID,
			G.MemberID,
			G.StaffID,
			Z.MemberTimeZoneName,
			I.TeacherPayTypeItemTitle,
			D.FranchiseName,
			D.FranchiseID 
		from Teachers A 
			inner join TeacherGroups B on A.TeacherGroupID=B.TeacherGroupID 
			inner join EduCenters C on B.EduCenterID=C.EduCenterID 
			inner join Franchises D on C.FranchiseID=D.FranchiseID 
			inner join Members G on A.TeacherID=G.TeacherID and G.MemberLevelID=15 
			left outer join MemberTimeZones Z on G.MemberTimeZoneID=Z.MemberTimeZoneID 
			left outer join TeacherPayTypeItems I on A.TeacherPayTypeItemID=I.TeacherPayTypeItemID
		where ".$AddSqlWhere." 
		order by A.TeacherOrder desc limit $StartRowNum, $PageListNum";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);



?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom">강사 리뷰</h3>

		<form name="SearchForm" method="get">
		<div class="md-card" style="margin-bottom:10px;">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin="">
					
					<div class="uk-width-medium-2-10" style="padding-top:7px;display:<?if ($HideSearchFranchiseID==1){?>none<?}?>;">
						<select id="SearchFranchiseID" name="SearchFranchiseID" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$프랜차이즈선택[$LangID]?>" style="width:100%;"/>
							<option value=""></option>
							<?
							$Sql2 = "select 
											A.* 
									from Franchises A 
									where A.FranchiseState<>0 
									order by A.FranchiseState asc, A.FranchiseName asc";
							$Stmt2 = $DbConn->prepare($Sql2);
							$Stmt2->execute();
							$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
							
							$OldSelectFranchiseState = -1;
							while($Row2 = $Stmt2->fetch()) {
								$SelectFranchiseID = $Row2["FranchiseID"];
								$SelectFranchiseName = $Row2["FranchiseName"];
								$SelectFranchiseState = $Row2["FranchiseState"];
							
								if ($OldSelectFranchiseState!=$SelectFranchiseState){
									if ($OldSelectFranchiseState!=-1){
										echo "</optgroup>";
									}
									
									if ($SelectFranchiseState==1){
										echo "<optgroup label=\"프랜차이즈(운영중)\">";
									}else if ($SelectFranchiseState==2){
										echo "<optgroup label=\"프랜차이즈(미운영)\">";
									}
								} 
								$OldSelectFranchiseState = $SelectFranchiseState;
							?>

							<option value="<?=$SelectFranchiseID?>" <?if ($SearchFranchiseID==$SelectFranchiseID){?>selected<?}?>><?=$SelectFranchiseName?></option>
							<?
							}
							$Stmt2 = null;
							?>
						</select>
					</div>

					<div class="uk-width-medium-2-10" style="padding-top:7px;display:">
						<select id="SearchEduCenterID" name="SearchEduCenterID" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$교육센터선택[$LangID]?>" style="width:100%;" />
							<option value=""></option>
							<?
							$AddWhere2 = "";
							if ($SearchFranchiseID!=""){
								$AddWhere2 = "and A.FranchiseID=".$SearchFranchiseID." ";
							}else{
								$AddWhere2 = " ";
							}
							$Sql2 = "SELECT  
											A.* 
									from EduCenters A 
										inner join Franchises B on A.FranchiseID=B.FranchiseID 
									where A.EduCenterState<>0 and B.FranchiseState<>0 ".$AddWhere2." 
									order by A.EduCenterState asc, A.EduCenterName asc";
							$Stmt2 = $DbConn->prepare($Sql2);
							$Stmt2->execute();
							$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
							
							$OldSelectEduCenterState = -1;
							while($Row2 = $Stmt2->fetch()) {
								$SelectEduCenterID = $Row2["EduCenterID"];
								$SelectEduCenterName = $Row2["EduCenterName"];
								$SelectEduCenterState = $Row2["EduCenterState"];
							
								if ($OldSelectEduCenterState!=$SelectEduCenterState){
									if ($OldSelectEduCenterState!=-1){
										echo "</optgroup>";
									}
									
									if ($SelectEduCenterState==1){
										echo "<optgroup label=\"교육센터(운영중)\">";
									}else if ($SelectEduCenterState==2){
										echo "<optgroup label=\"교육센터(미운영)\">";
									}
								}
								$OldSelectEduCenterState = $SelectEduCenterState;
							?>

							<option value="<?=$SelectEduCenterID?>" <?if ($SearchEduCenterID==$SelectEduCenterID){?>selected<?}?>><?=$SelectEduCenterName?></option>
							<?
							}
							$Stmt2 = null;
							?>
						</select>
					</div>
					<div class="uk-width-medium-2-10" style="padding-top:7px;">
						<select id="SearchTeacherGroupID" name="SearchTeacherGroupID" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$강사그룹선택[$LangID]?>" style="width:100%;"/>
							<option value=""></option>
							<?
							$AddWhere2 = "";
							if ($SearchEduCenterID!=""){
								$AddWhere2 = "and A.EduCenterID=".$SearchEduCenterID." ";
							}else{
								if ($SearchFranchiseID!=""){
									$AddWhere2 = "and B.FranchiseID=".$SearchFranchiseID." ";
								}else{
									$AddWhere2 = " ";
								}
							}
							$Sql2 = "select 
											A.* 
									from TeacherGroups A 
										inner join EduCenters B on A.EduCenterID=B.EduCenterID 
										inner join Franchises C on B.FranchiseID=C.FranchiseID 
									where A.TeacherGroupState<>0 and B.EduCenterState<>0 and C.FranchiseState<>0 ".$AddWhere2." 
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
								<option value="100" <?if ($SearchState=="100"){?>selected<?}?>>전체</option>
								<option value="1" <?if ($SearchState=="1"){?>selected<?}?>><?=$활동중[$LangID]?></option>
								<option value="2" <?if ($SearchState=="2"){?>selected<?}?>><?=$미활동[$LangID]?></option>
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
										<th nowrap><input name="CheckBoxAll" type="checkbox" onclick="CheckListAll(this)"></th>
										<th nowrap>No</th>
										<th nowrap><?=$강사명[$LangID]?></th>
										<th nowrap><?=$아이디[$LangID]?></th>
										<th nowrap><?=$닉네임[$LangID]?></th>
										<th nowrap><?=$전화번호[$LangID]?></th>
										<th nowrap><?=$휴대폰[$LangID]?></th>
										<th nowrap>80분연강</th>
										<th nowrap><?=$활동지역[$LangID]?></th>
										<th nowrap><?=$출신지역[$LangID]?></th>
										<th nowrap><?=$강사수수료_분[$LangID]?></th>
										<th nowrap><?=$교육센터명[$LangID]?></th>
										<th nowrap><?=$강사그룹명[$LangID]?></th>
										<th nowrap><?=$프랜차이즈[$LangID]?></th>
										<th nowrap><?=$상태[$LangID]?></th>
										<th nowrap>리뷰</th>
									</tr>
								</thead>
								<tbody>
									
									<?php
									$ListCount = 1;
									while($Row = $Stmt->fetch()) {
										$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

										$MemberID = $Row["MemberID"];
										$StaffID = $Row["StaffID"];
										$TeacherID = $Row["TeacherID"];
										$TeacherName = $Row["TeacherName"];
										$TeacherNickName = $Row["TeacherNickName"];
										$TeacherPhone1 = $Row["DecTeacherPhone1"];
										$TeacherPhone2 = $Row["DecTeacherPhone2"];
										$TeacherState = $Row["TeacherState"];
										$TeacherGroupName = $Row["TeacherGroupName"];
										$EduCenterName = $Row["EduCenterName"];
										$MemberLoginID = $Row["MemberLoginID"];
										$FranchiseID = $Row["FranchiseID"];
										$FranchiseName = $Row["FranchiseName"];
										$TeacherPayPerTime = $Row["TeacherPayPerTime"];
										$MemberTimeZoneName = $Row["MemberTimeZoneName"];
										$TeacherPayTypeItemTitle = $Row["TeacherPayTypeItemTitle"];
										$TeacherBlock80Min = $Row["TeacherBlock80Min"];
										
										if ($TeacherState==1){
											$StrTeacherState = "<span class=\"ListState_1\">활동중</span>";
										}else if ($TeacherState==2){
											$StrTeacherState = "<span class=\"ListState_2\">미활동</span>";
										}
										
										if ($TeacherBlock80Min==0){
											$StrTeacherBlock80Min = "<span class=\"ListState_1\">허용</span>";
										}else if ($TeacherBlock80Min==1){
											$StrTeacherBlock80Min = "<span class=\"ListState_2\">제한</span>";
										}


										
									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><input name="CheckBox_<?=$ListCount?>" id="CheckBox_<?=$ListCount?>" type="checkbox" value="<?=$MemberID?>"></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ListNumber?></td>
										<td class="uk-text-nowrap uk-table-td-center"><a href="teacher_form.php?ListParam=<?=$ListParam?>&TeacherID=<?=$TeacherID?>"><?=$TeacherName?></a> <a href="javascript:OpenTeacherDataForm(<?=$MemberID?>);"><i class="material-icons">unarchive</i></a></td>
										<td class="uk-text-nowrap uk-table-td-center"><a href="teacher_form.php?ListParam=<?=$ListParam?>&TeacherID=<?=$TeacherID?>"><?=$MemberLoginID?></a></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$TeacherNickName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$TeacherPhone1?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$TeacherPhone2?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrTeacherBlock80Min?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$MemberTimeZoneName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$TeacherPayTypeItemTitle?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=number_format($TeacherPayPerTime,0)?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$EduCenterName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$TeacherGroupName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$FranchiseName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrTeacherState?></td>
										<td class="uk-text-nowrap uk-table-td-center">
											<a type="button" href="javascript:OpenReview(<?=$TeacherID?>)" class="md-btn md-btn-primary">리뷰보기</a>
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
						include_once('./inc_pagination.php');
						?>

						<div class="uk-form-row" style="text-align:center;">
							<a type="button" href="teacher_form.php?ListParam=<?=$ListParam?>" class="md-btn md-btn-primary"><?=$신규등록[$LangID]?></a>
						</div>

					</div>
				</div>
			</div>
		</div>

	</div>
</div>


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
		alert("선택한 목록이 없습니다.");
	}else{
		
		MemberIDs = "|";
		for (ii=1;ii<=ListCount;ii++){
			if (document.getElementById("CheckBox_"+ii).checked){
				MemberIDs = MemberIDs + document.getElementById("CheckBox_"+ii).value + "|";
			}	
		}

	
		if (MemberIDs=="|"){
			alert("선택한 목록이 없습니다.");
		}else{

			openurl = "send_message_log_multi_form.php?MemberIDs="+MemberIDs;
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
	document.SearchForm.action = "teacher_list.php";
	document.SearchForm.submit();
}


function OpenTeacherDataForm(ReceiveMemberID){

	openurl = "teacher_data_form.php?ReceiveMemberID="+ReceiveMemberID;
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

function OpenReview(TeacherID) {

	var OpenUrl = "../review_list_view.php?adminView=1&TeacherID="+TeacherID;
	$.colorbox({	
		href:OpenUrl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "850"
		,maxHeight: "536"
		,title:""
		,iframe:true 
		,scrolling:true

		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}

</script>

<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>