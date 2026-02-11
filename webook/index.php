<?php
include( "_config.php" );

/**
 * 본 예시는 연동메뉴얼에서 제공되는 API들을 이용하여 구현한 데모의 index 파일입니다. (연동메뉴얼: https://goo.gl/YOjRhr)
 *
 * [데모파일 공통사항]
 * - _config.php 파일에 고객사 정보, API URL, API를 쿼리하기위한 클래스가 정의되어 있습니다. 
 * - 연동 API의 버전(URL)이 변경될수 있기 때문에 API URL은 _config.php와 같은 공통파일에 정의하여 공통파일의 내용을 변경할 경우 모든 부분에서 일괄 적용되도록 구현해 주세요.
 * - 모든 API의 리턴값은 UTF-8 Charset을 사용하므로 고객사 서버에서 UTF-8 이외의 Charset를 사용할 경우 적절하게 Convert Encording 해 주셔야 합니다.
 *
 * [이 데모파일 설명]
 * - 고객사의 커리큘럼 정보를 [커리큐럼조회 모듈] API를 통하여 '대분류' -> '과정' -> '교재' -> '유닛' 순서로 select box를 생성하여 순차적으로 쿼리하여 보여줍니다. (대분류는 이파일, 나머지는 _get_curri.php 이용하여 selectbox 구성함)
 * - 유닛 select box에서 유닛을 선택하면 [유닛컨텐츠출력 모듈] API를 통하여 특정 '유닛'에 대한 교재 내용을 '학생용', '학생용(모바일)', '강사용' 중 하나를  표시합니다. (이 예 에서는 '학생용' 표시, _get_unit_content.php)
 * - 이 예시를 이용하여 고객사에서 직접 학생용, 강사용 웹북을 구현할 수 있습니다. (전체 커리큘럼을 JT-Webbook의 교재를 사용하는 경우 JT-Webbook에서 제공되는 학생용 웹북을 사용할 수도 있습니다. 강사용은 본 예시를 참고하여 고객사 LMS에 반드시 직접 구현해 주세요!)
 */
?>
<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="utf-8">
<title> 웹북 연동 데모 </title>
<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
<style type="text/css">
	* { font-size:9pt }
</style>
<?php
/**
 * [커리큘럼조회 API] > '대분류' 쿼리
 *
 * @return string $data		대분류 목록을 각 레코드를 '#'으로 구분하여 리턴한다. 각 레코드의 필드는 "|"을 구분자로 한다. ("영문이름|한글이름|타입아이디|상위타입아이디|사용자정의값")
 */
{
	$option = array();
	
	// [공통정보(Head)]
	/*
	$option['보안코드'] = ''; // class에서 설정됨.
	$option['고객사도메인'] = ''; // class에서 설정됨.
	$option['접속단말종류'] = ''; // class에서 설정됨.
	$option['실행일시'] = ''; // class에서 설정됨.
	$option["학생아이디"] = ''; // class에서 설정됨.
	*/

	// [API 정보(Content)]	
	$option["언어"] = 'en';	// 'en', 'jp', 'cn' 설정	
	$option['타입'] = "대분류"; // "대분류", "과정", "교재", "유닛"	
	$option['상위타입아이디'] = ""; // 선택된 타입의 상위타입 아이디를 입력 (type='대분류'인 경우에는 '' 설정)

	// [공통정보(Tail)]
	$option['code복호값출력'] = 'No'; // code 복호값을 출력하려면 'Yes'를 설정한다
	$option['고객사정의필드'] = "index.php - 대분류 조회 (".$member["id"].")"; // 사용자정의 문자열로 고객사 시스템 파일 등을 지정할 수 있다 (디버깅 용도 등)	
	$option['API확장필드'] = ""; // API 확장 기능을 위한 필드
	$data = $JTW->query( "커리큘럼조회 API", $option );
}														

$curriculum = array();
$curriculum["대분류"] = explode( "#", $data );


echo '<table id="" cellpadding="4" cellspacing="1" bgcolor="#cccccc" border="0" style="margin:10px">';
echo '<colgroup><col width="200" style="background-color:#eeeeee;text-align:center"><col width="600" style="background-color:white;text-align:left"></colgroup>';

/**
 * [교재종류]
 *
 * - '학생용 교재' 또는 '강사용 교재'를 선택할 수 있다.
 * - 접속자가 학생인 경우 '학생용 교재', 강사용 경우 '강사용 교재'가 표시되도록 고객사 서버의 어플리케이션 구현시 처리해 주어야 한다.
 * - (참고) 강사용 교재는 학생용 교재와 완전히 동일할 수도 있고 다를 수도 있다. (JT-Webbook 교재 소개 페이지에서 참고)
 */
