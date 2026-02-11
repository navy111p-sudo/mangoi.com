<?php
/**
 * 데모 설정파일
 *
 * - 데모는 구현의 예시이므로 고객사 상황에 맞도록 수정하거나 새롭게 구현해서 사용할 수 있습니다.
 */

/**
 * 접속자 아이디 & 레벨 (이 값은 API 필수값은 아니지만, 이 예제에서는 각 API의 [고객사정의필드]에 이 정보가 설정되도록 하여, 각종 로그파일에서 접속자 정보를 추적하는 용도로 사용하였다)
 *
 * 현재 로그인한 회원 정보를 입력한다.
 * - 현재 접속자는 [학생], [강사], [관리자] 등이 될 수 있으며, [학생]인 경우 $member[id]와 아래 $student_id가 동일한 값이 되도록 설정하면 된다.
 * - 접속자가 [강사], [관리자]인 경우에도 특정 학생의 교재 정보를 보는 경우이므로 $student_id에는 교재열람할 학생의 id가 설정되도록 한다.
 */
$MemberLevelID = "student";

$member["id"] = "tester"; // 접속자 아이디 (학생아이디, 관리자아이디, 강사아이디 등)
$member['level'] = $MemberLevelID; // 학생, 관리자, 강사 등의 접속자 레벨 설정	('student', 'admin', 'teacher' 중 하나 입력)

/**
 * 학생아이디
 *
 * - 이 값은 모든 API 파라메터의 필수 값으로 이용된다.
 * - 접속자가 [학생] 자신일 경우 접속자 아이디를 설정한다.
 * - 접속자가 [강사/관리자]의 경우 특정 학생의 수업에 대한 정보를 열람하는 경우이므로 해당 수업의 학생아이디를 $student_id에 설정하면 된다
 * - [비회원]이 열람하는 경우에는 빈공백을 입력한다.
 */

//$student_id = 'hycc';
//$student_id = 'phptester';
//$student_id = $MemberLoginID; // 데모에서는 고정값으로 지정하고 있지만 구현시에는 변수로 설정해주세요. (접속자가 학생인 경우 접속자아이디, 강사/관리자인 경우 특정 수업에 대한 학생아이디)

/**
 * API 호출을 위한 공통정보 설정 
 */
$_cfg = array();

// API에서 사용되는 공통 옵션 설정
$_cfg["고객사아이디"] = $cid; // JT-Webbook에서 고객사에 발급해주는 고객사아이디 설정 	
$_cfg["보안코드"] = $secret_key; // JT-Webbook에서 고객사에 발급해주는 보안코드 설정 (보안코드가 외부에 노출되지 않도록 주의해 주세요)	

$_cfg["고객사도메인"] =  $_SERVER['HTTP_HOST']; // 고객사 웹서버 도메인 설정 (고객사에서 웹북 사용 로그나 각종 통계를 구분하기 위한 데이터로 활용가능)	
$_cfg["실행일시"] = date( "YmdHis" ); // 실행일시를 'YYYYMMDDHHiiss' 형식으로 설정 (고객사 서버의 시간이 실제 시간과 동기화 되어 있는지 확인 필요, HH는 24시 표기법임)
$_cfg["학생아이디"] = $student_id; // 학생아이디 입력, 없는 경우 빈공백 입력할 것

// 각 API URL 정보 설정 (API URL을 변경시 쉬운 적용을 위하여 아래와 같이 전역변수로 정의하여 사용할 것)
$_cfg["암호화API"] = "https://api.j-touch.com/crypto/encrypt.php"; // (주의: https 프로토콜을 사용함)
$_cfg["커리큘럼조회API"] = "http://api.j-touch.com/webbook/v5.0.1/get_curriculum.php";
$_cfg["유닛컨텐츠출력API"] = "http://api.j-touch.com/webbook/v5.0.1/get_unit_content.php";
$_cfg["학생진도전송API"] = "http://api.j-touch.com/webbook/v5.0.1/put_user_usage.php";
$_cfg["학생진도조회API"] = "http://api.j-touch.com/webbook/v5.0.1/get_user_usage.php";
$_cfg["타입정보조회API"] = "http://api.j-touch.com/webbook/v5.0.1/get_type_info.php";
$_cfg["학생웹북모듈"] = "http://api.j-touch.com/webbook/v5.0.1/modules/student/index.php";
$_cfg["음원플레이어모듈"] = "http://api.j-touch.com/webbook/v5.0.1/modules/audio/player.php";
$_cfg["음원다운로드모듈"] = "http://api.j-touch.com/webbook/v5.0.1/modules/audio/download.php";

