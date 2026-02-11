<?php
$DisplayMaxPageCount = 10;
$PrevPageNum = $CurrentPage > 1 ? $CurrentPage - 1 : NULL;
$NextPageNum = $CurrentPage < $TotalPageCount ? $CurrentPage + 1 : NULL;
$StartPageNum = ( ceil( $CurrentPage / $DisplayMaxPageCount ) -1 ) * $DisplayMaxPageCount + 1;
$EndPageNum = $TotalPageCount >= $StartPageNum + $DisplayMaxPageCount ? $StartPageNum + $DisplayMaxPageCount -1 : $TotalPageCount;
?>
<div class="number">
	<ul>
		<?php
		if ($PrevPageNum){
		?>
		<li><a href="?<?=$PaginationParam?>&CurrentPage=<?=$PrevPageNum?>">&laquo; prev</a></li>
		<?php
		}else{
		?>
		<li class="disabled">&laquo; prev</li>
		<?php
		}
		?>
		<?php
		for($i=$StartPageNum; $i<=$EndPageNum; $i++){
			if ($i==$CurrentPage){
		?>
		<li class='current'><?=$i?></li>
		<?php
			}else{
		?>
		<li><a href="?<?=$PaginationParam?>&CurrentPage=<?=$i?>"><?=$i?></a></li>
		<?php
			}
		}
		?>
		<?php
		if ($NextPageNum){
		?>
		<li><a href="?<?=$PaginationParam?>&CurrentPage=<?=$NextPageNum?>">next &raquo;</a></li>
		<?php
		}else{
		?>
		<li class="disabled">next &raquo;</li>
		<?php
		}
		?>
	</ul>
</div>