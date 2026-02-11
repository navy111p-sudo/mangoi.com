<?php
include_once('./includes/dbopen.php');
include_once('./includes/common.php');
include_once('./includes/board_config.php');

$err_num = 0;
$err_msg = "";

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$ListParam = str_replace("^^", "&", $ListParam);
$NewData = isset($_REQUEST["NewData"]) ? $_REQUEST["NewData"] : "";
$BoardCode = isset($_REQUEST["BoardCode"]) ? $_REQUEST["BoardCode"] : "";
$BoardContentID = isset($_REQUEST["BoardContentID"]) ? $_REQUEST["BoardContentID"] : "";
$BoardContentMemberID = isset($_REQUEST["BoardContentMemberID"]) ? $_REQUEST["BoardContentMemberID"] : "";
$BoardContentWriterName = isset($_REQUEST["BoardContentWriterName"]) ? $_REQUEST["BoardContentWriterName"] : "";
$BoardContentWriterPW = isset($_REQUEST["BoardContentWriterPW"]) ? $_REQUEST["BoardContentWriterPW"] : "";
$BoardContentWriterNewPW = isset($_REQUEST["BoardContentWriterNewPW"]) ? $_REQUEST["BoardContentWriterNewPW"] : "";
$BoardContentNotice = isset($_REQUEST["BoardContentNotice"]) ? $_REQUEST["BoardContentNotice"] : "";
$BoardContentSubject = isset($_REQUEST["BoardContentSubject"]) ? $_REQUEST["BoardContentSubject"] : "";
$BoardContent = isset($_REQUEST["BoardContent"]) ? $_REQUEST["BoardContent"] : "";
$BoardContentTag = isset($_REQUEST["BoardContentTag"]) ? $_REQUEST["BoardContentTag"] : "";
$BoardContentSecret = isset($_REQUEST["BoardContentSecret"]) ? $_REQUEST["BoardContentSecret"] : "";

$BoardContentReplyID = isset($_REQUEST["BoardContentReplyID"]) ? $_REQUEST["BoardContentReplyID"] : "";
$BoardContentReplyOrder = isset($_REQUEST["BoardContentReplyOrder"]) ? $_REQUEST["BoardContentReplyOrder"] : "";
$BoardContentReplyDepth = isset($_REQUEST["BoardContentReplyDepth"]) ? $_REQUEST["BoardContentReplyDepth"] : "";

$BoardCategoryID = isset($_REQUEST["BoardCategoryID"]) ? $_REQUEST["BoardCategoryID"] : "";

$BoardContentPrice = isset($_REQUEST["BoardContentPrice"]) ? $_REQUEST["BoardContentPrice"] : "";
$BoardContentHelp = isset($_REQUEST["BoardContentHelp"]) ? $_REQUEST["BoardContentHelp"] : "";



if ($BoardContentWriterNewPW!=""){
	$BoardContentWriterPW = md5($BoardContentWriterNewPW);
}else{
	$BoardContentWriterPW = $BoardContentWriterPW;
} 


if ($BoardContentNotice=="1"){
	$BoardContentNotice = "1";
}else{
	$BoardContentNotice = "0";
}

if ($BoardContentSecret=="1"){
	$BoardContentSecret = "1";
}else{
	$BoardContentSecret = "0";
}


$Sql = "select BoardID from Boards where BoardCode=:BoardCode";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':BoardCode', $BoardCode);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$BoardID = $Row["BoardID"];


