<?
$http_host = $_SERVER['HTTP_HOST'];
$request_uri = $_SERVER['REQUEST_URI'];

$url = 'http://' . $http_host . $request_uri;
if ( strpos($url, "/mypage/") != false){ //잉글리시텔, 토마스
	echo "aaaa";
}else{
	echo "bbbb";
}
?>