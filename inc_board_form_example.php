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

	//============= 추가항목 
	$RegionID = $Row["RegionID"];
	$IndustryTypeID = $Row["IndustryTypeID"];
	$LeadTypeID = $Row["LeadTypeID"];
	$Campany = $Row["Campany"];
	$Representative = $Row["Representative"];
	$MajorBiz = $Row["MajorBiz"];
	//============= 추가항목 

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


	$IndustryTypeID = 1;
	$LeadTypeID = 1;

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
		  
		  <tr style="display:none;">
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
			<th>산업구분</th>
			<td colspan="2">
					<?
					$Sql2 = "select * from IndustryTypes order by IndustryTypeID";
					$Stmt2 = $DbConn->prepare($Sql2);
					$Stmt2->execute();
					$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

					while($Row2 = $Stmt2->fetch()){
					?>
					<span><input type="radio" name="IndustryTypeID" value="<?=$Row2["IndustryTypeID"]?>" class="Rdo" <?if ($IndustryTypeID==$Row2["IndustryTypeID"]) {?>checked<?}?>> <?=$Row2["IndustryTypeName"]?></span> 
					<?
					}
					$Stmt2 = null;
					?>
			</td>
		  </tr>
		  <tr>
			<th>주도형태</th>
			<td colspan="2">
					<?
					$Sql2 = "select * from LeadTypes order by LeadTypeID";
					$Stmt2 = $DbConn->prepare($Sql2);
					$Stmt2->execute();
					$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

					while($Row2 = $Stmt2->fetch()){
					?>
					<span><input type="radio" name="LeadTypeID" value="<?=$Row2["LeadTypeID"]?>" class="Rdo" <?if ($LeadTypeID==$Row2["LeadTypeID"]) {?>checked<?}?>> <?=$Row2["LeadTypeName"]?></span> 
					<?
					}
					$Stmt2 = null;
					?>
			</td>
		  </tr>
		  <tr>
			<th>지역</th>
			<td colspan="2">
					<select name="RegionID" class="Select Font3">
						<option value="">선택하세요</option>
						<?
						$Sql2 = "select * from Regions order by RegionID";
						$Stmt2 = $DbConn->prepare($Sql2);
						$Stmt2->execute();
						$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

						while($Row2 = $Stmt2->fetch()){
						?>
						<option value="<?=$Row2["RegionID"]?>" <?if ($RegionID==$Row2["RegionID"]) {?>selected<?}?>><?=$Row2["RegionName"]?></option> 
						<?
						}
						$Stmt2 = null;
						?>
                    </select>	
			</td>
		  </tr>
		  <tr>
			<th>소속명</th>
			<td colspan="2">
					<input type="text" name="Campany" value="<?=$Campany?>" class="Input">
			</td>
		  </tr>
		  <tr>
			<th>대표자</th>
			<td colspan="2">
					<input type="text" name="Representative" value="<?=$Representative?>" class="Input">
			</td>
		  </tr>
		  <tr>
			<th>주요사업</th>
			<td colspan="2">
					<input type="text" name="MajorBiz" value="<?=$MajorBiz?>" class="Input">
			</td>
		  </tr>
		  <tr style="display:none;">
			<th>제목</th>
			<td colspan="2"><input type="text" name="BoardContentSubject" value="<?//=$BoardContentSubject?>우수사례" class="Input"></td>
		  </tr>
		  <?php
		  if ($AuthNotice){
		  ?>
		  <tr style="display:none;">
			<th>공지사항</th>
			<td colspan="2" class="br_bottom title"><input type="checkbox" name="BoardContentNotice" value="1" <?php if ($BoardContentNotice==1) {echo ("checked");}?> > 공지글로 맨 위에 출력됩니다.</td>
		  </tr>
		  <?php
		  }
		  ?>
		  <?php
		  if ($EnableSecret){
		  ?>
		  <tr style="display:none;">
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
			<th>파일첨부</th>
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




	obj = document.RegForm.RegionID;
	if (obj.value==""){
		alert('지역을 선택하세요.');
		obj.focus();
		return;
	}


	obj = document.RegForm.Campany;
	if (obj.value==""){
		alert('소속명을 입력하세요.');
		obj.focus();
		return;
	}

	obj = document.RegForm.Representative;
	if (obj.value==""){
		alert('대표자를 입력하세요.');
		obj.focus();
		return;
	}

	obj = document.RegForm.MajorBiz;
	if (obj.value==""){
		alert('주요사업을 입력하세요.');
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


