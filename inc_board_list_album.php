<?
$AddSqlWhere = "1=1";
$ListParam = "1=1";
$PaginationParam = "1=1";

$BoardCategoryID = isset($_REQUEST["BoardCategoryID"]) ? $_REQUEST["BoardCategoryID"] : "";
$CurrentPage = isset($_REQUEST["CurrentPage"]) ? $_REQUEST["CurrentPage"] : "";
$PageListNum = isset($_REQUEST["PageListNum"]) ? $_REQUEST["PageListNum"] : "";
$SearchItem = isset($_REQUEST["SearchItem"]) ? $_REQUEST["SearchItem"] : "";
$SearchText = isset($_REQUEST["SearchText"]) ? $_REQUEST["SearchText"] : "";


$Sql = "select BoardID from Boards where BoardCode=:BoardCode";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':BoardCode', $BoardCode);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$BoardID = $Row["BoardID"];


$ListParam = $ListParam . "&BoardCode=" . $BoardCode;
$AddSqlWhere = $AddSqlWhere . " and (A.BoardID=$BoardID) ";


if (!$CurrentPage){
	$CurrentPage = 1;	
}
if (!$PageListNum){
	$PageListNum = $BoardListRowNum;
}


if ($PageListNum!=""){
	$ListParam = $ListParam . "&PageListNum=" . $PageListNum;
}


if ($SearchText!=""){
	$ListParam = $ListParam . "&SearchItem=" . $SearchItem;
	$ListParam = $ListParam . "&SearchText=" . $SearchText;
	switch ($SearchItem) {
	case "1":
		$AddSqlWhere = $AddSqlWhere . " and (A.BoardContentSubject like '%".$SearchText."%' or A.BoardContent like '%".$SearchText."%') ";
		break;
	case "2":
		$AddSqlWhere = $AddSqlWhere . " and (A.BoardContentSubject like '%".$SearchText."%') ";
		break;	
	case "3":
		$AddSqlWhere = $AddSqlWhere . " and (A.BoardContent like '%".$SearchText."%') ";
		break;
	case "4":
		$AddSqlWhere = $AddSqlWhere . " and (A.BoardContentWriterName like '%".$SearchText."%') ";
		break;
	}
}


if ($BoardCategoryID!=""){
	$AddSqlWhere = $AddSqlWhere . " and A.BoardCategoryID=$BoardCategoryID ";
	$ListParam = $ListParam . "&BoardCategoryID=" . $BoardCategoryID;
}



$PaginationParam = $ListParam;
if ($CurrentPage!=""){
	$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}

$ListParam = str_replace("&", "^^", $ListParam);






$Sql = "select count(*) as TotalRowCount from BoardContents A where ".$AddSqlWhere." and A.BoardContentState=1";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TotalRowCount = $Row["TotalRowCount"];


$TotalPageCount = ceil($TotalRowCount / $PageListNum);
$StartRowNum = $PageListNum * ($CurrentPage - 1 );



$Sql = "select A.*, (select count(*) from BoardComments where BoardContentID=A.BoardContentID) as BoardCommentCount, timestampdiff(day, BoardContentRegDateTime, now()) as RecentDay from BoardContents A where ".$AddSqlWhere." and A.BoardContentState=1 order by A.BoardContentNotice desc, A.BoardContentReplyID desc, A.BoardContentReplyOrder asc limit $StartRowNum, $PageListNum";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

//echo $Sql;


