<?php
include_once('../includes/dbopen.php');
//include_once('../includes/member_check.php');
include_once('../includes/common.php');
?>
<!DOCTYPE html>
<html>
<head>
<?
include_once('./includes/common_meta_tag.php');
include_once('./inc_header.php');
?>
</head>
<body>

<div class="views">
    <div class="view view-main">

        <div class="pages navbar-fixed toolbar-fixed ">
            <div data-page="member_form" class="page">

<?

$AddSqlWhereSearchCenter = "1=1";
$AddSqlWhereSearchCenter = $AddSqlWhereSearchCenter . " and OnlineSiteID=$OnlineSiteID ";
$AddSqlWhereSearchCenter = $AddSqlWhereSearchCenter . " and CenterState=1 ";
$AddSqlWhereSearchCenter = $AddSqlWhereSearchCenter . " and CenterView=1 ";

if($DomainSiteID==0) { // 본사
	$AddSqlWhereSearchCenter = $AddSqlWhereSearchCenter . " and C.BranchGroupID!=20 and C.BranchGroupID!=18 and A.CenterID!=93 and  A.CenterID!=156 and A.CenterID!=157 and ( C.BranchGroupID!=19 and B.BranchID!=29 ) and ( C.BranchGroupID!=19 and B.BranchID!=31 ) and ( C.BranchGroupID!=10 and B.BranchID!=38 ) and ( C.BranchGroupID!=19 and B.BranchID!=154 ) ";
} else if($DomainSiteID==1) { // SLP
	$AddSqlWhereSearchCenter = $AddSqlWhereSearchCenter . " and C.BranchGroupID=18 ";
} else if($DomainSiteID==2) { // EIE
	$AddSqlWhereSearchCenter = $AddSqlWhereSearchCenter . " and C.BranchGroupID=19 and B.BranchID=29 ";
} else if($DomainSiteID==3) { // DREAM
	$AddSqlWhereSearchCenter = $AddSqlWhereSearchCenter . " and C.BranchGroupID=19 and B.BranchID=31 ";
} else if($DomainSiteID==6) { // HI
	$AddSqlWhereSearchCenter = $AddSqlWhereSearchCenter . " and B.BranchID=178 ";
}

$Sql = "select count(*) as TotalRowCount from Centers A 
			inner join Branches B on A.BranchID=B.BranchID 
			inner join BranchGroups C on B.BranchGroupID=C.BranchGroupID 
			where ".$AddSqlWhereSearchCenter;
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$CenterTotalRowCount = $Row["TotalRowCount"];



$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$MemberInviteID = 0;
$MemberInviteLoginID = "";
$BranchID = 0;
$CenterID = 0;
$MemberLevelID = 19;
$MemberName = "";
$MemberNickName = "";
$MemberParentName = "";
$MemberSex = 1; 

$MemberPhone1_1 = "";
$MemberPhone1_2 = "";
$MemberPhone1_3 = "";
$MemberPhone1Agree = 1;

$MemberPhone2_1 = "";
$MemberPhone2_2 = "";
$MemberPhone2_3 = "";
$MemberPhone2Agree = 1;

$MemberEmail2_1 = "";
$MemberEmail2_2 = "";
$MemberEmail2Agree = 1;

$MemberBirthday = "";
$SchoolName = ""; 
$MemberZip = ""; 
$MemberAddr1 = ""; 
$MemberAddr2 = ""; 
$MemberPhoto = ""; 
$MemberState = 1;
$CheckedID = 0;
$CheckedEmail = 0;
$MemberStudyAlarmTime = 30;
$MemberStudyAlarmType = 1;
$MemberChangeTeacher = 1;




$TempCenterID = "";
$TempCenterName = "소속단체선택";

if($CenterTotalRowCount==1) {

	$Sql = "select * from Centers A 
				inner join Branches B on A.BranchID=B.BranchID 
				inner join BranchGroups C on B.BranchGroupID=C.BranchGroupID 
				where $AddSqlWhereSearchCenter ";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$TempCenterID = $Row["CenterID"];
	$TempCenterName = $Row["CenterName"];

}


if($DomainSiteID==0) {
	$CenterID = 140;
	$CenterName = "소속단체없음";
}else{
	$CenterID = $TempCenterID;
	$CenterName = $TempCenterName;
}


