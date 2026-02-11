<?
//결재한 사람수와 결재한 사람수 비교하기
function compareApprovalMemberCount($DocumentReportID){
    global $DbConn;

    $Sql3 = "SELECT COUNT(*) AS totalCount from DocumentReportMembers A 
    inner join Members B on A.MemberID=B.MemberID 
    where A.DocumentReportID=".$DocumentReportID."";
    $Stmt3 = $DbConn->prepare($Sql3);
    $Stmt3->execute();
    $Stmt3->setFetchMode(PDO::FETCH_ASSOC);
    $Row3 = $Stmt3->fetch();
    $totalCount = $Row3["totalCount"];
    
    $Sql3 = "SELECT COUNT(*) AS okCount from DocumentReportMembers A 
                inner join Members B on A.MemberID=B.MemberID 
                where A.DocumentReportID=".$DocumentReportID." AND A.DocumentReportMemberState=1";
    $Stmt3 = $DbConn->prepare($Sql3);
    $Stmt3->execute();
    $Stmt3->setFetchMode(PDO::FETCH_ASSOC);
    $Row3 = $Stmt3->fetch();
    $okCount = $Row3["okCount"];

    if ($totalCount == $okCount) return true;
    else return false;
}

//나 자신 이전에 승인해야 할 멤버가 있는지 확인
function isPrevApproval($DocumentReportID,$MemberID){
    global $DbConn;

    $Sql3 = "SELECT COUNT(*) AS NotApprovalCount from DocumentReportMembers A 
                inner join Members B on A.MemberID=B.MemberID 
                where A.DocumentReportID=".$DocumentReportID." AND A.DocumentReportMemberOrder < 
                (SELECT DocumentReportMemberOrder  FROM DocumentReportMembers WHERE MemberID = ".$MemberID." AND DocumentReportID=".$DocumentReportID.")
                AND A.DocumentReportMemberState <> 1";
    $Stmt3 = $DbConn->prepare($Sql3);
    $Stmt3->execute();
    $Stmt3->setFetchMode(PDO::FETCH_ASSOC);
    $Row3 = $Stmt3->fetch();
    $NotApprovalCount = $Row3["NotApprovalCount"];
    
    if ($NotApprovalCount > 0) return true;
    else return false;
}
?>                        