<?php
include_once('./includes/dbopen.php');
include_once('./includes/common.php');
$DenyGuest = true;
include_once('./includes/member_check.php');

$MemberLevelID = $_LINK_MEMBER_LEVEL_ID_;

if($MemberLevelID==12 || $MemberLevelID==13) {
	header("Location: mypage_teacher_mode.php");
}		

$UseMain = 0;
$UseSub = 1;
$SubCode = "sub_07_2";
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
<link href="css/sub_style.css" rel="stylesheet" type="text/css" />
<?php
include_once('./includes/common_header.php');

$Sql = "select SubID from Subs where SubCode=:SubCode";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':SubCode', $SubCode);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$SubID = $Row["SubID"];


if ($UseMain==1){
	$Sql = "select * from Main limit 0,1";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$MainLayout = $Row["MainLayout"];
	$MainLayoutCss = $Row["MainLayoutCss"];
	$MainLayoutJavascript = $Row["MainLayoutJavascript"];
	list($MainLayoutTop, $MainLayoutBottom) = explode("{{Page}}", $MainLayout);
}else{
	$MainLayoutTop = "";
	$MainLayoutBottom = "";
	$MainLayoutCss = "";
	$MainLayoutJavascript = "";
}


if ($UseSub==1){
	$Sql = "select * from Subs where SubID=:SubID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':SubID', $SubID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$SubLayout = $Row["SubLayout"];
	$SubLayoutCss = $Row["SubLayoutCss"];
	$SubLayoutJavascript = $Row["SubLayoutJavascript"];
	list($SubLayoutTop, $SubLayoutBottom) = explode("{{Page}}", $SubLayout);
}else{
	$SubLayoutTop = "";
	$SubLayoutBottom = "";
	$SubLayoutCss = "";
	$SubLayoutJavascript = "";
}


if (trim($MainLayoutCss)!=""){
	echo "\n";
	echo "<style>";
	echo "\n";
	echo $MainLayoutCss;
	echo "\n";
	echo "</style>";
	echo "\n";
}

if (trim($SubLayoutCss)!=""){
	echo "\n";
	echo "<style>";
	echo "\n";
	echo $SubLayoutCss;
	echo "\n";
	echo "</style>";
	echo "\n";
}

?>
<!-- amchart4 -->
<script src="./amcharts4/core.js"></script>
<script src="./amcharts4/charts.js"></script>
<script src="./amcharts4/themes/animated.js"></script>
<!-- amchart4 -->
</head>
<body>
<?
include_once('./includes/common_body_top.php');
?>
<?php
// $MainLayoutTop = convertHTML(trim($MainLayoutTop));
// $SubLayoutTop = convertHTML(trim($SubLayoutTop));
// $SubLayoutBottom = convertHTML(trim($SubLayoutBottom));
// $MainLayoutBottom = convertHTML(trim($MainLayoutBottom));

if($DomainSiteID==7){
    $MainLayoutTop = convertHTML(trim("{{Piece(header_gumiivyleague)}}"));
    $SubLayoutTop = convertHTML(trim("{{Piece(sub_07_1_gumiivyleague)}}"));
    $SubLayoutBottom = convertHTML(trim("{{Piece(sub_01_gumiivyleague_bottom)}}"));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_gumiivyleague)}}"));

} else if($DomainSiteID==8){ //engliseed.kr
    $MainLayoutTop = convertHTML(trim("{{Piece(header_engliseed)}}"));
    $SubLayoutTop = convertHTML(trim("{{Piece(sub_06_engliseed)}}"));
    $SubLayoutBottom = convertHTML(trim("{{Piece(sub_01_engliseed_bottom)}}"));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_engliseed)}}"));

} else if($DomainSiteID==9){ //live.engedu.kr
    $MainLayoutTop = convertHTML(trim("{{Piece(header_engedu)}}"));
    $SubLayoutTop = convertHTML(trim("{{Piece(sub_06_engedu)}}"));
    $SubLayoutBottom = convertHTML(trim("{{Piece(sub_01_engedu_bottom)}}"));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_engedu)}}"));


} else {
    $MainLayoutTop = convertHTML(trim($MainLayoutTop));
    $SubLayoutTop = convertHTML(trim($SubLayoutTop));
    $SubLayoutBottom = convertHTML(trim($SubLayoutBottom));
    $MainLayoutBottom = convertHTML(trim($MainLayoutBottom));
  }

echo "\n";
echo $MainLayoutTop;
echo "\n";
echo $SubLayoutTop;
echo "\n";
?>

