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
/* 별점용 css */

.star-rating {
  border:solid 0
    px #ccc;
  display:flex;
  flex-direction: row-reverse;
  font-size:1.5em;
  justify-content:space-around;
  padding:0 .2em;
  text-align:center;
  width:5em;
}

.star-rating input {
  display:none;
}

.star-rating label {
  color:#ccc;
  cursor:pointer;
}

.star-rating :checked ~ label {
  color:#f90;
}

.star-rating label:hover,
.star-rating label:hover ~ label {
  color:#fc0;
}

/* explanation */

article {
  background-color:#ffe;
  box-shadow:0 0 1em 1px rgba(0,0,0,.25);
  color:#006;
  font-family:cursive;
  font-style:italic;
  margin:4em;
  max-width:30em;
  padding:2em;
}

</style>

<body>
<?
include_once('./includes/common_body_top.php');
?>
<?
$MemberID = $_LINK_MEMBER_ID_;

$TeacherID = isset($_REQUEST["TeacherID"]) ? $_REQUEST["TeacherID"] : "";
$isRedirect = isset($_REQUEST["isRedirect"]) ? $_REQUEST["isRedirect"] : "";


$Sql = "SELECT  A.MemberName
		from Members A 
		where A.MemberID=:MemberID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $MemberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$MemberName = $Row["MemberName"];


$Sql = "SELECT A.TeacherName
		from Teachers A 
		where A.TeacherID=:TeacherID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':TeacherID', $TeacherID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$TeacherName = $Row["TeacherName"];
?>

<div class="mantoman_write_wrap">
	<div class="mantoman_write_area">
		<h3 class="caption_underline TrnTag">리뷰 작성하기</h3>

		<form id="RegForm" name="RegForm" method="post" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="MemberID" value="<?=$MemberID?>">
		<input type="hidden" name="TeacherID" value="<?=$TeacherID?>">
		<input type="hidden" name="isRedirect" value="<?=$isRedirect?>">
		<table class="level_reserve_table">           
			<tr>
				<th>작성자</th>
				<td><?=$MemberName?></td>
			</tr>
			<tr>
				<th>강사</th>
				<td><?=$TeacherName?></td>
			</tr>
			<tr>
				<th>점수</th>
				<td>
					<div class="star-rating">
						<input type="radio" id="5-stars" name="rating" value="5" />
						<label for="5-stars" class="star">&#9733;</label>
						<input type="radio" id="4-stars" name="rating" value="4" />
						<label for="4-stars" class="star">&#9733;</label>
						<input type="radio" id="3-stars" name="rating" value="3" />
						<label for="3-stars" class="star">&#9733;</label>
						<input type="radio" id="2-stars" name="rating" value="2" />
						<label for="2-stars" class="star">&#9733;</label>
						<input type="radio" id="1-star" name="rating" value="1" />
						<label for="1-star" class="star">&#9733;</label>
					</div>
				</td>
			</tr>			
			<tr>
				<th>리뷰 내용</th>
				<td>
					<textarea id="content" name="content" style="width:400px;height:150px;"></textarea>
				</td>
			</tr>
		</table>
		</form>
		<div class="button_wrap flex_justify">
			<a href="javascript:FormSubmit();" class="button_orange_white mantoman TrnTag">작성하기</a>
			<a href="javascript:parent.$.fn.colorbox.close();" class="button_br_black mantoman TrnTag">취소하기</a>
		</div>
	</div>
</div>



<script language="javascript">
function FormSubmit(){

	obj = document.RegForm.content;
	if (obj.value==""){
		alert('리뷰 내용을 입력해 주세요.');
		return;
	}

	obj = document.RegForm.rating;
	if (obj.value==""){
		alert('점수를 입력해 주세요.');
		return;
	}

	AlertMsg = "리뷰를 등록하시겠습니까?";

	if (confirm(AlertMsg)){
		document.RegForm.action = "review_write_action.php"
		document.RegForm.submit();
	}

}

</script>




</body>
</html>
<?php
include_once('./includes/dbclose.php');
?>