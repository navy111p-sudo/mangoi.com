<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
include_once('./gridphp/config_lms.php');

// $PayMonth 급여 귀속년월 기본적으로 이번달을 귀속년월로 잡아주고
// 조건을 바꾸면 거기에 맞게 귀속년월이 바뀐다. 매월 1일로 설정한다.(혼선 방지)
$Month = date("Y-m-01");
$PayMonth = isset($_REQUEST["PayMonth"]) ? $_REQUEST["PayMonth"] : $Month;
$PayInputMode = isset($_REQUEST["PayInputMode"]) ? $_REQUEST["PayInputMode"] : "false";   // 새로운 급여정보 생성인지를 표시
$RecalcurationMode = isset($_REQUEST["RecalcurationMode"]) ? $_REQUEST["RecalcurationMode"] : "false";   // 4대보험 재계산모드인지 표시
$IsMailSend = isset($_REQUEST["isMailSend"]) ? $_REQUEST["isMailSend"] : 0;  //메일을 발송하고 돌아왔는지 확인

// 귀속년월의 급여 지급 상태($PayState)를 가져온다. 0:입력, 1:결재요청, 2:결재완료, 3:지급완료
$Sql3 = "SELECT *  FROM PayMonthState 
			WHERE PayMonth = :PayMonth";
$Stmt3 = $DbConn->prepare($Sql3);
$Stmt3->bindParam(':PayMonth', $PayMonth);
$Stmt3->execute();
$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
$Row3 = $Stmt3->fetch();
$PayState = isset($Row3["PayState"])?$Row3["PayState"]:99; //귀속년월 급여 상태 (0:입력,1:결재요청,2:결재완료,3:지급완료, 99:상태없음)
$PayMonthStateID = isset($Row3["PayMonthStateID"])?$Row3["PayMonthStateID"]:""; 


// 먼저 PayInsuranceRate 테이블에서 귀속 연도의 보험요율을 가지고 온다.
// 귀속 연도의 보험요율이 있으면 그걸 사용하고 없으면 이전 연도의 보험요율을 가지고 온다.
$Year = substr($PayMonth,0,4);
$PreYear = $Year - 1; 

$Sql3 = "SELECT * FROM PayInsuranceRate  
			WHERE Year = COALESCE((SELECT Year FROM PayInsuranceRate WHERE Year  = :Year),:PreYear)";
$Stmt3 = $DbConn->prepare($Sql3);
$Stmt3->bindParam(':Year', $Year);
$Stmt3->bindParam(':PreYear', $PreYear);

$Stmt3->execute();
$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
$Row3 = $Stmt3->fetch();

$EmploymentRate = $Row3["EmploymentInsurance"];  	//고용보험 요율
$HealthRate = $Row3["HealthInsurance"];				//건강보험 요율
$CareRate = $Row3["CareInsurance"];					//장기요양보험 요율
$NationalRate = $Row3["NationalPension"];			//국민연금 요율


// 귀속년월의 과세 여부 정보를 가지고 온다. 만약 해당 정보가 없으면 가장 최근의 정보를 가지고 온다.
$TaxInfoPayMonth = substr(str_replace("-","",$PayMonth),0,6);;
$Sql = "SELECT  
				*
		from PayTaxInfo 
		where PayMonth=:TaxInfoPayMonth";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':TaxInfoPayMonth', $TaxInfoPayMonth);
$Stmt->execute();
if ($Stmt->rowCount()<=0) {
	$Sql = "SELECT  
		*
		from PayTaxInfo 
		order by PayMonth desc limit 1";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
}
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TaxBasePay = $Row["BasePay"];
$TaxSpecialDutyPay = $Row["SpecialDutyPay"];
$TaxPositionPay = $Row["PositionPay"];
$TaxOverTimePay = $Row["OverTimePay"];
$TaxReplacePay = $Row["ReplacePay"];
$TaxIncentivePay = $Row["IncentivePay"];
$TaxSpecial1 = $Row["Special1"];
$TaxSpecial2 = $Row["Special2"];
$TaxAdd1Name = $Row["Add1Name"];
$TaxAdd2Name = $Row["Add2Name"];
$TaxAdd3Name = $Row["Add3Name"];
$TaxAdd4Name = $Row["Add4Name"];
$TaxAdd5Name = $Row["Add5Name"];
$TaxAdd6Name = $Row["Add6Name"];
$TaxAdd7Name = $Row["Add7Name"];
$TaxAdd1 = $Row["Add1"];
$TaxAdd2 = $Row["Add2"];
$TaxAdd3 = $Row["Add3"];
$TaxAdd4 = $Row["Add4"];
$TaxAdd5 = $Row["Add5"];
$TaxAdd6 = $Row["Add6"];
$TaxAdd7 = $Row["Add7"];


// 귀속년월의 공제여부 정보를 가지고 온다. 만약 해당 정보가 없으면 가장 최근의 정보를 가지고 온다.
$DeductionInfoPayMonth = substr(str_replace("-","",$PayMonth),0,6);;
$Sql = "SELECT  
				*
		from PayDeductionInfo 
		where PayMonth=:DeductionInfoPayMonth";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':DeductionInfoPayMonth', $DeductionInfoPayMonth);
$Stmt->execute();
if ($Stmt->rowCount()<=0) {
	$Sql = "SELECT  
		*
		from PayDeductionInfo 
		order by PayMonth desc limit 1";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
}
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$DeductionEmploymentInsurance = $Row["EmploymentInsurance"];
$DeductionHealthInsurance = $Row["HealthInsurance"];
$DeductionCareInsurance = $Row["CareInsurance"];
$DeductionNationalPension = $Row["NationalPension"];
$DeductionAdd1Name = $Row["Add1Name"];
$DeductionAdd2Name = $Row["Add2Name"];
$DeductionAdd3Name = $Row["Add3Name"];
$DeductionAdd4Name = $Row["Add4Name"];
$DeductionAdd1 = $Row["Add1"];
$DeductionAdd2 = $Row["Add2"];
$DeductionAdd3 = $Row["Add3"];
$DeductionAdd4 = $Row["Add4"];



