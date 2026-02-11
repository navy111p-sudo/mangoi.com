<?php
include_once('./includes/dbopen.php');
include_once('./includes/common.php');
include_once('./includes/member_check.php');

// 쿠키 도메인 설정
$cookieDomain = "." . $DefaultDomain2;

// 쿠키 삭제 함수 정의
function clearCookie($name, $domain) {
    setcookie($name, "", time() - 3600, "/", $domain);
    unset($_COOKIE[$name]);
}

// 로그인 쿠키 정보 가져오기
$LinkLoginMemberID = $_COOKIE["LinkLoginMemberID"];
$LoginMemberID = $_COOKIE["LoginMemberID"];

// HideHomeIntro 쿠키 삭제
clearCookie("HideHomeIntro", $cookieDomain);

// 본계정과 링크 계정이 같다면 (학생이 아니라면)
if ($LinkLoginMemberID == $LoginMemberID) {
    clearCookie("LoginAdminID", $cookieDomain);
    clearCookie("LoginMemberID", $cookieDomain);
    clearCookie("LinkLoginAdminID", $cookieDomain);
    clearCookie("LinkLoginMemberID", $cookieDomain);

    // 로그아웃 후 메인 페이지로 리다이렉트
    header("Location: index.php");
    exit;
} else { // 본 계정과 링크 계정이 다르다면 학생이라는 가정
    // 쿠키 재설정
    setcookie("LoginAdminID", $LoginMemberID, time() + 86400 * 365, "/", $cookieDomain);
    setcookie("LoginMemberID", $LoginMemberID, time() + 86400 * 365, "/", $cookieDomain);
    setcookie("LinkLoginAdminID", $LoginMemberID, time() + 3600, "/", $cookieDomain);
    setcookie("LinkLoginMemberID", $LoginMemberID, time() + 3600, "/", $cookieDomain);
    setcookie("ResponseLogoutCookie", 1, time() + 3600, "/", $cookieDomain);

    // 로그아웃 후 강사 모드 페이지로 리다이렉트
    header("Location: mypage_teacher_mode.php");
    exit;
}

include_once('./includes/dbclose.php');
?>
