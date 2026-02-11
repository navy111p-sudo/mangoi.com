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
$TrnBrowserLocCodeID = isset($_REQUEST["TrnBrowserLocCodeID"]) ? $_REQUEST["TrnBrowserLocCodeID"] : "";


if ($TrnBrowserLocCodeID!=""){

	$Sql = "select 
		A.*
		from TrnBrowserLocCodes A 
		where A.TrnBrowserLocCodeID=:TrnBrowserLocCodeID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TrnBrowserLocCodeID', $TrnBrowserLocCodeID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$TrnLanguageID = $Row["TrnLanguageID"];
	$TrnBrowserLocCode = $Row["TrnBrowserLocCode"];
	$TrnBrowserLocCodeName = $Row["TrnBrowserLocCodeName"];
	$TrnBrowserLocCodeOrder = $Row["TrnBrowserLocCodeOrder"];
	$TrnBrowserLocCodeState = $Row["TrnBrowserLocCodeState"];

}else{
	$TrnLanguageID = "";
	$TrnBrowserLocCode = "";
	$TrnBrowserLocCodeName = "";
	$TrnBrowserLocCodeState = 1;
}
?>

<h1 class="Title" style="margin-bottom:0px;">브라우져코드정보</h1>

<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" accept-charset="UTF-8" autocomplete="off">
<input type="hidden" name="TrnBrowserLocCodeID" value="<?=$TrnBrowserLocCodeID?>">
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_form" style="margin-bottom:15px;">

  <tr>
	<th>브라우져코드명<span></span></th>
	<td>
		<input type="text" id="TrnBrowserLocCodeName" name="TrnBrowserLocCodeName" value="<?=$TrnBrowserLocCodeName?>" style="width:200px;"/>
	</td>
  </tr>

  <tr>
	<th>브라우져코드<span></span></th>
	<td style="line-height;1.5;">
		<input type="text" id="TrnBrowserLocCode" name="TrnBrowserLocCode" value="<?=$TrnBrowserLocCode?>" style="width:200px;"/>
		<div style="margin-top:10px;">※ 코드 앞, 뒤 또는 양쪽에 % 를 붙이면 해당 문자가 포함되는 코드임.</div>
	</td>
  </tr>

  <tr>
	<th>번역언어<span></span></th>
	<td>
		<select id="TrnLanguageID" name="TrnLanguageID" style="width:200px;">
		<option value=""></option>
		<?
		$Sql2 = "select 
				A.*
		from TrnLanguages A 
		where A.TrnLanguageState=1 order by A.TrnLanguageOrder asc";

		$Stmt2 = $DbConn->prepare($Sql2);
		$Stmt2->execute();
		$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
		while($Row2 = $Stmt2->fetch()) {
		?>
		<option value="<?=$Row2["TrnLanguageID"]?>" <?if ($TrnLanguageID==$Row2["TrnLanguageID"]){?>selected<?}?>><?=$Row2["TrnLanguageName"]?></option>
		<?
		}
		$Stmt2 = null;
		?>
		</select>
	</td>
  </tr>

  <tr style="display:<?if ($TrnBrowserLocCodeID==""){?>none<?}?>;">
	<th>상태<span></span></th>
	<td class="radio" colspan="2">
		<input type="radio" name="TrnBrowserLocCodeState" id="TrnBrowserLocCodeState1" value="1" <?php if ($TrnBrowserLocCodeState==1) {echo ("checked");}?>> <label for="TrnBrowserLocCodeState1"><span></span>승인</label>
		<input type="radio" name="TrnBrowserLocCodeState" id="TrnBrowserLocCodeState2" value="2" <?php if ($TrnBrowserLocCodeState==2) {echo ("checked");}?>> <label for="TrnBrowserLocCodeState2"><span></span>미승인</label>
		<input type="radio" name="TrnBrowserLocCodeState" id="TrnBrowserLocCodeState0" value="0" <?php if ($TrnBrowserLocCodeState==0) {echo ("checked");}?>> <label for="TrnBrowserLocCodeState0"><span></span>삭제</label>
	</td>
  </tr>
</table>
</form>

<div class="btn_center" style="padding-top:25px;">
	<?if ($TrnBrowserLocCodeID!=""){?>
	<a href="javascript:FormSubmit();" class="btn red">수정하기</a>
	<?}else{?>
	<a href="javascript:FormSubmit();" class="btn red">등록하기</a>
	<?}?>
	<a href="javascript:parent.$.fn.colorbox.close();" class="btn gray">닫기</a>
</div>


<script>

function FormSubmit(){
	
	obj = document.RegForm.TrnBrowserLocCode;
	if (obj.value==""){
		alert("브라우져코드를 입력해 주세요.");
		obj.focus();
		return;
	}


	obj = document.RegForm.TrnBrowserLocCodeName;
	if (obj.value==""){
		alert("브라우져코드명을 입력해 주세요.");
		obj.focus();
		return;
	}


	obj = document.RegForm.TrnLanguageID;
	if (obj.value==""){
		alert("번역언어를 선택해 주세요.");
		obj.focus();
		return;
	}


	<?if ($TrnBrowserLocCodeID!=""){?>
	ConfrimMsg = "수정 하시겠습니까?";
	<?}else{?>
	ConfrimMsg = "등록 하시겠습니까?";
	<?}?>

	if (confirm(ConfrimMsg)){
		document.RegForm.action = "./pop_browser_loc_code_action.php"
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