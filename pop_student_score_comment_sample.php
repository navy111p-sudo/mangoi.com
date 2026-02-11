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

<body>
<?
include_once('./includes/common_body_top.php');
?>
<?
$AssmtStudentCommentLevel = isset($_REQUEST["AssmtStudentCommentLevel"]) ? $_REQUEST["AssmtStudentCommentLevel"] : "";
$CommentSection = isset($_REQUEST["CommentSection"]) ? $_REQUEST["CommentSection"] : "";
?>

<div class="mantoman_write_wrap">
	<div class="mantoman_write_area" style="width:90%;">
		<h3 class="caption_underline TrnTag">코멘트 선택</h3>

		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<table class="level_reserve_table">
			<?
			if ($CommentSection=="Total"){
				$Sql4 = "select * from AssmtStudentComments where AssmtStudentCommentSection=100 order by AssmtStudentCommentID asc ";
			}else{
				$Sql4 = "select * from AssmtStudentComments where AssmtStudentCommentSection=$CommentSection and AssmtStudentCommentLevel=$AssmtStudentCommentLevel order by AssmtStudentCommentID asc";
			}
			$Stmt4 = $DbConn->prepare($Sql4);
			$Stmt4->execute();
			$Stmt4->setFetchMode(PDO::FETCH_ASSOC);

			$iiii=1;
			while($Row4 = $Stmt4->fetch()) {
			?>
			<tr>
				<td class="radio_wrap time" style="cursor:pointer;" onclick="SelectComment(<?=$iiii?>);">
					<span style="color:#566BA8;"><?=$Row4["AssmtStudentCommentEng"]?></span>
					<br>
					<?=$Row4["AssmtStudentCommentKor"]?>
					
					<textarea style="display:none;" name="AssmtStudentCommentEng_<?=$iiii?>" id="AssmtStudentCommentEng_<?=$iiii?>"><?=$Row4["AssmtStudentCommentEng"]?></textarea>
					<textarea style="display:none;" name="AssmtStudentCommentKor_<?=$iiii?>" id="AssmtStudentCommentKor_<?=$iiii?>"><?=$Row4["AssmtStudentCommentKor"]?></textarea>
				</td>
			</tr>
			<?
				$iiii++;
			}
			$Stmt4 = null;
			?>
			</tr>
		</table>
		</form>
		<div class="button_wrap">
			<a href="javascript:CloseForm();" class="button_br_black mantoman TrnTag" style="margin:0px auto;width:100%;">닫기</a>
		</div>
	</div>
</div>


<script language="javascript">
function CloseForm(){
	parent.$.fn.colorbox.close();
}

function SelectComment(ListNum){
	AssmtStudentCommentEng = document.getElementById("AssmtStudentCommentEng_"+ListNum).value;
	AssmtStudentCommentKor = document.getElementById("AssmtStudentCommentKor_"+ListNum).value;
	parent.RegForm.AssmtStudentMonthlyScoreComment<?=$CommentSection?>.value = AssmtStudentCommentEng +"\n"+ AssmtStudentCommentKor;
	parent.$.fn.colorbox.close();
}

</script>

</body>
</html>
<?php
include_once('./includes/dbclose.php');
?>