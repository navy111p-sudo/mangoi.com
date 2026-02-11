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
$MainCode = 3;
$SubCode = 1;

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
	$PageListNum = 30;
}

if ($PageListNum!=""){
	$ListParam = $ListParam . "&PageListNum=" . $PageListNum;
}

if ($SearchState==""){
	$SearchState = "1";
}			
if ($SearchState!="100"){
	$ListParam = $ListParam . "&SearchState=" . $SearchState;
	$AddSqlWhere = $AddSqlWhere . " and A.TrnBrowserLocCodeState=$SearchState ";
}
$AddSqlWhere = $AddSqlWhere . " and A.TrnBrowserLocCodeState<>0 ";


if ($SearchText!=""){
	$ListParam = $ListParam . "&SearchText=" . $SearchText;
	$AddSqlWhere = $AddSqlWhere . " and (A.TrnBrowserLocCodeName like '%".$SearchText."%' or A.TrnBrowserLocCode like '%".$SearchText."%'  or B.TrnLanguageName like '%".$SearchText."%') ";
}

$PaginationParam = $ListParam;
if ($CurrentPage!=""){
	$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}

$ListParam = str_replace("&", "^^", $ListParam);

$Sql = "select 
				count(*) TotalRowCount 
		from TrnBrowserLocCodes A 
			inner join TrnLanguages B on A.TrnLanguageID=B.TrnLanguageID 
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
				B.TrnLanguageName
		from TrnBrowserLocCodes A 
			inner join TrnLanguages B on A.TrnLanguageID=B.TrnLanguageID 
		where ".$AddSqlWhere." order by A.TrnBrowserLocCodeOrder asc";// limit $StartRowNum, $PageListNum";

$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);


?>
		
<h2 class="Title">브라우져코드목록</h2>


<div class="box_search" style="margin-top:20px;">
<form name="SearchForm" method="get">
	
	<div style="margin-bottom:5px;">

		<div style="display:inline-block;width:150px;">
			상태 <select name="SearchState" onchange="SearchSubmit()" style="width:110px;">
				<option value="100" <?if ($SearchState=="100") {echo ("selected");}?>>전체</option>
				<option value="1" <?if ($SearchState=="1") {echo ("selected");}?>>등록</option>
				<option value="2" <?if ($SearchState=="2") {echo ("selected");}?>>미등록</option>
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
<col width="20%">
<col width="">
<col width="20%">
<col width="8%">
  <tr>
	<th class="TableTh">No</th>
	<th class="TableTh">브라우져코드</th>
	<th class="TableTh">브라우져코드명</th>
	<th class="TableTh">번역언어</th>
	<th class="TableTh2">상태</th>
  </tr>
<?
$ListCount = 1;
while($Row = $Stmt->fetch()) {
	$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

	$TrnLanguageID = $Row["TrnLanguageID"];
	$TrnBrowserLocCodeID = $Row["TrnBrowserLocCodeID"];
	$TrnBrowserLocCode = $Row["TrnBrowserLocCode"];
	$TrnBrowserLocCodeName = $Row["TrnBrowserLocCodeName"];
	$TrnBrowserLocCodeOrder = $Row["TrnBrowserLocCodeOrder"];
	$TrnBrowserLocCodeState = $Row["TrnBrowserLocCodeState"];

	$TrnLanguageName = $Row["TrnLanguageName"];

	if ($TrnBrowserLocCodeState==1){
		$StrTrnBrowserLocCodeState = "등록";
	}else{
		$StrTrnBrowserLocCodeState = "미등록";
	}
?>
	  <tr>
		<td class="TableTd"><?=$ListCount?></td>
		<td class="TableTd"><?=$TrnBrowserLocCode?></td>
		<td class="TableTd"><a href="javascript:OpenBrowserLocCodeForm(<?=$TrnBrowserLocCodeID?>);"><?=$TrnBrowserLocCodeName?></a></td>
		<td class="TableTd"><?=$TrnLanguageName?></td>
		<td class="TableTd2"><?=$StrTrnBrowserLocCodeState?></td>
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
<div style="margin-top:10px;">※ 브라우져 코드는 번역실행 방식이 브라우져 언어코드 기반일 때 유효합니다.</div>



<div class="btn_right" style="padding-top:25px;">
	<a href="javascript:OpenBrowserLocCodeForm('');" class="btn red">브라우져코드등록</a>
</div>



<script>
function OpenBrowserLocCodeForm(TrnBrowserLocCodeID){
	openurl = "./pop_browser_loc_code_form.php?TrnBrowserLocCodeID="+TrnBrowserLocCodeID;
	$.colorbox({	
		href:openurl
		,width:"800" 
		,height:"500"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}   
	}); 
}

function SearchSubmit(){
	document.SearchForm.action = "browser_loc_code_list.php";
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