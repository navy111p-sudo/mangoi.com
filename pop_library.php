<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, minimal-ui">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<title>망고아이</title>
<link href="css/sub_style.css" rel="stylesheet" type="text/css" />

<link href="css/common.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">



<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="./js/jquery-confirm.min.js"></script>
<link href="./css/jquery-confirm.min.css" rel="stylesheet" type="text/css"/>
</head>
<body>
<header class="header_app_wrap">
    <h1 class="header_app_title TrnTag">도서관</h1>
    <a href="javascript:window.Exit=true" class="header_app_close header_app_gray"><img src="images/btn_app_close_black.png" class="icon"></a>
</header>
<div class="sub_wrap bg_gray padding_app" style="border:0;">
    <section class="mypage_wrap">
        <div class="mypage_area" id="iframe_place">
		<style>
		.iframe100 {
		  display: block;
		  border: none;
		  height: 100vh;
		  width: 100vw;
		}
		</style>

		<?php

			//Detect special conditions devices
			$iPod    = stripos($_SERVER['HTTP_USER_AGENT'],"iPod");
			$iPhone  = stripos($_SERVER['HTTP_USER_AGENT'],"iPhone");
			$iPad    = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");
			$Android = stripos($_SERVER['HTTP_USER_AGENT'],"Android");
			$webOS   = stripos($_SERVER['HTTP_USER_AGENT'],"webOS");

			//do something with this information
			if( $iPod || $iPhone || $iPad) {
			?>
			<script>
				location.href = "http://new.bookclubs.co.kr/pages/user/login?aca_code=mangoi";
	//		window.open('http://new.bookclubs.co.kr/pages/user/login?aca_code=mangoi', '_system');
			</script>
	
			<?php
			}else {
			?>
				<iframe src="http://new.bookclubs.co.kr/pages/user/login?aca_code=mangoi" class="iframe100"></iframe>
			<?
			}
			?> 

        </div>
    </section>

</div>


</body>
</html>






