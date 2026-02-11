<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
?>
<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="ko"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="ko"> <!--<![endif]-->
<head>
<?
include_once('./includes/common_meta_tag.php');
include_once('./inc_header.php');
include_once('./inc_common_form_css.php');
$MemberLoginID     = isset($_COOKIE["LoginMemberID"   ]) ? $_COOKIE["LoginMemberID"   ] : "";


#-----------------------------------------------------------------------------------------------------------------------------------------#
# 회원 고유아이디(번호, 조직아이디) 찾기
#-----------------------------------------------------------------------------------------------------------------------------------------#
$Sql = "select T.*,M.* from Members as M 
			  left join Hr_OrganLevelTaskMembers T on T.MemberID=M.MemberID
			      where M.MemberLoginID=:MemberLoginID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberLoginID', $MemberLoginID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$My_MemberID     = $Row["MemberID"];

?>
<link rel="stylesheet" type="text/css" href="css/common.css" />
</head>
</body>
<?

$err_num = 0;
$err_msg = "";

$PayMonth = isset($_REQUEST["PayMonth"]) ? $_REQUEST["PayMonth"] : "";
$OverTimePay = isset($_REQUEST["OverTimePay"]) ? $_REQUEST["OverTimePay"] : "";

// 현재 급여정보가 입력중인지 확인. 만약 입력된 정보가 없거나, 이미 결재요청중이거나 지급완료인 경우에는 진행하지 않는다.
$Sql = "SELECT *
			FROM PayMonthState 
			WHERE PayMonth = :PayMonth AND PayState = 0";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':PayMonth', $PayMonth);
$Stmt->execute();
$rowCount = $Stmt->rowCount();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();


if ($rowCount > 0) {
	$PayMonthStateID = $Row["PayMonthStateID"];

	// 먼저 기존에 입력된 내용이 있는지 확인
	$Sql = "SELECT count(*) as isCount FROM PayOverTime
				WHERE MemberID=:MemberID AND PayMonthStateID=:PayMonthStateID";	

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':MemberID', $My_MemberID);
	$Stmt->bindParam(':PayMonthStateID', $PayMonthStateID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$isCount = $Row["isCount"];

	if ($isCount ==0) {
		// 입력된 게 없으면 새로 입력
		

		$Sql = "INSERT INTO PayOverTime
					(MemberID, OverTimePay, PayMonthStateID, Approval) 
					VALUES 
					(:MemberID, :OverTimePay, :PayMonthStateID, 0)";	

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':MemberID', $My_MemberID);
		$Stmt->bindParam(':PayMonthStateID', $PayMonthStateID);
		$Stmt->bindParam(':OverTimePay', $OverTimePay);
			
		$Stmt->execute();
		$Stmt = null;
	} else {
	// 있으면 업데이트

		$Sql = "UPDATE PayOverTime SET
					OverTimePay=:OverTimePay,  Approval=0 
					WHERE MemberID=:MemberID AND PayMonthStateID = :PayMonthStateID ";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':MemberID', $My_MemberID);
		$Stmt->bindParam(':PayMonthStateID', $PayMonthStateID);
		$Stmt->bindParam(':OverTimePay', $OverTimePay);
			
		$Stmt->execute();
		$Stmt = null;

	}

	

}
header("Location: overtimepay_form.php"); 
?>


<?
include_once('./inc_common_form_js.php');
?>
<script type="text/javascript" src="js/common.js"></script>
<link rel="stylesheet" type="text/css" href="css/jquery-confirm.min.css" />
<script type="text/javascript" src="js/jquery-confirm.min.js"></script>

<?
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');



?>
</body>
</html>

