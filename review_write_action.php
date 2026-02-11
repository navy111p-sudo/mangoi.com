<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$TeacherID = isset($_REQUEST["TeacherID"]) ? $_REQUEST["TeacherID"] : "";
$Content = isset($_REQUEST["content"]) ? $_REQUEST["content"] : "";
$Rating = isset($_REQUEST["rating"]) ? $_REQUEST["rating"] : "";
$isRedirect = isset($_REQUEST["isRedirect"]) ? $_REQUEST["isRedirect"] : "";


$Sql = "INSERT into reviews  
			( 
				memberID,
				teacherID,
				content,
				rating,
				submit_date
			) 
		values (  
			:memberID,  
			:teacherID,
			:content,
			:rating,  
			now()  
		)";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':memberID', $MemberID);
$Stmt->bindParam(':teacherID', $TeacherID);
$Stmt->bindParam(':content', $Content);
$Stmt->bindParam(':rating', $Rating);


$Stmt->execute();
$Stmt = null;


if ($err_num != 0){
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
</head>
<body>
<script>
alert("<?=$err_msg?>");
history.go(-1);
</script>
</body>
</html>
<?php
} else {
?>	
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
</head>
<body>
<script>
	parent.$.fn.colorbox.close();
	<?
	if ($isRedirect != '0') {
	?>
	parent.location.href="teacher_intro.php?teacherIntro=1";
	<? }?>
</script>
</body>
</html>	
<?	
}



include_once('./includes/dbclose.php');
?>