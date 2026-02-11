<?php
// 올북스에서 상품 배송 후에 배송 결과를 망고아이쪽으로 전송하는 예제 입니다.
// 주문번호(망고아이 주문번호)와 송장번호를 $ShipResults 변수에 보내주시면 됩니다.
// 전송시간 정보 $ShipResultSendDateTime 은 참고사항으로 시간 정보를 전송해 주시면 됩니다.



// post 전송 ===========================
$RemoteHost = "mangoi.co.kr";					//변경불가
$RemotePath = "/api/allbooks_send_result.php";	//변경불가


$ShipResults = "";
// 배송 결과 $ShipResults
// 배송 결과는 '/*/' 로 구분하여 주문번호(망고아이 주문번호),택배사명,송장번호 전송해 주세요.(택배사명은 참고사항으로 'CJ대한통운' 등 임의로 정해서 보내주시면 됩니다)
// 예) 주문번호(망고아이 주문번호)/*/택배사명/*/송장번호 형식 ==> 'BDH202012031402160000000087/*/CJ대한통운/*/888899993333555'
// 한번에 여러개 배송 정보를 보낼경우 결과와 결과를을 '/**/'로 구분 합니다.
// 예) 주문번호(망고아이 주문번호)/*/택배사명/*/송장번호/**/주문번호(망고아이 주문번호)/*/택배사명/*/송장번호/**/주문번호(망고아이 주문번호)/*/택배사명/*/송장번호 ==> 'BDH2020120314022360000000099/*/CJ대한통운/*/888899993333666/**/BDH202012031402160000000099/*/CJ대한통운/*/888899993333223/*/BDH202012031402160000000044/*/CJ대한통운/*/888899993333777'



$param = "ShipResults=".$ShipResults;
$param .= "&ShipResultSendDateTime=".time();

$fp = @fsockopen($RemoteHost,80,$errno,$errstr,30);
$return = "";
if (!$fp) {
	echo $errstr."(".$errno.")";
} else {
	fputs($fp, "POST ".$RemotePath." HTTP/1.1\r\n");
	fputs($fp, "Host: ".$RemoteHost."\r\n");
	fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
	fputs($fp, "Content-length: ".strlen($param)."\r\n");
	fputs($fp, "Connection: close\r\n\r\n");
	fputs($fp, $param."\r\n\r\n");
	while(!feof($fp)) $return .= fgets($fp,4096);
}
fclose ($fp);
// post 전송 ===========================



//전송 상태 업데이트 =========================== 
$return = str_replace("\r", "", $return);
$return = str_replace("\n", "", $return);
$return = str_replace("\t", "", $return);
$return = trim($return);


//망고아이 측에서 전송하신 결과를 정상적으로 수신할 경우 'OK' 를 반환합니다.
if (substr($return,-2)=="OK") {

	//올북스 DB 처리

}


?>