?>


	<div id="bbs">
    	
        <!--
		<ul class="TabGallery">
        	<li><a href="board_list.php?BoardCode=mylecture" class="Active">썸네일 보기</a></li>
            <li><a href="#">리스트 보기</a></li>
        </ul>
		-->
        
		<div class="TopSearch">
        	<span>총 <b><?=$TotalRowCount?></b>개</span>
            
			<form name="SearchForm" method="get">
			<?
			if ($BoardEnableCategory==1){
			?>
			<select name="BoardCategoryID" id="BoardCategoryID" class="Select" onchange="SearchSubmit()">
				<option value="">전 체</option>
				<?
				$Sql2 = "select * from BoardCategories  where BoardID=:BoardID order by BoardCategoryOrder asc";
				$Stmt2 = $DbConn->prepare($Sql2);
				$Stmt2->bindParam(':BoardID', $BoardID);
				$Stmt2->execute();
				$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

				while($Row2 = $Stmt2->fetch()){
				?>
				<option value="<?=$Row2["BoardCategoryID"]?>" <?if ($BoardCategoryID==$Row2["BoardCategoryID"]){?>selected<?}?>><?=$Row2["BoardCategoryName"]?></option>
				<?
				}
				$Stmt2 = null;
				?>
			</select>
			<?
			}
			?>			
			
			
			
			<input type="hidden" name="BoardCode" value="<?=$BoardCode?>">
			<input type="hidden" name="PageListNum" value="<?=$PageListNum?>">

					<select name="SearchItem" class="Select">
						<option value="1" <?php if ($SearchItem=="1") {echo "selected";}?>>제목+내용</option>
						<option value="2" <?php if ($SearchItem=="2") {echo "selected";}?>>제목</option>
						<option value="3" <?php if ($SearchItem=="3") {echo "selected";}?>>내용</option>
						<option value="4" <?php if ($SearchItem=="4") {echo "selected";}?>>작성자</option>
					</select>
					<input type="text" name="SearchText" class="Input2" placeholder="검색어를 입력해 주세요." value="<?=$SearchText?>"><a href="javascript:SearchSubmit()" class="BtnSearch">검색</a>


			</div>
            </form>
		</div>
        

		<ol class="Gallery">
			<?php
			$ListCount = 1;
			while($Row = $Stmt->fetch()) {
				$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

				if ($Row["BoardContentNotice"]==1){
					$IcoNotice = "<img src='images/board/icon_notice.png' class='notice' width='30' height='18'>";
					$IcoReply = "";
					$IcoDepth = "";
				}else{
					$IcoNotice = "";
				
					$IcoReply = "";
					if ($Row["BoardContentReplyOrder"]>0) {
						$IcoReply = "<img src='images/board/icon_re.png'>";
					}else{
						$IcoReply = "";
					}

					
					$IcoDepth = "";
					if ($Row["BoardContentReplyDepth"]>0){
						for ($ii=2; $ii<=$Row["BoardContentReplyDepth"]; $ii++){
							$IcoDepth = $IcoDepth . "&nbsp;&nbsp;";
						}
					}else{
						$IcoDepth = "";
					}
				
				}

				if ($Row["BoardCommentCount"]==0){
					$StrBoardCommentCount = "";
				}else{
					$StrBoardCommentCount = " <span class='comment'>[".$Row["BoardCommentCount"]."]</span>";
				}


				if ($Row["RecentDay"]>=6){
					$IcoNew = "";
				}else{
					$IcoNew = " <img src='images/board/icon_new.png' class='notice' width='23' height='10'>";
				}

				if ($Row["BoardContentSecret"]==0){
					$IcoSecret = "";
					$LinkUrl = "board_read.php?ListParam=$ListParam&BoardContentID=".$Row["BoardContentID"]."&BoardCode=$BoardCode";
				}else{
					$IcoSecret = " [비공개] ";
					$LinkUrl = "board_password_form.php?ListParam=$ListParam&BoardContentID=".$Row["BoardContentID"]."&BoardCode=$BoardCode&ActionMode=SecretRead+";
				}

				
				$StrListNumber = $ListNumber;
				if ($IcoNotice!=""){
					$StrListNumber = $IcoNotice;
				}

				if ($BoardEnableCategory==1){
					$Sql2 = "select BoardCategoryName from BoardCategories A where BoardCategoryID=:BoardCategoryID";
					$Stmt2 = $DbConn->prepare($Sql2);
					$Stmt2->bindParam(':BoardCategoryID', $Row["BoardCategoryID"]);
					$Stmt2->execute();
					$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
					$Row2 = $Stmt2->fetch();

					$BoardCategoryName = $Row2["BoardCategoryName"];
					if ($BoardCategoryName==""){
						$BoardCategoryName = "-";
					}
				}


				$Sql2 = "select ifnull(BoardFileName,'') as BoardFileName from BoardContentFiles A where BoardFileNumber=1 and BoardContentID=:BoardContentID";
				$Stmt2 = $DbConn->prepare($Sql2);
				$Stmt2->bindParam(':BoardContentID', $Row["BoardContentID"]);
				$Stmt2->execute();
				$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
				$Row2 = $Stmt2->fetch();

				$BoardFileName = $Row2["BoardFileName"];

				if ($BoardFileName==""){
					$StrImageUrl = "images/no_image.png";
				}else{
					$StrImageUrl = "uploads/board_files/".$BoardFileName;
				}
			?>
			<li>
				<a href="<?=$LinkUrl?>"><img src="<?=$StrImageUrl?>"></a>
				<p><b><?=$Row["BoardContentSubject"]?></b><?=substr($Row["BoardContentRegDateTime"],0,10)?></p>
			</li>
			<?php
				$ListCount ++;
			}
			$Stmt = null;
			?>
		</ol>		

	
	
		

		<?php
			include_once('./inc_pagination.php');
		?>
		<?php
		if ($AuthWrite){
		?>
		
        <div class="BtnRight"><a href="board_form.php?BoardCode=<?=$BoardCode?>" class="Btn2">글쓰기</a></div>
		<?php
		}else{
		?>
		<!--div class="BtnRight"><a href="javascript:DenyWrite()" class="Btn2">글쓰기</a></div-->	
		<?php
		}
		?>
	</div>


<script>
function SearchSubmit(){
	document.SearchForm.action = "board_list.php";
	document.SearchForm.submit();
}

function DenyWrite(){
	alert('권한이 없습니다.');
}
</script>