/**
 * 강사 정보 설정 ('학생진도전송API'에서 사용됨)
 *
 * - 접속자가 강사/관리자인 경우 사용된다.
 * - 진도정보는 특정 수업과 관련이 있으므로 해당 수업이 어떤 강사의 수업이었는지 확인하기 위한 정보인다. (필수 정보는 아님, 모니터링 용도) 
 * - '교육센터아아디'는 강사 소속을 의미하는 코드값을 입력할 수 있다.
 */
{
	$_cfg["교육센터아이디"] = "educenter_1"; // 강사가 소속된 교육센터 아이디 입력 
	$_cfg["강사아이디"] = "teacher_1"; // 학생과 수업중인 강사 아이디
}

/**
 * API 호출 데모 class 정의
 *
 * - 고객사 연동 메뉴얼(https://goo.gl/YOjRhr)을 이용하여 고객사 서버 환경에 맞게 구현해 주세요. (아래는 구현 예시입니다)
 */
class JtouchWebbook {

	private $config = array();

	/**
	 * 생성자 실행 (고객사 기본 환경설정)
	 */
	public function __construct() 
	{
		global $_cfg;

		// 각 API의 공통 부분 설정
		$this->config["고객사아이디"] = $_cfg["고객사아이디"];

		// API code 공통정보(Head) 설정
		$this->config["보안코드"] = $_cfg["보안코드"];
		$this->config["고객사도메인"] =  $_cfg["고객사도메인"];
		$this->config["접속단말종류"] =  $this->get_UA_type();
		$this->config["실행일시"] = $_cfg["실행일시"];
		$this->config["학생아이디"] = $_cfg["학생아이디"];

		// 각 API 호출 URL 정보 설정 (API URL을 변경시 쉬운 적용을 위하여 아래와 같이 전역으로 정의하여 사용할 것)
		$this->config["암호화API"] = $_cfg["암호화API"];
		$this->config["커리큘럼조회API"] = $_cfg["커리큘럼조회API"];
		$this->config["유닛컨텐츠출력API"] = $_cfg["유닛컨텐츠출력API"];
		$this->config["학생진도전송API"] = $_cfg["학생진도전송API"];
		$this->config["학생진도조회API"] = $_cfg["학생진도조회API"];
		$this->config["타입정보조회API"] = $_cfg["타입정보조회API"];

		$this->config["학생웹북모듈"] = $_cfg["학생웹북모듈"];
	} 

