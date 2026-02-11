<?
$HttpHost = $_SERVER['HTTP_HOST'];
$RequestUrl = $_SERVER['REQUEST_URI'];
$TrnCollectUrlDviceType = 1;//1:웹 2:앱

$Sql = "select 
			A.*
		from TrnSetup A where A.TrnSetupID=1";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$TrnCollectDomain = $Row["TrnCollectDomain"];
$TrnCollectTextMode = $Row["TrnCollectTextMode"];
$TrnCollectTextExplodeIndex = $Row["TrnCollectTextExplodeIndex"];
$TrnSiteLocCode = $Row["TrnSiteLocCode"];
$TrnIndexCode = $Row["TrnIndexCode"];
$TrnIndexCodeCommon = $Row["TrnIndexCodeCommon"];
$TrnIndexCodeCommonUrl = $Row["TrnIndexCodeCommonUrl"];
$TrnTranslationMode = $Row["TrnTranslationMode"];
$TrnRunType = $Row["TrnRunType"];
$TrnDefaultLanguageID = $Row["TrnDefaultLanguageID"];

$TrnCollectTextExplodeIndex = trim($TrnCollectTextExplodeIndex);
$TrnSiteLocCode = trim($TrnSiteLocCode);
$TrnIndexCode = trim($TrnIndexCode);
$TrnIndexCodeCommon = trim($TrnIndexCodeCommon);
$TrnIndexCodeCommonUrl = trim($TrnIndexCodeCommonUrl);


if (substr($TrnCollectDomain,-1)=="/"){
	$TrnCollectDomain = substr($TrnCollectDomain , 0, -1);
}

$TrnCollectDomain = str_replace("http://", "", $TrnCollectDomain);
$TrnCollectDomain = str_replace("https://", "", $TrnCollectDomain);
?>


<div style="display:none;">
<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" accept-charset="UTF-8" autocomplete="off">
<input type="text" name="TrnCollectUrlDviceType" value="<?=$TrnCollectUrlDviceType?>">
<input type="text" name="TrnCollectUrl" value="<?=$RequestUrl?>">
<input type="text" name="TrnCollectTextExplodeIndex" value="<?=$TrnCollectTextExplodeIndex?>">
<input type="text" name="TrnCollectTextType" value="1">
<textarea name="TrnCollectTexts"></textarea>
</form>
</div>

<!-- script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script -->
<script type="text/javascript" src="/tms/js/translation_i18n/jquery.li18n.js"></script>
<script type="text/javascript">

String.prototype.left = function(length){
	if(this.length <= length){
		return this;
	}else{
		return this.substring(0, length);
	}
}

String.prototype.right = function(length){
	if(this.length <= length){
		return this;
	}else{
		return this.substring(this.length - length, this.length);
	}
}

function GetBrowserLang() {
  var userLang = navigator.language || navigator.userLanguage;
  return userLang;
}


