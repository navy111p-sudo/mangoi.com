<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');


$ErrNum = 0;
$ErrMsg = "";
$UpPath = "../uploads/csv_add_student/";


$CenterID = isset($_REQUEST["CenterID"]) ? $_REQUEST["CenterID"] : "";
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

    $ExistFlag = 0;
    if(file_exists($UpPath.$RealFileName.'.'.$FileType)){
        $i = 1;
        while($ExistFlag != 1){
            if(!file_exists($UpPath.$RealFileName.'['.$i.'].'.$FileType)){
                $ExistFlag = 1;
                $MyFileName = $RealFileName.'['.$i.'].'.$FileType;

            }
            $i++;

        } 
    }else{
        $MyFileName = $RealFileName.'.'.$FileType;
    }

    /*
    if ($FileType=="xls" || $FileType=="php3"){
        $MyFileName = $MyFileName."_";
    }
    */

    if(!@copy($TempFile, $UpPath.$MyFileName)) { echo("error"); }

    $DbMyFileName      = (iconv('euc-kr','utf-8',$MyFileName));
    $DbMyFileRealName  = (iconv('euc-kr','utf-8',$MyFileRealName));
    $DbMyFileSize      = $MyFileSize;
    $DbMyFileExtension = $FileType;
    $DbMyFileMimeType  = $MyFileMimeType;

}

$row = 1; 
$handle = fopen($UpPath.$MyFileName, "r"); 

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo $_SITE_TITLE_;?></title>
	<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
	<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
<?
include_once('./includes/common_meta_tag.php');
include_once('./inc_header.php');
?>

<?
include_once('./inc_common_list_css.php');
?>
<!-- ============== only this page css ============== -->

<!-- ============== only this page css ============== -->
<!-- ============== common.css ============== -->
<link rel="stylesheet" type="text/css" href="./css/common.css" />
<!-- ============== common.css ============== -->
</head>
<body>

<div id="page_content">
	<div id="page_content_inner">
	<h3 class="heading_b uk-margin-bottom" style="text-align:center;">업로드 데이터 현황</h3>
	<form name="RegForm" method="get">
		<input type="hidden" name="MyFileName" value=<?=$MyFileName?> />
		<input type="hidden" name="UpPath" value=<?=$UpPath?> />
		<input type="hidden" name="CenterID" value=<?=$CenterID?> />

		<div style="text-align:right; ">
			
		</div>
			<div class="md-card">
				<div class="md-card-content">
					<div class="uk-grid" data-uk-grid-margin>
						<div class="uk-width-1-1">
							<div class="uk-overflow-container">
								<span style="float:right; font-size:12px;">ID중복:<span style="color:red;">빨간색, </span>정상:<span style="color:blue;">파란색</span></span>
								<table class="uk-table uk-table-align-vertical" style="width:100%;">
									<thead>
										<tr style="background-color:gray">
											<th style="border: 1px solid black">학생명</th>
											<th style="border: 1px solid black">영문명</th>
											<th style="border: 1px solid black">아이디</th>
											<th style="border: 1px solid black">전화번호</th>
										</tr>
									</thead>
									<tbody>

									<?
									$CheckMatch = 0;
									$CheckData = 0;
									while (($data = fgetcsv($handle, 1000, ",")) !== false) { 
										$num = count($data); 
										$row++; 
											
										if($row>7) {
											$Sql = "select count(*) as Count from Members A where A.MemberLoginID='$data[2]'";
											$Stmt = $DbConn->prepare($Sql);
											$Stmt->execute();
											$Row = $Stmt->fetch();
											$Count = $Row["Count"];
											$Stmt = null;
											if($data[0]=="" || $data[1]=="" || $data[2]=="" || $data[3]=="" || $data[4]=="" || $data[5]=="" || $data[6]=="" || $data[7]=="") {
												$CheckData = 1;
											}

											if($Count!=0) {
												$CheckMatch = 1; // 중복되는 값이 있다면 업데이트
											}


											if (mb_detect_encoding($data[0], "EUC-KR, UTF-8, ASCII")=="EUC-KR"){
												$data[0] = iconv("EUC-KR", "UTF-8", $data[0]);
											}
											if (mb_detect_encoding($data[1], "EUC-KR, UTF-8, ASCII")=="EUC-KR"){
												$data[1] = iconv("EUC-KR", "UTF-8", $data[1]);
											}
											if (mb_detect_encoding($data[2], "EUC-KR, UTF-8, ASCII")=="EUC-KR"){
												$data[2] = iconv("EUC-KR", "UTF-8", $data[2]);
											}
											if (mb_detect_encoding($data[4], "EUC-KR, UTF-8, ASCII")=="EUC-KR"){
												$data[4] = iconv("EUC-KR", "UTF-8", $data[4]);
											}
											?>

											<tr>
												<td style="border: 1px solid black"><?=$data[0]?></td>
												<td style="border: 1px solid black"><?=$data[1]?></td>
												<td style="border: 1px solid black; color: <?if($Count!=0){?>red<?}else{?>blue<?}?> "><?=$data[2]?></td>
												<td style="border: 1px solid black"><?=$data[4]?></td>
											</tr>
										<?
										} 
									} 
									?>
									</tbody>
								</table>
								<span style="float:right; color:red; font-size:12px; display:<?if($CheckMatch==0){?>none<?}?>">중복이 일어나지않게 변경 후 재시도 부탁드립니다.</span> 
							</div>
						</div>
					</div>
				</div>
			</div>
			<div style="margin-top: 20px; text-align:center;">
				<?if($CheckMatch==0) {?> 
					<a style="margin:0 auto;display:inline-block; background-color:#556BAC; color:#ffffff; text-align:center; width:110px; width:100px; line-height:32px; font-size:14px;" href="javascript:AddAllStudent(<?=$CheckData?>);">일괄등록</a>
				<?}?>
				<a style="margin:0 auto;display:inline-block; background-color:#556BAC; color:#ffffff; text-align:center; width:110px; width:100px; line-height:32px; font-size:14px;" href="javascript:CloseThisWinodw();">닫기</a>
			</div>
		</div>
	</form>
	</div>
</div>

<?
fclose($handle); 
?>

<script>
function CloseThisWinodw() {
	parent.$.fn.colorbox.close();
}

function AddAllStudent(CheckData) {
	if(CheckData==1) {
		alert("필수데이터가 비어있습니다.\n확인 후 재업로드 해주시기 바랍니다.");
	}else{
		document.RegForm.action = "student_list_csv_popup_upload_action.php";
		document.RegForm.submit();
	}
}
</script>
</body>
</html>


<?
include_once('../includes/dbclose.php');
?>

