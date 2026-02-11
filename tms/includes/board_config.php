<?php
$BoardCode = isset($_REQUEST["BoardCode"]) ? $_REQUEST["BoardCode"] : "";

$Sql = "select BoardName, BoardTitle, BoardFileCount, BoardEnableCategory, BoardEnableComment, BoardDateHide from Boards where BoardCode=:BoardCode";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':BoardCode', $BoardCode);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;


$BoardName = $Row["BoardName"];
$BoardTitle = $Row["BoardTitle"];
$BoardFileCount = $Row["BoardFileCount"];
$BoardEnableCategory = $Row["BoardEnableCategory"];
$BoardEnableComment = $Row["BoardEnableComment"];
$BoardDateHide = $Row["BoardDateHide"];


?>