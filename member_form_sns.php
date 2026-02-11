<?php
include_once('./includes/dbopen.php');
include_once('./includes/common.php');
include_once('./includes/member_check.php');

$UseMain = 0;
$UseSub = 1;

$SubCode = "sub_08";
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
<link href="css/sub_style.css" rel="stylesheet" type="text/css" />
<?php
include_once('./includes/common_header.php');

$Sql = "select SubID from Subs where SubCode=:SubCode";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':SubCode', $SubCode);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$SubID = $Row["SubID"];


if ($UseMain==1){
	$Sql = "select * from Main limit 0,1";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$MainLayout = $Row["MainLayout"];
	$MainLayoutCss = $Row["MainLayoutCss"];
	$MainLayoutJavascript = $Row["MainLayoutJavascript"];
	list($MainLayoutTop, $MainLayoutBottom) = explode("{{Page}}", $MainLayout);
}else{
	$MainLayoutTop = "";
	$MainLayoutBottom = "";
	$MainLayoutCss = "";
	$MainLayoutJavascript = "";
}


if ($UseSub==1){
	$Sql = "select * from Subs where SubID=:SubID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':SubID', $SubID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$SubLayout = $Row["SubLayout"];
	$SubLayoutCss = $Row["SubLayoutCss"];
	$SubLayoutJavascript = $Row["SubLayoutJavascript"];
	list($SubLayoutTop, $SubLayoutBottom) = explode("{{Page}}", $SubLayout);
}else{
	$SubLayoutTop = "";
	$SubLayoutBottom = "";
	$SubLayoutCss = "";
	$SubLayoutJavascript = "";
}


if (trim($MainLayoutCss)!=""){
	echo "\n";
	echo "<style>";
	echo "\n";
	echo $MainLayoutCss;
	echo "\n";
	echo "</style>";
	echo "\n";
}

if (trim($SubLayoutCss)!=""){
	echo "\n";
	echo "<style>";
	echo "\n";
	echo $SubLayoutCss;
	echo "\n";
	echo "</style>";
	echo "\n";
}


?>
</head>
<body>
<?
include_once('./includes/common_body_top.php');
?>
<?php
// $MainLayoutTop = convertHTML(trim($MainLayoutTop));
// $SubLayoutTop = convertHTML(trim($SubLayoutTop));
// $SubLayoutBottom = convertHTML(trim($SubLayoutBottom));
// $MainLayoutBottom = convertHTML(trim($MainLayoutBottom));

if($DomainSiteID==7){
    $MainLayoutTop = convertHTML(trim("{{Piece(header_gumiivyleague)}}"));
    $SubLayoutTop = convertHTML(trim("{{Piece(sub_08_gumiivyleague)}}"));
    $SubLayoutBottom = convertHTML(trim("{{Piece(sub_01_gumiivyleague_bottom)}}"));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_gumiivyleague)}}"));

} else if($DomainSiteID==8){ //engliseed.kr
    $MainLayoutTop = convertHTML(trim("{{Piece(header_engliseed)}}"));
    $SubLayoutTop = convertHTML(trim("{{Piece(sub_06_engliseed)}}"));
    $SubLayoutBottom = convertHTML(trim("{{Piece(sub_01_engliseed_bottom)}}"));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_engliseed)}}"));

} else if($DomainSiteID==9){ //live.engedu.kr
    $MainLayoutTop = convertHTML(trim("{{Piece(header_engedu)}}"));
    $SubLayoutTop = convertHTML(trim("{{Piece(sub_06_engedu)}}"));
    $SubLayoutBottom = convertHTML(trim("{{Piece(sub_01_engedu_bottom)}}"));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_engedu)}}"));


} else {
    $MainLayoutTop = convertHTML(trim($MainLayoutTop));
    $SubLayoutTop = convertHTML(trim($SubLayoutTop));
    $SubLayoutBottom = convertHTML(trim($SubLayoutBottom));
    $MainLayoutBottom = convertHTML(trim($MainLayoutBottom));
  }
echo "\n";
echo $MainLayoutTop;
echo "\n";
echo $SubLayoutTop;
echo "\n";
?>


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



