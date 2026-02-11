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
$callback = isset($_REQUEST["callback"]) ? preg_replace('/[^a-z0-9$_]/si', '', $_REQUEST["callback"]) : false;
$ServerPath = $AppDomain.$AppPath."/";




$Sql = "
		select 
			A.*,
			ifnull(A1.TeacherName, '-') as TeacherListBestTeacherName1,
			ifnull(A2.TeacherName, '-') as TeacherListBestTeacherName2,
			ifnull(A3.TeacherName, '-') as TeacherListBestTeacherName3,
			ifnull(A4.TeacherName, '-') as TeacherListBestTeacherName4,
			ifnull(A5.TeacherName, '-') as TeacherListBestTeacherName5,
			ifnull(A6.TeacherName, '-') as TeacherListBestTeacherName6,
			ifnull(A7.TeacherName, '-') as TeacherListBestTeacherName7,
			ifnull(A8.TeacherName, '-') as TeacherListBestTeacherName8,
			ifnull(A9.TeacherName, '-') as TeacherListBestTeacherName9,
			ifnull(A10.TeacherName, '-') as TeacherListBestTeacherName10
		from TeacherListBests A 
			left outer join Teachers A1 on A.TeacherListBestTeacherID1=A1.TeacherID 
			left outer join Teachers A2 on A.TeacherListBestTeacherID2=A2.TeacherID 
			left outer join Teachers A3 on A.TeacherListBestTeacherID3=A3.TeacherID 
			left outer join Teachers A4 on A.TeacherListBestTeacherID4=A4.TeacherID 
			left outer join Teachers A5 on A.TeacherListBestTeacherID5=A5.TeacherID 
			left outer join Teachers A6 on A.TeacherListBestTeacherID6=A6.TeacherID 
			left outer join Teachers A7 on A.TeacherListBestTeacherID7=A7.TeacherID 
			left outer join Teachers A8 on A.TeacherListBestTeacherID8=A8.TeacherID 
			left outer join Teachers A9 on A.TeacherListBestTeacherID9=A8.TeacherID 
			left outer join Teachers A10 on A.TeacherListBestTeacherID10=A10.TeacherID 
		where TeacherListBestState=1 order by A.TeacherListBestID desc limit 0,1 ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':TeacherListBestID', $TeacherListBestID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();

$TeacherListBestName = $Row["TeacherListBestName"];

$TeacherListBestTeacherID[1] = $Row["TeacherListBestTeacherID1"];
$TeacherListBestTeacherID[2] = $Row["TeacherListBestTeacherID2"];
$TeacherListBestTeacherID[3] = $Row["TeacherListBestTeacherID3"];
$TeacherListBestTeacherID[4] = $Row["TeacherListBestTeacherID4"];
$TeacherListBestTeacherID[5] = $Row["TeacherListBestTeacherID5"];
$TeacherListBestTeacherID[6] = $Row["TeacherListBestTeacherID6"];
$TeacherListBestTeacherID[7] = $Row["TeacherListBestTeacherID7"];
$TeacherListBestTeacherID[8] = $Row["TeacherListBestTeacherID8"];
$TeacherListBestTeacherID[9] = $Row["TeacherListBestTeacherID9"];
$TeacherListBestTeacherID[10] = $Row["TeacherListBestTeacherID10"];

$TeacherListBestTeacherName[1] = $Row["TeacherListBestTeacherName1"];
$TeacherListBestTeacherName[2] = $Row["TeacherListBestTeacherName2"];
$TeacherListBestTeacherName[3] = $Row["TeacherListBestTeacherName3"];
$TeacherListBestTeacherName[4] = $Row["TeacherListBestTeacherName4"];
$TeacherListBestTeacherName[5] = $Row["TeacherListBestTeacherName5"];
$TeacherListBestTeacherName[6] = $Row["TeacherListBestTeacherName6"];
$TeacherListBestTeacherName[7] = $Row["TeacherListBestTeacherName7"];
$TeacherListBestTeacherName[8] = $Row["TeacherListBestTeacherName8"];
$TeacherListBestTeacherName[9] = $Row["TeacherListBestTeacherName9"];
$TeacherListBestTeacherName[10] = $Row["TeacherListBestTeacherName10"];


$FlagImage = $AppDomain."/images/no_photo.png";

$PageTeacherListBestHTML = "";
$PageTeacherListBestHTML .= "<h3 class=\"caption_flag\"><img src=\"".$FlagImage."\" class=\"flag\"> ".$TeacherListBestName."</h3>";
$PageTeacherListBestHTML .= "<div class=\"teacher_rank_score\">학생들의 평가점수로 선발</div>";
$PageTeacherListBestHTML .= "<ul class=\"teacher_list\">";


