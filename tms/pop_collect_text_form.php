<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
include_once('./inc_header.php');
?>

</head>
<body style="padding:20px;">

<?php
$TrnCollectUrlID = isset($_REQUEST["TrnCollectUrlID"]) ? $_REQUEST["TrnCollectUrlID"] : "";
$TrnCollectTextID = isset($_REQUEST["TrnCollectTextID"]) ? $_REQUEST["TrnCollectTextID"] : "";
$TrnCollectUrlDviceType = isset($_REQUEST["TrnCollectUrlDviceType"]) ? $_REQUEST["TrnCollectUrlDviceType"] : "";

if ($TrnCollectTextID!="0"){
	$Sql = "select 
		A.*
		from TrnCollectTexts A 
		where A.TrnCollectTextID=:TrnCollectTextID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TrnCollectTextID', $TrnCollectTextID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$TrnCollectText = $Row["TrnCollectText"];
	$TrnCollectTextState = $Row["TrnCollectTextState"];
}else{
	$TrnCollectText = "";
	$TrnCollectTextState = 1;
}
?>

<h1 class="Title" style="margin-bottom:0px;">번역하기</h1>

<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" accept-charset="UTF-8" autocomplete="off">
<input type="hidden" name="TrnCollectUrlID" value="<?=$TrnCollectUrlID?>">
<input type="hidden" name="TrnCollectTextID" value="<?=$TrnCollectTextID?>">
<input type="hidden" name="TrnCollectUrlDviceType" value="<?=$TrnCollectUrlDviceType?>">
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_form" style="margin-bottom:15px;">

  <?if ($TrnCollectUrlID=="0" || $TrnCollectUrlID=="1"){?>
  <tr>
	<th>원본 TEXT<span></span></th>
	<td style="line-height;1.5;">
		<textarea id="TrnCollectText" name="TrnCollectText" style="width:100%;height:150px;background-color:#888888;color:#ffffff;"><?=$TrnCollectText?></textarea>
		<div style="margin-top:10px;">※ 경고문구 일경우 수정이 가능합니다.</div>
		<div style="margin-top:10px;">※ 경고문구는 실제 페이지에 존재하는 경구문구 일때만 적용됩니다.</div>
		<div style="margin-top:10px;">※ 실제 존재해는 문구(Enter 포함)와 정확히 일치 하여야 합니다. 마지막 문구에 Enter 가 입력되었는지 주의.</div>
	</td>
  </tr>
  <?}else{?>
  <tr>
	<th>원본 TEXT<span></span></th>
	<td style="line-height;1.5;">
		<textarea id="TrnCollectText" name="TrnCollectText" style="width:100%;height:150px;background-color:#888888;color:#ffffff;" readonly onclick="textareacopy()"><?=$TrnCollectText?></textarea>
		<div style="margin-top:10px;">※ 영역을 클릭하면 전체 TEXT가 클립보드에 저장됩니다.</div>
		<div style="margin-top:10px;">※ TEXT에 줄바꿈이 있거나 너무 길고 복잡하면 번역기가 정상작동되지 않습니다.</div>
	</td>
  </tr>
  <?}?>

	<?
	$Sql = "select 
					A.*
			from TrnLanguages A 
			where A.TrnLanguageState=1 order by A.TrnLanguageOrder asc";

	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	while($Row = $Stmt->fetch()) {

		$TrnLanguageID = $Row["TrnLanguageID"];
		$TrnLanguageName = $Row["TrnLanguageName"];

		$Sql2 = "select 
			A.*
			from TrnTranslationTexts A 
			where A.TrnLanguageID=:TrnLanguageID and A.TrnCollectTextID=:TrnCollectTextID";
		$Stmt2 = $DbConn->prepare($Sql2);
		$Stmt2->bindParam(':TrnLanguageID', $TrnLanguageID);
		$Stmt2->bindParam(':TrnCollectTextID', $TrnCollectTextID);
		$Stmt2->execute();
		$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
		$Row2 = $Stmt2->fetch();
		$Stmt2 = null;
		$TrnTranslationText = $Row2["TrnTranslationText"];
	?>
	<tr>
		<th><?=$TrnLanguageName?> TEXT<span></span></th>
		<td style="line-height;1.5;">
			<textarea id="TrnTranslationText_<?=$TrnCollectTextID?>_<?=$TrnLanguageID?>" name="TrnTranslationText_<?=$TrnCollectTextID?>_<?=$TrnLanguageID?>" style="width:100%;height:150px;"><?=$TrnTranslationText?></textarea>
		</td>
	</tr>
	<?
	}
	$Stmt = null;
	?>

  <tr style="display:<?if (($TrnCollectUrlID=="0" || $TrnCollectUrlID=="1") && $TrnCollectTextID!="0"){?><?}else{?>none<?}?>;">
	<th>상태<span></span></th>
	<td class="radio" colspan="2">
		<input type="radio" name="TrnCollectTextState" id="TrnCollectTextState1" value="1" <?php if ($TrnCollectTextState==1) {echo ("checked");}?>> <label for="TrnCollectTextState1"><span></span>사용</label>
		<input type="radio" name="TrnCollectTextState" id="TrnCollectTextState0" value="0" <?php if ($TrnCollectTextState==0) {echo ("checked");}?>> <label for="TrnCollectTextState0"><span></span>삭제</label>
	</td>
  </tr>

</table>
</form>

<div class="btn_center" style="padding-top:25px;">
	<a href="javascript:FormSubmit();" class="btn red">등록하기</a>
	<a href="javascript:parent.$.fn.colorbox.close();" class="btn gray">닫기</a>
</div>


<script>
function textareacopy() {
  let textarea = document.getElementById("TrnCollectText");
  textarea.select();
  document.execCommand("copy");
  alert("클립보드에 저장 되었습니다.");
}


function FormSubmit(){

	<?if ($TrnCollectUrlID=="0" || $TrnCollectUrlID=="1"){?>
	obj = document.RegForm.TrnCollectText;
	if (obj.value==""){
		alert("원본 TEXT 를 입력해 주세요.");
		obj.focus();
		return;
	}
	<?}?>


	ConfrimMsg = "등록 하시겠습니까?";

	if (confirm(ConfrimMsg)){
		document.RegForm.action = "./pop_collect_text_action.php"
		document.RegForm.submit(); 
	}

}
</script>



<?php
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>