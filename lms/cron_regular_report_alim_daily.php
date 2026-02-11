<?php
// 밤 11:59 이 되면 오늘 진행된 수업중에 정기 보고서를 입력해야 하는데 입력하지 않은 수업이 있는지 확인한다.
// 만약 등록되지 않은 수업이 있으면 강사에게 팝업 알림과 함께 메일로 정기 평가가 입력되지 않았음을 알려준다.

// cron에서 실행하므로 include 파일들의 절대경로가 필요하다. 절대경로 확인하기
$server_root_path = "/home/hosting_users/mangoi/www";

include_once($server_root_path.'/includes/dbopen.php');
//include_once($server_root_path.'/includes/common.php');
$EncryptionKey = "f2ffe2af6c94ba5c3b56b69658f5e471";//절대 변경 불가(변경되면 회원정보 복구 불가)

function InsertTeacherMessage($TeacherMemberID, $TeacherMessageType, $TeacherMessageText){

	$RequestMemberID = 0;
	
	$Sql = " insert into TeacherMessages ( ";
		$Sql .= " MemberID, ";
		$Sql .= " RequestMemberID, ";
		$Sql .= " TeacherMessageType, ";
		$Sql .= " TeacherMessageText, ";
		$Sql .= " TeacherMessageRegDateTime, ";
		$Sql .= " TeacherMessageModiDateTime ";
	$Sql .= " ) values ( ";
		$Sql .= " :MemberID, ";
		$Sql .= " :RequestMemberID, ";
		$Sql .= " :TeacherMessageType, ";
		$Sql .= " :TeacherMessageText, ";
		$Sql .= " now(), ";
		$Sql .= " now() ";
	$Sql .= " ) ";
	
	$Stmt = $GLOBALS['DbConn']->prepare($Sql);
	$Stmt->bindParam(':MemberID', $TeacherMemberID);
	$Stmt->bindParam(':RequestMemberID', $RequestMemberID);
	$Stmt->bindParam(':TeacherMessageType', $TeacherMessageType);
	$Stmt->bindParam(':TeacherMessageText', $TeacherMessageText);
	$Stmt->execute();
	$Stmt = null;

}


// 1. 오늘 진행 완료된 전체 수업 가져오기 (수업중 정기평가를 해야 하는 것 가지고 오기)
$Sql = "SELECT A.MemberID, A.TeacherID, A.StartDateTime, A.ClassAttendState, D.AssmtStudentMonthlyScoreSubject, E.MemberID as TeacherMemberID,
				AES_DECRYPT(UNHEX(E.MemberEmail),:EncryptionKey) AS Email
			FROM Classes A
			LEFT JOIN AssmtStudentMonthlyScores D ON A.ClassID = D.ClassID 
			LEFT JOIN Members E ON A.TeacherID = E.TeacherID
			WHERE DATE_FORMAT(StartDateTime, '%Y-%m-%d') = curdate() AND ClassState = 2	
			AND (A.ClassAttendState = 1 OR A.ClassAttendState = 2 )
			AND 
			(select count(*) from Classes B where B.ClassOrderID = A.ClassOrderID  AND B.ClassState=2 and TIMESTAMPDIFF(minute, B.StartDateTime, A.StartDateTime)>0) > 0 
			and 
			(select count(*)%8 from Classes B where B.ClassOrderID = A.ClassOrderID  AND B.ClassState=2 and TIMESTAMPDIFF(minute, B.StartDateTime, A.StartDateTime)>0) = 0 
			";
$Stmt = $DbConn->prepare($Sql);
//echo $Sql;
$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

// 오늘 정기평가해야 하는 수만큼 반복
while($Row = $Stmt->fetch()) {

	$TeacherMemberID = $Row["TeacherMemberID"];
	$TeacherEmail = $Row["Email"];
	// 정기평가를 했는지 확인하고 안 했으면
	if ($Row["AssmtStudentMonthlyScoreSubject"] == "" || $Row["AssmtStudentMonthlyScoreSubject"] == null ){

		// 강사에게 이메일과 메시지를 통해 정기평가를 하라고 알려준다. 
		

		$sendMessage = "Regular evaluation has not been completed. Please complete the regular evaluation.";
		InsertTeacherMessage($TeacherMemberID, 1, $sendMessage);

		//메일로도 보내기
		$from_name = "mangoi";
		$subject = $sendMessage;
		//한글 안깨지게 만들어줌
		$subject = "=?UTF-8?B?".base64_encode($subject)."?=";
		$content = $sendMessage;
		$Headers = "from: =?utf-8?B?".base64_encode($from_name)."?= <mangoi@mangoi.co.kr>"."\r\n"; // from 과 : 은 붙여주세요 => from: 
		$Headers .= "Content-Type: text/html;";
		
		$from = "mangoi@mangoi.co.kr";
		
		mail($TeacherEmail, $subject, $content, $Headers); 

	}	
}
// 반복이 끝나면 종료



include_once($server_root_path.'/includes/dbclose.php');

?>
