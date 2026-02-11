<?
header( "Content-type: application/vnd.ms-excel; charset=utf-8");
header( "Content-Disposition: attachment; filename = 학생_일괄등록_다운.xls" );     //filename = 저장되는 파일명을 설정합니다.
//header( "Content-Description: PHP4 Generated Data" );

// 한글 깨짐 방지 ( https://wonis-lifestory.tistory.com/entry/php-csv-%EB%8B%A4%EC%9A%B4%EB%A1%9C%EB%93%9C-%EC%8B%9C-%ED%95%9C%EA%B8%80-%EA%B9%A8%EC%A7%90 )
//echo "\xEF\xBB\xBF";

$filename = "../uploads/excel_add_student/학생_일괄등록_다운로드.xls";
$fp = fopen($filename, "rb"); 
fpassthru($fp);
fclose($fp);



?>  