echo '<tr align="center">';
	echo '<td>교재종류</td>';
	echo '<td align="left">';
		echo '<select name="content_type" id="content_type">
				<option value="학생" selected>학생용 교재</option>
				<option value="강사">강사용 교재</option>';
		echo '</select>';
	echo '</td>';
echo '</tr>';

/**
 * [학생아이디]
 *
 * - 접속자가 '학생', '강사', '관리자'에 상관없이 항상 열람하고자 하는 학생아이디를 설정해 주어야한다.
 * - 접속자가 '학생'인 경우, 접속자 아이디를 설정하며, 접속자가 비회원(로그인 전)일 경우 빈값이 설정되도록 해주어야 한다.
 * - 접속자가 '강사' 또는 '관리자'일 경우에는 특정 학생 정보(수업 포함)에 대한 교재를 열람하는 경우이므로 해당 특정 학생의 아이디값이 설정되도록 처리해 주어야 한다
 *
 *
 * [학쟁진도정보조회]
 *
 * - JT-Webbook 시스템에는 특정 학생아이디에 대한 '학생진도정보'를 커리큘럼의 각 계층별로 조회 할 수 있다.
 * - (단, '학생진도정보'는 최근 15일 안에 전송 되었던 진도정보만 조회할 수 있다)
 * - 학생 진도 정보를 고객사 서버의 DB에서 직접 관리하는 경우 이 기능을 이용할 필요가 없다.
 */
echo '<tr align="center"><td>학생아이디</td><td align="left">';
	echo $_cfg["학생아이디"];
	if ( $_cfg["학생아이디"] ) {
		echo '<span style="margin-left:10px">학생진도정보조회</span> : <a href="#" class="btn_get_user_usage" type="">전체조회</a> | <a href="#" class="btn_get_user_usage" type="대분류">대분류조회</a> | <a href="#" class="btn_get_user_usage" type="과정">과정조회</a> | <a href="#" class="btn_get_user_usage" type="교재">교재조회</a>';
	}
echo '</tr>';


/**
 * [대분류]
 * 
 * - 고객사 커리큘럼 조회 (대분류)
 */
echo '<tr align="center">';
	echo '<td>대분류</td>';
	echo '<td align="left">';
		echo '<div id="group_div"><select name="group" id="group"><option value="">::대분류를 선택하세요</option>';
			foreach ( $curriculum["대분류"] as $key => $value ) {
				$arr = explode( "|", $value );
				{
					$fields["영문이름"] = $arr[0];
					$fields["한글이름"] = $arr[1];
					$fields["타입아이디"] = $arr[2];
				}
				echo '<option value="'.$fields['타입아이디'].'">'.$fields['영문이름'].' ('.$fields['한글이름'].')</option>';
			}
			echo '</select></div>';
	echo '</td>';
echo '</tr>';

/**
 * [과정]
 * 
 * - 고객사 커리큘럼 조회 (과정)
 */
echo '<tr align="center">';
	echo '<td>과정1</td>';
	echo '<td align="left">';
		echo '<select id="category_div"></select>';
	echo '</td>';
echo '</tr>';

/**
 * [교재]
 * 
 * - 고객사 커리큘럼 조회 (교재)
 */
echo '<tr align="center">';
	echo '<td>교재</td>';
	echo '<td align="left">';
		echo '<div id="book_div"></div>';
	echo '</td>';
echo '</tr>';

/**
 * [유닛]
 * 
 * - 고객사 커리큘럼 조회 (유닛)
 */
echo '<tr align="center">';
	echo '<td>유닛</td>';
	echo '<td align="left">';
		echo '<div id="unit_div"></div>';
		echo '<div id="JTW-print" style="display:none"></div>';
	echo '</td>';
echo '</tr>';

/**
 * [유닛컨텐츠타입]
 * 
 * - '유닛컨텐츠'를 JT-Webbook에서 iframe으로 가공된 페이지를 리턴(추천)받을지, Raw Data 형식으로 리턴받을지 선택한다.
 * - 후자는 고객사에서 '유닛컨텐츠' 문서를 마크업해야하는 경우 사용될 수 있다(단, 영자신문 컨텐츠의 경우는 Raw Data를 제공하지 않음, 영자신문은 턴키방식으로 별도 데이터 전달 가능)
 */