if ($_LINK_MEMBER_ID_!="") {

	$Sql = "select 
		A.BranchID,
		A.CenterID,
		A.MemberID,
		A.MemberLoginType,
		A.MemberLevelID,
		A.MemberLoginPW,
		A.MemberLoginID,
		A.MemberName,
		A.MemberNickName,
		A.MemberParentName,
		A.MemberSex,
		AES_DECRYPT(UNHEX(A.MemberPhone1),:EncryptionKey) as MemberPhone1,
		A.MemberPhone1Agree,
		AES_DECRYPT(UNHEX(A.MemberPhone2),:EncryptionKey) as MemberPhone2,
		A.MemberPhone2Agree,
		AES_DECRYPT(UNHEX(A.MemberPhone3),:EncryptionKey) as MemberPhone3,
		AES_DECRYPT(UNHEX(A.MemberEmail),:EncryptionKey) as MemberEmail,
		A.MemberEmailAgree,
		AES_DECRYPT(UNHEX(A.MemberEmail2),:EncryptionKey) as MemberEmail2,
		A.MemberEmail2Agree,

		MemberBirthday,
		SchoolName,
		MemberZip,
		MemberAddr1,
		MemberAddr2, 

		A.MemberPhoto,
		A.MemberState,
		A.WithdrawalText,
		A.MemberStateText,

		ifnull(B.CenterName,'미등록') as CenterName,

		A.MemberStudyAlarmTime,
		A.MemberStudyAlarmType,
		A.MemberChangeTeacher
		from Members A left outer join Centers B on A.CenterID=B.CenterID where A.MemberID=:_MEMBER_ID_";
	
	

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
	$Stmt->bindParam(':_MEMBER_ID_', $_LINK_MEMBER_ID_);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$BranchID = $Row["BranchID"];
	$CenterID = $Row["CenterID"];
	$MemberID = $Row["MemberID"];
	$MemberLoginType = $Row["MemberLoginType"];
	$MemberLevelID = $Row["MemberLevelID"];
	$MemberLoginPW = $Row["MemberLoginPW"];
	$MemberLoginID = $Row["MemberLoginID"];
	$MemberName = $Row["MemberName"];
	$MemberNickName = $Row["MemberNickName"];
	$MemberParentName = $Row["MemberParentName"];

	$MemberSex = $Row["MemberSex"]; 
	
	if($Row["MemberPhone1"]) {
		$ArrMemberPhone1 = explode("-",$Row["MemberPhone1"]);
		$MemberPhone1_1 = $ArrMemberPhone1[0];
		$MemberPhone1_2 = $ArrMemberPhone1[1];
		$MemberPhone1_3 = $ArrMemberPhone1[2];
		$MemberPhone1Agree = $Row["MemberPhone1Agree"];
	} else {
		$MemberPhone1_1 = "";
		$MemberPhone1_2 = "";
		$MemberPhone1_3 = "";
		$MemberPhone1Agree = 1;
	}

	if($Row["MemberPhone2"]) {
		$ArrMemberPhone2 = explode("-",$Row["MemberPhone2"]);
		$MemberPhone2_1 = $ArrMemberPhone2[0];
		$MemberPhone2_2 = $ArrMemberPhone2[1];
		$MemberPhone2_3 = $ArrMemberPhone2[2];
		$MemberPhone2Agree = $Row["MemberPhone2Agree"];
	} else {
		$MemberPhone2_1 = "";
		$MemberPhone2_2 = "";
		$MemberPhone2_3 = "";
		$MemberPhone2Agree = 1;
	}

	if($Row["MemberPhone3"]) {
		$ArrMemberPhone3 = explode("-",$Row["MemberPhone3"]);
		$MemberPhone3_1 = $ArrMemberPhone3[0];
		$MemberPhone3_2 = $ArrMemberPhone3[1];
		$MemberPhone3_3 = $ArrMemberPhone3[2];
	} else {
		$MemberPhone3_1 = "";
		$MemberPhone3_2 = "";
		$MemberPhone3_3 = "";
	}

	if($Row["MemberEmail2"]) {
		$ArrMemberEmail2 = explode("@",$Row["MemberEmail2"]);
		$MemberEmail2_1 = $ArrMemberEmail2[0];
		$MemberEmail2_2 = $ArrMemberEmail2[1];
		$MemberEmail2Agree = $Row["MemberEmail2Agree"];
	} else {
		$MemberEmail2_1 = "";
		$MemberEmail2_2 = "";
		$MemberEmail2Agree = 1;
	}

	$MemberBirthday = $Row["MemberBirthday"]; 
	$SchoolName = $Row["SchoolName"]; 
	$MemberZip = $Row["MemberZip"]; 
	$MemberAddr1 = $Row["MemberAddr1"]; 
	$MemberAddr2 = $Row["MemberAddr2"]; 

	$MemberPhoto = $Row["MemberPhoto"];
	$MemberState = $Row["MemberState"];

	$WithdrawalText = $Row["WithdrawalText"];
	$WithdrawalText = str_replace("\n","<br>",$WithdrawalText);

	$CenterName = $Row["CenterName"];

	$MemberStateText = $Row["MemberStateText"];

	$MemberStudyAlarmTime = $Row["MemberStudyAlarmTime"];
	$MemberStudyAlarmType = $Row["MemberStudyAlarmType"];
	$MemberChangeTeacher = $Row["MemberChangeTeacher"];

	$CheckedID = 1;
	$CheckedEmail = 1;

}else{

	$BranchID = 0;
	$CenterID = 0;
	$MemberID = "";
	$MemberLevelID = 19;
	$MemberLoginPW = "";
	$MemberLoginID = "";
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

	$MemberPhone3_1 = "";
	$MemberPhone3_2 = "";
	$MemberPhone3_3 = "";

	$MemberEmail_1 = "";
	$MemberEmail_2 = "";
	$MemberEmailAgree = 1;

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
	$WithdrawalText = ""; 
	$MemberStateText = ""; 
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
}


