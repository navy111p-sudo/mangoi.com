<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');

$TaxInvoiceType = isset($_REQUEST["TaxInvoiceType"]) ? $_REQUEST["TaxInvoiceType"] : "";
$TaxInvoicePayID = isset($_REQUEST["TaxInvoicePayID"]) ? $_REQUEST["TaxInvoicePayID"] : "";
$TaxInvoiceID = isset($_REQUEST["TaxInvoiceID"]) ? $_REQUEST["TaxInvoiceID"] : "";

if ($TaxInvoiceID==""){

	$Sql = "
		insert into TaxInvoices 
			(TaxInvoiceRegType, TaxInvoiceType, TaxInvoicePayID, TaxInvoiceRegDateTime, TaxInvoiceModiDateTime) 
		values 
			(2, :TaxInvoiceType, :TaxInvoicePayID, now(), now());";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TaxInvoiceType', $TaxInvoiceType);
	$Stmt->bindParam(':TaxInvoicePayID', $TaxInvoicePayID);
	$Stmt->execute();

}else{
	$Sql = "update TaxInvoices set 
				TaxInvoiceRegType=2, 
				TaxInvoiceModiDateTime=now()
			where TaxInvoiceID=:TaxInvoiceID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TaxInvoiceID', $TaxInvoiceID);
	$Stmt->execute();
	$Stmt = null;
}

$ArrValue["CheckResult"] = 1;

$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('../includes/dbclose.php');
?>