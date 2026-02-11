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

<body class="disable_transitions sidebar_main_open sidebar_main_swipe">
<?php
$type = isset($_REQUEST["type"]) ? $_REQUEST["type"] : "";
if ($type==""){
	$type = "0";
}
$SearchState = $type;

$MainMenuID = 0;
$SubMenuID = 0;

include_once('./inc_top.php');
include_once('./inc_menu_left.php');
?>



<?php
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$ClassOrderID = isset($_REQUEST["ClassOrderID"]) ? $_REQUEST["ClassOrderID"] : "";
$SearchTeacherGroupID = isset($_REQUEST["SearchTeacherGroupID"]) ? $_REQUEST["SearchTeacherGroupID"] : "";

$Sql = "
		select 
				A.*,
				B.MemberName,
				C.TeacherPayTypeItemTitle,
				D.ClassOrderTimeTypeName
		from ClassOrders A 
			inner join Members B on A.MemberID=B.MemberID 
			inner join TeacherPayTypeItems C on A.TeacherPayTypeItemID=C.TeacherPayTypeItemID 
			inner join ClassOrderTimeTypes D on A.ClassOrderTimeTypeID=D.ClassOrderTimeTypeID 
		where A.ClassOrderID=:ClassOrderID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassOrderID', $ClassOrderID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$ClassProductID = $Row["ClassProductID"];
$ClassOrderID = $Row["ClassOrderID"];
$ClassOrderType = $Row["ClassOrderType"];
$ClassOrderMakeType = $Row["ClassOrderMakeType"];
$ClassOrderTimeSlotCount = $Row["ClassOrderTimeSlotCount"];
$TeacherPayTypeItemCenterPriceX = $Row["TeacherPayTypeItemCenterPriceX"];
$CenterPricePerTime = $Row["CenterPricePerTime"];
$ClassOrderWeekCount = $Row["ClassOrderWeekCount"];
$ClassOrderTotalWeekCount = $Row["ClassOrderTotalWeekCount"];
$ClassOrderMonthDiscount = $Row["ClassOrderMonthDiscount"];
$CenterFreeTrialCount = $Row["CenterFreeTrialCount"];
$CenterFreeTrialDiscount = $Row["CenterFreeTrialDiscount"];

$ClassOrderNumber = $Row["ClassOrderNumber"];
$MemberID = $Row["MemberID"];
$ClassOrderWeekCountID = $Row["ClassOrderWeekCountID"];
$ClassOrderTimeTypeID = $Row["ClassOrderTimeTypeID"];
$ClassOrderMonthNumberID = $Row["ClassOrderMonthNumberID"];
$ClassOrderWishStartDate = $Row["ClassOrderWishStartDate"];
$ClassOrderText1 = $Row["ClassOrderText1"];
$ClassOrderText2 = $Row["ClassOrderText2"];

$SellingPrice = $Row["SellingPrice"];
$DiscountPrice = $Row["DiscountPrice"];
$PaymentPrice = $Row["PaymentPrice"];
$UsePointPrice = $Row["UsePointPrice"];
$UseCashPrice = $Row["UseCashPrice"];
$UseCashPaymentType = $Row["UseCashPaymentType"];

$OrderProgress = $Row["OrderProgress"];
$ClassProgress = $Row["ClassProgress"];
$ClassOrderState = $Row["ClassOrderState"];

$TeacherPayTypeItemID = $Row["TeacherPayTypeItemID"];

$MemberName = $Row["MemberName"];
$TeacherPayTypeItemTitle = $Row["TeacherPayTypeItemTitle"];
$ClassOrderTimeTypeName = $Row["ClassOrderTimeTypeName"];

$ClassOrderStartDate = $Row["ClassOrderStartDate"];
if ($ClassOrderStartDate=="") {
	$ClassOrderStartDate = $ClassOrderWishStartDate;
}
?>


