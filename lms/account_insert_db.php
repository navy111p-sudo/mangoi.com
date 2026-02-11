<?php
/*
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
include_once('./BaroService_Config.php');  //바로빌 계좌이체 기본 설정 및 soap 객체 생성
*/

#------------------------------------------------------------------------------------------------------#
$err_num = 0;
$err_msg = "";
#------------------------------------------------------------------------------------------------------#
//1. 먼저 등록되어 있는 계좌번호를 가지고 온다.
$Result = $BaroService_BANKACCOUNT->GetBankAccount(array(
	'CERTKEY'	=> $CERTKEY,
	'CorpNum'	=> $CorpNum
))->GetBankAccountResult;

if (array_key_exists('BankAccount',$Result) && !is_array($Result->BankAccount) && $Result->BankAccount->BankAccountNum < 0){ //실패
	echo $Result->BankAccount->BankAccountNum;
}else{ //성공
	
	foreach ($Result->BankAccount as $value){
		//하나밖에 없을 때는 object 이므로 object형에서 값을 가져온다.
		if (is_object($Result->BankAccount)){
			$BankAccountNum = $Result->BankAccount->BankAccountNum;
			$BankName = $Result->BankAccount->BankName;
		} else {
			$BankAccountNum = $value->BankAccountNum;
			$BankName = $value->BankName;
		}
		//2. 가지고 온 계좌번호가 db에 이미 등록되어 있는지 확인하고 
		$Sql = "SELECT * FROM AccountState
				where AccountNumber = :AccountNumber";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':AccountNumber',  $BankAccountNum);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;

		if ($Row) { 
			//3. 이미 등록되어 있으면 db 에서 최종 업데이트 날짜를 가지고 온다.
			$BankAccountNum = $Row["AccountNumber"];
			$UpdateDate = $Row["UpdateDate"];
		} else {
			//4. 만약 등록되어 있지 않으면 db에 새로 등록한다.
			
			//계좌와 업데이트 날짜를 세팅한다.
			$UpdateDate = "";

			$Sql = "INSERT into AccountState ( 
						AccountType, 
						AccountName,
						AccountNumber
					) VALUES (
						0,
						:AccountName, 
						:AccountNumber)
					";

			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':AccountName',        $BankName);
			$Stmt->bindParam(':AccountNumber',   	$BankAccountNum);
			$Stmt->execute();
			$Stmt = null;
			
		}
		
		
		//5. 만약 마지막 등록날짜가 오늘이면 건너띄고 오늘이 아니면 이번달 계좌내역을 가지고 온다.
		//5-1. 만약 [거래내역 다시 가져오기] 버튼으로 리로드하는 거라면 해달 월의 내역을 가져온다. 
		if ($ReloadMonth != "") {
			//리로드하고자 하는 월의 내역을 다시 가져온다.
			UpdateAccountMonthly($BankAccountNum, $ReloadMonth);
			//echo "reloadmonth : $ReloadMonth";
		} else if ($UpdateDate != date('Y-m-d')) {

			//6. 바로빌 API를 이용해서 계좌내역을 가지고 온다. (월별로)
			//6-1. 만약 UpdateDate가 있었다면 즉, 계좌등록을 처음 한 것이 아니라면 이번달 계좌내역만 가져오고 
			//     처음 등록하는 거라면 2021년부터 계좌내역을 모두 가져온다.

			/*
			아래 코드는 계좌등록이 완료되면 다시 풀어줘야 거래내역을 불러올 때마다 속도 저하를 막을 수 있다.
			if ($UpdateDate != ""){
				$BaseMonth = date('Y').date('m');		//현재월을 기준월로 
				UpdateAccountMonthly($BankAccountNum, $BaseMonth);
			} else {
			*/

				//만약 1월이면 전년도의 데이타를 가져온다.
				if ((int)date('m') == 1) {
					$updateStartYear = (int)date('Y') - 1;
					$updateStartMonth = 12; 
				} else {
					$updateStartYear = (int)date('Y');
					$updateStartMonth = (int)date('m') - 1;
				}
				for ($i=$updateStartYear;$i<=(int)date('Y');$i++){
					for ($j=$updateStartMonth;$j<=12;$j++){
						if ($j<10) $j = "0".$j; //0을 붙여서 형식을 맞춰준다.
						$BaseMonth = $i.$j;		
						//echo $BaseMonth."<br>"; 
						UpdateAccountMonthly($BankAccountNum, $BaseMonth);
						
						// 만약 이번달이면 루프를 종료한다.
						if ($i==(int)date('Y') && $j==(int)date('m')) break;
					}
					$updateStartMonth = 1;
				}


			// }
			

		}

		//7. 해당 계좌에 최종 업데이틀 날짜를 등록해 준다.
		//계좌와 업데이트 날짜를 세팅한다.
		$UpdateDate = "";

		$Sql = "UPDATE AccountState SET 
					UpdateDate = now() 				
				WHERE AccountNumber = :AccountNumber";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':AccountNumber',   	$BankAccountNum);
		$Stmt->execute();
		$Stmt = null;


	//8. 그 다음 계좌로 진행

	}
}






