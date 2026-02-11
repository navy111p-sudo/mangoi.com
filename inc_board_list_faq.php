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


if ($BoardCategoryID=="" && $BoardEnableCategory==1){
	$Sql = "select BoardCategoryID from BoardCategories where BoardID=:BoardID order by BoardCategoryOrder asc limit 0,1";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':BoardID', $BoardID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$BoardCategoryID = $Row["BoardCategoryID"];
}


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
		<div class="TopSearch">
        	<span>총 <b><?=$TotalRowCount?></b> 개</span>
            
			<form name="SearchForm" method="get">
			<?
			if ($BoardEnableCategory==1){
			?>
			<select name="BoardCategoryID" id="BoardCategoryID" class="Select" onchange="SearchSubmit()" style="display:none;">
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
				<option value="0" <?if ($BoardCategoryID=="0"){?>selected<?}?>>기 타</option>
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


			
            </form>
		</div>
        

		
		<!--- 카테고리 탭 부분 -->
		<?if ($BoardEnableCategory==1){?>
			<style>
			.tab_data{border-bottom:1px solid #1b478a; height:43px; margin-bottom:10px;}
			.tab_data li{float:left; width:160px; height:42px; line-height:42px; text-align:center; border-radius:2px 2px 0 0; border-right:1px solid #1b478a; border-top:1px solid #1b478a; background:#fff; position:relative; font-size:15px; cursor:pointer; color:#1b478a;}
			.tab_data li:nth-child(3){width:240px;}
			.tab_data li:first-child{border-left:1px solid #1b478a;}
			.tab_data li:hover{background:#1b478a; color:#fff; opacity:0.8;}
			.tab_data li:hover span{position:absolute; width:100%; height:1px; background:#fff; left:0; bottom:-1px; display:none;}
			.tab_data li.active{background:#1b478a; color:#fff;}
			.tab_data li.active span{position:absolute; width:100%; height:1px; background:#fff; left:0; bottom:-1px; display:none;}
			.tab_data a{display:block;}
			@media only screen and (max-width : 767px) {
			.tab_data{border-bottom:0; height:auto; overflow:hidden;}
			.tab_data li{width:49%; margin:0 2% 2% 0;border:1px solid #ddd;}
			.tab_data li:nth-child(2n){margin-right:0;}
			.tab_data li:nth-child(3){width:49%;}
			.tab_data li span{display:none;}
			}
			@media screen and (max-width:479px) {
			.tab_data li{font-size:14px; width:49.5%; margin:0 1% 1% 0;}
			.tab_data li:nth-child(3){font-size:12px;width:49.5%; letter-spacing:-1px;}
			}
			</style>
			<ul class="tab_data">
			<?
			$Sql2 = "select * from BoardCategories  where BoardID=:BoardID order by BoardCategoryOrder asc";
			$Stmt2 = $DbConn->prepare($Sql2);
			$Stmt2->bindParam(':BoardID', $BoardID);
			$Stmt2->execute();
			$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

			while($Row2 = $Stmt2->fetch()){
			?>
			<li onclick="ChBoardCategoryID('<?=$Row2["BoardCategoryID"]?>')" class="<?php if ($BoardCategoryID==$Row2["BoardCategoryID"]) {?>active<?}?>"><?=$Row2["BoardCategoryName"]?><span></span></li>
			<?
			}
			$Stmt2 = null;
			?>
			<li onclick="ChBoardCategoryID('0')"  class="<?php if ($BoardCategoryID=="0") {?>active<?}?>">기타<span></span></li>
			</ul>
			<script>
			function ChBoardCategoryID(BoardCategoryID){
				document.SearchForm.BoardCategoryID.value = BoardCategoryID;
				SearchSubmit();
			}
			</script>
		<?}?>
		<!--- 카테고리 탭 부분 -->
		
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table1">
			  <tr>
				<th class="td1 th_color" width="7%">No</th>
				<?if ($BoardEnableCategory==1){?>
				<!--<th class="td5 th_color" style="width:10%;">분류</th>-->
				<?}?>
				<th class="td2 th_color">제목</th>
				<!--th class="td3 th_color" width="11%">작성자</th-->
				<?if ($BoardDateHide!=1) {?>
				<th class="td4 th_color" width="13%">등록일</th>
				<?}?>
				<th class="td5 th_color" width="9%">조회수</th>
				<?if ($BoardCode=="video" && _MEMBER_LEVEL_ID_<=1) {?>
				<th class="td5 th_color">순서</th>
				<?}?>
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
					$IcoSecret = "<img src='images/board/icon_lock.png' style='width:14px;'> ";
					$LinkUrl = "board_password_form.php?ListParam=$ListParam&BoardContentID=".$Row["BoardContentID"]."&BoardCode=$BoardCode&ActionMode=SecretRead";
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
						$BoardCategoryName = "기타";
					}
				}



			?>
			  <tr>
				<td class="td1 border1"><?=$StrListNumber?></td>
				<?if ($BoardEnableCategory==1){?>
				<!--<td class="td5 border1"><?=$BoardCategoryName?></th>-->
				<?}?>
				<td class="td2 title border1"><?=$IcoDepth?><?=$IcoReply?> <?=$IcoSecret?><a href="<?=$LinkUrl?>"><?=$Row["BoardContentSubject"]?></a><?=$StrBoardCommentCount?><?=$IcoNew?></td>
				<!--td class="td3 border1"><?=$Row["BoardContentWriterName"]?></td-->
				<?if ($BoardDateHide!=1) {?>
				<td class="td4 border1"><?=substr($Row["BoardContentRegDateTime"],0,10)?></td>
				<?}?>

				<td class="td5 border1"><?=$Row["BoardContentViewCount"]?></td>

				<?if ($BoardCode=="video" && _MEMBER_LEVEL_ID_<=1 && $Row["BoardContentReplyOrder"]==0 && $Row["BoardContentReplyDepth"]==0 ) {?>
				<td class="td5 border1">
				<a href="javascript:SetOrder(1,<?=$BoardID?>,<?=$Row["BoardContentID"]?>);"><img src="images/order_up.gif"></a><br>
				<a href="javascript:SetOrder(0,<?=$BoardID?>,<?=$Row["BoardContentID"]?>);"><img src="images/order_down.gif"></a>
				</td>
				<?}?>
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
		
        <div class="BtnRight"><a href="board_form.php?BoardCode=<?=$BoardCode?>" class="BtnGray">글쓰기</a></div>
		<?php
		}else{
		?>
		<!--div class="BtnRight"><a href="javascript:DenyWrite()" class="Btn2">글쓰기</a></div-->	
		<?php
		}
		?>
	</div>


<script>
function SetOrder(UpDown, BoardID, BoardContentID){
	
	url = "ajax_set_board_list_order.php";

	//location.href = url + "?UpDown="+UpDown+"&BoardID="+BoardID+"&BoardContentID="+BoardContentID;

    $.ajax(url, {
        data: {
			UpDown: UpDown,
			BoardID: BoardID,
			BoardContentID: BoardContentID
        },
        success: function (data) {
			location.reload();
        },
        error: function () {
            alert('Error while contacting server, please try again');
        }

    });


}

function SearchSubmit(){
	document.SearchForm.action = "board_list.php";
	document.SearchForm.submit();
}

function DenyWrite(){
	alert('권한이 없습니다.');
}
</script>