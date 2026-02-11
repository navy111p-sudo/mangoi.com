<?php
$top_menu_id = 5;
$left_menu_id = 2;
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
include_once('./_header.php');
include_once('./includes/board_config.php');
?>
<body>
<?php
include_once('./_top.php');
include_once('./_left.php');
?>


<div class="right">
	<div class="content">
		<h2><?=$BoardName?></h2>
		<div class="box">

			<?php
			
			$AddSqlWhere = "1=1";
			$ListParam = "1=1";
			$PaginationParam = "1=1";

			$BoardCategoryID = isset($_REQUEST["BoardCategoryID"]) ? $_REQUEST["BoardCategoryID"] : "";
			$CurrentPage = isset($_REQUEST["CurrentPage"]) ? $_REQUEST["CurrentPage"] : "";
			$PageListNum = isset($_REQUEST["PageListNum"]) ? $_REQUEST["PageListNum"] : "";
			$SearchItem = isset($_REQUEST["SearchItem"]) ? $_REQUEST["SearchItem"] : "";
			$SearchText = isset($_REQUEST["SearchText"]) ? $_REQUEST["SearchText"] : "";


			$Sql = "select BoardID from Boards where BoardCode=:BoardCode ";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':BoardCode', $BoardCode);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);
			$Row = $Stmt->fetch();
			$Stmt = null;

			$BoardID = $Row["BoardID"];

			$ListParam = $ListParam . "&BoardCode=" . $BoardCode;
			$AddSqlWhere = $AddSqlWhere . " and (BoardID=$BoardID) ";

	
			if (!$CurrentPage){
				$CurrentPage = 1;	
			}
			if (!$PageListNum){
				$PageListNum = 10;
			}

			
			if ($PageListNum!=""){
				$ListParam = $ListParam . "&PageListNum=" . $PageListNum;
			}

		
			if ($SearchText!=""){
				$ListParam = $ListParam . "&SearchItem=" . $SearchItem;
				$ListParam = $ListParam . "&SearchText=" . $SearchText;
				switch ($SearchItem) {
				case "1":
					$AddSqlWhere = $AddSqlWhere . " and (BoardContentSubject like '%".$SearchText."%' or BoardContent like '%".$SearchText."%') ";
					break;
				case "2":
					$AddSqlWhere = $AddSqlWhere . " and (BoardContentSubject like '%".$SearchText."%') ";
					break;	
				case "3":
					$AddSqlWhere = $AddSqlWhere . " and (BoardContent like '%".$SearchText."%') ";
					break;
				case "4":
					$AddSqlWhere = $AddSqlWhere . " and (BoardContentWriterName like '%".$SearchText."%') ";
					break;
				}

				
			}

			


			$PaginationParam = $ListParam;
			if ($CurrentPage!=""){
				$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
			}

			$ListParam = str_replace("&", "^^", $ListParam);




			if ($BoardCategoryID!=""){
				$AddSqlWhere = $AddSqlWhere . " and BoardCategoryID=$BoardCategoryID ";
			}



			
			$Sql = "select count(*) as TotalRowCount from BoardContents where ".$AddSqlWhere." ";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);
			$Row = $Stmt->fetch();
			$Stmt = null;

			$TotalRowCount = $Row["TotalRowCount"];

			$TotalPageCount = ceil($TotalRowCount / $PageListNum);
			$StartRowNum = $PageListNum * ($CurrentPage - 1 );

			

			$Sql = "select *, (select count(*) from BoardComments where BoardContentID=BoardContents.BoardContentID) as BoardCommentCount, timestampdiff(day, BoardContentRegDateTime, now()) as RecentDay from BoardContents  where ".$AddSqlWhere." and BoardContentState=1 order by BoardContentNotice desc, BoardContentReplyID desc, BoardContentReplyOrder asc limit $StartRowNum, $PageListNum";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);

			?>

			<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-bottom:5px;">
			  <tr>
				<td align="right">
				<form name="SearchForm" method="post">
					<select style="width:90px;" name="SearchItem">
						<option value="1" <?php if ($SearchItem=="1") {echo "selected";}?>>제목+내용</option>
						<option value="2" <?php if ($SearchItem=="2") {echo "selected";}?>>제목</option>
						<option value="3" <?php if ($SearchItem=="3") {echo "selected";}?>>내용</option>
						<option value="4" <?php if ($SearchItem=="4") {echo "selected";}?>>작성자</option>
					</select>
					<input type="text" id="search" name="SearchText" value="<?=$SearchText?>"/>
					<input type="button" name="button" value="Search" onclick="SearchSubmit()" class="btn_input">
				</form>				
				</td>
			  </tr>
			</table>

