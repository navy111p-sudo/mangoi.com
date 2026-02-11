<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title></title>
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" id="viewport" />
<body>

<?
//kcp와 통신후 kcp 서버에서 전송되는 결제 요청 정보 
$req_tx = isset($_POST["req_tx"]) ? $_POST["req_tx"] : "";
$res_cd = isset($_POST["res_cd"]) ? $_POST["res_cd"] : "";
$tran_cd = isset($_POST["tran_cd"]) ? $_POST["tran_cd"] : "";
$ordr_idxx = isset($_POST["ordr_idxx"]) ? $_POST["ordr_idxx"] : "";
$good_name = isset($_POST["good_name"]) ? $_POST["good_name"] : "";
$good_mny = isset($_POST["good_mny"]) ? $_POST["good_mny"] : "";
$buyr_name = isset($_POST["buyr_name"]) ? $_POST["buyr_name"] : "";
$buyr_tel1 = isset($_POST["buyr_tel1"]) ? $_POST["buyr_tel1"] : "";
$buyr_tel2 = isset($_POST["buyr_tel2"]) ? $_POST["buyr_tel2"] : "";
$buyr_mail = isset($_POST["buyr_mail"]) ? $_POST["buyr_mail"] : "";
$use_pay_method = isset($_POST["use_pay_method"]) ? $_POST["use_pay_method"] : "";
$enc_info = isset($_POST["enc_info"]) ? $_POST["enc_info"] : "";
$enc_data = isset($_POST["enc_data"]) ? $_POST["enc_data"] : "";

// 기타 파라메터 추가 부분
$param_opt_1 = isset($_POST["param_opt_1"]) ? $_POST["param_opt_1"] : "";
$param_opt_2 = isset($_POST["param_opt_2"]) ? $_POST["param_opt_2"] : "";
$param_opt_3 = isset($_POST["param_opt_3"]) ? $_POST["param_opt_3"] : "";
?>

<div style="display:none;">
<form name="pay_form" method="post" action="pp_cli_hub.php">
    <input type="text" name="req_tx"         value="<?=$req_tx?>">               <!-- 요청 구분          -->
    <input type="text" name="res_cd"         value="<?=$res_cd?>">               <!-- 결과 코드          -->
    <input type="text" name="tran_cd"        value="<?=$tran_cd?>">              <!-- 트랜잭션 코드      -->
    <input type="text" name="ordr_idxx"      value="<?=$ordr_idxx?>">            <!-- 주문번호           -->
    <input type="text" name="good_mny"       value="<?=$good_mny?>">             <!-- 휴대폰 결제금액    -->
    <input type="text" name="good_name"      value="<?=$good_name?>">            <!-- 상품명             -->
    <input type="text" name="buyr_name"      value="<?=$buyr_name?>">            <!-- 주문자명           -->
    <input type="text" name="buyr_tel1"      value="<?=$buyr_tel1?>">            <!-- 주문자 전화번호    -->
    <input type="text" name="buyr_tel2"      value="<?=$buyr_tel2?>">            <!-- 주문자 휴대폰번호  -->
    <input type="text" name="buyr_mail"      value="<?=$buyr_mail?>">            <!-- 주문자 E-mail      -->	
    <input type="text" name="enc_info"       value="<?=$enc_info?>">
    <input type="text" name="enc_data"       value="<?=$enc_data?>">
    <input type="text" name="use_pay_method" value="<?=$use_pay_method?>">   

    <!-- 추가 파라미터 -->
	<input type="text" name="param_opt_1"	   value="<?=$param_opt_1?>">
	<input type="text" name="param_opt_2"	   value="<?=$param_opt_2?>">
	<input type="text" name="param_opt_3"	   value="<?=$param_opt_3?>">
</form>
</div>

<script>
/* kcp 통신을 통해 받은 암호화 정보 체크 후 결제 요청 (변경불가) */
function chk_pay()
{
	self.name = "tar_opener";
	var pay_form = document.pay_form;

	if (pay_form.res_cd.value == "3001" )
	{
		alert("사용자가 취소하였습니다.");
		pay_form.res_cd.value = "";
	}

	if (pay_form.enc_info.value)
	{
		pay_form.submit();
	}
}

chk_pay();
</script>
</body>
</html>