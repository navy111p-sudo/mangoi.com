<?
header('Content-Type: text/csv; charset=utf-8');
header("Content-Disposition: attachment; filename = 학생_일괄등록_다운.csv" );

include_once('../includes/dbopen.php');
include_once('../includes/common.php');


// 내보낼 ( 다운로드 할 ) 파일 과 연결
$output = fopen("php://output", "w");

// 한글 깨짐 방지 ( https://wonis-lifestory.tistory.com/entry/php-csv-%EB%8B%A4%EC%9A%B4%EB%A1%9C%EB%93%9C-%EC%8B%9C-%ED%95%9C%EA%B8%80-%EA%B9%A8%EC%A7%90 )
echo "\xEF\xBB\xBF";

$WarnRequire = array(
	"★ 처리된 부분은 필수데이터로 꼭 기입해주세요."
);

$WarnPhone = array(
	"필수데이터 외의 데이터들을 기입을 원하지않으시면 공백으로 두시면 됩니다."
);

$WarnSyntax = array(
	"홍길동",
	"hong",
	"hong",
	"dlWKd354 ( 암호화 처리됩니다.)",
	"000-0000-0000",
	"asd@asd.com",
	"남자/여자",
	"1111-12-11",
	"60123",
	"도로명주소 등",
	"아파트명 호수 등",
	"특이사항 등"
);
$Line = array();

// 테이블 상단 정의 ( )
$Header = array( 
	"학생명★",
	"영문표기이름★",
	"아이디★",
	"비밀번호★",
	"전화번호★",
	"이메일★",
	"성별★",
	"생년월일★",
	"우편번호",
	"주소",
	"상세주소",
	"메모",
	"보호자이름",
	"부모님 전화번호",
	"학부모 이메일"
);

fputcsv($output, $WarnRequire); // 컬럼 정의
fputcsv($output, $WarnPhone); // 컬럼 정의
fputcsv($output, array("하단의 예제 참고하여 작성해주세요.")); // 컬럼 정의
fputcsv($output, $WarnSyntax); // 컬럼 정의
fputcsv($output, $Line); // 컬럼 정의
fputcsv($output, $Header); // 컬럼 정의


//readfile("/tmp/report.csv");

//echo fputcsv;  
?>  