echo '<tr align="center">';
	echo '<td>유닛컨텐츠타입</td>';
	echo '<td align="left">';
		echo '<select name="unit_content_type" id="unit_content_type">
				<option value="" selected>기본값 (JT-Webbook에서 가공한 HTML문서)</option>
				<option value="Raw Contents">Raw Contents (영자신문은 사용불가, 턴키로 RAW 데이터 공급 가능)</option>';
		echo '</select>';
	echo '</td>';
echo '</tr>';

/**
 * [선택된 유닛컨텐츠 음원]
 *
 * - 이 기능은 리턴되는 '유닛컨텐츠' IFRAME 문서에 이미 구현되어 있으므로 일반적으로 사용되지 않으며, 
     고객사에서 음원재생 플레이어와 다운로드 기능을 직접 구현하려고 할 경우 선택적으로 사용된다.
 */
echo '<tr align="center">';
	echo '<td>선택된 유닛컨텐츠 음원</td>';
	echo '<td align="left">';
		echo '<div id="audio_div" style="display:none"><a href="#" title="음원URL" target="_blank" id="btn_audio" >음원재생URL</a> | <a href="#" title="어학학습 플레이어 모듈" target="_blank" id="btn_audio_player">어학학습 플레이어 모듈로 실행</a> | <a href="#" title="음원 다운로드 모듈" id="btn_audio_download">음원 다운로드</a></div>';
	echo '</td>';
echo '</tr>';

/**
 * [타입상세조회]
 *
 * - 커리큘럼(대분류-과정-교재-유닛) 별로 특정 커리큘럼 아이디에 대한 상세 정보를 구할 수 있다.
 */
echo '<tr align="center">';
	echo '<td>타입상세조회</td>';
	echo '<td align="left">';
		echo '<div><a href="#" title="대분류상세조회" class="btn_get_type_info" type="대분류">대분류상세조회</a> | <a href="#" title="과정상세조회" class="btn_get_type_info" type="과정">과정상세조회</a> | <a href="#" title="교재상세조회" class="btn_get_type_info" type="교재">교재상세조회</a> | <a href="#" title="유닛상세조회" class="btn_get_type_info" type="유닛">유닛상세조회</a></div>';
	echo '</td>';
echo '</tr>';

/**
 * [진도정보전송]
 *
 * - JT-Webbook로 특정 학생아이디의 진도정보를 전송한다.
 */
echo '<tr align="center">';
	echo '<td>진도정보전송 (관리자, 강사용)</td>';
	echo '<td align="left">';
		if ( $_cfg["학생아이디"] ) {
			echo '<a href="#" id="btn_put_user_usage">학생 진도정보 전송하기</a>';
		} else {
		    echo '진도 정보전송 기능은 회원만 가능합니다.';
		}
	echo '</td>';
echo '</tr>';

/**
 * [JT-Webbook 웹북모듈 (학생용)]
 *
 * - 일반열람: 학생용 웹북을 실행
 * - 일반열람(미니): 세로사이즈가 600px 화면에 대응하는 웹북이 실행된다.
 * - 특정유닛 바로가기: unit_id에 설정된 유닛컨텐츠가 웹북 실행과 동시에 바로 실행되도록 한다. (샘플보기, 내교재 바로가기 구현에 활용가능)
 */

