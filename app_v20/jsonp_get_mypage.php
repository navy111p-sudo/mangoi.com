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
$LocalLinkMemberID = isset($_REQUEST["LocalLinkMemberID"]) ? $_REQUEST["LocalLinkMemberID"] : "";
$callback = isset($_REQUEST["callback"]) ? preg_replace('/[^a-z0-9$_]/si', '', $_REQUEST["callback"]) : false;
$ServerPath = $AppDomain.$AppPath."/";


$Sql = "
		select 
				sum(MemberPoint) as MemberPoint
		from MemberPoints A 
		where A.MemberID=:MemberID and A.MemberPointState=1 ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $LocalLinkMemberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$MemberPoint = $Row["MemberPoint"];



$Sql = "
		select 
				A.MemberLoginID,
				A.MemberName,
				A.MemberPhoto,
				DATE_FORMAT(A.MemberRegDateTime,'%Y년 %m월 %d일') as MemberRegDateTime
		from Members A 
		where A.MemberID=:MemberID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $LocalLinkMemberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$MemberName = $Row["MemberName"];
$MemberLoginID = $Row["MemberLoginID"];
$MemberRegDateTime = $Row["MemberRegDateTime"];

$MemberPhoto = $Row["MemberPhoto"];

if ($MemberPhoto==""){
	$StrMemberPhoto = $ServerPath."images/no_photo.png";
}else{
	$StrMemberPhoto = $ServerPath."uploads/member_photos/".$MemberPhoto;
}



$NowYear = date("Y");
$NowMonth = date("n");
$NowDay = date("j");


$Sql = "select 
				A.*,
				B.TeacherName

		from Classes A 
			inner join Teachers B on A.TeacherID=B.TeacherID 
		where 
			A.MemberID=:MemberID 
			and A.StartYear=:StartYear 
			and A.StartMonth=:StartMonth 
			and A.StartDay=:StartDay 
		order by A.StartHour asc, A.StartMinute asc limit 0,1";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $LocalLinkMemberID);
$Stmt->bindParam(':StartYear', $NowYear);
$Stmt->bindParam(':StartMonth', $NowMonth);
$Stmt->bindParam(':StartDay', $NowDay);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$StartHour = $Row["StartHour"];
$StartMinute = $Row["StartMinute"];
$TeacherName = $Row["TeacherName"];




