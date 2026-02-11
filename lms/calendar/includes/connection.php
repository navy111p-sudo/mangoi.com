<?php
	// DB Connection Configuration
	define('DB_HOST', ''); 
	define('DB_USERNAME', 'mangoi'); 
	define('DB_PASSWORD', 'mi!@#2019'); 
	define('DATABASE', 'mangoi'); 
	define('TABLE', 'calendar');
	define('USERS_TABLE', 'users');
	
	define('SITE_FILES_URL', '');
	
    // 사용자 권한에 따라 관리자는 전체, 한국인 강사 및 직원은 slp, 필리핀 강사는 망고아이 스케줄만 -->
	$adminLevel = isset($_REQUEST["adminLevel"]) ? $_REQUEST["adminLevel"] : "";
	$_SESSION['adminLevel'] = $adminLevel;

    if ($_SESSION['adminLevel']==0 || $_SESSION['adminLevel']==1){    //마스터    
        $categories = array("일반일정", "망고아이 스케줄", "SLP 스케줄", "필리핀강사 휴가");
    } else if ($_SESSION['adminLevel']==15 ) {   //강사
        $categories = array("망고아이 스케줄");
		$_POST['filter'] = "망고아이 스케줄";
    } else {
		$categories = array("SLP 스케줄");
		$_POST['filter'] = "SLP 스케줄";
	}  

	
	/*
	Only applied for non user versions
	Should (non admin versions) display user events from the database?
	 true - does not display user events 
	 false - will display all events on the database even private ones on non admin versions (e.g: 'Simple')
	*/
	define('PUBLIC_PRIVATE_EVENTS', true);
	
	// Feature to import events
	define('IMPORT_EVENTS', true);
	
?>