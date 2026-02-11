<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
$DenyGuest = true;
include_once('../includes/member_check.php');

$MemberLevelID = $_LINK_MEMBER_LEVEL_ID_;

if($MemberLevelID==12 || $MemberLevelID==13) {
	header("Location: mypage_teacher_mode.php");
}		

$UseMain = 0;
$UseSub = 1;
$SubCode = "sub_englishtell";
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="google-signin-client_id" content="950462494416-92ppoda203fvs2ghu0qjr2q592epuqsk.apps.googleusercontent.com">
<?if ($DomainSiteID==5){?>
<title>(주)englishtell</title>
<?}else{?>
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="../uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="../uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
<?}?>
<link href="css/sub_style.css" rel="stylesheet" type="text/css" />
<?php
include_once('../includes/common_header.php');

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

<!-- ===========================================   froala_editor   =========================================== -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css">
<link rel="stylesheet" href="../editors/froala_editor_3/css/froala_editor.css">
<link rel="stylesheet" href="../editors/froala_editor_3/css/froala_style.css">
<link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/code_view.css">
<link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/draggable.css">
<link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/colors.css">
<link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/emoticons.css">
<link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/image_manager.css">
<link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/image.css">
<link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/line_breaker.css">
<link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/table.css">
<link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/char_counter.css">
<link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/video.css">
<link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/fullscreen.css">
<link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/file.css">
<link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/quick_insert.css">
<link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/help.css">
<link rel="stylesheet" href="../editors/froala_editor_3/css/third_party/spell_checker.css">
<link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/special_characters.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.3.0/codemirror.min.css">
<!-- ===========================================   froala_editor   =========================================== -->
</head>
<body>
<?
include_once('../includes/common_body_top.php');
?>
<?php
$MainLayoutTop = convertHTML(trim($MainLayoutTop));
$SubLayoutTop = convertHTML(trim($SubLayoutTop));
$SubLayoutBottom = convertHTML(trim($SubLayoutBottom));
$MainLayoutBottom = convertHTML(trim($MainLayoutBottom));

echo "\n";
echo $MainLayoutTop;
echo "\n";
echo $SubLayoutTop;
echo "\n";


$MemberID = $_LINK_MEMBER_ID_;


$Sql3 = "select count(*) count from DirectQnaMembers A where A.MemberID=:MemberID";
$Stmt3 = $DbConn->prepare($Sql3);
$Stmt3->bindParam(':MemberID', $MemberID);
$Stmt3->execute();
$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
$Row3 = $Stmt3->fetch();


$Sql2 = "
		select 
			A.*
		from DirectQnaMembers A 
		where A.MemberID=:MemberID and A.DirectQnaMemberState<>0 
		order by A.DirectQnaMemberRegDateTime desc";// limit $StartRowNum, $PageListNum";

$Stmt2 = $DbConn->prepare($Sql2);
$Stmt2->bindParam(':MemberID', $MemberID);
$Stmt2->execute();
$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

?>


