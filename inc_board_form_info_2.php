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

	$BoardContentPrice = $Row["BoardContentPrice"];
	$BoardContentHelp = $Row["BoardContentHelp"];


	$NewData = "0";

	$BoardContentWriterName = str_replace('"', "&#34;", $BoardContentWriterName);
	$BoardContentSubject = str_replace('"', "&#34;", $BoardContentSubject);

}else{
	$BoardContentSubject = "";
	$BoardContent = "";
	$BoardContentWriterPW = "";

	$BoardCategoryID = "-1";
	$BoardContentMemberID = $_LINK_MEMBER_ID_;
	
	//$BoardContentWriterName = $_LINK_MEMBER_LOGIN_ID_;
	$BoardContentWriterName = $_LINK_MEMBER_NAME_;
	
	$BoardContentReplyID = "";
	$BoardContentReplyOrder = "0";
	$BoardContentReplyDepth = "0";

	$BoardContentNotice = false;
	$BoardContentSecret = false;

	$NewData = "1";
}

if ($GetBoardContentReplyID!=""){

	//$BoardContentMemberID = $_LINK_MEMBER_ID_;
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
			<th>카테고리</th>
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
				<option value="0" <?php if ($BoardCategoryID==0) { echo "selected"; }?>>기타</option>
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
				  <?
				  if ($BoardCode=="qna"){
			   			
						if ($_LINK_MEMBER_LEVEL_ID_ <= 1){
							$BoardContentWriterName = $_LINK_MEMBER_NAME_;
						}else{
							$BoardContentWriterName = $_LINK_MEMBER_LOGIN_ID_;
						}
				  
				  ?>
				  <tr>
					<th>작성자</th>
					<td><input type="text" name="BoardContentWriterName"  class="Input" value="<?=$BoardContentWriterName?>" readonly  onfocus="this.blur();"></td>
				  </tr>
				  <?
				  }else{
				  ?>
				  <tr>
					<th>작성자</th>
					<td><input type="text" name="BoardContentWriterName"  class="Input" value="<?=$BoardContentWriterName?>"></td>
				  </tr>
				  <?
				  }
				  ?>


		  <?php
		  if ( ($_LINK_MEMBER_LEVEL_ID_==10 || $BoardContentWriterPW!="" ) && $GetBoardContentReplyID==""){
		  ?>
		  <tr>
			<th>비밀번호</th>
			<td colspan="2"><input type="password" name="BoardContentWriterNewPW"  class="Input" value=""></td>
		  </tr>
		  <?php
		  }
		  ?>
		  <tr>
			<th>제목</th>
			<td colspan="2"><input type="text" name="BoardContentSubject" value="<?=$BoardContentSubject?>" class="Input"></td>
		  </tr>
		  <tr>
			<th>가격</th>
			<td colspan="2"><input type="text" name="BoardContentPrice" value="<?=$BoardContentPrice?>" class="Input" placeholder="12,500원, 12달러 등 입력"></td>
		  </tr>

		  <tr>
			<th>사용설명서 링크</th>
			<td colspan="2"><input type="text" name="BoardContentHelp" value="<?=$BoardContentHelp?>" class="Input"></td>
		  </tr>

		  
		  <?php
		  if ($AuthNotice){
		  ?>
		  <tr style="display:none;">
			<th>공지사항</th>
			<td colspan="2" class="br_bottom title"><input type="checkbox" name="BoardContentNotice" value="1" <?php if ($BoardContentNotice==1) {echo ("checked");}?> style="width:15px;"> 공지로 등록</td>
		  </tr>
		  <?php
		  }
		  ?>
		  <?php
		  if ($EnableSecret){
		  ?>
		  <tr>
			<th>비밀글</th>
			<td colspan="2" class="br_bottom"><input type="checkbox" name="BoardContentSecret" value="1" <?php if ($BoardContentSecret==1) {echo ("checked");}?> style="width:15px;"> 비밀글로 등록</td>
		  </tr>
		  <?php
		  }
		  ?>
		  
		  <tr>
			<td colspan="3" style="padding:15px 0;">
			
			※ 본 게시판은 일반 사용자에게 상세보기 화면이 제공되지 않습니다. 리스트 형식에 맞게 내용을 작성해 주시기 바랍니다.
			<br><br>
			<textarea id="BoardContent" name="BoardContent"><?=$BoardContent?></textarea>
			</td>
		  </tr>
		  <?php
		  if ($BoardFileCount>0) {
		  ?>
		  <tr>
			<th>첨부파일</th>
			<td colspan="2">
            	
						<?php
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
                        <ul>
                			<li>
							<input type="file" id="file"  class="File" name="BoardFile<?=$FileID?>" style="margin:3px 0; width:200px;display:inline-block;">
							<?php
							if ($BoardFileID!=""){
							?>
							<input type="checkbox" name="DelBoardFile<?=$FileID?>" value="1" style=" width:14px; height:14px; vertical-align:-2px;"> 삭제
							<?php
							}
							?>	
							
                            </li>
                		</ul>			
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
            <a href="javascript:FormSubmit();" class="BtnGray">등 록</a>
            <a href="board_list.php?BoardCode=<?=$BoardCode?>" class="BtnGray">목 록</a>
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


