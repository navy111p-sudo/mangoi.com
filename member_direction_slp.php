<?php
include_once('./includes/dbopen.php');
include_once('./includes/common.php');
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
<!--<script src="js/common.js"></script>-->

<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
</head>
<body>
<?
include_once('./includes/common_body_top.php');
?>
<?

function encrypt($arr) {
	$key = 'f9a90bfa9e8d1d9965fecc00q2e6786cf59f31x1';
	$key_256 = substr($key, 0, 256/8);
	$iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
	$encrypt_arr = openssl_encrypt($arr, 'AES-256-CBC', $key_256, 0, $iv);
	return $encrypt_arr;
}

function decrypt2($encrypt_arr) {
	//$encrypt_arr = str_replace(' ', '+', $encrypt_arr);
	//$encrypt_arr = urlencode($encrypt_arr);
	$encrypt_arr = preg_replace('/\ /', '+', $encrypt_arr);
	$decrypt_arr_arr = [];
	$key = 'f9a90bfa9e8d1d9965fecc00q2e6786cf59f31x1';
	$key_256 = substr($key, 0, 256/8);
	$iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
	$decrypt_arr = openssl_decrypt($encrypt_arr, 'AES-256-CBC', $key_256, 0, $iv);
	$decrypt_arr = explode('|', $decrypt_arr);

	foreach($decrypt_arr as $decrypt_arr_index => $decrypt_arr_value) {
		$decrypt_arr[$decrypt_arr_index] = $decrypt_arr_value;
	}
	return $decrypt_arr;
}

function EncodeURL($encrypt_arr) {
	//$encrypt_arr = str_replace(' ', '+', $encrypt_arr);
	//$encrypt_arr = urlencode($encrypt_arr);
	$encrypt_arr = preg_replace('/\ /', '+', $encrypt_arr);
	return $encrypt_arr;
}


$sso = isset($_GET['sso']) ? $_GET['sso'] : "";
$str = isset($_GET['str']) ? $_GET['str'] : "";
$Param = "";



// 학생은 sso 관리자는 str 파라미터로 넘어옴에 따른 분기
if($str!="") {
	$Param = $str;
	$arr = decrypt2($str);
} else if($sso!="") {
	$Param = $sso;
	$arr = decrypt2($sso);
}

$Param = EncodeURL($Param);
//$Param = preg_replace('/\ /', '+', $Param);

$SLPMemberLoginID = $arr[2];

$Sql = " select  count(*) as TotalRowCount
	from Members A 
	where A.MemberLoginID=:SLPMemberLoginID
	and A.MemberState=1 
	";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':SLPMemberLoginID', $SLPMemberLoginID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$TotalRowCount = $Row["TotalRowCount"];
$Stmt = null;  

?>

<link href="https://fonts.googleapis.com/css?family=Noto+Sans+KR:100,300,400,500,700,900&display=swap&subset=korean" rel="stylesheet">
    