<div id="page_content">
	<div id="page_content_inner">


                            

        <h3 class="heading_b uk-margin-bottom"><?=$수강신청_스케줄관리[$LangID]?></h3>




		<!--
		<div class="uk-width-xLarge-10-10  uk-width-large-10-10">
			<div class="md-card">
				<div class="md-card-toolbar">
					<h3 class="md-card-toolbar-heading-text">
						신청정보
					</h3>
				</div>
				<div class="md-card-content large-padding">
					<div class="uk-grid uk-grid-divider uk-grid-medium">
						<div class="uk-width-large-1-2">
							<div class="uk-grid uk-grid-small">
								<div class="uk-width-large-1-3">
									<span class="uk-text-muted uk-text-small">학생명</span>
								</div>
								<div class="uk-width-large-2-3">
									<span class="uk-text-large uk-text-middle"><?=$MemberName?></span>
								</div>
							</div>
							<div class="uk-grid uk-grid-small">
								<div class="uk-width-large-1-3">
									<span class="uk-text-muted uk-text-small">강사타입</span>
								</div>
								<div class="uk-width-large-2-3">
									<span class="uk-text-large uk-text-middle"><?=$TeacherPayTypeItemTitle?></span>
								</div>
							</div>
							<div class="uk-grid uk-grid-small">
								<div class="uk-width-large-1-3">
									<span class="uk-text-muted uk-text-small">수강시간</span>
								</div>
								<div class="uk-width-large-2-3">
									<?=$ClassOrderTimeTypeName?>
								</div>
							</div>
							<div class="uk-grid uk-grid-small">
								<div class="uk-width-large-1-3">
									<span class="uk-text-muted uk-text-small">수강회수/주</span>
								</div>
								<div class="uk-width-large-2-3">
									<?=$ClassOrderWeekCount?>회 (총 <?=$ClassOrderTotalWeekCount?>주)
								</div>
							</div>
							<div class="uk-grid uk-grid-small">
								<div class="uk-width-large-1-3">
									<span class="uk-text-muted uk-text-small">수강시작일</span>
								</div>
								<div class="uk-width-large-2-3 uk-input-group">
									<input type="text" id="ClassOrderStartDate" name="ClassOrderStartDate" value="<?=$ClassOrderStartDate?>" class="md-input label-fixed" data-uk-datepicker="{format:'YYYY-MM-DD', weekstart:0}">
									<span class="uk-input-group-addon"><i class="uk-input-group-icon uk-icon-calendar"></i></span>
								</div>
							</div>
							<hr class="uk-grid-divider uk-hidden-large">
						</div>
						<div class="uk-width-large-1-2">
							<p>
								<span class="uk-text-muted uk-text-small uk-display-block uk-margin-small-bottom">요청사항</span>
								<?=str_replace("\n","<br>",$ClassOrderText1)?>
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
		-->
		

        <form name="SearchForm" method="get">
        <input type="hidden" name="type" value="<?=$SearchState?>">
		<input type="hidden" name="ListParam" value="<?=$ListParam?>">
		<input type="hidden" name="ClassOrderID" value="<?=$ClassOrderID?>">
        <div class="md-card" style="margin-bottom:10px;">
            <div class="md-card-content">
                <div class="uk-grid" data-uk-grid-margin="">

                    <div class="uk-width-medium-2-10" style="padding-top:7px;">
                        <select id="SearchTeacherGroupID" name="SearchTeacherGroupID" class="uk-width-1-1" onchange="SearchSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="강사그룹선택" style="width:100%;"/>
                            <option value=""></option>
                            <?
                            $Sql2 = "select 
                                            A.* 
                                    from TeacherGroups A 
                                    where A.TeacherGroupState<>0 and A.TeacherGroupView=1
                                    order by A.TeacherGroupOrder asc";
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
                            
							<?
							$ArrWeekDayStr = explode(",","일,월,화,수,목,금,토");

							
							
							$EduCenterID = 1;

							//교육센터 정기휴일 검색
							$Sql = "
									select 
											A.*
									from EduCenters A 
									where A.EduCenterID=$EduCenterID";
							$Stmt = $DbConn->prepare($Sql);
							$Stmt->execute();
							$Stmt->setFetchMode(PDO::FETCH_ASSOC);
							$Row = $Stmt->fetch();
							$Stmt = null;


							$EduCenterStartHour = $Row["EduCenterStartHour"];
							$EduCenterEndHour = $Row["EduCenterEndHour"];


							$EduCenterHoliday[0] = $Row["EduCenterHoliday0"];
							$EduCenterHoliday[1] = $Row["EduCenterHoliday1"];
							$EduCenterHoliday[2] = $Row["EduCenterHoliday2"];
							$EduCenterHoliday[3] = $Row["EduCenterHoliday3"];
							$EduCenterHoliday[4] = $Row["EduCenterHoliday4"];
							$EduCenterHoliday[5] = $Row["EduCenterHoliday5"];
							$EduCenterHoliday[6] = $Row["EduCenterHoliday6"];

							$WorkDayCount = 7;
							for ($ii=0;$ii<=6;$ii++){
								if ($EduCenterHoliday[$ii]==1){
									$WorkDayCount--;
								}
							}
							//교육센터 정기휴일 검색
							
							
							//현재 페이지 강사 수 구하기, 강사들의 최소 시작, 최대 종료 시간 구하기
							$AddSqlWhere = "1=1";
							$AddSqlWhere = $AddSqlWhere . " and A.TeacherState=1 ";
							$AddSqlWhere = $AddSqlWhere . " and A.TeacherView=1 ";
							if ($SearchTeacherGroupID!=""){
								$AddSqlWhere = $AddSqlWhere . " and A.TeacherGroupID=$SearchTeacherGroupID ";
							}
                            $Sql2 = "select 
                                            count(*) as RowCount,
											min(TeacherStartHour) as MinTeacherHour,
											max(TeacherEndHour) as MaxTeacherHour
                                    from Teachers A 
										inner join TeacherGroups B on A.TeacherGroupID=B.TeacherGroupID 
                                    where ".$AddSqlWhere." ";
                            $Stmt2 = $DbConn->prepare($Sql2);
                            $Stmt2->execute();
                            $Stmt2->setFetchMode(PDO::FETCH_ASSOC);
							$Row2 = $Stmt2->fetch();
							$TeacherCount = $Row2["RowCount"];
							$MinTeacherHour = $Row2["MinTeacherHour"];
							$MaxTeacherHour = $Row2["MaxTeacherHour"];
                            //현재 페이지 강사 수 구하기, 강사들의 최소 시작, 최대 종료 시간 구하기


							//교육센터 브레이크 타임 검색
							for ($ii=$MinTeacherHour;$ii<=$MaxTeacherHour-1;$ii++){
								for ($jj=0;$jj<=50;$jj=$jj+10){
									for ($kk=0;$kk<=6;$kk++){
										if ($EduCenterHoliday[$kk]==0) {
											$EduCenterBreak[$kk][$ii][$jj] = 1;//[요일/시/분] 1은 수업가능
											
											for ($tt=1;$tt<=$TeacherCount;$tt++){
												$TeacherBreak[$tt][$kk][$ii][$jj] = 1;//[강사순번/요일/시/분] 1은 수업가능
												$TeacherStudySlot[$tt][$kk][$ii][$jj] = 0;//[강사순번/요일/시/분] 1은 수업가능
											}
										}
									}
								}
							}

							$Sql2 = "select 
											A.* 
									from EduCenterBreakTimes A 
									where A.EduCenterID=$EduCenterID and A.EduCenterBreakTimeState=1 
									order by A.EduCenterBreakTimeWeek asc, A.EduCenterBreakTimeHour asc, A.EduCenterBreakTimeMinute asc";
							$Stmt2 = $DbConn->prepare($Sql2);
							$Stmt2->execute();
							$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

							while($Row2 = $Stmt2->fetch()) {
								$EduCenterBreakTimeWeek = $Row2["EduCenterBreakTimeWeek"];
								$EduCenterBreakTimeHour = $Row2["EduCenterBreakTimeHour"];
								$EduCenterBreakTimeMinute = $Row2["EduCenterBreakTimeMinute"];
								$EduCenterBreakTimeType = $Row2["EduCenterBreakTimeType"];
								
								$EduCenterBreak[$EduCenterBreakTimeWeek][$EduCenterBreakTimeHour][$EduCenterBreakTimeMinute] = $EduCenterBreakTimeType;
							}
							$Stmt2 = null;
							//교육센터 브레이크 타임 검색


							//강사목록 검색
							$AddSqlWhere = "1=1";
							$AddSqlWhere = $AddSqlWhere . " and A.TeacherState=1 ";
							$AddSqlWhere = $AddSqlWhere . " and A.TeacherView=1 ";
							if ($SearchTeacherGroupID!=""){
								$AddSqlWhere = $AddSqlWhere . " and A.TeacherGroupID=$SearchTeacherGroupID ";
							}
                            $Sql2 = "select 
                                            A.*
                                    from Teachers A 
										inner join TeacherGroups B on A.TeacherGroupID=B.TeacherGroupID 
                                    where ".$AddSqlWhere."
                                    order by B.TeacherGroupOrder asc, A.TeacherOrder asc";
                            $Stmt2 = $DbConn->prepare($Sql2);
                            $Stmt2->execute();
                            $Stmt2->setFetchMode(PDO::FETCH_ASSOC);
                            

							$ArrTeacherID = [];
							$ArrTeacherName = [];
							$ArrTeacherStartHour = [];
							$ArrTeacherEndHour = [];

							$ListNum=1;
							while($Row2 = $Stmt2->fetch()) {
								$ArrTeacherID[$ListNum] = $Row2["TeacherID"];
								$ArrTeacherName[$ListNum] = $Row2["TeacherName"];
								$ArrTeacherStartHour[$ListNum] = $Row2["TeacherStartHour"];
								$ArrTeacherEndHour[$ListNum] = $Row2["TeacherEndHour"];

								$ListNum++;
							}
                            $Stmt2 = null;
							//강사목록 검색

							//강사 브레이크 타임 검색
							$jjjj=0;
							
							for ($ii=1;$ii<=$ListNum-1;$ii++){
								

								$Sql2 = "select 
												A.* 
										from TeacherBreakTimes A 
										where A.TeacherID=$ArrTeacherID[$ii] and A.TeacherBreakTimeState=1 
										order by A.TeacherBreakTimeWeek asc, A.TeacherBreakTimeHour asc, A.TeacherBreakTimeMinute asc";
								$Stmt2 = $DbConn->prepare($Sql2);
								$Stmt2->execute();
								$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

								while($Row2 = $Stmt2->fetch()) {
									$TeacherBreakTimeWeek = $Row2["TeacherBreakTimeWeek"];
									$TeacherBreakTimeHour = $Row2["TeacherBreakTimeHour"];
									$TeacherBreakTimeMinute = $Row2["TeacherBreakTimeMinute"];
									$TeacherBreakTimeType = $Row2["TeacherBreakTimeType"];
									
									$TeacherBreak[$ii][$TeacherBreakTimeWeek][$TeacherBreakTimeHour][$TeacherBreakTimeMinute] = $TeacherBreakTimeType;//[강사순번/요일/시/분] 1은 수업가능
								}
								$Stmt2 = null;
							}
							//강사 브레이크 타입 검색
                            ?>
							
							<table class="uk-table uk-table-align-vertical">
                                <thead>
                                    <tr>
                                        <th style="width:10%" nowrap rowspan="2"><?=$시[$LangID]?></th>
										<th style="width:10%" nowrap rowspan="2"><?=$분[$LangID]?></th>
                                        <?for ($tt=1;$tt<=$ListNum-1;$tt++){?>
										<th nowrap colspan="<?=$WorkDayCount?>"><?=$ArrTeacherName[$tt]?></th>
										<?}?>
                                    </tr>
									<tr>
										<?for ($tt=1;$tt<=$ListNum-1;$tt++){?>
											<?
											for ($kk=0;$kk<=6;$kk++){
												if ($EduCenterHoliday[$kk]==0) {
												
											?>
											<th nowrap><?=$ArrWeekDayStr[$kk]?></th>
											<?
												}
											}
											?>
										<?}?>
									</tr>
                                </thead>
                                <tbody>

								<?
								for ($ii=$MinTeacherHour;$ii<=$MaxTeacherHour-1;$ii++){
								?>
								<tr>
									<td class="uk-text-nowrap uk-table-td-center" rowspan="6"><?=$ii?></td>
								<?
									for ($jj=0;$jj<=50;$jj=$jj+10){

										$jjjj++;

										if ($jj>0){
								?>
								<tr>
								<?
										}
								?>
									<td class="uk-text-nowrap uk-table-td-center"><?=$jj?></td>
									<?
									for ($tt=1;$tt<=$ListNum-1;$tt++){
										$TeacherID = $ArrTeacherID[$tt];//현재 슬랏 교사 아이디 
									?>
											<?
											for ($kk=0;$kk<=6;$kk++){
												if ($EduCenterHoliday[$kk]==0) {

													$SlotBreakEvent = 0;
													$SlotBreakEventName = "선택";
													$TeacherStudySlot[$tt][$kk][$ii][$jj] = 0;

													if ($SlotBreakEvent ==0 ){//교사 기존 수업 검색
														if ($type=="11"){
															$Sql2 = "select 
																			count(*) as RowCount
																	from ClassOrderSlots A 
																		inner join ClassOrders B on A.ClassOrderID=B.ClassOrderID 
																	where 
																			A.StudyTimeWeek=$kk 
																			and A.StudyTimeHour=$ii 
																			and A.StudyTimeMinute=$jj 
																			and A.TeacherID=$TeacherID 
																			and B.ClassOrderState=1 
																			and B.ClassOrderID<>$ClassOrderID
																			";
														}else{
															$Sql2 = "select 
																			count(*) as RowCount
																	from ClassOrderSlots A 
																		inner join ClassOrders B on A.ClassOrderID=B.ClassOrderID 
																	where 
																			A.StudyTimeWeek=$kk 
																			and A.StudyTimeHour=$ii 
																			and A.StudyTimeMinute=$jj 
																			and A.TeacherID=$TeacherID 
																			and B.ClassOrderState=1 
																			";
														}
														$Stmt2 = $DbConn->prepare($Sql2);
														$Stmt2->execute();
														$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
														$Row2 = $Stmt2->fetch();
														$TeacherStudySlotCount = $Row2["RowCount"];

														if ($TeacherStudySlotCount>0) {
															$SlotBreakEvent = 1;
															$BgColor = "#CC99FF";
															$FontColor = "#FFFFFF";
															$SlotBreakEventName = "<?=$수업[$LangID]?>";
															$TeacherStudySlot[$tt][$kk][$ii][$jj] = 1;
														}

													}


													if ($SlotBreakEvent ==0 ){//교사 수업가능 시간 검색
														if ($ii < $ArrTeacherStartHour[$tt] || $ii > $ArrTeacherEndHour[$tt] ){
															$SlotBreakEvent = 1;
															$BgColor = "#888888";
															$FontColor = "#FFFFFF";
															$SlotBreakEventName = "<?=$궐석[$LangID]?>";
														}
													}
															
													if ($SlotBreakEvent ==0 ){//교육센터 브레이크타임 검색
														$BgColor = "#FBFBFB";
														$FontColor = "#888888";
														if ($EduCenterBreak[$kk][$ii][$jj]==1) {
															$BgColor = "#FBFBFB";
															$FontColor = "#888888";
														}else if ($EduCenterBreak[$kk][$ii][$jj]==2) {
															$SlotBreakEvent = 1;
															$BgColor = "#FFCC00";
															$FontColor = "#FFFFFF";
															$SlotBreakEventName = "<?=$식사_개[$LangID]?>";
														}else if ($EduCenterBreak[$kk][$ii][$jj]==3) {
															$SlotBreakEvent = 1;
															$BgColor = "#CC9933";
															$FontColor = "#FFFFFF";
															$SlotBreakEventName = "<?=$휴식_개[$LangID]?>";
														}else if ($EduCenterBreak[$kk][$ii][$jj]==4) {
															$SlotBreakEvent = 1;
															$BgColor = "#CC6666";
															$FontColor = "#FFFFFF";
															$SlotBreakEventName = "<?=$블락_개[$LangID]?>";
														}
													}


													if ($SlotBreakEvent ==0 ){//교육센터가 수업가능일경우 교사 브레이크타임 검색

														$BgColor = "#FBFBFB";
														$FontColor = "#888888";
														if ($TeacherBreak[$tt][$kk][$ii][$jj]==1) {
															$BgColor = "#FBFBFB";
															$FontColor = "#888888";
														}else if ($TeacherBreak[$tt][$kk][$ii][$jj]==2) {
															$SlotBreakEvent = 1;
															$BgColor = "#FFCC00";
															$FontColor = "#FFFFFF";
															$SlotBreakEventName = "<?=$식사[$LangID]?>";
														}else if ($TeacherBreak[$tt][$kk][$ii][$jj]==3) {
															$SlotBreakEvent = 1;
															$BgColor = "#CC9933";
															$FontColor = "#FFFFFF";
															$SlotBreakEventName = "<?=$휴식[$LangID]?>";
														}else if ($TeacherBreak[$tt][$kk][$ii][$jj]==4) {
															$SlotBreakEvent = 1;
															$BgColor = "#CC6666";
															$FontColor = "#FFFFFF";
															$SlotBreakEventName = "<?=$블락[$LangID]?>";
														}

													}

													if ($SlotBreakEvent ==0 ){//80분 연속수업인지 검색
														$jjj = $jj;
														$iii = $ii;
														$mmm = 0;
														for ($kkk=8;$kkk>=1;$kkk--){
															
															if ($jjj==0){
																$jjj=50;
																$iii=$iii-1;
															}else{
																$jjj=$jjj-10;
															}
															
															if ($iii>=$MinTeacherHour){
																if ($TeacherStudySlot[$tt][$kk][$iii][$jjj] && $TeacherStudySlot[$tt][$kk][$iii][$jjj] == 1){
																	$mmm++;
																}

																if ($mmm==8){
																	$SlotBreakEvent = 1;
																	$BgColor = "#FF0000";
																	$FontColor = "#FFFFFF";
																	$SlotBreakEventName = "<?=$강휴[$LangID]?>";
																}
															}

														}
													}

													

													if ($SlotBreakEventName!=""){$SlotBreakEventName=" (".$SlotBreakEventName.")";}
											
											
													
													if ($SlotBreakEvent==0){//수업가능
											?>
											<td nowrap id="Div_Slot_<?=$tt?>_<?=$kk?>_<?=$jjjj?>" style="background-color:<?=$BgColor?>;color:<?=$FontColor?>;text-align:center;cursor:pointer;" onclick="SelectSlot(<?=$tt?>,<?=$kk?>,<?=$jjjj?>);">
												<?=$ArrWeekDayStr[$kk]?><?=$SlotBreakEventName?>
												<input type="hidden" name="Slot_<?=$tt?>_<?=$kk?>_<?=$jjjj?>" id="Slot_<?=$tt?>_<?=$kk?>_<?=$jjjj?>" value="|<?=$TeacherID?>_<?=$kk?>_<?=$ii?>_<?=$jj?>">
												<input type="hidden" name="Able_<?=$tt?>_<?=$kk?>_<?=$jjjj?>" id="Able_<?=$tt?>_<?=$kk?>_<?=$jjjj?>" value="1">
											</td>
											<?
													}else{
											?>
											<td nowrap style="background-color:<?=$BgColor?>;color:<?=$FontColor?>;text-align:center;"><?=$ArrWeekDayStr[$kk]?><?=$SlotBreakEventName?></td>
											<?
													}
												}
											}
											?>
									<?
									}
									?>
								</tr>
								<?
									}
								}
								?>
                                </tbody>

                            </table>
							
							
							<div class="uk-form-row" style="text-align:center;display:<?if ($type!="11"){?>none<?}?>;">
								<input type="hidden" name="SelectSlotCode" id="SelectSlotCode" value="">
								<a type="button" href="javascript:ClassOrderSubmit();" class="md-btn md-btn-primary"><?=$적용하기[$LangID]?></a>
							</div>
	
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