if ($NewData=="1"){
	
	if ($BoardContentReplyID==""){
		$Sql = "select ifnull(Max(BoardContentReplyID),0) AS BoardContentReplyID from BoardContents where BoardID=:BoardID";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':BoardID', $BoardID);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;

		$BoardContentReplyID = $Row["BoardContentReplyID"]+1;

		$BoardContentReplyOrder = 0;
		$BoardContentReplyDepth = 0;
		$ReplayAction = "0";
	}else{
		$Sql = "update BoardContents set BoardContentReplyOrder=BoardContentReplyOrder+1 where BoardContentReplyID=:BoardContentReplyID and BoardContentReplyOrder>:BoardContentReplyOrder";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':BoardContentReplyID', $BoardContentReplyID);
		$Stmt->bindParam(':BoardContentReplyOrder', $BoardContentReplyOrder);
		$Stmt->execute();
		$Stmt = null;

		$BoardContentReplyOrder = $BoardContentReplyOrder + 1;
		$BoardContentReplyDepth = $BoardContentReplyDepth + 1;
		$ReplayAction = "1";
	}


	$Sql = " insert into BoardContents ( ";
		$Sql .= " BoardCategoryID, ";
		$Sql .= " BoardID, ";
		$Sql .= " BoardContentMemberID, ";
		$Sql .= " BoardContentWriterName, ";
		$Sql .= " BoardContentWriterPW, ";
		$Sql .= " BoardContentNotice, ";
		$Sql .= " BoardContentSubject, ";
		$Sql .= " BoardContent, ";
		$Sql .= " BoardContentTag, ";
		$Sql .= " BoardContentSecret, ";
		$Sql .= " BoardContentRegDateTime, ";
		$Sql .= " BoardContentModiDateTime, ";
		$Sql .= " BoardContentReplyID, ";
		$Sql .= " BoardContentReplyOrder, ";
		$Sql .= " BoardContentReplyDepth";
	$Sql .= " ) values ( ";
		$Sql .= " :BoardCategoryID, "; 
		$Sql .= " :BoardID, ";
		$Sql .= " :BoardContentMemberID, ";
		$Sql .= " :BoardContentWriterName, ";
		$Sql .= " :BoardContentWriterPW, ";
		$Sql .= " :BoardContentNotice, ";
		$Sql .= " :BoardContentSubject, ";
		$Sql .= " :BoardContent, ";
		$Sql .= " :BoardContentTag, ";
		$Sql .= " :BoardContentSecret, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :BoardContentReplyID, ";
		$Sql .= " :BoardContentReplyOrder,"; 
		$Sql .= " :BoardContentReplyDepth ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BoardCategoryID', $BoardCategoryID);
	$Stmt->bindParam(':BoardID', $BoardID);
	$Stmt->bindParam(':BoardContentMemberID', $BoardContentMemberID);
	$Stmt->bindParam(':BoardContentWriterName', $BoardContentWriterName);
	$Stmt->bindParam(':BoardContentWriterPW', $BoardContentWriterPW);
	$Stmt->bindParam(':BoardContentNotice', $BoardContentNotice);
	$Stmt->bindParam(':BoardContentSubject', $BoardContentSubject);
	$Stmt->bindParam(':BoardContent', $BoardContent);
	$Stmt->bindParam(':BoardContentTag', $BoardContentTag);
	$Stmt->bindParam(':BoardContentSecret', $BoardContentSecret);
	$Stmt->bindParam(':BoardContentReplyID', $BoardContentReplyID);
	$Stmt->bindParam(':BoardContentReplyOrder', $BoardContentReplyOrder);
	$Stmt->bindParam(':BoardContentReplyDepth', $BoardContentReplyDepth);

	$Stmt->execute();
	$BoardContentID = $DbConn->lastInsertId();
	$Stmt = null;

}else{

	$Sql = " update BoardContents set ";
		$Sql .= " BoardCategoryID = :BoardCategoryID, ";
		$Sql .= " BoardContentWriterName = :BoardContentWriterName, ";
		$Sql .= " BoardContentWriterPW = :BoardContentWriterPW, ";
		$Sql .= " BoardContentNotice = :BoardContentNotice, ";
		$Sql .= " BoardContentSubject = :BoardContentSubject, ";
		$Sql .= " BoardContent = :BoardContent, ";
		$Sql .= " BoardContentTag = :BoardContentTag, ";
		$Sql .= " BoardContentSecret = :BoardContentSecret, ";
		$Sql .= " BoardContentModiDateTime = now() ";
	$Sql .= " where BoardContentID = :BoardContentID";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BoardCategoryID', $BoardCategoryID);
	$Stmt->bindParam(':BoardContentWriterName', $BoardContentWriterName);
	$Stmt->bindParam(':BoardContentWriterPW', $BoardContentWriterPW);
	$Stmt->bindParam(':BoardContentNotice', $BoardContentNotice);
	$Stmt->bindParam(':BoardContentSubject', $BoardContentSubject);
	$Stmt->bindParam(':BoardContent', $BoardContent);
	$Stmt->bindParam(':BoardContentTag', $BoardContentTag);
	$Stmt->bindParam(':BoardContentSecret', $BoardContentSecret);
	$Stmt->bindParam(':BoardContentID', $BoardContentID);

	$Stmt->execute();
	$Stmt = null;

}




