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
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$TrnCollectUrlID = isset($_REQUEST["TrnCollectUrlID"]) ? $_REQUEST["TrnCollectUrlID"] : "";

$Sql = "select * from TrnCollectUrls where TrnCollectUrlID=:TrnCollectUrlID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':TrnCollectUrlID', $TrnCollectUrlID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row  = $Stmt->fetch();
$Stmt = null;
$TrnCollectUrl = $Row["TrnCollectUrl"];
$TrnCollectUrlName = $Row["TrnCollectUrlName"];



$Sql = "select count(*) as TotalRowCount from TrnCollectTexts A where A.TrnCollectUrlID=:TrnCollectUrlID and A.TrnCollectTextState=1 and trim(replace(A.TrnCollectText,'\r\n',''))<>''";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':TrnCollectUrlID', $TrnCollectUrlID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row  = $Stmt->fetch();
$Stmt = null;
$TotalRowCount = $Row["TotalRowCount"];


$Sql = "select 
				A.*,
				(select count(*) from TrnTranslationTexts AA inner join TrnLanguages BB on AA.TrnLanguageID=BB.TrnLanguageID where AA.TrnCollectTextID=A.TrnCollectTextID and AA.TrnTranslationTextState=1 and trim(replace(AA.TrnTranslationText,'\r\n',''))<>'' and BB.TrnLanguageState=1) as TrnTranslationTextCount
		from TrnCollectTexts A 
		where A.TrnCollectUrlID=:TrnCollectUrlID and A.TrnCollectTextState=1 and trim(replace(A.TrnCollectText,'\r\n',''))<>'' order by A.TrnCollectTextType desc, A.TrnCollectTextOrder desc";

$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':TrnCollectUrlID', $TrnCollectUrlID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
?>
		
<?if ($TrnCollectUrlID=="0" || $TrnCollectUrlID=="1"){?>
	<h2 class="Title" style="margin-bottom:20px;">번역목록 - <?=$TrnCollectUrl?></h2>
<?}else{?>
	<?if ($SearchTrnCollectUrlDviceType=="1"){?>
	<h2 class="Title" style="margin-bottom:20px;">번역목록 - <a href="<?=$TrnCollectUrl?>" target="_blank"><?=$TrnCollectUrl?></a></h2>
	<?}else{?>
	<h2 class="Title" style="margin-bottom:20px;">번역목록 - <?=$TrnCollectUrl?></h2>
	<?}?>
<?}?>

<div class="box_search" style="height:10px;"></div>


<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_list">
<col width="8%">
<col width="10%">
<col width="">
<col width="15%">
<col width="15%">
  <tr>
	<th class="TableTh">No</th>
	<th class="TableTh">구분</th>
	<th class="TableTh">TEXT(태그를 제외한 텍스트)</th>
	<th class="TableTh">번역된 언어수</th>
	<th class="TableTh2">번역관리</th>
  </tr>
<?
$ListCount = 1;
while($Row = $Stmt->fetch()) {
	$ListNumber = $TotalRowCount-$ListCount+1;

	$TrnCollectTextType = $Row["TrnCollectTextType"];
	$TrnCollectUrlID = $Row["TrnCollectUrlID"];
	$TrnCollectTextID = $Row["TrnCollectTextID"];
	$TrnCollectText = $Row["TrnCollectText"];

	$TrnTranslationTextCount = $Row["TrnTranslationTextCount"];

	$StrTrnCollectText = strip_tags($TrnCollectText);

	if ($TrnCollectTextType==1){
		$StrTrnCollectTextType = "페이지";
	}else{
		$StrTrnCollectTextType = "공통";
	}
?>
	  <tr>
		<td class="TableTd"><?=$ListNumber?></td>
		<td class="TableTd"><?=$StrTrnCollectTextType?></td>
		<td class="TableTd" style="text-align:left;padding-left:20px;padding-right:20px;line-height:1.5;"><a href="javascript:OpenCollectTextForm(<?=$TrnCollectTextID?>, <?=$SearchTrnCollectUrlDviceType?>, <?=$TrnCollectUrlID?>)"><?=$StrTrnCollectText?></a></td>
		<td class="TableTd"><?=$TrnTranslationTextCount?></td>
		<td class="TableTd2"><a href="javascript:OpenCollectTextForm(<?=$TrnCollectTextID?>, <?=$SearchTrnCollectUrlDviceType?>, <?=$TrnCollectUrlID?>)">번역관리</a></td>
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
<?if ($TrnCollectUrlID=="0" || $TrnCollectUrlID=="1"){?>
<div style="margin-top:10px;">※ 동일한 TEXT가 여러번 등록 되었을경우 마지막에 등록한 TEXT의 번역을 따릅니다.</div>
<?}?>

<div class="btn_center" style="padding-top:25px;">
	<a href="collect_url_list.php?<?=str_replace("^^","&",$ListParam)?>" class="btn gray">이전으로</a> 
	<?if ($TrnCollectUrlID=="0" || $TrnCollectUrlID=="1"){?>
	<a href="javascript:OpenCollectTextForm(0, <?=$SearchTrnCollectUrlDviceType?>, <?=$TrnCollectUrlID?>);" class="btn red">경고문구등록</a>
	<?}?>
</div>


<script>
function OpenCollectTextForm(TrnCollectTextID, TrnCollectUrlDviceType, TrnCollectUrlID){
	openurl = "./pop_collect_text_form.php?TrnCollectTextID="+TrnCollectTextID+"&TrnCollectUrlDviceType="+TrnCollectUrlDviceType+"&TrnCollectUrlID="+TrnCollectUrlID;
	$.colorbox({	
		href:openurl
		,width:"1000" 
		,height:"95%"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}   
	}); 
}

</script>



<?
include_once('./inc_bottom.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>