<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');


if ($_LINK_ADMIN_LEVEL_ID_==15){
	header("Location: class_list.php?type=1"); 
	exit;
}else{
	//header("Location: center_list.php"); 
	//exit;
}


function sendAlimPhone($phone1, $phone2, $msg, $tmplId){
	if (substr($phone1,4,1) != "-" && substr($phone1,0,3) == "010") $SendPhone = $phone1;
		else $SendPhone = $phone2;
					
	echo $msg.$SendPhone.$tmplId.$phone1;
	if ($SendPhone!= "" && $SendPhone!= null) 
		SendAlimtalk($SendPhone, $msg, $tmplId);
}

$SearchStartDate = isset($_REQUEST["SearchStartDate"]) ? $_REQUEST["SearchStartDate"] : "";
$SearchEndDate = isset($_REQUEST["SearchEndDate"]) ? $_REQUEST["SearchEndDate"] : "";

if ($SearchStartDate==""){
	$SearchStartDate = date("Y-m-01");
}	
if ($SearchEndDate==""){
	$SearchEndDate = date("Y-m-").date("t",strtotime($SearchStartDate));
}

//=========================================================================================================================
// 역량 평가기간이 지났는지 확인 후 기간 지난 후에도 미평가자가 있으면 문자 보냄(평가기간이 끝나고 4일이 지났는지도 확인)
// 이 루틴은 하루에 한번만 정해진 시간(오전 10시~오후 4시 사이) 안에서만 실행 
$timeNow = strtotime(date('Y-m-d H:i:s'));
$timeTarget1 = strtotime(date('Y-m-d 10:00:00'));
$timeTarget2 = strtotime(date('Y-m-d 17:00:00'));
if ($timeNow >= $timeTarget1 && $timeNow <= $timeTarget2){
	$SqlCron = "SELECT COUNT(*) as CountIs, Hr_EvaluationID, 
						(SELECT COUNT(*) FROM CronWork 
							WHERE CronWorkName='EvaluationSMS' AND CronWorkDate = CURDATE() ) as CronCount 
					FROM Hr_Evaluations 
					WHERE DATE_ADD( Hr_EvaluationEndDate , INTERVAL 4 DAY) > NOW()  AND Hr_EvaluationEndDate < NOW() AND Hr_EvaluationState = 1";
	$StmtCron = $DbConn->prepare($SqlCron);
	$StmtCron->execute();
	$StmtCron->setFetchMode(PDO::FETCH_ASSOC);
	$RowCron = $StmtCron->fetch();
	// 평가기간이 지났지만 평가기간이 지난지 4일 미만인 게 있을 경우만 실행 그리고 오늘 문자를 안 보낸 경우만 실행
	if ($RowCron["CountIs"] > 0 && $RowCron["CronCount"] == 0){
		
		// CronWork 테이블을 업데이트하기
		$SqlCron = "INSERT INTO CronWork (CronWorkName, CronWorkDate) VALUES ('EvaluationSMS',NOW())";
		$StmtCron = $DbConn->prepare($SqlCron);
		$StmtCron->execute();

		// 먼저 역량평가를 아직 진행하지 않은 사람을 검색해서 그 사람의 정보를 가져오기
		$Sql ="SELECT DISTINCT B.MemberName,
					AES_DECRYPT(UNHEX(C.StaffPhone1),:EncryptionKey) as DecMemberPhone1,
					AES_DECRYPT(UNHEX(C.StaffPhone2),:EncryptionKey) as DecMemberPhone2
				FROM Hr_EvaluationCompetencyMembers  A
				LEFT JOIN Members B ON A.Hr_EvaluationCompetencyMemberID = B.MemberID
				LEFT JOIN Staffs C ON B.StaffID = C.StaffID 
				WHERE A.Hr_EvaluationID = :Hr_EvaluationID AND A.Hr_EvaluationCompetencyAddTotalPoint IS NULL";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
		$Stmt->bindParam(':Hr_EvaluationID', $RowCron["Hr_EvaluationID"]);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		
		// 해당 사람에게 문자 발송
		while($Row = $Stmt->fetch()){
			$MemberName = $Row["MemberName"];
			$DecMemberPhone1 = $Row["DecMemberPhone1"];
			$DecMemberPhone2 = $Row["DecMemberPhone2"];
			
			if (substr($DecMemberPhone1,4,1) != "-" && substr($DecMemberPhone1,0,3) == "010") $SendPhone = $DecMemberPhone1;
			else $SendPhone = $DecMemberPhone2;
			$msg = "$MemberName 님 역량평가를 완료하지 않았습니다. 완료해주세요";
			
			$tmplId="mangoi_011";  //카카오 알림톡 템플릿 아이디(문자 메시지를 바꿀려면 비즈엠에서 템플릿 등록하고 그 메시지를 위에 복사후 템플릿id도 바꿔야 함)
			echo $msg.$SendPhone.$tmplId.$DecMemberPhone1;
			if ($SendPhone!= "" && $SendPhone!= null) 
				SendAlimtalk($SendPhone, $msg, $tmplId);
		}


		// 업적평가를 아직 진행하지 않은 사람을 검색해서 그 사람의 정보를 가져오기
		$Sql = "SELECT MemberID FROM Hr_Staff_Target WHERE HrEvaUseYN = 'N' AND  Hr_EvaluationID = :Hr_EvaluationID";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':Hr_EvaluationID', $RowCron["Hr_EvaluationID"]);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);

		
		// 현재 있는 조직원들의 직무레벨과 레벨이름 가져오는 뷰
		$ViweTable = "SELECT  
						AAAA.*, BBBB.MemberName, 
						AES_DECRYPT(UNHEX(SSSS.StaffPhone1),:EncryptionKey) as DecMemberPhone1,
						AES_DECRYPT(UNHEX(SSSS.StaffPhone2),:EncryptionKey) as DecMemberPhone2
						from Hr_OrganLevelTaskMembers AAAA 
						inner join Members BBBB on AAAA.MemberID=BBBB.MemberID and BBBB.MemberState=1
						inner join Staffs SSSS on BBBB.StaffID = SSSS.StaffID";

		while($Row = $Stmt->fetch()){
			
			// 해당 조직원의 이름과 상사에 대한 정보를 가져온다.
			$Sql2 = "SELECT 
						A.*,

						AA.MemberID as T_MemberID,
						AA.Hr_OrganLevel as T_Hr_OrganLevel,
						AA.Hr_OrganPositionName as T_Hr_OrganPositionName,
						AA.DecMemberPhone1 as T_Phone1, 
						AA.DecMemberPhone2 as T_Phone2, 

						(select count(*) from ($ViweTable) VVVV where VVVV.Hr_OrganLevel<A.Hr_OrganLevel and (VVVV.Hr_OrganLevelID=C.Hr_OrganLevel1ID or VVVV.Hr_OrganLevelID=C.Hr_OrganLevel2ID or VVVV.Hr_OrganLevelID=C.Hr_OrganLevel3ID)) as T_BossCount

					from ($ViweTable) A 
						left outer join Hr_OrganLevels C on A.Hr_OrganLevelID=C.Hr_OrganLevelID 
						left outer join Hr_OrganTask2 D on A.Hr_OrganTask2ID=D.Hr_OrganTask2ID 
						left outer join Hr_OrganTask1 E on D.Hr_OrganTask1ID=E.Hr_OrganTask1ID 
						LEFT JOIN Staffs S ON A.StaffID = S.StaffID 

						left outer join ($ViweTable) AA on AA.Hr_OrganLevel<A.Hr_OrganLevel and (AA.Hr_OrganLevelID=C.Hr_OrganLevel1ID or AA.Hr_OrganLevelID=C.Hr_OrganLevel2ID or AA.Hr_OrganLevelID=C.Hr_OrganLevel3ID)
					WHERE A.MemberID = :MemberID 
			";

			$Stmt2 = $DbConn->prepare($Sql2);
			$Stmt2->bindParam(':MemberID', $RowCron["MemberID"]);
			$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
			$Stmt2->execute();
			$Stmt2->setFetchMode(PDO::FETCH_ASSOC);		
			$i = 0;
			//해당 사람의 최고 관리자, 레벨3 부서팀장에게 문자 발송
			while($Row2 = $Stmt2->fetch()){
				$MemberName = $Row2["MemberName"];
				$msg = $MemberName."님의 업적평가가 진행되지 않았습니다. 업적평가를 완료해 주세요.";
				$tmplId="mangoi_012";  //카카오 알림톡 템플릿 아이디(문자 메시지를 바꿀려면 비즈엠에서 템플릿 등록하고 그 메시지를 위에 복사후 템플릿id도 바꿔야 함)
				
				// 해당 사람에게 문자 발송 한번만
				if ($i == 0){
					$DecMemberPhone1 = $Row2["DecMemberPhone1"];
					$DecMemberPhone2 = $Row2["DecMemberPhone2"];
					
					sendAlimPhone($DecMemberPhone1,$DecMemberPhone2, $msg,$tmplId);
				}

				// 상사에게 문자 발송 
				$T_Phone1 = $Row2["T_Phone1"];
				$T_Phone2 = $Row2["T_Phone2"];

				sendAlimPhone($T_Phone1,$T_Phone2, $msg,$tmplId);
				
				$i++;	
			}

		}


		
		
		
	}

}
//======================================================================================================================

