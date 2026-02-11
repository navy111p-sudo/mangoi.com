<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/common.php');
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
<link rel="stylesheet" type="text/css" href="./css/common.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script type="text/javascript" src="./js/jquery.techbytarun.excelexportjs.min.js"></script>


<style>
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
	th,td{
		border: 1px solid #444444;
		text-align:center;
	}
</style>

</head>
<body>
<center>
<br>
<a id="btnExport" href="#" download="">
	<button type='button' class="md-btn md-btn-primary" style="height:35px"  >엑셀 파일로 내보내기</button>
</a>
<br>
<br>
</center>
<table id='tblExport'>
	<tr>
		<td style="text-align:left;">
			<div id="contents" class="ContentPopup" style="width:750px;">
			</div>	
		</td>
	</tr>
</table>	

</body>
<script type="text/javascript">
	
	function isEmpty(str){
		
		if(typeof str == "undefined" || str == null || str == "")
			return true;
		else
			return false ;
	}


	var dt = new Date();
	var year =	itoStr( dt.getFullYear() );
	var month = itoStr( dt.getMonth() + 1 );
	var	day =	itoStr( dt.getDate() );
	var hour =	itoStr( dt.getHours() );
	var mins =	itoStr( dt.getMinutes() );

	function itoStr($num)
	{
		$num < 10 ? $num = '0'+$num : $num;
		return $num.toString();
	}

    $(document).ready(function () {
		var btn = $('#btnExport');
		var tbl = 'tblExport';

        btn.on('click', function () {

			var postfix = year + month + day + "_" + hour + mins;
			var fileName = "급여명세서_"+ postfix + ".xls";

            var uri = $("#"+tbl).excelexportjs({
                containerid: tbl
                , datatype: 'table'
                , returnUri: true
            });

            $(this).attr('download', fileName).attr('href', uri).attr('target', '_blank');
        });
    });
	</script>
<script>
	
// 현재 선택되어 있는 데이터의 id들을 가지고 온다.
var selRowIds = opener.$('#list1').jqGrid('getGridParam','selarrrow');

var Add1Name = opener.$('#jqgh_list1_Add1').text();
var Add2Name = opener.$('#jqgh_list1_Add2').text();
var Add3Name = opener.$('#jqgh_list1_Add3').text();
var Add4Name = opener.$('#jqgh_list1_Add4').text();
var Add5Name = opener.$('#jqgh_list1_Add5').text();
var Add6Name = opener.$('#jqgh_list1_Add6').text();
var Add7Name = opener.$('#jqgh_list1_Add7').text();

var DeductionAdd1Name = opener.$('#jqgh_list1_DeductionAdd1').text();
var DeductionAdd2Name = opener.$('#jqgh_list1_DeductionAdd2').text();
var DeductionAdd3Name = opener.$('#jqgh_list1_DeductionAdd3').text();
var DeductionAdd4Name = opener.$('#jqgh_list1_DeductionAdd4').text();
var DeductionAdd5Name = opener.$('#jqgh_list1_DeductionAdd5').text();
var DeductionAdd6Name = opener.$('#jqgh_list1_DeductionAdd6').text();
var DeductionAdd7Name = opener.$('#jqgh_list1_DeductionAdd7').text();

// 만약 선택되어 있는 값이 없으면 전체 id를 가져온다.
if (selRowIds.length == 0){
	selRowIds = opener.$('#list1').jqGrid('getDataIDs');
}

