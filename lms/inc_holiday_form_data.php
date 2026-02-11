<?

if (!function_exists('getVacationYearByDate')) {
	// Vacation year starts on March 1st; Jan/Feb belong to the previous year.
	function getVacationYearByDate($dateValue) {
		$timestamp = strtotime($dateValue);
		if ($timestamp === false) {
			$timestamp = time();
		}
		$year = intval(date("Y", $timestamp));
		if (intval(date("n", $timestamp)) < 3) {
			$year -= 1;
		}
		return $year;
	}
}

	// 휴가 세부내역을 가져온다.
	$Sql = "SELECT * FROM SpentHoliday
	WHERE DocumentReportID =  :DocumentReportID
	";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':DocumentReportID', $DocumentReportID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();

	$SpentDays = isset($Row["SpentDays"]) ? $Row["SpentDays"] : 0;
	$StartDate = isset($Row["StartDate"]) ? $Row["StartDate"] : "";
	$EndDate = isset($Row["EndDate"]) ? $Row["EndDate"] : "";

	$SearchYear = 0;
	if ($StartDate != "") {
		$SearchYear = getVacationYearByDate($StartDate);
	}
	if ($SearchYear == 0) {
		$SearchYear = getVacationYearByDate(date("Y-m-d"));
	}

	// 현재 설정되어 있는 최대 휴가일 수를 가져온다.
	$Sql2 = "SELECT StaffHolidayID, MaxHoliday
				from StaffHoliday 
				where StaffID = $DocumentReportStaffID and Year = $SearchYear";
	$Stmt2 = $DbConn->prepare($Sql2);
	$Stmt2->execute();
	$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
	$Row2 = $Stmt2->fetch();
	
	$StaffHolidayID = isset($Row2["StaffHolidayID"]) ? $Row2["StaffHolidayID"] : 0;
	$MaxHoliday = isset($Row2["MaxHoliday"]) ? $Row2["MaxHoliday"] : 0;
	
	// 이미 사용한 휴가 일수를 계산한다.
	$Sql2 = "SELECT SUM(SpentDays) AS SpentHoliday
				FROM SpentHoliday WHERE StaffHolidayID = $StaffHolidayID  
				GROUP BY StaffHolidayID ";
	$Stmt2 = $DbConn->prepare($Sql2);
	$Stmt2->execute();
	$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
	$Row2 = $Stmt2->fetch();
	$SpentHoliday = isset($Row2["SpentHoliday"]) ? $Row2["SpentHoliday"] : 0;

	#-----------------------------------------------------------------------------------------------------------------------------------------#
	# 문서를 작성한 직원(번호, 조직아이디) 찾기
	#-----------------------------------------------------------------------------------------------------------------------------------------#
	$Sql = "SELECT T.*,M.*, O.* from Members as M 
				left join Hr_OrganLevelTaskMembers T on T.MemberID=M.MemberID
				LEFT JOIN Hr_OrganLevels O on T.Hr_OrganLevelID = O.Hr_OrganLevelID
					where M.MemberID=:MemberID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':MemberID', $DocumentReportMemberID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	
	$Document_MemberName   = $Row["MemberName"];
	$Document_OrganName    = $Row["Hr_OrganLevelName"];
	$StaffID 		 = $Row["StaffID"]; 

?>
