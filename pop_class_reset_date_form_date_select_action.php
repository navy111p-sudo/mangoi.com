<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
include_once('./includes/member_check.php');

$err_num = 0;
$err_msg = "";

$StudyTimeDate = isset($_REQUEST["StudyTimeDate"]) ? $_REQUEST["StudyTimeDate"] : "";


// 연기하려는 날짜가 토요일 또는 일요일이면 아예 진행을 막는다. 

if (date('w',strtotime($StudyTimeDate))==0 || date('w',strtotime($StudyTimeDate))==6  ){
?>
	<!doctype html>
	<html>
	<head>
	<meta charset="utf-8">
	<title><?php echo $_SITE_TITLE_;?></title>
	<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
	<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
	
	<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
	<script src="./js/jquery-confirm.min.js"></script>
	<link href="./css/jquery-confirm.min.css" rel="stylesheet" type="text/css"/>
	</head>
	<body>
	<script>
		alert('선택된 날짜가 토/일요일입니다. 해당 날짜로는 연기가 불가능합니다.');
		window.Exit=true;
		parent.$.fn.colorbox.close();
	</script>
	</body>
	</html>

<?php
} else {
	$StudyTimeHour = isset($_REQUEST["StudyTimeHour"]) ? $_REQUEST["StudyTimeHour"] : "";
	$StudyTimeMinute = isset($_REQUEST["StudyTimeMinute"]) ? $_REQUEST["StudyTimeMinute"] : "";
	
	$FromDevice = isset($_REQUEST["FromDevice"]) ? $_REQUEST["FromDevice"] : "";
	$ClassID = isset($_REQUEST["ClassID"]) ? $_REQUEST["ClassID"] : "";
	$ClassOrderID = isset($_REQUEST["ClassOrderID"]) ? $_REQUEST["ClassOrderID"] : "";
	$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
	$TeacherID = isset($_REQUEST["TeacherID"]) ? $_REQUEST["TeacherID"] : "";
	$ClassProductID = isset($_REQUEST["ClassProductID"]) ? $_REQUEST["ClassProductID"] : "";
	$ClassMemberType = isset($_REQUEST["ClassMemberType"]) ? $_REQUEST["ClassMemberType"] : "";
	$ClassOrderPayID = isset($_REQUEST["ClassOrderPayID"]) ? $_REQUEST["ClassOrderPayID"] : "";
	$CenterPayType = isset($_REQUEST["CenterPayType"]) ? $_REQUEST["CenterPayType"] : "";
	$ClassOrderTimeTypeID = isset($_REQUEST["ClassOrderTimeTypeID"]) ? $_REQUEST["ClassOrderTimeTypeID"] : "";
	$GroupRowCount = isset($_REQUEST["GroupRowCount"]) ? $_REQUEST["GroupRowCount"] : "";
	$ResetType = isset($_REQUEST["ResetType"]) ? $_REQUEST["ResetType"] : "";
	$IframeMode = isset($_REQUEST["IframeMode"]) ? $_REQUEST["IframeMode"] : "0";
	$FromPage = isset($_REQUEST["FromPage"]) ? $_REQUEST["FromPage"] : "";
	
	//스케줄 변경의 경우 기존 슬랏 정보(이 슬랏들의 종료날짜를 업데이트 해준다)
	$SetTeacherID = isset($_REQUEST["SetTeacherID"]) ? $_REQUEST["SetTeacherID"] : "";
	$SetYear = isset($_REQUEST["SetYear"]) ? $_REQUEST["SetYear"] : "";
	$SetMonth = isset($_REQUEST["SetMonth"]) ? $_REQUEST["SetMonth"] : "";
	$SetDay = isset($_REQUEST["SetDay"]) ? $_REQUEST["SetDay"] : "";
	$SetHour = isset($_REQUEST["SetHour"]) ? $_REQUEST["SetHour"] : "";
	$SetMinute = isset($_REQUEST["SetMinute"]) ? $_REQUEST["SetMinute"] : "";
	$SetWeek = isset($_REQUEST["SetWeek"]) ? $_REQUEST["SetWeek"] : "";
	
	
	$SetStudyTimeDate = "";
	$ClassOrderResetApplyDate = "";
	
	
	if ($ResetType=="EverChange"){
		$NewSlotTimeWeek = date("w", strtotime($StudyTimeDate));
		$OldSlotTimeWeek = date("w", strtotime($SetYear."-".$SetMonth."-".$SetDay));

		$NewWeekOfYear = date("W", strtotime($StudyTimeDate));
		$OldWeekOfYear = date("W", strtotime($SetYear."-".$SetMonth."-".$SetDay));
		
		// 변경하려는 시간이 원래의 시간의 다음 주(또는 그 이상)이라면 SetStydyTimeDate 에 원래의 시간 -1 을 해 준다. 
		if ($NewWeekOfYear > $OldWeekOfYear ) {
			$SetStudyTimeDate = date("Y-m-d", strtotime($SetYear."-".$SetMonth."-".$SetDay. " -1 days"));

		} else {  // 그게 아니라면 원래대로 계산해서 넣어준다. 

			if ($OldSlotTimeWeek < $NewSlotTimeWeek){
				$PlusWeekNum = $OldSlotTimeWeek - $NewSlotTimeWeek -1 ;
			}else if ($OldSlotTimeWeek >= $NewSlotTimeWeek){
				$PlusWeekNum = -1;
			}
		
			$SetStudyTimeDate = date("Y-m-d", strtotime(substr($StudyTimeDate,0,10). " ".$PlusWeekNum." days"));

		}

	}
	
	
	//스케줄 변경의 경우 기존 슬랏 정보(이 슬랏들의 종료날짜를 업데이트 해준다)
	
	
	
	
	$OldTeacherID = 0;
	$OldTeacherMemberID = 0;
	$NewTeacherID = $TeacherID;
	$Sql = "SELECT A.MemberID, A.MemberName, A.MemberLoginID,
				AES_DECRYPT(UNHEX(A.MemberEmail),:EncryptionKey) AS Email
			 from Members A where A.TeacherID=$NewTeacherID and A.MemberLevelID=15";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$NewTeacherMemberID=$Row["MemberID"];
	$NewTeacherMemberEmail=$Row["Email"];
	$NewTeacherMemberName=$Row["MemberName"];
	$NewTeacherMemberLoginID=$Row["MemberLoginID"];
	
	$ClassOrderSlotType = 2;
	if ($ResetType=="ChTeacher"){
		$StrResetType = "강사변경";
	}else if ($ResetType=="PlusClass"){
		$StrResetType = "보강등록";
	}else if ($ResetType=="EverChange"){
		$StrResetType = "스케줄변경";
		$ClassOrderSlotType = 1;
	}else{
		$StrResetType = "수업연기";
	}
	
	//=================== 로그 남기기 ==========================
	$ClassOrderSlotLogMemberID = $_LINK_MEMBER_ID_;
	$ClassOrderSlotLogMemo = $StrResetType;
	$Sql = " insert into ClassOrderSlotLogs ( ";
		$Sql .= " ClassOrderID, ";
		$Sql .= " MemberID, ";
		$Sql .= " ClassOrderSlotLogMemo, ";
		$Sql .= " ClassOrderSlotLogRegDateTime ";
	$Sql .= " ) values ( ";
		$Sql .= " :ClassOrderID, ";
		$Sql .= " :MemberID, ";
		$Sql .= " :ClassOrderSlotLogMemo, ";
		$Sql .= " now() ";
	$Sql .= " ) ";
	
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ClassOrderID', $ClassOrderID);
	$Stmt->bindParam(':MemberID', $ClassOrderSlotLogMemberID);
	$Stmt->bindParam(':ClassOrderSlotLogMemo', $ClassOrderSlotLogMemo);
	$Stmt->execute();
	$Stmt = null;
	//=================== 로그 남기기 ==========================
	
	
	
	
	$ClassOrderSlotType2 = 0;
	$ClassAttendStateMsg = $StrResetType ."(".$NewTeacherMemberName.")<br>". $StudyTimeDate . "(".$StudyTimeHour.":".$StudyTimeMinute.")";
	
	$ClassAttendStateMemberID = $_LINK_MEMBER_ID_;
	if ($ResetType=="ChTeacher"){
		$ClassAttendState = 8;//강사변경
	} else if ($ResetType=="PlusClass"){
		$ClassAttendState = 10000;// 보강등록(State 크드는 10000으로 정하되 기존 강의를 살려두기 때문에 사실상 입력하지는 않음)
	} else if ($ResetType=="EverChange"){
		$ClassAttendState = 20000;// 스케줄변경(State 크드는 20000으로 정하되 기존 강의를 살려두기 때문에 사실상 입력하지는 않음)
	}else{
		if ($_LINK_MEMBER_LEVEL_ID_==19){
			$ClassAttendState = 4;//학생연기
		}else{
			$ClassAttendState = 5;//강사연기
		}
	}
	$ClassOrderSlotType2 = $ClassAttendState;
	
	
	if ($ClassMemberType!="1"){
	
		$Sql = "
				select 
					* 
				from Classes A 
				where ClassID=$ClassID 
		";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;
		$TeacherID=$Row["TeacherID"];
		$StartDateTimeStamp=$Row["StartDateTimeStamp"];
		$EndDateTimeStamp=$Row["EndDateTimeStamp"];
	
	
		$Sql = "
				select 
					* 
				from Classes A 
				where A.TeacherID=$TeacherID 
					and A.StartDateTimeStamp=$StartDateTimeStamp 
					and A.EndDateTimeStamp=$EndDateTimeStamp 
					and A.ClassAttendState<=3 
		";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		
		$ClassIDs = "|";
		$ClassOrderIDs = "|";
		while($Row = $Stmt->fetch()) {
			$ClassID = $Row["ClassID"];
			$ClassOrderID = $Row["ClassOrderID"];
			$ClassIDs = $ClassIDs . $ClassID . "|";
			$ClassOrderIDs = $ClassOrderIDs . $ClassOrderID . "|";
		}
		$Stmt = null;
	
	
	
	}else{
		$ClassOrderIDs = "|".$ClassOrderID."|";
		$ClassIDs = "|".$ClassID."|";
	}
	
	//echo $ClassIDs;
	
	$ArrClassID = explode("|", $ClassIDs);
	$ArrClassOrderID = explode("|", $ClassOrderIDs);
	
	for ($ii=1;$ii<=count($ArrClassID)-2;$ii++){
	
		$ClassID = $ArrClassID[$ii];
		$ClassOrderID = $ArrClassOrderID[$ii];
	
		if ($ResetType=="PlusClass" || $ResetType=="EverChange"){
			//보강은 기존강의를 그대로 살려둔다.
			//스케줄변경은 당일 수업부터 해당 슬랏의 종료날짜가 당일 하루전으로 설정되기때문에 모든 페이지에서 9를 걸러줄 필요는 없다.
		}else{
	
			$Sql = " update Classes set ";
				$Sql .= " ClassAttendState = :ClassAttendState, ";
				$Sql .= " ClassAttendStateMemberID = :ClassAttendStateMemberID, ";
				$Sql .= " ClassAttendStateMsg=:ClassAttendStateMsg, ";
				$Sql .= " ClassModiDateTime = now() ";
			$Sql .= " where ClassID = :ClassID ";
	
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':ClassAttendState', $ClassAttendState);
			$Stmt->bindParam(':ClassAttendStateMsg', $ClassAttendStateMsg);
			$Stmt->bindParam(':ClassAttendStateMemberID', $ClassAttendStateMemberID);
			$Stmt->bindParam(':ClassID', $ClassID);
			$Stmt->execute();
			$Stmt = null;
	
			if ($ii==1){
				$Sql = "
						select 
							A.* 
						from Classes A 
						where A.ClassID=".$ClassID." 
							and A.StartYear=".date("Y")." 
							and A.StartMonth=".date("n")." 
							and A.StartDay=".date("j")."
				";
				$Stmt = $DbConn->prepare($Sql);
				$Stmt->execute();
				$Stmt->setFetchMode(PDO::FETCH_ASSOC);
				$Row = $Stmt->fetch();
				$Stmt = null;
				$OldTeacherID=$Row["TeacherID"];
				
				if ($OldTeacherID){
					$Sql = "select A.MemberID from Members A where A.TeacherID=$OldTeacherID and A.MemberLevelID=15";
					$Stmt = $DbConn->prepare($Sql);
					$Stmt->execute();
					$Stmt->setFetchMode(PDO::FETCH_ASSOC);
					$Row = $Stmt->fetch();
					$Stmt = null;
					$OldTeacherMemberID=$Row["MemberID"];
					
					InsertTeacherMessage($OldTeacherMemberID, 2, "Today's class schedule is changing. Please refresh the screen.");
				}
			}
		}
	

	
		$ResetTimeWeek = date("w", strtotime($StudyTimeDate));
		$ClassOrderResetApplyDate = $StudyTimeDate;
		$ResetTimeHour = $StudyTimeHour;
		$ResetTimeMinute = $StudyTimeMinute;
		
	
		//레벨테스트 20분 슬랏 등록 (2개)
		for ($iiii=1;$iiii<=$ClassOrderTimeTypeID;$iiii++){
	
			$TempResetTimeWeek = $ResetTimeWeek;
			$TempResetTimeHour = $ResetTimeHour;
			$TempResetTimeMinute = $ResetTimeMinute + ( ($iiii-1) * 10);
			
			if ($TempResetTimeMinute>=60){
				$TempResetTimeHour = $TempResetTimeHour + 1;
				$TempResetTimeMinute = $TempResetTimeMinute - 60;
			}
	
			if ($TempResetTimeHour>=24){
	
				if ($TempResetTimeHour==24){
					$TempResetTimeHour = 0;
				}else if ($TempResetTimeHour==25){
					$TempResetTimeHour = 1;
				}
	
				if ($TempResetTimeWeek==6){
					$TempResetTimeWeek = 0;
				}else{
					$TempResetTimeWeek = $TempResetTimeWeek + 1;
				}
			}
	
			if ($iiii==1){
				$ClassOrderSlotMaster = 1;
	
				$Sql = "select ifnull(Max(ClassOrderSlotGroupID),0) as ClassOrderSlotGroupID from ClassOrderSlots";
				$Stmt = $DbConn->prepare($Sql);
				$Stmt->execute();
				$Stmt->setFetchMode(PDO::FETCH_ASSOC);
				$Row = $Stmt->fetch();
				$Stmt = null;
				$ClassOrderSlotGroupID = $Row["ClassOrderSlotGroupID"]+1;
			}else{
				$ClassOrderSlotMaster = 0;
			}
	
	
	
			$Sql = " insert into ClassOrderSlots ( ";
				$Sql .= " ClassOrderSlotGroupID, ";
				$Sql .= " ClassOrderPayID, ";
				$Sql .= " ClassMemberType, ";
				$Sql .= " ClassOrderSlotType, ";
				$Sql .= " ClassOrderSlotType2, ";
				if ($ResetType=="EverChange"){
					$Sql .= " ClassOrderSlotStartDate, ";//새 슬랏 시작날짜 설정
				}else{
					$Sql .= " ClassOrderSlotDate, ";
				}
				$Sql .= " TeacherID, ";
				$Sql .= " ClassOrderID, ";
				$Sql .= " ClassOrderSlotMaster, ";
				$Sql .= " StudyTimeWeek, ";
				$Sql .= " StudyTimeHour, ";
				$Sql .= " StudyTimeMinute, ";
				$Sql .= " ClassOrderSlotState, ";
				$Sql .= " ClassOrderSlotRegDateTime ";
			$Sql .= " ) values ( ";
				$Sql .= " :ClassOrderSlotGroupID, ";
				$Sql .= " :ClassOrderPayID, ";
				$Sql .= " :ClassMemberType, ";
				$Sql .= " :ClassOrderSlotType, ";
				$Sql .= " :ClassOrderSlotType2, ";
				if ($ResetType=="EverChange"){
					$Sql .= " :ClassOrderSlotStartDate, ";//새 슬랏 시작날짜 설정
				}else{
					$Sql .= " :ClassOrderSlotDate, ";
				}
				$Sql .= " :TeacherID, ";
				$Sql .= " :ClassOrderID, ";
				$Sql .= " :ClassOrderSlotMaster, ";
				$Sql .= " :StudyTimeWeek, ";
				$Sql .= " :StudyTimeHour, ";
				$Sql .= " :StudyTimeMinute, ";
				$Sql .= " 1, ";
				$Sql .= " now() ";
			$Sql .= " ) ";
	
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':ClassOrderSlotGroupID', $ClassOrderSlotGroupID);
			$Stmt->bindParam(':ClassOrderPayID', $ClassOrderPayID);
			$Stmt->bindParam(':ClassMemberType', $ClassMemberType);
			$Stmt->bindParam(':ClassOrderSlotType', $ClassOrderSlotType);
			$Stmt->bindParam(':ClassOrderSlotType2', $ClassOrderSlotType2);
			if ($ResetType=="EverChange"){
				$Stmt->bindParam(':ClassOrderSlotStartDate', $ClassOrderResetApplyDate);
			}else{
				$Stmt->bindParam(':ClassOrderSlotDate', $ClassOrderResetApplyDate);
			}
			$Stmt->bindParam(':TeacherID', $NewTeacherID);
			$Stmt->bindParam(':ClassOrderID', $ClassOrderID);
			$Stmt->bindParam(':ClassOrderSlotMaster', $ClassOrderSlotMaster);
			$Stmt->bindParam(':StudyTimeWeek', $TempResetTimeWeek);
			$Stmt->bindParam(':StudyTimeHour', $TempResetTimeHour);
			$Stmt->bindParam(':StudyTimeMinute', $TempResetTimeMinute);
			$Stmt->execute();
			$Stmt = null;
	
	
			//if ($ResetType=="EverChange" && $iiii==1){
			if ($ResetType=="EverChange"){
	
	
				$TempSetTimeWeek = $SetWeek;
				$TempSetTimeHour = $SetHour;
				$TempSetTimeMinute = $SetMinute + ( ($iiii-1) * 10);
				
				if ($TempSetTimeMinute>=60){
					$TempSetTimeHour = $TempSetTimeHour + 1;
					$TempSetTimeMinute = $TempSetTimeMinute - 60;
				}
	
				if ($TempSetTimeHour>=24){
	
					if ($TempSetTimeHour==24){
						$TempSetTimeHour = 0;
					}else if ($TempSetTimeHour==25){
						$TempSetTimeHour = 1;
					}
	
					if ($TempSetTimeWeek==6){
						$TempSetTimeWeek = 0;
					}else{
						$TempSetTimeWeek = $TempSetTimeWeek + 1;
					}
				}
	
	
				//기존 슬랏 종료날짜 설정
				$Sql = " update ClassOrderSlots set ";
					$Sql .= " ClassOrderSlotEndDate = '$SetStudyTimeDate' ";
				$Sql .= " where 
								ClassOrderID = $ClassOrderID 
								and TeacherID=$SetTeacherID 
								and ClassMemberType=$ClassMemberType 
								and StudyTimeWeek=$TempSetTimeWeek 
								and StudyTimeHour=$TempSetTimeHour 
								and StudyTimeMinute=$TempSetTimeMinute
								and ClassOrderSlotType=1
						";

						//echo $Sql;  
				$Stmt = $DbConn->prepare($Sql);
				$Stmt->execute();
				$Stmt = null;
	
				
			}
	
		}
	
	
	}
	
	
	if ( $OldTeacherMemberID!=$NewTeacherMemberID && ( $ClassOrderResetApplyDate==date("Y-m-d") || $SetStudyTimeDate==date("Y-m-d") ) ){//보강, 연기, 변경 이고 그 날짜가 오늘일때
		$sendMessage = "Today's class schedule is changing. Please refresh the screen.";
		InsertTeacherMessage($NewTeacherMemberID, 1, $sendMessage);

		//메일로도 보내기
		$from_name = "mangoi";
		$subject = $sendMessage;
		//한글 안깨지게 만들어줌
		$subject = "=?UTF-8?B?".base64_encode($subject)."?=";
		$content = $sendMessage;
		$Headers = "from: =?utf-8?B?".base64_encode($from_name)."?= <mangoi@mangoi.co.kr>"."\r\n"; // from 과 : 은 붙여주세요 => from: 
		$Headers .= "Content-Type: text/html;";
		
		$from = "mangoi@mangoi.co.kr";
		
		mail($NewTeacherMemberEmail,$subject,$content,$Headers); 
	}
	
	if ($err_num != 0){
	?>
	<!doctype html>
	<html>
	<head>
	<meta charset="utf-8">
	<title><?php echo $_SITE_TITLE_;?></title>
	<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
	<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
	</head>
	<body>
	<script>
	alert("<?=$err_msg?>");
	history.go(-1);
	</script>
	</body>
	</html>
	<?php
	}
	
	include_once('./includes/dbclose.php');
	
	
	if ($err_num == 0){
	?>
	
	<!doctype html>
	<html>
	<head>
	<meta charset="utf-8">
	<title><?php echo $_SITE_TITLE_;?></title>
	<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
	<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
	
	<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
	<script src="./js/jquery-confirm.min.js"></script>
	<link href="./css/jquery-confirm.min.css" rel="stylesheet" type="text/css"/>
	</head>
	<body>
	<script>
	<?if ($IframeMode==1) {?>
		window.close();
		window.opener.location.close();
	<? } else { ?>
		<?if ($FromPage=="Calendar"){?>
			top.location.reload();
		<?}else{?>
			<?if ($FromDevice==""){?>
				parent.location.reload();
			<?}else{?>
				window.Exit=true;
				parent.$.fn.colorbox.close();
			<?}?>
		<?}?>
	<?}?>
	</script>
	</body>
	</html>
	<?
	}
	 
}

?>