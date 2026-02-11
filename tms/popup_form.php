<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
include_once('./inc_header.php');
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
<?php
$MainCode = 7;
$SubCode = 1;
include_once('./inc_top.php');
?>


<?php
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$PopupID = isset($_REQUEST["PopupID"]) ? $_REQUEST["PopupID"] : "";

if ($PopupID!=""){

	$Sql = "select * from Popups where PopupID=:PopupID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':PopupID', $PopupID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$PopupName = $Row["PopupName"];
	$PopupTitle = $Row["PopupTitle"];
	$PopupStartDateNum = $Row["PopupStartDateNum"];
	$PopupEndDateNum = $Row["PopupEndDateNum"];
	$PopupWidth = $Row["PopupWidth"];
	$PopupHeight = $Row["PopupHeight"];
	$PopupTop = $Row["PopupTop"];
	$PopupLeft = $Row["PopupLeft"];
	$PopupType = $Row["PopupType"];
	$MobilePopup = $Row["MobilePopup"];
	$PopupContent = $Row["PopupContent"];
	$PopupImage = $Row["PopupImage"];
	$PopupState = $Row["PopupState"];
	$PopupBgImageID = $Row["PopupBgImageID"];
	$NewData = "0";

	$PopupImageLink = $Row["PopupImageLink"];
	$PopupImageLinkType = $Row["PopupImageLinkType"];

	$PopupName = str_replace('"', "&#34;", $PopupName);
	$PopupTitle = str_replace('"', "&#34;", $PopupTitle);



}else{
	$PopupName = "";
	$PopupTitle = "";
	$PopupStartDateNum = date("Y-m-d");
	$PopupEndDateNum = date("Y-m-d");
	$PopupWidth = 500;
	$PopupHeight = 500;
	$PopupTop = 0;
	$PopupLeft = 0;
	$PopupType = 1;
	$MobilePopup = 1;
	$PopupContent = "";
	$PopupImage = "";
	$PopupState = 2;
	$NewData = "1";
	$PopupImageLink = "";
	$PopupImageLinkType=1;
	$PopupBgImageID = 0;

}

?>

<h1 class="Title" style="margin-bottom:20px;">팝업 정보</h1>

