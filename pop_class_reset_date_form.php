<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
include_once('./includes/member_check.php');

$UseMain = 0;
$UseSub = 1;
$SubCode = "sub_04";
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, minimal-ui">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
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

<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="./js/jquery-confirm.min.js"></script>
<link href="./css/jquery-confirm.min.css" rel="stylesheet" type="text/css"/>
</head>
<body style="background-color:#fcf9f0;">
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
    // $SubLayoutTop = convertHTML(trim($SubLayoutTop));
    // $SubLayoutBottom = convertHTML(trim($SubLayoutBottom));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_gumiivyleague)}}"));

} else if($DomainSiteID==8){ //engliseed.kr
    $MainLayoutTop = convertHTML(trim("{{Piece(header_engliseed)}}"));
//    $SubLayoutTop = convertHTML(trim("{{Piece(sub_06_engliseed)}}"));
//    $SubLayoutBottom = convertHTML(trim("{{Piece(sub_01_engliseed_bottom)}}"));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_engliseed)}}"));

} else if($DomainSiteID==9){ //live.engedu.kr
    $MainLayoutTop = convertHTML(trim("{{Piece(header_engedu)}}"));
//    $SubLayoutTop = convertHTML(trim("{{Piece(sub_06_engliseed)}}"));
//    $SubLayoutBottom = convertHTML(trim("{{Piece(sub_01_engliseed_bottom)}}"));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_engedu)}}"));


} else {
    $MainLayoutTop = convertHTML(trim($MainLayoutTop));
    $SubLayoutTop = convertHTML(trim($SubLayoutTop));
    $SubLayoutBottom = convertHTML(trim($SubLayoutBottom));
    $MainLayoutBottom = convertHTML(trim($MainLayoutBottom));
  }
echo "\n";
//echo $MainLayoutTop;
echo "\n";
//echo $SubLayoutTop;
echo "\n";
?>


<?
$ClassID = isset($_REQUEST["ClassID"]) ? $_REQUEST["ClassID"] : "";
$GroupRowCount = isset($_REQUEST["GroupRowCount"]) ? $_REQUEST["GroupRowCount"] : "";
$ClassMemberType = isset($_REQUEST["ClassMemberType"]) ? $_REQUEST["ClassMemberType"] : "";
$SetWeek = isset($_REQUEST["SetWeek"]) ? $_REQUEST["SetWeek"] : "";
$SetHour = isset($_REQUEST["SetHour"]) ? $_REQUEST["SetHour"] : "";
$SetMinute = isset($_REQUEST["SetMinute"]) ? $_REQUEST["SetMinute"] : "";
$FromDevice = isset($_REQUEST["FromDevice"]) ? $_REQUEST["FromDevice"] : "";
$FromPage = isset($_REQUEST["FromPage"]) ? $_REQUEST["FromPage"] : "";
$IframeMode = isset($_REQUEST["IframeMode"]) ? $_REQUEST["IframeMode"] : "0";

$Sql = "
	select
		A.*,
		B.ClassProductID, 
		D.CenterPayType
	from Classes A 
		inner join ClassOrders B on A.ClassOrderID=B.ClassOrderID 
		inner join Members C on A.MemberID=C.MemberID 
		inner join Centers D on C.CenterID=D.CenterID 
	where ClassID=$ClassID 
";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$ClassOrderID = $Row["ClassOrderID"];
$MemberID = $Row["MemberID"];
$TeacherID = $Row["TeacherID"];
$ClassProductID = $Row["ClassProductID"];
$CenterPayType = $Row["CenterPayType"];



$SampleStudy = 0;
if ($ClassID=="-1"){
	$SampleStudy = 1;
	$ClassProductID = 1;
}
?>

<!-- 헤더(앱) 영역 -->
<?if ($FromDevice=="app"){?>
<header class="header_app_wrap">
    <h1 class="header_app_title">수업연기</h1>
    <a href="javascript:window.Exit=true" class="header_app_close header_app_gray"><img src="images/btn_app_close_black.png" class="icon"></a>
</header>
<?}?>

