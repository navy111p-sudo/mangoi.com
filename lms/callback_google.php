<?php
require_once 'lms/vendor/autoload.php';
  
// init configuration
$clientID = '295330190065-e47pvh55mpgvtf5ea37qn9t273ugp611.apps.googleusercontent.com';
$clientSecret = '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';
$redirectUri = 'http://www.mangoi.co.kr/callback_google.php';
   
// create Client Request to access Google API
$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");
  
// authenticate code from Google OAuth Flow
if (isset($_GET['code'])) {
  $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
  $client->setAccessToken($token['access_token']);
   
  // get profile info
  $google_oauth = new Google_Service_Oauth2($client);
  $google_account_info = $google_oauth->userinfo->get();
  $email =  $google_account_info->email;
  $name =  $google_account_info->name;
  echo "구글 로그인".$email;
  // now you can use this profile info to create account in your website and make user logged in.
} else {
  echo "<a href='".$client->createAuthUrl()."'>Google Login</a>";
}
?>
