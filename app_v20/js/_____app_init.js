var AppVersionID = "20190701-01";//숫자와 하이픈만 허용, 하이픈 제거시 기존 버전보다 높은 숫자가 되도록. AppVersion 이 다른 플러그인에서 사용하는듯. AppVersion 으로 하면 오류생김.
var AppID = "mangoi";
var AppLocalStorageID = "mangoi_";
var AppAlertTitle = "망고아이";
var AppDomain = "http://mangoidev.hihome.kr";
var AppPath = "/app_v1";
var AppProjectID = "kr.ahsol.mangoi";
var AppRegUID = localStorage.getItem(AppLocalStorageID+"AppRegUID");
var DeviceToken = localStorage.getItem(AppLocalStorageID+"DeviceToken");



//날짜 포맷
Date.prototype.format = function (f) {
	if (!this.valueOf()) return " ";
	var weekKorName = ["일요일", "월요일", "화요일", "수요일", "목요일", "금요일", "토요일"];
	var weekKorShortName = ["일", "월", "화", "수", "목", "금", "토"];
	var weekEngName = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
	var weekEngShortName = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
	var d = this;

	return f.replace(/(yyyy|yy|MM|dd|KS|KL|ES|EL|HH|hh|mm|ss|a\/p)/gi, function ($1) {
		switch ($1) {
			case "yyyy": return d.getFullYear(); // 년 (4자리)
			case "yy": return (d.getFullYear() % 1000).zf(2); // 년 (2자리)
			case "MM": return (d.getMonth() + 1).zf(2); // 월 (2자리)
			case "dd": return d.getDate().zf(2); // 일 (2자리)
			case "KS": return weekKorShortName[d.getDay()]; // 요일 (짧은 한글)
			case "KL": return weekKorName[d.getDay()]; // 요일 (긴 한글)
			case "ES": return weekEngShortName[d.getDay()]; // 요일 (짧은 영어)
			case "EL": return weekEngName[d.getDay()]; // 요일 (긴 영어)
			case "HH": return d.getHours().zf(2); // 시간 (24시간 기준, 2자리)
			case "hh": return ((h = d.getHours() % 12) ? h : 12).zf(2); // 시간 (12시간 기준, 2자리)
			case "mm": return d.getMinutes().zf(2); // 분 (2자리)
			case "ss": return d.getSeconds().zf(2); // 초 (2자리)
			case "a/p": return d.getHours() < 12 ? "am" : "pm"; // 오전/오후 구분
			default: return $1;
		}
	});
};

String.prototype.string = function (len) { var s = '', i = 0; while (i++ < len) { s += this; } return s; };
String.prototype.zf = function (len) { return "0".string(len - this.length) + this; };
Number.prototype.zf = function (len) { return this.toString().zf(len); };
//날짜 포맷


//CSS 동적 로딩
function LoadCss(filename) {
	var cssNode = document.createElement("link");
	cssNode.setAttribute("rel", "stylesheet");
	cssNode.setAttribute("type", "text/css");
	cssNode.setAttribute("href", filename);
	document.getElementsByTagName("head")[0].appendChild(cssNode);
}
//LoadCss(AppDomain+AppPath+"/css/common.css");
//LoadCss(AppDomain+AppPath+"/assets/css/common.css");
//CSS 동적 로딩

//폰트 사이즈 고정
if(window.MobileAccessibility){
	window.MobileAccessibility.usePreferredTextZoom(false);
}
//폰트 사이즈 고정



var app = {
	initialize: function() {
		document.addEventListener('deviceready', this.onDeviceReady.bind(this), false);
	},
	onDeviceReady: function() {
		this.receivedEvent('deviceready');
	},
	receivedEvent: function(id) {

		//StatusBar.styleBlackTranslucent();
		
		// ======== 권한
		var Permissions = cordova.plugins.permissions;
		
		Permissions.requestPermission(Permissions.INTERNET, PermissionSuccess1, PermissionError1);
		function PermissionError1() { /**$.alert({title: '안내', content: '저장공간에 대한 권한을 설정해 주시기 바랍니다.'});**/}
		function PermissionSuccess1( status ) { if( !status.hasPermission ) error(); }
		
		Permissions.requestPermission(Permissions.CAMERA, PermissionSuccess2, PermissionError2);
		function PermissionError2() { /**$.alert({title: '안내', content: '저장공간에 대한 권한을 설정해 주시기 바랍니다.'});**/}
		function PermissionSuccess2( status ) { if( !status.hasPermission ) error(); }

		Permissions.requestPermission(Permissions.READ_EXTERNAL_STORAGE, PermissionSuccess3, PermissionError3);
		function PermissionError3() { /**$.alert({title: '안내', content: '저장공간에 대한 권한을 설정해 주시기 바랍니다.'});**/}
		function PermissionSuccess3( status ) { if( !status.hasPermission ) error(); }

		Permissions.requestPermission(Permissions.WRITE_EXTERNAL_STORAGE, PermissionSuccess4, PermissionError4);
		function PermissionError4() { /**$.alert({title: '안내', content: '저장공간에 대한 권한을 설정해 주시기 바랍니다.'});**/}
		function PermissionSuccess4( status ) { if( !status.hasPermission ) error(); }

		Permissions.requestPermission(Permissions.RECORD_AUDIO, PermissionSuccess5, PermissionError5);
		function PermissionError5() { /**$.alert({title: '안내', content: '저장공간에 대한 권한을 설정해 주시기 바랍니다.'});**/}
		function PermissionSuccess5( status ) { if( !status.hasPermission ) error(); }

		Permissions.requestPermission(Permissions.RECORD_VIDEO, PermissionSuccess6, PermissionError6);
		function PermissionError6() { /**$.alert({title: '안내', content: '저장공간에 대한 권한을 설정해 주시기 바랍니다.'});**/}
		function PermissionSuccess6( status ) { if( !status.hasPermission ) error(); }
		// ======== 권한		
		
		// ======== window.open 정의
		window.open = cordova.InAppBrowser.open;
		// ======== window.open 정의


		// ======== 앱 고유번호 만들어 저장하기 - 새로 설치하면 바뀜.
		if (AppRegUID=="" || AppRegUID==null){
			TempTimeStamp = + new Date();
			AppRegUID = device.uuid + "-" + TempTimeStamp;
			localStorage.setItem(AppLocalStorageID+"AppRegUID", AppRegUID);
		}
		cookieMaster.setCookieValue(AppDomain, 'AppRegUID', AppRegUID, function() {}, function(error) {});
		// ======== 앱 고유번호 만들어 저장하기 - 새로 설치하면 바뀜.	

		
		// ======== 최근 접속일 저장 
		var _today = new Date(); 
		OldLastVisitDate = localStorage.getItem(AppLocalStorageID+"OldLastVisitDate");
		LastVisitDate = localStorage.getItem(AppLocalStorageID+"LastVisitDate");
		ScriptNowDate = _today.format('yyyy-MM-dd HH:mm:ss');
		if (LastVisitDate==""){
			localStorage.setItem(AppLocalStorageID+"OldLastVisitDate", ScriptNowDate);
		}else{
			localStorage.setItem(AppLocalStorageID+"OldLastVisitDate", LastVisitDate);
		}
		localStorage.setItem(AppLocalStorageID+"LastVisitDate", ScriptNowDate);
		// ======== 최근 접속일 저장 


		

	}
};

app.initialize();