$ForceTaxMemberInfoPage = 0;//세금계산서 작성 페이지로 강제 이동
if ($_ADMIN_LEVEL_ID_==12 || $_ADMIN_LEVEL_ID_==13){

		$OrganType = 1;
		$OrganID = $_ADMIN_CENTER_ID_;
		
		$Sql_99 = "
				select 
						A.*
				from TaxMemberInfos A 
				where A.OrganType=:OrganType and A.OrganID=:OrganID";
		$Stmt_99 = $DbConn->prepare($Sql_99);
		$Stmt_99->bindParam(':OrganType', $OrganType);
		$Stmt_99->bindParam(':OrganID', $OrganID);
		$Stmt_99->execute();
		$Stmt_99->setFetchMode(PDO::FETCH_ASSOC);
		$Row_99 = $Stmt_99->fetch();
		$Stmt_99 = null;

		$TaxMemberInfoID = $Row_99["TaxMemberInfoID"];

		if (!$TaxMemberInfoID){
			$ForceTaxMemberInfoPage = 1;
		}

}



?>
<!DOCTYPE html>
<html lang="ko">
<head>
<?
include_once('./includes/common_meta_tag.php');
include_once('./inc_header.php');
?>
<?
include_once('./inc_common_list_css.php');
?>
<!-- ============== only this page css ============== -->
        <!-- weather icons -->
        <link rel="stylesheet" href="bower_components/weather-icons/css/weather-icons.min.css" media="all">
        <!-- metrics graphics (charts) -->
        <link rel="stylesheet" href="bower_components/metrics-graphics/dist/metricsgraphics.css">
        <!-- chartist -->
        <link rel="stylesheet" href="bower_components/chartist/dist/chartist.min.css">
<!-- ============== only this page css ============== -->
<!-- ============== common.css ============== -->
<link rel="stylesheet" type="text/css" href="./css/common.css" />
<!-- ============== common.css ============== -->

</head>

<body class="disable_transitions sidebar_main_open sidebar_main_swipe">
<?php
$MainMenuID = 0;
$SubMenuID = 0;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
?>



