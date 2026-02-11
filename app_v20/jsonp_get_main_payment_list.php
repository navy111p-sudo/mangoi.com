<?
header('Access-Control-Allow-Origin: *'); 
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');

$ErrNum = 0;
$ErrMsg = "성공";
$AppRegUID = isset($_REQUEST["AppRegUID"]) ? $_REQUEST["AppRegUID"] : "";
$AppID = isset($_REQUEST["AppID"]) ? $_REQUEST["AppID"] : "";
$AppDomain = isset($_REQUEST["AppDomain"]) ? $_REQUEST["AppDomain"] : "";
$AppPath = isset($_REQUEST["AppPath"]) ? $_REQUEST["AppPath"] : "";
$LocalLinkMemberID = isset($_REQUEST["LocalLinkMemberID"]) ? $_REQUEST["LocalLinkMemberID"] : "";
$callback = isset($_REQUEST["callback"]) ? preg_replace('/[^a-z0-9$_]/si', '', $_REQUEST["callback"]) : false;
$ServerPath = $AppDomain.$AppPath."/";


if ($LocalLinkMemberID==""){//비회원일경우 아무것도 보여주지 않는다.
	$LocalLinkMemberID = -1;
}

//=================================== 수강 등록  =======================
$Sql = "
	select 
		A.MemberPayType,
		B.CenterPayType,
		B.CenterRenewType,
		B.CenterStudyEndDate 
	from Members A 
		inner join Centers B on A.CenterID=B.CenterID 
	where A.MemberID=$LocalLinkMemberID and MemberLevelID=19 
";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$MemberPayType = $Row["MemberPayType"];
$CenterPayType = $Row["CenterPayType"];
$CenterRenewType = $Row["CenterRenewType"];
$CenterStudyEndDate = $Row["CenterStudyEndDate"];


if (!$CenterPayType){
	$CenterPayType = 0;
}



$Sql = "
		select 
				count(*) as TotalCount
		from ClassOrders A 
			inner join ClassProducts B on A.ClassProductID=B.ClassProductID 
		where A.MemberID=$LocalLinkMemberID and A.ClassProductID=1 and A.ClassProgress=11 and A.ClassOrderState >= 1  ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$TotalCount = $Row["TotalCount"];


$Sql = "select 
			A.*,
			B.ClassProductName
		from ClassOrders A 
			inner join ClassProducts B on A.ClassProductID=B.ClassProductID 
		where A.MemberID=$LocalLinkMemberID and A.ClassProductID=1 and A.ClassProgress=11 and A.ClassOrderState >= 1 
		order by A.ClassOrderRegDateTime desc";

$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);



$MainPaymentListHTML = "";
$MainPaymentListHTML .= "<div style=\"margin-top:5px;margin-bottom:5px;line-height:1.5;\">※ 종료일자는 정규 종료일자 입니다.<br>※ 연기/보강/변경 등은 종료일자 이후라도 수업이 진행됩니다.<br>※ 종료일자 미설정시 연장이 불가합니다.(문의요망)</div>";
$MainPaymentListHTML .= "<table class=\"mypage_point_table\">";
$MainPaymentListHTML .= "	<col width=\"\">";
$MainPaymentListHTML .= "	<col width=\"21%\">";
$MainPaymentListHTML .= "	<col width=\"21%\">";
$MainPaymentListHTML .= "	<col width=\"21%\">";
$MainPaymentListHTML .= "	<tr>";
$MainPaymentListHTML .= "		<th>신청일</th>";
$MainPaymentListHTML .= "		<th>시작일</th>";
$MainPaymentListHTML .= "		<th rowspan=\"2\">상태</th>";
$MainPaymentListHTML .= "		<th rowspan=\"2\">수강연장</th>";
$MainPaymentListHTML .= "	</tr>";
$MainPaymentListHTML .= "	<tr>";
$MainPaymentListHTML .= "		<th>수강명</th>";
$MainPaymentListHTML .= "		<th>종료일</th>";
$MainPaymentListHTML .= "	</tr>";



