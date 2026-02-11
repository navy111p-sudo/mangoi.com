<?php
include_once('./includes/dbopen.php');
include_once('./includes/common.php');
include_once('./includes/member_check.php');
include_once('./includes/board_config.php');
?>

<?php
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$BoardContentID = isset($_REQUEST["BoardContentID"]) ? $_REQUEST["BoardContentID"] : "";
$BoardCommentID = isset($_REQUEST["BoardCommentID"]) ? $_REQUEST["BoardCommentID"] : "";
$ActionMode = isset($_REQUEST["ActionMode"]) ? $_REQUEST["ActionMode"] : "";
$Reply = isset($_REQUEST["Reply"]) ? $_REQUEST["Reply"] : "";


if ($ActionMode!="CommentDelete"){
	$Sql = "select * from BoardContents where BoardContentID=:BoardContentID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BoardContentID', $BoardContentID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$BoardContentMemberID = $Row["BoardContentMemberID"];
}
if ($ActionMode=="CommentDelete"){
	$Sql = "select * from BoardComments where BoardCommentID=:BoardCommentID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BoardCommentID', $BoardCommentID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$BoardCommentMemberID = $Row["BoardCommentMemberID"];
}


$checkPassword = true;
switch ($ActionMode) {
case "Delete":
	if ($AuthModify || ($_LINK_MEMBER_ID_!="" && $BoardContentMemberID==$_LINK_MEMBER_ID_)){
		$checkPassword = false;
		$CheckSum = md5($BoardContentID);
		setcookie("BoardCheckSum",$CheckSum);
		header("Location: board_delete.php?ListParam=$ListParam&BoardContentID=$BoardContentID"); 
		exit;		
	}
	break;
case "Modify":
	if ($AuthModify || ($_LINK_MEMBER_ID_!="" && $BoardContentMemberID==$_LINK_MEMBER_ID_) ){
		$checkPassword = false;
		$CheckSum = md5($BoardContentID);
		setcookie("BoardCheckSum",$CheckSum);
		if ($BoardCode=="cert_biz") {
			header("Location: certification_biz_form.php?ListParam=$ListParam&BoardContentID=$BoardContentID&BoardCode=$BoardCode");
		}else if ($BoardCode=="facilities") {
			header("Location: facilities_form.php?ListParam=$ListParam&BoardContentID=$BoardContentID&BoardCode=$BoardCode");
		}else if ($BoardCode=="research") {
			header("Location: research_form.php?ListParam=$ListParam&BoardContentID=$BoardContentID&BoardCode=$BoardCode");
		}else{
			header("Location: board_form.php?ListParam=$ListParam&BoardContentID=$BoardContentID&BoardCode=$BoardCode"); 
		}
		exit;		
	}
	break;	
case "SecretRead":

	//이 글이 답글인데 원글이 내글일때(회원)
	$Sql = "select * from BoardContents where BoardContentID=:BoardContentID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BoardContentID', $BoardContentID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$BoardID = $Row["BoardID"];
	$BoardContentReplyID = $Row["BoardContentReplyID"];
	$BoardContentReplyDepth = $Row["BoardContentReplyDepth"];

	$Sql = "select count(*) as TotalRowCount from BoardContents where BoardID=:BoardID and BoardContentReplyID=:BoardContentReplyID and BoardContentReplyDepth<:BoardContentReplyDepth and BoardContentMemberID=:_MEMBER_ID_";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BoardID', $BoardID);
	$Stmt->bindParam(':BoardContentReplyID', $BoardContentReplyID);
	$Stmt->bindParam(':BoardContentReplyDepth', $BoardContentReplyDepth);
	$Stmt->bindParam(':_MEMBER_ID_', $_LINK_MEMBER_ID_);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$TotalRowCount = $Row["TotalRowCount"];
	if ($TotalRowCount>0){
		$AuthSecretRead = true;
	}

	//echo $Sql;
	//이 글이 답글인데 원글이 내글일때(회원)


	if ($AuthSecretRead || ($_LINK_MEMBER_ID_!="" && $BoardContentMemberID==$_LINK_MEMBER_ID_)){
		$checkPassword = false;
		$CheckSum = md5($BoardContentID);
		setcookie("BoardCheckSum",$CheckSum);
		header("Location: board_read.php?ListParam=$ListParam&BoardContentID=$BoardContentID&BoardCode=$BoardCode&Reply=$Reply"); 
		exit;		
	}
	break;
case "CommentDelete":	
	if ($AuthModify || ($_LINK_MEMBER_ID_!="" && $BoardCommentMemberID==$_LINK_MEMBER_ID_)){
		$checkPassword = false;
		$CheckSum = md5($BoardCommentID);
		setcookie("BoardCheckSum",$CheckSum);
		header("Location: board_comment_delete.php?ListParam=$ListParam&BoardContentID=$BoardContentID&BoardCode=$BoardCode&BoardCommentID=$BoardCommentID"); 
		exit;		
	}
	break;
}


