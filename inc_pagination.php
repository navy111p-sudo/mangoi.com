<?php
$DisplayMaxPageCount = 10;
$PrevPageNum = $CurrentPage > 1 ? $CurrentPage - 1 : NULL;
$NextPageNum = $CurrentPage < $TotalPageCount ? $CurrentPage + 1 : NULL;
$StartPageNum = ( ceil( $CurrentPage / $DisplayMaxPageCount ) -1 ) * $DisplayMaxPageCount + 1;
$EndPageNum = $TotalPageCount >= $StartPageNum + $DisplayMaxPageCount ? $StartPageNum + $DisplayMaxPageCount -1 : $TotalPageCount;
?>


<div class="bbs_page">
	<?php
	if ($CurrentPage>1){
	?>
	<a href="?<?=$PaginationParam?>&CurrentPage=1" class="arrow_left_2"><img src="images/arrow_bbs_left_2.png"></a>
	<?php
	}else{
	?>
	<span class="arrow_left_2"><img src="images/arrow_bbs_left_2.png"></span>
	<?php
	}
	?>

			<?php
			if (floor(($CurrentPage-1)/$DisplayMaxPageCount)*$DisplayMaxPageCount>=1){
			?>
			<a href="?<?=$PaginationParam?>&CurrentPage=<?=floor(($CurrentPage-1)/$DisplayMaxPageCount)*$DisplayMaxPageCount?>" class="arrow_left_1"><img src="images/arrow_bbs_left_1.png"></a>
			<?php
			}else{
			?>
			<span class="arrow_left_1"><img src="images/arrow_bbs_left_1.png"></span>
			<?php
			}
			?>


					<!--
					<?php
					if ($PrevPageNum){
					?>
					<a href="?<?=$PaginationParam?>&CurrentPage=<?=$PrevPageNum?>"><</a>
					<?php
					}else{
					?>
					<span class="pad"><img src="images/arrow_bbs_left_1.png"></span>
					<?php
					}
					?>
					-->

					<?php
					for($i=$StartPageNum; $i<=$EndPageNum; $i++){
						if ($i==$CurrentPage) {
					?>
						<span class="active"><?=$i?></span>
					<?
						}else{
					?>
						<a href="?<?=$PaginationParam?>&CurrentPage=<?=$i?>"><?=$i?></a>
					<?
						}
					}
					?>	

					<!--
					<?php
					if ($NextPageNum){
					?>
					<a href="?<?=$PaginationParam?>&CurrentPage=<?=$NextPageNum?>">></a>
					<?php
					}else{
					?>
					<span class="pad"><img src="images/arrow_bbs_right_1.png"></span>
					<?php
					}
					?>
					-->


			<?php
			if (floor(($CurrentPage+$DisplayMaxPageCount-1)/$DisplayMaxPageCount)*$DisplayMaxPageCount+1<=$TotalPageCount){
			?>
			<a href="?<?=$PaginationParam?>&CurrentPage=<?=floor(($CurrentPage+$DisplayMaxPageCount-1)/$DisplayMaxPageCount)*$DisplayMaxPageCount+1?>" class="arrow_right_1"><img src="images/arrow_bbs_right_1.png"></a>
			<?php
			}else{
			?>
			<span class="arrow_right_1"><img src="images/arrow_bbs_right_1.png"></span>
			<?php
			}
			?>


	<?php
	if ($CurrentPage<$TotalPageCount){
	?>
	<a href="?<?=$PaginationParam?>&CurrentPage=<?=$TotalPageCount?>" class="arrow_right_2"><img src="images/arrow_bbs_right_2.png"></a>
	<?php
	}else{
	?>
	<span class="arrow_right_2"><img src="images/arrow_bbs_right_2.png"></span>
	<?php
	}
	?>
</div>

