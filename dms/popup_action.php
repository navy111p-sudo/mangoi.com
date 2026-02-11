<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');

$err_num = 0;
$err_msg = "";

$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$ListParam = str_replace("^^", "&", $ListParam);
$NewData = isset($_REQUEST["NewData"]) ? $_REQUEST["NewData"] : "";

$PopupID = isset($_REQUEST["PopupID"]) ? $_REQUEST["PopupID"] : "";
$PopupName = isset($_REQUEST["PopupName"]) ? $_REQUEST["PopupName"] : "";
$PopupTitle = isset($_REQUEST["PopupTitle"]) ? $_REQUEST["PopupTitle"] : "";
$PopupType = isset($_REQUEST["PopupType"]) ? $_REQUEST["PopupType"] : "";
$PopupStartDateNum = isset($_REQUEST["PopupStartDateNum"]) ? $_REQUEST["PopupStartDateNum"] : "";
$PopupEndDateNum = isset($_REQUEST["PopupEndDateNum"]) ? $_REQUEST["PopupEndDateNum"] : "";
$PopupWidth = isset($_REQUEST["PopupWidth"]) ? $_REQUEST["PopupWidth"] : "";
$PopupHeight = isset($_REQUEST["PopupHeight"]) ? $_REQUEST["PopupHeight"] : "";
$PopupTop = isset($_REQUEST["PopupTop"]) ? $_REQUEST["PopupTop"] : "";
$PopupLeft = isset($_REQUEST["PopupLeft"]) ? $_REQUEST["PopupLeft"] : "";
$PopupContent = isset($_REQUEST["PopupContent"]) ? $_REQUEST["PopupContent"] : "";
$PopupState = isset($_REQUEST["PopupState"]) ? $_REQUEST["PopupState"] : "";

$PopupContent = convertRequest($PopupContent);


//== 기존 사운드, 이미지 이름 가져오기 =====================
if ($PopupID!=""){
	$Sql = "select * from Popups where PopupID=:PopupID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':PopupID', $PopupID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$DbPopupImage = $Row["PopupImage"];
}
//== 기존 사운드, 이미지 이름 가져오기 =====================


$PopupImage = $DbPopupImage;

//업로드 폴더
$AddSql = "";
$UploadPath = '../uploads/popup_images/';

//====== 1번 이미지 =======================================
$TempFile = $_FILES['PopupImage']['tmp_name'];
if ($TempFile){

	$MyFile = $_FILES['PopupImage']['name'];
	$MyFileName = (iconv('utf-8','euc-kr',$MyFile));
	
	$FileTypeCheck = explode('.',$MyFileName);
	$FileExp = $FileTypeCheck[count($FileTypeCheck)-1];
	$i = 0;

	$RealFileName = "";
	while($i < count($FileTypeCheck)-1){
		$RealFileName .= $FileTypeCheck[$i];
		$i++;
	}
	
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
	}

	if ($FileExp=="php" || $FileExp=="php3" || $FileExp=="html"){
		$MyFileName = $MyFileName."_";
	}


	if(!@copy($TempFile, $UploadPath.$MyFileName)) { echo("error"); }

	$DbMyFileName = (iconv('euc-kr','utf-8',$MyFileName));
	$PopupImage = $DbMyFileName;
}


if ($DelPopupImage=="1"){
	$PopupImage = "";
}
//====== 1번 이미지 =======================================


