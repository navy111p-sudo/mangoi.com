<script>
function OpenBranchSummary(BranchID){
	openurl = "../summary_branch.php?BranchID="+BranchID
	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "850"
		,maxHeight: "750"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	});
}

function OpenCenterSummary(CenterID){
	openurl = "../summary_center.php?CenterID="+CenterID
	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "850"
		,maxHeight: "750"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	});
}


function OpenStudentForm(MemberID){
	//openurl = "student_form_pop.php?MemberID="+MemberID+"&PageTabID=1";
	openurl = "../summary_student.php?MemberID="+MemberID
	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "850"
		,maxHeight: "750"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	});
}

function OpenStudentFormPop(MemberID){
	openurl = "student_form_pop.php?MemberID="+MemberID+"&PageTabID=1";
	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "1100"
		,maxHeight: "800"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	});
}


function OpenStudentCalendar(MemberID){
    var OpenUrl = "../pop_study_calendar.php?MemberID="+MemberID;

    $.colorbox({    
        href:OpenUrl
        ,width:"95%" 
        ,height:"95%"
        ,maxWidth: "1000"
        ,maxHeight: "850"
        ,title:""
        ,iframe:true 
        ,scrolling:true
        //,onClosed:function(){location.reload(true);}
        //,onComplete:function(){alert(1);}
    }); 
}
</script>



<link rel="stylesheet" href="../js/colorbox/example2/colorbox.css" />
<script src="../js/colorbox/jquery.colorbox.js"></script>
<script>
jQuery(document).ready(function(){
$(document).bind('cbox_open', function() {
	$('html').css({ overflow: 'hidden' });
}).bind('cbox_closed', function() {
	$('html').css({ overflow: '' });
});
});

/*
var cboxOptions = {
  width: '95%',
  height: '95%',
  maxWidth: '850px',
  maxHeight: '750px',
}

$('.cbox-link').colorbox(cboxOptions);

$(window).resize(function(){
	$.colorbox.resize({
	  width: window.innerWidth > parseInt(cboxOptions.maxWidth) ? cboxOptions.maxWidth : cboxOptions.width,
	  height: window.innerHeight > parseInt(cboxOptions.maxHeight) ? cboxOptions.maxHeight : cboxOptions.height
	});
});
*/
</script>

<!--<script src="../js/mvapi.js?ver=6"></script>-->
<script src="../js/mvapi.min.js"></script>


<?

$MasterMessageAlert = isset($MasterMessageAlert) ? $MasterMessageAlert : 1;

if ($_LINK_ADMIN_LEVEL_ID_<=4 && $MasterMessageAlert==1){

	$Sql = "select 
					count(*) TotalRowCount 
			from MasterMessages A 
			where 
				A.MasterMessageID not in (select MasterMessageID from MasterMessageReads where MemberID=".$_LINK_ADMIN_ID_.")
			";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$TotalRowCount = $Row["TotalRowCount"];

	if ($TotalRowCount>0){
?>
<script>	
/*
	UIkit.modal.confirm(
		'확인할 메시지가 (<?=$TotalRowCount?>)건 있습니다. 확인하시겠습니까?', 
		function(){ 
			location.href = "master_message_list.php";
		}
	);
*/
</script>
<?
	}
}

?>


<?
$TeacherMessageAlert = isset($TeacherMessageAlert) ? $TeacherMessageAlert : 1;

if ($_LINK_ADMIN_LEVEL_ID_==15 && $TeacherMessageAlert==1){
?>
<script>
function CheckNewTeacherMsg(){
		
		url = "ajax_check_new_teacher_msg.php";

		//location.href = url + "?ClassOrderWeekCountID="+ClassOrderWeekCountID+"&ClassOrderTimeTypeID="+ClassOrderTimeTypeID+"&ClassOrderStartDate="+ClassOrderStartDate+"&ClassProductID="+ClassProductID+"&SelectSlotCode="+SelectSlotCode+"&SelectStudyTimeCode="+SelectStudyTimeCode;
		$.ajax(url, {
			data: {
			},
			success: function (data) {
				if (data.MsgCount>=1){
					alert(data.Msg);
				}
			},
			error: function () {

			}
		});
}

//각 강사별로 수업 시간 10분전 체크
function Check10MinuteMsg(){
		
		url = "ajax_check_class_time_send_msg.php";

		//location.href = url + "?ClassOrderWeekCountID="+ClassOrderWeekCountID+"&ClassOrderTimeTypeID="+ClassOrderTimeTypeID+"&ClassOrderStartDate="+ClassOrderStartDate+"&ClassProductID="+ClassProductID+"&SelectSlotCode="+SelectSlotCode+"&SelectStudyTimeCode="+SelectStudyTimeCode;
		$.ajax(url, {
			data: {
			},
			success: function (data) {
				if (data.EnableClassTime == 1){
					alert("It is 10 10 minutes before class starts.");
				}
			},
			error: function () {
				
			}
		});
}




var MyMsgInterval;
var Check10Interval;

CheckNewTeacherMsg();

<?if ($_LINK_ADMIN_LEVEL_ID_==15){?>
	MyMsgInterval = setInterval(CheckNewTeacherMsg, 10000);
	Check10Interval = setInterval(Check10MinuteMsg, 120000);
<?}?>


</script>

<?
}

// 관리자들에게 강사가 2분이 지나도 수업에 입장하지 않은 경우 팝업을 띄워 준다. 
//if ($_LINK_ADMIN_LEVEL_ID_==0 or $_LINK_ADMIN_LEVEL_ID_==1 or $_LINK_ADMIN_LEVEL_ID_==3 or $_LINK_ADMIN_LEVEL_ID_==4 
//	or $_LINK_ADMIN_LEVEL_ID_==6 or $_LINK_ADMIN_LEVEL_ID_==7)

