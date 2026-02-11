<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$ListParam = str_replace("^^", "&", $ListParam);

$ProductID = isset($_REQUEST["ProductID"]) ? $_REQUEST["ProductID"] : "";
$ProductSellerBookID = isset($_REQUEST["ProductSellerBookID"]) ? $_REQUEST["ProductSellerBookID"] : "";
$ProductISBN = isset($_REQUEST["ProductISBN"]) ? $_REQUEST["ProductISBN"] : "";
$ProductCategoryID = isset($_REQUEST["ProductCategoryID"]) ? $_REQUEST["ProductCategoryID"] : "";
$ProductName = isset($_REQUEST["ProductName"]) ? $_REQUEST["ProductName"] : "";
$ProductMemo = isset($_REQUEST["ProductMemo"]) ? $_REQUEST["ProductMemo"] : "";
$ProductCostPrice = isset($_REQUEST["ProductCostPrice"]) ? $_REQUEST["ProductCostPrice"] : "";//업데이트 안함
$ProductPrice = isset($_REQUEST["ProductPrice"]) ? $_REQUEST["ProductPrice"] : "";
$ProductState = isset($_REQUEST["ProductState"]) ? $_REQUEST["ProductState"] : "";
$ProductView = isset($_REQUEST["ProductView"]) ? $_REQUEST["ProductView"] : "";

$ProductViewPrice = $ProductPrice;

if ($ProductView!="1"){
	$ProductView = 0;
}

if ($ProductState!="1"){
	$ProductState = 2;
}


//================================== 파일 업로드 ============================
$Path = "../uploads/product_images/";
$UploadDir = str_replace(basename(__FILE__), '', realpath($Path)) . "/";


$TempFile = $_FILES['UpFile']['tmp_name'];
if ($TempFile){

	$MyFile         = $_FILES['UpFile']['name'];
	$MyFileSize     = $_FILES['UpFile']['size'];
	$MyFileMimeType = $_FILES['UpFile']['type'];
	$MyFileName     = (iconv('utf-8','euc-kr',$MyFile));
	$MyFileRealName = $MyFileName;

	$FileTypeCheck = explode('.',$MyFileName);
	$FileType       = $FileTypeCheck[count($FileTypeCheck)-1];
	$i = 0;
	
	$RealFileName = "";
	while($i < count($FileTypeCheck)-1){
		$RealFileName .= $FileTypeCheck[$i];
		$i++;
	}
	
	$RealFileName = md5($RealFileName);
	$RealFileNameResize = $RealFileName."_rs";
	$RealFileNameCrop = $RealFileName."_cp";

	$ExistFlag = 0;
	if(file_exists($Path.$RealFileName.'.'.$FileType)){
		$i = 1;
		while($ExistFlag != 1){
			if(!file_exists($Path.$RealFileName.'['.$i.'].'.$FileType)){
				$ExistFlag = 1;
				$MyFileName = $RealFileName.'['.$i.'].'.$FileType;
				$MyFileNameResize = $RealFileNameResize.'['.$i.'].'.$FileType;
				$MyFileNameCrop = $RealFileNameCrop.'['.$i.'].'.$FileType;
			}
			$i++;

		} 
	}else{
		$MyFileName = $RealFileName.'.'.$FileType;
		$MyFileNameResize = $RealFileNameResize.'.'.$FileType;
		$MyFileNameCrop = $RealFileNameCrop.'.'.$FileType;
	}

	if ($FileType=="php" || $FileType=="php3" || $FileType=="html"){
		$MyFileName = $MyFileName."_";
	}

	if(!@copy($TempFile, $Path.$MyFileName)) { echo("error"); }


	//사이즈 줄이기 and 자르기 ==============
	
	
	/*
	$ImgSize = getimagesize($UploadDir."".$MyFileName);
	$ImgWidth = $ImgSize[0];
	$ImgHeight = $ImgSize[1];

	$ffmpeg = "ffmpeg";

	
	//방법 1) 큰쪽을 상한 사이즈에 맞추기
	if ($ImgWidth>=$ImgHeight && $ImgWidth>1080){
		
		$ffmpegcmd = $ffmpeg." -i ".$UploadDir."".$MyFileName." -vf scale=1080:-1 ".$UploadDir."".$MyFileNameResize;
		exec($ffmpegcmd);
		unlink($UploadDir."".$MyFileName);

	}else if ($ImgWidth<$ImgHeight && $ImgHeight>1080){
		echo "bbbb";
		$ffmpegcmd = $ffmpeg." -i ".$UploadDir."".$MyFileName." -vf scale=-1:1080 ".$UploadDir."".$MyFileNameResize;
		unlink($UploadDir."".$MyFileName);

	}else{
		$MyFileNameResize = $MyFileName;
	}
	
	$MyFileName = $MyFileNameResize;



	//방법 2)정사각형 이미지 만들기

	if ($ImgWidth>=$ImgHeight && $ImgHeight>1080){

		$ffmpegcmd = $ffmpeg." -i ".$UploadDir."".$MyFileName." -vf scale=-1:1080 ".$UploadDir."".$MyFileNameResize;
		exec($ffmpegcmd);
		unlink($UploadDir."".$MyFileName);

	}else if ($ImgWidth<$ImgHeight && $ImgWidth>1080){
		
		$ffmpegcmd = $ffmpeg." -i ".$UploadDir."".$MyFileName." -vf scale=1080:-1 ".$UploadDir."".$MyFileNameResize;
		exec($ffmpegcmd);
		unlink($UploadDir."".$MyFileName);

	}else{
		$MyFileNameResize = $MyFileName;
	}
	
	$MyFileName = $MyFileNameResize;

	if ($ImgWidth>=1080 && $ImgHeight>=1080){

		$ffmpegcmd = $ffmpeg." -i ".$UploadDir."".$MyFileName." -vf crop=1080:1080 ".$UploadDir."".$MyFileNameCrop;
		exec($ffmpegcmd);
		unlink($UploadDir."".$MyFileName);

	}else{
		$MyFileNameCrop = $MyFileName;
	}

	$MyFileName = $MyFileNameCrop;
	*/


	//사이즈 줄이기 and 자르기 ==============




	$DbMyFileName      = (iconv('euc-kr','utf-8',$MyFileName));
	$DbMyFileRealName  = (iconv('euc-kr','utf-8',$MyFileRealName));
	$DbMyFileSize      = $MyFileSize;
	$DbMyFileExtension = $FileType;
	$DbMyFileMimeType  = $MyFileMimeType;

}
//================================== 파일 업로드 ============================




