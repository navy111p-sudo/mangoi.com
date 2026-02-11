<?php
//=======================================================================================================
$EncryptionKey = "f2ffe2af6c94ba5c3b56b69658f5e471";//절대 변경 불가(변경되면 회원정보 복구 불가)
//=======================================================================================================
date_default_timezone_set('Asia/Seoul');

//=======================================================================================================
// Sentry
require '/home/hosting_users/mangoi/www/vendor/autoload.php';

\Sentry\init([
//    'dsn' => 'https://645bfcdeeab311d0f88abe8cfdb44396@o4507340614991872.ingest.us.sentry.io/4507340616171520',
    'dsn' => 'https://b54b113cf958c1cf9a890d1431ff0ae3@sentry.datacredit.kr/2',
    // Specify a fixed sample rate
    'traces_sample_rate' => 1.0,
    // Set a sampling rate for profiling - this is relative to traces_sample_rate
    'profiles_sample_rate' => 1.0,
    'attach_metric_code_locations' => true,
//    'traces_sampler' => function (\Sentry\Tracing\SamplingContext $context): float {
//        // return a number between 0 and 1
//    },]);
]);
//=======================================================================================================


// http 리다이렉트
/*
$allowed_hosts = array("slpmangoi.com");
if(!isset($_SERVER["HTTPS"]) && !in_array($_SERVER["HTTP_HOST"], $allowed_hosts)) {
	header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"], true, 301);
	exit;
} else if(isset($_SERVER["HTTPS"]) && in_array($_SERVER["HTTP_HOST"], $allowed_hosts)) {
	header("Location: http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"], true, 301);
	exit;
}
*/

if(!isset($_SERVER["HTTPS"])) {
	header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"], true, 301);
	exit;
}

$Sql = "select SiteName, SiteTitle, SiteFavicon, SmsID, SiteSkin,
		AES_DECRYPT(UNHEX(SmsNumber),'$EncryptionKey') as SmsNumber,
		AES_DECRYPT(UNHEX(SmsPW),'$EncryptionKey') as SmsPW,
		AES_DECRYPT(UNHEX(AdminMail),'$EncryptionKey') as AdminMail,
		AES_DECRYPT(UNHEX(AdminPhone),'$EncryptionKey') as AdminPhone

		from SiteSetup where Seq='1'";


$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$_SITE_TITLE_ = $Row["SiteTitle"];
$_SITE_ADM_TITLE_ = $Row["SiteTitle"] . " :: 홈페이지 관리자";
$_SITE_LMS_TITLE_ =  $Row["SiteTitle"] . "";
$_SITE_FAVICON_ = $Row["SiteFavicon"];
$_SITE_SMS_ID_ =  $Row["SmsID"];
$_SITE_SMS_PW_ = $Row["SmsPW"];
$_SITE_SMS_NUMBER_ = $Row["SmsNumber"];
$_SITE_PAGE_SKIN_ = $Row["SiteSkin"];
$AdminReturnEmail = "jangjiwoong@mangoi.com"; //관리자 이메일

//== 팝빌 세금계산서 코드 =========
$Popbill_LinkID = "MANGOI";
$Popbill_SecretKey = "AydfTev0YxfKuNUrRbhLvQ1a0TX6MfrRCnCIe6P64ZI=";
//== 팝빌 세금계산서 코드 =========


//=======================================================================================================
$OnlineSiteID = 1;//독립 사이트 아이디

//0 이면 단체 연장을 하지 않아도 수업을 계속한다.(과도기) 최종적으로는 1으로 해야한다. ***** 이부분 수정할때 app_push_cron 파일도 반드시 수정해 줘야 한다.
$NoIgnoreCenterRenew = 0;
//0 이면 단체 연장을 하지 않아도 수업을 계속한다.(과도기) 최종적으로는 1으로 해야한다. ***** 이부분 수정할때 app_push_cron 파일도 반드시 수정해 줘야 한다.



$Sql = "select 
				*
		from OnlineSites 
		where OnlineSiteID=$OnlineSiteID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$OnlineSiteMemberRegPoint = $Row["OnlineSiteMemberRegPoint"];
$OnlineSiteMemberLoginPoint = $Row["OnlineSiteMemberLoginPoint"];
$OnlineSitePaymentPointRatio = $Row["OnlineSitePaymentPointRatio"];
$OnlineSiteStudyPoint = $Row["OnlineSiteStudyPoint"];
$OnlineSitePreStudyPoint = $Row["OnlineSitePreStudyPoint"];
$OnlineSiteReStudyPoint = $Row["OnlineSiteReStudyPoint"];
$OnlineSiteTeacherAssmtPoint = $Row["OnlineSiteTeacherAssmtPoint"];

$OnlineSitePgCardFeeRatio = $Row["OnlineSitePgCardFeeRatio"];
$OnlineSitePgDirectFeePrice = $Row["OnlineSitePgDirectFeePrice"];
$OnlineSitePgDirectFeeRatio = $Row["OnlineSitePgDirectFeeRatio"];
$OnlineSitePgVBankFeePrice = $Row["OnlineSitePgVBankFeePrice"];

$OnlineSiteShipPrice = $Row["OnlineSiteShipPrice"];

$OnlineSiteGuideVideoType = $Row["OnlineSiteGuideVideoType"];
$OnlineSiteGuideVideoCode = $Row["OnlineSiteGuideVideoCode"];

//=======================================================================================================
$http_host = $_SERVER['HTTP_HOST'];
$request_uri = $_SERVER['REQUEST_URI'];
$DomainSiteID = 0;//본사


$BookOrderNumberHeader = "BP";

if ( strpos($http_host, "slpmangoi.com") !== false ) {

	$DomainSiteID = 1;//SLP

	$DefaultDomain = "www.slpmangoi.com";
	$DefaultDomain2 = "slpmangoi.com";

}else if ( strpos($http_host, "eie.mangoi.co.kr") !== false ) {
	
	$DomainSiteID = 2;//EIE

	$DefaultDomain = "eie.mangoi.co.kr";
	$DefaultDomain2 = "eie.mangoi.co.kr";

}else if ( strpos($http_host, "dream.mangoi.co.kr") !== false ) {

	$DomainSiteID = 3;//DREAM

	$DefaultDomain = "dream.mangoi.co.kr";
	$DefaultDomain2 = "dream.mangoi.co.kr";

}else if ( strpos($http_host, "thomas.mangoi.co.kr") !== false ) {

	$DomainSiteID = 4;//THOMAS

	$DefaultDomain = "thomas.mangoi.co.kr";
	$DefaultDomain2 = "thomas.mangoi.co.kr";

}else if ( strpos($http_host, "englishtell.co.kr") !== false ) {
	
	$DomainSiteID = 5;//ENGLISHTELL

	$DefaultDomain = "www.englishtell.co.kr";
	$DefaultDomain2 = "englishtell.co.kr";

}else if ( strpos($http_host, "hi.mangoi.co.kr") !== false ) {
	
	$DomainSiteID = 6;//HI ( 토마스를 여기로 옮긴다고 함 )

	$DefaultDomain = "hi.mangoi.co.kr";
	$DefaultDomain2 = "hi.mangoi.co.kr";

}else if ( strpos($http_host, "gumiivyleagueenglish.co.kr") !== false ) {

    $DomainSiteID = 7;//gumiivyleague

    $DefaultDomain = "gumiivyleagueenglish.co.kr";
    $DefaultDomain2 = "gumiivyleagueenglish.co.kr";

}else if ( strpos($http_host, "engliseed.kr") !== false ) {

    $DomainSiteID = 8;//engliseed.kr

    $DefaultDomain = "engliseed.kr";
    $DefaultDomain2 = "engliseed.kr";


}else if ( strpos($http_host, "live.engedu.kr") !== false ) {

    $DomainSiteID = 9;//live.engedu.kr

    $DefaultDomain = "live.engedu.kr";
    $DefaultDomain2 = "live.engedu.kr";

}else {
	
	$DomainSiteID = 0;//본사

	$DefaultDomain = "www.mangoi.co.kr";
	$DefaultDomain2 = "mangoi.co.kr";

}

//ENGLISHTELL, THOMAS 전용 페이지로 이동하기
$url = 'http://' . $http_host . $request_uri;

if ( strpos($url, "/lms/") != false){
	$SsoNoRedirct = "1";
}

if( $DomainSiteID == 7){
	$_SITE_TITLE_ = "즐거운 화상 영어 아이비리그에 오신걸 환영합니다";
}elseif ( $DomainSiteID == 8){
    $_SITE_TITLE_ = "즐거운 화상 영어 잉글리씨드에 오신걸 환영합니다";
}elseif ( $DomainSiteID == 9){
    $_SITE_TITLE_ = "즐거운 이엔지 화상 영어에 오신걸 환영합니다";
}


if ( $DomainSiteID==5 && strpos($url, "/mypage/") === false ) {//잉글리시텔
	$SsoNoRedirct = isset($SsoNoRedirct) ? $SsoNoRedirct : "";
	if ($SsoNoRedirct!="1"){
		header("Location: /mypage/");
		exit;
	}
}else if ($DomainSiteID==4 && strpos($url, "/mypage/") === false){//토마스

	$SsoNoRedirct = isset($SsoNoRedirct) ? $SsoNoRedirct : "";
	if ($SsoNoRedirct!="1"){
		$ThomasSSO = isset($_REQUEST["sso"]) ? $_REQUEST["sso"] : "";
		header("Location: member_direction_thomas_action.php?sso=".$ThomasSSO);
		exit;
	}
}


$SsoDomainSite = 0;
if ( ($DomainSiteID==4 || $DomainSiteID==5) && strpos($url, "/mypage/") != false){ //잉글리시텔, 토마스
	$SsoDomainSite = 1;
}
//ENGLISHTELL, THOMAS 전용 페이지로 이동하기


//=======================================================================================================





$OG_Title = "";
$OG_Keywords = "";
$OG_Description = "";
//==============================================================


#====================================================================================================================================#
# cURL stdClass 
#------------------------------------------------------------------------------------------------------------------------------------#
class stdObject {
    public function __construct(array $arguments = array()) {
        if (!empty($arguments)) {
            foreach ($arguments as $property => $argument) {
                $this->{$property} = $argument;
            }
        }
    }

    public function __call($method, $arguments) {
        $arguments = array_merge(array("stdObject" => $this), $arguments); // Note: method argument 0 will always referred to the main class ($this).
        if (isset($this->{$method}) && is_callable($this->{$method})) {
            return call_user_func_array($this->{$method}, $arguments);
        } else {
            throw new Exception("Fatal error: Call to undefined method stdObject::{$method}()");
        }
    }
}
#====================================================================================================================================#
function getUTFtoKR($str){
   return iconv('utf-8','euc-kr',$str);
}


function ConvAmPm($OldClassStartTime){//20:30 ==> pm 08:30
	$ArrOldClassStartTime = explode(":", $OldClassStartTime);
	
	if ( $ArrOldClassStartTime[0] > 12 ){
		$NewClassStartTime = "pm ".substr("0".((int)$ArrOldClassStartTime[0]-12), -2).":".$ArrOldClassStartTime[1];
	}else{
		$NewClassStartTime = "am ".$OldClassStartTime;
	}

	return $NewClassStartTime;
}


//좌표로 거리계산
function GetDistance($lat1, $lng1, $lat2, $lng2)
{
    $earth_radius = 6371;
    $dLat = deg2rad(doubleval($lat2) - doubleval($lat1));
    $dLon = deg2rad(doubleval($lng2) - doubleval($lng1));
    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad(doubleval($lat1))) * cos(deg2rad(doubleval($lat2))) * sin($dLon/2) * sin($dLon/2);
    $c = 2 * asin(sqrt($a));
    $d = $earth_radius * $c;
    return $d;
}


function getSendMail($to,$from,$subject,$content,$html) {

	if ($from==""){
		$from = "jangjiwoong@mangoi.com|망고아이";
	}

	if ($html == 'TEXT') $content = nl2br(htmlspecialchars($content));
	$to_exp   = explode('|', $to);
	$from_exp = explode('|', $from);

	$charset='UTF-8';//아솔서버 추가
	
	$To = $to_exp[1] ? "\"".getUTFtoKR($to_exp[1])."\" <$to_exp[0]>" : $to_exp[0];
	//$Frm = $from_exp[1] ? "\"".getUTFtoKR($from_exp[1])."\" <$from_exp[0]>" : $from_exp[0];
	$Frm= "\"=?".$charset."?B?".base64_encode($from_exp[1])."?=\" <".$from_exp[0].">" ;//아솔서버 추가

	$subject = "=?".$charset."?B?".base64_encode($subject)."?= ";//아솔서버 추가
	$Header = "From:$Frm\nReply-To:$Frm\nX-Mailer:PHP/".phpversion();
	$Header.= "\nContent-Type:text/html;charset=UTF-8\r\n"; 
	return @mail($To,$subject,$content,$Header);
}


function convertRequest($html){
	$rhtml = $html;
	//$rhtml = addslashes($rhtml);
	return $rhtml;
}



function convertHTML($html){
	$html = convertPiece($html);
	$html = convertRecent($html);
	$html = convertLogin($html);
	$html = convertCode($html);
	
	$html = str_replace("{{textarea", "<textarea", $html);
	$html = str_replace("textarea}}", "textarea>", $html);
	$html = str_ireplace("pagecode", "PageCode", $html);
	$html = str_ireplace("boardcode", "BoardCode", $html);

	return $html;
}


function convertPiece($html){
	if ($html!=""){
		
		$pattern = '/\{\{Piece\(([^)]*)\)\}\}/';
		preg_match_all($pattern, $html, $matches, PREG_SET_ORDER);

		for ($pat_i=0; $pat_i<count($matches); $pat_i++) {

			$PieceCodePatten = $matches[$pat_i][0];
			$PieceCode = $matches[$pat_i][1];
			
			$Sql = "select count(*) as ExistCount from Pieces where PieceCode=:PieceCode";
			$Stmt = $GLOBALS['DbConn']->prepare($Sql);
			$Stmt->bindParam(':PieceCode', $PieceCode);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);
			$Row = $Stmt->fetch();
			$Stmt = null;

			$ExistCount = $Row["ExistCount"];
			

			if ($ExistCount==0){
				$PieceLayout = "{{잘못된 PieceCode : $PieceCode}}";
			}else{
				$Sql = "select PieceLayout from Pieces where PieceCode=:PieceCode";
				$Stmt = $GLOBALS['DbConn']->prepare($Sql);
				$Stmt->bindParam(':PieceCode', $PieceCode);
				$Stmt->execute();
				$Stmt->setFetchMode(PDO::FETCH_ASSOC);
				$Row = $Stmt->fetch();
				$Stmt = null;

				$PieceLayout = $Row["PieceLayout"];
			}

			$html = str_replace($PieceCodePatten,$PieceLayout,$html);

		}

	}
	return $html;
}


