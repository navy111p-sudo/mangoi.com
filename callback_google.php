<?php
require_once 'lms/vendor/autoload.php';
include_once('./includes/dbopen.php');
include_once('./includes/common.php');
include_once('./includes/password_hash.php');
  
// init configuration
$clientID = '295330190065-e47pvh55mpgvtf5ea37qn9t273ugp611.apps.googleusercontent.com';
$clientSecret = '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';
$redirectUri = 'https://www.mangoi.co.kr/callback_google.php';
   
// create Client Request to access Google API
$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");
  
// authenticate code from Google OAuth Flow
if (isset($_REQUEST['code'])) {
  $token = $client->fetchAccessTokenWithAuthCode($_REQUEST['code']);
  $client->setAccessToken($token['access_token']);
   
  // 구글 프로필 정보 가져오기
  $google_oauth = new Google_Service_Oauth2($client);
  $google_account_info = $google_oauth->userinfo->get();
  $Email =  $google_account_info->email;
  $MemberName =  $google_account_info->name;
  
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
              setcookie("LoginMemberID", $ApplyMemberLoginID, 0, false, NULL);
              setcookie("LinkLoginMemberID", $ApplyMemberLoginID, 0, false, NULL);
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
              //echo $Sql;
              $Stmt->execute();
              //echo $Email."test2";
              
              $Stmt = null;

              echo "redirect2";
              $Sql = "SELECT * from Members where MemberLoginID='". $Email ."'";
              $Stmt = $DbConn->prepare($Sql);
              $Stmt->execute();
              $Stmt->setFetchMode(PDO::FETCH_ASSOC);
              $Row = $Stmt->fetch();

              $MemberID = $Row["MemberID"];

              InsertPoint(1, 0, $MemberID, "회원가입(웹)", "회원가입(웹)" ,$OnlineSiteMemberRegPoint);
              SendSmsWelcome($MemberID, $EncryptionKey);
              setcookie("LoginMemberID", $Email, 0, "/");
              setcookie("LinkLoginMemberID", $Email, 0, "/");
              echo "redirect3";

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