?>



<!-- Section: inner-header -->
<section class="inner-header divider parallax layer-overlay overlay-white-2" data-bg-img="images/Sub_Visual_2.jpg" style="display:none;">
  <div class="container pt-60 pb-60">
	<!-- Section Content -->
	<div class="section-content">
	  <div class="row">
		<?if ($_LINK_MEMBER_ID_!="") {?>
		<div class="col-md-12 text-center">
		  <h2 class="title TrnTag">내정보 수정</h2>
		  <ol class="breadcrumb text-center text-black mt-10">
			<li><a href="#">Home</a></li>
			<li class="active text-theme-colored TrnTag">내정보 수정</li>
		  </ol>
		</div>
		<?}else{?>
		<div class="col-md-12 text-center">
		  <h2 class="title TrnTag">회원가입</h2>
		  <ol class="breadcrumb text-center text-black mt-10">
			<li><a href="#">Home</a></li>
			<li class="active text-theme-colored TrnTag">회원가입</li>
		  </ol>
		</div>
		<?}?>
	  </div>
	</div>
  </div>
</section>


<div class="sub_wrap">       

    <section class="member_wrap">
        <div class="member_area">           
			<?if ($_LINK_MEMBER_ID_!="") {?>
			 <h2 class="member_caption TrnTag">정보<span class="normal">추가기입</span></h2>
			<?}else{?>
			 <h2 class="member_caption TrnTag">망고아이 회원<span class="normal">가입</span></h2>  
			<?}?>
            <div class="member_caption_text TrnTag">SNS 계정 사용자들은 최초 1회 추가적인 정보를 입력해야합니다.</div>
			<form name="RegForm" method="post" class="pt-30 pb-40" autocomplete="off">
				<input type="hidden" name="MemberID" value="<?=$MemberID?>">
				<input type="hidden" name="CheckedID" value="<?=$CheckedID?>">
				<input type="hidden" name="CheckedEmail" value="<?=$CheckedEmail?>">
				
				<input type="hidden" name="MemberLevelID" value="<?=$MemberLevelID?>">
				<input type="hidden" name="MemberLoginPW" value="<?=$MemberLoginPW?>">

				<input type="hidden" name="MemberPhone3_1" value="<?=$MemberPhone3_1?>">
				<input type="hidden" name="MemberPhone3_2" value="<?=$MemberPhone3_2?>">
				<input type="hidden" name="MemberPhone3_3" value="<?=$MemberPhone3_3?>">


            <h3 class="member_join_caption TrnTag">학생정보 입력<span><b class="member_red">★</b> 표시는 필수 입력사항입니다.</span></h3>
            <table class="member_table">
                <tr>
                    <th class="TrnTag">영문표기이름 <b class="member_red">★</b></th>
                    <td>
                        <input type="text" name="MemberNickName" id="MemberNickName" class="member_common" value="<?=$MemberNickName?>">
                        <!--<div class="member_idpw_text">예) 홍길동  ▷  Hong Gil Dong</div>-->
                    </td>
                </tr>
				<tr>
					<th class="TrnTag">학교명</th>
					<td>
					<input type="text" id="SchoolName" name="SchoolName" class="member_common" value="<?=$SchoolName?>"/>
					</td>
				</tr>
				<tr>
					<th class="TrnTag">성별 <b class="member_red">★</b></th>
					<td class="radio_wrap teacher">
                        <input type="radio" id="teacher_1" class="input_radio" checked name="MemberSex" value="1" <?if ($MemberSex==1) {?>checked<?}?>><label class="label TrnTag" for="teacher_1"><span class="bullet_radio"></span>남자</label>
                        <input type="radio" id="teacher_2" class="input_radio" name="MemberSex" value="2" <?if ($MemberSex==2) {?>checked<?}?>><label class="label TrnTag" for="teacher_2"><span class="bullet_radio"></span>여자</label>
                    </td>
				</tr> 
                <tr>
                    <th class="TrnTag">생년월일 <b class="member_red">★</b></th>
                    <td>
						<input type="text" id="MemberBirthday" name="MemberBirthday"  value="<?=$MemberBirthday?>" style="background:none; margin:0; border:0; padding:0 0 0 5px; height:40px; font-size:14px;"/>
						<script>
						$(document).ready(function() {
							$("#MemberBirthday").kendoDatePicker({
								format: "yyyy-MM-dd"
							});
						});
						</script>
                    </td>
                </tr>
                <tr>
                    <th class="TrnTag">휴대폰 <b class="member_red">★</b></th>
                    <td>
						<select name="MemberPhone1_1" class="member_select_1">
							<option value="010" <?If ($MemberPhone1_1=="010") {?>selected<?}?>>010</option>
							<option value="011" <?If ($MemberPhone1_1=="011") {?>selected<?}?>>011</option>
							<option value="016" <?If ($MemberPhone1_1=="016") {?>selected<?}?>>016</option>
							<option value="017" <?If ($MemberPhone1_1=="017") {?>selected<?}?>>017</option>
							<option value="018" <?If ($MemberPhone1_1=="018") {?>selected<?}?>>018</option>
							<option value="019" <?If ($MemberPhone1_1=="019") {?>selected<?}?>>019</option>
							<option value="070" <?If ($MemberPhone1_1=="070") {?>selected<?}?>>070</option>
							<option value="02" <?If ($MemberPhone1_1=="02") {?>selected<?}?>>02</option>
							<option value="031" <?If ($MemberPhone1_1=="031") {?>selected<?}?>>031</option>
							<option value="032" <?If ($MemberPhone1_1=="032") {?>selected<?}?>>032</option>
							<option value="033" <?If ($MemberPhone1_1=="033") {?>selected<?}?>>033</option>
							<option value="041" <?If ($MemberPhone1_1=="041") {?>selected<?}?>>041</option>
							<option value="042" <?If ($MemberPhone1_1=="042") {?>selected<?}?>>042</option>
							<option value="043" <?If ($MemberPhone1_1=="043") {?>selected<?}?>>043</option>
							<option value="044" <?If ($MemberPhone1_1=="044") {?>selected<?}?>>044</option>
							<option value="049" <?If ($MemberPhone1_1=="049") {?>selected<?}?>>049</option>
							<option value="051" <?If ($MemberPhone1_1=="051") {?>selected<?}?>>051</option>
							<option value="052" <?If ($MemberPhone1_1=="052") {?>selected<?}?>>052</option>
							<option value="053" <?If ($MemberPhone1_1=="053") {?>selected<?}?>>053</option>
							<option value="054" <?If ($MemberPhone1_1=="054") {?>selected<?}?>>054</option>
							<option value="055" <?If ($MemberPhone1_1=="055") {?>selected<?}?>>055</option>
							<option value="061" <?If ($MemberPhone1_1=="061") {?>selected<?}?>>061</option>
							<option value="062" <?If ($MemberPhone1_1=="062") {?>selected<?}?>>062</option>
							<option value="063" <?If ($MemberPhone1_1=="063") {?>selected<?}?>>063</option>
							<option value="064" <?If ($MemberPhone1_1=="064") {?>selected<?}?>>064</option>
						</select>
						<span class="member_space">-</span>
						<input type="text" name="MemberPhone1_2" class="member_input_small" value="<?=$MemberPhone1_2?>" numberonly="true" maxlength="4">
						<span class="member_space">-</span>
						<input type="text" name="MemberPhone1_3" class="member_input_small" value="<?=$MemberPhone1_3?>" numberonly="true" maxlength="4">
						<div class="member_idpw_text check_wrap agree" style="display:none;"><input type="checkbox" id="MemberPhone1Agree" class="input_check" value="1" <?if ($MemberPhone1Agree==1) {?>checked<?}?> name="MemberPhone1Agree"><label class="label" for="MemberPhone1Agree"><span class="bullet_check"></span>SMS 수신동의</label></div>
                    </td>
                </tr>
                <tr>
                    <th class="TrnTag">주소</th>
                    <td>
						<input type="text" class="member_input_zip" name="MemberZip" id="MemberZip" value="<?=$MemberZip?>" style="margin-right:4px;">
						<a href="javascript:ExecDaumPostcode();" class="member_btn_confirm zip TrnTag" id="BtnCheckID3">우편번호검색</a>
						<input type="text" name="MemberAddr1" id="MemberAddr1" value="<?=$MemberAddr1?>" class="member_common mameber_margin">
						<input type="text" name="MemberAddr2" id="MemberAddr2" value="<?=$MemberAddr2?>" class="member_common" placeholder="나머지 주소">
                    </td>
                </tr>

                <tr>
                    <th class="TrnTag">소속단체명</th>
                    <td>
						
						<input type="hidden" name="CenterID" value="<?=$CenterID?>">
						<input type="text" name="CenterName" id="CenterName" value="<?=$CenterName?>" class="member_input_zip" readonly style="width:50%;margin-right:4px;">

						<?if($CenterTotalRowCount>1) { ?>
							<a href="javascript:SearchCenter();" class="member_btn_confirm TrnTag">검색</a>
						<?}?>

						<?if($DomainSiteID==0) { ?>
						<div class="member_idpw_text TrnTag">단체 소속이 아닌 일반회원은 소속단체 없음을 선택해 주세요.</div>
						<?}?>
						<!--
                        <input type="text" class="member_common red" placeholder="사전에 받으신 쿠폰이 있는 경우 입력해 주세요.">
						-->
                    </td>
                </tr>
            </table>		

            <h3 class="member_join_caption TrnTag" style="display:none;">수업정보, 상담정보, 기타 부가정보</h3>
			<table class="member_table" style="display:none;">
				<tr>
					<th class="TrnTag">수업 전 알림</th>
					<td>
						<select class="member_select_2 parent" id="MemberStudyAlarmTime" name="MemberStudyAlarmTime">
							<option value="10" <?if ($MemberStudyAlarmTime==10){?>selected<?}?>>10분전</option>
							<option value="30" <?if ($MemberStudyAlarmTime==30){?>selected<?}?>>30분전</option>
							<option value="60" <?if ($MemberStudyAlarmTime==60){?>selected<?}?>>1시간전</option>
						</select>
						<div class="radio_wrap alram">
							<input type="radio" id="MemberStudyAlarmType_1" class="input_radio" value="1" name="MemberStudyAlarmType" <?if ($MemberStudyAlarmType==1){?>checked<?}?>><label class="label TrnTag" for="MemberStudyAlarmType_1"><span class="bullet_radio"></span>수신</label>
							<input type="radio" id="MemberStudyAlarmType_2" class="input_radio" value="2" name="MemberStudyAlarmType" <?if ($MemberStudyAlarmType==2){?>checked<?}?>><label class="label TrnTag" for="MemberStudyAlarmType_2"><span class="bullet_radio"></span>거부</label>
						</div>
					</td>
				</tr>
				<tr>
					<th class="TrnTag">강사 대체 정책</th>
					<td class="radio_wrap teacher">
						<input type="radio" id="MemberChangeTeacher_1" class="input_radio" value="1" name="MemberChangeTeacher" <?if ($MemberChangeTeacher==1){?>checked<?}?>><label class="label TrnTag" for="MemberChangeTeacher_1"><span class="bullet_radio"></span>다른 강사로 대체</label>
						<input type="radio" id="MemberChangeTeacher_2" class="input_radio" value="2" name="MemberChangeTeacher" <?if ($MemberChangeTeacher==2){?>checked<?}?>><label class="label TrnTag" for="MemberChangeTeacher_2"><span class="bullet_radio"></span>강사 대체없이 수업 취소</label>
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

			<h3 class="member_join_caption TrnTag">보호자 정보사항<span>성인의 경우 입력하지 않으셔도 됩니다.</span></h3>
			<table class="member_table">
				<tr>
					<th class="TrnTag">보호자 성명</th>
					<td>
						<input type="text" class="member_common" name="MemberParentName" id="MemberParentName" value="<?=$MemberParentName?>">
						<div class="member_idpw_text"><!--무통장 입금 시 입금자명--></div>
					</td>
				</tr>
				<tr>
					<th class="TrnTag">연락처</th>
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
						<option value="0505" <?If ($MemberPhone2_1=="0505") {?>selected<?}?>>0505</option>
						<option value="0502" <?If ($MemberPhone2_1=="0502") {?>selected<?}?>>0502</option>
					</select>
					<span class="member_space">-</span>
					<input type="text" name="MemberPhone2_2" class="member_input_small"  value="<?=$MemberPhone2_2?>" numberonly="true" maxlength="4">
					<span class="member_space">-</span>
					<input type="text" name="MemberPhone2_3" class="member_input_small"   value="<?=$MemberPhone2_3?>" numberonly="true" maxlength="4">
					<div class="member_idpw_text check_wrap agree" style="display:none;"><input type="checkbox" id="MemberPhone2Agree" class="input_check" value="1" <?if ($MemberPhone2Agree==1) {?>checked<?}?> name="MemberPhone2Agree"><label class="label TrnTag" for="MemberPhone2Agree"><span class="bullet_check"></span>SMS 수신동의</label></div>
				</td>
				</tr>
				<tr>
					<th class="TrnTag">이메일</th>
				<td>
					<input type="text" name="MemberEmail2_1" id="MemberEmail2_1" class="member_input_mid"  value="<?=$MemberEmail2_1?>"> 
					<span class="member_space">@</span>
					<input type="text" name="MemberEmail2_2" id="MemberEmail2_2" class="member_input_mid"  value="<?=$MemberEmail2_2?>">						
					<select name="MemberEmail2_3" onchange="ParentSetEmailName()" class="member_select_2">
						<option value="" class="TrnTag">직접입력</option>
						<option value="naver.com">naver.com</option>
						<option value="empal.com">empal.com</option>
						<option value="empas.com">empas.com</option>
						<option value="daum.net">daum.net</option>
						<option value="hanmail.net">hanmail.net</option>
						<option value="hotmail.com">hotmail.com</option>
						<option value="dreamwiz.com">dreamwiz.com</option>				
						<option value="korea.com">korea.com</option>
						<option value="paran.com">paran.com</option>
						<option value="nate.com">nate.com</option>
						<option value="lycos.co.kr">lycos.co.kr</option>
						<option value="yahoo.co.kr">yahoo.co.kr</option>
					</select>
					<!--
					<a href="javascript:CheckEmail()" class="member_btn_confirm email" id="BtnCheckID2" style="display:<?if ($MemberID=="") {?><?}else{?>none<?}?>">중복확인</a>
					-->
					<div class="member_idpw_text check_wrap agree" style="display:none;"><input type="checkbox" id="MemberEmail2Agree" class="input_check" value="1" <?if ($MemberEmail2Agree==1) {?>checked<?}?> name="MemberEmail2Agree"><label class="label TrnTag" for="MemberEmail2Agree"><span class="bullet_check"></span>이메일 수신동의</label></div>
				</td>
				</tr>
			</table>


			<?
			if ($MemberID==""){ 
			?>
			<a href="javascript:FormSubmit();" class="button_member_yellow TrnTag">회원가입</a><a href="#" class="button_member_gray TrnTag">취소</a>
			<?
			}else{
			?>
			<a href="javascript:FormSubmit();" class="button_member_yellow TrnTag">정보수정</a><a href="#" class="button_member_gray TrnTag">취소</a>
			<?
			}
			?>
			</form>

		</div>
	</section>
