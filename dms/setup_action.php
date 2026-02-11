<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";
$SiteName = isset($_REQUEST["SiteName"]) ? $_REQUEST["SiteName"] : "";
$SiteTitle = isset($_REQUEST["SiteTitle"]) ? $_REQUEST["SiteTitle"] : "";
$DelSiteFavicon = isset($_REQUEST["DelSiteFavicon"]) ? $_REQUEST["DelSiteFavicon"] : "";


//업로드 폴더
$AddSql = "";
$UploadPath = '../uploads/favicons/';
$TempFile = $_FILES['SiteFavicon']['tmp_name'];
if ($TempFile){

	$MyFile = $_FILES['SiteFavicon']['name'];
	$MyFileName = (iconv('utf-8','euc-kr',$MyFile));
	
	$FileTypeCheck = explode('.',$MyFileName);
	$FileExp = $FileTypeCheck[count($FileTypeCheck)-1];
	$i = 0;

	$RealFileName = "";
	while($i < count($FileTypeCheck)-1){
		$RealFileName .= $FileTypeCheck[$i];
		$i++;
	}
	
	$ExistFlag = 0;
	if(file_exists($UploadPath.$RealFileName.'.'.$FileExp)){
		$i = 1;
		while($ExistFlag != 1){
			if(!file_exists($UploadPath.$RealFileName.'['.$i.'].'.$FileExp)){
				$ExistFlag = 1;
				$MyFileName = $RealFileName.'['.$i.'].'.$FileExp;
			}
			$i++;

		} 
	}

	if ($FileExp=="php" || $FileExp=="php3" || $FileExp=="html"){
		$MyFileName = $MyFileName."_";
	}


	if(!@copy($TempFile, $UploadPath.$MyFileName)) { echo("error"); }

	$DbMyFileName = (iconv('euc-kr','utf-8',$MyFileName));
	$AddSql = ", SiteFavicon='".$DbMyFileName."'";
	
}


if ($DelSiteFavicon=="1"){
	$AddSql = ", SiteFavicon=''";
}


$Sql = "update SiteSetup set SiteName='$SiteName', SiteTitle='$SiteTitle'".$AddSql." where Seq=1";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt = null;

if ($err_num != 0){
	include_once('./_header.php');
?>
<body>
<script>
alert("<?=$err_msg?>");
history.go(-1);
</script>
</body>
<?php
	include_once('./_footer.php');
}

include_once('../includes/dbclose.php');


if ($err_num == 0){
	header("Location: setup_form.php"); 
	exit;
}
?>


