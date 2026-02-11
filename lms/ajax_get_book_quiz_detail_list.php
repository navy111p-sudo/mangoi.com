<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');

$BookQuizID = isset($_REQUEST["BookQuizID"]) ? $_REQUEST["BookQuizID"] : "";


$BookQuizDetailList = "";


$BookQuizDetailList .= "<table class=\"uk-table uk-table-align-vertical\">";
$BookQuizDetailList .= "	<thead>";
$BookQuizDetailList .= "		<tr>";
$BookQuizDetailList .= "			<th width=\"10%\" nowrap>번호</th>";
$BookQuizDetailList .= "			<th width=\"10%\" nowrap>구분</th>";
$BookQuizDetailList .= "			<th nowrap>문제</th>";
$BookQuizDetailList .= "			<th width=\"10%\" nowrap>상태</th>";
$BookQuizDetailList .= "			<th width=\"10%\" nowrap>순서</th>";
$BookQuizDetailList .= "		</tr>";
$BookQuizDetailList .= "	</thead>";
$BookQuizDetailList .= "	<tbody>";

$Sql2 = "
		select 
			A.*
		from BookQuizDetails A
		where A.BookQuizID=:BookQuizID and A.BookQuizDetailState<>0
		order by A.BookQuizDetailOrder asc";
$Stmt2 = $DbConn->prepare($Sql2);
$Stmt2->bindParam(':BookQuizID', $BookQuizID);
$Stmt2->execute();
$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

$ListCount2 = 1;
while($Row2 = $Stmt2->fetch()) {
	$BookQuizDetailID = $Row2["BookQuizDetailID"];
	$BookQuizDetailText = $Row2["BookQuizDetailText"];
	$BookQuizDetailState = $Row2["BookQuizDetailState"];
	$BookQuizDetailQuizType = $Row2["BookQuizDetailQuizType"];

	if ($BookQuizDetailState==1){
		$StrBookQuizDetailState = "<span class=\"ListState_1\">사용</span>";
	}else if ($BookQuizDetailState==2){
		$StrBookQuizDetailState = "<span class=\"ListState_2\">미사용</span>";
	}

	if ($BookQuizDetailQuizType==1) {
		$StrBookQuizDetailQuizType = "일반";
	} else {
		$StrBookQuizDetailQuizType = "듣기";
	}

	$BookQuizDetailList .= "<tr>";
	$BookQuizDetailList .= "	<td class=\"uk-text-nowrap uk-table-td-center\">".$ListCount2."</td>";
	$BookQuizDetailList .= "	<td class=\"uk-text-nowrap uk-table-td-center\">".$StrBookQuizDetailQuizType."</td>";
	$BookQuizDetailList .= "	<td class=\"uk-text-nowrap uk-table-td\"><a href=\"javascript:OpenBookQuizDetailForm(".$BookQuizID.", ".$BookQuizDetailID.");\">".$BookQuizDetailText."</td>";
	$BookQuizDetailList .= "	<td class=\"uk-text-nowrap uk-table-td-center\">".$StrBookQuizDetailState."</td>";
	$BookQuizDetailList .= "		<td class=\"uk-text-nowrap uk-table-td-center\">";
	$BookQuizDetailList .= "			<i onclick=\"SetBookQuizDetailListOrder(".$BookQuizDetailID.", 1, ".$BookQuizID.");\" class=\"material-icons md-24\" style=\"display:inline-block;cursor:pointer;\" >arrow_drop_up</i></a>";
	$BookQuizDetailList .= "			<i onclick=\"SetBookQuizDetailListOrder(".$BookQuizDetailID.", 0, ".$BookQuizID.");\" class=\"material-icons md-24\" style=\"display:inline-block;cursor:pointer;\" >arrow_drop_down</i></a>";
	$BookQuizDetailList .= "		</td>";
	$BookQuizDetailList .= "</tr>";

	$ListCount2 ++;
}
$Stmt2 = null;


$BookQuizDetailList .= "	</tbody>";
$BookQuizDetailList .= "</table>";






$ArrValue["BookQuizDetailList"] = $BookQuizDetailList;

$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('../includes/dbclose.php');
?>