function convertRecent($html){
	if ($html!=""){

	}
	return $html;
}


function convertLogin($html){
	if ($html!=""){

			$pos1 = strpos($html, "{{BeforeLogin}}");
			$pos2 = strpos($html, "{{/BeforeLogin}}");
			
			if ($pos1 != 0 && $pos2 != 0 && $pos1 < $pos2){
				$pos2 = $pos2 + 16;
				$deletestr = substr($html, $pos1, $pos2 - $pos1);
				if (isset($_COOKIE["LoginMemberID"])){
					$html = str_replace($deletestr,"",$html);
				}
			}

			$pos1 = strpos($html, "{{AfterLogin}}");
			$pos2 = strpos($html, "{{/AfterLogin}}");

			if ($pos1 != 0 && $pos2 != 0 && $pos1 < $pos2){
				$pos2 = $pos2 + 15;
				$deletestr = substr($html, $pos1, $pos2 - $pos1);
				if (!isset($_COOKIE["LoginMemberID"])){
					$html = str_replace($deletestr,"",$html);
				}
			}


			$html = str_replace("{{BeforeLogin}}","",$html);
			$html = str_replace("{{/BeforeLogin}}","",$html);
			$html = str_replace("{{AfterLogin}}","",$html);
			$html = str_replace("{{/AfterLogin}}","",$html);



			$pos1 = strpos($html, "{{BeforeLogin2}}");
			$pos2 = strpos($html, "{{/BeforeLogin2}}");
			
			if ($pos1 != 0 && $pos2 != 0 && $pos1 < $pos2){
				$pos2 = $pos2 + 17;
				$deletestr = substr($html, $pos1, $pos2 - $pos1);
				if (isset($_COOKIE["LoginMemberID"])){
					$html = str_replace($deletestr,"",$html);
				}
			}

			$pos1 = strpos($html, "{{AfterLogin2}}");
			$pos2 = strpos($html, "{{/AfterLogin2}}");

			if ($pos1 != 0 && $pos2 != 0 && $pos1 < $pos2){
				$pos2 = $pos2 + 16;
				$deletestr = substr($html, $pos1, $pos2 - $pos1);
				if (!isset($_COOKIE["LoginMemberID"])){
					$html = str_replace($deletestr,"",$html);
				}
			}


			$html = str_replace("{{BeforeLogin2}}","",$html);
			$html = str_replace("{{/BeforeLogin2}}","",$html);
			$html = str_replace("{{AfterLogin2}}","",$html);
			$html = str_replace("{{/AfterLogin2}}","",$html);


	}
	return $html;
}


function convertCode($html){

	$LinkLoginMemberID = isset($_COOKIE["LinkLoginMemberID"]) ? $_COOKIE["LinkLoginMemberID"] : "";

	if ($LinkLoginMemberID!=""){
		$Sql = "select * from Members where MemberLoginID=:MemberLoginID";
		$Stmt = $GLOBALS['DbConn']->prepare($Sql);
		$Stmt->bindParam(':MemberLoginID', $LinkLoginMemberID);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;

		$FnMemberID = $Row["MemberID"];
		$FnMemberName = $Row["MemberName"];
		$FnMemberLoginID = $LinkLoginMemberID;
	}else{
		$FnMemberID = "";
		$FnMemberName = "";
		$FnMemberLoginID = "";
	}
	
	if ($html!=""){

		$html = str_replace("{{MemberID}}", $FnMemberID, $html);
		$html = str_replace("{{MemberName}}", $FnMemberName, $html);
		$html = str_replace("{{MemberLoginID}}", $FnMemberLoginID, $html);



		//=========================== SLP 망고아이 설정 ============================
		$http_host = $_SERVER['HTTP_HOST'];
		$request_uri = $_SERVER['REQUEST_URI'];
		$DomainSiteID = 0;//본사

		if ( strpos($http_host, "slpmangoi.com") !== false ) {
			$DomainSiteID = 1;//SLP
		}else if ( strpos($http_host, "eie.mangoi.co.kr") !== false ) {
			$DomainSiteID = 2;//EIE
		}else if ( strpos($http_host, "dream.mangoi.co.kr") !== false ) {
			$DomainSiteID = 3;//DREAM
		}else if ( strpos($http_host, "thomas.mangoi.co.kr") !== false ) {
			$DomainSiteID = 4;//THOMAS
		}else if ( strpos($http_host, "englishtell.co.kr") !== false ) {
			$DomainSiteID = 5;//ENGLISHTELL
		}else if ( strpos($http_host, "hi.mangoi.co.kr") !== false ) {
			$DomainSiteID = 6;//HI ( 토마스를 여기로 옮긴다고 함 )
		}else if ( strpos($http_host, "gumiivyleagueenglish.co.kr") !== false ) {
            $DomainSiteID = 7;//gumiivyleague
        }else if ( strpos($http_host, "engliseed.kr") !== false ) {
            $DomainSiteID = 8;//engliseed.kr
        }else if ( strpos($http_host, "live.engedu.kr") !== false ) {
            $DomainSiteID = 9;//live.engedu.kr

		}else {
			$DomainSiteID = 0;//본사
		}


		if ($DomainSiteID==0){//본사

			$html = str_replace("{{QuickMenuSLP}}", "", $html);
			$html = str_replace("{{QuickMenuLibrary}}", "<li><a href=\"http://new.bookclubs.co.kr/mangoi\" target=\"_blank\" class=\"four\"><img src=\"images/icon_quick_5.png\" alt=\"온라인도서관\" class=\"img\"><div class=\"TrnTagCom\">온라인<br>도서관</div></a></li>", $html);

			$html = str_replace("{{SubVisualText}}", "<b class=\"sub_visual_bold TrnTagCom\">망고아이</b>", $html);
			$html = str_replace("{{HomeTopLogo}}", "<img src=\"images/logo_mangoi.png\" alt=\"logo\" class=\"header_logo\">", $html);
			$html = str_replace("{{FooterLogo}}", "<img src=\"images/logo_mangoi_gray.png\" alt=\"로고\" class=\"foot_logo\">", $html);
			$html = str_replace("{{FooterAddr1}}", "<trn class\"TrnTagCom\">상호 : (주)에듀비전   주소 :  경기도 안산시 상록구 이동 716-10번지 6층</trn> <div class=\"break TrnTagCom\">대표  : 정우영  사업자등록번호 : 134-86-30816</div>", $html);

			$html = str_replace("{{IntroHideStart1}}", "", $html);
			$html = str_replace("{{IntroHideEnd1}}", "", $html);
			$html = str_replace("{{IntroHideStart2}}", "", $html);
			$html = str_replace("{{IntroHideEnd2}}", "", $html);
			 
		}else if ($DomainSiteID==1){//SLP

			$html = str_replace("망고아이", "SLP 망고아이", $html);

			$html = str_replace("{{QuickMenuSLP}}", "<li><a href=\"http://slp.ac.kr/\" target=\"_blank\" class=\"eight\"><img src=\"images/icon_quick_9.png\" alt=\"SLP\" class=\"img\"><div class=\"TrnTagCom\">SLP본사</div></a></li>", $html);
			$html = str_replace("{{QuickMenuLibrary}}", "", $html);

			$html = str_replace("{{SubVisualText}}", "<b class=\"sub_visual_bold slp TrnTagCom\">SLP 망고아이</b>", $html);
			$html = str_replace("{{HomeTopLogo}}", "<img src=\"images/logo_slp.png\" alt=\"logo\" class=\"header_logo_slp\">", $html);
			$html = str_replace("{{FooterLogo}}", "<img src=\"images/logo_slp_gray.png\" alt=\"로고\" class=\"foot_logo\" style=\"display:none;\">", $html);
			$html = str_replace("{{FooterAddr1}}", "<trn class=\"TrnTagCom\">망고아이</trn><div class=\"break\"> </div>", $html);

			$html = str_replace("{{IntroHideStart1}}", "<!--", $html);
			$html = str_replace("{{IntroHideEnd1}}", "-->", $html);
			$html = str_replace("{{IntroHideStart2}}", "<!--", $html);
			$html = str_replace("{{IntroHideEnd2}}", "-->", $html);
			
		}else if ($DomainSiteID==2){//EIE

			$html = str_replace("{{QuickMenuSLP}}", "", $html);
			$html = str_replace("{{QuickMenuLibrary}}", "<li><a href=\"http://new.bookclubs.co.kr/mangoi\" target=\"_blank\" class=\"four\"><img src=\"images/icon_quick_5.png\" alt=\"온라인도서관\" class=\"img\"><div class=\"TrnTagCom\">온라인<br>도서관</div></a></li>", $html);

			$html = str_replace("{{SubVisualText}}", "<b class=\"sub_visual_bold TrnTagCom\">망고아이</b>", $html);
			$html = str_replace("{{HomeTopLogo}}", "<img src=\"images/logo_eie.png\" alt=\"logo\" class=\"header_logo\">", $html);
			$html = str_replace("{{FooterLogo}}", "<img src=\"images/logo_eie_gray.png\" alt=\"로고\" class=\"foot_logo\">", $html);
			$html = str_replace("{{FooterAddr1}}", "<trn class\"TrnTagCom\">상호 : (주)에듀비전   주소 :  경기도 안산시 상록구 이동 716-10번지 6층</trn> <div class=\"break TrnTagCom\">대표  : 정우영  사업자등록번호 : 134-86-30816</div>", $html);

			$html = str_replace("{{IntroHideStart1}}", "", $html);
			$html = str_replace("{{IntroHideEnd1}}", "", $html);
			$html = str_replace("{{IntroHideStart2}}", "", $html);
			$html = str_replace("{{IntroHideEnd2}}", "", $html);

		}else if ($DomainSiteID==3){//DREAM

			$html = str_replace("{{QuickMenuSLP}}", "", $html);
			$html = str_replace("{{QuickMenuLibrary}}", "<li><a href=\"http://new.bookclubs.co.kr/mangoi\" target=\"_blank\" class=\"four\"><img src=\"images/icon_quick_5.png\" alt=\"온라인도서관\" class=\"img\"><div class=\"TrnTagCom\">온라인<br>도서관</div></a></li>", $html);

			$html = str_replace("{{SubVisualText}}", "<b class=\"sub_visual_bold TrnTagCom\">망고아이</b>", $html);
			$html = str_replace("{{HomeTopLogo}}", "<img src=\"images/logo_dream.png\" alt=\"logo\" class=\"header_logo\">", $html);
			$html = str_replace("{{FooterLogo}}", "<img src=\"images/logo_dream_gray.png\" alt=\"로고\" class=\"foot_logo\">", $html);
			$html = str_replace("{{FooterAddr1}}", "<trn class=\"TrnTagCom\">상호 : (주)에듀비전   주소 :  경기도 안산시 상록구 이동 716-10번지 6층</trn> <div class=\"break TrnTagCom\">대표  : 정우영  사업자등록번호 : 134-86-30816</div>", $html);

			$html = str_replace("{{IntroHideStart1}}", "", $html);
			$html = str_replace("{{IntroHideEnd1}}", "", $html);
			$html = str_replace("{{IntroHideStart2}}", "", $html);
			$html = str_replace("{{IntroHideEnd2}}", "", $html);

		}else if ($DomainSiteID==4){//THOMAS

			$html = str_replace("{{SSO_SITE_LOGO}}", "<img src=\"images/logo_thomas.png\" alt=\"logo\" class=\"header_logo\" style=\"max-height:75px;\">", $html);

		}else if ($DomainSiteID==5){//ENGLISHTELL

			$html = str_replace("{{SSO_SITE_LOGO}}", "<img src=\"images/logo_englishtell.png\" alt=\"logo\" class=\"header_logo\">", $html);

		}else if ($DomainSiteID==6){//HI

			$html = str_replace("{{QuickMenuSLP}}", "", $html);
			$html = str_replace("{{QuickMenuLibrary}}", "<li><a href=\"http://new.bookclubs.co.kr/mangoi\" target=\"_blank\" class=\"four\"><img src=\"images/icon_quick_5.png\" alt=\"온라인도서관\" class=\"img\"><div class=\"TrnTagCom\">온라인<br>도서관</div></a></li><li><a href=\"http://band.us/@himangoi\" target=\"_blank\" class=\"ten\"><img src=\"images/icon_quick_10.png\" alt=\"알스영어자료실\" class=\"img\"><div>알스영어<br>자료실</div></a></li>", $html);
            
			$html = str_replace("{{SubVisualText}}", "<b class=\"sub_visual_bold\">망고아이</b>", $html);
			$html = str_replace("{{HomeTopLogo}}", "<img src=\"images/logo_mangoi.png\" alt=\"logo\" class=\"header_logo\">", $html);
			$html = str_replace("{{FooterLogo}}", "<img src=\"images/logo_mangoi_gray.png\" alt=\"로고\" class=\"foot_logo\">", $html);
			$html = str_replace("{{FooterAddr1}}", "<trn class=\"TrnTagCom\">상호 : (주)에듀비전   주소 :  경기도 안산시 상록구 이동 716-10번지 6층</trn> <div class=\"break TrnTagCom\">대표  : 정우영  사업자등록번호 : 134-86-30816</div>", $html);

			$html = str_replace("{{IntroHideStart1}}", "", $html);
			$html = str_replace("{{IntroHideEnd1}}", "", $html);
			$html = str_replace("{{IntroHideStart2}}", "", $html);
			$html = str_replace("{{IntroHideEnd2}}", "", $html);

        }else if ($DomainSiteID==7){//gumiivyleague
			$html = str_replace("망고아이", "아이비리그", $html);
			$html = str_replace("Mangoi", "Ivyleague", $html);
            $html = str_replace("{{QuickMenuSLP}}", "<li><a href=\"http://slp.ac.kr/\" target=\"_blank\" class=\"eight\"><img src=\"images/icon_quick_9.png\" alt=\"SLP\" class=\"img\"><div class=\"TrnTagCom\">SLP본사</div></a></li>", $html);
			$html = str_replace("{{QuickMenuLibrary}}", "", $html);
            
            $html = str_replace("{{SubVisualText}}", "<b class=\"sub_visual_bold TrnTagCom\">아이비리그</b>", $html);
			$html = str_replace("{{HomeTopLogo}}", "<img src=\"images/logo_gumiivyleague.png\" alt=\"logo\" class=\"header_logo_gumiivyleague\" style=\"position:absolute; width: 80px; left: 40px; top: 5px;\">", $html);
            $html = str_replace("{{FooterLogo}}", "<img src=\"images/logo_gumiivyleague_gray.png\" alt=\"로고\" class=\"foot_logo\" >", $html);
            $html = str_replace("{{FooterAddr1}}", "<trn class=\"TrnTagCom\">아이비리그</trn><div class=\"break\"> </div>", $html);

			$html = str_replace("{{IntroHideStart1}}", "<!--", $html);
			$html = str_replace("{{IntroHideEnd1}}", "-->", $html);
			$html = str_replace("{{IntroHideStart2}}", "<!--", $html);
			$html = str_replace("{{IntroHideEnd2}}", "-->", $html);
			
		}else if ($DomainSiteID==8){//engliseed.kr
            $html = str_replace("망고아이", "잉글리씨드", $html);
            $html = str_replace("통신판매업신고번호 : 제 2010-경기안산-0634호", "사업자등록번호 179-87-00461  |  정보관리책임자 원준성", $html);
            $html = str_replace("개인정보 보호 책임자 : 장지웅(jangjiwoong@mangoi.com)", "부산광역시 해운대구 좌동순환로72 마이우스골드 902호", $html);
            $html = str_replace("1644-0561", "1899-0578", $html);
            $html = str_replace("10:00 ~ 20:00", "09:00 ~ 18:00", $html);
            $html = str_replace("Mangoi", "Engliseed", $html);
            $html = str_replace("img_best.png", "img_best_engliseed.png", $html);
            $html = str_replace("logo_text_mangoi.png", "logo_engliseed.png", $html);
            $html = str_replace("img_mangoi_vision.jpg", "og_logo_engliseed.png", $html);
            $html = str_replace("아이비리그", "잉글리씨드", $html);
            $html = str_replace("아이비리그", "잉글리씨드", $html);

            $html = str_replace("{{QuickMenuSLP}}", "<li><a href=\"https://engliseed.com/\" target=\"_blank\" class=\"eight\"><img src=\"images/icon_quick_12.png\" alt=\"잉글리씨드\" class=\"img\"><div class=\"TrnTagCom\">잉글리씨드</div></a></li>", $html);
            $html = str_replace("{{QuickMenuLibrary}}", "", $html);

            $html = str_replace("{{SubVisualText}}", "<b class=\"sub_visual_bold TrnTagCom\">잉글리씨드</b>", $html);

            $html = str_replace("{{HomeTopLogo}}", "<img src=\"images/logo_engliseed.png\" alt=\"logo\" class=\"header_logo\">", $html);

            $html = str_replace("{{FooterLogo}}", "<img src=\"images/logo_engliseed_gray.png\" alt=\"로고\" class=\"foot_logo\" >", $html);

            $html = str_replace("{{FooterAddr1}}", "<trn class=\"TrnTagCom\">주식회사 잉글리씨드(Engliseed Co..Ltd)  |  대표 임창문</trn><div class=\"break\"> </div>", $html);

            $html = str_replace("{{IntroHideStart1}}", "<!--", $html);
            $html = str_replace("{{IntroHideEnd1}}", "-->", $html);
            $html = str_replace("{{IntroHideStart2}}", "<!--", $html);
            $html = str_replace("{{IntroHideEnd2}}", "-->", $html);

        }else if ($DomainSiteID==9){//live.engedu.kr
            $html = str_replace("망고아이", "이엔지 화상영어", $html);
            $html = str_replace("통신판매업신고번호 : 2023-서울서초-3253", "사업자등록번호 732-86-03151  |  정보관리책임자 임윤재", $html);
            $html = str_replace("개인정보 보호 책임자 : 임윤재(peri129@hanmail.net)", "서울특별시 서초구 반포동 745 금성빌딩", $html);
            $html = str_replace("1644-0561", "1566-4522", $html);
            $html = str_replace("10:00 ~ 20:00", "09:00 ~ 18:00", $html);
            $html = str_replace("Mangoi", "engedu", $html);
            $html = str_replace("img_best.png", "img_best_engedu.png", $html);
            $html = str_replace("logo_text_mangoi.png", "eng-edu-logo.png", $html);
            $html = str_replace("img_mangoi_vision.jpg", "og_logo_engedu.png", $html);
            $html = str_replace("아이비리그", "이엔지 화상영어", $html);
            $html = str_replace("아이비리그", "이엔지 화상영어", $html);

            $html = str_replace("{{QuickMenuSLP}}", "<li><a href=\"https://www.engedu.kr/\" target=\"_blank\" class=\"eight\"><img src=\"images/icon_quick_12.png\" alt=\"이엔지 화상영어\" class=\"img\"><div class=\"TrnTagCom\">이엔지 화상영어</div></a></li>", $html);
            $html = str_replace("{{QuickMenuLibrary}}", "", $html);

            $html = str_replace("{{SubVisualText}}", "<b class=\"sub_visual_bold TrnTagCom\">이엔지 화상영어</b>", $html);

            $html = str_replace("{{HomeTopLogo}}", "<img src=\"images/eng-edu-logo.png\" alt=\"logo\" class=\"header_logo\">", $html);

            $html = str_replace("{{FooterLogo}}", "<img src=\"images/eng-edu-logo-gray.png\" alt=\"로고\" class=\"foot_logo\" >", $html);

            $html = str_replace("{{FooterAddr1}}", "<trn class=\"TrnTagCom\">이엔지 화상영어  |  대표 임윤재</trn><div class=\"break\"> </div>", $html);

            $html = str_replace("{{IntroHideStart1}}", "<!--", $html);
            $html = str_replace("{{IntroHideEnd1}}", "-->", $html);
            $html = str_replace("{{IntroHideStart2}}", "<!--", $html);
            $html = str_replace("{{IntroHideEnd2}}", "-->", $html);
        }

		//=========================== SLP 망고아이 설정 ============================




	}
	return $html;
}