$_MEMBER_LOGIN_ID_ = "";
?>
	<!-- 헤더 영역 -->
	<div class="header_wrap">
		<h1 class="header_title TrnTag">회원정보</h1>
		<a href="javascript:window.Exit=true" class="header_close gray"><img src="images/btn_close_black.png" class="icon"></a>
	</div>


    <div class="view another-view navbar-fixed bg_gray_2">
        <!-- Pages -->
        <div class="pages">
            <div class="page no-toolbar" data-page="intro_mypage">
                <div class="page-content navbar-fix">
					<section class="member_area">
						<form name="RegForm" method="post" autocomplete="off">
						<input type="hidden" name="MemberID" value="<?=$MemberID?>">
						<input type="hidden" name="CheckedID" value="<?=$CheckedID?>">
						<input type="hidden" name="MemberName" value="<?=$MemberName?>">
						<input type="hidden" name="CheckedEmail" value="<?=$CheckedEmail?>">
						<input type="hidden" name="CheckedEmail" value="<?=$CheckedEmail?>">
						<input type="hidden" name="MemberLevelID" value="<?=$MemberLevelID?>">
						<input type="hidden" name="MemberLoginPW" value="<?=$MemberLoginPW?>">

						<h3 class="member_join_caption TrnTag">추가정보 입력<span><b class="member_red">★</b> 표시는 필수 입력사항입니다.</span></h3>
						<table class="member_table">
							<tr>
								<th class="TrnTag">영문표기이름 <b class="member_red">★</b></th>
								<td>
									<input type="email" name="MemberNickName" id="MemberNickName" class="member_common" value="<?=$MemberNickName?>">
									<!--<div class="member_idpw_text">예) 홍길동  ▷  Hong Gil Dong</div>-->
								</td>
							</tr>
							<tr>
								<th class="TrnTag">학교명 </th>
								<td>
									<input type="text" id="SchoolName" name="SchoolName" class="member_common" value="<?=$SchoolName?>"/>
								</td>
							</tr>
							<tr>
								<th class="TrnTag">성별 <b class="member_red">★</b></th>
								<td class="radio_wrap sex">
									<input type="radio" id="sex_1" class="input_radio" name="MemberSex" value="1" <?if ($MemberSex==1) {?>checked<?}?> checked><label class="label TrnTag" for="sex_1"><span class="bullet_radio"></span>남</label>
									<input type="radio" id="sex_2" class="input_radio" name="MemberSex" value="2" <?if ($MemberSex==2) {?>checked<?}?> ><label class="label TrnTag" for="sex_2"><span class="bullet_radio"></span>여</label>
								</td>
							</tr>
							<tr>
								<th class="TrnTag">생년월일 <b class="member_red">★</b></th>
								<td>
									<input type="date" id="MemberBirthday" name="MemberBirthday" class="member_common" value="<?=$MemberBirthday?>" style="border:0px;">
								</td>
							</tr>

							<tr>
								<th class="TrnTag">휴대폰 <b class="member_red">★</b></th>
								<td>
									<select name="MemberPhone1_1" class="member_select_1">
										<option value="010" <?If ($MemberPhone1_1=="010") {?>selected<?}?> >010</option>
										<option value="011" <?If ($MemberPhone1_1=="011") {?>selected<?}?> >011</option>
										<option value="016" <?If ($MemberPhone1_1=="016") {?>selected<?}?> >016</option>
										<option value="017" <?If ($MemberPhone1_1=="017") {?>selected<?}?> >017</option>
										<option value="018" <?If ($MemberPhone1_1=="018") {?>selected<?}?> >018</option>
										<option value="019" <?If ($MemberPhone1_1=="019") {?>selected<?}?> >019</option>
										<option value="070" <?If ($MemberPhone1_1=="070") {?>selected<?}?> >070</option>
										<option value="02" <?If ($MemberPhone1_1=="02") {?>selected<?}?>   >02</option>
										<option value="031" <?If ($MemberPhone1_1=="031") {?>selected<?}?> >031</option>
										<option value="032" <?If ($MemberPhone1_1=="032") {?>selected<?}?> >032</option>
										<option value="033" <?If ($MemberPhone1_1=="033") {?>selected<?}?> >033</option>
										<option value="041" <?If ($MemberPhone1_1=="041") {?>selected<?}?> >041</option>
										<option value="042" <?If ($MemberPhone1_1=="042") {?>selected<?}?> >042</option>
										<option value="043" <?If ($MemberPhone1_1=="043") {?>selected<?}?> >043</option>
										<option value="044" <?If ($MemberPhone1_1=="044") {?>selected<?}?> >044</option>
										<option value="049" <?If ($MemberPhone1_1=="049") {?>selected<?}?> >049</option>
										<option value="051" <?If ($MemberPhone1_1=="051") {?>selected<?}?> >051</option>
										<option value="052" <?If ($MemberPhone1_1=="052") {?>selected<?}?> >052</option>
										<option value="053" <?If ($MemberPhone1_1=="053") {?>selected<?}?> >053</option>
										<option value="054" <?If ($MemberPhone1_1=="054") {?>selected<?}?> >054</option>
										<option value="055" <?If ($MemberPhone1_1=="055") {?>selected<?}?> >055</option>
										<option value="061" <?If ($MemberPhone1_1=="061") {?>selected<?}?> >061</option>
										<option value="062" <?If ($MemberPhone1_1=="062") {?>selected<?}?> >062</option>
										<option value="063" <?If ($MemberPhone1_1=="063") {?>selected<?}?> >063</option>
										<option value="064" <?If ($MemberPhone1_1=="064") {?>selected<?}?> >064</option>
									</select>
									<span class="member_space">-</span>
									<input type="number" name="MemberPhone1_2" class="member_input_small" value="<?=$MemberPhone1_2?>" numberonly="true" maxlength="4">
									<span class="member_space">-</span>
									<input type="number" name="MemberPhone1_3" class="member_input_small" value="<?=$MemberPhone1_3?>" numberonly="true" maxlength="4">
									<div class="member_idpw_text check_wrap agree" style="display:none;"><input type="checkbox" id="MemberPhone1Agree" class="input_check" value="1" <?if ($MemberPhone1Agree==1) {?>checked<?}?> name="MemberPhone1Agree"> <label class="label TrnTag" for="MemberPhone1Agree"><span class="bullet_check "></span>SMS 수신동의</label></div>
								</td>
							</tr>
							<tr style="display:none;">
								<th>주소 <b class="member_red">★</b></th>
								<td>
									<input type="text" name="MemberZip" id="MemberZip" class="member_input_zip" value="<?=$MemberZip?>" >
									<a href="javascript:ExecDaumPostcode();" class="member_btn_confirm_2 TrnTag" id="BtnCheckID3">우편번호</a>
									<input type="text" name="MemberAddr1" id="MemberAddr1" value="<?=$MemberAddr1?>" class="member_common mameber_margin">
									<input type="text" name="MemberAddr2" id="MemberAddr2" value="<?=$MemberAddr2?>" class="member_common" placeholder="나머지 주소" >
								</td>
							</tr>

							<tr>
								<th class="TrnTag">소속단체명 <b class="member_red">★</b></th>
								<td>
									
									<input type="hidden" name="CenterID" value="<?=$CenterID?>">
									<input type="text" name="CenterName" id="CenterName" value="<?=$CenterName?>" class="member_input_zip" readonly style="width:50%;margin-right:4px;">

									<?if($CenterTotalRowCount>1) { ?>
										<a href="javascript:SearchCenter();" class="member_btn_confirm_2 TrnTag">검색</a>
									<?}?>

									<?if($DomainSiteID==0) { ?>
									<div class="member_idpw_text TrnTag">단체 소속이 아닌 일반회원은 소속단체 없음을 선택해 주세요.</div>
									<?}?>
									<!--
									<input type="text" class="member_common red" placeholder="사전에 받으신 쿠폰이 있는 경우 입력해 주세요.">
									-->
								</td>
							</tr>
							<tr>
								<th class="TrnTag">추천인ID</th>
								<td>
									<?
									if($MemberInviteID) {
										$Sql2 = "select A.MemberLoginID from Members A where A.MemberID=:MemberInviteID";
										$Stmt2 = $DbConn->prepare($Sql2);
										$Stmt2->bindParam(':MemberInviteID', $MemberInviteID);
										$Stmt2->execute();
										$Row2 = $Stmt2->fetch();
										$MemberInviteLoginID = $Row["MemberLoginID"];
										$Stmt2 = null;
									}
									?>
									<input type="hidden" name="MemberInviteID" id="MemberInviteID" value="<?=$MemberInviteID?>">
									<input type="text" name="MemberInviteLoginID" id="MemberInviteLoginID" value="<?=$MemberInviteLoginID?>" class="member_input_zip" readonly style="width:50%;margin-right:4px;">

									<a href="javascript:SearchInviteMember();" class="member_btn_confirm_2 TrnTag">검색</a>


									<!--
									<input type="text" class="member_common red" placeholder="사전에 받으신 쿠폰이 있는 경우 입력해 주세요.">
									-->
								</td>
							</tr>
						</table>

						<h3 class="member_join_caption TrnTag">수업정보, 상담정보, 기타 부가정보</h3>
						<table class="member_table" style="display:none;">
							<tr>
								<th class="TrnTag">수업 전 알림</th>
								<td>
									<select class="member_select_2 parent" id="MemberStudyAlarmTime" name="MemberStudyAlarmTime">
										<option value="10" <?if ($MemberStudyAlarmTime==10){?>selected<?}?> class="TrnTag">10분전</option>
										<option value="30" <?if ($MemberStudyAlarmTime==30){?>selected<?}?> class="TrnTag">30분전</option>
										<option value="60" <?if ($MemberStudyAlarmTime==60){?>selected<?}?> class="TrnTag">1시간전</option>
									</select>
									<div class="radio_wrap alram" style="display:none;">
										<input type="radio" id="MemberStudyAlarmType_1" class="input_radio" value="1" name="MemberStudyAlarmType" <?if ($MemberStudyAlarmType==1){?>checked<?}?> checked><label class="label TrnTag" for="MemberStudyAlarmType_1"><span class="bullet_radio"></span>수신</label>
										<input type="radio" id="MemberStudyAlarmType_2" class="input_radio" value="2" name="MemberStudyAlarmType" <?if ($MemberStudyAlarmType==2){?>checked<?}?> ><label class="label TrnTag" for="MemberStudyAlarmType_2"><span class="bullet_radio"></span>거부</label>
									</div>
								</td>
							</tr>
							<tr>
								<th class="TrnTag">강사 대체 정책</th>
								<td class="radio_wrap teacher">
									<input type="radio" id="MemberChangeTeacher_1" class="input_radio" value="1" name="MemberChangeTeacher" <?if ($MemberChangeTeacher==1){?>checked<?}?> checked><label class="label TrnTag" for="MemberChangeTeacher_1"><span class="bullet_radio"></span>다른 강사로 대체</label>
									<input type="radio" id="MemberChangeTeacher_2" class="input_radio" value="2" name="MemberChangeTeacher" <?if ($MemberChangeTeacher==2){?>checked<?}?> ><label class="label TrnTag" for="MemberChangeTeacher_2"><span class="bullet_radio"></span>강사 대체없이 수업 취소</label>
								</td>
							</tr>
							<!--
							<tr>
								<th>자동등록방지 <b class="member_red">★</b></th>
								<td>
									<i class="member_auto_prevention">fd7d</i>
									<input type="text" class="member_input_auto">
									<div class="member_idpw_text">자동등록방지 코드를 입력하세요.</div>
								</td>
							</tr>
							-->
						</table>


						<h3 class="member_join_caption TrnTag" style="display:none;">보호자 정보사항<span>성인의 경우 입력하지 않으셔도 됩니다.</span></h3>
						<table class="member_table" style="display:none;">
							<tr>
								<th class="TrnTag">보호자 성명 <b class="member_red">★</b></th>
								<td>
									<input type="email" name="MemberParentName" id="MemberParentName" class="member_common" value="<?=$MemberParentName?>">
									<div class="member_idpw_text"><!--무통장 입금 시 입금자명--></div>
								</td>
							</tr>
							<tr>
								<th class="TrnTag">연락처 <b class="member_red">★</b></th>
								<td>
									<select name="MemberPhone2_1" class="member_select_1">
										<option value="010" <?If ($MemberPhone2_1=="010") {?>selected<?}?>>010</option>
										<option value="011" <?If ($MemberPhone2_1=="011") {?>selected<?}?>>011</option>
										<option value="016" <?If ($MemberPhone2_1=="016") {?>selected<?}?>>016</option>
										<option value="017" <?If ($MemberPhone2_1=="017") {?>selected<?}?>>017</option>
										<option value="018" <?If ($MemberPhone2_1=="018") {?>selected<?}?>>018</option>
										<option value="019" <?If ($MemberPhone2_1=="019") {?>selected<?}?>>019</option>
										<option value="070" <?If ($MemberPhone2_1=="070") {?>selected<?}?>>070</option>
										<option value="02" <?If ($MemberPhone2_1=="02") {?>selected<?}?>>02</option>
										<option value="031" <?If ($MemberPhone2_1=="031") {?>selected<?}?>>031</option>
										<option value="032" <?If ($MemberPhone2_1=="032") {?>selected<?}?>>032</option>
										<option value="033" <?If ($MemberPhone2_1=="033") {?>selected<?}?>>033</option>
										<option value="041" <?If ($MemberPhone2_1=="041") {?>selected<?}?>>041</option>
										<option value="042" <?If ($MemberPhone2_1=="042") {?>selected<?}?>>042</option>
										<option value="043" <?If ($MemberPhone2_1=="043") {?>selected<?}?>>043</option>
										<option value="044" <?If ($MemberPhone2_1=="044") {?>selected<?}?>>044</option>
										<option value="049" <?If ($MemberPhone2_1=="049") {?>selected<?}?>>049</option>
										<option value="051" <?If ($MemberPhone2_1=="051") {?>selected<?}?>>051</option>
										<option value="052" <?If ($MemberPhone2_1=="052") {?>selected<?}?>>052</option>
										<option value="053" <?If ($MemberPhone2_1=="053") {?>selected<?}?>>053</option>
										<option value="054" <?If ($MemberPhone2_1=="054") {?>selected<?}?>>054</option>
										<option value="055" <?If ($MemberPhone2_1=="055") {?>selected<?}?>>055</option>
										<option value="061" <?If ($MemberPhone2_1=="061") {?>selected<?}?>>061</option>
										<option value="062" <?If ($MemberPhone2_1=="062") {?>selected<?}?>>062</option>
										<option value="063" <?If ($MemberPhone2_1=="063") {?>selected<?}?>>063</option>
										<option value="064" <?If ($MemberPhone2_1=="064") {?>selected<?}?>>064</option>
									</select>
									<span class="member_space">-</span>
									<input type="text" name="MemberPhone2_2" class="member_input_small"  value="<?=$MemberPhone2_2?>" numberonly="true" maxlength="4">
									<span class="member_space">-</span>
									<input type="text" name="MemberPhone2_3" class="member_input_small"  value="<?=$MemberPhone2_3?>" numberonly="true" maxlength="4">
									<div class="member_idpw_text check_wrap agree" style="display:none;"><input type="checkbox" id="MemberPhone2Agree" class="input_check" value="1" <?if ($MemberPhone2Agree==1) {?>checked<?}?> name="MemberPhone2Agree"><label class="label TrnTag" for="MemberPhone2Agree"><span class="bullet_check"></span>SMS 수신동의</label></div>
								</td>
							</tr>
						</table>
						<div class="TrnTag">회원 필수정보 업데이트(최초 1회)</div>
						</form>
						<div class="button_wrap flex_justify member">
							<a href="javascript:FormSubmit();" class="btn_yellow_black TrnTag">저장하기</a>
							<a href="#" class="btn_br_black mantoman close-popup TrnTag" onclick="javascript:window.Exit=true">취소하기</a>
						</div>
						<br><br><br><br>
					</section>
				</div>


            </div>
        </div>

    </div>
