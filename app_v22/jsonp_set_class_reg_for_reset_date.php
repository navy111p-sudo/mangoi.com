<?
header('Access-Control-Allow-Origin: *'); 
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');

$ErrNum = 0;
$ErrMsg = "성공";
$AppRegUID = isset($_REQUEST["AppRegUID"]) ? $_REQUEST["AppRegUID"] : "";
$AppID = isset($_REQUEST["AppID"]) ? $_REQUEST["AppID"] : "";
$AppDomain = isset($_REQUEST["AppDomain"]) ? $_REQUEST["AppDomain"] : "";
$AppPath = isset($_REQUEST["AppPath"]) ? $_REQUEST["AppPath"] : "";

$ClassOrderID = isset($_REQUEST["MemberID"]) ? $_REQUEST["ClassOrderID"] : "";
$SelectYear = isset($_REQUEST["SelectYear"]) ? $_REQUEST["SelectYear"] : "";
$SelectMonth = isset($_REQUEST["SelectMonth"]) ? $_REQUEST["SelectMonth"] : "";
$SelectDay = isset($_REQUEST["SelectDay"]) ? $_REQUEST["SelectDay"] : "";
$StudyTimeHour = isset($_REQUEST["StudyTimeHour"]) ? $_REQUEST["StudyTimeHour"] : "";
$StudyTimeMinute = isset($_REQUEST["StudyTimeMinute"]) ? $_REQUEST["StudyTimeMinute"] : "";
$ClassOrderTimeTypeID = isset($_REQUEST["ClassOrderTimeTypeID"]) ? $_REQUEST["ClassOrderTimeTypeID"] : "";
$TeacherID = isset($_REQUEST["TeacherID"]) ? $_REQUEST["TeacherID"] : "";
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$ClassMemberType = isset($_REQUEST["ClassMemberType"]) ? $_REQUEST["ClassMemberType"] : "";

$callback = isset($_REQUEST["callback"]) ? preg_replace('/[^a-z0-9$_]/si', '', $_REQUEST["callback"]) : false;
$ServerPath = $AppDomain.$AppPath."/";

$StartYear = $SelectYear;
$StartMonth = $SelectMonth;
$StartDay = $SelectDay;
$StartHour = $StudyTimeHour;
$StartMinute = $StudyTimeMinute;



$Sql = "select 
		A.TeacherPayPerTime,
		A.TeacherName,
		B.MemberLoginID
	from 
		Teachers A 
			inner join Members B on A.TeacherID=B.TeacherID and B.MemberLevelID=15 
	where A.TeacherID=:TeacherID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':TeacherID', $TeacherID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$TeacherPayPerTime = $Row["TeacherPayPerTime"];
$TeacherName = $Row["TeacherName"];
$TeacherLoginID = $Row["MemberLoginID"];


if ($ClassOrderTimeTypeID==2){
	$PlusMinute = 20;
}else if ($ClassOrderTimeTypeID==3){
	$PlusMinute = 30;
}else if ($ClassOrderTimeTypeID==4){
	$PlusMinute = 40;
}

$EndMinute = $StartMinute + $PlusMinute;
if ($EndMinute>=60){
	$EndMinute = $EndMinute - 60;
	$EndHour = $StartHour + 1;
}else{
	$EndHour = $StartHour;
}


//종로시간이 24를 넘어가면 23시 59분으로 맞춘다.
if ($EndHour>=24){
	$EndHour = 23;
	$EndMinute = 59;
}
//종로시간이 24를 넘어가면 23시 59분으로 맞춘다.


$EndYear = $StartYear;
$EndMonth = $StartMonth;
$EndDay = $StartDay;



$Sql = "select 
			ClassID 
		from Classes 
		where 
			MemberID=".$MemberID."
			and TeacherID=".$TeacherID."
			and StartYear=".$StartYear."
			and StartMonth=".$StartMonth."
			and StartDay=".$StartDay."
			and StartHour=".$StartHour."
			and StartMinute=".$StartMinute."

			and EndYear=".$EndYear."
			and EndMonth=".$EndMonth."
			and EndDay=".$EndDay."
			and EndHour=".$EndHour."
			and EndMinute=".$EndMinute."

	";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':TeacherID', $TeacherID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$ClassID = $Row["ClassID"];
$CommonShClassCode = $Row["CommonShClassCode"];