	/**
	 * API 쿼리 함수
	 *
	 * - API 별로 전문을 구성하여 쿼리하는 메소드 입니다.
	 *
	 * @param string $name 실행할 API 이름
	 * @param array $option {$name}에서 추가로 필요한 입력 필드를 배열 형태로 설정한다. (API 연동 메뉴얼 참고)
	 */
	public function query( $name, $option )
	{
		$params = array();

		/**
		 * [커리큘럼조회 모듈] API 설정
		 *
		 * - _get_curri.php 파일에서 $option 값들이 설정됨
		 */
		if ( $name == "커리큘럼조회 API" ) {
			
			$params['cid'] = $this->config['고객사아이디']; // 고객사 아이디 설정
			$params['code'] = ""; // 암호화할 필드를 설정한다. (아래에서 설정함)
			{
				// code 필드 설정
				$code = array();

				// [공통정보(Head)]
				$code['보안코드'] = $this->config['보안코드']; // 고객사 보안코드 설정 
				$code['고객사도메인'] = $this->config["고객사도메인"]; // 고객사 도메인 정보 설정
				$code['접속단말종류'] = $this->config["접속단말종류"]; // 접속자 단말기 종류를 설정('desktop', 'mobile)
				$code['실행일시'] = $this->config["실행일시"]; // 실행일시를 'YYYYMMDDHHiiss' 형식으로 설정 (고객사 서버의 시간이 실제 시간과 동기화 되어 있는지 확인 필요, HH는 24시 표기법임)
				$code['학생아이디'] = $this->config['학생아이디']; // 학생아이디 설정, 비회원인경우 빈공백 입력
				
				// [API 정보(Content)]																			   				
				$code['언어'] = $option["언어"]; // 'en', 'jp', 'cn' 설정
				$code['타입'] = $option["타입"]; // "대분류", "과정", "교재", "유닛"
				$code['상위타입아이디'] = $option["상위타입아이디"]; // 선택된 타입의 상위타입 아이디를 입력 (type='대분류'인 경우에는 '' 설정)				

				// [공통정보(Tail)]
				$code['code복호값출력'] = $option["code복호값출력"]; // code 복호값을 출력하려면 'Yes'를 설정한다.
				$code['고객사정의필드'] = $option["고객사정의필드"]; // 웹북 관리페이지의 에러로그목록에서 고객사 에러 발생 위치를 추적하기 우해 활용할 수 있다.				
				$code['API확장필드'] = $option["API확장필드"]; // 확장 기능을 위한 필드				

				// 각 필드를 '|'을 구분자로 하여 평문 구성한다.
				$code_str = $this->create_code_str( $code );

				// 암호화API를 이용하여 code_str값 암호화 한다. 
				$enc_data =  $this->encrypt( $code_str );
			
				$params['code'] = $enc_data; // code_str을 암호화 한 값을 $param["code"]에 설정한다.
			}

			// [커리큘럼조회 모듈] API 쿼리
			return $this->query_api( $this->config["커리큘럼조회API"], $params ); // $params을 POST로 API를 실행한 실행 결과를 $data에 저장한다.		
		}

		/**
		 * 특정 유닛에 대한 컨텐츠(실제 교재 내용)를 출력하는 IFRAME을 리턴한다.
		 */
		else if ( $name == "유닛컨텐츠출력 API" ) {
			
			$params['cid'] = $this->config['고객사아이디']; // 고객사 아이디 설정
			$params['code'] = ""; // 암호화할 필드를 설정한다. (아래에서 설정함)
			{
				// code 필드 설정
				$code = array();
				$option['가로크기'] = $_POST['width'];

				// [공통정보(Head)]
				$code['보안코드'] = $this->config['보안코드']; // 고객사 보안코드 설정 
				$code['고객사도메인'] = $this->config["고객사도메인"]; // 고객사 도메인 정보 설정
				$code['접속단말종류'] = $this->config["접속단말종류"]; // 접속자 단말기 종류를 설정('desktop', 'mobile)
				$code['실행일시'] = $this->config["실행일시"]; // 실행일시를 'YYYYMMDDHHiiss' 형식으로 설정 (고객사 서버의 시간이 실제 시간과 동기화 되어 있는지 확인 필요, HH는 24시 표기법임)
				$code['학생아이디'] = $this->config['학생아이디']; // 학생아이디 설정
				
				// [API 정보(Content)]													 				
				$code['컨텐츠타입'] = $option['컨텐츠타입']; // ('학생', '학생(lite)', '강사' 설정)
				$code['IFRAME가로크기'] = $option['가로크기']; // 컨텐츠 가로크기를 설정 ('100%', '800' 등 %또는 정수형으로 설정)
				$code['IFRAME세로크기'] = $option['IFRAME세로크기']; // 컨텐츠 세로크기를 설정
				$code['IFRAME확장필드'] = $option["IFRAME확장필드"];
				$code['IFRAME테마'] = $option["IFRAME테마"];

				// [공통정보(Tail)]
				$code['code복호값출력'] = $option["code복호값출력"]; // code 복호값을 출력하려면 'Yes'를 설정한다.
				$code['고객사정의필드'] = $option["고객사정의필드"]; // 웹북 관리페이지의 에러로그목록에서 고객사 에러 발생 위치를 추적하기 우해 활용할 수 있다. 
				$code['API확장필드'] = $option["API확장필드"]; // 확장 기능을 위한 필드
				
				// 각 필드를 '|'을 구분자로 하여 평문 구성한다.
				$code_str = $this->create_code_str( $code );

				// 암호화API를 이용하여 code_str값 암호화 한다. 
				$enc_data =  $this->encrypt( $code_str );
				
				$params['code'] = $enc_data; // code_str	을 암호화 한 값을 $param["code"]에 설정한다.
			}
			$params["unit_id"] = $option["유닛아이디"];


			// API 쿼리
			return $this->query_api( $this->config["유닛컨텐츠출력API"], $params ); // $params을 POST로 API를 실행한 실행 결과를 $data에 저장한다.
		}


		/**
		 * 학생진도 정보 전송 API
		 */
		else if ( $name == "학생진도전송 API" ) {
			
			$params['cid'] = $this->config['고객사아이디']; // 고객사 아이디 설정
			$params['code'] = ""; // 암호화할 필드를 설정한다. (아래에서 설정함)
			{
				// code 필드 설정
				$code = array();

				// [공통정보(Head)]
				$code['보안코드'] = $this->config['보안코드']; // 고객사 보안코드 설정 
				$code['고객사도메인'] = $this->config["고객사도메인"]; // 고객사 도메인 정보 설정
				$code['접속단말종류'] = $this->config["접속단말종류"]; // 접속자 단말기 종류를 설정('desktop', 'mobile)
				$code['실행일시'] = $this->config["실행일시"]; // 실행일시를 'YYYYMMDDHHiiss' 형식으로 설정 (고객사 서버의 시간이 실제 시간과 동기화 되어 있는지 확인 필요, HH는 24시 표기법임)
				$code['학생아이디'] = $this->config['학생아이디']; // 학생아이디 설정
				
				// [API 정보(Content)]													 				
				$code['언어'] = $option['언어']; 
				$code['수업종류'] = $option['수업종류'];
				$code['수업날짜'] = $option['수업날짜']; 
				$code['수업시작시간'] = $option["수업시작시간"];
				$code['수업진행시간'] = $option["수업진행시간"];
				$code['유닛아이디'] = $option["유닛아이디"];
				$code['교육센터아이디'] = $option["교육센터아이디"];
				$code['강사아이디'] = $option["강사아이디"];

				// [공통정보(Tail)]
				$code['code복호값출력'] = $option["code복호값출력"]; // code 복호값을 출력하려면 'Yes'를 설정한다.
				$code['고객사정의필드'] = $option["고객사정의필드"]; // 웹북 관리페이지의 에러로그목록에서 고객사 에러 발생 위치를 추적하기 위해 활용할 수 있다. 
				$code['API확장필드'] = $option["API확장필드"]; // 확장 기능을 위한 필드

				// 각 필드를 '|'을 구분자로 하여 평문 구성한다.
				$code_str = $this->create_code_str( $code );

				// 암호화API를 이용하여 code_str값 암호화 한다. 
				$enc_data =  $this->encrypt( $code_str );
				
				$params['code'] = $enc_data; // code_str	을 암호화 한 값을 $param["code"]에 설정한다.
			}

			return $this->query_api( $this->config["학생진도전송API"], $params ); // $params을 POST로 API를 실행한 실행 결과를 $data에 저장한다.		
		}


		/**
		 * 학생진도조회 API
		 */
		else if ( $name == "학생진도조회 API" ) {
			
			$params['cid'] = $this->config['고객사아이디']; // 고객사 아이디 설정
			$params['code'] = ""; // 암호화할 필드를 설정한다. (아래에서 설정함)
			{
				// code 필드 설정
				$code = array();

				// [공통정보(Head)]
				$code['보안코드'] = $this->config['보안코드']; // 고객사 보안코드 설정 
				$code['고객사도메인'] = $this->config["고객사도메인"]; // 고객사 도메인 정보 설정
				$code['접속단말종류'] = $this->config["접속단말종류"]; // 접속자 단말기 종류를 설정('desktop', 'mobile)
				$code['실행일시'] = $this->config["실행일시"]; // 실행일시를 'YYYYMMDDHHiiss' 형식으로 설정 (고객사 서버의 시간이 실제 시간과 동기화 되어 있는지 확인 필요, HH는 24시 표기법임)
				$code['학생아이디'] = $this->config['학생아이디']; // 학생아이디 설정
				
				// [API 정보(Content)]													 				
				$code['언어'] = $option['언어']; 
				$code['타입'] = $option['타입'];
				$code['타입아이디'] = $option['타입아이디']; 

				// [공통정보(Tail)]
				$code['code복호값출력'] = $option["code복호값출력"]; // code 복호값을 출력하려면 'Yes'를 설정한다.
				$code['고객사정의필드'] = $option["고객사정의필드"]; // 웹북 관리페이지의 에러로그목록에서 고객사 에러 발생 위치를 추적하기 우해 활용할 수 있다. 
				$code['API확장필드'] = $option["API확장필드"]; // 확장 기능을 위한 필드

				// 각 필드를 '|'을 구분자로 하여 평문 구성한다.
				$code_str = $this->create_code_str( $code );

				// 암호화API를 이용하여 code_str값 암호화 한다. 
				$enc_data =  $this->encrypt( $code_str );
				
				$params['code'] = $enc_data; // code_str	을 암호화 한 값을 $param["code"]에 설정한다.
			}

			return $this->query_api( $this->config["학생진도조회API"], $params ); // $params을 POST로 API를 실행한 실행 결과를 $data에 저장한다.		
		}

		/**
		 * 타입정보조회 API
		 */
		else if ( $name == "타입정보조회 API" ) {
			
			$params['cid'] = $this->config['고객사아이디']; // 고객사 아이디 설정
			$params['code'] = ""; // 암호화할 필드를 설정한다. (아래에서 설정함)
			{
				// code 필드 설정
				$code = array();

				// [공통정보(Head)]
				$code['보안코드'] = $this->config['보안코드']; // 고객사 보안코드 설정 
				$code['고객사도메인'] = $this->config["고객사도메인"]; // 고객사 도메인 정보 설정
				$code['접속단말종류'] = $this->config["접속단말종류"]; // 접속자 단말기 종류를 설정('desktop', 'mobile)
				$code['실행일시'] = $this->config["실행일시"]; // 실행일시를 'YYYYMMDDHHiiss' 형식으로 설정 (고객사 서버의 시간이 실제 시간과 동기화 되어 있는지 확인 필요, HH는 24시 표기법임)
				$code['학생아이디'] = $this->config['학생아이디']; // 학생아이디 설정
				
				// [API 정보(Content)]													 				
				$code['언어'] = $option['언어']; 
				$code['타입'] = $option['타입'];
				$code['타입아이디'] = $option['타입아이디']; 

				// [공통정보(Tail)]
				$code['code복호값출력'] = $option["code복호값출력"]; // code 복호값을 출력하려면 'Yes'를 설정한다.
				$code['고객사정의필드'] = $option["고객사정의필드"]; // 웹북 관리페이지의 에러로그목록에서 고객사 에러 발생 위치를 추적하기 우해 활용할 수 있다. 
				$code['API확장필드'] = $option["API확장필드"]; // 확장 기능을 위한 필드

				// 각 필드를 '|'을 구분자로 하여 평문 구성한다.
				$code_str = $this->create_code_str( $code );

				// 암호화API를 이용하여 code_str값 암호화 한다. 
				$enc_data =  $this->encrypt( $code_str );
				
				$params['code'] = $enc_data; // code_str	을 암호화 한 값을 $param["code"]에 설정한다.
			}

			return $this->query_api( $this->config["타입정보조회API"], $params ); // $params을 POST로 API를 실행한 실행 결과를 $data에 저장한다.		
		}


		/**
		 * JT-Webbook 에서 제공하는 학생웹북 모듈
		 */
		else if ( $name == "학생웹북 모듈" ) {
			
			$params['cid'] = $this->config['고객사아이디']; // 고객사 아이디 설정
			$params['code'] = ""; // 암호화할 필드를 설정한다. (아래에서 설정함)
			{
				// code 필드 설정
				$code = array();

				// [공통정보(Head)]
				$code['보안코드'] = $this->config['보안코드']; // 고객사 보안코드 설정 
				$code['고객사도메인'] = $this->config["고객사도메인"]; // 고객사 도메인 정보 설정
				$code['접속단말종류'] = $this->config["접속단말종류"]; // 접속자 단말기 종류를 설정('desktop', 'mobile)
				$code['실행일시'] = $this->config["실행일시"]; // 실행일시를 'YYYYMMDDHHiiss' 형식으로 설정 (고객사 서버의 시간이 실제 시간과 동기화 되어 있는지 확인 필요, HH는 24시 표기법임)
				$code['학생아이디'] = $this->config['학생아이디']; // 학생아이디 설정
				
				// [API 정보(Content)]													 				
				$code['언어'] = $option['언어']; // 
				$code['내교재바로보기'] = $option['내교재바로보기']; // 
				$code['숨은커리큘럼표시'] = $option['숨은커리큘럼표시']; // 
				$code['미니버전'] = $option["미니버전"];

				// [공통정보(Tail)]
				$code['code복호값출력'] = $option["code복호값출력"]; // code 복호값을 출력하려면 'Yes'를 설정한다.
				$code['고객사정의필드'] = $option["고객사정의필드"]; // 웹북 관리페이지의 에러로그목록에서 고객사 에러 발생 위치를 추적하기 우해 활용할 수 있다. 
				$code['API확장필드'] = $option["API확장필드"]; // 확장 기능을 위한 필드

				// 각 필드를 '|'을 구분자로 하여 평문 구성한다.
				$code_str = $this->create_code_str( $code );

				// 암호화API를 이용하여 code_str값 암호화 한다. 
				$enc_data =  $this->encrypt( $code_str );
				
				$params['code'] = $enc_data; // code_str	을 암호화 한 값을 $param["code"]에 설정한다.
			}
			$param["shortcut"] = $option["바로가기"];

			// [커리큘럼조회 모듈] API 쿼리
			$query_url = $this->config["학생웹북모듈"]."?cid=".$params["cid"]."&code=".$enc_data; // $params을 POST로 API를 실행한 실행 결과를 $data에 저장한다
			return $query_url; // $params을 POST로 API를 실행한 실행 결과를 $data에 저장한다.			    
			
		}

		/**
		 * JT-Webbook 에서 제공하는 학생웹북 모듈
		 */
		else if ( $name == "학생웹북 모듈 (모바일)" ) {
			
			$params['cid'] = $this->config['고객사아이디']; // 고객사 아이디 설정
			$params['code'] = ""; // 암호화할 필드를 설정한다. (아래에서 설정함)
			{
				// code 필드 설정
				$code = array();

				// [공통정보(Head)]
				$code['보안코드'] = $this->config['보안코드']; // 고객사 보안코드 설정 
				$code['고객사도메인'] = $this->config["고객사도메인"]; // 고객사 도메인 정보 설정
				$code['접속단말종류'] = $this->config["접속단말종류"]; // 접속자 단말기 종류를 설정('desktop', 'mobile)
				$code['실행일시'] = $this->config["실행일시"]; // 실행일시를 'YYYYMMDDHHiiss' 형식으로 설정 (고객사 서버의 시간이 실제 시간과 동기화 되어 있는지 확인 필요, HH는 24시 표기법임)
				$code['학생아이디'] = $this->config['학생아이디']; // 학생아이디 설정
				
				// [API 정보(Content)]													 				
				$code['언어'] = $option['언어']; // 
				$code['내교재바로보기'] = $option['내교재바로보기']; // 
				$code['숨은커리큘럼표시'] = $option['숨은커리큘럼표시']; // 
				$code['미니버전'] = $option["미니버전"];

				// [공통정보(Tail)]
				$code['code복호값출력'] = $option["code복호값출력"]; // code 복호값을 출력하려면 'Yes'를 설정한다.
				$code['고객사정의필드'] = $option["고객사정의필드"]; // 웹북 관리페이지의 에러로그목록에서 고객사 에러 발생 위치를 추적하기 우해 활용할 수 있다. 
				$code['API확장필드'] = $option["API확장필드"]; // 확장 기능을 위한 필드

				// 각 필드를 '|'을 구분자로 하여 평문 구성한다.
				$code_str = $this->create_code_str( $code );

				// 암호화API를 이용하여 code_str값 암호화 한다. 
				$enc_data =  $this->encrypt( $code_str );
				
				$params['code'] = $enc_data; // code_str	을 암호화 한 값을 $param["code"]에 설정한다.
			}
			$param["shortcut"] = $option["바로가기"];

			// [커리큘럼조회 모듈] API 쿼리
			$query_url = $this->config["학생웹북모듈"]."?device=mobile&cid=".$params["cid"]."&code=".$enc_data; // $params을 POST로 API를 실행한 실행 결과를 $data에 저장한다
			return $query_url; // $params을 POST로 API를 실행한 실행 결과를 $data에 저장한다.			    
			
		}

	}

