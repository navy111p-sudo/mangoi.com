<?php
$BoardCode = isset($_REQUEST["BoardCode"]) ? $_REQUEST["BoardCode"] : "";

$Sql = "select BoardName, BoardFileCount, BoardEnableCategory from Boards where BoardCode=:BoardCode";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':BoardCode', $BoardCode);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$BoardName = $Row["BoardName"];
$BoardFileCount = $Row["BoardFileCount"];
$BoardEnableCategory = $Row["BoardEnableCategory"];


?>