<script>
var ClassOrderTimeSlotCount = <?=$ClassOrderTimeSlotCount?>;
var ClassOrderID = <?=$ClassOrderID?>;

var ClassOrderWeekCount = <?=$ClassOrderWeekCount?>;
var SelectedSlotCount = 0;


function SelectSlot(TeacherNum, WeekNum, TimeNum){
<?
if ($type=="11"){//======================================================= 
?>	
	if ($("#Able_"+TeacherNum+"_"+WeekNum+"_"+TimeNum).val()=="1"){

		DenySelect = 0;
		
		SelectSlotCode = document.getElementById("SelectSlotCode").value;
		SlotCode = "";
		for (ii=TimeNum; ii<=TimeNum+ClassOrderTimeSlotCount-1; ii++){
			if ($("#Slot_"+TeacherNum+"_"+WeekNum+"_"+ii).length==0){
				DenySelect = 1;
			}else{
				SlotCode = SlotCode.concat($("#Slot_"+TeacherNum+"_"+WeekNum+"_"+ii).val());
			}
		}

		

		if (SelectSlotCode.indexOf(SlotCode)<0 && SelectedSlotCount>=(ClassOrderWeekCount*ClassOrderTimeSlotCount)){
			UIkit.modal.alert( "<?=$주[$LangID]?>" + ClassOrderWeekCount + '<?=$회까지_선택할_수_있습니다[$LangID]?>');
		}else{
			if (DenySelect==1){
				UIkit.modal.alert( (ClassOrderTimeSlotCount*10) + '<?=$분_수업을_구성할_수_없습니다[$LangID]?>');
			}else{
				
				ablenum = 0;
				for (ii=TimeNum; ii<=TimeNum+ClassOrderTimeSlotCount-1; ii++){
					
					if (SelectSlotCode.indexOf(SlotCode)>=0){
						document.getElementById("SelectSlotCode").value = SelectSlotCode.replace(SlotCode, ""); 
						$("#Div_Slot_"+TeacherNum+"_"+WeekNum+"_"+ii).css("background-color","#FBFBFB");
						$("#Div_Slot_"+TeacherNum+"_"+WeekNum+"_"+ii).css("color","#888888");

						SelectedSlotCount--;

						if (ablenum>0){
							$("#Able_"+TeacherNum+"_"+WeekNum+"_"+ii).val("1");
						}
					}else{
						document.getElementById("SelectSlotCode").value = SelectSlotCode + SlotCode;
						if (ablenum==0){
							$("#Div_Slot_"+TeacherNum+"_"+WeekNum+"_"+ii).css("background-color","#2D96FF");
						}else{
							$("#Div_Slot_"+TeacherNum+"_"+WeekNum+"_"+ii).css("background-color","#76B6FC");
						}
						$("#Div_Slot_"+TeacherNum+"_"+WeekNum+"_"+ii).css("color","#ffffff");

						SelectedSlotCount++;

						if (ablenum>0){
							$("#Able_"+TeacherNum+"_"+WeekNum+"_"+ii).val("0");
						}
					}
					
				
					ablenum++;
				}
			}
		}
	}

<?
}//======================================================= 
?>
}