function GetGeoCode($juso) {//네이버 : 주소로 좌표 가져오기
                    
        $ch = curl_init();
        $address = urlencode($juso);
        $encoding="utf-8"; //출력 결과 인코딩 값으로 'utf-8', 'euc-kr' 가능
        $coord="latlng"; //출력 좌표 체계 값으로 latlng(위경도), tm128(카텍) 가능
        $output="json" ;//json,xml
        
        $qry_str = "?encoding=".$encoding."&coord=".$coord."&output=".$output."&query=".$address;
        $headers = array(
            "X-Naver-Client-Id: $NaverClientID", //Client ID            
            "X-Naver-Client-Secret: $NaverClientSecret" //Client Secret
        );
    
        $url="https://openapi.naver.com/v1/map/geocode";
        curl_setopt($ch, CURLOPT_URL, $url.$qry_str);
         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);                
        $res =curl_exec($ch);
        curl_close($ch);
        
		//$xmlData = $this->simplexml->xml_parse($resXML);
        //echo($res);
        //debug_var($xmlData);
        
		return $res;                    
}


function InsertMasterMessage($MasterMessageType, $MasterMessageText){

	$Sql = " insert into MasterMessages ( ";
		
		$Sql .= " MasterMessageType, ";
		$Sql .= " MasterMessageText, ";
		$Sql .= " MasterMessageRegDateTime, ";
		$Sql .= " MasterMessageModiDateTime ";
	$Sql .= " ) values ( ";
		$Sql .= " :MasterMessageType, ";
		$Sql .= " :MasterMessageText, ";
		$Sql .= " now(), ";
		$Sql .= " now() ";
	$Sql .= " ) ";
	
	$Stmt = $GLOBALS['DbConn']->prepare($Sql);
	$Stmt->bindParam(':MasterMessageType', $MasterMessageType);
	$Stmt->bindParam(':MasterMessageText', $MasterMessageText);
	$Stmt->execute();
	$Stmt = null;

}


function InsertTeacherMessage($TeacherMemberID, $TeacherMessageType, $TeacherMessageText){

	$RequestMemberID = 0;
	
	$Sql = " insert into TeacherMessages ( ";
		$Sql .= " MemberID, ";
		$Sql .= " RequestMemberID, ";
		$Sql .= " TeacherMessageType, ";
		$Sql .= " TeacherMessageText, ";
		$Sql .= " TeacherMessageRegDateTime, ";
		$Sql .= " TeacherMessageModiDateTime ";
	$Sql .= " ) values ( ";
		$Sql .= " :MemberID, ";
		$Sql .= " :RequestMemberID, ";
		$Sql .= " :TeacherMessageType, ";
		$Sql .= " :TeacherMessageText, ";
		$Sql .= " now(), ";
		$Sql .= " now() ";
	$Sql .= " ) ";
	
	$Stmt = $GLOBALS['DbConn']->prepare($Sql);
	$Stmt->bindParam(':MemberID', $TeacherMemberID);
	$Stmt->bindParam(':RequestMemberID', $RequestMemberID);
	$Stmt->bindParam(':TeacherMessageType', $TeacherMessageType);
	$Stmt->bindParam(':TeacherMessageText', $TeacherMessageText);
	$Stmt->execute();
	$Stmt = null;

}


