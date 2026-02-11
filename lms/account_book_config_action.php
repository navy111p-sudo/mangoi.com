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

$err_num = 0;
$err_msg = "";

$edit_mode        = isset($_REQUEST["edit_mode"       ]) ? $_REQUEST["edit_mode"       ] : "";
$Oldacc_type      = isset($_REQUEST["Oldacc_type"     ]) ? $_REQUEST["Oldacc_type"     ] : "";
$acc_type         = isset($_REQUEST["acc_type"        ]) ? $_REQUEST["acc_type"        ] : "";
$Oldaccount_id    = isset($_REQUEST["Oldaccount_id"   ]) ? $_REQUEST["Oldaccount_id"   ] : "";
$account_id       = isset($_REQUEST["account_id"      ]) ? $_REQUEST["account_id"      ] : "";
$account_type     = isset($_REQUEST["account_type"    ]) ? $_REQUEST["account_type"    ] : "";
$account_name     = isset($_REQUEST["account_name"    ]) ? $_REQUEST["account_name"    ] : "";
$account_subid    = isset($_REQUEST["account_subid"   ]) ? $_REQUEST["account_subid"   ] : "";
$account_subname  = isset($_REQUEST["account_subname" ]) ? $_REQUEST["account_subname" ] : "";
$AccBookConfigSubType  = isset($_REQUEST["AccBookConfigSubType" ]) ? $_REQUEST["AccBookConfigSubType" ] : "";
#------------------------------------------------------------------------------------------------------#
if ($edit_mode < 3) {
#------------------------------------------------------------------------------------------------------#

		if ($account_id && $edit_mode==1) {           // 수정

			$Sql = "UPDATE account_bookconfig set ";
				$Sql .= " AccBookConfigType = :AccBookConfigType, ";
				$Sql .= " AccBookConfigSubType = :AccBookConfigSubType, ";
				$Sql .= " AccBookConfigName = :AccBookConfigName ";
			$Sql .= " where AccBookConfigID = :AccBookConfigID ";

			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':AccBookConfigType', $account_type);
			$Stmt->bindParam(':AccBookConfigSubType', $AccBookConfigSubType);
			$Stmt->bindParam(':AccBookConfigName', $account_name);
			$Stmt->bindParam(':AccBookConfigID',   $account_id); 
			$Stmt->execute();
			$Stmt = null;

		} else if ($account_id && $edit_mode==2) {    // 삭제

			$Sql = " delete from account_bookconfig where AccBookConfigID = :AccBookConfigID ";

			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':AccBookConfigID',   $account_id); 
			$Stmt->execute();
			$Stmt = null;

		} else {

			if ($account_name!=""){

					$Sql = "SELECT * from account_bookconfig where AccBookConfigName=:AccBookConfigName"; 
					 
					$Stmt = $DbConn->prepare($Sql);
					$Stmt->bindParam(':AccBookConfigName', $account_name);
					$Stmt->execute();
					$Stmt->setFetchMode(PDO::FETCH_ASSOC);
					$Row = $Stmt->fetch();
					$Stmt = null;
					$AccBookConfigName = $Row["AccBookConfigName"];

					if (!$AccBookConfigName || $AccBookConfigName!=$account_name) {

							$Sql = " insert into account_bookconfig ( ";
								$Sql .= " AccBookConfigType, ";
								$Sql .= " AccBookConfigSubType, ";
								$Sql .= " AccBookConfigName, ";
								$Sql .= " wdate ";
							$Sql .= " ) values ( ";
								$Sql .= " :AccBookConfigType, ";
								$Sql .= " :AccBookConfigSubType, ";
								$Sql .= " :AccBookConfigName, ";
								$Sql .= " now() ";
							$Sql .= " ) ";

							$Stmt = $DbConn->prepare($Sql);
							$Stmt->bindParam(':AccBookConfigType', $account_type);
							$Stmt->bindParam(':AccBookConfigSubType', $AccBookConfigSubType);
							$Stmt->bindParam(':AccBookConfigName', $account_name);
							$Stmt->execute();
							$Stmt = null;

					}

			 }

		}

#------------------------------------------------------------------------------------------------------#
} else if ($edit_mode > 30) {
#------------------------------------------------------------------------------------------------------#

		if ($account_subid && $edit_mode==31) {           // 수정

			$Sql = " update account_booksubconfig set ";
				$Sql .= " AccBookSubConfigName = :AccBookSubConfigName ";
			$Sql .= " where AccBookSubConfigID = :AccBookSubConfigID ";

			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':AccBookSubConfigName', $account_subname);
			$Stmt->bindParam(':AccBookSubConfigID',   $account_subid); 
			$Stmt->execute();
			$Stmt = null;

		} else if ($account_subid && $edit_mode==32) {    // 삭제

			$Sql = " delete from account_booksubconfig where AccBookSubConfigID = :AccBookSubConfigID ";

			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':AccBookSubConfigID', $account_subid); 
			$Stmt->execute();
			$Stmt = null;

		} else {

			if ($account_subname!=""){

					$Sql = "select * from account_booksubconfig where AccBookSubConfigName=:AccBookSubConfigName"; 
					 
					$Stmt = $DbConn->prepare($Sql);
					$Stmt->bindParam(':AccBookSubConfigName', $account_subname);
					$Stmt->execute();
					$Stmt->setFetchMode(PDO::FETCH_ASSOC);
					$Row = $Stmt->fetch();
					$Stmt = null;

					$AccBookSubConfigName = $Row["AccBookSubConfigName"];

					if (!$AccBookSubConfigName || $AccBookSubConfigName!=$account_subname) {

							$Sql = " insert into account_booksubconfig ( ";
								$Sql .= " AccBookConfigID, ";
								$Sql .= " AccBookSubConfigName, ";
								$Sql .= " wdate ";
							$Sql .= " ) values ( ";
								$Sql .= " :AccBookConfigID, ";
								$Sql .= " :AccBookSubConfigName, ";
								$Sql .= " now() ";
							$Sql .= " ) ";

							$Stmt = $DbConn->prepare($Sql);
							$Stmt->bindParam(':AccBookConfigID',      $account_id);
							$Stmt->bindParam(':AccBookSubConfigName', $account_subname);
							$Stmt->execute();
							$Stmt = null;

					}

			 }

		}


#------------------------------------------------------------------------------------------------------#
}
#------------------------------------------------------------------------------------------------------#
//echo $edit_mode . "/" . $account_id . "/" . $account_subid;
?>


<?
include_once('./inc_common_form_js.php');
?>
<script type="text/javascript" src="js/common.js"></script>
<link rel="stylesheet" type="text/css" href="css/jquery-confirm.min.css" />
<script type="text/javascript" src="js/jquery-confirm.min.js"></script>
<script>
var edit_mode  = '<?=$edit_mode?>';
var account_id = '<?=$Oldaccount_id?>';
var acc_type   = '<?=$Oldacc_type?>';
if (edit_mode > 30) {
       location.href  = "account_book_config.php?edit_mode=3&account_id=" + account_id + "&acc_type=" + acc_type;
} else {
       location.href  = "account_book_config.php";
}
</script>
<?
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>

