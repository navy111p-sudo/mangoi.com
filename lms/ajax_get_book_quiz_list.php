<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');

$BookID = isset($_REQUEST["BookID"]) ? $_REQUEST["BookID"] : "";


$BookQuizList = "";


$BookQuizList .= "<div class=\"md-card-content\">";
$BookQuizList .= "	<div class=\"uk-accordion\"  data-uk-accordion=\"{showfirst: false}\">";

$Sql = "
		select 
			A.*
		from BookQuizs A
		where A.BookID=:BookID and A.BookQuizState<>0
		order by A.BookQuizOrder asc";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':BookID', $BookID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

$ListCount = 1;
while($Row = $Stmt->fetch()) {
	$BookQuizID = $Row["BookQuizID"];
	$BookQuizName = $Row["BookQuizName"];
	$BookQuizState = $Row["BookQuizState"];
	
	if ($BookQuizState==1){
		$StrBookQuizName = "<span class=\"ListState_1\">".$BookQuizName."</span>";
	}else if ($BookQuizState==2){
		$StrBookQuizName = "<span class=\"ListState_2\">".$BookQuizName."(미사용)</span>";
	}



	$BookQuizList .= "<h3 class=\"uk-accordion-title\">";
	$BookQuizList .= "".$StrBookQuizName." ";
	$BookQuizList .= "</h3>";

	$BookQuizList .= "<div class=\"uk-accordion-content\">";
	$BookQuizList .= "	<div>";
	$BookQuizList .= "		<div style=\"display:inline-block;width:50%;text-align:left;\"><a class=\"md-btn md-btn-primary md-btn-small md-btn-wave-light\" href=\"javascript:OpenBookQuizForm(".$BookQuizID.");\">퀴즈그룹수정</a></div>";
	$BookQuizList .= "		<div style=\"display:inline-block;width:49%;text-align:right;\">";
	$BookQuizList .= "			<a class=\"md-btn md-btn-primary md-btn-small md-btn-wave-light\" href=\"javascript:SetBookQuizListOrder(".$BookQuizID.", 1, ".$BookID.");\"><i class=\"material-icons md-24\" style=\"display:block\" >arrow_drop_up</i></a>";
	$BookQuizList .= "			<a class=\"md-btn md-btn-primary md-btn-small md-btn-wave-light\" href=\"javascript:SetBookQuizListOrder(".$BookQuizID.", 0, ".$BookID.");\"><i class=\"material-icons md-24\" style=\"display:block\" >arrow_drop_down</i></a>";
	$BookQuizList .= "		</div>";
	$BookQuizList .= "	</div>";
	$BookQuizList .= "	<div id=\"BookQuizDetailList_".$BookQuizID."\" style=\"margin-top:20px;\">";
					
	$BookQuizList .= "		<table class=\"uk-table uk-table-align-vertical\">";
	$BookQuizList .= "			<thead>";
	$BookQuizList .= "				<tr>";
	$BookQuizList .= "					<th width=\"10%\" nowrap>번호</th>";
	$BookQuizList .= "					<th width=\"10%\" nowrap>구분</th>";
	$BookQuizList .= "					<th>문제</th>";
	$BookQuizList .= "					<th width=\"10%\" nowrap>상태</th>";
	$BookQuizList .= "					<th width=\"10%\" nowrap>순서</th>";
	$BookQuizList .= "				</tr>";
	$BookQuizList .= "			</thead>";
	$BookQuizList .= "			<tbody>";

	
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
				
		$BookQuizList .= "	<tr>";
		$BookQuizList .= "		<td class=\"uk-text-nowrap uk-table-td-center\">".$ListCount2."</td>";
		$BookQuizList .= "		<td class=\"uk-text-nowrap uk-table-td-center\">".$StrBookQuizDetailQuizType."</td>";
		$BookQuizList .= "		<td class=\"uk-text-nowrap uk-table-td\"><a href=\"javascript:OpenBookQuizDetailForm(".$BookQuizID.", ".$BookQuizDetailID.");\">".$BookQuizDetailText."</td>";
		$BookQuizList .= "		<td class=\"uk-text-nowrap uk-table-td-center\">".$StrBookQuizDetailState."</td>";
		$BookQuizList .= "		<td class=\"uk-text-nowrap uk-table-td-center\">";
		$BookQuizList .= "			<i onclick=\"SetBookQuizDetailListOrder(".$BookQuizDetailID.", 1,".$BookQuizID.");\" class=\"material-icons md-24\" style=\"display:inline-block;cursor:pointer;\" >arrow_drop_up</i>";
		$BookQuizList .= "			<i onclick=\"SetBookQuizDetailListOrder(".$BookQuizDetailID.", 0,".$BookQuizID.");\" class=\"material-icons md-24\" style=\"display:inline-block;cursor:pointer;\" >arrow_drop_down</i>";
		$BookQuizList .= "		</td>";
		$BookQuizList .= "	</tr>";
		
		$ListCount2 ++;
	}
	$Stmt2 = null;



	$BookQuizList .= "			</tbody>";
	$BookQuizList .= "		</table>";
	$BookQuizList .= "	</div>";
	$BookQuizList .= "	<div style=\"text-align:center;\">";
	$BookQuizList .= "		<a class=\"md-btn md-btn-primary md-btn-small md-btn-wave-light\" href=\"javascript:OpenBookQuizDetailForm(".$BookQuizID.", '');\">문제추가</a>";
	$BookQuizList .= "	</div>";
	$BookQuizList .= "</div>";

	$ListCount ++;
}
$Stmt = null;

if ($ListCount==1){
	$BookQuizList .= "<h3 class=\"uk-accordion-title\">";
	$BookQuizList .= "	<div style=\"text-align:center;\">";
	$BookQuizList .= "	아래 신규등록 버튼을 눌러 퀴즈를 추가해 주세요. ";
	$BookQuizList .= "	</div>";
	$BookQuizList .= "</h3>";
	$BookQuizList .= "<div class=\"uk-accordion-content\"></div>";
}

$BookQuizList .= "	</div>";
$BookQuizList .= "</div>";


$BookQuizList .= "<div class=\"uk-form-row\" style=\"text-align:center;\">";
$BookQuizList .= "	<a type=\"button\" href=\"javascript:OpenBookQuizForm('')\" class=\"md-btn md-btn-primary\">퀴즈그룹 신규등록</a>";
$BookQuizList .= "</div>";


$ArrValue["BookQuizList"] = $BookQuizList;

$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('../includes/dbclose.php');
?>