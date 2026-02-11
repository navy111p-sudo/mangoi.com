<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";


$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$MemberLoginPW = isset($_REQUEST["MemberLoginPW"]) ? $_REQUEST["MemberLoginPW"] : "";
$MemberLoginID = isset($_REQUEST["MemberLoginID"]) ? $_REQUEST["MemberLoginID"] : "";
$MemberLoginNewPW = isset($_REQUEST["MemberLoginNewPW"]) ? $_REQUEST["MemberLoginNewPW"] : "";

// password_hash 에서의 사용하는 함수를 이용하여 bcrypt & sha1 를 사용하여 암호화
$MemberLoginNewPW_hash = password_hash(sha1($MemberLoginNewPW), PASSWORD_DEFAULT);

$Sql = "UPDATE Members set ";
	if ($MemberLoginNewPW!=""){
		$Sql .= " MemberLoginPW = :MemberLoginNewPW_hash ";
	}
$Sql .= "WHERE MemberID = :MemberID ";

$Stmt = $DbConn->prepare($Sql);
if ($MemberLoginNewPW!=""){
	$Stmt->bindParam(':MemberLoginNewPW_hash', $MemberLoginNewPW_hash);
}
$Stmt->bindParam(':MemberID', $MemberID);


$Stmt->execute();
$Stmt = null;

$AlertMsg = "회원정보가 수정되었습니다.";

include_once('./inc_header.php');

if ($err_num != 0){

?>
<body>
<script>
alert("<?=$err_msg?>");
history.go(-1);
</script>
<?php
	include_once('./inc_footer.php'); 
?>
</body>
</html>
<?
} else {
?>
	<body>
	<script>
	alert("<?=$AlertMsg?>");
	history.go(-1);
	</script>
	<?php
		include_once('./inc_footer.php'); 
	?>
	</body>
	</html>
	<?
	 
}

include_once('../includes/dbclose.php');


?>
