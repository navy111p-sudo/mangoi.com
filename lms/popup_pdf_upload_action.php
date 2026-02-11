<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');


$ErrNum = 0;
$ErrMsg = "";

$FormNameFile = isset($_REQUEST["FormNameFile"]) ? $_REQUEST["FormNameFile"] : "";
$UpPath = isset($_REQUEST["UpPath"]) ? $_REQUEST["UpPath"] : "";

$UpPath = $UpPath ."/";
$UploadDir = str_replace(basename(__FILE__), '', realpath($UpPath)) . "/";


$TempFile = $_FILES['UpFile']['tmp_name'];
if ($TempFile){

    $MyFile         = $_FILES['UpFile']['name'];
    $MyFileSize     = $_FILES['UpFile']['size'];
    $MyFileMimeType = $_FILES['UpFile']['type'];
    $MyFileName     = (iconv('utf-8','euc-kr',$MyFile));
    $MyFileRealName = $MyFileName;

    $FileTypeCheck = explode('.',$MyFileName);
    $FileType       = $FileTypeCheck[count($FileTypeCheck)-1];
    $i = 0;
    
    $RealFileName = "";
    while($i < count($FileTypeCheck)-1){
        $RealFileName .= $FileTypeCheck[$i];
        $i++;
    }
    
    $RealFileName = md5($RealFileName);
    $RealFileNameResize = $RealFileName."_rs";
    $RealFileNameCrop = $RealFileName."_cp";

    $ExistFlag = 0;
    if(file_exists($UpPath.$RealFileName.'.'.$FileType)){
        $i = 1;
        while($ExistFlag != 1){
            if(!file_exists($UpPath.$RealFileName.'['.$i.'].'.$FileType)){
                $ExistFlag = 1;
                $MyFileName = $RealFileName.'['.$i.'].'.$FileType;
                $MyFileNameResize = $RealFileNameResize.'['.$i.'].'.$FileType;
                $MyFileNameCrop = $RealFileNameCrop.'['.$i.'].'.$FileType;
            }
            $i++;

        } 
    }else{
        $MyFileName = $RealFileName.'.'.$FileType;
        $MyFileNameResize = $RealFileNameResize.'.'.$FileType;
        $MyFileNameCrop = $RealFileNameCrop.'.'.$FileType;
    }

    if ($FileType=="php" || $FileType=="php3" || $FileType=="html"){
        $MyFileName = $MyFileName."_";
    }

    if(!@copy($TempFile, $UpPath.$MyFileName)) { echo("error"); }




    $DbMyFileName      = (iconv('euc-kr','utf-8',$MyFileName));
    $DbMyFileRealName  = (iconv('euc-kr','utf-8',$MyFileRealName));
    $DbMyFileSize      = $MyFileSize;
    $DbMyFileExtension = $FileType;
    $DbMyFileMimeType  = $MyFileMimeType;

}



if ($ErrNum != 0){
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
alert("<?=$ErrMsg?>");
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
parent.document.<?=$FormNameFile?>.value = "<?=$DbMyFileName?>";
parent.$.fn.colorbox.close();
</script>
</body>
</html>
<?
}
include_once('../includes/dbclose.php');
?>





