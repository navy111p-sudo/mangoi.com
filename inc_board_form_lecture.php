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

	$VideoSaveType = $Row["VideoSaveType"];
	$VideoFileName = $Row["VideoFileName"];
	$VideoFileName1 = $Row["VideoFileName1"];
	$VideoFileName2 = $Row["VideoFileName2"];
	$VideoFileName3 = $Row["VideoFileName3"];



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



<style>
body{background:#fff;}
.ContentPopup{padding:30px 30px; text-align:center;}
.ContentPopup h2{border-bottom:1px solid #ccc; padding-bottom:10px; font-size:16px; color:#444; text-align:left; margin-bottom:50px;}


#progress-container {
            -webkit-box-shadow: none;
            box-shadow: inset none;
            display:none;
        }
</style>


	<div id="bbs">
		<table width="100%" border="0" cellspacing="0" cellpadding="0" class="TbWriteTop">
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

		<input type="hidden" name="VideoID" value="<?=$VideoFileName?>">
		
		  
		  
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
		  
		  <tr style="display:none;">
			<th>작성자</th>
			<td><input type="text" name="BoardContentWriterName"  class="Input" value="<?=$BoardContentWriterName?>"></td>
		  </tr>
		  <?php
		  if ($_LINK_MEMBER_LEVEL_ID_==10 || $BoardContentWriterPW!=""){
		  ?>
		  <tr style="display:none;">
			<th>비밀번호</th>
			<td colspan="2"><input type="password" name="BoardContentWriterNewPW"  class="Input" value="<?=$BoardContentWriterNewPW?>"></td>
		  </tr>
		  <?php
		  }
		  ?>
		  <tr>
			<th>강의제목</th>
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
		  <tr style="display:none;">
			<th>비밀글</th>
			<td colspan="2" class="br_bottom title"><input type="checkbox" name="BoardContentSecret" value="1" <?php if ($BoardContentSecret==1) {echo ("checked");}?> > 비밀글로 작성자와 관리자만 볼 수 있습니다.</td>
		  </tr>
		  <?php
		  }
		  ?>
		  
		  <tr>
			<th>강의내용</th>
			<td colspan="2" style="padding-top:5px;padding-bottom:5px;">
			<textarea id="BoardContent" name="BoardContent"><?=$BoardContent?></textarea>
			</td>
		  </tr>
		  <?php
		  if ($BoardFileCount>0) {
		  ?>
		  <tr style="display:none;">
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
		  <tr style="display:none;">
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
		  </form>
		
		  <form id="RegForm2" enctype="multipart/form-data" method="post" onsubmit="return doSubmit(event, this);">
		  <input type="hidden" id="accessToken" name="accessToken" value="<?=$VimeoApiKey?>">
		  <input type="hidden" id="videoName" name="name" value="With Study">
		  <input type="hidden" id="videoDescription" name="description" value="With Study">
		  <input type="checkbox" id="upgrade_to_1080" name="upgrade_to_1080" style="display:none;">
		  <input type="checkbox" id="make_private" name="make_private" style="display:none;">
		  <tr>
			<th>강의영상</th>
			<td colspan="2" style="padding-top:5px;">
			
				<div id="progress-container" class="progress">
					<div id="progress" class="progress-bar progress-bar-info progress-bar-striped active" role="progressbar" aria-valuenow="46" aria-valuemin="0" aria-valuemax="100" style="width: 0%">&nbsp;0%
					</div>
				</div>

				<div id="results"></div>

				<div style="width:100%;text-align:left;">
					<input id="browse"  type="file" style="display:inline-block;width:300px; height:32px;"> 
					<a class="Btn3 Font1" id="BtnUpload" style="color:#ffffff;cursor:pointer;">영상업로드</a>
				</div>



			</td>
		  </tr>
		  </form>
		</table>

        <div class="BtnCenter">
            <a href="javascript:FormSubmit();" class="BtnGray">등 록</a>
            <a href="board_list.php?BoardCode=<?=$BoardCode?>" class="BtnGray">목 록</a>
        </div>
	</div>

<?php
}
?>


