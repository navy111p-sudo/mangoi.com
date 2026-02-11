<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');

$BookID = isset($_REQUEST["BookID"]) ? $_REQUEST["BookID"] : "";


$BookVideoList = "";


$BookVideoList .= "<table class=\"uk-table uk-table-align-vertical\">";
$BookVideoList .= "	<thead>";
$BookVideoList .= "		<tr>";
$BookVideoList .= "			<th width=\"15%\" nowrap>번호</th>";
$BookVideoList .= "			<th nowrap>제목</th>";
$BookVideoList .= "			<th width=\"10%\" nowrap>A타입 영상</th>";
$BookVideoList .= "			<th width=\"10%\" nowrap>B타입 영상</th>";
$BookVideoList .= "			<th width=\"10%\" nowrap>상태</th>";
$BookVideoList .= "			<th width=\"10%\" nowrap>순서</th>";
$BookVideoList .= "		</tr>";
$BookVideoList .= "	</thead>";
$BookVideoList .= "	<tbody>";

$Sql = "
		select 
			A.*
		from BookVideos A
		where A.BookID=:BookID and A.BookVideoState<>0
		order by A.BookVideoOrder asc";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':BookID', $BookID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

$ListCount = 1;
while($Row = $Stmt->fetch()) {
	$BookVideoID = $Row["BookVideoID"];
	$BookVideoName = $Row["BookVideoName"];
	$BookVideoType = $Row["BookVideoType"];
	$BookVideoType2 = $Row["BookVideoType2"];
	$BookVideoCode = $Row["BookVideoCode"];
	$BookVideoCode2 = $Row["BookVideoCode2"];
	$BookVideoState = $Row["BookVideoState"];
	
	if ($BookVideoState==1){
		$StrBookVideoState = "<span class=\"ListState_1\">사용</span>";
	}else if ($BookVideoState==2){
		$StrBookVideoState = "<span class=\"ListState_2\">미사용</span>";
	}

	if ($BookVideoType==1){
		$StrBookVideoType = "Youtube";
	}else{
		$StrBookVideoType = "Vimeo";
	}

	if ($BookVideoType2==1){
		$StrBookVideoType2 = "Youtube";
	}else{
		$StrBookVideoType2 = "Vimeo";
	}




	$BookVideoList .= "		<tr>";
	$BookVideoList .= "			<td class=\"uk-text-nowrap uk-table-td-center\">".$ListCount."</td>";
	$BookVideoList .= "			<td class=\"uk-text-nowrap uk-table-td\"><a href=\"javascript:OpenBookVideoForm(".$BookVideoID.");\">".$BookVideoName."</td>";

	if ($BookVideoCode!=""){
		$BookVideoList .= "			<td class=\"uk-text-nowrap uk-table-td-center\"><a href=\"javascript:OpenVideoPlayer(".$BookVideoType.", '".$BookVideoCode."');\"><i class=\"material-icons\">videocam</i></a></td>";
	}else{
		$BookVideoList .= "			<td class=\"uk-text-nowrap uk-table-td-center\">-</td>";
	}

	if ($BookVideoCode2!=""){
		$BookVideoList .= "			<td class=\"uk-text-nowrap uk-table-td-center\"><a href=\"javascript:OpenVideoPlayer(".$BookVideoType2.", '".$BookVideoCode2."');\"><i class=\"material-icons\">videocam</i></a></td>";
	}else{
		$BookVideoList .= "			<td class=\"uk-text-nowrap uk-table-td-center\">-</td>";
	}

	$BookVideoList .= "			<td class=\"uk-text-nowrap uk-table-td-center\">".$StrBookVideoState."</td>";
	$BookVideoList .= "			<td class=\"uk-text-nowrap uk-table-td-center\">";
	$BookVideoList .= "				<div class=\"uk-text-nowrap uk-table-td-center\">";
	$BookVideoList .= "					<a href=\"javascript:SetBookVideoListOrder(".$BookVideoID.", 1);\" class=\"top_menu_toggle\"><i class=\"material-icons md-24\" style=\"display:inline-block\">arrow_drop_up</i></a>";
	$BookVideoList .= "					<a href=\"javascript:SetBookVideoListOrder(".$BookVideoID.", 0);\" class=\"top_menu_toggle\"><i class=\"material-icons md-24\" style=\"display:inline-block\">arrow_drop_down</i></a>";
	$BookVideoList .= "				</div>";
	$BookVideoList .= "			</td>";
	$BookVideoList .= "		</tr>";

	$ListCount ++;
}
$Stmt = null;



$BookVideoList .= "	</tbody>";
$BookVideoList .= "</table>";

$BookVideoList .= "<div class=\"uk-form-row\" style=\"text-align:center;\">";
$BookVideoList .= "	<a type=\"button\" href=\"javascript:OpenBookVideoForm('')\" class=\"md-btn md-btn-primary\">신규등록</a>";
$BookVideoList .= "</div>";






$ArrValue["BookVideoList"] = $BookVideoList;

$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('../includes/dbclose.php');
?>