function ClassOrderSubmit(){
	if (SelectedSlotCount < (ClassOrderWeekCount*ClassOrderTimeSlotCount)){
		UIkit.modal.alert( "<?=$주[$LangID]?>" + ClassOrderWeekCount + '<?=$회_선택하셔야_합니다[$LangID]?>');
	}else{
		
		UIkit.modal.confirm(
			'<?=$적용_하시겠습니까[$LangID]?>?', 
			function(){ 

				ClassOrderStartDate = document.getElementById("ClassOrderStartDate").value;
				SelectSlotCode = document.getElementById("SelectSlotCode").value;
				url = "ajax_set_class_order_class.php";

				//location.href = url + "?ClassOrderStartDate="+ClassOrderStartDate+"&ClassOrderID="+ClassOrderID+"&SelectSlotCode="+SelectSlotCode;

				
				$.ajax(url, {
					data: {
						ClassOrderStartDate: ClassOrderStartDate,
						ClassOrderID: ClassOrderID,
						SelectSlotCode: SelectSlotCode
					},
					success: function (data) {
						location.href = "class_order_list.php?type=21";
					},
					error: function () {

					}
				});
				



			}
		);



	}
}

function SearchSubmit(){
	document.SearchForm.action = "edu_center_schedule.php";
    document.SearchForm.submit();
}
</script>

<!-- ==============  only this page js ============== -->
<!-- ==============  common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ==============  common.js ============== -->





<script language="javascript">

function FormSubmit(){
	obj = document.RegForm.FranchiseID;
	if (obj.value==""){
		UIkit.modal.alert('<?=$프랜차이즈를_선택하세요[$LangID]?>');
		obj.focus();
		return;
	}

	obj = document.RegForm.EduCenterName;
	if (obj.value==""){
		UIkit.modal.alert("<?=$교육센터명을_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}

	obj = document.RegForm.EduCenterManagerName;
	if (obj.value==""){
		UIkit.modal.alert("<?=$교육센터_관리자명을_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}

	UIkit.modal.confirm(
		'<?=$저장_하시겠습니까[$LangID]?>?', 
		function(){ 
			document.RegForm.action = "edu_center_action.php";
			document.RegForm.submit();
		}
	);
}

</script>

<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>