<?
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
include_once('./inc_header.php');
include_once('./includes/board_config.php');
?>
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


$Sql = "select *, DATE_FORMAT(EventStartDate,'%Y-%m-%d') as EventStartDate2 , DATE_FORMAT(EventEndDate,'%Y-%m-%d') as EventEndDate2 from BoardContents where BoardContentID=:BoardContentID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':BoardContentID', $BoardContentID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$BoardContentMemberID = $Row["BoardContentMemberID"];
$BoardContentWriterName = $Row["BoardContentWriterName"];
$BoardContentWriterPW = $Row["BoardContentWriterPW"];
$BoardContentNotice = $Row["BoardContentNotice"];
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

$BoardContent = str_replace('&amp;', "&", $BoardContent);
$BoardContent = str_replace('&quot;', '"', $BoardContent);
$BoardContent = str_replace('&#039;', "'", $BoardContent);
$BoardContent = str_replace('&lt;', "<", $BoardContent);
$BoardContent = str_replace('&gt;', ">", $BoardContent);
$BoardContent = str_replace('<p></p>', "<br>", $BoardContent);

$BoardContent = str_replace('https:////', "https://", $BoardContent);

$EventStartDate = $Row["EventStartDate2"];
$EventEndDate = $Row["EventEndDate2"];


?>


<h2 class="title"><img src="images/logo_small.png"> <?=$BoardTitle?></h2>

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_form" style="margin-bottom:15px;">
  <tr>
	<th>제목<span></span></th>
	<td colspan="3"><label><?=$BoardContentSubject?></label></td>
  </tr>
  <tr>
	<th>작성자<span></span></th>
	<td><?=$BoardContentWriterName?></td>
	<th>작성일<span></span></th>
	<td><?=substr($BoardContentRegDateTime,0,10)?></td>
  </tr>
  <?if ($BoardCode=="event" || $BoardCode=="main_notice"){?>
  <tr>
	<th>시작날짜<span></span></th>
	<td><?=$EventStartDate?></td>
	<th>종료날짜<span></span></th>
	<td><?=$EventEndDate?></td>
  </tr>
  <?}?>
  <tr>
	<td colspan="4">
		<div style="min-height:200px;">
		<?
		if ($BoardCode=="event" || $BoardCode=="main_notice"){
			$Sql = "select * from BoardContentFiles  where BoardContentID=:BoardContentID order by BoardFileNumber asc";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':BoardContentID', $BoardContentID);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);

			while($Row = $Stmt->fetch()) {
		
				$file_type_check = explode('.',$Row["BoardFileName"]);
				$file_type = $file_type_check[count($file_type_check)-1];
				
				if (
					  $file_type=="png" 
					|| $file_type=="jpg"
					|| $file_type=="jpeg"
					|| $file_type=="gif"
					
					) {
		?>
			<img src="../uploads/board_files/<?=$Row["BoardFileName"]?>" width="90%">
			<br><br>
		<?
				}
			}
			$Stmt = null;
		}
		?>


		<?if ($BoardCode=="manual"){?>
		<div id="section1">
		<iframe src="https://player.vimeo.com/video/<?=$BoardContentVideoCode?>" width="100%" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
		</div>
		<br><br><br>
		<?}?>					


		<?=str_replace("\n","<br>",$BoardContent)?>

		</div>
	</td>
  </tr>
  <?
  $Sql = "select count(*) as BoardFileIDCount from BoardContentFiles where BoardContentID=:BoardContentID";
  $Stmt = $DbConn->prepare($Sql);
  $Stmt->bindParam(':BoardContentID', $BoardContentID);
  $Stmt->execute();
  $Stmt->setFetchMode(PDO::FETCH_ASSOC);
  $Row = $Stmt->fetch();
  $Stmt = null;

  $BoardFileIDCount = $Row["BoardFileIDCount"];

  if ($BoardFileIDCount>0){
  ?>
  <tr>
	<th>파일<span></span></th>
	<td colspan="3" style="line-height:1.5;">
	<?
	$Sql = "select * from BoardContentFiles  where BoardContentID=:BoardContentID order by BoardFileNumber asc";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BoardContentID', $BoardContentID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);

	while($Row = $Stmt->fetch()) {
	?>
	<?=$Row["BoardFileRealName"]?> <a href="board_file_down.php?BoardFileID=<?=$Row["BoardFileID"]?>"> &nbsp;<img src="images/filedown.png" style="vertical-align:middle;"></a><br>
	<?
	}
	$Stmt = null;
	?>
	</td>
  </tr>
  <?
  } 	
  ?>