// 급여 입력모드
if ($PayInputMode == "true") {
	// 지정한 월의 급여 정보를 생성해서 table에 넣어준다.
	// 혹시 지정한 월의 급여 정보가 있으면 그 정보는 업데이트한다. 
	$Sql = "SELECT
				A.*,
				B.MemberID 
				from Staffs A 
				inner join Members B on A.StaffID=B.StaffID and B.MemberLevelID=4
				where A.StaffState = 1";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);

	while($Row = $Stmt->fetch()) {
		$Sql2 = "SELECT A.*, C.* 
					FROM PayInfo A LEFT JOIN Hr_OrganLevelTaskMembers B
					ON A.MemberID = B.MemberID
					LEFT JOIN Hr_OrganLevels C
					ON B.Hr_OrganLevelID = C.Hr_OrganLevelID 
					WHERE A.MemberID = :MemberID";
		$Stmt2 = $DbConn->prepare($Sql2);
		$Stmt2->bindParam(':MemberID', $Row["MemberID"]);
		$Stmt2->execute();
		$RowCount = $Stmt2->rowCount();
		$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
		$Row2 = $Stmt2->fetch();

		// 급여 기본 정보가 등록되어 있으면 그 정보를 없으면 0 값을 입력
		// 급여 기본 정보를 바탕으로 4대보험을 계산해서 자동으로 입력해 준다.
		if ($RowCount > 0) {
			$WorkType = $Row2["WorkType"];              //근로자 0 / 사업자 1
			$BasePay = $Row2["BasePay"];				//기본급
			$SpecialDutyPay = $Row2["SpecialDutyPay"];	//특무수당
			$PositionPay = $Row2["PositionPay"];		//직책수당
			$NationalPay = $Row2["NationalPay"];		//국민연금 보수총액 (국민연금 산정기준)

			

			// 인센티브 등급을 해당월의 성과 평가 결과 등급에서 가져온다.
			$dateArr = explode("-", $PayMonth); // 년과 월을 가져온다.


			$Sql3 = "SELECT Hr_ResultLevel 
						FROM Hr_Staff_ResultEvaluation A
						Inner JOIN Hr_Evaluations B ON A.Hr_EvaluationID = B.Hr_EvaluationID 
							AND B.Hr_EvaluationState = 1 
							AND B.Hr_EvaluationYear='".$dateArr[0]."' AND B.Hr_EvaluationMonth='".$dateArr[1]."' 
						WHERE A.MemberID = ".$Row["MemberID"];

			
			$Stmt3 = $DbConn->prepare($Sql3);
			$Stmt3->execute();
			$RowCount3 = $Stmt3->rowCount();
			$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
			$Row3 = $Stmt3->fetch();

			$IncentiveGrade = isset($Row3["Hr_ResultLevel"])?$Row3["Hr_ResultLevel"]:"";    // 인센티브 등급
			
			if ($IncentiveGrade == "S") $IncentivePay = $Row2["Hr_Incentive1"];
			else if ($IncentiveGrade == "A") $IncentivePay = $Row2["Hr_Incentive2"];
			else if ($IncentiveGrade == "B") $IncentivePay = $Row2["Hr_Incentive3"];
			else if ($IncentiveGrade == "C") $IncentivePay = $Row2["Hr_Incentive4"];
			else if ($IncentiveGrade == "D") $IncentivePay = $Row2["Hr_Incentive5"];
			else $IncentivePay = 0;
			
		} else {
			$WorkType = 0;
			$BasePay = 0;
			$SpecialDutyPay = 0;
			$PositionPay = 0;
			$IncentivePay = 0;
		}	

		$SumPay = $BasePay + $SpecialDutyPay + $PositionPay + $IncentivePay; //위 4가지 합계 급여
		
		//고용보험을 확인하고 적용한다.
		if ($Row2["EmploymentInsurance"] == 1){
			$EmploymentInsurance = ($SumPay * $EmploymentRate / 100);
		} else {
			$EmploymentInsurance = 0;
		}


		//건강보험을 확인하고 적용한다.
		//장기요양보험을 확인하고 적용한다. 장기요양보험은 건강보험료에 요율을 곱해서 구한다.
		if ($Row2["HealthInsurance"] == 1){
			$HealthInsurance = floor($SumPay * $HealthRate / 100 / 10) * 10;
			$CareInsurance = floor($HealthInsurance * $CareRate / 100 /10) * 10;
		} else {
			$HealthInsurance = 0;
			$CareInsurance = 0;
		}


		//국민연금을 확인하고 적용한다.
		if ($Row2["NationalPension"] == 1){
			$NationalPension = ($NationalPay * $NationalRate / 100);
		} else {
			$NationalPension = 0;
		}

		// 혹시 해당 귀속년월에 해당 멤버의 급여 정보가 있으면 그 멤버는 넘어간다.
		$Sql3 = "SELECT COUNT(*) AS PayMemCount  FROM Pay 
					WHERE MemberID = :MemberID  
					AND PayMonth = :PayMonth";
		$Stmt3 = $DbConn->prepare($Sql3);
		$Stmt3->bindParam(':MemberID', $Row["MemberID"]);
		$Stmt3->bindParam(':PayMonth', $PayMonth);
		$Stmt3->execute();
		$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
		$Row3 = $Stmt3->fetch();
		$PayMemCount = $Row3["PayMemCount"];
		
		//지급합계와 공제합계를 계산한다.
		$TotalPay = $BasePay + $SpecialDutyPay + $PositionPay + $IncentivePay;

		//사업소득자인 경우 소득세 3.3% 를 추가한다.
		if ($WorkType == 1){
			$IncomeTax = round($TotalPay * 0.033);
		} else {
			$IncomeTax = 0;
		}
		
		//공제합계
		$SumOfDeductions = $EmploymentInsurance + $HealthInsurance + $CareInsurance + $NationalPension + $IncomeTax;

		$ActualPay = $TotalPay - $SumOfDeductions;


		//과세급여와 비과세 급여를 계산한다.

		$TaxationPay = 0;
		$TaxFreePay = 0;
		if ($TaxBasePay == 1) $TaxationPay += $BasePay;
			else $TaxFreePay +=  $BasePay;
		if ($TaxSpecialDutyPay == 1) $TaxationPay += $SpecialDutyPay;
			else $TaxFreePay +=  $SpecialDutyPay;	
		if ($TaxPositionPay == 1) $TaxationPay += $PositionPay;
			else $TaxFreePay +=  $PositionPay;		
		if ($TaxIncentivePay == 1) $TaxationPay += $IncentivePay;
			else $TaxFreePay +=  $IncentivePay;			



		if ($PayMemCount == 0){
			// 새로운 급여 정보 입력
			$Sql3 = "INSERT INTO Pay (
						MemberID,
						BasePay,
						SpecialDutyPay,
						PositionPay,
						IncentivePay,
						EmploymentInsurance,
						HealthInsurance,
						CareInsurance,
						NationalPension,
						PayMonth,
						TotalPay,
						SumOfDeductions,
						ActualPay,
						IncomeTax,
						TaxationPay,
						TaxFreePay,
						NationalPay) 
					VALUES (
						:MemberID,
						:BasePay,
						:SpecialDutyPay,
						:PositionPay,
						:IncentivePay,
						:EmploymentInsurance,
						:HealthInsurance,
						:CareInsurance,
						:NationalPension,
						:PayMonth,
						:TotalPay,
						:SumOfDeductions,
						:ActualPay,
						:IncomeTax,
						:TaxationPay,
						:TaxFreePay,
						:NationalPay
					);";
			$Stmt3 = $DbConn->prepare($Sql3);
			$Stmt3->bindParam(':MemberID', $Row["MemberID"]);
			$Stmt3->bindParam(':BasePay', $BasePay);
			$Stmt3->bindParam(':SpecialDutyPay', $SpecialDutyPay);
			$Stmt3->bindParam(':PositionPay', $PositionPay);
			$Stmt3->bindParam(':IncentivePay', $IncentivePay);
			$Stmt3->bindParam(':EmploymentInsurance', $EmploymentInsurance);
			$Stmt3->bindParam(':HealthInsurance', $HealthInsurance);
			$Stmt3->bindParam(':CareInsurance', $CareInsurance);
			$Stmt3->bindParam(':NationalPension', $NationalPension);
			$Stmt3->bindParam(':PayMonth', $PayMonth);
			$Stmt3->bindParam(':TotalPay', $TotalPay);
			$Stmt3->bindParam(':SumOfDeductions', $SumOfDeductions);
			$Stmt3->bindParam(':ActualPay', $ActualPay);
			$Stmt3->bindParam(':IncomeTax', $IncomeTax);
			$Stmt3->bindParam(':TaxationPay', $TaxationPay);
			$Stmt3->bindParam(':TaxFreePay', $TaxFreePay);
			$Stmt3->bindParam(':NationalPay', $NationalPay);
			$Stmt3->execute();
		} else {
			$Sql3 = "UPDATE Pay SET 
					BasePay = :BasePay,
					SpecialDutyPay = :SpecialDutyPay,
					PositionPay = :PositionPay,
					IncentivePay = :IncentivePay,
					EmploymentInsurance = :EmploymentInsurance,
					HealthInsurance = :HealthInsurance,
					CareInsurance = :CareInsurance,
					NationalPension = :NationalPension,
					TotalPay = :TotalPay,
					SumOfDeductions = :SumOfDeductions,
					ActualPay = :ActualPay,
					IncomeTax = :IncomeTax,
					TaxationPay = :TaxationPay,
					TaxFreePay = :TaxFreePay,
					NationalPay = :NationalPay 
					WHERE MemberID = :MemberID AND PayMonth = :PayMonth
			";
			$Stmt3 = $DbConn->prepare($Sql3);
			$Stmt3->bindParam(':MemberID', $Row["MemberID"]);
			$Stmt3->bindParam(':BasePay', $BasePay);
			$Stmt3->bindParam(':SpecialDutyPay', $SpecialDutyPay);
			$Stmt3->bindParam(':PositionPay', $PositionPay);
			$Stmt3->bindParam(':IncentivePay', $IncentivePay);
			$Stmt3->bindParam(':EmploymentInsurance', $EmploymentInsurance);
			$Stmt3->bindParam(':HealthInsurance', $HealthInsurance);
			$Stmt3->bindParam(':CareInsurance', $CareInsurance);
			$Stmt3->bindParam(':NationalPension', $NationalPension);
			$Stmt3->bindParam(':PayMonth', $PayMonth);
			$Stmt3->bindParam(':TotalPay', $TotalPay);
			$Stmt3->bindParam(':SumOfDeductions', $SumOfDeductions);
			$Stmt3->bindParam(':ActualPay', $ActualPay);
			$Stmt3->bindParam(':IncomeTax', $IncomeTax);
			$Stmt3->bindParam(':TaxationPay', $TaxationPay);
			$Stmt3->bindParam(':TaxFreePay', $TaxFreePay);			
			$Stmt3->bindParam(':NationalPay', $NationalPay);			
			$Stmt3->execute();
		}


	}

	// PayMonthState에 해당 귀속년월의 상태를 생성해 준다.
	$Sql3 = "INSERT INTO PayMonthState (
				PayMonth, PayState ) 
			VALUES (:PayMonth, 0)
			ON DUPLICATE KEY UPDATE
				PayState = 0";
	$Stmt3 = $DbConn->prepare($Sql3);
	$Stmt3->bindParam(':PayMonth', $PayMonth);
	$Stmt3->execute();


	header( "Location: pay.php?PayMonth=".$PayMonth );
}


