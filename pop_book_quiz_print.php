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

$BookQuizID = isset($_REQUEST["BookQuizID"]) ? $_REQUEST["BookQuizID"] : "";
?>

<div class="mantoman_write_wrap">
	<div class="mantoman_write_area" style="width:90%;">
		<h3 class="caption_underline">리뷰 퀴즈</h3>

		<?
		$Sql = "select 
					A.* 	
				from BookQuizDetails A
				where 
					A.BookQuizID=:BookQuizID 
					and A.BookQuizDetailView=1 
					and A.BookQuizDetailState=1 
				order by A.BookQuizDetailOrder asc";

		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':BookQuizID', $BookQuizID);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);

		$ii=1;
		while($Row = $Stmt->fetch()) {

		$BookQuizDetailText = $Row["BookQuizDetailText"];
		$BookQuizDetailImageFileName = $Row["BookQuizDetailImageFileName"];

		$BookQuizDetailChoice1 = $Row["BookQuizDetailChoice1"];
		$BookQuizDetailChoice2 = $Row["BookQuizDetailChoice2"];
		$BookQuizDetailChoice3 = $Row["BookQuizDetailChoice3"];
		$BookQuizDetailChoice4 = $Row["BookQuizDetailChoice4"];
		?>
		
		<?if ($ii>1){?>
		<div class="endline"></div><br style="height:0; line-height:0">
		<?}?>
		<table class="level_reserve_table" style="margin-bottom:20px;">
			<tr>
				<th style="text-align:left;padding:20px;font-size:20px;">
				<?=$ii?>. <?=$BookQuizDetailText?>
				</th>
			</tr>
			<?if ($BookQuizDetailImageFileName!=""){?>
			<tr>
				<td style="height:450px;text-align:center;">
					<img src="./uploads/book_quiz_images/<?=$BookQuizDetailImageFileName?>" style="width:400px;">
				</td>
			</tr>
			<?}?>
			
			<tr>
				<td style="height:250px;text-align:center;">
					<?if ($BookQuizDetailChoice1!=""){?>
					<div style="text-align:left;font-size:20px;margin-top:20px;">1) <?=$BookQuizDetailChoice1?></div>
					<?}?>
					<?if ($BookQuizDetailChoice2!=""){?>
					<div style="text-align:left;font-size:20px;margin-top:20px;">2) <?=$BookQuizDetailChoice2?></div>
					<?}?>
					<?if ($BookQuizDetailChoice3!=""){?>
					<div style="text-align:left;font-size:20px;margin-top:20px;">3) <?=$BookQuizDetailChoice3?></div>
					<?}?>
					<?if ($BookQuizDetailChoice4!=""){?>
					<div style="text-align:left;font-size:20px;margin-top:20px;">4) <?=$BookQuizDetailChoice4?></div>
					<?}?>
				</td>
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