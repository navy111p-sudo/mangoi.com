
<?php
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$BoardContentID = isset($_REQUEST["BoardContentID"]) ? $_REQUEST["BoardContentID"] : "";


$Sql = "update BoardContents set BoardContentViewCount=BoardContentViewCount+1 where BoardContentID=:BoardContentID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':BoardContentID', $BoardContentID);
$Stmt->execute();
$Stmt = null;


$Sql = "select * from BoardContents where BoardContentID=:BoardContentID";
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


//$BoardContent = str_replace('<span ', "<div ", $BoardContent);
//$BoardContent = str_replace('</span ', "</div ", $BoardContent);
$BoardContent = str_replace('https:////', "https://", $BoardContent);


if ($BoardContentSecret==1) {
	$CheckSumRequest = isset($_COOKIE["BoardCheckSum"]) ? $_COOKIE["BoardCheckSum"] : "";//비밀글일때는 체크섬 체크
	$CheckSumResult = md5($BoardContentID);
	//setcookie("BoardCheckSum","");
}
	



?>


<?php
if ($BoardContentSecret==1 && $CheckSumRequest!=$CheckSumResult){
?>
<script>
	alert('비밀글로 작성된 게시물 입니다. 로그인 하시거나 비밀번호를 입력하세요.');
location.href = "board_password_form.php?BoardContentID=<?=$BoardContentID?>&BoardCode=<?=$BoardCode?>&ActionMode=SecretRead";
</script>
<?php
}else{
?>

<?
//include_once('./inc_board_sns_share.php');
?>

	<div id="bbs">
		<table width="100%" border="0" cellspacing="0" cellpadding="0" class="TbRead1">
		  <tr>
            <td class="Caption"><?=$BoardContentSubject?></td>
          </tr>
          <tr>
            <td><span class="detail"><trn class="TrnTag">작성자</trn> : <?=$BoardContentWriterName?></span> <?if ($BoardDateHide!=1) {?><span class="detail"><trn class="TrnTag">등록일</trn> : <?=$BoardContentRegDateTime?></span><?}?> <span class="detail"><trn class="TrnTag">조회수</trn> : <?=$BoardContentViewCount?></span></td>
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
			<td>
			<div>첨부파일 : 

			<?php
			$Sql = "select * from BoardContentFiles  where BoardContentID=:BoardContentID order by BoardFileNumber asc";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':BoardContentID', $BoardContentID);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);

			while($Row = $Stmt->fetch()) {
			?>
			<strong><a href="board_file_down.php?BoardFileID=<?=$Row["BoardFileID"]?>"><?=$Row["BoardFileRealName"]?></a></strong>&nbsp;
			<?php
			}
			$Stmt = null;
			?>
            </div>
			</td>
		  </tr>
		  <?php
		  } 	
		  ?>
		  <tr>
			<td class="Con1" style="min-height:200px; height:200px; vertical-align:top;"><?=$BoardContent?></td>
		  </tr>
		</table>
        


		<?php
		if ($EnableComment && $AuthComment){
		?>
			<?php
			$BoardCommentMemberID = $_LINK_MEMBER_ID_;
			$BoardCommentWriterName = $_LINK_MEMBER_NAME_;
			?>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="TbReply">
			<form id="form" name="RegForm" method="post" enctype="multipart/form-data" accept-charset="UTF-8">
			<input type="hidden" name="BoardCode" value="<?=$BoardCode?>">
			<input type="hidden" name="BoardContentID" value="<?=$BoardContentID?>">
			<input type="hidden" name="ListParam" value="<?=$ListParam?>">			
			<input type="hidden" name="BoardCommentMemberID" value="<?=$BoardCommentMemberID?>">
			  <tr>
				<td>
					<trn class="TrnTag">작성자</trn> : <input type="text" id="BoardCommentWriterName" name="BoardCommentWriterName"  value="<?=$BoardCommentWriterName?>" style="width:100px;" class="Input" /> 
					<?php
					if ($_LINK_MEMBER_LEVEL_ID_==10){
					?>
					<trn class="TrnTag">비밀번호</trn> : <input type="password" id="BoardCommentWriterPW" name="BoardCommentWriterPW"  value="" style="width:100px;" class="Input" />
					<?php
					}
					?>					
				</td>
			  </tr>
			  <tr>
				<td>
					<textarea name="BoardComment"></textarea>
					<a href="javascript:FormSubmit()" class="BtnReply TrnTag">댓글달기</a>
				</td>
			  </tr>
			</form>
			</table>

		<?php
		}
		?>
		
		<?php
		if ($EnableComment){
			$Sql = "select count(*) as BoardCommentCount from BoardComments where BoardContentID=:BoardContentID";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':BoardContentID', $BoardContentID);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);
			$Row = $Stmt->fetch();
			$Stmt = null;

			$BoardCommentCount = $Row["BoardCommentCount"];
		
			if ($BoardCommentCount>0){
		?>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="TbReply">
			<?php
			$Sql = "select * from BoardComments where BoardContentID=:BoardContentID order by BoardCommentRegDateTime asc";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':BoardContentID', $BoardContentID);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);

			while($Row = $Stmt->fetch()) {
			?>             
              <tr>
                <td>
                    <div class="ReplyTop">
                        <span>작성자 : <?=$Row["BoardCommentWriterName"]?></span> <span>날짜 : <?=$Row["BoardCommentRegDateTime"]?></span>
                        <?php
						if ($AuthModify || $Row["BoardCommentMemberID"]==$_LINK_MEMBER_ID_){
						?>
                        <a href="javascript:DeleteComment(<?=$Row["BoardCommentID"]?>,1)"><img src="images/comment_del.png"></a>
                        <?php
						}else if ($_LINK_MEMBER_LEVEL_ID_==10){
						?>
						<a href="javascript:DeleteComment(<?=$Row["BoardCommentID"]?>,0)"><img src="images/comment_del.png"></a>
						<?
						}
						?>
                        
                    </div>
                    <div class="ReplyBottom"><?=str_replace("\n","<br>",$Row["BoardComment"])?></div>
                </td>
              </tr>
			<?php
			}
			$Stmt = null;
			?>	           
			</table>              
 		<?php
		}
		}
		?>             

				<?
				if ($ListParam==""){
					$ListParam = "BoardCode=".$BoardCode;
				}
				?>        
		<div class="BtnCenter">
			<div class="left" style="display:none;">
				<span class="btn3 left"><!--a href="#" class="back_color3 txt_color2 border1">이전글</a--></span>
				<span class="btn3 left"><!--a href="#" class="back_color3 txt_color2 border1">다음글</a--></span>
			</div>


				<a href="board_list.php?<?=str_replace("^^", "&", $ListParam)?>" class="BtnGray TrnTag">목 록</a>
				<?php 
				if ($EnableReplay && $AuthReply){
				?>
				<a href="board_form.php?ListParam=<?=$ListParam?>&BoardContentID=<?=$BoardContentID?>&BoardCode=<?=$BoardCode?>&boardcontentreplyid=<?=$BoardContentReplyID?>" class="BtnGray TrnTag">답 글</a>
				<?php
				}
				?>
				<?php
				//if ($AuthModify || $BoardContentMemberID==$_LINK_MEMBER_ID_ || $_LINK_MEMBER_LEVEL_ID_==10){
				if ($AuthModify || $BoardContentMemberID==$_LINK_MEMBER_ID_ || ($_LINK_MEMBER_LEVEL_ID_==10 && $BoardCode=="free")){
				?>
					<a href="javascript:ModifyContent();" class="BtnGray TrnTag">수 정</a>
					<a href="javascript:DeleteContent();" class="BtnGray TrnTag">삭 제</a>
				<?php
				}
				?>
            
		</div>
		
	</div>

