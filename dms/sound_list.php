<?php
$top_menu_id = 6;
$left_menu_id = 2;
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
include_once('./_header.php');
?>
<body>
<?php
include_once('./_top.php');
?>
<div id="content">
	<?php
	include_once('./_left.php');
	?>
	<div id="right">
		<div class="box">
			<div class="title">
				<h5>공지사항</h5>
				<div class="search">
					<form action="#" method="post">
						<div class="input">
							<input type="text" id="search" name="search" />
						</div>
						<div class="button">
							<input type="submit" name="submit" value="Search" />
						</div>
					</form>
				</div>
			</div>
			<div class="table">
				<?php

				$PaginationParam = "1=1";

				//총게시글 수
				$Sql = "select count(*) as TotalRowCount from MemberLevels";
				$Stmt = $DbConn->prepare($Sql);
				$Stmt->execute();
				$Stmt->setFetchMode(PDO::FETCH_ASSOC);
				$Row = $Stmt->fetch();
				$Stmt = null;

				$TotalRowCount = $Row["TotalRowCount"];

				//게시글 사용되는 변수
				$CurrentPage = isset($_REQUEST["CurrentPage"]) ? $_REQUEST["CurrentPage"] : "";
				$PageListNum = isset($_REQUEST["PageListNum"]) ? $_REQUEST["PageListNum"] : "";

				if (!$CurrentPage){
					$CurrentPage = 1;	
				}
				if (!$PageListNum){
					$PageListNum = 5;
				}
				$DisplayMaxPageCount = 10;

				$TotalPageCount = ceil($TotalRowCount / $PageListNum);
				$StartRowNum = $PageListNum * ($CurrentPage - 1 );
				$PrevPageNum = $CurrentPage > 1 ? $CurrentPage - 1 : NULL;
				$NextPageNum = $CurrentPage < $TotalPageCount ? $CurrentPage + 1 : NULL;
				$StartPageNum = ( ceil( $CurrentPage / $DisplayMaxPageCount ) -1 ) * $DisplayMaxPageCount + 1;
				$EndPageNum = $TotalPageCount >= $StartPageNum + $DisplayMaxPageCount ? $StartPageNum + $DisplayMaxPageCount -1 : $TotalPageCount;
				

				$Sql = "select * from MemberLevels limit :StartRowNum, :PageListNum";
				$Stmt = $DbConn->prepare($Sql);
				$Stmt->bindParam(':StartRowNum', $StartRowNum);
				$Stmt->bindParam(':PageListNum', $PageListNum);
				$Stmt->execute();
				$Stmt->setFetchMode(PDO::FETCH_ASSOC);

				

				?>
				<form action="" method="post">
				<table>
					<thead>
						<tr>
							<th>No</th>
							<th>제목</th>
							<th>작성자</th>
							<th>작성일</th>		
							<th>조회</th>								
						</tr>
					</thead>
					<tbody>
				<?php
				while($Row = $Stmt->fetch()) {
				
					//list( $MemberLevelID, $MemberLevelName) = $Row;
				?>

						<tr>
							<td><?=$Row[1]?></td>
							<td><a href="#"><?=$MemberLevelID?></a></td>
							<td><?=$MemberLevelName?></td>
							<td><?=$Row["MemberLevelName"]?></td>
							<td>11</td>
						</tr>
				<?php
				}
				$Stmt = null;
				?>
						
					</tbody>
				</table>
				<?php
				include_once('./include_pagination.php');
				?>
				</form>
			</div>
		</div>
	</div>
</div>
<?php
include_once('./_bottom.php');
?>
</body>
<?php
include_once('./_footer.php');
include_once('../includes/dbclose.php');
?>