// include and create object
include(PHPGRID_LIBPATH."inc/jqgrid_dist.php");

$db_conf = array(
                    "type"      => PHPGRID_DBTYPE,
                    "server"    => PHPGRID_DBHOST,
                    "user"      => PHPGRID_DBUSER,
                    "password"  => PHPGRID_DBPASS,
                    "database"  => PHPGRID_DBNAME
                );

$g = new jqgrid($db_conf);

$opt = array();
$opt["rowNum"] = 20; // by default 20
$opt["sortname"] = 'StaffID'; // by default sort grid by this field
$opt["sortorder"] = "desc"; // ASC or DESC
$opt["caption"] = $급여관리[$LangID]; // caption of grid
$opt["autowidth"] = true; // expand grid to screen width
$opt["cellEdit"] = true;  //엑셀형태로 바로 수정가능하게 만들어준다.

$opt["height"] = "100%";
$opt["multiselect"] = true; // allow you to multi-select through checkboxes
$opt["altRows"] = true; 
$opt["altclass"] = "myAltRowClass"; 

$opt["rowactions"] = true; // allow you to multi-select through checkboxes

// export XLS file
// export to excel parameters
$opt["export"] = array("format"=>"xls", "filename"=>$급여관리[$LangID], "sheetname"=>$급여관리[$LangID]);

$opt["cmTemplate"]["visible"] = "xs+"; // show all column on small screens
$opt["shrinkToFit"] = false; // enable horizontal scrollbar

//$opt["onAfterSave"] = "function(){jQuery('#list1').trigger('reloadGrid',[{current:true}]);}";
//$opt["edit_options"]["reloadAfterSubmit"]=false;
//$opt["reloadedit"] = true;
$opt["afterSaveCell"] = "function(){var rowId = jQuery('#list1').jqGrid('getGridParam', 'selrow');updateOneRow(rowId);}";
                                                 

$g->set_options($opt);

$e["on_update"] = array("update_client", null, true);

$g->set_events($e);

/* $PayState 단계별로 급여정보 수정이 불가능하게 하려면 아래 조건에 추가한다.
if ($PayState == 1 || $PayState == 2 || $PayState == 3 ) $editable = false;
    else $editable = true;
*/
//현재는 급여정보를 항상 수정할 수 있게 세팅	
$editable = true;

$g->set_actions(array(	
						"add"=>false, // allow/disallow add
						"edit"=>$editable, // allow/disallow edit
						"delete"=>false, // allow/disallow delete
						"rowactions"=>true, // show/hide row wise edit/del/save option
						"showhidecolumns"=>false, // show/hide row wise edit/del/save option
						"export"=>true, // show/hide export to excel option
						"autofilter" => true, // show/hide autofilter for search
						"search" => "simple" // show single/multi field search condition (e.g. simple or advance)
					) 
				);

// you can provide custom SQL query to display data
$g->select_command = "SELECT  
						A.StaffID,
						A.StaffName,
						B.FranchiseName,
						C.MemberLoginID,
						Hr_OrganPositionName,
						E.*,
						F.WorkType as WorkType1,
						F.EmploymentInsurance AS IsEmployment,
						F.HealthInsurance AS IsHealth,
						F.NationalPension AS IsNational,
						G.DepartmentName 
						from Staffs A 
						inner join Franchises B on A.FranchiseID=B.FranchiseID and A.StaffState=1
						inner join Members C on A.StaffID=C.StaffID and C.MemberLevelID=4 
						left outer join Departments G on G.DepartmentID=A.StaffManageMent 
						left outer join Hr_OrganLevelTaskMembers D on C.MemberID=D.MemberID
						left outer join PayInfo F on C.MemberID=F.MemberID 
						left outer join Pay E on C.MemberID=E.MemberID and E.PayMonth = '$PayMonth'";

// this db table will be used for add,edit,delete
$g->table = "Pay";


