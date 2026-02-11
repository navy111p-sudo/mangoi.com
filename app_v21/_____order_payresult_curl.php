<?php
######################################################################################################################################
######################################################### cURL GET DATA ##############################################################
######################################################################################################################################
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
#------------------------------------------------------------------------------------------------------------------------------------#
$ret_val = 9;
#====================================================================================================================================#
$myObj = new stdObject();
$myObj = new stdClass;
$myObj = json_decode(file_get_contents('php://input'));
#------------------------------------------------------------------------------------------------------------------------------------#
$Mobile_PayMethod = $myObj->Mobile_PayMethod; 
$PayCustName      = $myObj->PayCustName;      
$PayGoods         = $myObj->PayGoods;         
$PayMny           = $myObj->PayMny;           
$PayTradenum      = $myObj->PayTradenum;      
$PayResultCd      = $myObj->PayResultCd;     
$PayResultMsg     = $myObj->PayResultMsg;    
$PayReTrno        = $myObj->PayReTrno;       
$PayReNum         = $myObj->PayReNum;        
$PayReTime        = $myObj->PayReTime;       
if ($Mobile_PayMethod=='CARD') {
	  $PayCardCD  = $myObj->PayCardCD;       
	  $PayCard    = $myObj->PayCard;         
	  $PayDivMon  = $myObj->PayDivMon;       
} else {
      $PayBankCD  = $myObj->PayBankCD;
	  $PayBank    = $myObj->PayBank;
	  $PayCashYN  = $myObj->PayCashYN;
}
$PayReqURL        = $myObj->PayReqURL;       
#------------------------------------------------------------------------------------------------------------------------------------#
$err_num = 0;
$err_msg = "";

$Sql = "UPDATE ClassOrders SET ";
	if ($PayResultCd == "0000"){
		  if ($Mobile_PayMethod=='CARD') {
		         $Sql .= " UseCashPaymentType = 1, ";
		  } else {
		         $Sql .= " UseCashPaymentType = 2, ";
		  }
		  $Sql .= " OrderProgress = 21, ";
		  $Sql .= " ClassOrderPaymentDateTime = now(), ";
	}
	//kcp ==============
	$Sql .= " PayCustName  = :PayCustName,  ";
	$Sql .= " PayGoods     = :PayGoods,     ";
	$Sql .= " PayMny       = :PayMny,       ";
	$Sql .= " PayResultCd  = :PayResultCd,  ";
	$Sql .= " PayResultMsg = :PayResultMsg, ";
	$Sql .= " PayReTrno    = :PayReTrno,    ";
	$Sql .= " PayReNum     = :PayReNum,     ";
	$Sql .= " PayReTime    = :PayReTime,    ";
	if ($Mobile_PayMethod=='CARD') {
		 $Sql .= " PayCardCD = :PayCardCD, ";
		 $Sql .= " PayCard   = :PayCard,   ";
		 $Sql .= " PayDivMon = :PayDivMon, ";
    } else { 
		 $Sql .= " PayBankCD = :PayBankCD, ";
		 $Sql .= " PayBank   = :PayBank,   ";
		 $Sql .= " PayCashYN = :PayCashYN, ";
	}
	$Sql .= " PayReqURL    = :PayReqURL,    ";	
	//kcp ==============
	$Sql .= " ClassOrderModiDateTime = now() ";
$Sql .= " WHERE ClassOrderNumber = :OrderNumPay ";
$Stmt = $DbConn->prepare($Sql);
//kcp ==============
$Stmt->bindParam(':PayCustName',  $PayCustName);
$Stmt->bindParam(':PayGoods',     $PayGoods);
$Stmt->bindParam(':PayMny',       $PayMny);
$Stmt->bindParam(':OrderNumPay',  $PayTradenum);
$Stmt->bindParam(':PayResultCd',  $PayResultCd);
$Stmt->bindParam(':PayResultMsg', $PayResultMsg);
$Stmt->bindParam(':PayReTrno',    $PayReTrno);
$Stmt->bindParam(':PayReNum',     $PayReNum);
$Stmt->bindParam(':PayReTime',    $PayReTime);
if ($Mobile_PayMethod=='CARD') {
	 $Stmt->bindParam(':PayCardCD', $PayCardCD);
	 $Stmt->bindParam(':PayCard',   $PayCard);
	 $Stmt->bindParam(':PayDivMon', $PayDivMon);
} else {
	 $Stmt->bindParam(':PayBankCD', $PayBankCD);
	 $Stmt->bindParam(':PayBank',   $PayBank);
	 $Stmt->bindParam(':PayCashYN', $PayCashYN);
}
$Stmt->bindParam(':PayReqURL',    $PayReqURL);
//kcp ==============
$Stmt->execute();
$Stmt = null;

if ($err_msg != 0) {
      $ret_val = 1;
}

include_once('../includes/dbclose.php');
######################################################################################################################################
echo $ret_val;
?>