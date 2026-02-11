<?
// 관리부서를 불러와서 배열에 대입한다.
function getDepartments($LangID){
    global $DbConn;
    $departments = [];

    $Sql = "SELECT
                * 
                from Departments 
                WHERE InUse = 1";
    $Stmt = $DbConn->prepare($Sql);
    $Stmt->execute();
    $Stmt->setFetchMode(PDO::FETCH_ASSOC);
    
    while($Row = $Stmt->fetch()){
        if ($LangID == 0) {
            $departments[$Row['DepartmentID']] =  $Row['DepartmentName'];
        } else {
            $departments[$Row['DepartmentID']] =  $Row['DepartmentNameEng'];
        }    
    }
    $departments[0] = "미정";
    return $departments;
}

?>