<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" accept-charset="UTF-8" autocomplete="off">
<input type="hidden" name="PopupID" value="<?=$PopupID?>">
<input type="hidden" name="ListParam" value="<?=$ListParam?>">
<input type="hidden" name="NewData" value="<?=$NewData?>">
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_form" style="margin-bottom:15px;">
  <tr>
	<th>팝업명<span></span></th>
	<td><input type="text" id="PopupName" name="PopupName" value="<?=$PopupName?>" placeholder="팝업명을 입력하세요."/></td>
  </tr>
  <tr>
	<th>팝업타이틀<span></span></th>
	<td><input type="text" id="PopupTitle" name="PopupTitle"  value="<?=$PopupTitle?>" placeholder="타이틀을 입력하세요."/></td>
  </tr>
  <tr>
	<th>모바일팝업<span></span></th>
	<td class="check"><input type="checkbox" name="MobilePopup" id="MobilePopup" value="1" <?if ($MobilePopup==1){?>checked<?}?>> <label for="MobilePopup"><span></span>모바일에서 팝업 열기 (팝업타입이 이미지 일때)</label></td>
  </tr>
  <tr>
	<th>기간<span></span></th>
	<td>
		<label>시작날짜 :</label> <input type="text" id="PopupStartDateNum" name="PopupStartDateNum"  value="<?=$PopupStartDateNum?>" style="width:160px;background-color:#EDEDED;"/>
		<label>종료날짜 :</label> <input type="text" id="PopupEndDateNum" name="PopupEndDateNum"   value="<?=$PopupEndDateNum?>" style="width:160px;background-color:#EDEDED;"/>
		<script>
			$(document).ready(function() {
				$("#PopupStartDateNum").kendoDatePicker({
					format: "yyyy-MM-dd"
				});
				$("#PopupEndDateNum").kendoDatePicker({
					format: "yyyy-MM-dd"
				});
			});
		</script>

	</td>
  </tr>
  <tr>
	<th>사이즈<span></span></th>
	<td>
		<label>가로너비 :</label> <input type="text" id="PopupWidth" name="PopupWidth" value="<?=$PopupWidth?>" style="width:60px;height:30px;margin-top:-7px;" class="allownumericwithoutdecimal"/> px 
		<label>세로높이 :</label> <input type="text" id="PopupHeight" name="PopupHeight" value="<?=$PopupHeight?>"  style="width:60px;height:30px;margin-top:-7px;" class="allownumericwithoutdecimal"/> px 
	</td>
  </tr>
  <tr>
	<th>위치<span></span></th>
	<td>
		<label>상단위치 :</label> <input type="text" id="PopupTop" name="PopupTop" value="<?=$PopupTop?>"  style="width:60px;height:30px;margin-top:-7px;" class="allownumericwithoutdecimal"/> px 
		<label>좌측위치 :</label> <input type="text" id="PopupLeft" name="PopupLeft" value="<?=$PopupLeft?>"  style="width:60px;height:30px;margin-top:-7px;" class="allownumericwithoutdecimal"/> px 
	</td>
  </tr>
  <tr>
	<th>팝업타입<span></span></th>
	<td class="radio">
		<input type="radio" name="PopupType" id="PopupType1" value="1" <?php if ($PopupType==1) { echo "checked";}?> onclick="CheckPopupType(1);"> <label for="PopupType1"><span></span>이미지</label>
		<input type="radio" name="PopupType" id="PopupType2" value="2" <?php if ($PopupType==2) { echo "checked";}?> onclick="CheckPopupType(2);"> <label for="PopupType2"><span></span>텍스트</label>
		<script>
		function CheckPopupType(type){
			if (type==1){
				document.getElementById('DivPopupType2').style.display = "none";
				document.getElementById('DivPopupType1_1').style.display = "";
				document.getElementById('DivPopupType1_2').style.display = "";
				document.getElementById('DivPopupType1_3').style.display = "";
			}else{
				document.getElementById('DivPopupType1_1').style.display = "none";
				document.getElementById('DivPopupType1_2').style.display = "none";
				document.getElementById('DivPopupType1_3').style.display = "none";
				document.getElementById('DivPopupType2').style.display = "";
			}
		}
		</script>
	</td>
  </tr>
  <tr id="DivPopupType1_1" style="display:<?php if ($PopupType==1) { } else{ echo "none";}?>;">
	<th>이미지 선택<span></span></th>
	<td>
		<input type="file" name="PopupImage" style="height:28px; width:50%;">
	</td>
  </tr>

  <tr id="DivPopupType2" style="display:<?php if ($PopupType==2) { } else{ echo "none;";}?>">
	<td colspan="2" class="radio">
		<div style="margin-top:20px;margin-bottom:20px;line-height:2;">
			<input type="radio" name="PopupBgImageID" id="PopupBgImageID0" value="0" <?php if ($PopupBgImageID==0) {echo ("checked");}?> onclick="SetPopupBg(0)"> <label for="PopupBgImageID0"><span></span>배경없음</label>
			<input type="radio" name="PopupBgImageID" id="PopupBgImageID1" value="1" <?php if ($PopupBgImageID==1) {echo ("checked");}?> onclick="SetPopupBg(1)"> <label for="PopupBgImageID1"><span></span>배경 1</label>
			<input type="radio" name="PopupBgImageID" id="PopupBgImageID2" value="2" <?php if ($PopupBgImageID==2) {echo ("checked");}?> onclick="SetPopupBg(2)"> <label for="PopupBgImageID2"><span></span>배경 2</label>
			<input type="radio" name="PopupBgImageID" id="PopupBgImageID3" value="3" <?php if ($PopupBgImageID==3) {echo ("checked");}?> onclick="SetPopupBg(3)"> <label for="PopupBgImageID3"><span></span>배경 3</label>
			<input type="radio" name="PopupBgImageID" id="PopupBgImageID4" value="4" <?php if ($PopupBgImageID==4) {echo ("checked");}?> onclick="SetPopupBg(4)"> <label for="PopupBgImageID4"><span></span>배경 4</label>
			<br>
			※ <b>배경을 사용하시려면 에디터 툴바 맨앞의 ☆ 버튼을 클릭하여 템플릿을 입력한 후 사용하세요.</b>
			<br>
			※ 배경은 가로 500px, 세로 500px 에 최적화 되어 있습니다.
		</div>
		<textarea name="PopupContent" id="PopupContent" cols="100" rows="12" style="margin-top:5px;margin-bottom:5px;"><?=$PopupContent?></textarea>
	</td>
  </tr>


  <tr id="DivPopupType1_2" style="display:<?php if ($PopupType==1) { } else{ echo "none";}?>;">
	<th>이미지 링크<span></span></th>
	<td><input type="text" name="PopupImageLink" id="PopupImageLink" value="<?=$PopupImageLink?>" placeholder="http:// 포함 전체 주소 입력"/>※ 입력하지 않으면 링크를 걸지 않습니다.</td>
  </tr>
  <tr id="DivPopupType1_3" style="display:<?php if ($PopupType==1) { } else{ echo "none";}?>;">
	<th>링크 열기<span></span></th>
	<td class="radio">
		<input type="radio" name="PopupImageLinkType" id="PopupImageLinkType1" value="1" <?if ($PopupImageLinkType==1){?>checked<?}?>> <label for="PopupImageLinkType1"><span></span>현재창에 열기</label> <input type="radio" name="PopupImageLinkType" id="PopupImageLinkType2" value="2" <?if ($PopupImageLinkType==2){?>checked<?}?>> <label for="PopupImageLinkType2"><span></span>새창으로 열기</label>
	</td>
  </tr>
  <tr>
	<th>상태<span></span></th>
	<td class="radio">
		<input type="radio" name="PopupState" id="PopupState1" value="1" <?php if ($PopupState==1) {echo ("checked");}?>> <label for="PopupState1"><span></span>승인</label>
		<input type="radio" name="PopupState" id="PopupState2" value="2" <?php if ($PopupState==2) {echo ("checked");}?>> <label for="PopupState2"><span></span>미승인</label>
		<?if ($PopupID!=""){?>
		<input type="radio" name="PopupState" id="PopupState0" value="0" <?php if ($PopupState==0) {echo ("checked");}?>> <label for="PopupState0"><span></span>삭제</label>
		<?}?>
	</td>
  </tr>

