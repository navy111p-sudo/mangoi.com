<?php
$top_menu_id = 5;
$left_menu_id = 2;
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
include_once('./_header.php');
include_once('./includes/board_config.php');
?>
<body>
<?php
include_once('./_top.php');
include_once('./_left.php');
?>




<div class="right">
	<div class="content">
		<h2><?=$BoardName?></h2>
		<?php
		$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
		$BoardContentID = isset($_REQUEST["BoardContentID"]) ? $_REQUEST["BoardContentID"] : "";

		$Sql = "select * from BoardContents where BoardContentID=:BoardContentID ";
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
		$NewData = "0";

		$BoardContentWriterName = str_replace('"', "&#34;", $BoardContentWriterName);
		$BoardContentSubject = str_replace('"', "&#34;", $BoardContentSubject);

		$BoardContent = str_replace('&amp;', "&", $BoardContent);
		$BoardContent = str_replace('&quot;', '"', $BoardContent);
		$BoardContent = str_replace('&#039;', "'", $BoardContent);
		$BoardContent = str_replace('&lt;', "<", $BoardContent);
		$BoardContent = str_replace('&gt;', ">", $BoardContent);
		$BoardContent = str_replace('<p></p>', "<br>", $BoardContent);


		
		
		?>


		<div class="box">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table2">
			  <tr>
				<th width="15%">작성자</th>
				<td width="35%"><?=$BoardContentWriterName?></td>
				<th width="15%">작성일</th>
				<td width="35%"><?=$BoardContentRegDateTime?></td>
			  </tr>
			  <tr>
				<th>제목</th>
				<td colspan="3" class="subject"><?=$BoardContentSubject?></td>
			  </tr>
			  <tr>
				<th  style="vertical-align:top;">내용</th>
				<td colspan="3" class="subject" style="min-height:200px; vertical-align:top;"><?=$BoardContent?></td>
			  </tr>
			  <?php
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
				<th style="vertical-align:top;">파일</th>
				<td colspan="3" class="subject">
				<?php
				$Sql = "select * from BoardContentFiles  where BoardContentID=:BoardContentID order by BoardFileNumber asc";
				$Stmt = $DbConn->prepare($Sql);
				$Stmt->bindParam(':BoardContentID', $BoardContentID);
				$Stmt->execute();
				$Stmt->setFetchMode(PDO::FETCH_ASSOC);

				while($Row = $Stmt->fetch()) {
				?>
				<?=$Row["BoardFileRealName"]?> <a href="board_file_down.php?BoardFileID=<?=$Row["BoardFileID"]?>"><img src="images/filedown.png" style="vertical-align:middle;"></a><br>
				<?php
				}
				$Stmt = null;
				?>
				</td>
			  </tr>
			  <?php
			  } 	
			  ?>
			</table>

			
			<div class="button" style="margin-bottom:20px;">
				<a href="board_list.php?<?=str_replace("^^", "&", $ListParam)?>">목록</a>
				<a href="board_form.php?ListParam=<?=$ListParam?>&BoardContentID=<?=$BoardContentID?>&BoardCode=<?=$BoardCode?>&boardcontentreplyid=<?=$BoardContentReplyID?>">답변</a>
				<a href="board_form.php?ListParam=<?=$ListParam?>&BoardContentID=<?=$BoardContentID?>&BoardCode=<?=$BoardCode?>">수정</a>
				<a href="javascript:DeleteContent();">삭제</a>
			</div>


			<?php
			$Sql = "select * from BoardComments where BoardContentID=:BoardContentID order by BoardCommentRegDateTime asc";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':BoardContentID', $BoardContentID);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);

			while($Row = $Stmt->fetch()) {
			?>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table2">
			  <tr>
				<td class="subject">
					<b>작성자</b> : <?=$Row["BoardCommentWriterName"]?> &nbsp;
					<b>작성일</b> : <?=$Row["BoardCommentRegDateTime"]?> &nbsp;
					<input type="button" value="삭제" onclick="DeleteComment(<?=$Row["BoardCommentID"]?>)">
				</td>
			  </tr>
			  <tr>
				<td class="subject"><?=str_replace("\n","<br>",$Row["BoardComment"])?></th>
			  </tr>
			</table>
			<br>
			<?php
			}
			$Stmt = null;
			?>

			
			
			<?php
			$BoardCommentMemberID = $_ADMIN_ID_;
			$BoardCommentWriterName = $_ADMIN_NAME_;
			?>

			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table2">
			<form id="form" name="RegForm" method="post" enctype="multipart/form-data" accept-charset="UTF-8">
			<input type="hidden" name="BoardCode" value="<?=$BoardCode?>">
			<input type="hidden" name="BoardContentID" value="<?=$BoardContentID?>">
			<input type="hidden" name="ListParam" value="<?=$ListParam?>">			
			<input type="hidden" name="BoardCommentMemberID" value="<?=$BoardCommentMemberID?>">			
			  <tr>
				<th width="15%">작성자</th>
				<td class="subject" colspan="2"><input type="text" id="BoardCommentWriterName" name="BoardCommentWriterName"  value="<?=$BoardCommentWriterName?>"/></td>
			  </tr>
			  <tr>
				<th width="15%">댓글</th>
				<td class="subject" style="border-right:0px;"><textarea name="BoardComment" style="width:100%;height:50px;border:1px solid #cccccc;padding:10px;"></textarea></td>
				<td width="15%"><input type="button" value="전송" style="width:60%;height:70px;" onclick="FormSubmit()"></th>
			  </tr>
			</form>
			</table>


		</div>

	
	
	</div>
</div>	



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




<?php
include_once('./_bottom.php');
?>
</body>
<?php
include_once('./_footer.php');
include_once('../includes/dbclose.php');
?>







