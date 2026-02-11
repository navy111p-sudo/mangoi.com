<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('./includes/dbopen.php');

$Dummy = isset($_REQUEST["Dummy"]) ? $_REQUEST["Dummy"] : "";

$TableName = "phoneinvite";
$sql="
	select *
		from $TableName
		order by SeqNo desc
		limit 0,1
 ";
$rs=mysql_query($sql);
$row=mysql_fetch_array($rs);

$SeqNo= $row["SeqNo"];
$PhoneNo= $row["PhoneNo"];
$TelNo= $row["TelNo"];
$CallDate= $row["CallDate"];
$ConnectTime= $row["ConnectTime"];
$CallStatus= $row["CallStatus"];
$LineNo= $row["LineNo"];
$LineCount= $row["LineCount"];
$PhoneType= $row["PhoneType"];


$ArrValue["SeqNo"] = $SeqNo;
$ArrValue["PhoneNo"] = $PhoneNo;
$ArrValue["TelNo"] = $TelNo;
$ArrValue["CallDate"] = $CallDate;
$ArrValue["ConnectTime"] = $ConnectTime;
$ArrValue["CallStatus"] = $CallStatus;
$ArrValue["LineNo"] = $LineNo;
$ArrValue["LineCount"] = $LineCount;
$ArrValue["PhoneType"] = $PhoneType;


$ResultValues = my_json_encode($ArrValue);
echo $ResultValues; 

function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('./includes/dbclose.php');
?>