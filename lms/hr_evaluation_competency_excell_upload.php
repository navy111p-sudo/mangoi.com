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

$SearchState = isset($_REQUEST["SearchState"]) ? $_REQUEST["SearchState"] : "";
#============================================================================================================================#
if ($SearchState) {
#============================================================================================================================#
		$tempFile  = $_FILES['EvaluationCompetencyData']['tmp_name'];
		#--------------------------------------------------------------------------------------------------------------------#
		if ($tempFile){
		#--------------------------------------------------------------------------------------------------------------------#
				// $Sql  = "delete from Hr_EvaluationCompetencyMembers where Hr_EvaluationID=:Hr_EvaluationID";
				// $Stmt = $DbConn->prepare($Sql);
				// $Stmt->bindParam(':Hr_EvaluationID', $SearchState);
				// $Stmt->execute();
				// $Stmt = null;
		        #------------------------------------------------------------------------------------------------------------#
				$upFileName = iconv("UTF-8", "EUC-KR", $_FILES['EvaluationCompetencyData']['name']);
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
					foreach ($cellIterator as $cell) {
						  if ($cell->getValue()) {
								  $sheet_data = $sheet_data . "|" . $cell->getValue();
						  }
					}
					if ($row_cnt > 2) {

                            $sheetdata_array = explode("|",$sheet_data);
                            

                            $MemberLoginID = $sheetdata_array[1];
							$Sql = "select * from Members where MemberLoginID=:MemberLoginID";
							$Stmt = $DbConn->prepare($Sql);
							$Stmt->bindParam(':MemberLoginID', $MemberLoginID);
							$Stmt->execute();
							$Stmt->setFetchMode(PDO::FETCH_ASSOC);
							$Row = $Stmt->fetch();
							$MemberID = $Row["MemberID"];

							$EvaluationCompetencyMemberType = $sheetdata_array[2];
							
							$MemberLoginID = $sheetdata_array[3];
							$Sql = "select * from Members where MemberLoginID=:MemberLoginID";
							$Stmt = $DbConn->prepare($Sql);
							$Stmt->bindParam(':MemberLoginID', $MemberLoginID);
							$Stmt->execute();
							$Stmt->setFetchMode(PDO::FETCH_ASSOC);
							$Row = $Stmt->fetch();
							$EvaluationCompetencyMemberID = $Row["MemberID"];
                            
                            $EvaluationCompetencyAddValue   = $sheetdata_array[4];
                             
							if ($MemberID > 0 and $EvaluationCompetencyMemberID > 0 and $EvaluationCompetencyMemberType > 0 and $EvaluationCompetencyAddValue > 0) {
									$Sql = " insert into Hr_EvaluationCompetencyMembers ( ";
										$Sql .= " Hr_EvaluationID, ";
										$Sql .= " MemberID, ";
										$Sql .= " Hr_EvaluationCompetencyMemberID, ";
										$Sql .= " Hr_EvaluationCompetencyMemberType, ";
										$Sql .= " Hr_EvaluationCompetencyAddValue, ";
										$Sql .= " Hr_EvaluationCompetencyMemberRegDateTime ";
									$Sql .= " ) values ( ";
										$Sql .= " ".$SearchState.", ";
										$Sql .= " ".$MemberID.", ";
										$Sql .= " ".$EvaluationCompetencyMemberID.", ";
										$Sql .= " ".$EvaluationCompetencyMemberType.", ";
										$Sql .= " ".$EvaluationCompetencyAddValue.", ";
										$Sql .= " now() ";
									$Sql .= " ) ";
									$Stmt = $DbConn->prepare($Sql);
									$Stmt->execute();
									$Stmt = null;
					        } 

					}
				}
		        #------------------------------------------------------------------------------------------------------------#
				@unlink($up_file);
		#--------------------------------------------------------------------------------------------------------------------#
		}                
#============================================================================================================================#
}
#============================================================================================================================#
include_once('../includes/dbclose.php');
?>
<form name="RegForm" method="post">
<input type="hidden" name="SearchState"  value="<?=$SearchState?>" />
</form>

<script>
 document.RegForm.target = "_top";
 document.RegForm.action = "hr_evaluation_competency_table.php";
 document.RegForm.submit();
</script>

