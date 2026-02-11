<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

header( "Content-type: application/vnd.ms-excel; charset=utf-8");
header( "Content-Disposition: attachment; filename = Expense_Sheet.xls" );
header( "Content-Description: PHP4 Generated Data" );
?>
<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="ko"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="ko"> <!--<![endif]-->
<head>
<meta charset="utf-8">
</head>
<body>


 
<?php

$AddSqlWhere = "1=1";
$ListParam = "1=1";
$PaginationParam = "1=1";

$ListType = isset($_REQUEST["ListType"]) ? $_REQUEST["ListType"] : "";

$CurrentPage = isset($_REQUEST["CurrentPage"]) ? $_REQUEST["CurrentPage"] : "";
$PageListNum = isset($_REQUEST["PageListNum"]) ? $_REQUEST["PageListNum"] : "";
$SearchText = isset($_REQUEST["SearchText"]) ? $_REQUEST["SearchText"] : "";
$SearchText2 = isset($_REQUEST["SearchText2"]) ? $_REQUEST["SearchText2"] : "";
$SearchState = isset($_REQUEST["SearchState"]) ? $_REQUEST["SearchState"] : "";
$SearchDocumentID = isset($_REQUEST["SearchDocumentID"]) ? $_REQUEST["SearchDocumentID"] : "";

$SearchStartYear = isset($_REQUEST["SearchStartYear"]) ? $_REQUEST["SearchStartYear"] : "";
$SearchStartMonth = isset($_REQUEST["SearchStartMonth"]) ? $_REQUEST["SearchStartMonth"] : "";
$SearchStartDay = isset($_REQUEST["SearchStartDay"]) ? $_REQUEST["SearchStartDay"] : "";

$SearchEndYear = isset($_REQUEST["SearchEndYear"]) ? $_REQUEST["SearchEndYear"] : "";
$SearchEndMonth = isset($_REQUEST["SearchEndMonth"]) ? $_REQUEST["SearchEndMonth"] : "";
$SearchEndDay = isset($_REQUEST["SearchEndDay"]) ? $_REQUEST["SearchEndDay"] : "";


$Auth_Document_99 = 0;
//if ($_LINK_ADMIN_ID_==22050 || $_LINK_ADMIN_ID_==22054 ||  $_LINK_ADMIN_ID_==1){
if ($_LINK_ADMIN_ID_==22050 || $_LINK_ADMIN_ID_==22054){//정우영, 이지애
	$Auth_Document_99 = 1;
}


if ($SearchStartYear==""){
	$SearchStartYear = date("Y");
}
if ($SearchStartMonth==""){
	$SearchStartMonth = date("m");
}
if ($SearchStartDay==""){
	$SearchStartDay = date("d");
}

if ($SearchEndYear==""){
	$SearchEndYear = date("Y");
}
if ($SearchEndMonth==""){
	$SearchEndMonth = date("m");
}
if ($SearchEndDay==""){
	$SearchEndDay = date("d");
}

$StartDate = $SearchStartYear . "-" . substr("0".$SearchStartMonth,-2) . "-" . substr("0".$SearchStartDay,-2);
$EndDate = $SearchEndYear . "-" . substr("0".$SearchEndMonth,-2) . "-" . substr("0".$SearchEndDay,-2);



if (!$CurrentPage){
	$CurrentPage = 1;	
}
if (!$PageListNum){
	$PageListNum = 30;
}


if ($PageListNum!=""){
	$ListParam = $ListParam . "&PageListNum=" . $PageListNum;
}

if ($SearchState==""){
	$SearchState = "1";
}	

if ($SearchDocumentID==""){
	$SearchDocumentID = "100";
}	



$ListParam = $ListParam . "&SearchStartYear=" . $SearchStartYear;
$ListParam = $ListParam . "&SearchStartMonth=" . $SearchStartMonth;
$ListParam = $ListParam . "&SearchStartDay=" . $SearchStartDay;
$ListParam = $ListParam . "&SearchEndYear=" . $SearchEndYear;
$ListParam = $ListParam . "&SearchEndMonth=" . $SearchEndMonth;
$ListParam = $ListParam . "&SearchEndDay=" . $SearchEndDay;
$AddSqlWhere = $AddSqlWhere . " and datediff(A.DocumentReportRegDateTime, '$StartDate')>=0 ";
$AddSqlWhere = $AddSqlWhere . " and datediff(A.DocumentReportRegDateTime, '$EndDate')<=0 ";

		
if ($SearchState!="100"){
	$ListParam = $ListParam . "&SearchState=" . $SearchState;
	$AddSqlWhere = $AddSqlWhere . " and A.DocumentReportState=$SearchState ";
}
$AddSqlWhere = $AddSqlWhere . " and A.DocumentReportState=1 ";

