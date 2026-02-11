<?php

include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('../includes/dbclose.php');


//  foreach($_COOKIE as $key=>$val){
//      setcookie($key,"",time()-3600,"/", ".".$DefaultDomain2);
//  }

 setcookie("LoginAdminID", "", time()-3600, "/");
 setcookie("LoginMemberID", "", time()-3600, "/");

 setcookie("LinkLoginAdminID", "", time()-3600, "/");
 setcookie("LinkLoginMemberID", "", time()-3600, "/");

 setcookie("Class10MinuteBefore", "", time()-3600, "/");
 setcookie("EndDateTimeStamp", "", time()-3600, "/");


 unset($_COOKIE["LoginAdminID"]);
 unset($_COOKIE["LoginMemberID"]);
 unset($_COOKIE["LinkLoginAdminID"]);
 unset($_COOKIE["LinkLoginMemberID"]);
 unset($_COOKIE["Class10MinuteBefore"]);
 unset($_COOKIE["EndDateTimeStamp"]);

header("Location: ./"); 
exit;
?>