</div>



<div id="layer" style="padding-top:20px;display:none;position:fixed;overflow:hidden;z-index:100000;-webkit-overflow-scrolling:touch;background-color:#ffffff;">
<img src="//i1.daumcdn.net/localimg/localimages/07/postcode/320/close.png" id="btnCloseLayer" style="cursor:pointer;position:absolute;right:-3px;top:-3px;z-index:1" onclick="closeDaumPostcode()" alt="닫기 버튼">
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
                var fullAddr = data.address; // 최종 주소 변수
                var extraAddr = ''; // 조합형 주소 변수

                // 기본 주소가 도로명 타입일때 조합한다.
                if(data.addressType === 'R'){
                    //법정동명이 있을 경우 추가한다.
                    if(data.bname !== ''){
                        extraAddr += data.bname;
                    }
                    // 건물명이 있을 경우 추가한다.
                    if(data.buildingName !== ''){
                        extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                    }
                    // 조합형주소의 유무에 따라 양쪽에 괄호를 추가하여 최종 주소를 만든다.
                    fullAddr += (extraAddr !== '' ? ' ('+ extraAddr +')' : '');
                }

                // 우편번호와 주소 정보를 해당 필드에 넣는다.
                document.getElementById('MemberZip').value = data.zonecode; //5자리 새우편번호 사용
                document.getElementById('MemberAddr1').value = fullAddr;
                //document.getElementById('sample2_addressEnglish').value = data.addressEnglish;

                // iframe을 넣은 element를 안보이게 한다.
                // (autoClose:false 기능을 이용한다면, 아래 코드를 제거해야 화면에서 사라지지 않는다.)
                element_layer.style.display = 'none';
            },
            width : '100%',
            height : '100%'
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
        var width = 500; //우편번호서비스가 들어갈 element의 width
        var height = 600; //우편번호서비스가 들어갈 element의 height
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
function SearchCenter() {
		openurl = "pop_search_center_form.php";
		$.colorbox({	
			href:openurl
			,width:"800" 
			,height:"710"
			,title:""
			,iframe:true 
			,scrolling:true
			//,onClosed:function(){location.reload(true);}   
		});
}

