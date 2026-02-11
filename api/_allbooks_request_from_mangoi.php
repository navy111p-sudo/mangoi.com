<?php
// 주문 정보 메인
$OrderNumber		=	isset($_REQUEST["OrderNumber"]) ? $_REQUEST["OrderNumber"] : "";	//주문번호(망고아이 주문번호)			, varchar(30)
$MemberName			=	isset($_REQUEST["MemberName"]) ? $_REQUEST["MemberName"] : "";		//주문자 이름						, varchar(50)
$RcMemberName		=	isset($_REQUEST["RcMemberName"]) ? $_REQUEST["RcMemberName"] : "";	//수취인 이름						, varchar(50)
$RcPhone1			=	isset($_REQUEST["RcPhone1"]) ? $_REQUEST["RcPhone1"] : "";			//전화번호 1						, varchar(15)
$RcPhone2			=	isset($_REQUEST["RcPhone2"]) ? $_REQUEST["RcPhone2"] : "";			//전화번호 2						, varchar(15)
$RcZipCode			=	isset($_REQUEST["RcZipCode"]) ? $_REQUEST["RcZipCode"] : "";		//우편번호							, varchar(10)
$RcAddr1			=	isset($_REQUEST["RcAddr1"]) ? $_REQUEST["RcAddr1"] : "";			//주소 1							, varchar(250)
$RcAddr2			=	isset($_REQUEST["RcAddr2"]) ? $_REQUEST["RcAddr2"] : "";			//주소 2(번지, 호수 등)				, varchar(250)
$RcMemo				=	isset($_REQUEST["RcMemo"]) ? $_REQUEST["RcMemo"] : "";				//주문 메시지(경비실에 맡겨주세요 등)		, text

// 주문 상품 $OrderProducts
// 상품 정보는 '/*/' 로 구분하여 상품고유번호, 상품명, 수량을 전송합니다. 상품 고유번호는 올북스에서 관리하는 고유번호 입니다.
// 예) 상품고유번호/*/상품명/*/수량 형식 ==> 'ABC000111/*/초등영어 1/*/3'
// 상품 종류가 여러개 일경우 상품과 상품을 '/**/'로 구분 합니다.
// 예) 상품고유번호/*/상품명/*/수량/**/상품고유번호/*/상품명/*/수량/**/상품고유번호/*/상품명/*/수량 ==> 'ABC000111/*/초등영어 1/*/1/**/ABC000222/*/초등영어 2/*/2/**/ABC000333/*/초등영어 3/*/2'
$OrderProducts		=	isset($_REQUEST["OrderProducts"]) ? $_REQUEST["OrderProducts"] : "";
// 상품 종류 수 $OrderProductCount
// 참고사항이며 배송해야할 상품이 초등영어 1, 초등영어 2, 초등영어 3 일경우 3을 전송 합니다.
$OrderProductCount	=	isset($_REQUEST["OrderProductCount"]) ? $_REQUEST["OrderProductCount"] : "";



//************************** DB 처리 **************************//

	//올북스 DB를 처리해 주세요.
	//DB insert 전에 $OrderNumber 중복 체크를 해주세요.
	//망고아이 측에서 네트워크 문제로 'OK' 를 수신하지 못하면 계속 보내기 때문에 동일한 주문건이 전송될 수 있습니다.


	// DB 처리가 정상적으로 완료될 경우 'OK' 를 반환해 주세요.
	// 'OK' 이후에는 어떤 문자도 추가되어선 안됩니다.
	// 'OK' 가 반환되지 않을경우 망고아이에서는 올북스 측으로 한시간 마다 계속 전송합니다.
	echo "OK";

//************************** DB 처리 **************************//
?>
