<?php
header('Content-Type: application/json; charset=UTF-8');
include_once('./includes/dbopen.php');
include_once('./includes/common.php');
include_once('./includes/password_hash.php');

$Type = isset($_REQUEST["Type"]) ? $_REQUEST["Type"] : "";
$Id = isset($_REQUEST["Id"]) ? $_REQUEST["Id"] : "";
$Email = isset($_REQUEST["Email"]) ? $_REQUEST["Email"] : "";
$Name = isset($_REQUEST["Name"]) ? $_REQUEST["Name"] : "";
$AppRegUID = isset($_REQUEST["AppRegUID"]) ? $_REQUEST["AppRegUID"] : "";

$SnsName = "";
if($Type==1) {
    $SnsName = "kakao_";
} else if($Type==2) {
    $SnsName = "naver_";
} else if($Type==3) {
    $SnsName = "google_";
} else if($Type==4) {
    $SnsName = "facebook_";
}

$Id = $SnsName."".$Id;
$Email = $SnsName."".$Email;
$Pw = $Id;
$Pw_hash = password_hash(sha1($Pw), PASSWORD_DEFAULT);

$Sql = "select count(*) as Count from Members where MemberLoginID=:Id";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':Id', $Id);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$Count = $Row["Count"];

if( $Count!=0 ) {
    // 계정이 있으면
    $Sql = "update Members set LastAppLoginDateTime=now() where MemberLoginID=:Id";
    $Stmt = $DbConn->prepare($Sql);
    $Stmt->bindParam(':Id', $Id);
    $Stmt->execute();
    $Stmt = null;
    //$ArrValue["CheckResult"] = "joined";
} else {
    $Sql = "insert into Members (
    `CenterID`, `MemberID`, `MemberLoginType`, `MemberLoginID`, `MemberLoginPW`, `MemberName`, `MemberEmail`, `MemberView`, `MemberState`, `MemberRegDateTime`, `MemberModiDateTime`
    ) values (
    1, NULL, :Type, :Id, :Pw_hash, :Name, HEX(AES_ENCRYPT(:Email, :EncryptionKey)), 1, 1, now(), now()
    )";
    $Stmt = $DbConn->prepare($Sql);
    $Stmt->bindParam(':Id', $Id);
	$Stmt->bindParam(':Type', $Type);
    $Stmt->bindParam(':Email', $Email);
    $Stmt->bindParam(':Name', $Name);
    $Stmt->bindParam(':Pw_hash', $Pw_hash);
    $Stmt->bindParam(':EncryptionKey', $EncryptionKey);
    $Stmt->execute();
    $Stmt = null;
    //$ArrValue["CheckResult"] = "join";
}    

$Sql = "select A.MemberNickName, A.MemberID, A.MemberLevelID from Members A where A.MemberLoginID=:Id";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':Id', $Id);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$MemberID = $Row["MemberID"];
$MemberLevelID = $Row["MemberLevelID"];
$MemberNickName = $Row["MemberNickName"];

InsertPoint(1, 0, $MemberID, "회원가입(앱)", "회원가입(앱)" ,$OnlineSiteMemberRegPoint);

$ArrValue["MemberLevelID"] = $MemberLevelID;
$ArrValue["MemberID"] = $MemberID;
$ArrValue["Id"] = $Id;
$ArrValue["Email"] = $Email;
$ArrValue["Name"] = $Name;
$ArrValue["AppRegUID"] = $AppRegUID;
$ArrValue["MemberNickName"] = $MemberNickName;

$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
    //convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
    array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
    return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('./includes/dbclose.php');

?>
