<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');


//================================================================
// 7일 이하동안 유지되는 슬랏 중에 그 기간내에 수업이 없는 슬랏을 삭제 처리
$Sql = "SELECT 
					distinct ClassOrderSlotID 
				FROM View_ClassOrderSlotDelTargets 
				WHERE 
					ClassOrderSlotID NOT in (SELECT ClassOrderSlotID FROM View_ClassOrderSlotDelTargets WHERE ClassOrderSlotWeek=StudyWeek)";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

while($Row = $Stmt->fetch()) {

	$DelClassOrderSlotID = $Row["ClassOrderSlotID"];

	$Sql2 = "
		update ClassOrderSlots set 
			ClassOrderSlotState=0,
			DelAdminUnder7Day=1,
			DelAdminUnder7DayDateTime=now(),
			ClassOrderSlotDateModiDateTime=now()
		where ClassOrderSlotID=$DelClassOrderSlotID
	";
	$Stmt2 = $DbConn->prepare($Sql2);
	$Stmt2->execute();
	$Stmt2 = null;

}
$Stmt = null;				
//================================================================



$ErrNum = 0;
$ErrMsg = "";
$UpPath = "../uploads/excel_add_student/";


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

$UploadFileName = $UpPath.$MyFileName;
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
	<h3 class="heading_b uk-margin-bottom" style="text-align:center;margin-top:-30px;"><?=$업로드_데이터현황[$LangID]?></h3>
	<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="UploadFileName" value="<?=$UploadFileName?>">
		<div style="text-align:right; ">
			
		</div>
			<div class="md-card">
				<div class="md-card-content">
					<div class="uk-grid" data-uk-grid-margin>
						<div class="uk-width-1-1">
							<div class="uk-overflow-container">
								<span style="float:right; font-size:12px;"><span style="color:red;">■</span><?=$존재하지_않는_아이디_또는_잘못된_데이터[$LangID]?></span>

