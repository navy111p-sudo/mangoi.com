<?
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
include_once('./inc_header.php');
?>

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
</head>
<body>
<?
$MainCode = 8;
$SubCode = 11;
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
	$PageListNum = 10;
}


$AuthOrderChange=1;

if ($PageListNum!=""){
	$ListParam = $ListParam . "&PageListNum=" . $PageListNum;
}

if ($SearchState==""){
	$SearchState = "1";
}
if ($SearchState!="100"){
	$ListParam = $ListParam . "&SearchState=" . $SearchState;
	$AddSqlWhere = $AddSqlWhere . " and A.FaqState=$SearchState ";
}

if ($SearchText!=""){
	$ListParam = $ListParam . "&SearchText=" . $SearchText;
	$AddSqlWhere = $AddSqlWhere . " and A.FaqTitle like '%".$SearchText."%' ";

	$AuthOrderChange=0;
}


if ($SearchState!="1"){
	$AuthOrderChange=0;
}


$PaginationParam = $ListParam;
if ($CurrentPage!=""){
	$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}

$ListParam = str_replace("&", "^^", $ListParam);



$Sql = "select 
				count(*) TotalRowCount 
		from Faqs A 
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
				A.*
		from Faqs A 
		where ".$AddSqlWhere." order by A.FaqOrder desc";// limit $StartRowNum, $PageListNum";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);



?>


<h1 class="Title">자주묻는질문</h1>

<div class="box_search">
<form name="SearchForm" method="post">
	<select name="SearchState" onchange="SearchSubmit()" style="width:14%;">
		<option value="100" <?if ($SearchState=="100") {echo ("selected");}?>>전체</option>
		<option value="1" <?if ($SearchState=="1") {echo ("selected");}?>>승인</option>
		<option value="2" <?if ($SearchState=="2") {echo ("selected");}?>>미승인</option>
	</select>

	<input type="text" id="search" name="SearchText" value="<?=$SearchText?>" style="width:28%;border:1px solid #cccccc;"/>
	<a href="javascript:SearchSubmit();" class="btn_search"><img src="images/icon_zoom_gray.png"></a>
</form>				
</div>



<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_list" style="table-layout:fixed; width:100%;">
<col width="8%">
<col width="">
<col width="13%">
<col width="10%">
  <tr>
	<th class="TableTh">No</th>
	<th class="TableTh">제목</th>
	<th class="TableTh">상태</th>
	<th class="TableTh2">순서</th>
  </tr>



	<?
	$ListCount = 1;
	while($Row = $Stmt->fetch()) {
		$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

		$FaqID = $Row["FaqID"];
		$FaqTitle = $Row["FaqTitle"];
		$FaqState = $Row["FaqState"];

		if ($FaqState==1){
			$StrFaqState = "승인";
		}else{
			$StrFaqState = "미승인";
		}

	?>
	  <tr>
		<td class="TableTd"><?=$ListNumber?></td>
		<td class="TableTd" style="text-align:left;padding-left:20px;padding-right:20px;text-overflow:ellipsis; overflow:hidden;"><a href="faq_form.php?ListParam=<?=$ListParam?>&FaqID=<?=$FaqID?>" class="color"><nobr><?=$FaqTitle?><nobr></a></td>
		<td class="TableTd"><?=$StrFaqState?></td>
		<td class="TableTd2 arrow">
			<?if ($AuthOrderChange==1){?>
			<a href="javascript:SetFaqOrder(<?=$FaqID?>,0);"><img src="images/arrow_up.png"></a>
			<a href="javascript:SetFaqOrder(<?=$FaqID?>,1);"><img src="images/arrow_down.png"></a>	
			<?}?>
		</td>
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

<div class="btn_right" style="padding-top:25px;">
	<a href="faq_form.php" class="btn red">등록하기</a>
</div>

<script>
function SetFaqOrder(FaqID, OrderType){
		url = "./ajax_set_faq_order.php";
		//location.href = url + "?FaqID="+FaqID+"&OrderType="+OrderType;
		
		$.ajax(url, {
			data: {
				FaqID: FaqID,
				OrderType: OrderType
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
	document.SearchForm.action = "faq_list.php";
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