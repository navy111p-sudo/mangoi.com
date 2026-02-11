<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('../includes/dbclose.php');

setcookie("LoginAdminID", "", 0, "/", ".".$DefaultDomain2);
setcookie("LoginMemberID", "", 0, "/", ".".$DefaultDomain2);

setcookie("LinkLoginAdminID", "", 0, "/", ".".$DefaultDomain2);
setcookie("LinkLoginMemberID", "", 0, "/", ".".$DefaultDomain2);

header("Location: ./"); 
exit;
?>
