<?php
// 수업이 있는 날 (월~금) 오전 10시 이후~  밤 10시까지  매 10분마다 cron을 실행해서 수업이 미참석한 학생들에게 문자 보내기. cron으로 자동실행
// cron에서 실행하므로 include 파일들의 절대경로가 필요하다. 절대경로 확인하기
$server_root_path = "/home/hosting_users/mangoi/www";

//=======================================================================================================
$EncryptionKey = "f2ffe2af6c94ba5c3b56b69658f5e471";//절대 변경 불가(변경되면 회원정보 복구 불가)
//=======================================================================================================


include_once($server_root_path.'/includes/dbopen.php');
//include_once($server_root_path.'/includes/common.php');

// 1. 지금 시간을 확인해서 현재 수업에 미참석한 학생 확인하기


//카카오 알림톡으로 메시지 전송
function SendAlimtalk($phn, $msg, $tmplId){
	$url = "https://alimtalk-api.bizmsg.kr/v2/sender/send"; //주소셋팅
	
	//추가할 헤더값이 있을시 추가하면 됨
	 $headers = array(
	 	"userid:mangoi",
	 	"content-type:application/json"
	 );

	//POST방식으로 보낼 JSON데이터 생성
	$post_arr = [];
	
	$post_arr["message_type"] = "at";
	$post_arr["phn"] = $phn;
	$post_arr["profile"] = "29425cbfa7f359560a6d8ef74ac7fa9cb74c7a1c";
	$post_arr["msg"] = $msg;
	$post_arr["tmplId"] = $tmplId;

	//배열을 JSON데이터로 생성
	$post_data = json_encode(array($post_arr),JSON_UNESCAPED_UNICODE);
	//var_dump($post_data);

	//CURL함수 사용
	$ch=curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL, $url);
	//header값 셋팅(없을시 삭제해도 무방함)
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	//POST방식
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POST, true);
	//POST방식으로 넘길 데이터(JSON데이터)
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	curl_setopt($ch, CURLOPT_TIMEOUT, 3);

	$response = curl_exec($ch);

	if(curl_error($ch)){
		$curl_data = null;
	} else {
		$curl_data = $response;
	}

	curl_close($ch);

	$json_data= array();
	//받은 JSON데이터를 배열로 만듬
	$json_data = json_decode($curl_data,true);
	
	

}


$Sql = "SELECT DISTINCT c.ClassID, c.MemberID, c.ClassAttendState, 
			m.MemberName,
			AES_DECRYPT(UNHEX(m.MemberPhone1),'$EncryptionKey') as DecMemberPhone1,   
			AES_DECRYPT(UNHEX(m.MemberPhone2),'$EncryptionKey') as DecMemberPhone2   
			from Classes c
			inner join ClassOrderSlots s on c.ClassOrderID = s.ClassOrderID 
				and s.StudyTimeWeek = DAYOFWEEK(now())-1 and s.StudyTimeHour = DATE_FORMAT(now(),'%H') 
				and s.StudyTimeMinute =  DATE_FORMAT(DATE_ADD(now(),INTERVAL -1 MINUTE),'%i')  and (ClassOrderSlotEndDate >= now() or CLassOrderSlotEndDate IS NULL) 
			left join AssmtStudentSelfScores asss on asss.ClassID = c.ClassID and asss.MemberID = c.MemberID 
			left join Members m on c.MemberID = m.MemberID 
			where StartDateTime >= DATE_ADD(NOW(),INTERVAL -5 MINUTE) and StartDateTime < now() and asss.AssmtStudentSelfScoreRegDateTime is null
			and TeacherInDateTime IS NOT NULL and c.ClassAttendState < 3
			and StudentInTime IS NULL";

$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

// 미참석 학생 수만큼 반복
while($Row = $Stmt->fetch()) {

	$MemberName = $Row["MemberName"];
	$DecMemberPhone1 = $Row["DecMemberPhone1"];
	$DecMemberPhone2 = $Row["DecMemberPhone2"];

	// 학생 정보를 이용해서 알림톡 발송
	$msg = "$MemberName 학생이 수업에 현재 결석중으로 확인되어 연락드립니다. 수업에 입장 부탁드립니다.";
		
	$tmplId="mangoi_015";  //카카오 알림톡 템플릿 아이디(문자 메시지를 바꿀려면 비즈엠에서 템플릿 등록하고 그 메시지를 위에 복사후 템플릿id도 바꿔야 함)
	
	if (!empty($DecMemberPhone1))
		SendAlimtalk($DecMemberPhone1, $msg,$tmplId);
	if (!empty($DecMemberPhone2))	
		SendAlimtalk($DecMemberPhone2, $msg,$tmplId);
	
}



include_once($server_root_path.'/includes/dbclose.php');

?>