//InsertNewPoint(1, 1, 87, 'zz', '{{이름}}님, {{포인트}} 먹어', 100);
function InsertNewPoint($MemberPointTypeID, $RegMemberID, $PointMemberID, $MemberPointName, $MemberPointText, $MemberPoint){
	
	if ($PointMemberID>0){

		$Sql = "select 
					A.MemberLevelID,
					A.MemberName,
					AES_DECRYPT(UNHEX(A.MemberPhone1),:EncryptionKey) as DecMemberPhone1,
					ifnull((select DeviceToken from DeviceTokens where MemberID=A.MemberID order by ModiDateTime desc limit 0,1), '') as DeviceToken,
					ifnull((select DeviceType from DeviceTokens where MemberID=A.MemberID order by ModiDateTime desc limit 0,1), '') as DeviceType 
				from Members A 
				where A.MemberID=:MemberID";
		$Stmt = $GLOBALS['DbConn']->prepare($Sql);
		$Stmt->bindParam(':MemberID', $PointMemberID);
		$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
		$Stmt->execute();
		$Row = $Stmt->fetch();
		$Stmt = null;
		
		$MemberLevelID = $Row["MemberLevelID"];
		$DeviceToken = $Row["DeviceToken"];
		$DeviceType = $Row["DeviceType"];
		$SmsMessagePhoneNumber = $Row["DecMemberPhone1"];
		$KakaoMessagePhoneNumber = $Row["DecMemberPhone1"];
		$MemberName = $Row["MemberName"];		


		$MemberPointText = str_replace("{{이름}}", $MemberName, $MemberPointText);
		$MemberPointText = str_replace("{{포인트}}", number_format($MemberPoint,0), $MemberPointText);


		$Sql = " insert into MemberPoints ( ";
			$Sql .= " MemberPointTypeID, ";
			$Sql .= " RegMemberID, ";
			$Sql .= " MemberID, ";
			$Sql .= " MemberPointName, ";
			$Sql .= " MemberPointText, ";
			$Sql .= " MemberPoint, ";
			$Sql .= " MemberPointRegDateTime, ";
			$Sql .= " MemberPointModiDateTime, ";
			$Sql .= " MemberPointState ";
		$Sql .= " ) values ( ";
			$Sql .= " :MemberPointTypeID, ";
			$Sql .= " :RegMemberID, ";
			$Sql .= " :MemberID, ";
			$Sql .= " :MemberPointName, ";
			$Sql .= " :MemberPointText, ";
			$Sql .= " :MemberPoint, ";
			$Sql .= " now(), ";
			$Sql .= " now(), ";
			$Sql .= " 1 ";
		$Sql .= " ) ";
		
		$Stmt = $GLOBALS['DbConn']->prepare($Sql);
		$Stmt->bindParam(':MemberPointTypeID', $MemberPointTypeID);
		$Stmt->bindParam(':RegMemberID', $RegMemberID);
		$Stmt->bindParam(':MemberID', $PointMemberID);
		$Stmt->bindParam(':MemberPointName', $MemberPointName);
		$Stmt->bindParam(':MemberPointText', $MemberPointText);
		$Stmt->bindParam(':MemberPoint', $MemberPoint);
		$Stmt->execute();
		$Stmt = null;

	
	
		$SendMemberID = 0;
		$SendTitle="포인트적립알림";
		$SendMessage = $MemberPointText;
		
		$SendMemo="";
		$SendMessageDateTime=date("Y-m-d H:i:s");
		$UseSendPush=1;
		$UseSendSms=1;
		$UseSendKakao=1;

		if($MemberLevelID==19) {//학생일때만 푸시 보냄

			//학생에게 푸시
			if ($DeviceToken!=""){
				$Sql_Push = " insert into SendMessageLogs ( ";
					$Sql_Push .= " MemberID, ";
					$Sql_Push .= " SendMemberID, ";
					$Sql_Push .= " SendTitle, ";
					$Sql_Push .= " SendMessage, ";
					$Sql_Push .= " SendMemo, ";
					$Sql_Push .= " SendMessageDateTime, ";
					$Sql_Push .= " SendMessageLogRegDateTime, ";
					$Sql_Push .= " SendMessageLogModiDateTime, ";
					$Sql_Push .= " UseSendPush, ";
					$Sql_Push .= " UseSendSms, ";
					$Sql_Push .= " UseSendKakao, ";
					$Sql_Push .= " DeviceToken, ";
					$Sql_Push .= " DeviceType, ";
					$Sql_Push .= " PushMessageState, ";
					$Sql_Push .= " SmsMessagePhoneNumber, ";
					$Sql_Push .= " SmsMessageState, ";
					$Sql_Push .= " KakaoMessagePhoneNumber, ";
					$Sql_Push .= " KakaoMessageState ";
				$Sql_Push .= " ) values ( ";
					$Sql_Push .= " :MemberID, ";
					$Sql_Push .= " :SendMemberID, ";
					$Sql_Push .= " :SendTitle, ";
					$Sql_Push .= " :SendMessage, ";
					$Sql_Push .= " :SendMemo, ";
					$Sql_Push .= " :SendMessageDateTime, ";
					$Sql_Push .= " now(), ";
					$Sql_Push .= " now(), ";
					$Sql_Push .= " :UseSendPush, ";
					$Sql_Push .= " :UseSendSms, ";
					$Sql_Push .= " :UseSendKakao, ";
					$Sql_Push .= " :DeviceToken, ";
					$Sql_Push .= " :DeviceType, ";
					$Sql_Push .= " 1, ";
					$Sql_Push .= " :SmsMessagePhoneNumber, ";
					$Sql_Push .= " 1, ";
					$Sql_Push .= " :KakaoMessagePhoneNumber, ";
					$Sql_Push .= " 1 ";
				$Sql_Push .= " ) ";

				$Stmt_Push = $GLOBALS['DbConn']->prepare($Sql_Push);
				$Stmt_Push->bindParam(':MemberID', $PointMemberID);
				$Stmt_Push->bindParam(':SendMemberID', $SendMemberID);
				$Stmt_Push->bindParam(':SendTitle', $SendTitle);
				$Stmt_Push->bindParam(':SendMessage', $SendMessage);
				$Stmt_Push->bindParam(':SendMemo', $SendMemo);
				$Stmt_Push->bindParam(':SendMessageDateTime', $SendMessageDateTime);
				$Stmt_Push->bindParam(':UseSendPush', $UseSendPush);
				$Stmt_Push->bindParam(':UseSendSms', $UseSendSms);
				$Stmt_Push->bindParam(':UseSendKakao', $UseSendKakao);
				$Stmt_Push->bindParam(':DeviceToken', $DeviceToken);
				$Stmt_Push->bindParam(':DeviceType', $DeviceType);
				$Stmt_Push->bindParam(':SmsMessagePhoneNumber', $SmsMessagePhoneNumber);
				$Stmt_Push->bindParam(':KakaoMessagePhoneNumber', $KakaoMessagePhoneNumber);
				$Stmt_Push->execute();
				$Stmt_Push = null;
			}

			//부모에게 푸시
			$Sql2 = "select
						ifnull((select DeviceToken from DeviceTokens where MemberID=A.MemberID order by ModiDateTime desc limit 0,1), '') as DeviceToken,
						ifnull((select DeviceType from DeviceTokens where MemberID=A.MemberID order by ModiDateTime desc limit 0,1), '') as DeviceType 
					from MemberChilds A 
					where A.MemberChildID=:MemberID";

			$Stmt2 = $GLOBALS['DbConn']->prepare($Sql2);
			$Stmt2->bindParam(':MemberID', $PointMemberID);
			$Stmt2->execute();
			
			while($Row2 = $Stmt2->fetch()) {
				$DeviceToken = $Row2["DeviceToken"];
				$DeviceType = $Row2["DeviceType"];


				if ($DeviceToken!=""){
					$Sql_Push = " insert into SendMessageLogs ( ";
						$Sql_Push .= " MemberID, ";
						$Sql_Push .= " SendMemberID, ";
						$Sql_Push .= " SendTitle, ";
						$Sql_Push .= " SendMessage, ";
						$Sql_Push .= " SendMemo, ";
						$Sql_Push .= " SendMessageDateTime, ";
						$Sql_Push .= " SendMessageLogRegDateTime, ";
						$Sql_Push .= " SendMessageLogModiDateTime, ";
						$Sql_Push .= " UseSendPush, ";
						$Sql_Push .= " UseSendSms, ";
						$Sql_Push .= " UseSendKakao, ";
						$Sql_Push .= " DeviceToken, ";
						$Sql_Push .= " DeviceType, ";
						$Sql_Push .= " PushMessageState, ";
						$Sql_Push .= " SmsMessagePhoneNumber, ";
						$Sql_Push .= " SmsMessageState, ";
						$Sql_Push .= " KakaoMessagePhoneNumber, ";
						$Sql_Push .= " KakaoMessageState ";
					$Sql_Push .= " ) values ( ";
						$Sql_Push .= " :MemberID, ";
						$Sql_Push .= " :SendMemberID, ";
						$Sql_Push .= " :SendTitle, ";
						$Sql_Push .= " :SendMessage, ";
						$Sql_Push .= " :SendMemo, ";
						$Sql_Push .= " :SendMessageDateTime, ";
						$Sql_Push .= " now(), ";
						$Sql_Push .= " now(), ";
						$Sql_Push .= " :UseSendPush, ";
						$Sql_Push .= " :UseSendSms, ";
						$Sql_Push .= " :UseSendKakao, ";
						$Sql_Push .= " :DeviceToken, ";
						$Sql_Push .= " :DeviceType, ";
						$Sql_Push .= " 1, ";
						$Sql_Push .= " :SmsMessagePhoneNumber, ";
						$Sql_Push .= " 1, ";
						$Sql_Push .= " :KakaoMessagePhoneNumber, ";
						$Sql_Push .= " 1 ";
					$Sql_Push .= " ) ";

					$Stmt_Push = $GLOBALS['DbConn']->prepare($Sql_Push);
					$Stmt_Push->bindParam(':MemberID', $PointMemberID);
					$Stmt_Push->bindParam(':SendMemberID', $SendMemberID);
					$Stmt_Push->bindParam(':SendTitle', $SendTitle);
					$Stmt_Push->bindParam(':SendMessage', $SendMessage);
					$Stmt_Push->bindParam(':SendMemo', $SendMemo);
					$Stmt_Push->bindParam(':SendMessageDateTime', $SendMessageDateTime);
					$Stmt_Push->bindParam(':UseSendPush', $UseSendPush);
					$Stmt_Push->bindParam(':UseSendSms', $UseSendSms);
					$Stmt_Push->bindParam(':UseSendKakao', $UseSendKakao);
					$Stmt_Push->bindParam(':DeviceToken', $DeviceToken);
					$Stmt_Push->bindParam(':DeviceType', $DeviceType);
					$Stmt_Push->bindParam(':SmsMessagePhoneNumber', $SmsMessagePhoneNumber);
					$Stmt_Push->bindParam(':KakaoMessagePhoneNumber', $KakaoMessagePhoneNumber);
					$Stmt_Push->execute();
					$Stmt_Push = null;
				}
			}
			$Stmt2 = null;
		}
	}
	
}


//InsertNewPoint() 사용 아래는 사용안함
function InsertPoint($MemberPointTypeID, $RegMemberID, $PointMemberID, $MemberPointName, $MemberPointText, $MemberPoint){
	/*
	if ($PointMemberID>0){
		
		$Sql = " insert into MemberPoints ( ";
			
			$Sql .= " MemberPointTypeID, ";
			$Sql .= " RegMemberID, ";
			$Sql .= " MemberID, ";
			$Sql .= " MemberPointName, ";
			$Sql .= " MemberPointText, ";
			$Sql .= " MemberPoint, ";
			$Sql .= " MemberPointRegDateTime, ";
			$Sql .= " MemberPointModiDateTime, ";
			$Sql .= " MemberPointState ";
		$Sql .= " ) values ( ";
			$Sql .= " :MemberPointTypeID, ";
			$Sql .= " :RegMemberID, ";
			$Sql .= " :MemberID, ";
			$Sql .= " :MemberPointName, ";
			$Sql .= " :MemberPointText, ";
			$Sql .= " :MemberPoint, ";
			$Sql .= " now(), ";
			$Sql .= " now(), ";
			$Sql .= " 1 ";
		$Sql .= " ) ";
		
		$Stmt = $GLOBALS['DbConn']->prepare($Sql);
		$Stmt->bindParam(':MemberPointTypeID', $MemberPointTypeID);
		$Stmt->bindParam(':RegMemberID', $RegMemberID);
		$Stmt->bindParam(':MemberID', $PointMemberID);
		$Stmt->bindParam(':MemberPointName', $MemberPointName);
		$Stmt->bindParam(':MemberPointText', $MemberPointText);
		$Stmt->bindParam(':MemberPoint', $MemberPoint);
		$Stmt->execute();
		$Stmt = null;

		$Sql = "select 
					A.MemberPointTypeName 
				from MemberPointTypes A 
				where A.MemberPointTypeID=:MemberPointTypeID ";
		$Stmt = $GLOBALS['DbConn']->prepare($Sql);
		$Stmt->bindParam(':MemberPointTypeID', $MemberPointTypeID);
		$Stmt->execute();
		$Row = $Stmt->fetch();
		$MemberPointTypeName = $Row["MemberPointTypeName"];
		$StrMemberPointTypeName = "";
		if($MemberPointTypeName) {
			$StrMemberPointTypeName = " (".$MemberPointTypeName.")";
		}
		$Stmt = null;

		$Sql = "select 
					A.MemberLevelID,
					A.MemberName,
					AES_DECRYPT(UNHEX(A.MemberPhone1),:EncryptionKey) as DecMemberPhone1,
					ifnull((select DeviceToken from DeviceTokens where MemberID=A.MemberID order by ModiDateTime desc limit 0,1), '') as DeviceToken,
					ifnull((select DeviceType from DeviceTokens where MemberID=A.MemberID order by ModiDateTime desc limit 0,1), '') as DeviceType 
				from Members A 
				where A.MemberID=:MemberID";
		$Stmt = $GLOBALS['DbConn']->prepare($Sql);
		$Stmt->bindParam(':MemberID', $PointMemberID);
		$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
		$Stmt->execute();
		$Row = $Stmt->fetch();
		$Stmt = null;
		
		$MemberLevelID = $Row["MemberLevelID"];
		$DeviceToken = $Row["DeviceToken"];
		$DeviceType = $Row["DeviceType"];
		$SmsMessagePhoneNumber = $Row["DecMemberPhone1"];
		$KakaoMessagePhoneNumber = $Row["DecMemberPhone1"];
		$MemberName = $Row["MemberName"];
		
		$SendMemberID = 0;
		$SendTitle="적립알림";
		//$SendMessage=$MemberName." 님께서 ".$MemberPoint." 포인트(".$MemberPointTypeName.")를 적립하였습니다.";
		$SendMessage=$MemberName." 학생에게 ".$MemberPoint." 포인트를 적립되었습니다.".$StrMemberPointTypeName;
		$SendMemo="";
		$SendMessageDateTime=date("Y-m-d H:i:s");
		$UseSendPush=1;
		$UseSendSms=1;
		$UseSendKakao=1;

		if($MemberLevelID==19) {//학생일때만 푸시 보냄

			//학생에게 푸시
			if ($DeviceToken!=""){
				$Sql_Push = " insert into SendMessageLogs ( ";
					$Sql_Push .= " MemberID, ";
					$Sql_Push .= " SendMemberID, ";
					$Sql_Push .= " SendTitle, ";
					$Sql_Push .= " SendMessage, ";
					$Sql_Push .= " SendMemo, ";
					$Sql_Push .= " SendMessageDateTime, ";
					$Sql_Push .= " SendMessageLogRegDateTime, ";
					$Sql_Push .= " SendMessageLogModiDateTime, ";
					$Sql_Push .= " UseSendPush, ";
					$Sql_Push .= " UseSendSms, ";
					$Sql_Push .= " UseSendKakao, ";
					$Sql_Push .= " DeviceToken, ";
					$Sql_Push .= " DeviceType, ";
					$Sql_Push .= " PushMessageState, ";
					$Sql_Push .= " SmsMessagePhoneNumber, ";
					$Sql_Push .= " SmsMessageState, ";
					$Sql_Push .= " KakaoMessagePhoneNumber, ";
					$Sql_Push .= " KakaoMessageState ";
				$Sql_Push .= " ) values ( ";
					$Sql_Push .= " :MemberID, ";
					$Sql_Push .= " :SendMemberID, ";
					$Sql_Push .= " :SendTitle, ";
					$Sql_Push .= " :SendMessage, ";
					$Sql_Push .= " :SendMemo, ";
					$Sql_Push .= " :SendMessageDateTime, ";
					$Sql_Push .= " now(), ";
					$Sql_Push .= " now(), ";
					$Sql_Push .= " :UseSendPush, ";
					$Sql_Push .= " :UseSendSms, ";
					$Sql_Push .= " :UseSendKakao, ";
					$Sql_Push .= " :DeviceToken, ";
					$Sql_Push .= " :DeviceType, ";
					$Sql_Push .= " 1, ";
					$Sql_Push .= " :SmsMessagePhoneNumber, ";
					$Sql_Push .= " 1, ";
					$Sql_Push .= " :KakaoMessagePhoneNumber, ";
					$Sql_Push .= " 1 ";
				$Sql_Push .= " ) ";

				$Stmt_Push = $GLOBALS['DbConn']->prepare($Sql_Push);
				$Stmt_Push->bindParam(':MemberID', $PointMemberID);
				$Stmt_Push->bindParam(':SendMemberID', $SendMemberID);
				$Stmt_Push->bindParam(':SendTitle', $SendTitle);
				$Stmt_Push->bindParam(':SendMessage', $SendMessage);
				$Stmt_Push->bindParam(':SendMemo', $SendMemo);
				$Stmt_Push->bindParam(':SendMessageDateTime', $SendMessageDateTime);
				$Stmt_Push->bindParam(':UseSendPush', $UseSendPush);
				$Stmt_Push->bindParam(':UseSendSms', $UseSendSms);
				$Stmt_Push->bindParam(':UseSendKakao', $UseSendKakao);
				$Stmt_Push->bindParam(':DeviceToken', $DeviceToken);
				$Stmt_Push->bindParam(':DeviceType', $DeviceType);
				$Stmt_Push->bindParam(':SmsMessagePhoneNumber', $SmsMessagePhoneNumber);
				$Stmt_Push->bindParam(':KakaoMessagePhoneNumber', $KakaoMessagePhoneNumber);
				$Stmt_Push->execute();
				$Stmt_Push = null;
			}

			//부모에게 푸시
			$Sql2 = "select
						ifnull((select DeviceToken from DeviceTokens where MemberID=A.MemberID order by ModiDateTime desc limit 0,1), '') as DeviceToken,
						ifnull((select DeviceType from DeviceTokens where MemberID=A.MemberID order by ModiDateTime desc limit 0,1), '') as DeviceType 
					from MemberChilds A 
					where A.MemberChildID=:MemberID";

			$Stmt2 = $GLOBALS['DbConn']->prepare($Sql2);
			$Stmt2->bindParam(':MemberID', $PointMemberID);
			$Stmt2->execute();
			
			while($Row2 = $Stmt2->fetch()) {
				$DeviceToken = $Row2["DeviceToken"];
				$DeviceType = $Row2["DeviceType"];


				if ($DeviceToken!=""){
					$Sql_Push = " insert into SendMessageLogs ( ";
						$Sql_Push .= " MemberID, ";
						$Sql_Push .= " SendMemberID, ";
						$Sql_Push .= " SendTitle, ";
						$Sql_Push .= " SendMessage, ";
						$Sql_Push .= " SendMemo, ";
						$Sql_Push .= " SendMessageDateTime, ";
						$Sql_Push .= " SendMessageLogRegDateTime, ";
						$Sql_Push .= " SendMessageLogModiDateTime, ";
						$Sql_Push .= " UseSendPush, ";
						$Sql_Push .= " UseSendSms, ";
						$Sql_Push .= " UseSendKakao, ";
						$Sql_Push .= " DeviceToken, ";
						$Sql_Push .= " DeviceType, ";
						$Sql_Push .= " PushMessageState, ";
						$Sql_Push .= " SmsMessagePhoneNumber, ";
						$Sql_Push .= " SmsMessageState, ";
						$Sql_Push .= " KakaoMessagePhoneNumber, ";
						$Sql_Push .= " KakaoMessageState ";
					$Sql_Push .= " ) values ( ";
						$Sql_Push .= " :MemberID, ";
						$Sql_Push .= " :SendMemberID, ";
						$Sql_Push .= " :SendTitle, ";
						$Sql_Push .= " :SendMessage, ";
						$Sql_Push .= " :SendMemo, ";
						$Sql_Push .= " :SendMessageDateTime, ";
						$Sql_Push .= " now(), ";
						$Sql_Push .= " now(), ";
						$Sql_Push .= " :UseSendPush, ";
						$Sql_Push .= " :UseSendSms, ";
						$Sql_Push .= " :UseSendKakao, ";
						$Sql_Push .= " :DeviceToken, ";
						$Sql_Push .= " :DeviceType, ";
						$Sql_Push .= " 1, ";
						$Sql_Push .= " :SmsMessagePhoneNumber, ";
						$Sql_Push .= " 1, ";
						$Sql_Push .= " :KakaoMessagePhoneNumber, ";
						$Sql_Push .= " 1 ";
					$Sql_Push .= " ) ";

					$Stmt_Push = $GLOBALS['DbConn']->prepare($Sql_Push);
					$Stmt_Push->bindParam(':MemberID', $PointMemberID);
					$Stmt_Push->bindParam(':SendMemberID', $SendMemberID);
					$Stmt_Push->bindParam(':SendTitle', $SendTitle);
					$Stmt_Push->bindParam(':SendMessage', $SendMessage);
					$Stmt_Push->bindParam(':SendMemo', $SendMemo);
					$Stmt_Push->bindParam(':SendMessageDateTime', $SendMessageDateTime);
					$Stmt_Push->bindParam(':UseSendPush', $UseSendPush);
					$Stmt_Push->bindParam(':UseSendSms', $UseSendSms);
					$Stmt_Push->bindParam(':UseSendKakao', $UseSendKakao);
					$Stmt_Push->bindParam(':DeviceToken', $DeviceToken);
					$Stmt_Push->bindParam(':DeviceType', $DeviceType);
					$Stmt_Push->bindParam(':SmsMessagePhoneNumber', $SmsMessagePhoneNumber);
					$Stmt_Push->bindParam(':KakaoMessagePhoneNumber', $KakaoMessagePhoneNumber);
					$Stmt_Push->execute();
					$Stmt_Push = null;
				}
			}
			$Stmt2 = null;
		}
	}
	*/
}


