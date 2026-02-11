<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('../includes/member_check.php');


$LinkLoginMemberID = $_COOKIE["LinkLoginMemberID"];
$LoginMemberID = $_COOKIE["LoginMemberID"];

setcookie("HideHomeIntro", "", 0, "/", ".".$DefaultDomain2);

// 본계정과 링크 계정이 같다면 ( 학생이 아니라면 )
if($LinkLoginMemberID==$LoginMemberID) {
	setcookie("LoginAdminID", "", 0, "/", ".".$DefaultDomain2);
	setcookie("LoginMemberID", "", 0, "/", ".".$DefaultDomain2);

	setcookie("LinkLoginAdminID", "", 0, "/", ".".$DefaultDomain2);
	setcookie("LinkLoginMemberID", "", 0, "/", ".".$DefaultDomain2);

	header("Location: index.php"); 
	exit;

} else { // 본 계정과 링크 계정이 다르다면 학생이라는 가정	
	
	setcookie("LoginAdminID", $LoginMemberID, time() + 86400*365, "/", ".".$DefaultDomain2);
	setcookie("LoginMemberID", $LoginMemberID, time() + 86400*365, "/", ".".$DefaultDomain2);

	setcookie("LinkLoginAdminID", $LoginMemberID, time() + 3600, "/", ".".$DefaultDomain2);
	setcookie("LinkLoginMemberID", $LoginMemberID, time() + 3600, "/", ".".$DefaultDomain2);

	setcookie("ResponseLogoutCookie", 1, time() + 3600, "/", ".".$DefaultDomain2);

	header("Location: mypage_teacher_mode.php"); 
}

include_once('../includes/dbclose.php');

?>