$ii=1;
while($Row = $Stmt->fetch()) {

	$ClassProductID = $Row["ClassProductID"];
	$ClassMemberType = $Row["ClassMemberType"]; 
	$ClassOrderTimeTypeID = $Row["ClassOrderTimeTypeID"];
	$ClassOrderWeekCountID = $Row["ClassOrderWeekCountID"];
	$ClassOrderStartDate = $Row["ClassOrderStartDate"];
	$ClassOrderEndDate = $Row["ClassOrderEndDate"];
	$ClassOrderID = $Row["ClassOrderID"];

	$ClassOrderState = $Row["ClassOrderState"];
	$ClassProgress = $Row["ClassProgress"];
	$ClassOrderRegDateTime = $Row["ClassOrderRegDateTime"];

	$ClassProductName = $Row["ClassProductName"];

	//-1:신청중 0:완전삭제 1:정상 2:종료대상 3:종료완료 4:장기홀드 5:레벨테스트완료 6:미응시
	if ($ClassOrderState==1){
		$StrClassOrderState = "수업진행";
	}else if ($ClassOrderState==2){
		$StrClassOrderState = "종료대상";
	}else if ($ClassOrderState==3){
		$StrClassOrderState = "종료";
	}else if ($ClassOrderState==4){
		$StrClassOrderState = "장기홀드";
	}

	if ($CenterPayType==1){//B2B결제
		if ($MemberPayType==0){
			$StrStudyAuthDate = $CenterStudyEndDate;
		}else{
			$StrStudyAuthDate = $ClassOrderEndDate;
		}
	}else{
		$StrStudyAuthDate = $ClassOrderEndDate;
	}

	$MainPaymentListHTML .= "	<tr>";
	$MainPaymentListHTML .= "		<td>".str_replace("-",".",substr($ClassOrderRegDateTime,0,10))."</td>";
	$MainPaymentListHTML .= "		<td>".str_replace("-",".",$ClassOrderStartDate)."</td>";
	$MainPaymentListHTML .= "		<td rowspan=\"2\" style=\"line-height:1.5;\">".str_replace("-",".",$StrClassOrderState)."</td>";
	if ($ClassOrderState==1 || $ClassOrderState==2) {
		
		//일단 연장하기 막기
		//if ($ClassOrderEndDate!=""){
		//	$MainPaymentListHTML .= "		<td rowspan=\"2\">-</td>";
		//}else{
			if ($CenterPayType==2){//1: B2B 결제, 2:B2C 결제
				if ($StrStudyAuthDate=="" || $StrStudyAuthDate=="0000-00-00"){
					$MainPaymentListHTML .= "		<td rowspan=\"2\">종료일미설정</td>";
				}else{
					$MainPaymentListHTML .= "		<td rowspan=\"2\" onclick=\"PayPreAction(".$ClassOrderID.")\">연장하기</td>";
				}
			}else{
				if ($MemberPayType==1){//1:개인결제 - B2B 학원결제일 경우 일겨우만 사용
					if ($StrStudyAuthDate=="" || $StrStudyAuthDate=="0000-00-00"){
						$MainPaymentListHTML .= "		<td rowspan=\"2\">종료일미설정</td>";
					}else{
						$MainPaymentListHTML .= "		<td rowspan=\"2\" onclick=\"PayPreAction(".$ClassOrderID.")\">연장하기</td>";
					}
				}else{
					$MainPaymentListHTML .= "		<td rowspan=\"2\" onclick=\"PayPreActionErr(".$ClassOrderID.")\">연장하기</td>";
				}
			}
		//}


		//일단 연장하기 막기
		//$MainPaymentListHTML .= "		<td rowspan=\"2\">-</td>";

	}else{
		$MainPaymentListHTML .= "		<td rowspan=\"2\">-</td>";
	}
	$MainPaymentListHTML .= "	</tr>";
	$MainPaymentListHTML .= "	<tr>";
	$MainPaymentListHTML .= "		<td>".$ClassProductName."</td>";
	if ($CenterPayType==1 && $CenterRenewType==2 && $MemberPayType==0){
		$MainPaymentListHTML .= "		<td>-</td>";
	}else{
		$MainPaymentListHTML .= "		<td>".str_replace("-",".",$StrStudyAuthDate)."</td>";
	}
	$MainPaymentListHTML .= "	</tr>";




	$ii++;
}
$Stmt = null;

if ($ii==1){
	$MainPaymentListHTML .= "	<tr>";
	$MainPaymentListHTML .= "		<td colspan='4'>수강신청 내역이 없습니다</td>";
	$MainPaymentListHTML .= "	</tr>";
}

$MainPaymentListHTML .= "</table>";
//=================================== 수강 등록  =======================




$Sql = "
		select 
			A.*,
			date_format(A.ClassOrderPayDateTime, '%Y.%m.%d') as ClassOrderPayDateTime
		from ClassOrderPays A

		where A.ClassOrderPayPaymentMemberID=:LocalLinkMemberID
				and A.ClassOrderPayProgress > 1
		order by A.ClassOrderPayDateTime desc";// limit $StartRowNum, $PageListNum";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':LocalLinkMemberID', $LocalLinkMemberID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);		



