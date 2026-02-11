<body class="disable_transitions sidebar_main_open sidebar_main_swipe" style="padding-top:0px;">
<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

// --- 삭제 권한 여부 판단 ---
$canDelete = false;

// 로그인 ID·MemberID 는 프로젝트에 맞게 읽어오세요.
$loginID  = isset($_LINK_ADMIN_LOGIN_ID_)   ? $_LINK_ADMIN_LOGIN_ID_   : '';
$loginMID = isset($_LINK_ADMIN_MEMBER_ID_)  ? $_LINK_ADMIN_MEMBER_ID_  : 0;

// '장지웅1' 또는 'maiskd' 계정일 경우 삭제 권한 부여
if ($loginID === '장지웅1' || $loginID === 'maiskd') {
    $canDelete = true;
}

// 기존 코드의 ClassOrderID, MemberID 등을 읽어오는 부분은 그대로 둡니다.
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";

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
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$ClassOrderID = isset($_REQUEST["ClassOrderID"]) ? $_REQUEST["ClassOrderID"] : "";
$ClassOrderPayID = isset($_REQUEST["ClassOrderPayID"]) ? $_REQUEST["ClassOrderPayID"] : "";


if($ClassOrderPayID!="") {
	$Sql = "
		select 
			A.ClassOrderPayStartDate 
		from ClassOrderPays A 
		where 
			A.ClassOrderPayID=:ClassOrderPayID
	";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ClassOrderPayID', $ClassOrderPayID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$ClassOrderPayStartDate = $Row["ClassOrderPayStartDate"];
	$Stmt = null;
} else {
	$ClassOrderPayStartDate = 0;
}

if ($ClassOrderID!=""){

	$Sql = "
			select 
					A.*,
					B.MemberName
			from ClassOrders A 
				inner join Members B on A.MemberID=B.MemberID 
			where A.ClassOrderID=:ClassOrderID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ClassOrderID', $ClassOrderID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$ClassProductID = $Row["ClassProductID"];
	$ClassOrderLeveltestApplyTypeID = $Row["ClassOrderLeveltestApplyTypeID"];
	$ClassOrderLeveltestApplyLevel = $Row["ClassOrderLeveltestApplyLevel"];
	$ClassOrderLeveltestApplyOverseaTypeID = $Row["ClassOrderLeveltestApplyOverseaTypeID"];
	$ClassOrderLeveltestApplyText = $Row["ClassOrderLeveltestApplyText"];
	$ClassOrderTimeTypeID = $Row["ClassOrderTimeTypeID"];
	$ClassOrderWeekCountID = $Row["ClassOrderWeekCountID"];
	$MemberID = $Row["MemberID"];
	$ClassOrderRequestText = $Row["ClassOrderRequestText"];
	$ClassOrderState = $Row["ClassOrderState"];
	$ClassOrderStartDate = $Row["ClassOrderStartDate"];
	$ClassOrderEndDate = $Row["ClassOrderEndDate"];
	$ClassMemberType = $Row["ClassMemberType"];
	$ClassProgress = $Row["ClassProgress"];
	$ClassOrderRegDateTime = $Row["ClassOrderRegDateTime"];
	$ClassOrderModiDateTime = $Row["ClassOrderModiDateTime"];

	$MemberName = $Row["MemberName"];

	if ($ClassProductID==2){
		$ClassOrderRequestText = $ClassOrderLeveltestApplyText;
	}

}else{
	$ClassProductID = 1;
	$ClassOrderLeveltestApplyTypeID = 1;
	$ClassOrderLeveltestApplyLevel=1;
	$ClassOrderLeveltestApplyOverseaTypeID=1;
	$ClassOrderTimeTypeID = 2;
	$ClassOrderWeekCountID = 1;

	$ClassOrderRequestText = "";
	$ClassOrderState = 1;
	$ClassMemberType = 1;
	$ClassProgress = 1;


	$Sql = "
			select 
					A.*
			from Members A 
			where A.MemberID=:MemberID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$MemberName = $Row["MemberName"];
}
?>

