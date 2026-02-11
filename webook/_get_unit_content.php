<?php
$MemberLoginID = isset($_REQUEST["MemberLoginID"]) ? $_REQUEST["MemberLoginID"] : "";
$student_id = $MemberLoginID;
$secret_key = isset($_REQUEST["secret_key"]) ? $_REQUEST["secret_key"] : "";
$cid = isset($_REQUEST["cid"]) ? $_REQUEST["cid"] : "";
include_once( "_config.php" );

/**
 * 유닛아이디에에 해당하는 컨텐츠를 표시
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
	$option['컨텐츠타입'] = $_POST['content_type']; // ('학생', '학생(lite)', '강사' 설정)
	$option['IFRAME가로크기'] = $_POST['width']; // 컨텐츠 가로크기를 설정 ('100%', '800' 등 %또는 정수형으로 설정)
	$option['IFRAME세로크기'] = $_POST['height']; // 컨텐츠 세로크기를 설정
	$option['IFRAME확장필드'] = 'Yes@Yes'; // 
	$option['IFRAME테마'] = ''; // 확장필드

	// [공통정보(Tail)]
	$option['code복호값출력'] = 'No'; // code 복호값을 출력하려면 'Yes'를 설정한다
	$option['고객사정의필드'] = "_get_unit_content.php (".$member["id"].", ".$member["level"].")"; // 사용자정의 문자열로 고객사 시스템 파일 등을 지정할 수 있다 (디버깅 용도 등)	
	$option['API확장필드'] = $_POST['api_extension']; // 확장 기능을 위한 필드

	// 기타 파라메터
	$option['유닛아이디'] = $_POST['unit_id']; // 실행하고자 하는 이북의 유닛아이디 설정
	
}
$data = $JTW->query( "유닛컨텐츠출력 API", $option );

if ( $option['API확장필드'] == "Additional Contents" ) {
	if ( $_POST['unit_contents_type'] != '영자신문' ) {	// 영자신문은 'Additional Contents'를 지원하지 않는다 (iframe에 통합 지원)
		$fields = explode( '|', $data );
		$audio_url = $fields[0];
		echo $audio_url;
		exit;
	}
} else if ( $option['API확장필드'] == "Raw Contents" ) {
	if ( $_POST['unit_contents_type'] == '영자신문' ) {	// 영자신문은 'Raw Contents'를 지원하지 않으므로 iframe 리턴받는 방식을 이용한다.
		echo $data;
	} else {
		$fields = explode( '|', $data );
		$idx = 1;
		foreach ( $fields as $v ) {
			if ( $v ) {
				echo "page ".$idx++."<br>";
				echo '<div><img src="'.$v.'" width="100%" title="page '.($idx-1).'"></div>';
			}
		}
		exit;    
	}	
} else {
  echo $data;
}
	

