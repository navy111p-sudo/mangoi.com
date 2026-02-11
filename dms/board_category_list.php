<?php
$top_menu_id = 2;
$left_menu_id = 2;
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
include_once('./_header.php');
?>

<style>
.content{width:100%; padding:0; margin:0;}
.table1{margin-top:20px; border-left:1px solid #ddd; border-top:1px solid #ddd;}
.table1 th{height:25px;}
.table1 td{background:#fff;}
</style>

<body>
<div class="content">
	<h2>게시판 카테고리 목록</h2>
	<div class="box">

		<?php

		$BoardID = isset($_REQUEST["BoardID"]) ? $_REQUEST["BoardID"] : "";


		$Sql = "select * from BoardCategories  where BoardID=$BoardID order by BoardCategoryOrder asc";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);

		?>


		<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table1">
		  <tr>
			<th width="11%">No</th>
			<th>카테고리명</th>
			<th width="16%">삭제</th>
		  </tr>



			<?php
			$ListCount = 1;
			while($Row = $Stmt->fetch()) {
				$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

			?>
			  <tr>
				<td><?=$ListCount?></td>
				<td class="subject"><a href="board_category_form.php?BoardCategoryID=<?=$Row["BoardCategoryID"]?>"><?=$Row["BoardCategoryName"]?></a></td>
				<td><a href="javascript:BoardCatetoryDel(<?=$Row["BoardID"]?>,<?=$Row["BoardCategoryID"]?>);"><img src="images/btn_del.png"></a></td>
			  </tr>
			<?php
				$ListCount ++;
			}
			$Stmt = null;
			?>

		</table>

		<div class="button" style="margin-top:15px;">
			<a href="board_category_form.php?BoardID=<?=$BoardID?>">등록하기</a>
		</div>

	</div>
</div>


<script>

function BoardCatetoryDel(BoardID, BoardCategoryID){
	if (confirm('삭제하시겠습니까?')){
		location.href = "board_category_del.php?BoardID="+BoardID+"&BoardCategoryID="+BoardCategoryID;
	}

}

</script>



</body>
<?php
include_once('./_footer.php');
include_once('../includes/dbclose.php');
?>