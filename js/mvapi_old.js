/**
 * MV Web API
 * 
 * @version 7.3.0.3
 * @since 2020-06-25
 * @dependency MV_SPEC 7.3.0
 */
(function(window){
	//'use strict';
	
	// jQuery 필수 정보 확인
	if(typeof jQuery == 'undefined'){
		console.warn('[MvApi] requied jQuery. v1.7 이상');
		// extend 1.1.4
		// ajax 1.5
		// each 1.0
		return;
	}
	
	/**
	 * 버전
	 */
	var VERSION = '7.3.0.3';
	
	/**
	 * 기본 설정
	 */
	var defaultSettings = {
			debug: false,
			// 설치 페이지 (include | popup | none)
			installPage: 'include',
			// TCPS 정보 - v7.1.5
			tcps: {
				// TCPS 키
				// key: 'MTgwLjE1MC4yMzAuMTk1OjcwMDE',
				key: 'MTIxLjE3MC4xNjQuMjMxOjcwMDE',
				// 세컨 아이피 사용 여부, v7.1.3
				secondIp: false
			},
			// 서버 접속 정보 @Deprecated v7.1.5
			server: {
				// 아이피
				ip: '',
				// 포트
				port: 0,
				// 세컨 아이피 사용 여부, v7.1.3
				secondIp: false
			},
			// 접속 회사 접보
			company: {
				// 접속 코드
				code: 2,
				// 인증키
				authKey: '1605233653',
				// 사이트 아이디(커스터마이징된 사이트 정보) - v7.1.4
				siteId: '',
			},
			// 클라이언트 설정 정보
			client: {
				// 암호화 사용 여부
				encrypt: false,
				// Windows Client 설정
				windows: {
					// 프로그램 이름
					product: 'BODA'
				}, 
				// Mobile Client 설정
				mobile: {
					// 스토어 배포 방식. true: Store 배포, false: 사설 배포
					store: false, 
					// 스킴 이름
					scheme: 'mangoi',
					// 패키지 이름
					packagename: 'zone.mangoi',
				},
				// Mac Client 설정 - V7.3.0
				mac: {
					// 스킴 이름
					scheme: 'shezviewx',
					// 패키지 이름
					packagename: 'com.saeha.ezViewX.mac',
				},
				// 사용언어 - 없으면 한국어
				language: 'ko',
				// 테마 - 클라이언트의 테마 코드 값 - v7.1.3
				theme: 3,
				// 버튼 타입 - 버튼을 표시하는 방식 - v7.1.3
				btnType: 1,
				// 어플리케이션 모드 - 회의,교육 등 동작 모드 설정 - v7.1.4
				appMode: 2
			},
			// WebAgent 설정
			agent: {
				// 포트 정보
				port: {http: 5555, https: 5556}, 
				// HTTPS로만 접속
				onlyHttps: true
			},
			// MV_WEB 접속 주소
			web: {
				// url: 'http://180.150.230.195:8080'
				url: 'http://121.170.164.231:8080'
			}
	};
	
	// iframe 이벤트
	window.addEventListener('message', function(e) {
		if(e.data.multiview){
			if(defaultSettings.debug) console.debug('[MvApi] Event Message [', e.data.multiview, ']');
			
			// iframe 닫기
			if(e.data.multiview.event == 'close'){
				$('#multiview_install').remove();
				$('body').css('overflow', '');
			}
		}
	})
	
	var ClientDownload = {
			installGuide: function(){
				var url = defaultSettings.web.url + '/program/installGuide.do?groupcode='+defaultSettings.company.code+'&language='+defaultSettings.client.language;
				
				if(defaultSettings.installPage == 'popup'){
					var specs = {
							width: '950',
							height: '600'
					};
					
					var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
					var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;
					
					var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
					var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;
					
					specs.left = ((width / 2) - (specs.height / 2)) + dualScreenLeft;
					specs.top = ((height / 2) - (specs.width / 2)) + dualScreenTop;
					
					//specs.top = (window.screen.height - specs.height) / 2;
					//specs.left = (window.screen.width - specs.width)  / 2;
					
					var popupSpecs = [];
					if(specs.width) popupSpecs.push('width='+specs.width);
					if(specs.height) popupSpecs.push('height='+specs.height); 
					if(specs.top) popupSpecs.push('top='+specs.top);
					if(specs.left) popupSpecs.push('left='+specs.left);
					popupSpecs.push('toolbar=no');
					popupSpecs.push('menubar=no');
					popupSpecs.push('scrollbars=no');
					popupSpecs.push('resizable=no');
					popupSpecs.push('fullscreen=no');
					popupSpecs.push('fullscreen=no');
					popupSpecs.push('status=no');
					popupSpecs.push('titlebar=no');
					
					window.open(url, 'multiview_install', popupSpecs.join(','));
				}
				else if(defaultSettings.installPage == 'include'){
					var $if = $('<iframe id="multiview_install" class="multiview_install" style="z-index: 99999; border: 0; position: fixed; left: 0; top: 0; width: 100%; height: 100%" scrolling="yes"></iframe>');
					$if.attr('src', url);
					
					$('body').append($if).css('overflow', 'hidden');
				}
			},
			exec: function(requestMsg, successCallback, errorCallback){
				var platformType = requestMsg.platformType || '';
				
				if(platformType == '' || platformType == undefined){
					platformType = Device.platform;
				}
				else{
					platformType = platformType.toUpperCase();
				}
				
				if(platformType == 'ANDROID' || platformType == 'IOS' || platformType == 'WINDOWS' || platformType == 'MAC'){
					location.href = defaultSettings.web.url+'/program/download/'+defaultSettings.company.code+'/'+platformType+'.do?language='+defaultSettings.client.language;
					
					successCallback();
				}
				else{
					errorCallback(ERROR_CODE.NOT_SUPPORTED_DEVICE, platformType);
				}
			}
	}
	
	/**
	 * MV_WEB_API
	 */
	var ERROR_CODE = {
			/**
			 * 필수 입력 값이 없음
			 */
			NONE_REQUIRED_VALUE: 'MVAPI-NONE_REQUIRED_VALUE',
			/**
			 * 입력값이 유효하지 않습니다.
			 */
			INVALID_VALUE: 'MVAPI-INVALID_VALUE',
			/**
			 * 서버 설정 정보 없음
			 */
			NONE_TCPS_INFO: 'MVAPI-NONE_TCPS_INFO',
			NONE_SERVER_INFO: 'MVAPI-NONE_SERVER_INFO',
			/**
			 * 회사 설정 정보 없음
			 */
			NONE_COMPANY_INFO: 'MVAPI-NONE_COMPANY_INFO',
			/**
			 * 지원하지 않는 단말
			 */
			NOT_SUPPORTED_DEVICE: 'EZVIEWAPI-NOT_SUPPORTED_DEVICE',
			/**
			 * Agent 접속 실패
			 */
			NOT_CONNECTED_AGENT: 'MVAPI-NOT_CONNECTED_AGENT',
			/**
			 * 이미 프로그램이 실행 중 입니다.
			 */
			ALREADY_EXECUTE_PROGRAM: 'MVAPI-ALREADY_EXECUTE_PROGRAM',
			/**
			 * Window Client 호출 오류
			 */
			WIN_CALL_ERROR: 'MVAPI-WIN_CALL_ERROR',
			/**
			 * 모바일 앱 호출 오류(앱 내 호출)
			 */
			APP_CALL_ERROR: 'MVAPI-APP_CALL_ERROR',
			/**
			 * 프로그램이 설치되어 있지 않습니다.
			 */
			NOT_INSTALLED: 'MVAPI-NOT_INSTALLED',
			/**
			 * 정의되지 않은 오류
			 */
			UNDEFINED_ERROR : 'MVAPI-UNDEFINED_ERROR',
	};
	
	/** window.console 지원하지 않는 브라우져의 오류 차단을 위한 코드 **/
	if(typeof console == "undefined"){console = {log: function(){}, info: function(){}, debug: function(){}, error: function(){}};}
	if(typeof console.log == "undefined"){console.log = function(){};}
	if(typeof console.info == "undefined"){console.info = console.log;}
	if(typeof console.debug == "undefined"){console.debug = console.log;}
	if(typeof console.warn == "undefined"){console.warn = console.log;}
	if(typeof console.error == "undefined"){console.error = console.log;}
	
	/*******************************************************************************************************
	 * 단말 정보
	 *******************************************************************************************************/
	var Device = {
			// userAgent 문자열
			_userAgent: null,
			// 단말 구분
			platformType: {Windows: 'WINDOWS', Mac: 'MAC', Android: 'ANDROID', iOS: 'IOS', Unknown: 'UNKNOWN'},
			// 단말 종류
			platform: null,
			ext: {chromeVersion: 0, isNative: false, isNaverApp: false, isKakaoTalkApp: false},
			
			// userAgent 파싱
			parse: function(userAgent){
				this._userAgent = userAgent;
				
				// 단말 종류
				if(this._userAgent.match('Windows') != null){this.platform = this.platformType.Windows;}
				else if(this._userAgent.match('Android') != null) {this.platform = this.platformType.Android;}
				else if(this._userAgent.match('iPhone') != null || this._userAgent.match('iPad') != null) {this.platform = this.platformType.iOS;}
				else if(this._userAgent.match('Mac') != null || this._userAgent.match('Macintosh') != null) {this.platform = this.platformType.Mac;}
				else {this.platform = this.platformType.Unknown;}
				
				// 기타 정보
				try{this.ext.chromeVersion = parseFloat(this._userAgent.match(/Chrome\/[^\ ]*/)[0].toLowerCase().substr(7, 2));}catch(e){}
				try{this.ext.isNative = navigator.userAgent.match(/NATIVE/) != null;}catch(e){}
				try{this.ext.isNaverApp = navigator.userAgent.match(/NAVER/) != null;}catch(e){}
				try{this.ext.isKakaoTalkApp = navigator.userAgent.match(/KAKAOTALK/) != null;}catch(e){}
				try{this.ext.isWebapp = navigator.userAgent.match(/WEBAPP/) != null;}catch(e){}
			},
			// platform = windows
			isWindows: function(){
				return this.platform == this.platformType.Windows;
			},
			// platform = Macintosh
			isMac: function(){
				return this.platform == this.platformType.Mac;
			},
			// platform = android or ios
			isMobile: function(){
				return this.platform == this.platformType.Android || this.platformType.iOS;
			},
			// platform = android
			isAndroid: function(){
				return this.platform == this.platformType.Android;
			},
			// platform = ios
			isIos: function(){
				return this.platform == this.platformType.iOS;
			}
	};
	// userAgent Parsing
	Device.parse(window.navigator.userAgent);
	
	/*******************************************************************************************************
	 * 메시지 생성기
	 *******************************************************************************************************/
	var MessageMaker = {
			// 파리미터 정보
			parameter: {
				command:       'command',
				tcpsInfo:      'tcpsInfo',
				serverInfo:    'serverInfo',
				companyInfo:   'companyInfo',
				clientInfo:    'clientInfo',
				confInfo:      'confInfo', 
				confOption:    'confOption',
				confInfoEx:    'confInfoEx',
				joinInfo:      'joinInfo', 
				userInfo:      'userInfo',
				webInfo:       'webInfo',
				directWebInfo: 'directWebInfo',
				webMessage:    'webMessage',
			},
			// 메시지 생성
			make: function(keys, command, requestMsg){
				var THIS = this;
				
				var msg = [];
				$.each(keys, function(index, key){
					if(typeof THIS._parameterMaker[key] == 'function'){
						msg.push(THIS._parameterMaker[key].call(THIS, command, requestMsg));
					}
					else{
						console.warn('[MvApi] Unknown Parameter Key. ', key);
					}
				});
				
				var resultMsg = msg.join('&');
				if(defaultSettings.debug) console.debug('[MvApi] Message: ', resultMsg);
				
				return resultMsg;
			},
			// 파라미터 생성기
			_parameterMaker: {
				// 명령어=명령
				command: function(command){
					return this._makeKeyValue(this.parameter.command, command);
				},
				// TCPS 정보=키|세컨트 아이피 사용 여부
				tcpsInfo: function(command, requestMsg){
					return this._makeKeyValue(
							this.parameter.tcpsInfo, 
							[defaultSettings.tcps.key, defaultSettings.tcps.secondIp ? '1' : '0']
					);
				},
				// 서버 접속 정보=TCPS 아이피|TCPS 포트|세컨트 아이피 사용 여부
				serverInfo: function(command, requestMsg){
					return this._makeKeyValue(
							this.parameter.serverInfo, 
							[defaultSettings.server.ip, defaultSettings.server.port, defaultSettings.server.secondIp ? '1' : '0']
					);
				},
				// 회사 정보=그룹코드|인증코드|사이트아이디
				companyInfo: function(){
					return this._makeKeyValue(
							this.parameter.companyInfo, 
							[defaultSettings.company.code, defaultSettings.company.authKey, defaultSettings.company.siteId]
					);
				},
				// 클라이언트 정보=언어|테마|버튼타입|앱모드
				clientInfo: function(command, requestMsg){
					return this._makeKeyValue(
							this.parameter.clientInfo, 
							[defaultSettings.client.language, defaultSettings.client.theme, defaultSettings.client.btnType, defaultSettings.client.appMode]
					);
				},
				// 멀티룸 개설 정보=멀티룸코드|템플릿 번호|멀티룸제목|개설 된 멀티룸 입장 옵션
				confInfo: function(command, requestMsg){
					return this._makeKeyValue(
							this.parameter.confInfo, 
							[requestMsg.roomCode, requestMsg.template, requestMsg.title, requestMsg.openOption]
					);
				},
				// 멀티룸 옵션 정보=옵션코드=옵션값&...
				confOption: function(command, requestMsg){
					if(requestMsg.roomOption == undefined){
						return null;
					}
					
					if(typeof requestMsg.roomOption == 'object'){
						var ro = [];
						$.each(requestMsg.roomOption, function(key, value){
							ro.push(key + '=' + value);
						});
						
						return this._makeKeyValue(
								this.parameter.confOption, ro.join('&')
						);
					}
					else{
						return this._makeKeyValue(
								this.parameter.confOption, requestMsg.roomOption
						);
					}
				},
				// 멀티륨 확장 정보=설정값
				confInfoEx: function(command, requestMsg){
					if(requestMsg.extraMsg == undefined){
						return null;
					}
					return this._makeKeyValue(
							this.parameter.confInfoEx, requestMsg.extraMsg
					);
				},
				// 멀티룸 입장 정보=멀티룸코드|입장 사용자 유형
				joinInfo: function(command, requestMsg){
					return this._makeKeyValue(
							this.parameter.joinInfo, 
							[requestMsg.roomCode, requestMsg.joinUserType]
					);
				},
				// 사용자 정보=그룹코드|인증코드|DB 사용자 여부|사용자 아이디|사용자 비밀번호|사용자 이름
				userInfo: function(command, requestMsg){
					return this._makeKeyValue(
							this.parameter.userInfo, 
							[defaultSettings.company.code, defaultSettings.company.authKey, 0, requestMsg.userId, '', requestMsg.userName]
					);
				},
				// 웹 정보=웹 URL
				webInfo: function(command, requestMsg){
					return this._makeKeyValue(
							this.parameter.webInfo, defaultSettings.web.url
					);
				},
				// 웹 뷰 로드 정보=웹 URL
				directWebInfo: function(command, requestMsg){
					return this._makeKeyValue(
							this.parameter.directWebInfo, requestMsg.directWebInfo
					);
				},
				// 웹 메시지=웹 메시지
				webMessage: function(command, requestMsg){
					return this._makeKeyValue(
							this.parameter.webInfo, defaultSettings.web.url
					);
				},
			},
			// Key Value 생성
			_makeKeyValue: function(key, values){
				var THIS = this;
				var valueData;
				if(typeof values == 'object'){
					var vd = [];
					$.each(values, function(key, value){
						vd.push(THIS._escape(value));
					});
					valueData = vd.join('|');
				}else{
					valueData = values == undefined ? '' : this._escape(values);
				}
				return key + '=' + valueData;
			},
			// 구분자 Escape
			_escape: function(value){
				if(typeof value == 'string'){
					value = value.replace(/&/gi, String.fromCharCode(162))
					value = value.replace(/=/gi, String.fromCharCode(163))
					value = value.replace(/[|]/gi, String.fromCharCode(164))
				} 
				return value;
			}
	}
	
	/*******************************************************************************************************
	 * WebAgent 호출
	 *******************************************************************************************************/
	var WebAgent = {
			// AJAX 옵션
			_ajaxOptions: {
					type: 'post',
					dataType: 'json',
					async: true,
					cache: false,
					timeout: 10000,
			},
			// 클라이언트 실행 여부 확인
			running: function(successCallback, errorCallback){
				var THIS = this;
				// 옵션
				var ao = $.extend({}, this._ajaxOptions);
				// URL
				ao.url = this._makeHost();
				// Parameter
				ao.data = {
						COMMAND: 'running',
						PRODUCTNAME: defaultSettings.client.windows.product,
				}
				// 성공 시 처리
				ao.success = function(res){
					// WebAgent 처리 결과 확인
					if(res.RETURN == THIS._retuenCode.CLIENT_NOT_RUNNING){
						successCallback();
					}
					// WebAgent 파라미터 오류 or 클라이언트 오류
					else if(res.RETURN == THIS._retuenCode.CLIENT_RUNNING){
						errorCallback(ERROR_CODE.ALREADY_EXECUTE_PROGRAM, res.RETURN);
					}
					else{
						errorCallback(ERROR_CODE.UNDEFINED_ERROR, res.RETURN);
					}
					// FIXME 다른 오류 코드 처리는?
				}
				// 오류 시 처리
				ao.error = function(jqXHR, textStatus, error){
					// webAgent 접속 실패
					if(jqXHR.readyState == 0){
						errorCallback(ERROR_CODE.NOT_CONNECTED_AGENT);
						
						ClientDownload.installGuide();
					}
					// 알수 없는 오류
					else{
						ClientDownload.installGuide();
					}
				}
				// 실행
				$.ajax(ao);
			},
			// 실행
			exec: function(message, successCallback, errorCallback){
				var THIS = this;
				// 옵션
				var ao = $.extend({}, this._ajaxOptions);
				// URL
				ao.url = this._makeHost();
				// Parameter
				ao.data = {
						COMMAND: 'exec',
						PRODUCTNAME: defaultSettings.client.windows.product,
						EXECMSG: encodeURIComponent(message)
				}
				// 성공 시 처리
				ao.success = function(res){
					// WebAgent 처리 결과 확인
					if(res.RETURN == THIS._retuenCode.OK || res.RETURN == THIS._retuenCode.CLIENT_MISS_VERSION){
						successCallback();
					}
					// WebAgent 파라미터 오류 or 클라이언트 오류
					else if(res.RETURN == THIS._retuenCode.WA_UNKNOWN_PARAMETER || res.RETURN == THIS._retuenCode.CLIENT_FAIL){
						errorCallback(ERROR_CODE.WIN_CALL_ERROR, res.RETURN);
					}
					// Client 응답 없음 - 설치 필요
					else if(res.RETURN == THIS._retuenCode.CLIENT_NO_ANSWER){
						errorCallback(ERROR_CODE.NOT_INSTALLED, res.RETURN);
						
						ClientDownload.installGuide();
					}
					// 프로그램이 이미 생성되어 있는 경우
					else if(res.RETURN == THIS._retuenCode.CLIENT_FAIL_ALREADYEXIST){
						errorCallback(ERROR_CODE.ALREADY_EXECUTE_PROGRAM, res.RETURN);
					}
					else{
						errorCallback(ERROR_CODE.UNDEFINED_ERROR, res.RETURN);
					}
					// FIXME 다른 오류 코드 처리는?
				}
				// 오류 시 처리
				ao.error = function(jqXHR, textStatus, error){
					// webAgent 접속 실패
					if(jqXHR.readyState == 0){
						errorCallback(ERROR_CODE.NOT_CONNECTED_AGENT);
						
						ClientDownload.installGuide();
					}
					// 알수 없는 오류
					else{
						ClientDownload.installGuide();
					}
				}
				// 실행
				$.ajax(ao);
			},
			// 접속 HOST 생성
			_makeHost: function(){
				var protocol = defaultSettings.agent.onlyHttps ? 'https:' : (window.location.protocol == 'https:' ? 'https:' : 'http:');
				var port = defaultSettings.agent.onlyHttps ? defaultSettings.agent.port.https
						: (protocol == 'http:' ? defaultSettings.agent.port.http : defaultSettings.agent.port.https)
				return protocol + '//127.0.0.1:' + port + '/mvAgent';
			},
			// WebAgent 결과 코드
			_retuenCode: {
				OK: 'OK', 												// 성공
				WA_MISS_VERSION: 'WA_MISS_VERSION', 					// WebAgent 버전 불일치
				WA_UNKNOWN_PARAMETER: 'WA_UNKNOWN_PARAMETER',			// WebAgent 알 수 없는 파라메터 요청
				CLIENT_NO_ANSWER: 'CLIENT_NO_ANSWER', 					// Client 응답없음
				CLIENT_MISS_VERSION: 'CLIENT_MISS_VERSION',				// Client 버전 불일치
				CLIENT_FAIL_ALREADYEXIST: 'CLIENT_FAIL_ALREADYEXIST', 	// Client 오류 (이미 입장된Client있음)
				CLIENT_MISS_PASSWORD: 'CLIENT_MISS_PASSWORD', 			// Client 입장오류 (비밀번호 오류)
				CLIENT_FAIL: 'CLIENT_FAIL', 							// Client 오류 (그외. Unknown)
				OK_BANISHFORCE: 'OK_BANISHFORCE', 						// 멀티룸 강제퇴장
				OK_CLOSURE: 'OK_CLOSURE', 								// 멀티룸 종료
				
				CLIENT_RUNNING: 'CLIENT_RUNNING',						// 클라이언트 실행 중
				CLIENT_NOT_RUNNING: 'CLIENT_NOT_RUNNING',				// 클라이언트 실행 중 아님
			}
	}
	/*******************************************************************************************************
	 * Mobile Scheme 처리
	 *******************************************************************************************************/
	var MobileScheme = {
			// 실행
			exec: function(message, successCallback, errorCallback){
				if(Device.ext.isNative){
					this._toApp(message, successCallback, errorCallback);
				}
				else if(Device.ext.isWebapp){
					this._toWebapp(message, successCallback, errorCallback);
				}
				else{
					// Android
					if(Device.isAndroid()){
						var scheme = [];
						scheme.push('intent://mv?' + message + '#Intent');
						scheme.push('scheme=' + defaultSettings.client.mobile.scheme);
						scheme.push('action=android.intent.action.VIEW;category=android.intent.category.BROWSABLE');
						if(defaultSettings.client.mobile.store){
							if(Device.ext.isKakaoTalkApp){
								scheme.push('package=' + defaultSettings.client.mobile.packagename);
							}
						}
						scheme.push('end');
						
						this._exec_android(scheme.join(';'), successCallback, errorCallback);
					}
					// iOS
					else if(Device.isIos()){
						var scheme = defaultSettings.client.mobile.scheme + '://mv?' + message;
						
						this._exec_ios(scheme, successCallback, errorCallback);
					}
					// MAC
					else if(Device.isMac()){
						var scheme = defaultSettings.client.mac.scheme + '://mv?' + message;
						
						this._exec_mac(scheme, successCallback, errorCallback);
					}
				}
			},
			// 앱 내 호출
			_toApp: function(message, successCallback, errorCallback){
				if(defaultSettings.debug) console.debug('[MvApi] toApp.');
				
				try{
					// Android
					if(Device.isAndroid()){
						window.callbackHandler.postMessage(message);
						successCallback();
					} 
					// iOS and Mac
					else if(Device.isIos() || Device.isMac()){
						webkit.messageHandlers.callbackHandler.postMessage(message);
						successCallback();
					}
				}catch(e){
					console.error(e);
					errorCallback(ERROR_CODE.APP_CALL_ERROR, e);
				}
			},
			// WebAPP 인 경우 호출
			_toWebapp: function(message, successCallback, errorCallback){
				var scheme = defaultSettings.client.mobile.scheme + '://mv?' + message;
				if(defaultSettings.debug) console.debug('[MvApi] toWebapp. scheme:' + scheme);
				
				window.location.href = scheme;
				successCallback();
			},
			// Android Scheme 실행
			_exec_android: function(scheme, successCallback, errorCallback){
				if(defaultSettings.debug) console.debug('[MvApi] Android Scheme [', scheme, ']', Device.ext.chromeVersion);
				
				if(defaultSettings.client.mobile.store){
					if(Device.ext.isKakaoTalkApp){
						var popup = window.open();
						if(!popup) {
							alert(defaultSettings.localeMessage.allowPopup);
						}else{
							popup.location.href = scheme;
							setTimeout(function(){
								if(!popup.closed){
									popup.close();
									// 미설치
									ClientDownload.installGuide();
									
									errorCallback(ERROR_CODE.NOT_INSTALLED);
								}else{
									successCallback();
								}
							}, 1000);
						}
					}
					else{
						window.location.href = scheme;
						
						var clickedAt = +new Date;
						setTimeout(function(){
							if(new Date() - clickedAt < 1200){
								// 미설치
								ClientDownload.installGuide();
								
								errorCallback(ERROR_CODE.NOT_INSTALLED);
							}
							else{
								successCallback();
							}
						}, 1000);
					}
				}
				else{
					if(Device.ext.chromeVersion >= 42){
						window.location.href = scheme;
						
						var clickedAt = +new Date;
						setTimeout(function(){
							if(new Date() - clickedAt < 1200){
								// 미설치
								ClientDownload.installGuide();
								
								errorCallback(ERROR_CODE.NOT_INSTALLED);
							}
							else{
								successCallback();
							}
						}, 1000);
					}
					else if(Device.ext.chromeVersion >= 25) {
						var popup = window.open();
						if(!popup) {
							alert('팝업을 허용하고 다시 시도해 주세요.');
						}else{
							popup.location.href = scheme;
							setTimeout(function(){
								if(!popup.closed){
									popup.close();
									// 미설치
									ClientDownload.installGuide();
									
									errorCallback(ERROR_CODE.NOT_INSTALLED);
								}else{
									successCallback();
								}
							}, 1000);
						}
					}
					else {
						var $checkframe = $('<iframe></iframe>', {
							id : 'appSchemeCheckFrame'
							,src : scheme
							,width : 0
							,height : 0
						}).css({'border': '0', 'display': 'none'}).appendTo('body');
						
						setTimeout(function() {
							// body 내용 확인
							var bodyHtml = $checkframe.contents().find("body").html();
							if (bodyHtml == null || bodyHtml == '' || bodyHtml == undefined) {
								// 미설치
								ClientDownload.installGuide();
								
								errorCallback(ERROR_CODE.NOT_INSTALLED);
							}
							else{
								successCallback();
							}
						}, 2000);
					}
				}
			},
			// iOS Scheme 실행
			_exec_ios: function(scheme, successCallback, errorCallback){
				if(defaultSettings.debug) console.debug('[MvApi] iOS Scheme [', scheme, ']');
				
				var clickedAt = +new Date;
				setTimeout(function(){
					if(+new Date() - clickedAt < 2000){
						ClientDownload.installGuide();
						
						errorCallback(ERROR_CODE.NOT_INSTALLED);
					}else{
						successCallback();
					}
				}, 1500);
				
				if(parent.window.frames.length > 0){
					parent.window.location.href = scheme;
				}else{
					window.location.href = scheme;
				}
			},
			// MAC Scheme 실행
			_exec_mac: function(scheme, successCallback, errorCallback){
				if(defaultSettings.debug) console.debug('[MvApi] Mac Scheme [', scheme, ']');
				
				var $checkframe = $('<iframe></iframe>', {
					id : 'appSchemeCheckFrame'
					,src : scheme
					,width : 0
					,height : 0
				}).css({'border': '0', 'display': 'none'}).appendTo('body');
				
				setTimeout(function() {
					// body 내용 확인
					var bodyHtml = $checkframe.contents().find("body").html();
					if (bodyHtml == null || bodyHtml == '' || bodyHtml == undefined) {
						// 미설치
						ClientDownload.installGuide();
						
						errorCallback(ERROR_CODE.NOT_INSTALLED);
					}
					else{
						successCallback();
					}
				}, 1000);
//				var clickedAt = +new Date;
//				setTimeout(function(){
//					if(+new Date() - clickedAt < 2000){
//						ClientDownload.installGuide();
//						
//						errorCallback(ERROR_CODE.NOT_INSTALLED);
//					}else{
//						successCallback();
//					}
//				}, 1500);
//				
//				if(parent.window.frames.length > 0){
//					parent.window.location.href = scheme;
//				}else{
//					window.location.href = scheme;
//				}
			}
	}
	
	var Utils = {
			leadingZeros: function(n, digits){
				var zero = '';
				n = n.toString();
				if (digits > n.length) {
					for (var i = 0; digits - n.length > i; i++) {
						zero += '0';
					}
				}
				return zero + n;
			}
	}

	var Util = {
			pad: function(n, width) {
				n = n + '';
				return n.length >= width ? n : new Array(width - n.length + 1).join('0') + n;
			}
	}

	
	/*******************************************************************************************************
	 * Process Control
	 *******************************************************************************************************/
	var ProcessControl = {
			/**
			 * API 정의
			 */
			api: {
				running:  {name: 'running',  parameters: ['command']},
				open:     {name: 'open',     parameters: ['command','tcpsInfo','serverInfo','companyInfo','clientInfo','confInfo','confOption','userInfo','webInfo','confInfoEx']},
				openJoin: {name: 'openJoin', parameters: ['command','tcpsInfo','serverInfo','companyInfo','clientInfo','confInfo','confOption','joinInfo','userInfo','webInfo','confInfoEx']},
				join:     {name: 'join',     parameters: ['command','tcpsInfo','serverInfo','companyInfo','clientInfo','joinInfo','userInfo','webInfo','confInfoEx']},
				close:    {name: 'close',    parameters: ['command','tcpsInfo','serverInfo','companyInfo','clientInfo','confInfo','userInfo','webInfo']},
				quit:     {name: 'quit',     parameters: ['command','tcpsInfo','serverInfo','companyInfo','clientInfo','confInfo','userInfo','webInfo']},
				avset:    {name: 'avset',    parameters: ['command','clientInfo']},
				
				downloadClient:   {name: 'downloadClient',   parameters: null},
				
				webLoad:    {name: 'webLoad',     parameters: ['command','directWebInfo']},
				logout:     {name: 'logout',      parameters: ['command','clientInfo']},
				relogin:    {name: 'relogin',     parameters: ['command','clientInfo']},
				webMessage: {name: 'webMessage',  parameters: ['command','webMessage']},
			},
			_validation: function(api, requestMsg, errorCallback){
				// tcps.key
				if(defaultSettings.tcps.key == undefined || defaultSettings.tcps.key == ''){
					console.warn('[MvApi] tcps.key is required.');
					errorCallback(ERROR_CODE.NONE_TCPS_INFO, 'tcps.key');
					return false;
				}
				// server.ip
				if(defaultSettings.server.ip == undefined || defaultSettings.server.ip == ''){
					console.warn('[MvApi] server.ip is required.');
//					errorCallback(ERROR_CODE.NONE_SERVER_INFO, 'server.ip');
//					return false;
				}
				// server.port
				if(defaultSettings.server.port == undefined || defaultSettings.server.port == 0){
					console.warn('[MvApi] server.port is required.');
//					errorCallback(ERROR_CODE.NONE_SERVER_INFO, 'server.port');
//					return false;
				}
				// company.code
				if(defaultSettings.company.code == undefined || defaultSettings.company.code < 2){
					console.warn('[MvApi] company.code is required.');
					errorCallback(ERROR_CODE.NONE_COMPANY_INFO, 'company.code');
					return false;
				}
				// company.authKey
				if(defaultSettings.company.authKey == undefined || defaultSettings.company.authKey == ''){
					console.warn('[MvApi] company.authKey is required.');
					errorCallback(ERROR_CODE.NONE_COMPANY_INFO, 'company.authKey');
					return false;
				}
				// company.siteId
				if(defaultSettings.company.siteId !== undefined && defaultSettings.company.siteId !== ''){
					var v = Number(defaultSettings.company.siteId);
					if(!v || v < 0){
						console.warn('[MvApi] company.siteId is invalid. (only Number)');
						errorCallback(ERROR_CODE.INVALID_VALUE, 'company.siteId');
						return false;
					}
					defaultSettings.company.siteId = v;
				}
				// roomCode
				if(api.name == this.api.open.name || api.name == this.api.openJoin.name || api.name == this.api.join.name || api.name == this.api.close.name){
					if(requestMsg.roomCode == undefined || requestMsg.roomCode == ''){
						console.warn('[MvApi] roomCode is required.');
						errorCallback(ERROR_CODE.NONE_REQUIRED_VALUE, 'roomCode');
						return false;
					}
					if(!defaultSettings.client.encrypt){
						if(requestMsg.roomCode.length > 32){
							console.warn('[MvApi] roomCode is invalid. (max length: 32)');
							errorCallback(ERROR_CODE.INVALID_VALUE, 'roomCode');
							return false;
						}
						var prefixRoomCode = Utils.leadingZeros(defaultSettings.company.code, 6);
						var inputRoomCode = requestMsg.roomCode.substr(0, 6);
						if(prefixRoomCode != inputRoomCode){
							console.warn('[MvApi] roomCode is invalid. (prefix not match companyCode)');
							errorCallback(ERROR_CODE.INVALID_VALUE, 'roomCode');
							return false;
						}
					}
				}
				// template
				if(api.name == this.api.open.name || api.name == this.api.openJoin.name){
					if(requestMsg.template === undefined || requestMsg.template === ''){
						console.warn('[MvApi] template is required.');
						errorCallback(ERROR_CODE.NONE_REQUIRED_VALUE, 'template');
						return false;
					}
					var v = Number(requestMsg.template);
					if(!v || v < 0){
						console.warn('[MvApi] template is invalid. (only Number)');
						errorCallback(ERROR_CODE.INVALID_VALUE, 'template');
						return false;
					}
					requestMsg.template = v;
				}
				// title
				if(api.name == this.api.open.name || api.name == this.api.openJoin.name){
					if(requestMsg.title == undefined || requestMsg.title == ''){
						console.warn('[MvApi] title is required.');
						errorCallback(ERROR_CODE.NONE_REQUIRED_VALUE, 'title');
						return false;
					}
					if(requestMsg.title.length > 128){
						console.warn('[MvApi] title is invalid. (max length: 128)');
						errorCallback(ERROR_CODE.INVALID_VALUE, 'title');
						return false;
					}
				}
				// openOption
				if(api.name == this.api.openJoin.name){
					if(requestMsg.openOption == undefined || requestMsg.openOption == ''){
						requestMsg.openOption = 0;
					}
					var v = Number(requestMsg.openOption);
					if(v < 0 || v > 1){
						console.warn('[MvApi] openOption is invalid. (range: 0~1)');
						errorCallback(ERROR_CODE.INVALID_VALUE, 'openOption');
						return false;
					}
					requestMsg.openOption = v;
				}
				// joinUserType
				if(api.name == this.api.open.name || api.name == this.api.openJoin.name || api.name == this.api.join.name){
					if(requestMsg.joinUserType == undefined || requestMsg.joinUserType == ''){
						requestMsg.joinUserType = 1;
					}
					var v = Number(requestMsg.joinUserType);
					if(v <= 0 || v > 20){
						console.warn('[MvApi] joinUserType is invalid. (range: 1~20)');
						errorCallback(ERROR_CODE.INVALID_VALUE, 'joinUserType');
						return false;
					}
					requestMsg.joinUserType = v;
				}
				// userId
				if(api.name == this.api.open.name || api.name == this.api.openJoin.name || api.name == this.api.join.name || api.name == this.api.close.name || api.name == this.api.quit.name){
					if(requestMsg.userId == undefined || requestMsg.userId == ''){
						console.warn('[MvApi] userId is required.');
						errorCallback(ERROR_CODE.NONE_REQUIRED_VALUE, 'userId');
						return false;
					}
					if(!defaultSettings.client.encrypt){
						if(requestMsg.userId.length > 32){
							console.warn('[MvApi] userId is invalid. (max length: 32)');
							errorCallback(ERROR_CODE.INVALID_VALUE, 'userId');
							return false;
						}
					}
				}
				// userName
				if(api.name == this.api.open.name || api.name == this.api.openJoin.name || api.name == this.api.join.name || api.name == this.api.close.name || api.name == this.api.quit.name){
					if(requestMsg.userName == undefined || requestMsg.userName == ''){
						console.warn('[MvApi] userName is required.');
						errorCallback(ERROR_CODE.NONE_REQUIRED_VALUE, 'userName');
						return false;
					}
					if(!defaultSettings.client.encrypt){
						if(requestMsg.userName.length > 32){
							console.warn('[MvApi] userName is invalid. (max length: 32)');
							errorCallback(ERROR_CODE.INVALID_VALUE, 'userName');
							return false;
						}
					}
				}
				return true;
			},
			/**
			 * 기능 처리
			 */
			call: function(api, requestMsg, successCallback, errorCallback){
				if(!successCallback){
					successCallback = function(){
						console.info('[MvApi] success.');
					}
				}
				if(!errorCallback){
					errorCallback = function(errorCode, reason){
						console.error('[MvApi] error. errorCode: ', errorCode, reason);
					}
				}
				
				// 파라미터 명칭 변경에 다른 하위 호환
				requestMsg = this._requestMsg_convert(requestMsg);
				
				// 유효성 검사
				if(!this._validation(api, requestMsg, errorCallback)){
					return;
				}
				
				// 다운로드인 경우
				if(api.name == ProcessControl.api.downloadClient.name){
					ClientDownload.exec(requestMsg, successCallback, errorCallback);
					return;
				}
				
				// 클라이언트 실행 여부 확인인 경우
				if(api.name == ProcessControl.api.running.name){
					if(Device.isWindows()){
						WebAgent.running(successCallback, errorCallback);
					}
					else{
						successCallback();
					}
					return;
				}
				
				// 메시지 생성
				var message = MessageMaker.make(api.parameters, api.name, requestMsg);
				
				// 메시지 호출 - Windows Client
				if(Device.isWindows()){
					WebAgent.exec(message, successCallback, errorCallback);
				}
				// 메시지 호출 - Mac Client
				else if(Device.isMac()){
					MobileScheme.exec(message, successCallback, errorCallback);
				}
				// 메시지 호출 - Mobile Client
				else if(Device.isMobile()){
					MobileScheme.exec(message, successCallback, errorCallback);
				}
				else {
					console.warn('[MvApi] Not Supported Device.');
				}
			},
			_requestMsg_convert: function(requestMsg){
				if(requestMsg.roomCode == undefined && requestMsg.roomcode != undefined){
					requestMsg.roomCode = requestMsg.roomcode;
				}
				if(requestMsg.userId == undefined && requestMsg.userid != undefined){
					requestMsg.userId = requestMsg.userid;
				}
				if(requestMsg.userName == undefined && requestMsg.username != undefined){
					requestMsg.userName = requestMsg.username;
				}
				return requestMsg;
			}
	}
	
	/*******************************************************************************************************
	 * 외부 제공 Function
	 *******************************************************************************************************/
	var MvApi = {
			VERSION: VERSION,
			// 기본 설정 변경
			defaultSettings: function(settings){
				if(typeof settings != 'undefined' && typeof settings != 'object'){
					console.error('[MvApi] argument type only Object. input type: ' + (typeof settings));
					return defaultSettings;
				}
				$.extend(true, defaultSettings, settings);
				if(defaultSettings.debug) console.debug('[MvApi] settings: ', defaultSettings);
				return defaultSettings;
			},
			// 클라이언트 실행 여부 확인
			runCheck: function(requestMsg, successCallback, errorCallback){
				console.info('[MvApi] running');
				
				ProcessControl.call(ProcessControl.api.running, requestMsg, successCallback, errorCallback);
			},
			// 개설
			open: function(requestMsg, successCallback, errorCallback){
				console.info('[MvApi] open');
				
				ProcessControl.call(ProcessControl.api.open, requestMsg, successCallback, errorCallback);
			},
			// 개설 후 입장
			openJoin: function(requestMsg, successCallback, errorCallback){
				console.info('[MvApi] openJoin');
				
				ProcessControl.call(ProcessControl.api.openJoin, requestMsg, successCallback, errorCallback);
			},
			// 입장
			join: function(requestMsg, successCallback, errorCallback){
				console.info('[MvApi] join');
				
				ProcessControl.call(ProcessControl.api.join, requestMsg, successCallback, errorCallback);
			},
			// 폐쇄
			close: function(requestMsg, successCallback, errorCallback){
				console.info('[MvApi] close');
				
				ProcessControl.call(ProcessControl.api.close, requestMsg, successCallback, errorCallback);
			},
			// 퇴장
			quit: function(requestMsg, successCallback, errorCallback){
				console.info('[MvApi] quit');
				
				ProcessControl.call(ProcessControl.api.quit, requestMsg, successCallback, errorCallback);
			},
			// 장치 마법사
			avset: function(requestMsg, successCallback, errorCallback){
				console.info('[MvApi] avset');
				
				// 하위 호환으로 임시 처리
				if(typeof requestMsg == 'function'){
					errorCallback = successCallback;
					successCallback = requestMsg;
				}
					
				ProcessControl.call(ProcessControl.api.avset, {}, successCallback, errorCallback);
			},
			
			// 클라이언트 다운로드
			downloadClient: function(requestMsg, successCallback, errorCallback){
				console.info('[MvApi] downloadClient');
				
				ProcessControl.call(ProcessControl.api.downloadClient, requestMsg, successCallback, errorCallback);
			},
			
			// webLoad
			webLoad: function(requestMsg, successCallback, errorCallback){
				console.info('[MvApi] webLoad');
				
				ProcessControl.call(ProcessControl.api.webLoad, requestMsg, successCallback, errorCallback);
			},
			
			// 로그아웃 - ezViewX app에서만 사용
			logout: function(requestMsg, successCallback, errorCallback){
				console.info('[MvApi] logout');
				
				ProcessControl.call(ProcessControl.api.logout, {}, successCallback, errorCallback);
			},
			// 다시 로그인 - ezViewX app에서만 사용
			relogin: function(requestMsg, successCallback, errorCallback){
				console.info('[MvApi] relogin');
				
				ProcessControl.call(ProcessControl.api.relogin, {}, successCallback, errorCallback);
			},
			// 웹 메시지 전달 - ezViewX app에서만 사용
			webMessage: function(requestMsg, successCallback, errorCallback){
				console.info('[MvApi] webMessage');
				
				ProcessControl.call(ProcessControl.api.webMessage, requestMsg, successCallback, errorCallback);
			}
	};
	
	window.MvApi = MvApi;
})(window);

