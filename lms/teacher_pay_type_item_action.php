<?php error_reporting( E_ALL ); ini_set( "display_errors", 1 ); ?> 
<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$ListParam = str_replace("^^", "&", $ListParam);

$TeacherPayTypeItemID = isset($_REQUEST["TeacherPayTypeItemID"]) ? $_REQUEST["TeacherPayTypeItemID"] : "";
$TeacherPayTypeItemTitle = isset($_REQUEST["TeacherPayTypeItemTitle"]) ? $_REQUEST["TeacherPayTypeItemTitle"] : "";
$TeacherPayTypeItemContent = isset($_REQUEST["TeacherPayTypeItemContent"]) ? $_REQUEST["TeacherPayTypeItemContent"] : "";
$TeacherPayTypeItemCenterPriceX = isset($_REQUEST["TeacherPayTypeItemCenterPriceX"]) ? $_REQUEST["TeacherPayTypeItemCenterPriceX"] : "";
$TeacherPayTypeItemState = isset($_REQUEST["TeacherPayTypeItemState"]) ? $_REQUEST["TeacherPayTypeItemState"] : "";
$TeacherPayTypeItemView = isset($_REQUEST["TeacherPayTypeItemView"]) ? $_REQUEST["TeacherPayTypeItemView"] : "";

if ($TeacherPayTypeItemView!="1"){
	$TeacherPayTypeItemView = 0;
}

if ($TeacherPayTypeItemState!="1"){
	$TeacherPayTypeItemState = 2;
}



// 국기 파일 업로드하기
$target_file = "";
if ($_FILES['NationalFlagFile']['size'] > 0) {

    $target_dir = "../images/";
	$target_file = $target_dir. $_FILES["NationalFlagFile"]["name"];
	$target_filename = $_FILES["NationalFlagFile"]["name"];
	echo $target_file;
	$uploadOk = 1;
	$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
	
	// Check if file already exists
	if (file_exists($target_file)) {
		echo "Sorry, file already exists.";
		$uploadOk = 0;
	}
	// Check file size
	if ($_FILES["NationalFlagFile"]["size"] > 10000000) {
		echo "Sorry, your file is too large.";
		$uploadOk = 0;
	}
	// Allow certain file formats
	if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
	&& $imageFileType != "gif" ) {
		echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
		$uploadOk = 0;
	}
	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0) {
		echo "Sorry, your file was not uploaded.";
	// if everything is ok, try to upload file
	} else {
		move_uploaded_file($_FILES["NationalFlagFile"]["tmp_name"], $target_file);
	}
	
}


if ($TeacherPayTypeItemID==""){

	$Sql = "SELECT ifnull(Max(TeacherPayTypeItemOrder),0) as TeacherPayTypeItemOrder from TeacherPayTypeItems";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$TeacherPayTypeItemOrder = $Row["TeacherPayTypeItemOrder"]+1;

	$Sql = "INSERT into TeacherPayTypeItems ( ";
		$Sql .= " TeacherPayTypeItemTitle, ";
		$Sql .= " TeacherPayTypeItemTitle2, ";
		$Sql .= " TeacherPayTypeItemContent, ";
		$Sql .= " TeacherPayTypeItemCenterPriceX, ";
		$Sql .= " TeacherPayTypeItemRegDateTime, ";
		$Sql .= " TeacherPayTypeItemModiDateTime, ";
		$Sql .= " TeacherPayTypeItemState, ";
		$Sql .= " TeacherPayTypeItemView, ";
		$Sql .= " TeacherPayTypeItemOrder, ";
		$Sql .= " NationalFlagFile ";
	$Sql .= " ) values ( ";
		$Sql .= " :TeacherPayTypeItemTitle, ";
		$Sql .= " :TeacherPayTypeItemTitle2, ";
		$Sql .= " :TeacherPayTypeItemContent, ";
		$Sql .= " :TeacherPayTypeItemCenterPriceX, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " :TeacherPayTypeItemState, ";
		$Sql .= " :TeacherPayTypeItemView, ";
		$Sql .= " :TeacherPayTypeItemOrder, ";
		$Sql .= " :NationalFlagFile ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TeacherPayTypeItemTitle', $TeacherPayTypeItemTitle);
	$Stmt->bindParam(':TeacherPayTypeItemTitle2', $TeacherPayTypeItemTitle);
	$Stmt->bindParam(':TeacherPayTypeItemContent', $TeacherPayTypeItemContent);
	$Stmt->bindParam(':TeacherPayTypeItemCenterPriceX', $TeacherPayTypeItemCenterPriceX);
	$Stmt->bindParam(':TeacherPayTypeItemState', $TeacherPayTypeItemState);
	$Stmt->bindParam(':TeacherPayTypeItemView', $TeacherPayTypeItemView);
	$Stmt->bindParam(':TeacherPayTypeItemOrder', $TeacherPayTypeItemOrder);
	$Stmt->bindParam(':NationalFlagFile', $target_filename);
	$Stmt->execute();
	$TeacherPayTypeItemID = $DbConn->lastInsertId();
	$Stmt = null;


}else{

	$Sql = "UPDATE TeacherPayTypeItems set ";
		$Sql .= " TeacherPayTypeItemTitle = :TeacherPayTypeItemTitle, ";
		$Sql .= " TeacherPayTypeItemTitle2 = :TeacherPayTypeItemTitle, ";
		$Sql .= " TeacherPayTypeItemContent = :TeacherPayTypeItemContent, ";
		$Sql .= " TeacherPayTypeItemCenterPriceX = :TeacherPayTypeItemCenterPriceX, ";
		$Sql .= " TeacherPayTypeItemModiDateTime = now(), ";
		$Sql .= " TeacherPayTypeItemState = :TeacherPayTypeItemState, ";
		
		if ($target_file != "") {	// 업로드한 파일이 있을때만 적용한다. 
			$Sql .= " NationalFlagFile = :NationalFlagFile, ";
		}
		$Sql .= " TeacherPayTypeItemView = :TeacherPayTypeItemView ";
	$Sql .= " where TeacherPayTypeItemID = :TeacherPayTypeItemID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TeacherPayTypeItemTitle', $TeacherPayTypeItemTitle);
	$Stmt->bindParam(':TeacherPayTypeItemTitle2', $TeacherPayTypeItemTitle);
	$Stmt->bindParam(':TeacherPayTypeItemContent', $TeacherPayTypeItemContent);
	$Stmt->bindParam(':TeacherPayTypeItemCenterPriceX', $TeacherPayTypeItemCenterPriceX);
	$Stmt->bindParam(':TeacherPayTypeItemState', $TeacherPayTypeItemState);
	$Stmt->bindParam(':TeacherPayTypeItemView', $TeacherPayTypeItemView);
	$Stmt->bindParam(':TeacherPayTypeItemID', $TeacherPayTypeItemID);
	if ($target_file != "") {
		$Stmt->bindParam(':NationalFlagFile', $target_filename);
	}
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
?>
<script>
parent.$.fn.colorbox.close();
</script>
<?
}
?>