function PopupAddImage(ImgID,FormName,Path){
	window.open('pop_image_upload_form.php?ImgID='+ImgID+'&FormName='+FormName+'&Path='+Path,'pop_image_upload','width=500,height=280,toolbar=no,top=100,left=100');
}

function SelectCampusID(){
		openurl = "pop_campus_select_form.php";
		$.colorbox({	
			href:openurl
			,width:"800" 
			,height:"710"
			,title:""
			,iframe:true 
			,scrolling:true
			//,onClosed:function(){location.reload(true);}   
		}); 
}


function SelectBranchID(){
		openurl = "pop_branch_select_form.php";
		$.colorbox({	
			href:openurl
			,width:"800" 
			,height:"710"
			,title:""
			,iframe:true 
			,scrolling:true
			//,onClosed:function(){location.reload(true);}   
		}); 
}


function EnNewID(){
	document.RegForm.CheckedID.value = "0";
	document.getElementById('BtnCheckID').style.display = "";
}

function CheckID(){
    var NewID = $.trim($('#MemberLoginID').val());

    if (NewID == "") {
        alert('아이디를 입력하세요.');
        document.RegForm.CheckedID.value = "0";
    } else if (NewID.length<4)  {
		alert('아이디는 4자 이상 입력하세요.');
        document.RegForm.CheckedID.value = "0";
	} else {
        url = "ajax_check_id.php";

		//location.href = url + "?NewID="+NewID;
        $.ajax(url, {
            data: {
                NewID: NewID
            },
            success: function (data) {
				json_data = data;
				CheckResult = json_data.CheckResult;
                if (CheckResult == 1) {
                    alert('사용 가능한 아이디 입니다.');
                    document.RegForm.CheckedID.value = "1";
					document.getElementById('BtnCheckID').style.display = "none";
                }
                else {
                    alert('이미 사용중인 아이디 입니다.');
                    document.RegForm.CheckedID.value = "0";
					document.getElementById('BtnCheckID').style.display = "";
                }
            },
            error: function () {
                alert('Error while contacting server, please try again');
                document.RegForm.CheckedID.value = "0";
				document.getElementById('BtnCheckID').style.display = "";
            }
        });

    }

}