function InsertNewTypePoint($MemberPointNewTypeID, $RegMemberID, $PointMemberID, $MemberPointVaridate){
	if ($PointMemberID>0){
		$Sql = "select 
					A.MemberLevelID,
					A.MemberName,
					AES_DECRYPT(UNHEX(A.MemberPhone1),:EncryptionKey) as DecMemberPhone1,
					ifnull((select DeviceToken from DeviceTokens where MemberID=A.MemberID order by ModiDateTime desc limit 0,1), '') as DeviceToken,
					ifnull((select DeviceType from DeviceTokens where MemberID=A.MemberID order by ModiDateTime desc limit 0,1), '') as DeviceType 
				from Members A 
				where A.MemberID=:MemberID";
		$Stmt = $GLOBALS['DbConn']->prepare($Sql);
		$Stmt->bindParam(':MemberID', $PointMemberID);
		$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
		$Stmt->execute();
		$Row = $Stmt->fetch();
		$Stmt = null;
		
		$MemberLevelID = $Row["MemberLevelID"];
		$DeviceToken = $Row["DeviceToken"];
		$DeviceType = $Row["DeviceType"];
		$SmsMessagePhoneNumber = $Row["DecMemberPhone1"];
		$KakaoMessagePhoneNumber = $Row["DecMemberPhone1"];
		$MemberName = $Row["MemberName"];

		/*
		if($MemberLevelID==19) {
		} else if($MemberLevelID==18) {
		} else if($MemberLevelID==12 or $MemberLevelID==13) {
		}
		*/

		$Sql = "select 
					A.MemberPoint, 
					A.MemberPointTypeName, 
					A.MemberPointTypeText
				from MemberPointNewTypes A 
				where A.MemberPointTypeID=:MemberPointNewTypeID";
		$Stmt = $GLOBALS['DbConn']->prepare($Sql);
		$Stmt->bindParam(':MemberPointNewTypeID', $MemberPointNewTypeID);
		$Stmt->execute();
		$Row = $Stmt->fetch();
		$Stmt = null;
		
		$MemberPoint = $Row["MemberPoint"];
		$MemberPointTypeName = $Row["MemberPointTypeName"];
		$MemberPointTypeText = $Row["MemberPointTypeText"];


		$MemberPointTypeText = str_replace("{{이름}}", $MemberName, $MemberPointTypeText);
		$MemberPointTypeText = str_replace("{{포인트}}", number_format($MemberPoint,0), $MemberPointTypeText);


		$Sql = "
			select
				A.MemberPointID
			from MemberPoints A
			where 
				A.MemberPointTypeID=:MemberPointNewTypeID 
				and 
				A.MemberID=:MemberID 
				and 
				A.MemberPointState=1 
				and 
				datediff(A.MemberPointRegDateTime, now())=0
				and
				A.MemberPointVaridate=:MemberPointVaridate
		";
		$Stmt = $GLOBALS['DbConn']->prepare($Sql);
		$Stmt->bindParam(':MemberID', $PointMemberID);
		$Stmt->bindParam(':MemberPointNewTypeID', $MemberPointNewTypeID);
		$Stmt->bindParam(':MemberPointVaridate', $MemberPointVaridate);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;
		$MemberPointID = $Row["MemberPointID"];
		
		if (!$MemberPointID) {

			$Sql = " insert into MemberPoints ( ";
				$Sql .= " MemberPointTypeID, ";
				$Sql .= " RegMemberID, ";
				$Sql .= " MemberID, ";
				$Sql .= " MemberPointName, ";
				$Sql .= " MemberPointText, ";
				$Sql .= " MemberPoint, ";
				$Sql .= " MemberPointRegDateTime, ";
				$Sql .= " MemberPointModiDateTime, ";
				$Sql .= " MemberPointState, ";
				$Sql .= " MemberPointVaridate ";
				
			$Sql .= " ) values ( ";
				$Sql .= " :MemberPointTypeID, ";
				$Sql .= " :RegMemberID, ";
				$Sql .= " :MemberID, ";
				$Sql .= " :MemberPointName, ";
				$Sql .= " :MemberPointText, ";
				$Sql .= " :MemberPoint, ";
				$Sql .= " now(), ";
				$Sql .= " now(), ";
				$Sql .= " 1, ";
				$Sql .= " :MemberPointVaridate ";
			$Sql .= " ) ";
			
			$Stmt = $GLOBALS['DbConn']->prepare($Sql);
			$Stmt->bindParam(':MemberPointTypeID', $MemberPointNewTypeID);
			$Stmt->bindParam(':RegMemberID', $RegMemberID);
			$Stmt->bindParam(':MemberID', $PointMemberID);
			$Stmt->bindParam(':MemberPointName', $MemberPointTypeName);
			$Stmt->bindParam(':MemberPointText', $MemberPointTypeText);
			$Stmt->bindParam(':MemberPoint', $MemberPoint);
			$Stmt->bindParam(':MemberPointVaridate', $MemberPointVaridate);
			$Stmt->execute();
			$Stmt = null;

			$SendMemberID = 0;
			$SendTitle="적립알림";
			//$SendMessage=$MemberName." 님께서 ".$MemberPoint." 포인트(".$MemberPointTypeName.")를 적립하였습니다.";
			//$SendMessage=$MemberName." 학생에게 ".$MemberPoint." 포인트를 적립되었습니다.".$StrMemberPointTypeName;
			$SendMemo="";
			$SendMessageDateTime=date("Y-m-d H:i:s");
			$UseSendPush=1;
			$UseSendSms=0;
			$UseSendKakao=0;

			if($MemberLevelID==19) {//학생일때만 푸시 보냄

				//학생에게 푸시
				if ($DeviceToken!=""){
					$Sql_Push = " insert into SendMessageLogs ( ";
						$Sql_Push .= " MemberID, ";
						$Sql_Push .= " SendMemberID, ";
						$Sql_Push .= " SendTitle, ";
						$Sql_Push .= " SendMessage, ";
						$Sql_Push .= " SendMemo, ";
						$Sql_Push .= " SendMessageDateTime, ";
						$Sql_Push .= " SendMessageLogRegDateTime, ";
						$Sql_Push .= " SendMessageLogModiDateTime, ";
						$Sql_Push .= " UseSendPush, ";
						$Sql_Push .= " UseSendSms, ";
						$Sql_Push .= " UseSendKakao, ";
						$Sql_Push .= " DeviceToken, ";
						$Sql_Push .= " DeviceType, ";
						$Sql_Push .= " PushMessageState, ";
						$Sql_Push .= " SmsMessagePhoneNumber, ";
						$Sql_Push .= " SmsMessageState, ";
						$Sql_Push .= " KakaoMessagePhoneNumber, ";
						$Sql_Push .= " KakaoMessageState ";
					$Sql_Push .= " ) values ( ";
						$Sql_Push .= " :MemberID, ";
						$Sql_Push .= " :SendMemberID, ";
						$Sql_Push .= " :SendTitle, ";
						$Sql_Push .= " :SendMessage, ";
						$Sql_Push .= " :SendMemo, ";
						$Sql_Push .= " :SendMessageDateTime, ";
						$Sql_Push .= " now(), ";
						$Sql_Push .= " now(), ";
						$Sql_Push .= " :UseSendPush, ";
						$Sql_Push .= " :UseSendSms, ";
						$Sql_Push .= " :UseSendKakao, ";
						$Sql_Push .= " :DeviceToken, ";
						$Sql_Push .= " :DeviceType, ";
						$Sql_Push .= " 1, ";
						$Sql_Push .= " :SmsMessagePhoneNumber, ";
						$Sql_Push .= " 1, ";
						$Sql_Push .= " :KakaoMessagePhoneNumber, ";
						$Sql_Push .= " 1 ";
					$Sql_Push .= " ) ";

					$Stmt_Push = $GLOBALS['DbConn']->prepare($Sql_Push);
					$Stmt_Push->bindParam(':MemberID', $PointMemberID);
					$Stmt_Push->bindParam(':SendMemberID', $SendMemberID);
					$Stmt_Push->bindParam(':SendTitle', $SendTitle);
					$Stmt_Push->bindParam(':SendMessage', $MemberPointTypeText);
					$Stmt_Push->bindParam(':SendMemo', $SendMemo);
					$Stmt_Push->bindParam(':SendMessageDateTime', $SendMessageDateTime);
					$Stmt_Push->bindParam(':UseSendPush', $UseSendPush);
					$Stmt_Push->bindParam(':UseSendSms', $UseSendSms);
					$Stmt_Push->bindParam(':UseSendKakao', $UseSendKakao);
					$Stmt_Push->bindParam(':DeviceToken', $DeviceToken);
					$Stmt_Push->bindParam(':DeviceType', $DeviceType);
					$Stmt_Push->bindParam(':SmsMessagePhoneNumber', $SmsMessagePhoneNumber);
					$Stmt_Push->bindParam(':KakaoMessagePhoneNumber', $KakaoMessagePhoneNumber);
					$Stmt_Push->execute();
					$Stmt_Push = null;
				}
				//부모에게 푸시
				$Sql2 = "select
							ifnull((select DeviceToken from DeviceTokens where MemberID=A.MemberID order by ModiDateTime desc limit 0,1), '') as DeviceToken,
							ifnull((select DeviceType from DeviceTokens where MemberID=A.MemberID order by ModiDateTime desc limit 0,1), '') as DeviceType 
						from MemberChilds A 
						where A.MemberChildID=:MemberID";

				$Stmt2 = $GLOBALS['DbConn']->prepare($Sql2);
				$Stmt2->bindParam(':MemberID', $PointMemberID);
				$Stmt2->execute();
				
				while($Row2 = $Stmt2->fetch()) {
					$DeviceToken = $Row2["DeviceToken"];
					$DeviceType = $Row2["DeviceType"];


					if ($DeviceToken!=""){
						$Sql_Push = " insert into SendMessageLogs ( ";
							$Sql_Push .= " MemberID, ";
							$Sql_Push .= " SendMemberID, ";
							$Sql_Push .= " SendTitle, ";
							$Sql_Push .= " SendMessage, ";
							$Sql_Push .= " SendMemo, ";
							$Sql_Push .= " SendMessageDateTime, ";
							$Sql_Push .= " SendMessageLogRegDateTime, ";
							$Sql_Push .= " SendMessageLogModiDateTime, ";
							$Sql_Push .= " UseSendPush, ";
							$Sql_Push .= " UseSendSms, ";
							$Sql_Push .= " UseSendKakao, ";
							$Sql_Push .= " DeviceToken, ";
							$Sql_Push .= " DeviceType, ";
							$Sql_Push .= " PushMessageState, ";
							$Sql_Push .= " SmsMessagePhoneNumber, ";
							$Sql_Push .= " SmsMessageState, ";
							$Sql_Push .= " KakaoMessagePhoneNumber, ";
							$Sql_Push .= " KakaoMessageState ";
						$Sql_Push .= " ) values ( ";
							$Sql_Push .= " :MemberID, ";
							$Sql_Push .= " :SendMemberID, ";
							$Sql_Push .= " :SendTitle, ";
							$Sql_Push .= " :SendMessage, ";
							$Sql_Push .= " :SendMemo, ";
							$Sql_Push .= " :SendMessageDateTime, ";
							$Sql_Push .= " now(), ";
							$Sql_Push .= " now(), ";
							$Sql_Push .= " :UseSendPush, ";
							$Sql_Push .= " :UseSendSms, ";
							$Sql_Push .= " :UseSendKakao, ";
							$Sql_Push .= " :DeviceToken, ";
							$Sql_Push .= " :DeviceType, ";
							$Sql_Push .= " 1, ";
							$Sql_Push .= " :SmsMessagePhoneNumber, ";
							$Sql_Push .= " 1, ";
							$Sql_Push .= " :KakaoMessagePhoneNumber, ";
							$Sql_Push .= " 1 ";
						$Sql_Push .= " ) ";

						$Stmt_Push = $GLOBALS['DbConn']->prepare($Sql_Push);
						$Stmt_Push->bindParam(':MemberID', $PointMemberID);
						$Stmt_Push->bindParam(':SendMemberID', $SendMemberID);
						$Stmt_Push->bindParam(':SendTitle', $SendTitle);
						$Stmt_Push->bindParam(':SendMessage', $SendMessage);
						$Stmt_Push->bindParam(':SendMemo', $SendMemo);
						$Stmt_Push->bindParam(':SendMessageDateTime', $SendMessageDateTime);
						$Stmt_Push->bindParam(':UseSendPush', $UseSendPush);
						$Stmt_Push->bindParam(':UseSendSms', $UseSendSms);
						$Stmt_Push->bindParam(':UseSendKakao', $UseSendKakao);
						$Stmt_Push->bindParam(':DeviceToken', $DeviceToken);
						$Stmt_Push->bindParam(':DeviceType', $DeviceType);
						$Stmt_Push->bindParam(':SmsMessagePhoneNumber', $SmsMessagePhoneNumber);
						$Stmt_Push->bindParam(':KakaoMessagePhoneNumber', $KakaoMessagePhoneNumber);
						$Stmt_Push->execute();
						$Stmt_Push = null;
					}
				}
				$Stmt2 = null;
			}
		}
	}
}

