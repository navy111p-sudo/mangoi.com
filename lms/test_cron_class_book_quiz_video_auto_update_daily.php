<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

?>
<?php
// 새벽 00:00 이 되면 오늘 진행될 수업에 교재, 퀴즈, 비디오가 등록되어 있는지 확인하고
// 만약 등록되어 있지 않다면 이전 수업 교재를 등록한다. cron으로 자동실행

// cron에서 실행하므로 include 파일들의 절대경로가 필요하다. 절대경로 확인하기
$server_root_path =  $_SERVER['DOCUMENT_ROOT'];

include_once($server_root_path.'/includes/dbopen.php');
//include_once($server_root_path.'/includes/common.php');

// 0.먼저 오늘 수업을 등록해 준다. (class_list.php 에서 등록부분을 떼어 온다.)
//include_once($server_root_path.'/lms/cron_class_insert.php');


// 1. 오늘 있을 전체 수업 가져오기
$Sql = "SELECT * FROM Classes 
		WHERE StartDateTime >= now() AND StartDateTime <= date_add(now(),INTERVAL 1 DAY) ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

// 오늘 전체 수업 수만큼 반복
while($Row = $Stmt->fetch()) {
	echo "하나더 :: ";

	// 2. 각 수업별로 교재가 등록되어 있는지 확인
	if ($Row["BookScanID"] == "0" && $Row["BookVideoID"] == "0" && $Row["BookQuizID"] == "0"){

		// 3. 교재가 등록되어 있지 않은 경우 이전 수업내용에서 교재 가져오기
		


		$Sql2 = "SELECT * FROM Classes 
					WHERE MemberID = :MemberID AND TeacherID = :TeacherID 
							AND BookScanID <> 0 AND BookVideoID <> 0 AND BookQuizID <> 0 
					ORDER BY ClassID desc";
		
		$Stmt2 = $DbConn->prepare($Sql2);
		$Stmt2->bindParam(':MemberID', $Row["MemberID"]);
		$Stmt2->bindParam(':TeacherID', $Row["TeacherID"]);
		$Stmt2->execute();
		$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
		$Row2 = $Stmt2->fetch();
		$BookScanID = isset($Row2["BookScanID"])?$Row2["BookScanID"]:"0";
		$BookVideoID = isset($Row2["BookVideoID"])?$Row2["BookVideoID"]:"0";
		$BookQuizID = isset($Row2["BookQuizID"])?$Row2["BookQuizID"]:"0";
		$ClassID = $Row2["ClassID"];

		echo "아이디:".$Row["ClassID"].":".$BookScanID.":".$BookVideoID."<br>";



		// 4. 가져온 교재 등록하기
		$Sql2 = "UPDATE Classes SET 
					BookScanID = :BookScanID,
					BookVideoID = :BookVideoID,
					BookQuizID = :BookQuizID 
				WHERE ClassID=:ClassID";
		$Stmt2 = $DbConn->prepare($Sql2);
		$Stmt2->bindParam(':BookScanID', $BookScanID);
		$Stmt2->bindParam(':BookVideoID', $BookVideoID);
		$Stmt2->bindParam(':BookQuizID', $BookQuizID);
		$Stmt2->bindParam(':ClassID', $Row["ClassID"]);
		$Stmt2->execute();


	}	
}
// 반복이 끝나면 종료



include_once($server_root_path.'/includes/dbclose.php');

?>
