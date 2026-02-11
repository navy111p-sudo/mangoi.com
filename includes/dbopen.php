<?php
$DbHost = "localhost";
$DbName = "mangoi";
$DbUser = "mangoi";
$DbPass = "mi!@#2019";


// 허용할 IP 주소
$allowed_ip = '3.34.7.148';

// 방문자의 IP 주소 가져오기
$user_ip = $_SERVER['REMOTE_ADDR'];

// 허용되지 않은 IP 주소일 경우 점검 중 페이지로 리디렉션
// 활성화 여부를 쉽게 제어할 수 있도록 할 것

$site_status = 'open'; // open 또는 close

if ($site_status == 'close') {
    if ($user_ip != $allowed_ip) {
        header('Location: /maintenance.php');
        exit;
    }
}


try {
    $DbConn = new PDO("mysql:host=$DbHost;dbname=$DbName;charset=utf8", $DbUser, $DbPass);
    $DbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e){
    echo "Connection failed: " . $e->getMessage();
}
?>