<?php
$MemberLoginID = isset($_REQUEST["MemberLoginID"]) ? $_REQUEST["MemberLoginID"] : "";
$student_id = $MemberLoginID;
$secret_key = isset($_REQUEST["secret_key"]) ? $_REQUEST["secret_key"] : "";
$cid = isset($_REQUEST["cid"]) ? $_REQUEST["cid"] : "";
include( "_config.php" );

/**
 * 고객사의 커리큐럼를 선택박스 형식으로 리턴한다.
 *
 * - index.php 파일에서 $('#group'),change(), $('#category').change(), $('#book')change(), $('#unit').change() 이벤트 발생시 AJAX로 이 파일이 사용된다.
 * - 이 파일을 이용하여 $('#category'0, $('#book'), $('#unit') ID를 가지는 selectbox 요소가 순차적으로 생성된다.
 */

if ( $_POST['type'] == '과정' ) {
	$div_id = "category";
} else if ( $_POST['type'] == "교재" ) {
	$div_id = "book";
} else if ( $_POST['type'] == "유닛" ) {
	$div_id = "unit";
	$max_chapter_unit = $_POST["max_chapter_unit"];
	$current_chapter_unit = $_POST["current_chapter_unit"];
}

/**
 * [커리큘럼조회 API] > $_POST["type"]의 값에 따라 "과정", "교재", "유닛" 쿼리 
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
	$option["언어"] = 'en';	// 'en', 'jp', 'cn' 설정
	$option['타입'] = $_POST['type']; // "대분류", "과정", "교재", "유닛"
	$option['상위타입아이디'] = $_POST['type_id']; // 선택된 타입의 상위타입 아이디를 입력 (type='대분류'인 경우에는 '' 설정) 

	// [공통정보(Tail)]
	$option['code복호값출력'] = 'No'; // code 복호값을 출력하려면 'Yes'를 설정한다
	$option['고객사정의필드'] = "_get_curri.php - ".$_POST['type']." 조회 (".$member["id"].", ".$member["level"].")"; // 사용자정의 문자열로 고객사 시스템 파일 등을 지정할 수 있다 (디버깅 용도 등)	
	$option['API확장필드'] = ""; // 확장 기능을 위한 필드
}
$data = $JTW->query( "커리큘럼조회 API", $option );


$curriculum = array();
$curriculum["과정|교재|유닛"] = explode( "#", $data ); // "타입" 값에 따라 '과정', '교재', '유닛' 값중 하나가 된다.

if ( $_POST["type"] == "교재" ) {
	echo '<select name="'.$div_id.'" id="'.$div_id.'" style="height:30px;width:90%;margin-top:5px;"><option value="">::'.$_POST['type'].'를 선택하세요</option>';
} else {
	echo '<select name="'.$div_id.'" id="'.$div_id.'" style="height:30px;width:90%;margin-top:5px;"><option value="">::'.$_POST['type'].'을 선택하세요</option>';    
}

$idx = 0;
foreach ( $curriculum["과정|교재|유닛"] as $key => $value ) {

	$idx++;
	$arr = explode( "|", $value );

	$display_webbook_text = "";
	$have_contents_text = "";
	$etc_status_text = "";

	if ( $_POST['type'] == '과정' ) {  // 과정인 경우

		$fields['과정이름'] = $arr[0];
		$fields['과정한글이름'] = $arr[1];
		$fields['과정아이디'] = $arr[2];
		$fields['대분류아이디'] = $arr[3];
		$fields['학생웹북출력여부'] = $arr[4];
		$fields['실제컨텐츠존재여부'] = $arr[5];
		$fields['기타상태'] = $arr[6];
		if ( $fields['학생웹북출력여부'] == "No" ) { // 이 값이 No인 경우 학생용 웹북에서는 숨겨지도록 처리해 주세요.
			$display_webbook_text = " [★학생웹북미노출★] ";  
		}
		if ( $fields['실제컨텐츠존재여부'] == "No" ) { // 이 값이 'No'인 경우 실제 컨텐츠는 존재하지 않습니다. (예. 교재 없이 하는 프리토킹 과정 등)
			$have_contents_text = " [★No컨텐츠★] ";
		}
		if ( $fields['기타상태'] ) { // 이 값이 존재하는 경우 'Deprecated', 'Substitute' 같은 값이 올 수 있으며 이 경우 강사용 웹북에만 해당 문구가 표시되어 강사가 교재상태를 확인하여 사전에 교육받은 대로 적절히 대응할 수 있도록 처리해 주세요. 
			$etc_status_text = " [★".$fields['기타상태']."★] ";
		}
		echo '<option value="'.$fields['과정아이디'].'">'.$fields['과정이름'].' ('.$fields['과정한글이름'].') '.$display_webbook_text.$have_contents_text.$etc_status_text.'</option>';
	}

	else if ( $_POST['type'] == '교재' ) {  // 교재인 경우

		$fields['교재이름'] = $arr[0];
		$fields['교재한글이름'] = $arr[1];
		$fields['교재아이디'] = $arr[2];
		$fields['과정아이디'] = $arr[3];
		$fields['학생웹북출력여부'] = $arr[4];
		$fields['컨텐츠존재여부'] = $arr[5];
		{
			// '실제컨텐츠유부@오디오유무@자가학습유무'
			$sub_arr = explode( "@", $fields['컨텐츠존재여부'] );
			$sub_fields = array();
			$sub_fields["실제컨텐츠존재유무"] = $sub_arr[0];
			$sub_fields["오디오존재유무"] = $sub_arr[1];
			$sub_fields["자가학습존재유무"] = $sub_arr[2];
		}
		$fields['기타상태'] = $arr[6];
		$fields['열람권한'] = $arr[7];
		$fields['현재진도정보'] = $arr[8];
		$fields['최대열람가능유닛번호'] = $arr[9]; // JT-Webbook의 학생아이디에 대한 진도정보를 조사하여 현 교재에 대한 최대열람가능 유닛번호를 리턴한다.

		if ( $fields['학생웹북출력여부'] == "No" ) { // 이 값이 No인 경우 학생용 웹북에서는 숨겨지도록 처리해 주세요.
			$display_webbook_text = " [★학생웹북미노출★] ";  
		}
		if ( $fields['기타상태'] ) { // 이 값이 존재하는 경우 'Deprecated', 'Substitute' 같은 값이 올수 있으며 이경우 강사용 웹북에만 표시되어 강사가 확인 할수 있도록 처리해 주세요.
			$etc_status_text = " [★".$fields['기타상태']."★] ";
		}
		if ( $sub_fields["실제컨텐츠존재유무"] == "No" ) { // 이 값이 'No'인 경우 실제 컨텐츠는 존재하지 않습니다. (예. 교재 없이 하는 프리토킹 과정 등)
			$have_contents_text = " [★No컨텐츠★] ";
		}
		if ( $sub_fields["오디오존재유무"] == "Yes" ) { // 이 값이 'No'인 경우 오디오가 존재하지 않습니다.
			$have_contents_text = " [MP3] ";
		}
		if ( $sub_fields["자가학습존재유무"] == "Yes" ) { // 이 값이 'No'인 경우 자가학습 컨텐츠는 존재하지 않습니다.
			$have_contents_text = " [자가학습] ";
		}

		echo '<option value="'.$fields['교재아이디'].'" max-chapter-unit="'.$fields['최대열람가능유닛번호'].'" current-chapter-unit="'.$fields['현재진도정보'].'">'.$fields['교재이름'].' '.$display_webbook_text.$have_contents_text.'</option>';
	}

	else if ( $_POST['type'] == '유닛' ) {  // 유닛인 경우
		$fields['챕터'] = $arr[0];
		$fields['유닛번호'] = $arr[1];
		$fields['유닛제목'] = $arr[2];
		$fields['유닛아이디'] = $arr[3];
		$fields['교재아이디'] = $arr[4];
		$fields['유닛컨텐츠종류'] = $arr[5]; // '일반교재', '영자신문' 중 한 가지 상태값 리턴

		$chapter = $fields["챕터"];
		$unit_no = $fields["유닛번호"];
		$unit_text = $chapter."-".$unit_no;

		if ( $fields['챕터'].$fields['유닛번호'] == $current_chapter_unit ) { 			
			echo '<option value="'.$fields['유닛아이디'].'" unit_contents_type="'.$fields['유닛컨텐츠종류'].'">'.$unit_text.' : '.$fields['유닛제목'].' - [현재진도]</option>';		    			
		} else {
			echo '<option value="'.$fields['유닛아이디'].'" unit_contents_type="'.$fields['유닛컨텐츠종류'].'">'.$unit_text.' : '.$fields['유닛제목'].'</option>';		    			
		}
	}
}
echo '</select>';
?>