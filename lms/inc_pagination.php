<?php
$DisplayMaxPageCount = 10;
$PrevPageNum = $CurrentPage > 1 ? $CurrentPage - 1 : NULL;
$NextPageNum = $CurrentPage < $TotalPageCount ? $CurrentPage + 1 : NULL;
$StartPageNum = ( ceil( $CurrentPage / $DisplayMaxPageCount ) -1 ) * $DisplayMaxPageCount + 1;
$EndPageNum = $TotalPageCount >= $StartPageNum + $DisplayMaxPageCount ? $StartPageNum + $DisplayMaxPageCount -1 : $TotalPageCount;
?>

<ul class="uk-pagination uk-margin-medium-top uk-margin-medium-bottom">
	<?if ($CurrentPage>1){?>
	<li><a href="?<?=$PaginationParam?>&CurrentPage=1"><i class="uk-icon-angle-double-left"></i></a></li>
	<?}else{?>
	<li class="uk-disabled"><span><i class="uk-icon-angle-double-left"></i></span></li>
	<?}?>

			<?if (floor(($CurrentPage-1)/$DisplayMaxPageCount)*$DisplayMaxPageCount>=1){?>
			<li><a href="?<?=$PaginationParam?>&CurrentPage=<?=floor(($CurrentPage-1)/$DisplayMaxPageCount)*$DisplayMaxPageCount?>"><i class="uk-icon-angle-left"></i></a></li>
			<?}else{?>
			<li class="uk-disabled"><span><i class="uk-icon-angle-left"></i></span></li>
			<?}?>


						<?
						if ($EndPageNum==0){
						?>
							<li class="uk-active"><span>1</span></li>
						<?
						}else{
							for($i=$StartPageNum; $i<=$EndPageNum; $i++){
								
								if ($i==$CurrentPage) {
						?>
									<li class="uk-active"><span><?=$i?></span></li>
						<?		
								}else{
						?>
									<li><a href="?<?=$PaginationParam?>&CurrentPage=<?=$i?>" class="number <?if ($i==$CurrentPage) {?>active<?}?>"><?=$i?></a></li>
						<?
								}

							}
						}
						?>


			<?php
			if (floor(($CurrentPage+$DisplayMaxPageCount-1)/$DisplayMaxPageCount)*$DisplayMaxPageCount+1<=$TotalPageCount){
			?>
			<li><a href="?<?=$PaginationParam?>&CurrentPage=<?=floor(($CurrentPage+$DisplayMaxPageCount-1)/$DisplayMaxPageCount)*$DisplayMaxPageCount+1?>"><i class="uk-icon-angle-right"></i></a></li>
			<?php
			}else{
			?>
			<li class="uk-disabled"><span><i class="uk-icon-angle-right"></i></span></li>
			<?php
			}
			?>

	<?php
	if ($CurrentPage<$TotalPageCount){
	?>
	<li><a href="?<?=$PaginationParam?>&CurrentPage=<?=$TotalPageCount?>"><i class="uk-icon-angle-double-right"></i></a></li>
	<?php
	}else{
	?>
	<li class="uk-disabled"><span><i class="uk-icon-angle-double-right"></i></span></li>
	<?php
	}
	?>
</ul>

