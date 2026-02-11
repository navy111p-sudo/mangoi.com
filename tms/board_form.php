<?
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
include_once('./includes/board_config.php');
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
<?
$MainCode = 7;

if ($BoardCode=="notice"){
	$SubCode = 2;
}else if ($BoardCode=="center_reference"){
	$SubCode = 12;
}

include_once('./inc_top.php');
?>


<?

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$BoardContentID = isset($_REQUEST["BoardContentID"]) ? $_REQUEST["BoardContentID"] : "";
$GetBoardContentReplyID = isset($_REQUEST["GetBoardContentReplyID"]) ? $_REQUEST["GetBoardContentReplyID"] : "";


$Sql = "select BoardID from Boards where BoardCode=:BoardCode ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':BoardCode', $BoardCode);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$BoardID = $Row["BoardID"];


if ($BoardContentID!=""){
	$Sql = "select *, DATE_FORMAT(EventStartDate,'%Y-%m-%d') as EventStartDate2 , DATE_FORMAT(EventEndDate,'%Y-%m-%d') as EventEndDate2 from BoardContents where BoardContentID=:BoardContentID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BoardContentID', $BoardContentID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$BoardCategoryID = $Row["BoardCategoryID"];
	$BoardContentMemberID = $Row["BoardContentMemberID"];
	$BoardContentWriterName = $Row["BoardContentWriterName"];
	$BoardContentWriterPW = $Row["BoardContentWriterPW"];
	$BoardContentNotice = $Row["BoardContentNotice"];
	$BoardContentMain = $Row["BoardContentMain"];
	$BoardContentSubject = $Row["BoardContentSubject"];
	$BoardContent = $Row["BoardContent"];
	$BoardContentTag = $Row["BoardContentTag"];
	$BoardContentSecret = $Row["BoardContentSecret"];
	$BoardContentState = $Row["BoardContentState"];
	$BoardContentRegDateTime = $Row["BoardContentRegDateTime"];
	$BoardContentReplyID = $Row["BoardContentReplyID"];
	$BoardContentReplyOrder = $Row["BoardContentReplyOrder"];
	$BoardContentReplyDepth = $Row["BoardContentReplyDepth"];
	$BoardContentViewCount = $Row["BoardContentViewCount"];

	$BoardContentVideoCode = $Row["BoardContentVideoCode"];

	$NewData = "0";

	$BoardContentWriterName = str_replace('"', "&#34;", $BoardContentWriterName);
	$BoardContentSubject = str_replace('"', "&#34;", $BoardContentSubject);

	$EventStartDate = $Row["EventStartDate2"];
	$EventEndDate = $Row["EventEndDate2"];


}else{
	$BoardContentMemberID = $_LINK_ADMIN_ID_;
	$BoardContentWriterName = $_LINK_ADMIN_NAME_;
	$BoardContentWriterPW = "";
	$BoardContentSubject = "";
	$BoardContent = "";
	$BoardContentReplyID = "";
	$BoardContentReplyOrder = "0";
	$BoardContentReplyDepth = "0";

	$BoardContentNotice = 0;
	$BoardContentMain = 0;
	$BoardContentSecret = 0;

	$NewData = "1";

	$EventStartDate = date("Y-m-d");
	$EventEndDate = date("Y-m-d");
}

if ($GetBoardContentReplyID!=""){

	$BoardContentMemberID = $_MEMBER_ID_;
	$BoardContentWriterName = $_MEMBER_NAME_;

	$BoardContentID = "";
	$NewData = "1";
	$BoardContentSubject = "[답변]".$BoardContentSubject;
	$BoardContent = "<br><br><br>======================================================<br><br>".$BoardContent;
}

?>
	

<h1 class="Title" style="margin-bottom:20px;"><?=$BoardTitle?></h1>
			
<form id="form" name="RegForm" method="post" enctype="multipart/form-data" accept-charset="UTF-8">
<input type="hidden" name="BoardCode" value="<?=$BoardCode?>">
<input type="hidden" name="BoardContentID" value="<?=$BoardContentID?>">
<input type="hidden" name="BoardContentMemberID" value="<?=$BoardContentMemberID?>">

