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
$MainMenuID = 13; 
$SubMenuID = 1306;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
?>



<?php
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$ClassTypeID = isset($_REQUEST["ClassTypeID"]) ? $_REQUEST["ClassTypeID"] : "";

if ($ClassTypeID!=""){

	$Sql = "
			select 
					A.* 
			from ClassTypes A 
			where A.ClassTypeID=:ClassTypeID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ClassTypeID', $ClassTypeID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$EduCenterID = $Row["EduCenterID"];
	$ClassTypeName = $Row["ClassTypeName"];
	$ClassTypeStudyMinute = $Row["ClassTypeStudyMinute"];
	$ClassTypePreviewMinute = $Row["ClassTypePreviewMinute"];
	$ClassTypeReviewMinute = $Row["ClassTypeReviewMinute"];
	$ClassTypeIntroText = $Row["ClassTypeIntroText"];
	$ClassTypeState = $Row["ClassTypeState"];
	$ClassTypeView = $Row["ClassTypeView"];

}else{
	$EduCenterID = "";
	$ClassTypeName = "";
	$ClassTypeStudyMinute = 0;
	$ClassTypePreviewMinute = 0;
	$ClassTypeReviewMinute = 0;
	$ClassTypeIntroText = "";
	$ClassTypeState = 1;
	$ClassTypeView = 1;
}

?>


