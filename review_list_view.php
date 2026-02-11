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
<link href="css/sub_style.css?ver=1" rel="stylesheet" type="text/css" />
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
$adminView = isset($_REQUEST["adminView"]) ? $_REQUEST["adminView"] : "";


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


$Sql = "SELECT A.*, B.MemberName 
		from reviews A
		LEFT JOIN Members B ON A.memberID = B.MemberID 
		where A.TeacherID=:TeacherID ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':TeacherID', $TeacherID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$reviews = $Stmt->fetchAll(PDO::FETCH_ASSOC);


$Stmt = $DbConn->prepare('SELECT AVG(rating) AS overall_rating, COUNT(*) AS total_reviews FROM reviews WHERE teacherID = ?');
$Stmt->execute([$TeacherID]);
$reviews_info = $Stmt->fetch(PDO::FETCH_ASSOC);

?>

<div class="mantoman_write_wrap">
	<div class="mantoman_write_area">
		<h3 class="caption_underline TrnTag">리뷰 보기</h3>

		<form id="RegForm" name="RegForm" method="post" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="MemberID" value="<?=$MemberID?>">
		<input type="hidden" name="TeacherID" value="<?=$TeacherID?>">
		<div class="reviews">
		<h2 >강사 : <?=$TeacherName?></h2>
		
		<div class="overall_rating">
			<span class="num"><?=number_format($reviews_info['overall_rating'], 1)?></span>
			<span class="stars"><?=str_repeat('&#9733;', round($reviews_info['overall_rating']))?></span>
			<span class="total"><?=$reviews_info['total_reviews']?> reviews</span>
		</div>
		
		<?php 
		if (empty($reviews) ){
		?>	
			<div class="review " style="text-align:center;font-size:16px">
				<br><br><br>
				리뷰가 아직 등록되지 않았습니다.~
				<br><br><br>
			</div>
		<?php
		} else {
			foreach ($reviews as $review): ?>
				<div class="review">
					<h3 class="name"><?
						if ($adminView == 1){
							echo htmlspecialchars($review['MemberName'], ENT_QUOTES);
						} else {
							echo preg_replace('/.(?=.$)/u','*',htmlspecialchars($review['MemberName'], ENT_QUOTES));
						}
					 	?>
					</h3>
					<div>
						<span class="rating"><?=str_repeat('&#9733;', $review['rating'])?></span>
						<span class="date"><?=$review['submit_date']?></span>
					</div>
					<p class="content"><?=htmlspecialchars($review['content'], ENT_QUOTES)?></p>
				</div>
		<?php 
			endforeach;
		}
		?>
		</div>
		</form>
		<div style="text-align:center">
			<a href="javascript:parent.$.fn.colorbox.close();" class="button_br_black mantoman TrnTag">닫기</a>
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