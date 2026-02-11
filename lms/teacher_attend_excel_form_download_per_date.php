<?

header( "Content-type: application/vnd.ms-excel; charset=utf-8");
header( "Content-Disposition: attachment; filename = 강사출결현황.xls" );     //filename = 저장되는 파일명을 설정합니다.
header( "Content-Description: PHP4 Generated Data" );

include_once('../includes/dbopen.php');
include_once('../includes/common.php');

// 한글 깨짐 방지 ( https://wonis-lifestory.tistory.com/entry/php-csv-%EB%8B%A4%EC%9A%B4%EB%A1%9C%EB%93%9C-%EC%8B%9C-%ED%95%9C%EA%B8%80-%EA%B9%A8%EC%A7%90 )
//echo "\xEF\xBB\xBF";

$DisplayLateSec = 60;//화면에 표시할 늦은 시간(초)
$PenaltyLateSec = 120;//사유를 적어야할 늦은 시간(초)


// 전달받은 파라미터 정의
$SearchStartYear = isset($_REQUEST["SearchStartYear"]) ? $_REQUEST["SearchStartYear"] : "";
$SearchStartMonth = isset($_REQUEST["SearchStartMonth"]) ? $_REQUEST["SearchStartMonth"] : "";
$SearchStartDay = isset($_REQUEST["SearchStartDay"]) ? $_REQUEST["SearchStartDay"] : "";

$SearchEndYear = isset($_REQUEST["SearchEndYear"]) ? $_REQUEST["SearchEndYear"] : "";
$SearchEndMonth = isset($_REQUEST["SearchEndMonth"]) ? $_REQUEST["SearchEndMonth"] : "";
$SearchEndDay = isset($_REQUEST["SearchEndDay"]) ? $_REQUEST["SearchEndDay"] : "";

$SearchTeacherID = isset($_REQUEST["SearchTeacherID"]) ? $_REQUEST["SearchTeacherID"] : "";

$AddTeacherWhere = "";
if ($SearchTeacherID!=""){
	$AddTeacherWhere = " and A.TeacherID=".$SearchTeacherID." ";
}

// 필요변수 정의
$TeacherCount = 0;
$SearchStartDatetime = $SearchStartYear."-".$SearchStartMonth."-".$SearchStartDay;
$SearchEndDatetime = $SearchEndYear."-".$SearchEndMonth."-".$SearchEndDay;
$CurrentDate = "";
// 출근, 입장 타입을 나눠 헤더 안에 넣을 변수 정의 

?>

<style>
  table {
    width: 1000px;
    border: 1px solid black;
    border-collapse: collapse;
  }
  th, td {
    border: 1px solid black;
  }

</style>

<?
// 공통 헤더 정의
$Contents = "
				<table >
					<thead>
						<tr style='background-color: #E3E2E2; '>
							<td>날짜 (date)</td>
							<td>강사명 (teacher)</td>
							<td>규정출근시간 (start work time)</td>
							<td>실제출근시간 (Actual attend time)</td>
							<td>경과시간 (elapsed time)</td>
							<td>경과시간 (elapsed time - second)</td>
							<td>상태 (States)</td>
						</tr>
					</thead>
					<tbody>
					";


// 콘텐츠 정의
$Sql = " select 
				A.*,
				B.MemberName,
				timestampdiff(second, A.TeacherAttendanceHour, A.TeacherAttendanceDateTime) as AttendDiff
			from TeacherAttendances A 
				inner join Members B on A.TeacherID=B.TeacherID 
			where 
				datediff(A.CheckDate,'$SearchStartDatetime')>=0 
				and datediff(A.CheckDate,'$SearchEndDatetime')<=0 
				".$AddTeacherWhere." 
			order by A.CheckDate asc, A.TeacherAttendanceHour asc";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();

while($Row = $Stmt->fetch() ) {
	$MemberName = $Row["MemberName"];
	$TeacherAttendanceHour = $Row["TeacherAttendanceHour"]; // 출근해야할 시간
	$TeacherAttendanceDateTime = $Row["TeacherAttendanceDateTime"]; // 출근 시간
	$CheckDate = $Row["CheckDate"];
	$AttendDiff = $Row["AttendDiff"];

	$TempTeacherAttendanceHour = date('H:i:s', strtotime($TeacherAttendanceHour));
	if($TeacherAttendanceDateTime == null) {
		$TempTeacherAttendanceDateTime = "";
	} else {
		$TempTeacherAttendanceDateTime = date('H:i:s', strtotime($TeacherAttendanceDateTime));
	}

	if ($AttendDiff==""){
		$TempAttendDiff = "";
		$TempAttendMsg = "미출근(absent)";
		$StrPlus = "";
	}else{
		if ($AttendDiff<0){
			$AttendDiff = $AttendDiff * -1;
			$TempAttendMsg = "";
			$StrPlus = "-";
		}else{

			if ($AttendDiff>$DisplayLateSec){
				$TempAttendMsg = "지각(late)";
				$StrPlus = "";
			}else{
				$TempAttendMsg = "";
				$StrPlus = "";
			}

		}
		$TempAttendDiff = floor($AttendDiff/60).":". substr("0".($AttendDiff % 60),-2);
	}

	$Contents .= "
					<tr>
						<td>
							".$CheckDate."
						</td>
						<td>
							".$MemberName."
						</td>
						<td>
							".$TempTeacherAttendanceHour."
						</td>
						<td>
							".$TempTeacherAttendanceDateTime."
						</td>
						<td style=mso-number-format:'\@'>
							".$StrPlus . $TempAttendDiff."
						</td>
						<td>
							".$StrPlus . $AttendDiff."
						</td>
						<td>
							".$TempAttendMsg."
						</td>
					</tr>
					";
	}
$Contents .= "</tbody></table>";

echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";

echo $Contents;

?>