<?php
$MemberLoginID = isset($_REQUEST["MemberLoginID"]) ? $_REQUEST["MemberLoginID"] : "";
$student_id = $MemberLoginID;
$secret_key = isset($_REQUEST["secret_key"]) ? $_REQUEST["secret_key"] : "";
$cid = isset($_REQUEST["cid"]) ? $_REQUEST["cid"] : "";
include_once( "_config.php" );

/**
 * 타입정보조회 API
 */
{
	$option = array();

	// [공통정보(Head)]
	/*
	$option['보안코드'] = ''; // class에서 설정됨.
	$option['고객사도메인'] = ''; // class에서 설정됨.
	$option['접속단말종류'] = ''; // class에서 설정됨.
	$option['실행일시'] = ''; // class에서 설정됨.
	$option["학생아이디"] = ''; // class에서 설정됨.
	*/

	// [API 정보(Content)]	
	$option['언어'] = 'en';
	$option['타입'] = $_POST['type']; 
	$option['타입아이디'] = $_POST['type_id']; 

	// [공통정보(Tail)]
	$option['code복호값출력'] = 'No'; // code 복호값을 출력하려면 'Yes'를 설정한다.
	$option['고객사정의필드'] = "_get_type_info.php (".$member["id"].", ".$member["level"].")"; // 사용자정의 문자열로 고객사 시스템 파일 등을 지정할 수 있다 (디버깅 용도 등)	
	$option['API확장필드'] = ''; // 확장 기능을 위한 필드	
}
$data = $JTW->query( "타입정보조회 API", $option );


$arr = explode( "|", $data );
$arr_idx = 0;

$field['대분류아이디'] = $arr[$arr_idx++];
$field['대분류이름'] = $arr[$arr_idx++];
$field['대분류이름(한글)'] = $arr[$arr_idx++];

$field['과정아이디'] = $arr[$arr_idx++];
$field['과정이름'] = $arr[$arr_idx++];
$field['과정이름(한글)'] = $arr[$arr_idx++];
$field['과정웹북출력여부'] = $arr[$arr_idx++];
$field['과정컨텐츠존재여부'] = $arr[$arr_idx++];
$field['과정기타상태'] = $arr[$arr_idx++];

$field['교재아이디'] = $arr[$arr_idx++];
$field['교재이름'] = $arr[$arr_idx++];
$field['교재이름(한글)'] = $arr[$arr_idx++];
$field['교재웹북출력여부'] = $arr[$arr_idx++];
$field['교재컨텐츠존재여부'] = $arr[$arr_idx++];	
$field['교재기타상태'] = $arr[$arr_idx++];	

$field['유닛아이디'] = $arr[$arr_idx++];	
$field['챕터번호'] = $arr[$arr_idx++];	
$field['유닛번호'] = $arr[$arr_idx++];	
$field['유닛제목'] = $arr[$arr_idx++];	

/**
 * 이 예제에서는 리턴되는 정보를 alert로 출력시켜주는 역할만 함.
 * 고객사에서 구현시 필요한 부분에 적절히 활용해 주세요.
 */
print_r( $field );
?>