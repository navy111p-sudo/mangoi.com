<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
include_once('./includes/member_check.php');
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />

<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" id="viewport" />
<link href="css/common.css" rel="stylesheet" type="text/css" />
<link href="css/sub_style.css" rel="stylesheet" type="text/css" />
<script src="js/jquery-2.2.4.min.js"></script>
<script src="js/common.js"></script>
<style> 
.endline{page-break-before:always}
</style>
<body>
<?
include_once('./includes/common_body_top.php');
?>
<?
$MemberID = $_LINK_MEMBER_ID_;

$BookScanID = isset($_REQUEST["BookScanID"]) ? $_REQUEST["BookScanID"] : "";
$ClassID = isset($_REQUEST["ClassID"]) ? $_REQUEST["ClassID"] : "";


//================= 로그 ======================
$Sql2 = "insert into ClassBookScanViewLogs (
				ClassID,
				ClassBookScanViewLogDateTime

	) values (
				:ClassID,
				now()
	)";

$Stmt2 = $DbConn->prepare($Sql2);
$Stmt2->bindParam(':ClassID', $ClassID);
$Stmt2->execute();
$Stmt2 = null;
//================= 로그 ======================
?>

<div class="mantoman_write_wrap">
	<div class="mantoman_write_area" style="width:90%;">
		<h3 class="caption_underline TrnTag">교재 열람</h3>

		<?
		$Sql = "select 
					A.* 	
				from BookScans A
				where 
					A.BookScanID=:BookScanID 
					and A.BookScanView=1 
					and A.BookScanState=1 
				order by A.BookScanOrder asc";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':BookScanID', $BookScanID);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);

		$ii=1;
		while($Row = $Stmt->fetch()) {

		$BookScanName = $Row["BookScanName"];
		$BookScanImageFileName = $Row["BookScanImageFileName"];
		?>
		
		<?if ($ii>1){?>
		<!--<div class="endline"></div><br style="height:0; line-height:0">-->
		<?}?>
		<table class="level_reserve_table" style="margin-bottom:20px;">
			<tr>
				<th>
				<?=$BookScanName?>
				</th>
			</tr>
			<tr>
				<td style="height:750px;text-align:center;"><img src="./uploads/book_scan_images/<?=$BookScanImageFileName?>" style="width:90%;"></td>
			</tr>		
		</table>
		<?
			$ii++;
		}
		$Stmt = null;
		?>

		<div class="button_wrap flex_justify" id="DivBtn">
			<a href="javascript:PagePrint();" class="button_orange_white mantoman TrnTag">인쇄하기</a>
			<a href="javascript:parent.$.fn.colorbox.close();" class="button_br_black mantoman TrnTag">닫기</a>
		</div>
	</div>
</div>


<script>
function PagePrint(){
	document.getElementById("DivBtn").style.display = "none";
	setTimeout(PagePrintAction, 1000);
}

function PagePrintAction(){
	print();
	document.getElementById("DivBtn").style.display = "";
}
</script>

</body>
</html>
<?php
include_once('./includes/dbclose.php');
?>