</table>
</form>

<div class="btn_center" style="padding-top:25px;">
	<?
	if ($PopupID==""){ 
	?>
	<a href="javascript:FormSubmit();" class="btn red">등록하기</a>
	<?
	}else{
	?>
	<a href="javascript:FormSubmit();" class="btn red">수정하기</a>
	<?
	}
	?>
	<a href="javascript:history.go(-1);" class="btn gray">목록으로</a>
</div>



<script language="javascript">
function FormSubmit(){
	obj = document.RegForm.PopupName;
	if (obj.value==""){
		alert('팝업명을 입력하세요.');
		obj.focus();
		return;
	}

	obj = document.RegForm.PopupTitle;
	if (obj.value==""){
		alert('팝업타이틀을 입력하세요.');
		obj.focus();
		return;
	}


	document.RegForm.action = "popup_action.php";
	document.RegForm.submit();
}

</script>


<!-- ===========================================   froala_editor   =========================================== -->
<style>
#PopupContent {
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
  const editorInstance = new FroalaEditor('#PopupContent', {
	
	// Set custom buttons.
	//============== 툴바 버튼 커스트마이징 =======================================
	toolbarButtons: {
			// Key represents the more button from the toolbar.
			moreText: {
			// List of buttons used in the  group.
			buttons: ['insertHTML', 'bold', 'italic', 'underline', 'strikeThrough', 'subscript', 'superscript', 'fontFamily', 'fontSize', 'textColor', 'backgroundColor', 'inlineClass', 'inlineStyle', 'clearFormatting'],

			// Alignment of the group in the toolbar.
			align: 'left',

			// By default, 3 buttons are shown in the main toolbar. The rest of them are available when using the more button.
			buttonsVisible: 3
		},


		moreParagraph: {
			buttons: ['alignLeft', 'alignCenter', 'formatOLSimple', 'alignRight', 'alignJustify', 'formatOL', 'formatUL', 'paragraphFormat', 'paragraphStyle', 'lineHeight', 'outdent', 'indent', 'quote'],
			align: 'left',
			buttonsVisible: 3
		},

		moreRich: {
			buttons: ['insertLink', 'insertImage', 'insertVideo', 'insertTable', 'emoticons', 'fontAwesome', 'specialCharacters', 'embedly', 'insertFile', 'insertHR'],
			align: 'left',
			buttonsVisible: 3
		},

		moreMisc: {
			buttons: ['undo', 'redo', 'fullscreen', 'print', 'getPDF', 'spellChecker', 'selectAll', 'html', 'help'],
			align: 'right',
			buttonsVisible: 2
		}
	  },
	// Change buttons for XS screen.
	toolbarButtonsXS: [['undo', 'redo'], ['bold', 'italic', 'underline']],
	//============== 툴바 버튼 커스트마이징 =======================================


	key: "xGE6oB4B3C3A6D6E5fLUQZf1ASFb1EFRNh1Hb1BCCQDUHnA8B6E5B4B1C3I3A1B8A6==",
	enter: FroalaEditor.ENTER_BR,
	heightMin: 300,
	fileUploadURL: '../froala_editor_file_upload.php',
	imageUploadURL: '../froala_editor_image_upload.php',
	placeholderText: null,
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

//====== 버튼을 클릭하면 템플릿 입력 ======================
TableTemplete = "<table style=\"width: 500px;\"><tbody><tr><td style=\"width: 100.0000%;height:85px;\"><br></td></tr><tr><td style=\"text-align:center;height:60px;\">제목을 입력하세요</td></tr><tr><td style=\"padding:20px 30px 0px 30px;\"><br>내용을 입력하세요.<br><br><br><br><br><br><br><br><br><br><br></td></tr></tbody></table>";

FroalaEditor.DefineIcon('insertHTML', { NAME: 'plus', SVG_KEY: 'star' });
FroalaEditor.RegisterCommand('insertHTML', {
  title: '배경 템플릿 추가',
  focus: true,
  undo: true,
  refreshAfterCallback: true,
  callback: function () {
    this.html.insert(TableTemplete);
    this.undo.saveStep();
  }
});
//====== 버튼을 클릭하면 템플릿 입력 ======================

</script>


<script>
function SetPopupBg(v){

}	
</script>
<!-- ===========================================   froala_editor   =========================================== -->




<?php
include_once('./inc_bottom.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>