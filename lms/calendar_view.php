
<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
?>
<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="ko"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="ko"> <!--<![endif]-->
<head>
<?
include_once('./includes/common_meta_tag.php');
include_once('./inc_header.php');
include_once('./inc_common_form_css.php');
?>
<!-- ============== only this page css ============== -->
<!-- dropify -->
<link rel="stylesheet" href="assets/skins/dropify/css/dropify.css">
<!-- ============== only this page css ============== -->
<!-- ============== common.css ============== -->
<link rel="stylesheet" type="text/css" href="css/common.css" />
<!-- ============== common.css ============== -->

</head>

<body class="disable_transitions sidebar_main_open sidebar_main_swipe">
<?php

$MainMenuID = 44;
$SubMenuID = 4401;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');

// 먼저 휴가 테이블을 검색하여 필리핀 강사의 휴가 내역중 캘린더에 입력하지 않은 것을 찾아서
// 입력해 준다.
// 제일 아래 두 SELECT 문은 승인완료한 수와 승인해야 할 수가 같은지를 비교한다. 
$Sql = "SELECT  DISTINCT  sh.StaffHolidayID, sh.StartDate, sh.EndDate,  dr.DocumentReportName, 
				dr.DocumentReportID, m.MemberName 
		from SpentHoliday sh  
			left join DocumentReports dr on sh.DocumentReportID = dr.DocumentReportID  AND dr.DocumentID =2 
			left join DocumentReportMembers drm on dr.DocumentReportID = drm.DocumentReportID 
			left join Members m on dr.MemberID = m.MemberID 
		where sh.CalendarUpdate = 0 and dr.MemberID IN (
				select MemberID from Members A
					inner join Staffs B on A.StaffID = B.StaffID 
				where B.StaffManageMent = 6
			)
		AND 	
		(select count(*) from DocumentReports D 
		left join DocumentReportMembers F on D.DocumentReportID = F.DocumentReportID 
		where D.DocumentReportID =dr.DocumentReportID)  
		=
		(select count(*) from DocumentReports H 
		left join DocumentReportMembers I on H.DocumentReportID = I.DocumentReportID 
		where I.DocumentReportMemberState = 1 and H.DocumentReportID = dr.DocumentReportID)
		";

$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
while ($Row = $Stmt->fetch()){
	$StaffHolidayID     	= $Row["StaffHolidayID"];
	$StartDate     			= $Row["StartDate"];
	$EndDate     			= $Row["EndDate"];
	$DocumentReportID   	= $Row["DocumentReportID"];
	$MemberName   			= $Row["MemberName"];
	$DocumentReportName		= $Row["DocumentReportName"];

	// 캘린더에 삽입해 준다.
	$Sql2 = "INSERT INTO calendar (title, description, start, end, allDay, color, url, category, repeat_type, user_id,  repeat_id, timezone)
				VALUES ('".$MemberName."', '".$DocumentReportName."','".$StartDate."','".$EndDate."','true', '#FF0000','false', '필리핀강사 휴가', 'no',0,0,'Asia/Seoul')
	";
	$Stmt2 = $DbConn->prepare($Sql2);
	$Stmt2->execute();

	// SpentHoliday 에 CalendarUpdate를 1로 세팅한다.
	$Sql3 = "UPDATE SpentHoliday SET CalendarUpdate = 1 WHERE StaffHolidayID = $StaffHolidayID";
	$Stmt3 = $DbConn->prepare($Sql3);
	$Stmt3->execute();
}


?>
<div id="page_content">
	<div id="page_content_inner">

		<div class="md-card" style="margin-bottom:10px;height:100%">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin="">
					
					<div class="uk-width-medium-1-1" >

                        <iframe src="/lms/calendar/index.php?adminLevel=<?=$_ADMIN_LEVEL_ID_?>" style="width:100%;height:1400px"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>    
   


<?
include_once('./inc_common_form_js.php');
?>
<!-- ==============  only this page js ============== -->
<!--  dropify -->
<script src="bower_components/dropify/dist/js/dropify.min.js"></script>
<!--  form file input functions -->
<script src="assets/js/pages/forms_file_input.min.js"></script>
<script>
$(function() {
	if(isHighDensity()) {
		$.getScript( "assets/js/custom/dense.min.js", function(data) {
			// enable hires images
			altair_helpers.retina_images();
		});
	}
	if(Modernizr.touch) {
		// fastClick (touch devices)
		FastClick.attach(document.body);
	}
});
$window.load(function() {
	// ie fixes
	altair_helpers.ie_fix();
});
</script>


<!-- ==============  only this page js ============== -->
<!-- ==============  common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ==============  common.js ============== -->

<script language="javascript">

function FormSubmit(){

	obj = document.RegForm.CountryName;
	if (obj.value==""){
		UIkit.modal.alert("국가명을 입력하세요.");
		obj.focus();
		return;
	}

	obj = document.RegForm.Currency;
	if (obj.value==""){
		UIkit.modal.alert("환율을 입력하세요.");
		obj.focus();
		return;
	}


	UIkit.modal.confirm(
		'<?=$저장하시겠습니까[$LangID]?>?', 
		function(){ 
				document.RegForm.action = "currency_action.php";
				document.RegForm.submit();
		}
	);

}


</script>



<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>