function update_client($data)
{
	// you can also use grid object to execute sql, useful in non-mysql driver
	// global $grid;
	// $grid->execute_query("MY-SQL");
	/*
		These comments are just to show the input param format
		$data => Array
		(
			[client_id] => 2
			[params] => Array
				(
					[client_id] => 2
					[name] => Client 2
					[gender] => male
					[company] => Client 2 Company
				)
		)
	*/
	global $TaxBasePay, $TaxSpecialDutyPay,	$TaxPositionPay,$TaxOverTimePay, $TaxReplacePay,$TaxIncentivePay,$TaxSpecial1,	$TaxSpecial2;
	global $TaxAdd1, $TaxAdd2, $TaxAdd3, $TaxAdd4, $TaxAdd5, $TaxAdd6, $TaxAdd7; 
	global $DeductionEmploymentInsurance, $DeductionHealthInsurance, $DeductionCareInsurance, $DeductionNationalPension;
	global $DeductionAdd1, $DeductionAdd2, $DeductionAdd3, $DeductionAdd4; 

	global $g;
	global $DbConn;
	global $EmploymentRate, $HealthRate, $CareRate, $NationalRate, $IncomeTax;

	//4대보험과 지급합계, 공제합계를 재계산해서 DB에 업데이트해준다.

	// 기본정보 테이블에서 해당 사람의 4대보험 가입유무 정보와 급여정보를 가져온다.
	
	$Sql2 = "SELECT A.BasePay, A.SpecialDutyPay, A.PositionPay, A.OverTimePay, A.ReplacePay, A.IncentivePay, A.Special1, A.Special2, A.Special3, A.Special4, A.IncomeTax, A.ResidenceTax, 
		    A.NationalPay, A.Add1, A.Add2, A.Add3, A.Add4, A.Add5, A.Add6, A.Add7, A.DeductionAdd1, A.DeductionAdd2, A.DeductionAdd3, A.DeductionAdd4, 
			B.EmploymentInsurance, B.HealthInsurance, B.NationalPension, B.WorkType  
			FROM Pay A
			LEFT JOIN PayInfo B 
			ON A.MemberID = B.MemberID 
			WHERE A.PayID = :PayID ";
	$Stmt2 = $DbConn->prepare($Sql2);
	$Stmt2->bindParam(':PayID', $data["PayID"]);

	$Stmt2->execute();
	$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
	$Row2 = $Stmt2->fetch();

	// $data 로 넘어온 값이 변경된 값이므로 key를 이용해 해당 값을 대체해 준다.
	foreach($data["params"] as $key => $value) {
		
		//키값이 $Row2 배열에 있으면 해당 값을 대체한다.
		if (array_key_exists($key, $Row2)) {
			$Row2[$key] = $data["params"][$key];
		}
	}

	$WorkType = $Row2["WorkType"];

	// 4대보험을 계산해서 자동으로 입력해 준다.

	$AllPay = $Row2["BasePay"] + $Row2["SpecialDutyPay"] + $Row2["PositionPay"] + $Row2["OverTimePay"] + $Row2["ReplacePay"] + $Row2["IncentivePay"] + $Row2["Special1"] + $Row2["Special2"] 
			+ $Row2["Add1"]  + $Row2["Add2"]  + $Row2["Add3"]  + $Row2["Add4"]  + $Row2["Add5"]  + $Row2["Add6"]  + $Row2["Add7"] ;				//지급합계(보수월액) -> 고용보험, 건강보험,산재보험 등의 산정 기준
	$NationalPay = $Row2["NationalPay"];	// 국민연금 산정 기준


	//고용보험을 확인하고 적용한다.
	if ($Row2["EmploymentInsurance"] == 1){
		$EmploymentInsurance = round((($AllPay * $EmploymentRate) / 100),-1);
	} else {
		$EmploymentInsurance = 0;
	}

	
	//건강보험을 확인하고 적용한다.
	if ($Row2["HealthInsurance"] == 1){
		$HealthInsurance = round((($AllPay * $HealthRate) / 100),-1);
	} else {
		$HealthInsurance = 0;
	}
	

	//장기요양보험을 확인하고 적용한다. 장기요양보험은 건강보험료에 요율을 곱해서 구한다.
	if ($Row2["HealthInsurance"] == 1){
		$CareInsurance = round((($HealthInsurance * $CareRate) / 100),-1);
	} else {
		$CareInsurance = 0;
	}

	//국민연금을 확인하고 적용한다.
	if ($Row2["NationalPension"] == 1){
		$NationalPension = round((($NationalPay * $NationalRate) / 100),-1);
	} else {
		$NationalPension = 0;
	}

	//사업자인 경우 소득세 3.3% 를 적용해 준다. 
	if ($WorkType == 1) {
		$IncomeTax = round($AllPay * 0.033);
	} else {
		$IncomeTax = $Row2["IncomeTax"];
	}

	
	// 공제급여를 계산한다. 공제항목에 지정한대로 공제 여부를 적용한다.
	$SumOfDeductions = ($DeductionEmploymentInsurance==1?$EmploymentInsurance:0) 
						+ ($DeductionHealthInsurance==1?$HealthInsurance:0) 
						+ ($DeductionCareInsurance==1?$CareInsurance:0)
						+ ($DeductionNationalPension==1?$NationalPension:0)
						+ $IncomeTax  + $Row2["ResidenceTax"];
	for ($i=1;$i<=4;$i++){
		if (${"DeductionAdd".$i} == 1) $SumOfDeductions += $Row2["DeductionAdd".$i];
	}


	$ActualPay = $AllPay - $SumOfDeductions;

	//과세급여와 비과세 급여를 계산한다.

	$TaxationPay = 0;
	$TaxFreePay = 0;
	if ($TaxBasePay == 1) $TaxationPay += $Row2["BasePay"];
		else $TaxFreePay +=  $Row2["BasePay"];
	if ($TaxSpecialDutyPay == 1) $TaxationPay += $Row2["SpecialDutyPay"];
		else $TaxFreePay +=  $Row2["SpecialDutyPay"];	
	if ($TaxPositionPay == 1) $TaxationPay += $Row2["PositionPay"];
		else $TaxFreePay +=  $Row2["PositionPay"];		
	if ($TaxReplacePay == 1) $TaxationPay += $Row2["ReplacePay"];
		else $TaxFreePay +=  $Row2["ReplacePay"];			
	if ($TaxIncentivePay == 1) $TaxationPay += $Row2["IncentivePay"];
		else $TaxFreePay +=  $Row2["IncentivePay"];				
	if ($TaxOverTimePay == 1) $TaxationPay += $Row2["OverTimePay"];
		else $TaxFreePay +=  $Row2["OverTimePay"];					
	if ($TaxSpecial1 == 1) $TaxationPay += $Row2["Special1"];
		else $TaxFreePay +=  $Row2["Special1"];					
	if ($TaxSpecial2 == 1) $TaxationPay += $Row2["Special2"];
		else $TaxFreePay +=  $Row2["Special2"];									

	for ($i=1;$i<=7;$i++){
		if (${"TaxAdd".$i} == 1) $TaxationPay += $Row2["Add".$i];
			else $TaxFreePay +=  $Row2["Add".$i];									
	}

	//해당 멤버의 4대보험 정보를 업데이트한다.

	$Sql3 = "UPDATE Pay SET 
		EmploymentInsurance = ".$EmploymentInsurance.",
		HealthInsurance = ".$HealthInsurance.",
		CareInsurance = ".$CareInsurance.",
		NationalPension = ".$NationalPension.",
		TotalPay = ".$AllPay.",
		SumOfDeductions = ".$SumOfDeductions.",
		ActualPay = ".$ActualPay.",  
		IncomeTax = ".$IncomeTax.",  
		TaxationPay = ".$TaxationPay.",
		TaxFreePay = ".$TaxFreePay."
		WHERE PayID = ".$data['PayID']." 
	";
	
	$g->execute_query($Sql3);
	
}

// you can customize your own columns ...
$col = array();
$col["title"] = "구분ID"; // caption of column
$col["name"] = "PayID"; // grid column name, must be exactly same as returned column-name from sql (tablefield or field-alias)
$col["width"] = "40";
$col["hidden"] = true;
$cols[] = $col;		

$col = array();
$col["title"] = $교사_및_직원명[$LangID];
$col["name"] = "StaffName";
$col["width"] = "58";
$col["editable"] = false; // this column is not editable
$col["align"] = "center"; 
$cols[] = $col;

$col = array();
$col["title"] = $직무[$LangID];
$col["name"] = "Hr_OrganPositionName";
$col["width"] = "58";
$col["editable"] = false; // this column is not editable
$col["align"] = "center"; 
$cols[] = $col;

$col = array();
$col["title"] = "스태프아이디";
$col["name"] = "StaffID";
$col["editable"] = false; // this column is not editable
$col["hidden"] = true;
$cols[] = $col;


$col = array();
$col["title"] = "부서";
$col["name"] = "DepartmentName";
$col["editable"] = false; // this column is not editable
$col["hidden"] = true;
$cols[] = $col;

$col = array();
$col["title"] = "주민번호1";
$col["name"] = "Jumin1";
$col["editable"] = false; // this column is not editable
$col["hidden"] = true;
$cols[] = $col;