$(document).ready(function() {
	<?
	//수집 : 번역이 실행되기 전에 이부분이 실행되어야 함.======================================================
	if ($TrnCollectTextMode==1 && strpos($HttpHost, $TrnCollectDomain) !== false ){
	?>
		url = "/tms/includes/tms_ajax_set_collect_text.php";
		TrnCollectTextExplodeIndex = "<?=$TrnCollectTextExplodeIndex?>";
		TrnCollectTexts = TrnCollectTextExplodeIndex;

		$(".<?=$TrnIndexCode?>").each(function(idx, obj) {
			TrnCollectText = $(obj).html();
			TrnCollectTexts = TrnCollectTexts + TrnCollectText + TrnCollectTextExplodeIndex;
		});

		
		document.RegForm.TrnCollectTexts.value = TrnCollectTexts;
		//document.RegForm.action = url;
		//document.RegForm.submit();

		if (TrnCollectTexts!=TrnCollectTextExplodeIndex){

			var params =  $("#RegForm").serialize();
			jQuery.ajax({
				url: url,
				type: 'POST',
				data:params,
				contentType: 'application/x-www-form-urlencoded; charset=UTF-8', 
				dataType: 'html',
				success: function (data) {


					<?
					//공통수집 대상 URL 일때 ================================================
					if ($TrnIndexCodeCommonUrl==$RequestUrl){
					?>
						document.RegForm.TrnCollectTextType.value = "2";

						url = "/tms/includes/tms_ajax_set_collect_text.php";
						TrnCollectTextExplodeIndex = "<?=$TrnCollectTextExplodeIndex?>";
						TrnCollectTexts = TrnCollectTextExplodeIndex;

						$(".<?=$TrnIndexCodeCommon?>").each(function(idx, obj) {
							TrnCollectText = $(obj).html();
							TrnCollectTexts = TrnCollectTexts + TrnCollectText + TrnCollectTextExplodeIndex;
						});

						
						document.RegForm.TrnCollectTexts.value = TrnCollectTexts;
						//document.RegForm.action = url;
						//document.RegForm.submit();

						if (TrnCollectTexts!=TrnCollectTextExplodeIndex){

							var params =  $("#RegForm").serialize();
							jQuery.ajax({
								url: url,
								type: 'POST',
								data:params,
								contentType: 'application/x-www-form-urlencoded; charset=UTF-8', 
								dataType: 'html',
								success: function (data) {

								},
								error: function () {

								}
							});

						}

					<?
					}
					//공통수집 대상 URL 일때 ================================================
					?>


				},
				error: function () {

				}
			});

		}



	
	<?
	}
	//수집 : 번역이 실행되기 전에 이부분이 실행되어야 함.======================================================
	?>


	<?
	//번역 : 수집이 완료된 후에 실행되어야 함.======================================================
	if ($TrnTranslationMode==1){
	?>
		
		<?if ($TrnRunType==1){?>
		TrnBrowserLocCode = GetBrowserLang();
		<?}else{?>
		TrnBrowserLocCode = "";//쿠키에서 가져온다.
		<?}?>
		TrnBrowserLocCode = "zh-US";//테스트 임시


		TrnSiteLocCode = "<?=$TrnSiteLocCode?>";
		IncTrnSiteLocCode = TrnSiteLocCode.replace(/\%/gi, "");
		IncTrnSiteLocCodeLen = IncTrnSiteLocCode.length;
		RunTrn = true;
		if (TrnBrowserLocCode!=""){
			if (TrnSiteLocCode.left(1)=="%" && TrnSiteLocCode.right(1)=="%"){
				if (TrnBrowserLocCode.indexOf(IncTrnSiteLocCode) != -1) {
					RunTrn = false;
				}
			}else if (TrnSiteLocCode.left(1)=="%"){
				if (TrnBrowserLocCode.right(IncTrnSiteLocCodeLen) == IncTrnSiteLocCode) {
					RunTrn = false;
				}
			}else if (TrnSiteLocCode.right(1)=="%"){
				if (TrnBrowserLocCode.left(IncTrnSiteLocCodeLen) == IncTrnSiteLocCode) {
					RunTrn = false;
				}
			}else{
				if (TrnSiteLocCode == IncTrnSiteLocCode) {
					RunTrn = false;
				}
			}
		}


		if (RunTrn){
			
			//============================= 페이지부분 =====================================
			TrnCollectTextType="1";
			url = "/tms/includes/tms_ajax_get_translation_text.php";

			//window.open(url + "?TrnCollectTextType="+TrnCollectTextType+"&TrnBrowserLocCode="+TrnBrowserLocCode+"&TrnCollectUrlDviceType=<?=$TrnCollectUrlDviceType?>&TrnCollectUrl=<?=$RequestUrl?>");
			$.ajax(url, {
				data: {
					TrnCollectTextType: TrnCollectTextType,
					TrnCollectUrlDviceType: "<?=$TrnCollectUrlDviceType?>",
					TrnBrowserLocCode: TrnBrowserLocCode,
					TrnCollectUrl: "<?=$RequestUrl?>"
				},
				success: function (data) {

					obj_lang = data.obj_lang;

					$.li18n.translations = {ln:obj_lang};
					$.li18n.currentLocale = "ln";
					
					$(".<?=$TrnIndexCode?>").each(function(idx, obj) {
						$.li18n.onTranslationMissing = function() {
							//return "ln";//번역문을 못찾았을때
						};

						$(obj).html( $.li18n.translate( $(obj).html() ) );
					});



					//============================= 공통부분 =====================================
					TrnCollectTextType="2";
					url = "/tms/includes/tms_ajax_get_translation_text.php";

					//location.href = url + "?TrnCollectTextType="+TrnCollectTextType+"&TrnBrowserLocCode="+TrnBrowserLocCode+"&TrnCollectUrlDviceType=<?=$TrnCollectUrlDviceType?>&TrnCollectUrl=<?=$RequestUrl?>";
					$.ajax(url, {
						data: {
							TrnCollectTextType: TrnCollectTextType,
							TrnCollectUrlDviceType: "<?=$TrnCollectUrlDviceType?>",
							TrnBrowserLocCode: TrnBrowserLocCode,
							TrnCollectUrl: "<?=$RequestUrl?>"
						},
						success: function (data) {

							obj_lang = data.obj_lang;



							$.li18n.translations = {ln:obj_lang};
							$.li18n.currentLocale = "ln";
							
							$(".<?=$TrnIndexCodeCommon?>").each(function(idx, obj) {
								$.li18n.onTranslationMissing = function() {
									//return "ln";//번역문을 못찾았을때
								};

								$(obj).html( $.li18n.translate( $(obj).html() ) );
							});


						},
						error: function () {

						}
					});
					//============================= 공통부분 =====================================



				},
				error: function () {

				}
			});
			//============================= 페이지부분 =====================================


		
		}


	<?
	//번역 : 수집이 완료된 후에 실행되어야 함.======================================================
	}
	?>

});
</script>