<div id="page_content">
	<div id="page_content_inner">



		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="ClassOrderID" value="<?=$ClassOrderID?>">
		<input type="hidden" name="MemberID" value="<?=$MemberID?>">
		<input type="hidden" name="ClassProgress" value="<?=$ClassProgress?>">
		<input type="hidden" name="ClassOrderPayID" value="<?=$ClassOrderPayID?>">
		

		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$MemberName?></span><span class="sub-heading" id="user_edit_position"><?=$스케줄요청관리[$LangID]?></span></h2>
						</div>
					</div>
					<div class="user_content">
						

						<h3 class="full_width_in_card heading_c">
							<?=$신청강좌[$LangID]?>
						</h3>					
						<div class="uk-margin-top" style="margin-top:20px;">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-2-10">
								<?=$강좌타입[$LangID]?>
								</div>
								<div class="uk-width-medium-8-10">
									<?
									if ($ClassOrderID!=""){
									
										$Sql2 = "select 
														A.ClassProductName 
												from ClassProducts A 
												where ClassProductID=$ClassProductID ";
										$Stmt2 = $DbConn->prepare($Sql2);
										$Stmt2->execute();
										$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
										$Row2 = $Stmt2->fetch();
										$Stmt2 = null;
										$ClassProductName = $Row2["ClassProductName"];
									?>
										<?=$ClassProductName?>
										<input type="hidden" name="ClassProductID" value="<?=$ClassProductID?>">
									<?}
									else{
									?>
										<?
										$Sql2 = "select 
														A.* 
												from ClassProducts A 
												where ClassProductState=1 
												order by A.ClassProductOrder asc";
										$Stmt2 = $DbConn->prepare($Sql2);
										$Stmt2->execute();
										$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
										
										while($Row2 = $Stmt2->fetch()) {
											$SelectClassProductID = $Row2["ClassProductID"];
											$SelectClassProductName = $Row2["ClassProductName"];
										
										?>
										<span class="icheck-inline">
											<input type="radio" id="ClassProductID<?=$SelectClassProductID?>" onclick="ChClassProductID(<?=$SelectClassProductID?>);" class="radio_input" name="ClassProductID" <?php if ($SelectClassProductID==$ClassProductID) { echo "checked";}?> value="<?=$SelectClassProductID?>">
											<label for="ClassProductID<?=$SelectClassProductID?>" class="radio_label"><span class="radio_bullet"></span><?=$SelectClassProductName?></label>
										</span>


										<?
										}
										$Stmt2 = null;
										?>
									<?
									}
									?>
								</div>
							</div>
						</div>
						
						<script>
						function ChClassProductID(ClassProductID){
							if (ClassProductID==1){//화상수업
								document.getElementById("TrClassOrderLeveltestApplyTypeID").style.display = "none";
								document.getElementById("TrClassOrderLeveltestApplyOverseaTypeID").style.display = "none";
								document.getElementById("TrClassOrderLeveltestApplyLevel").style.display = "none";
								document.getElementById("TrClassMemberType").style.display = "";

								document.getElementById("TrClassOrderWeekCountID_1").style.display = "";
								document.getElementById("TrClassOrderWeekCountID_2").style.display = "";
								document.getElementById("TrClassOrderTimeTypeID_1").style.display = "";
								document.getElementById("TrClassOrderTimeTypeID_2").style.display = "";

							}else if (ClassProductID==2){//레벨테스트
								document.getElementById("TrClassOrderLeveltestApplyTypeID").style.display = "";
								document.getElementById("TrClassOrderLeveltestApplyOverseaTypeID").style.display = "";
								document.getElementById("TrClassOrderLeveltestApplyLevel").style.display = "";
								document.getElementById("TrClassMemberType").style.display = "none";

								document.getElementById("TrClassOrderWeekCountID_1").style.display = "none";
								document.getElementById("TrClassOrderWeekCountID_2").style.display = "none";
								document.getElementById("TrClassOrderTimeTypeID_1").style.display = "none";
								document.getElementById("TrClassOrderTimeTypeID_2").style.display = "none";

							}else if (ClassProductID==3){//체험수업
								document.getElementById("TrClassOrderLeveltestApplyTypeID").style.display = "none";
								document.getElementById("TrClassOrderLeveltestApplyOverseaTypeID").style.display = "none";
								document.getElementById("TrClassOrderLeveltestApplyLevel").style.display = "none";
								document.getElementById("TrClassMemberType").style.display = "";

								document.getElementById("TrClassOrderWeekCountID_1").style.display = "none";
								document.getElementById("TrClassOrderWeekCountID_2").style.display = "none";
								document.getElementById("TrClassOrderTimeTypeID_1").style.display = "none";
								document.getElementById("TrClassOrderTimeTypeID_2").style.display = "none";
							}
						}
						</script>

						<div id="TrClassMemberType" class="uk-margin-top" style="margin-top:20px;display:<?if ($ClassProductID==2){?>none<?}?>;" >
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-2-10">
								<?=$수업타입[$LangID]?>
								</div>
								<div class="uk-width-medium-8-10">
									<?
									if ($ClassOrderID!=""){
									
										if ($ClassMemberType==1){
									?>
											1:1수업
									<?
										}else if ($ClassMemberType==2){
									?>
											1:2수업
									<?
										}else if ($ClassMemberType==3){
									?>
											<?=$그룹수업[$LangID]?>
									<?
										}
									}else{
									?>
										<span class="icheck-inline">
											<input type="radio" id="ClassMemberType1" class="radio_input" name="ClassMemberType" <?php if ($ClassMemberType==1) { echo "checked";}?> value="1">
											<label for="ClassMemberType1" class="radio_label"><span class="radio_bullet"></span>1:1 수업</label>
										</span>
										<span class="icheck-inline">
											<input type="radio" id="ClassMemberType2" class="radio_input" name="ClassMemberType" <?php if ($ClassMemberType==2) { echo "checked";}?> value="2">
											<label for="ClassMemberType2" class="radio_label"><span class="radio_bullet"></span>1:2 수업</label>
										</span>
										<span class="icheck-inline">
											<input type="radio" id="ClassMemberType3" class="radio_input" name="ClassMemberType" <?php if ($ClassMemberType==3) { echo "checked";}?> value="3">
											<label for="ClassMemberType3" class="radio_label"><span class="radio_bullet"></span><?=$그룹수업[$LangID]?></label>
										</span>
									<?
									}
									?>
								</div>
							</div>
						</div>

						<div id="TrClassOrderLeveltestApplyTypeID" class="uk-margin-top" style="margin-top:20px;display:<?if ($ClassProductID!=2){?>none<?}?>;">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-2-10">
								신청동기
								</div>
								<div class="uk-width-medium-8-10">
									<?
									if ($ClassOrderID!=""){
									
										$Sql2 = "select 
														A.ClassOrderLeveltestApplyTypeName 
												from ClassOrderLeveltestApplyTypes A 
												where ClassOrderLeveltestApplyTypeID=$ClassOrderLeveltestApplyTypeID ";
										$Stmt2 = $DbConn->prepare($Sql2);
										$Stmt2->execute();
										$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
										$Row2 = $Stmt2->fetch();
										$Stmt2 = null;
										$ClassOrderLeveltestApplyTypeName = $Row2["ClassOrderLeveltestApplyTypeName"];
									?>
										<?=$ClassOrderLeveltestApplyTypeName?>
									<?}
									else{
									?>
										<select id="ClassOrderLeveltestApplyTypeID" name="ClassOrderLeveltestApplyTypeID" style="width:95%;height:30px;">
											<?
											$Sql2 = "select 
															A.* 
													from ClassOrderLeveltestApplyTypes A 
													order by A.ClassOrderLeveltestApplyTypeID asc";
											$Stmt2 = $DbConn->prepare($Sql2);
											$Stmt2->execute();
											$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
											
											while($Row2 = $Stmt2->fetch()) {
												$SelectClassOrderLeveltestApplyTypeID = $Row2["ClassOrderLeveltestApplyTypeID"];
												$SelectClassOrderLeveltestApplyTypeName = $Row2["ClassOrderLeveltestApplyTypeName"];
											
											?>
											<option value="<?=$ClassOrderLeveltestApplyTypeID?>" <?if ($SelectClassOrderLeveltestApplyTypeID==$ClassOrderLeveltestApplyTypeID){?>selected<?}?>><?=$SelectClassOrderLeveltestApplyTypeName?></option>
											<?
											}
											$Stmt2 = null;
											?>
										</select>
									<?
									}
									?>
								</div>
							</div>
						</div>

						<div id="TrClassOrderLeveltestApplyLevel" class="uk-margin-top" style="margin-top:20px;display:<?if ($ClassProductID!=2){?>none<?}?>;">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-2-10">
								<?=$신청레벨[$LangID]?>
								</div>
								<div class="uk-width-medium-8-10">
									<input type="radio" id="ClassOrderLeveltestApplyLevel1" class="radio_input" name="ClassOrderLeveltestApplyLevel" value="1" <?if ($ClassOrderLeveltestApplyLevel==1){?>checked<?}?>><label  class="radio_label" for="ClassOrderLeveltestApplyLevel1" ><span class="radio_bullet"></span>1레벨</label>
									<input type="radio" id="ClassOrderLeveltestApplyLevel2" class="radio_input" name="ClassOrderLeveltestApplyLevel" value="2" <?if ($ClassOrderLeveltestApplyLevel==2){?>checked<?}?>><label  class="radio_label" for="ClassOrderLeveltestApplyLevel2"><span class="radio_bullet"></span>2레벨</label>
									<input type="radio" id="ClassOrderLeveltestApplyLevel3" class="radio_input" name="ClassOrderLeveltestApplyLevel" value="3" <?if ($ClassOrderLeveltestApplyLevel==3){?>checked<?}?>><label  class="radio_label" for="ClassOrderLeveltestApplyLevel3"><span class="radio_bullet"></span>3레벨</label>
									<input type="radio" id="ClassOrderLeveltestApplyLevel4" class="radio_input" name="ClassOrderLeveltestApplyLevel" value="4" <?if ($ClassOrderLeveltestApplyLevel==4){?>checked<?}?>><label  class="radio_label" for="ClassOrderLeveltestApplyLevel4"><span class="radio_bullet"></span>4레벨</label>
									<input type="radio" id="ClassOrderLeveltestApplyLevel5" class="radio_input" name="ClassOrderLeveltestApplyLevel" value="5" <?if ($ClassOrderLeveltestApplyLevel==5){?>checked<?}?>><label  class="radio_label" for="ClassOrderLeveltestApplyLevel5"><span class="radio_bullet"></span>5레벨</label>
									<input type="radio" id="ClassOrderLeveltestApplyLevel6" class="radio_input" name="ClassOrderLeveltestApplyLevel" value="6" <?if ($ClassOrderLeveltestApplyLevel==6){?>checked<?}?>><label  class="radio_label" for="ClassOrderLeveltestApplyLevel6"><span class="radio_bullet"></span>6레벨</label>
									<input type="radio" id="ClassOrderLeveltestApplyLevel7" class="radio_input" name="ClassOrderLeveltestApplyLevel" value="7" <?if ($ClassOrderLeveltestApplyLevel==7){?>checked<?}?>><label  class="radio_label" for="ClassOrderLeveltestApplyLevel7"><span class="radio_bullet"></span>7레벨</label>
									<input type="radio" id="ClassOrderLeveltestApplyLevel8" class="radio_input" name="ClassOrderLeveltestApplyLevel" value="8" <?if ($ClassOrderLeveltestApplyLevel==8){?>checked<?}?>><label  class="radio_label" for="ClassOrderLeveltestApplyLevel8"><span class="radio_bullet"></span>8레벨</label>
								</div>
							</div>
						</div>

						<div id="TrClassOrderLeveltestApplyOverseaTypeID" class="uk-margin-top" style="margin-top:20px;display:<?if ($ClassProductID!=2){?>none<?}?>;">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-2-10">
								연수경험
								</div>
								<div class="uk-width-medium-8-10">
									<input type="radio" id="ClassOrderLeveltestApplyOverseaTypeID1" class="radio_input" name="ClassOrderLeveltestApplyOverseaTypeID" value="1" <?if ($ClassOrderLeveltestApplyOverseaTypeID==1){?>checked<?}?>><label  class="radio_label" for="ClassOrderLeveltestApplyOverseaTypeID1"><span class="radio_bullet"></span><?=$없음[$LangID]?></label>
									<input type="radio" id="ClassOrderLeveltestApplyOverseaTypeID2" class="radio_input" name="ClassOrderLeveltestApplyOverseaTypeID" value="2"> <?if ($ClassOrderLeveltestApplyOverseaTypeID==2){?>checked<?}?><label  class="radio_label" for="ClassOrderLeveltestApplyOverseaTypeID2"><span class="radio_bullet"></span>3개월</label>
									<input type="radio" id="ClassOrderLeveltestApplyOverseaTypeID3" class="radio_input" name="ClassOrderLeveltestApplyOverseaTypeID" value="3" <?if ($ClassOrderLeveltestApplyOverseaTypeID==3){?>checked<?}?>><label  class="radio_label" for="ClassOrderLeveltestApplyOverseaTypeID3"><span class="radio_bullet"></span>6개월</label>
									<input type="radio" id="ClassOrderLeveltestApplyOverseaTypeID4" class="radio_input" name="ClassOrderLeveltestApplyOverseaTypeID" value="4" <?if ($ClassOrderLeveltestApplyOverseaTypeID==4){?>checked<?}?>><label  class="radio_label" for="ClassOrderLeveltestApplyOverseaTypeID4"><span class="radio_bullet"></span>1년</label>
									<input type="radio" id="ClassOrderLeveltestApplyOverseaTypeID5" class="radio_input" name="ClassOrderLeveltestApplyOverseaTypeID" value="5" <?if ($ClassOrderLeveltestApplyOverseaTypeID==5){?>checked<?}?>><label  class="radio_label" for="ClassOrderLeveltestApplyOverseaTypeID5"><span class="radio_bullet"></span>2년</label>
									<input type="radio" id="ClassOrderLeveltestApplyOverseaTypeID6" class="radio_input" name="ClassOrderLeveltestApplyOverseaTypeID" value="6" <?if ($ClassOrderLeveltestApplyOverseaTypeID==6){?>checked<?}?>><label  class="radio_label" for="ClassOrderLeveltestApplyOverseaTypeID6"><span class="radio_bullet"></span>3년이상</label>
								</div>
							</div>
						</div>


						<h3 id="TrClassOrderWeekCountID_1" class="full_width_in_card heading_c" style="display:<?if ($ClassProductID!=1){?>none<?}?>;">
							<?=$주당_학습회수[$LangID]?>
						</h3>
						<div id="TrClassOrderWeekCountID_2" class="uk-margin-top" style="display:<?if ($ClassProductID!=1){?>none<?}?>;">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-2-10">
								<?=$회수[$LangID]?>/<?=$주[$LangID]?>
								</div>
								<div class="uk-width-medium-8-10">
									<?
									//ClassOrderWeekCount
									if ($ClassOrderID!=""){
									
										$Sql2 = "select 
														A.ClassOrderWeekCountName
												from ClassOrderWeekCounts A 
												where ClassOrderWeekCountID=$ClassOrderWeekCountID ";
										$Stmt2 = $DbConn->prepare($Sql2);
										$Stmt2->execute();
										$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
										$Row2 = $Stmt2->fetch();
										$Stmt2 = null;

										$ClassOrderWeekCountName = $Row2["ClassOrderWeekCountName"]
									?>
										<?=$ClassOrderWeekCountName?>
									<?
									}else{
									?>
									
										<?
										$Sql2 = "select 
														A.* 
												from ClassOrderWeekCounts A 
												where ClassOrderWeekCountState=1 
												order by A.ClassOrderWeekCountOrder asc";
										$Stmt2 = $DbConn->prepare($Sql2);
										$Stmt2->execute();
										$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
										
										while($Row2 = $Stmt2->fetch()) {
											$SelectClassOrderWeekCountID = $Row2["ClassOrderWeekCountID"];
											$SelectClassOrderWeekCountName = $Row2["ClassOrderWeekCountName"];
										
										?>
										<span class="icheck-inline">
											<input type="radio" class="radio_input" name="ClassOrderWeekCountID" id="ClassOrderWeekCountID<?=$SelectClassOrderWeekCountID?>" value="<?=$SelectClassOrderWeekCountID?>" <?if ($ClassOrderWeekCountID==$SelectClassOrderWeekCountID){?>checked<?}?>/>
											<label for="ClassOrderWeekCountID<?=$SelectClassOrderWeekCountID?>" class="radio_label"><span class="radio_bullet"></span><?=$SelectClassOrderWeekCountName?></label>
										</span>
										<?
										}
										?>
									<?
									}
									?>
								</div>
							</div>
						</div>

						<h3 id="TrClassOrderTimeTypeID_1" class="full_width_in_card heading_c" style="display:<?if ($ClassProductID!=1){?>none<?}?>;">
							<?=$회당_학습시간[$LangID]?>
						</h3>
						<div id="TrClassOrderTimeTypeID_2" class="uk-margin-top" style="display:<?if ($ClassProductID!=1){?>none<?}?>;">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-2-10">
								<?=$분[$LangID]?>/<?=$회[$LangID]?>
								</div>
								<div class="uk-width-medium-8-10">
									<?
									
									if ($ClassOrderID!=""){
									
										$Sql2 = "select 
													A.ClassOrderTimeTypeName
											from ClassOrderTimeTypes A 
											where ClassOrderTimeTypeID=$ClassOrderTimeTypeID";
										$Stmt2 = $DbConn->prepare($Sql2);
										$Stmt2->execute();
										$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
										$Row2 = $Stmt2->fetch();
										$Stmt2 = null;

										$ClassOrderTimeTypeName = $Row2["ClassOrderTimeTypeName"]
									?>
										<?=$ClassOrderTimeTypeName?>
									<?
									}else{
									?>
										<?
										//ClassOrderTimeSlotCount
										$Sql2 = "select 
														A.* 
												from ClassOrderTimeTypes A 
												where ClassOrderTimeTypeState=1 
												order by A.ClassOrderTimeTypeOrder asc";
										$Stmt2 = $DbConn->prepare($Sql2);
										$Stmt2->execute();
										$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
										
										while($Row2 = $Stmt2->fetch()) {
											$SelectClassOrderTimeTypeID = $Row2["ClassOrderTimeTypeID"];
											$SelectClassOrderTimeTypeName = $Row2["ClassOrderTimeTypeName"];
										
										?>
										<span class="icheck-inline">
											<input type="radio" class="radio_input" name="ClassOrderTimeTypeID" id="ClassOrderTimeTypeID<?=$SelectClassOrderTimeTypeID?>" value="<?=$SelectClassOrderTimeTypeID?>" <?if ($ClassOrderTimeTypeID==$SelectClassOrderTimeTypeID){?>checked<?}?>/>
											<label for="ClassOrderTimeTypeID<?=$SelectClassOrderTimeTypeID?>" class="radio_label"><span class="radio_bullet"></span><?=$SelectClassOrderTimeTypeName?></label>
										</span>
										<?
										}
										?>
									<?
									}
									?>									
								</div>
							</div>
						</div>


						
						<h3 class="full_width_in_card heading_c">
							<?=$요청_및_전달사항[$LangID]?>
						</h3>
						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<label for="ClassOrderRequestText"><?=$상세한_희망수강시간_등_요청사항을_작성해주세요[$LangID]?></label>
									<textarea class="md-input" name="ClassOrderRequestText" id="ClassOrderRequestText" cols="30" rows="4"><?=$ClassOrderRequestText?></textarea>
								</div>
							</div>
						</div>


						<?if ($ClassOrderID!="" && $ClassProductID==1){?>
						<h3 class="full_width_in_card heading_c">
							<?=$수강기간[$LangID]?>
						</h3>
						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-2-10">
								<?=$시작일[$LangID]?>
								</div>
								<div class="uk-width-medium-3-10">
									<input type="text" id="ClassOrderStartDate" name="ClassOrderStartDate" value="<?=$ClassOrderStartDate?>" class="md-input label-fixed " style="text-align:center" data-uk-datepicker="{format:'YYYY-MM-DD', weekstart:0}" readonly>
								</div>
								<div class="uk-width-medium-2-10">
								<?=$종료일[$LangID]?>
								</div>
								<div class="uk-width-medium-3-10">
									<input type="text" id="ClassOrderEndDate" name="ClassOrderEndDate" value="<?=$ClassOrderEndDate?>" class="md-input label-fixed" style="text-align:center;" data-uk-datepicker="{format:'YYYY-MM-DD', weekstart:0}">
								</div>

								<div class="uk-width-medium-10-10">
								※ B2C 결제 학생의 경우 종료일 이후 수업이 진행되지 않습니다.
								<br>
								※ B2C 결제 또는 개인결제 학생의 경우 반드시 종료일이 설정되어 있어야 합니다.<br>&nbsp;&nbsp;&nbsp;(종료일 없으면 학습리스트가 노출되지 않음)
								<br>
								※ B2B 결제 학생의 경우 종료일 이후에도 수업이 가능하며 수업상태를 [종료완료]로 설정하면<br>&nbsp;&nbsp;&nbsp;더 이상 진행되지 않습니다.
								<br>
								※ 종료일과 상관없이 [종료완료]로 설정되면 해당 시간 슬랏에 다른 수업이 배정될 수 있으니 주의 바랍니다.
								</div>
							</div>
						</div>
						<?}?>


						<hr style="display:<?if ($ClassOrderID=="" || $_LINK_ADMIN_LEVEL_ID_>4){?>none<?}?>;">

						<h3 class="full_width_in_card heading_c">
							<?=$수강상태[$LangID]?>
						</h3>
						<div class="uk-margin-top" style="display:<?if ($ClassOrderID=="" || $_LINK_ADMIN_LEVEL_ID_>4){?>none<?}?>;">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-2-10">
								<?=$수업상태[$LangID]?>
								</div>
								<div class="uk-width-medium-8-10">
									<span class="icheck-inline">
										<input type="radio" class="radio_input" name="ClassOrderState" id="ClassOrderState1" value="1" <?if ($ClassOrderState==1){?>checked<?}?>/>
										<label for="ClassOrderState1" class="radio_label"><span class="radio_bullet"></span><?=$정상수업[$LangID]?></label>
									</span>
									<?if ($ClassProductID==1){?>
									<span class="icheck-inline">
										<input type="radio" class="radio_input" name="ClassOrderState" id="ClassOrderState2" value="2" <?if ($ClassOrderState==2){?>checked<?}?>/>
										<label for="ClassOrderState2" class="radio_label"><span class="radio_bullet"></span><?=$종료대상[$LangID]?></label>
									</span>
									<span class="icheck-inline">
										<input type="radio" class="radio_input" name="ClassOrderState" id="ClassOrderState3" value="3" onclick="ExplainOrderState();" <?if ($ClassOrderState==3){?>checked<?}?>/>
										<label for="ClassOrderState3" class="radio_label"><span class="radio_bullet"></span><?=$종료완료[$LangID]?></label>
									</span>
									<span class="icheck-inline">
										<input type="radio" class="radio_input" name="ClassOrderState" id="ClassOrderState4" value="4" <?if ($ClassOrderState==4){?>checked<?}?>/>
										<label for="ClassOrderState4" class="radio_label"><span class="radio_bullet"></span><?=$장기홀드[$LangID]?></label>
									</span>
									<?}?>

                                    <span class="icheck-inline">
                                       <input type="radio" class="radio_input" name="ClassOrderState" id="ClassOrderState0" value="0" <?if ($ClassOrderState==0){?>checked<?}?> <?php if(!$canDelete) echo 'disabled'; ?>/>
                                       <label for="ClassOrderState0" class="radio_label"><span class="radio_bullet"></span><?=$삭제[$LangID]?></label>
                                    </span>

                                    </div>
                                </div>

                                    <?php if (!$canDelete): ?>
                                        <p style="color:#ff0000;margin-top:8px;">
                                            수업 삭제 기능 사용이 제한되었습니다. 필요 시, 관리자에게 문의해 주시기 바랍니다.
                                        </p>
                                    <?php endif; ?>
                                </div>
								</div>
							</div>
						</div>


	
						<div class="uk-margin-top" style="text-align:center;padding-top:30px;">
							<?if ($ClassOrderID==""){?>
								<a type="button" href="javascript:PreFormSubmit();" class="md-btn md-btn-primary"><?=$저장하기[$LangID]?></a>
							<?}else{?>
								<a type="button" href="javascript:PreFormSubmit();" class="md-btn md-btn-primary"><?=$수정하기[$LangID]?></a>
							<?}?>
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

