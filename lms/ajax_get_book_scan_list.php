<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');

$BookID = isset($_REQUEST["BookID"]) ? $_REQUEST["BookID"] : "";


$BookScanList = "";


$BookScanList .= "<table class=\"uk-table uk-table-align-vertical\">";
$BookScanList .= "	<thead>";
$BookScanList .= "		<tr>";
$BookScanList .= "			<th width=\"15%\" nowrap>번호</th>";
$BookScanList .= "			<th nowrap>교재 PDF자료 제목</th>";
//$BookScanList .= "			<th width=\"10%\" nowrap>이미지확인</th>";
$BookScanList .= "			<th width=\"10%\" nowrap>상태</th>";
$BookScanList .= "			<th width=\"10%\" nowrap>순서</th>";
$BookScanList .= "			<th width=\"10%\" nowrap></th>";
$BookScanList .= "		</tr>";
$BookScanList .= "	</thead>";
$BookScanList .= "	<tbody>";

$Sql = "
		select 
			A.*
		from BookScans A
		where A.BookID=:BookID and A.BookScanState<>0
		order by A.BookScanOrder asc";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':BookID', $BookID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

$ListCount = 1;
while($Row = $Stmt->fetch()) {
	$BookScanID = $Row["BookScanID"];
	$BookScanName = $Row["BookScanName"];
	$BookScanState = $Row["BookScanState"];
	$BookScanImageFileName = $Row["BookScanImageFileName"];
	
	if ($BookScanState==1){
		$StrBookScanState = "<span class=\"ListState_1\">사용</span>";
	}else if ($BookScanState==2){
		$StrBookScanState = "<span class=\"ListState_2\">미사용</span>";
	}

	$BookScanList .= "		<tr>";
	$BookScanList .= "			<td class=\"uk-text-nowrap uk-table-td-center\">".$ListCount."</td>";
	$BookScanList .= "			<td class=\"uk-text-nowrap uk-table-td\"><a href=\"javascript:OpenBookScanForm(".$BookScanID.");\">".$BookScanName."</td>";
	//$BookScanList .= "			<td class=\"uk-text-nowrap uk-table-td-center\"><a href=\"javascript:OpenBookViewer(".$BookScanID.");\"><i class=\"material-icons\">photo_size_select_actual</i></a></td>";
	$BookScanList .= "			<td class=\"uk-text-nowrap uk-table-td-center\">".$StrBookScanState."</td>";
	$BookScanList .= "			<td class=\"uk-text-nowrap uk-table-td-center\">";
	$BookScanList .= "				<div class=\"uk-text-nowrap uk-table-td-center\">";
	$BookScanList .= "				<a href=\"javascript:SetBookScanListOrder(".$BookScanID.", 1);\" class=\"top_menu_toggle\"><i class=\"material-icons md-24\" style=\"display:inline-block\" >arrow_drop_up</i></a>";
	$BookScanList .= "				<a href=\"javascript:SetBookScanListOrder(".$BookScanID.", 0);\" class=\"top_menu_toggle\"><i class=\"material-icons md-24\" style=\"display:inline-block\">arrow_drop_down</i></a>";
	$BookScanList .= "				</div>";
	$BookScanList .= "			</td>";
	$BookScanList .= "			<td class=\"uk-text-nowrap uk-table-td-center\">";
	$BookScanList .= "			<a href=\"javascript:OpenContentType('".$BookScanImageFileName."');\">미리보기";
	$BookScanList .= "			</td>";
	$BookScanList .= "		</tr>";

	$ListCount ++;
}
$Stmt = null;



$BookScanList .= "	</tbody>";
$BookScanList .= "</table>";

$BookScanList .= "<div class=\"uk-form-row\" style=\"text-align:center;\">";
$BookScanList .= "	<a type=\"button\" href=\"javascript:OpenBookScanForm('')\" class=\"md-btn md-btn-primary\">신규등록</a>";
$BookScanList .= "</div>";


$ArrValue["BookScanList"] = $BookScanList;

$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');	
}

include_once('../includes/dbclose.php');
?>