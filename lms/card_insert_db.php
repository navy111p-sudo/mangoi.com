<?php
/*
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
*/
include_once('./BaroService_Config.php');   //바로빌 카드거래 기본 설정 및 soap 객체 생성

#------------------------------------------------------------------------------------------------------#
$err_num = 0;
$err_msg = "";
#------------------------------------------------------------------------------------------------------#

//1. 먼저 등록되어 있는 카드번호를 가지고 온다.
$Result = $BaroService_CARD->GetCard(array(
	'CERTKEY'	=> $CERTKEY,
	'CorpNum'	=> $CorpNum
))->GetCardResult;

if (array_key_exists('Card',$Result) && !is_array($Result->Card) && $Result->Card->CardNum < 0){ //실패
	echo $Result->Card->CardNum;
}else{ //성공
	
	//print_r($Result);
	foreach ($Result->Card as $value){
		//하나밖에 없을 때는 object 이므로 object형에서 값을 가져온다.
		if (is_object($Result->Card)){
			$CardNum = $Result->Card->CardNum;
			$CardCompanyName = $Result->Card->CardCompanyName;
		} else {
			$CardNum = $value->CardNum;
			$CardCompanyName = $value->CardCompanyName;
		}
		//2. 가지고 온 번호가 db에 이미 등록되어 있는지 확인하고 
		$Sql = "SELECT * FROM AccountState
				where AccountNumber = :AccountNumber";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':AccountNumber',  $CardNum);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;

		if ($Row) { 
			//3. 이미 등록되어 있으면 db 에서 최종 업데이트 날짜를 가지고 온다.
			$CardNum = $Row["AccountNumber"];
			$UpdateDate = $Row["UpdateDate"];
		} else {
			//4. 만약 등록되어 있지 않으면 db에 새로 등록한다.
			
			//카드와 업데이트 날짜를 세팅한다.
			$UpdateDate = "";
			

			$Sql = "INSERT into AccountState ( 
						AccountType, 
						AccountName,
						AccountNumber
					) VALUES (
						1,
						:AccountName, 
						:AccountNumber)
					";

			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':AccountName',        $CardCompanyName);
			$Stmt->bindParam(':AccountNumber',   	$CardNum);
			$Stmt->execute();
			$Stmt = null;

		}	

		//5. 만약 마지막 등록날짜가 오늘이면 건너띄고 오늘이 아니면 이번달 카드 거래내역을 가지고 온다.
		//5-1. 만약 [거래내역 다시 가져오기] 버튼으로 리로드하는 거라면 해달 월의 내역을 가져온다. 
		if ($ReloadMonth != "") {
			//리로드하고자 하는 월의 내역을 다시 가져온다.
			UpdateCardMonthly($CardNum, $ReloadMonth);

		} else  if ($UpdateDate != date('Y-m-d')) {

			//6. 바로빌 API를 이용해서 카드거래내역을 가지고 온다. (월별로)
			//6-1. 만약 UpdateDate가 있었다면 즉, 등록을 처음 한 것이 아니라면 이번달 내역만 가져오고 
			//     처음 등록하는 거라면 2021년부터 내역을 모두 가져온다.

			/*
			아래 코드는 계좌등록이 완료되면 다시 풀어줘야 거래내역을 불러올 때마다 속도 저하를 막을 수 있다.
			if ($UpdateDate != ""){
				$BaseMonth = date('Y').date('m');		//현재월을 기준월로 
				UpdateCardMonthly($CardNum, $BaseMonth);
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
						UpdateCardMonthly($CardNum, $BaseMonth);
						
						// 만약 이번달이면 루프를 종료한다.
						if ($i==(int)date('Y') && $j==(int)date('m')) break;
					}
					$updateStartMonth = 1;
				}
			//}
			

		}

		//7. 해당 카드번호에 최종 업데이틀 날짜를 등록해 준다.
		//카드와 업데이트 날짜를 세팅한다.
		$UpdateDate = "";

		$Sql = "UPDATE AccountState SET 
					UpdateDate = now() 				
				WHERE AccountNumber = :AccountNumber";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':AccountNumber',   	$CardNum);
		$Stmt->execute();
		$Stmt = null;


	//8. 그 다음 카드번호로 진행
	}	
}




// 계좌와 해당 월을 지정해 주면 계좌내역을 가져와서 DB에 입력한다. 중복된 데이타는 입력되지 않는다.
function UpdateCardMonthly($CardNum, $BaseMonth){

	global $BaroService_CARD,$DbConn,$CERTKEY,$CorpNum,$ID;

	$CountPerPage = 1000;			//한 페이지 당 조회 건 수
	$CurrentPage = 1;				//현재페이지
	$OrderDirection = 2;			//1:ASC 2:DESC

	$Result = $BaroService_CARD->GetMonthlyCardLogEx(array(
		'CERTKEY'			=> $CERTKEY,
		'CorpNum'			=> $CorpNum,
		'ID'				=> $ID,
		'CardNum'			=> $CardNum,
		'BaseMonth'			=> $BaseMonth,
		'CountPerPage'		=> $CountPerPage,
		'CurrentPage'		=> $CurrentPage,
		'OrderDirection' 	=> $OrderDirection
	))->GetMonthlyCardLogExResult;

	if ($Result->CurrentPage < 0) { //실패
		//echo $Result->CurrentPage;
	}else{ //성공
		//print_r($Result);
		
		//페이지가 여러 페이지인 경우 1페이지씩 증가시키면서 데이타를 받아온다.
		while ($CurrentPage <= $Result->MaxPageNum){
			
			if ($Result->MaxIndex > 0){
			
				foreach ($Result->CardLogList->CardLogEx as $value){
					//하나밖에 없을 때는 object 이므로 object형에서 값을 가져온다.
					if (is_object($Result->CardLogList->CardLogEx)){
						$UseDT = $Result->CardLogList->CardLogEx->UseDT;
						$CardApprovalCost = $Result->CardLogList->CardLogEx->CardApprovalCost;
						if (isset($Result->CardLogList->CardLogEx->UseStoreBizType)) {
							$UseStoreBizType = $Result->CardLogList->CardLogEx->UseStoreBizType;
						} else {
							$UseStoreBizType = "";
						}
						
						$CardNum  = $Result->CardLogList->CardLogEx->CardNum;
						$UseStoreName = $Result->CardLogList->CardLogEx->UseStoreName;
						$UseKey =  $Result->CardLogList->CardLogEx->UseKey;
						$CardApprovalType  =  $Result->CardLogList->CardLogEx->CardApprovalType;
					} else {
						$UseDT = $value->UseDT;
						$CardApprovalCost = $value->CardApprovalCost;
						if (isset($value->UseStoreBizType)) {
							$UseStoreBizType = $value->UseStoreBizType;
						} else {
							$UseStoreBizType = "";
						}
						
						$CardNum  = $value->CardNum;
						$UseStoreName = $value->UseStoreName;
						$UseKey =  $value->UseKey;
						$CardApprovalType =  $value->CardApprovalType;
						
					}
					
					if ($CardApprovalType == "승인"){
						// 거래날짜 
						$AccBookDate = substr($UseDT,0,4)."-".substr($UseDT,4,2)."-".substr($UseDT,6,2);
	
						$AccBookConfigID = 14;   // 카드는 비용이므로 구분을 14로 "카드 비용"
	
						// 수익/비용 종류와 금액 설정
						$AccBookType = 2;
						$AccBookMoney = $CardApprovalCost;
								
						
						// 거래 종류 0:계좌이체, 1:신용카드, 2:현금 및 기타
						$AccType = 1;
	
						$Sql = "INSERT into account_book ( 
										AccBookDate, 
										AccBookType, 
										AccBookSubject, 
										AccBookConfigID, 
										AccBookMoney,
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
										:AccBookSubject,
										:AccBookConfigID, 
										:AccBookMoney,
										now(),
										:AccType,
										:TransactDate,
										:AccNumber,
										:TransactMemo,
										:StoreName,
										:TransRefKey
									FROM DUAL
									WHERE NOT EXISTS 
										(SELECT AccBookID FROM account_book WHERE AccNumber = :AccNumber AND TransRefKey = :TransRefKey) ";
	
						$Stmt = $DbConn->prepare($Sql);
						$Stmt->bindParam(':AccBookDate',        $AccBookDate);
						$Stmt->bindParam(':AccBookType',        $AccBookType);
						$Stmt->bindParam(':AccBookSubject',     $UseStoreBizType);
						$Stmt->bindParam(':AccBookConfigID',    $AccBookConfigID);
						$Stmt->bindParam(':AccBookMoney',       $AccBookMoney);
						$Stmt->bindParam(':AccType',       		$AccType);
						$Stmt->bindParam(':TransactDate',       $UseDT);
						$Stmt->bindParam(':AccNumber',       	$CardNum);
						$Stmt->bindParam(':TransactMemo',       $UseStoreBizType);
						$Stmt->bindParam(':StoreName',       	$UseStoreName);
						$Stmt->bindParam(':TransRefKey',       	$UseKey);
						$Stmt->execute();
						$Stmt = null;
	
					}
	
					
						
				}
			}
			$CurrentPage++;
			// 만약 페이지가 끝났다면 끝내고 더 남았다면 다시 한번 읽어온다.
			if ($CurrentPage <= (int)$Result->MaxPageNum) {
				
				$Result = $BaroService_CARD->GetMonthlyCardLogEx(array(
					'CERTKEY'			=> $CERTKEY,
					'CorpNum'			=> $CorpNum,
					'ID'				=> $ID,
					'CardNum'			=> $CardNum,
					'BaseMonth'			=> $BaseMonth,
					'CountPerPage'		=> $CountPerPage,
					'CurrentPage'		=> $CurrentPage,
					'OrderDirection' 	=> $OrderDirection
				))->GetMonthlyCardLogExResult;
			}
			

		}
		

	}

}



?>


<?
//include_once('../includes/dbclose.php');
?>