// 수동으로 기입
function InsertNewTypePoint2($MemberPointNewTypeID, $RegMemberID, $PointMemberID, $MemberPointTypeName, $MemberPointTypeText, $MemberPoint){
	if ($PointMemberID>0){
		$Sql = "select 
					A.MemberLevelID,
					A.MemberName,
					AES_DECRYPT(UNHEX(A.MemberPhone1),:EncryptionKey) as DecMemberPhone1,
					ifnull((select DeviceToken from DeviceTokens where MemberID=A.MemberID order by ModiDateTime desc limit 0,1), '') as DeviceToken,
					ifnull((select DeviceType from DeviceTokens where MemberID=A.MemberID order by ModiDateTime desc limit 0,1), '') as DeviceType 
				from Members A 
				where A.MemberID=:MemberID";
		$Stmt = $GLOBALS['DbConn']->prepare($Sql);
		$Stmt->bindParam(':MemberID', $PointMemberID);
		$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
		$Stmt->execute();
		$Row = $Stmt->fetch();
		$Stmt = null;
		
		$MemberLevelID = $Row["MemberLevelID"];
		$DeviceToken = $Row["DeviceToken"];
		$DeviceType = $Row["DeviceType"];
		$SmsMessagePhoneNumber = $Row["DecMemberPhone1"];
		$KakaoMessagePhoneNumber = $Row["DecMemberPhone1"];
		$MemberName = $Row["MemberName"];

		/*
		if($MemberLevelID==19) {
		} else if($MemberLevelID==18) {
		} else if($MemberLevelID==12 or $MemberLevelID==13) {
		}
		*/

		$MemberPointTypeText = str_replace("{{이름}}", $MemberName, $MemberPointTypeText);
		$MemberPointTypeText = str_replace("{{포인트}}", number_format($MemberPoint,0), $MemberPointTypeText);


		$Sql = " insert into MemberPoints ( ";
			$Sql .= " MemberPointTypeID, ";
			$Sql .= " RegMemberID, ";
			$Sql .= " MemberID, ";
			$Sql .= " MemberPointName, ";
			$Sql .= " MemberPointText, ";
			$Sql .= " MemberPoint, ";
			$Sql .= " MemberPointRegDateTime, ";
			$Sql .= " MemberPointModiDateTime, ";
			$Sql .= " MemberPointState ";
			
		$Sql .= " ) values ( ";
			$Sql .= " :MemberPointTypeID, ";
			$Sql .= " :RegMemberID, ";
			$Sql .= " :MemberID, ";
			$Sql .= " :MemberPointName, ";
			$Sql .= " :MemberPointText, ";
			$Sql .= " :MemberPoint, ";
			$Sql .= " now(), ";
			$Sql .= " now(), ";
			$Sql .= " 1 ";
		$Sql .= " ) ";
			
		$Stmt = $GLOBALS['DbConn']->prepare($Sql);
		$Stmt->bindParam(':MemberPointTypeID', $MemberPointNewTypeID);
		$Stmt->bindParam(':RegMemberID', $RegMemberID);
		$Stmt->bindParam(':MemberID', $PointMemberID);
		$Stmt->bindParam(':MemberPointName', $MemberPointTypeName);
		$Stmt->bindParam(':MemberPointText', $MemberPointTypeText);
		$Stmt->bindParam(':MemberPoint', $MemberPoint);
		$Stmt->execute();
		$Stmt = null;

		$SendMemberID = 0;
		$SendTitle="적립알림";
		//$SendMessage=$MemberName." 님께서 ".$MemberPoint." 포인트(".$MemberPointTypeName.")를 적립하였습니다.";
		//$SendMessage=$MemberName." 학생에게 ".$MemberPoint." 포인트를 적립되었습니다.".$StrMemberPointTypeName;
		$SendMemo="";
		$SendMessageDateTime=date("Y-m-d H:i:s");
		$UseSendPush=1;
		$UseSendSms=0;
		$UseSendKakao=0;

		if($MemberLevelID==19) {//학생일때만 푸시 보냄

			//학생에게 푸시
			if ($DeviceToken!=""){
				$Sql_Push = " insert into SendMessageLogs ( ";
					$Sql_Push .= " MemberID, ";
					$Sql_Push .= " SendMemberID, ";
					$Sql_Push .= " SendTitle, ";
					$Sql_Push .= " SendMessage, ";
					$Sql_Push .= " SendMemo, ";
					$Sql_Push .= " SendMessageDateTime, ";
					$Sql_Push .= " SendMessageLogRegDateTime, ";
					$Sql_Push .= " SendMessageLogModiDateTime, ";
					$Sql_Push .= " UseSendPush, ";
					$Sql_Push .= " UseSendSms, ";
					$Sql_Push .= " UseSendKakao, ";
					$Sql_Push .= " DeviceToken, ";
					$Sql_Push .= " DeviceType, ";
					$Sql_Push .= " PushMessageState, ";
					$Sql_Push .= " SmsMessagePhoneNumber, ";
					$Sql_Push .= " SmsMessageState, ";
					$Sql_Push .= " KakaoMessagePhoneNumber, ";
					$Sql_Push .= " KakaoMessageState ";
				$Sql_Push .= " ) values ( ";
					$Sql_Push .= " :MemberID, ";
					$Sql_Push .= " :SendMemberID, ";
					$Sql_Push .= " :SendTitle, ";
					$Sql_Push .= " :SendMessage, ";
					$Sql_Push .= " :SendMemo, ";
					$Sql_Push .= " :SendMessageDateTime, ";
					$Sql_Push .= " now(), ";
					$Sql_Push .= " now(), ";
					$Sql_Push .= " :UseSendPush, ";
					$Sql_Push .= " :UseSendSms, ";
					$Sql_Push .= " :UseSendKakao, ";
					$Sql_Push .= " :DeviceToken, ";
					$Sql_Push .= " :DeviceType, ";
					$Sql_Push .= " 1, ";
					$Sql_Push .= " :SmsMessagePhoneNumber, ";
					$Sql_Push .= " 1, ";
					$Sql_Push .= " :KakaoMessagePhoneNumber, ";
					$Sql_Push .= " 1 ";
				$Sql_Push .= " ) ";

				$Stmt_Push = $GLOBALS['DbConn']->prepare($Sql_Push);
				$Stmt_Push->bindParam(':MemberID', $PointMemberID);
				$Stmt_Push->bindParam(':SendMemberID', $SendMemberID);
				$Stmt_Push->bindParam(':SendTitle', $SendTitle);
				$Stmt_Push->bindParam(':SendMessage', $MemberPointTypeText);
				$Stmt_Push->bindParam(':SendMemo', $SendMemo);
				$Stmt_Push->bindParam(':SendMessageDateTime', $SendMessageDateTime);
				$Stmt_Push->bindParam(':UseSendPush', $UseSendPush);
				$Stmt_Push->bindParam(':UseSendSms', $UseSendSms);
				$Stmt_Push->bindParam(':UseSendKakao', $UseSendKakao);
				$Stmt_Push->bindParam(':DeviceToken', $DeviceToken);
				$Stmt_Push->bindParam(':DeviceType', $DeviceType);
				$Stmt_Push->bindParam(':SmsMessagePhoneNumber', $SmsMessagePhoneNumber);
				$Stmt_Push->bindParam(':KakaoMessagePhoneNumber', $KakaoMessagePhoneNumber);
				$Stmt_Push->execute();
				$Stmt_Push = null;
			}
			//부모에게 푸시
			$Sql2 = "select
						ifnull((select DeviceToken from DeviceTokens where MemberID=A.MemberID order by ModiDateTime desc limit 0,1), '') as DeviceToken,
						ifnull((select DeviceType from DeviceTokens where MemberID=A.MemberID order by ModiDateTime desc limit 0,1), '') as DeviceType 
					from MemberChilds A 
					where A.MemberChildID=:MemberID";

			$Stmt2 = $GLOBALS['DbConn']->prepare($Sql2);
			$Stmt2->bindParam(':MemberID', $PointMemberID);
			$Stmt2->execute();
			
			while($Row2 = $Stmt2->fetch()) {
				$DeviceToken = $Row2["DeviceToken"];
				$DeviceType = $Row2["DeviceType"];

				if ($DeviceToken!=""){
					$Sql_Push = " insert into SendMessageLogs ( ";
						$Sql_Push .= " MemberID, ";
						$Sql_Push .= " SendMemberID, ";
						$Sql_Push .= " SendTitle, ";
						$Sql_Push .= " SendMessage, ";
						$Sql_Push .= " SendMemo, ";
						$Sql_Push .= " SendMessageDateTime, ";
						$Sql_Push .= " SendMessageLogRegDateTime, ";
						$Sql_Push .= " SendMessageLogModiDateTime, ";
						$Sql_Push .= " UseSendPush, ";
						$Sql_Push .= " UseSendSms, ";
						$Sql_Push .= " UseSendKakao, ";
						$Sql_Push .= " DeviceToken, ";
						$Sql_Push .= " DeviceType, ";
						$Sql_Push .= " PushMessageState, ";
						$Sql_Push .= " SmsMessagePhoneNumber, ";
						$Sql_Push .= " SmsMessageState, ";
						$Sql_Push .= " KakaoMessagePhoneNumber, ";
						$Sql_Push .= " KakaoMessageState ";
					$Sql_Push .= " ) values ( ";
						$Sql_Push .= " :MemberID, ";
						$Sql_Push .= " :SendMemberID, ";
						$Sql_Push .= " :SendTitle, ";
						$Sql_Push .= " :SendMessage, ";
						$Sql_Push .= " :SendMemo, ";
						$Sql_Push .= " :SendMessageDateTime, ";
						$Sql_Push .= " now(), ";
						$Sql_Push .= " now(), ";
						$Sql_Push .= " :UseSendPush, ";
						$Sql_Push .= " :UseSendSms, ";
						$Sql_Push .= " :UseSendKakao, ";
						$Sql_Push .= " :DeviceToken, ";
						$Sql_Push .= " :DeviceType, ";
						$Sql_Push .= " 1, ";
						$Sql_Push .= " :SmsMessagePhoneNumber, ";
						$Sql_Push .= " 1, ";
						$Sql_Push .= " :KakaoMessagePhoneNumber, ";
						$Sql_Push .= " 1 ";
					$Sql_Push .= " ) ";

					$Stmt_Push = $GLOBALS['DbConn']->prepare($Sql_Push);
					$Stmt_Push->bindParam(':MemberID', $PointMemberID);
					$Stmt_Push->bindParam(':SendMemberID', $SendMemberID);
					$Stmt_Push->bindParam(':SendTitle', $SendTitle);
					$Stmt_Push->bindParam(':SendMessage', $SendMessage);
					$Stmt_Push->bindParam(':SendMemo', $SendMemo);
					$Stmt_Push->bindParam(':SendMessageDateTime', $SendMessageDateTime);
					$Stmt_Push->bindParam(':UseSendPush', $UseSendPush);
					$Stmt_Push->bindParam(':UseSendSms', $UseSendSms);
					$Stmt_Push->bindParam(':UseSendKakao', $UseSendKakao);
					$Stmt_Push->bindParam(':DeviceToken', $DeviceToken);
					$Stmt_Push->bindParam(':DeviceType', $DeviceType);
					$Stmt_Push->bindParam(':SmsMessagePhoneNumber', $SmsMessagePhoneNumber);
					$Stmt_Push->bindParam(':KakaoMessagePhoneNumber', $KakaoMessagePhoneNumber);
					$Stmt_Push->execute();
					$Stmt_Push = null;
				}
			}
			$Stmt2 = null;
		}
	}
}