{
	/**
	 * 학생용 웹북 설정 파일 (
	 * - JT-Webook를 통하여 고객사의 전체 커리큘럼을 관리하는 형태로 이용할 고객사에서 사용할 수 있는 모듈
	 */

	{
		$option = array();

		// [공통정보(Head)]
		/*
		$option['보안코드'] = ''; // class에서 설정됨.
		$option['고객사도메인'] = ''; // class에서 설정됨.
		$option['접속단말종류'] = ''; // class에서 설정됨.
		$option['실행일시'] = ''; // class에서 설정됨.
		$option["학생아이디"] = ''; // class에서 설정됨.
		*/

		// [API 정보(Content)]	
		$option["언어"] = 'en';	// 'en', 'jp', 'cn' 설정
		$option['내교재바로보기'] = "Server"; // 
		$option['숨은커리큘럼표시'] = 'No'; // 'Yes' 설정할 경우, 고객사 커리큘럼 정보에서 '웹북미출력'='Yes'로 설정되어 학생웹북에 표시되지 않는 과정(또는 교재)을 학생웹북에 보여지도록 한다. (특수용도임)
		$option['미니버전'] = 'No'; // 'Yes' 설정할 경우, 세로 사이즈가 600px인 웹북이 실행된다.

		// [공통정보(Tail)]
		$option['code복호값출력'] = 'No'; // code 복호값을 출력하려면 'Yes'를 설정한다
		$option['고객사정의필드'] = "demo > student_webbook.php (".$member["id"].")"; // 사용자정의 문자열로 고객사 시스템 파일 등을 지정할 수 있다 (디버깅 용도 등)	
		$option['API확장필드'] = ""; // 확장 기능을 위한 필드

		// shortcut 파라메터
		$option['바로가기'] = '';
	}

	// 일반버전
	$student_webbook_url = $JTW->query( "학생웹북 모듈", $option );	

	// 모바일 버전 
	$student_webbook_mobile_url = $JTW->query( "학생웹북 모듈 (모바일)", $option );
   	
	// mini 일반버전 
	{
		$option['미니버전'] = 'Yes';
	}
	$student_webbook_mini_url = $JTW->query( "학생웹북 모듈", $option );

	
}
echo '<tr align="center">';
	echo '<td>JT-Webbook 웹북 모듈 (학생용)</td>';
	echo '<td align="left">';
		echo '<a href="'.$student_webbook_url.'" target="_blank" id="student_webbook">일반열람</a>';
		echo ' | <a href="'.$student_webbook_mini_url.'" target="_blank">일반열람(미니)</a>';		
		echo ' | <a href="#" target="_blank" class="student_webbook_shortcut" type="unit" device="desktop">특정유닛 바로가기</a>';		
		echo ' | <a href="#" target="_blank" class="student_webbook_shortcut" type="book" device="desktop">특정교재 바로가기</a>';
		echo ' | <a href="#" target="_blank" class="student_webbook_shortcut" type="category" device="desktop">특정과정 바로가기</a><br>';
		echo '<a href="'.$student_webbook_mobile_url.'" target="_blank" id="student_webbook_mobile">일반열람(모바일)</a>';
		echo ' | <a href="#" target="_blank" class="student_webbook_shortcut" type="unit" device="mobile">특정유닛 바로가기 (모바일)</a>';
		echo ' | <a href="#" target="_blank" class="student_webbook_shortcut" type="book" device="mobile">특정교재 바로가기 (모바일)</a>';
	echo ' (접속단말: '.$JTW->get_UA_type().')</td>';
echo '</tr>';
echo '</table>';
echo '<div id="content" style="width:100%;"></div>';
?>
<script type="text/javascript">
<!--
/**
 * 위의 대분류 select 를 선택하면 선택된 대분류 아이디를 키값으로 하여 순차적으로 '과정'-'교재'-'유닛'을 ajax(_get_curri.php)를 통하여 질의한다.
 */
$("#group").change( function() {
	init_category();
	$.post( "_get_curri.php", { type:'과정', type_id:$(this).val() }, function( data ) {

		$("#category_div").html( data ); 

		$("#category").change( function() {
			
			init_book(); 

			$.post( "_get_curri.php", { type:'교재', type_id:$(this).val() }, function( data ) {
				
				$("#book_div").html( data ); 				

				$("#book").change( function() {
					
					init_unit();

					// max_chapter_unit은 '학생아이디'별 열람가능한 최대 유닛정보를 의미한다. (_get_curri.php 파일 구현 참고)
					$.post( "_get_curri.php", { type:'유닛', type_id:$(this).val(), max_chapter_unit:$("#book option:selected").attr("max-chapter-unit"), current_chapter_unit:$("#book option:selected").attr("current-chapter-unit") }, function( data ) {
						
						$('#unit_div').html( data );
						
						$("#unit").change( function() {
														
							init_content();
							
							$wrapper = $(window);

							$top_table_height = $("#top_table").height() + 40;// iframe의 세로 사이즈를 동적으로 조절하기 위한 변수
							var iframe_height = $wrapper.height() - $top_table_height;
							$.post( "_get_unit_content.php", { content_type:$("#content_type").val(), unit_id:$("#unit").val(), api_extension:$("#unit_content_type").val(), width:"100%", height: iframe_height, unit_contents_type:$("#unit option:selected").attr("unit_contents_type") })
							.done(function( data ) {
								$("#content").html( data );
								console.log(data);

								$( window ).resize( function() {									
									$("#JTW-iframe").css( "height", $wrapper.height()-$top_table_height );
								} );
  							});

							/**
							 * 부가 컨텐츠 쿼리 (api_extension = 'Additional Contents'를 설정하여 오디오 URL를 쿼리한다
							 */
							$.post( "_get_unit_content.php", { content_type:$("#content_type").val(), unit_id:$("#unit").val(), api_extension:"Additional Contents" }, function( data ) {
								if ( data ) {
									$("#audio_div").show();
									$("#btn_audio").attr( "href", data );
									$("#btn_audio_player").attr( "href", "<?php echo $_cfg["음원플레이어모듈"];?>?code=" + $("#unit").val() + "&cid=<?php echo $_cfg["고객사아이디"];?>" );
									$("#btn_audio_download").attr( "href", "<?php echo $_cfg["음원다운로드모듈"];?>?code=" + $("#unit").val() + "&cid=<?php echo $_cfg["고객사아이디"];?>" );
								} else {
									$("#audio_div").hide();
								}								
							} );
						} );	
						
						$("#content_type").change( function() {
							$("#unit").change();
						} );

						$("#unit_content_type").change( function() {
							$("#unit").change();
						} );
					} );
				} );
			} );
		} );
	} );
} );

