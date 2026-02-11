 <?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');

$YearMonth = isset($_REQUEST["YearMonth"]) ? $_REQUEST["YearMonth"] : "";
$Comment = isset($_REQUEST["Comment"]) ? $_REQUEST["Comment"] : "";
$Company = isset($_REQUEST["SelectedCompany"]) ? $_REQUEST["SelectedCompany"] : 2;

$Sql = "SELECT count(*) as ExistCount from IncomeStatementComment  
		where YearMonth = :YearMonth AND Company = :Company";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':YearMonth', $YearMonth);
$Stmt->bindParam(':Company', $Company);

$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$ExistCount = $Row["ExistCount"];

if ($ExistCount==0){
	
	$Sql = "INSERT into IncomeStatementComment ( 
				YearMonth, 
				Comment,
				Company,
				CreateDatetime 
			) values ( 
				:YearMonth, 
				:Comment, 
				:Company,
				now() 
			) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':YearMonth', $YearMonth);
	$Stmt->bindParam(':Comment', $Comment);
	$Stmt->bindParam(':Company', $Company);
	$Stmt->execute();
	$Stmt = null;

}else{
	$Sql = "UPDATE IncomeStatementComment 
			set Comment=:Comment 
			where YearMonth=:YearMonth 
				AND Company = :Company";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':YearMonth', $YearMonth);
	$Stmt->bindParam(':Comment', $Comment);
	$Stmt->bindParam(':Company', $Company);

	$Stmt->execute();
	$Stmt = null;
}

$ArrValue["CheckResult"] = "";

$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('../includes/dbclose.php');
?>