<style>
*{margin:0; padding:0; line-height:1; font-family: 'Noto Sans KR', sans-serif;}
.slp_wrap{width:100%; max-width:1200px; margin:0 auto; padding:0 3%;}
.slp_guide{line-height:1.8; font-size:14px; }  
.slp_guide_text{padding:25px 30px; background-color:#fafafa; border-radius:10px; line-height:1.7; border:2px solid #efefef; color:#444;}
.slp_bold{font-size:17px; color:#C00; padding:0 0 10px 0;}
.slp_guide_list{margin:0 0 0 15px}
.slp_guide_list li{ line-height:1.6;}    
.slp_title{font-size:20px; font-weight:500; padding:30px 0 15px 5px;}
.slp_title:before{content:''; display:inline-block; width:6px; height:6px; border-radius:50%; background-color:#444; vertical-align:5px; margin:0 8px 0 0;}
.logo_slp{position:absolute; right:35px; top:16px; width:130px;}
.slp_content{line-height:1.8; padding:0 30px 30px 30px; color:#555;}  
.slp_cpation{color:#111; padding:20px 0 12px 0; font-size:17px; font-weight:500;}
 
</style>


<div class="slp_wrap">

	<div class='slp_guide' id="ConfirmTitle" style="margin:30px auto;display:none;">
		<h4 class='slp_title'>'eduSLP-망고아이' 서비스 통합에 따른 계정 전환 안내</h4>
		<div class='slp_guide_text'>
			<h5 class='slp_bold'>SLP 재원생 및 학부모님께 안내 말씀 드립니다.</h5>다음과 같이 망고아이 화상영어(www.mangoi.com)와 eduSLP(www.eduslp.ac.kr) 서비스가 통합됩니다. 이에 기존에 보유하고 계셨던 각 사이트의 계정(ID)을 하나의 eduSLP 계정으로 통합하셔야 정상적인 서비스 이용이 가능합니다. 계정 통합에 SLP 재원생 및 학부모님의 협조를 부탁드립니다.
			<ul class='slp_guide_list'>
				<li>전환 대상 : eduSLP 회원 및 망고아이 회원</li>
				<li>전환 방법 : 아래의 절차에 따라 통합계정으로 전환</li>
				<li>관련 문의 : SLP 망고아이 고객센터 1644-0561(이용시간: 월~금 10:00~21:00)</li>
			</ul>
		</div>
		<h4 class='slp_title'>서비스 통합에 따른 개인정보 활용 동의서</h4>

		<div class="slp_content" id="ConfirmContent" style="height:300px;overflow-y:scroll;display:none;border:2px solid #cccccc;padding:30px;border-radius:10px;">
				<h4 class="slp_cpation" style="padding-top:0;">제1조 목적</h4>본 약관은 ㈜서강교육그룹(이하 "회사"라 한다)이 운영하는 통합 사이트와 기타 학습 사이트<br/>SLP 온라인 통합 학습사이트 (www.eduslp.ac.kr),<br/>SLP 화상영어 망고아이 사이트(www.slpmangoi.com),<br/>SLP 온라인 독서 프로그램 사이트(sori. eduslp.ac.kr)에서<br/>제공하는 서비스(이하 합하여 "서비스"라 한다)를 하나의 회원 아이디와 비밀번호로 동시에 각 사이트에 가입하여 이용함에 있어 회사와 이용자의 권리, 의무 및 책임사항을 규정함을 목적으로 합니다.<h4 class="slp_cpation">제2조 통합 회원가입</h4>1. 이용자는 “회사”가 정한 가입 양식에 따라 SLP 온라인 통합 학습사이트에 회원으로 가입한 후, 이 약관에 동의한다는 의사 표시를 함으로서 계정 통합을 신청합니다.<br/>2. “회사”는 제1항과 같이 회원으로 가입할 것을 신청한 이용자 중 다음 각호에 해당하지 않는 한 회원으로 등록합니다.<br/>①. 가입신청자가 이 약관 제3조 제3항에 의하여 이전에 회원자격을 상실한 적이 있는 경우, 다만 제3조 제3항에 의한 회원자격 상실 후 3년이 경과한 자로서 “회사”의 회원 재가입 승낙을 얻은 경우에는 예외로 합니다.<br/>②. 등록 내용에 허위, 기재누락, 오기가 있는 경우<br/>③. 기타 회원으로 등록하는 것이 “회사”의 기술상 현저히 지장이 있다고 판단되는 경우<br/>3. 회원가입계약의 성립시기는 “회사”의 승낙이 회원에게 도달한 시점으로 합니다.<br/>4. 하나의 ID 및 PASSWORD로 이용할 수 있는 ㈜서강교육그룹의 사이트는 다음과 같으며, 2013년 9월 2일 이후부터 SLP 온라인 통합 학습사이트(eduSLP)에 한하여 회원등록이 가능하며, eduSLP 사이트 회원등록 시 부여 받은 ID로 접속(Log-in)하여 별도의 로그인 없이 모든 곳을 이용할 수 있습니다.<br/>①. SLP 온라인 통합 학습사이트 (www.eduslp.ac.kr)<br/>②. SLP 화상영어 망고아이 사이트(www.slpmangoi.com)<br/>③. SLP 온라인 독서 프로그램 사이트(sori. eduslp.ac.kr)<h4 class="slp_cpation">제3조 회원 탈퇴 및 자격 상실 등</h4>1. 회원은 “회사”에 언제든지 탈퇴를 요청할 수 있으며 “회사”는 즉시 회원탈퇴를 처리합니다.<br/>2. 회원이 다음 각호의 사유에 해당하는 경우, “회사”는 회원자격을 제한 및 정지시킬 수 있습니다.<br/>①. 가입 신청 시에 허위 내용을 등록한 경우<br/>②. “회사”를 이용하여 구입한 재화 등의 대금, 기타 “회사”이용에 관련하여 회원이 부담하는 채무를 기일에 지급하지 않는 경우<br/>③. 다른 사람의 “회사” 이용을 방해하거나 그 정보를 도용하는 등 전자상거래 질서를 위협하는 경우<br/>④. “회사”를 이용하여 법령 또는 이 약관이 금지하거나 공서 양속에 반하는 행위를 하는 경우<br/>3. “회사”가 회원 자격을 제한·정지 시킨 후, 동일한 행위가 2회 이상 반복되거나 30일 이내에 그 사유가 시정되지 아니하는 경우 “회사”는 회원자격을 상실시킬 수 있습니다.<br/>4. “회사”가 회원자격을 상실시키는 경우에는 회원등록을 말소합니다. 이 경우 회원에게 이를 통지하고, 회원등록 말소 전에 최소한 30일 이상의 기간을 정하여 소명할 기회를 부여합니다.<h4 class="slp_cpation">제 4 조 회원에 대한 통지</h4>1. “회사”가 회원에 대한 통지를 하는 경우, 회원이 “회사”와 미리 약정하여 지정한 전자우편 주소로 할 수 있습니다.<br/>2. “회사”는 불특정다수 회원에 대한 통지의 경우 1주일이상 “회사” 게시판에 게시함으로서 개별 통지에 갈음할 수 있습니다. 다만, 회원 본인의 거래와 관련하여 중대한 영향을 미치는 사항에 대하여는 개별통지를 합니다.<h4 class="slp_cpation">제 5 조 개인정보보호</h4>1. “회사”는 이용자의 정보 수집 시 구매계약 이행에 필요한 최소한의 정보를 수집합니다.<br/>다음 사항을 필수사항으로 하며 그 외 사항은 선택사항으로 합니다.<br/>①. 학부모 성명<br/>②. 학부모 아이디<br/>③. 비밀번호<br/>④. 학부모 주민등록번호<br/>⑤. 주소<br/>⑥. 일반전화번호<br/>⑦. 핸드폰번호 (SMS수신여부)<br/>⑧. 전자우편주소 (메일수신여부)<br/>⑨. 재원중인 SLP학당명<br/>⑩. 사이트에 가입할 자녀 수<br/>⑪. 자녀 아이디<br/>⑫. 자녀 비밀번호<br/>⑬. 자녀 국문 이름<br/>⑭. 자녀 영문 이름<br/>⑮. 학교명 (학년, 취학여부)<br/><br/>2. “회사”가 이용자의 개인식별이 가능한 개인정보를 수집하는 때에는 반드시 당해 이용자의 동의를 받습니다.<br/>3. 제공된 개인정보는 당해 이용자의 동의없이 목적외의 이용이나 제3자에게 제공할 수 없으며, 이에 대한 모든 책임은 “회사”가 집니다. 다만, 다음의 경우에는 예외로 합니다.<br/>①. 배송업무상 배송업체에게 배송에 필요한 최소한의 이용자의 정보(성명, 주소, 전화번호)를 알려주는 경우<br/>②. 통계작성, 학술연구 또는 시장조사를 위하여 필요한 경우로서 특정 개인을 식별할 수 없는 형태로 제공하는 경우<br/>③. 재화등의 거래에 따른 대금정산을 위하여 필요한 경우<br/>④. 도용방지를 위하여 본인확인에 필요한 경우<br/>⑤. 법률의 규정 또는 법률에 의하여 필요한 불가피한 사유가 있는 경우<br/>4. “회사”가 제2항과 제3항에 의해 이용자의 동의를 받아야 하는 경우에는 개인정보관리 책임자의 신원(소속, 성명 및 전화번호, 기타 연락처), 정보의 수집목적 및 이용목적, 제3자에 대한 정보제공 관련사항(제공받은자, 제공목적 및 제공할 정보의 내용) 등 정보통신망이용촉진등에관한법률 제22조제2항이 규정한 사항을 미리 명시하거나 고지해야 하며 이용자는 언제든지 이 동의를 철회할 수 있습니다.<br/>5. 이용자는 언제든지 “회사”가 가지고 있는 자신의 개인정보에 대해 열람 및 오류정정을 요구할 수 있으며 “회사”는 이에 대해 지체 없이 필요한 조치를 취할 의무를 집니다. 이용자가 오류의 정정을 요구한 경우에는 “회사”는 그 오류를 정정할 때까지 당해 개인정보를 이용하지 않습니다.<br/>6. “회사”는 개인정보 보호를 위하여 관리자를 한정하여 그 수를 최소화하며 신용카드, 은행계좌 등을 포함한 이용자의 개인정보의 분실, 도난, 유출, 변조 등으로 인한 이용자의 손해에 대하여 모든 책임을 집니다.<br/>7. “회사” 또는 그로부터 개인정보를 제공받은 제3자는 개인정보의 수집목적 또는 제공받은 목적을 달성한 때에는 당해 개인정보를 지체 없이 파기합니다.<br/>8. “회사"는 회원이 탈퇴한 경우 당해 회원의 개인정보를 파기하는 것을 원칙으로 합니다. 다만, 전자상거래등에서의 소비자보호에 관한 법률 제6조 및 동법 시행령 제6조에 의하여 표시ㆍ광고에 관한 기록은 6월, 계약 또는 청약철회 등에 관한 기록은 5년, 대금결제 및 재화 등의 공급에 관한 기록은 5년, 소비자의 불만 또는 분쟁처리에 관한 기록은 3년간 보존합니다.<h4 class="slp_cpation">제 6 조 회사의 의무</h4>1. “회사”는 법령과 이 약관이 금지하거나 공서양속에 반하는 행위를 하지 않으며 이 약관이 정하는 바에 따라 지속적이고, 안정적으로 재화·용역을 제공하는데 최선을 다하여야 합니다.<br/>2. “회사”는 이용자가 안전하게 인터넷 서비스를 이용할 수 있도록 이용자의 개인정보(신용정보포함)보호를 위한 보안 시스템을 갖추어야 합니다.<br/>3. “회사”의 상품이나 용역에 대하여 「표시·광고의공정화에관한법률」 제3조 소정의 부당한 표시·광고행위를 함으로써 이용자가 손해를 입은 때에는 이를 배상할 책임을 집니다.<br/>4. “회사”는 이용자가 원하지 않는 영리목적의 광고성 전자우편을 발송하지 않습니다.<h4 class="slp_cpation">제 7 조 저작권의 귀속 및 이용제한</h4>1. “회사“가 작성한 저작물에 대한 저작권 기타 지적재산권은 ”회사“에 귀속합니다.<br/>2. 이용자는 “회사”를 이용함으로써 얻은 정보 중 “회사”에게 지적재산권이 귀속된 정보를 “회사”의 사전 승낙없이 복제, 송신, 출판, 배포, 방송 기타 방법에 의하여 영리목적으로 이용하거나 제3자에게 이용하게 하여서는 안됩니다.<br/>3. “회사”는 약정에 따라 이용자에게 귀속된 저작권을 사용하는 경우 당해 이용자에게 통보하여야 합니다.<br/>4. 회원이 이용 계약을 해지하고자 하는 때에는 회원 본인이 회원 탈퇴를 회사에 신청하여야 합니다.<br/>5. 회사는 회원이 다음 각 호에 해당하는 행위를 하였을 경우 사전통지 없이 이용계약을 해지하거나 또는 일정기간 이용을 제한할 수 있습니다.<br/>① 타인의 서비스 ID 및 비밀번호를 도용한 경우<br/>② 서비스 운영을 고의로 방해한 경우<br/>③ 가입한 이름이 실명이 아닌 경우<br/>④ 같은 사용자가 다른 아이디로 이중등록을 한 경우<br/>⑤ 공공질서 및 미풍양속에 저해되는 내용을 고의로 유포시키는 경우<br/>⑥ 범죄적 행위에 관련되는 경우<br/>⑦ 타인의 명예를 손상시키거나 불이익을 주는 경우<br/>⑧ 회원이 국익 또는 사회적 공익을 저해할 목적으로 서비스 이용을 계획 또는 실행하는 경우<br/>⑨ 정보통신설비의 오작동이나 정보의 파괴를 유발시키는 컴퓨터 바이러스 프로그램 등을 유포하는 경우<br/>⑩ 회사의 서비스 정보를 이용하여 얻은 정보를 회사의 사전 승낙없이 복제 또는 유통시키거나 상업적으로 이용하는 경우<br/>⑪ 회원이 자사의 홈페이지와 게시판에 음란물을 게재하거나 음란 사이트 링크하는 경우<br/>⑫ 본 약관을 포함하여 기타 회사가 정한 이용 조건에 위반한 경우<br/><br/>6. 개인정보의 보유 및 이용기간<br/>회원님이 회사의 회원으로 회사에서 제공하는 서비스를 이용하는 동안 회사는 회원님들의 개인정보를 계속적으로 보유합니다.<br/>단, 개인정보의 수집목적 또는 제공받은 목적이 달성된 때에는 귀하의 개인정보를 지체 없이 파기합니다.<br/>① 회원가입정보: 탈퇴 시 또는 제명된 때<br/>② 대금지급정보: 대금 완제일 또는 채권소멸시효기간 만료시<br/>③ 배송정보: 물품 또는 서비스가 인도되거나 제공된 때<br/>④ 설문조사, 이벤트: 당해 설문조사, 이벤트가 완료된때<br/><br/>7. 수집목적 또는 제공받은 목적이 달성된 경우 파기를 원칙으로 하고 있으나, 상법, 전자상거래등에서의 소비자보호에 관한 법률, 국세기본법 등 법령의 규정에 의하여 보존할 필요성이 있는 경우에는 귀하의 개인정보를 보유할 수 있습니다.<br/>회사의 개인정보 보유근거 및 기간은 다음과 같습니다.<br/>① 계약 또는 청약철회 등에 관한 기록: 5년<br/>② 대금결제 및 재화등의 공급에 관한 기록: 5년<br/>③ 소비자의 불만 또는 분쟁처리에 관한 기록: 3년<br/>④ 불건전한 서비스 이용에 따른 사법기관 수사의뢰 및 다른 회원을 보호하기 위해: 1년<br/>⑤ 계정 압류자: 2년<br/>⑥ 단순 가입·탈퇴를 반복하는 재가입 방지를 위해: 6개월<br/>⑦ 탈퇴시 멤버쉽 포인트 잔여분이 있는 경우 정산완료시까지 보유<h4 class="slp_cpation">제 8 조 손해배상</h4>1. 이용자는 다음 행위를 하여서는 안됩니다.<br/>① 신청 또는 변경 시 허위 내용의 등록<br/>② 타인의 정보 도용<br/>③ “회사”에 게시된 정보의 변경<br/>④ “회사”가 정한 정보 이외의 정보(컴퓨터 프로그램 등) 등의 송신 또는 게시<br/>⑤ “회사”의 이용권한, 기타 이용 계약상 지위를 타인에게 양도, 증여하는 행위<br/>⑥ “회사”의 이용 권한을 타인과 시간을 분할하여 사용하거나, 이를 담보로 제공하는 행위<br/>⑦ “회사” 기타 제3자의 저작권 등 지적재산권에 대한 침해<br/>⑧ “회사” 기타 제3자의 명예를 손상시키거나 업무를 방해하는 행위<br/>⑨ 외설 또는 폭력적인 메시지, 화상, 음성, 기타 공서양속에 반하는 정보를 몰에 공개 또는 게시하는 행위<h4 class="slp_cpation">제 9 조 연결“회사”와 피연결 “회사” 간의 관계</h4>1. 상위 “회사”와 하위 “회사”가 하이퍼링크(예: 하이퍼 링크의 대상에는 문자, 그림 및 동화상 등이 포함됨)방식 등으로 연결된 경우, 전자를 연결 “회사”(웹 사이트)라고 하고 후자를 피연결 “회사”(웹사이트)라고 합니다.<br/>2. 연결“회사”는 피연결“회사”가 독자적으로 제공하는 재화등에 의하여 이용자와 행하는 거래에 대해서 보증책임을 지지 않는다는 뜻을 연결“회사”의 초기화면 또는 연결되는 시점의 팝업화면으로 명시한 경우에는 그 거래에 대한 보증책임을 지지 않습니다.<h4 class="slp_cpation">제 10 조 저작권의 귀속 및 이용제한</h4>1. “회사 “가 작성한 저작물에 대한 저작권 기타 지적재산권은 ”회사“에 귀속합니다.<br/>2. 이용자는 “회사”를 이용함으로써 얻은 정보 중 “회사”에게 지적재산권이 귀속된 정보를 “회사”의 사전 승낙없이 복제, 송신, 출판, 배포, 방송 기타 방법에 의하여 영리목적으로 이용하거나 제3자에게 이용하게 하여서는 안됩니다.<br/>3. “회사”는 약정에 따라 이용자에게 귀속된 저작권을 사용하는 경우 당해 이용자에게 통보하여야 합니다.<h4 class="slp_cpation">제 11 조 분쟁해결</h4>1. “회사”는 이용자가 제기하는 정당한 의견이나 불만을 반영하고 그 피해를 보상처리하기 위하여 피해보상처리기구를 설치·운영합니다.<br/>2. “회사”는 이용자로부터 제출되는 불만사항 및 의견은 우선적으로 그 사항을 처리합니다. 다만, 신속한 처리가 곤란한 경우에는 이용자에게 그 사유와 처리일정을 즉시 통보해 드립니다<br/>3. “회사”와 이용자간에 발생한 전자상거래 분쟁과 관련하여 이용자의 피해구제신청이 있는 경우에는 공정거래위원회 또는 시·도지사가 의뢰하는 분쟁조정기관의 조정에 따를 수 있습니다.<h4 class="slp_cpation">제 12 조 재판권 및 준거법</h4>1. “회사”와 이용자간에 발생한 전자상거래 분쟁에 관한 소송은 제소 당시의 이용자의 주소에 의하고, 주소가 없는 경우에는 거소를 관할하는 지방법원의 전속관할로 합니다. 다만, 제소 당시 이용자의 주소 또는 거소가 분명하지 않거나 외국 거주자의 경우에는 민사소송법상의 관할법원에 제기합니다.<br/>2. “회사”와 이용자간에 제기된 전자상거래 소송에는 한국법을 적용합니다.
			</div>


	</div>

	

	<div style="text-align:center;display:none;" id="ConfirmBtn">
		<div style="display:inline-block;width:200px;height:50px; font-size:18px; line-height:50px;background-color:#777;color:#ffffff;cursor:pointer;border-radius:5px;margin-right:10px;" onclick="Agree(1)">동의</div>
		<div style="display:inline-block;width:200px;height:50px; font-size:18px; line-height:50px;background-color:#bbb;color:#ffffff;cursor:pointer;border-radius:5px;" onclick="Agree(0)">거부</div>
	</div>
</div>


<script>


	<? if($TotalRowCount==0) { ?>
		document.getElementById("ConfirmBtn").style.display = "";
		document.getElementById("ConfirmTitle").style.display = "";
		document.getElementById("ConfirmContent").style.display = "";
	<? } else {?>
		location.replace("http://<?=$DefaultDomain2?>/member_direction_slp_action.php?Param=<?=$Param?>");
	<? } ?>

	function Agree(v){
		if (v==1){
			location.replace("http://<?=$DefaultDomain2?>/member_direction_slp_action.php?Param=<?=$Param?>");
		}else{
			history.go(-1);
		}
	}


</script>

</body>
</html>
<?php
include_once('./includes/dbclose.php');
//header("Location: http://$DefaultDomain2/mypage_study_room.php");


?>