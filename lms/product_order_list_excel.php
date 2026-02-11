<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');


header( "Content-type: application/vnd.ms-excel; charset=utf-8");
header( "Content-Disposition: attachment; filename = BOOK_ORDER_LIST.xls" );
header( "Content-Description: PHP4 Generated Data" );
?>
<!doctype html>
<head>
<meta charset="utf-8">
</head>
<body>
<?php
$type = isset($_REQUEST["type"]) ? $_REQUEST["type"] : "";
if ($type==""){
	$type = "11";
}
$SearchState = $type;


$AddSqlWhere = "1=1";
$AddSqlWhere2 = "1=1";
$ListParam = "1=1";
$PaginationParam = "1=1";


$CurrentPage = isset($_REQUEST["CurrentPage"]) ? $_REQUEST["CurrentPage"] : "";
$PageListNum = isset($_REQUEST["PageListNum"]) ? $_REQUEST["PageListNum"] : "";
$SearchText = isset($_REQUEST["SearchText"]) ? $_REQUEST["SearchText"] : "";
$SearchFranchiseID = isset($_REQUEST["SearchFranchiseID"]) ? $_REQUEST["SearchFranchiseID"] : "";
$SearchCompanyID = isset($_REQUEST["SearchCompanyID"]) ? $_REQUEST["SearchCompanyID"] : "";
$SearchBranchGroupID = isset($_REQUEST["SearchBranchGroupID"]) ? $_REQUEST["SearchBranchGroupID"] : "";
$SearchBranchID = isset($_REQUEST["SearchBranchID"]) ? $_REQUEST["SearchBranchID"] : "";
$SearchCenterID = isset($_REQUEST["SearchCenterID"]) ? $_REQUEST["SearchCenterID"] : "";
$SearchProductSellerID = isset($_REQUEST["SearchProductSellerID"]) ? $_REQUEST["SearchProductSellerID"] : "";

//================== 서치폼 감추기 =================
$HideSearchCenterID = 0;
$HideSearchBranchID = 0;
$HideSearchBranchGroupID = 0;
$HideSearchCompanyID = 0;
$HideSearchFranchiseID = 0;

if ($_LINK_ADMIN_LEVEL_ID_==0 or $_LINK_ADMIN_LEVEL_ID_==1){//마스터
	//모두허용
}else if ($_LINK_ADMIN_LEVEL_ID_==3 or $_LINK_ADMIN_LEVEL_ID_==4){//프랜차이즈 관리자
	$SearchFranchiseID = $_LINK_ADMIN_FRANCHISE_ID_;
	
	$HideSearchFranchiseID = 1;
}else if ($_LINK_ADMIN_LEVEL_ID_==6 or $_LINK_ADMIN_LEVEL_ID_==7){//대표지사 관리자
	$SearchBranchGroupID = $_LINK_ADMIN_BRANCH_GROUP_ID_;
	$SearchCompanyID = $_LINK_ADMIN_COMPANY_ID_;
	$SearchFranchiseID = $_LINK_ADMIN_FRANCHISE_ID_;
	
	$HideSearchBranchGroupID = 1;
	$HideSearchCompanyID = 1;
	$HideSearchFranchiseID = 1;
}else if ($_LINK_ADMIN_LEVEL_ID_==9 or $_LINK_ADMIN_LEVEL_ID_==10){//지사 관리자
	$SearchBranchID = $_LINK_ADMIN_BRANCH_ID_;
	$SearchBranchGroupID = $_LINK_ADMIN_BRANCH_GROUP_ID_;
	$SearchCompanyID = $_LINK_ADMIN_COMPANY_ID_;
	$SearchFranchiseID = $_LINK_ADMIN_FRANCHISE_ID_;
	
	$HideSearchBranchID = 1;
	$HideSearchBranchGroupID = 1;
	$HideSearchCompanyID = 1;
	$HideSearchFranchiseID = 1;
}else if ($_LINK_ADMIN_LEVEL_ID_==12 or $_LINK_ADMIN_LEVEL_ID_==13){//대리점 관리자
	$SearchCenterID = $_LINK_ADMIN_CENTER_ID_;
	$SearchBranchID = $_LINK_ADMIN_BRANCH_ID_;
	$SearchBranchGroupID = $_LINK_ADMIN_BRANCH_GROUP_ID_;
	$SearchCompanyID = $_LINK_ADMIN_COMPANY_ID_;
	$SearchFranchiseID = $_LINK_ADMIN_FRANCHISE_ID_;

	$HideSearchCenterID = 1;
	$HideSearchBranchID = 1;
	$HideSearchBranchGroupID = 1;
	$HideSearchCompanyID = 1;
	$HideSearchFranchiseID = 1;
}else if ($_LINK_ADMIN_LEVEL_ID_==15){//강사 
	//접속불가
}
//================== 서치폼 감추기 =================





