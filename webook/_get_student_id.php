<?php

$student_id = isset($_POST["MemberLoginID"]) ? $_POST["MemberLoginID"] : "";
$secret_key = isset($_REQUEST["secret_key"]) ? $_REQUEST["secret_key"] : "";
$cid = isset($_REQUEST["cid"]) ? $_REQUEST["cid"] : "";
if($student_id) {
	// 값이 있다면
	$Tempstudent_id = $student_id;
}

?>