<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
include_once('./includes/password_hash.php');


$err_num = 0;
$err_msg = "";

$uid = isset($_REQUEST["uid"]) ? $_REQUEST["uid"] : "";
$page = isset($_REQUEST["page"]) ? $_REQUEST["page"] : "";


$Sql = "select MemberLoginID from Members where MemberID=:MemberID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberID', $uid);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$ApplyMemberLoginID = $Row["MemberLoginID"];


// 카카오 등 SNS 계정으로 로그인하면 Center 값이 비어 아래에 소속 체크가 문제가 생김
// 이를 방지하고자 로그인 타입을 체크
$Sql = "select MemberNickName, MemberLoginType, CenterID from Members where MemberLoginID=:ApplyMemberLoginID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ApplyMemberLoginID', $ApplyMemberLoginID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$MemberLoginType = $Row["MemberLoginType"];
$CenterID = $Row["CenterID"];
$MemberNickName = $Row["MemberNickName"];


//센터, 학생 은 독립 사이트 소속 체크
$Sql = "select 
			count(*) as TotalRowCount 
		from Members A 
			left outer join Centers B on A.CenterID=B.CenterID 
		where 
			
			A.MemberState=1 
			and A.MemberLoginID=:ApplyMemberLoginID 
			and (
					(B.OnlineSiteID=$OnlineSiteID and A.MemberLevelID>=12 and A.MemberLevelID<=19)
					or 
					(A.MemberLevelID<12)
				)
		";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ApplyMemberLoginID', $ApplyMemberLoginID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$TotalRowCount = $Row["TotalRowCount"];


if ($TotalRowCount==0){
	$MemberLoginPW = "";
	$err_num = 1;
	$err_msg = "아이디를 잘못 입력하셨습니다.";
}else{

	if ($page=="1"){
		header("Location: product_order_cart.php?FromDevice=mypage");
		exit;
	}else{
		echo $page;
		header("Location: product_order_list.php?FromDevice=mypage");
		exit;
	}
	

}


include_once('./includes/dbclose.php');

if ($err_num == 0){
?>
<script>
alert("잘못된 접근 입니다.");
parent.$.fn.colorbox.close();
</script>
<?
}

?>