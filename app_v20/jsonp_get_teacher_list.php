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


/*
$ScoreItemID = [];
$ScoreItemName = [];
$ScoreItemScore = [];
$Sql = "select * from TeacherAssessmentItems where TeacherAssessmentItemState=1 and TeacherAssessmentItemView=1 order by TeacherAssessmentItemOrder asc";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$ScoreItemCount=0;
while($Row = $Stmt->fetch()) {
	$ScoreItemID[$ScoreItemCount+1]=$Row["TeacherAssessmentItemID"];
	$ScoreItemName[$ScoreItemCount+1]=$Row["TeacherAssessmentItemTitle"];
	$ScoreItemScore[$ScoreItemCount+1]=0;
	$ScoreItemCount++;
}
$Stmt = null;
*/

$Sql = "
		select 
			A.*
		from TeacherPayTypeItems A 
		where A.TeacherPayTypeItemState=1 order by A.TeacherPayTypeItemOrder asc";//." limit $StartRowNum, $PageListNum";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);


$PageTeacherListHTML = "";

$ListNum = 1;
while($Row = $Stmt->fetch()) {

	$TeacherPayTypeItemID = $Row["TeacherPayTypeItemID"];
	$TeacherPayTypeItemTitle = $Row["TeacherPayTypeItemTitle"];

	$FlagImage = $AppDomain."/images/no_photo.png";
	if ($TeacherPayTypeItemID==1){
		$FlagImage = $AppDomain.$AppPath."/images/flag_phi.png";
	}else if ($TeacherPayTypeItemID==2){
		$FlagImage = $AppDomain.$AppPath."/images/flag_usa.png";
	}

	$PageTeacherListHTML .= "<h3 class=\"caption_flag\"><img src=\"".$FlagImage."\" class=\"flag\"> ".$TeacherPayTypeItemTitle."</h3>";
	$PageTeacherListHTML .= "<ul class=\"teacher_list\">";
	
	
	
	$Sql2 = "
			select 
				A.*
			from Teachers A 

			where A.TeacherPayTypeItemID=:TeacherPayTypeItemID and A.TeacherState=1 and A.TeacherView=1 
			order by A.TeacherOrder desc";
	$Stmt2 = $DbConn->prepare($Sql2);
	$Stmt2->bindParam(':TeacherPayTypeItemID', $TeacherPayTypeItemID);
	$Stmt2->execute();
	$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

	while($Row2 = $Stmt2->fetch()) {

		$TeacherID = $Row2["TeacherID"];
		$TeacherName = $Row2["TeacherName"];
		$TeacherImageFileName = $Row2["TeacherImageFileName"];
		$TeacherIntroText = $Row2["TeacherIntroText"];
		$TeacherVideoType = $Row2["TeacherVideoType"];
		$TeacherVideoCode = $Row2["TeacherVideoCode"];

		$TeacherIntroSpec = $Row2["TeacherIntroSpec"];

		if ($TeacherImageFileName==""){
			$StrTeacherImageFileName = $AppDomain."/images/no_photo.png";
		}else{
			$StrTeacherImageFileName = $AppDomain."/uploads/teacher_images/".$TeacherImageFileName;
		}

		/*
		for ($ii=1;$ii<=$ScoreItemCount;$ii++){

			$Sql3 = "select ifnull(avg(TeacherAssessmentScore),0) as Score from TeacherAssessmentResultDetails where TeacherID=$TeacherID";
			$Stmt3 = $DbConn->prepare($Sql3);
			$Stmt3->execute();
			$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
			$Row3 = $Stmt3->fetch();
			$ScoreItemScore[$ii] = $Row3["Score"];
		}
		*/

		$PageTeacherListHTML .= "<li>";
		$PageTeacherListHTML .= "<a href=\"#\" class=\"teacher_btn_orange\" onclick=\"OpenTeacherVideo('".$TeacherVideoType."','".$TeacherVideoCode."')\">인사영상</a>";
		//$PageTeacherListHTML .= "<a href=\"#\" data-picker=\".picker_best_teacher\" class=\"open-picker link\">";
		$PageTeacherListHTML .= "	<div class=\"teacher_top\">";
		$PageTeacherListHTML .= "		<div class=\"teacher_photo\" style=\"background-image:url(".$StrTeacherImageFileName.");\">";
		//$PageTeacherListHTML .= "			<span class=\"teacher_rank\"><b>1</b>위</span>";
		$PageTeacherListHTML .= "		</div>";

		$PageTeacherListHTML .= "		<div class=\"teacher_left\">";
		$PageTeacherListHTML .= "			<div class=\"teacher_name\">".$TeacherName."</div>";
		$PageTeacherListHTML .= "			<div class=\"teacher_edu\">".$TeacherIntroSpec."</div>";
		$PageTeacherListHTML .= "		</div>";
		$PageTeacherListHTML .= "	</div>";
		$PageTeacherListHTML .= "	<div class=\"teacher_bottom\">";
		$PageTeacherListHTML .= "		<div class=\"teacher_comment\">";
		$PageTeacherListHTML .= "			".$TeacherIntroText." ";
		$PageTeacherListHTML .= "		</div>";
		$PageTeacherListHTML .= "		<div class=\"teacher_chart\">";
		$PageTeacherListHTML .= "			<!--<img src=\"images/sample_teacher_chart_1.png\" style=\"width:100%;\">-->";
		$PageTeacherListHTML .= "		</div>";
		$PageTeacherListHTML .= "	</div>";
		//$PageTeacherListHTML .= "</a>";
		$PageTeacherListHTML .= "</li>";

		/*
		$PageTeacherListHTML .= "<li>";
		$PageTeacherListHTML .= "	<div class=\"teacher_top\">";
		$PageTeacherListHTML .= "		<div class=\"teacher_photo\" style=\"background-image:url(".$StrTeacherImageFileName.");\"></div>";
		$PageTeacherListHTML .= "		<a href=\"#\" class=\"teacher_btn_orange\" onclick=\"OpenTeacherVideo('".$TeacherVideoType."','".$TeacherVideoCode."')\">인사영상</a>";
		$PageTeacherListHTML .= "		<div class=\"teacher_left\">";
		$PageTeacherListHTML .= "			<div class=\"teacher_name\">".$TeacherName."</div>";
		$PageTeacherListHTML .= "			<div class=\"teacher_edu\">".$TeacherIntroSpec."</div>";
		$PageTeacherListHTML .= "		</div>";
		$PageTeacherListHTML .= "	</div>";
		$PageTeacherListHTML .= "	<div class=\"teacher_bottom\">";
		$PageTeacherListHTML .= "		<div class=\"teacher_comment\">";
		$PageTeacherListHTML .= "			".$TeacherIntroText." ";
		$PageTeacherListHTML .= "		</div>";
		$PageTeacherListHTML .= "		<div class=\"teacher_chart\">";
		$PageTeacherListHTML .= "			<!--<img src=\"images/sample_teacher_chart_1.png\" style=\"width:100%;\">-->";
		$PageTeacherListHTML .= "		</div>";
		$PageTeacherListHTML .= "	</div>";
		$PageTeacherListHTML .= "</li>";
		*/

	
	}
	$PageTeacherListHTML .= "</ul>";


	$ListNum++;
}




$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["PageTeacherListHTML"] = $PageTeacherListHTML;


$ResultValue = my_json_encode($ArrValue);
//echo $_GET['callback'] . "(".(string)$ResultValue.")"; 
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>