function EnNewEmail(){
	document.RegForm.CheckedEmail.value = "0";
	document.getElementById('BtnCheckID2').style.display = "";
}


function CheckEmail(){
    var MemberEmail_1 = $.trim($('#MemberEmail_1').val());
	var MemberEmail_2 = $.trim($('#MemberEmail_2').val());

    if (MemberEmail_1 == "" || MemberEmail_2 == "") {
        alert('이메일을 입력하세요.');
        document.RegForm.CheckedEmail.value = "0";
	} else {
        url = "ajax_check_email.php";

		//location.href = url + "?NewID="+NewID;
        $.ajax(url, {
            data: {
                MemberEmail_1: MemberEmail_1,
				MemberEmail_2: MemberEmail_2,
				MemberID: "<?=$MemberID?>"
            },
            success: function (data) {
				json_data = data;
				CheckResult = json_data.CheckResult;
                if (CheckResult == 1) {
                    alert('사용 가능한 이메일 입니다.');
                    document.RegForm.CheckedEmail.value = "1";
					document.getElementById('BtnCheckID2').style.display = "none";
                }
                else {
                    alert('이미 등록된 이메일 입니다.');
                    document.RegForm.CheckedEmail.value = "0";
					document.getElementById('BtnCheckID2').style.display = "";
                }
            },
            error: function () {
                alert('Error while contacting server, please try again');
                document.RegForm.CheckedEmail.value = "0";
				document.getElementById('BtnCheckID2').style.display = "";
            }
        });
    }
}


