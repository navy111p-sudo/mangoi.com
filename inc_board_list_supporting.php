<?
$AddSqlWhere = "1=1";
$ListParam = "1=1";
$PaginationParam = "1=1";

$BoardCategoryID = isset($_REQUEST["BoardCategoryID"]) ? $_REQUEST["BoardCategoryID"] : "";
$CurrentPage = isset($_REQUEST["CurrentPage"]) ? $_REQUEST["CurrentPage"] : "";
$PageListNum = isset($_REQUEST["PageListNum"]) ? $_REQUEST["PageListNum"] : "";
$SearchText = isset($_REQUEST["SearchText"]) ? $_REQUEST["SearchText"] : "";
$SearchSupportItemID = isset($_REQUEST["SearchSupportItemID"]) ? $_REQUEST["SearchSupportItemID"] : "";
$SearchSupportOrganID = isset($_REQUEST["SearchSupportOrganID"]) ? $_REQUEST["SearchSupportOrganID"] : "";




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
	$ListParam = $ListParam . "&SearchText=" . $SearchText;
	$AddSqlWhere = $AddSqlWhere . " and (A.BoardContentSubject like '%".$SearchText."%' or A.BoardContent like '%".$SearchText."%') ";
}

if ($SearchSupportItemID!=""){
	$ListParam = $ListParam . "&SearchSupportItemID=" . $SearchSupportItemID;
	$AddSqlWhere = $AddSqlWhere . " and A.SupportItemID=$SearchSupportItemID ";
}

if ($SearchSupportOrganID!=""){
	$ListParam = $ListParam . "&SearchSupportOrganID=" . $SearchSupportOrganID;
	$AddSqlWhere = $AddSqlWhere . " and A.SupportOrganID=$SearchSupportOrganID ";
}


if ($BoardCategoryID!=""){
	$ListParam = $ListParam . "&BoardCategoryID=" . $BoardCategoryID;
	$AddSqlWhere = $AddSqlWhere . " and A.BoardCategoryID=$BoardCategoryID ";
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



$Sql = "select A.*, 
		(select count(*) from BoardComments where BoardContentID=A.BoardContentID) as BoardCommentCount, 
		timestampdiff(day, BoardContentRegDateTime, now()) as RecentDay,
		(select SupportItemName from SupportItems where SupportItemID=A.SupportItemID) as SupportItemName,
		(select SupportOrganName from SupportOrgans where SupportOrganID=A.SupportOrganID) as SupportOrganName
		from BoardContents A where ".$AddSqlWhere." and A.BoardContentState=1 order by A.BoardContentNotice desc, A.BoardContentReplyID desc, A.BoardContentReplyOrder asc limit $StartRowNum, $PageListNum";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

//echo $Sql;


?>

	
	<div id="bbs">
		<div class="TopSearch2">
            <form name="SearchForm" method="get">
			<input type="hidden" name="BoardCode" value="<?=$BoardCode?>">
			<input type="hidden" name="PageListNum" value="<?=$PageListNum?>">
			<div class="Board">
			<div class="BbsTop">
				<div class="TopLeft">
					<select name="SearchSupportItemID" class="Select Font3" style="width:48%;" onchange="SearchSubmit()">
						<option value="">지원항목선택</option>
						<?
						$Sql2 = "select * from SupportItems order by SupportItemID";
						$Stmt2 = $DbConn->prepare($Sql2);
						$Stmt2->execute();
						$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

						while($Row2 = $Stmt2->fetch()){
						?>
						<option value="<?=$Row2["SupportItemID"]?>" <?if ($SearchSupportItemID==$Row2["SupportItemID"]) {?>selected<?}?>><?=$Row2["SupportItemName"]?></option> 
						<?
						}
						$Stmt2 = null;
						?>
					</select>
					<select name="SearchSupportOrganID" class="Select Font3" style="width:48%;" onchange="SearchSubmit()">
						<option value="">지원기관선택</option>
						<?
						$Sql2 = "select * from SupportOrgans order by SupportOrganID";
						$Stmt2 = $DbConn->prepare($Sql2);
						$Stmt2->execute();
						$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

						while($Row2 = $Stmt2->fetch()){
						?>
						<option value="<?=$Row2["SupportOrganID"]?>" <?if ($SearchSupportOrganID==$Row2["SupportOrganID"]) {?>selected<?}?>><?=$Row2["SupportOrganName"]?></option> 
						<?
						}
						$Stmt2 = null;
						?>
					</select>
				</div>
				<div class="TopRight">
					<input type="text" name="SearchText" class="Input" placeholder="검색어를 입력해 주세요." value="<?=$SearchText?>"><a href="javascript:SearchSubmit()" class="BtnSearch">검색</a>
				</div>
			</div>
			</div>
            </form>
		</div>
        
		
		
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="BBSTb3">
			  <tr>
				<th class="Col1">번호</th>
				<th class="Col2">지원항목</th>
				<th class="Col3">지원기관</th>
				<th class="Col4">지원사업제목</th>
				<th class="Col6">상세내용</th>
			  </tr>
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
					$LinkUrl = "board_password_form.php?ListParam=$ListParam&BoardContentID=".$Row["BoardContentID"]."&BoardCode=$BoardCode&ActionMode=SecretRead";
				}

				
				$StrListNumber = $ListNumber;
				if ($IcoNotice!=""){
					$StrListNumber = $IcoNotice;
				}

			?>
			  <tr>
				<td class="Col1"><?=$StrListNumber?></td>
				<td class="Col2"><?=$Row["SupportItemName"]?></td>
				<td class="Col3"><?=$Row["SupportOrganName"]?></td>
				<td class="Col4 LeftA"><?=$Row["BoardContentSubject"]?></td>
				<td class="Col6"><a href="<?=$LinkUrl?>" class="State1">보기</a></td>
			  </tr>
			<?php
				$ListCount ++;
			}
			$Stmt = null;
			?>
			  
		</table>
		
		

		<?php
			include_once('./inc_pagination.php');
		?>
		<?php
		if ($AuthWrite){
		?>
		<div class="BtnRight"><a href="board_form.php?BoardCode=<?=$BoardCode?>" class="Btn2">등록하기</a></div>
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