function InsertPointWithRootOrderID($MemberPointTypeID, $RegMemberID, $PointMemberID, $MemberPointName, $MemberPointText, $MemberPoint, $RootOrderID){

/*
	if ($PointMemberID>0){
		
		$Sql = " insert into MemberPoints ( ";
			
			$Sql .= " MemberPointTypeID, ";
			$Sql .= " RootOrderID, ";
			$Sql .= " RegMemberID, ";
			$Sql .= " MemberID, ";
			$Sql .= " MemberPointName, ";
			$Sql .= " MemberPointText, ";
			$Sql .= " MemberPoint, ";
			$Sql .= " MemberPointRegDateTime, ";
			$Sql .= " MemberPointModiDateTime, ";
			$Sql .= " MemberPointState ";
		$Sql .= " ) values ( ";
			$Sql .= " :MemberPointTypeID, ";
			$Sql .= " :RootOrderID, ";
			$Sql .= " :RegMemberID, ";
			$Sql .= " :MemberID, ";
			$Sql .= " :MemberPointName, ";
			$Sql .= " :MemberPointText, ";
			$Sql .= " :MemberPoint, ";
			$Sql .= " now(), ";
			$Sql .= " now(), ";
			$Sql .= " 1 ";
		$Sql .= " ) ";
		
		$Stmt = $GLOBALS['DbConn']->prepare($Sql);
		var_dump($Stmt);
		$Stmt->bindParam(':MemberPointTypeID', $MemberPointTypeID);
		$Stmt->bindParam(':RootOrderID', $RootOrderID);
		$Stmt->bindParam(':RegMemberID', $RegMemberID);
		$Stmt->bindParam(':MemberID', $PointMemberID);
		$Stmt->bindParam(':MemberPointName', $MemberPointName);
		$Stmt->bindParam(':MemberPointText', $MemberPointText);
		$Stmt->bindParam(':MemberPoint', $MemberPoint);
		$Stmt->execute();
		$Stmt = null;
	}
*/

}

	/*
function MunjaSend($OnlineSiteID, $RegMemberID, $MemberID, $SmsSendTypeID, $SmsReceiveType, $SmsReceiveNumber, $SmsSendName, $SmsSendText, $SmsReserve, $SmsSendDateTime, $InsertLog){
	//$SmsSendTypeID --> 0 이아니면 DB 에서 내용을 가져온 것이다. 하지만 여기서 가져오지 않고.. 보내는 측에서 가져와 적절히 변형 후 들어온게 된다.
	//$SmsReceiveType --> 1:$SmsReceiveNumber 로 보냄, 2: $OnlineSiteReceiveNumber 로 보냄
	

	$CheckHpNumber = 0;
	$hp = $SmsReceiveNumber;
	$hp = preg_replace("/[^0-9]/", "", $hp);
	if(preg_match("/^01[0-9]{8,9}$/", $hp)){
	
		$Sql = "
			select 
					A.*
			from OnlineSites A 
			where A.OnlineSiteID=:OnlineSiteID";
		$Stmt = $GLOBALS['DbConn']->prepare($Sql);
		$Stmt->bindParam(':OnlineSiteID', $OnlineSiteID);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;

		$OnlineSiteSmsID = $Row["OnlineSiteSmsID"];
		$OnlineSiteSmsPW = $Row["OnlineSiteSmsPW"];
		$OnlineSiteSendNumber = $Row["OnlineSiteSendNumber"];
		$OnlineSiteReceiveNumber = $Row["OnlineSiteReceiveNumber"];


		if ($SmsReceiveType!=1){
			$SmsReceiveNumber = str_replace("-","",$OnlineSiteReceiveNumber);
		}

		// 문자 메시지 검증 ========================================
		$host = "www.munja114.co.kr";
		$id = $OnlineSiteSmsID; // 문자114 아이디 입력
		$pass = $OnlineSiteSmsPW; // 문자114 비밀번호 입력
		$callback = str_replace("-","",$OnlineSiteSendNumber);

		$contents = "";
		$etc1 = "";
		$etc2 = "";
		$name = "";

		if (!preg_match("/[0-9]/", $RegMemberID)) { $RegMemberID = 0; }
		if (!preg_match("/[0-9]/", $MemberID)) { $MemberID = 0; }


		if ($SmsReserve!=1){
			$SmsReserve=0;
			$SmsSendDateTime = date("Y-m-d H:i:s");
		}


		if (mb_strwidth ( $SmsSendText,"UTF-8" )>90){
			$mtype = "lms";
		}else{
			$mtype = "";
		}	
		// 문자 메시지 검증 ========================================

		// 전송 로그 ========================================
		if ($InsertLog==1){
			$Sql = " insert into SmsSends ( ";
				$Sql .= " SmsSendTypeID, ";
				$Sql .= " RegMemberID, ";
				$Sql .= " MemberID, ";
				$Sql .= " SmsSendDateTime, ";
				$Sql .= " SmsSendName, ";
				$Sql .= " SmsSendText, ";
				$Sql .= " SmsSendRegDateTime, ";
				$Sql .= " SmsSendModiDateTime, ";
				$Sql .= " SmsSendState ";
			$Sql .= " ) values ( ";
				$Sql .= " :SmsSendTypeID, ";
				$Sql .= " :RegMemberID, ";
				$Sql .= " :MemberID, ";
				$Sql .= " :SmsSendDateTime, ";
				$Sql .= " :SmsSendName, ";
				$Sql .= " :SmsSendText, ";
				$Sql .= " now(), ";
				$Sql .= " now(), ";
				$Sql .= " 1 ";
			$Sql .= " ) ";

			$Stmt = $GLOBALS['DbConn']->prepare($Sql);
			$Stmt->bindParam(':SmsSendTypeID', $SmsSendTypeID);
			$Stmt->bindParam(':RegMemberID', $RegMemberID);
			$Stmt->bindParam(':MemberID', $MemberID);
			$Stmt->bindParam(':SmsSendDateTime', $SmsSendDateTime);
			$Stmt->bindParam(':SmsSendName', $SmsSendName);
			$Stmt->bindParam(':SmsSendText', $SmsSendText);
			$Stmt->execute();
			$Stmt = null;
		}
		// 전송 로그 ========================================
		
		
		$param = "remote_id=".$id;
		$param .= "&remote_pass=".$pass;
		$param .= "&remote_reserve=".$SmsReserve;
		$param .= "&remote_reservetime=".$SmsSendDateTime;
		$param .= "&remote_name=".$name;
		$param .= "&remote_phone=".$SmsReceiveNumber;
		$param .= "&remote_callback=".$callback;
		$param .= "&remote_msg=".$SmsSendText;
		$param .= "&remote_contents=".$contents;
		$param .= "&remote_etc1=".$etc1;
		$param .= "&remote_etc2=".$etc2;
		if ($mtype == "lms") {
			$path = "/Remote/RemoteMms.html";
		} else {
			$path = "/Remote/RemoteSms.html";
		}
		$fp = @fsockopen($host,80,$errno,$errstr,30);
		$return = "";
		if (!$fp) {
			echo $errstr."(".$errno.")";
		} else {
			fputs($fp, "POST ".$path." HTTP/1.1\r\n");
			fputs($fp, "Host: ".$host."\r\n");
			fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
			fputs($fp, "Content-length: ".strlen($param)."\r\n");
			fputs($fp, "Connection: close\r\n\r\n");
			fputs($fp, $param."\r\n\r\n");
			while(!feof($fp)) $return .= fgets($fp,4096);
		}
		fclose ($fp);
		$_temp_array = explode("\r\n\r\n", $return);
		$_temp_array2 = explode("\r\n", $_temp_array[1]);
		if (sizeof($_temp_array2) > 1) {
			$return_string = $_temp_array2[1];
		} else {
			$return_string = $_temp_array2[0];
		}
		return $return_string;

	}else{
		return "유효하지않은 휴대폰번호";
	}
}
*/

/*
function send_notification ($token, $title, $message){
	
	$server_key = 'AAAAfmHgpkE:APA91bHpji0wsR3E5hP5HYZt1l56RYFWawSAFjLVJSwS6W-7KbeOq3Kd_7zr4dExs2KbuJnyPIRbf0WR0IsDS7ye_0Cop1grlkRednoicvIKIW1RW-kE3Pba0x3keU8Sa5clMVRGxKZB';
	$str_result = "";

	//등록 하기 ==============================================
	$url = 'https://iid.googleapis.com/iid/v1:batchAdd';
	$fields['registration_tokens'] = array($token);
	$fields['to'] = '/topics/my-app';
	$headers = array(
		'Content-Type:application/json',
		'Authorization:key='.$server_key
	);


	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
	$result = curl_exec($ch);
	curl_close($ch);
	$str_result .= "//" . $result;
	//var_dump($result);
	//등록 하기 ==============================================


	//발송 하기 ==============================================
	$payload = array(
		'to'=>'/topics/my-app',
		'priority'=>'high',
		"mutable_content"=>true,
		"notification"=>array(
			"title"=> $title,
			"body"=> $message
		)
	);

	$headers = array(
		'Authorization:key ='.$server_key,
		'Content-Type: application/json'
	);

    $ch = curl_init();
    curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
    curl_setopt( $ch,CURLOPT_POST, true );
    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $payload ) );
    $result = curl_exec($ch );
    curl_close( $ch );
	$str_result .= "//" . $result;
    //var_dump($result);
	//발송 하기 ==============================================

	//삭제 하기 ==============================================
	$url = "https://iid.googleapis.com/iid/v1:batchRemove";
	$fields['registration_tokens'] = array($token);
	$fields['to'] = '/topics/my-app';
	$headers = array(
		'Content-Type:application/json',
		'Authorization:key='.$server_key
	);


	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
	$result = curl_exec($ch);
	curl_close($ch);
	$str_result .= "//" . $result;
	//var_dump($result);
	//삭제 하기 ==============================================
	return $str_result;
}
*/

function GetMemberSmsInfo($MemberID){
	global $EncryptionKey;

	$Sql = "SELECT  
					A.MemberName,
					AES_DECRYPT(UNHEX(A.MemberPhone1),:EncryptionKey) as DecMemberPhone1,
					AES_DECRYPT(UNHEX(A.MemberPhone2),:EncryptionKey) as DecMemberPhone2
					from Members A 
					where A.MemberID=:MemberID";
	$Stmt = $GLOBALS['DbConn']->prepare($Sql);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
	$Stmt->execute();
	$Row = $Stmt->fetch();
	$Stmt = null;

	return $Row;

}

function SendSmsWelcome($MemberID_SSW, $EncryptionKey_SSW){

	$Row_SSW = GetMemberSmsInfo($MemberID_SSW);
	
	$MemberName_SSW = $Row_SSW["MemberName"];
	$DecMemberPhone1_SSW = $Row_SSW["DecMemberPhone1"];
	$DecMemberPhone2_SSW = $Row_SSW["DecMemberPhone2"];


	$msg = "안녕하세요 망고아이 입니다
	$MemberName_SSW 학생의 회원가입을 축하 드립니다
	레벨테스트 배정과 수강 배정 방법 수강 연장 방법을 설명 드립니다.

	1) 레벨테스트 배정
	망고아이 홈페이지접속 http://www.mangoi.co.kr
	- 레벨테스트 - 레벨테스트 신청 - 강사리스트중
	원하는 부분 선택(강사/요일/시간) - 레벨테스트 예약
	신청동기/레벨/경험/참고사항 작성 - 신청하기
	-> 신청확인은 마이페이지 시간표출력 or 오늘의 수업(공부방입장) 에서 확인 가능

	2) 수강 배정 방법(신규 or 추가배정일 경우)
	망고아이 홈페이지접속 http://www.mangoi.co.kr
	- 수강신청 - 수강신청및수강료결제 - 신규수강생 신청하기
	- 원하는 키워드 / 수업횟수 (주기준) / 시작일 설정후
	검색 (강사찾기) - 강사리스트중 원하는 부분 선택
	(강사/요일/시간) - 수강신청하기 버튼 선택 확인 - 수강기간 원하시는 부분선택 - 정보/금액 확인 - 결제하기 (카드/계좌이체가능. 무통장입금은 포함되지 않습니다)
	-> 결제확인은 마이페이지 결제내역 - 나의결제내역에서 확인 가능


	3) 수강 연장 (기존회원 수업연장 하실 경우)
	망고아이 홈페이지접속 http://www.mangoi.co.kr/
	- 마이페이지 - 결제내역 - 나의수강신청정보 - 수강연장
	순으로 결제를 진행 (카드/계좌이체가능. 무통장입금은 포함되지 않습니다)";
	
	$tmplId="mangoi_001";  //카카오 알림톡 템플릿 아이디(문자 메시지를 바꿀려면 비즈엠에서 템플릿 등록하고 그 메시지를 위에 복사후 템플릿id도 바꿔야 함)

	if (!empty($DecMemberPhone1_SSW))
		SendAlimtalk($DecMemberPhone1_SSW, $msg,$tmplId);
	if (!empty($DecMemberPhone2_SSW))		
		SendAlimtalk($DecMemberPhone2_SSW, $msg,$tmplId);


}



