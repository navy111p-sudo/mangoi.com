<?php
$top_menu_id = 5;
$left_menu_id = 0;
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
include_once('./_header.php');
?>
<body>
<?php
include_once('./_top.php');
include_once('./_left.php');
?>


<div class="right">
	<div class="content">
		<h2>게시판목록</h2>
		<div class="box">

			<?php
			
			$AddSqlWhere = "1=1";
			$ListParam = "1=1";
			$PaginationParam = "1=1";
			
			$CurrentPage = isset($_REQUEST["CurrentPage"]) ? $_REQUEST["CurrentPage"] : "";
			$PageListNum = isset($_REQUEST["PageListNum"]) ? $_REQUEST["PageListNum"] : "";
			$SearchText = isset($_REQUEST["SearchText"]) ? $_REQUEST["SearchText"] : "";
			$SearchState = isset($_REQUEST["SearchState"]) ? $_REQUEST["SearchState"] : "";


			if (!$CurrentPage){
				$CurrentPage = 1;	
			}
			if (!$PageListNum){
				$PageListNum = 10;
			}

			
			if ($PageListNum!=""){
				$ListParam = $ListParam . "&PageListNum=" . $PageListNum;
			}

			if ($SearchState==""){
				$SearchState = "1";
			}			
			if ($SearchState!="2"){
				$ListParam = $ListParam . "&SearchState=" . $SearchState;
				$AddSqlWhere = $AddSqlWhere . " and A.BoardState=$SearchState ";
			}

			if ($SearchText!=""){
				$ListParam = $ListParam . "&SearchText=" . $SearchText;
				$AddSqlWhere = $AddSqlWhere . " and (A.BoardName like '%".$SearchText."%' or BoardTitle like '%".$SearchText."%') ";
			}

			$PaginationParam = $ListParam;
			if ($CurrentPage!=""){
				$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
			}

			$ListParam = str_replace("&", "^^", $ListParam);

			$Sql = "select count(*) as TotalRowCount from Boards A where ".$AddSqlWhere." ";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);
			$Row = $Stmt->fetch();
			$Stmt = null;

			$TotalRowCount = $Row["TotalRowCount"];



			$TotalPageCount = ceil($TotalRowCount / $PageListNum);
			$StartRowNum = $PageListNum * ($CurrentPage - 1 );

			
			$add_sql_select = "";
			$add_sql_select .= ", ifnull((select MemberLevelName from MemberLevels where MemberLevelID=A.BoardListLevel),'비회원') as StrBoardListLevel ";
			$add_sql_select .= ", ifnull((select MemberLevelName from MemberLevels where MemberLevelID=A.BoardReadLevel),'비회원') as StrBoardReadLevel ";
			$add_sql_select .= ", ifnull((select MemberLevelName from MemberLevels where MemberLevelID=A.BoardWriteLevel),'비회원') as StrBoardWriteLevel ";
			$add_sql_select .= ", ifnull((select MemberLevelName from MemberLevels where MemberLevelID=A.BoardReplyLevel),'비회원') as StrBoardReplyLevel ";

			$Sql = "select A.* ".$add_sql_select." from Boards A where ".$AddSqlWhere." order by A.BoardRegDateTime desc limit $StartRowNum, $PageListNum";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);

			

			?>

			<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-bottom:5px;">
			  <tr>
				<td align="right">
				<form name="SearchForm" method="post">
					<select name="SearchState" onchange="SearchSubmit()">
						<option value="2" <?php if ($SearchState=="2") {echo ("selected");}?>>전체</option>
						<option value="1" <?php if ($SearchState=="1") {echo ("selected");}?>>승인</option>
						<option value="0" <?php if ($SearchState=="0") {echo ("selected");}?>>미승인</option>
					</select>
					<input type="text" id="search" name="SearchText" value="<?=$SearchText?>"/>
					<input type="button" name="button" value="Search" onclick="SearchSubmit()" class="btn_input">
				</form>				
				</td>
			  </tr>
			</table>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table1">
			  <tr>
				<th width="6%">No</th>
				<th>게시판코드</th>
				<th>게시판명</th>
				<th>타이틀</th>
				<th width="12%">리스트권한</th>
				<th width="12%">읽기권한</th>
				<th width="12%">쓰기권한</th>
				<th width="12%">답변권한</th>
				<th width="8%">상태</th>
			  </tr>



				<?php
				$ListCount = 1;
				while($Row = $Stmt->fetch()) {
					$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

					if ($Row["BoardState"]==1){
						$str_member_state = "승인";
					}else{
						$str_member_state = "미승인";
					}
				?>
				  <tr>
					<td><?=$ListNumber?></td>
					<td><?=$Row["BoardCode"]?></td>
					<td class="subject"><a href="boardset_form.php?ListParam=<?=$ListParam?>&BoardID=<?=$Row["BoardID"]?>&BoardCode=<?=$Row["BoardCode"]?>"><?=$Row["BoardName"]?></a></td>
					<td class="subject"><a href="boardset_form.php?ListParam=<?=$ListParam?>&BoardID=<?=$Row["BoardID"]?>&BoardCode=<?=$Row["BoardCode"]?>"><?=$Row["BoardTitle"]?></a></td>
					<td><?=$Row["StrBoardListLevel"]?></td>
					<td><?=$Row["StrBoardReadLevel"]?></td>
					<td><?=$Row["StrBoardWriteLevel"]?></td>
					<td><?=$Row["StrBoardReplyLevel"]?></td>
					<td><?=$str_member_state?></td>
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
				<a href="boardset_form.php">등록하기</a>
			</div>

		</div>
	</div>

</div>

<script>
function SearchSubmit(){
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







