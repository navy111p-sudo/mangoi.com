<?php
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
<?php
$MainCode = 7;
$SubCode = 4;
include_once('./inc_top.php');
?>


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
	
if ($SearchState!="100"){
	$ListParam = $ListParam . "&SearchState=" . $SearchState;
	$AddSqlWhere = $AddSqlWhere . " and A.MemberState=$SearchState ";
}
$AddSqlWhere = $AddSqlWhere . " and A.MemberState<>0";

if ($SearchText!=""){
	$ListParam = $ListParam . "&SearchText=" . $SearchText;
	$AddSqlWhere = $AddSqlWhere . " and (A.MemberName like '%".$SearchText."%' or A.MemberLoginID like '%".$SearchText."%') ";
}
$AddSqlWhere = $AddSqlWhere . " and A.MemberLevelID=2";//직원


$PaginationParam = $ListParam;
if ($CurrentPage!=""){
	$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}

$ListParam = str_replace("&", "^^", $ListParam);

$Sql = "select 
				count(*) TotalRowCount 
		from Members A 
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
				AES_DECRYPT(UNHEX(A.MemberPhone1),:EncryptionKey) as DecMemberPhone1,
				AES_DECRYPT(UNHEX(A.MemberPhone2),:EncryptionKey) as DecMemberPhone2,
				AES_DECRYPT(UNHEX(A.MemberPhone3),:EncryptionKey) as DecMemberPhone3
		from Members A 
		where ".$AddSqlWhere." order by A.MemberRegDateTime desc limit $StartRowNum, $PageListNum";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);



?>


<h1 class="Title">직원 목록</h1>

<div class="box_search">
<form name="SearchForm" method="post">
	<select name="SearchState" onchange="SearchSubmit()" style="width:14%;">
		<option value="100" <?php if ($SearchState=="100") {echo ("selected");}?>>전체</option>
		<option value="1" <?php if ($SearchState=="1") {echo ("selected");}?>>승인</option>
		<option value="2" <?php if ($SearchState=="2") {echo ("selected");}?>>미승인</option>
	</select>
	<input type="text" id="search" name="SearchText" value="<?=$SearchText?>" style="width:28%;border:1px solid #cccccc;"/>
	<a href="javascript:SearchSubmit();" class="btn_search"><img src="images/icon_zoom_gray.png"></a>
</form>				
</div>



<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_list">
<col width="8%">
<col width="15%">
<!--<col width="10%">-->
<col width="">
<col width="">
<!--<col width="15%">-->
<col width="8%">
  <tr>
	<th class="TableTh">No</th>
	<th class="TableTh">직책</th>
	<!--<th class="TableTh">담당</th>-->
	<th class="TableTh">이름</th>
	<th class="TableTh">아이디</th>
	<!--<th class="TableTh">전화번호</th>-->
	<th class="TableTh2">상태</th>
  </tr>



	<?php
	$ListCount = 1;
	while($Row = $Stmt->fetch()) {
		$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

		$MemberDepartmentType = $Row["MemberDepartmentType"];


		if ($Row["MemberState"]==1){
			$str_staff_state = "승인";
		}else{
			$str_staff_state = "미승인";
		}
	?>
	  <tr>
		<td class="TableTd"><?=$ListNumber?></td>
		<td class="TableTd"><?=$Row["MemberPositionName"]?></td>
		<!--<td class="TableTd"><?=$StrMemberDepartmentType?></td>-->
		<td class="TableTd"><a href="staff_form.php?ListParam=<?=$ListParam?>&MemberID=<?=$Row["MemberID"]?>" class="color"><?=$Row["MemberName"]?></a></td>
		<td class="TableTd"><a href="staff_form.php?ListParam=<?=$ListParam?>&MemberID=<?=$Row["MemberID"]?>" class="color"><?=$Row["MemberLoginID"]?></a></td>
		<!--<td class="TableTd"><?=$Row["DecMemberPhone1"]?></td>-->
		<td class="TableTd2"><?=$str_staff_state?></td>
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

<div class="btn_right" style="padding-top:25px;">
	<a href="staff_form.php" class="btn red">등록하기</a>
</div>



<script>
function SearchSubmit(){
	document.SearchForm.action = "staff_list.php";
	document.SearchForm.submit();
}
</script>

<?php
include_once('./inc_bottom.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>