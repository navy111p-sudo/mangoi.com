<?php
  error_reporting( E_ALL );
  ini_set( "display_errors", 1 );
?>
<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
?>
<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="ko"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="ko"> <!--<![endif]-->
<head>
<?
include_once('./includes/common_meta_tag.php');
include_once('./inc_header.php');
include_once('./inc_common_form_css.php');
// include for excell read
include_once('./vendor/autoload.php');
use PhpOffice\PhpSpreadsheet\Spreadsheet; 
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
?>
<link rel="stylesheet" type="text/css" href="css/common.css" />
</head>
</body>
<?
$savedir = "../uploads"; 
$err_num = 0;
$err_msg = "";

		$tempFile  = $_FILES['AccountData']['tmp_name'];
		#--------------------------------------------------------------------------------------------------------------------#
		if ($tempFile){
		#--------------------------------------------------------------------------------------------------------------------#
		        #------------------------------------------------------------------------------------------------------------#
				$upFileName = iconv("UTF-8", "EUC-KR", $_FILES['AccountData']['name']);
		        #------------------------------------------------------------------------------------------------------------#
				$file_type_check = explode('.',$upFileName);
				$file_type = $file_type_check[1];

				if ($file_type=="php" || $file_type=="php3" || $file_type=="html"){
					   $upFileName = $upFileName."_";
				}

				if(@copy($tempFile, $savedir."/".$upFileName)) { 
					 @unlink($tempFile);
				}

				$up_file = $savedir . "/" . $upFileName;
		        #------------------------------------------------------------------------------------------------------------#
				$spreadsheet = $reader->load($up_file); 
				$worksheet   = $spreadsheet->getActiveSheet();

                $row_cnt = 0;
				foreach ($worksheet->getRowIterator() as $row) {

					$cellIterator = $row->getCellIterator();
					$cellIterator->setIterateOnlyExistingCells(FALSE);
					
					$row_cnt++;
                    $sheet_data = $row_cnt; 
					
					//$maxRow = $worksheet->getHighestRow();
					$sheetdata_array = [];

						array_push($sheetdata_array,$worksheet->getCell('A' . $row_cnt)->getValue());
						array_push($sheetdata_array,$worksheet->getCell('B' . $row_cnt)->getValue());
						array_push($sheetdata_array,$worksheet->getCell('C' . $row_cnt)->getValue());
						array_push($sheetdata_array,$worksheet->getCell('D' . $row_cnt)->getValue());
						array_push($sheetdata_array,$worksheet->getCell('E' . $row_cnt)->getValue());
						array_push($sheetdata_array,$worksheet->getCell('F' . $row_cnt)->getValue());
						array_push($sheetdata_array,$worksheet->getCell('G' . $row_cnt)->getValue());
				
				
					if ($row_cnt > 2) {

						try {

							// 엑셀에서 불러온 내용에서 회계 정보를 추출한다.
							$AccBookConfigID = $sheetdata_array[0];
							$AccBookDate = $sheetdata_array[1];
							$AccBookType = $sheetdata_array[2];
							$AccBookSubject = $sheetdata_array[3];
							$AccBookMoney = $sheetdata_array[4];
							$AccType = $sheetdata_array[5];
							$AccNumber = $sheetdata_array[6];
							
								$Sql = "INSERT into account_book ( 
										AccBookConfigID,
										AccBookDate,
										AccBookType,
										AccBookSubject,
										AccBookMoney,
										AccType,
										AccNumber,
										TransactDate,
										wdate)
									VALUES 
										(
										:AccBookConfigID,
										:AccBookDate,
										:AccBookType,
										:AccBookSubject,
										:AccBookMoney,
										:AccType,
										:AccNumber,
										:AccBookDate,
										now()
							)";
									
							$Stmt = $DbConn->prepare($Sql);
							$Stmt->bindParam(':AccBookConfigID', $AccBookConfigID);
							$Stmt->bindParam(':AccBookDate', $AccBookDate);
							$Stmt->bindParam(':AccBookType', $AccBookType);
							$Stmt->bindParam(':AccBookSubject', $AccBookSubject);
							$Stmt->bindParam(':AccBookMoney', $AccBookMoney);
							$Stmt->bindParam(':AccType', $AccType);
							$Stmt->bindParam(':AccNumber', $AccNumber);
							$Stmt->execute();
							$Stmt = null;
	

						} catch(Exception $e) {
							echo $e;
						}

					}
				}
		        #------------------------------------------------------------------------------------------------------------#
				@unlink($up_file);
		#--------------------------------------------------------------------------------------------------------------------#
		}                

include_once('../includes/dbclose.php');
?>
<form name="RegForm" method="post">

</form>

<script>
 document.RegForm.target = "_top";
 document.RegForm.action = "account_book.php";
 document.RegForm.submit();
</script>

