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
$TrnLanguageID = isset($_REQUEST["TrnLanguageID"]) ? $_REQUEST["TrnLanguageID"] : "";


if ($TrnLanguageID!=""){

	$Sql = "select 
		A.*
		from TrnLanguages A 
		where A.TrnLanguageID=:TrnLanguageID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TrnLanguageID', $TrnLanguageID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$TrnLanguageID = $Row["TrnLanguageID"];
	$TrnLanguageName = $Row["TrnLanguageName"];
	$TrnLanguageOrder = $Row["TrnLanguageOrder"];
	$TrnLanguageState = $Row["TrnLanguageState"];

}else{
	$TrnLanguageID = "";
	$TrnLanguageName = "";
	$TrnLanguageState = 1;
}
?>

<h1 class="Title" style="margin-bottom:0px;">번역언어정보</h1>

<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" accept-charset="UTF-8" autocomplete="off">
<input type="hidden" name="TrnLanguageID" value="<?=$TrnLanguageID?>">
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_form" style="margin-bottom:15px;">


  <tr>
	<th>번역언어명<span></span></th>
	<td style="line-height;1.5;">
		<input type="text" id="TrnLanguageName" name="TrnLanguageName" value="<?=$TrnLanguageName?>" style="width:200px;"/>
	</td>
  </tr>


  <tr style="display:<?if ($TrnLanguageID==""){?>none<?}?>;">
	<th>상태<span></span></th>
	<td class="radio" colspan="2">
		<input type="radio" name="TrnLanguageState" id="TrnLanguageState1" value="1" <?php if ($TrnLanguageState==1) {echo ("checked");}?>> <label for="TrnLanguageState1"><span></span>승인</label>
		<input type="radio" name="TrnLanguageState" id="TrnLanguageState2" value="2" <?php if ($TrnLanguageState==2) {echo ("checked");}?>> <label for="TrnLanguageState2"><span></span>미승인</label>
		<input type="radio" name="TrnLanguageState" id="TrnLanguageState0" value="0" <?php if ($TrnLanguageState==0) {echo ("checked");}?>> <label for="TrnLanguageState0"><span></span>삭제</label>
	</td>
  </tr>
</table>
</form>

<div class="btn_center" style="padding-top:25px;">
	<?if ($TrnLanguageID!=""){?>
	<a href="javascript:FormSubmit();" class="btn red">수정하기</a>
	<?}else{?>
	<a href="javascript:FormSubmit();" class="btn red">등록하기</a>
	<?}?>
	<a href="javascript:parent.$.fn.colorbox.close();" class="btn gray">닫기</a>
</div>


<script>

function FormSubmit(){
	
	obj = document.RegForm.TrnLanguageName;
	if (obj.value==""){
		alert("번역언어명을 입력해 주세요.");
		obj.focus();
		return;
	}


	<?if ($TrnLanguageID!=""){?>
	ConfrimMsg = "수정 하시겠습니까?";
	<?}else{?>
	ConfrimMsg = "등록 하시겠습니까?";
	<?}?>

	if (confirm(ConfrimMsg)){
		document.RegForm.action = "./pop_language_action.php"
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