<div class="sub_wrap bg_gray padding_app" style="border:0;<?if ($FromDevice==""){?>margin-top:-60px;<?}?>">   
    <section class="level_application_wrap">
        <div class="level_application_area" style="padding:20px;">

			<?if ($FromDevice==""){?>
			<h3 class="caption_underline TrnTag" style="margin-top:30px;">수업 연기</h3>
			<?}?>
		
			<form name="RegForm" id="RegForm">
			<input type="hidden" name="FromDevice" id="FromDevice" value="<?=$FromDevice?>">
			<input type="hidden" name="FromPage" id="FromPage" value="<?=$FromPage?>">
			<input type="hidden" name="ClassID" value="<?=$ClassID?>">
			<input type="hidden" name="ClassOrderID" value="<?=$ClassOrderID?>">
			<input type="hidden" name="MemberID" value="<?=$MemberID?>">
			<input type="hidden" name="TeacherID" value="<?=$TeacherID?>">
			<input type="hidden" name="ClassProductID" value="<?=$ClassProductID?>">
			<input type="hidden" name="ClassMemberType" id="ClassMemberType" value="<?=$ClassMemberType?>">
			<input type="hidden" name="CenterPayType" value="<?=$CenterPayType?>">
			<input type="hidden" name="SetWeek" value="<?=$SetWeek?>">
			<input type="hidden" name="SetHour" value="<?=$SetHour?>">
			<input type="hidden" name="SetMinute" value="<?=$SetMinute?>">
			<input type="hidden" name="IframeMode" value="<?=$IframeMode?>">
			<input type="hidden" name="ResetDateType" value="2">
			<input type="hidden" name="ResetType" value="EverChange">

			<input type="hidden" name="GroupRowCount" id="GroupRowCount" value="<?=$GroupRowCount?>">

			<table class="level_reserve_table">
				<tr>
					<th style="display:<?if ($FromDevice!=""){?>none<?}?>;" class="TrnTag">연기방식</th>
				</tr>
				<tr>
					<td class="radio_wrap reset">
						<input type="radio" id="ResetDateType2" class="input_radio" name="ResetDateType_" value="2" onclick="ChResetDateType(2)" checked><label class="label TrnTag" for="ResetDateType2" style="margin:0 5px 0 0;"><span class="bullet_radio"></span>날짜 선택하여 연기하기</label>
						<div class="break">
                            <span style="display:<?if ($ClassProductID!=1){?>none<?}?>;">
                            <input type="radio" id="ResetDateType1" class="input_radio" name="ResetDateType_" value="1" onclick="ChResetDateType(1)"><label class="label TrnTag" for="ResetDateType1" style="margin:0 5px 0 0;"><span class="bullet_radio"></span>마지막 수업 뒤로 연기</label>
                            </span>
                        </div>
                        <input type="radio" id="ResetDateType3" class="input_radio" name="ResetDateType_" value="1" onclick="ChResetDateType(3)"><label class="label TrnTag" for="ResetDateType3"><span class="bullet_radio"></span>스케줄 변경</label>
					</td>
				</tr>
                <tr>
                    <td style="border: none;">
                        <h4>연기 전 참고 사항</h4>
                        <p>
                            <span style="color:red">*스케줄 변경</span> : 고정 스케줄이 아예 바뀌는 점 숙지하고 변경 바랍니다.
                            <br/> 
                            <b>[스케줄 변경은 하루 전(24시간 전)에만 가능]</b>
                        </p>
                    </td>
                </tr>
			</table>

			</form>

			<div class="button_wrap flex_justify">
				<?if ($SampleStudy==1){?>
				<a href="javascript:FormSubmitErr();" class="button_orange_white mantoman TrnTag" style="width:100%;">연기</a>
				<?}else{?>
				<a href="javascript:FormSubmit();" class="button_orange_white mantoman TrnTag" style="width:100%;">연기</a>
				<?}?>
			</div>

        </div>
    </section>
</div>



<script>
function ChResetDateType(ResetDateTypeValue){
	document.RegForm.ResetDateType.value = ResetDateTypeValue;
}


function FormSubmitErr(){
	<?if ($FromDevice==""){?>
		alert("샘플 강좌는 연기할 수 없습니다.");
	<?}else{?>
		$.confirm({
			title: '안내',
			content: '샘플 강좌는 연기할 수 없습니다.',
			buttons: {
				확인: function () {

				}
			}
		});
	<?}?>
}