if (!$ClassID){

	$StartDate = $StartYear."-".substr("0".$StartMonth,-2)."-".substr("0".$StartDay,-2);
	$CommonShClassCode = $TeacherID."_". str_replace("-","",$StartDate) ."_".$StartHour."_".$StartMinute;

	$StartDateTime = $StartYear."-".substr("0".$StartMonth,-2)."-".substr("0".$StartDay,-2)." ".substr("0".$StartHour,-2).":".substr("0".$StartMinute,-2).":00";
	$EndDateTime   = $EndYear.  "-".substr("0".$EndMonth,  -2)."-".substr("0".$EndDay,-2  )." ".substr("0".$EndHour,-2  ).":".substr("0".$EndMinute,-2  ).":00";

	$StartDateTimeStamp = DateToTimestamp($StartDateTime, "Asia/Seoul");
	$EndDateTimeStamp =   DateToTimestamp($EndDateTime  , "Asia/Seoul");


	$Sql = " insert into Classes ( ";
		$Sql .= " ClassOrderID, ";
		$Sql .= " MemberID, ";
		$Sql .= " TeacherID, ";
		$Sql .= " TeacherPayPerTime, ";
		$Sql .= " StartDateTime, ";
		$Sql .= " StartDateTimeStamp, ";
		$Sql .= " StartYear, ";
		$Sql .= " StartMonth, ";
		$Sql .= " StartDay, ";
		$Sql .= " StartHour, ";
		$Sql .= " StartMinute, ";
		$Sql .= " EndDateTime, ";
		$Sql .= " EndDateTimeStamp, ";
		$Sql .= " EndYear, ";
		$Sql .= " EndMonth, ";
		$Sql .= " EndDay, ";
		$Sql .= " EndHour, ";
		$Sql .= " EndMinute, ";
		$Sql .= " CommonUseClassIn, ";
		$Sql .= " CommonShClassCode, ";
		$Sql .= " ClassRegDateTime, ";
		$Sql .= " ClassModiDateTime ";
	$Sql .= " ) values ( ";
		$Sql .= " :ClassOrderID, ";
		$Sql .= " :MemberID, ";
		$Sql .= " :TeacherID, ";
		$Sql .= " :TeacherPayPerTime, ";
		$Sql .= " :StartDateTime, ";
		$Sql .= " :StartDateTimeStamp, ";
		$Sql .= " :StartYear, ";
		$Sql .= " :StartMonth, ";
		$Sql .= " :StartDay, ";
		$Sql .= " :StartHour, ";
		$Sql .= " :StartMinute, ";
		$Sql .= " :EndDateTime, ";
		$Sql .= " :EndDateTimeStamp, ";
		$Sql .= " :EndYear, ";
		$Sql .= " :EndMonth, ";
		$Sql .= " :EndDay, ";
		$Sql .= " :EndHour, ";
		$Sql .= " :EndMinute, ";
		$Sql .= " 0, ";
		$Sql .= " :CommonShClassCode, ";
		$Sql .= " now(), ";
		$Sql .= " now() ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ClassOrderID', $ClassOrderID);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->bindParam(':TeacherID', $TeacherID);
	$Stmt->bindParam(':TeacherPayPerTime', $TeacherPayPerTime);
	$Stmt->bindParam(':StartDateTime', $StartDateTime);
	$Stmt->bindParam(':StartDateTimeStamp', $StartDateTimeStamp);
	$Stmt->bindParam(':StartYear', $StartYear);
	$Stmt->bindParam(':StartMonth', $StartMonth);
	$Stmt->bindParam(':StartDay', $StartDay);
	$Stmt->bindParam(':StartHour', $StartHour);
	$Stmt->bindParam(':StartMinute', $StartMinute);
	$Stmt->bindParam(':EndDateTime', $EndDateTime);
	$Stmt->bindParam(':EndDateTimeStamp', $EndDateTimeStamp);
	$Stmt->bindParam(':EndYear', $EndYear);
	$Stmt->bindParam(':EndMonth', $EndMonth);
	$Stmt->bindParam(':EndDay', $EndDay);
	$Stmt->bindParam(':EndHour', $EndHour);
	$Stmt->bindParam(':EndMinute', $EndMinute);
	$Stmt->bindParam(':CommonShClassCode', $CommonShClassCode);
	$Stmt->execute();
	$ClassID = $DbConn->lastInsertId();
	$Stmt = null;

}

$ArrValue["ClassID"] = $ClassID;
$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;



$ResultValue = my_json_encode($ArrValue);
//echo $_GET['callback'] . "(".(string)$ResultValue.")"; 
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>