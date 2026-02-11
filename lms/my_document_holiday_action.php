<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$ListParam = str_replace("^^", "&", $ListParam);


$DocumentReportID = isset($_REQUEST["DocumentReportID"]) ? $_REQUEST["DocumentReportID"] : "";
$DocumentID = isset($_REQUEST["DocumentID"]) ? $_REQUEST["DocumentID"] : "";

$DocumentReportName = isset($_REQUEST["DocumentReportName"]) ? $_REQUEST["DocumentReportName"] : "";
$DocumentReportContent = isset($_REQUEST["DocumentReportContent"]) ? $_REQUEST["DocumentReportContent"] : "";
$DocumentReportState = isset($_REQUEST["DocumentReportState"]) ? $_REQUEST["DocumentReportState"] : "";

$PayDate = isset($_REQUEST["PayDate"]) ? $_REQUEST["PayDate"] : "";
$AccCode = isset($_REQUEST["AccCode"]) ? $_REQUEST["AccCode"] : "";
$FileName = isset($_REQUEST["FileName"]) ? $_REQUEST["FileName"] : "";
$FileRealName = isset($_REQUEST["FileRealName"]) ? $_REQUEST["FileRealName"] : "";
$OrganName = isset($_REQUEST["OrganName"]) ? $_REQUEST["OrganName"] : "";
$OrganPhone = isset($_REQUEST["OrganPhone"]) ? $_REQUEST["OrganPhone"] : "";
$OrganManagerName = isset($_REQUEST["OrganManagerName"]) ? $_REQUEST["OrganManagerName"] : "";
$PayMethod = isset($_REQUEST["PayMethod"]) ? $_REQUEST["PayMethod"] : "";
$RequestPayDate = isset($_REQUEST["RequestPayDate"]) ? $_REQUEST["RequestPayDate"] : date("Y-m-d");
$PayMemo = isset($_REQUEST["PayMemo"]) ? $_REQUEST["PayMemo"] : "";
$StaffHolidayID = isset($_REQUEST["StaffHolidayID"]) ? $_REQUEST["StaffHolidayID"] : "";
$SpentDays = isset($_REQUEST["Holiday"]) ? $_REQUEST["Holiday"] : "";
$StartDate = isset($_REQUEST["StartDate"]) ? $_REQUEST["StartDate"] : "";
$EndDate = isset($_REQUEST["EndDate"]) ? $_REQUEST["EndDate"] : "";
$HolidayType = isset($_REQUEST["HolidayType"]) ? $_REQUEST["HolidayType"] : "";
$Reason = $DocumentReportContent;


if ($DocumentReportID=="0"){  //신규 문서인 경우에 DocumentReports에 새로 입력한다.

	$MemberID = $_LINK_ADMIN_ID_;

	$Sql = " insert into DocumentReports ( ";
		$Sql .= " DocumentID, ";
		$Sql .= " MemberID, ";
		$Sql .= " DocumentReportName, ";
		$Sql .= " DocumentReportContent, ";
		$Sql .= " PayDate, ";
		$Sql .= " AccCode, ";
		$Sql .= " FileName, ";
		$Sql .= " FileRealName, ";
		$Sql .= " OrganName, ";
		$Sql .= " OrganPhone, ";
		$Sql .= " OrganManagerName, ";
		$Sql .= " PayMethod, ";
		$Sql .= " RequestPayDate, ";
		$Sql .= " PayMemo, ";
		$Sql .= " DocumentReportRegDateTime, ";
		$Sql .= " DocumentReportModiDateTime, ";
		$Sql .= " DocumentReportState ";
	$Sql .= " ) values ( ";
		$Sql .= " :DocumentID, ";
		$Sql .= " :MemberID, ";
		$Sql .= " :DocumentReportName, ";
		$Sql .= " :DocumentReportContent, ";
		$Sql .= " :PayDate, ";
		$Sql .= " :AccCode, ";
		$Sql .= " :FileName, ";
		$Sql .= " :FileRealName, ";
		$Sql .= " :OrganName, ";
		$Sql .= " :OrganPhone, ";
		$Sql .= " :OrganManagerName, ";
		$Sql .= " :PayMethod, ";
		$Sql .= " :RequestPayDate, ";
		$Sql .= " :PayMemo, ";

		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :DocumentReportState ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':DocumentID', $DocumentID);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->bindParam(':DocumentReportName', $DocumentReportName);
	$Stmt->bindParam(':DocumentReportContent', $DocumentReportContent);
	$Stmt->bindParam(':PayDate', $PayDate);
	$Stmt->bindParam(':AccCode', $AccCode);
	$Stmt->bindParam(':FileName', $FileName);
	$Stmt->bindParam(':FileRealName', $FileRealName);
	$Stmt->bindParam(':OrganName', $OrganName);
	$Stmt->bindParam(':OrganPhone', $OrganPhone);
	$Stmt->bindParam(':OrganManagerName', $OrganManagerName);
	$Stmt->bindParam(':PayMethod', $PayMethod);
	$Stmt->bindParam(':RequestPayDate', $RequestPayDate);
	$Stmt->bindParam(':PayMemo', $PayMemo);
	$Stmt->bindParam(':DocumentReportState', $DocumentReportState);
	$Stmt->execute();
	$DocumentReportID = $DbConn->lastInsertId();
	$Stmt = null;



} else {

	$Sql = "UPDATE DocumentReports set ";
		$Sql .= " DocumentReportName = :DocumentReportName, ";
		$Sql .= " DocumentReportContent = :DocumentReportContent, ";
		$Sql .= " PayDate = :PayDate, ";
		$Sql .= " AccCode = :AccCode, ";
		$Sql .= " FileName = :FileName, ";
		$Sql .= " FileRealName = :FileRealName, ";
		$Sql .= " OrganName = :OrganName, ";
		$Sql .= " OrganPhone = :OrganPhone, ";
		$Sql .= " OrganManagerName = :OrganManagerName, ";
		$Sql .= " PayMethod = :PayMethod, ";
		$Sql .= " RequestPayDate = :RequestPayDate, ";
		$Sql .= " PayMemo = :PayMemo, ";
		$Sql .= " DocumentReportState = :DocumentReportState, ";
		$Sql .= " DocumentReportModiDateTime = now() ";
	$Sql .= " where DocumentReportID = :DocumentReportID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':DocumentReportName', $DocumentReportName);
	$Stmt->bindParam(':DocumentReportContent', $DocumentReportContent);
	$Stmt->bindParam(':PayDate', $PayDate);
	$Stmt->bindParam(':AccCode', $AccCode);
	$Stmt->bindParam(':FileName', $FileName);
	$Stmt->bindParam(':FileRealName', $FileRealName);
	$Stmt->bindParam(':OrganName', $OrganName);
	$Stmt->bindParam(':OrganPhone', $OrganPhone);
	$Stmt->bindParam(':OrganManagerName', $OrganManagerName);
	$Stmt->bindParam(':PayMethod', $PayMethod);
	$Stmt->bindParam(':RequestPayDate', $RequestPayDate);
	$Stmt->bindParam(':PayMemo', $PayMemo);
	$Stmt->bindParam(':DocumentReportState', $DocumentReportState);
	$Stmt->bindParam(':DocumentReportID', $DocumentReportID);
	$Stmt->execute();
	$Stmt = null;


	
	$Sql = "DELETE from SpentHoliday where DocumentReportID=:DocumentReportID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':DocumentReportID', $DocumentReportID);
	$Stmt->execute();
	$Stmt = null;


	$Sql = "DELETE from DocumentReportMembers where DocumentReportID=:DocumentReportID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':DocumentReportID', $DocumentReportID);
	$Stmt->execute();
	$Stmt = null;
}

