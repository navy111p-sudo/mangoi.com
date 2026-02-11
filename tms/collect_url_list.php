<?
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
include_once('./inc_header.php');
?>
</head>
<body>

<style>
.TabBtnActive{display:inline-block;width:70px;height:30px;line-height:30px;text-align:center;margin-top:2px;margin-right:2px;border:1px solid #888888;background-color:#888888;color:#f1f1f1; cursor:pointer;}
.TabBtn{display:inline-block;width:70px;height:30px;line-height:30px;text-align:center;margin-top:2px;margin-right:2px;border:1px solid #888888;background-color:#f1f1f1;color:#888888; cursor:pointer;}
.TableTh{border-left:1px solid #CBCBCB;background-color:#fbfbfb;}
.TableTh2{border-left:1px solid #CBCBCB;background-color:#fbfbfb;border-right:1px solid #CBCBCB;}
.TableTd{border-left:1px solid #CBCBCB;}
.TableTd2{border-left:1px solid #CBCBCB;border-right:1px solid #CBCBCB;}
.TableTd a{text-decoration:none;color:#888888;}
.TableTd2 a{text-decoration:none;color:#888888;}
.table_list .arrow img{display:block; width:25px;margin:0px auto;}
.MemberListBtn {display:inline-block;margin-right:5px;padding:5px;border-radius:5px;background-color:#185A96;color:#ffffff;cursor:pointer;text-align:center;}
</style>

<?
$SearchTrnCollectUrlDviceType = isset($_REQUEST["SearchTrnCollectUrlDviceType"]) ? $_REQUEST["SearchTrnCollectUrlDviceType"] : "";
if ($SearchTrnCollectUrlDviceType==""){
	$SearchTrnCollectUrlDviceType = "1";
}


$MainCode = 1;
if ($SearchTrnCollectUrlDviceType=="1"){
	$SubCode = 1;
}else{
	$SubCode = 2;
}

include_once('./inc_top.php');
?>


<?

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
if ($SearchState!="100"){
	$ListParam = $ListParam . "&SearchState=" . $SearchState;
	$AddSqlWhere = $AddSqlWhere . " and A.TrnCollectUrlState=$SearchState ";
}
$AddSqlWhere = $AddSqlWhere . " and A.TrnCollectUrlState<>0 ";


if ($SearchTrnCollectUrlDviceType!=""){
	$ListParam = $ListParam . "&SearchTrnCollectUrlDviceType=" . $SearchTrnCollectUrlDviceType;
	$AddSqlWhere = $AddSqlWhere . " and A.TrnCollectUrlDviceType=".$SearchTrnCollectUrlDviceType." ";
}


if ($SearchText!=""){
	$ListParam = $ListParam . "&SearchText=" . $SearchText;
	$AddSqlWhere = $AddSqlWhere . " and (A.TrnCollectUrlName like '%".$SearchText."%' or A.TrnCollectUrl like '%".$SearchText."%') ";
}

$PaginationParam = $ListParam;
if ($CurrentPage!=""){
	$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}

$ListParam = str_replace("&", "^^", $ListParam);

$Sql = "select 
				count(*) TotalRowCount 
		from TrnCollectUrls A 
		where ".$AddSqlWhere." ";

$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TotalRowCount = $Row["TotalRowCount"];

$TotalPageCount = ceil($TotalRowCount / $PageListNum);
$StartRowNum = $PageListNum * ($CurrentPage - 1 );



$Sql = "select 
				A.*,
				(select count(*) from TrnCollectTexts where TrnCollectUrlID=A.TrnCollectUrlID and TrnCollectTextState=1) as TrnCollectTextCount
		from TrnCollectUrls A 
		where ".$AddSqlWhere." order by A.TrnCollectUrlGroupOrder asc, A.TrnCollectUrl asc limit $StartRowNum, $PageListNum";

$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);


?>
		
<h2 class="Title">페이지목록</h2>


<div class="box_search" style="margin-top:20px;">
<form name="SearchForm" method="get">
<input type="hidden" id="SearchTrnCollectUrlDviceType" name="SearchTrnCollectUrlDviceType" value="<?=$SearchTrnCollectUrlDviceType?>"/>
	<div style="margin-bottom:5px;">

		<div style="display:inline-block;width:180px;">
			상태 <select name="SearchState" onchange="SearchSubmit()" style="width:140px;">
				<option value="100" <?if ($SearchState=="100") {echo ("selected");}?>>전체</option>
				<option value="1" <?if ($SearchState=="1") {echo ("selected");}?>>등록된 URL</option>
				<option value="2" <?if ($SearchState=="2") {echo ("selected");}?>>삭제된 URL</option>
			</select>
		</div>

		<div style="display:inline-block;width:200px;">			
			<input type="text" id="SearchText" name="SearchText" value="<?=$SearchText?>" style="vertical-align:middle;width:120px;" />
			<a href="javascript:SearchSubmit();" class="btn_search" style="vertical-align:middle;"><img src="images/icon_zoom_gray.png"></a> 
		</div>
	</div>

</form>
</div> 



<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_list">
<col width="8%">
<col width="">
<col width="10%">
<col width="10%">
  <tr>
	<th class="TableTh">No</th>
	<th class="TableTh">페이지 URL</th>
	<th class="TableTh">번역대상</th>
	<th class="TableTh2">관리</th>
  </tr>
<?
$ListCount = 1;
while($Row = $Stmt->fetch()) {
	$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

	$TrnCollectUrlDviceType = $Row["TrnCollectUrlDviceType"];
	$TrnCollectUrlID = $Row["TrnCollectUrlID"];
	$TrnCollectUrl = $Row["TrnCollectUrl"];
	$TrnCollectUrlName = $Row["TrnCollectUrlName"];
	$TrnCollectUrlState = $Row["TrnCollectUrlState"];

	$TrnCollectTextCount = $Row["TrnCollectTextCount"];
	$TrnCollectUrlGroupOrder = $Row["TrnCollectUrlGroupOrder"];

	if ($TrnCollectUrlState==1){
		$StrTrnCollectUrlState = "삭제";
	}else{
		$StrTrnCollectUrlState = "등록";
	}
?>
	  <tr style="<?if ($TrnCollectUrlGroupOrder==0){?>background-color:#FFFFE8;<?}?>">
		<td class="TableTd">
			<?if ($TrnCollectUrlGroupOrder==0){?>
				-
			<?}else{?>
				<?=$ListNumber?>
			<?}?>
		</td>
		<td class="TableTd" style="text-align:left;padding-left:20px;padding-right:20px;line-height:1.5;"><a href="collect_text_list.php?ListParam=<?=$ListParam?>&TrnCollectUrlID=<?=$TrnCollectUrlID?>&SearchTrnCollectUrlDviceType=<?=$SearchTrnCollectUrlDviceType?>"><?=$TrnCollectUrl?></a></td>
		<td class="TableTd"><?=$TrnCollectTextCount?></td>
		<td class="TableTd2">
			<?if ($TrnCollectUrlGroupOrder==0){?>
			-
			<?}else{?>
			<a href="javascript:DeleteCollectUrl(<?=$TrnCollectUrlID?>, <?=$TrnCollectUrlState?>)"><?=$StrTrnCollectUrlState?></a>
			<?}?>
		</td>
	  </tr>
<?
$ListCount ++;
}
$Stmt = null;

if (!$TotalRowCount) {
?>
<tr>
	 <td colspan="10" class="TableTd2">목록이 없습니다</td>
</tr>
<? 
}
?>
</table>


<?
include_once('inc_pagination.php');
?>



<script>
function DeleteCollectUrl(TrnCollectUrlID, TrnCollectUrlState){
	if (TrnCollectUrlState==1){
		ConfirmMsg = "삭제 하시겠습니까? 삭제 후에는 삭제된 URL에서 확인 가능합니다.";
	}else{
		ConfirmMsg = "등록 하시겠습니까? 등록 후에는 등록된 URL에서 확인 가능합니다.";
	}
	
	if (confirm(ConfirmMsg)){
	
		url = "./ajax_set_collect_url_delete.php";
		//location.href = url + "?TrnLanguageID="+TrnLanguageID+"&OrderType="+OrderType+"&SearchState="+SearchState;
		
		$.ajax(url, {
			data: {
				TrnCollectUrlID: TrnCollectUrlID,
				TrnCollectUrlState: TrnCollectUrlState
			},
			success: function (data) {
					
				location.reload();
			},
			error: function () {
				//alert('Error while contacting server, please try again');
			}
		});
	}
}

function SearchSubmit(){
	document.SearchForm.action = "collect_url_list.php";
	document.SearchForm.submit();
}
</script>

<?
include_once('./inc_bottom.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>