</div>



<!-- iOS에서는 position:fixed 버그가 있음, 적용하는 사이트에 맞게 position:absolute 등을 이용하여 top,left값 조정 필요 -->
<div id="layer" style="padding-top:20px;display:none;position:fixed;overflow:hidden;z-index:100000;-webkit-overflow-scrolling:touch;background-color:#ffffff;">
<!--
<div id="layer" style="display:none;position:fixed;overflow:hidden;z-index:1;-webkit-overflow-scrolling:touch;">
-->
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
				document.getElementById('MemberZip').value = data.zonecode;
				document.getElementById("MemberAddr1").value = addr;
				// 커서를 상세주소 필드로 이동한다.
				//document.getElementById("sample2_detailAddress").focus();

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




<script>

function ParentSetEmailName(){
	var MemberEmail_3 = document.RegForm.MemberEmail2_3.value;
	var MemberEmail_2 = document.RegForm.MemberEmail2_2;

	if (MemberEmail_3==""){
		MemberEmail_2.value = "";
		MemberEmail_2.readOnly = false;
	}else{
		MemberEmail_2.value = MemberEmail_3;
		MemberEmail_2.readOnly = true;
	}

	EnNewEmail();
}

</script>



<script language="javascript">

function SearchInviteMember() {
		openurl = "../pop_search_invite_member_form.php";
		$.colorbox({	
			href:openurl
			,width:"95%"
			,height:"95%"
			,maxWidth:"800" 
			,maxHeight:"710"
			,title:""
			,iframe:true 
			,scrolling:true
			//,onClosed:function(){location.reload(true);}   
		});
}