if ($DocumentReportState != 0) {    // DocumentReportState 가 삭제가 아닐 경우에만 실행

	// 결재라인을 DocumentReportMembers 테이블에 추가한다.
	for ($ii=0; $ii<=5; $ii++){

		$DocumentReportMemberID = isset($_REQUEST["DocumentReportMemberID".$ii]) ? $_REQUEST["DocumentReportMemberID".$ii] : "";
		if ($DocumentReportMemberID!="0" && $DocumentReportMemberID!=NULL && $DocumentReportMemberID!=""){

			$DocumentReportMemberOrder = $ii;

			$Sql = "INSERT into DocumentReportMembers ( ";
				$Sql .= " DocumentReportID, ";
				$Sql .= " MemberID, ";
				$Sql .= " DocumentReportMemberOrder, ";
				$Sql .= " DocumentReportMemberRegDateTime, ";
				$Sql .= " DocumentReportMemberModiDateTime, ";
				$Sql .= " DocumentReportMemberState ";
			$Sql .= " ) values ( ";
				$Sql .= " :DocumentReportID, ";
				$Sql .= " :MemberID, ";
				$Sql .= " :DocumentReportMemberOrder, ";
				$Sql .= " now(), ";
				$Sql .= " now(), ";
				$Sql .= " 0 ";
			$Sql .= " ) ";

			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':DocumentReportID', $DocumentReportID);
			$Stmt->bindParam(':DocumentReportMemberOrder', $DocumentReportMemberOrder);
			$Stmt->bindParam(':MemberID', $DocumentReportMemberID);
			$Stmt->execute();
			$Stmt = null;

		}
	}
	


	// 휴가 사용내역을 테이블에 등록한다.
	$Sql = "INSERT INTO SpentHoliday ( ";
				$Sql .= " StaffHolidayID, ";
				$Sql .= " DocumentReportID, ";				
				$Sql .= " SpentDays, ";
				$Sql .= " RegistDate, ";
				$Sql .= " StartDate, ";
				$Sql .= " EndDate, ";
				$Sql .= " HolidayType, ";
				$Sql .= " Reason ";
			$Sql .= " ) values ( ";
				$Sql .= " :StaffHolidayID, ";
				$Sql .= " :DocumentReportID, ";
				$Sql .= " :SpentDays, ";
				$Sql .= " now(), ";
				$Sql .= " :StartDate, ";
				$Sql .= " :EndDate, ";								
				$Sql .= " :HolidayType, ";
				$Sql .= " :Reason ";
			$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':StaffHolidayID', $StaffHolidayID);
	$Stmt->bindParam(':DocumentReportID', $DocumentReportID);
	$Stmt->bindParam(':SpentDays', $SpentDays);
	$Stmt->bindParam(':StartDate', $StartDate);
	$Stmt->bindParam(':EndDate', $EndDate);
	$Stmt->bindParam(':HolidayType', $HolidayType);
	$Stmt->bindParam(':Reason', $Reason);
	$Stmt->execute();

	
}


if ($err_num != 0){
	include_once('./inc_header.php');
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
}

include_once('../includes/dbclose.php');


if ($err_num == 0){
	header("Location: my_document_holiday_list.php?$ListParam"); 
	exit;
}
?>