	/**
	 * $code 배열을 이용하여 $code_str로 생성한다.(API 전문 생성)
	 *
	 * $code 각 배열의 값을 '|'을 구분자로 하여 문자열을 구성한다.
	 */
	private function create_code_str( $code )
	{
		$return = "";
		if ( is_array($code) ) {
			foreach( $code as $key => $value ) {
				if ( $key != "보안코드" ) {
					 $return .= "|";
				}
				$return .= $value;
			}
		}
		return $return;		
	}

	/**
	 * 암호모듈 API 호출 (query함수에서 필수로 사용됨)
	 */
	private function encrypt( $str )
	{
		$api_post = array();
		$api_post["str"] = $str;
		$api_post["key"] = $this->config["고객사아이디"]; // 암호키로 고객사의 cid를 설정한다.
		return $this->query_api( $this->config["암호화API"], $api_post );
	}

	/**
	 * API를 쿼리하기 위한 CURL 함수를 구현한다.
	 *
	 * @param string $url	api url 설정
	 * @param array $post	api 로 전달될 POST 파라메터 값을 배열로 설정
	 */
	private function query_api( $url, $post ) 
	{
		$ch = curl_init();

		curl_setopt( $ch, CURLOPT_URL, $url );
		$post_cnt = count( $post );
		if ( $post_cnt ) {
			curl_setopt( $ch, CURLOPT_POST, 1 );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $post );
		}
		curl_setopt( $ch, CURLOPT_FRESH_CONNECT, true );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT ,30 );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 30 );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );

		$response = curl_exec( $ch );
		curl_close( $ch );

		return $response;
	}	

	/**
	 * 접속단말종류를 구한다.
	 */
	public function get_UA_type() { 
	
		global $_SERVER;

		$ua = $_SERVER["HTTP_USER_AGENT"];

		$iphone = strstr(strtolower($ua), 'mobile'); //Search for 'mobile' in user-agent (iPhone have that) 
		$android = strstr(strtolower($ua), 'android'); //Search for 'android' in user-agent 
		$windowsPhone = strstr(strtolower($ua), 'phone'); //Search for 'phone' in user-agent (Windows Phone uses that)
				
		$androidTablet = $this->_androidTablet($ua); //Do androidTablet function 
		$ipad = strstr(strtolower($ua), 'ipad'); //Search for iPad in user-agent 
		 
		if($androidTablet || $ipad){ //If it's a tablet (iPad / Android) 
			return 'mobile'; 
		} 
		elseif($iphone && !$ipad || $android && !$androidTablet || $windowsPhone){ //If it's a phone and NOT a tablet 
			return 'mobile'; 
		} 
		else{ //If it's not a mobile device 
			return 'desktop'; 
		}     
	} 

	/**
	 * 안드로이드 테블릿인지 검사
	 */
	private function _androidTablet($ua){ //Find out if it is a tablet 
		if(strstr(strtolower($ua), 'android') ){//Search for android in user-agent 
			if(!strstr(strtolower($ua), 'mobile')){ //If there is no ''mobile' in user-agent (Android have that on their phones, but not tablets) 
				return true; 
			} 
		} 
	}
}

// 제이터치 웹북 클래스 객체 선언
$JTW = new JtouchWebbook();
?>