function SendSmsWelcome_old($MemberID_SSW, $EncryptionKey_SSW){

	$Sql_SSW = "select 
				A.MemberSendWelcomeSms,
				A.MemberName,
				AES_DECRYPT(UNHEX(A.MemberPhone1),:EncryptionKey) as DecMemberPhone1,
				AES_DECRYPT(UNHEX(A.MemberPhone2),:EncryptionKey) as DecMemberPhone2
			from Members A 
			where A.MemberID=:MemberID";
	$Stmt_SSW = $GLOBALS['DbConn']->prepare($Sql_SSW);
	$Stmt_SSW->bindParam(':MemberID', $MemberID_SSW);
	$Stmt_SSW->bindParam(':EncryptionKey', $EncryptionKey_SSW);
	$Stmt_SSW->execute();
	$Row_SSW = $Stmt_SSW->fetch();
	$Stmt_SSW = null;
	
	$MemberSendWelcomeSms_SSW = $Row_SSW["MemberSendWelcomeSms"];
	$MemberName_SSW = $Row_SSW["MemberName"];
	$DecMemberPhone1_SSW = $Row_SSW["DecMemberPhone1"];
	$DecMemberPhone2_SSW = $Row_SSW["DecMemberPhone2"];

	if ($MemberSendWelcomeSms_SSW==0){

		$SendMessage_SSW = "안녕하세요 망고아이 입니다
".$MemberName_SSW." 학생의 회원가입을 축하 드립니다
레벨테스트 배정과 수강 배정 방법 수강 연장 방법을 설명 드립니다.

1) 레벨테스트 배정
망고아이 홈페이지접속 http://www.mangoi.co.kr
- 레벨테스트 - 레벨테스트 신청 - 강사리스트중 
원하는 부분 선택(강사/요일/시간) -  레벨테스트 예약
신청동기/레벨/경험/참고사항 작성 - 신청하기
-> 신청확인은 마이페이지 시간표출력 or 오늘의 수업(공부방입장) 에서 확인 가능

2) 수강 배정 방법(신규 or 추가배정일 경우)
망고아이 홈페이지접속 http://www.mangoi.co.kr
- 수강신청 - 수강신청및수강료결제 - 신규수강생 신청하기
- 원하는 키워드 /  수업횟수 (주기준) / 시작일 설정후 
검색 (강사찾기) - 강사리스트중 원하는 부분 선택
(강사/요일/시간) -  수강신청하기 버튼 선택 확인 - 수강기간 원하시는 부분선택 - 정보/금액 확인 - 결제하기 (카드/계좌이체가능 무통장입금은 포함되지 않습니다 (결재X)
-> 결제확인은 마이페이지 결제내역 - 나의결제내역에서 확인 가능

3) 수강 연장 (기존회원 수업연장 하실 경우)
망고아이 홈페이지접속 http://www.mangoi.co.kr/
- 마이페이지 - 결제내역 - 나의수강신청정보 - 수강연장
순으로 결제를 진행 (카드/계좌이체가능 무통장입금은 포함되지 않습니다 (결재X)
 
4) 수업 입장 방법 (컴퓨터/노트북):
 1) www.mangoi.co.kr 접속
 2) 부여받은 아이디와 비번 로그인
 3) 마이 페이지 클릭
 4) 공부방 들어가기 클릭
 5) 수업 입장 클릭 
*노트북 원격설치를 원하시는경우 카톡 답변 주시면
순차적으로 확인 후 진행드리도록 하겠습니다  (주중 오전 10:30~ 오후 6:00)

5) 수업 입장 방법 (태블릿/핸드폰):
 1) 구글 play store에서 망고아이 다운로드
 2) 구글 play store에서 스쿨넷 다운로드
 3) 망고아이 어플 실행
 4) 부여받은 아이디와 비번 로그인
 5) MY 바로가기 클릭
 6) 수업 입장 클릭

궁금하신 사항이나 문의사항 있으신경우 카카오톡플러스
http://pf.kakao.com/_xlqnSxd
접속후 상담남겨주시면 순차적으로 상담드리도록 하겠습니다. 감사합니다.";
		$SendMemo_SSW = "";//공백
		$DeviceToken_SSW = "";//공백
		$DeviceType_SSW = "";//공백
		

		//========================== 학생폰 ===================================
		$Sql_SSW = " insert into SendMessageLogs ( ";
			$Sql_SSW .= " MemberID, ";
			$Sql_SSW .= " SendMemberID, ";
			$Sql_SSW .= " SendTitle, ";
			$Sql_SSW .= " SendMessage, ";
			$Sql_SSW .= " SendMemo, ";
			$Sql_SSW .= " SendMessageDateTime, ";
			$Sql_SSW .= " SendMessageLogRegDateTime, ";
			$Sql_SSW .= " SendMessageLogModiDateTime, ";
			$Sql_SSW .= " UseSendPush, ";
			$Sql_SSW .= " UseSendSms, ";
			$Sql_SSW .= " UseSendKakao, ";
			$Sql_SSW .= " DeviceToken, ";
			$Sql_SSW .= " DeviceType, ";
			$Sql_SSW .= " PushMessageState, ";
			$Sql_SSW .= " SmsMessagePhoneNumber, ";
			$Sql_SSW .= " SmsMessageState, ";
			$Sql_SSW .= " KakaoMessagePhoneNumber, ";
			$Sql_SSW .= " KakaoMessageState ";
		$Sql_SSW .= " ) values ( ";
			$Sql_SSW .= " :MemberID, ";
			$Sql_SSW .= " 0, ";
			$Sql_SSW .= " '망고아이 사이트 이용안내', ";
			$Sql_SSW .= " :SendMessage, ";
			$Sql_SSW .= " :SendMemo, ";
			$Sql_SSW .= " now(), ";
			$Sql_SSW .= " now(), ";
			$Sql_SSW .= " now(), ";
			$Sql_SSW .= " 0, ";
			$Sql_SSW .= " 1, ";
			$Sql_SSW .= " 1, ";
			$Sql_SSW .= " :DeviceToken, ";
			$Sql_SSW .= " :DeviceType, ";
			$Sql_SSW .= " 1, ";
			$Sql_SSW .= " :SmsMessagePhoneNumber, ";
			$Sql_SSW .= " 1, ";
			$Sql_SSW .= " :KakaoMessagePhoneNumber, ";
			$Sql_SSW .= " 1 ";
		$Sql_SSW .= " ) ";

		$Stmt_SSW = $GLOBALS['DbConn']->prepare($Sql_SSW);
		$Stmt_SSW->bindParam(':MemberID', $MemberID_SSW);
		$Stmt_SSW->bindParam(':SendMessage', $SendMessage_SSW);
		$Stmt_SSW->bindParam(':SendMemo', $SendMemo_SSW);
		$Stmt_SSW->bindParam(':DeviceToken', $DeviceToken_SSW);
		$Stmt_SSW->bindParam(':DeviceType', $DeviceType_SSW);
		$Stmt_SSW->bindParam(':SmsMessagePhoneNumber', $DecMemberPhone1_SSW);
		$Stmt_SSW->bindParam(':KakaoMessagePhoneNumber', $DecMemberPhone1_SSW);
		$Stmt_SSW->execute();
		$Stmt_SSW = null;
		//========================== 학생폰 ===================================

		//========================== 부모폰 ===================================
		$Sql_SSW = " insert into SendMessageLogs ( ";
			$Sql_SSW .= " MemberID, ";
			$Sql_SSW .= " SendMemberID, ";
			$Sql_SSW .= " SendTitle, ";
			$Sql_SSW .= " SendMessage, ";
			$Sql_SSW .= " SendMemo, ";
			$Sql_SSW .= " SendMessageDateTime, ";
			$Sql_SSW .= " SendMessageLogRegDateTime, ";
			$Sql_SSW .= " SendMessageLogModiDateTime, ";
			$Sql_SSW .= " UseSendPush, ";
			$Sql_SSW .= " UseSendSms, ";
			$Sql_SSW .= " UseSendKakao, ";
			$Sql_SSW .= " DeviceToken, ";
			$Sql_SSW .= " DeviceType, ";
			$Sql_SSW .= " PushMessageState, ";
			$Sql_SSW .= " SmsMessagePhoneNumber, ";
			$Sql_SSW .= " SmsMessageState, ";
			$Sql_SSW .= " KakaoMessagePhoneNumber, ";
			$Sql_SSW .= " KakaoMessageState ";
		$Sql_SSW .= " ) values ( ";
			$Sql_SSW .= " :MemberID, ";
			$Sql_SSW .= " 0, ";
			$Sql_SSW .= " '망고아이 사이트 이용안내', ";
			$Sql_SSW .= " :SendMessage, ";
			$Sql_SSW .= " :SendMemo, ";
			$Sql_SSW .= " now(), ";
			$Sql_SSW .= " now(), ";
			$Sql_SSW .= " now(), ";
			$Sql_SSW .= " 0, ";
			$Sql_SSW .= " 1, ";
			$Sql_SSW .= " 1, ";
			$Sql_SSW .= " :DeviceToken, ";
			$Sql_SSW .= " :DeviceType, ";
			$Sql_SSW .= " 1, ";
			$Sql_SSW .= " :SmsMessagePhoneNumber, ";
			$Sql_SSW .= " 1, ";
			$Sql_SSW .= " :KakaoMessagePhoneNumber, ";
			$Sql_SSW .= " 1 ";
		$Sql_SSW .= " ) ";

		$Stmt_SSW = $GLOBALS['DbConn']->prepare($Sql_SSW);
		$Stmt_SSW->bindParam(':MemberID', $MemberID_SSW);
		$Stmt_SSW->bindParam(':SendMessage', $SendMessage_SSW);
		$Stmt_SSW->bindParam(':SendMemo', $SendMemo_SSW);
		$Stmt_SSW->bindParam(':DeviceToken', $DeviceToken_SSW);
		$Stmt_SSW->bindParam(':DeviceType', $DeviceType_SSW);
		$Stmt_SSW->bindParam(':SmsMessagePhoneNumber', $DecMemberPhone2_SSW);
		$Stmt_SSW->bindParam(':KakaoMessagePhoneNumber', $DecMemberPhone2_SSW);
		$Stmt_SSW->execute();
		$Stmt_SSW = null;
		//========================== 부모폰 ===================================


		$Sql_SSW = " update Members set ";
			$Sql_SSW .= " MemberSendWelcomeSms = 1, ";
			$Sql_SSW .= " MemberModiDateTime = now() ";
		$Sql_SSW .= " where MemberID = :MemberID ";
		$Stmt_SSW = $GLOBALS['DbConn']->prepare($Sql_SSW);
		$Stmt_SSW->bindParam(':MemberID', $MemberID_SSW);
		$Stmt_SSW->execute();
		$Stmt_SSW = null;


	}


}



//시간 SNS 형식으로 표시
function DisplayDatetime($datetime = ''){
	
	if (empty($datetime)) {
        return false;
    }

    $diff = time() - strtotime($datetime);

    $s = 60; //1분 = 60초
    $h = $s * 60; //1시간 = 60분
    $d = $h * 24; //1일 = 24시간
    $y = $d * 10; //1년 = 1일 * 10일

    if ($diff < $s) {
        $QueryResult = $diff . '초전';
    } elseif ($h > $diff && $diff >= $s) {
        $QueryResult = round($diff/$s) . '분전';
    } elseif ($d > $diff && $diff >= $h) {
        $QueryResult = round($diff/$h) . '시간전';
    } elseif ($y > $diff && $diff >= $d) {
        //$QueryResult = round($diff/$d) . '일전';
		$QueryResult = date('Y.m.d. A h:i', strtotime($datetime));
    } else {
    	//$QueryResult = date('Y.m.d.', strtotime($datetime));
		$QueryResult = date('Y.m.d. A h:i', strtotime($datetime));
    }

    return $QueryResult;
}


function StrCut_utf8($str, $len){
    preg_match_all('/[\xE0-\xFF][\x80-\xFF]{2}|./', $str, $match);
    $m = $match[0];
    $slen = strlen($str); // length of source string
    $tail = '...';
    $tlen = $tail; // length of tail string
    if ($slen <= $len) return $str;
    $ret = array();
    $count = 0;
    for ($i=0; $i < $len; $i++){
        $count += (strlen($m[$i]) > 1)?2:1;
 
        if ($count + $tlen > $len) break;
        $ret[] = $m[$i];
    }
    return join('', $ret).$tail;
}


function StrCut_euckr($msg, $limit)
{
    $msg = substr($msg, 0, $limit);
 
    for ($i = $limit - 1; $i > 1; $i--)
    {   
        if (ord(substr($msg,$i,1)) < 128) break;
    }
 
    $msg = substr($msg, 0, $limit - ($limit - $i + 1) % 2);
 
    return $msg;
}



function Coupon_Generator($clen){
    $len = $clen;
    $chars = "ABCDEFGHJKLMNPQRSTUVWXY3456789";

    srand((double)microtime()*1000000);

    $i = 0;
    $str = "";

    while ($i < $len) {
        $num = rand() % strlen($chars);
        $tmp = substr($chars, $num, 1);
        $str .= $tmp;
        $i++;
    }

    $str = preg_replace("/([0-9A-Z]{4})([0-9A-Z]{4})([0-9A-Z]{4})([0-9A-Z]{4})/", "\1-\2-\3-\4", $str);

    return $str;
}




function TimestampToDate($TimeStamp, $TimeZone) {
	// ex, GetTimeStamp(16050230, Asia/Seoul)
	$Date = DateTime::createFromFormat('Y-m-d H:i:s', $TimeStamp);
	$NewTimeStamp = new DateTime($Date, new DateTimeZone($TimeZone));

	return $NewTimeStamp->format('Y-m-d H:i:s');
}

function DateToTimestamp($Date, $TimeZone) {
	// ex, GetTimeStamp(16050230, 9)
	$NewTimeStamp = new DateTime($Date, new DateTimeZone($TimeZone));

	return $NewTimeStamp->getTimestamp(); // 1457690400
}

//카카오 알림톡으로 메시지 전송
function SendAlimtalk($phn, $msg, $tmplId){
	$url = "https://alimtalk-api.bizmsg.kr/v2/sender/send"; //주소셋팅
	
	//추가할 헤더값이 있을시 추가하면 됨
	 $headers = array(
	 	"userid:mangoi",
	 	"content-type:application/json"
	 );

	//POST방식으로 보낼 JSON데이터 생성
	$post_arr = [];
	
	$post_arr["message_type"] = "at";
	$post_arr["phn"] = $phn;
	$post_arr["profile"] = "29425cbfa7f359560a6d8ef74ac7fa9cb74c7a1c";
	$post_arr["msg"] = $msg;
	$post_arr["tmplId"] = $tmplId;

	//배열을 JSON데이터로 생성
	$post_data = json_encode(array($post_arr),JSON_UNESCAPED_UNICODE);
	//var_dump($post_data);

	//CURL함수 사용
	$ch=curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL, $url);
	//header값 셋팅(없을시 삭제해도 무방함)
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	//POST방식
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POST, true);
	//POST방식으로 넘길 데이터(JSON데이터)
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	curl_setopt($ch, CURLOPT_TIMEOUT, 3);

	$response = curl_exec($ch);

	if(curl_error($ch)){
		$curl_data = null;
	} else {
		$curl_data = $response;
	}

	curl_close($ch);

	$json_data= array();
	//받은 JSON데이터를 배열로 만듬
	$json_data = json_decode($curl_data,true);
	//var_dump($json_data);
	//배열 제어
	
	/*
	if($json_data["code"] == "success"){
	$cnt = 0;
	foreach($json_data["msg"] as $msg_data){
		foreach($msg_data as $msgval_data){
			//msg_val값만 출력합니다.
			echo $msgval_data[$cnt]["msg_val"];
			$cnt++;
		}
	}
	
	
	}
	*/
	

}

?>