// 각 선택박스 영역을 초기화 하는 함수 (매겨변후 이하의 선택박스를 초기화 한다, '대분류','과정','교재',유닛')
function init_content()
{
	$("#content").empty();
}

function init_audio()
{
	$("#audio_div").hide();
}

function init_unit()
{
	init_content();
	init_audio();
	$("#unit_div").empty();
}

function init_book()
{
	init_unit();
	$("#book_div").empty();
}

function init_category()
{
	init_book();
	$("#category_div").empty();
}


function init_group()
{
	init_category();
	$("#group_div").empty();
}

// 학생최종진도 모듈 조회
$(".btn_get_user_usage").on( "click", function() {

	$type = $(this).attr("type");
	$type_id = "";
	if ( $type == "대분류" ) {
		$type_id = $("#group").val();
	} else if ( $type == "과정" ) {
		$type_id = $("#category").val();
	} else if ( $type == "교재" ) {
		$type_id = $("#book").val();
	}

	if ( $type != "" ) {	
		if ( $type_id == "" ) {
			alert( "먼저 '" + $type + "'을 선택해 주세요" );
			return;
		}
	}

	$.post( "_get_user_usage.php", { type: $(this).attr("type"), type_id: $type_id }, function( data ) {
		alert( data );
	} );
} );


// 특정 타입(대분류, 과정, 교재, 유닛)의 상세 정보 조회
$(".btn_get_type_info").on( "click", function() {

	$type = $(this).attr( "type" );
	$type_id = "";
	{
		if ( $type == "대분류" ) {
			$type_id = $("#group").val();
		} else if ( $type == "과정" ) {
			$type_id = $("#category").val();
		} else if ( $type == "교재" ) {
			$type_id = $("#book").val();			
		} else if ( $type == "유닛" ) {
			$type_id = $("#unit").val();			
		}
	}

	if ( $type_id ) { 
		$.post( "_get_type_info.php", { type:$type, type_id:$type_id }, function( data ) { 
			alert( data );	
		} );
	} else {
		alert( $type+"을 선택해 주세요." );	
	}
} );


// JT-Webbook 서버로 학생진도 정보를 전송
$("#btn_put_user_usage").on( "click", function() { 	
	$unit_id = $('#unit').val();
	if ( $unit_id ) {
		$.post( "_put_user_usage.php", { type:'유닛', unit_id:$unit_id }, function( data ) { 
			alert( data );	
		} );
	} else {
		alert( '먼저 유닛을 선택해 주세요.' );
		return false;
	}
} );


// JT-Webbook 학생웹북을 바로가기 기능으로 실행하는 경우 (유닛아이디에 대한 바로가기만 구현한 예시임)
$(".student_webbook_shortcut").on( "click", function() { 	

	$type = $(this).attr( "type" );
	$device = $(this).attr( "device" );

	$type_id = $("#"+$type).val();
	if ( $type_id ) {
		if ( $device == "mobile" ) {
			url = $("#student_webbook_mobile").attr("href") + "&shortcut=" + $type_id + "@" + $type;
		} else {
			url = $("#student_webbook").attr("href") + "&shortcut=" + $type_id + "@" + $type;			
		}
		$(this).attr( "href", url ).click();
	} else {
		if ( $type == 'unit' ) {
			alert( "먼저 유닛을 선택해 주세요." );
		} else if ( $type == 'book' ) {
			alert( "먼저 교재를 선택해 주세요." );	
		} else if ( $type == 'category' ) {
			alert( "먼저 과정을 선택해 주세요." );	
		}
		return false;
	}
} );
//-->
</script>