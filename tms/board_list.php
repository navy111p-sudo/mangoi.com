<?
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
include_once('./inc_header.php');
include_once('./includes/board_config.php');
?>
<style>
ol.tab{overflow:hidden; width:100%; margin:20px 0 0 0; border-bottom:1px solid #546b83;}
ol.tab li{float:left; width:110px; margin-right:1px;}
ol.tab li a{display:block; height:28px; line-height:28px; background:#546B83; color:#fff; text-align:center; font-weight:bold; border-radius:4px 4px 0 0;}
ol.tab li a:hover{background:#1F364E;}
ol.tab li a.active{background:#1F364E;}
</style>
</head>
<body>
<?
$MainCode = 7;

if ($BoardCode=="notice"){
	$SubCode = 2;
}else if ($BoardCode=="center_reference"){
	$SubCode = 12;
}

include_once('./inc_top.php');
?>


<?

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


//매뉴얼일때 기본값
if ($BoardCode=="manual" && $BoardCategoryID==""){
	$BoardCategoryID = "1";
}
//매뉴얼일때 기본값



if ($BoardCategoryID!=""){
	$AddSqlWhere = $AddSqlWhere . " and BoardCategoryID=$BoardCategoryID ";
}

$AddSqlWhere = $AddSqlWhere . " and BoardContentState=1 ";


$Sql = "select count(*) as TotalRowCount from BoardContents where ".$AddSqlWhere." ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TotalRowCount = $Row["TotalRowCount"];


$TotalPageCount = ceil($TotalRowCount / $PageListNum);
$StartRowNum = $PageListNum * ($CurrentPage - 1 );



$Sql = "select *, DATE_FORMAT(EventStartDate,'%Y-%m-%d') as EventStartDate2 , DATE_FORMAT(EventEndDate,'%Y-%m-%d') as EventEndDate2, (select count(*) from BoardComments where BoardContentID=BoardContents.BoardContentID) as BoardCommentCount, timestampdiff(day, BoardContentRegDateTime, now()) as RecentDay from BoardContents  where ".$AddSqlWhere."  order by BoardContentNotice desc, BoardContentReplyID desc, BoardContentReplyOrder asc limit $StartRowNum, $PageListNum";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

?>


<h1 class="Title"><?=$BoardTitle?></h1>

<div class="box_search">
	<!--
	게시물 작성일 숨기기 <input type="checkbox" name="BoardDateHide" id="BoardDateHide" value="1" <?if ($BoardDateHide==1) {?>checked<?}?> onclick="ChBoardDateHide()">

	<script>
		function ChBoardDateHide(){
			
			if (document.getElementById('BoardDateHide').checked){
				BoardDateHide = 1;
				AlertMsg = "게시물 작성일을 숨기기로 설정하시겠습니까?";
			}else{
				BoardDateHide = 0;
				AlertMsg = "게시물 작성일을 숨기기를 해제하시겠습니까?";
			}

			if (confirm(AlertMsg)){
				url = "./ajax_set_board_date_hide.php";

				//location.href = url+"?BandID="+BandID;
				$.ajax(url, {
					data: {
						BoardDateHide: BoardDateHide,
						BoardCode: '<?=$BoardCode?>'
					},
					success: function (data) {
						
					},
					error: function () {

					}
				});								
			}else{
				if (BoardDateHide==1){
					document.getElementById('BoardDateHide').checked = false;
				}else{
					document.getElementById('BoardDateHide').checked = true;
				}
			}

		}
	</script>
	-->

	<form name="SearchForm" method="get">
		<input type="hidden" name="BoardCode" value="<?=$BoardCode?>">
		<input type="hidden" name="PageListNum" value="<?=$PageListNum?>">
		<?if ($BoardEnableCategory==1){?>
		<select style="width:150px;" name="BoardCategoryID" onchange="SearchSubmit()" class="Select">
			<?if ($BoardCode!="manual"){?>
			<option value="" <? if ($BoardCategoryID=="") {echo "selected";}?>>전체</option>
			<?}?>
			
			<?
			$Sql2 = "select * from BoardCategories  where BoardID=:BoardID order by BoardCategoryOrder asc";
			$Stmt2 = $DbConn->prepare($Sql2);
			$Stmt2->bindParam(':BoardID', $BoardID);
			$Stmt2->execute();
			$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

			while($Row2 = $Stmt2->fetch()){
			?>
			<option value="<?=$Row2["BoardCategoryID"]?>" <? if ($BoardCategoryID==$Row2["BoardCategoryID"]) {echo "selected";}?>><?=$Row2["BoardCategoryName"]?></option>
			<?
			}
			$Stmt2 = null;
			?>
		</select>
		<?}?>

		<select style="width:150px;" name="SearchItem" class="Select">
			<option value="1" <? if ($SearchItem=="1") {echo "selected";}?>>제목+내용</option>
			<option value="2" <? if ($SearchItem=="2") {echo "selected";}?>>제목</option>
			<option value="3" <? if ($SearchItem=="3") {echo "selected";}?>>내용</option>
			<option value="4" <? if ($SearchItem=="4") {echo "selected";}?>>작성자</option>
		</select>
		<input type="text" id="search" name="SearchText" value="<?=$SearchText?>" style="border:1px solid #cccccc;"/>
		<a href="javascript:SearchSubmit();" class="btn_search"><img src="images/icon_zoom_gray.png"></a>		
	</form>				

</div>




<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_list">
  <tr>
	<th width="5%">No</th>
	<th width="5%">Main</th>
	<th>제목</th>
	<?if ($BoardCode=="event" || $BoardCode=="main_notice"){?>
	<th>시작일</th>
	<th>종료일</th>
	<?}else{?>
	<th width="15%">작성자</th>
	<th width="12%">작성일</th>
	<?}?>
	<th width="5%">조회수</th>
  </tr>

	<?
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

		$EventStartDate = $Row["EventStartDate2"];
		$EventEndDate = $Row["EventEndDate2"];

		$BoardContentMain = $Row["BoardContentMain"];
	?>
	  <tr>
		<td><?=$StrListNumber?></td>
		<td><?if ($BoardContentMain==1){?>V<?}?></td>
		<td class="left">
			<?=$IcoDepth?><?=$IcoReply?><a href="board_read.php?ListParam=<?=$ListParam?>&BoardContentID=<?=$Row["BoardContentID"]?>&BoardCode=<?=$BoardCode?>" class="color"><?=$Row["BoardContentSubject"]?></a><?=$StrBoardCommentCount?><?=$IcoSecret?><?=$IcoNew?>
		</td>
		
		<?if ($BoardCode=="event" || $BoardCode=="main_notice"){?>
		<td><?=$EventStartDate?></td>
		<td><?=$EventEndDate?></td>
		<?}else{?>
		<td><?=$Row["BoardContentWriterName"]?></td>
		<td><?=substr($Row["BoardContentRegDateTime"],0,10)?></td>
		<?}?>
		<td><?=$Row["BoardContentViewCount"]?></td>
	  </tr>
	<?
		$ListCount ++;
	}
	$Stmt = null;
	?>

</table>



<?			
include_once('./inc_pagination.php');			
?>

<?if ($_LINK_ADMIN_LEVEL_ID_<=2){?>
<div class="btn_right" style="padding-top:25px;">
	<a href="board_form.php?BoardCode=<?=$BoardCode?>" class="btn red">등록하기</a>
</div>
<?}?>



<script>
function SearchSubmit(){
	document.SearchForm.action = "board_list.php";
	document.SearchForm.submit();
}
</script>

<?
include_once('./inc_bottom.php');
?>
<?
include_once('./inc_bottom.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>