<?php
}
?>



<script language="javascript">
function ModifyContent(){
		location.href = "board_password_form.php?ListParam=<?=$ListParam?>&BoardContentID=<?=$BoardContentID?>&BoardCode=<?=$BoardCode?>&ActionMode=Modify";
}
function DeleteContent(){
	<?php
	if ($AuthModify || ($_LINK_MEMBER_ID_!="" && $BoardContentMemberID==$_LINK_MEMBER_ID_)){
	?>
	if (confirm('삭제하시겠습니까?')){
		location.href = "board_password_form.php?ListParam=<?=$ListParam?>&BoardContentID=<?=$BoardContentID?>&BoardCode=<?=$BoardCode?>&ActionMode=Delete";
	}
	<?php
	}else{
	?>
		location.href = "board_password_form.php?ListParam=<?=$ListParam?>&BoardContentID=<?=$BoardContentID?>&BoardCode=<?=$BoardCode?>&ActionMode=Delete";
	<?
	}
	?>
}




function DeleteComment(BoardCommentID,ConfirmType){
	if (ConfirmType==1){
		if (confirm('댓글을 삭제하시겠습니까?')){
			location.href = "board_password_form.php?ListParam=<?=$ListParam?>&BoardContentID=<?=$BoardContentID?>&BoardCode=<?=$BoardCode?>&BoardCommentID="+BoardCommentID+"&ActionMode=CommentDelete";
		}
	}else{
			location.href = "board_password_form.php?ListParam=<?=$ListParam?>&BoardContentID=<?=$BoardContentID?>&BoardCode=<?=$BoardCode?>&BoardCommentID="+BoardCommentID+"&ActionMode=CommentDelete";
	}

}


function FormSubmit(){


	obj = document.RegForm.BoardCommentWriterName;
	if (obj.value==""){
		alert('이름을 입력하세요.');
		obj.focus();
		return;
	}

	<?php
	if ($_LINK_MEMBER_LEVEL_ID_==10){
	?>
	obj = document.RegForm.BoardCommentWriterPW;
	if (obj.value==""){
		alert('비밀번호를 입력하세요.');
		obj.focus();
		return;
	}
	<?
	}
	?>

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