<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.rawgit.com/websemantics/bragit/0.1.2/bragit.js"></script>
<script type="text/javascript" src="./js_vimeo/vimeo-upload.js"></script>
<script type="text/javascript">

	function handleFileSelect(evt) {
		if (document.getElementById('browse').value!=""){
			evt.stopPropagation()
			evt.preventDefault()

			var files = evt.dataTransfer ? evt.dataTransfer.files : $('#browse').get(0).files
			var results = document.getElementById('results')

			/* Clear the results div */
			while (results.hasChildNodes()) results.removeChild(results.firstChild)

			/* Rest the progress bar and show it */
			updateProgress(0)
			document.getElementById('progress-container').style.display = 'block'

			
			/* Instantiate Vimeo Uploader */
			;(new VimeoUpload({
				name: document.getElementById('videoName').value,
				description: document.getElementById('videoDescription').value,
				private: document.getElementById('make_private').checked,
				file: files[0],
				token: document.getElementById('accessToken').value,
				upgrade_to_1080: document.getElementById('upgrade_to_1080').checked,
				onError: function(data) {
					//alert(JSON.parse(data))
					alert('업로드 ERROR');
					document.getElementById('BtnUpload').style.display ="";
				},
				onProgress: function(data) {
					document.getElementById('BtnUpload').style.display ="none";
					updateProgress(data.loaded / data.total);
				},
				onComplete: function(videoId, index) {
					
					
					//parent.$.fn.colorbox.close();
					document.RegForm.VideoID.value = videoId;
					alert('업로드 완료');
					//document.RegForm.action="pop_upload_market_question_answer_action.php";
					//document.RegForm.submit();
					//var url = 'https://vimeo.com/' + videoId

					//if (index > -1) {
					//	/* The metadata contains all of the uploaded video(s) details see: https://developer.vimeo.com/api/endpoints/videos#/{video_id} */
					//	url = this.metadata[index].link //

					//	/* add stringify the json object for displaying in a text area */
					//	var pretty = JSON.stringify(this.metadata[index], null, 2)

					//	console.log(pretty) /* echo server data */
					//}

				}
			})).upload()
		}else{
			alert('영상파일을 선택하세요.');
		}

	}



	/**
	 * Dragover handler to set the drop effect.
	 */
	function handleDragOver(evt) {
		evt.stopPropagation()
		evt.preventDefault()
		evt.dataTransfer.dropEffect = 'copy'
	}

	/**
	 * Updat progress bar.
	 */
	function updateProgress(progress) {
		progress = Math.floor(progress * 100)
		var element = document.getElementById('progress')
		element.setAttribute('style', 'width:' + progress + '%')
		element.innerHTML = '&nbsp;' + progress + '%'
	}
	/**
	 * Wire up drag & drop listeners once page loads
	 */
	/*
	document.addEventListener('DOMContentLoaded', function() {
		var dropZone = document.getElementById('drop_zone')
		var browse = document.getElementById('browse')
		dropZone.addEventListener('dragover', handleDragOver, false)
		dropZone.addEventListener('drop', handleFileSelect, false)
		browse.addEventListener('change', handleFileSelect, false)
	})
	*/

	document.addEventListener('DOMContentLoaded', function() {
		var btn = document.getElementById('BtnUpload')
		btn.addEventListener('click', handleFileSelect, false)
	})

</script>

<script>

function OpenVideo(VimeoCode){
		openurl = "pop_play_video.php?VideoSaveType=5&VideoFileName="+VimeoCode+"&VideoFileName1="+VimeoCode;
		
		if (DeviceType==1){
			$.colorbox({	
				href:openurl
				,width:"90%" 
				,height:"90%"
				,title:""
				,iframe:true 
				,scrolling:true
				//,onClosed:function(){location.reload(true);}   
			});	
		}else{
			window.open(openurl,'pop_play_video','width=100%,height=100%,toolbar=no,top=100,left=100');
		}
}
</script>

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
		alert('강의제목을 입력하세요.');
		obj.focus();
		return;
	}

	/*
	obj = document.RegForm.VideoID;
	if (obj.value==""){
		alert('강의영상을 업로드 하세요.');
		obj.focus();
		return;
	}
	*/
	
	document.RegForm.action = "board_action.php";
	document.RegForm.submit();
}

</script>


