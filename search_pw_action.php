<?php
include_once('./includes/dbopen.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$MemberName = isset($_REQUEST["MemberName"]) ? $_REQUEST["MemberName"] : "";
$MemberLoginID = isset($_REQUEST["MemberLoginID"]) ? $_REQUEST["MemberLoginID"] : "";
$MemberEmail = isset($_REQUEST["MemberEmail"]) ? $_REQUEST["MemberEmail"] : "";



$Sql = "select count(*) as TotalRowCount from Members where MemberState=1 and MemberName=:MemberName and MemberEmail=HEX(AES_ENCRYPT(:MemberEmail, :EncryptionKey)) and MemberLoginID=:MemberLoginID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberName', $MemberName);
$Stmt->bindParam(':MemberEmail', $MemberEmail);
$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
$Stmt->bindParam(':MemberLoginID', $MemberLoginID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TotalRowCount = $Row["TotalRowCount"];


if ($TotalRowCount==0){
	$err_num = 1;
	$err_msg = "일치하는 회원이 없습니다.";

}else{

	$Sql = "select * from Members where MemberState=1 and MemberName=:MemberName and MemberEmail=HEX(AES_ENCRYPT(:MemberEmail, :EncryptionKey)) and MemberLoginID=:MemberLoginID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':MemberName', $MemberName);
	$Stmt->bindParam(':MemberEmail', $MemberEmail);
	$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
	$Stmt->bindParam(':MemberLoginID', $MemberLoginID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$MemberName = $Row["MemberName"];
	$MemberLgoinID = $Row["MemberLgoinID"];



	$NewMemberLoginPW = rand ( 100000, 999999); 

	$Sql = "update Members set MemberLoginPW=HEX(AES_ENCRYPT('$NewMemberLoginPW', MD5('$NewMemberLoginPW'))) where MemberLoginID='$MemberLoginID'";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt = null;


	$MailHTML = "<div style=\"width:100%; margin:0 auto; padding:3% 2%; font-family:'돋움', '굴림', '맑은 고딕'; text-align:center; box-sizing:border-box; color:#696969;\">
	<div style=\"overflow:hidden; border-bottom:2px solid #111; padding-bottom:30px; letter-spacing:-0.5px;\">
        <h2 style=\"font-family:'맑은 고딕', '나눔고딕', '돋움'; font-size:23px; padding:0; margin:0; color:#303030; text-align:left; float:left; padding-top:16px;\">비밀번호 정보를 알려드립니다.
            <p style=\"font-size:14px; padding:4px 0 0 0; margin:0; color:#4e4e4e;\">요청하신 비밀번호 정보를 안내드립니다.</p>
        </h2>
        <img src=\"http://eduwho.co.kr/images/Logo.png\" style=\"float:right; padding-top:10px;\">
    </div>

    <div style=\"font-size:14px; padding:40px 0; border-bottom:1px solid #ccc;\">
    	안녕하세요 <u><b>".$MemberName."(".$MemberLoginID.")</b></u>님 <br><br>
		회원님의 임시 비밀번호는 <u><b>".$NewMemberLoginPW."</b></u>입니다. 
    </div>
    <div style=\"text-align:center; padding:40px 0; border-bottom:1px solid #ccc;\">
    	<a href=\"http://eduwho.co.kr\" style=\"display:inline-block; background:#333; height:50px; line-height:50px; width:80%; color:#fff; font-weight:bold; text-decoration:none;\">홈페이지로 이동</a>
    </div>
    <div style=\"font-size:12px; padding:25px 0 30px 0; line-height:1.4;\">
    	본 메일은 고객님의 요청에 의해 발송되었습니다. 문의사항은 망고아이로 문의주시기 바랍니다.
		<p style=\"color:#999; padding-top:5px; margin:0;\">주소 : 경기도 안산시 상록구 이동 716-10번지 6층 Tel : 1644-0561 <br>
		e-mail : jangjiwoong@mangoi.com<br>Copyright All Rights Reserved by 망고아이</p>
    </div>
	</div>";


	$to = $MemberEmail."|".$MemberName;
	$from = $AdminReturnEmail."|망고아이";
	$subject = "망고아이 임시 비밀번호 안내";
	$content = $MailHTML;
	$html = "HTML";
	getSendMail($to,$from,$subject,$content,$html);
}


if ($err_num != 0){
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
</head>
<body>
<script>
alert("<?=$err_msg?>");
history.go(-1);
</script>
</body>
</html>
<?php
}else{
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
</head>
<body>
<script>
alert("가입하신 이메일로 임시 비밀번호를 발송했습니다.");
location.href = "login_form.php";
</script>

<?
include_once('./includes/common_analytics.php');
?>
</body>
</html>
<?
}

include_once('./includes/dbclose.php');
?>