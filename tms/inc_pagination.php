<?php
$DisplayMaxPageCount = 10;
//$PrevPageNum = $CurrentPage > 1 ? $CurrentPage - 1 : NULL;
//$NextPageNum = $CurrentPage < $TotalPageCount ? $CurrentPage + 1 : NULL;
$StartPageNum = ( ceil( $CurrentPage / $DisplayMaxPageCount ) -1 ) * $DisplayMaxPageCount + 1;
$EndPageNum = $TotalPageCount >= $StartPageNum + $DisplayMaxPageCount ? $StartPageNum + $DisplayMaxPageCount -1 : $TotalPageCount;
?>
<div class="pagenation">
	<?php
	if ($CurrentPage>1){
	?>
	<a href="?<?=$PaginationParam?>&CurrentPage=1"><<</a>
	<?php
	}else{
	?>
	<span><<</span>
	<?php
	}
	?>

		<?php
		if (floor(($CurrentPage-1)/$DisplayMaxPageCount)*$DisplayMaxPageCount>=1){
		?>
		<a href="?<?=$PaginationParam?>&CurrentPage=<?=floor(($CurrentPage-1)/$DisplayMaxPageCount)*$DisplayMaxPageCount?>"><</a>
		<?php
		}else{
		?>
		<span><</span>
		<?php
		}
		?>


			<?php
			if ($EndPageNum==0){
			?>
				<span class="number">1</span>
			<?
			}else{
				for($i=$StartPageNum; $i<=$EndPageNum; $i++){
			?>
				<a  href="?<?=$PaginationParam?>&CurrentPage=<?=$i?>" class="number <?if ($i==$CurrentPage) {?>active<?}?>"><?=$i?></a>
			<?php
				}
			}
			?>	


		<?php
		if (floor(($CurrentPage+$DisplayMaxPageCount-1)/$DisplayMaxPageCount)*$DisplayMaxPageCount+1<=$TotalPageCount){
		?>
		<a href="?<?=$PaginationParam?>&CurrentPage=<?=floor(($CurrentPage+$DisplayMaxPageCount-1)/$DisplayMaxPageCount)*$DisplayMaxPageCount+1?>">></a>
		<?php
		}else{
		?>
		<span>></span>
		<?php
		}
		?>


	<?php
	if ($CurrentPage<$TotalPageCount){
	?>
	<a href="?<?=$PaginationParam?>&CurrentPage=<?=$TotalPageCount?>">>></a>
	<?php
	}else{
	?>
	<span>>></span>
	<?php
	}
	?>
</div>