if (!$CurrentPage){
	$CurrentPage = 1;	
}
if (!$PageListNum){
	$PageListNum = 30;
}

if ($PageListNum!=""){
	$ListParam = $ListParam . "&PageListNum=" . $PageListNum;
}

		
if ($SearchState=="11"){//결제완료
	$AddSqlWhere = $AddSqlWhere . " and A.ProductOrderState=21 ";//결제완료
}else if ($SearchState=="21"){
	$AddSqlWhere = $AddSqlWhere . " and (A.ProductOrderShipState=21 and A.ProductOrderShipState=31) ";//발송완료
}else if ($SearchState=="31"){
	$AddSqlWhere = $AddSqlWhere . " and A.ProductOrderState=33 ";//취소완료
}

$AddSqlWhere = $AddSqlWhere . " and A.ProductOrderState>=11 ";
$AddSqlWhere = $AddSqlWhere . " and B.MemberState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and C.CenterState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and D.BranchState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and E.BranchGroupState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and F.CompanyState<>0 ";
$AddSqlWhere = $AddSqlWhere . " and G.FranchiseState<>0 ";

if ($SearchText!=""){
	$ListParam = $ListParam . "&SearchText=" . $SearchText;
	$AddSqlWhere = $AddSqlWhere . " and (B.MemberName like '%".$SearchText."%' or B.MemberLoginID like '%".$SearchText."%' or B.MemberNickName like '%".$SearchText."%') ";
}

if ($SearchFranchiseID!=""){
	$ListParam = $ListParam . "&SearchFranchiseID=" . $SearchFranchiseID;
	$AddSqlWhere = $AddSqlWhere . " and F.FranchiseID=$SearchFranchiseID ";
}

if ($SearchCompanyID!=""){
	$ListParam = $ListParam . "&SearchCompanyID=" . $SearchCompanyID;
	$AddSqlWhere = $AddSqlWhere . " and E.CompanyID=$SearchCompanyID ";
}

if ($SearchBranchGroupID!=""){
	$ListParam = $ListParam . "&SearchBranchGroupID=" . $SearchBranchGroupID;
	$AddSqlWhere = $AddSqlWhere . " and D.BranchGroupID=$SearchBranchGroupID ";
}

if ($SearchBranchID!=""){
	$ListParam = $ListParam . "&SearchBranchID=" . $SearchBranchID;
	$AddSqlWhere = $AddSqlWhere . " and C.BranchID=$SearchBranchID ";
}

if ($SearchCenterID!=""){
	$ListParam = $ListParam . "&SearchCenterID=" . $SearchCenterID;
	$AddSqlWhere = $AddSqlWhere . " and B.CenterID=$SearchCenterID ";
}

if ($SearchProductSellerID!=""){
	$ListParam = $ListParam . "&SearchProductSellerID=" . $SearchProductSellerID;
	$AddSqlWhere = $AddSqlWhere . " and A.ProductSellerID=$SearchProductSellerID ";
}

$ListParam = $ListParam . "&type=" . $type;


$PaginationParam = $ListParam;
if ($CurrentPage!=""){
	$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}

$ListParam = str_replace("&", "^^", $ListParam);



$Sql = "select 
				count(*) TotalRowCount 
		from ProductOrders A 
			inner join ProductSellers BB on A.ProductSellerID=BB.ProductSellerID 
			inner join Members B on A.MemberID=B.MemberID 
			inner join Centers C on B.CenterID=C.CenterID 
			inner join Branches D on C.BranchID=D.BranchID  
			inner join BranchGroups E on D.BranchGroupID=E.BranchGroupID 
			inner join Companies F on E.CompanyID=F.CompanyID 
			inner join Franchises G on F.FranchiseID=G.FranchiseID 
		where ".$AddSqlWhere." ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TotalRowCount = $Row["TotalRowCount"];

$TotalPageCount = ceil($TotalRowCount / $PageListNum);
$StartRowNum = $PageListNum * ($CurrentPage - 1 );


