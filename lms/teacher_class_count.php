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
$MainMenuID = 17;
$SubMenuID = 1706;
include_once('./inc_top.php'); 
include_once('./inc_menu_left.php');
#===== 모바일 결제창에서 결제하지 않고 다시 돌아올경우 셀프페이에 남겨진 고유코드를 다시 재사용하기위한 변수 입니다. =====#
$ReqUrl = isset($_REQUEST["ReqUrl"]) ? $_REQUEST["ReqUrl"] : ""; //' 결제창에서 결제실행전 돌아올때
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


$SearchYear = isset($_REQUEST["SearchYear"]) ? $_REQUEST["SearchYear"] : "";
$SearchMonth = isset($_REQUEST["SearchMonth"]) ? $_REQUEST["SearchMonth"] : "";

if ($SearchYear==""){
	$SearchYear = date("Y");
}
if ($SearchMonth==""){
	$SearchMonth = date("m");
}



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

$Sql = "select 
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



$Sql = "
		select 
			A.* ,
			AES_DECRYPT(UNHEX(A.TeacherPhone1),:EncryptionKey) as DecTeacherPhone1,
			AES_DECRYPT(UNHEX(A.TeacherPhone2),:EncryptionKey) as DecTeacherPhone2,
			B.TeacherGroupName,
			C.EduCenterName,
			G.MemberLoginID,
			G.MemberID,
			Z.MemberTimeZoneName,
			I.TeacherPayTypeItemTitle,
			D.FranchiseName 
		from Teachers A 
			inner join TeacherGroups B on A.TeacherGroupID=B.TeacherGroupID 
			inner join EduCenters C on B.EduCenterID=C.EduCenterID 
			inner join Franchises D on C.FranchiseID=D.FranchiseID 
			inner join Members G on A.TeacherID=G.TeacherID and G.MemberLevelID=15 
			left outer join MemberTimeZones Z on G.MemberTimeZoneID=Z.MemberTimeZoneID 
			left outer join TeacherPayTypeItems I on A.TeacherPayTypeItemID=I.TeacherPayTypeItemID
		where ".$AddSqlWhere." 
		order by A.TeacherOrder desc ";//limit $StartRowNum, $PageListNum";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$수업현황[$LangID]?></h3>

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
						<select id="SearchEduCenterID" name="SearchEduCenterID" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$교육센터선택[$LangID]?>" style="width:100%;"/>
							<option value=""></option>
							<?
							$AddWhere2 = "";
							if ($SearchFranchiseID!=""){
								$AddWhere2 = "and A.FranchiseID=".$SearchFranchiseID." ";
							}else{
								$AddWhere2 = " ";
							}
							$Sql2 = "select 
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


					<div class="uk-width-medium-2-10" style="padding-top:7px;display:<?if ($HideSearchBranchGroupID==1){?>none<?}?>;">
						<select id="SearchYear" name="SearchYear" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="<?=$년도선택[$LangID]?>" style="width:100%;"/>
							<option value=""><?=$년도선택[$LangID]?></option>
							<?
							for ($iiii=2018;$iiii<=date("Y")+1;$iiii++) {
							?>
							<option value="<?=$iiii?>" <?if ($SearchYear==$iiii){?>selected<?}?>><?=$iiii?> 년</option>
							<?
							}
							?>
						</select>
					</div>
					<div class="uk-width-medium-2-10" style="padding-top:7px;display:<?if ($HideSearchBranchGroupID==1){?>none<?}?>;">
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
							
							※ 데이터를 불러오는데 오랜 시간이 걸립니다. [설명 : 수업수 (10분단위 슬랏수) ]
							<table class="uk-table uk-table-align-vertical">
								<thead>
									<tr>
										<!--<th nowrap><input name="CheckBoxAll" type="checkbox" onclick="CheckListAll(this)"></th>-->
										<th nowrap>No</th>
										<th nowrap>
										Teacher Name(<?=$아이디[$LangID]?>) 
										</th>
										<?
										$MonthEndDay = date('t', strtotime($SearchYear."-".substr("0".$SearchMonth,-2)."-01"));
										for ($ii=1;$ii<=$MonthEndDay;$ii++){
										?>
										<th nowrap><?=$ii?><?=$일일[$LangID]?></th>
										<?
										}
										?>

										<!--
										<th nowrap><?=$출석율[$LangID]?></th>
										-->

									</tr>
								</thead>
								<tbody>
									
								<?php

								$ListCount = 1;
								while($Row = $Stmt->fetch()) {
									$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

									$TeacherID = $Row["TeacherID"];
									$TeacherName = $Row["TeacherName"];
									$MemberLoginID = $Row["MemberLoginID"];

										
								?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ListNumber?></td>
										<td class="uk-text-nowrap uk-table-td-center">
											<?=$TeacherName?>(<?=$MemberLoginID?>) 
										</td>
										<?
										$MonthEndDay = date('t', strtotime($SearchYear."-".substr("0".$SearchMonth,-2)."-01"));
										$ClassTotalCount = 0;
										$ClassAttendCount = 0;
										for ($ii=1;$ii<=$MonthEndDay;$ii++){
										?>
										<td nowrap class="uk-text-nowrap uk-table-td-center" id="Td_<?=$ListCount?>_<?=$ii?>" onclick="SelectTd(<?=$ListCount?>,<?=$ii?>)" style="">
										<?
											$SelectDate = $SearchYear."-".substr("0".$SearchMonth,-2)."-".$ii;	
											$SelectWeek = date('w', strtotime($SelectDate));
											

											
											$ViewTable = "

												select 
													
													ClassOrderTimeTypeID

												from ClassOrderSlots COS 

														left outer join Classes CLS on COS.ClassOrderID=CLS.ClassOrderID and CLS.StartYear=".$SearchYear." and CLS.StartMonth=".$SearchMonth." and CLS.StartDay=".$ii." and CLS.StartHour=COS.StudyTimeHour and CLS.StartMinute=COS.StudyTimeMinute and CLS.ClassAttendState<>99 

														inner join ClassOrders CO on COS.ClassOrderID=CO.ClassOrderID 




														inner join Members MB on CO.MemberID=MB.MemberID 
														inner join Centers CT on MB.CenterID=CT.CenterID 
														inner join Branches BR on CT.BranchID=BR.BranchID 
														inner join BranchGroups BRG on BR.BranchGroupID=BRG.BranchGroupID 
														inner join Companies COM on BRG.CompanyID=COM.CompanyID 
														inner join Franchises FR on COM.FranchiseID=FR.FranchiseID 
														inner join Teachers TEA on COS.TeacherID=TEA.TeacherID 
														left outer join Teachers TEA2 on CLS.TeacherID=TEA2.TeacherID 
														inner join Members MB2 on TEA.TeacherID=MB2.TeacherID 
														left outer join Members MB3 on CT.CenterID=MB3.CenterID and MB3.MemberLevelID=12 

												where TEA.TeacherState=1 
														and COS.TeacherID=".$TeacherID." 
														and COS.ClassOrderSlotMaster=1 
														and ( 
																(COS.ClassOrderSlotType=1 and COS.StudyTimeWeek=".$SelectWeek." and COS.ClassOrderSlotStartDate is NULL and COS.ClassOrderSlotEndDate is NULL and datediff(CO.ClassOrderStartDate, '".$SelectDate."')<=0 ) 

																or 
																(COS.ClassOrderSlotType=1 and COS.StudyTimeWeek=".$SelectWeek." and datediff(COS.ClassOrderSlotStartDate, '".$SelectDate."')<=0 and COS.ClassOrderSlotEndDate is NULL and datediff(CO.ClassOrderStartDate, '".$SelectDate."')<=0 ) 

																or 
																(COS.ClassOrderSlotType=1 and COS.StudyTimeWeek=".$SelectWeek." and COS.ClassOrderSlotStartDate is NULL and datediff(COS.ClassOrderSlotEndDate, '".$SelectDate."')>=0 and datediff(CO.ClassOrderStartDate, '".$SelectDate."')<=0 ) 

																or 
																(COS.ClassOrderSlotType=1 and COS.StudyTimeWeek=".$SelectWeek." and datediff(COS.ClassOrderSlotStartDate, '".$SelectDate."')<=0 and datediff(COS.ClassOrderSlotEndDate, '".$SelectDate."')>=0 and datediff(CO.ClassOrderStartDate, '".$SelectDate."')<=0 ) 

																or 
																(COS.ClassOrderSlotType=2 and datediff(COS.ClassOrderSlotDate, '".$SelectDate."')=0 )   
															)  
														and COS.ClassOrderSlotState=1 
														and CO.ClassProgress=11 
														and (CO.ClassOrderState=1 or CO.ClassOrderState=2 or CO.ClassOrderState=4 or CO.ClassOrderState=5 or CO.ClassOrderState=6)

														and (
																(CT.CenterPayType=1 and MB.MemberPayType=0 and ((CO.ClassOrderState=1 or CO.ClassOrderState=2 or CO.ClassOrderState=5 or CO.ClassOrderState=6) or (CO.ClassOrderState=3 and datediff(CO.ClassOrderEndDate, '".$SelectDate."')>=0) )) 
																or 
																( 
																	( CT.CenterPayType=2 and datediff(CO.ClassOrderEndDate, '".$SelectDate."')>=0 ) 
																	or 
																	( CT.CenterPayType=1 and MB.MemberPayType=1 and datediff(CO.ClassOrderEndDate, '".$SelectDate."')>=0 ) 
																)
																or
																CO.ClassProductID=2 
																or 
																CO.ClassProductID=3 
																or 
																(COS.ClassOrderSlotType=2 and datediff(COS.ClassOrderSlotDate, '".$SelectDate."')=0) 
															)
											
													GROUP BY COS.StudyTimeHour, COS.StudyTimeMinute
											";

											
											$Sql2 = "select 
															sum(ClassOrderTimeTypeID/ClassOrderTimeTypeID) as ClassCount,
															sum(ClassOrderTimeTypeID) as MinuteCount 
													from ($ViewTable) V 
											";

//                                            echo $ViewTable;
//                                            echo "<br>";
//                                            echo $Sql2;
//                                            echo "<br>";

//                                            echo '<script>';
//                                            echo 'console.log("'.$Sql2.'")';
//                                            echo '</script>';
//                                            var_dump($Sql2);

                                            # query copy to $debug_sql
                                            $debug_sql = $Sql2;

											$Stmt2 = $DbConn->prepare($Sql2);
											$Stmt2->execute();
											$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
											$Row2 = $Stmt2->fetch();
											$Stmt2 = null;
											$MinuteCount = $Row2["MinuteCount"];
											$ClassCount = round($Row2["ClassCount"],0);

											if ($ClassCount==0){
										
											}else{
										?>
											<?=$ClassCount?> (<?=$MinuteCount?>)
										<?
											}
										?>
										</td>
										<?
										}
										?>

										<!--
										<td nowrap class="uk-text-nowrap uk-table-td-center" style="font-size:11px;">
											<?if ($ClassTotalCount>0) {?>
												<?=round(($ClassAttendCount/$ClassTotalCount)*100,0)?> %
											<?}else{?>
												0 %
											<?}?>
										</td>
										-->
									</tr>
								<?php
									$ListCount++;
								}
							

								$Stmt = null;
								?>

								</tbody>
							</table>
						</div>

                        <? echo $debug_sql; ?>

						<!--
						<div class="uk-form-row" style="text-align:left;margin-top:20px;">
                            <a type="button" href="javascript:SendMessageForm()" class="md-btn md-btn-primary"><?=$메시지전송[$LangID]?></a>
                        </div>
						-->

					

						<?php			
						//include_once('./inc_pagination.php');
						?>

						<!--
						<div class="uk-form-row" style="text-align:center;">
							<a type="button" href="student_form.php?ListParam=<?=$ListParam?>" class="md-btn md-btn-primary"><?=$신규등록[$LangID]?></a>
						</div>
						-->

					</div>
				</div>
			</div>
		</div>

	</div>
</div>

<style>
.TdActive{
	background-color:#f1f1f1;
}
</style>

<script>
var SelectedTd = "|";
function SelectTd(ListCount,Day){

	alert(SelectedTd.indexOf("Td_"+ListCount+"_"+Day));
	if (SelectedTd.indexOf("Td_"+ListCount+"_"+Day) != -1) {
		ClassName = "";
		SelectedTd = SelectedTd.replace("Td_"+ListCount+"_"+Day, "");
	}else{
		ClassName = "TdActive";
		SelectedTd = SelectedTd + "Td_"+ListCount+"_"+Day + "|";
	}

	for (ii=1;ii<=<?=$MonthEndDay?>;ii++){
		document.getElementById("Td_"+ListCount+"_"+ii).className=ClassName;
	}

	for (ii=1;ii<=<?=$ListCount-1?>;ii++){
		document.getElementById("Td_"+ii+"_"+Day).className=ClassName;
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
	document.SearchForm.action = "teacher_class_count.php";
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