function SearchCenter() {
		openurl = "../pop_search_center_form.php";
		$.colorbox({	
			href:openurl
			,width:"80%" 
			,height:"80%"
			,title:""
			,iframe:true 
			,scrolling:true
			//,onClosed:function(){location.reload(true);}   
		});
}

function FormSubmit(){

	MemberLevelID = document.RegForm.MemberLevelID.value;

	obj = document.RegForm.MemberNickName;
	if (obj.value==""){
		$.alertable.alert('영문표기이름을 입력하세요.').always(function() { });
		obj.focus();
		return;
	}

	obj = document.RegForm.MemberBirthday;
	if (obj.value==""){
		$.alertable.alert('생년월일을 입력하세요.').always(function() { });
		obj.focus();
		return;
	}

	obj = document.RegForm.MemberPhone1_2;
	if (obj.value==""){
		$.alertable.alert('휴대폰 번호를 입력하세요.').always(function() { });
		obj.focus();
		return;
	}

	obj = document.RegForm.MemberPhone1_3;
	if (obj.value==""){
		$.alertable.alert('연락처 번호를 입력하세요.').always(function() { });
		obj.focus();
		return;
	}

	obj = document.RegForm.CenterID;
	if (obj.value==""){
		alert('소속단체를 선택해 주세요.');
		obj.focus();
		return;
	}
	/*
	obj = document.RegForm.MemberZip;
	if (obj.value==""){
		$.alertable.alert('우편번호를 선택해주세요').always(function() { });
		obj.focus();
		return;
	}

	obj = document.RegForm.MemberAddr1;
	if (obj.value==""){
		$.alertable.alert('주소를 입력해주세요.').always(function() { });
		obj.focus();
		return;
	}

	obj = document.RegForm.MemberAddr2;
	if (obj.value==""){
		$.alertable.alert('상세 주소를 입력해주세요.').always(function() { });
		obj.focus();
		return;
	}

	obj = document.RegForm.MemberParentName;
	if (obj.value==""){
		$.alertable.alert('보호자 이름을 입력해주세요.').always(function() { });
		obj.focus();
		return;
	}

	obj = document.RegForm.MemberPhone2_2;
	if (obj.value==""){
		$.alertable.alert('보호자 연락처를 입력해주세요.').always(function() { });
		obj.focus();
		return;
	}

	obj = document.RegForm.MemberPhone2_3;
	if (obj.value==""){
		$.alertable.alert('보호자 연락처를 입력해주세요.').always(function() { });
		obj.focus();
		return;
	}

	obj = document.RegForm.MemberEmail2_1;
	if (obj.value==""){
		$.alertable.alert('보호자 이메일을 입력해주세요.').always(function() { });
		obj.focus();
		return;
	}

	obj = document.RegForm.MemberEmail2_2;
	if (obj.value==""){
		$.alertable.alert('보호자 이메일을 입력해주세요.').always(function() { });
		obj.focus();
		return;
	}

	*/


	
	<?if ($MemberID != ""){?>
		AlertMsg = "회원정보를 수정하시겠습니까?";
	<?}else{?>
		AlertMsg = "회원가입을 진행하시겠습니까?";
	<?}?>


	$.alertable.confirm(AlertMsg).then(function() {
		document.RegForm.action = "member_action_sns.php"
		document.RegForm.submit();
	}, function() {
	
	
	});

}

function FormSubmitEn(){
	if (event.keyCode == 13){
		FormSubmit();
	}
}

</script>


<?
include_once('./inc_footer.php');
?>
</body>
</html>