<!-- ===========================================  table ================================================= -->	
<table class="uk-table uk-table-align-vertical" style="width:100%;margin-top:20px;">
	<thead>
		<tr style="background-color:gray">
			<th style="border: 1px solid black"><?=$대리점[$LangID]?>/<?=$학생명[$LangID]?>/<?=$아이디[$LangID]?></th>
			<th style="border: 1px solid black"><?=$수업구분[$LangID]?></th>
			<th style="border: 1px solid black"><?=$테스트레벨[$LangID]?></th>
			<th style="border: 1px solid black"><?=$수업시간[$LangID]?></th>
			<th style="border: 1px solid black"><?=$시작일[$LangID]?></th>
			<th style="border: 1px solid black"><?=$체험[$LangID]?>/<?=$레벨시간[$LangID]?></th>
			<th style="border: 1px solid black"><?=$월[$LangID]?></th>
			<th style="border: 1px solid black"><?=$화[$LangID]?></th>
			<th style="border: 1px solid black"><?=$수[$LangID]?></th>
			<th style="border: 1px solid black"><?=$목[$LangID]?></th>
			<th style="border: 1px solid black"><?=$금[$LangID]?></th>
		</tr>
	</thead>
	<tbody>

	<?
	$LinkAdminLevelID = $_LINK_ADMIN_LEVEL_ID_;
	$EduCenterID = 1;
	$ArrWeekName = explode("|", "일요일|월요일|화요일|수요일|목요일|금요일|토요일");


	include_once("../PHPExcel-1.8/Classes/PHPExcel.php");

	libxml_use_internal_errors(true); // 일반적인 경고문을 안보여주는...https://codeday.me/ko/qa/20190325/149807.html also stackoverflow too,
	$objPHPExcel = new PHPExcel();
	$filename = $UpPath.$MyFileName; // 읽어들일 엑셀 파일의 경로와 파일명을 지정한다.

	try {

		// 업로드 된 엑셀 형식에 맞는 Reader객체를 만든다.
		$objReader = PHPExcel_IOFactory::createReaderForFile($filename);
		// 읽기전용으로 설정
		$objReader->setReadDataOnly(true);
		// 엑셀파일을 읽는다
		$objExcel = $objReader->load($filename);
		// 첫번째 시트를 선택
		$objExcel->setActiveSheetIndex(0);
		$objWorksheet = $objExcel->getActiveSheet();
		$rowIterator = $objWorksheet->getRowIterator();

		foreach ($rowIterator as $row) { // 모든 행에 대해서
			$cellIterator = $row->getCellIterator();
			$cellIterator->setIterateOnlyExistingCells(false); 
		}

		$maxRow = $objWorksheet->getHighestRow();


		$TotalExcelListNum = 0;
		$AbleExcelListNum = 0;
		for ($i = 11 ; $i <= $maxRow ; $i++) {
			
			
			include("./class_order_bulk_excel_check_inc.php");
			
			$TotalExcelListNum ++;
			$AllData = 0;
			$ClassOrderLeveltestApplyLevel = "";
			if ($DataOk[1]==1 && $DataOk[2]==1 && $DataOk[3]==1 && $DataOk[4]==1 && $DataOk[5]==1 && $DataOk[6]==1 && $DataOk[7]==1 && $DataOk[8]==1 && $DataOk[9]==1 && $DataOk[10]==1){
				
				$AbleExcelListNum++;
				$AllData = 1;
				

				//1: 강좌 2:레벨테스트 3:체험수업
				
				if ($TempClassType=="정규"){
					$ClassProductID = 1;
					$ClassOrderLeveltestApplyLevel = "";
				}else if ($TempClassType=="레벨"){
					$ClassProductID = 2;
					$ClassOrderLeveltestApplyLevel = "LEVEL ".$TempClassOrderLeveltestApplyLevel;
				}else if ($TempClassType=="체험"){
					$ClassProductID = 3;
					$ClassOrderLeveltestApplyLevel = "";
				}



				$TempClassStartDateWeekDay = date("w", strtotime($TempClassStartDate));
				$date=date_create($TempClassStartDate); 
				date_add($date, date_interval_create_from_date_string("-".$TempClassStartDateWeekDay." days")); 
				$TempClassStartDateWeekStartDate = date_format($date, "Y-m-d");//선택한날 기준으로 이전 일요일
			}

			?>
				<tr>
					<td style="border: 1px solid black;text-align:left;padding-left:20px;line-height:1.5;width:350px;">
						<span style="color:<?if($DataOk[1]==0){?>red<?}?>;"><?=$StrMemberLoginID?></span>
					</td>
					<td style="border: 1px solid black;text-align:center;">
						<span style="color:<?if($DataOk[2]==0){?>red<?}?>;"><?=$StrClassType?></span>
					</td>
					<td style="border: 1px solid black;text-align:center;">
						<span><?=$ClassOrderLeveltestApplyLevel?></span>
					</td>
					<td style="border: 1px solid black;text-align:center;">
						<span style="color:<?if($DataOk[3]==0){?>red<?}?>;"><?=$StrClassOrderTimeTypeID?></span>
					</td>
					<td style="border: 1px solid black;text-align:center;">
						<span style="color:<?if($DataOk[4]==0){?>red<?}?>;"><?=$StrClassStartDate?></span>
					</td>
					<td style="border: 1px solid black;text-align:center;width:120px;<?if ($StrStartTimeLevel!=""){?>background-color:#E1E6F0;<?}?>">
						<span style="color:<?if($DataOk[5]==0){?>red<?}?>;"><?=$StrStartTimeLevel?></span>
					</td>
					<td style="border: 1px solid black;text-align:center;width:120px;<?if ($StrStartTimeWeek1!=""){?>background-color:#E1E6F0;<?}?>">
						<span style="color:<?if($DataOk[6]==0){?>red<?}?>;"><?=$StrStartTimeWeek1?></span>
					</td>
					<td style="border: 1px solid black;text-align:center;width:120px;<?if ($StrStartTimeWeek2!=""){?>background-color:#E1E6F0;<?}?>">
						<span style="color:<?if($DataOk[7]==0){?>red<?}?>;"><?=$StrStartTimeWeek2?></span>
					</td>
					<td style="border: 1px solid black;text-align:center;width:120px;<?if ($StrStartTimeWeek3!=""){?>background-color:#E1E6F0;<?}?>">
						<span style="color:<?if($DataOk[8]==0){?>red<?}?>;"><?=$StrStartTimeWeek3?></span>
					</td>
					<td style="border: 1px solid black;text-align:center;width:120px;<?if ($StrStartTimeWeek4!=""){?>background-color:#E1E6F0;<?}?>">
						<span style="color:<?if($DataOk[9]==0){?>red<?}?>;"><?=$StrStartTimeWeek4?></span>
					</td>
					<td style="border: 1px solid black;text-align:center;width:120px;<?if ($StrStartTimeWeek5!=""){?>background-color:#E1E6F0;<?}?>">
						<span style="color:<?if($DataOk[10]==0){?>red<?}?>;"><?=$StrStartTimeWeek5?></span>
					</td>
				</tr>

			<?
		}

	} catch (exception $e) {
		echo '엑셀파일을 읽는도중 오류가 발생하였습니다.';
	}

	?>
	</tbody>