<?if($DomainSiteID==7){?>
    <div class="sub_wrap" style="margin-top:100px;">   
    <div class="sub_title_common_wrap"><h2 class="sub_title_common TrnTag"><b>마이</b>페이지</h2></div>

    <section class="mypage_wrap" style="border-bottom:0;">
        <div class="mypage_area">


			<?
			$HideLinkBtn = 0;
			include_once('mypage_student_info_include.php');
			?>			
			
			<div id="tip_top"></div>
            <ul class="mypage_tip_btns">
                <li><a href="#tip_1" onclick="$('#tip_1').animatescroll();" class="TrnTag"><img src="images/icon_tip_1.png"><b>화상강의실</b> 이용팁</a></li>
                <!--li><a href="#tip_2" onclick="$('#tip_2').animatescroll();"><img src="images/icon_tip_2.png"><b>웹북</b> 이용팁</a></li-->
                <li><a href="#tip_3" onclick="$('#tip_3').animatescroll();" class="TrnTag"><img src="images/icon_tip_3.png"><b>수업 녹화방법</b> 보기</a></li>
            </ul>

        </div>
    </section>

    <section class="app_class_wrap bg_image">
        <div class="app_class_area mypage">
            <h3 class="caption_left_common center TrnTag">수강<b>안내</b></h3>
            <div class="class_guide_box">
                <ul class="class_guide_list">
                    <li class="table">
                        <table class="class_guide_list_table one">
                            <th class="TrnTag">수업 준비물 :</th>
                            <td class="TrnTag">테블릿 PC , 노트북 또는 데스크 탑 컴퓨터(웹캠, 헤드셋 포함)</td>
                        </table>
                    </li>
                    <li class="table">
                        <table class="class_guide_list_table two">
                            <th class="TrnTag">교재 :</th>
                            <td class="TrnTag">1. 아이비리그 교재 / 2. 학당(어학원) 전용교재</td>
                        </table>
                    </li>
                    <li class="table">
                        <table class="class_guide_list_table three">
                            <th class="TrnTag">수업 스케줄 :</th>
                            <td class="TrnTag">14시~23시</td>
                        </table>
                    </li>
                    <li class="table">
                        <table class="class_guide_list_table four">
                            <th class="TrnTag">주 1회~5회 가능 :</th>
                            <td class="TrnTag">월 수업 수 계산으로 정산(예 : 주2회는 월 8회 수업)</td>
                        </table>
                    </li>
                    <li class="TrnTag">1회 수업은 20분, 2번 연속하여 40분 수업도 가능</li>
                </ul>
            </div>

            <ul class="class_guide_list one">
                <li class="TrnTag">수업연기 신청은 수업시작 30분전까지 My Page (나의 공부방)에 수업연기 버튼을 눌러서 원하는 일시를 눌러 자동으로 신청하시기 바랍니다.<br>연기 횟수는 수강 횟수의 1/2회까지 가능합니다.<br>(예: 주1회 최대 월 2회, 주 3회 시 최대 월 6회)</li>
            </ul>

            <ul class="class_guide_list two">
                <li class="TrnTag">수업연기요청은 수업시작 30분전에까지 교사의 공강 여부에 따라 가능합니다.</li>
                <li class="TrnTag">수강기간은 일수가 아닌 횟수로 정해집니다. <b>(주5회 수업 - 20회)</b></li>
                <li class="TrnTag">한국 공휴일은 공식적으로 휴강이고 <b>해당국가의 사정에 따라 휴강</b> 될수 있으며 수업 일수에 포함되지 않습니다.</li>
                <li class="TrnTag">재수강 등록은 수업 종료일 3일전까지이며 재수강 등록 시 기존 강사와의 수업시간은 그대로 유지됩니다.<br><b>수업 종료일 이후 등록시에는 타수강자의 등록으로 인하여 강사와 시간이 변경될 수 있습니다.</b></li>
            </ul>

            <ul class="class_guide_list three">
                <li class="TrnTag">월별 평가서는 총 10회 이상의 수강 진행 후 제공되며 주당 수업횟수에 따라 다를 수 있습니다.</li>
                <li class="TrnTag">월 10회 이하 수강시 학생의 평가가 어려운점 양해 부탁드립니다.</li>
            </ul>

            <ul class="class_guide_list">
                <li class="TrnTag">환불규정</li>
            </ul>

            <table class="app_class_refund_table blue">
                <col width="33.3%">
                <col width="33.3%">
                <col width="">
                <tr>
                    <th class="TrnTag">환불 요구 시기</th>
                    <th class="TrnTag">환불 금액(%)</th>
                    <th class="TrnTag">예) 수강료 100,000원 지불</th>
                </tr>
                <tr>
                    <td class="TrnTag">수업 시작 전</td>
                    <td class="TrnTag">납부금액의 100%</td>
                    <td class="TrnTag">100,000원 환불</td>
                </tr>
                <tr>
                    <td class="TrnTag">총 수업시간의 1/3 이전</td>
                    <td class="TrnTag">납부금액의 70%</td>
                    <td class="TrnTag">70,000원 환불</td>
                </tr>
                <tr>
                    <td class="TrnTag">총 수업시간의 1/2 이전</td>
                    <td class="TrnTag">납부금액의 50%</td>
                    <td class="TrnTag">50,000원 환불</td>
                </tr>
                <tr>
                    <td class="TrnTag">총 수업시간의 1/2 이후</td>
                    <td class="TrnTag">납부금액의 0%</td>
                    <td class="TrnTag">환불 안 됨</td>
                </tr>
            </table>

            <h3 class="caption_left_common center two TrnTag">수강<b>규정</b></h3>
            <div class="class_guide_box center">
                <h3 class="TrnTag">맞춤식 고정 수업 안내</h3>
                <trn class="TrnTag">강사 한분과 매일 또는 주3회, 주2회 정해진 시간에 규칙적으로 꾸준히 수업하려는 목표를 가진분을 위한 시스템입니다.</trn>
            </div>

            <h4 class="class_rule_caption" class="TrnTag">고정 수업 가이드</h4>
            <ul class="class_rule_list">
                <li class="TrnTag"><span>01.</span>수강료 결제를 완료하시면 24시간 내에 학습매니저가 회원님에게 카카오톡으로 수강 방법에 대해 안내해 드립니다.</li>
                <li class="TrnTag"><span>02.</span>수업스케줄이 잡히면 바로 수강하실 수 있으며, 교재 구입전이라도 온라인상의 샘플교재를 이용하여 수강하실 수 있습니다.</li>
                <li class="TrnTag"><span>03.</span>수강기간은 횟수로 정해집니다. (1개월 수업의 경우 주5회는 20회 수업, 주3회 수업은 12회 수업, 주2회 수업은 8회 수업)</li>
            </ul>

            <h4 class="class_rule_caption TrnTag">일일 수업 연기</h4>
            <ul class="class_rule_list">
                <li class="TrnTag"><span>01.</span>상황이 불가피하여 수업을 받을 수 없는 경우에 수업을 연기할 수 있습니다. </li>
                <li class="TrnTag"><span>02.</span>수업연기 횟수는 진도와 향상을 위해서 전체 월 수업 횟수의 ½만 가능합니다.</li>
                <li class="TrnTag"><span>03.</span>수업연기는 마이 페이지(My Page) > 공부방 입장 > 수업 연기요청으로 들어가시면 됩니다. 당일 수업의 연기는 교사의 수업상황에 따라서 수업 시작 30분 전까지 가능합니다.</li>
            </ul>

            <h4 class="class_rule_caption TrnTag">수업 시간 변경</h4>
            <ul class="class_rule_list">
                <li class="TrnTag"><span>01.</span>수업시간을 변경하고자 하는 경우에는 어플에서 연기신청을 눌러서 교사 또는 수업 시간 둘 중 선택가능합니다.</li>
                <li class="TrnTag"><span>02.</span>수업시간을 변경하시면 담당강사가 변경될 수 있으며, 변경신청 시 유념하시기 바랍니다.</li>
                <li class="TrnTag"><span>03.</span>수업시간 변경은 전체 월 수업시간의 ½회 가능합니다.</li>
            </ul>

            <h4 class="class_rule_caption TrnTag">담당 강사 변경</h4>
            <ul class="class_rule_list">
                <li class="TrnTag"><span>01.</span>담당강사를 바꾸자 하는 경우에는 [학습매니저 1:1게시판]을 이용하여 변경신청이 가능합니다.</li>
                <li class="TrnTag"><span>02.</span>담당강사 변경은 당일 수업의 1일 전까지 가능합니다.</li>
                <li class="TrnTag"><span>03.</span>담당강사 변경은 월1회 가능합니다. (단, 강사의 불성실로 인한 교체는 제한 횟수에 불포함)</li>
            </ul>

            <h4 class="class_rule_caption TrnTag">결석 처리 안내</h4>
            <ul class="class_rule_list">
                <li class="TrnTag"><span>01.</span>전화영어, 휴대전화영어 수업은 총3회 전화하여 응답이 없을 경우 결석처리 됩니다.</li>
                <li class="TrnTag"><span>02.</span>화상영어 수업은 강사가 10분간 대기하여도 강의실에 입장하지 않으면 결석처리 됩니다.</li>
                <li class="TrnTag"><span>03.</span>수강생의 사정으로 인해 수업시작이 지연된 경우에는 수업시간을 채우지 못했다 할지라도 예정된 종료시간에 수업이 종료됩니다.<br>(뒤이어 예약된 학생의 수업에 지장을 끼칠 우려가 있으므로 불가피함)</li>
            </ul>
        </div>        
    </section>

    <div id="tip_1"></div>
    <section class="mypage_tip_wrap_1">        
        <div class="mypage_tip_area">
            <h3 class="caption_sub_img TrnTag"><img src="images/icon_tip_1.png"><b>화상강의실</b> 이용팁</h3>
            <img src="images/img_monitor_tip_1.png" class="tip_img_1">
            <ul class="tip_list_1">
                <li class="TrnTag"><b>1</b> 수업녹화</li>
                <li class="TrnTag"><b>2</b> 웹사이트(수업 중 추가 필요자료는 인터넷 검색)</li>
                <li class="TrnTag"><b>3</b> 판서</li>
                <li class="TrnTag"><b>4</b> 타이핑</li>
                <li class="TrnTag"><b>5</b> 지우개</li>
                <li class="TrnTag"><b>6</b> 교재목록</li>
                <li class="TrnTag"><b>7</b> 채팅창</li>
            </ul>
			<a href="#tip_top" onclick="$('#tip_top').animatescroll();" class="mypage_tip_top TrnTag"><img src="images/arrow_top.png">위로</a>
        </div>        
    </section>

    <!--div id="tip_2"></div>
    <section class="mypage_tip_wrap_2">        
        <div class="mypage_tip_area">
            <h3 class="caption_sub_img"><img src="images/icon_tip_2.png"><b>웹북</b> 이용팁</h3>
            <div class="mypage_tip_box">
                <img src="images/img_monitor_tip_2.png" class="tip_img_2">
                <ul class="tip_list_2">
                    <li>
                        <b>1</b>
                        <h5>대분류 선택 버튼</h5>
                        정규/심화/특화로 분류된 대분류를 선택할 수 있는 버튼입니다.<br>
                        인트로 페이지 다음에 나타나는 메인 페이지에서도 대분류를 선택할 수 있습니다.
                    </li>
                    <li>
                        <b>2</b>
                        <h5>내 교재 바로가기 버튼</h5>
                        현재 수강중인 교재의 진도로 바로 이동하기 위한 버튼입니다.<br>
                        아직 수강중이지 않거나 교재가 결정되지 않은 경우 (첫 수업)에는 “미리 등록된 교재 정보가 없다”는 경고창이 나타납니다.
                    </li>
                </ul>
            </div>
            <div class="mypage_tip_box">
                <img src="images/img_monitor_tip_3.png" class="tip_img_3">
                <ul class="tip_list_3">
                    <li>
                        <b>1</b>
                        <h5>대분류 선택 버튼</h5>
                        정규/심화/특화로 분류된 대분류를 선택할 수 있는 버튼입니다.<br>
                        인트로 페이지 다음에 나타나는 메인 페이지에서도 대분류를 선택할 수 있습니다.                        
                    </li>
                    <li>
                        <b>2</b>
                        <h5>내 교재 바로가기 버튼</h5>
                        현재 수강중인 교재의 진도로 바로 이동하기 위한 버튼입니다.<br>                        
                        아직 수강중이지 않거나 교재가 결정되지 않은 경우 (첫 수업)에는 “미리 등록된 교재 정보가 없다”는 경고창이 나타납니다.                          
                    </li>                    
                </ul>                
            </div>
            <div class="text_center"><a href="#" class="button_blue_white">온라인 웹북 보기</a></div>
			<a href="#tip_top" onclick="$('#tip_top').animatescroll();" class="mypage_tip_top"><img src="images/arrow_top.png">위로</a>
        </div>        
    </section-->
    
    <div id="tip_3"></div>
    <section class="mypage_tip_wrap_3">        
        <div class="mypage_tip_area">
            <h3 class="caption_sub_img TrnTag"><img src="images/icon_tip_3.png"><b>수업 녹화방법</b> 보기</h3>
            <img src="images/img_monitor_tip_1.png" class="tip_img_1">
            <ul class="tip_list_1">
                <li class="TrnTag"><b>1</b> 수업녹화</li>
                <li class="TrnTag"><b>2</b> 웹사이트(수업 중 추가 필요자료는 인터넷 검색)</li>
                <li class="TrnTag"><b>3</b> 판서</li>
                <li class="TrnTag"><b>4</b> 타이핑</li>
                <li class="TrnTag"><b>5</b> 지우개</li>
                <li class="TrnTag"><b>6</b> 교재목록</li>
                <li class="TrnTag"><b>7</b> 채팅창</li>
            </ul>
			<a href="#tip_top" onclick="$('#tip_top').animatescroll();" class="mypage_tip_top TrnTag"><img src="images/arrow_top.png">위로</a>
        </div>
    </section>