?>

<?php
if ($checkPassword){
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $_SITE_TITLE_;?><?=$UseMain?></title>
<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
<link href="css/board.css" rel="stylesheet" type="text/css">
<script src="js/javascript.js"></script>
	<?php
	include_once('./includes/common_header.php');

	if ($UseMain==1){
		$Sql = "select * from Main limit 0,1";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;

		$MainLayout = $Row["MainLayout"];
		$MainLayoutCss = $Row["MainLayoutCss"];
		$MainLayoutJavascript = $Row["MainLayoutJavascript"];
		list($MainLayoutTop, $MainLayoutBottom) = explode("{{Page}}", $MainLayout);
	}else{
		$MainLayoutTop = "";
		$MainLayoutBottom = "";
		$MainLayoutCss = "";
		$MainLayoutJavascript = "";
	}


	if ($UseSub==1){
		$Sql = "select * from Subs where SubID=:SubID";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':SubID', $SubID);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;

		$SubLayout = $Row["SubLayout"];
		$SubLayoutCss = $Row["SubLayoutCss"];
		$SubLayoutJavascript = $Row["SubLayoutJavascript"];
		list($SubLayoutTop, $SubLayoutBottom) = explode("{{Page}}", $SubLayout);
	}else{
		$SubLayoutTop = "";
		$SubLayoutBottom = "";
		$SubLayoutCss = "";
		$SubLayoutJavascript = "";
	}


	list($BoardLayoutTop, $BoardLayoutBottom) = explode("{{Board}}", $BoardLayout);



	if (trim($MainLayoutCss)!=""){
		echo "\n";
		echo "<style>";
		echo "\n";
		echo $MainLayoutCss;
		echo "\n";
		echo "</style>";
		echo "\n";
	}

	if (trim($SubLayoutCss)!=""){
		echo "\n";
		echo "<style>";
		echo "\n";
		echo $SubLayoutCss;
		echo "\n";
		echo "</style>";
		echo "\n";
	}

	if (trim($BoardCss)!=""){
		echo "\n";
		echo "<style>";
		echo "\n";
		echo $BoardCss;
		echo "\n";
		echo "</style>";
		echo "\n";
	}
	?>
	</head>
	<body>
	<?
	include_once('./includes/common_body_top.php');
	?>
	<?php
	// $MainLayoutTop = convertHTML(trim($MainLayoutTop));
	// $SubLayoutTop = convertHTML(trim($SubLayoutTop));
	// $BoardLayoutTop = convertHTML(trim($BoardLayoutTop));
	// $BoardLayoutBottom = convertHTML(trim($BoardLayoutBottom));
	// $SubLayoutBottom = convertHTML(trim($SubLayoutBottom));
	// $MainLayoutBottom = convertHTML(trim($MainLayoutBottom));


    if($DomainSiteID==7){
        $BoardLayoutTop = convertHTML(trim($BoardLayoutTop));
        $BoardLayoutBottom = convertHTML(trim($BoardLayoutBottom));

    } else if($DomainSiteID==8){ //engliseed.kr
        $BoardLayoutTop = convertHTML(trim($BoardLayoutTop));
        $BoardLayoutBottom = convertHTML(trim($BoardLayoutBottom));

    } else if($DomainSiteID==9){ //live.engedu.kr
        $BoardLayoutTop = convertHTML(trim($BoardLayoutTop));
        $BoardLayoutBottom = convertHTML(trim($BoardLayoutBottom));

      } else {
        $MainLayoutTop = convertHTML(trim($MainLayoutTop));
        $SubLayoutTop = convertHTML(trim($SubLayoutTop));
        $BoardLayoutTop = convertHTML(trim($BoardLayoutTop));
        $BoardLayoutBottom = convertHTML(trim($BoardLayoutBottom));
        $SubLayoutBottom = convertHTML(trim($SubLayoutBottom));
        $MainLayoutBottom = convertHTML(trim($MainLayoutBottom));
      }
      
	echo "\n";
	echo $MainLayoutTop;
	echo "\n";
	echo $SubLayoutTop;
	echo "\n";
	echo $BoardLayoutTop;
	echo "\n";
	?>

	
		

	<?
	$Sql = "select * from BoardContents where BoardContentID=:BoardContentID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BoardContentID', $BoardContentID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$BoardContentWriterPW = $Row["BoardContentWriterPW"];
	$BoardContentMemberID = $Row["BoardContentMemberID"];

	//echo $Sql;

	//if ($BoardContentWriterPW=="" && $BoardContentMemberID!=""){

	//	$AlertMsg = "비공개글 입니다. 글쓴 회원만 확인 가능합니다.";

	//	echo "<script>alert('".$AlertMsg."');history.go(-1);</script>";
	//}
	?>
		
		
		
		
		<form id="form" name="RegForm" method="post" enctype="multipart/form-data" accept-charset="UTF-8">
		<input type="hidden" name="ActionMode" value="<?=$ActionMode?>">
		<input type="hidden" name="BoardCode" value="<?=$BoardCode?>">
		<input type="hidden" name="BoardContentID" value="<?=$BoardContentID?>">
		<input type="hidden" name="BoardCommnetID" value="<?=$BoardCommnetID?>">
		<input type="hidden" name="ListParam" value="<?=$ListParam?>">
        
        <style>
			.BgPw{background:#f9f9f9; padding:30px 0; border:1px solid #ddd;}
			.BgPw img{margin-bottom:20px;}
			.BgPw input{border:1px solid #ccc; height:28px; width:200px; text-align:center;}
		</style>	

		<div id="bbs">
			<div class="BgPw">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
				  <tr>
					<th><img src="images/board/icon_lock.png"></th>
				  </tr>
				  <tr>
					<th><input type="password" id="Password" name="Password" placeholder="비밀번호를 입력하여 주세요."></th>
				  </tr>
				</table>
				
			  <div class="BtnCenter">
				  <!--span class="btn1"><a href="#" class="back_color1 txt_color1">목록</a></span-->
				  <a href="javascript:CheckPassword()" class="BtnGray TrnTag">확 인</a>
			  </div>
			</div>
		</div>

		</form>
        


		<script>
		function CheckPassword() {

			var StrPassword = $.trim($('#Password').val());
			
			if (StrPassword == "") {
				alert('비밀번호를 입력하세요.');
			} else {
				<?
				if ($ActionMode=="CommentDelete"){
				?>
				url = "ajax_check_pass_board_comment.php";
				StrID = "<?=$BoardCommentID?>";
				<?php
				}else{
				?>
				url = "ajax_check_pass_board_content.php";
				StrID = "<?=$BoardContentID?>";
				<?php
				}
				?>	
					
				//location.href = url + "?StrPassword="+StrPassword+"&StrID"+StrID;
				
				$.ajax(url, {
					data: {
						StrPassword: StrPassword,
						StrID: StrID
					},
					success: function (data) {
						json_data = data;
						CheckResult = json_data.CheckResult;

						if (CheckResult == 1) {

							<?php
							switch ($ActionMode) {
							case "Delete":
							?>
								location.href = "board_delete.php?ListParam=<?=$ListParam?>&BoardContentID=<?=$BoardContentID?>"; 
							<?php
								break;
							case "Modify":
							?>
								location.href = "board_form.php?ListParam=<?=$ListParam?>&BoardContentID=<?=$BoardContentID?>&BoardCode=<?=$BoardCode?>"; 
							<?php
								break;	
							case "SecretRead":
							?>
								location.href = "board_read.php?ListParam=<?=$ListParam?>&BoardContentID=<?=$BoardContentID?>&BoardCode=<?=$BoardCode?>"; 
							<?php	
								break;
							case "CommentDelete":
							?>
								location.href = "board_comment_delete.php?ListParam=<?=$ListParam?>&BoardContentID=<?=$BoardContentID?>&BoardCode=<?=$BoardCode?>&BoardCommentID=<?=$BoardCommentID?>"; 
							<?php	
								break;
							}
							?>

						}
						else {
							alert('비밀번호가 일치하지 않습니다.');
						}
					},
					error: function () {
						alert('Error while contacting server, please try again');
						document.RegForm.CheckedCode.value = "0";
						document.getElementById("BtnCodeCheck").style.display = "inline";
					}
				});

			}

		}
		</script>

	<?php
	echo $BoardLayoutBottom;
	echo "\n";
	echo $SubLayoutBottom;
	echo "\n";
	echo $MainLayoutBottom;
	echo "\n";
	?>

	<?
	include_once('./includes/common_analytics.php');
	?>

	<?php
	include_once('./includes/common_footer.php');

	if (trim($BoardJavascript)!=""){
		echo "\n";
		echo "<script>";
		echo "\n";
		echo $BoardJavascript;
		echo "\n";
		echo "</script>";
		echo "\n";
	}

	if (trim($SubLayoutJavascript)!=""){
		echo "\n";
		echo "<script>";
		echo "\n";
		echo $SubLayoutJavascript;
		echo "\n";
		echo "</script>";
		echo "\n";
	}

	if (trim($MainLayoutJavascript)!=""){
		echo "\n";
		echo "<script>";
		echo "\n";
		echo $MainLayoutJavascript;
		echo "\n";
		echo "</script>";
		echo "\n";
	}
	?>
	</body>
	</html>
<?php
}
?>


<?php
include_once('./includes/dbclose.php');
?>