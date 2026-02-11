<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');

$MainLayout = isset($_REQUEST["MainLayout"]) ? $_REQUEST["MainLayout"] : "";
$MainLayoutCss = isset($_REQUEST["MainLayoutCss"]) ? $_REQUEST["MainLayoutCss"] : "";
$MainLayoutJavascript = isset($_REQUEST["MainLayoutJavascript"]) ? $_REQUEST["MainLayoutJavascript"] : "";

$MainLayout = str_replace("<textarea", "{{textarea", $MainLayout);
$MainLayout = str_replace("textarea>", "textarea}}", $MainLayout);

$MainLayout = convertRequest($MainLayout);
$MainLayoutCss = convertRequest($MainLayoutCss);
$MainLayoutJavascript = convertRequest($MainLayoutJavascript);


$Sql = "update Main set MainLayout='$MainLayout', MainLayoutCss='$MainLayoutCss', MainLayoutJavascript='$MainLayoutJavascript'";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt = null;


header("Location: main_layout_form.php"); 
exit;

?>