</div>
<?}else{?>
<div class="sub_wrap" style="margin-top:100px;">   
    <div class="sub_title_common_wrap"><h2 class="sub_title_common TrnTag"><b>마이</b>페이지</h2></div>

    <section class="mypage_wrap" style="border-bottom:0;">
        <div class="mypage_area">


			<?
			$HideLinkBtn = 0;
			include_once('mypage_student_info_include.php');
			?>			
			
			<div id="tip_top"></div>
            <ul class="mypage_tip_btns">
                <li><a href="#tip_1" onclick="$('#tip_1').animatescroll();" class="TrnTag"><img src="images/icon_tip_1.png"><b>화상강의실</b> 이용팁</a></li>
                <!--li><a href="#tip_2" onclick="$('#tip_2').animatescroll();"><img src="images/icon_tip_2.png"><b>웹북</b> 이용팁</a></li-->
                <li><a href="#tip_3" onclick="$('#tip_3').animatescroll();" class="TrnTag"><img src="images/icon_tip_3.png"><b>수업 녹화방법</b> 보기</a></li>
            </ul>

        </div>
    </section>

    <section class="app_class_wrap bg_image">
        <div class="app_class_area mypage">
            <h3 class="caption_left_common center TrnTag">수강<b>안내</b></h3>
            <div class="class_guide_box">
                <ul class="class_guide_list">
                    <li class="table">
                        <table class="class_guide_list_table one">
                            <th class="TrnTag">수업 준비물 :</th>
                            <td class="TrnTag">테블릿 PC , 노트북 또는 데스크 탑 컴퓨터(웹캠, 헤드셋 포함)</td>
                        </table>
                    </li>
                    <li class="table">
                        <table class="class_guide_list_table two">
                            <th class="TrnTag">교재 :</th>
                            <td class="TrnTag">1. 자사 제작 교재 / 2. 학당(어학원) 전용교재</td>
                        </table>
                    </li>
                    <li class="table">
                        <table class="class_guide_list_table three">
                            <th class="TrnTag">수업 스케줄 :</th>
                            <td class="TrnTag">14시~23시</td>
                        </table>
                    </li>
                    <li class="table">
                        <table class="class_guide_list_table four">
                            <th class="TrnTag">주 1회~5회 가능 :</th>
                            <td class="TrnTag">월 수업 수 계산으로 정산(예 : 주2회는 월 8회 수업)</td>
                        </table>
                    </li>
                    <li class="TrnTag">1회 수업은 20분, 2번 연속하여 40분 수업도 가능</li>
                </ul>
            </div>

            <ul class="class_guide_list one">
                <li class="TrnTag">수업연기 신청은 수업시작 30분전까지 My Page (나의 공부방)에 수업연기 버튼을 눌러서 원하는 일시를 눌러 자동으로 신청하시기 바랍니다.<br>연기 횟수는 수강 횟수의 1/2회까지 가능합니다.<br>(예: 주1회 최대 월 2회, 주 3회 시 최대 월 6회)</li>
            </ul>

            <ul class="class_guide_list two">
                <li class="TrnTag">수업연기요청은 수업시작 30분전에까지 교사의 공강 여부에 따라 가능합니다.</li>
                <li class="TrnTag">수강기간은 일수가 아닌 횟수로 정해집니다. <b>(주5회 수업 - 20회)</b></li>
                <li class="TrnTag">한국 공휴일은 공식적으로 휴강이고 <b>해당국가의 사정에 따라 휴강</b> 될수 있으며 수업 일수에 포함되지 않습니다.</li>
                <li class="TrnTag">재수강 등록은 수업 종료일 3일전까지이며 재수강 등록 시 기존 강사와의 수업시간은 그대로 유지됩니다.<br><b>수업 종료일 이후 등록시에는 타수강자의 등록으로 인하여 강사와 시간이 변경될 수 있습니다.</b></li>
            </ul>

            <ul class="class_guide_list three">
                <li class="TrnTag">월별 평가서는 총 10회 이상의 수강 진행 후 제공되며 주당 수업횟수에 따라 다를 수 있습니다.</li>
                <li class="TrnTag">월 10회 이하 수강시 학생의 평가가 어려운점 양해 부탁드립니다.</li>
            </ul>

            <ul class="class_guide_list">
                <li class="TrnTag">환불규정</li>
            </ul>

            <table class="app_class_refund_table blue">
                <col width="33.3%">
                <col width="33.3%">
                <col width="">
                <tr>
                    <th class="TrnTag">환불 요구 시기</th>
                    <th class="TrnTag">환불 금액(%)</th>
                    <th class="TrnTag">예) 수강료 100,000원 지불</th>
                </tr>
                <tr>
                    <td class="TrnTag">수업 시작 전</td>
                    <td class="TrnTag">납부금액의 100%</td>
                    <td class="TrnTag">100,000원 환불</td>
                </tr>
                <tr>
                    <td class="TrnTag">총 수업시간의 1/3 이전</td>
                    <td class="TrnTag">납부금액의 70%</td>
                    <td class="TrnTag">70,000원 환불</td>
                </tr>
                <tr>
                    <td class="TrnTag">총 수업시간의 1/2 이전</td>
                    <td class="TrnTag">납부금액의 50%</td>
                    <td class="TrnTag">50,000원 환불</td>
                </tr>
                <tr>
                    <td class="TrnTag">총 수업시간의 1/2 이후</td>
                    <td class="TrnTag">납부금액의 0%</td>
                    <td class="TrnTag">환불 안 됨</td>
                </tr>
            </table>

            <h3 class="caption_left_common center two TrnTag">수강<b>규정</b></h3>
            <div class="class_guide_box center">
                <h3 class="TrnTag">맞춤식 고정 수업 안내</h3>
                <trn class="TrnTag">강사 한분과 매일 또는 주3회, 주2회 정해진 시간에 규칙적으로 꾸준히 수업하려는 목표를 가진분을 위한 시스템입니다.</trn>
            </div>

            <h4 class="class_rule_caption" class="TrnTag">고정 수업 가이드</h4>
            <ul class="class_rule_list">
                <li class="TrnTag"><span>01.</span>수강료 결제를 완료하시면 24시간 내에 학습매니저가 회원님에게 카카오톡으로 수강 방법에 대해 안내해 드립니다.</li>
                <li class="TrnTag"><span>02.</span>수업스케줄이 잡히면 바로 수강하실 수 있으며, 교재 구입전이라도 온라인상의 샘플교재를 이용하여 수강하실 수 있습니다.</li>
                <li class="TrnTag"><span>03.</span>수강기간은 횟수로 정해집니다. (1개월 수업의 경우 주5회는 20회 수업, 주3회 수업은 12회 수업, 주2회 수업은 8회 수업)</li>
            </ul>

            <h4 class="class_rule_caption TrnTag">일일 수업 연기</h4>
            <ul class="class_rule_list">
                <li class="TrnTag"><span>01.</span>상황이 불가피하여 수업을 받을 수 없는 경우에 수업을 연기할 수 있습니다. </li>
                <li class="TrnTag"><span>02.</span>수업연기 횟수는 진도와 향상을 위해서 전체 월 수업 횟수의 ½만 가능합니다.</li>
                <li class="TrnTag"><span>03.</span>수업연기는 마이 페이지(My Page) > 공부방 입장 > 수업 연기요청으로 들어가시면 됩니다. 당일 수업의 연기는 교사의 수업상황에 따라서 수업 시작 30분 전까지 가능합니다.</li>
            </ul>

            <h4 class="class_rule_caption TrnTag">수업 시간 변경</h4>
            <ul class="class_rule_list">
                <li class="TrnTag"><span>01.</span>수업시간을 변경하고자 하는 경우에는 어플에서 연기신청을 눌러서 교사 또는 수업 시간 둘 중 선택가능합니다.</li>
                <li class="TrnTag"><span>02.</span>수업시간을 변경하시면 담당강사가 변경될 수 있으며, 변경신청 시 유념하시기 바랍니다.</li>
                <li class="TrnTag"><span>03.</span>수업시간 변경은 전체 월 수업시간의 ½회 가능합니다.</li>
            </ul>

            <h4 class="class_rule_caption TrnTag">담당 강사 변경</h4>
            <ul class="class_rule_list">
                <li class="TrnTag"><span>01.</span>담당강사를 바꾸자 하는 경우에는 [학습매니저 1:1게시판]을 이용하여 변경신청이 가능합니다.</li>
                <li class="TrnTag"><span>02.</span>담당강사 변경은 당일 수업의 1일 전까지 가능합니다.</li>
                <li class="TrnTag"><span>03.</span>담당강사 변경은 월1회 가능합니다. (단, 강사의 불성실로 인한 교체는 제한 횟수에 불포함)</li>
            </ul>

            <h4 class="class_rule_caption TrnTag">결석 처리 안내</h4>
            <ul class="class_rule_list">
                <li class="TrnTag"><span>01.</span>전화영어, 휴대전화영어 수업은 총3회 전화하여 응답이 없을 경우 결석처리 됩니다.</li>
                <li class="TrnTag"><span>02.</span>화상영어 수업은 강사가 10분간 대기하여도 강의실에 입장하지 않으면 결석처리 됩니다.</li>
                <li class="TrnTag"><span>03.</span>수강생의 사정으로 인해 수업시작이 지연된 경우에는 수업시간을 채우지 못했다 할지라도 예정된 종료시간에 수업이 종료됩니다.<br>(뒤이어 예약된 학생의 수업에 지장을 끼칠 우려가 있으므로 불가피함)</li>
            </ul>
        </div>        
    </section>

    <div id="tip_1"></div>
    <section class="mypage_tip_wrap_1">        
        <div class="mypage_tip_area">
            <h3 class="caption_sub_img TrnTag"><img src="images/icon_tip_1.png"><b>화상강의실</b> 이용팁</h3>
            <img src="images/img_monitor_tip_1.png" class="tip_img_1">
            <ul class="tip_list_1">
                <li class="TrnTag"><b>1</b> 수업녹화</li>
                <li class="TrnTag"><b>2</b> 웹사이트(수업 중 추가 필요자료는 인터넷 검색)</li>
                <li class="TrnTag"><b>3</b> 판서</li>
                <li class="TrnTag"><b>4</b> 타이핑</li>
                <li class="TrnTag"><b>5</b> 지우개</li>
                <li class="TrnTag"><b>6</b> 교재목록</li>
                <li class="TrnTag"><b>7</b> 채팅창</li>
            </ul>
			<a href="#tip_top" onclick="$('#tip_top').animatescroll();" class="mypage_tip_top TrnTag"><img src="images/arrow_top.png">위로</a>
        </div>        
    </section>

    <!--div id="tip_2"></div>
    <section class="mypage_tip_wrap_2">        
        <div class="mypage_tip_area">
            <h3 class="caption_sub_img"><img src="images/icon_tip_2.png"><b>웹북</b> 이용팁</h3>
            <div class="mypage_tip_box">
                <img src="images/img_monitor_tip_2.png" class="tip_img_2">
                <ul class="tip_list_2">
                    <li>
                        <b>1</b>
                        <h5>대분류 선택 버튼</h5>
                        정규/심화/특화로 분류된 대분류를 선택할 수 있는 버튼입니다.<br>
                        인트로 페이지 다음에 나타나는 메인 페이지에서도 대분류를 선택할 수 있습니다.
                    </li>
                    <li>
                        <b>2</b>
                        <h5>내 교재 바로가기 버튼</h5>
                        현재 수강중인 교재의 진도로 바로 이동하기 위한 버튼입니다.<br>
                        아직 수강중이지 않거나 교재가 결정되지 않은 경우 (첫 수업)에는 “미리 등록된 교재 정보가 없다”는 경고창이 나타납니다.
                    </li>
                </ul>
            </div>
            <div class="mypage_tip_box">
                <img src="images/img_monitor_tip_3.png" class="tip_img_3">
                <ul class="tip_list_3">
                    <li>
                        <b>1</b>
                        <h5>대분류 선택 버튼</h5>
                        정규/심화/특화로 분류된 대분류를 선택할 수 있는 버튼입니다.<br>
                        인트로 페이지 다음에 나타나는 메인 페이지에서도 대분류를 선택할 수 있습니다.                        
                    </li>
                    <li>
                        <b>2</b>
                        <h5>내 교재 바로가기 버튼</h5>
                        현재 수강중인 교재의 진도로 바로 이동하기 위한 버튼입니다.<br>                        
                        아직 수강중이지 않거나 교재가 결정되지 않은 경우 (첫 수업)에는 “미리 등록된 교재 정보가 없다”는 경고창이 나타납니다.                          
                    </li>                    
                </ul>                
            </div>
            <div class="text_center"><a href="#" class="button_blue_white">온라인 웹북 보기</a></div>
			<a href="#tip_top" onclick="$('#tip_top').animatescroll();" class="mypage_tip_top"><img src="images/arrow_top.png">위로</a>
        </div>        
    </section-->
    
    <div id="tip_3"></div>
    <section class="mypage_tip_wrap_3">        
        <div class="mypage_tip_area">
            <h3 class="caption_sub_img TrnTag"><img src="images/icon_tip_3.png"><b>수업 녹화방법</b> 보기</h3>
            <img src="images/img_monitor_tip_1.png" class="tip_img_1">
            <ul class="tip_list_1">
                <li class="TrnTag"><b>1</b> 수업녹화</li>
                <li class="TrnTag"><b>2</b> 웹사이트(수업 중 추가 필요자료는 인터넷 검색)</li>
                <li class="TrnTag"><b>3</b> 판서</li>
                <li class="TrnTag"><b>4</b> 타이핑</li>
                <li class="TrnTag"><b>5</b> 지우개</li>
                <li class="TrnTag"><b>6</b> 교재목록</li>
                <li class="TrnTag"><b>7</b> 채팅창</li>
            </ul>
			<a href="#tip_top" onclick="$('#tip_top').animatescroll();" class="mypage_tip_top TrnTag"><img src="images/arrow_top.png">위로</a>
        </div>
    </section>