if ($NewData=="1"){

	$Sql = " insert into Popups ( ";
		$Sql .= " PopupName, ";
		$Sql .= " PopupTitle, ";
		$Sql .= " PopupType, ";
		$Sql .= " PopupStartDateNum, ";
		$Sql .= " PopupEndDateNum, ";
		$Sql .= " PopupWidth, ";
		$Sql .= " PopupHeight, ";
		$Sql .= " PopupTop, ";
		$Sql .= " PopupLeft, ";
		$Sql .= " PopupImage, ";
		$Sql .= " PopupContent, ";
		$Sql .= " PopupRegDateTime, ";
		$Sql .= " PopupState ";
	$Sql .= " ) values ( ";
		$Sql .= " :PopupName, ";
		$Sql .= " :PopupTitle, ";
		$Sql .= " :PopupType, ";
		$Sql .= " :PopupStartDateNum, ";
		$Sql .= " :PopupEndDateNum, ";
		$Sql .= " :PopupWidth, ";
		$Sql .= " :PopupHeight, ";
		$Sql .= " :PopupTop, ";
		$Sql .= " :PopupLeft, ";
		$Sql .= " :PopupImage, ";
		$Sql .= " :PopupContent, ";
		$Sql .= " now(), ";
		$Sql .= " :PopupState ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':PopupName', $PopupName);
	$Stmt->bindParam(':PopupTitle', $PopupTitle);
	$Stmt->bindParam(':PopupType', $PopupType);
	$Stmt->bindParam(':PopupStartDateNum', $PopupStartDateNum);
	$Stmt->bindParam(':PopupEndDateNum', $PopupEndDateNum);
	$Stmt->bindParam(':PopupWidth', $PopupWidth);
	$Stmt->bindParam(':PopupHeight', $PopupHeight);
	$Stmt->bindParam(':PopupTop', $PopupTop);
	$Stmt->bindParam(':PopupLeft', $PopupLeft);
	$Stmt->bindParam(':PopupImage', $PopupImage);
	$Stmt->bindParam(':PopupContent', $PopupContent);
	$Stmt->bindParam(':PopupState', $PopupState);
	$Stmt->execute();
	$Stmt = null;

}else{

	$Sql = " update Popups set ";
		$Sql .= " PopupName = :PopupName, ";
		$Sql .= " PopupTitle = :PopupTitle, ";
		$Sql .= " PopupType = :PopupType, ";
		$Sql .= " PopupStartDateNum = :PopupStartDateNum, ";
		$Sql .= " PopupEndDateNum = :PopupEndDateNum, ";
		$Sql .= " PopupWidth = :PopupWidth, ";
		$Sql .= " PopupHeight = :PopupHeight, ";
		$Sql .= " PopupTop = :PopupTop, ";
		$Sql .= " PopupLeft = :PopupLeft, ";
		$Sql .= " PopupImage = :PopupImage, ";
		$Sql .= " PopupContent = :PopupContent, ";
		$Sql .= " PopupState = :PopupState ";
	$Sql .= " where PopupID = :PopupID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':PopupName', $PopupName);
	$Stmt->bindParam(':PopupTitle', $PopupTitle);
	$Stmt->bindParam(':PopupType', $PopupType);
	$Stmt->bindParam(':PopupStartDateNum', $PopupStartDateNum);
	$Stmt->bindParam(':PopupEndDateNum', $PopupEndDateNum);
	$Stmt->bindParam(':PopupWidth', $PopupWidth);
	$Stmt->bindParam(':PopupHeight', $PopupHeight);
	$Stmt->bindParam(':PopupTop', $PopupTop);
	$Stmt->bindParam(':PopupLeft', $PopupLeft);
	$Stmt->bindParam(':PopupImage', $PopupImage);
	$Stmt->bindParam(':PopupContent', $PopupContent);
	$Stmt->bindParam(':PopupState', $PopupState);
	$Stmt->bindParam(':PopupID', $PopupID);
	$Stmt->execute();
	$Stmt = null;

}


if ($err_num != 0){
	include_once('./_header.php');
?>
<body>
<script>
alert("<?=$err_msg?>");
history.go(-1);
</script>
</body>
<?php
	include_once('./_footer.php');
}

include_once('../includes/dbclose.php');


if ($err_num == 0){
	header("Location: popup_list.php?$ListParam"); 
	exit;
}
?>


