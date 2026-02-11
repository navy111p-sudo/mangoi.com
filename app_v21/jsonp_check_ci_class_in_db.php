<?
header('Access-Control-Allow-Origin: *'); 
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');

$ErrNum = 0;
$ErrMsg = "성공";

$AppRegUID = isset($_REQUEST["AppRegUID"]) ? $_REQUEST["AppRegUID"] : "";
$AppID = isset($_REQUEST["AppID"]) ? $_REQUEST["AppID"] : "";
$AppDomain = isset($_REQUEST["AppDomain"]) ? $_REQUEST["AppDomain"] : "";
$AppPath = isset($_REQUEST["AppPath"]) ? $_REQUEST["AppPath"] : "";
$ClassID = isset($_REQUEST["ClassID"]) ? $_REQUEST["ClassID"] : "";
$MemberType = isset($_REQUEST["MemberType"]) ? $_REQUEST["MemberType"] : "";
$CommonCiTelephone = isset($_REQUEST["CommonCiTelephone"]) ? $_REQUEST["CommonCiTelephone"] : "";
$BeginTime = isset($_REQUEST["BeginTime"]) ? $_REQUEST["BeginTime"] : "";
$EndTime = isset($_REQUEST["EndTime"]) ? $_REQUEST["EndTime"] : "";
$callback = isset($_REQUEST["callback"]) ? preg_replace('/[^a-z0-9$_]/si', '', $_REQUEST["callback"]) : false;
$ServerPath = $AppDomain.$AppPath."/";


$Sid = 2351620;
$Secret = "dNG0uoa4";
$TimeStamp = DateToTimestamp(date("Y-m-d H:i:s"), "Asia/Seoul");
$Res = $Secret . $TimeStamp;
$SafeKey = md5($Res);


$Sql = "
	select 
			A.CommonCiCourseID,
			A.CommonCiClassID
		from Classes A 
			inner join Teachers B on A.TeacherID=B.TeacherID 
			inner join Members C on A.MemberID=C.MemberID 
		where A.ClassID=:ClassID 
";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassID', $ClassID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;


$CommonCiCourseID = $Row["CommonCiCourseID"];
$CommonCiClassID = $Row["CommonCiClassID"];


//==== 최종 주소 만들기 ===============
if ($CommonCiCourseID !="" && $CommonCiClassID!=""){


	$Url = "https://www.eeo.cn/partner/api/course.api.php?action=getLoginLinked";
	$Params = array(
		'SID'=> $Sid,
		'safeKey'=> $SafeKey,
		'timeStamp'=> $TimeStamp,
		'telephone'=> $CommonCiTelephone,
		'courseId'=> $CommonCiCourseID,
		'classId'=> $CommonCiClassID
	);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $Url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $Params);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	$result = json_decode(curl_exec($ch), true);
	$ClassRoomUrl = $result['data'];
	curl_close($ch);

}else{
	$ClassRoomUrl = "";
}


//==== 최종 주소 만들기 ===============


$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["ClassRoomUrl"] = $ClassRoomUrl;


$ResultValue = my_json_encode($ArrValue);
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');

function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>