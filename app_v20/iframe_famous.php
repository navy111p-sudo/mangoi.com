<?
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Untitled Document</title>
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
</head>

<body>

<div class="swiper-container">
    <div class="swiper-wrapper main_famous_text">
        <?
		$Sql = "select * from FamousTexts order by rand() limit 0, 5";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		while($Row = $Stmt->fetch()) {
			$FamousTextEng = $Row["FamousTextEng"];
			$FamousTextKor = $Row["FamousTextKor"];
			$FamousTextWriter = $Row["FamousTextWriter"];
		?>
		<div class="swiper-slide" style="color:#4b1c7a; font-weight:500;"><?=$FamousTextEng?></div>
        <div class="swiper-slide">
            <?=$FamousTextKor?>
            <span class="main_famous_name">- <?=$FamousTextWriter?> -</span>
        </div>
        <?
		}
		$Stmt = null;
		?>
		<!--
		<div class="swiper-slide">Stay hungry, Stay foolish</div>
        <div class="swiper-slide">
            항상 굶주리고 배고파 하라
            <span class="main_famous_name">- Stever Jobs -</span>
        </div>
        
		<div class="swiper-slide">Every child is an artist. The problem is how to remain an artist once he grows up</div>
        <div class="swiper-slide">
            모든 아이들은 예술가이다, 문제는 성인이 되었을 때 어떻게 예술가로 남는 가이다.
            <span class="main_famous_name">- Pablo Picasso -</span>
        </div>
		-->
    </div>
</div>

</body>
<link rel="stylesheet" href="js/swiper_famous/swiper.css">
<script type="text/javascript" src="js/swiper_famous/swiper.js"></script>
<script>
var swiper = new Swiper('.swiper-container', {
    spaceBetween: 0,
    centeredSlides: true,
    loop: true,
    autoplay: {
    delay: 3000,
    disableOnInteraction: false,
    },
    navigation: {
    nextEl: '.swiper-button-next',
    prevEl: '.swiper-button-prev',
    },
});
</script>
</html>
<?
include_once('../includes/dbclose.php');
?>