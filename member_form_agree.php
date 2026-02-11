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
if ($_LINK_MEMBER_ID_!="") {

	$Sql = "select 
		A.BranchID,
		A.CenterID,
		A.MemberID,
		A.MemberLevelID,
		A.MemberLoginPW,
		A.MemberLoginID,
		A.MemberName,
		A.MemberSex,
		AES_DECRYPT(UNHEX(A.MemberPhone1),:EncryptionKey1) as MemberPhone1,
		AES_DECRYPT(UNHEX(A.MemberEmail),:EncryptionKey2) as MemberEmail,

		MemberBirthday,
		SchoolName,
		MemberZip,
		MemberAddr1,
		MemberAddr2, 

		A.MemberPhoto,
		A.MemberState,
		A.WithdrawalText,
		A.MemberStateText,

		ifnull(B.CampusName,'미등록') as CampusName
	
		from Members A left outer join Centers B on A.CenterID=B.CenterID where A.MemberID=:_MEMBER_ID_";
	

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->bindParam(':EncryptionKey1', $EncryptionKey);
	$Stmt->bindParam(':EncryptionKey2', $EncryptionKey);
	$Stmt->bindParam(':_MEMBER_ID_', $_LINK_MEMBER_ID_);
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$BranchID = $Row["BranchID"];
	$CenterID = $Row["CenterID"];
	$MemberID = $Row["MemberID"];
	$MemberLevelID = $Row["MemberLevelID"];
	$MemberLoginPW = $Row["MemberLoginPW"];
	$MemberLoginID = $Row["MemberLoginID"];
	$MemberName = $Row["MemberName"];

	$MemberSex = $Row["MemberSex"]; 
	
	$ArrMemberPhone1 = explode("-",$Row["MemberPhone1"]);
	
	$MemberPhone1_1 = $ArrMemberPhone1[0];
	$MemberPhone1_2 = $ArrMemberPhone1[1];
	$MemberPhone1_3 = $ArrMemberPhone1[2];

	$ArrMemberEmail = explode("@",$Row["MemberEmail"]);
	
	$MemberEmail_1 = $ArrMemberEmail[0];
	$MemberEmail_2 = $ArrMemberEmail[1];


	$MemberBirthday = $Row["MemberBirthday"]; 
	$SchoolName = $Row["SchoolName"]; 
	$MemberZip = $Row["MemberZip"]; 
	$MemberAddr1 = $Row["MemberAddr1"]; 
	$MemberAddr2 = $Row["MemberAddr2"]; 


	$MemberPhoto = $Row["MemberPhoto"];
	$MemberState = $Row["MemberState"];

	$WithdrawalText = $Row["WithdrawalText"];
	$WithdrawalText = str_replace("\n","<br>",$WithdrawalText);

	$CampusID = $Row["CampusID"];
	$CampusName = $Row["CampusName"];

	$MemberStateText = $Row["MemberStateText"];

	$CheckedID = 1;
	$CheckedEmail = 1;

}else{

	$BranchID = 0;
	$CenterID = 0;
	$MemberID = "";
	$MemberLevelID = 9;
	$MemberLoginPW = "";
	$MemberLoginID = "";
	$MemberName = "";
	$MemberSex = 1; 
	$MemberPhone1_1 = "";
	$MemberPhone1_2 = "";
	$MemberPhone1_3 = "";
	$MemberEmail_1 = "";
	$MemberEmail_2 = "";
	$MemberBirthday = "";
	$SchoolName = ""; 
	$MemberZip = ""; 
	$MemberAddr1 = ""; 
	$MemberAddr2 = ""; 
	$MemberPhoto = ""; 
	$MemberState = 1;
	$WithdrawalText = ""; 
	$MemberStateText = ""; 
	$CheckedID = 1;
	$CheckedEmail = 1;
}


?>

