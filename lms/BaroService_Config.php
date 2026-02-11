<?php
/*
================================================================================================
바로빌 카드/계좌조회 연동서비스
version : 1.0 (2015-09)
		 
바로빌 연동개발지원 사이트
http://dev.barobill.co.kr/
================================================================================================
*/

//바로빌 연동서비스 웹서비스 참조(WebService Reference) URL	
//$BaroService_URL = 'http://testws.baroservice.com/BANKACCOUNT.asmx?WSDL';	//테스트베드용
$BaroService_URL = 'http://ws.baroservice.com/BANKACCOUNT.asmx?WSDL';		//실서비스용
//$CERTKEY = '77A4DCE4-D190-49E5-A1AE-8A000498BF22';			// 테스트용 인증키
$CERTKEY = 'B566EA7C-DF94-4DE6-8B6A-4F01A2529F17';			// 실운영용 인증키
$CorpNum = '1348630816';		//바로빌 회원 사업자번호 ('-' 제외, 10자리)

$ID = 'ansanslp';				//바로빌 회원 아이디
//$BankAccountNum = '100027577892';	//계좌번호


$BaroService_BANKACCOUNT = new SoapClient($BaroService_URL, array(
	'trace' => 'true',
	'encoding' => 'UTF-8' //소스를 ANSI로 사용할 경우 euc-kr로 수정
));
					
function getErrStr($CERTKEY, $ErrCode){
	global $BaroService_BANKACCOUNT;

	$ErrStr = $BaroService_BANKACCOUNT->GetErrString(array(
		'CERTKEY' => $CERTKEY,
		'ErrCode' => $ErrCode
	))->GetErrStringResult;

	return $ErrStr;
}

//바로빌 연동서비스 웹서비스 참조(WebService Reference) URL	
//$BaroService_URL = 'http://testws.baroservice.com/CARD.asmx?WSDL';	//테스트베드용
$BaroService_URL = 'http://ws.baroservice.com/CARD.asmx?WSDL';	//실서비스용

$BaroService_CARD = new SoapClient($BaroService_URL, array(
	'trace' => 'true',
	'encoding' => 'UTF-8' //소스를 ANSI로 사용할 경우 euc-kr로 수정
));
					
?>