$ListNum = 1;
for ($ii=1;$ii<=10;$ii++) {

	if ($TeacherListBestTeacherID[$ii]!=0){


		$Sql2 = "select 
						A.*,
						ifnull((select sum(MemberPoint) from MemberPoints where MemberID=B.MemberID),0) as MemberPoint
				from Teachers A 
					inner join Members B on A.TeacherID=B.TeacherID 
				where A.TeacherID=:TeacherID";
		$Stmt2 = $DbConn->prepare($Sql2);
		$Stmt2->bindParam(':TeacherID', $TeacherListBestTeacherID[$ii]);
		$Stmt2->execute();
		$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
		$Row2 = $Stmt2->fetch();

		$TeacherID = $Row2["TeacherID"];
		$TeacherName = $Row2["TeacherName"];
		$TeacherImageFileName = $Row2["TeacherImageFileName"];
		$TeacherIntroText = $Row2["TeacherIntroText"];
		$TeacherVideoType = $Row2["TeacherVideoType"];
		$TeacherVideoCode = $Row2["TeacherVideoCode"];

		$TeacherIntroSpec = $Row2["TeacherIntroSpec"];

		$MemberPoint = $Row2["MemberPoint"];

		if ($TeacherImageFileName==""){
			$StrTeacherImageFileName = $AppDomain."/images/no_photo.png";
		}else{
			$StrTeacherImageFileName = $AppDomain."/uploads/teacher_images/".$TeacherImageFileName;
		}

		$PageTeacherListBestHTML .= "<li>";
		$PageTeacherListBestHTML .= "<a href=\"#\" class=\"teacher_btn_orange\" onclick=\"OpenTeacherVideo('".$TeacherVideoType."','".$TeacherVideoCode."')\">인사영상</a>";
		$PageTeacherListBestHTML .= "<a href=\"#\" data-picker=\".picker_best_teacher\" class=\"open-picker link\" onclick=\"OpenTeacherPointPicker('".$TeacherName."', '".$StrTeacherImageFileName."', '".number_format($MemberPoint,0)."점')\">";
		$PageTeacherListBestHTML .= "	<div class=\"teacher_top\">";
		$PageTeacherListBestHTML .= "		<div class=\"teacher_photo\" style=\"background-image:url(".$StrTeacherImageFileName.");\">";
		$PageTeacherListBestHTML .= "			<span class=\"teacher_rank\"><b>".$ListNum."</b>위</span>";
		$PageTeacherListBestHTML .= "		</div>";

		$PageTeacherListBestHTML .= "		<div class=\"teacher_left\">";
		$PageTeacherListBestHTML .= "			<div class=\"teacher_name\">".$TeacherName."</div>";
		$PageTeacherListBestHTML .= "			<div class=\"teacher_edu\">".$TeacherIntroSpec."</div>";
		$PageTeacherListBestHTML .= "		</div>";
		$PageTeacherListBestHTML .= "	</div>";
		$PageTeacherListBestHTML .= "	<div class=\"teacher_bottom\">";
		$PageTeacherListBestHTML .= "		<div class=\"teacher_comment\">";
		$PageTeacherListBestHTML .= "			".$TeacherIntroText." ";
		$PageTeacherListBestHTML .= "		</div>";
		$PageTeacherListBestHTML .= "		<div class=\"teacher_chart\">";
		$PageTeacherListBestHTML .= "			<!--<img src=\"images/sample_teacher_chart_1.png\" style=\"width:100%;\">-->";
		$PageTeacherListBestHTML .= "		</div>";
		$PageTeacherListBestHTML .= "	</div>";
		$PageTeacherListBestHTML .= "</a>";
		$PageTeacherListBestHTML .= "</li>";

		/*
		$PageTeacherListBestHTML .= "<li>";
		$PageTeacherListBestHTML .= "	<div class=\"teacher_top\">";
		$PageTeacherListBestHTML .= "		<div class=\"teacher_photo\" style=\"background-image:url(".$StrTeacherImageFileName.");\"></div>";
		$PageTeacherListBestHTML .= "		<a href=\"#\" class=\"teacher_btn_orange\" onclick=\"OpenTeacherVideo('".$TeacherVideoType."','".$TeacherVideoCode."')\">인사영상</a>";
		$PageTeacherListBestHTML .= "		<div class=\"teacher_left\">";
		$PageTeacherListBestHTML .= "			<div class=\"teacher_name\">".$TeacherName."</div>";
		$PageTeacherListBestHTML .= "			<div class=\"teacher_edu\">".$TeacherIntroSpec."</div>";
		$PageTeacherListBestHTML .= "		</div>";
		$PageTeacherListBestHTML .= "	</div>";
		$PageTeacherListBestHTML .= "	<div class=\"teacher_bottom\">";
		$PageTeacherListBestHTML .= "		<div class=\"teacher_comment\">";
		$PageTeacherListBestHTML .= "			".$TeacherIntroText." ";
		$PageTeacherListBestHTML .= "		</div>";
		$PageTeacherListBestHTML .= "		<div class=\"teacher_chart\">";
		$PageTeacherListBestHTML .= "			<!--<img src=\"images/sample_teacher_chart_1.png\" style=\"width:100%;\">-->";
		$PageTeacherListBestHTML .= "		</div>";
		$PageTeacherListBestHTML .= "	</div>";
		$PageTeacherListBestHTML .= "</li>";
		*/
	
	}
	
	$ListNum++;
}
$PageTeacherListBestHTML .= "</ul>";



$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["PageTeacherListBestHTML"] = $PageTeacherListBestHTML;


$ResultValue = my_json_encode($ArrValue);
//echo $_GET['callback'] . "(".(string)$ResultValue.")"; 
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>