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

$FavoriteMenuID = isset($_REQUEST["FavoriteMenuID"]) ? $_REQUEST["FavoriteMenuID"] : "";
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$FavoriteName = isset($_REQUEST["FavoriteName"]) ? $_REQUEST["FavoriteName"] : "";
$FavoriteUrl = isset($_REQUEST["FavoriteUrl"]) ? $_REQUEST["FavoriteUrl"] : "";
$FavoriteState = isset($_REQUEST["FavoriteState"]) ? $_REQUEST["FavoriteState"] : "";


if ($FavoriteMenuID==""){

	
	$Sql = "select ifnull(Max(FavoriteOrder),0) as FavoriteOrder from Favorites";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$FavoriteOrder = $Row["FavoriteOrder"]+1;


	$Sql = " insert into Favorites ( ";
		$Sql .= " MemberID, ";
		$Sql .= " FavoriteName, ";
		$Sql .= " FavoriteUrl, ";
		$Sql .= " FavoriteRegDateTime, ";
		$Sql .= " FavoriteModiDateTime, ";
		$Sql .= " FavoriteState, ";
		$Sql .= " FavoriteOrder ";
		
	$Sql .= " ) values ( ";
		$Sql .= " :MemberID, ";
		$Sql .= " :FavoriteName, ";
		$Sql .= " :FavoriteUrl, ";
		$Sql .= " now(), ";
		$Sql .= " now(), ";
		$Sql .= " 1, ";
		$Sql .= " :FavoriteOrder ";
	$Sql .= " ) ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->bindParam(':FavoriteName', $FavoriteName);
	$Stmt->bindParam(':FavoriteUrl', $FavoriteUrl);
	$Stmt->bindParam(':FavoriteOrder', $FavoriteOrder);
	$Stmt->execute();
	$Stmt = null;

}else{

	$Sql = " update Favorites set ";
		$Sql .= " FavoriteName = :FavoriteName, ";
		$Sql .= " FavoriteUrl = :FavoriteUrl, ";
		$Sql .= " FavoriteModiDateTime = now(), ";
		$Sql .= " FavoriteState = :FavoriteState ";
	$Sql .= " where FavoriteMenuID = :FavoriteMenuID ";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':FavoriteName', $FavoriteName);
	$Stmt->bindParam(':FavoriteUrl', $FavoriteUrl);
	$Stmt->bindParam(':FavoriteState', $FavoriteState);
	$Stmt->bindParam(':FavoriteMenuID', $FavoriteMenuID);
	$Stmt->execute();
	$Stmt = null;

}
?>


<?
include_once('./inc_common_form_js.php');
?>
<script type="text/javascript" src="js/common.js"></script>
<link rel="stylesheet" type="text/css" href="css/jquery-confirm.min.css" />
<script type="text/javascript" src="js/jquery-confirm.min.js"></script>
<script>
parent.$.fn.colorbox.close();
</script>
<?
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>