$col = array();
$col["title"] = "근로소득/사업소득";
$col["name"] = "WorkType1";
$col["editable"] = false; // this column is not editable
$col["hidden"] = true;
$cols[] = $col;


$col = array();
$col["title"] = $프랜차이즈명[$LangID];
$col["name"] = "FranchiseName";
$col["width"] = "58";
$col["editable"] = false; // this column is not editable
$col["align"] = "center"; 
$cols[] = $col;

$col = array();
$col["title"] = $기본급[$LangID];
$col["name"] = "BasePay";
$col["width"] = "65";
$col["editable"] = true;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);

$cols[] = $col;

$col = array();
$col["title"] = $특무수당[$LangID];
$col["name"] = "SpecialDutyPay";
$col["width"] = "58";
$col["editable"] = true;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;

$col = array();
$col["title"] = $직책수당[$LangID];
$col["name"] = "PositionPay";
$col["width"] = "58";
$col["editable"] = true;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;

$col = array();
$col["title"] = $초과근무수당[$LangID];
$col["name"] = "OverTimePay";
$col["width"] = "58";
$col["editable"] = true;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;

$col = array();
$col["title"] = $대체수당[$LangID];
$col["name"] = "ReplacePay";
$col["width"] = "58";
$col["editable"] = true;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;

$col = array();
$col["title"] = $인센티브[$LangID];
$col["name"] = "IncentivePay";
$col["width"] = "58";
$col["editable"] = true;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;

$col = array();
$col["title"] = $상여금명1[$LangID];
$col["name"] = "SpecialName1";
$col["width"] = "58";
$col["editable"] = true;

$cols[] = $col;

$col = array();
$col["title"] = $상여금1[$LangID];
$col["name"] = "Special1";
$col["width"] = "58";
$col["editable"] = true;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;

$col = array();
$col["title"] = $상여금명2[$LangID];
$col["name"] = "SpecialName2";
$col["width"] = "58";
$col["editable"] = true;

$cols[] = $col;

$col = array();
$col["title"] = $상여금2[$LangID];
$col["name"] = "Special2";
$col["width"] = "58";
$col["editable"] = true;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;

for ($i=1;$i<=7;$i++){
	if (${"TaxAdd".$i."Name"} != ""){
		$col = array();
		$col["title"] = ${"TaxAdd".$i."Name"};
		$col["name"] = "Add".$i;
		$col["width"] = "58";
		$col["editable"] = true;
		$col["formatter"] = "number";
		$col["formatoptions"] = array("thousandsSeparator" => ",",
										"decimalSeparator" => ".",
										"decimalPlaces" => 0);
		$cols[] = $col;
	}
}


$col = array();
$col["title"] = $지급합계[$LangID];
$col["name"] = "TotalPay";
$col["width"] = "66";
$col["cellattr"] = "' style=\"color:red;\"' ";
$col["editable"] = false;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;


$col = array();
$col["title"] = $고용보험[$LangID];
$col["name"] = "IsEmployment";
$col["hidden"] = true;
$cols[] = $col;

$col = array();
$col["title"] = $건강보험[$LangID];
$col["name"] = "IsHealth";
$col["hidden"] = true;
$cols[] = $col;


$col = array();
$col["title"] = $국민연금[$LangID];
$col["name"] = "IsNational";
$col["hidden"] = true;
$cols[] = $col;


$col = array();
$col["title"] = $고용보험[$LangID];
$col["name"] = "EmploymentInsurance";
$col["width"] = "60";
$col["editable"] = false;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;


$col = array();
$col["title"] = $건강보험[$LangID];
$col["name"] = "HealthInsurance";
$col["width"] = "60";
$col["editable"] = false;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;

$col = array();
$col["title"] = $장기요양보험[$LangID];
$col["name"] = "CareInsurance";
$col["width"] = "60";
$col["editable"] = false;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;


$col = array();
$col["title"] = "국민연금<br>보수총액";
$col["name"] = "NationalPay";
$col["width"] = "63";
$col["editable"] = true;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;


$col = array();
$col["title"] = $국민연금[$LangID];
$col["name"] = "NationalPension";
$col["width"] = "60";
$col["editable"] = false;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;


for ($i=1;$i<=4;$i++){
	if (${"DeductionAdd".$i."Name"} != ""){
		$col = array();
		$col["title"] = ${"DeductionAdd".$i."Name"};
		$col["name"] = "DeductionAdd".$i;
		$col["width"] = "58";
		$col["editable"] = true;
		$col["formatter"] = "number";
		$col["formatoptions"] = array("thousandsSeparator" => ",",
										"decimalSeparator" => ".",
										"decimalPlaces" => 0);
		$cols[] = $col;
	}
}





$col = array();
$col["title"] = $소득세[$LangID];
$col["name"] = "IncomeTax";
$col["width"] = "58";
$col["editable"] = true;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;


$col = array();
$col["title"] = $주민세[$LangID];
$col["name"] = "ResidenceTax";
$col["width"] = "58";
$col["editable"] = true;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;


$col = array();
$col["title"] = $공제합계[$LangID];
$col["name"] = "SumOfDeductions";
$col["width"] = "66";
$col["editable"] = false;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;


$col = array();
$col["title"] = $차인지급액[$LangID];
$col["name"] = "ActualPay";
$col["width"] = "66";
$col["editable"] = false;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;

$col = array();
$col["title"] = $과세급여[$LangID];
$col["name"] = "TaxationPay";
$col["width"] = "66";
$col["editable"] = false;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;

$col = array();
$col["title"] = $비과세급여[$LangID];
$col["name"] = "TaxFreePay";
$col["width"] = "66";
$col["editable"] = false;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;

$col = array();
$col["title"] = "근로일수";
$col["name"] = "TotalWorkDay";
$col["width"] = "58";
$col["editable"] = true;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 0);
$cols[] = $col;

$col = array();
$col["title"] = "총근로시간";
$col["name"] = "TotalWorkTime";
$col["width"] = "58";
$col["editable"] = true;
$col["formatter"] = "number";
$col["formatoptions"] = array("thousandsSeparator" => ",",
                                "decimalSeparator" => ".",
                                "decimalPlaces" => 2);
$cols[] = $col;

$col = array();
$col["title"] = "급여 지급일";
$col["width"] = "90";
$col["name"] = "GivePayDate";
$col["editable"] = true; // this column is not editable
$col["hidden"] = false;
$cols[] = $col;




// pass the cooked columns to grid
$g->set_columns($cols);

$f = array();
$f["column"] = "PayID";
$f["css"] = "'background-color':'#E2E2E2', 'color':'black'"; 
$f_conditions[] = $f;

$f = array();
$f["column"] = "StaffName";
$f["css"] = "'background-color':'#E2E2E2', 'color':'black'"; 
$f_conditions[] = $f;

$f = array();
$f["column"] = "Hr_OrganPositionName";
$f["css"] = "'background-color':'#E2E2E2', 'color':'black'"; 
$f_conditions[] = $f;

$f = array();
$f["column"] = "FranchiseName";
$f["css"] = "'background-color':'#E2E2E2', 'color':'black'"; 
$f_conditions[] = $f;

$f = array();
$f["column"] = "EmploymentInsurance";
$f["css"] = "'background-color':'#E2E2E2', 'color':'black'"; 
$f_conditions[] = $f;

$f = array();
$f["column"] = "HealthInsurance";
$f["css"] = "'background-color':'#E2E2E2', 'color':'black'"; 
$f_conditions[] = $f;


$f = array();
$f["column"] = "CareInsurance";
$f["css"] = "'background-color':'#E2E2E2', 'color':'black'"; 
$f_conditions[] = $f;