<style>
ol.tab{overflow:hidden; width:100%; margin:20px 0 0 0; border-bottom:1px solid #546b83;}
ol.tab li{float:left; width:110px; margin-right:1px;}
ol.tab li a{display:block; height:28px; line-height:28px; background:#546B83; color:#fff; text-align:center; font-weight:bold; border-radius:4px 4px 0 0;}
ol.tab li a:hover{background:#1F364E;}
ol.tab li a.active{background:#1F364E;}
</style>

			<ol class="tab">
			<li><a href="board_list.php?<?=$PaginationParam?>" <?php if ($BoardCategoryID=="") { echo "class='active'";}?>>전체</a></li>
			<?
			if ($BoardEnableCategory==1){
				$Sql2 = "select * from BoardCategories  where BoardID=:BoardID order by BoardCategoryOrder asc";
				$Stmt2 = $DbConn->prepare($Sql2);
				$Stmt2->bindParam(':BoardID', $BoardID);
				$Stmt2->execute();
				$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

				while($Row2 = $Stmt2->fetch()){
			?>
				<li><a href="board_list.php?<?=$PaginationParam?>&BoardCategoryID=<?=$Row2["BoardCategoryID"]?>" <?php if ($BoardCategoryID==$Row2["BoardCategoryID"]) { echo "class='active'";}?>><?=$Row2["BoardCategoryName"]?></a></li>
			<?
				}
				$Stmt2 = null;
			}
			?>
			
			<li><a href="board_list.php?<?=$PaginationParam?>&BoardCategoryID=0" <?php if ($BoardCategoryID=="0") { echo "class='active'";}?>>기타</a></li>
			</ol>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table1">
			  <tr>
				<th width="6%">No</th>
				<th>제목</th>
				<th width="12%">작성자</th>
				<th width="12%">작성일</th>
				<th width="8%">조회수</th>
			  </tr>



				<?php
				$ListCount = 1;
				while($Row = $Stmt->fetch()) {
					$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

					if ($Row["BoardContentNotice"]==1){
						$IcoNotice = "<img src='images/icon_notice.png' class='notice'>";
						$IcoReply = "";
						$IcoDepth = "";
					}else{
						$IcoNotice = "";
					
						$IcoReply = "";
						if ($Row["BoardContentReplyOrder"]>0) {
							$IcoReply = "<img src='images/icon_re.png'>";
						}else{
							$IcoReply = "";
						}

						
						$IcoDepth = "";
						if ($Row["BoardContentReplyDepth"]>0){
							for ($ii=1; $ii<=$Row["BoardContentReplyDepth"]; $ii++){
								$IcoDepth = $IcoDepth . "&nbsp;&nbsp;";
							}
						}else{
							$IcoDepth = "";
						}
					
					}



					if ($Row["BoardCommentCount"]==0){
						$StrBoardCommentCount = "";
					}else{
						$StrBoardCommentCount = "&nbsp;<span style='color:#FF663C'>[".$Row["BoardCommentCount"]."]</span>";
					}

					if ($Row["RecentDay"]>=6){
						$IcoNew = "";
					}else{
						$IcoNew = " <img src='images/icon_new.png' class='notice'>";
					}

					if ($Row["BoardContentSecret"]==0){
						$IcoSecret = "";
					}else{
						$IcoSecret = " <img src='images/icon_key.png'>";
					}


					$StrListNumber = $ListNumber;
					if ($IcoNotice!=""){
						$StrListNumber = $IcoNotice;
					}


				?>
				  <tr>
					<td><?=$StrListNumber?></td>
					<td class="subject"><?=$IcoDepth?><?=$IcoReply?><a href="board_read.php?ListParam=<?=$ListParam?>&BoardContentID=<?=$Row["BoardContentID"]?>&BoardCode=<?=$BoardCode?>"><?=$Row["BoardContentSubject"]?></a><?=$StrBoardCommentCount?><?=$IcoSecret?><?=$IcoNew?></td>
					<td><?=$Row["BoardContentWriterName"]?></td>
					<td><?=substr($Row["BoardContentRegDateTime"],0,10)?></td>
					<td><?=$Row["BoardContentViewCount"]?></td>
				  </tr>
				<?php
					$ListCount ++;
				}
				$Stmt = null;
				?>

			</table>
			<?php
			include_once('./include_pagination.php');
			?>

			<div class="button">
				<a href="board_form.php?BoardCode=<?=$BoardCode?>">등록하기</a>
			</div>

		</div>
	</div>

</div>

<script>
function SearchSubmit(){
	document.SearchForm.action = "board_list.php?BoardCode=<?=$BoardCode?>&PageListNum=<?=$PageListNum?>";
	document.SearchForm.submit();
}
</script>

<?php
include_once('./_bottom.php');
?>
</body>
<?php
include_once('./_footer.php');
include_once('../includes/dbclose.php');
?>