<script>

function ExplainOrderState() {
	//UIkit.modal.alert("※수강 종료 안내<br/><br/>강사는 다른 수업을 배정받을 수 있으며,<br/>이후에 다시 되살릴 경우 수업중복이 발생할 수 있으므로<br/>종료여부 재확인 해주세요.");
	var ClassOrderPayStartDate = '<?=$ClassOrderPayStartDate?>';

	if(ClassOrderPayStartDate=="") {
		UIkit.modal.alert("	수업의 시작일자가 비어있어,<br/> 잔여수업을 자동적으로 계산할 수 없습니다.<br/>수동으로 계산하여 진행해주세요.");
	}

}

function PreFormSubmit() {
	var TempClassOrderState = <?=$ClassOrderState?>;
	var ClassOrderState = document.RegForm.ClassOrderState.value;

	// 삭제상태 -> 정상 으로 돌릴 때
	if(TempClassOrderState==3 && ClassOrderState==1) {

		url = "ajax_check_class_time_exist.php";
		var ClassOrderID = "<?=$ClassOrderID?>";

		//location.href = url + "?ClassOrderID="+ClassOrderID;
		$.ajax(url, {
			data: {
				ClassOrderID: ClassOrderID
			},
			async: false,
			success: function (data) {
				var CheckResult = data.CheckResult;

				if(CheckResult==1) {
					var ClassOrderSlotType = data.ClassOrderSlotType;
					var ClassOrderSlotDate = data.ClassOrderSlotDate;
					var MemberName = data.MemberName;
					var ClassOrderStartDate = data.ClassOrderStartDate;

					if(ClassOrderSlotType==1) {
						var StrClassOrderSlotType = "정규수업("+ClassOrderStartDate+")";
					} else {
						var StrClassOrderSlotType = "임시수업("+ClassOrderSlotDate+")";
					}

					UIkit.modal.alert(MemberName+"님의 "+StrClassOrderSlotType+"이 존재하여<br/>수업을 되돌릴 수 없습니다.");
					return;
				}else{
					FormSubmit();
				}
			},
			error: function () {

			}
		});
	// 삭제 할때
	} else if(ClassOrderState==3) {
		//url = "ajax_check_class_exist.php";
		var ClassOrderPayStartDate = '<?=$ClassOrderPayStartDate?>';

		UIkit.modal.confirm(
			'※수강 종료 안내<br/><br/>강사는 다른 수업을 배정받을 수 있으며,<br/>이후에 다시 되살릴 경우 수업중복이 발생할 수 있으므로<br/>종료여부 재확인 해주세요.',
			function() {
				FormSubmit();
			}
		);
		/*
		if(ClassOrderPayStartDate=="") {
			UIkit.modal.confirm(
				//'수업의 시작일자가 비어있어,<br/> 잔여수업을 자동적으로 계산할 수 없습니다.<br/>수동으로 계산하여 진행해주세요.', 
				function(){ 
					FormSubmit();
				}
			);
			//UIkit.modal.alert("수업의 시작일자가 비어있어,<br/> 잔여수업을 자동적으로 계산할 수 없습니다.<br/>수동으로 계산하여 진행해주세요.");
		} else {
			FormSubmit();
		}
		*/

	} else {
		FormSubmit();
	}
}

function FormSubmit(){

	<?if ($ClassProductID==1){?>
	if (document.RegForm.ClassOrderState[2].checked && document.RegForm.ClassOrderEndDate.value == ""){
		UIkit.modal.alert("<?=$수강종료완료시_종료일을_반드시_입력해주세요[$LangID]?>");
		document.RegForm.ClassOrderEndDate.focus();
		return;	
	}
	<?}?>

	UIkit.modal.confirm(
		'<?=$저장_하시겠습니까[$LangID]?>?', 
		function(){ 
			document.RegForm.action = "class_order_action.php";
			document.RegForm.submit();
		}
	);

}

</script>


<?php
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>
