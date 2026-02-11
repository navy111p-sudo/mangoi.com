<?
include_once('./includes/dbopen.php');
include_once('./includes/common.php');
include_once('./includes/member_check.php');

$LocalLinkMemberID = isset($_REQUEST["LocalLinkMemberID"]) ? $_REQUEST["LocalLinkMemberID"] : "";
$MemberID = $LocalLinkMemberID;


$Sql = "
		select 
				count(*) as TotalCount
		from AssmtStudentMonthlyScores A 
		where A.ClassID in (select ClassID from Classes where MemberID=$MemberID)  
		";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$TotalCount = $Row["TotalCount"];


if($TotalCount==0) {
	header("Location: ./report_sample_monthly.php");
} else {
	$Sql = "
		select 
			A.*
		from AssmtStudentMonthlyScores A 
		where A.ClassID in (select ClassID from Classes where MemberID=$MemberID)  
		order by A.AssmtStudentMonthlyScoreID asc
		limit 0,1
	";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);

	$Row = $Stmt->fetch();
	$AssmtStudentMonthlyScoreID = $Row["AssmtStudentMonthlyScoreID"];

	header("Location: ./report_monthly.php?AssmtStudentMonthlyScoreID=$AssmtStudentMonthlyScoreID");
}



?>