$MainPaymentListHTML .= "<table class=\"mypage_point_table\" style=\"margin-top:30px;\">";
$MainPaymentListHTML .= "	<col width=\"\">";
$MainPaymentListHTML .= "	<col width=\"21%\">";
$MainPaymentListHTML .= "	<col width=\"21%\">";
$MainPaymentListHTML .= "	<col width=\"21%\">";
$MainPaymentListHTML .= "	<tr>";
$MainPaymentListHTML .= "		<th>구매일자1</th>";
$MainPaymentListHTML .= "		<th>금액</th>";
$MainPaymentListHTML .= "		<th rowspan=\"2\">구매상태</th>";
$MainPaymentListHTML .= "		<th rowspan=\"2\">결제수단</th>";
$MainPaymentListHTML .= "	</tr>";
$MainPaymentListHTML .= "	<tr>";
$MainPaymentListHTML .= "		<th>이용권명</th>";
$MainPaymentListHTML .= "		<th>수량</th>";
$MainPaymentListHTML .= "	</tr>";


$ii=1;
while($Row = $Stmt->fetch()) {

	$ClassOrderPayPaymentPrice = $Row["ClassOrderPayPaymentPrice"];
	$ClassOrderPayDateTime = $Row["ClassOrderPayDateTime"];
	$ClassOrderPayProgress = $Row["ClassOrderPayProgress"];
	$ClassOrderPayUseCashPaymentType = $Row["ClassOrderPayUseCashPaymentType"];

	if ($ClassOrderPayProgress==1){//없음
		$StrClassOrderPayProgress = "DB등록";
	}else if ($ClassOrderPayProgress==11){
		$StrClassOrderPayProgress = "미결제";
	}else if ($ClassOrderPayProgress==21){
		$StrClassOrderPayProgress = "결제완료";
	}else if ($ClassOrderPayProgress==31){
		$StrClassOrderPayProgress = "취소요청";
	}else if ($ClassOrderPayProgress==33){
		$StrClassOrderPayProgress = "취소완료";
	}else if ($ClassOrderPayProgress==41){
		$StrClassOrderPayProgress = "환불요청";
	}else if ($ClassOrderPayProgress==43){
		$StrClassOrderPayProgress = "환불완료";
	}

	if ($ClassOrderPayUseCashPaymentType==1){
		$StrClassOrderPayUseCashPaymentType = "카드";
	}else if ($ClassOrderPayUseCashPaymentType==2){
		$StrClassOrderPayUseCashPaymentType = "실시간";
	}else if ($ClassOrderPayUseCashPaymentType==3){
		$StrClassOrderPayUseCashPaymentType = "가상계좌";
	}else if ($ClassOrderPayUseCashPaymentType==4){
		$StrClassOrderPayUseCashPaymentType = "계좌입금";
	}else if ($ClassOrderPayUseCashPaymentType==5){
		$StrClassOrderPayUseCashPaymentType = "오프라인";
	}else if ($ClassOrderPayUseCashPaymentType==6){
		$StrClassOrderPayUseCashPaymentType = "기타";
	}

	$MainPaymentListHTML .= "	<tr>";
	$MainPaymentListHTML .= "		<td>".$ClassOrderPayDateTime."</td>";
	$MainPaymentListHTML .= "		<td>".number_format($ClassOrderPayPaymentPrice,0)."원</td>";
	$MainPaymentListHTML .= "		<td rowspan=\"2\">".$StrClassOrderPayProgress."</td>";
	$MainPaymentListHTML .= "		<td rowspan=\"2\">".$StrClassOrderPayUseCashPaymentType."</td>";
	$MainPaymentListHTML .= "	</tr>";
	$MainPaymentListHTML .= "	<tr>";
	$MainPaymentListHTML .= "		<td>망고아이 수강등록</td>";
	$MainPaymentListHTML .= "		<td>-</td>";
	$MainPaymentListHTML .= "	</tr>";

	$ii++;
}
$Stmt = null;

if ($ii==1){
	$MainPaymentListHTML .= "	<tr>";
	$MainPaymentListHTML .= "		<td colspan='4'>결제내역이 없습니다</td>";
	$MainPaymentListHTML .= "	</tr>";
}




$MainPaymentListHTML .= "</table>";


$ArrValue["ErrNum"] = $ErrNum;
$ArrValue["ErrMsg"] = $ErrMsg;
$ArrValue["MainPaymentListHTML"] = $MainPaymentListHTML;


$ResultValue = my_json_encode($ArrValue);
//echo $_GET['callback'] . "(".(string)$ResultValue.")"; 
echo ($callback ? $callback . '(' : '') . $ResultValue . ($callback ? ')' : '');


function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}
include_once('../includes/dbclose.php');
?>