if ($ListType!="100"){
	if ($Auth_Document_99==0){
		$AddSqlWhere = $AddSqlWhere . " and A.DocumentReportID in (select DocumentReportID from DocumentReportMembers where MemberID=".$_LINK_ADMIN_ID_.") ";
	}
}

if ($SearchText!=""){
	$ListParam = $ListParam . "&SearchText=" . $SearchText;
	$AddSqlWhere = $AddSqlWhere . " and A.DocumentReportName like '%".$SearchText."%' ";
}

if ($SearchText2!=""){
	$ListParam = $ListParam . "&SearchText2=" . $SearchText2;
	$AddSqlWhere = $AddSqlWhere . " and C.MemberName like '%".$SearchText2."%' ";
}

if ($SearchDocumentID!=""){
	$ListParam = $ListParam . "&SearchDocumentID=" . $SearchDocumentID;
	if ($SearchDocumentID=="99"){
		$AddSqlWhere = $AddSqlWhere . " and A.DocumentID=99 ";
	}else if ($SearchDocumentID=="88"){
		$AddSqlWhere = $AddSqlWhere . " and A.DocumentID<>99 ";
	}else{

	}
}


$PaginationParam = $ListParam;
if ($CurrentPage!=""){
	$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}

$ListParam = str_replace("&", "^^", $ListParam);

$Sql = "select 
				count(*) TotalRowCount 
		from DocumentReports A 
			left outer join Documents B on A.DocumentID=B.DocumentID 
			inner join Members C on A.MemberID=C.MemberID 
		where ".$AddSqlWhere." ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TotalRowCount = $Row["TotalRowCount"];

$TotalPageCount = ceil($TotalRowCount / $PageListNum);
$StartRowNum = $PageListNum * ($CurrentPage - 1 );



$Sql = "
		select 
			A.*,
			ifnull(B.DocumentName, '-') as DocumentName, 
			C.MemberName,
			C.MemberDprtName
		from DocumentReports A 
			left outer join Documents B on A.DocumentID=B.DocumentID 
			inner join Members C on A.MemberID=C.MemberID 
		where ".$AddSqlWhere." 
		order by A.DocumentReportRegDateTime desc";// limit $StartRowNum, $PageListNum";



$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
?>


