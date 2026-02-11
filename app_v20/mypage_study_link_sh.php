<?
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Mangoi</title>
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" id="viewport" />
</head>
<body> 

<?php
$userid = isset($_REQUEST["userid"]) ? $_REQUEST["userid"] : "";
$username = isset($_REQUEST["username"]) ? $_REQUEST["username"] : "";
$usertype = isset($_REQUEST["usertype"]) ? $_REQUEST["usertype"] : "";
$confcode = isset($_REQUEST["confcode"]) ? $_REQUEST["confcode"] : "";
$version = isset($_REQUEST["version"]) ? $_REQUEST["version"] : "";


//================= ���� =====================
$Sql = "
		select 
				A.*
		from Members A 
		where A.MemberLoginID=:MemberLoginID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberLoginID', $userid);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$MemberLanguageID = $Row["MemberLanguageID"];

$ShLanguage = "ko";
if ($MemberLanguageID){
	if ($MemberLanguageID!=0){
		$ShLanguage = "en";
	}
}
//================= ���� =====================

if ($version=="" || $version=="null"){
	$version = 2;
}

// �л�, ���� �ڵ� ����
/*
$joinUserType = 11;
if($usertype==0) {
	$joinUserType = 12;
} else if($usertype==1) {
	$joinUserType = 11;
}
*/
$joinUserType = 21;
if($usertype==0) {
	$joinUserType = 22;
} else {
	$joinUserType = 21;
}


?>

<script src="/assets/js/jquery.bundle.js?ver=150"></script>
<script src="/js/mvapi.js"></script>
<!-- <script src="/js/mvapi.min.js"></script> -->

<div style="display:none;">
	
	<? if ($version==1) {?>
	<section>
		<form id="openJoinForm" data-mv-api="openJoin">
		<article>
			<div class="body">
				<div class="input-section">
					<input type="text" name="roomCode" value="<?=$confcode?>"> <!-- ��Ƽ�� �ڵ� -->
					<input type="text" name="template" value="1"> <!-- ���ø� ��ȣ -->
					<input type="text" name="title" value="������� ����"> <!-- ��Ƽ�� ���� -->
					<input type="text" name="openOption" value="0">
					<input type="text" name="joinUserType" value="<?=$joinUserType?>"> <!-- ���� ����� Ÿ�� -->
					<input type="text" name="userId" value="<?=$userid?>"> <!-- ����� ���̵� -->
					<input type="text" name="userName" value="<?=$username?>"> <!-- ����� �̸� -->
					<input type="text" name="roomOption" value=""> <!-- ��Ƽ�� �ɼ� -->
					<input type="text" name="extraMsg" value=""> <!-- Ȯ�� �޽��� -->
				</div>
			</div>
		</article>
		</form>
	</section>
	<?}else{?>
    <!-- <form name="ShClassForm" id="ShClassForm" action="http://180.150.230.195/sso/type1.do" method="POST"> -->
    <form name="ShClassForm" id="ShClassForm" action="https://www.mangoiclass.co.kr/sso/type1.do" method="POST">
        <input type="text" name="userid" value="<?=$userid?>" />
        <input type="text" name="username" value="<?=$username?>" />
        <input type="text" name="usertype" value="<?=$usertype?>" />
        <input type="text" name="remote" value="1" />
        <input type="text" name="confcode" value="<?=$confcode?>" />
        <input type="text" name="conftype" value="2" />
    </form>
	<?}?>
</div>