$f = array();
$f["column"] = "NationalPension";
$f["css"] = "'background-color':'#E2E2E2', 'color':'black'"; 
$f_conditions[] = $f;

$f = array();
$f["column"] = "TaxationPay";
$f["css"] = "'background-color':'#E2E2E2', 'color':'black'"; 
$f_conditions[] = $f;

$f = array();
$f["column"] = "TaxFreePay";
$f["css"] = "'background-color':'#E2E2E2', 'color':'black'"; 
$f_conditions[] = $f;


$f = array();
$f["column"] = "TotalPay";
$f["css"] = "'background-color':'#FBEC88', 'color':'green'"; // must use (single quote ') with css attr and value
$f_conditions[] = $f;

$f = array();
$f["column"] = "SumOfDeductions";
$f["css"] = "'background-color':'#FBEC88', 'color':'red'"; // must use (single quote ') with css attr and value
$f_conditions[] = $f;

$f = array();
$f["column"] = "ActualPay";
$f["css"] = "'background-color':'#cef7f0', 'color':'black'"; // must use (single quote ') with css attr and value
$f_conditions[] = $f;


$g->set_conditional_css($f_conditions);


// generate grid output, with unique grid name as 'list1'
$out = $g->render("list1");

?>

<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="ko"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="ko"> <!--<![endif]-->
<head>
<?
include_once('./includes/common_meta_tag.php');
include_once('./inc_header.php');
include_once('./inc_common_list_css.php');
?>
<!-- ============== common.css ============== -->
<link rel="stylesheet" type="text/css" href="./css/common.css" />
<!-- ============== common.css ============== -->
<!-- ---------  GRID4PHP 용 스크립트와 CSS ----------------- -->
<link rel="stylesheet" type="text/css" media="screen" href="./gridphp/lib/js/themes/material/jquery-ui.custom.css"></link>
<link rel="stylesheet" type="text/css" media="screen" href="./gridphp/lib/js/jqgrid/css/ui.jqgrid.css"></link>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-migrate/3.0.1/jquery-migrate.min.js"></script>
<script src="./gridphp/lib/js/jqgrid/js/i18n/grid.locale-kr.js" type="text/javascript"></script>
<script src="./gridphp/lib/js/jqgrid/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script src="./gridphp/lib/js/themes/jquery-ui.custom.min.js" type="text/javascript"></script>

<!-- ---------  GRID4PHP 용 스크립트와 CSS ----------------- -->

</head>

<body class="disable_transitions sidebar_main_open sidebar_main_swipe">
<?php
$MainMenuID = 77;
$SubMenuID = 7702;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
include('./inc_departments.php');
$departments = getDepartments($LangID);

?>


