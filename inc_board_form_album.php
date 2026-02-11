<?php
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$BoardContentID = isset($_REQUEST["BoardContentID"]) ? $_REQUEST["BoardContentID"] : "";
$GetBoardContentReplyID = isset($_REQUEST["boardcontentreplyid"]) ? $_REQUEST["boardcontentreplyid"] : "";


$Sql = "select BoardID from Boards where BoardCode=:BoardCode";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':BoardCode', $BoardCode);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$BoardID = $Row["BoardID"];


if ($BoardContentID!=""){
	
	$CheckSumRequest = isset($_COOKIE["BoardCheckSum"]) ? $_COOKIE["BoardCheckSum"] : "";//비밀글일때는 체크섬 체크
	$CheckSumResult = md5($BoardContentID);
	//setcookie("BoardCheckSum","");
	
	$Sql = "select * from BoardContents where BoardContentID=:BoardContentID";
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

}else{
	$BoardContentSubject = "";
	$BoardContent = "";
	$BoardContentWriterPW = "";

	$BoardContentMemberID = $_LINK_MEMBER_ID_;
	$BoardContentWriterName = $_LINK_MEMBER_NAME_;
	$BoardContentReplyID = "";
	$BoardContentReplyOrder = "0";
	$BoardContentReplyDepth = "0";

	$BoardContentNotice = false;
	$BoardContentSecret = false;

	$NewData = "1";
}

if ($GetBoardContentReplyID!=""){

	$BoardContentMemberID = $_LINK_MEMBER_ID_;
	$BoardContentWriterName = $_LINK_MEMBER_NAME_;

	$BoardContentID = "";
	$NewData = "1";
	$BoardContentSubject = "[답변]".$BoardContentSubject;
	$BoardContent = "<br><br><br>======================================================<br><br>".$BoardContent;
}

?>


