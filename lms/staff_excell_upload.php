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

		$tempFile  = $_FILES['StaffData']['tmp_name'];
		#--------------------------------------------------------------------------------------------------------------------#
		if ($tempFile){
		#--------------------------------------------------------------------------------------------------------------------#
		        #------------------------------------------------------------------------------------------------------------#
				$upFileName = iconv("UTF-8", "EUC-KR", $_FILES['StaffData']['name']);
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

					
						array_push($sheetdata_array,"1");  //배열 인덱스를 맞추기 위한 임의값
						array_push($sheetdata_array,$worksheet->getCell('A' . $row_cnt)->getValue());
						array_push($sheetdata_array,$worksheet->getCell('B' . $row_cnt)->getValue());
						array_push($sheetdata_array,$worksheet->getCell('C' . $row_cnt)->getValue());
						array_push($sheetdata_array,$worksheet->getCell('D' . $row_cnt)->getValue());
						array_push($sheetdata_array,$worksheet->getCell('E' . $row_cnt)->getValue());
						array_push($sheetdata_array,$worksheet->getCell('F' . $row_cnt)->getValue());
						if ($worksheet->getCell('G' . $row_cnt)->getValue()=='1') {
							array_push($sheetdata_array,$worksheet->getCell('G' . $row_cnt)->getValue());
						} else {
							array_push($sheetdata_array,'0');
						}
						if ($worksheet->getCell('H' . $row_cnt)->getValue()=='1' || $worksheet->getCell('H' . $row_cnt)->getValue()=='2' ) {
							array_push($sheetdata_array,$worksheet->getCell('H' . $row_cnt)->getValue());
						} else {
							array_push($sheetdata_array,'0');
						}
						if ($worksheet->getCell('I' . $row_cnt)->getValue()=='1') {
							array_push($sheetdata_array,$worksheet->getCell('I' . $row_cnt)->getValue());
						} else {
							array_push($sheetdata_array,'0');
						}
						array_push($sheetdata_array,$worksheet->getCell('J' . $row_cnt)->getValue());
						array_push($sheetdata_array,$worksheet->getCell('K' . $row_cnt)->getValue());
						array_push($sheetdata_array,$worksheet->getCell('L' . $row_cnt)->getValue());
						array_push($sheetdata_array,$worksheet->getCell('M' . $row_cnt)->getValue());
						array_push($sheetdata_array,$worksheet->getCell('N' . $row_cnt)->getValue());
						array_push($sheetdata_array,$worksheet->getCell('O' . $row_cnt)->getValue());
						array_push($sheetdata_array,$worksheet->getCell('P' . $row_cnt)->getValue());
						array_push($sheetdata_array,$worksheet->getCell('Q' . $row_cnt)->getValue());
						array_push($sheetdata_array,$worksheet->getCell('R' . $row_cnt)->getValue());
						array_push($sheetdata_array,$worksheet->getCell('S' . $row_cnt)->getCalculatedValue());
						array_push($sheetdata_array,$worksheet->getCell('T' . $row_cnt)->getCalculatedValue());
						array_push($sheetdata_array,$worksheet->getCell('U' . $row_cnt)->getCalculatedValue());
						array_push($sheetdata_array,$worksheet->getCell('V' . $row_cnt)->getCalculatedValue());
				
				
					  

					/*
					foreach ($cellIterator as $cell) {
						
						  if ($cell->getCalculatedValue()) {
								  $sheet_data = $sheet_data . "|" . $cell->getCalculatedValue();
						  }
					}
					*/
					if ($row_cnt > 2) {

						try {

							$MemberLoginID = $sheetdata_array[1];
							$Sql = "SELECT * from Members where MemberLoginID=:MemberLoginID";
							$Stmt = $DbConn->prepare($Sql);
							$Stmt->bindParam(':MemberLoginID', $MemberLoginID);
							$Stmt->execute();
							$Stmt->setFetchMode(PDO::FETCH_ASSOC);
							$Row = $Stmt->fetch();
	
							if (!$Row) { // 저장되어 있는 아이디가 없을 경우 직원 추가 작업 진행
								echo("추가한다!");
	
								// 엑셀에서 불러온 내용에서 직원 정보를 추출한다.
								$FranchiseID = $sheetdata_array[9];
								
								$StaffName = $sheetdata_array[3];
								$StaffNickName = $sheetdata_array[4];
								$StaffManageMent = $sheetdata_array[19];
	
								$StaffPhone1 = $sheetdata_array[12];
								
								$StaffPhone2 = $sheetdata_array[13];
								
								$StaffEmail = $sheetdata_array[14];
	
								$Jumin1 = $sheetdata_array[5];
								$Jumin2 = $sheetdata_array[6];
								$WorkType = $sheetdata_array[7];
								if ($WorkType == 0 ){
									$EmploymentInsurance = 1;
									$IndustrialInsurance = 1;
									$HealthInsurance = 1;
									$NationalPension = 1;
								} else {
									$EmploymentInsurance = 0;
									$IndustrialInsurance = 0;
									$HealthInsurance = 0;
									$NationalPension = 0;
								}
	
								$StaffState = 1;
								$StaffView = 1;
	
								//Members 
								$MemberID = "";
								$MemberDprtName = $sheetdata_array[10];
								$MemberLoginPW = $sheetdata_array[2];
								$MemberLanguageID = $sheetdata_array[8];
								
	
								//인사평가
								$Hr_OrganLevelID = $sheetdata_array[20];
								$Hr_OrganTask2ID = $sheetdata_array[22];
								$Hr_OrganPositionName = $sheetdata_array[18];
								$Hr_OrganLevel = "";
	
								$MemberLoginNewPW_hash = password_hash(sha1($MemberLoginPW), PASSWORD_DEFAULT);
	
	
	
										$Sql = "SELECT ifnull(Max(StaffOrder),0) as StaffOrder from Staffs";
										$Stmt = $DbConn->prepare($Sql);
										$Stmt->execute();
										$Stmt->setFetchMode(PDO::FETCH_ASSOC);
										$Row = $Stmt->fetch();
										$Stmt = null;
	
										$StaffOrder = $Row["StaffOrder"]+1;
	
										$Sql = "INSERT into Staffs ( ";
											$Sql .= " FranchiseID, ";
											$Sql .= " StaffManageMent, ";
											$Sql .= " StaffName, ";
											$Sql .= " StaffNickName, ";
											$Sql .= " StaffPhone1, ";
											$Sql .= " StaffPhone2, ";
											$Sql .= " StaffEmail, ";
											$Sql .= " StaffRegDateTime, ";
											$Sql .= " StaffModiDateTime, ";
											$Sql .= " StaffState, ";
											$Sql .= " StaffView, ";
											$Sql .= " Jumin1, ";
											$Sql .= " Jumin2, ";
											$Sql .= " StaffOrder ";
										$Sql .= " ) values ( ";
											$Sql .= " :FranchiseID, ";
											$Sql .= " :StaffManageMent, ";
											$Sql .= " :StaffName, ";
											$Sql .= " :StaffNickName, ";
											$Sql .= " HEX(AES_ENCRYPT(:StaffPhone1, :EncryptionKey)), ";
											$Sql .= " HEX(AES_ENCRYPT(:StaffPhone2, :EncryptionKey)), ";
											$Sql .= " HEX(AES_ENCRYPT(:StaffEmail, :EncryptionKey)), ";
											$Sql .= " now(), ";
											$Sql .= " now(), ";
											$Sql .= " :StaffState, ";
											$Sql .= " :StaffView, ";
											$Sql .= " :Jumin1, ";
											$Sql .= " :Jumin2, ";
											$Sql .= " :StaffOrder ";
										$Sql .= " ) ";
	
										$Stmt = $DbConn->prepare($Sql);
										$Stmt->bindParam(':FranchiseID', $FranchiseID);
										$Stmt->bindParam(':StaffManageMent', $StaffManageMent);
										$Stmt->bindParam(':StaffName', $StaffName);
										$Stmt->bindParam(':StaffNickName', $StaffNickName);
										$Stmt->bindParam(':StaffPhone1', $StaffPhone1);
										$Stmt->bindParam(':StaffPhone2', $StaffPhone2);
										$Stmt->bindParam(':StaffEmail', $StaffEmail);
										$Stmt->bindParam(':StaffState', $StaffState);
										$Stmt->bindParam(':StaffView', $StaffView);
										$Stmt->bindParam(':StaffOrder', $StaffOrder);
										$Stmt->bindParam(':Jumin1', $Jumin1);
										$Stmt->bindParam(':Jumin2', $Jumin2);
										$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
										$Stmt->execute();
										$StaffID = $DbConn->lastInsertId();
										$Stmt = null;
	
	
										//Members 
										$MemberLevelID = 4;//직원
	
										$Sql = "INSERT into Members ( ";
											$Sql .= " StaffID, ";
											$Sql .= " MemberDprtName, ";
											$Sql .= " MemberLevelID, ";
											$Sql .= " MemberLoginID, ";
											$Sql .= " MemberLanguageID, ";
											$Sql .= " MemberLoginPW, ";
											$Sql .= " MemberName, ";
											$Sql .= " MemberEmail, ";
											$Sql .= " MemberView, ";
											$Sql .= " MemberState, ";
											$Sql .= " MemberRegDateTime ";
	
										$Sql .= " ) values ( ";
	
											$Sql .= " :StaffID, ";
											$Sql .= " :MemberDprtName, ";
											$Sql .= " :MemberLevelID, ";
											$Sql .= " :MemberLoginID, ";
											$Sql .= " :MemberLanguageID, ";
											$Sql .= " :MemberLoginNewPW_hash, ";
											$Sql .= " :MemberName, ";
											$Sql .= " HEX(AES_ENCRYPT(:MemberEmail, :EncryptionKey)), ";
											$Sql .= " :MemberView, ";
											$Sql .= " :MemberState, ";
											$Sql .= " now() ";
										$Sql .= " ) ";
	
										$Stmt = $DbConn->prepare($Sql);
										$Stmt->bindParam(':StaffID', $StaffID);
										$Stmt->bindParam(':MemberDprtName', $MemberDprtName);
										$Stmt->bindParam(':MemberLevelID', $MemberLevelID);
										$Stmt->bindParam(':MemberLoginID', $MemberLoginID);
										$Stmt->bindParam(':MemberLanguageID', $MemberLanguageID);
										$Stmt->bindParam(':MemberLoginNewPW_hash', $MemberLoginNewPW_hash);
										$Stmt->bindParam(':MemberName', $StaffName);
										$Stmt->bindParam(':MemberEmail', $StaffEmail);
										$Stmt->bindParam(':MemberView', $StaffView);
										$Stmt->bindParam(':MemberState', $StaffState);
	
										$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
										$Stmt->execute();
										$MemberID = $DbConn->lastInsertId();
										$Stmt = null;
	
										//PayInfo 에 신규 입력
										
	
										$Sql = "INSERT INTO PayInfo (MemberID, WorkType, EmploymentInsurance, IndustrialInsurance, HealthInsurance, NationalPension, regDate) 
													VALUES ('$MemberID', $WorkType, $EmploymentInsurance, $IndustrialInsurance, $HealthInsurance, $NationalPension, now());";
										$Stmt = $DbConn->prepare($Sql);
										$Stmt->execute();
	
	
										// Hr_OrganLevel을 쿼리해 온다.
										
	
										$Sql = "SELECT * from Hr_OrganLevels where Hr_OrganLevelID = :Hr_OrganLevelID";
										$Stmt = $DbConn->prepare($Sql);
										$Stmt->bindParam(':Hr_OrganLevelID', $Hr_OrganLevelID);
										$Stmt->execute();
										$Stmt->setFetchMode(PDO::FETCH_ASSOC);
										$Row = $Stmt->fetch();
										$Hr_OrganLevel = $Row["Hr_OrganLevel"];
				
	
										//인사평가
										$Sql = "INSERT into Hr_OrganLevelTaskMembers ( ";
										$Sql .= " MemberID, ";
										$Sql .= " Hr_OrganLevel, ";
										$Sql .= " Hr_OrganLevelID, ";
										$Sql .= " Hr_OrganTask2ID, ";
										$Sql .= " Hr_OrganPositionName, ";
										$Sql .= " Hr_OrganLevelTaskMemberRegDateTime, ";
										$Sql .= " Hr_OrganLevelTaskMemberModiDateTime ";
	
										$Sql .= " ) values ( ";
										$Sql .= " :MemberID, ";
										$Sql .= " :Hr_OrganLevel, ";
										$Sql .= " :Hr_OrganLevelID, ";
										$Sql .= " :Hr_OrganTask2ID, ";
										$Sql .= " :Hr_OrganPositionName, ";
										$Sql .= " now(), ";
										$Sql .= " now() ";
	
									$Sql .= " ) ";
	
									$Stmt = $DbConn->prepare($Sql);
									$Stmt->bindParam(':MemberID', $MemberID);
									$Stmt->bindParam(':Hr_OrganLevel', $Hr_OrganLevel);
									$Stmt->bindParam(':Hr_OrganLevelID', $Hr_OrganLevelID);
									$Stmt->bindParam(':Hr_OrganTask2ID', $Hr_OrganTask2ID);
									$Stmt->bindParam(':Hr_OrganPositionName', $Hr_OrganPositionName);
									$Stmt->execute();
									$MemberID = $DbConn->lastInsertId();
									$Stmt = null;
	
							}
	

						} catch(Exception $e) {
							echo $e;
						}

						//$sheetdata_array = explode("|",$sheet_data);
						//var_dump($sheetdata_array);

						// 가장 먼저 해당 아이디가 이미 등록이 되어 있는지 확인한다. 
						// 만약 아이디가 등록이 되어 있는 상태이면 해당 엑셀 라인은 건너뛴다. 


						    

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
 document.RegForm.action = "staff_list.php";
 document.RegForm.submit();
</script>