<script>
<? if ($version==2) {?>
	window.onload = function() {
		document.ShClassForm.submit();
	}
<?} else if($version==1) {?>
	window.onload = function() {

		MvApi.defaultSettings({
			/*
			debug: false,
			tcps: {key: 'MTgwLjE1MC4yMzAuMTk1OjcwMDE'},
			installPagePopup: "popup",
			company: {code: 2, authKey: '1605233653'},
			//web: {url: 'http://180.150.230.195:8080'},
			web: {url: 'https://www.mangoiclass.co.kr'},
			*/
			debug: false,
			// tcps: {key: 'MTgwLjE1MC4yMzAuMTk1OjcwMDE'},
			tcps: {key: 'MTIxLjE3MC4xNjQuMjMxOjcwMDE'},
			installPagePopup: "popup",
			company: {code: 2, authKey: '1577840400'},
			//web: {url: 'http://180.150.230.195:8080'},
			web: {url: 'https://www.mangoiclass.co.kr:8080'},
			
			// Ŭ���̾�Ʈ ���� ����
			client: {
				// ��ȣȭ ��� ���� - ��ȿ�� �˻縦 �������� �ʴ´�.
				encrypt: false,
				// Windows Client ����
				windows: {
					// ���α׷� �̸�
					product: 'BODA'
				}, 
				// Mobile Client ����
				mobile: {
					// ����� ���� ���. true: Store ����, false: �缳 ����
					store: false, 
					// ��Ŵ �̸�
					scheme: 'mangoi',
					// ��Ű�� �̸�
					packagename: 'zone.mangoi',
				},
				// Mac Client ���� - V7.3.0
				mac: {
					// ����� ���� ���. true: Store ����, false: �缳 ���� - V7.3.1
					store: false, 
					// ��Ŵ �̸�
					scheme: 'mangoi',
					// ��Ű�� �̸�
					packagename: 'zone.mangoi',
				},
				// ����� - ������ �ѱ���
				language: '<?=$ShLanguage?>',
				// �׸� - Ŭ���̾�Ʈ�� �׸� �ڵ� �� - v7.1.3
				theme: 3,
				// ��ư Ÿ�� - ��ư�� ǥ���ϴ� ��� - v7.1.3
				btnType: 1,
				// ���ø����̼� ��� - ȸ��,���� �� ���� ��� ���� - v7.1.4
				appMode: 2
			},
			

		});


		// ��� ���� ��ū Ŭ�� �� ó��
		$('form[data-mv-api]').submit(function(){
			var $this = $(this);
			var api = $this.data('mvApi');
			
			// ��û �޽��� ���� ����
			var requestMsg = {};
			var parameters = $this.serializeArray();
			$.each(parameters, function(index, parameter){
				requestMsg[parameter.name] = parameter.value;
			})
					
			// API ȣ��
			MvApi[api](
					// ��û�޽���
					requestMsg,
					// ���� callback
					function(){
						console.log('success.');
					},
					// ���� callback
					function(errorCode, reason){
						//console.error('error.', errorCode, reason);
						//alert('error :' + errorCode +" / "+ reason);
					}
			);
			return false;
		});

		$('form[data-mv-api]').submit();
	}
<?}?>



</script>

</body>
</html>
<?
include_once('../includes/dbclose.php');
?>

<!--
<?php
$userid = isset($_REQUEST["userid"]) ? $_REQUEST["userid"] : "";
$username = isset($_REQUEST["username"]) ? $_REQUEST["username"] : "";
$usertype = isset($_REQUEST["usertype"]) ? $_REQUEST["usertype"] : "";
$confcode = isset($_REQUEST["confcode"]) ? $_REQUEST["confcode"] : "";
?>


<div style="display:none;">
    <form name="ShClassForm" id="ShClassForm" action="https://www.mangoiclass.co.kr/sso/type1.do" method="POST">
        <input type="text" name="userid" value="<?=$userid?>" />
        <input type="text" name="username" value="<?=$username?>" />
        <input type="text" name="usertype" value="<?=$usertype?>" />
        <input type="text" name="remote" value="1" />
        <input type="text" name="confcode" value="<?=$confcode?>" />
        <input type="text" name="conftype" value="2" />
    </form>
</div>

<script>
window.onload = function() {
	document.ShClassForm.submit();
}
</script>
-->