<div id="page_content">
	<div id="page_content_inner">
	<div class="md-card" style="min-height:150px;padding:25px;margin:15px;position:relative;min-width:250px">
		<div style="width:40%;float:left;left:10px">
			<form name="SearchForm" method="get">
				<input type="hidden" name="PayInputMode" value="false">
				<input type="hidden" name="RecalcurationMode" value="false">
				<h3 class="heading_b uk-margin-bottom"><?=$급여관리[$LangID]?></h3>
				<h3 class="heading_b uk-margin-bottom"><?=$귀속년월[$LangID]?> : 
					<select id="PayMonth" name="PayMonth" class="uk-width-2-4" style="min-width:159px" onchange="SearchSubmit(false)" data-md-select2 data-allow-clear="true" data-placeholder="귀속년월 선택"/>
						<option value=""><?=$귀속년월선택[$LangID]?></option>
						<?
						for ($yearCount=2021; $yearCount<=((int)date("Y")); $yearCount++) {
							if ($yearCount == date("Y")) $maxMonth = date("m");
								else $maxMonth =12;
							for ($monthCount=1; $monthCount<=$maxMonth; $monthCount++) {
								$optMonth = $yearCount."-".sprintf('%02d',$monthCount)."-01";
						?>
							<option value="<?=$yearCount?>-<?=sprintf('%02d',$monthCount)?>-01" <?if ($PayMonth==$optMonth){?>selected<?}?>><?=$yearCount?> 년 <?=$monthCount?> 월</option>
						<?
							}
						}
						?>
					</select>
				</h3>
				<p>
					 4대보험요율 : 고용보험 <?=$EmploymentRate?>%, 건강보험 <?=$HealthRate?>%, 장기요양보험 <?=$CareRate?>%, 국민연금 <?=$NationalRate?>% 

				</p>
				<p>
					* 아래 표에서 하얀색 칸만 수정 가능하며, 색상이 있는 칸들은 자동계산되는 칸입니다.
				</p>
				
			</form>
		</div>

		<?
			$Feedback = array();
			$MemberName = array();
			$DocumentReportMemberID = array();
			$DocumentPermited = false;    // 품의서를 승인한 사람이 있는지 체크해서 있으면 true를 넣어준다.
		?>
		<div style="float:right;width:30%;margin-right:5px;min-width:230px">
		<form name="approvalForm" method="get">
		<input type="hidden" name="PayMonthStateID" value="<?=$PayMonthStateID?>">
		<input type="hidden" name="PayMonth" value="<?=$PayMonth?>">
		<table class="draft_approval" style="width:100%">
			<col width="">
			<colgroup span="4" width="22.5%"></colgroup>
			
			<tr style="height:60px;">
				<th rowspan="2">결<br><br>재</th>
			<? for ($tdCount=1;$tdCount<4;$tdCount++) { ?>

				<td>
					<?
					${"StrDocumentReportMemberState".$tdCount} = "-";
					if ($PayState==1 || $PayState==2 || $PayState==3) {
						
						$Sql3 = "SELECT A.*, B.MemberName from PayApprovalMembers A 
									inner join Members B on A.MemberID=B.MemberID 
									where A.PayMonthStateID = $PayMonthStateID 
									and A.ApprovalMemberOrder = $tdCount";
						$Stmt3 = $DbConn->prepare($Sql3);
						$Stmt3->execute();
						$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
						$Row3 = $Stmt3->fetch();
						$Stmt3 = null;
						$MemberName[$tdCount] = $Row3["MemberName"];
						$Feedback[$tdCount] = $Row3["Feedback"];
						$ApprovalState = $Row3["ApprovalState"];
						$ApprovalModiDateTime = substr($Row3["ApprovalModiDateTime"],0,10);
						if ($ApprovalState==0){
							${"StrDocumentReportMemberState".$tdCount} = "-";
						}else if ($ApprovalState==1){
							$DocumentPermited = true;
							${"StrDocumentReportMemberState".$tdCount} = $ApprovalModiDateTime . "<br>승인";
						}else if ($ApprovalState==2){
							${"StrDocumentReportMemberState".$tdCount} = $ApprovalModiDateTime . "<br>반려";
						}
						echo ("<input type='hidden' id='DocumentReportMemberID".$tdCount."' name='DocumentReportMemberID".$tdCount."' value='".$Row3["MemberID"]."'>");
						echo ($MemberName[$tdCount]); 
						
						} else {

						?>
							<select id="category<?=$tdCount?>" onchange="javascript:categoryChange(this,'DocumentReportMemberID<?=$tdCount?>',0)">
								<option>부서 선택</option>
						<?		
								foreach($departments as $key => $value){
									echo "<option value='{$key}'>{$value}</option>";
								}
						?>
							</select>
							<select id="DocumentReportMemberID<?=$tdCount?>" name="DocumentReportMemberID<?=$tdCount?>">
								<option>직원 선택</option>
							</select>
							<?
								if ($DocumentReportState==2){
									$Sql3 = "SELECT A.MemberID, C.StaffManagement from DocumentReportMembers A 
												LEFT JOIN Members B ON A.MemberID = B.MemberID
												LEFT JOIN Staffs C ON B.StaffID = C.StaffID
												where A.DocumentReportID=$DocumentReportID and DocumentReportMemberOrder= $tdCount ";
									$Stmt3 = $DbConn->prepare($Sql3);
									$Stmt3->execute();
									$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
									$Row3 = $Stmt3->fetch();
									$Stmt3 = null;
									array_push($DocumentReportMemberID,[$Row3["MemberID"] => $Row3["StaffManagement"]]);
								}else{
									array_push($DocumentReportMemberID,[0 => NULL]);
								}
								?>
							<?
							 }
							?>
						</td>
						<? } ?>
						<td>
						<?
					$StrDocumentReportMemberState4 = "-";
				if ($PayState==1 || $PayState==2 || $PayState==3) {
					$Sql3 = "SELECT A.*, B.MemberName from PayApprovalMembers A 
									inner join Members B on A.MemberID=B.MemberID 
									where A.PayMonthStateID = $PayMonthStateID 
									and A.ApprovalMemberOrder = 4";
					$Stmt3 = $DbConn->prepare($Sql3);
					$Stmt3->execute();
					$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
					$Row3 = $Stmt3->fetch();
					$Stmt3 = null;
					$MemberName[$tdCount] = $Row3["MemberName"];
					$Feedback[$tdCount] = $Row3["Feedback"];
					$ApprovalState = $Row3["ApprovalState"];
					$ApprovalModiDateTime = substr($Row3["ApprovalModiDateTime"],0,10);
					if ($ApprovalState==0){
						$StrDocumentReportMemberState4 = "-";
					}else if ($ApprovalState==1){
						$DocumentPermited = true;
						$StrDocumentReportMemberState4 = $ApprovalModiDateTime . "<br>승인";
					}else if ($ApprovalState==2){
						$StrDocumentReportMemberState4 = $ApprovalModiDateTime . "<br>반려";
					}
					echo ("<input type='hidden' id='DocumentReportMemberID4' name='DocumentReportMemberID4' value='22050'>");
					echo ($MemberName[4]); 
				}else{
					?>
					<select id="DocumentReportMemberID4" name="DocumentReportMemberID4">
						<option value="22050">정우영</option>
					</select>
					<?
				}
				?>
				</td>
				</tr>
				<tr>
					<td><?=$StrDocumentReportMemberState1?></td>
					<td><?=$StrDocumentReportMemberState2?></td>
					<td><?=$StrDocumentReportMemberState3?></td>
					<td><?=$StrDocumentReportMemberState4?></td>
				</tr>
			</table>
			<?php 
				for ($i=1; $i<=4; $i++) {
					if (strstr(${"StrDocumentReportMemberState".$i},'반려')){
					$j = $i - 1;
				?>			
				<style>
				.box {
				width: 250px;
				min-height: 50px;
				border: 1px solid gray;
				display: block;
				background-color:bisque;
				/*box-shadow: 5px 5px 20px;*/
				margin: auto;
				margin-bottom: 10px;
				transition: all 0.5s;
				transition-delay: 0.4s;
				padding: 10px;
				}
				.box:hover {
				width: 255px;
				min-height: 55px;
				}
			</style>
			<div>
					<div class="box">
						<h6 style="text-align:center;color:darkslategrey"><?=$MemberName[$j]?> 님의 반려 사유</h6>
						<?=$Feedback[$j]?>
					</div>
			</div>
			
			
			
			<?			
					}
				}
			?>
			</form>
			</div>
		
		<? include('./inc_category_change.php');?>
		
		
		<? if ($PayState == 0)	{ ?>
		<div style="text-align:center;width:100%;display:inline-block;margin-top:10px">
			<!--
			<a type="button" onclick="SearchSubmit(true)" class="md-btn md-btn-primary" style="margin-top:10px"><?=$급여정보재생성[$LangID]?></a> 
			<a type="button" onclick="RecalcurationSubmit()" class="md-btn md-btn-primary" style="margin-top:10px"><?=$사대보험재계산[$LangID]?></a> 
			-->
			<a type="button" href="javascript:OpenSpecialForm()" class="md-btn md-btn-primary" style="margin-top:10px"><?=$상여금일괄등록[$LangID]?></a> 
			<a type="button" href="javascript:ApprovalSubmit()" class="md-btn md-btn-primary" style="margin-top:10px"><?=$결재요청[$LangID]?></a>
		<?} else if ($PayState == 1)	{ ?>
		<div class="uk-form-row" style="text-align:center;">
			<h3><?=$현재상태결재요청[$LangID]?></h3>
		<?} else if ($PayState == 2)	{ ?>
		<div class="uk-form-row" style="text-align:center;">
			<h3><?=$현재상태결재완료[$LangID]?></h3>
			<a type="button" href="javascript:CompleteSubmit()" class="md-btn md-btn-primary"  style="margin-top:10px"><?=$지급완료처리[$LangID]?></a>
		<?} else if ($PayState == 3)	{ ?>
		<div class="uk-form-row" style="text-align:center;">
			<h3><?=$현재상태지급완료[$LangID]?></h3>
		<?} ?>
		<? if ($PayState != 99) {?>	
			
			<a type="button" href="javascript:PayStub()" class="md-btn md-btn-primary"  style="margin-top:10px">급여명세서</a>
			<a type="button" href="javascript:EmailSubmit()" class="md-btn md-btn-primary"  style="margin-top:10px"><?=$급여명세표일괄이메일발송[$LangID]?></a>
			<a type="button" href="javascript:InitSubmit()" class="md-btn md-btn-warning"  style="margin-top:10px">초기화</a>
		<? } ?>	
		</div>
	</div>					
		<? if ($PayState != 99)	{ ?>				
			<div style="margin:10px">

				<!-- display grid here -->
				<?php echo $out?>
				<!-- display grid here -->

			</div>
		<? } else if ($PayState == 99) {?>

		<div class="uk-form-row" style="text-align:center;">
			<a type="button" onclick="SearchSubmit(true)" class="md-btn md-btn-primary"><?=$급여정보생성[$LangID]?></a>
		</div>

		<div style="margin:10px">
			<br><br><br>
			<center>
			<h4 class="heading_b uk-margin-bottom">
			급여 정보가 생성되어 있지 않았습니다.<br>
			먼저 급여 정보 생성을 하고 급여 정보를 입력해 주세요.
			</h4>
		</div>
		<? } ?>
	</div>
</div>		
<!-- common functions grid4php 와 충돌을 막기 위해 jquery를 제거 -->
<script src="assets/js/common_no_jquery.js"></script>
<!-- uikit functions -->
<script src="assets/js/uikit_custom.js"></script>
<!-- altair common functions/helpers -->
<script src="assets/js/altair_admin_common.js"></script>
<!-- select2 -->
<script src="bower_components/select2/dist/js/select2.min.js"></script>

<!-- ============== common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ============== common.js ============== -->
<script>
var ids;
<?php 
//이메일 발송하고 난 후라면 성공했다는 메시지를 띄워준다.
if ($IsMailSend == 1){
?>
	alert('급여명세서를 이메일로 발송했습니다!');
<?
}
?>

function OpenSpecialForm(){
	openurl = "special_form.php";
	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"70%"
		,maxWidth: "850"
		,maxHeight: "550"
		,title:""
		,iframe:true 
		,scrolling:true
		,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}

function CompleteSubmit(){
	document.approvalForm.action = "pay_complete.php";
	document.approvalForm.submit();
}