// 계좌와 해당 월을 지정해 주면 계좌내역을 가져와서 DB에 입력한다. 중복된 데이타는 입력되지 않는다.
function UpdateAccountMonthly($BankAccountNum, $BaseMonth){
	
	global $BaroService_BANKACCOUNT,$DbConn,$CERTKEY,$CorpNum,$ID;

	$CountPerPage = 1000;		//한 페이지 당 조회 건 수
	$CurrentPage = 1;		//현재페이지
	$OrderDirection = 2;	//1:ASC 2:DESC
	$Result = $BaroService_BANKACCOUNT->GetMonthlyBankAccountLogEx(array(
		'CERTKEY'			=> $CERTKEY,
		'CorpNum'			=> $CorpNum,
		'ID'				=> $ID,
		'BankAccountNum'	=> $BankAccountNum,
		'BaseMonth'			=> $BaseMonth,
		'CountPerPage'		=> $CountPerPage,
		'CurrentPage'		=> $CurrentPage,
		'OrderDirection'	=> $OrderDirection
	))->GetMonthlyBankAccountLogExResult;
	if ($Result->CurrentPage < 0) { //실패
		//echo $Result->CurrentPage;
	}else{ //성공
		
		while ($CurrentPage <= $Result->MaxPageNum){
			if ($Result->MaxIndex > 0){
				foreach ($Result->BankAccountLogList->BankAccountLogEx as $value){
					//하나밖에 없을 때는 object 이므로 object형에서 값을 가져온다.
					if (is_object($Result->BankAccountLogList->BankAccountLogEx)){
						$Withdraw = $Result->BankAccountLogList->BankAccountLogEx->Withdraw;
						$TransDT = $Result->BankAccountLogList->BankAccountLogEx->TransDT;
						$Deposit = $Result->BankAccountLogList->BankAccountLogEx->Deposit;
						$TransRemark  = $Result->BankAccountLogList->BankAccountLogEx->TransRemark;
						$BankAccountNum = $Result->BankAccountLogList->BankAccountLogEx->BankAccountNum;
						$TransOffice =  $Result->BankAccountLogList->BankAccountLogEx->TransOffice;
						$TransRefKey =  $Result->BankAccountLogList->BankAccountLogEx->TransRefKey;
					} else {
						$Withdraw = $value->Withdraw;
						$TransDT = $value->TransDT;
						$Deposit = $value->Deposit;
						$TransRemark  = $value->TransRemark;
						$BankAccountNum = $value->BankAccountNum;
						$TransOffice =  $value->TransOffice;
						$TransRefKey =  $value->TransRefKey;
						
					}
					// 거래날짜 
					$AccBookDate = substr($TransDT,0,4)."-".substr($TransDT,4,2)."-".substr($TransDT,6,2);
				// 수익/비용 종류와 금액 설정
					if ($Withdraw > 0){
						$AccBookType = 2;
						$AccBookMoney = $Withdraw;
						$AccBookConfigID = 15;   // 비용일 경우 구분을 15으로 "계좌 비용"
					} else if ($Deposit > 0){
						$AccBookType = 1;
						$AccBookMoney = $Deposit;
						$AccBookConfigID = 1;   // 수익일 경우 구분을 매출로 설정
					}else if($Withdraw == null) {
						$AccBookType = 2;
						$AccBookMoney = $Withdraw;
						$AccBookConfigID = 15;
					}else if($Deposit == null) {
						$AccBookType = 1;
						$AccBookMoney = $Deposit;
						$AccBookConfigID = 1;
					}



				// 거래 종류 0:계좌이체, 1:신용카드, 2:현금 및 기타
					$AccType = 0;
					$Sql = "INSERT into account_book ( 
								AccBookDate, 
								AccBookType, 
								AccBookConfigID, 
								AccBookMoney,
								AccBookSubject,
								wdate,
								AccType,
								TransactDate,
								AccNumber,
								TransactMemo,
								StoreName,
								TransRefKey
						) SELECT   
								:AccBookDate,
								:AccBookType, 
								:AccBookConfigID, 
								:AccBookMoney,
								:AccBookSubject,
								now(),
								:AccType,
								:TransactDate,
								:AccNumber,
								:TransactMemo,
								:StoreName,
								:TransRefKey
							FROM DUAL
							WHERE NOT EXISTS 
								(SELECT AccBookID FROM account_book WHERE AccNumber = :AccNumber AND TransRefKey = :TransRefKey)
							";
					$Stmt = $DbConn->prepare($Sql);
					$Stmt->bindParam(':AccBookDate',        $AccBookDate);
					$Stmt->bindParam(':AccBookType',        $AccBookType);
					$Stmt->bindParam(':AccBookConfigID',    $AccBookConfigID);
					$Stmt->bindParam(':AccBookSubject',     $TransRemark);
					$Stmt->bindParam(':AccBookMoney',       $AccBookMoney);
					$Stmt->bindParam(':AccType',       		$AccType);
					$Stmt->bindParam(':TransactDate',       $TransDT);
					$Stmt->bindParam(':AccNumber',       	$BankAccountNum);
					$Stmt->bindParam(':TransactMemo',       $TransRemark);
					$Stmt->bindParam(':StoreName',       	$TransOffice);
					$Stmt->bindParam(':TransRefKey',       	$TransRefKey);
					$Stmt->execute();
					$Stmt = null;
					
				}
	
			}
			$CurrentPage++;
			// 만약 페이지가 끝났다면 끝내고 더 남았다면 다시 한번 읽어온다.
			if ($CurrentPage <= (int)$Result->MaxPageNum) {
				$Result = $BaroService_BANKACCOUNT->GetMonthlyBankAccountLogEx(array(
					'CERTKEY'			=> $CERTKEY,
					'CorpNum'			=> $CorpNum,
					'ID'				=> $ID,
					'BankAccountNum'	=> $BankAccountNum,
					'BaseMonth'			=> $BaseMonth,
					'CountPerPage'		=> $CountPerPage,
					'CurrentPage'		=> $CurrentPage,
					'OrderDirection'	=> $OrderDirection
				))->GetMonthlyBankAccountLogExResult;
	
			}
			
		}
		
		
		
	#------------------------------------------------------------------------------------------------------#
		
	}
}

?>


<?
//include_once('../includes/dbclose.php');
?>