// 각 아이디별로 데이터를 가지고 와서 급여명세서 양식을 넣어주고 화면에 출력해준다.
for (key in selRowIds) {
	var obj = opener.$('#list1').jqGrid('getRowData', selRowIds[key]);

	var payStub = "<table style='border-collapse: collapse; border: none;width: 700px; ' border='1' cellspacing='0' cellpadding='0'> \
<tbody> \
<tr> \
  <td rowspan='5' colspan=2 style='width: 400px; height: 53px; border-left: none; border-right: solid #000000 0.4pt; border-top: none; border-bottom: none; padding: 1.4pt 2.0pt 1.4pt 2.0pt;'  valign='middle'> \
    <h2 style='text-align: center;'><span style='font-size: 18.0pt;'>급 여 명 세 서</span></h2> \
  </td> \
  <td style='width: 70px; height: 26px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'> \
    <p style='text-align: center;'>사원번호</p> \
  </td> \
  <td style='width: 151px; height: 26px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'> \
    <p style='text-align: center;'>"+obj.StaffID.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",")+"</p> \
  </td> \
</tr> \
<tr> \
  <td style='width: 70px; height: 26px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'> \
    <p style='text-align: center;'>부&nbsp; 서</p> \
  </td> \
  <td style='width: 151px; height: 26px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'> \
    <p style='text-align: center;'>"+obj.DepartmentName.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",")+"</p> \
  </td> \
</tr> \
<tr> \
  <td style='width: 70px; height: 26px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'> \
    <p style='text-align: center;'>직&nbsp; 무</p> \
  </td> \
  <td style='width: 151px; height: 26px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'> \
    <p style='text-align: center;'>"+obj.Hr_OrganPositionName.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",")+"</p> \
  </td> \
</tr> \
<tr> \
  <td style='width: 70px; height: 26px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'> \
    <p style='text-align: center;'>성&nbsp; 명</p> \
  </td> \
  <td style='width: 151px; height: 26px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'> \
    <p style='text-align: center;'>"+obj.StaffName.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",")+"</p> \
  </td> \
</tr> \
<tr> \
  <td style='width: 70px; height: 26px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'> \
    <p style='text-align: center;'>생년월일</p> \
  </td> \
  <td style='width: 151px; height: 26px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'> \
    <p style='text-align: center;'>"+obj.Jumin1.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",")+"</p> \
  </td> \
</tr> \
</tbody> \
</table> \
<p>&nbsp;</p> \
<p><span style='font-weight: bold;'>1. 지급액</span>&nbsp;</p> \
<table style='border-collapse: collapse; border: none black; width: 700px; ' border='1' cellspacing='0' cellpadding='0'> \
<tbody> \
<tr style='height: 26px; background-color: #e0ffff;'> \
  <td style='width:25%;text-align: center; height: 26px;'>&nbsp;과세급여</td> \
  <td style='width:25%;text-align: center; height: 26px;'>비과세급여&nbsp;</td> \
  <td style='width:25%;text-align: center; height: 26px;'>지급합계&nbsp;</td> \
  <td style='width:25%;text-align: center; height: 26px;'>공제합계&nbsp;</td> \
</tr> \
<tr style='height: 26px;'> \
  <td  style='text-align: center;'>"+obj.TaxationPay.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",")+"</td> \
  <td  style='text-align: center;'>"+obj.TaxFreePay.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",")+"</td> \
  <td  style='text-align: center;'>"+obj.TotalPay.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",")+"</td> \
  <td  style='text-align: center;'>"+obj.SumOfDeductions.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",")+"</td> \
</tr> \
<tr style='height: 26px;'> \
  <td style='width:20%;text-align: center; height: 26px; background-color: #e0ffff;'>차감 지급액&nbsp;</td> \
  <td style='text-align: center;'>"+obj.ActualPay.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",")+"</td> \
  <td style='width:20%;text-align: center; height: 26px; background-color: #e0ffff;'>지급일자</td> \
  <td style='text-align: center;'>"+obj.GivePayDate.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",")+"</td> \
</tr> \
</tbody> \
</table> \
<p>&nbsp;</p> \
<p><span style='font-weight: bold;'>2. 지급 내역</span>&nbsp;</p> \
<table style='width:700px;border-collapse: collapse; border: none;' border='1' cellspacing='0' cellpadding='0'> \
<tbody> \
<tr style='background-color: #e0ffff;'> \
<td style='width: 50%; height: 15px; border-left: solid #000000 1.1pt; border-right: solid #000000 0.4pt; border-top: solid #000000 1.1pt; border-bottom: solid #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' colspan='2' valign='middle'> \
<p style='text-align: center;'>지급항목</p> \
</td> \
<td style='width: 50%; height: 15px; border-left: solid #000000 0.4pt; border-right: solid #000000 1.1pt; border-top: solid #000000 1.1pt; border-bottom: solid #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' colspan='2' valign='middle'> \
<p style='text-align: center;'>공제항목</p> \
</td> \
</tr> \
<tr> \
<td style='width: 25%; height: 15px; border-left: solid #000000 1.1pt; border-right: solid #000000 0.4pt; border-top: solid #000000 0.4pt; border-bottom: solid #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'> \
<p style='text-align: center;'>기본급여</p> \
</td> \
<td style='width: 25%; height: 15px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'> \
<p style='text-align: center;'>"+obj.BasePay.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",")+"</p> \
</td> \
<td style='width: 25%; height: 15px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'> \
<p style='text-align: center;'>소 득 세</p> \
</td> \
<td style='width: 25%; height: 15px; border-left: solid #000000 0.4pt; border-right: solid #000000 1.1pt; border-top: solid #000000 0.4pt; border-bottom: solid #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'> \
<p style='text-align: center;'>"+obj.IncomeTax.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",")+"</p> \
</td> \
</tr> \
<tr> \
<td style=' height: 15px; border-left: solid #000000 1.1pt; border-right: solid #000000 0.4pt; border-top: solid #000000 0.4pt; border-bottom: solid #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'> \
<p style='text-align: center;'>직책수당</p> \
</td> \
<td style=' height: 15px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'> \
<p style='text-align: center;'>"+obj.PositionPay.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",")+"</p> \
</td> \
<td style=' height: 15px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'> \
<p style='text-align: center;'>주 민 세</p> \
</td> \
<td style=' height: 15px; border-left: solid #000000 0.4pt; border-right: solid #000000 1.1pt; border-top: solid #000000 0.4pt; border-bottom: solid #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'> \
<p style='text-align: center;'>"+obj.ResidenceTax.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",")+"</p> \
</td> \
</tr> \
<tr> \
<td style=' height: 15px; border-left: solid #000000 1.1pt; border-right: solid #000000 0.4pt; border-top: solid #000000 0.4pt; border-bottom: solid #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'> \
<p style='text-align: center;'>특무수당</p> \
</td> \
<td style=' height: 15px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'> \
<p style='text-align: center;'>"+obj.SpecialDutyPay.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",")+"</p> \
</td> \
<td style=' height: 15px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'> \
<p style='text-align: center;'>고용보험</p> \
</td> \
<td style=' height: 15px; border-left: solid #000000 0.4pt; border-right: solid #000000 1.1pt; border-top: solid #000000 0.4pt; border-bottom: solid #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'> \
<p style='text-align: center;'>"+obj.EmploymentInsurance.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",")+"</p> \
</td> \
</tr> \
<tr> \
<td style=' height: 15px; border-width: 0.4pt 0.4pt 0.4pt 1.1pt; border-style: solid; border-color: #000000; padding: 1.4pt 2.0pt; text-align: center;' valign='middle'>오버타임수당</td> \
<td style=' height: 15px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'> \
<p style='text-align: center;'>"+obj.OverTimePay.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",")+"</p> \
</td> \
<td style=' height: 15px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'> \
<p style='text-align: center;'>건강보험</p> \
</td> \
<td style=' height: 15px; border-left: solid #000000 0.4pt; border-right: solid #000000 1.1pt; border-top: solid #000000 0.4pt; border-bottom: solid #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'> \
<p style='text-align: center;'>"+obj.HealthInsurance.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",")+"</p> \
</td> \
</tr> \
<tr> \
<td style=' height: 15px; border-left: solid #000000 1.1pt; border-right: solid #000000 0.4pt; border-top: solid #000000 0.4pt; border-bottom: solid #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'> \
<p style='text-align: center;'>&nbsp;대체수당</p> \
</td> \
<td style=' height: 15px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'> \
<p style='text-align: center;'>"+obj.ReplacePay.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",")+"</p> \
</td> \
<td style=' height: 15px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'> \
<p style='text-align: center;'>장기요양보험</p> \
</td> \
<td style=' height: 15px; border-left: solid #000000 0.4pt; border-right: solid #000000 1.1pt; border-top: solid #000000 0.4pt; border-bottom: solid #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'> \
<p style='text-align: center;'>"+obj.CareInsurance.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",")+"</p> \
</td> \
</tr> \
<tr> \
<td style=' height: 15px; border-left: solid #000000 1.1pt; border-right: solid #000000 0.4pt; border-top: solid #000000 0.4pt; border-bottom: solid #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'> \
<p style='text-align: center;'>"+obj.SpecialName1+"</p> \
</td> \
<td style=' height: 15px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'> \
<p style='text-align: center;'>"+obj.Special1.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",")+"</p> \
</td> \
<td style=' height: 15px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'> \
<p style='text-align: center;'>국민연금</p> \
</td> \
<td style=' height: 15px; border-left: solid #000000 0.4pt; border-right: solid #000000 1.1pt; border-top: solid #000000 0.4pt; border-bottom: solid #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'> \
<p style='text-align: center;'>"+obj.NationalPension.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",")+"</p> \
</td> \
</tr> \
<tr> \
<td style=' height: 15px; border-left: solid #000000 1.1pt; border-right: solid #000000 0.4pt; border-top: solid #000000 0.4pt; border-bottom: solid #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'> \
<p style='text-align: center;'>"+obj.SpecialName2+"</p> \
</td> \
<td style=' height: 15px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'> \
<p style='text-align: center;'>"+obj.Special2.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",")+"</p> \
</td> \
<td style=' height: 15px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'> \
<p style='text-align: center;'>&nbsp;</p> \
</td> \
<td style=' height: 15px; border-left: solid #000000 0.4pt; border-right: solid #000000 1.1pt; border-top: solid #000000 0.4pt; border-bottom: solid #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'> \
<p style='text-align: center;'>&nbsp;</p> \
</td> \
</tr> \
<tr> \
<td style=' height: 15px; border-left: solid #000000 1.1pt; border-right: solid #000000 0.4pt; border-top: solid #000000 0.4pt; border-bottom: solid #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'> \
<p style='text-align: center;'>"+Add1Name+"</p> \
</td> \
<td style=' height: 15px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'> \
<p style='text-align: center;'>"+(isEmpty(obj.Add1)?"0":obj.Add1.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ","))+"</p> \
</td> \
<td style=' height: 15px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'> \
<p style='text-align: center;'>"+DeductionAdd1Name+"</p> \
</td> \
<td style=' height: 15px; border-left: solid #000000 0.4pt; border-right: solid #000000 1.1pt; border-top: solid #000000 0.4pt; border-bottom: solid #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'> \
<p style='text-align: center;'>"+(isEmpty(obj.DeductionAdd1)?"0":obj.DeductionAdd1.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ","))+"</p> \
</td> \
</tr> \
<tr> \
<td style=' height: 15px; border-left: solid #000000 1.1pt; border-right: solid #000000 0.4pt; border-top: solid #000000 0.4pt; border-bottom: solid #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'> \
<p style='text-align: center;'>"+Add2Name+"</p> \
</td> \
<td style='height: 15px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'> \
<p style='text-align: center;'>"+(isEmpty(obj.Add2)?"0":obj.Add2.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ","))+"</p> \
</td> \
<td style='height: 15px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'> \
<p style='text-align: center;'>"+DeductionAdd2Name+"</p> \
</td> \
<td style='height: 15px; border-left: solid #000000 0.4pt; border-right: solid #000000 1.1pt; border-top: solid #000000 0.4pt; border-bottom: solid #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'> \
<p style='text-align: center;'>"+(isEmpty(obj.DeductionAdd2)?"0":obj.DeductionAdd2.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ","))+"</p> \
</td> \
</tr> \
<tr> \
<td style='height: 15px; border-left: solid #000000 1.1pt; border-right: solid #000000 0.4pt; border-top: solid #000000 0.4pt; border-bottom: solid #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'> \
<p style='text-align: center;'>"+Add3Name+"</p> \
</td> \
<td style='height: 15px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'> \
<p style='text-align: center;'>"+(isEmpty(obj.Add3)?"0":obj.Add3.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ","))+"</p> \
</td> \
<td style='height: 15px; padding: 1.4pt 2.0pt 1.4pt 2.0pt; border: solid #000000 0.4pt;' valign='middle'> \
<p style='text-align: center;'>"+DeductionAdd3Name+"</p> \
</td> \
<td style='height: 15px; border-left: solid #000000 0.4pt; border-right: solid #000000 1.1pt; border-top: solid #000000 0.4pt; border-bottom: solid #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'> \
<p style='text-align: center;'>"+(isEmpty(obj.DeductionAdd3)?"0":obj.DeductionAdd3.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ","))+"</p> \
</td> \
</tr> \
<tr> \
<td style='height: 15px; border-left: solid #000000 0.4pt; border-right: solid #000000 0.4pt; border-top: solid #000000 0.4pt; border-bottom: double #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'> \
<p style='text-align: center;'>"+Add4Name+"</p> \
</td> \
<td style='height: 15px; border-left: solid #000000 0.4pt; border-right: solid #000000 0.4pt; border-top: solid #000000 0.4pt; border-bottom: double #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'> \
<p style='text-align: center;'>"+(isEmpty(obj.Add4)?"0":obj.Add4.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ","))+"</p> \
</td> \
<td style='height: 15px; border-left: solid #000000 0.4pt; border-right: solid #000000 0.4pt; border-top: solid #000000 0.4pt; border-bottom: double #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'> \
<p style='text-align: center;'>"+DeductionAdd4Name+"</p> \
</td> \
<td style='height: 15px; border-left: solid #000000 0.4pt; border-right: solid #000000 1.1pt; border-top: solid #000000 0.4pt; border-bottom: double #000000 0.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'> \
<p style='text-align: center;'>"+(isEmpty(obj.DeductionAdd4)?"0":obj.DeductionAdd4.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ","))+"</p> \
</td> \
</tr> \
<tr> \
<td style='height: 15px; border-left: solid #000000 1.1pt; border-right: solid #000000 0.4pt; border-top: solid #000000 0.4pt; border-bottom: double #000000 1.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'> \
<p style='text-align: center;'>"+Add5Name+"</p> \
</td> \
<td style='height: 15px; border-left: solid #000000 0.4pt; border-right: solid #000000 0.4pt; border-top: solid #000000 0.4pt; border-bottom: double #000000 1.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'> \
<p style='text-align: center;'>"+(isEmpty(obj.Add5)?"0":obj.Add5.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ","))+"</p> \
</td> \
<td style='height: 15px; border-left: solid #000000 0.4pt; border-right: solid #000000 0.4pt; border-top: solid #000000 0.4pt; border-bottom: double #000000 1.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'> \
<p style='text-align: center;'>"+DeductionAdd5Name+"</p> \
</td> \
<td style='height: 15px; border-left: solid #000000 0.4pt; border-right: solid #000000 1.1pt; border-top: solid #000000 0.4pt; border-bottom: double #000000 1.4pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'> \
<p style='text-align: center;'>"+(isEmpty(obj.DeductionAdd5)?"0":obj.DeductionAdd5.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ","))+"</p> \
</td> \
</tr> \
<tr> \
<td style='height: 15px; border-left: solid #000000 1.1pt; border-right: solid #000000 0.4pt; border-top: double #000000 1.4pt; border-bottom: solid #000000 1.1pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'> \
<p style='text-align: center;'>급여 총액</p> \
</td> \
<td style='height: 15px; border-left: solid #000000 0.4pt; border-right: solid #000000 0.4pt; border-top: double #000000 1.4pt; border-bottom: solid #000000 1.1pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'> \
<p style='text-align: center;'>"+obj.TotalPay.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",")+"</p> \
</td> \
<td style='height: 15px; border-left: solid #000000 0.4pt; border-right: solid #000000 0.4pt; border-top: double #000000 1.4pt; border-bottom: solid #000000 1.1pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'> \
<p style='text-align: center;'>공제 총액</p> \
</td> \
<td style='height: 15px; border-left: solid #000000 0.4pt; border-right: solid #000000 1.1pt; border-top: double #000000 1.4pt; border-bottom: solid #000000 1.1pt; padding: 1.4pt 2.0pt 1.4pt 2.0pt;' valign='middle'> \
<p style='text-align: center;'>"+obj.SumOfDeductions.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",")+"</p> \
</td> \
</tr> \
</tbody> \
</table> \
<br>\
<table  style='width:700px;border: 1px solid #444444;border-collapse: collapse;'> \
	<tr style='background-color: #e0ffff;'><td colspan=2>임금계산 기초사항</td><td colspan=2>계산방법</td></tr>\
	<tr><td style='width:25%'>근로일수</td><td style='width:25%'>"+obj.TotalWorkDay.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",")+"</td><td style='width:20%'>연장근로수당</td><td style='width:30%'>연장근로시간X상시급X1.5</td></tr>\
	<tr><td >총근로시간</td><td>"+obj.TotalWorkTime.replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",")+"</td><td>야간근로수당</td><td>야간근로시간X상시급X0.5</td></tr>\
	<tr><td>연장근로시간</td><td>0</td><td>휴일근로수당</td><td>휴일근로시간X상시급X1.5</td></tr>\
	<tr><td>야간근로시간</td><td>0</td><td>근로소득세</td><td>간이세액조견표 적용</td></tr>\
	<tr><td>휴일근로시간</td><td>0</td><td>국민연금</td><td>기준소득월액X4.5%</td></tr>\
	<tr><td></td><td></td><td>고용보험</td><td>보수월액X0.8%</td></tr>\
	<tr><td></td><td></td><td>건강보험</td><td>보수월액X3.43%</td></tr>\
	<tr><td></td><td></td><td>장기요양보험</td><td>건강보험료X11.52%</td></tr>\
</table> \
<p>&nbsp;</p> \
<p style='text-align: center;'><span style='font-size: 12.0pt; font-weight: bold; line-height: 160%;'>노고에 대단히 감사드립니다.</span></p> \
\
<p style='text-align: center;'>"+year+" 년&nbsp;&nbsp;"+month+"월&nbsp;&nbsp;&nbsp; "+day+"일</p> \
\
<p style='text-align: center;'><span style='font-size: 14.0pt; font-weight: bold; line-height: 160%;'>㈜ 에듀비전</span></p> <br><br><br>";

	$('#contents').append('<div>'+payStub+'</div>');
	
}

</script>



</html>
<?php
include_once('../includes/dbclose.php');
?>