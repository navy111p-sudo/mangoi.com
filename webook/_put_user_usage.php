<?php
$MemberLoginID = isset($_REQUEST["MemberLoginID"]) ? $_REQUEST["MemberLoginID"] : "";
$student_id = $MemberLoginID;
$secret_key = isset($_REQUEST["secret_key"]) ? $_REQUEST["secret_key"] : "";
$cid = isset($_REQUEST["cid"]) ? $_REQUEST["cid"] : "";
include_once( "_config.php" );

/**
 * 학생 진도 정보를 전송한다. (중요)
 *
 * - 학생진도 정보에 따라 학생의 교재 열람 범위가 동적으로 조절되므로 실제 수업진행에 따라 정확하게 진도 정보를 전송해야 한다.
 *
 * [대표적인 진도전송 시점]
 *  1. 첫 수업 스케줄링후 교재 정보를 설정하는 시점
 *  2. 매 수업이 끝나고 강사가 학습피드백을 등록하는 시점
 *  3. 스케줄링 변경등 교재를 변경하는 시점
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
	$option['수업종류'] = 'Phone'; 
	$option['수업날짜'] = date('Y-m-d'); 
	$option['수업시작시간'] = '06:00'; 
	$option['수업진행시간'] = '20'; 
	$option['유닛아이디'] = $_POST['unit_id']; 
	$option['교육센터아이디'] = $_cfg['교육센터아이디']; 
	$option['강사아이디'] = $_cfg['강사아이디']; 

	// [공통정보(Tail)]
	$option['code복호값출력'] = 'No'; // code 복호값을 출력하려면 'Yes'를 설정한다.
	$option['고객사정의필드'] = "_put_user_usage.php (".$member["id"].", ".$member["level"].")"; // 사용자정의 문자열로 고객사 시스템 파일 등을 지정할 수 있다 (디버깅 용도 등)	
	$option['API확장필드'] = ''; // 확장 기능을 위한 필드	
}
$data = $JTW->query( "학생진도전송 API", $option );
echo $data;
?>