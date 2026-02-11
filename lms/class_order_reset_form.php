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
    <style>
        .radio_input:disabled + .radio_label {
            color: #a0a0a0;
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>

</head>

<?php
// --- 삭제 권한 여부 판단 --------------------------------------------- // 수정
$canDelete = false;

// 로그인 ID·MemberID 는 프로젝트에 맞게 읽어오세요.
$loginID  = isset($_LINK_ADMIN_LOGIN_ID_)   ? $_LINK_ADMIN_LOGIN_ID_   : '';
$loginMID = isset($_LINK_ADMIN_MEMBER_ID_)  ? $_LINK_ADMIN_MEMBER_ID_  : 0;

if ($loginID === '장지웅1' || $loginID === 'maiskd' || $loginMID == 22055) {
    $canDelete = true;
}

//if ($loginID === '정우영1' || $loginMID == 22050) {
//    $canDelete = true;
//}
//
//if ($loginID === 'master' || $loginMID == 1) {
//    $canDelete = true;
//}

?>

<body class="disable_transitions sidebar_main_open sidebar_main_swipe" style="padding-top:0px;">
<?php
$ClassMemberTypeGroupID = isset($_REQUEST["ClassMemberTypeGroupID"]) ? $_REQUEST["ClassMemberTypeGroupID"] : "";
$ClassOrderID = isset($_REQUEST["ClassOrderID"]) ? $_REQUEST["ClassOrderID"] : "";
$IframeMode = isset($_REQUEST["IframeMode"]) ? $_REQUEST["IframeMode"] : "0";

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

}
?>

<div id="page_content">
	<div id="page_content_inner">



		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="ClassMemberTypeGroupID" value="<?=$ClassMemberTypeGroupID?>">
		<input type="hidden" name="ClassOrderID" value="<?=$ClassOrderID?>">
		<input type="hidden" name="ClassProductID" value="<?=$ClassProductID?>">
		<input type="hidden" name="IframeMode" value="<?=$IframeMode?>">
		
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$설정변경[$LangID]?></span><span class="sub-heading" id="user_edit_position"><?=$그룹수업의_경우_일괄변경됩니다[$LangID]?></span></h2>
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

								</div>
							</div>
						</div>
						


						<div id="TrClassMemberType" class="uk-margin-top" style="margin-top:20px;display:<?if ($ClassProductID==2){?>none<?}?>;" >
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-2-10">
								<?=$수업타입[$LangID]?>
								</div>
								<div class="uk-width-medium-8-10">
									<?
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
									?>
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
								<span style='color:#ff0000;'>※ 1:2 이상 그룹수업의 경우 일괄 변경됩니다.(종료일, 수업상태)</span>
								<br>
								<span style='color:#ff0000;'>※ 각각 변경해야 할경우 수강신청관리에서 변경해 주시기 바랍니다.</span>
								<br>
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
									<?if ($ClassOrderState==3){?>
										종료된 수업 : 종료된 수업의 변경은 수강신청 관리에서만 가능합니다.
									<?}else{?>
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
                                            <input type="radio" class="radio_input"
                                                   name="ClassOrderState" id="ClassOrderState0" value="0"
                                                       <?php if(!$canDelete) echo 'disabled'; ?>
                                                        <?php if($ClassOrderState==0) echo 'checked'; ?>
                                                />
                                                <label for="ClassOrderState0" class="radio_label">
                                                    <span class="radio_bullet"></span><?=$삭제[$LangID]?>
                                                </label>
                                        </span>
									<?}?>
								</div>
							</div>
                            <p style="color:#ff0000;margin-top:8px;">
                                수업 삭제 기능 사용이 제한되었습니다. 필요 시, 관리자에게 문의해 주시기 바랍니다.
                            </p>
						</div>


	
						<div class="uk-margin-top" style="text-align:center;padding-top:30px;">
							<a type="button" href="javascript:FormSubmit();" class="md-btn md-btn-primary"><?=$수정하기[$LangID]?></a>
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
	UIkit.modal.alert("※수강 종료 안내<br/><br/>강사는 다른 수업을 배정받을 수 있으며,<br/>이후에 다시 되살릴 경우 수업중복이 발생할 수 있으므로<br/>종료여부 재확인 해주세요.");
}

function FormSubmit(){

	<?if ($ClassProductID==1){?>
	if (document.RegForm.ClassOrderState[2].checked && document.RegForm.ClassOrderEndDate.value == ""){
		UIkit.modal.alert("수강 종료완료시 종료일을 반드시 입력해 주세요.");
		document.RegForm.ClassOrderEndDate.focus();
		return;	
	}
	<?}?>

	UIkit.modal.confirm(
		'</span><?=$저장_하시겠습니까[$LangID]?>?', 
		function(){ 
			document.RegForm.action = "class_order_reset_action.php";
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
