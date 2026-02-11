<tr style="background-color:<?=$GroupTrColor?>;">





	<td class="uk-text-nowrap uk-table-td-center">

		<?if ($_LINK_ADMIN_LEVEL_ID_==15) {?>
			<?=$CenterLoginID?>
		<?}else{?>
			<?=$CenterName?>
		<?}?>
		<br>

		<?=$MemberName?> <span style="color:#006BD7;"><?=$MemberLoginID?></span>

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
	</td>
 


	<?if ($_LINK_ADMIN_LEVEL_ID_!=15) {?>
		<td class="uk-text-nowrap uk-table-td-center"><?=$TeacherName?></td>
	<?}?>





	<td class="uk-text-nowrap uk-table-td-center">
		<?=$StrClassAttendState?>
		<?if ($ClassAttendState>=4 && $ClassAttendState<=8) {?>
			<?if ($ClassAttendStateMsg!=""){?>
				<br>
				<?=$ClassAttendStateMsg?>
			<?}?>
			<br>
			<a href="javascript:ClassReturn(<?=$ClassID?>, <?=$ClassAttendState?>)" class="md-btn md-btn-primary md-btn-mini md-btn-wave-light" style="background-color:#719D40;margin-top:10px;width:160px;"><?=$복원하기[$LangID]?></a>
		<?}else if ($ClassProductID==1 && $ClassOrderSlotType==2){?>
			<br>
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
				$StrDeleteBtn = "<?=$삭제_스케줄변경[$LangID]?>";//안나옴 ClassOrderSlotType=1 일때만 생성됨
			}			
			?>
			
			<a class="md-btn md-btn-gray md-btn-mini md-btn-wave-light" href="javascript:DeleteClassOrderSlot('<?=$SelectDate?>', <?=$ClassOrderID?>, <?=$ClassID?>, <?=$ClassOrderTimeTypeID?>, <?=$TeacherID?>, <?=(int)$StudyTimeHour?>, <?=(int)$StudyTimeMinute?>);"  style="margin-top:10px;background-color:#8080C0;color:#ffffff;width:160px;"><?=$StrDeleteBtn?></a>
		<?}?>
	</td>



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



	<td class="uk-text-nowrap uk-table-td-center" id="TrClsSet_<?=$ClassOrderID?>_<?=$SelectYear?>_<?=(int)$SelectMonth?>_<?=(int)$SelectDay?>_<?=(int)$StudyTimeHour?>_<?=(int)$StudyTimeMinute?>" style="display:none;">
		<?
		if ($ClassID==0){
			if ($ClassAttendState==4 || $ClassAttendState==5 || $ClassAttendState==6 || $ClassAttendState==7 || $ClassAttendState==8){

			}else{
				$StrClassRegScript = $StrClassRegScript . "OpenClassReg(".$ClassOrderID.", ".$SelectYear.", ".(int)$SelectMonth.", ".(int)$SelectDay.", ".(int)$StudyTimeHour.", ".(int)$StudyTimeMinute.", ".$ClassOrderTimeTypeID.", ".$TeacherID.", ".$MemberID.", ".$ClassMemberType.", ".$LastClassID.", ".$LastAssmtStudentDailyScoreID.", 1, ".$GroupRowCount.", ".$ClassProductID.", ".$ClassOrderSlotType.", ".$StudyTimeWeek.", ".$ClassOrderSlotEndDate.");";
			}
		}else{
			$RegClassCount++;
		}
		?>
		
	</td>

	<?if ($_LINK_ADMIN_LEVEL_ID_<=4) {?>
		<?if ($GroupListCount==1){?>
		<td class="uk-text-nowrap uk-table-td-center" rowspan="<?=$GroupRowCount?>">
			<a class="md-btn md-btn-gray md-btn-mini md-btn-wave-light" href="javascript:ResetClassOrder(<?=$ClassMemberTypeGroupID?>, <?=$ClassOrderID?>);"  style="margin-top:10px;background-color:#C7C7C7;"><?=$설정변경[$LangID]?></a>
		</th>
		<?}?>
		<td class="uk-text-nowrap uk-table-td-center"><?=$StrMemberChangeTeacher?></th>
	<?}?>

</tr>