$PageMyPageHTML = "";
$PageMyPageHTML .= "
<section class=\"mypage_start_area\">
	<ul class=\"mypage_tabs\">
		<li><a href=\"#\" class=\"active\"><span class=\"bar\"></span>마이 페이지</a></li>
		<li><a href=\"mypage_study_room.html\" class=\"item-link item-content\"><span class=\"bar\"></span>나의 공부방</a></li>
		<li><a href=\"mypage_study_history.html\" class=\"item-link item-content\"><span class=\"bar\"></span>학습 이력</a></li>
	</ul>
	<section class=\"study_today_area\">
		<div class=\"study_today_left\">
			<div class=\"study_today_photo\" style=\"background-image:url(".$StrMemberPhoto.");\"></div>
		</div>
		<div class=\"study_today_right\">
			<h3 class=\"study_today_title ellipsis\">".$MemberName."(".$MemberLoginID.")님</h3>
			<small class=\"study_today_time\">".$MemberRegDateTime."부터 특별한 망고아이와 함께 하고 있습니다.</small>
			<div class=\"study_today_point\">나의 포인트 <b>".number_format($MemberPoint,0)."P</b></div>
			<div class=\"study_today_chart_wrap\">
				<div class=\"study_today_btns\">
					<a href=\"mypage_study_history.html\" class=\"item-link item-content study_today_btn yellow\">학습이력</a>
					<a href=\"#\" class=\"item-link item-content open-popup study_today_btn blue\" data-popup=\".popup-point-list\">포인트내역</a>
					<a href=\"#\" class=\"item-link item-content open-popup study_today_btn pink\" data-popup=\".popup-payment-list\">결제내역</a>
				</div>
			</div>
			<a href=\"mypage_study_room.html\" class=\"my_enter_btn item-link item-content\"><img src=\"images/icon_write_yellow.png\" class=\"icon\"><br>나의 공부방<br>입장하기</a>
		</div>
	</section>

	<table class=\"study_month_banner\">
		<tr>
			<td class=\"study_month_left\">
				<img src=\"images/icon_write_yellow.png\" class=\"icon\">
				<div class=\"study_month_inner\">
					<h3>나의 공부방 입장하기</h3>
					망고아이와 특별한 경험을 체험하세요!
				</div>
			</td>
			<td class=\"study_month_right\">
				<a href=\"mypage_study_room.html\" class=\"item-link item-content\">
					<img src=\"images/bg_ribon.png\" class=\"study_month_bg\">
					<b class=\"study_month_go\">GO</b>
				</a>
			</td>
		</tr>
	</table>

	<h3 class=\"caption_center_underline\">최근학습 <b>평가결과</b></h3>
	<div class=\"study_result\">
		<div class=\"study_result_chart\"><img src=\"images/sample_chart_3.png\" style=\"width:100%; display:block; max-width:410px; margin:0 auto;\"></div>
		<table class=\"study_result_table\">
			<col width=\"\">
			<col width=\"23%\">
			<col width=\"23%\">
			<col width=\"23%\">
			<tr>
				<th class=\"th_yellow\">날짜</th>
				<th class=\"th_yellow\">말하기</th>
				<th class=\"th_yellow\">듣기</th>
				<th class=\"th_yellow\">참여도</th>
			</tr>";

			$Sql = "select 
							A.*,
							date_format(A.AssmtStudentDailyScoreRegDateTime, '%Y.%m.%d') as StrRegDateTime
					from AssmtStudentDailyScores A 
					where 
						A.MemberID=$LocalLinkMemberID
					order by A.AssmtStudentDailyScoreRegDateTime desc
					limit 0, 10
					";

			$Stmt = $DbConn->prepare($Sql);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);

			$ii=1;
			$SumAssmtStudentDailyScore1 = 0;
			$SumAssmtStudentDailyScore2 = 0;
			$SumAssmtStudentDailyScore3 = 0;
			while($Row = $Stmt->fetch()) {
				
				$StrRegDateTime = $Row["StrRegDateTime"];
				$AssmtStudentDailyScore1 = $Row["AssmtStudentDailyScore1"];
				$AssmtStudentDailyScore2 = $Row["AssmtStudentDailyScore2"];
				$AssmtStudentDailyScore3 = $Row["AssmtStudentDailyScore3"];

				$SumAssmtStudentDailyScore1 =  $SumAssmtStudentDailyScore1 + $AssmtStudentDailyScore1;
				$SumAssmtStudentDailyScore2 =  $SumAssmtStudentDailyScore2 + $AssmtStudentDailyScore2;
				$SumAssmtStudentDailyScore3 =  $SumAssmtStudentDailyScore3 + $AssmtStudentDailyScore3;

				$PageMyPageHTML .= "
					<tr>
						<td>".$StrRegDateTime."</td>
						<td>".$AssmtStudentDailyScore1."</td>
						<td>".$AssmtStudentDailyScore2."</td>
						<td>".$AssmtStudentDailyScore3."</td>
					</tr>";

			
				$ii++;
			}
			$Stmt = "";

			if ($ii>1){
				$AvgAssmtStudentDailyScore1 = round($SumAssmtStudentDailyScore1/($ii-1),0);
				$AvgAssmtStudentDailyScore2 = round($SumAssmtStudentDailyScore2/($ii-1),0);
				$AvgAssmtStudentDailyScore3 = round($SumAssmtStudentDailyScore3/($ii-1),0);
			}else{
				$AvgAssmtStudentDailyScore1 = 0;
				$AvgAssmtStudentDailyScore2 = 0;
				$AvgAssmtStudentDailyScore3 = 0;
			}

$PageMyPageHTML .= "
			<tr>
				<th class=\"th_gray_1\">합계</th>
				<th class=\"th_gray_1\">".$SumAssmtStudentDailyScore1."</th>
				<th class=\"th_gray_1\">".$SumAssmtStudentDailyScore2."</th>
				<th class=\"th_gray_1\">".$SumAssmtStudentDailyScore3."</th>
			</tr>
			<tr>
				<th class=\"th_gray_2\">평균</th>
				<th class=\"th_gray_2\">".$AvgAssmtStudentDailyScore1."</th>
				<th class=\"th_gray_2\">".$AvgAssmtStudentDailyScore2."</th>
				<th class=\"th_gray_2\">".$AvgAssmtStudentDailyScore3."</th>
			</tr>
		</table>
	</div>
</section>";





$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["PageMyPageHTML"] = $PageMyPageHTML;


$ResultValue = my_json_encode($ArrValue);
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>