function FormSubmit(){

	ResetDateType = document.RegForm.ResetDateType.value;
	if (ResetDateType==1){//마지막 수업 뒤로
		<?if ($FromDevice==""){?>
			if (confirm('연기 하시겠습니까?')){
				document.RegForm.action = "pop_class_reset_date_action.php";
				document.RegForm.submit();
			}
		<?}else{?>
			$.confirm({
				title: '안내',
				content: '연기 하시겠습니까?',
				buttons: {
					확인: function () {
						document.RegForm.action = "pop_class_reset_date_action.php";
						document.RegForm.submit();
					},
					취소: function () {

					} 
				}
			});
		<?}?>
	} else{
		<?if ($FromPage=="Calendar"){?>
			parent.parent.$.colorbox.resize({width:"95%", height:"95%"}); // 맨 마지막 강사와 scrollbar 가 온전히 보이게 하기 위해 부모의 창 사이즈 조절
			//parent.parent.$.colorbox.resize({width:800, height:785}); // 맨 마지막 강사와 scrollbar 가 온전히 보이게 하기 위해 부모의 창 사이즈 조절
		<?}?>
		<?if ($FromDevice==""){?>
		parent.$.colorbox.resize({width:"95%", height:"95%"});
		//parent.$.colorbox.resize({width:800, height:750});
        if(ResetDateType==3){ //스케줄 변경
            //만약 결제타입이 B2B 결제라면, 안내 메시지 출력 후 연기 불가 처리 (창 닫기)
            //$CenterPayType==1 : B2B 결제
            // alert("CenterPayType1 : " + document.RegForm.CenterPayType.value);
            if(document.RegForm.CenterPayType.value == 1){
                    alert("현재 계정에서는 스케줄 변경 기능을 사용하실 수 없습니다.\n\n스케줄 변경이 필요하신 경우, 대리점(학원)에 문의하여 주시기 바랍니다.");
                    parent.$.fn.colorbox.close();
                    return;
                }

                //스케줄 변경 오류 수정 전까지 전체 유저에 대해서 메시지 출력
                alert("불편을 드려 죄송합니다. 스케줄 변경 기능은 준비중입니다.\n\n스케줄 변경이 필요하신 경우, 고객센터로 문의하여 주시기 바랍니다.");
                parent.$.fn.colorbox.close();
                return;

                document.RegForm.action = "pop_class_reset_date_form_date_select_form.php";
                document.RegForm.submit();
            } else{
				document.RegForm.action = "pop_class_reset_date_form_teacher_select_form.php";
				//document.RegForm.action = "pop_class_reset_date_form_date_select_form.php";
				document.RegForm.ResetType.value = "";
				document.RegForm.submit();
            }
		<?}else{?>
            if(ResetDateType==3){ //스케줄 변경
                //만약 결제타입이 B2B 결제라면, 안내 메시지 출력 후 연기 불가 처리 (창 닫기)
                //$CenterPayType==1 : B2B 결제
                // alert("CenterPayType2 : " + document.RegForm.CenterPayType.value);
                if(document.RegForm.CenterPayType.value == 1){
                    alert("현재 계정에서는 스케줄 변경 기능을 사용하실 수 없습니다.\n\n스케줄 변경이 필요하신 경우, 대리점(학원)에 문의하여 주시기 바랍니다.");
                    parent.$.fn.colorbox.close();
                    return;
                }

                //스케줄 변경 오류 수정 전까지 전체 유저에 대해서 메시지 출력
                alert("불편을 드려 죄송합니다. 스케줄 변경 기능은 준비중입니다.\n\n스케줄 변경이 필요하신 경우, 고객센터로 문의하여 주시기 바랍니다.");
                parent.$.fn.colorbox.close();
                return;

                document.RegForm.action = "pop_class_reset_date_form_date_select_form.php";
                document.RegForm.submit();
            } else{
				document.RegForm.action = "pop_class_reset_date_form_teacher_select_form.php";
				//document.RegForm.action = "pop_class_reset_date_form_date_select_form.php";
				document.RegForm.ResetType.value = "";
				document.RegForm.submit();
            }
        <?}?>
	}
}

function CloseForm(){
	<?if ($FromDevice==""){?>
	parent.$.fn.colorbox.close();
	<?}else{?>
	window.Exit=true;
	<?}?>
}
</script>

<?php
echo "\n";
//echo $SubLayoutBottom;
echo "\n";
//echo $MainLayoutBottom;
echo "\n";
?>

<?
include_once('./includes/common_analytics.php');
?>


<?php
//include_once('./includes/common_footer.php');

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


<!-- ====    kendo -->
<link href="./kendo/styles/kendo.common.min.css" rel="stylesheet">
<link href="./kendo/styles/kendo.default.min.css" rel="stylesheet">
<script src="./kendo/js/kendo.web.min.js"></script>
<!-- ====    kendo   === -->


<!-- ====   Color Box -->
<?
$ColorBox = isset($ColorBox) ? $ColorBox : "";
if ($ColorBox==""){
	$ColorBox = "example2";
}
?>
<link rel="stylesheet" href="../js/colorbox/<?=$ColorBox?>/colorbox.css" />
<script src="../js/colorbox/jquery.colorbox.js"></script>
<script>
jQuery(document).ready(function(){
$(document).bind('cbox_open', function() {
    $('html').css({ overflow: 'hidden' });
}).bind('cbox_closed', function() {
    $('html').css({ overflow: '' });
});
});
</script>
<!-- ====   Color Box   === -->

</body>
</html>
<?php
include_once('./includes/dbclose.php');
?>