$Sql = "
		select 
			A.*,
			AES_DECRYPT(UNHEX(A.ReceivePhone1),:EncryptionKey) as DecReceivePhone1,
			B.MemberName,
			B.MemberLoginID, 
			B.MemberLevelID,
			C.CenterID as JoinCenterID,
			C.CenterName as JoinCenterName,
			D.BranchID as JoinBranchID,
			D.BranchName as JoinBranchName, 
			E.BranchGroupID as JoinBranchGroupID,
			E.BranchGroupName as JoinBranchGroupName,
			F.CompanyID as JoinCompanyID,
			F.CompanyName as JoinCompanyName,
			G.FranchiseName,
			K.MemberLevelName,
			(select sum(AA.ProductCount*AA.ProductPrice) from ProductOrderDetails AA where AA.ProductOrderID=A.ProductOrderID and AA.ProductOrderDetailState=1) as ProductOrderProductPrice,
			BB.ProductSellerName
		from ProductOrders A
			inner join ProductSellers BB on A.ProductSellerID=BB.ProductSellerID 
			inner join Members B on A.MemberID=B.MemberID 
			inner join Centers C on B.CenterID=C.CenterID 
			inner join Branches D on C.BranchID=D.BranchID 
			inner join BranchGroups E on D.BranchGroupID=E.BranchGroupID 
			inner join Companies F on E.CompanyID=F.CompanyID 
			inner join Franchises G on F.FranchiseID=G.FranchiseID 
			inner join MemberLevels K on B.MemberLevelID=K.MemberLevelID 
		where ".$AddSqlWhere." 
		order by A.ProductOrderID desc limit $StartRowNum, $PageListNum";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

?>




<table border="1">
<thead>
	<tr>
		<th>No</th>
		<th>구매일</th>
		<th>학생명</th>
		<th>아이디</th>
		<th>구분</th>
		<th>상품명</th>
		<th>상품가</th>
		<th>배송료</th>
		<th>결제금액</th>
		<th>결제상태</th>
		<th>배송상태</th>
		<th>결제일</th>
		<th>발송일</th>
		<th>취소일</th>

		<th>상품목록</th>

		<th>수취인</th>
		<th>전화번호</th>
		<th>이메일</th>
		<th>우편번호</th>
		<th>주소</th>
		<th>메모</th>
	</tr>
