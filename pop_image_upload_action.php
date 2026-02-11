<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');


$err_num = 0;
$err_msg = "";

$ImgID = isset($_REQUEST["ImgID"]) ? $_REQUEST["ImgID"] : "";
$FormName = isset($_REQUEST["FormName"]) ? $_REQUEST["FormName"] : "";
$UpPath = isset($_REQUEST["UpPath"]) ? $_REQUEST["UpPath"] : "";
$ReScale = isset($_REQUEST["ReScale"]) ? $_REQUEST["ReScale"] : "";

if ($ReScale==""){
	$ReScale = "0";
}


if (substr($UpPath,0,3)=="../"){
	$UploadPath = substr($UpPath,1).'/';
	$uploaddir = $ServerDir.substr($UpPath,3).'/';
}else{
	$UploadPath = $UpPath.'/';
	$uploaddir = $ServerDir.substr($UpPath,2).'/';
}

$TempFile = $_FILES['UpFile']['tmp_name'];


if ($TempFile){

	$MyFile = $_FILES['UpFile']['name'];
	$MyFileSize = $_FILES['UpFile']['size'];
	$MyFileMimeType = $_FILES['UpFile']['type'];
	$MyFileName = (iconv('utf-8','euc-kr',$MyFile));
	$MyFileRealName = $MyFileName;


	$FileTypeCheck = explode('.',$MyFileName);
	$FileExp = $FileTypeCheck[count($FileTypeCheck)-1];
	$i = 0;

	
	$RealFileName = "";
	while($i < count($FileTypeCheck)-1){
		$RealFileName .= $FileTypeCheck[$i];
		$i++;
	}
	


	$RealFileName = md5($RealFileName);
	$RealFileName_resize = $RealFileName."_rs";

	$ExistFlag = 0;
	if(file_exists($UploadPath.$RealFileName.'.'.$FileExp)){
		$i = 1;
		while($ExistFlag != 1){
			if(!file_exists($UploadPath.$RealFileName.'['.$i.'].'.$FileExp)){
				$ExistFlag = 1;
				$MyFileName = $RealFileName.'['.$i.'].'.$FileExp;
				$MyFileName_Resize = $RealFileName_resize.'['.$i.'].'.$FileExp;
			}
			$i++;

		} 
	}else{
		$MyFileName = $RealFileName.'.'.$FileExp;
		$MyFileName_Resize = $RealFileName_resize.'.'.$FileExp;
	}
	if ($FileExp=="php" || $FileExp=="php3" || $FileExp=="html"){
		$MyFileName = $MyFileName."_";
	}


	if(!@copy($TempFile, $UploadPath.$MyFileName)) { echo("error"); }

	$DbMyFileName = (iconv('euc-kr','utf-8',$MyFileName));
	$DbMyFileName_Resize = (iconv('euc-kr','utf-8',$MyFileName_Resize));
	$DbMyFileRealName = (iconv('euc-kr','utf-8',$MyFileRealName));
	$DbMyFileSize = $MyFileSize;
	$DbMyFileExtension = $FileExp;
	$DbMyFileMimeType = $MyFileMimeType;


	if ($ReScale!="0"){
		//사이즈 줄이기
		$ffmpeg = "/usr/local/bin/ffmpeg";
		$ffmpegcmd = $ffmpeg." -i ".$uploaddir."".$DbMyFileName." -vf scale=".$ReScale.":-1 ".$uploaddir."".$DbMyFileName_Resize;
		exec($ffmpegcmd);
		//사이즈 줄이기
	}else{

		$DbMyFileName_Resize = $DbMyFileName;
	}

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
//opener.document.getElementById('<?=$ImgID?>').src = "<?=$UpPath?>/<?=$DbMyFileName?>";
//opener.document.<?=$FormName?>.value = "<?=$DbMyFileName?>";
//window.close();

parent.document.getElementById('<?=$ImgID?>').src = "<?=$UpPath?>/<?=$DbMyFileName_Resize?>";
parent.document.<?=$FormName?>.value = "<?=$DbMyFileName_Resize?>";
parent.$.fn.colorbox.close();
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





