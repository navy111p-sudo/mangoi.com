<?php
 //$mailto="받는주소";
 $mailto="sidhero@naver.com";
 $subject="메일을 테스트합니다.hahah";
 $content="정말로 테스트합니다.";
 $result=mail($mailto, $subject, $content);
 if($result){
  echo "mail success";
  }else  {
  echo "mail fail";
 }
?>