if ($BoardFileCount>0) {

	//업로드 폴더
	$UploadPath = 'uploads/board_files/';

	for ($FileID=1;$FileID<=$BoardFileCount;$FileID++){

		$DelBoardFile = isset($_REQUEST["DelBoardFile".$FileID]) ? $_REQUEST["DelBoardFile".$FileID] : "";

		if ($DelBoardFile=="1"){
		
			$Sql = "delete from BoardContentFiles where BoardContentID=:BoardContentID and BoardFileNumber=:FileID";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':BoardContentID', $BoardContentID);
			$Stmt->bindParam(':FileID', $FileID);
			$Stmt->execute();
			$Stmt = null;
		
		}else{

			$TempFile = $_FILES['BoardFile'.$FileID]['tmp_name'];
			if ($TempFile){

				$MyFile = $_FILES['BoardFile'.$FileID]['name'];
				$MyFileSize = $_FILES['BoardFile'.$FileID]['size'];
				$MyFileMimeType = $_FILES['BoardFile'.$FileID]['type'];
				$MyFileName = (iconv('utf-8','euc-kr',$MyFile));
				$MyFileRealName = $MyFileName;

				$FileTypeCheck = explode('.',$MyFileName);
				$FileExp = $FileTypeCheck[count($FileTypeCheck)-1];
				$i = 0;

				$RealFileName = "";
				while($i < count($FileTypeCheck)-1){
					$RealFileName .= $FileTypeCheck[$i];
					$i++;
				}

				$RealFileName = md5($RealFileName);
				
				$ExistFlag = 0;
				if(file_exists($UploadPath.$RealFileName.'.'.$FileExp)){
					$i = 1;
					while($ExistFlag != 1){
						if(!file_exists($UploadPath.$RealFileName.'['.$i.'].'.$FileExp)){
							$ExistFlag = 1;
							$MyFileName = $RealFileName.'['.$i.'].'.$FileExp;
						}
						$i++;

					} 
				}else{
					$MyFileName = $RealFileName.'.'.$FileExp;
				}
				

				if ($FileExp=="php" || $FileExp=="php3" || $FileExp=="html"){
					$MyFileName = $MyFileName."_";
				}

				if(!@copy($TempFile, $UploadPath.$MyFileName)) { echo("error"); }

				$DbMyFileName = (iconv('euc-kr','utf-8',$MyFileName));
				$DbMyFileRealName = (iconv('euc-kr','utf-8',$MyFileRealName));
				$DbMyFileSize = $MyFileSize;
				$DbMyFileExtension = $FileExp;
				$DbMyFileMimeType = $MyFileMimeType;


				if ($NewData=="0"){	
					$Sql = "delete from BoardContentFiles where BoardContentID=:BoardContentID and BoardFileNumber=:FileID";
					$Stmt = $DbConn->prepare($Sql);
					$Stmt->bindParam(':BoardContentID', $BoardContentID);
					$Stmt->bindParam(':FileID', $FileID);
					$Stmt->execute();
					$Stmt = null;
				}

				$Sql = " insert into BoardContentFiles ( ";
					$Sql .= " BoardContentID, ";
					$Sql .= " BoardFileNumber, ";
					$Sql .= " BoardFileName, ";
					$Sql .= " BoardFileRealName, ";
					$Sql .= " BoardFileSize, ";
					$Sql .= " BoardFileExtension, ";
					$Sql .= " BoardFileMimeType ";
				$Sql .= " ) values ( ";
					$Sql .= " :BoardContentID, ";
					$Sql .= " :FileID, ";
					$Sql .= " :DbMyFileName, ";
					$Sql .= " :DbMyFileRealName, ";
					$Sql .= " :DbMyFileSize, ";
					$Sql .= " :DbMyFileExtension, ";
					$Sql .= " :DbMyFileMimeType ";
				$Sql .= " ) ";

				$Stmt = $DbConn->prepare($Sql);
				$Stmt->bindParam(':BoardContentID', $BoardContentID);
				$Stmt->bindParam(':FileID', $FileID);
				$Stmt->bindParam(':DbMyFileName', $DbMyFileName);
				$Stmt->bindParam(':DbMyFileRealName', $DbMyFileRealName);
				$Stmt->bindParam(':DbMyFileSize', $DbMyFileSize);
				$Stmt->bindParam(':DbMyFileExtension', $DbMyFileExtension);
				$Stmt->bindParam(':DbMyFileMimeType', $DbMyFileMimeType);
				$Stmt->execute();
				$Stmt = null;

			}

		}
	
	}

}







if ($err_num != 0){
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
</head>
<body>
<script>
alert("<?=$err_msg?>");
history.go(-1);
</script>

<?
include_once('./includes/common_analytics.php');
?>
</body>
</html>
<?php
}

include_once('./includes/dbclose.php');


if ($err_num == 0){
	

	if ($ListParam==""){
		$ListParam = "BoardCode=".$BoardCode;
	}

	if ($NewData=="1"){
		if ($ReplayAction=="1"){
			header("Location: board_list.php?$ListParam");
			//echo "1:board_list.php?$ListParam";
		}else{
			header("Location: board_list.php?BoardCode=$BoardCode"); 
			//echo "2:board_list.php?BoardCode=$BoardCode";
		}
	}else{
	header("Location: board_list.php?$ListParam");
	//echo "3:board_list.php?$ListParam";
	}


}
?>





