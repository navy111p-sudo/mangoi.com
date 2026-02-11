<?php
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$BoardContentID = isset($_REQUEST["BoardContentID"]) ? $_REQUEST["BoardContentID"] : "";


$Sql = "update BoardContents set BoardContentViewCount=BoardContentViewCount+1 where BoardContentID=:BoardContentID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':BoardContentID', $BoardContentID);
$Stmt->execute();
$Stmt = null;


$Sql = "select A.*,
		(select RegionName from Regions where RegionID=A.RegionID) as RegionName,
		(select IndustryTypeName from IndustryTypes where IndustryTypeID=A.IndustryTypeID) as IndustryTypeName,
		(select LeadTypeName from LeadTypes where LeadTypeID=A.LeadTypeID) as LeadTypeName
		from BoardContents A where A.BoardContentID=:BoardContentID";
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
$BoardContent = str_replace('<p></p>', "", $BoardContent);
$BoardContent = str_replace('<p>', "<br>", $BoardContent);
$BoardContent = str_replace('</p>', "", $BoardContent);

$RegionName = $Row["RegionName"];
$IndustryTypeName = $Row["IndustryTypeName"];
$LeadTypeName = $Row["LeadTypeName"];
$Campany = $Row["Campany"];
$Representative = $Row["Representative"];
$MajorBiz = $Row["MajorBiz"];


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
		<table width="100%" border="0" cellspacing="0" cellpadding="0" class="TbWriteTop">
		  <tr>
			<th>산업구분</th>
			<td colspan="2"><input type="text" name="" class="Input" value="<?=$IndustryTypeName?>" readonly onfocus="this.blur();"></td>
		  </tr>
		  <tr>
			<th>주도형태</th>
			<td colspan="2"><input type="text" name="" class="Input" value="<?=$LeadTypeName?>" readonly onfocus="this.blur();"></td>
		  </tr>
		  <tr>
			<th>지역</th>
			<td colspan="2"><input type="text" name="" class="Input" value="<?=$RegionName?>" readonly onfocus="this.blur();"></td>
		  </tr>
		  <tr>
			<th>소속명</th>
			<td colspan="2"><input type="text" name="" class="Input" value="<?=$Campany?>" readonly onfocus="this.blur();"></td>
		  </tr>
		  <tr>
			<th>대표자명</th>
			<td colspan="2"><input type="text" name="" class="Input" value="<?=$Representative?>" readonly onfocus="this.blur();"></td>
		  </tr>
		  <tr>
			<th>주요사업</th>
			<td colspan="2"><input type="text" name="" class="Input" value="<?=$MajorBiz?>" readonly onfocus="this.blur();"></td>
		  </tr>
		</table>
        
        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="TbWrite2">
          <tr>
            <th style="height:44px;"><h3>상세내용</h3></td>
            <td></td>
          </tr>
          <tr>
            <td colspan="2" class="Padding">
                <div style="background:#fff; line-height:1.5; border:1px solid #ddd; padding:15px;" class="fr-view"><?=$BoardContent?></div>
            </td>
          </tr>
          <?
		  $Sql = "select count(*) as TotalRowCount from BoardContentFiles  where BoardContentID=:BoardContentID";
		  $Stmt = $DbConn->prepare($Sql);
		  $Stmt->bindParam(':BoardContentID', $BoardContentID);
		  $Stmt->execute();
		  $Stmt->setFetchMode(PDO::FETCH_ASSOC);
		  $Row = $Stmt->fetch();
		  $Stmt = null;

		  $TotalRowCount = $Row["TotalRowCount"];
		  if (TotalRowCount>0){
		  ?>
		  <tr>
            <th class="Line2">파일첨부</th>
            <td class="Line2">
			<?php

				$Sql = "select * from BoardContentFiles  where BoardContentID=:BoardContentID order by BoardFileNumber asc";
				$Stmt = $DbConn->prepare($Sql);
				$Stmt->bindParam(':BoardContentID', $BoardContentID);
				$Stmt->execute();
				$Stmt->setFetchMode(PDO::FETCH_ASSOC);

				while($Row = $Stmt->fetch()) {
			?>
			<a href="board_file_down.php?BoardFileID=<?=$Row["BoardFileID"]?>"><?=$Row["BoardFileRealName"]?></a>
			<?php
				}
				$Stmt = null;
			?>
			</td>
          </tr>
		  <?
		  }
		  ?>
		</table>

		<?
		if ($ListParam==""){
			$ListParam = "BoardCode=".$BoardCode;
		}
		?>
        <div class="BtnCenter">
				<a href="board_list.php?<?=str_replace("^^", "&", $ListParam)?>" class="Btn3 Font1" style="width:80px;">목 록</a>
				<?php 
				if ($EnableReplay && $AuthReply){
				?>
				<a href="board_form.php?ListParam=<?=$ListParam?>&BoardContentID=<?=$BoardContentID?>&BoardCode=<?=$BoardCode?>&boardcontentreplyid=<?=$BoardContentReplyID?>" class="Btn3 Font1" style="width:80px;">답 글</a>
				<?php
				}
				?>
				<?php
				if ($AuthModify || $BoardContentMemberID==$_LINK_MEMBER_ID_){
				?>
				<a href="javascript:ModifyContent();" class="Btn3 Font1" style="width:80px;">수 정</a>			
				<a href="javascript:DeleteContent();" class="Btn3 Font1" style="width:80px;">삭 제</a>
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


