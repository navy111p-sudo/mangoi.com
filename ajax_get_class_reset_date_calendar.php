 <?php
header('Content-Type: application/json; charset=UTF-8');
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
include_once('./includes/member_check.php');

$SelDate = isset($_REQUEST["SelDate"]) ? $_REQUEST["SelDate"] : "";
$ArrWeekDayStr = explode(",","일요일,월요일,화요일,수요일,목요일,금요일,토요일");

$today = strtotime($SelDate);
$year = date('Y', $today);
$month = (int)date('m', $today);
$SelTimeWeek = date('w', $today);

$DateTitle = str_replace("-",".",$SelDate)." (".$ArrWeekDayStr[$SelTimeWeek].")";


$p_month = $month - 1;
$n_month = $month + 1;

if ($p_month==0){
	$p_month = 12;
	$p_year = $year-1;
}else{
	$p_year = $year;
}

if ($n_month==13){
	$n_month = 1;
	$n_year = $year+1;
}else{
	$n_year = $year;
}

$time = strtotime($year.'-'.$month.'-01'); 
list($tday, $sweek) = explode('-', date('t-w', $time));  // 총 일수, 시작요일 
$tweek = ceil(($tday + $sweek) / 7);  // 총 주차 
$lweek = date('w', strtotime($year.'-'.$month.'-'.$tday));  // 마지막요일 

$PrevLinkDate = $p_year."-".substr("0".$p_month,-2)."-".date('t', strtotime($p_year."-".$p_month."-01"));
$NextLinkDate = $n_year."-".substr("0".$n_month,-2)."-01";

$SelCalendarHTML = "";
$SelCalendarHTML .= "<h3 class=\"lms_month_caption\">";
$SelCalendarHTML .= "	<a href=\"javascript:GetCalendar('".$PrevLinkDate."')\"><img src=\"images/btn_prev_black.png\" class=\"lms_month_prev\"></a>";
$SelCalendarHTML .= "	".$year.".".substr("0".$month,-2)." ";
$SelCalendarHTML .= "	<a href=\"javascript:GetCalendar('".$NextLinkDate."')\"><img src=\"images/btn_next_black.png\" class=\"lms_month_next\"></a>";
$SelCalendarHTML .= "</h3>";
$SelCalendarHTML .= "<table class=\"lms_month_table\">";
$SelCalendarHTML .= "	<tr>";
$SelCalendarHTML .= "		<th>SUN</th>";
$SelCalendarHTML .= "		<th>MON</th>";
$SelCalendarHTML .= "		<th>TUE</th>";
$SelCalendarHTML .= "		<th>WED</th>";
$SelCalendarHTML .= "		<th>THU</th>";
$SelCalendarHTML .= "		<th>FRI</th>";
$SelCalendarHTML .= "		<th>SAT</th>";
$SelCalendarHTML .= "	</tr>";

$ClassAttendCount = 0;
$ClassTotalCount = 0;

for ($nn=1,$ii=0; $ii<$tweek; $ii++){
	
	$SelCalendarHTML .= "<tr>";

	for ($kk=0; $kk<7; $kk++){
		

		$day2 = $nn;
		$NowDate2 = strtotime($year . "-" . substr("0".$month,-2) . "-" . substr("0".$day2,-2));

		if ($today-$NowDate2==0 && $SelTimeWeek==$kk){
			$UseLink = 0;
			$SelCalendarHTML .= "<td class=\"active\">";
		}else{
			$UseLink = 1;
			$SelCalendarHTML .= "<td>";
		}

		if (($ii == 0 && $kk < $sweek) || ($ii == $tweek-1 && $kk > $lweek)) {
			$SelCalendarHTML .=  "</td>\n";
			continue;
		}

		$day = $nn++;
		$SelectDate = $year . "-" . substr("0".$month,-2) . "-" . substr("0".$day,-2);

		
		if ($UseLink==1){
			$SelCalendarHTML .=  "<a href=\"javascript:GetCalendar('".$SelectDate."');\" style=\"color:#000000;\">".$day."</a>";
		}else{
			$SelCalendarHTML .= $day ;
		}
		$SelCalendarHTML .=  "</td>";

	}
	
	$SelCalendarHTML .=  "</tr>";
}

$SelCalendarHTML .= "</table>";



$ArrValue["DateTitle"] = $DateTitle;
$ArrValue["SelCalendarHTML"] = $SelCalendarHTML;

$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('./includes/dbclose.php');
?>