<tr id="TrList_<?=$CheckListCount?>" onclick="SelectList(<?=$CheckListCount?>);" class="" style="background-color:<?=$GroupTrColor?>;">
	<td class="uk-text-nowrap uk-table-td-center">
		<?if ($ListSelectResetDate=="1"){?>
		<input name="CheckBox_<?=$CheckListCount?>" id="CheckBox_<?=$CheckListCount?>" type="checkbox" value="<?=$ClassID?>">
		<?}else{?>
		<input name="CheckBox_<?=$CheckListCount?>" id="CheckBox_<?=$CheckListCount?>" type="checkbox" value="<?=$MemberID?>">
		<?}?>
	</td>
	
	
	<?if ($GroupListCount==1){?>
		<td class="uk-text-nowrap uk-table-td-center" rowspan="<?=$GroupRowCount?>">
			<?=$ListNumber?>
			<?=$ClassMemberTypeName?>
		</td>
	<?}?>
	<td class="uk-text-nowrap uk-table-td-center">
			<?=$StrClassProductID?>
			<br>
			(
				<?=$ClassID?>-<?=$ClassOrderID?>-<?=$ClassOrderSlotID?><?if ($ClassMemberType!=1){?>-<?=$ClassMemberTypeGroupID?><?}?>
			) 
	</td>
	<td class="uk-text-nowrap uk-table-td-center">
		<?=$SelectYear?>-<?=substr("0".$SelectMonth,-2)?>-<?=substr("0".$SelectDay,-2)?>
		<br>
		<span style="color:#006BD7;"><?=ConvAmPm($ClassStartTime)?> ~ <?=ConvAmPm($StrClassEndTime)?></span>


	</td>
	<?if ($_LINK_ADMIN_LEVEL_ID_<=4) {?>
	<td class="uk-text-nowrap uk-table-td-center">
		
		<?
		if ($TeacherInDateTime!=""){
			$StartDateTimeNum = $SelectYear.substr("0".$SelectMonth,-2).substr("0".$SelectDay,-2).str_replace(":","",$ClassStartTime)."00";
			$TeacherInDateTimeNum = date("YmdHis", strtotime($TeacherInDateTime));

			$DiffMinutes = abs(strtotime($SelectYear."-".substr("0".$SelectMonth,-2)."-".substr("0".$SelectDay,-2)." ".$ClassStartTime.":"."00") - strtotime($TeacherInDateTime)) / 60;

			if ($StartDateTimeNum-$TeacherInDateTimeNum>=0){
				$StrTimeColor = "#0000ff";
				$StrDiffMinutes = "-".floor($DiffMinutes)." min";
			}else{
				$StrTimeColor = "#ff0000";
				$StrDiffMinutes = "+".floor($DiffMinutes)." min";
			}
		?>
			<span style="color:<?=$StrTimeColor?>;"><?=str_replace(" ", "<br>", $TeacherInDateTime)?></span>
			<br>
			<?=$StrDiffMinutes?>
		<?
		}
		?>
		
	</td>
	<?}?>
	<!--
	<td class="uk-text-nowrap uk-table-td-center">
		<span style="color:#006BD7;"><?=$StrClassOrderTimeTypeName?></span>
	</td>
	-->




	<td class="uk-text-nowrap uk-table-td-center">

		<?if ($_LINK_ADMIN_LEVEL_ID_==15) {?>
			<?=$CenterLoginID?>
		<?}else{?>
			<?=$CenterName?>
		<?}?>
		<br>

		<?=$MemberName?> <span style="color:#006BD7;"><?=$MemberLoginID?></span> 
		<br><?=$DecMemberPhone1?>
		<a href="javascript:OpenMessageSendForm(<?=$MemberID?>);"><i class="material-icons">sms</i></a>
		
		<?if ($_LINK_ADMIN_LEVEL_ID_<=4){?>
			<a href="javascript:OpenMemberPointForm(<?=$MemberID?>);"><i class="material-icons">monetization_on</i></a>
		<?}?>

		<?if ($_LINK_ADMIN_LEVEL_ID_==15) {?>
			<br>
			<?=$MemberNickName?>
		<?}?>
		<br>
		<? if ( $MemberAcceptCallByTeacher==1) { ?>
			<span style="color:#000000">NoCall</span>
		<? } else { ?>
			<span style="color:#7CB342">Call Alarm</span>			
		<? } ?>

		<br>
		<?=$CommonShNewClassCode?>


		<br>
		<a class="md-btn md-btn-warning md-btn-mini md-btn-wave-light" style="background-color:#808040;margin-top:10px;" href="javascript:OpenProductCartList(<?=$MemberID?>);"><?=$교재판매설정[$LangID]?></a>

	</td>

	<?if ($_LINK_ADMIN_LEVEL_ID_<15) {?>
		<td class="uk-text-nowrap uk-table-td-center"><?=$StrCenterPayType?></td>
		<!--<td class="uk-text-nowrap uk-table-td-center"><?=$StrStudyAuthDate?></td>-->
	<?}?>



	<td class="uk-text-nowrap uk-table-td-center">
		<?if ($MemberLoginID!="") {?>
			<a href="javascript:OpenStudentForm(<?=$MemberID?>);"><i class="material-icons">account_box</i></a>                                        
		<?}else{?>
			-
		<?}?>
	</td>

	
	<td class="uk-text-nowrap uk-table-td-center">
		<?if ($MemberLevelID==19) {?>
		<a href="javascript:OpenStudentCalendar(<?=$MemberID?>);"><i class="material-icons">date_range</i></a>
		<?}else{?>
		-
		<?}?>
	</td>

	<?if ($_LINK_ADMIN_LEVEL_ID_!=15) {?>
		<td class="uk-text-nowrap uk-table-td-center">
			<?=$TeacherName?>
			<a href="javascript:OpenTeacherMessageForm(<?=$TeacherID?>);"><i class="material-icons">new_releases</i></a>
		</td>
	<?}?>

	<td class="uk-text-nowrap uk-table-td-center" id="TrClsQna_<?=$ClassOrderID?>_<?=$SelectYear?>_<?=(int)$SelectMonth?>_<?=(int)$SelectDay?>_<?=(int)$StudyTimeHour?>_<?=(int)$StudyTimeMinute?>">
		<?if ($ClassID!=0){?>
		<a href="javascript:OpenClassQnaForm(<?=$ClassID?>);"><i class="material-icons">new_releases</i></a>
		<?}else{?>
		-
		<?}?>
	</td>
   
	<td class="uk-text-nowrap uk-table-td-center">
		<?=$StrClassState?>
	</td>

	<td class="uk-text-nowrap uk-table-td-center">
		<?if ($TodayIsEduCenterHoliday==1){?>
			-
		<?}else{?>
			<?if ($ClassAttendState==4 || $ClassAttendState==5 || $ClassAttendState==6 || $ClassAttendState==7 || $ClassAttendState==8){?>
				-
			<?}else{?>
				<?
				if ($LastClassID!=0 && $LastAssmtStudentDailyScoreID==0){//이전 수업이 있고 피드백을 하지 않은 경우
				?>
				<a class="md-btn md-btn-primary md-btn-mini md-btn-wave-light" href="javascript:OpenStudentScoreDailyForm(<?=$LastClassID?>);" style="background-color:#9467BA;width:120px;"><?=$이전수업평가[$LangID]?></a>
				<?
				}else{
					if ($LastAssmtStudentDailyScoreID>0){
				?>
					<a class="md-btn md-btn-success md-btn-mini md-btn-wave-light" href="javascript:OpenStudentScoreDailyReport(<?=$LastClassID?>);" style="background-color:#9595CA;width:120px;"><?=$이전평가보고서[$LangID]?></a>
					<br>
					<a class="md-btn md-btn-primary md-btn-mini md-btn-wave-light" href="javascript:OpenStudentScoreDailyForm(<?=$LastClassID?>);" style="background-color:#9467BA;margin-top:3px;width:120px;"><?=$이전수업평가[$LangID]?></a>
				<?
					}else{
				?>
					-
				<?
					}
				}
				?>
			<?}?>
		<?}?>
	</td>

	<td class="uk-text-nowrap uk-table-td-center">


		<?if ($TodayIsEduCenterHoliday==1){?>
			- 
		<?}else{?>

			<?if ($ClassAttendState==4 || $ClassAttendState==5 || $ClassAttendState==6 || $ClassAttendState==7 || $ClassAttendState==8){?>
				-
			<?}else{?>
				<?
				if ($ClassState==2){
				
					if ($ClassProductID==2){//레벨테스트
						$Sql3 = "select AssmtStudentLeveltestScoreID from AssmtStudentLeveltestScores where ClassID=:ClassID";
						$Stmt3 = $DbConn->prepare($Sql3);
						$Stmt3->bindParam(':ClassID', $ClassID);
						$Stmt3->execute();
						$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
						$Row3 = $Stmt3->fetch();
						$Stmt3 = null;
						$AssmtStudentLeveltestScoreID = $Row3["AssmtStudentLeveltestScoreID"];
					}else{
						$Sql3 = "select AssmtStudentDailyScoreID from AssmtStudentDailyScores where ClassID=:ClassID";
						$Stmt3 = $DbConn->prepare($Sql3);
						$Stmt3->bindParam(':ClassID', $ClassID);
						$Stmt3->execute();
						$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
						$Row3 = $Stmt3->fetch();
						$Stmt3 = null;
						$AssmtStudentDailyScoreID = $Row3["AssmtStudentDailyScoreID"];
					}


					if ($ClassProductID==2){//레벨테스트
						if (!$AssmtStudentLeveltestScoreID){
				?>
							<a class="md-btn md-btn-primary md-btn-mini md-btn-wave-light" href="javascript:OpenStudentScoreLeveltestForm(<?=$ClassID?>);" style="background-color:#9467BA;width:120px;"><?=$수업평가[$LangID]?></a>
				<?
						}else{
				?>
							<a class="md-btn md-btn-success md-btn-mini md-btn-wave-light" href="javascript:OpenStudentScoreLeveltestReport(<?=$AssmtStudentLeveltestScoreID?>);" style="background-color:#8080C0;width:120px;"><?=$평가보고서[$LangID]?></a>
							<br>
							<a class="md-btn md-btn-primary md-btn-mini md-btn-wave-light" href="javascript:OpenStudentScoreLeveltestForm(<?=$ClassID?>);" style="background-color:#9467BA;margin-top:3px;width:120px;"><?=$수업평가[$LangID]?></a>
				<?
						}

					}else{
						if (!$AssmtStudentDailyScoreID){
				?>
							<a class="md-btn md-btn-primary md-btn-mini md-btn-wave-light" href="javascript:OpenStudentScoreDailyForm(<?=$ClassID?>);" style="background-color:#9467BA;width:120px;"><?=$수업평가[$LangID]?></a>
				<?
						}else{
				?>
							<a class="md-btn md-btn-success md-btn-mini md-btn-wave-light" href="javascript:OpenStudentScoreDailyReport(<?=$ClassID?>);" style="background-color:#8080C0;width:120px;"><?=$평가보고서[$LangID]?></a>
							<br>
							<a class="md-btn md-btn-primary md-btn-mini md-btn-wave-light" href="javascript:OpenStudentScoreDailyForm(<?=$ClassID?>);" style="background-color:#9467BA;margin-top:3px;width:120px;"><?=$수업평가[$LangID]?></a>
				<?
						}

					}

					if ($LastStudyClassCount>0 && $LastStudyClassCount % 8 == 0){//월간보고서


						$Sql3 = "select AssmtStudentMonthlyScoreID from AssmtStudentMonthlyScores where ClassID=:ClassID";
						$Stmt3 = $DbConn->prepare($Sql3);
						$Stmt3->bindParam(':ClassID', $ClassID);
						$Stmt3->execute();
						$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
						$Row3 = $Stmt3->fetch();
						$Stmt3 = null;
						$AssmtStudentMonthlyScoreID = $Row3["AssmtStudentMonthlyScoreID"];


						if (!$AssmtStudentMonthlyScoreID){
				?>
							<br>
							<a class="md-btn md-btn-primary md-btn-mini md-btn-wave-light" href="javascript:OpenStudentScoreMonthlyForm(<?=$ClassID?>);" style="background-color:#3859AF;margin-top:3px;width:120px;"><?=$정기평가[$LangID]?></a>
							
				<?
						}else{
				?>
							<br>
							<a class="md-btn md-btn-success md-btn-mini md-btn-wave-light" href="javascript:OpenStudentScoreMonthlyReport(<?=$AssmtStudentMonthlyScoreID?>);" style="background-color:#004080;margin-top:3px;width:120px;"><?=$정기평가보고서[$LangID]?></a>
							<br>
							<a class="md-btn md-btn-primary md-btn-mini md-btn-wave-light" href="javascript:OpenStudentScoreMonthlyForm(<?=$ClassID?>);" style="background-color:#3859AF;margin-top:3px;width:120px;"><?=$정기평가[$LangID]?></a>
				<?

						}
					}
				}else{
				?>
				-
				<?
				}
				?>
			<?}?>
		<?}?>
	</td>

	<? // 타입에 따른 ( mangoi, jt ) 값 정의
	$BookScanValue = "";
	if($BookSystemType==0) {
		if($BookScanID!="") {
			$BookScanValue = $BookScanImageFileName;
		}
	} else if($BookSystemType==1) {
		$BookScanValue = $BookWebookUnitID;
	}
	?>

	<td class="uk-text-nowrap uk-table-td-center">
		<?=$StrClassAttendState?>
		<?if ($ClassAttendState>=4 && $ClassAttendState<=8) {?>
			<?if ($ClassAttendStateMsg!=""){?>
				<br>
				<?=$ClassAttendStateMsg?>
			<?}?>

			<?if ($_LINK_ADMIN_LEVEL_ID_<=4){?>
				<br>
				<a href="javascript:ClassReturn(<?=$ClassID?>, <?=$ClassAttendState?>)" class="md-btn md-btn-primary md-btn-mini md-btn-wave-light" style="background-color:#719D40;margin-top:10px;width:160px;"><?=$복원하기[$LangID]?></a>
			<?}?>
			
		<?}else if ($ClassProductID==1 && $ClassOrderSlotType==2){?>
			
			<?
			$StrDeleteBtn = "<?=$삭제[$LangID]?>";
			if ($ClassOrderSlotType2==4){
				$StrDeleteBtn = "<?=$삭제_연기수업[$LangID]?>";
			}else if ($ClassOrderSlotType2==5){
				$StrDeleteBtn = "<?=$삭제_연기수업[$LangID]?>";
			}else if ($ClassOrderSlotType2==8){
				$StrDeleteBtn = "<?=$삭제_강사변경수업[$LangID]?>";
			}else if ($ClassOrderSlotType2==10000){
				$StrDeleteBtn = "<?=$삭제_보강수업[$LangID]?>";
			}else if ($ClassOrderSlotType2==20000){
				$StrDeleteBtn = "<?=$교재판매설정[$LangID]?>";//안나옴 ClassOrderSlotType=1 일때만 생성됨
			}		
			?>
			
			<?if ($_LINK_ADMIN_LEVEL_ID_<=4){?>
				<br>
				<a class="md-btn md-btn-gray md-btn-mini md-btn-wave-light" href="javascript:DeleteClassOrderSlot('<?=$SelectDate?>', <?=$ClassOrderID?>, <?=$ClassID?>, <?=$ClassOrderTimeTypeID?>, <?=$TeacherID?>, <?=(int)$StudyTimeHour?>, <?=(int)$StudyTimeMinute?>);"  style="margin-top:10px;background-color:#8080C0;color:#ffffff;width:160px;"><?=$StrDeleteBtn?></a>
			<?}?>

		<?}?>
	</td>


	<td class="uk-text-nowrap uk-table-td-center">
	<?if($BookScanValue=="") { ?>
		-
	<? } else { ?>
		<a href="javascript:OpenScan('<?=$BookScanValue?>', <?=$ClassID?>, <?=$BookSystemType?>);">
			<i class="material-icons">
				book
			</i>
		</a>
	<? } ?>
	</td>

	<td class="uk-text-nowrap uk-table-td-center">
	<!-- Video A -->
	<?if($BookVideoID==0) { ?>
		-
	<? } else { ?>
		<? if($BookVideoCode=="" ) { ?>
			-
		<? } else { ?>
			<a href="javascript:OpenVideo(<?=$BookVideoType?>, '<?=$BookVideoCode?>', <?=$ClassID?>, 1);">
				<i class="material-icons">
					video_library
				</i>
			</a>
		<? } ?>
	<? } ?>
	<!-- Video B -->
	<?if($BookVideoID==0) { ?>
		-
	<? } else { ?>
		<? if($BookVideoCode2=="" ) { ?>
			-
		<? } else { ?>
			<a href="javascript:OpenVideo(<?=$BookVideoType2?>, '<?=$BookVideoCode2?>', <?=$ClassID?>, 2);">
				<i class="material-icons">
					video_library
				</i>
			</a>
		<? } ?>
	<? } ?>
	</td>



	<td class="uk-text-nowrap uk-table-td-center">
	<?if($BookQuizID==0) { ?>
		-
	<? } else { ?>
		<a href="javascript:OpenQuiz(<?=$BookQuizID?>);">
			<i class="material-icons">
				create
			</i>	
		</a>
	<? } ?>
	</td>
	<?
	//=============================== 수업 상태에 따른 버튼 출력 ============================
	if ($GroupListCount==1){?>
	<td class="uk-text-nowrap uk-table-td-center" rowspan="<?=$GroupRowCount?>" id="TrClsEnter_<?=$TeacherID?>_<?=$SelectYear?>_<?=(int)$SelectMonth?>_<?=(int)$SelectDay?>_<?=(int)$StudyTimeHour?>_<?=(int)$StudyTimeMinute?>">
	- 
	</td>
	<?
	}

	$StrClassEnterBtn = "-";
	//이전수업 미평가 처리 --> 아래 주석을 지우준다. javascript 도 처리해 준다.

	if ($TodayIsEduCenterHoliday==0){

		//if ($LastClassID!=0 && $LastAssmtStudentDailyScoreID==0){//이전 수업이 있고 피드백을 하지 않은 경우
		//	$LastClassNotAssmtCount++;
		//	$LastClassNotAssmtClasses = $LastClassNotAssmtClasses . $TeacherID."_".$SelectYear."_".(int)$SelectMonth."_".(int)$SelectDay."_".(int)$StudyTimeHour."_".(int)$StudyTimeMinute . "|";
		//}else{
		if($_LINK_ADMIN_LEVEL_ID_ <=13 && $_LINK_ADMIN_LEVEL_ID_>=9) { // 지사 나 센터일 경우 옵저버로 입장

			if ($ClassID!=0 && $ClassState!=2 && $ClassAttendState<4 && $ClassLinkType==1){
				$StrClassEnterBtn = "<a style=\\\"background-color:#808000;\\\" class=\\\"md-btn md-btn-success md-btn-mini md-btn-wave-light\\\" href=\\\"javascript:OpenClassSh('".$ClassID."', '".$CommonShClassCode."', 2, '".$_LINK_ADMIN_NAME_."', '".$_LINK_ADMIN_LOGIN_ID_."');\\\">수업참관 (SH)</a>";
				//$StrClassEnterBtn .= "<a style=\\\"background-color:#80FF00;\\\" class=\\\"md-btn md-btn-success md-btn-mini md-btn-wave-light\\\" href=\\\"javascript:CopyScanLink('".$BookScanValue."', '".$ClassID."', '".$BookSystemType."', '".$MemberLoginID."');\\\">교재링크</a>";
			}

		} else if($_LINK_ADMIN_LEVEL_ID_==15) { // 강사일 경우 수업입장

			if ($ClassID!=0 && $ClassState!=2 && $ClassAttendState<4){
				if ($ClassLinkType==1){
					$StrClassEnterBtn = "<a class=\\\"md-btn md-btn-success md-btn-mini md-btn-wave-light\\\" href=\\\"javascript:OpenClassSh('".$ClassID."', '".$CommonShClassCode."', 1, '".$TeacherName."', '".$TeacherLoginID."');\\\">".$수업입장[$LangID]." (SH)</a>";
					//$StrClassEnterBtn .= "<a style=\\\"background-color:#80FF00;\\\" class=\\\"md-btn md-btn-success md-btn-mini md-btn-wave-light\\\" href=\\\"javascript:CopyScanLink('".$BookScanValue."', '".$ClassID."', '".$BookSystemType."', '".$MemberLoginID."');\\\">교재링크</a>";
				}else{
					$StrClassEnterBtn = "<a class=\\\"md-btn md-btn-primary md-btn-mini md-btn-wave-light\\\" href=\\\"javascript:OpenClassCiCheck('".$ClassID."', '".$CommonCiTelephoneTeacher."', '".$CommonCiTelephoneStudent."', 2, '".$TeacherName."', 'MangoiClass_".$ClassID."');\\\">".$수업입장[$LangID]." (CI)</a>";
					//$StrClassEnterBtn .= "<a style=\\\"background-color:#80FF00;\\\" class=\\\"md-btn md-btn-success md-btn-mini md-btn-wave-light\\\" href=\\\"javascript:CopyScanLink('".$BookScanValue."', '".$ClassID."', '".$BookSystemType."', '".$MemberLoginID."');\\\">".$교재링크[$LangID]."</a>";
				}
			}

		}else if($_LINK_ADMIN_LEVEL_ID_ < 9) { // 지사보다 위인 관리자들은 옵저버, 수업입장 둘다 출력
			if ($ClassID!=0 && $ClassState!=2 && $ClassAttendState<4){
				if ($ClassLinkType==1){
					$StrClassEnterBtn = "<a class=\\\"md-btn md-btn-success md-btn-mini md-btn-wave-light\\\" href=\\\"javascript:OpenClassSh('".$ClassID."', '".$CommonShClassCode."', 1, '".$TeacherName."', '".$TeacherLoginID."');\\\">".$수업입장[$LangID]." (SH)</a>";
					$StrClassEnterBtn .= "<a style=\\\"background-color:#808000;\\\" class=\\\"md-btn md-btn-success md-btn-mini md-btn-wave-light\\\" href=\\\"javascript:OpenClassSh('".$ClassID."', '".$CommonShClassCode."', 2, '".$_LINK_ADMIN_NAME_."', '".$_LINK_ADMIN_LOGIN_ID_."');\\\">".$수업참관_SH[$LangID]."</a>";
					//$StrClassEnterBtn .= "<a style=\\\"background-color:#80FF00;\\\" class=\\\"md-btn md-btn-success md-btn-mini md-btn-wave-light\\\" href=\\\"javascript:CopyScanLink('".$BookScanValue."', '".$ClassID."', '".$BookSystemType."', '".$MemberLoginID."');\\\">".$교재링크[$LangID]."</a>";
				}else{
					$StrClassEnterBtn = "<a class=\\\"md-btn md-btn-primary md-btn-mini md-btn-wave-light\\\" href=\\\"javascript:OpenClassCiCheck('".$ClassID."', '".$CommonCiTelephoneTeacher."', '".$CommonCiTelephoneStudent."', 2, '".$TeacherName."', 'MangoiClass_".$ClassID."');\\\">".$수업입장[$LangID]." (CI)</a>";
					//$StrClassEnterBtn .= "<a style=\\\"background-color:#80FF00;\\\" class=\\\"md-btn md-btn-success md-btn-mini md-btn-wave-light\\\" href=\\\"javascript:CopyScanLink('".$BookScanValue."', '".$ClassID."', '".$BookSystemType."', '".$MemberLoginID."');\\\">".$교재링크[$LangID]."</a>";
				}

			}

		}
			$OpenClassCount++;

		//}
	
	}
	//=============================== 수업 상태에 따른 버튼 출력 ============================
	?>

	<?if ($_LINK_ADMIN_LEVEL_ID_<=4) {?>
		<td class="uk-text-nowrap uk-table-td-center"><?=$StrMemberChangeTeacher?></th>
	<?}?>

	<?if ($GroupListCount==1){?>
	<td class="uk-text-nowrap uk-table-td-center" id="TrClsReSet_<?=$TeacherID?>_<?=$SelectYear?>_<?=(int)$SelectMonth?>_<?=(int)$SelectDay?>_<?=(int)$StudyTimeHour?>_<?=(int)$StudyTimeMinute?>" rowspan="<?=$GroupRowCount?>">
		
		<?
		if ($ClassID==0){
		?>
			-
			<!--
			<a class="md-btn md-btn-gray md-btn-mini md-btn-wave-light" href="javascript:OpenClassReg(<?=$ClassOrderID?>, <?=$SelectYear?>, <?=(int)$SelectMonth?>, <?=(int)$SelectDay?>, <?=(int)$StudyTimeHour?>, <?=(int)$StudyTimeMinute?>, <?=$ClassOrderTimeTypeID?>, <?=$TeacherID?>, <?=$MemberID?>, <?=$ClassMemberType?>, <?=$LastClassID?>, <?=$LastAssmtStudentDailyScoreID?>, 2, <?=$GroupRowCount?>, <?=$ClassProductID?>, <?=$ClassOrderSlotType?>, <?=$StudyTimeWeek?>);"><?=$수업연기[$LangID]?></a>
			<br>
			<a class="md-btn md-btn-gray md-btn-mini md-btn-wave-light" href="javascript:OpenClassReg(<?=$ClassOrderID?>, <?=$SelectYear?>, <?=(int)$SelectMonth?>, <?=(int)$SelectDay?>, <?=(int)$StudyTimeHour?>, <?=(int)$StudyTimeMinute?>, <?=$ClassOrderTimeTypeID?>, <?=$TeacherID?>, <?=$MemberID?>, <?=$ClassMemberType?>, <?=$LastClassID?>, <?=$LastAssmtStudentDailyScoreID?>, 3, <?=$GroupRowCount?>, <?=$ClassProductID?>, <?=$ClassOrderSlotType?>, <?=$StudyTimeWeek?>);" style="margin-top:10px;background-color:#f1f1f1;"><?=$강사변경[$LangID]?></a>
			-->
		<?
		}else{
		?>
			<?if ($ClassAttendState==4 || $ClassAttendState==5 || $ClassAttendState==6 || $ClassAttendState==7 || $ClassAttendState==8 || $ClassState==2){?>
				-
			<?}else{?>
				<?if ($ClassState!=2){ //0:미등록 전 1:등록완료 2:수업완료?>

					<?if ($ListSelectResetDate!=""){?>
						<a class="md-btn md-btn-gray md-btn-mini md-btn-wave-light" href="javascript:OpenResetDateForm(<?=$ClassID?>, <?=$GroupRowCount?>, <?=$ClassMemberType?>, <?=(int)$StudyTimeHour?>, <?=(int)$StudyTimeMinute?>);"><?=$수업연기[$LangID]?></a>
						<a class="md-btn md-btn-gray md-btn-mini md-btn-wave-light" href="javascript:OpenResetDateForm2(<?=$ClassID?>, <?=$ClassOrderID?>, <?=$MemberID?>, <?=$TeacherID?>, <?=$GroupRowCount?>, <?=$ClassMemberType?>, <?=(int)$StudyTimeHour?>, <?=(int)$StudyTimeMinute?>);"  style="background-color:#f1f1f1;"><?=$강사변경[$LangID]?></a>
						<?if ($ClassProductID==1 && $ClassOrderSlotType==1){?>
							<br>
							<a class="md-btn md-btn-gray md-btn-mini md-btn-wave-light" href="javascript:OpenResetDateForm3(<?=$ClassID?>, <?=$ClassOrderID?>, <?=$MemberID?>, <?=$TeacherID?>, <?=$GroupRowCount?>, <?=$ClassMemberType?>, <?=(int)$StudyTimeHour?>, <?=(int)$StudyTimeMinute?>);"  style="margin-top:10px;background-color:#C7C7C7;"><?=$보강등록[$LangID]?></a>
							<a class="md-btn md-btn-gray md-btn-mini md-btn-wave-light" href="javascript:OpenResetDateForm4(<?=$ClassID?>, <?=$ClassOrderID?>, <?=$MemberID?>, <?=$TeacherID?>, <?=$GroupRowCount?>, <?=$ClassMemberType?>, <?=$SelectYear?>, <?=(int)$SelectMonth?>, <?=(int)$SelectDay?>, <?=(int)$StudyTimeHour?>, <?=(int)$StudyTimeMinute?>, <?=$StudyTimeWeek?>, <?=$ClassOrderSlotEndDate?>);"  style="margin-top:10px;background-color:#999999;color:#ffffff"><?=$스케줄변경[$LangID]?></a>
						<?}?>

					<?}else{?>
					-
					<?}?>
				
				<?}else{?>
				-
				<?}?>
			<?}?>
		<?
		}
		?>
	</td>
	<?}?>

	<!--
	<td class="uk-text-nowrap uk-table-td-center" id="TrClsTotSet_<?=$ClassOrderID?>_<?=$SelectYear?>_<?=(int)$SelectMonth?>_<?=(int)$SelectDay?>_<?=(int)$StudyTimeHour?>_<?=(int)$StudyTimeMinute?>">
			<?if ($ClassProductID==1){?>
			<a class="md-btn md-btn-success md-btn-mini md-btn-wave-light" style="background-color:#0080C0;" href="javascript:OpenClassDetailList(<?=$ClassOrderID?>);"><?=$전체수업설정[$LangID]?></a>
			<?}else{?>
			-
			<?}?>
	</td>
	-->

	<td class="uk-text-nowrap uk-table-td-center" id="TrClsSet_<?=$ClassOrderID?>_<?=$SelectYear?>_<?=(int)$SelectMonth?>_<?=(int)$SelectDay?>_<?=(int)$StudyTimeHour?>_<?=(int)$StudyTimeMinute?>">

		<?if ($TodayIsEduCenterHoliday==1){?>
			-
		<?}else{?>
			<?
			if ($ClassID==0){
				
			?>
				<?
				if ($ClassAttendState==4 || $ClassAttendState==5 || $ClassAttendState==6 || $ClassAttendState==7 || $ClassAttendState==8){
				?>
					-
				<?
				}else{
				?>
					<a class="md-btn md-btn-danger md-btn-mini md-btn-wave-light" href="javascript:OpenClassReg(<?=$ClassOrderID?>, <?=$SelectYear?>, <?=(int)$SelectMonth?>, <?=(int)$SelectDay?>, <?=(int)$StudyTimeHour?>, <?=$StudyTimeMinute?>, <?=$ClassOrderTimeTypeID?>, <?=$TeacherID?>, <?=$MemberID?>, <?=$ClassMemberType?>, <?=$LastClassID?>, <?=$LastAssmtStudentDailyScoreID?>, 1, <?=$GroupRowCount?>, <?=$ClassProductID?>, <?=$ClassOrderSlotType?>, <?=$StudyTimeWeek?>, <?=$ClassOrderPayID?>, '<?=$ClassOrderSlotEndDate?>');"><?=$수업등록[$LangID]?></a>
				<?
								
					//$StrClassRegScript = $StrClassRegScript . "OpenClassReg(".$ClassOrderID.", ".$SelectYear.", ".(int)$SelectMonth.", ".(int)$SelectDay.", ".(int)$StudyTimeHour.", ".(int)$StudyTimeMinute.", ".$ClassOrderTimeTypeID.", ".$TeacherID.", ".$MemberID.", ".$ClassMemberType.", ".$LastClassID.", ".$LastAssmtStudentDailyScoreID.", 1, ".$GroupRowCount.", ".$ClassProductID.", ".$ClassOrderSlotType.", ".$StudyTimeWeek.");";
				}
				?>
			<?
			}else{
			?>
				<?if ($ClassAttendState==4 || $ClassAttendState==5 || $ClassAttendState==6 || $ClassAttendState==7 || $ClassAttendState==8){?>
					-
				<?}else{?>
					<a class="md-btn md-btn-warning md-btn-mini md-btn-wave-light" href="javascript:OpenClassSetup(<?=$ClassID?>);"><?=$수업설정[$LangID]?></a>
				<?}?>
			<?
				$RegClassCount++;
			}
			?>
		<?}?>

	</td>



	<!--
	<td class="uk-text-nowrap " style="display:none;">
		클래스인코스ID<input type="text" id="CommonCiCourseID_<?=$ClassID?>" value="<?=$CommonCiCourseID?>" readonly style="background-color:#f1f1f1;">
		클클스인클래스ID<input type="text" id="CommonCiClassID_<?=$ClassID?>" value="<?=$CommonCiClassID?>" readonly style="background-color:#f1f1f1;">
		강사클래스인ID<input type="text" id="CommonCiTelephoneTeacher_<?=$ClassID?>" value="<?=$CommonCiTelephoneTeacher?>">
		학생클래스인ID<input type="text" id="CommonCiTelephoneStudent_<?=$ClassID?>" value="<?=$CommonCiTelephoneStudent?>">
	</td>
	<td class="uk-text-nowrap uk-table-td-center" style="display:none;">
		<?if ($ClassAttendState==4 || $ClassAttendState==5 || $ClassAttendState==6 || $ClassAttendState==7 || $ClassAttendState==8){?>
			<?=$StrClassAttendState?>
		<?}else{?>
			<a class="md-btn md-btn-success md-btn-mini md-btn-wave-light" href="javascript:OpenClassSh('<?=$ClassID?>', '<?=$CommonShClassCode?>', 0, '<?=$MemberName?>', '<?=$MemberLoginID?>');">새하</a>
			<a class="md-btn md-btn-primary md-btn-mini md-btn-wave-light" href="javascript:OpenClassCiCheck('<?=$ClassID?>', '<?=$CommonCiTelephoneTeacher?>', '<?=$CommonCiTelephoneStudent?>', 1, '<?=$MemberName?>', 'MangoiClass_<?=$ClassID?>');">클래스인</a>
		<?}?>
	</td>
	-->
</tr>