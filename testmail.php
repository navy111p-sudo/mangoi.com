<?php
include_once('./includes/dbopen.php');
include_once('./includes/common.php');

	//메일 수신주소
	$toEmail 	= "hikid@hanmail.net";	

	//제목
	$subject="메일 테스트 세번째";
    
    //내용
	$content="내용이 들어갑니다";

	//한글 안깨지게 만들어줌
	$subject = "=?UTF-8?B?".base64_encode($subject)."?=";
	
	$headers .= 'From: mangoi@mangoi.co.kr '. "\r\n";  
	$headers .= 'Reply-To: mangoi@mangoi.co.kr ' . "\r\n"; 
     // Return Path는 PHP 5.2 에서까지만 쓰였다는것 같다 의미없음
	 //$headers .= 'Return-Path: mangoi@mangoi.co.kr ' . "\r\n";
	//참조
    //숨은참조
	//$headers .= 'BCC: mangoi@mangoi.co.kr ' . "\r\n";
	$headers .= 'Organization: Sender Organization ' . "\r\n";
	$headers .= 'MIME-Version: 1.0 ' . "\r\n";
	$headers .= 'Content-type: text/html; charset=utf-8 ' . "\r\n";
	$headers .= 'X-Priority: 3 ' ."\r\n" ;
	$headers .= 'X-Mailer: PHP". phpversion() ' ."\r\n" ;
	
	$mailResult = mail($toEmail, $subject, $content, $headers);

	if($mailResult) {
	    echo "발송완료";
	}else{
		echo "발송X";
	}
 //$mailto="받는주소";
/* 
 $mailto="losthero@daum.net";
 $subject = "PHP 메일 발송";
 $contents = "PHP mail()함수를 이용한 메일 발송 테스트";
 $headers = "From: mangoi@mangoi.co.kr";
 $result=mail($mailto, $subject, $content);
*/
 
/*
 $MemberEmail = 'losthero@daum.net';
 $memberName = '이성철';

 $AdminReturnEmail = 'sidhero2112@gmail.com';

 $MailHTML = '<H3>좋은 저녁</H3>';


 $to = $MemberEmail."|".$MemberName;
 $from = $AdminReturnEmail."|망고아이";
 $subject = "망고아이 아이디 안내";
 $content = $MailHTML;
 $html = "HTML";

$result  = getSendMail($to,$from,$subject,$content,$html);
 if($result){
  echo "mail success";
  }else  {
  echo "mail fail";
 }
*/
?>
