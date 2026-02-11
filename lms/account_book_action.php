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
?>
<link rel="stylesheet" type="text/css" href="css/common.css" />
</head>
</body>
<?
#------------------------------------------------------------------------------------------------------#
$err_num = 0;
$err_msg = "";
#------------------------------------------------------------------------------------------------------#
$WorkSW           = isset($_REQUEST["WorkSW"          ]) ? $_REQUEST["WorkSW"          ] : "";
$edit_mode        = isset($_REQUEST["edit_mode"       ]) ? $_REQUEST["edit_mode"       ] : "";
$edit_accid       = isset($_REQUEST["edit_accid"      ]) ? $_REQUEST["edit_accid"      ] : "";
$YearNumber       = isset($_REQUEST["YearNumber"      ]) ? $_REQUEST["YearNumber"      ] : "";
$MonthNumber      = isset($_REQUEST["MonthNumber"     ]) ? $_REQUEST["MonthNumber"     ] : "";
$DayNumber        = isset($_REQUEST["DayNumber"       ]) ? $_REQUEST["DayNumber"       ] : "";
$MakeDate         = $YearNumber . "-" . iif($MonthNumber < 10,"0","") . $MonthNumber . "-" . iif($DayNumber < 10,"0","") . $DayNumber;
#------------------------------------------------------------------------------------------------------#
$account_type     = isset($_REQUEST["account_type"    ]) ? $_REQUEST["account_type"    ] : "";
$account_id       = isset($_REQUEST["account_id"      ]) ? $_REQUEST["account_id"      ] : "";
$account_subid    = isset($_REQUEST["account_subid"   ]) ? $_REQUEST["account_subid"   ] : "";
$account_subname  = isset($_REQUEST["account_subname" ]) ? $_REQUEST["account_subname" ] : "";
$account_money    = isset($_REQUEST["account_money"   ]) ? $_REQUEST["account_money"   ] : "";
$account_money    = str_replace(',' , '', $account_money);
#------------------------------------------------------------------------------------------------------#
if ($edit_accid && $edit_mode==1) {           // 수정
#------------------------------------------------------------------------------------------------------#
			$Sql = " update account_book set ";
				$Sql .= " AccBookConfigID=:AccBookConfigID, ";
				$Sql .= " AccBookSubConfigID=:AccBookSubConfigID, ";
				$Sql .= " AccBookSubject=:AccBookSubject, ";
				$Sql .= " AccBookMoney=:AccBookMoney  ";
			$Sql .= " where AccBookID=:AccBookID ";

			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':AccBookConfigID',    $account_id);
			$Stmt->bindParam(':AccBookSubConfigID', $account_subid);
			$Stmt->bindParam(':AccBookSubject',     $account_subname);
			$Stmt->bindParam(':AccBookMoney',       $account_money);
			$Stmt->bindParam(':AccBookID',          $edit_accid); 
			$Stmt->execute();
			$Stmt = null;
#------------------------------------------------------------------------------------------------------#
} else if ($edit_accid && $edit_mode==2) {    // 삭제
#------------------------------------------------------------------------------------------------------#
			$Sql = " delete from account_book where AccBookID=:AccBookID ";

			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':AccBookID', $edit_accid); 
			$Stmt->execute();
			$Stmt = null;
#------------------------------------------------------------------------------------------------------#
} else {
#------------------------------------------------------------------------------------------------------#
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
			$Stmt->bindParam(':AccBookDate',        $MakeDate);
			$Stmt->bindParam(':AccBookConfigID',    $account_id);
			$Stmt->bindParam(':AccBookSubConfigID', $account_subid);
			$Stmt->bindParam(':AccBookType',        $account_type);
			$Stmt->bindParam(':AccBookSubject',     $account_subname);
			$Stmt->bindParam(':AccBookMoney',       $account_money);
			$Stmt->execute();
			$Stmt = null;
#------------------------------------------------------------------------------------------------------#
}
#------------------------------------------------------------------------------------------------------#
?>


<?
include_once('./inc_common_form_js.php');
?>
<script type="text/javascript" src="js/common.js"></script>
<link rel="stylesheet" type="text/css" href="css/jquery-confirm.min.css" />
<script type="text/javascript" src="js/jquery-confirm.min.js"></script>

<script>
var YearNumber  = '<?=$YearNumber?>';
var MonthNumber = '<?=$MonthNumber?>';
var DayNumber   = '<?=$DayNumber?>';
var WorkSW      = '<?=$WorkSW?>';

location.href  = "account_book_form.php?WorkSW="+WorkSW+"&YearNumber=" + YearNumber + "&MonthNumber=" + MonthNumber + "&DayNumber=" + DayNumber;

</script>
<?
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>

