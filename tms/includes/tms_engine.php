<?
$TrnHttpHost = $_SERVER['HTTP_HOST'];
$TrnRequestUrl = $_SERVER['REQUEST_URI'];
$TrnCollectUrlDviceType = 1;//1:웹 2:앱

if ($TrnRequestUrl=="/"){
	$TrnRequestUrl = "/index.php";
}


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
$TrnCollectByFullUrl = $Row["TrnCollectByFullUrl"];
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

$TrnCollectByFullUrl = str_replace("\r", "", $TrnCollectByFullUrl);
$TrnCollectByFullUrl = str_replace("\t", "", $TrnCollectByFullUrl);
$TrnCollectByFullUrl = str_replace("\n", ",", $TrnCollectByFullUrl);
$TrnCollectByFullUrl = ",".$TrnCollectByFullUrl.",";

if ( strpos($TrnRequestUrl, "?") !== false ){
	$ArrTrnRequestUrl = explode("?",$TrnRequestUrl);
	$TrnRequestUrlFileName = $ArrTrnRequestUrl[0];
}else{
	$TrnRequestUrlFileName = $TrnRequestUrl;
}

if ( strpos($TrnCollectByFullUrl, ",".$TrnRequestUrlFileName.",") !== false ){
	//파라미터 포함된 주소로 수집
}else{
	$TrnRequestUrl = $TrnRequestUrlFileName;//파라미터 제외한 주소로 수집
}

//망고아이 app 의 member_form.php
$TrnRequestUrl = preg_replace("/\/app_v[0-9]*\//", "/app_v**/", $TrnRequestUrl);
//망고아이 app 의 member_form.php



if (substr($TrnCollectDomain,-1)=="/"){
	$TrnCollectDomain = substr($TrnCollectDomain , 0, -1);
}

$TrnCollectDomain = str_replace("http://", "", $TrnCollectDomain);
$TrnCollectDomain = str_replace("https://", "", $TrnCollectDomain);

//===================== 망고아이 =========================
$BtnTrnDisplay = "none";//버튼을 숨긴다.
if ($_MEMBER_LOGIN_ID_=="tmsmaster" && $TrnTranslationMode==2){//마스터 이면서 미리보기 모드 이면
	$TrnTranslationMode=1;
}
if ($TrnRunType==2 && $TrnTranslationMode==1){//버튼방식, 번역실행
	$BtnTrnDisplay = "";//버튼을 보여준다.
}


if ($BtnTrnDisplay==""){
?>
<script>
if(document.getElementById("BtnTrnBox") !== null){
	document.getElementById('BtnTrnBox').style.display ="";
}
</script>
<?
}


