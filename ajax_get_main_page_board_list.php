<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
$ErrNum = 0;
$ErrMsg = "";

//============== 공지사항 ===============
if ($DomainSiteID == 9) {
    $ContentHTML1 = "";
} else {
    $Sql = "select * from BoardContents where BoardID=1 and BoardContentState=1 order by BoardContentID DESC LIMIT 0,5";
    $Stmt = $DbConn->prepare($Sql);
    $Stmt->execute();
    $Stmt->setFetchMode(PDO::FETCH_ASSOC);

    $ContentHTML1 = "";
    while($Row = $Stmt->fetch()){
        $BoardContentID = $Row["BoardContentID"];
        $BoardContentSubject = $Row["BoardContentSubject"];
        $ContentHTML1 .= "<li>
                            <a href=\"board_read.php?BoardContentID=".$BoardContentID."&BoardCode=notice\">
                            ".$BoardContentSubject."</a>
                        </li>";
    }
}
//============== 공지사항 ===============

//============== 질답 ===============
/*
$Sql = "select * from BoardContents where BoardID=4 and BoardContentState=1 order by BoardContentID DESC LIMIT 0,5";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

$ContentHTML2 = "";
while($Row = $Stmt->fetch()){
	$BoardContentID = $Row["BoardContentID"];
	$BoardContentSubject = $Row["BoardContentSubject"];
	$ContentHTML2 .= "<li>
						<a href=\"board_read.php?BoardContentID=".$BoardContentID."&BoardCode=qna\">
						".$BoardContentSubject."</a>
					</li>";
}
*/


$Sql = "select * from Faqs order by rand() LIMIT 0,5";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

$ContentHTML2 = "";
while($Row = $Stmt->fetch()){
	$FaqTitle = $Row["FaqTitle"];
	$FaqContent = $Row["FaqContent"];
	$ContentHTML2 .= "<li>
						<a href=\"faq.php\">
						".$FaqTitle."</a>
					</li>";
}

//============== 질답 ===============



$ArrValue["ContentHTML1"] = $ContentHTML1;
$ArrValue["ContentHTML2"] = $ContentHTML2;

$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('./includes/dbclose.php');
?>