</table>

<div class="btn_center" style="padding-top:25px;">
	<?if ($_LINK_ADMIN_LEVEL_ID_<=2){?>
	<a href="board_form.php?ListParam=<?=$ListParam?>&BoardContentID=<?=$BoardContentID?>&BoardCode=<?=$BoardCode?>" class="btn red">수정하기</a>
	<a href="javascript:DeleteContent();" class="btn red">삭제하기</a>
	<?}?>
	<a href="board_list.php?<?=str_replace("^^", "&", $ListParam)?>" class="btn gray">목록으로</a>
</div>



<div style="display:<?if ($BoardEnableComment==0){?>none<?}?>;">
<?
$Sql = "select * from BoardComments where BoardContentID=:BoardContentID order by BoardCommentRegDateTime asc";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':BoardContentID', $BoardContentID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

while($Row = $Stmt->fetch()) {
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_form" style="margin-bottom:15px;">
  <tr>
	<th>작성자<span></span></th>
	<td><?=$Row["BoardCommentWriterName"]?></td>
	<th>작성일<span></span></th>
	<td>
		<?=$Row["BoardCommentRegDateTime"]?> 
		&nbsp;
		<input type="button" value="삭제" onclick="DeleteComment(<?=$Row["BoardCommentID"]?>)">
	</td>
  </tr>
  <tr>
	<td colspan="4">
		<?=str_replace("\n","<br>",$Row["BoardComment"])?>
	</td>
  </tr>
</table>
<br>
<?
}
$Stmt = null;
?>



<?
$BoardCommentMemberID = $_LINK_ADMIN_ID_;
$BoardCommentWriterName = $_LINK_ADMIN_NAME_;
?>

<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" accept-charset="UTF-8" autocomplete="off">
<input type="hidden" name="BoardCode" value="<?=$BoardCode?>">
<input type="hidden" name="BoardContentID" value="<?=$BoardContentID?>">
<input type="hidden" name="ListParam" value="<?=$ListParam?>">			
<input type="hidden" name="BoardCommentMemberID" value="<?=$BoardCommentMemberID?>">
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_form" style="margin-bottom:15px;">
  <tr>
	<th>작성자<span></span></th>
	<td colspan="2"><input type="text" id="BoardCommentWriterName" name="BoardCommentWriterName"  value="<?=$BoardCommentWriterName?>"/></td>
  </tr>
  <tr>
	<th>댓글<span></span></th>
	<td><textarea name="BoardComment" style="width:96%;height:50px;border:1px solid #cccccc;padding:10px;"></textarea></td>
	<td><input type="button" value="전송" style="width:60%;height:70px;margin-top:-10px;" onclick="FormSubmit()"></th>
  </tr>
</table>
</form>



<script language="javascript">
function DeleteContent(){
	if (confirm('삭제하시겠습니까?')){
		location.href = "board_delete.php?ListParam=<?=$ListParam?>&BoardContentID=<?=$BoardContentID?>";
	}

}

function DeleteComment(BoardCommentID){
	if (confirm('댓글을 삭제하시겠습니까?')){
		location.href = "board_comment_delete.php?ListParam=<?=$ListParam?>&BoardContentID=<?=$BoardContentID?>&BoardCode=<?=$BoardCode?>&BoardCommentID="+BoardCommentID;
	}

}


function FormSubmit(){


	obj = document.RegForm.BoardCommentWriterName;
	if (obj.value==""){
		alert('작성자를 입력하세요.');
		obj.focus();
		return;
	}

	obj = document.RegForm.BoardComment;
	if (obj.value==""){
		alert('댓글을 작성하세요.');
		obj.focus();
		return;
	}

	document.RegForm.action = "board_comment_action.php";
	document.RegForm.submit();

}



</script>


<style>
#section1 {
  position: relative;
  padding-bottom: 56.25%;
  height: 0;
  overflow: hidden;
  max-width: 100%;
  height: auto;
}

#section1 iframe,
#section1 object,
#section1 embed {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}
</style>

<?
include_once('./inc_bottom.php');
?>
<?
include_once('./inc_bottom.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>