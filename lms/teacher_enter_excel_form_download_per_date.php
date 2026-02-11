<?

header( "Content-type: application/vnd.ms-excel; charset=utf-8");
header( "Content-Disposition: attachment; filename = 강사출석현황.xls" );
header( "Content-Description: PHP4 Generated Data" );

include_once('../includes/dbopen.php');
include_once('../includes/common.php');

// 한글 깨짐 방지 ( https://wonis-lifestory.tistory.com/entry/php-csv-%EB%8B%A4%EC%9A%B4%EB%A1%9C%EB%93%9C-%EC%8B%9C-%ED%95%9C%EA%B8%80-%EA%B9%A8%EC%A7%90 )
//echo "\xEF\xBB\xBF";

$DisplayLateSec = 60;//화면에 표시할 늦은 시간(초)
$PenaltyLateSec = 120;//사유를 적어야할 늦은 시간(초)
$PenaltyPerMin = 10;//10페소

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
							<td>규정출석시간 (start work time)</td>
							<td>수업시간 (Class minute)</td>
							<td>실제출석시간(Actual enter time)</td>
							<td>경과시간 (elapsed time)</td>
							<td>경과시간 (elapsed time - second)</td>
							<td>상태 (States)</td>
							<td>지각사유 (Reason)</td>
							<td>관리자답변 (Answer)</td>
							<td>벌점 (Penalty)</td>
							<td>벌점삭제 (Delete)</td>
						</tr>
					</thead>
					<tbody>
					";


// 콘텐츠 정의
$Sql = " select 
				A.*,
				B.MemberName,
				timestampdiff(second, A.ClassStartDateTime, A.ClassEnterDateTime) as AttendDiff
			from ClassTeacherEnters A 
				inner join Members B on A.TeacherID=B.TeacherID 
			where 
				datediff(A.ClassDate,'$SearchStartDatetime')>=0 
				and datediff(A.ClassDate,'$SearchEndDatetime')<=0 
				".$AddTeacherWhere." 
			order by A.ClassDate asc, A.ClassStartDateTime asc";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();

while($Row = $Stmt->fetch() ) {
	$MemberName = $Row["MemberName"];
	$ClassStartDateTime = $Row["ClassStartDateTime"]; // 출근해야할 시간
	$ClassEnterDateTime = $Row["ClassEnterDateTime"]; // 출근 시간
	$ClassDate = $Row["ClassDate"];
	$AttendDiff = $Row["AttendDiff"];
	$ClassRunMinute = $Row["ClassRunMinute"];

	$ClassEnterLateReason = $Row["ClassEnterLateReason"];
	$ClassEnterLateReasonAnswer = $Row["ClassEnterLateReasonAnswer"];
	$ClassEnterLateReasonConfirm = $Row["ClassEnterLateReasonConfirm"];

	$TempClassStartDateTime = date('H:i:s', strtotime($ClassStartDateTime));

	if($ClassEnterDateTime==null) { // 수업을 입장하지 않았을 때.. 분기처리
		$TempClassEnterDateTime = "";		
	} else {
		$TempClassEnterDateTime = date('H:i:s', strtotime($ClassEnterDateTime));
	}

	if ($ClassEnterLateReasonConfirm==0){
		$StrClassEnterLateReasonConfirm = "";
	}else if ($ClassEnterLateReasonConfirm==1){
		$StrClassEnterLateReasonConfirm = "삭제(deleted)";
	}

	$Penalty = 0;

	if ($AttendDiff==""){
		$TempAttendDiff = "";
		$TempAttendMsg = "미출석(absent)";
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

				
				if ($AttendDiff>$PenaltyLateSec && $ClassEnterLateReasonConfirm==0) {//벌점주는 시간보다 더 늦어지고 삭제가 아니면 
					$Penalty = $PenaltyPerMin * ceil(($AttendDiff-$PenaltyLateSec)/60);
				}

			}else{
				$TempAttendMsg = "";
				$StrPlus = "";
			}

		}
		$TempAttendDiff = floor($AttendDiff/60).":". substr("0".($AttendDiff % 60),-2);

	}

	if ($Penalty>0){
		$NameColor="#ff0000";
	}else{
		$NameColor="";
	}

	$Contents .= "
					<tr>
						<td>
							".$ClassDate."
						</td>
						<td style='color:".$NameColor.";'>
							".$MemberName."
						</td>
						<td>
							".$TempClassStartDateTime."
						</td>
						<td>
							".$ClassRunMinute."
						</td>
						<td>
							".$TempClassEnterDateTime."
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

						<td>
							".$ClassEnterLateReason."
						</td>
						<td>
							".$ClassEnterLateReasonAnswer."
						</td>
						
						<td>
							".$Penalty."
						</td>
						<td>
							".$StrClassEnterLateReasonConfirm."
						</td>
					</tr>
					";
	}
$Contents .= "</tbody></table>";

echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";

echo $Contents;

?>