<div id="page_content">
	<div id="page_content_inner">

		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="ClassTypeID" value="<?=$ClassTypeID?>">
		<input type="hidden" name="ListParam" value="<?=$ListParam?>">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<!--
						<div class="user_heading_avatar fileinput fileinput-new" data-provides="fileinput">
							<div class="fileinput-new thumbnail">
								<img src="assets/img/avatars/user.png" alt="user avatar"/>
							</div>
							<div class="fileinput-preview fileinput-exists thumbnail"></div>
							<div class="user_avatar_controls">
								<span class="btn-file">
									<span class="fileinput-new"><i class="material-icons">&#xE2C6;</i></span>
									<span class="fileinput-exists"><i class="material-icons">&#xE86A;</i></span>
									<input type="file" name="user_edit_avatar_control" id="user_edit_avatar_control">
								</span>
								<a href="#" class="btn-file fileinput-exists" data-dismiss="fileinput"><i class="material-icons">&#xE5CD;</i></a>
							</div>
						</div>
						-->
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$ClassTypeName?></span><span class="sub-heading" id="user_edit_position"><?=$수업구조정보[$LangID]?></span></h2>
						</div>
						<!--
						<div class="md-fab-wrapper">
							<div class="md-fab md-fab-toolbar md-fab-small md-fab-accent">
								<i class="material-icons">&#xE8BE;</i>
								<div class="md-fab-toolbar-actions">
									<button type="submit" id="user_edit_save" data-uk-tooltip="{cls:'uk-tooltip-small',pos:'bottom'}" title="Save"><i class="material-icons md-color-white">&#xE161;</i></button>
									<button type="submit" id="user_edit_print" data-uk-tooltip="{cls:'uk-tooltip-small',pos:'bottom'}" title="Print"><i class="material-icons md-color-white">&#xE555;</i></button>
									<button type="submit" id="user_edit_delete" data-uk-tooltip="{cls:'uk-tooltip-small',pos:'bottom'}" title="Delete"><i class="material-icons md-color-white">&#xE872;</i></button>
								</div>
							</div>
						</div>
						-->
					</div>
					<div class="user_content">
						
						<ul id="user_edit_tabs" class="uk-tab" data-uk-tab="{connect:'#user_edit_tabs_content'}" style="display:none;">
							<li class="uk-active"><a href="#">Basic</a></li>
							<li><a href="#">Groups</a></li>
							<li><a href="#">Todo</a></li>
						</ul>
						<ul id="user_edit_tabs_content" class="uk-switcher uk-margin">
							<li>
								<div class="uk-margin-top">
									<h3 class="full_width_in_card heading_c">
										<?=$수업구조명_및_수업시간[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-4-10" style="padding-top:7px;">
											<select id="EduCenterID" name="EduCenterID" class="uk-width-1-1" data-md-select2 data-allow-clear="true" data-placeholder="<?=$교육센터선택[$LangID]?>" style="width:100%;"/>
												<option value=""></option>
												<?
												$Sql2 = "select 
																A.*,
																B.FranchiseName 
														from EduCenters A 
															inner join Franchises B on A.FranchiseID=B.FranchiseID 
														where A.EduCenterState<>0 and B.FranchiseState<>0 
														order by A.EduCenterState asc, B.FranchiseName asc, A.EduCenterName asc";
												$Stmt2 = $DbConn->prepare($Sql2);
												$Stmt2->execute();
												$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
												
												$OldSelectEduCenterState = -1;
												while($Row2 = $Stmt2->fetch()) {
													$SelectEduCenterID = $Row2["EduCenterID"];
													$SelectEduCenterName = $Row2["EduCenterName"];
													$SelectEduCenterState = $Row2["EduCenterState"];
													$SelectFranchiseName = $Row2["FranchiseName"];
													if ($_LINK_ADMIN_LEVEL_ID_ <=2){
														$StrSelectFranchiseName = " (".$SelectFranchiseName.")";
													}else{
														$StrSelectFranchiseName = "";
													}
												
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

												<option value="<?=$SelectEduCenterID?>" <?if ($EduCenterID==$SelectEduCenterID){?>selected<?}?>><?=$SelectEduCenterName?><?=$StrSelectFranchiseName?></option>
												<?
												}
												$Stmt2 = null;
												?>
											</select>
										</div>
										<div class="uk-width-medium-3-10">
											<label for="ClassTypeName"><?=$수업구조명[$LangID]?></label>
											<input type="text" id="ClassTypeName" name="ClassTypeName" value="<?=$ClassTypeName?>" class="md-input label-fixed"/>
										</div>
										<div class="uk-width-medium-3-10 uk-input-group">
											<label for="ClassTypeStudyMinute"><?=$수업시간_분[$LangID]?></label>
											<input type="number" id="ClassTypeStudyMinute" name="ClassTypeStudyMinute" value="<?=$ClassTypeStudyMinute?>" class="md-input label-fixed allownumericwithoutdecimal" />
										</div>
									</div>
									<h3 class="full_width_in_card heading_c">
										<?=$예습_및_복습_시간[$LangID]?>
									</h3>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-medium-1-2 uk-input-group">
											<label for="ClassTypePreviewMinute"><?=$예습시간_분[$LangID]?></label>
											<input type="number" id="ClassTypePreviewMinute" name="ClassTypePreviewMinute" value="<?=$ClassTypePreviewMinute?>" class="md-input label-fixed allownumericwithoutdecimal" />
										</div>
										<div class="uk-width-medium-1-2 uk-input-group">
											<label for="ClassTypeReviewMinute"><?=$복습시간_분[$LangID]?></label>
											<input type="number" id="ClassTypeReviewMinute" name="ClassTypeReviewMinute" value="<?=$ClassTypeReviewMinute?>" class="md-input label-fixed allownumericwithoutdecimal" />
										</div>
									</div>
									<div class="uk-grid" data-uk-grid-margin>
										<div class="uk-width-1-1">
											<label for="ClassTypeIntroText"><?=$메모[$LangID]?></label>
											<textarea class="md-input" name="ClassTypeIntroText" id="ClassTypeIntroText" cols="30" rows="4"><?=$ClassTypeIntroText?></textarea>
										</div>
									</div>


								</div>
							</li>
							<li>

							</li>
							<li>

							</li>
						</ul>
					</div>
				</div>
			</div>
			<div class="uk-width-large-3-10">
				<div class="md-card">
					<div class="md-card-content">
						<h3 class="heading_c uk-margin-medium-bottom"><?=$기타설정[$LangID]?></h3>
						<div class="uk-form-row" style="display:none;">
							<input type="checkbox" id="ClassTypeView" name="ClassTypeView" value="1" <?php if ($ClassTypeView==1) { echo "checked";}?> data-switchery/>
							<label for="ClassTypeView" class="inline-label"><?=$활성화[$LangID]?></label>
						</div>
						<hr class="md-hr" style="display:none;">

						<div class="uk-form-row">
							<input type="checkbox" id="ClassTypeState" name="ClassTypeState" value="1" <?php if ($ClassTypeState==1) { echo "checked";}?> data-switchery/>
							<label for="ClassTypeState" class="inline-label"><?=$운영중[$LangID]?></label>
						</div>
						<hr class="md-hr">
						<div class="uk-form-row">
							<a type="button" href="javascript:FormSubmit();" class="md-btn md-btn-primary"><?=$저장하기[$LangID]?></a>
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



<!-- iOS에서는 position:fixed 버그가 있음, 적용하는 사이트에 맞게 position:absolute 등을 이용하여 top,left값 조정 필요 -->
<div id="layer" style="display:none;position:fixed;overflow:hidden;z-index:1;-webkit-overflow-scrolling:touch;">
<img src="//t1.daumcdn.net/postcode/resource/images/close.png" id="btnCloseLayer" style="cursor:pointer;position:absolute;right:-3px;top:-3px;z-index:1" onclick="closeDaumPostcode()" alt="닫기 버튼">
</div>

<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
<script>
    // 우편번호 찾기 화면을 넣을 element
    var element_layer = document.getElementById('layer');

    function closeDaumPostcode() {
        // iframe을 넣은 element를 안보이게 한다.
        element_layer.style.display = 'none';
    }

    function ExecDaumPostcode() {
        new daum.Postcode({
            oncomplete: function(data) {
                // 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

                // 각 주소의 노출 규칙에 따라 주소를 조합한다.
                // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
                var addr = ''; // 주소 변수
                var extraAddr = ''; // 참고항목 변수

                //사용자가 선택한 주소 타입에 따라 해당 주소 값을 가져온다.
                if (data.userSelectedType === 'R') { // 사용자가 도로명 주소를 선택했을 경우
                    addr = data.roadAddress;
                } else { // 사용자가 지번 주소를 선택했을 경우(J)
                    addr = data.jibunAddress;
                }

                // 사용자가 선택한 주소가 도로명 타입일때 참고항목을 조합한다.
                if(data.userSelectedType === 'R'){
                    // 법정동명이 있을 경우 추가한다. (법정리는 제외)
                    // 법정동의 경우 마지막 문자가 "동/로/가"로 끝난다.
                    if(data.bname !== '' && /[동|로|가]$/g.test(data.bname)){
                        extraAddr += data.bname;
                    }
                    // 건물명이 있고, 공동주택일 경우 추가한다.
                    if(data.buildingName !== '' && data.apartment === 'Y'){
                        extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                    }
                    // 표시할 참고항목이 있을 경우, 괄호까지 추가한 최종 문자열을 만든다.
                    if(extraAddr !== ''){
                        extraAddr = ' (' + extraAddr + ')';
                    }
                    // 조합된 참고항목을 해당 필드에 넣는다.
                    //document.getElementById("sample2_extraAddress").value = extraAddr;
                
                } else {
                    //document.getElementById("sample2_extraAddress").value = '';
                }

                // 우편번호와 주소 정보를 해당 필드에 넣는다.
                document.getElementById('ClassTypeZip').value = data.zonecode;
                document.getElementById("ClassTypeAddr1").value = addr;
                // 커서를 상세주소 필드로 이동한다.
                document.getElementById("ClassTypeAddr2").focus();

                // iframe을 넣은 element를 안보이게 한다.
                // (autoClose:false 기능을 이용한다면, 아래 코드를 제거해야 화면에서 사라지지 않는다.)
                element_layer.style.display = 'none';
            },
            width : '100%',
            height : '100%',
            maxSuggestItems : 5
        }).embed(element_layer);

        // iframe을 넣은 element를 보이게 한다.
        element_layer.style.display = 'block';

        // iframe을 넣은 element의 위치를 화면의 가운데로 이동시킨다.
        initLayerPosition();
    }

    // 브라우저의 크기 변경에 따라 레이어를 가운데로 이동시키고자 하실때에는
    // resize이벤트나, orientationchange이벤트를 이용하여 값이 변경될때마다 아래 함수를 실행 시켜 주시거나,
    // 직접 element_layer의 top,left값을 수정해 주시면 됩니다.
    function initLayerPosition(){
        var width = 300; //우편번호서비스가 들어갈 element의 width
        var height = 400; //우편번호서비스가 들어갈 element의 height
        var borderWidth = 5; //샘플에서 사용하는 border의 두께

        // 위에서 선언한 값들을 실제 element에 넣는다.
        element_layer.style.width = width + 'px';
        element_layer.style.height = height + 'px';
        element_layer.style.border = borderWidth + 'px solid';
        // 실행되는 순간의 화면 너비와 높이 값을 가져와서 중앙에 뜰 수 있도록 위치를 계산한다.
        element_layer.style.left = (((window.innerWidth || document.documentElement.clientWidth) - width)/2 - borderWidth) + 'px';
        element_layer.style.top = (((window.innerHeight || document.documentElement.clientHeight) - height)/2 - borderWidth) + 'px';
    }
</script>


<script language="javascript">

function FormSubmit(){


	obj = document.RegForm.EduCenterID;
	if (obj.value==""){
		UIkit.modal.alert("<?=$소속_교육센터를_선택하세요[$LangID]?>");
		obj.focus();
		return;
	}

	obj = document.RegForm.ClassTypeName;
	if (obj.value==""){
		UIkit.modal.alert("<?=$수업구조명을_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}

	UIkit.modal.confirm(
		'<?=$저장_하시겠습니까[$LangID]?>?', 
		function(){ 
			document.RegForm.action = "class_type_action.php";
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