<? if($DomainSiteID == 7) {?>
	<div class="sub_wrap">       

<section class="member_wrap">
	<div class="member_area">
		<h2 class="member_caption TrnTag">아이비리그 회원<span class="normal">가입</span></h2>
		<div class="member_caption_text TrnTag">회원가입은 무료이며, 회원 가입을 통해 영어 실력 향상을 위한 최고의 학습 서비스를 제공 받으실 수 있습니다.</div>

		<h3 class="member_agree_caption TrnTag">회원약관<span>* 반드시 회원약관을 읽은 후 동의 여부를 결정해 주세요.</span></h3>
		<iframe src="agree_content_1.php" class="member_iframe"></iframe>
		<div class="member_agree check_wrap"><input type="checkbox" id="agree_1" class="input_check" checked name=""><label class="label TrnTag" for="agree_1"><span class="bullet_check"></span>약관의 내용에 동의합니다.</label></div>
		
		<h3 class="member_agree_caption TrnTag">개인정보 취급방침<span>* 반드시 개인정보 취급방침을 읽은 후 동의 여부를 결정해 주세요.</span></h3>
		<iframe src="agree_content_2.php" class="member_iframe"></iframe>
		<div class="member_agree check_wrap"><input type="checkbox" id="agree_2" class="input_check" checked name=""><label class="label TrnTag" for="agree_2"><span class="bullet_check"></span>개인정보 취급방침의 내용에 동의합니다.</label></div>
		<a href="javascript:FormSubmit();" class="button_yellow TrnTag">회원가입</a>
	</div>
</section>

</div>

<? } else if($DomainSiteID == 8) {?>
    <div class="sub_wrap">

        <section class="member_wrap">
            <div class="member_area">
                <h2 class="member_caption TrnTag">잉글리씨드 회원<span class="normal">가입</span></h2>
                <div class="member_caption_text TrnTag">회원가입은 무료이며, 회원 가입을 통해 영어 실력 향상을 위한 최고의 학습 서비스를 제공 받으실 수 있습니다.</div>

                <h3 class="member_agree_caption TrnTag">회원약관<span>* 반드시 회원약관을 읽은 후 동의 여부를 결정해 주세요.</span></h3>
                <iframe src="agree_content_1.php" class="member_iframe"></iframe>
                <div class="member_agree check_wrap"><input type="checkbox" id="agree_1" class="input_check" checked name=""><label class="label TrnTag" for="agree_1"><span class="bullet_check"></span>약관의 내용에 동의합니다.</label></div>

                <h3 class="member_agree_caption TrnTag">개인정보 취급방침<span>* 반드시 개인정보 취급방침을 읽은 후 동의 여부를 결정해 주세요.</span></h3>
                <iframe src="agree_content_2.php" class="member_iframe"></iframe>
                <div class="member_agree check_wrap"><input type="checkbox" id="agree_2" class="input_check" checked name=""><label class="label TrnTag" for="agree_2"><span class="bullet_check"></span>개인정보 취급방침의 내용에 동의합니다.</label></div>
                <a href="javascript:FormSubmit();" class="button_yellow TrnTag">회원가입</a>
            </div>
        </section>

    </div>
<? } else if($DomainSiteID == 9) {?>
    <div class="sub_wrap">

        <section class="member_wrap">
            <div class="member_area">
                <h2 class="member_caption TrnTag">이엔지 화상영어 회원<span class="normal">가입</span></h2>
                <div class="member_caption_text TrnTag">회원가입은 무료이며, 회원 가입을 통해 영어 실력 향상을 위한 최고의 학습 서비스를 제공 받으실 수 있습니다.</div>

                <h3 class="member_agree_caption TrnTag">회원약관<span>* 반드시 회원약관을 읽은 후 동의 여부를 결정해 주세요.</span></h3>
                <iframe src="agree_content_1.php" class="member_iframe"></iframe>
                <div class="member_agree check_wrap"><input type="checkbox" id="agree_1" class="input_check" checked name=""><label class="label TrnTag" for="agree_1"><span class="bullet_check"></span>약관의 내용에 동의합니다.</label></div>

                <h3 class="member_agree_caption TrnTag">개인정보 취급방침<span>* 반드시 개인정보 취급방침을 읽은 후 동의 여부를 결정해 주세요.</span></h3>
                <iframe src="agree_content_2.php" class="member_iframe"></iframe>
                <div class="member_agree check_wrap"><input type="checkbox" id="agree_2" class="input_check" checked name=""><label class="label TrnTag" for="agree_2"><span class="bullet_check"></span>개인정보 취급방침의 내용에 동의합니다.</label></div>
                <a href="javascript:FormSubmit();" class="button_yellow TrnTag">회원가입</a>
            </div>
        </section>

    </div>
<?} else {?>

<div class="sub_wrap">       

    <section class="member_wrap">
        <div class="member_area">
            <h2 class="member_caption TrnTag">망고아이 회원<span class="normal">가입</span></h2>
            <div class="member_caption_text TrnTag">회원가입은 무료이며, 회원 가입을 통해 영어 실력 향상을 위한 최고의 학습 서비스를 제공 받으실 수 있습니다.</div>

            <h3 class="member_agree_caption TrnTag">회원약관<span>* 반드시 회원약관을 읽은 후 동의 여부를 결정해 주세요.</span></h3>
            <iframe src="agree_content_1.php" class="member_iframe"></iframe>
            <div class="member_agree check_wrap"><input type="checkbox" id="agree_1" class="input_check" checked name=""><label class="label TrnTag" for="agree_1"><span class="bullet_check"></span>약관의 내용에 동의합니다.</label></div>
            
            <h3 class="member_agree_caption TrnTag">개인정보 취급방침<span>* 반드시 개인정보 취급방침을 읽은 후 동의 여부를 결정해 주세요.</span></h3>
            <iframe src="agree_content_2.php" class="member_iframe"></iframe>
            <div class="member_agree check_wrap"><input type="checkbox" id="agree_2" class="input_check" checked name=""><label class="label TrnTag" for="agree_2"><span class="bullet_check"></span>개인정보 취급방침의 내용에 동의합니다.</label></div>
            <a href="javascript:FormSubmit();" class="button_yellow TrnTag">회원가입</a>
        </div>
    </section>

</div>
<?}?>



    <!--input type="radio" name="CheckAgree" value="1"> 서비스이용약관 및 개인정보 취급방침에 동의합니다. -->


<script language="javascript">

function FormSubmit(){

	var agree_1 = document.getElementById("agree_1");
	var agree_2 = document.getElementById("agree_2");

	// 회원가입 약관 체크
	if (agree_1.checked==false){
		alert('회원약관에 동의하여야 합니다.');
		agree_1.focus();
		return;
	}

	// 개인정보 취급방침 체크
	if (agree_2.checked==false){
		alert('개인정보 취급방침을 동의하여야 합니다.');
		agree_2.focus();
		return;
	}

	location.href='member_form.php';
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