 <?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');

$ClassOrderID = isset($_REQUEST["ClassOrderID"]) ? $_REQUEST["ClassOrderID"] : "";
$StartYear = isset($_REQUEST["StartYear"]) ? $_REQUEST["StartYear"] : "";
$StartMonth = isset($_REQUEST["StartMonth"]) ? $_REQUEST["StartMonth"] : "";
$StartDay = isset($_REQUEST["StartDay"]) ? $_REQUEST["StartDay"] : "";
$StartHour = isset($_REQUEST["StartHour"]) ? $_REQUEST["StartHour"] : "";
$StartMinute = isset($_REQUEST["StartMinute"]) ? $_REQUEST["StartMinute"] : "";
$ClassOrderTimeTypeID = isset($_REQUEST["ClassOrderTimeTypeID"]) ? $_REQUEST["ClassOrderTimeTypeID"] : "";
$TeacherID = isset($_REQUEST["TeacherID"]) ? $_REQUEST["TeacherID"] : "";
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$ClassOrderPayID = isset($_REQUEST["ClassOrderPayID"]) ? $_REQUEST["ClassOrderPayID"] : "";


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
$MemberLoginID = $Row["MemberLoginID"];


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


$EndYear = $StartYear;
$EndMonth = $StartMonth;
$EndDay = $StartDay;



$Sql = "select 
			ClassID, 
			CommonShClassCode, 
			BookScanID, 
			BookSystemType, 
			BookWebookUnitID 
		from Classes 
		where 
			ClassOrderID=".$ClassOrderID." 
			and MemberID=".$MemberID." 
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

			and ClassAttendState<>99 
	";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':TeacherID', $TeacherID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$ClassID = $Row["ClassID"];
$CommonShClassCode = $Row["CommonShClassCode"];
$BookWebookUnitID = $Row["BookWebookUnitID"];
$BookScanID = $Row["BookScanID"];
$BookSystemType = $Row["BookSystemType"];

// outer 조인 하라했으나, 개념부족으로 구별 지음
$Sql = "select 
			BookScanImageFileName 
		from BookScans 
		where 
			BookScanID=:BookScanID 
	";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':BookScanID', $BookScanID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$BookScanImageFileName = $Row["BookScanImageFileName"];

if (!$ClassID){

	$StartDate = $StartYear."-".substr("0".$StartMonth,-2)."-".substr("0".$StartDay,-2);
	$CommonShClassCode = $TeacherID."_". str_replace("-","",$StartDate) ."_".$StartHour."_".$StartMinute;

	$StartDateTime = $StartYear."-".substr("0".$StartMonth,-2)."-".substr("0".$StartDay,-2)." ".substr("0".$StartHour,-2).":".substr("0".$StartMinute,-2).":00";
	$EndDateTime   = $EndYear.  "-".substr("0".$EndMonth,  -2)."-".substr("0".$EndDay,-2  )." ".substr("0".$EndHour,-2  ).":".substr("0".$EndMinute,-2  ).":00";

	$StartDateTimeStamp = DateToTimestamp($StartDateTime, "Asia/Seoul");
	$EndDateTimeStamp =   DateToTimestamp($EndDateTime  , "Asia/Seoul");

	if($ClassOrderPayID==0) {
		$Sql2 = "
			select 
				A.ClassOrderPayID 
			from ClassOrderPays A 
				inner join ClassOrderPayDetails B on A.ClassOrderPayID=B.ClassOrderPayID 
			where 
				B.ClassOrderID=:ClassOrderID 
				and 
				B.TeacherID=:TeacherID 
				and
				datediff(A.ClassOrderPayStartDate, now() ) < 0
			order by 
				A.ClassOrderPayID desc
			limit
				0,1
		";
		$Stmt2 = $DbConn->prepare($Sql2);
		$Stmt2->bindParam(':ClassOrderID', $ClassOrderID);
		$Stmt2->bindParam(':TeacherID', $TeacherID);
		$Stmt2->execute();
		$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
		$Row2 = $Stmt2->fetch();
		$ClassOrderPayID = $Row2["ClassOrderPayID"];
		if($ClassOrderPayID==null) {
			$ClassOrderPayID = 0;
		}
		$Stmt2 = null;
	}

	$Sql = " insert into Classes ( ";
		$Sql .= " ClassOrderPayID, ";
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
		$Sql .= " :ClassOrderPayID, ";
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
	$Stmt->bindParam(':ClassOrderPayID', $ClassOrderPayID);
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


$ArrValue["ClassOrderID"] = $ClassOrderID;
$ArrValue["ClassID"] = $ClassID;
$ArrValue["CommonShClassCode"] = $CommonShClassCode;
$ArrValue["TeacherName"] = $TeacherName;
$ArrValue["TeacherLoginID"] = $TeacherLoginID;
$ArrValue["BookScanImageFileName"] = $BookScanImageFileName;
$ArrValue["BookWebookUnitID"] = $BookWebookUnitID;
$ArrValue["BookSystemType"] = $BookSystemType;
$ArrValue["MemberLoginID"] = $MemberLoginID;

$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('../includes/dbclose.php');
?>