if ($_MEMBER_LOGIN_ID_=="tmsmaster" && $TrnCollectTextMode==2){//마스터 이면서 수동수집 모드 이면
?>
<style>
.btn_print_fix{display:inline-block; width:200px; text-align:right; position:fixed; top:20px; right:20px; left:20px;z-index:10000000;}
.btn_print_form{display:inline-block; width:200px; height:42px; line-height:42px; text-align:center; border-radius:5px; background-color:rgba(0,140,214,0.9); color:#fff; font-size:17px; font-weight:500; cursor:pointer;}
</style>
<div class="btn_print_fix">
	<div class="btn_print_form" onclick="TrnCollect();">번역 TEXT 수집하기</div>
</div>
<?
}
//===================== 망고아이 =========================
?>
<div style="display:none;">
<form id="TrnRegForm" name="TrnRegForm" method="post" enctype="multipart/form-data" accept-charset="UTF-8" autocomplete="off">
<input type="text" name="TrnCollectUrlDviceType" value="">
<input type="text" name="TrnCollectUrl" value="">
<input type="text" name="TrnCollectTextExplodeIndex" value="">
<input type="text" name="TrnCollectTextType" value="">
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


function SetCkTrnLang( value ){ 
	var todayDate = new Date(); 
	todayDate.setDate( todayDate.getDate() + 100 ); 
	document.cookie = "TrnBrowserLocCode=" + escape( value ) + "; path=/; expires=" + todayDate.toGMTString() + ";" 
	location.reload();
} 


function GetCookieTrnLanguageID(){
    var cookieValue=null;
    if(document.cookie){
        var array=document.cookie.split((escape("TrnBrowserLocCode")+'='));
        if(array.length >= 2){
            var arraySub=array[1].split(';');
            cookieValue=unescape(arraySub[0]);
        }
    }
    return cookieValue;
}


var TrnHttpHost = "<?=$TrnHttpHost?>";
var TrnRequestUrl = "<?=$TrnRequestUrl?>";
var TrnCollectUrlDviceType = <?=$TrnCollectUrlDviceType?>;
var TrnCollectDomain = "<?=$TrnCollectDomain?>";
var TrnCollectTextMode = <?=$TrnCollectTextMode?>;
var TrnCollectTextExplodeIndex = "<?=$TrnCollectTextExplodeIndex?>";
var TrnSiteLocCode = "<?=$TrnSiteLocCode?>";
var TrnIndexCode = "<?=$TrnIndexCode?>";
var TrnIndexCodeCommon = "<?=$TrnIndexCodeCommon?>";
var TrnIndexCodeCommonUrl = "<?=$TrnIndexCodeCommonUrl?>";
var TrnTranslationMode = <?=$TrnTranslationMode?>;
var TrnRunType = <?=$TrnRunType?>;
var TrnBrowserLocCode = "";

var TrnRun = true;
if (TrnTranslationMode==1){

	if (TrnRunType==1){
		TrnBrowserLocCode = GetBrowserLang();
		//TrnBrowserLocCode = "zh-US";//테스트 임시
	}else{
		TrnBrowserLocCode = GetCookieTrnLanguageID();//쿠키에서 가져온다.

		
		if (TrnBrowserLocCode==null || TrnBrowserLocCode==""){
			TrnBrowserLocCode = "0";
		}
	}

	
	if (TrnRunType==1){

		IncTrnSiteLocCode = TrnSiteLocCode.replace(/\%/gi, "");
		IncTrnSiteLocCodeLen = IncTrnSiteLocCode.length;
		
		if (TrnBrowserLocCode!=""){
			if (TrnSiteLocCode.left(1)=="%" && TrnSiteLocCode.right(1)=="%"){
				if (TrnBrowserLocCode.indexOf(IncTrnSiteLocCode) != -1) {
					TrnRun = false;
				}
			}else if (TrnSiteLocCode.left(1)=="%"){
				if (TrnBrowserLocCode.right(IncTrnSiteLocCodeLen) == IncTrnSiteLocCode) {
					TrnRun = false;
				}
			}else if (TrnSiteLocCode.right(1)=="%"){
				if (TrnBrowserLocCode.left(IncTrnSiteLocCodeLen) == IncTrnSiteLocCode) {
					TrnRun = false;
				}
			}else{
				if (TrnSiteLocCode == IncTrnSiteLocCode) {
					TrnRun = false;
				}
			}
		}

	
	}else{

		if (TrnBrowserLocCode=="0"){
			TrnRun = false;
		}

	}

	

}


// alert 관련 다국어 =======================================================================================
window.old_alert = window.alert;
window.alert = function(AlertMsg){
	if (obj_lang_alert!=null && obj_lang_alert!=""){
		if (TrnTranslationMode==1 && TrnRun){

			TrnAlertMsg = obj_lang_alert[AlertMsg];
			
			if (TrnAlertMsg!=null && TrnAlertMsg!=""){
				return old_alert(TrnAlertMsg);
			}else{
				return old_alert(AlertMsg);
			}

		}else{
			return old_alert(AlertMsg);
		}
	}else{
		return old_alert(AlertMsg);
	}
};

window.old_confirm = window.confirm;
window.confirm = function(AlertMsg){
	if (obj_lang_alert!=null && obj_lang_alert!=""){
	
		if (TrnTranslationMode==1 && TrnRun){
			
			TrnAlertMsg = obj_lang_alert[AlertMsg];

			if (TrnAlertMsg!=null && TrnAlertMsg!=""){
				return old_confirm(TrnAlertMsg);
			}else{
				return old_confirm(AlertMsg);
			}
			
		}else{
			return old_confirm(AlertMsg);
		}

	}else{
		return old_confirm(AlertMsg);
	}
};
// alert 관련 다국어 =======================================================================================


var obj_lang_alert = null;
if (TrnTranslationMode==1 && TrnRun){
	TrnStart();


	// alert 관련 다국어 =======================================================================================
	url = "/tms/includes/tms_ajax_get_translation_alert.php";

	//window.open(url + "?TrnBrowserLocCode="+TrnBrowserLocCode+"&TrnCollectUrlDviceType="+TrnCollectUrlDviceType);
	$.ajax({
		url:url,
		data: {
			TrnCollectUrlDviceType: TrnCollectUrlDviceType,
			TrnBrowserLocCode: TrnBrowserLocCode
		},
		async:false,
		success: function (data) {
			obj_lang_alert = data.obj_lang_alert;
		},
		error: function () {
			obj_lang_alert = null;
		}
	});
	// alert 관련 다국어 =======================================================================================
}


$(document).ready(function() {

	document.TrnRegForm.TrnCollectUrlDviceType.value = TrnCollectUrlDviceType;
	document.TrnRegForm.TrnCollectUrl.value = TrnRequestUrl;
	document.TrnRegForm.TrnCollectTextExplodeIndex.value = TrnCollectTextExplodeIndex;

	//================ 수집 : 번역이 실행되기 전에 이부분이 실행되어야 함.======================================================
	//if (TrnCollectTextMode==1 && TrnHttpHost.indexOf(TrnCollectDomain)!=-1 ){
	if (TrnCollectTextMode==1){//망고아이는 다른 도메인도 사용한다.
		TrnCollect();
	}else{
		TrnTranslation();
	}
	//================ 수집 : 번역이 실행되기 전에 이부분이 실행되어야 함.======================================================
	

});


function TrnCollect(){

	document.TrnRegForm.TrnCollectTextType.value = "1";

	url = "/tms/includes/tms_ajax_set_collect_text.php";
	TrnCollectTexts = TrnCollectTextExplodeIndex;

	$("."+TrnIndexCode).each(function(idx, obj) {
		TrnCollectText = $(obj).html();
		TrnCollectTexts = TrnCollectTexts + TrnCollectText + TrnCollectTextExplodeIndex;
	});

	
	document.TrnRegForm.TrnCollectTexts.value = TrnCollectTexts;
	//document.TrnRegForm.action = url;
	//document.TrnRegForm.submit();

	if (TrnCollectTexts!=TrnCollectTextExplodeIndex){

		var params =  $("#TrnRegForm").serialize();
		jQuery.ajax({
			url: url,
			type: 'POST',
			data:params,
			contentType: 'application/x-www-form-urlencoded; charset=UTF-8', 
			dataType: 'html',
			success: function (data) {

				if (TrnCollectTextMode==2){
					alert("번역대상 TEXT 수집을 완료했습니다.");
				}

				TrnCollectCom();


			},
			error: function () {
				TrnCollectCom();
			}
		});

	}else{

		TrnCollectCom();

	}

}


function TrnCollectCom(){

	if (TrnIndexCodeCommonUrl==TrnRequestUrl){
	
		document.TrnRegForm.TrnCollectTextType.value = "2";

		url = "/tms/includes/tms_ajax_set_collect_text.php";
		
		TrnCollectTexts = TrnCollectTextExplodeIndex;

		$("."+TrnIndexCodeCommon).each(function(idx, obj) {
			TrnCollectText = $(obj).html();
			TrnCollectTexts = TrnCollectTexts + TrnCollectText + TrnCollectTextExplodeIndex;
		});

		
		document.TrnRegForm.TrnCollectTexts.value = TrnCollectTexts;
		//document.TrnRegForm.action = url;
		//document.TrnRegForm.submit();

		if (TrnCollectTexts!=TrnCollectTextExplodeIndex){

			var params =  $("#TrnRegForm").serialize();
			jQuery.ajax({
				url: url,
				type: 'POST',
				data:params,
				contentType: 'application/x-www-form-urlencoded; charset=UTF-8', 
				dataType: 'html',
				success: function (data) {
					TrnTranslation();
				},
				error: function () {
					TrnTranslation();
				}
			});

		}else{
			TrnTranslation();
		}

	
	}else{
		TrnTranslation();
	}

}


function TrnTranslation(){

	//================ 번역 : 수집이 완료된 후에 실행되어야 함.======================================================
	if (TrnTranslationMode==1 && TrnRun){

		if ($("."+TrnIndexCode).length>0){


			//============================= 페이지부분 =====================================
			TrnCollectTextType="1";
			url = "/tms/includes/tms_ajax_get_translation_text.php";

			//window.open(url + "?TrnCollectTextType="+TrnCollectTextType+"&TrnBrowserLocCode="+TrnBrowserLocCode+"&TrnCollectUrlDviceType="+TrnCollectUrlDviceType+"&TrnCollectUrl="+TrnRequestUrl);
			$.ajax(url, {
				data: {
					TrnCollectTextType: TrnCollectTextType,
					TrnCollectUrlDviceType: TrnCollectUrlDviceType,
					TrnBrowserLocCode: TrnBrowserLocCode,
					TrnCollectUrl: TrnRequestUrl
				},
				success: function (data) {

					obj_lang = data.obj_lang;

					if (obj_lang!=null && obj_lang!=""){


						$.li18n.translations = {ln:obj_lang};
						$.li18n.currentLocale = "ln";
						
						$("."+TrnIndexCode).each(function(idx, obj) {
							$.li18n.onTranslationMissing = function() {
								//return "ln";//번역문을 못찾았을때
							};

							$(obj).html( $.li18n.translate( $(obj).html() ) );

							if ($("."+TrnIndexCode).length === idx+1) {
								TrnTranslationCommon();
							}
						});

					}else{
						TrnTranslationCommon();
					}

				},
				error: function () {
					TrnTranslationCommon();
				}
			});
			//============================= 페이지부분 =====================================

		}else{
			TrnTranslationCommon();
		}
	
	}else{
		TrnEnd();
	}
	//================ 번역 : 수집이 완료된 후에 실행되어야 함.======================================================
	
}


function TrnTranslationCommon(){

	if ($("."+TrnIndexCodeCommon).length>0){

		//============================= 공통부분 =====================================
		TrnCollectTextType="2";
		url = "/tms/includes/tms_ajax_get_translation_text.php";

		//window.open(url + "?TrnCollectTextType="+TrnCollectTextType+"&TrnBrowserLocCode="+TrnBrowserLocCode+"&TrnCollectUrlDviceType="+TrnCollectUrlDviceType+"&TrnCollectUrl="+TrnRequestUrl);
		$.ajax(url, {
			data: {
				TrnCollectTextType: TrnCollectTextType,
				TrnCollectUrlDviceType: TrnCollectUrlDviceType,
				TrnBrowserLocCode: TrnBrowserLocCode,
				TrnCollectUrl: TrnRequestUrl
			},
			success: function (data) {

				obj_lang = data.obj_lang;

				if (obj_lang!=null && obj_lang!=""){

					$.li18n.translations = {ln:obj_lang};
					$.li18n.currentLocale = "ln";
					
					$("."+TrnIndexCodeCommon).each(function(idx, obj) {
						$.li18n.onTranslationMissing = function() {
							//return "ln";//번역문을 못찾았을때
						};
						$(obj).html( $.li18n.translate( $(obj).html() ) );


						if ($("."+TrnIndexCodeCommon).length === idx+1) {
							TrnEnd();
						}
					});

				}else{
					TrnEnd();
				}


			},
			error: function () {
				TrnEnd();
			}
		});
		//============================= 공통부분 =====================================
	}else{
		TrnEnd();
	}
}

function TrnStart(){
	$("#DivTrnLoading").css("display", "");
	//$("body").css("display", "none");
}
function TrnEnd(){
	$("#DivTrnLoading").css("display", "none");
	//$("body").css("display", "");
}
</script>