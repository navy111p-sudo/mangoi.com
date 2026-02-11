<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
include_once('./inc_header.php');
?>
</head>
<body>
<?php
$MainCode = 7;
$SubCode = 3;
include_once('./inc_top.php');
?>

<?php
$Sql = "select 
			*
		from TrnSetup where TrnSetupID=1";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TrnCollectDomain = $Row["TrnCollectDomain"];
$TrnCollectTextMode = $Row["TrnCollectTextMode"];
$TrnCollectTextExplodeIndex = $Row["TrnCollectTextExplodeIndex"];
$TrnCollectByFullUrl = $Row["TrnCollectByFullUrl"];
$TrnSiteLocCode = $Row["TrnSiteLocCode"];
$TrnIndexCode = $Row["TrnIndexCode"];
$TrnIndexCodeCommon = $Row["TrnIndexCodeCommon"];
$TrnIndexCodeCommonUrl = $Row["TrnIndexCodeCommonUrl"];
$TrnTranslationMode = $Row["TrnTranslationMode"];
$TrnTranslationModeApp = $Row["TrnTranslationModeApp"];
$TrnRunType = $Row["TrnRunType"];
$TrnDefaultLanguageID = $Row["TrnDefaultLanguageID"];
$TrnSetupModiDateTime = $Row["TrnSetupModiDateTime"];

$Sql = "select 
			count(*) as TrnLanguageCount
		from TrnLanguages where TrnLanguageState=1";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$TrnLanguageCount = $Row["TrnLanguageCount"];
if ($TrnLanguageCount==0){
	$TrnDefaultLanguageID=0;
}
?>
	

<h1 class="Title" style="margin-bottom:20px;">번역 설정</h1>