</thead>
<tbody>
	<?php
	$ListCount = 1;
	
	while($Row = $Stmt->fetch()) {

		$StrProducts = "";

		$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

		$res_msg = $Row["res_msg"];
		$tno = $Row["tno"];

		$ReceiveName = $Row["ReceiveName"];
		$ReceivePhone1 = $Row["DecReceivePhone1"];
		$ReceiveZipCode = $Row["ReceiveZipCode"];
		$ReceiveAddr1 = $Row["ReceiveAddr1"];
		$ReceiveAddr2 = $Row["ReceiveAddr2"];
		$ReceiveMemo = $Row["ReceiveMemo"];
		$ProductOrderEmail = $Row["ProductOrderEmail"];

		$ReceiveMemo = str_replace("\n", " ", $ReceiveMemo);

		$ProductOrderShipNumber = $Row["ProductOrderShipNumber"];
		$PayResultMsg = $Row["PayResultMsg"];
		$ProductOrderID = $Row["ProductOrderID"];

		$ProductOrderNumber = $Row["ProductOrderNumber"];
		$ProductOrderName = $Row["ProductOrderName"];
		$ProductOrderProductPrice = $Row["ProductOrderProductPrice"];
		$ProductOrderShipPrice = $Row["ProductOrderShipPrice"];
		
		$OrderPayPgFeeRatio = $Row["OrderPayPgFeeRatio"];
		$OrderPayPgFeePrice = $Row["OrderPayPgFeePrice"];
		$ProductOrderState = $Row["ProductOrderState"];
		$ProductOrderShipState = $Row["ProductOrderShipState"];
		$UseCashPaymentType = $Row["UseCashPaymentType"];

		$OrderDateTime = $Row["OrderDateTime"];
		$PaymentDateTime = $Row["PaymentDateTime"];
		$CancelRequestDateTime = $Row["CancelRequestDateTime"];
		$CancelDateTime = $Row["CancelDateTime"];
		$RefundRequestDateTime = $Row["RefundRequestDateTime"];
		$RefundDateTime = $Row["RefundDateTime"];
		$ProductOrderRegDateTime = $Row["ProductOrderRegDateTime"];
		$ProductOrderModiDateTime = $Row["ProductOrderModiDateTime"];
		$ShipDateTime = $Row["ShipDateTime"];

		$MemberName = $Row["MemberName"];
		$MemberLoginID = $Row["MemberLoginID"];
		
		$CenterID = $Row["JoinCenterID"];
		$CenterName = $Row["JoinCenterName"];
		$BranchID = $Row["JoinBranchID"];
		$BranchName = $Row["JoinBranchName"];
		$BranchGroupID = $Row["JoinBranchGroupID"];
		$BranchGroupName = $Row["JoinBranchGroupName"];
		$CompanyID = $Row["JoinCompanyID"];
		$CompanyName = $Row["JoinCompanyName"];
		$FranchiseName = $Row["FranchiseName"];

		$ProductSellerName = $Row["ProductSellerName"];

		$ProductOrderProductPriceWhthShip = $ProductOrderShipPrice + $ProductOrderProductPrice;

		$StrUseCashPaymentType="-";
		if ($UseCashPaymentType==1){
			$StrUseCashPaymentType="카드";
		}else if ($UseCashPaymentType==2){
			$StrUseCashPaymentType="실시간이체";
		}else if ($UseCashPaymentType==3){
			$StrUseCashPaymentType="가상계좌";
		}else if ($UseCashPaymentType==4){
			$StrUseCashPaymentType="계좌입금";
		}else if ($UseCashPaymentType==5){
			$StrUseCashPaymentType="오프라인";
		}else if ($UseCashPaymentType==9){
			$StrUseCashPaymentType="기타";
		}

		//0: 삭제  1:DB만생성   11: 미결제   21:결제완료  31:취소신청   33: 취소완료   41:환불신청    43:환불완료
		if ($ProductOrderState==1){
			$StrProductOrderState = "-";
		}else if ($ProductOrderState==11){
			$StrProductOrderState = "주문완료";
		}else if ($ProductOrderState==21){
			$StrProductOrderState = "결제완료";
		}else if ($ProductOrderState==31){
			$StrProductOrderState = "취소요청";
		}else if ($ProductOrderState==33){
			$StrProductOrderState = "취소완료";
		}else if ($ProductOrderState==41){
			$StrProductOrderState = "환불요청";
		}else if ($ProductOrderState==43){
			$StrProductOrderState = "환불완료";
		}


		//1: 주문접수 11:배송준비중 21:발송완료 31:수취확인
		$StrProductOrderShipState = "-";
		if ($ProductOrderShipState==1){
			$StrProductOrderShipState = "주문접수";
		}else if ($ProductOrderShipState==11){
			$StrProductOrderShipState = "배송준비중";
		}else if ($ProductOrderShipState==21){
			$StrProductOrderShipState = "발송완료";
		}else if ($ProductOrderShipState==31){
			$StrProductOrderShipState = "수취확인";
		}

		$StrOrderDateTime = substr($OrderDateTime, 0,10);
		$StrPaymentDateTime = substr($PaymentDateTime, 0,10);
		$StrCancelDateTime = substr($CancelDateTime, 0,10);
		$StrShipDateTime = substr($ShipDateTime, 0,10);


		$Sql2 = "select 
					A.*
				from ProductOrderDetails A 
				where A.ProductOrderID=:ProductOrderID 
					and A.ProductOrderDetailState=1 
				order by A.ProductOrderDetailID asc";

		$Stmt2 = $DbConn->prepare($Sql2);
		$Stmt2->bindParam(':ProductOrderID', $ProductOrderID);
		$Stmt2->execute();
		$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

		$ListCount2=1;
		$ProductPriceTotSum = 0;
		while($Row2 = $Stmt2->fetch()) {
			$ProductOrderDetailID = $Row2["ProductOrderDetailID"];
			$ProductID = $Row2["ProductID"];
			$ProductCount = $Row2["ProductCount"];
			$ProductName = $Row2["ProductName"];
			$ProductPrice = $Row2["ProductPrice"];

			if ($ListCount2>1){
				$StrProducts = $StrProducts . ", ";
			}
			
			$StrProducts = $StrProducts . $ProductName;
			

			$ListCount2++;
		}

	?>
	<tr>
		<td><?=$ListNumber?></td>
		<td><?=$StrOrderDateTime?></td>
		<td><?=$MemberName?></td>
		<td><?=$MemberLoginID?></td>
		<td><?=$ProductSellerName?></td>
		<td><?=$ProductOrderName?></td>
		<td><?=number_format($ProductOrderProductPrice,0)?></td>
		<td><?=number_format($ProductOrderShipPrice,0)?></td>
		<td><?=number_format($ProductOrderProductPriceWhthShip,0)?></td>
		<td><?=$StrProductOrderState?></td>
		<td><?=$StrProductOrderShipState?></td>
		<td><?=$StrPaymentDateTime?></td>
		<td>
			<?if ($ProductOrderShipState==21 || $ProductOrderShipState==31){?>
				<?=$StrShipDateTime?>
			<?}else{?>
				-
			<?}?>
		</td>
		<td>
			<?if ($ProductOrderState==33){?>
				<?=$StrCancelDateTime?>
			<?}else{?>
				-
			<?}?>
		</td>

		<td><?=$StrProducts?></td>


		<td><?=$ReceiveName?></td>
		<td style="mso-number-format:'\@'"><?=$ReceivePhone1?></td>
		<td><?=$ProductOrderEmail?></td>
		<td style="mso-number-format:'\@'"><?=$ReceiveZipCode?></td>
		<td><?=$ReceiveAddr1?> <?=$ReceiveAddr2?></td>
		<td><?=$ReceiveMemo?></td>
	</tr>
	<?php
		$ListCount ++;
	}
	$Stmt = null;
	?>
</tbody>
</table>

<?php
include_once('../includes/dbclose.php');
?>
</body>
</html>