// 이메일을 발송한다.
function EmailSubmit(){
	if (confirm("이메일을 보내겠습니까?")) {
        document.approvalForm.action = "pay_email.php";
		document.approvalForm.submit();
    } else {
        return;
    }
}	

function InitSubmit(){
	if (confirm("정말로 초기화하시겠습니까?")) {
		document.approvalForm.action = "pay_init.php";
		document.approvalForm.submit();
	} 
}

//급여명세서를 보여주는 창을 열어준다. 
function PayStub(selectedRows){

	// 먼저 현재 선택되어 있는 데이터를 가져온다.
	//ids = $('#list1').jqGrid('getDataIDs');



	myWindow = window.open("pay_stub.php?selectedRows="+selectedRows, "PayStub", "scrollbars=yes,resizable=yes,top=150,left=300,width=780,height=700");
	myWindow.focus();
}


function ApprovalSubmit(){
	document.approvalForm.action = "pay_action.php";
	document.approvalForm.submit();
}

function SearchSubmit(PayInputMode){
	document.SearchForm.PayInputMode.value = PayInputMode;
	document.SearchForm.action = "pay.php";
	document.SearchForm.submit();
}

//4대보험 재계산 모드
function RecalcurationSubmit(){
	document.SearchForm.RecalcurationMode.value = 'true';
	document.SearchForm.action = "pay.php";
	document.SearchForm.submit();
}

// 한 줄의 데이타를 재계산해서 입력해 준다. (화면상에만 나오고 실제 db에 반영되는 데이타는 update 에서 진행해야 한다.)
function updateOneRow(rowId){
	//4대보험 요율을 가져온다.
	var employmentRate = <?=$EmploymentRate?>;
	var healthRate = <?=$HealthRate?>;
	var careRate = <?=$CareRate?>;
	var nationalRate = <?=$NationalRate?>;
	var TaxBasePay =<?=$TaxBasePay?>;
	var TaxSpecialDutyPay =<?=$TaxSpecialDutyPay?>;
	var TaxPositionPay =<?=$TaxPositionPay?>;
	var TaxOverTimePay =<?=$TaxOverTimePay?>;
	var TaxReplacePay =<?=$TaxReplacePay?>;
	var TaxIncentivePay =<?=$TaxIncentivePay?>;
	var TaxSpecial1 =<?=$TaxSpecial1?>;
	var TaxSpecial2 =<?=$TaxSpecial2?>;
	
	// 선택된 줄의 데이타를 모두 가져온다.
	var obj = jQuery('#list1').getRowData(rowId);

	// 먼저 지급합계를 계산한다.
	var totalPay = parseInt(obj.BasePay) + parseInt(obj.SpecialDutyPay) + parseInt(obj.PositionPay)  + parseInt(obj.OverTimePay)  + parseInt(obj.ReplacePay) + parseInt(obj.IncentivePay) + parseInt(obj.Special1) + parseInt(obj.Special2);
	
	//추가 항목이 있으면 추가로 totalPay에 더해 준다.
<?php 
	for ($i=1;$i<=7;$i++){
		if (${"TaxAdd".$i."Name"} != "") {	
?>	
			totalPay += parseInt(obj.Add<?=$i?>);
<?php 
		}
	}
?>			
	var WorkType = parseInt(obj.WorkType1);
	var NationalPay = parseInt(obj.NationalPay);
	
	// 4대 보험을 재계산해서 적용한다. (10단위로 반올림한다.)
	if (obj.IsEmployment == '1') {
		var employmentInsurance = Math.round((totalPay * employmentRate) / 1000 ) * 10;
	} else {
		var employmentInsurance = 0;
	}
	if (obj.IsHealth == '1') {
		var healthInsurance = Math.round((totalPay * healthRate) / 1000 ) * 10;
		var careInsurance = Math.round((healthInsurance * careRate) / 1000 ) * 10;
	} else {
		var healthInsurance = 0;
		var careInsurance = 0;
	}	
	
	if (obj.IsNational == '1') {
		var nationalPension = Math.round((NationalPay * nationalRate) / 1000 ) * 10;
	} else {
		var nationalPension = 0;
	}	

	obj.EmploymentInsurance = employmentInsurance;
	obj.HealthInsurance = healthInsurance;
	obj.CareInsurance = careInsurance;
	obj.NationalPension = nationalPension;
	

	// 지급합계와 공제합계를 재계산해서 적용한다.
	obj.TotalPay = totalPay;
	// 만약 사업자일 경우 소득세 3.3% 로 추가한다.
	var IncomeTax = 0;
	if (WorkType==1){
		IncomeTax = Math.round(totalPay * 0.033);
	} else {
		IncomeTax = parseInt(obj.IncomeTax);
	}
	obj.IncomeTax = IncomeTax;
	obj.SumOfDeductions = <?=$DeductionEmploymentInsurance==1?"employmentInsurance + ":""?> <?=$DeductionHealthInsurance==1?"healthInsurance + ":""?> <?=$DeductionCareInsurance==1?"careInsurance + ":""?> <?=$DeductionNationalPension==1?"nationalPension + ":""?> IncomeTax + parseInt(obj.ResidenceTax);

	//alert(obj.SumOfDeductions);
<?php 
	for ($i=1;$i<=4;$i++){
		if (${"DeductionAdd".$i."Name"} != "") {	
?>	
			var DeductionAdd<?=$i?> = <?=${"DeductionAdd".$i}?>;
			if (DeductionAdd<?=$i?> == 1) obj.SumOfDeductions += parseInt(obj.DeductionAdd<?=$i?>);
			
<?php 
		}
	}
?>	
	
 	//차인지급액
	obj.ActualPay = totalPay - obj.SumOfDeductions;

	// 과세급여와 비과세급여를 계산한다.
	obj.TaxationPay = 0;
	obj.TaxFreePay = 0;

	if (TaxBasePay == 1) obj.TaxationPay += parseInt(obj.BasePay);
		else obj.TaxFreePay += parseInt(obj.BasePay);
	if (TaxSpecialDutyPay == 1) obj.TaxationPay += parseInt(obj.SpecialDutyPay);
		else obj.TaxFreePay += parseInt(obj.SpecialDutyPay); 
	if (TaxPositionPay == 1) obj.TaxationPay += parseInt(obj.PositionPay);
		else obj.TaxFreePay += parseInt(obj.PositionPay);
	if (TaxOverTimePay == 1) obj.TaxationPay += parseInt(obj.OverTimePay);
		else obj.TaxFreePay += parseInt(obj.OverTimePay); 
	if (TaxReplacePay == 1) obj.TaxationPay += parseInt(obj.ReplacePay);
		else obj.TaxFreePay += parseInt(obj.ReplacePay); 
	if (TaxIncentivePay == 1) obj.TaxationPay += parseInt(obj.IncentivePay);
		else obj.TaxFreePay += parseInt(obj.IncentivePay); 
	if (TaxSpecial1 == 1) obj.TaxationPay += parseInt(obj.Special1);
		else obj.TaxFreePay += parseInt(obj.Special1); 
	if (TaxSpecial2 == 1) obj.TaxationPay += parseInt(obj.Special2);
		else obj.TaxFreePay += parseInt(obj.Special2);
<?php 
	for ($i=1;$i<=7;$i++){
		if (${"TaxAdd".$i."Name"} != "") {	
?>	
			var TaxAdd<?=$i?> = <?=${"TaxAdd".$i}?>;
			if (TaxAdd<?=$i?> == 1) obj.TaxationPay += parseInt(obj.Add<?=$i?>);
				else obj.TaxFreePay += parseInt(obj.Add<?=$i?>);	
<?php 
		}
	}
?>		
	
	jQuery('#list1').setRowData(rowId,obj);
	//alert(totalPay);

	

}


</script>



<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>





</body>
</html>