</div>
<?}?>

<!-- 사진 변경 라이트 박스 -->
<div class="light_box_wrap photo_change">
    <div class="light_box_area">
        <a href="#"><img src="images/btn_close_white.png" alt="닫기" class="light_box_close"></a>
        <div class="light_box_box">
            <div class="phto_change_wrap">
                <div class="phto_change_area">
                    <h3 class="caption_underline TrnTag">사진 업로드</h3>
                    <div class="photo_change_box" style="background-image:url(images/no_photo.png)"></div>
                    <input type="file" class="photo_change_file">
                    <div class="button_wrap"><a href="#" class="button_orange_white photo_change TrnTag">업로드</a></div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- 사진 변경 라이트 박스 -->

<!-- 변경 사유 라이트 박스 -->
<div class="light_box_wrap">
    <div class="light_box_area">
        <a href="#"><img src="images/btn_close_white.png" alt="닫기" class="light_box_close"></a>
        <div class="light_box_box">
            <div class="mantoman_write_wrap">
                <div class="mantoman_write_area">
                    <h3 class="caption_underline TrnTag">수업 변경 요청</h3>
                    <ul class="mantoman_write_list">
                        <li class="TrnTag">변경 사유와 연락처를 적어주시면 전화 연락 후 수업일정을 조정합니다.</li>
                    </ul>
                    <table class="mantoman_write_table">
                        <tr>
                            <th class="TrnTag">연락처</th>
                            <td>
                                <select name="MemberPhone1_1" class="member_select_1 change">
                                    <option value="010">010</option>
                                    <option value="011">011</option>
                                </select>
                                <span class="member_space">-</span>
                                <input type="text" name="MemberPhone1_2" class="member_input_small change">
                                <span class="member_space">-</span>
                                <input type="text" name="MemberPhone1_3" class="member_input_small change">
                            </td>
                        </tr>
                        <tr>
                            <th class="TrnTag">변경사유</th>
                            <td><textarea class="mantoman_textarea"></textarea></td>
                        </tr>
                    </table>
                    <div class="button_wrap"><a href="#" class="button_orange_white mantoman TrnTag">변경 요청하기</a></div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- 라이트 박스 -->

<script language="javascript">
$('.sub_visual_navi .one').addClass('active');
</script>

<?php
echo "\n";
echo $SubLayoutBottom;
echo "\n";
echo $MainLayoutBottom;
echo "\n";
?>

<?
include_once('./includes/common_analytics.php');
?>


<?php
include_once('./includes/common_footer.php');

if (trim($SubLayoutJavascript)!=""){
	echo "\n";
	echo "<script>";
	echo "\n";
	echo $SubLayoutJavascript;
	echo "\n";
	echo "</script>";
	echo "\n";
}

if (trim($MainLayoutJavascript)!=""){
	echo "\n";
	echo "<script>";
	echo "\n";
	echo $MainLayoutJavascript;
	echo "\n";
	echo "</script>";
	echo "\n";
}
?>
<script src="js/animatescroll.min.js"></script>


</body>
</html>
<?php
include_once('./includes/dbclose.php');
?>