<div id="page_content">
	<div id="page_content_inner">

		<div class="uk-grid <?if ($_LINK_ADMIN_LEVEL_ID_==9 || $_LINK_ADMIN_LEVEL_ID_==10){?>uk-grid-width-large-1-5<?}else if ($_LINK_ADMIN_LEVEL_ID_==12 || $_LINK_ADMIN_LEVEL_ID_==13){?>uk-grid-width-large-1-3<?}else{?>uk-grid-width-large-1-4<?}?> uk-grid-width-medium-1-2 uk-grid-medium hierarchical_show" data-uk-grid-margin>
			<?
			$AddSqlWhere = " 1=1 "; 
			if ($_LINK_ADMIN_LEVEL_ID_==6 || $_LINK_ADMIN_LEVEL_ID_==7){//대표 지사
				$AddSqlWhere .= " and E.BranchGroupID=".$_LINK_ADMIN_BRANCH_GROUP_ID_." ";
			}else if ($_LINK_ADMIN_LEVEL_ID_==9 || $_LINK_ADMIN_LEVEL_ID_==10){//지사
				$AddSqlWhere .= " and D.BranchID=".$_LINK_ADMIN_BRANCH_ID_." ";
			}else if ($_ADMIN_LEVEL_ID_==12 || $_ADMIN_LEVEL_ID_==13){//대리점
				$AddSqlWhere .= " and C.CenterID=".$_LINK_ADMIN_CENTER_ID_." ";
			}else{//대표지사 이상
				
			}

			$AddSqlWhere = $AddSqlWhere . " and B.MemberState<>0 ";
			$AddSqlWhere = $AddSqlWhere . " and C.CenterState<>0 ";
			$AddSqlWhere = $AddSqlWhere . " and D.BranchState<>0 ";
			$AddSqlWhere = $AddSqlWhere . " and E.BranchGroupState<>0 ";
			$AddSqlWhere = $AddSqlWhere . " and F.CompanyState<>0 ";
			$AddSqlWhere = $AddSqlWhere . " and G.FranchiseState<>0 ";

			$AddSqlWhere2 = $AddSqlWhere;
			$AddSqlWhere2 = $AddSqlWhere2 . " and datediff(B.MemberRegDateTime, '".$SearchStartDate."')>=0 and datediff(B.MemberRegDateTime, '".$SearchEndDate."')<=0 ";

			?>
			<form name="SearchForm" method="get" style="width: 100%;" >
                <div class="lms_favorite_list" id="Idx_FvIcons_list">
				<?
				if( ($_LINK_ADMIN_LEVEL_ID_>=9 && $_LINK_ADMIN_LEVEL_ID_<=10) || ($_LINK_ADMIN_LEVEL_ID_>=12 && $_LINK_ADMIN_LEVEL_ID_<=13 ) ){
					$Sql_Favorite_Menu = "
						select
							*
						from FavoriteLmsMenus A
						where
							A.MemberID=:MemberID
							and
							A.FavoriteLmsMenuState=1
					";
					$Stmt_Favorite_Menu = $DbConn->prepare($Sql_Favorite_Menu);
					$Stmt_Favorite_Menu->bindParam(':MemberID', $_LINK_ADMIN_ID_);
					$Stmt_Favorite_Menu->execute();
					while($Row_Favorite_Menu = $Stmt_Favorite_Menu->fetch()) { 
						$FavoriteLmsMenuSubMenuID = $Row_Favorite_Menu["FavoriteLmsMenuSubMenuID"];
						$FavoriteLmsMenuName = $Row_Favorite_Menu["FavoriteLmsMenuName"];
						$FavoriteLmsMenuType = $Row_Favorite_Menu["FavoriteLmsMenuType"];
						$FavoriteLmsMenuUrl = $Row_Favorite_Menu["FavoriteLmsMenuUrl"];

						// 호출방식 차이
						if($FavoriteLmsMenuType==1) {
							// 페이지 이동
							$StrFavoriteLmsMenuType = $FavoriteLmsMenuUrl;
						} else if($FavoriteLmsMenuType==2) {
							// 함수
							$StrFavoriteLmsMenuType = $FavoriteLmsMenuUrl;
						}

						?>

							<a id="Idx_FvIcon_<?=$FavoriteLmsMenuSubMenuID?>" href="<?=$StrFavoriteLmsMenuType?>" name="Idx_FvIcons"><?=$FavoriteLmsMenuName?></a>

					<? }
				} ?>
                </div>

				<div class="uk-width-medium-4-10" style="padding-top:7px; display: inline-block; float: left; ">
					<label for="uk_dp_start"><?=$시작일[$LangID]?></label>
					<input type="text" id="SearchStartDate" name="SearchStartDate" value="<?=$SearchStartDate?>" class="md-input label-fixed" onchange="javascript:SearchSubmit();" data-uk-datepicker="{format:'YYYY-MM-DD', weekstart:0}">
				</div>
				<div class="uk-width-medium-4-10" style="padding-top:7px; display: inline-block; float: right;">
					<label for="uk_dp_end"><?=$종료일[$LangID]?></label>
					<input type="text" id="SearchEndDate" name="SearchEndDate" value="<?=$SearchEndDate?>" class="md-input label-fixed" onchange="javascript:SearchSubmit();" data-uk-datepicker="{format:'YYYY-MM-DD', weekstart:0}">
				</div>
			</form>
            
            <style>
                .lms_favorite_list{display:-webkit-box; display:-webkit-flex; display:-ms-flexbox; display:flex; flex-direction:row; flex-wrap:wrap;}
                .lms_favorite_list a{width:48%; margin:0 4% 4% 0; color:#333; text-align:center; line-height:42px; height:42px; box-shadow:0 1px 2px rgba(0,0,0,0.2); background-color:#fff; font-weight:700;}
                .lms_favorite_list a:nth-child(2n){margin-right:0;}
                @media all and (min-width:640px){
                    .lms_favorite_list a{width:31%; margin:0 3.5% 3.5% 0;}
                    .lms_favorite_list a:nth-child(2n){margin-right:3.5%;}
                    .lms_favorite_list a:nth-child(3n){margin-right:0;}
                }
                @media all and (min-width:768px){
                    .lms_favorite_list a{width:23.5%; margin:0 2% 2% 0;}
                    .lms_favorite_list a:nth-child(2n){margin-right:2%;}
                    .lms_favorite_list a:nth-child(3n){margin-right:2%;}
                    .lms_favorite_list a:nth-child(4n){margin-right:0;}
                }
                @media all and (min-width:1024px){
                    .lms_favorite_list a{width:18%; margin:0 2.5% 2.5% 0;}
                    .lms_favorite_list a:nth-child(2n){margin-right:2.5%;}
                    .lms_favorite_list a:nth-child(3n){margin-right:2.5%;}
                    .lms_favorite_list a:nth-child(4n){margin-right:2.5%;}
                    .lms_favorite_list a:nth-child(5n){margin-right:0;}
                }
                @media all and (min-width:1680px){
                    .lms_favorite_list a{width:15%; margin:0 2% 2% 0;}
                    .lms_favorite_list a:nth-child(2n){margin-right:2%;}
                    .lms_favorite_list a:nth-child(3n){margin-right:2%;}
                    .lms_favorite_list a:nth-child(4n){margin-right:2%;}
                    .lms_favorite_list a:nth-child(5n){margin-right:2%;}
                    .lms_favorite_list a:nth-child(6n){margin-right:0;}
                }
            </style>
			<?

			$Sql = "select 
						count(*) as SumStudentCount
				from Members B 
					inner join Centers C on B.CenterID=C.CenterID 
					inner join Branches D on C.BranchID=D.BranchID
					inner join BranchGroups E on D.BranchGroupID=E.BranchGroupID 
					inner join Companies F on E.CompanyID=F.CompanyID 
					inner join Franchises G on F.FranchiseID=G.FranchiseID 
				where ".$AddSqlWhere2." and B.MemberState=1 and B.MemberLevelID=19 ";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);
			$Row = $Stmt->fetch();
			$Stmt = null;
			$SumStudentCount = $Row["SumStudentCount"];
			?>
			<div>
				<div class="md-card">
					<div class="md-card-content">
						<div class="uk-float-right uk-margin-top uk-margin-small-right"><span class="peity_visitors peity_data">5,3,9,6,5,9,7</span></div>
						<span class="uk-text-muted uk-text-small"><?=$학생수[$LangID]?></span>
						<h2 class="uk-margin-remove"><span class="countUpMe">0<noscript><?=$SumStudentCount?></noscript></span></h2>
					</div>
				</div>
			</div>

			
			<?if ($_LINK_ADMIN_LEVEL_ID_<=10){?>

				<?
				$AddSqlWhere = " 1=1 "; 
				if ($_LINK_ADMIN_LEVEL_ID_==6 || $_LINK_ADMIN_LEVEL_ID_==7){//대표 지사
					$AddSqlWhere .= " and E.BranchGroupID=".$_LINK_ADMIN_BRANCH_GROUP_ID_." ";
				}else if ($_LINK_ADMIN_LEVEL_ID_==9 || $_LINK_ADMIN_LEVEL_ID_==10){//지사
					$AddSqlWhere .= " and D.BranchID=".$_LINK_ADMIN_BRANCH_ID_." ";
				}else if ($_ADMIN_LEVEL_ID_==12 || $_ADMIN_LEVEL_ID_==13){//대리점
					$AddSqlWhere .= " and C.CenterID=".$_LINK_ADMIN_CENTER_ID_." ";
				}else{//대표지사 이상
					
				}

				//$AddSqlWhere = $AddSqlWhere . " and B.MemberState<>0 ";
				//$AddSqlWhere = $AddSqlWhere . " and C.CenterState<>0 ";
				//$AddSqlWhere = $AddSqlWhere . " and D.BranchState<>0 ";
				//$AddSqlWhere = $AddSqlWhere . " and E.BranchGroupState<>0 ";
				//$AddSqlWhere = $AddSqlWhere . " and F.CompanyState<>0 ";
				//$AddSqlWhere = $AddSqlWhere . " and G.FranchiseState<>0 ";

				$AddSqlWhere2 = $AddSqlWhere;
				$AddSqlWhere2 = $AddSqlWhere2 . " and datediff(A.ClassOrderPayDateTime, '".$SearchStartDate."')>=0 and datediff(A.ClassOrderPayDateTime, '".$SearchEndDate."')<=0 ";

				//PG 수수료 제외로 계산
				$Sql = "select 
							sum(A.ClassOrderPayUseCashPrice-A.ClassOrderPayPgFeePrice-(A.ClassOrderPayUseCashPrice * A.ClassOrderPayPgFeeRatio / 100)) as SumClassOrderPayUseCashPrice,
							sum((A.ClassOrderPayUseCashPrice-A.ClassOrderPayPgFeePrice-(A.ClassOrderPayUseCashPrice * A.ClassOrderPayPgFeeRatio / 100)) * ( (A.CenterPricePerTime - A.CompanyPricePerTime) / A.CenterPricePerTime )) * 0.967 as SumClassOrderPayUseCashPriceCommission
					from ClassOrderPays A 
						inner join Members B on A.ClassOrderPayPaymentMemberID=B.MemberID 
						inner join Centers C on A.CenterID=C.CenterID 
						inner join Branches D on C.BranchID=D.BranchID
						inner join BranchGroups E on D.BranchGroupID=E.BranchGroupID 
						inner join Companies F on E.CompanyID=F.CompanyID 
						inner join Franchises G on F.FranchiseID=G.FranchiseID 
					where ".$AddSqlWhere2." and (A.ClassOrderPayProgress=21 or A.ClassOrderPayProgress=31 or A.ClassOrderPayProgress=41) ";
				$Stmt = $DbConn->prepare($Sql);
				$Stmt->execute();
				$Stmt->setFetchMode(PDO::FETCH_ASSOC);
				$Row = $Stmt->fetch();
				$Stmt = null;
				$SumClassOrderPayUseCashPrice = $Row["SumClassOrderPayUseCashPrice"];
				$SumClassOrderPayUseCashPriceCommission = $Row["SumClassOrderPayUseCashPriceCommission"];

				?>
				<div>
					<div class="md-card">
						<div class="md-card-content">
							<div class="uk-float-right uk-margin-top uk-margin-small-right"><span class="peity_sale2 peity_data">8,7,3,6,5,7,4,9,6,9</span></div>
							<span class="uk-text-muted uk-text-small"><?=$매출[$LangID]?>(<?=$수수료제외[$LangID]?>)</span>
							<h2 class="uk-margin-remove">￦<span class="countUpMe">0<noscript><?=$SumClassOrderPayUseCashPrice?></noscript></span></h2>
						</div>
					</div>
				</div>

				<?if ($_LINK_ADMIN_LEVEL_ID_==9 || $_LINK_ADMIN_LEVEL_ID_==10){?>
				<div>
					<div class="md-card">
						<div class="md-card-content">
							<div class="uk-float-right uk-margin-top uk-margin-small-right"><span class="peity_sale peity_data">5,3,9,6,5,9,7,3,5,9</span></div>
							<span class="uk-text-muted uk-text-small"><?=$커미션[$LangID]?>(<?=$수수료제외[$LangID]?>)</span>
							<h2 class="uk-margin-remove">￦<span class="countUpMe">0<noscript><?=round($SumClassOrderPayUseCashPriceCommission,0)?></noscript></span></h2>
						</div>
					</div>
				</div>
				<?}?>

			<?}?>


			<?
			$TargetDate = date("Y-m-d");
			
			$AddSqlWhere = " 1=1 "; 
			if ($_LINK_ADMIN_LEVEL_ID_==6 || $_LINK_ADMIN_LEVEL_ID_==7){//대표 지사
				$AddSqlWhere .= " and E.BranchGroupID=".$_LINK_ADMIN_BRANCH_GROUP_ID_." ";
			}else if ($_LINK_ADMIN_LEVEL_ID_==9 || $_LINK_ADMIN_LEVEL_ID_==10){//지사
				$AddSqlWhere .= " and D.BranchID=".$_LINK_ADMIN_BRANCH_ID_." ";
			}else if ($_ADMIN_LEVEL_ID_==12 || $_ADMIN_LEVEL_ID_==13){//대리점
				$AddSqlWhere .= " and C.CenterID=".$_LINK_ADMIN_CENTER_ID_." ";
			}else{//대표지사 이상
				
			}

			$AddSqlWhere = $AddSqlWhere . " and B.MemberState<>0 ";
			$AddSqlWhere = $AddSqlWhere . " and C.CenterState<>0 ";
			$AddSqlWhere = $AddSqlWhere . " and D.BranchState<>0 ";
			$AddSqlWhere = $AddSqlWhere . " and E.BranchGroupState<>0 ";
			$AddSqlWhere = $AddSqlWhere . " and F.CompanyState<>0 ";
			$AddSqlWhere = $AddSqlWhere . " and G.FranchiseState<>0 ";

			//$AddSqlWhere2 = $AddSqlWhere;
			//$AddSqlWhere2 = $AddSqlWhere2 . " and datediff(A.ClassOrderPayDateTime, '".$SearchStartDate."')>=0 and datediff(A.ClassOrderPayDateTime, '".$SearchEndDate."')<=0 ";


			$ViewTable = "select
				C.CenterName,
				B.MemberLoginID,
				B.MemberName,
				(select count(*) from Classes AA where AA.ClassAttendState<>99 and AA.MemberID=B.MemberID and datediff(AA.StartDateTime , '".$SearchStartDate."')>=0 and datediff(AA.EndDateTime , '".$SearchEndDate."')<=0 ) AS 전체등록,
				(select count(*) from Classes AA where AA.ClassState=2 and AA.ClassAttendState<>99 and AA.MemberID=B.MemberID and  datediff(AA.StartDateTime , '".$SearchStartDate."')>=0 and datediff(AA.EndDateTime , '".$SearchEndDate."')<=0 ) AS 전체학습,
				(select count(*) from Classes AA inner join ClassOrders BB ON AA.ClassOrderID=BB.ClassOrderID where AA.ClassState=2 and AA.ClassAttendState<>99 and AA.MemberID=B.MemberID and BB.ClassProductID=1 and  datediff(AA.StartDateTime , '".$SearchStartDate."')>=0 and datediff(AA.EndDateTime , '".$SearchEndDate."')<=0 ) AS 정규수업,
				(select count(*) from Classes AA inner join ClassOrders BB ON AA.ClassOrderID=BB.ClassOrderID where AA.ClassState=2 and AA.ClassAttendState<>99 and AA.MemberID=B.MemberID and BB.ClassProductID=3 and  datediff(AA.StartDateTime , '".$SearchStartDate."')>=0 and datediff(AA.EndDateTime , '".$SearchEndDate."')<=0 ) AS 체험수업,
				(select count(*) from Classes AA inner join ClassOrders BB ON AA.ClassOrderID=BB.ClassOrderID where AA.ClassState=2 and AA.ClassAttendState<>99 and AA.MemberID=B.MemberID and BB.ClassProductID=2 and  datediff(AA.StartDateTime , '".$SearchStartDate."')>=0 and datediff(AA.EndDateTime , '".$SearchEndDate."')<=0 ) AS 레벨테스트,
				(select count(*) from Classes AA where AA.ClassState=2 and AA.MemberID=B.MemberID and AA.ClassAttendState<>99 and (AA.ClassAttendState=1 or AA.ClassAttendState=2) and  datediff(AA.StartDateTime , '".$SearchStartDate."')>=0 and datediff(AA.EndDateTime , '".$SearchEndDate."')<=0 ) AS 출석,
				(select count(*) from Classes AA where AA.ClassState=2 and AA.MemberID=B.MemberID and AA.ClassAttendState<>99 and AA.ClassAttendState=3 and  datediff(AA.StartDateTime , '".$SearchStartDate."')>=0 and datediff(AA.EndDateTime , '".$SearchEndDate."')<=0 ) AS 결석,
				(select count(*) from Classes AA where AA.MemberID=B.MemberID and AA.ClassAttendState<>99 and AA.ClassAttendState>=4 and AA.ClassAttendState<=5 and  datediff(AA.StartDateTime , '".$SearchStartDate."')>=0 and datediff(AA.EndDateTime , '".$SearchEndDate."')<=0 ) AS 연기,
				(select count(*) from Classes AA where AA.MemberID=B.MemberID and AA.ClassAttendState<>99 and AA.ClassAttendState>=6 and AA.ClassAttendState<=7 and  datediff(AA.StartDateTime , '".$SearchStartDate."')>=0 and datediff(AA.EndDateTime , '".$SearchEndDate."')<=0 ) AS 취소,
				(select count(*) from Classes AA where AA.MemberID=B.MemberID and AA.ClassAttendState<>99 and AA.ClassAttendState=8 and  datediff(AA.StartDateTime , '".$SearchStartDate."')>=0 and datediff(AA.EndDateTime , '".$SearchEndDate."')<=0 ) AS 변경
			
			from Members B

					inner join Centers C on B.CenterID=C.CenterID 
					inner join Branches D on C.BranchID=D.BranchID
					inner join BranchGroups E on D.BranchGroupID=E.BranchGroupID 
					inner join Companies F on E.CompanyID=F.CompanyID 
					inner join Franchises G on F.FranchiseID=G.FranchiseID 

			where ".$AddSqlWhere." ";


			$Sql = "
					select 
						sum(V.출석) AS 출석합계,
						sum(V.결석) AS 결석합계,
						100 * sum(V.출석) / (sum(V.출석) + sum(V.결석)) AS AttendRatio 
					from ($ViewTable) V
			";

			$Stmt = $DbConn->prepare($Sql);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);
			$Row = $Stmt->fetch();
			$Stmt = null;

			$AttendRatio = $Row["AttendRatio"];

			?>

			<div>
				<div class="md-card">
					<div class="md-card-content">
						<div class="uk-float-right uk-margin-top uk-margin-small-right"><span class="peity_visitors2 peity_data"><?=round($AttendRatio,0)?>/100</span></div>
						<span class="uk-text-muted uk-text-small"><?=$출석율[$LangID]?></span>
						<h2 class="uk-margin-remove"><span class="countUpMe">0<noscript><?=round($AttendRatio,0)?></noscript></span>%</h2>
					</div>
				</div>
			</div>


			<? 

			$SelectYear = date("Y");
			$SelectMonth = date("m");
			$SelectDay = date("d");

			$SelectDate = $SelectYear."-".$SelectMonth."-".$SelectDay;
			$SelectDateWeek = date('w', strtotime($SelectDate));

			$AddSqlWhere = " 1=1 ";
			$AddSqlWhere = $AddSqlWhere . " and MB.MemberState<>0 ";
			$AddSqlWhere = $AddSqlWhere . " and CT.CenterState<>0 ";
			$AddSqlWhere = $AddSqlWhere . " and BR.BranchState<>0 ";
			$AddSqlWhere = $AddSqlWhere . " and BRG.BranchGroupState<>0 ";
			$AddSqlWhere = $AddSqlWhere . " and COM.CompanyState<>0 ";
			$AddSqlWhere = $AddSqlWhere . " and FR.FranchiseState<>0 ";



			if ($_LINK_ADMIN_LEVEL_ID_==6 || $_LINK_ADMIN_LEVEL_ID_==7){
				$AddSqlWhere = $AddSqlWhere . " and BR.BranchGroupID=".$_LINK_ADMIN_BRANCH_GROUP_ID_;
			}

			if ($_LINK_ADMIN_LEVEL_ID_==9 || $_LINK_ADMIN_LEVEL_ID_==10){
				$AddSqlWhere = $AddSqlWhere . " and CT.BranchID=".$_LINK_ADMIN_BRANCH_ID_;
			}

			if ($_LINK_ADMIN_LEVEL_ID_==12 || $_LINK_ADMIN_LEVEL_ID_==13){
				$AddSqlWhere = $AddSqlWhere . " and MB.CenterID=".$_LINK_ADMIN_CENTER_ID_;
			}

			if ($_LINK_ADMIN_LEVEL_ID_==15){
				$AddSqlWhere = $AddSqlWhere . " and TEA.TeacherID=".$_LINK_ADMIN_TEACHER_ID_;
			}


			$AddSqlWhere = $AddSqlWhere . " and TEA.TeacherState=1 ";


			$AddSqlWhere = $AddSqlWhere . " and COS.ClassOrderSlotMaster=1 ";
			$AddSqlWhere = $AddSqlWhere . " and ( 
													(COS.ClassOrderSlotType=1 and COS.StudyTimeWeek=".$SelectDateWeek." and COS.ClassOrderSlotStartDate is NULL and COS.ClassOrderSlotEndDate is NULL and datediff(CO.ClassOrderStartDate, '".$SelectDate."')<=0 ) 

													or 
													(COS.ClassOrderSlotType=1 and COS.StudyTimeWeek=".$SelectDateWeek." and datediff(COS.ClassOrderSlotStartDate, '".$SelectDate."')<=0 and COS.ClassOrderSlotEndDate is NULL and datediff(CO.ClassOrderStartDate, '".$SelectDate."')<=0 ) 

													or 
													(COS.ClassOrderSlotType=1 and COS.StudyTimeWeek=".$SelectDateWeek." and COS.ClassOrderSlotStartDate is NULL and datediff(COS.ClassOrderSlotEndDate, '".$SelectDate."')>=0 and datediff(CO.ClassOrderStartDate, '".$SelectDate."')<=0 ) 

													or 
													(COS.ClassOrderSlotType=1 and COS.StudyTimeWeek=".$SelectDateWeek." and datediff(COS.ClassOrderSlotStartDate, '".$SelectDate."')<=0 and datediff(COS.ClassOrderSlotEndDate, '".$SelectDate."')>=0 and datediff(CO.ClassOrderStartDate, '".$SelectDate."')<=0 ) 

													or 
													(COS.ClassOrderSlotType=2 and datediff(COS.ClassOrderSlotDate, '".$SelectDate."')=0 )   
												)  
											";
			$AddSqlWhere = $AddSqlWhere . " and COS.ClassOrderSlotState=1 ";

			$AddSqlWhere = $AddSqlWhere . " and CO.ClassProgress=11 ";
			$AddSqlWhere = $AddSqlWhere . " and (CO.ClassOrderState=1 or CO.ClassOrderState=2 or CO.ClassOrderState=3 or CO.ClassOrderState=5 or CO.ClassOrderState=6) ";



			$ViewTable = "

				select 
					COS.ClassMemberType,
					COS.ClassOrderSlotType,
					COS.ClassOrderSlotDate,
					COS.TeacherID,
					COS.ClassOrderSlotMaster,
					COS.StudyTimeWeek,
					COS.StudyTimeHour,
					COS.StudyTimeMinute,
					COS.ClassOrderSlotState,
					concat(lpad(COS.StudyTimeHour,2,0) ,':', lpad(COS.StudyTimeMinute,2,0)) as ClassStartTime, 

					CO.ClassOrderID,
					CO.ClassProductID,
					CO.ClassOrderTimeTypeID,
					CO.MemberID,
					CO.ClassOrderStartDate,
					CO.ClassOrderEndDate,
					CO.ClassOrderState,

					ifnull(CLS.ClassID,0) as ClassID,
					CLS.TeacherID as ClassTeacherID,
					CLS.ClassLinkType,
					CLS.StartDateTime,
					CLS.StartDateTimeStamp,
					CLS.StartYear,
					CLS.StartMonth,
					CLS.StartDay,
					CLS.StartHour,
					CLS.StartMinute,
					CLS.EndDateTime,
					CLS.EndDateTimeStamp,
					CLS.EndYear,
					CLS.EndMonth,
					CLS.EndDay,
					CLS.EndHour,
					CLS.EndMinute,
					CLS.CommonUseClassIn,
					CLS.CommonShClassCode,
					CLS.CommonCiCourseID,
					CLS.CommonCiClassID,
					CLS.CommonCiTelephoneTeacher,
					CLS.CommonCiTelephoneStudent,
					ifnull(CLS.ClassAttendState,-1) as ClassAttendState,
					CLS.ClassAttendStateMemberID,
					ifnull(CLS.ClassState, 0) as ClassState,
					CLS.BookVideoID,
					CLS.BookQuizID,
					CLS.BookScanID,
					CLS.ClassRegDateTime,
					CLS.ClassModiDateTime,

					MB.MemberName,
					MB.MemberPayType,
					MB.MemberNickName,
					MB.MemberLoginID, 
					MB.MemberLevelID,
					MB.MemberCiTelephone,

					TEA.TeacherName,
					MB2.MemberLoginID as TeacherLoginID, 
					MB2.MemberCiTelephone as TeacherCiTelephone,
					CT.CenterID as JoinCenterID,
					CT.CenterName as JoinCenterName,
					CT.CenterPayType,
					CT.CenterRenewType,
					CT.CenterStudyEndDate,

					BR.BranchID as JoinBranchID,
					BR.BranchName as JoinBranchName, 
					BRG.BranchGroupID as JoinBranchGroupID,
					BRG.BranchGroupName as JoinBranchGroupName,
					COM.CompanyID as JoinCompanyID,
					COM.CompanyName as JoinCompanyName,
					FR.FranchiseName,
					MB3.MemberLoginID as CenterLoginID,
					TEA2.TeacherName as ClassTeacherName

				from ClassOrderSlots COS 

						left outer join Classes CLS on COS.ClassOrderID=CLS.ClassOrderID and CLS.StartYear=".$SelectYear." and CLS.StartMonth=".$SelectMonth." and CLS.StartDay=".$SelectDay." and CLS.StartHour=COS.StudyTimeHour and CLS.StartMinute=COS.StudyTimeMinute and CLS.TeacherID=COS.TeacherID and CLS.ClassAttendState<>99 

						inner join ClassOrders CO on COS.ClassOrderID=CO.ClassOrderID 



						inner join Members MB on CO.MemberID=MB.MemberID 
						inner join Centers CT on MB.CenterID=CT.CenterID 
						inner join Branches BR on CT.BranchID=BR.BranchID 
						inner join BranchGroups BRG on BR.BranchGroupID=BRG.BranchGroupID 
						inner join Companies COM on BRG.CompanyID=COM.CompanyID 
						inner join Franchises FR on COM.FranchiseID=FR.FranchiseID 
						inner join Teachers TEA on COS.TeacherID=TEA.TeacherID 
						left outer join Teachers TEA2 on CLS.TeacherID=TEA2.TeacherID 
						inner join Members MB2 on TEA.TeacherID=MB2.TeacherID 
						left outer join Members MB3 on CT.CenterID=MB3.CenterID and MB3.MemberLevelID=12 

				where ".$AddSqlWhere." ";

						//결제 안해도 관리자가 등록한 수업 진행가능하도록 주석 처리
						//inner join ClassOrderPayDetails COPD on COS.ClassOrderID=COPD.ClassOrderID 
						//inner join ClassOrderPays COP on COPD.ClassOrderPayID=COP.ClassOrderPayID and (COP.ClassOrderPayProgress=21 or COP.ClassOrderPayProgress=31 or COP.ClassOrderPayProgress=41)


			$SqlWhereCenterRenew = "";
			if ($NoIgnoreCenterRenew==1){
				$SqlWhereCenterRenew = " and datediff(V.ClassOrderEndDate, '".$SelectDate."')>=0 ";
			}

			$Sql = "select count(*) TotalRowCount from 
						(select 
								count(*) 
						from ($ViewTable) V 

						where 
							(
								V.CenterPayType=1 and V.CenterRenewType=1 
								".$SqlWhereCenterRenew." 
								and V.MemberPayType=0 
								and (
										(V.ClassOrderState=1 or V.ClassOrderState=2 or V.ClassOrderState=5 or V.ClassOrderState=6) 
										or 
										(V.ClassOrderState=3 and datediff(V.ClassOrderEndDate, '".$SelectDate."')>=0) 
									)
							) 
							or 
							(
								V.CenterPayType=1 and V.CenterRenewType=2 
								and V.MemberPayType=0 
								and (
										(V.ClassOrderState=1 or V.ClassOrderState=2 or V.ClassOrderState=5 or V.ClassOrderState=6) 
										or 
										(V.ClassOrderState=3 and datediff(V.ClassOrderEndDate, '".$SelectDate."')>=0) 
									)
							)
							or 
							( 
								( V.CenterPayType=2 and datediff(V.ClassOrderEndDate, '".$SelectDate."')>=0 ) 
								or 
								( V.CenterPayType=1 and V.MemberPayType=1 and datediff(V.ClassOrderEndDate, '".$SelectDate."')>=0 ) 
							)
							or
							V.ClassProductID=2 
							or 
							V.ClassProductID=3  
							or 
							(V.ClassOrderSlotType=2 and datediff(V.ClassOrderSlotDate, '".$SelectDate."')=0) 
						group by V.ClassMemberType, V.ClassOrderSlotType, V.ClassOrderSlotDate, V.TeacherID, V.StudyTimeWeek, V.StudyTimeHour, V.StudyTimeMinute
						) VV
					";


			$Stmt = $DbConn->prepare($Sql);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);
			$Row = $Stmt->fetch();
			$Stmt = null;

			$TotalClassCount = $Row["TotalRowCount"];

			?>
			<div>
				<div class="md-card">
					<div class="md-card-content">
						<div class="uk-float-right uk-margin-top uk-margin-small-right"><span class="peity_visitors3 peity_data">3,6,2,1,4,8,7</span></div>
						<span class="uk-text-muted uk-text-small"><?=$오늘수업[$LangID]?></span>
						<h2 class="uk-margin-remove"><span class="countUpMe">0<noscript><?=$TotalClassCount?></noscript></span></h2>
					</div>
				</div>
			</div>