<div class="sub_wrap">       
    <div class="sub_title_common_wrap"><h2 class="sub_title_common"><b>1:1</b> 문의</h2></div>

    <section class="mantoman_wrap">
        <div class="mantoman_area">


            <!-- 1:1 문의 -->
            <div class="">
				<div class="mantoman_btn_area"><a href="#" class="button_whtie_border_arrow light_box_btn TrnTag">1:1 친절상담 문의하기</a></div>
                <h3 class="caption_left_common_span "><trn class="TrnTag">나의 <b>상담이력</b></trn><span>Total : <b><?=$Row3['count']?></b></span></h3>
				<ul class="mantoman_list">
					<li class="mantoman_first">
						<div class="caption TrnTag">제목</div>
						<div class="date TrnTag">등록일</div>
						<div class="status TrnTag">상태</div>
					</li>
					<?php while($Row2 = $Stmt2->fetch()) { 
						
						$ContentType = $Row2["ContentType"];
						$AnswerType = $Row2["AnswerType"];
						
						$DirectQnaMemberTitle = $Row2["DirectQnaMemberTitle"];
						$DirectQnaMemberContent = $Row2["DirectQnaMemberContent"];
						$DirectQnaMemberAnswer = $Row2["DirectQnaMemberAnswer"];
						$DirectQnaMemberState = $Row2["DirectQnaMemberState"];
						$StrDirectQnaMemberState = "";
						$DirectQnaMemberRegDateTime = $Row2["DirectQnaMemberRegDateTime"];
						$TempDirectQnaMemberRegDateTime = date("Y.m.d", strtotime($DirectQnaMemberRegDateTime));

						if($DirectQnaMemberState == 1) {
							$StrDirectQnaMemberState = "<a href=\"#\" class=\"mantoman_btn\" style=\"background-color:#cccccc;\">답변대기중</a>";
						} else if($DirectQnaMemberState == 2) {
							$StrDirectQnaMemberState = "<a href=\"#\" class=\"mantoman_btn\">답변보기 <span class=\"mantoman_arrow \"></span></a>";
						}

						if ($ContentType==0){
							$DirectQnaMemberContent = str_replace("\n","<br>",$DirectQnaMemberContent);
						}

						if ($AnswerType==0){
							$DirectQnaMemberAnswer = str_replace("\n","<br>",$DirectQnaMemberAnswer);
						}
					?>
					<li>
						<div class="mantoman_q">
							<div class="mantoman_icon"><img src="images/icon_q.png" alt="질문" class="icon"></div>
							<div class="mantoman_question ellipsis">
								<?=$DirectQnaMemberTitle?>
							</div>
							<div class="mantoman_date"><?=$TempDirectQnaMemberRegDateTime?></div>
							<div class="mantoman_status"><?=$StrDirectQnaMemberState?></div>
						</div>
						<div class="mantoman_a">
							
							<div class="mantoman_answer" style="width:100%;">
								<div style="padding:10px;background-color:#888888;color:#ffffff;margin-top:10px;margin-bottom:10px;" class="TrnTag">질문내용</div>	
								<?=$DirectQnaMemberContent?>
							</div>
							
							
							<div class="mantoman_answer" style="width:100%;display:<?if ($DirectQnaMemberState == 1){?>none<?}?>;">
								<div style="padding:10px;background-color:#FE9147;color:#ffffff;margin-top:10px;margin-bottom:10px;" class="TrnTag">답변내용</div>	
								<?=$DirectQnaMemberAnswer?>
							</div>
						</div>
					</li>
					<?
					}
					?>
	
				</ul>
				<div>
				</div>

				<!--
				<div class="bbs_page">
                    <span class="arrow_left_2"><img src="images/arrow_bbs_left_2.png"></span>
                    <span class="arrow_left_1"><img src="images/arrow_bbs_left_1.png"></span>
                    <span class="active">1</span>
                    <span class="arrow_right_1"><img src="images/arrow_bbs_right_1.png"></span>
                    <span class="arrow_right_2"><img src="images/arrow_bbs_right_2.png"></span>
                </div>
                -->
                
            </div>

        </div>
    </section>

</div>

