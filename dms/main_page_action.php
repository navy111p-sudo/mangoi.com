<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');


$MainContent = isset($_REQUEST["MainContent"]) ? $_REQUEST["MainContent"] : "";
$MainContentCss = isset($_REQUEST["MainContentCss"]) ? $_REQUEST["MainContentCss"] : "";
$MainContentJavascript = isset($_REQUEST["MainContentJavascript"]) ? $_REQUEST["MainContentJavascript"] : "";

$MainContent = str_replace("<textarea", "{{textarea", $MainContent);
$MainContent = str_replace("textarea>", "textarea}}", $MainContent);

$MainContent = convertRequest($MainContent);
$MainContentCss = convertRequest($MainContentCss);
$MainContentJavascript = convertRequest($MainContentJavascript);


$Sql = "update Main set MainContent='$MainContent', MainContentCss='$MainContentCss', MainContentJavascript='$MainContentJavascript'";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt = null;


header("Location: main_page_form.php"); 
exit;

?>





