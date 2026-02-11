<?
include_once('./includes/common_header.php');
include_once('./includes/dbopen.php');
include_once('./includes/common.php');
?>
<!DOCTYPE html>
<html>
<head>
<?
$AppRegUID = isset($_REQUEST["AppRegUID"]) ? $_REQUEST["AppRegUID"]: "";
$Type = isset($_REQUEST["Type"]) ? $_REQUEST["Type"]: "";
?>
</head>
<body>


<script type='text/javascript'>
var Type = "<?=$Type?>";
if(Type=="1") { // 카카오 로그인
	Kakao.init('bec8f62e0063487d792fd833b37984d3');
	LoginWithKakao();
} else if(Type=="11") { // 카카오 링크
	Kakao.init('bec8f62e0063487d792fd833b37984d3');
	LinkWithKakao();
} else if(Type=="2") { // 네이버 로그인
	// naver
} else if(Type=="3") { // 구글 로그인
	//google
	gapi.load('auth2', function() {
		var GoogleAuth = gapi.auth2.init( {
			client_id: '950462494416-92ppoda203fvs2ghu0qjr2q592epuqsk.apps.googleusercontent.com',
			cookie_policy: "none",
			scope: "profile email openid",
			fetch_basic_profile: true
		});
		GoogleAuth.then(function(suc) {
			var Gauth = gapi.auth2.getAuthInstance();
			Gauth.signIn({ // 로그인, select 프롬프트가 보이게끔
				prompt:"select_account"
			}).then(
				function onInit() { // 로그인 성공 시,
					LoginWithGoogle(Gauth);
				},
				function onError() {
					alert("구글 로그인에 실패하였습니다.");
				}
			);
		}, function() {
			alert("구글 API 호출이 실패하였습니다.");
		});
	});
} else if(Type==4) {
	//facebook
} else if(Type=="") {
	// 인자가 없다면 그냥 종료
	top.window.close();
}

function LinkWithKakao() {
	Kakao.Auth.getStatusInfo(function callback(statusObj) {
		var status = statusObj['status'];

		if(status=="connected") { // 로그인 상태라면..
			Kakao.Link.sendDefault({
				objectType: 'feed',
				content: {
					title: '즐거운 화상영어 Mangoi',
					description: '#영어학습 #이벤트중',
					imageUrl: 'images/logo_mangoi.png',
					link: {
						//mobileWebUrl: 'http://mangoinew.hihome.kr/',
						webUrl: "<?=$DefaultDomain?>"+'/page.php?PageCode=company'
					}
				},
				social: {
					likeCount: 286,
					commentCount: 45,
					sharedCount: 845
				},
				buttons: [
					{
						title: '웹으로 보기',
						link: {
							//mobileWebUrl: 'https://developers.kakao.com',
							webUrl: "<?=$DefaultDomain?>"+'/page.php?PageCode=company'
						}
					},
					{
						title: '앱으로 보기',
						link: {
							//mobileWebUrl: 'https://developers.kakao.com',
							webUrl: "<?=$DefaultDomain?>"+'/page.php?PageCode=company'
						}
					}
				],
				installTalk: true,
				fail: function() {
					alert("카카오 링크를 지원하지 않는 플랫폼(iOS/Android 외의 플랫폼)에서 함수를 호출했습니다.");
				}
			});
		} else { // 비로그인 상태라면...
			LoginWithKakao("Link");
		}
	});
}

function LoginWithGoogle(Gauth) {
	var IsSigned = Gauth.isSignedIn.get();
	if(IsSigned) {
		var Profile = Gauth.currentUser.get().getBasicProfile();

		var Id = Profile.getId();
		var Name = Profile.getName();
		var GivenName = Profile.getGivenName();
		var FamilyName = Profile.getFamilyName();
		var ImageUrl = Profile.getImageUrl();
		var Email = Profile.getEmail();

		CheckAccount(Id, Email, Name, 3, "<?=$AppRegUID?>");
		//top.window.close(); // 추가 시 에러 발생 ( 정보 받아오기 전에 끊어져서 그런듯 ? )
	} else {
		alert("로그인에 실패하였습니다.");
	}
}

function LoginWithKakao(Action) {
	// 로그인 창을 띄웁니다.
	Kakao.Auth.loginForm({
		success: function(authObj) {
			//alert("authObj : "+authObj);
			Kakao.Auth.getStatusInfo(function callback(statusObj) {
				var KakaoEmail = statusObj["user"]["kakao_account"]["email"];
				var KakaoId = statusObj["user"]["id"];
				var KakaoName = statusObj["user"]["properties"]["nickname"];

				if(Action=="" || Action==undefined) {
					CheckAccount(KakaoId, KakaoEmail, KakaoName, 1, "<?=$AppRegUID?>");
					
					//window.open("ahsolapp://kr.ahsol.mangoi");
					//setTimeout(WinClose,1000);
				} else if(Action=="Link") {
					LinkWithKakao();
				}
			});
		},
		fail: function(err) {
			alert("로그인에 실패하였습니다.");
			//alert(JSON.stringify(err));
		}
	});
};

function CheckAccount(Id, Email, Name, Type, AppRegUID) {
	url = "check_sns_account_app.php";

    $.ajax(url, {
        data: {
			Id: Id,
			Email: Email,
			Name: Name,
			Type: Type,
			AppRegUID: AppRegUID
        },
        success: function (data) {
			doSend(data);
			if(Type==1) {
				// 카카오 로그인 아웃
				Kakao.Auth.logout();
			} else if(Type==11) {
				// 카카오 링크 아웃
				Kakao.Auth.logout();
			} else if(Type==3) {
				// 구글 로그인 아웃
				var Gauth = gapi.auth2.getAuthInstance();
				Gauth.SignOut().then(function onInit() {
					Gauth.disconnect();
				},
				function onError() {
					alert("구글 계정 로그아웃에 실패하였습니다.");
				});
				// 아래 예제는 사이트에서 로그아웃 후 백하는 예제 ( 유저 캐쉬를 지우기 위함 )
				//document.location.href = "https://www.google.com/accounts/Logout?continue=https://appengine.google.com/_ah/logout?continue=http://mangoinew.hihome.kr/login_form_app_sns.php";
			}
			top.window.close();
        },
        error: function (err, status, thrown) {
            alert('Error while contacting server, please try again');
			//alert(JSON.stringify(err));
			//alert(JSON.stringify(status));
			//alert(JSON.stringify(thrown));
        }

    });
	//window.open("ahsolapp://kr.ahsol.mangoi");
	//setTimeout(WinClose,1000); 
};	               


</script>

<script>
    websocket = new WebSocket("ws://211.117.60.181:8090/");
    websocket.onopen = function(Evt) { onOpen(Evt) };
    websocket.onclose = function(Evt) { onClose(Evt) };
    websocket.onmessage = function(Evt) { onMessage(Evt) };
    websocket.onerror = function(Evt) { onError(Evt) };


    function doSend(Message) {
        var Strjson = JSON.stringify(Message);
        // 소켓에서 받을 때 "Object: Object 로 받게 되어 String 으로 변환 발송
        websocket.send(Strjson);
    }

    function onOpen(Evt) {
    }

    function onClose(Evt)
    {
        websocket.close();
    }

    function onMessage(Evt)
    {
    }

    function onError(Evt)
    {
        websocket.close();

    }

    function doDisconnect() {
        websocket.close();
    }
</script>

</body>
</html>