<!-- 라이트 박스 -->
<div class="light_box_wrap">
	<div class="light_box_area">
		<a href="#"><img src="images/btn_close_white.png" alt="닫기" class="light_box_close"></a>
		<div class="light_box_box">
			<div class="mantoman_write_wrap">
				<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
					<input type="hidden" name="MemberID" value="<?=$MemberID?>">
					<input type="hidden" name="ContentType" value="1">
					<div class="mantoman_write_area">
						<h3 class="caption_underline TrnTag">1:1 친절상담 문의하기</h3>
						<ul class="mantoman_write_list">
							<li class="TrnTag">서비스, 홈페이지, 학습방법 등 여러분의 여러 궁금증을 1:1로 상담하여 드립니다. </li>
							<li class="TrnTag">이곳에 문의하신 내용은 100% 비밀이 보장됩니다. </li>
							<li class="TrnTag">상담가능시간 : 평일 AM 10:00 ~ PM 9:00</li>
						</ul>
						<table class="mantoman_write_table">
							<tr style="display:none;">
								<th class="TrnTag">분류</th>
								<td>
									<select class="mantoman_select" name="DirectQnaMemberType">
										<option name="DirectQnaMemberType" value="0" class="TrnTag">선택하세요</option>
										<option name="DirectQnaMemberType" value="1" class="TrnTag">수강신청관련</option>
									</select>
								</td>
							</tr>
							<tr>
								<th class="TrnTag">제목</th>
								<td><input type="text" class="mantoman_input" name="DirectQnaMemberTitle"></td>
							</tr>
							<tr>
								<td colspan="2" style="border:0px;"><textarea class="mantoman_textarea" id="DirectQnaMemberContent" name="DirectQnaMemberContent"></textarea></td>
							</tr>
							<!--
							<tr>
								<th>파일</th>
								<td><input type="file" class="mantoman_file"></td>
							</tr>
							-->
						</table>
						<div class="button_wrap flex_justify">
							<a href="javascript:FormSubmit();" class="button_orange_white mantoman TrnTag">신청하기</a>
							<a href="#" class="button_br_black mantoman light_box_cancle TrnTag">취소하기</a>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- 라이트 박스 -->





<script language="javascript">
$(document).ready(function () {
    $('.navi_lnb .three').addClass('active');
});


function FormSubmit() {

	var RegForm = document.getElementById("RegForm");

	/*
	obj = RegForm.DirectQnaMemberType;
	if (obj.value=="0"){
		alert('분류를 선택해주세요.');
		obj.focus();
		return;
	}
	*/

	obj = RegForm.DirectQnaMemberTitle;
	if (obj.value==""){
		alert('제목을 입력하세요.');
		obj.focus();
		return;
	}

	obj = RegForm.DirectQnaMemberContent;
	if (obj.value==""){
		alert('내용을 입력하세요.');
		obj.focus();
		return;
	}

	if (confirm("상담신청을 하시겠습니까?")){
		document.RegForm.action = "mypage_inquiry_action.php";
		document.RegForm.submit();	
	}



}


</script>



<!-- ===========================================   froala_editor   =========================================== -->
<style>
#DirectQnaMemberContent {
  width: 81%;
  margin: auto;
  text-align: left;
}
</style>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.3.0/codemirror.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.3.0/mode/xml/xml.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/froala_editor.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/align.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/char_counter.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/code_beautifier.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/code_view.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/colors.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/draggable.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/emoticons.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/entities.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/file.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/font_size.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/font_family.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/fullscreen.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/image.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/image_manager.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/line_breaker.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/inline_style.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/link.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/lists.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/paragraph_format.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/paragraph_style.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/quick_insert.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/quote.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/table.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/save.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/url.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/video.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/help.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/print.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/third_party/spell_checker.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/special_characters.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/word_paste.min.js"></script>

<script>
(function () {
  const editorInstance = new FroalaEditor('#DirectQnaMemberContent', {
	key: "xGE6oB4B3C3A6D6E5fLUQZf1ASFb1EFRNh1Hb1BCCQDUHnA8B6E5B4B1C3I3A1B8A6==",
	enter: FroalaEditor.ENTER_BR,
	heightMin: 200,
	fileUploadURL: '../froala_editor_file_upload.php',
	imageUploadURL: '../froala_editor_image_upload.php',
	placeholderText: null,
	toolbarButtons: [ ['undo', 'redo', 'bold', 'italic', 'underline', 'strikeThrough', 'textColor', 'upload', 'insertImage'] ],
	events: {
	  initialized: function () {
		const editor = this
		this.el.closest('form').addEventListener('submit', function (e) {
		  console.log(editor.$oel.val())
		  e.preventDefault()
		})
	  }
	}
  })
})()


</script>
<!-- ===========================================   froala_editor   =========================================== -->

<?php
echo "\n";
echo $SubLayoutBottom;
echo "\n";
echo $MainLayoutBottom;
echo "\n";
?>

<?
include_once('../includes/common_analytics.php');
?>


<?php
include_once('../includes/common_footer.php');

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

</body>
</html>
<?php
include_once('../includes/dbclose.php');
?>