<!--		</div>-->
            <!--
            <div class="uk-grid" data-uk-grid-margin data-uk-grid-match="{target:'.md-card-content'}">
                <?php if ($_LINK_ADMIN_LEVEL_ID_<=10) { ?>
                <div class="uk-width-medium-1-2">
                    <h3 class="md-card-toolbar-heading-text">
                        <?php echo $지사_매출_순위_수수료제외[$LangID]; ?>
                    </h3>
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-overflow-container" style="height:440px;overflow-y:auto;">
                                <table class="uk-table">
                                    <thead>
                                        <tr>
                                            <th class="uk-text-nowrap"><?php echo $지사명[$LangID]; ?></th>
                                            <th class="uk-text-nowrap"><?php echo $매출[$LangID]; ?></th>
                                            <th class="uk-text-nowrap"><?php echo $차트[$LangID]; ?></th>
                                        </tr>
                                    </thead>

                                    <?php
                $AddSqlWhere = " 1=1 ";
                $AddSqlWhere2 = $AddSqlWhere;
                $AddSqlWhere2 = $AddSqlWhere2 . " and datediff(A.ClassOrderPayDateTime, '".$SearchStartDate."')>=0 and datediff(A.ClassOrderPayDateTime, '".$SearchEndDate."')<=0 ";

                $ViewTable = "select 
                                                D.BranchID, 
                                                sum(A.ClassOrderPayUseCashPrice-A.ClassOrderPayPgFeePrice-(A.ClassOrderPayUseCashPrice * ClassOrderPayPgFeeRatio / 100)) as SumClassOrderPayUseCashPrice
                                        from ClassOrderPays A 
                                            inner join Members B on ClassOrderPayPaymentMemberID=B.MemberID 
                                            inner join Centers C on A.CenterID=C.CenterID 
                                            inner join Branches D on C.BranchID=D.BranchID
                                            inner join BranchGroups E on D.BranchGroupID=E.BranchGroupID 
                                            inner join Companies F on E.CompanyID=F.CompanyID 
                                            inner join Franchises G on F.FranchiseID=G.FranchiseID 
                                        where ".$AddSqlWhere2." and (A.ClassOrderPayProgress=21 or A.ClassOrderPayProgress=31 or A.ClassOrderPayProgress=41) 
                                        group by D.BranchID 
                                        ";

                $Sql = "select 
                                                AA.BranchID,
                                                AA.SumClassOrderPayUseCashPrice,
                                                BB.BranchName
                                            from ($ViewTable) AA 
                                                inner join Branches BB on AA.BranchID=BB.BranchID
                                            order by AA.SumClassOrderPayUseCashPrice desc 
                                             
                                            ";
                $Stmt = $DbConn->prepare($Sql);
                $Stmt->execute();
                $Stmt->setFetchMode(PDO::FETCH_ASSOC);
                ?>


                                    <tbody>
                                        <?php
                $TopSumClassOrderPayUseCashPrice = "";
                while($Row = $Stmt->fetch()) {
                    $BranchID = $Row["BranchID"];
                    $SumClassOrderPayUseCashPrice = $Row["SumClassOrderPayUseCashPrice"];
                    $BranchName = $Row["BranchName"];

                    if ($TopSumClassOrderPayUseCashPrice==""){
                        $SumClassOrderPayUseCashPriceRatio = 100;
                        if ($SumClassOrderPayUseCashPrice==0){
                            $SumClassOrderPayUseCashPriceRatio = 0;
                        }

                        $TopSumClassOrderPayUseCashPrice = $SumClassOrderPayUseCashPrice;

                    }else{
                        if ($TopSumClassOrderPayUseCashPrice==0){
                            $SumClassOrderPayUseCashPriceRatio = 0;
                        }else{
                            $SumClassOrderPayUseCashPriceRatio = ($SumClassOrderPayUseCashPrice / $TopSumClassOrderPayUseCashPrice) * 100;
                        }
                    }
                    ?>
                                        <tr class="uk-table-middle">
                                            <td class="uk-width-3-10 uk-text-nowrap"><?php echo $BranchName; ?></td>
                                            <td class="uk-width-2-10 uk-text-nowrap">￦ <?php echo number_format($SumClassOrderPayUseCashPrice,0); ?></td>
                                            <td class="uk-width-3-10">
                                                <div class="uk-progress uk-progress-mini uk-progress-warning uk-margin-remove">
                                                    <div class="uk-progress-bar" style="width: <?php echo $SumClassOrderPayUseCashPriceRatio; ?>%;"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php
                }
                $Stmt = null;
                ?>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="uk-width-medium-1-2">
                    <h3 class="md-card-toolbar-heading-text">
                        <?php echo $대리점_매출_순위_수수료제외[$LangID]; ?>
                    </h3>
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-overflow-container" style="height:440px;overflow-y:auto;">
                                <table class="uk-table">
                                    <thead>
                                        <tr>
                                            <th class="uk-text-nowrap"><?php echo $대리점명[$LangID]; ?></th>
                                            <th class="uk-text-nowrap"><?php echo $매출[$LangID]; ?></th>
                                            <th class="uk-text-nowrap"><?php echo $차트[$LangID]; ?></th>
                                        </tr>
                                    </thead>

                                    <?php
                $AddSqlWhere = " 1=1 ";
                if ($_LINK_ADMIN_LEVEL_ID_==6 || $_LINK_ADMIN_LEVEL_ID_==7){//대표 지사
                    $AddSqlWhere .= " and E.BranchGroupID=".$_LINK_ADMIN_BRANCH_GROUP_ID_." ";
                }else if ($_LINK_ADMIN_LEVEL_ID_==9 || $_LINK_ADMIN_LEVEL_ID_==10){//지사
                    $AddSqlWhere .= " and D.BranchID=".$_LINK_ADMIN_BRANCH_ID_." ";
                }else if ($_ADMIN_LEVEL_ID_==12 || $_ADMIN_LEVEL_ID_==13){//대리점
                    $AddSqlWhere .= " and C.CenterID=".$_LINK_ADMIN_CENTER_ID_." ";
                }else{//대표지사 이상

                }

                $AddSqlWhere2 = $AddSqlWhere;
                $AddSqlWhere2 = $AddSqlWhere2 . " and datediff(A.ClassOrderPayDateTime, '".$SearchStartDate."')>=0 and datediff(A.ClassOrderPayDateTime, '".$SearchEndDate."')<=0 ";

                $ViewTable = "select 
                                                C.CenterID, 
                                                sum(A.ClassOrderPayUseCashPrice-A.ClassOrderPayPgFeePrice-(A.ClassOrderPayUseCashPrice * ClassOrderPayPgFeeRatio / 100)) as SumClassOrderPayUseCashPrice
                                        from ClassOrderPays A 
                                            inner join Members B on A.ClassOrderPayPaymentMemberID=B.MemberID 
                                            inner join Centers C on A.CenterID=C.CenterID 
                                            inner join Branches D on C.BranchID=D.BranchID
                                            inner join BranchGroups E on D.BranchGroupID=E.BranchGroupID 
                                            inner join Companies F on E.CompanyID=F.CompanyID 
                                            inner join Franchises G on F.FranchiseID=G.FranchiseID 
                                        where ".$AddSqlWhere2." and (A.ClassOrderPayProgress=21 or A.ClassOrderPayProgress=31 or A.ClassOrderPayProgress=41) 
                                        group by C.CenterID 
                                        ";

                $Sql = "select 
                                                AA.CenterID,
                                                AA.SumClassOrderPayUseCashPrice,
                                                BB.CenterName
                                            from ($ViewTable) AA 
                                                inner join Centers BB on AA.CenterID=BB.CenterID
                                            order by AA.SumClassOrderPayUseCashPrice desc 
                                            limit 0,50 
                                            ";


                $Stmt = $DbConn->prepare($Sql);
                $Stmt->execute();
                $Stmt->setFetchMode(PDO::FETCH_ASSOC);
                ?>
                                    <tbody>
                                        <?php
                $TopSumClassOrderPayUseCashPrice = "";
                while($Row = $Stmt->fetch()) {
                    $CenterID = $Row["CenterID"];
                    $SumClassOrderPayUseCashPrice = $Row["SumClassOrderPayUseCashPrice"];
                    $CenterName = $Row["CenterName"];

                    if ($TopSumClassOrderPayUseCashPrice==""){
                        $SumClassOrderPayUseCashPriceRatio = 100;
                        if ($SumClassOrderPayUseCashPrice==0){
                            $SumClassOrderPayUseCashPriceRatio = 0;
                        }
                        $TopSumClassOrderPayUseCashPrice = $SumClassOrderPayUseCashPrice;
                    }else{
                        if ($TopSumClassOrderPayUseCashPrice==0){
                            $SumClassOrderPayUseCashPriceRatio = 0;
                        }else{
                            $SumClassOrderPayUseCashPriceRatio = ($SumClassOrderPayUseCashPrice / $TopSumClassOrderPayUseCashPrice) * 100;
                        }
                    }
                    ?>
                                        <tr class="uk-table-middle">
                                            <td class="uk-width-3-10 uk-text-nowrap"><?php echo $CenterName; ?></td>
                                            <td class="uk-width-2-10 uk-text-nowrap">￦ <?php echo number_format($SumClassOrderPayUseCashPrice,0); ?></td>
                                            <td class="uk-width-3-10">
                                                <div class="uk-progress uk-progress-mini uk-progress-success uk-margin-remove">
                                                    <div class="uk-progress-bar" style="width: <?php echo $SumClassOrderPayUseCashPriceRatio; ?>%;"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php
                }
                $Stmt = null;
                ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
            -->



        <?
			//주간 포인트
			$TodayWeek = date('w', strtotime(date("Y-m-d")));
			$PointStartDate = date("Y-m-d", strtotime("-".$TodayWeek." day", strtotime(date("Y-m-d"))));
			$PointEndDate = date("Y-m-d", strtotime((6-$TodayWeek)." day", strtotime(date("Y-m-d"))));

			
			
			$AddSqlWhere = " 1=1 "; 
			if ($_LINK_ADMIN_LEVEL_ID_==6 || $_LINK_ADMIN_LEVEL_ID_==7){//대표 지사
				$AddSqlWhere .= " and F.BranchGroupID=".$_LINK_ADMIN_BRANCH_GROUP_ID_." ";
			}else if ($_LINK_ADMIN_LEVEL_ID_==9 || $_LINK_ADMIN_LEVEL_ID_==10){//지사
				$AddSqlWhere .= " and E.BranchID=".$_LINK_ADMIN_BRANCH_ID_." ";
			}else if ($_ADMIN_LEVEL_ID_==12 || $_ADMIN_LEVEL_ID_==13){//대리점
				$AddSqlWhere .= " and D.CenterID=".$_LINK_ADMIN_CENTER_ID_." ";
			}else{//대표지사 이상
				
			}

			$AddSqlWhere = $AddSqlWhere . " and C.MemberState<>0 ";
			$AddSqlWhere = $AddSqlWhere . " and D.CenterState<>0 ";
			$AddSqlWhere = $AddSqlWhere . " and E.BranchState<>0 ";
			$AddSqlWhere = $AddSqlWhere . " and F.BranchGroupState<>0 ";
			$AddSqlWhere = $AddSqlWhere . " and G.CompanyState<>0 ";
			$AddSqlWhere = $AddSqlWhere . " and H.FranchiseState<>0 ";


			$ViewTable1 = "
				select 
					A.MemberID, 
					sum(A.MemberPoint) as MemberTotalPoint 
				from MemberPoints A 
					inner join Members C on A.MemberID=C.MemberID 
					inner join Centers D on C.CenterID=D.CenterID 
					inner join Branches E on D.BranchID=E.BranchID
					inner join BranchGroups F on E.BranchGroupID=F.BranchGroupID 
					inner join Companies G on F.CompanyID=G.CompanyID 
					inner join Franchises H on G.FranchiseID=H.FranchiseID 
				where 
					".$AddSqlWhere." 
					and datediff(A.MemberPointRegDateTime, '".$PointStartDate."')>=0 and datediff(A.MemberPointRegDateTime, '".$PointEndDate."')<=0 
				group by A.MemberID 
			";


			//월간 포인트
			$PointStartDate = date("Y-m-01");
			$PointEndDate = date("Y-m-").date('t', strtotime(date("Y-m-01")));

			$ViewTable2 = "
				select 
					A.MemberID, 
					sum(A.MemberPoint) as MemberTotalPoint 
				from MemberPoints A 
					inner join Members C on A.MemberID=C.MemberID 
					inner join Centers D on C.CenterID=D.CenterID 
					inner join Branches E on D.BranchID=E.BranchID
					inner join BranchGroups F on E.BranchGroupID=F.BranchGroupID 
					inner join Companies G on F.CompanyID=G.CompanyID 
					inner join Franchises H on G.FranchiseID=H.FranchiseID 
				where 
					".$AddSqlWhere." 
					and datediff(A.MemberPointRegDateTime, '".$PointStartDate."')>=0 and datediff(A.MemberPointRegDateTime, '".$PointEndDate."')<=0 
				group by A.MemberID 
			";


			//전체 포인트
			$ViewTable3 = "
				select 
					A.MemberID, 
					sum(A.MemberPoint) as MemberTotalPoint 
				from MemberPoints A 
					inner join Members C on A.MemberID=C.MemberID 
					inner join Centers D on C.CenterID=D.CenterID 
					inner join Branches E on D.BranchID=E.BranchID
					inner join BranchGroups F on E.BranchGroupID=F.BranchGroupID 
					inner join Companies G on F.CompanyID=G.CompanyID 
					inner join Franchises H on G.FranchiseID=H.FranchiseID 
				where 
					".$AddSqlWhere." 
				group by A.MemberID 
			";
			?>

			<div class="uk-width-medium-2-6">
				<h3 class="md-card-toolbar-heading-text">
					<?=$학생_전체_포인트_순위[$LangID]?>
				</h3>
				<div class="md-card">
					<div class="md-card-content">
						<div class="uk-overflow-container" style="height:440px;overflow-y:auto;">
							<table class="uk-table">
								<thead>
									<tr>
										<th class="uk-text-nowrap"><?=$학생명[$LangID]?></th>
										<th class="uk-text-nowrap"><?=$포인트[$LangID]?></th>
										<th class="uk-text-nowrap"><?=$차트[$LangID]?></th>
									</tr>
								</thead>
								<tbody>

									<?
									$Sql = "
										select 
											A.MemberID,
											A.MemberTotalPoint,
											B.MemberLoginID,
											B.MemberName,
											C.CenterName
										from (".$ViewTable3.") A 
											inner join Members B on A.MemberID=B.MemberID 
											inner join Centers C on B.CenterID=C.CenterID 
										where B.MemberLevelID=19 
										order by A.MemberTotalPoint desc limit 0, 50
									";


									$Stmt = $DbConn->prepare($Sql);
									$Stmt->execute();
									$Stmt->setFetchMode(PDO::FETCH_ASSOC);

									$ListCount = 1;
									$TopMemberTotalPoint = "";
									while($Row = $Stmt->fetch()) {
										$MemberID = $Row["MemberID"];
										$MemberLoginID = $Row["MemberLoginID"];
										$MemberName = $Row["MemberName"];
										$MemberTotalPoint = $Row["MemberTotalPoint"];
										$CenterName = $Row["CenterName"];

										if ($TopMemberTotalPoint==""){
											$MemberTotalPointRatio = 100;
											$TopMemberTotalPoint = $MemberTotalPoint;
										}else{
											$MemberTotalPointRatio = ($MemberTotalPoint / $TopMemberTotalPoint) * 100;
										}
									?>
			

									<tr class="uk-table-middle">
										<td class="uk-width-3-10 uk-text-nowrap"><?=$MemberName?>(<?=$CenterName?>)</td>
										<td class="uk-width-2-10 uk-text-nowrap"><?=number_format($MemberTotalPoint)?>점</td>
										<td class="uk-width-3-10">
											<div class="uk-progress uk-progress-mini uk-progress-danger uk-margin-remove">
												<div class="uk-progress-bar" style="width: <?=$MemberTotalPointRatio?>%;"></div>
											</div>
										</td>
									</tr>
									<?
										$ListCount++;
									}
									$Stmt = null;
									?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>



			<div class="uk-width-medium-2-6">
				<h3 class="md-card-toolbar-heading-text">
					<?=$학생_월간_포인트_순위[$LangID]?>
				</h3>
				<div class="md-card">
					<div class="md-card-content">
						<div class="uk-overflow-container" style="height:440px;overflow-y:auto;">
							<table class="uk-table">
								<thead>
									<tr>
										<th class="uk-text-nowrap"><?=$학생명[$LangID]?></th>
										<th class="uk-text-nowrap"><?=$포인트[$LangID]?></th>
										<th class="uk-text-nowrap"><?=$차트[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
									<?
									$Sql = "
										select 
											A.MemberID,
											A.MemberTotalPoint,
											B.MemberLoginID,
											B.MemberName,
											C.CenterName
										from (".$ViewTable2.") A 
											inner join Members B on A.MemberID=B.MemberID 
											inner join Centers C on B.CenterID=C.CenterID 
										where B.MemberLevelID=19 
										order by A.MemberTotalPoint desc limit 0, 50
									";

									$Stmt = $DbConn->prepare($Sql);
									$Stmt->execute();
									$Stmt->setFetchMode(PDO::FETCH_ASSOC);

									$ListCount = 1;
									$TopMemberTotalPoint = "";
									while($Row = $Stmt->fetch()) {
										$MemberID = $Row["MemberID"];
										$MemberLoginID = $Row["MemberLoginID"];
										$MemberName = $Row["MemberName"];
										$MemberTotalPoint = $Row["MemberTotalPoint"];
										$CenterName = $Row["CenterName"];

										if ($TopMemberTotalPoint==""){
											$MemberTotalPointRatio = 100;
											$TopMemberTotalPoint = $MemberTotalPoint;
										}else{
											$MemberTotalPointRatio = ($MemberTotalPoint / $TopMemberTotalPoint) * 100;
										}
									?>
									<tr class="uk-table-middle">
										<td class="uk-width-3-10 uk-text-nowrap"><?=$MemberName?>(<?=$CenterName?>)</td>
										<td class="uk-width-2-10 uk-text-nowrap"><?=number_format($MemberTotalPoint)?>점</td>
										<td class="uk-width-3-10">
											<div class="uk-progress uk-progress-mini uk-margin-remove">
												<div class="uk-progress-bar" style="width: <?=$MemberTotalPointRatio?>%;"></div>
											</div>
										</td>
									</tr>
									<?
										$ListCount++;
									}
									$Stmt = null;
									?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>

			<div class="uk-width-medium-2-6">
				<h3 class="md-card-toolbar-heading-text">
					<?=$학생_주간_포인트_순위[$LangID]?>
				</h3>
				<div class="md-card">
					<div class="md-card-content">
						<div class="uk-overflow-container" style="height:440px;overflow-y:auto;">
							<table class="uk-table">
								<thead>
									<tr>
										<th class="uk-text-nowrap"><?=$학생명[$LangID]?></th>
										<th class="uk-text-nowrap"><?=$포인트[$LangID]?></th>
										<th class="uk-text-nowrap"><?=$차트[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
									<?
									$Sql = "
										select 
											A.MemberID,
											A.MemberTotalPoint,
											B.MemberLoginID,
											B.MemberName,
											C.CenterName
										from (".$ViewTable1.") A 
											inner join Members B on A.MemberID=B.MemberID 
											inner join Centers C on B.CenterID=C.CenterID 
										where B.MemberLevelID=19 
										order by A.MemberTotalPoint desc limit 0, 50
									";

									$Stmt = $DbConn->prepare($Sql);
									$Stmt->execute();
									$Stmt->setFetchMode(PDO::FETCH_ASSOC);

									$ListCount = 1;
									$TopMemberTotalPoint = "";
									while($Row = $Stmt->fetch()) {
										$MemberID = $Row["MemberID"];
										$MemberLoginID = $Row["MemberLoginID"];
										$MemberName = $Row["MemberName"];
										$MemberTotalPoint = $Row["MemberTotalPoint"];
										$CenterName = $Row["CenterName"];

										if ($TopMemberTotalPoint==""){
											$MemberTotalPointRatio = 100;
											$TopMemberTotalPoint = $MemberTotalPoint;
										}else{
											$MemberTotalPointRatio = ($MemberTotalPoint / $TopMemberTotalPoint) * 100;
										}
						
									?>
									<tr class="uk-table-middle">
									<td class="uk-width-3-10 uk-text-nowrap"><?=$MemberName?>(<?=$CenterName?>)</td>
									<td class="uk-width-2-10 uk-text-nowrap"><?=number_format($MemberTotalPoint)?>점</td>
										<td class="uk-width-3-10">
											<div class="uk-progress uk-progress-mini uk-progress-success uk-margin-remove">
												<div class="uk-progress-bar" style="width: <?=$MemberTotalPointRatio?>%;"></div>
											</div>
										</td>
									</tr>
									<?
										$ListCount++;
									}
									$Stmt = null;
									?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- large chart -->
		<div class="uk-grid">
			<div class="uk-width-1-1">
				<div class="md-card">
					<div class="md-card-toolbar">
						<div class="md-card-toolbar-actions">
							<!--
							<i class="md-icon material-icons md-card-fullscreen-activate">&#xE5D0;</i>
							<i class="md-icon material-icons">&#xE5D5;</i>
							<div class="md-card-dropdown" data-uk-dropdown="{pos:'bottom-right'}">
								<i class="md-icon material-icons">&#xE5D4;</i>
								<div class="uk-dropdown uk-dropdown-small">
									<ul class="uk-nav">
										<li><a href="#">Action 1</a></li>
										<li><a href="#">Action 2</a></li>
									</ul>
								</div>
							</div>
							-->
						</div>
						<h3 class="md-card-toolbar-heading-text">
							<?=$신규학생가입추이[$LangID]?>
						</h3>
					</div>
					<div class="md-card-content">
						<div class="mGraph-wrapper">
							<div id="mGraph_sale" class="mGraph" data-uk-check-display></div>
							<!-- index_json_student_reg_count.php -->
						</div>
					</div>
				</div>
			</div>
		</div>


	</div>
</div>




<!-- common functions -->
<script src="assets/js/common.min.js"></script>
<!-- uikit functions -->
<script src="assets/js/uikit_custom.min.js"></script>
<!-- altair common functions/helpers -->
<script src="assets/js/altair_admin_common.min.js"></script>

<!-- page specific plugins -->
<!-- d3 -->
<script src="bower_components/d3/d3.min.js"></script>
<!-- metrics graphics (charts) -->
<script src="bower_components/metrics-graphics/dist/metricsgraphics.min.js"></script>
<!-- chartist (charts) -->
<!-- script src="bower_components/chartist/dist/chartist.min.js"></script -->
<!-- maplace (google maps) -->
<!-- script src="http://maps.google.com/maps/api/js?key=AIzaSyC2FodI8g-iCz1KHRFE7_4r8MFLA7Zbyhk"></script  -->
<!--  script src="bower_components/maplace-js/dist/maplace.min.js"></script  -->
<!-- peity (small charts) -->
<script src="bower_components/peity/jquery.peity.min.js"></script>
<!-- easy-pie-chart (circular statistics) -->
<script src="bower_components/jquery.easy-pie-chart/dist/jquery.easypiechart.min.js"></script>
<!-- countUp -->
<script src="bower_components/countUp.js/dist/countUp.min.js"></script>
<!-- handlebars.js -->
<script src="bower_components/handlebars/handlebars.min.js"></script>
<script src="assets/js/custom/handlebars_helpers.min.js"></script>
<!-- CLNDR -->
<script src="bower_components/clndr/clndr.min.js"></script>

<!--  dashbord functions -->
<script src="assets/js/pages/dashboard.js"></script>

<!-- ==============  common.js ============== -->
<script src="js/common.js"></script>
<!-- ==============  common.js ============== -->


<script>
function SearchSubmit(){
	document.SearchForm.action = "index.php";
	document.SearchForm.submit();
}

function PreviewPopup(id, t, l, w, h){
	h=h+40;
	newwin = window.open('../popup_preview.php?PopupID='+id,'','width='+w+',height='+h+',toolbar=no,top='+t+',left='+l);
	newwin.focus();
}
</script>


<?
if ($ForceTaxMemberInfoPage==1){
?>
<script>
UIkit.modal.confirm(
	'<?=$세금계산서_자동_발행을_위해_사업자_정보를_입력해_주세요[$LangID]?>', 
	function(){ 
		location.href = "center_form.php?CenterID=<?=$_ADMIN_CENTER_ID_?>&PageTabID=8";
	}
);
</script>
<?
}
?>

<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>