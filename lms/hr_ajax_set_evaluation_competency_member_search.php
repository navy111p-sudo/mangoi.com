 <?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');

$QueryResult = 1;
$EvaluationCompetencyMemberID = isset($_REQUEST["EvaluationCompetencyMemberID"]) ? $_REQUEST["EvaluationCompetencyMemberID"] : "";

$Sql = "SELECT A.* from Members A LEFT JOIN Staffs B ON A.StaffID = B.StaffID  
                where B.StaffState = 1  AND A.MemberLoginID = :MemberID AND A.StaffID <> 0";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $EvaluationCompetencyMemberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$MemberID = $Row["MemberID"];
if ($MemberID) {
        $QueryResult = $MemberID;
}
include_once('../includes/dbclose.php');

echo $QueryResult; 
?>