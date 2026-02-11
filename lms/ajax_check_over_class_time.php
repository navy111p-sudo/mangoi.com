<?php
/*
강의 시간을 체크해서 강의시간 2분이 지나도 강사가 입장하지
않은 경우 관리자에게 팝업을 띄운다.   
*/


	header('Content-Type: application/json; charset=UTF-8');
	include_once('../includes/dbopen.php');
	include_once('../includes/common.php');

	$ClassID = isset($_REQUEST["ClassID"]) ? $_REQUEST["ClassID"] : "";
	$Check3Minute = 0;
	$Sql = "SELECT  DISTINCT A.StartDateTimeStamp,
				A.EndDateTimeStamp,
				A.StartDateTime,
				B.MemberName
			from Classes A
			LEFT JOIN Members B ON A.TeacherID = B.TeacherID  
			LEFT JOIN ClassOrders D ON A.ClassOrderID = D.ClassOrderID 
			where A.StartDateTime >= DATE_SUB(Now(), INTERVAL 15 MINUTE) AND DATE_ADD(A.StartDateTime, INTERVAL 2 MINUTE) <= NOW() 
			AND A.TeacherInDateTime IS NULL 
			AND A.ClassAttendState = 0 
			AND A.ClassModiDateTime >= DATE_SUB(curdate(), INTERVAL 1 DAY) and A.ClassModiDateTime >= curdate() 
			AND (D.ClassOrderStartDate <= CURDATE() OR D.ClassOrderStartDate IS NULL) AND (D.ClassOrderEndDate >= CURDATE() OR D.ClassOrderEndDate IS NULL)
	";
	//echo $Sql;
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ClassID', $ClassID);
	$Stmt->execute();
	$rowCount = $Stmt->rowCount();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$MemberName = "";
	while($Row = $Stmt->fetch()){
		$MemberName .= $Row["MemberName"]."(".substr($Row["StartDateTime"],11,5)."수업) , ";
		
	}
	
	if ($rowCount>0){
		$Check3Minute = 1;
	}

	$ArrValue["Check3Minute"] = $Check3Minute;
	$ArrValue["MemberName"] = $MemberName;
	$QueryResult = my_json_encode($ArrValue);
	echo $QueryResult;



function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('../includes/dbclose.php');

?>