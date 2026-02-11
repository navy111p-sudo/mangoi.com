<?php
$top_menu_id = 4;
$left_menu_id = 3;
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
		<h2>서브목록</h2>
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
				$PageListNum = 100;
			}

			
			if ($PageListNum!=""){
				$ListParam = $ListParam . "&PageListNum=" . $PageListNum;
			}

			if ($SearchState==""){
				$SearchState = "1";
			}			
			if ($SearchState!="2"){
				$ListParam = $ListParam . "&SearchState=" . $SearchState;
				$AddSqlWhere = $AddSqlWhere . " and A.SubState=$SearchState ";
			}

			if ($SearchText!=""){
				$ListParam = $ListParam . "&SearchText=" . $SearchText;
				$AddSqlWhere = $AddSqlWhere . " and A.SubName like '%".$SearchText."%' ";
			}

			$PaginationParam = $ListParam;
			if ($CurrentPage!=""){
				$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
			}

			$ListParam = str_replace("&", "^^", $ListParam);

			$Sql = "select count(*) as TotalRowCount from Subs A where ".$AddSqlWhere." ";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);
			$Row = $Stmt->fetch();
			$Stmt = null;

			$TotalRowCount = $Row["TotalRowCount"];

			$TotalPageCount = ceil($TotalRowCount / $PageListNum);
			$StartRowNum = $PageListNum * ($CurrentPage - 1 );

			
			$Sql = "select A.* from Subs A where ".$AddSqlWhere." order by A.SubName asc limit $StartRowNum, $PageListNum";
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
				<th width="20%">서브코드</th>
				<th>서브명</th>
				<th width="8%">상태</th>
			  </tr>



				<?php
				$ListCount = 1;
				while($Row = $Stmt->fetch()) {
					$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

					if ($Row["SubState"]==1){
						$str_member_state = "승인";
					}else{
						$str_member_state = "미승인";
					}
				?>
				  <tr>
					<td><?=$ListNumber?></td>
					<td class="subject"><a href="sub_layout_form.php?ListParam=<?=$ListParam?>&SubID=<?=$Row["SubID"]?>"><?=$Row["SubCode"]?></a></td>
					<td class="subject"><a href="sub_layout_form.php?ListParam=<?=$ListParam?>&SubID=<?=$Row["SubID"]?>"><?=$Row["SubName"]?></a></td>
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
				<a href="sub_layout_form.php">등록하기</a>
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







