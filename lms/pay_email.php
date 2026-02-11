<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

function mailCheck($_str)
{
    if (preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $_str) == false)
    {
        return false;
     }
    else
    {
        return true;
     }
}

$err_num = 0;
$err_msg = "";

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$ListParam = str_replace("^^", "&", $ListParam);

$PayMonth = isset($_REQUEST["PayMonth"]) ? $_REQUEST["PayMonth"] : "";

$YearMonth = explode("-",$PayMonth);
$SumOfPay = 0;


// 이메일 발송하기
	$Sql = "SELECT  
				A.StaffID,
				A.StaffName,
				C.MemberLoginID,
				C.MemberName,
				AES_DECRYPT(UNHEX(MemberEmail),:EncryptionKey) AS Email,
				Hr_OrganPositionName,
				E.*,
				F.DepartmentName,
				G.Add1Name, G.Add2Name, G.Add3Name, G.Add4Name, G.Add5Name, G.Add6Name, G.Add7Name,
				H.Add1Name as Deduction1Name, H.Add2Name as Deduction2Name, H.Add3Name as Deduction3Name, H.Add4Name  as Deduction4Name
				from Staffs A 
				inner join Members C on A.StaffID=C.StaffID and C.MemberLevelID=4 
				left outer join Hr_OrganLevelTaskMembers D on C.MemberID=D.MemberID 
				left outer join Departments F on A.StaffManageMent=F.DepartmentID
				left outer join Pay E on C.MemberID=E.MemberID and E.PayMonth = '".$PayMonth."'
				left outer join PayTaxInfo G on G.PayMonth = '".$YearMonth[0].$YearMonth[1]."'
				left outer join PayDeductionInfo H on H.PayMonth = '".$YearMonth[0].$YearMonth[1]."'
				WHERE C.MemberState = 1 ";
	
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	//echo $Sql;
	
	$i=0;
	//스태프 숫자만큼 메일을 발송
	while($Row = $Stmt->fetch()){
		$MemberName = $Row["MemberName"];
		$MemberEmail = $Row["Email"];
		$MemberLoginID = $Row["MemberLoginID"];
		$Hr_OrganPositionName = $Row["Hr_OrganPositionName"];

		$BasePay = isset($Row["BasePay"])?$Row["BasePay"]:0;
		$SpecialDutyPay = isset($Row["SpecialDutyPay"])?$Row["SpecialDutyPay"]:0;
		$PositionPay = isset($Row["PositionPay"])?$Row["PositionPay"]:0;
		$OverTimePay = isset($Row["OverTimePay"])?$Row["OverTimePay"]:0;
		$ReplacePay = isset($Row["ReplacePay"])?$Row["ReplacePay"]:0;
		$IncentivePay = isset($Row["IncentivePay"])?$Row["IncentivePay"]:0;
		$Special1 = isset($Row["Special1"])?$Row["Special1"]:0;
		$Special2 = isset($Row["Special2"])?$Row["Special2"]:0;
		$SpecialName1 = isset($Row["SpecialName1"])?$Row["SpecialName1"]:"";
		$SpecialName2 = isset($Row["SpecialName2"])?$Row["SpecialName2"]:"";
		$EmploymentInsurance = isset($Row["EmploymentInsurance"])?$Row["EmploymentInsurance"]:"";
		$HealthInsurance = isset($Row["HealthInsurance"])?$Row["HealthInsurance"]:"";
		$CareInsurance = isset($Row["CareInsurance"])?$Row["CareInsurance"]:"";
		$NationalPension = isset($Row["NationalPension"])?$Row["NationalPension"]:"";
		$IncomeTax = isset($Row["IncomeTax"])?$Row["IncomeTax"]:"";
		$ResidenceTax = isset($Row["ResidenceTax"])?$Row["ResidenceTax"]:"";
		$TotalPay = isset($Row["TotalPay"])?$Row["TotalPay"]:"";
		$SumOfDeductions = isset($Row["SumOfDeductions"])?$Row["SumOfDeductions"]:"";
		$ActualPay = isset($Row["ActualPay"])?$Row["ActualPay"]:"";
		$TaxationPay = isset($Row["TaxationPay"])?$Row["TaxationPay"]:"";
		$TaxFreePay = isset($Row["TaxFreePay"])?$Row["TaxFreePay"]:"";
		$TotalWorkDay = isset($Row["TotalWorkDay"])?$Row["TotalWorkDay"]:"";
		$TotalWorkTime = isset($Row["TotalWorkTime"])?$Row["TotalWorkTime"]:"";
		$GivePayDate = isset($Row["GivePayDate"])?$Row["GivePayDate"]:"";
		$StaffID = isset($Row["StaffID"])?$Row["StaffID"]:"";
		$DepartmentName = isset($Row["DepartmentName"])?$Row["DepartmentName"]:"";
		$Add1 = isset($Row["Add1"])?number_format($Row["Add1"]):"";
		$Add2 = isset($Row["Add2"])?number_format($Row["Add2"]):"";
		$Add3 = isset($Row["Add3"])?number_format($Row["Add3"]):"";
		$Add4 = isset($Row["Add4"])?number_format($Row["Add4"]):"";
		$Add5 = isset($Row["Add5"])?number_format($Row["Add5"]):"";
		$Add6 = isset($Row["Add6"])?number_format($Row["Add6"]):"";
		$Add7 = isset($Row["Add7"])?number_format($Row["Add7"]):"";
		$Add1Name = isset($Row["Add1Name"])?$Row["Add1Name"]:"";
		$Add2Name = isset($Row["Add2Name"])?$Row["Add2Name"]:"";
		$Add3Name = isset($Row["Add3Name"])?$Row["Add3Name"]:"";
		$Add4Name = isset($Row["Add4Name"])?$Row["Add4Name"]:"";
		$Add5Name = isset($Row["Add5Name"])?$Row["Add5Name"]:"";
		$Add6Name = isset($Row["Add6Name"])?$Row["Add6Name"]:"";
		$Add7Name = isset($Row["Add7Name"])?$Row["Add7Name"]:"";
		$DeductionAdd1 = isset($Row["DeductionAdd1"])?number_format($Row["DeductionAdd1"]):"";
		$DeductionAdd2 = isset($Row["DeductionAdd2"])?number_format($Row["DeductionAdd2"]):"";
		$DeductionAdd3 = isset($Row["DeductionAdd3"])?number_format($Row["DeductionAdd3"]):"";
		$DeductionAdd4 = isset($Row["DeductionAdd4"])?number_format($Row["DeductionAdd4"]):"";
		$Deduction1Name = isset($Row["Deduction1Name"])?$Row["Deduction1Name"]:"";
		$Deduction2Name = isset($Row["Deduction2Name"])?$Row["Deduction2Name"]:"";
		$Deduction3Name = isset($Row["Deduction3Name"])?$Row["Deduction3Name"]:"";
		$Deduction4Name = isset($Row["Deduction4Name"])?$Row["Deduction4Name"]:"";

		


		$MailHTML = "<style>
		* {
			box-sizing: border-box;
			-moz-box-sizing: border-box;
			-webkit-box-sizing: border-box;
			font-size:10pt;
		}
		.ContentPopup{padding:30px 30px; }
		table{
			width: 730px;
			border: 1px solid black;
		}
		body{
			margin:0;
			padding:0;
		  background:#fff;
		  }
		  p{
		  margin:0;
			padding:0;
		}
		.emailtd{
			border: 1px solid #444444;
			text-align:center;
		}	
		</style>
		<table style='border-collapse: collapse; border: none;width: 700px; ' border='1' cellspacing='0' cellpadding='0'>
		<tbody>
		<tr>
		  <td rowspan='5' style='width: 400px; height: 53px; border-left: none; border-right: solid #000000 0.4pt; border-top: none; border-bottom: none; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' colspan=2 valign='middle'>
			<h2 style='text-align: center;'><span style='font-size: 18.0pt;'>급 여 명 세 서</span></h2>
		  </td>
		  <td style='width: 70px; height: 26px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'>
			<p style='text-align: center;'>사원번호</p>
		  </td>
		  <td style='width: 151px; height: 26px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'>
			<p style='text-align: center;'> $StaffID </p>
		  </td>
		</tr>
		<tr>
		  <td style='width: 70px; height: 26px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'>
			<p style='text-align: center;'>부&nbsp; 서</p>
		  </td>
		  <td style='width: 151px; height: 26px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'>
			<p style='text-align: center;'> $DepartmentName </p>
		  </td>
		</tr>
		<tr>
		  <td style='width: 70px; height: 26px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'>
			<p style='text-align: center;'>직&nbsp; 무</p>
		  </td>
		  <td style='width: 151px; height: 26px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'>
			<p style='text-align: center;'> $Hr_OrganPositionName </p>
		  </td>
		</tr>
		<tr>
		  <td style='width: 70px; height: 26px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'>
			<p style='text-align: center;'>성&nbsp; 명</p>
		  </td>
		  <td style='width: 151px; height: 26px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'>
			<p style='text-align: center;'> $MemberName </p>
		  </td>
		</tr>
		
		</tbody>
		</table>
		<p>&nbsp;</p>
		<p><span style='font-weight: bold;'>1. 지급액</span>&nbsp;</p>
		<table style='border-collapse: collapse; border: none black; width: 700px; ' border='1' cellspacing='0' cellpadding='0'>
		<tbody>
		<tr style='height: 26px; background-color: #e0ffff;'>
		  <td style='width:25%;text-align: center; height: 26px;'>&nbsp;과세급여</td>
		  <td style='width:25%;text-align: center; height: 26px;'>비과세급여&nbsp;</td>
		  <td style='width:25%;text-align: center; height: 26px;'>지급합계&nbsp;</td>
		  <td style='width:25%;text-align: center; height: 26px;'>공제합계&nbsp;</td>
		</tr>
		<tr style='height: 26px;'>
		  <td  style='text-align: center;'>".number_format($TaxationPay)."</td>
		  <td  style='text-align: center;'>".number_format($TaxFreePay)."</td>
		  <td  style='text-align: center;'>".number_format($TotalPay)."</td>
		  <td  style='text-align: center;'>".number_format($SumOfDeductions)."</td>
		</tr>
		<tr style='height: 26px;'>
		  <td style='width:20%;text-align: center; height: 26px; background-color: #e0ffff;'>차감 지급액&nbsp;</td>
		  <td style='text-align: center;'>".number_format($ActualPay)."</td>
		  <td style='width:20%;text-align: center; height: 26px; background-color: #e0ffff;'>지급일&nbsp;</td>
		  <td style='text-align: center;'>".$GivePayDate."</td>
		</tr>
		</tbody>
		</table>
		<p>&nbsp;</p>
		<p><span style='font-weight: bold;'>2. 지급 내역</span>&nbsp;</p>
		<table style='width:700px;border-collapse: collapse; border: none;' border='1' cellspacing='0' cellpadding='0'>
		<tbody>
		<tr style='background-color: #e0ffff;'>
		<td style='width: 50%; height: 15px; border-left: solid #000000 1.1pt; border-right: solid #000000 0.4pt; border-top: solid #000000 1.1pt; border-bottom: solid #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' colspan='2' valign='middle'>
		<p style='text-align: center;'>지급항목</p>
		</td>
		<td style='width: 50%; height: 15px; border-left: solid #000000 0.4pt; border-right: solid #000000 1.1pt; border-top: solid #000000 1.1pt; border-bottom: solid #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' colspan='2' valign='middle'>
		<p style='text-align: center;'>공제항목</p>
		</td>
		</tr>
		<tr>
		<td style='width: 25%; height: 15px; border-left: solid #000000 1.1pt; border-right: solid #000000 0.4pt; border-top: solid #000000 0.4pt; border-bottom: solid #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'>
		<p style='text-align: center;'>기본급여</p>
		</td>
		<td style='width: 25%; height: 15px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'>
		<p style='text-align: center;'>".number_format($BasePay)."</p>
		</td>
		<td style='width: 25%; height: 15px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'>
		<p style='text-align: center;'>소 득 세</p>
		</td>
		<td style='width: 25%; height: 15px; border-left: solid #000000 0.4pt; border-right: solid #000000 1.1pt; border-top: solid #000000 0.4pt; border-bottom: solid #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'>
		<p style='text-align: center;'>".number_format($IncomeTax)."</p>
		</td>
		</tr>
		<tr>
		<td style=' height: 15px; border-left: solid #000000 1.1pt; border-right: solid #000000 0.4pt; border-top: solid #000000 0.4pt; border-bottom: solid #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'>
		<p style='text-align: center;'>직책수당</p>
		</td>
		<td style=' height: 15px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'>
		<p style='text-align: center;'>".number_format($PositionPay)."</p>
		</td>
		<td style=' height: 15px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'>
		<p style='text-align: center;'>주 민 세</p>
		</td>
		<td style=' height: 15px; border-left: solid #000000 0.4pt; border-right: solid #000000 1.1pt; border-top: solid #000000 0.4pt; border-bottom: solid #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'>
		<p style='text-align: center;'>".number_format($ResidenceTax)."</p>
		</td>
		</tr>
		<tr>
		<td style=' height: 15px; border-left: solid #000000 1.1pt; border-right: solid #000000 0.4pt; border-top: solid #000000 0.4pt; border-bottom: solid #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'>
		<p style='text-align: center;'>특무수당</p>
		</td>
		<td style=' height: 15px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'>
		<p style='text-align: center;'>".number_format($SpecialDutyPay)."</p>
		</td>
		<td style=' height: 15px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'>
		<p style='text-align: center;'>고용보험</p>
		</td>
		<td style=' height: 15px; border-left: solid #000000 0.4pt; border-right: solid #000000 1.1pt; border-top: solid #000000 0.4pt; border-bottom: solid #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'>
		<p style='text-align: center;'>".number_format($EmploymentInsurance)."</p>
		</td>
		</tr>
		<tr>
		<td style=' height: 15px; border-width: 0.4pt 0.4pt 0.4pt 1.1pt; border-style: solid; border-color: #000000; padding: 1.4pt 2.0pt; text-align: center;' valign='middle'>오버타임수당</td>
		<td style=' height: 15px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'>
		<p style='text-align: center;'>".number_format($OverTimePay)."</p>
		</td>
		<td style=' height: 15px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'>
		<p style='text-align: center;'>건강보험</p>
		</td>
		<td style=' height: 15px; border-left: solid #000000 0.4pt; border-right: solid #000000 1.1pt; border-top: solid #000000 0.4pt; border-bottom: solid #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'>
		<p style='text-align: center;'>".number_format($HealthInsurance)."</p>
		</td>
		</tr>
		<tr>
		<td style=' height: 15px; border-left: solid #000000 1.1pt; border-right: solid #000000 0.4pt; border-top: solid #000000 0.4pt; border-bottom: solid #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'>
		<p style='text-align: center;'>&nbsp;대체수당</p>
		</td>
		<td style=' height: 15px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'>
		<p style='text-align: center;'>".number_format($ReplacePay)."</p>
		</td>
		<td style=' height: 15px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'>
		<p style='text-align: center;'>장기요양보험</p>
		</td>
		<td style=' height: 15px; border-left: solid #000000 0.4pt; border-right: solid #000000 1.1pt; border-top: solid #000000 0.4pt; border-bottom: solid #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'>
		<p style='text-align: center;'>".number_format($CareInsurance)."</p>
		</td>
		</tr>
		<tr>
		<td style=' height: 15px; border-left: solid #000000 1.1pt; border-right: solid #000000 0.4pt; border-top: solid #000000 0.4pt; border-bottom: solid #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'>
		<p style='text-align: center;'>".$SpecialName1."</p>
		</td>
		<td style=' height: 15px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'>
		<p style='text-align: center;'>".number_format($Special1)."</p>
		</td>
		<td style=' height: 15px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'>
		<p style='text-align: center;'>국민연금</p>
		</td>
		<td style=' height: 15px; border-left: solid #000000 0.4pt; border-right: solid #000000 1.1pt; border-top: solid #000000 0.4pt; border-bottom: solid #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'>
		<p style='text-align: center;'>".number_format($NationalPension)."</p>
		</td>
		</tr>
		<tr>
		<td style=' height: 15px; border-left: solid #000000 1.1pt; border-right: solid #000000 0.4pt; border-top: solid #000000 0.4pt; border-bottom: solid #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'>
		<p style='text-align: center;'>".$SpecialName2."</p>
		</td>
		<td style=' height: 15px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'>
		<p style='text-align: center;'>".number_format($Special2)."</p>
		</td>
		<td style=' height: 15px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'>
		<p style='text-align: center;'>&nbsp;</p>
		</td>
		<td style=' height: 15px; border-left: solid #000000 0.4pt; border-right: solid #000000 1.1pt; border-top: solid #000000 0.4pt; border-bottom: solid #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'>
		<p style='text-align: center;'>&nbsp;</p>
		</td>
		</tr>
		<tr>
		<td style=' height: 15px; border-left: solid #000000 1.1pt; border-right: solid #000000 0.4pt; border-top: solid #000000 0.4pt; border-bottom: solid #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'>
		<p style='text-align: center;'>".$Add1Name."</p>
		</td>
		<td style=' height: 15px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'>
		<p style='text-align: center;'>".$Add1."</p>
		</td>
		<td style=' height: 15px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'>
		<p style='text-align: center;'>".$Deduction1Name."</p>
		</td>
		<td style=' height: 15px; border-left: solid #000000 0.4pt; border-right: solid #000000 1.1pt; border-top: solid #000000 0.4pt; border-bottom: solid #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'>
		<p style='text-align: center;'>".$DeductionAdd1."</p>
		</td>
		</tr>
		<tr>
		<td style=' height: 15px; border-left: solid #000000 1.1pt; border-right: solid #000000 0.4pt; border-top: solid #000000 0.4pt; border-bottom: solid #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'>
		<p style='text-align: center;'>".$Add2Name."</p>
		</td>
		<td style='height: 15px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'>
		<p style='text-align: center;'>".$Add2."</p>
		</td>
		<td style='height: 15px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'>
		<p style='text-align: center;'>".$Deduction2Name."</p>
		</td>
		<td style='height: 15px; border-left: solid #000000 0.4pt; border-right: solid #000000 1.1pt; border-top: solid #000000 0.4pt; border-bottom: solid #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'>
		<p style='text-align: center;'>".$DeductionAdd2."</p>
		</td>
		</tr>
		<tr>
		<td style='height: 15px; border-left: solid #000000 1.1pt; border-right: solid #000000 0.4pt; border-top: solid #000000 0.4pt; border-bottom: solid #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'>
		<p style='text-align: center;'>".$Add3Name."</p>
		</td>
		<td style='height: 15px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'>
		<p style='text-align: center;'>".$Add3."</p>
		</td>
		<td style='height: 15px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'>
		<p style='text-align: center;'>".$Deduction3Name."</p>
		</td>
		<td style='height: 15px; border-left: solid #000000 0.4pt; border-right: solid #000000 1.1pt; border-top: solid #000000 0.4pt; border-bottom: solid #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'>
		<p style='text-align: center;'>".$DeductionAdd3."</p>
		</td>
		</tr>
		<tr>
		<td style='height: 15px; border-left: solid #000000 1.1pt; border-right: solid #000000 0.4pt; border-top: solid #000000 0.4pt; border-bottom: double #000000 1.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'>
		<p style='text-align: center;'>".$Add4Name."</p>
		</td>
		<td style='height: 15px; border-left: solid #000000 0.4pt; border-right: solid #000000 0.4pt; border-top: solid #000000 0.4pt; border-bottom: double #000000 1.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'>
		<p style='text-align: center;'>".$Add4."</p>
		</td>
		<td style='height: 15px; border-left: solid #000000 0.4pt; border-right: solid #000000 0.4pt; border-top: solid #000000 0.4pt; border-bottom: double #000000 1.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'>
		<p style='text-align: center;'>".$Deduction4Name."</p>
		</td>
		<td style='height: 15px; border-left: solid #000000 0.4pt; border-right: solid #000000 1.1pt; border-top: solid #000000 0.4pt; border-bottom: double #000000 1.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'>
		<p style='text-align: center;'>".$DeductionAdd4."</p>
		</td>
		</tr>
		<tr>
		<td style='height: 15px; border-left: solid #000000 1.1pt; border-right: solid #000000 0.4pt; border-top: double #000000 1.4pt; border-bottom: solid #000000 1.1pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'>
		<p style='text-align: center;'>급여 총액</p>
		</td>
		<td style='height: 15px; border-left: solid #000000 0.4pt; border-right: solid #000000 0.4pt; border-top: double #000000 1.4pt; border-bottom: solid #000000 1.1pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'>
		<p style='text-align: center;'>".number_format($TotalPay)."</p>
		</td>
		<td style='height: 15px; border-left: solid #000000 0.4pt; border-right: solid #000000 0.4pt; border-top: double #000000 1.4pt; border-bottom: solid #000000 1.1pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'>
		<p style='text-align: center;'>공제 총액</p>
		</td>
		<td style='height: 15px; border-left: solid #000000 0.4pt; border-right: solid #000000 1.1pt; border-top: double #000000 1.4pt; border-bottom: solid #000000 1.1pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'>
		<p style='text-align: center;'>".number_format($SumOfDeductions)."</p>
		</td>
		</tr>
		</tbody>
		</table>
		<br>
		<table class='emailtd' style='width:700px;border: 1px solid #444444;border-collapse: collapse;'> 
			<tr style='background-color: #e0ffff;border: 1px solid #444444;text-align:center;'><td colspan=2>임금계산 기초사항</td><td colspan=2>계산방법</td></tr>
			<tr><td style='width:25%;border: 1px solid #444444;text-align:center;'>근로일수</td><td  style='width:25%;border: 1px solid #444444;text-align:center;'>".number_format($TotalWorkDay)."</td><td  style='width:20%;border: 1px solid #444444;text-align:center;'>연장근로수당</td><td  style='width:30%;border: 1px solid #444444;text-align:center;'>연장근로시간X상시급X1.5</td></tr>
			<tr><td style='border: 1px solid #444444;text-align:center;'>총근로시간</td><td  style='border: 1px solid #444444;text-align:center;'>".number_format($TotalWorkTime)."</td><td  style='border: 1px solid #444444;text-align:center;'>야간근로수당</td><td  style='border: 1px solid #444444;text-align:center;'>야간근로시간X상시급X0.5</td></tr>
			<tr><td style='border: 1px solid #444444;text-align:center;'>연장근로시간</td><td  style='border: 1px solid #444444;text-align:center;'>0</td><td  style='border: 1px solid #444444;text-align:center;'>휴일근로수당</td><td  style='border: 1px solid #444444;text-align:center;'>휴일근로시간X상시급X1.5</td></tr>
			<tr><td style='border: 1px solid #444444;text-align:center;'>야간근로시간</td><td  style='border: 1px solid #444444;text-align:center;'>0</td><td  style='border: 1px solid #444444;text-align:center;'>근로소득세</td><td  style='border: 1px solid #444444;text-align:center;'>간이세액조견표 적용</td></tr>
			<tr><td style='border: 1px solid #444444;text-align:center;'>휴일근로시간</td><td  style='border: 1px solid #444444;text-align:center;'>0</td><td  style='border: 1px solid #444444;text-align:center;'>국민연금</td><td  style='border: 1px solid #444444;text-align:center;'>기준소득월액X4.5%</td></tr>
			<tr><td style='border: 1px solid #444444;text-align:center;'></td><td  style='border: 1px solid #444444;text-align:center;'></td><td  style='border: 1px solid #444444;text-align:center;'>고용보험</td><td  style='border: 1px solid #444444;text-align:center;'>보수월액X0.8%</td></tr>
			<tr><td style='border: 1px solid #444444;text-align:center;'></td><td  style='border: 1px solid #444444;text-align:center;'></td><td  style='border: 1px solid #444444;text-align:center;'>건강보험</td><td  style='border: 1px solid #444444;text-align:center;'>보수월액X3.43%</td></tr>
			<tr><td style='border: 1px solid #444444;text-align:center;'></td><td  style='border: 1px solid #444444;text-align:center;'></td><td  style='border: 1px solid #444444;text-align:center;'>장기요양보험</td><td  style='border: 1px solid #444444;text-align:center;'>건강보험료X11.52%</td></tr>
		</table> 
		<p>&nbsp;</p> 
		<div style='width:700px;text-align:center'>
			<p style='text-align: center;'><span style='font-size: 12.0pt; font-weight: bold; line-height: 160%;'>노고에 대단히 감사드립니다.</span></p>
			<p style='text-align: center;'> $YearMonth[0] 년&nbsp;&nbsp; $YearMonth[1]월&nbsp;&nbsp;&nbsp;</p>
			<p style='text-align: center;'><span style='font-size: 14.0pt; font-weight: bold; line-height: 160%;'>㈜ 에듀비전</span></p> <br><br><br>
		</div>";
		
		//실제
		//$to = $MemberEmail;
		
		//임시 테스트용
		
		$to = "sidhero@naver.com";
		if ($i==1) $to = "losthero@daum.net";
		if ($i==2) $to = "sidhero2112@gmail.com";
		
		
		$from_name = "망고아이";
		$subject = "급여 명세표";
		//한글 안깨지게 만들어줌
		$subject = "=?UTF-8?B?".base64_encode($subject)."?=";
		$content = $MailHTML;
		$Headers = "from: =?utf-8?B?".base64_encode($from_name)."?= <mangoi@mangoi.co.kr>"."\r\n"; // from 과 : 은 붙여주세요 => from: 
		$Headers .= "Content-Type: text/html;";
		
		$from = "mangoi@mangoi.co.kr";
		//if ($i==0) getSendMail($to,$from,$subject,$content,$html);
		
		//실제
		//if (mailCheck($MemberEmail)) mail($to,$subject,$content,$Headers); 
		
		//테스트용
		if ($i==0 || $i==1 || $i==2) mail($to,$subject,$content,$Headers); 
		
		$i++;

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
	header("Location: pay.php?isMailSend=1&$ListParam"); 
	exit;
}


?>