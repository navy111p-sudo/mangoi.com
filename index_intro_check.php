<?
include_once('./includes/dbopen.php');
include_once('./includes/common.php');

$CloseType = isset($_REQUEST["CloseType"]) ? $_REQUEST["CloseType"] : "";
setcookie("HideHomeIntro", "1", 0, "/", ".".$DefaultDomain2);

if ($CloseType=="1"){
	header("Location: login_form.php");
	exit;
}else{
	header("Location: /");
	exit;
}

include_once('./includes/dbclose.php');
?>