<table border="1" width="80%">
	<thead>
		<tr>
			<th>No</th>
			<th>발의일</th>
			<th>제목</th>
			<th>금액</th>
			<th>부서</th>
			<th>작성자</th>
			<th>지출일</th>
			<th>지출방법</th>
			<th>증빙자료</th>
			<th>계정과목</th>
			<!--<th>승인일</th>-->
			<th>진행상태</th>
			
		</tr>
	</thead>
	<tbody>
		
		<?php
		$ListCount = 1;
		$TTL_Price = 0;
		while($Row = $Stmt->fetch()) {
			$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

			$DocumentID = $Row["DocumentID"];
			$DocumentReportID = $Row["DocumentReportID"];
			$DocumentReportName = $Row["DocumentReportName"];
			$DocumentReportState = $Row["DocumentReportState"];
			$DocumentReportRegDateTime = $Row["DocumentReportRegDateTime"];
			$FileName = $Row["FileName"];
			$FileRealName = $Row["FileRealName"];

			$PayDate = $Row["PayDate"];
			$PayMethod = $Row["PayMethod"];
			$AccCode = $Row["AccCode"];

			if ($DocumentID==99){
				$DocumentName = "기안 및 지출서";
			}else{
				$DocumentName = $Row["DocumentName"];
			}

			$MemberName = $Row["MemberName"];
			$MemberDprtName = $Row["MemberDprtName"];
			
			if ($DocumentReportState==1){
				$StrDocumentReportState = "<span class=\"ListState_1\">활성</span>";
			}else if ($DocumentReportState==2){
				$StrDocumentReportState = "<span class=\"ListState_2\">미활성</span>";
			}


			
			$Sql2 = "select count(*) as DetailCount from DocumentReportDetails A where A.DocumentReportID=".$DocumentReportID." and trim(A.DocumentReportDetailName)<>'' ";
			$Stmt2 = $DbConn->prepare($Sql2);
			$Stmt2->execute();
			$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
			$Row2 = $Stmt2->fetch();
			$Stmt2 = null;
			$DetailCount = $Row2["DetailCount"];

			$Sql2 = "select A.* from DocumentReportDetails A where A.DocumentReportID=".$DocumentReportID." and trim(A.DocumentReportDetailName)<>'' ";
			$Stmt2 = $DbConn->prepare($Sql2);
			$Stmt2->execute();
			$Stmt2->setFetchMode(PDO::FETCH_ASSOC);


			$DetailListCount=1;
			while($Row2 = $Stmt2->fetch()) {
				$DocumentReportDetailPrice = $Row2["DocumentReportDetailPrice"];
				$DocumentReportDetailVat = $Row2["DocumentReportDetailVat"];

				$TTL_Price = $TTL_Price + ($DocumentReportDetailPrice+$DocumentReportDetailVat);
		?>
			<tr>
				<?if ($DetailListCount==1){?>
					<td rowspan="<?=$DetailCount ?>"><?=$ListNumber?></td>
					<td rowspan="<?=$DetailCount ?>"><?=substr($DocumentReportRegDateTime,0,10)?></td>
					<td rowspan="<?=$DetailCount ?>"><?=$DocumentReportName?></td>
					<td><?=$DocumentReportDetailPrice+$DocumentReportDetailVat?></td>
				<?}else{?>
					<td><?=$DocumentReportDetailPrice+$DocumentReportDetailVat?></td>
				<?}?>
				
				
				<?if ($DetailListCount==1){?>
					<td rowspan="<?=$DetailCount ?>"><?=$MemberDprtName?></td>
					<td rowspan="<?=$DetailCount ?>"><?=$MemberName?></td>
					<td rowspan="<?=$DetailCount ?>"><?=$PayDate?></td>
					<td rowspan="<?=$DetailCount ?>"><?=$PayMethod ?></td>
					<td rowspan="<?=$DetailCount ?>"><?=$FileRealName?></td>
					<td rowspan="<?=$DetailCount ?>"><?=$AccCode?></td>
					<!--<td rowspan="<?=$DetailCount ?>">-</td>-->

					<td rowspan="<?=$DetailCount ?>">
							<?
							$Sql3 = "select A.*, B.MemberName from DocumentReportMembers A inner join Members B on A.MemberID=B.MemberID where A.DocumentReportID=".$DocumentReportID." order by A.DocumentReportMemberOrder asc";
							$Stmt3 = $DbConn->prepare($Sql3);
							$Stmt3->execute();
							$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
							
							$ii=1;
							while($Row3 = $Stmt3->fetch()) {
						
								$DocumentReportMemberState = $Row3["DocumentReportMemberState"];
								$DocumentReportMemberModiDateTime = $Row3["DocumentReportMemberModiDateTime"];
								if ($DocumentReportMemberState==0){
									$StrDocumentReportMemberState = "-";
									$StrDocumentReportMemberModiDateTime = "";
								}else if ($DocumentReportMemberState==1){
									$StrDocumentReportMemberState = "승인";
									$StrDocumentReportMemberModiDateTime = ", ".substr($DocumentReportMemberModiDateTime, 0,10);
								}else if ($DocumentReportMemberState==2){
									$StrDocumentReportMemberState = "반려";
									$StrDocumentReportMemberModiDateTime = "";
								}

								if ($ii>1){
									echo ", ";
								}
							?>
								<b><?=$Row3["MemberName"]?> (<?=$StrDocumentReportMemberState?><?=$StrDocumentReportMemberModiDateTime?>) </b>
							<?
								$ii++;
							}
							$Stmt3 = null;
							
							?>
					</td>
				<?}?>
			</tr>
			
		<?	
				$DetailListCount++;
			}
			$Stmt2 = null;
			$ListCount ++;
		}
		$Stmt = null;
		?>
		<tr>
			<th colspan="3">TTL 지출</th>
			<th><?=$TTL_Price?></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<!--<th></th>-->
			<th></th>
			
		</tr>

	</tbody>
</table>



<?php
include_once('../includes/dbclose.php');
?>
</body>
</html>