function CheckAppVersion(){

	var JsonUrl = AppDomain + AppPath + "/jsonp_get_app_version.php?Seq=1&callback=?";

	$.getJSON( JsonUrl, {
		format: "json"
	})
	.done(function( data ) {

		ServerAppVersionID = data.AppVersionID;
		LocalAppVersionID = AppVersionID;

		ServerAppVersionID = ServerAppVersionID.replace("-", "");
		LocalAppVersionID = LocalAppVersionID.replace("-", "");

		if (ServerAppVersionID != LocalAppVersionID){

			myApp.confirm('새로운 버전이 출시되었습니다.<br>플레이 스토어로 이동합니다.', function () {
				location.href = "market://details?id="+AppProjectID;
			});

			/*
			$.confirm({
				title: '안내',
				content: '새로운 버전이 출시되었습니다.<br>플레이 스토어로 이동합니다.',
				//content: '새로운 버전이 출시되었습니다.<br>개발자에게 설치파일을 요청해 주세요.',
				buttons: {
					확인: function () {
						location.href = "market://details?id="+AppProjectID;
						//OpenDownloadApk();
					}
				}
			});
			*/

		}


	}).fail(function() {

	});	

}

function OpenDownloadApk(){
	//LocationHref("download_apk.html", 1)
}


//MainActivity 로 부터 호출되는 함수, Device 토큰을 받아온다. index.html 에 위치시킴
function GetDeviceToken(ReceiveDeviceToken){
	cookieMaster.setCookieValue(AppDomain, 'DeviceToken', ReceiveDeviceToken, function() {}, function(error) {});
	localStorage.setItem(AppLocalStorageID+"DeviceToken", ReceiveDeviceToken);
	DeviceToken = ReceiveDeviceToken;//Defalut 변수저장

	SetDeviceToken();
}


function SetDeviceToken(){//서버저장
	LocalMemberID = localStorage.getItem(AppLocalStorageID+"MemberID");
	LocalDeviceToken = localStorage.getItem(AppLocalStorageID+"DeviceToken");

	var JsonUrl = AppDomain + AppPath + "/jsonp_set_device_token_update.php?AppRegUID="+AppRegUID+"&AppID="+AppID+"&AppDomain="+AppDomain+"&AppPath="+AppPath+"&MemberID="+LocalMemberID+"&DeviceToken="+LocalDeviceToken+"&DeviceType=Android&callback=?";

	$.getJSON( JsonUrl, {
		format: "json"
	})
	.done(function( data ) {
		//Success
	}).fail(function() {
		//Failure
	});

}
//MainActivity 로 부터 호출되는 함수, Device 토큰을 받아온다. index.html 에 위치시킴


function LoadInitPage(){

	LocalMemberID = localStorage.getItem(AppLocalStorageID+"MemberID");

	if (LocalMemberID=="" || LocalMemberID==null){//비로그인 상태

	}else{//로그인 상태

	}
}


function onBackKeyDown() {
	/*
	myApp.confirm('앱을 종료하시겠습니까?', function () {
		navigator.app.exitApp();
	});

	*/
	$.confirm({
		title: AppAlertTitle,
		content: '앱을 종료하시겠습니까?',
		buttons: {
			확인: function () {
				navigator.app.exitApp();
			},
			취소: function () {
				//취소
			}
		}
	});	

}

function NewWindowClosed(){//인앱 브라우져를 닫았을때

}


window.onload = function(){

	//인클루드 html 로딩
	$("div[data-includeHTML]").each(function () {                
		$(this).load($(this).attr("data-includeHTML"));
	});
	//인클루드 html 로딩
	

	// ======== 버전체크
	CheckAppVersion();
	// ======== 버전체크
	
	document.addEventListener("backbutton", onBackKeyDown, false);
	
	localStorage.setItem(AppLocalStorageID+"CampionAction", "");
	LocalMemberID = localStorage.getItem(AppLocalStorageID+"MemberID");

	if (LocalMemberID==null){
		LocalMemberID = "";
	}

	OpenRemoteUrl('intro_video.php');//동영상 인트로 오픈
	setTimeout(LoadInitPage, 1000);

}