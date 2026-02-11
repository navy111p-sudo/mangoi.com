<?
include_once('../../includes/dbopen.php');


/* ============================================================================== */
/* =   PAGE : 결과 처리 PAGE                                                    = */
/* = -------------------------------------------------------------------------- = */
/* =   Copyright (c)  2013   KCP Inc.   All Rights Reserverd.                   = */
/* ============================================================================== */
?>
<? 
/* ============================================================================== */
/* =   01. 인증 결과                                                            = */
/* = -------------------------------------------------------------------------- = */
$res_cd      = $_POST[ "res_cd"      ];                // 결과 코드
$res_msg     = $_POST[ "res_msg"     ];                // 결과 메시지
/* = -------------------------------------------------------------------------- = */
$ordr_idxx   = $_POST[ "ordr_idxx"   ];                // 주문번호
$buyr_name   = $_POST[ "buyr_name"   ];                // 요청자 이름
$card_cd     = $_POST[ "card_cd"     ];                // 카드 코드
$card_name     = $_POST[ "card_name"     ];            
$batch_key   = $_POST[ "batch_key"   ];                // 배치 인증키
/* ============================================================================== */

$good_mny = 2000;


if ($res_cd=="0000"){
	$ClassOrderPayBatchState = 1;
}else{
	$ClassOrderPayBatchState = 0;
}	


$Sql = " update ClassOrderPayBatchs set ";
$Sql .= "	ClassOrderPayBatchState=:ClassOrderPayBatchState, ";

$Sql .= "	ClassOrderPayBatchKey=:ClassOrderPayBatchKey, ";
$Sql .= "	batch_key=:batch_key, ";
$Sql .= "	res_cd=:res_cd, ";
$Sql .= "	res_msg=:res_msg, ";
$Sql .= "	ordr_idxx=:ordr_idxx, ";
$Sql .= "	good_mny=:good_mny, ";
$Sql .= "	card_cd=:card_cd, ";
$Sql .= "	card_name=:card_name, ";

$Sql .= "	ClassOrderPayBatchModiDateTime=now() ";
$Sql .= " where ClassOrderPayBatchNumber=:ordr_idxx ";

$Stmt = $DbConn->prepare($Sql);

$Stmt->bindParam(':ClassOrderPayBatchState', $ClassOrderPayBatchState);

$Stmt->bindParam(':ClassOrderPayBatchKey', $batch_key);
$Stmt->bindParam(':batch_key', $batch_key);
$Stmt->bindParam(':res_cd', $res_cd);
$Stmt->bindParam(':res_msg', $res_msg);
$Stmt->bindParam(':ordr_idxx', $ordr_idxx);
$Stmt->bindParam(':good_mny', $good_mny);
$Stmt->bindParam(':card_cd', $card_cd);
$Stmt->bindParam(':card_name', $card_name);

$Stmt->bindParam(':ClassOrderPayBatchNumber', $ordr_idxx);
$Stmt->execute();
$Stmt = null;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title></title>
<meta name="viewport" content="width=device-width, user-scalable=1.0, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
<script>
<?if ($res_cd=="0000"){?>
alert("등록되었습니다.");
<?}else{?>
alert("등록 실패입니다. 다시 시도해 주세요.");
<?}?>
parent.location.href = "../../mypage_payment_list.php";
</script>
</body>
</html>
<?php
include_once('../../includes/dbclose.php');
?>