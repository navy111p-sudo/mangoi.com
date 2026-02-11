<?php
include_once('../includes/dbopen.php');
include_once('./includes/common.php');
#----------------------------------------------------------------------------------------------#
$work_sw      = isset($_REQUEST["work_sw"]) ? $_REQUEST["work_sw"] : "";
$account_date = isset($_REQUEST["account_date"]) ? $_REQUEST["account_date"] : "";
$account_val  = isset($_REQUEST["account_val" ]) ? $_REQUEST["account_val" ] : "";
#----------------------------------------------------------------------------------------------#
$ArrValue   = "";
$Total_val  = "";
#----------------------------------------------------------------------------------------------#
if ($work_sw==1) {            // 일일 매출합계 구하기
#----------------------------------------------------------------------------------------------#
		$Sql = "select sum(A.ClassOrderPaySellingPrice) as Total_Pay 
					   from ClassOrderPays A 
					  where A.ClassOrderPayProgress=21 and 
					        date_format(A.ClassOrderPayPaymentDateTime, '%Y-%m-%d')='".$account_date."'";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Total_val = $Row["Total_Pay"];
		$Stmt = null;
#----------------------------------------------------------------------------------------------#
} else if ($work_sw==2) {    // 월간 총매출합계 구하기
#----------------------------------------------------------------------------------------------#
		$Sql = "select sum(A.ClassOrderPaySellingPrice) as Total_Pay 
					   from ClassOrderPays A 
					  where A.ClassOrderPayProgress=21 and 
					        date_format(A.ClassOrderPayPaymentDateTime, '%Y-%m')='".$account_date."'";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Total_val = $Row["Total_Pay"];
		$Stmt = null;
#----------------------------------------------------------------------------------------------#
} else if ($work_sw==3) {    // 월간 일일 매출합계 구하기
#----------------------------------------------------------------------------------------------#
		$Sql = "select sum(A.ClassOrderPaySellingPrice) as Total_Pay, 
		               date_format(A.ClassOrderPayPaymentDateTime, '%Y-%m-%d') as Total_date 
					   from ClassOrderPays A 
					  where A.ClassOrderPayProgress=21 and 
					        date_format(A.ClassOrderPayPaymentDateTime, '%Y-%m')='".$account_date."' 
				   group by date_format(A.ClassOrderPayPaymentDateTime, '%Y-%m-%d') 
				   order by A.ClassOrderPayPaymentDateTime asc";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		while($Row = $Stmt->fetch()) {
				$Total_date = $Row["Total_date"];
				$Total_Pay  = $Row["Total_Pay"];
				$Total_val .= iif($Total_val,"＾","") . $Total_date . "|" . $Total_Pay;
		} 
		$Stmt = null;
#----------------------------------------------------------------------------------------------#
} else if ($work_sw==31) {    // 월간 일일 매출 저장하기
#----------------------------------------------------------------------------------------------#
        $Total_val = 0;
        $account__array = explode("＾",$account_val);

		for ($i=0; $i < count($account__array); $i++) {

               $account_subarray = explode("/",$account__array[$i]);
			   
			   $account_date     = $account_subarray[0];
			   $account_id       = $account_subarray[1];
			   $account_subid    = $account_subarray[2];
			   $account_name     = $account_subarray[3];
			   $account_subname  = $account_subarray[4];
			   $account_money    = $account_subarray[5];
			   $account_type     = 1;

               if ($account_date && $account_name && $account_subname && $account_money > 0) {

						$Sql = " insert into account_book ( ";
							$Sql .= " AccBookDate, ";
							$Sql .= " AccBookConfigID, ";
							$Sql .= " AccBookSubConfigID, ";
							$Sql .= " AccBookType, ";
							$Sql .= " AccBookSubject, ";
							$Sql .= " AccBookMoney,  ";
							$Sql .= " wdate ";
						$Sql .= " ) values ( ";
							$Sql .= " :AccBookDate, ";
							$Sql .= " :AccBookConfigID, ";
							$Sql .= " :AccBookSubConfigID, ";
							$Sql .= " :AccBookType, ";
							$Sql .= " :AccBookSubject, ";
							$Sql .= " :AccBookMoney,  ";
							$Sql .= " now() ";
						$Sql .= " ) ";

						$Stmt = $DbConn->prepare($Sql);
						$Stmt->bindParam(':AccBookDate',        $account_date);
						$Stmt->bindParam(':AccBookConfigID',    $account_id);
						$Stmt->bindParam(':AccBookSubConfigID', $account_subid);
						$Stmt->bindParam(':AccBookType',        $account_type);
						$Stmt->bindParam(':AccBookSubject',     $account_subname);
						$Stmt->bindParam(':AccBookMoney',       $account_money);
						$Stmt->execute();
						$Stmt = null;

						$Total_val++;
               
			   }

		}
#----------------------------------------------------------------------------------------------#
}
#----------------------------------------------------------------------------------------------#
include_once('../includes/dbclose.php');
#----------------------------------------------------------------------------------------------#
if ($Total_val) {
        $ArrValue = "9＾" . $Total_val; 
} else {
        $ArrValue = "1＾0"; 
}
#----------------------------------------------------------------------------------------------#
echo $ArrValue;
?>