</table>
<!-- ===========================================  table ================================================= -->	

							</div>
						</div>
					</div>
				</div>
			</div>
			
			<?if ($AbleExcelListNum>0){?>
			<div style="margin-top:20px;text-align:center;color:#990000;">※ 아래 진행하기를 클릭하시면 선택하신 시간에 수업 가능한 강사를 분석하여 보여드립니다. 혹시 가능한 강사가 없을경우 신청서를 수정하여 다시 업로드 해주시기 바랍니다.</div>
			<?}?>
			
			<div style="margin-top: 20px; text-align:center;" id="BtnUpload">
				<a style="margin:0 auto;display:inline-block; background-color:#888888; color:#ffffff; text-align:center; width:110px; line-height:32px; font-size:14px;" href="javascript:GoPrev();"><?=$이전으로[$LangID]?></a>
				<?if ($AbleExcelListNum>0 && $AbleExcelListNum==$TotalExcelListNum){?>
					<a style="margin:0 auto;display:inline-block; background-color:#556BAC; color:#ffffff; text-align:center; width:200px; line-height:32px; font-size:14px;" href="javascript:FormSubmit();"><?=$수강신청_진행하기[$LangID]?></a>
				<?}else if ($AbleExcelListNum>0 && $AbleExcelListNum!=$TotalExcelListNum){?>
					<a style="margin:0 auto;display:inline-block; background-color:#556BAC; color:#ffffff; text-align:center; width:200px; line-height:32px; font-size:14px;" href="javascript:FormSubmit();">유효한_학생만_진행하기</a>
				<?}?>
			</div>
		</div>
	</form>
	</div>
</div>

<script>
function CloseThisWinodw() {
	parent.$.fn.colorbox.close();
}

function GoPrev(){
	location.href = "class_order_bulk_form.php";
}

function FormSubmit() {
	if (confirm("<?=$진행_하시겠습니까[$LangID]?>?")){
		document.getElementById("BtnUpload").innerHTML = "<img src='images/uploading_ing.gif'><br><br><?=$수강신청서_분석중입니다[$LangID]?><br><br><?=$수강신청_인원에_따라_시간이_오래_소요될_수_있으니_화면이_전환될_때까지_기다려_주시기_바랍니다[$LangID]?>";
		document.RegForm.action = "class_order_bulk_time_check.php";
		document.RegForm.submit();
	}

}

parent.$.colorbox.resize({width:"95%", height:"95%", maxWidth: "1500", maxHeight: "1000"});
</script>



<?
function validateDate($date, $format = 'Y-m-d H:i:s'){
	$d = DateTime::createFromFormat($format, $date);
	return $d && $d->format($format) == $date;
}
?>
</body>
</html>


<?
include_once('../includes/dbclose.php');
?>