<input type="hidden" name="BoardContentReplyID" value="<?=$BoardContentReplyID?>">
<input type="hidden" name="BoardContentReplyOrder" value="<?=$BoardContentReplyOrder?>">
<input type="hidden" name="BoardContentReplyDepth" value="<?=$BoardContentReplyDepth?>">

<input type="hidden" name="ListParam" value="<?=$ListParam?>">
<input type="hidden" name="NewData" value="<?=$NewData?>">


<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_form" style="margin-bottom:15px;">
  
  <?
  if ($BoardEnableCategory==1){
  ?>
  <tr>
	<th>카테고리<span></span></th>
	<td colspan="3">
	<select name="BoardCategoryID">
		<?
		$Sql2 = "select * from BoardCategories  where BoardID=:BoardID order by BoardCategoryOrder asc";
		$Stmt2 = $DbConn->prepare($Sql2);
		$Stmt2->bindParam(':BoardID', $BoardID);
		$Stmt2->execute();
		$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

		while($Row2 = $Stmt2->fetch()){
		?>
		<option value="<?=$Row2["BoardCategoryID"]?>" <? if ($BoardCategoryID==$Row2["BoardCategoryID"]) { echo "selected"; }?>><?=$Row2["BoardCategoryName"]?></option>
		<?
		}
		$Stmt2 = null;
		?>
	</select>
	</td>
  </tr>
  <?
  }else{
  ?>
  <input type="hidden" name="BoardCategoryID" value="0">
  <?
  }
  ?>
  <tr>
	<th>작성자<span></span></th>
	<td colspan="3"><input type="text" id="BoardContentWriterName" name="BoardContentWriterName" value="<?=$BoardContentWriterName?>" style="width:200px;"/></td>
  </tr>
  <?if ($BoardContentID!=""){?>
  <tr style="display:none;">
	<th>조회수 수정<span></span></th>
	<td colspan="3"><input type="text" id="BoardContentViewCount" name="BoardContentViewCount" value="<?=$BoardContentViewCount?>" style="width:50px;text-align:center;padding:0px;"/> 입력한 숫자 부터 카운트 됩니다.</td>
  </tr>
  <tr style="display:none;">
	<th>작성일 수정<span></span></th>
	<td colspan="3"><input type="text" id="BoardContentRegDateTime" name="BoardContentRegDateTime" value="<?=$BoardContentRegDateTime?>" style="width:200px;"/> 작성형식 : 2017-12-01 16:02:56 (반드시 지정한 형식으로 입력하세요.)</td>
  </tr>
  <?}?>

  <?
  if ($BoardContentWriterPW!=""){
  ?>
  <tr>
	<th>비밀번호<span></span></th>
	<td colspan="3"><input type="text" id="BoardContentWriterPW" name="BoardContentWriterPW" placeholder="비밀번호" value="<?=$BoardContentWriterPW?>" style="width:200px;"/></td>
  </tr>
  <?
  }
  ?>
  <tr>
	<th>제목<span></span></th>
	<td colspan="3">
		<input type="text" id="BoardContentSubject" name="BoardContentSubject" placeholder="제목을 입력해 주세요." value="<?=$BoardContentSubject?>"/>
	</td>
  </tr>
  <?if ($_LINK_ADMIN_LEVEL_ID_<=2){?>
  <tr>
	<th>옵션<span></span></th>
	<td colspan="3" class="check">
		<input type="checkbox" name="BoardContentNotice" id="BoardContentNotice" value="1" <? if ($BoardContentNotice==1) {echo ("checked");}?> > <label for="BoardContentNotice"><span></span>공지사항</label> 
		<input type="checkbox" name="BoardContentMain" id="BoardContentMain" value="1" <? if ($BoardContentMain==1) {echo ("checked");}?> > <label for="BoardContentMain"><span></span>메인화면출력</label>
		<input type="checkbox" name="BoardContentSecret" id="BoardContentSecret" value="1" <? if ($BoardContentSecret==1) {echo ("checked");}?> > <label for="BoardContentSecret"><span></span>비밀글</label>
	</td>
  </tr>
  <?}?>
  <?if ($BoardCode=="event" || $BoardCode=="main_notice"){?>
  <tr>
	<th>시작날짜<span></span></th>
	<td><input type="text" id="EventStartDate" name="EventStartDate"  value="<?=$EventStartDate?>" style="width:160px;"/></td>
	<th>종료날짜<span></span></th>
	<td><input type="text" id="EventEndDate" name="EventEndDate"   value="<?=$EventEndDate?>" style="width:160px;"/></td>
  </tr>

  <script>
	$(document).ready(function() {
		$("#EventStartDate").kendoDatePicker({
			format: "yyyy-MM-dd"
		});
		$("#EventEndDate").kendoDatePicker({
			format: "yyyy-MM-dd"
		});
	});
  </script>
  <?}?>
  
  
  <?if ($BoardCode=="manual"){?>
  <tr>
	<th>비메오코드</th>
	<td colspan="3"><input type="text" id="BoardContentVideoCode" name="BoardContentVideoCode" value="<?=$BoardContentVideoCode?>"/></td>
  </tr>
  <?}?>
  <tr>
	<td colspan="4">
		<textarea id="BoardContent" name="BoardContent" rows="10" cols="30" style="width:60%;height:400px"><?=$BoardContent?></textarea>
	</td>
  </tr>
  
  
  <?
  if ($BoardFileCount>0) {
  ?>
  <tr>
	<th>첨부파일</th>
	<td  colspan="3" class="check">
	<?
	for ($FileID=1;$FileID<=$BoardFileCount;$FileID++){
		
		$Sql = "select count(*) as BoardFileIDCount from BoardContentFiles where BoardContentID=:BoardContentID and BoardFileNumber=:FileID";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':BoardContentID', $BoardContentID);
		$Stmt->bindParam(':FileID', $FileID);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;

		$BoardFileIDCount = $Row["BoardFileIDCount"];

		if ($BoardFileIDCount>0){					
			$Sql = "select BoardFileID from BoardContentFiles where BoardContentID=:BoardContentID and BoardFileNumber=:FileID";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':BoardContentID', $BoardContentID);
			$Stmt->bindParam(':FileID', $FileID);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);
			$Row = $Stmt->fetch();
			$Stmt = null;

			$BoardFileID = $Row["BoardFileID"];
		}else{
			$BoardFileID = "";
		}
	?>
		<input type="file" id="file" name="BoardFile<?=$FileID?>" style="width:300px;">
		<?
		if ($BoardFileID!=""){
		?>
		<input type="checkbox" name="DelBoardFile<?=$FileID?>" id="DelBoardFile<?=$FileID?>" value="1"> <label for="DelBoardFile<?=$FileID?>"><span></span>삭제</label>
		<?
		}

		if ($BoardCode=="event"){
			if ($FileID==1){
				echo "&nbsp; &nbsp;리스트 이미지 (640 * 150)";
			}else if ($FileID==2){
				echo "&nbsp; &nbsp;내용 이미지 (가로 640)";
			}
		}else if ($BoardCode=="main_notice"){
			if ($FileID==1){
				echo "&nbsp; &nbsp;메인 이미지 (1080 * 600)";
			}else if ($FileID==2){
				echo "&nbsp; &nbsp;내용 이미지 (가로 640)";
			}					
		}
		?>				
	<br> 
	<?
	}
	?>
	</td>
  </tr>
  <?
  }
  ?>

</table>
</form>


<div class="btn_center" style="padding-top:25px;">
	<?
	if ($BoardContentID==""){ 
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

	obj = document.RegForm.BoardContentWriterName;
	if (obj.value==""){
		alert('작성자를 입력하세요.');
		obj.focus();
		return;
	}
	
	obj = document.RegForm.BoardContentSubject;
	if (obj.value==""){
		alert('제목을 입력하세요.');
		obj.focus();
		return;
	}
	
	

	document.RegForm.action = "board_action.php";
	document.RegForm.submit();
}

</script>


<!-- ===========================================   froala_editor   =========================================== -->
<style>
#BoardContent {
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
  const editorInstance = new FroalaEditor('#BoardContent', {
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


</script>
<!-- ===========================================   froala_editor   =========================================== -->


<?
include_once('./inc_bottom.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>