// 관리자에게 강사가 2분이 지나도 수업에 입장하지 않은 경우 팝업을 띄워 준다. 
if ($_LINK_ADMIN_LEVEL_ID_<=2)
{?>
	<script>
		var Check3Interval;
		//3분이 지나도 수업 입장하지 않은 강사 체크
		function Check3MinuteMsg(){
				
				url = "ajax_check_over_class_time.php";

				//location.href = url + "?ClassOrderWeekCountID="+ClassOrderWeekCountID+"&ClassOrderTimeTypeID="+ClassOrderTimeTypeID+"&ClassOrderStartDate="+ClassOrderStartDate+"&ClassProductID="+ClassProductID+"&SelectSlotCode="+SelectSlotCode+"&SelectStudyTimeCode="+SelectStudyTimeCode;
				$.ajax(url, {
					data: {
					},
					success: function (data) {
						if (data.Check3Minute == 1){
							var Teachers = data.MemberName.substr(0,data.MemberName.length-2)
							alert("강사 "+Teachers +" 가 아직 수업에 입장하지 않았습니다!!");
						}
					},
					error: function () {
						
					}
				});
		}

		// setInterval 함수를 이용해서 주기적으로 함수 호출
		Check3Interval = setInterval(Check3MinuteMsg, 120000);
	</script>	
<?}?>

<script>

// 강사 출석 체크
function CheckTeacherAttendance(TeacherID){
	openurl = "./popup_check_teacher_attendance_form.php?TeacherID="+TeacherID;
	$.colorbox({	
		href:openurl
		,width:"30%" 
		,height:"40%"
		,maxWidth: "850"
		,maxHeight: "750"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	})
}





<?
if($_LINK_ADMIN_LEVEL_ID_==15) {

	$SelectDate = date("Y-m-d");
	$SelectDateWeek = date("w", strtotime($SelectDate));
	$TeacherIDIsHoliday = "|";
	$EduCenterID = 1;

	$Sql_edu = "SELECT  A.EduCenterHolidayName  from EduCenterHolidays A where A.EduCenterHolidayDate=:EduCenterHolidayDate and A.EduCenterID=:EduCenterID and A.EduCenterHolidayState=1 ";
	$Stmt_edu = $DbConn->prepare($Sql_edu);
	$Stmt_edu->bindParam(':EduCenterHolidayDate', $SelectDate);
	$Stmt_edu->bindParam(':EduCenterID', $EduCenterID);
	$Stmt_edu->execute();
	$Stmt_edu->setFetchMode(PDO::FETCH_ASSOC);
	$Row_edu = $Stmt_edu->fetch();
	$Stmt_edu = null;

	$EduCenterHolidayName = $Row_edu["EduCenterHolidayName"];

	$Sql = " SELECT A.EduCenterHoliday0, A.EduCenterHoliday1, A.EduCenterHoliday2, A.EduCenterHoliday3, A.EduCenterHoliday4, A.EduCenterHoliday5, A.EduCenterHoliday6  from EduCenters A where A.EduCenterID=:EduCenterID and A.EduCenterState=1 ";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':EduCenterID', $EduCenterID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();

	$EduCenterHoliday[0] = $Row["EduCenterHoliday0"];
	$EduCenterHoliday[1] = $Row["EduCenterHoliday1"];
	$EduCenterHoliday[2] = $Row["EduCenterHoliday2"];
	$EduCenterHoliday[3] = $Row["EduCenterHoliday3"];
	$EduCenterHoliday[4] = $Row["EduCenterHoliday4"];
	$EduCenterHoliday[5] = $Row["EduCenterHoliday5"];
	$EduCenterHoliday[6] = $Row["EduCenterHoliday6"];

	if($EduCenterHoliday[$SelectDateWeek]==1) {
		$IsRegHoliday = 1;
	} else {
		$IsRegHoliday = 0;
	}

	$Sql = " SELECT A.TeacherID from TeacherHolidays A inner join Teachers B on A.TeacherID=B.TeacherID inner join TeacherGroups C on B.TeacherGroupID=C.TeacherGroupID where A.TeacherHolidayDate=:TeacherHolidayDate and C.EduCenterID=:EduCenterID and A.TeacherHolidayState=1 ";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TeacherHolidayDate', $SelectDate);
	$Stmt->bindParam(':EduCenterID', $EduCenterID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);

	while($Row = $Stmt->fetch() ) {
		$TeacherID = $Row["TeacherID"];
		$TeacherIDIsHoliday = $TeacherIDIsHoliday . $TeacherID . "|";
	}
	$Stmt = null;

	if($EduCenterHolidayName==null && $IsRegHoliday==0){


		if(strpos($TeacherIDIsHoliday, "|".$_LINK_ADMIN_TEACHER_ID_."|" )!==false) {
			// 강사가 휴일 이라면
			return false;
		} else {
			$Sql = "SELECT A.TeacherAttendanceDateTime from TeacherAttendances A where A.TeacherID=$_LINK_ADMIN_TEACHER_ID_ and A.CheckDate=date_format(now(), '%Y-%m-%d')";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);
			$Row = $Stmt->fetch();
			$TeacherAttendanceDateTime = $Row["TeacherAttendanceDateTime"];

			if($TeacherAttendanceDateTime=="" or $TeacherAttendanceDateTime==null) { ?>
				CheckTeacherAttendance(<?=$_LINK_ADMIN_TEACHER_ID_?>);
			<? }
		}
	} 
} 
?>
</script>
