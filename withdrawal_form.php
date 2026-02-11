<?php
include_once('./includes/dbopen.php');
include_once('./includes/common.php');

$DenyGuest = true;
include_once('./includes/member_check.php');

$UseMain = 0;
$UseSub = 1;
if ($_LINK_MEMBER_LEVEL_ID_==6 || $_LINK_MEMBER_LEVEL_ID_==7 ){
	if ($MemberCampusType==1){
		$SubCode = "Sub12";
	}else{			
		$SubCode = "Sub13";
	}
}else if ($_LINK_MEMBER_LEVEL_ID_==9){
	$SubCode = "Sub11";
}

?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
<link href="css/style.css" rel="stylesheet" type="text/css">
<script src="js/javascript.js"></script>
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

if (trim($PageContentCss)!=""){
	echo "\n";
	echo "<style>";
	echo "\n";
	echo $PageContentCss;
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
// $PageContent = convertHTML(trim($PageContent));
// $SubLayoutBottom = convertHTML(trim($SubLayoutBottom));
// $MainLayoutBottom = convertHTML(trim($MainLayoutBottom));

if($DomainSiteID==7){
    $SubLayoutTop = convertHTML(trim($SubLayoutTop));
    $PageContent = convertHTML(trim($PageContent));
    $SubLayoutBottom = convertHTML(trim($SubLayoutBottom));

} else if($DomainSiteID==8){ //engliseed.kr
    $SubLayoutTop = convertHTML(trim($SubLayoutTop));
    $PageContent = convertHTML(trim($PageContent));
    $SubLayoutBottom = convertHTML(trim($SubLayoutBottom));


} else if($DomainSiteID==9){ //live.engedu.kr
    $SubLayoutTop = convertHTML(trim($SubLayoutTop));
    $PageContent = convertHTML(trim($PageContent));
    $SubLayoutBottom = convertHTML(trim($SubLayoutBottom));


} else {
    $MainLayoutTop = convertHTML(trim($MainLayoutTop));
    $SubLayoutTop = convertHTML(trim($SubLayoutTop));
    $PageContent = convertHTML(trim($PageContent));
	$SubLayoutBottom = convertHTML(trim($SubLayoutBottom));
    $MainLayoutBottom = convertHTML(trim($MainLayoutBottom));
  }

echo "\n";
echo $MainLayoutTop;
echo "\n";
echo $SubLayoutTop;
echo "\n";
?>


    
    <!-------------------------- 우측영역 시작 -------------------------->
	<div class="RightArea">
    	<div class="NaviLocation">
        	<img src="images1/IconHome.png"> <span>></span> 지식나눔+ <span>></span> 회원탈퇴
        </div>
        <div class="SubVisual">
        	<img src="images1/SubImg08.png">
            <div class="Title">
            	<span></span>
                <h1>회원탈퇴</h1>
				지식나눔+ 회원탈퇴 입니다.
            </div>
        </div>
        
        <!-------------------------- 컨텐츠영역 시작 -------------------------->
        <div class="ContentArea">

		
                <form name="RegForm" method="post">
				<input type="hidden" name="MemberID" value="<?=$_LINK_MEMBER_ID_?>">
				<div class="MemberBox3">
                	<div>회원탈퇴를 하시면 학습내역, 포인트 등이 삭제됩니다. 신중히 생각하시고 진행해 주세요.</div>
                    <br><br>
					<h4>회원탈퇴사유</h4>
                    <textarea name="WithdrawalText" style="width:100%;height:100px;padding:10px;border:1px solid #CCCCCC;"></textarea>
                </div>
				</form>


				<div class="BtnCenter"><a href="javascript:FormSubmit();" class="BtnGray2" style="width:130px; height:40px; line-height:40px;">회원탈퇴</a></div>


            
        </div><!-- 컨텐츠영역 끝 -->
        
    </div><!-- 우측영역 끝 -->


<script language="javascript">
function FormSubmit(){
	obj = document.RegForm.WithdrawalText;
	if (obj.value==""){
		alert('탈퇴사유를 입력하세요.');
		obj.focus();
		return;
	}

	if (confirm('정말로 탈퇴하시겠습니까?')){
		document.RegForm.action = "withdrawal_action.php";
		document.RegForm.submit();
	}
}


</script>


<script>
document.getElementById("Snavi3").className="active";
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

if (trim($PageContentJavascript)!=""){
	echo "\n";
	echo "<script>";
	echo "\n";
	echo $PageContentJavascript;
	echo "\n";
	echo "</script>";
	echo "\n";
}

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