<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" accept-charset="UTF-8" autocomplete="off">
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_form" style="margin-bottom:15px;">
  <tr>
	<th style="width:180px;">수집할 도메인<span></span></th>
	<td><input type="text" id="TrnCollectDomain" name="TrnCollectDomain" value="<?=$TrnCollectDomain?>" style="width:300px;background-color:#888888;color:#ffffff;" readonly></td>
  </tr>
  <tr>
	<th>수집할 문구 구분자<span></span></th>
	<td><input type="text" id="TrnCollectTextExplodeIndex" name="TrnCollectTextExplodeIndex" value="<?=$TrnCollectTextExplodeIndex?>" style="width:300px;background-color:#888888;color:#ffffff;" readonly></td>
  </tr>
  <tr>
	<th>사이트 제작 언어코드<span></span></th>
	<td><input type="text" id="TrnSiteLocCode" name="TrnSiteLocCode" value="<?=$TrnSiteLocCode?>" style="width:300px;background-color:#888888;color:#ffffff;" readonly></td>
  </tr>
  <tr>
	<th>수집대상 코드<span></span></th>
	<td><input type="text" id="TrnIndexCode" name="TrnIndexCode" value="<?=$TrnIndexCode?>" style="width:300px;background-color:#888888;color:#ffffff;" readonly></td>
  </tr>
  <tr>
	<th>수집대상 코드(공통)<span></span></th>
	<td><input type="text" id="TrnIndexCodeCommon" name="TrnIndexCodeCommon" value="<?=$TrnIndexCodeCommon?>" style="width:300px;background-color:#888888;color:#ffffff;" readonly></td>
  </tr>

  <tr>
	<th>전체 주소로 수집<span></span></th>
	<td>
		<textarea id="TrnCollectByFullUrl" name="TrnCollectByFullUrl"><?=$TrnCollectByFullUrl?></textarea>
		<div style="margin-top:10px;">※ 상세주소(?xxx=1&yyy=3 등)가 포함된 주소로 수집할 파일명</div>
		<div style="margin-top:10px;">※ 여러개일경우 엔터로 구분합니다</div>
	</td>
  </tr>

  <tr>
	<th>공통 수집 URL<span></span></th>
	<td><input type="text" id="TrnIndexCodeCommonUrl" name="TrnIndexCodeCommonUrl" value="<?=$TrnIndexCodeCommonUrl?>" style="width:300px;"></td>
  </tr>



  <tr>
	<th>수집 실행<span></span></th>
	<td class="radio">
		<input type="radio" name="TrnCollectTextMode" id="TrnCollectTextMode1" value="1" <?php if ($TrnCollectTextMode==1) {echo ("checked");}?>> <label for="TrnCollectTextMode1"><span></span>자동수집</label>
		<input type="radio" name="TrnCollectTextMode" id="TrnCollectTextMode2" value="2" <?php if ($TrnCollectTextMode==2) {echo ("checked");}?>> <label for="TrnCollectTextMode2"><span></span>수동수집</label>
		<input type="radio" name="TrnCollectTextMode" id="TrnCollectTextMode0" value="0" <?php if ($TrnCollectTextMode==0) {echo ("checked");}?>> <label for="TrnCollectTextMode0"><span></span>수집안함</label>
		<div style="margin-top:10px;">※ 수동수집은 <span style="color:#bb0000;">tmsmaster</span> 로 로그인한 후 해당 페이지에서 수집하기 버튼을 눌렀을때 수집합니다.</div>
		<div style="margin-top:10px;">※ 앱은 위 설정과 관계없이 <span style="color:#bb0000;">tmsmaster</span> 로 로그인 하면 자동으로 수집하게 되고</div>
		<div style="margin-top:10px;padding-left:20px;">일반 아이디로 로그인한 경우는 수집되지 않습니다.</div>
	</td>
  </tr>
  <tr>
	<th>번역 실행<span></span></th>
	<td class="radio">
		<input type="radio" name="TrnTranslationMode" id="TrnTranslationMode1" value="1" <?php if ($TrnTranslationMode==1) {echo ("checked");}?>> <label for="TrnTranslationMode1"><span></span>번역실행</label>
		<input type="radio" name="TrnTranslationMode" id="TrnTranslationMode2" value="2" <?php if ($TrnTranslationMode==2) {echo ("checked");}?>> <label for="TrnTranslationMode2"><span></span>미리보기</label>
		<input type="radio" name="TrnTranslationMode" id="TrnTranslationMode0" value="0" <?php if ($TrnTranslationMode==0) {echo ("checked");}?>> <label for="TrnTranslationMode0"><span></span>번역안함</label>
		<div style="margin-top:10px;">※ 미리보기는 <span style="color:#bb0000;">tmsmaster</span> 로 로그인 했을때만 실행합니다.</div>
	</td>
  </tr>


  <tr>
	<th>App 번역 실행<span></span></th>
	<td class="radio">
		<input type="radio" name="TrnTranslationModeApp" id="TrnTranslationModeApp1" value="1" <?php if ($TrnTranslationModeApp==1) {echo ("checked");}?>> <label for="TrnTranslationModeApp1"><span></span>번역실행</label>
		<input type="radio" name="TrnTranslationModeApp" id="TrnTranslationModeApp0" value="0" <?php if ($TrnTranslationModeApp==0) {echo ("checked");}?>> <label for="TrnTranslationModeApp0"><span></span>번역안함</label>
		<div style="margin-top:10px;">※ 앱은 <span style="color:#bb0000;">tmsmaster</span> 로 로그인한 경우에는 항상 번역이 실행 되고</div>
		<div style="margin-top:10px;padding-left:20px;">일반 아이디로 로그인한 경우는 위 설정이 '번역실행'인 경우만 번역이 됩니다.</div>
	</td>
  </tr>


  <tr>
	<th>번역 실행 방식<span></span></th>
	<td class="radio">
		버튼클릭 방식
		<div style="display:none;">
		<input type="radio" name="TrnRunType" id="TrnRunType1" value="1" <?php if ($TrnRunType==1) {echo ("checked");}?>> <label for="TrnRunType1"><span></span>브라우져 언어코드 기반</label>
		<input type="radio" name="TrnRunType" id="TrnRunType2" value="2" <?php if ($TrnRunType==2) {echo ("checked");}?>> <label for="TrnRunType2"><span></span>버튼클릭 방식</label>
		</div>
	</td>
  </tr>
  <tr>
	<th>번역 언어 없을경우<span></span></th>
	<td class="radio">
		<input type="radio" name="TrnDefaultLanguageID" id="TrnDefaultLanguageID0" value="0" <?php if ($TrnDefaultLanguageID==0) {echo ("checked");}?>> <label for="TrnDefaultLanguageID0"><span></span>사이트제작언어</label>
		<?
		$Sql2 = "select 
				A.*
		from TrnLanguages A 
		where A.TrnLanguageState=1 order by A.TrnLanguageOrder asc";

		$Stmt2 = $DbConn->prepare($Sql2);
		$Stmt2->execute();
		$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
		while($Row2 = $Stmt2->fetch()) {
			$TrnLanguageID = $Row2["TrnLanguageID"];
			$TrnLanguageName = $Row2["TrnLanguageName"];
		?>
		<input type="radio" name="TrnDefaultLanguageID" id="TrnDefaultLanguageID<?=$TrnLanguageID?>" value="<?=$TrnLanguageID?>" <?php if ($TrnDefaultLanguageID==$TrnLanguageID) {echo ("checked");}?>> <label for="TrnDefaultLanguageID<?=$TrnLanguageID?>"><span></span><?=$TrnLanguageName?></label>
		<?
		}
		$Stmt2 = null;
		?>
	</td>
  </tr>



</table>
</form>


<div class="btn_center" style="padding-top:25px;">
	<a href="javascript:FormSubmit();" class="btn red">등록하기</a>
	<a href="javascript:history.go(-1);" class="btn gray">취소하기</a>
</div>


	
	
	



<script language="javascript">
function FormSubmit(){
	obj = document.RegForm.TrnCollectDomain;
	if (obj.value==""){
		alert('수집할 도메인을 입력하세요.');
		obj.focus();
		return;
	}

	obj = document.RegForm.TrnCollectTextExplodeIndex;
	if (obj.value==""){
		alert('수집할 문구 구분자를 입력하세요.');
		obj.focus();
		return;
	}

	obj = document.RegForm.TrnSiteLocCode;
	if (obj.value==""){
		alert('사이트 제작 언어코드를 입력하세요.');
		obj.focus();
		return;
	}

	obj = document.RegForm.TrnIndexCode;
	if (obj.value==""){
		alert('수집대상 코드를 입력하세요.');
		obj.focus();
		return;
	}

	obj = document.RegForm.TrnIndexCodeCommon;
	if (obj.value==""){
		alert('수집대상 코드(공통)을 입력하세요.');
		obj.focus();
		return;
	}

	obj = document.RegForm.TrnIndexCodeCommonUrl;
	if (obj.value==""){
		alert('공통수집 URL을 입력하세요.');
		obj.focus();
		return;
	}

	if (confirm("등록 하시겠습니까?")){
		document.RegForm.action = "trn_setup_action.php";
		document.RegForm.submit();
	}


}

</script>




<?php
include_once('./inc_bottom.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>