function SetEmailName(){
	var MemberEmail_3 = document.RegForm.MemberEmail_3.value;
	var MemberEmail_2 = document.RegForm.MemberEmail_2;

	if (MemberEmail_3==""){
		MemberEmail_2.value = "";
		MemberEmail_2.readOnly = false;
	}else{
		MemberEmail_2.value = MemberEmail_3;
		MemberEmail_2.readOnly = true;
	}

	EnNewEmail();
}

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
$('.sub_visual_navi .two').addClass('active');

function FormSubmit(){

	obj = document.RegForm.MemberNickName;
	if (obj.value==""){
		alert('영문표기이름을 입력하세요.');
		obj.focus();
		return;
	}

	obj = document.RegForm.MemberBirthday;
	if (obj.value==""){
		alert('생년월일을 입력하세요.');
		obj.focus();
		return;
	}

	obj = document.RegForm.MemberPhone1_2;
	if (obj.value==""){
		alert('휴대폰 번호를 입력하세요.');
		obj.focus();
		return;
	}

	obj = document.RegForm.CenterID;
	if (obj.value==""){
		alert('소속단체를 선택해 주세요.');
		obj.focus();
		return;
	}
	
	<?if ($_LINK_MEMBER_ID_ != ""){?>
		AlertMsg = "회원정보를 수정하시겠습니까?";
	<?}else{?>
		AlertMsg = "회원가입을 진행하시겠습니까?";
	<?}?>

	if (confirm(AlertMsg)){
		document.RegForm.action = "member_action_sns.php"
		document.RegForm.submit();
	}
}

function FormSubmitEn(){
	if (event.keyCode == 13){
		FormSubmit();
	}
}


</script>




<?php
echo "\n";
echo $SubLayoutBottom;
echo "\n";
echo $MainLayoutBottom;
echo "\n";
?>

<?
include_once('./includes/common_analytics.php');
?>


<?php
include_once('./includes/common_footer.php');


if (trim($SubLayoutJavascript)!=""){
	echo "\n";
	echo "<script>";
	echo "\n";
	echo $SubLayoutJavascript;
	echo "\n";
	echo "</script>";
	echo "\n";
}

if (trim($MainLayoutJavascript)!=""){
	echo "\n";
	echo "<script>";
	echo "\n";
	echo $MainLayoutJavascript;
	echo "\n";
	echo "</script>";
	echo "\n";
}
?>

</body>
</html>
<?php
include_once('./includes/dbclose.php');
?>