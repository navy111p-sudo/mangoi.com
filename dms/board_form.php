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
			$BoardContentMemberID = $_LINK_ADMIN_ID_;
			$BoardContentWriterName = $_LINK_ADMIN_NAME_;
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

		<div class="box">
			<form id="form" name="RegForm" method="post" enctype="multipart/form-data" accept-charset="UTF-8">
			<input type="hidden" name="BoardCode" value="<?=$BoardCode?>">
			<input type="hidden" name="BoardContentID" value="<?=$BoardContentID?>">
			<input type="hidden" name="BoardContentMemberID" value="<?=$BoardContentMemberID?>">

			<input type="hidden" name="BoardContentReplyID" value="<?=$BoardContentReplyID?>">
			<input type="hidden" name="BoardContentReplyOrder" value="<?=$BoardContentReplyOrder?>">
			<input type="hidden" name="BoardContentReplyDepth" value="<?=$BoardContentReplyDepth?>">

			<input type="hidden" name="ListParam" value="<?=$ListParam?>">
			<input type="hidden" name="NewData" value="<?=$NewData?>">
			
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table3">
			  
			  <?php
			  if ($BoardEnableCategory==1){
			  ?>
			  <tr>
				<th width="15%">카테고리</th>
				<td>
				<select name="BoardCategoryID">
					<option value="0">기타</option>
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
				<th width="15%">작성자</th>
				<td><input type="text" id="BoardContentWriterName" name="BoardContentWriterName"  value="<?=$BoardContentWriterName?>"/></td>
			  </tr>
			  <?php
			  if ($BoardContentWriterPW!=""){
			  ?>
			  <tr>
				<th width="15%">비밀번호</th>
				<td><input type="text" id="BoardContentWriterPW" name="BoardContentWriterPW"  value="<?=$BoardContentWriterPW?>"/></td>
			  </tr>
			  <?php
			  }
			  ?>
			  <tr>
				<th width="15%">제목</th>
				<td>
					<input type="text" id="BoardContentSubject" name="BoardContentSubject"  value="<?=$BoardContentSubject?>" style="width:300px;"/>
					<input type="checkbox" name="BoardContentNotice" value="1" <?php if ($BoardContentNotice==1) {echo ("checked");}?> > 공지사항 
					<input type="checkbox" name="BoardContentSecret" value="1" <?php if ($BoardContentSecret==1) {echo ("checked");}?> > 비밀글
				</td>
			  </tr>
			  <tr>
				<th width="15%">내용</th>
				<td>
				    <textarea id="BoardContent" name="BoardContent" rows="10" cols="30" style="width:100%;height:400px"><?=$BoardContent?></textarea>
				</td>
			  </tr>
			  <?php
			  if ($BoardFileCount>0) {
			  ?>
			  <tr>
				<th width="15%">첨부파일</th>
				<td style="line-height:1.5;">
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
				<input type="file" id="file" name="BoardFile<?=$FileID?>" style="width:300px;">
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

			<div class="button">
				<a href="javascript:FormSubmit();">등록</a>
			</div>
			
		</div>
	</div>
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




<?php
include_once('./_bottom.php');
?>
</body>
<?php
include_once('./_footer.php');
include_once('../includes/dbclose.php');
?>