<?php
if (!$AuthWrite){
?>
<script>
alert('권한이 없습니다.');
history.go(-1);
</script>
<?php
}else if(!$AuthReply && $GetBoardContentReplyID!=""){
?>
<script>
alert('권한이 없습니다.');
history.go(-1);
</script>
<?php
}else if($BoardContentID!="" && $CheckSumRequest!=$CheckSumResult){
?>
<script>
alert('잘못된 접근 입니다.');
history.go(-1);
</script>
<?php
}else{
?>



	<div id="bbs">
		<form id="form" name="RegForm" method="post" enctype="multipart/form-data" accept-charset="UTF-8">
		<input type="hidden" name="BoardCode" value="<?=$BoardCode?>">
		<input type="hidden" name="BoardContentID" value="<?=$BoardContentID?>">
		<input type="hidden" name="BoardContentMemberID" value="<?=$BoardContentMemberID?>">

		<input type="hidden" name="BoardContentReplyID" value="<?=$BoardContentReplyID?>">
		<input type="hidden" name="BoardContentReplyOrder" value="<?=$BoardContentReplyOrder?>">
		<input type="hidden" name="BoardContentReplyDepth" value="<?=$BoardContentReplyDepth?>">

		<input type="hidden" name="BoardContentWriterPW" value="<?=$BoardContentWriterPW?>">

		<input type="hidden" name="ListParam" value="<?=$ListParam?>">
		<input type="hidden" name="NewData" value="<?=$NewData?>">
		<table width="100%" border="0" cellspacing="0" cellpadding="0" class="TbWriteTop">
		  
		  
		  <?php
		  if ($BoardEnableCategory==1){
		  ?>
		  <tr>
			<th class="th_color br_right br_bottom br_top txt_right">카테고리</th>
			<td class="br_bottom br_top title">
			<select name="BoardCategoryID" class="Select">
				<?
				$Sql2 = "select * from BoardCategories  where BoardID=:BoardID order by BoardCategoryOrder asc";
				$Stmt2 = $DbConn->prepare($Sql2);
				$Stmt2->bindParam(':BoardID', $BoardID);
				$Stmt2->execute();
				$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

				while($Row2 = $Stmt2->fetch()){
				?>
				<option value="<?=$Row2["BoardCategoryID"]?>" <?php if ($BoardCategoryID==$Row2["BoardCategoryID"]) { echo "selected"; }?>><?=$Row2["BoardCategoryName"]?></option>
				<?
				}
				$Stmt2 = null;
				?>
			</select>
			</td>
		  </tr>
		  <?php
		  }else{
		  ?>
		  <input type="hidden" name="BoardCategoryID" value="0">
		  <?php
		  }
		  ?>
		  
		  <tr>
			<th>작성자</th>
			<td><input type="text" name="BoardContentWriterName"  class="Input" value="<?=$BoardContentWriterName?>"></td>
		  </tr>
		  <?php
		  if ($_LINK_MEMBER_LEVEL_ID_==10 || $BoardContentWriterPW!=""){
		  ?>
		  <tr>
			<th>비밀번호</th>
			<td colspan="2"><input type="password" name="BoardContentWriterNewPW"  class="Input" value="<?=$BoardContentWriterNewPW?>"></td>
		  </tr>
		  <?php
		  }
		  ?>
		  <tr>
			<th>제목</th>
			<td colspan="2"><input type="text" name="BoardContentSubject" value="<?=$BoardContentSubject?>" class="Input"></td>
		  </tr>
		  <?php
		  if ($AuthNotice){
		  ?>
		  <tr>
			<th>공지사항</th>
			<td colspan="2" class="br_bottom title"><input type="checkbox" name="BoardContentNotice" value="1" <?php if ($BoardContentNotice==1) {echo ("checked");}?> > 공지글로 맨 위에 출력됩니다.</td>
		  </tr>
		  <?php
		  }
		  ?>
		  <?php
		  if ($EnableSecret){
		  ?>
		  <tr>
			<th>비밀글</th>
			<td colspan="2" class="br_bottom title"><input type="checkbox" name="BoardContentSecret" value="1" <?php if ($BoardContentSecret==1) {echo ("checked");}?> > 비밀글로 작성자와 관리자만 볼 수 있습니다.</td>
		  </tr>
		  <?php
		  }
		  ?>
		  
		  <tr>
			<td colspan="3">
			<textarea id="BoardContent" name="BoardContent"><?=$BoardContent?></textarea>
			</td>
		  </tr>
		  <?php
		  if ($BoardFileCount>0) {
		  ?>
		  <tr>
			<th>대표사진</th>
			<td colspan="2">
				<?php
				for ($FileID=1;$FileID<=1;$FileID++){
					
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
				<input type="file" id="file"  class="File" name="BoardFile<?=$FileID?>" style="margin:2px 0;">
					<?php
					if ($BoardFileID!=""){
					?>
					<input type="checkbox" name="DelBoardFile<?=$FileID?>" value="1"> 삭제
					<?php
					}
					?>				
				<br> 
				<?php
			    }
				?>
				※ 목록에 출력되는 사진 입니다.
			</td>
		  </tr>
		  <tr>
			<th>첨부파일</th>
			<td colspan="2">
				<?php
				for ($FileID=2;$FileID<=$BoardFileCount;$FileID++){
					
					$Sql = "select count(*) as BoardFileIDCount from BoardContentFiles where BoardContentID=:BoardContentID and BoardFileNumber=:FileID";
					$Stmt = $DbConn->prepare($Sql);
					$Stmt->bindParam(':BoardContentID', $BoardContentID);
					$Stmt->bindParam(':FileID', $FileID);
					$Stmt->execute();
					$Stmt->setFetchMode(PDO::FETCH_ASSOC);
					$Row = $Stmt->fetch();

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
				<input type="file" id="file"  class="File" name="BoardFile<?=$FileID?>" style="margin:2px 0;">
					<?php
					if ($BoardFileID!=""){
					?>
					<input type="checkbox" name="DelBoardFile<?=$FileID?>" value="1"> 삭제
					<?php
					}
					?>				
				<br> 
				<?php
			    }
				?>
			</td>
		  </tr>
		  <?php
		  }
		  ?>
		</table>
		</form>



        <div class="BtnCenter">
            <a href="javascript:FormSubmit();" class="Btn3 Font1" style="width:120px;">등 록</a>
            <a href="board_list.php?BoardCode=<?=$BoardCode?>" class="Btn3 Font1" style="width:120px;">목 록</a>
        </div>
	</div>

<?php
}
?>


<script language="javascript">
function FormSubmit(){

	obj = document.RegForm.BoardContentWriterName;
	if (obj.value==""){
		alert('작성자를 입력하세요.');
		obj.focus();
		return;
	}

	<?php
	if ($_LINK_MEMBER_LEVEL_ID_==10 && $BoardContentWriterPW==""){
	?>	
	obj = document.RegForm.BoardContentWriterNewPW;
	if (obj.value==""){
		alert('비밀번호를 입력하세요.');
		obj.focus();
		return;
	}
	<?php
	}
	?>

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