if ($ProductID==""){

	$Sql = "select ifnull(Max(ProductOrder),0) as ProductOrder from Products";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$ProductOrder = $Row["ProductOrder"]+1;

	$Sql = " insert into Products ( ";
		$Sql .= " ProductSellerBookID, ";
		$Sql .= " ProductISBN, ";
		$Sql .= " ProductCategoryID, ";
		$Sql .= " ProductName, ";
		$Sql .= " ProductMemo, ";
		if ($TempFile){
			$Sql .= " ProductImageFileName, ";
			$Sql .= " ProductImageFileRealName, ";
		}
		$Sql .= " ProductViewPrice, ";
		$Sql .= " ProductPrice, ";
		$Sql .= " ProductRegDateTime, ";
		$Sql .= " ProductModiDateTime, ";
		$Sql .= " ProductState, ";
		$Sql .= " ProductView, ";
		$Sql .= " ProductOrder ";
	$Sql .= " ) values ( ";
		$Sql .= " :ProductSellerBookID, ";
		$Sql .= " :ProductISBN, ";
		$Sql .= " :ProductCategoryID, ";
		$Sql .= " :ProductName, ";
		$Sql .= " :ProductMemo, ";
		if ($TempFile){
			$Sql .= " :ProductImageFileName, ";
			$Sql .= " :ProductImageFileRealName, ";
		}
		$Sql .= " :ProductViewPrice, ";
		$Sql .= " :ProductPrice, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :ProductState, ";
		$Sql .= " :ProductView, ";
		$Sql .= " :ProductOrder ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ProductSellerBookID', $ProductSellerBookID);
	$Stmt->bindParam(':ProductISBN', $ProductISBN);
	$Stmt->bindParam(':ProductCategoryID', $ProductCategoryID);
	$Stmt->bindParam(':ProductName', $ProductName);
	$Stmt->bindParam(':ProductMemo', $ProductMemo);
	if ($TempFile){
		$Stmt->bindParam(':ProductImageFileName', $DbMyFileName);
		$Stmt->bindParam(':ProductImageFileRealName', $DbMyFileRealName);
	}
	$Stmt->bindParam(':ProductViewPrice', $ProductViewPrice);
	$Stmt->bindParam(':ProductPrice', $ProductPrice);
	$Stmt->bindParam(':ProductState', $ProductState);
	$Stmt->bindParam(':ProductView', $ProductView);
	$Stmt->bindParam(':ProductOrder', $ProductOrder);
	$Stmt->execute();
	$Stmt = null;

}else{

	$Sql = " update Products set ";
		$Sql .= " ProductSellerBookID = :ProductSellerBookID, ";
		$Sql .= " ProductISBN = :ProductISBN, ";
		$Sql .= " ProductCategoryID = :ProductCategoryID, ";
		$Sql .= " ProductName = :ProductName, ";
		$Sql .= " ProductMemo = :ProductMemo, ";
		if ($TempFile){
			$Sql .= " ProductImageFileName = :ProductImageFileName, ";
			$Sql .= " ProductImageFileRealName = :ProductImageFileRealName, ";
		}
		$Sql .= " ProductViewPrice = :ProductViewPrice, ";
		$Sql .= " ProductPrice = :ProductPrice, ";
		$Sql .= " ProductState = :ProductState, ";
		$Sql .= " ProductView = :ProductView, ";
		$Sql .= " ProductModiDateTime = now() ";
	$Sql .= " where ProductID = :ProductID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ProductSellerBookID', $ProductSellerBookID);
	$Stmt->bindParam(':ProductISBN', $ProductISBN);
	$Stmt->bindParam(':ProductCategoryID', $ProductCategoryID);
	$Stmt->bindParam(':ProductName', $ProductName);
	$Stmt->bindParam(':ProductMemo', $ProductMemo);
	if ($TempFile){
		$Stmt->bindParam(':ProductImageFileName', $DbMyFileName);
		$Stmt->bindParam(':ProductImageFileRealName', $DbMyFileRealName);
	}
	$Stmt->bindParam(':ProductViewPrice', $ProductViewPrice);
	$Stmt->bindParam(':ProductPrice', $ProductPrice);
	$Stmt->bindParam(':ProductState', $ProductState);
	$Stmt->bindParam(':ProductView', $ProductView);
	$Stmt->bindParam(':ProductID', $ProductID);
	$Stmt->execute();
	$Stmt = null;

}


if ($err_num != 0){
	include_once('./inc_header.php');
?>
<body>
<script>
alert("<?=$err_msg?>");
history.go(-1);
</script>
<?php
	include_once('./inc_footer.php'); 
?>
</body>
</html>
<?
}

include_once('../includes/dbclose.php');


if ($err_num == 0){
	header("Location: product_list.php?$ListParam"); 
	exit;
}
?>


