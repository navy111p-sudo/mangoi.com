<?php

error_reporting( E_ALL );
ini_set( "display_errors", 1 );
/*
session_start();
*/
include_once('./includes/dbopen.php');
include_once('./includes/common.php');
include_once('./includes/password_hash.php');
 

// authenticate code from Google OAuth Flow
if ($_GET['code']) {

      $returnCode = $_GET["code"]; // 서버로 부터 토큰을 발급받을 수 있는 코드를 받아옵니다
      $restAPIKey = "53b552c616b3e023cf1bfb07b152c621"; // REST API KEY
      $callbacURI = urlencode("https://mangoi.co.kr/callback_kakao.php"); // Call Back URL
      //토큰
      $getTokenUrl = "https://kauth.kakao.com/oauth/token?grant_type=authorization_code&client_id=".$restAPIKey."&redirect_uri=".$callbacURI."&code=".$returnCode;

      $isPost = false;
      $ch = curl_init();                                    //1. curl 초기화 
      curl_setopt($ch, CURLOPT_URL, $getTokenUrl);          //2. URL 지정
      curl_setopt($ch, CURLOPT_POST, $isPost);              //3. $isPost = false; POST 통신이 아니므로
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);       //4.  true일 경우 curl_exec의 결과값을 return 하게 되어 변수에 저장 가능
      
      $headers = array();                                   //header 배열 생성
      $loginResponse = curl_exec ($ch);                     //4. $ch 실행             
      $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); //6. 연결 리소스 핸들 컬에 대한 정보 얻기
      curl_close ($ch);                                     //7. Close a cURL session
      
      //사용자 정보 요청
      $jsonres= json_decode($loginResponse);      //Access Token만 따로 뺌
      //var_dump($loginResponse);
      $accessToken = $jsonres->access_token;
      $header = "Bearer ".$accessToken; // Bearer 다음에 공백 추가
      $getProfileUrl = "https://kapi.kakao.com/v2/user/me"; // 개인정보가져오는 url
      
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $getProfileUrl);
      curl_setopt($ch, CURLOPT_POST, $isPost);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      
      $headers = array();
      $headers[] = "Authorization: ".$header;
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      
      $profileResponse = curl_exec ($ch);
      $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
      curl_close ($ch);
      //var_dump($profileResponse);
      $profileResponse = json_decode($profileResponse);
      
      $MemberName = $profileResponse->properties->nickname;
      $Email = $profileResponse->kakao_account->email;
      
      

     // 로그인과 관련된 프로세스 진행

    //해당 아이디값을 정상적으로 가져온다면 디비에 해당 아이디로 회원가입 여부 확인 하여 회원 가입을 하였으면 자동 로그인 구현.

          $Sql = "SELECT * from Members where MemberLoginID='". $Email ."'";
          $Stmt = $DbConn->prepare($Sql);
          $Stmt->execute();
          $Stmt->setFetchMode(PDO::FETCH_ASSOC);
          $Row = $Stmt->fetch();
          $Stmt = null;
          
          if($Row["MemberID"]){ // 이미 가입된 회원이면 자동로그인한다.
              $ApplyMemberLoginID = $Row['MemberLoginID'];
              setcookie("LoginMemberID", $ApplyMemberLoginID, 0);
              setcookie("LinkLoginMemberID", $ApplyMemberLoginID, 0);
              $Sql = "UPDATE Members set LastLoginDateTime=now() where MemberLoginID=:ApplyMemberLoginID";
              $Stmt = $DbConn->prepare($Sql);
              $Stmt->bindParam(':ApplyMemberLoginID', $ApplyMemberLoginID);
              $Stmt->execute();
              $Stmt = null;
              echo "<script>
                  //팝업창에서 부모창을 다른페이지로 이동합니다.
                  window.opener.location.href='index.php';
                  self.close();
                  </script>";
              //header("Location: index.php");
              

              exit;
          } else {        // 새로 회원가입을 하고 자동로그인추가한다.
              
              $Sql = " INSERT into Members ( ";
                  $Sql .= " MemberLoginID, ";
                  $Sql .= " MemberName, ";
                  $Sql .= " MemberEmail, ";
                  $Sql .= " MemberRegDateTime, ";
                  $Sql .= " MemberModiDateTime, ";
                  $Sql .= " MemberState ";
              $Sql .= " ) values ( ";
                  $Sql .= " '".$Email."', ";
                  $Sql .= " '".$MemberName."', ";
                  $Sql .= " HEX(AES_ENCRYPT('".$Email."', '".$EncryptionKey."')), ";
                  $Sql .= " now(), ";
                  $Sql .= " now(), ";
                  $Sql .= " 1 ";
              $Sql .= " )";

              
              $Stmt = $DbConn->prepare($Sql);
              $Stmt->execute();
              
              $Stmt = null;
              echo "redirect2";
              setcookie("LoginMemberID", $Email, 0);
              setcookie("LinkLoginMemberID", $Email, 0);
              
              $Sql = "SELECT * from Members where MemberLoginID='". $Email ."'";
              $Stmt = $DbConn->prepare($Sql);
              $Stmt->execute();
              $Stmt->setFetchMode(PDO::FETCH_ASSOC);
              $Row = $Stmt->fetch();

              $MemberID = $Row["MemberID"];
              echo "redirect3";
              InsertPoint(1, 0, $MemberID, "회원가입(웹)", "회원가입(웹)" ,$OnlineSiteMemberRegPoint);
              SendSmsWelcome($MemberID, $EncryptionKey);
              echo "<script>
                  //팝업창에서 부모창을 다른페이지로 이동합니다.
                  window.opener.location.href='member_form_sns.php';
                  self.close();
                  </script>";
              //header("Location: member_form_sns.php");

              exit;      
          }
      

    
} else {
  header("Location: http://www.mangoi.co.kr/login_form.php");
 }
include_once('./includes/dbclose.php');
?>
