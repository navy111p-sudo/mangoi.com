<?
include_once('../../includes/dbopen.php');

include "../cfg/site_conf_inc.php";       // 환경설정 파일 include


/* ============================================================================== */
/* =   지불 결과                                                                = */
/* = -------------------------------------------------------------------------- = */
// 지불 정보
$req_tx = isset($_POST["req_tx"]) ? $_POST["req_tx"] : "";

// 결과 코드
$res_cd = isset($_POST["res_cd"]) ? $_POST["res_cd"] : "";
$res_msg = isset($_POST["res_msg"]) ? $_POST["res_msg"] : "";

// 주문 정보
$ordr_idxx = isset($_POST["ordr_idxx"]) ? $_POST["ordr_idxx"] : "";
$good_name = isset($_POST["good_name"]) ? $_POST["good_name"] : "";
$good_mny = isset($_POST["good_mny"]) ? $_POST["good_mny"] : "";
$buyr_name = isset($_POST["buyr_name"]) ? $_POST["buyr_name"] : "";

// 신용카드
$card_cd = isset($_POST["card_cd"]) ? $_POST["card_cd"] : "";
$card_name = isset($_POST["card_name"]) ? $_POST["card_name"] : "";
$batch_key = isset($_POST["batch_key"]) ? $_POST["batch_key"] : "";

//기타 파라메터 추가 부분 
$param_opt_1 = isset($_POST["param_opt_1"]) ? $_POST["param_opt_1"] : "";
$param_opt_2 = isset($_POST["param_opt_2"]) ? $_POST["param_opt_2"] : "";
$param_opt_3 = isset($_POST["param_opt_3"]) ? $_POST["param_opt_3"] : "";


//$good_name   = iconv('EUC-KR', 'UTF-8', $good_name);
//$card_name   = iconv('EUC-KR', 'UTF-8', $card_name);
//$buyr_name   = iconv('EUC-KR', 'UTF-8', $buyr_name);
//$res_msg   = iconv('EUC-KR', 'UTF-8', $res_msg);
//$res_msg   = iconv("cp949//IGNORE","UTF-8", $res_msg);
/* ============================================================================== */

if ($res_cd=="0000"){
	$ClassOrderPayBatchState = 1;
}else{
	$ClassOrderPayBatchState = 0;
}	


$Sql = " update ClassOrderPayBatchs set ";
$Sql .= "	ClassOrderPayBatchState=:ClassOrderPayBatchState, ";

$Sql .= "	ClassOrderPayBatchKey=:ClassOrderPayBatchKey, ";
$Sql .= "	batch_key=:batch_key, ";
$Sql .= "	req_tx=:req_tx, ";
$Sql .= "	res_cd=:res_cd, ";
$Sql .= "	res_msg=:res_msg, ";
$Sql .= "	ordr_idxx=:ordr_idxx, ";
$Sql .= "	good_mny=:good_mny, ";
$Sql .= "	card_cd=:card_cd, ";
$Sql .= "	card_name=:card_name, ";
$Sql .= "	param_opt_1=:param_opt_1, ";
$Sql .= "	param_opt_2=:param_opt_2, ";
$Sql .= "	param_opt_3=:param_opt_3, ";


$Sql .= "	ClassOrderPayBatchModiDateTime=now() ";
$Sql .= " where ClassOrderPayBatchNumber=:ordr_idxx ";

$Stmt = $DbConn->prepare($Sql);

$Stmt->bindParam(':ClassOrderPayBatchState', $ClassOrderPayBatchState);

$Stmt->bindParam(':ClassOrderPayBatchKey', $batch_key);
$Stmt->bindParam(':batch_key', $batch_key);
$Stmt->bindParam(':req_tx', $req_tx);
$Stmt->bindParam(':res_cd', $res_cd);
$Stmt->bindParam(':res_msg', $res_msg);
$Stmt->bindParam(':ordr_idxx', $ordr_idxx);
$Stmt->bindParam(':good_mny', $good_mny);
$Stmt->bindParam(':card_cd', $card_cd);
$Stmt->bindParam(':card_name', $card_name);
$Stmt->bindParam(':param_opt_1', $param_opt_1);
$Stmt->bindParam(':param_opt_2', $param_opt_2);
$Stmt->bindParam(':param_opt_3', $param_opt_3);

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
location.href = "../../mypage_payment_list.php";
</script>
</body>
</html>
<?php
include_once('../../includes/dbclose.php');
?>