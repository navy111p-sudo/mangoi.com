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
?>

<?
include_once('./inc_common_list_css.php');
?>
<!-- ============== only this page css ============== -->

<!-- ============== only this page css ============== -->
<!-- ============== common.css ============== -->
<link rel="stylesheet" type="text/css" href="./css/common.css" />
<!-- ============== common.css ============== -->
</head>

<body class="disable_transitions sidebar_main_open sidebar_main_swipe" style="padding-top:0px;">
<?php
$ClassOrderID = isset($_REQUEST["ClassOrderID"]) ? $_REQUEST["ClassOrderID"] : "";
$ClassStudyType = isset($_REQUEST["ClassStudyType"]) ? $_REQUEST["ClassStudyType"] : "";
?>



<?php




$Sql = "
	select 
		count(*) as TotalRowCount
	from Classes A
            inner join Members B on A.MemberID=B.MemberID 
            inner join Centers C on B.CenterID=C.CenterID 
            inner join Branches D on C.BranchID=D.BranchID 
            inner join BranchGroups E on D.BranchGroupID=E.BranchGroupID 
            inner join Companies F on E.CompanyID=F.CompanyID 
            inner join Franchises G on F.FranchiseID=G.FranchiseID 
            inner join Teachers H on A.TeacherID=H.TeacherID 
			inner join Members I on H.TeacherID=I.TeacherID 	
	where A.ClassOrderID=:ClassOrderID ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassOrderID', $ClassOrderID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$TotalRowCount = $Row["TotalRowCount"];


$Sql = "
	select 
		A.ClassOrderID,
		A.ClassID,
		A.MemberID,
		A.TeacherID,
		A.ClassLinkType,
		A.StartDateTime,
		A.StartDateTimeStamp,
		A.StartYear,
		A.StartMonth,
		A.StartDay,
		A.StartHour,
		A.StartMinute,
		A.EndDateTime,
		A.EndDateTimeStamp,
		A.EndYear,
		A.EndMonth,
		A.EndDay,
		A.EndHour,
		A.EndMinute,
		A.CommonUseClassIn,
		A.CommonShClassCode,
		A.CommonCiCourseID,
		A.CommonCiClassID,
		A.CommonCiTelephoneTeacher,
		A.CommonCiTelephoneStudent,
		A.ClassAttendState,
		A.ClassAttendStateMemberID,
		A.ClassState,
		A.BookVideoID,
		A.BookQuizID,
		A.BookScanID,
		A.ClassRegDateTime,
		A.ClassModiDateTime,

		B.MemberName,
		B.MemberLoginID, 
		B.MemberCiTelephone,
		H.TeacherName,
		I.MemberLoginID as TeacherLoginID, 
		I.MemberCiTelephone as TeacherCiTelephone,
		C.CenterID as JoinCenterID,
		C.CenterName as JoinCenterName,
		D.BranchID as JoinBranchID,
		D.BranchName as JoinBranchName, 
		E.BranchGroupID as JoinBranchGroupID,
		E.BranchGroupName as JoinBranchGroupName,
		F.CompanyID as JoinCompanyID,
		F.CompanyName as JoinCompanyName,
		G.FranchiseName
	from Classes A
            inner join Members B on A.MemberID=B.MemberID 
            inner join Centers C on B.CenterID=C.CenterID 
            inner join Branches D on C.BranchID=D.BranchID 
            inner join BranchGroups E on D.BranchGroupID=E.BranchGroupID 
            inner join Companies F on E.CompanyID=F.CompanyID 
            inner join Franchises G on F.FranchiseID=G.FranchiseID 
            inner join Teachers H on A.TeacherID=H.TeacherID 
			inner join Members I on H.TeacherID=I.TeacherID 	
	where A.ClassOrderID=:ClassOrderID ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassOrderID', $ClassOrderID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);



?>


<div id="page_content">
    <div id="page_content_inner">

        <h3 class="heading_b uk-margin-bottom"><?=$수업관리[$LangID]?></h3>


        <div class="md-card">
            <div class="md-card-content">
                <div class="uk-grid" data-uk-grid-margin>
                    <div class="uk-width-1-1">
                        <div class="uk-overflow-container">
                            
							<table class="uk-table uk-table-align-vertical">
                                <thead>
                                    <tr>
                                        <th nowrap>No</th>
                                        <th nowrap><?=$수업시작[$LangID]?></th>
                                        <th nowrap><?=$수업종료[$LangID]?></th>
                                        <th nowrap><?=$학생명[$LangID]?></th>
                                        <th nowrap><?=$강사명[$LangID]?></th>
                                        <th nowrap><?=$요청[$LangID]?></th>
                                        <th nowrap><?=$상태[$LangID]?></th>
										<th nowrap><?=$수업설정[$LangID]?></th>
										<th nowrap><?=$수업연기[$LangID]?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                    <?php
                                    $ListCount = 1;
                                    while($Row = $Stmt->fetch()) {
										
										$ClassOrderID = $Row["ClassOrderID"];
										
	                                       
										$ClassID = $Row["ClassID"];
										$ClassLinkType = $Row["ClassLinkType"];
                                        $MemberID = $Row["MemberID"];
										
                                        $StartDateTimeStamp = $Row["StartDateTimeStamp"];
                                        $StartYear = $Row["StartYear"];
                                        $StartMonth = $Row["StartMonth"];
                                        $StartDay = $Row["StartDay"];
                                        $StartHour = $Row["StartHour"];
                                        $StartMinute = $Row["StartMinute"];

                                        $EndDateTimeStamp = $Row["EndDateTimeStamp"];
                                        $EndYear = $Row["EndYear"];
                                        $EndMonth = $Row["EndMonth"];
                                        $EndDay = $Row["EndDay"];
                                        $EndHour = $Row["EndHour"];
                                        $EndMinute = $Row["EndMinute"];

                                        $CommonUseClassIn = $Row["CommonUseClassIn"];
                                        $CommonShClassCode = $Row["CommonShClassCode"];
                                        $CommonCiCourseID = $Row["CommonCiCourseID"];
                                        $CommonCiClassID = $Row["CommonCiClassID"];
                                        $CommonCiTelephoneTeacher = $Row["CommonCiTelephoneTeacher"];
                                        $CommonCiTelephoneStudent = $Row["CommonCiTelephoneStudent"];

                                        $ClassAttendState = $Row["ClassAttendState"];
										$ClassAttendStateMemberID = $Row["ClassAttendStateMemberID"];
										$ClassState = $Row["ClassState"];

										$BookVideoID = $Row["BookVideoID"];
										$BookQuizID = $Row["BookQuizID"];
										$BookScanID = $Row["BookScanID"];

                                        $ClassRegDateTime = $Row["ClassRegDateTime"];
                                        $ClassModiDateTime = $Row["ClassModiDateTime"];
                                        
                                        $MemberName = $Row["MemberName"];
                                        $MemberLoginID = $Row["MemberLoginID"];
										$MemberCiTelephone = $Row["MemberCiTelephone"];

                                        $TeacherName = $Row["TeacherName"];
                                        $TeacherLoginID = $Row["TeacherLoginID"];
										$TeacherCiTelephone = $Row["TeacherCiTelephone"];
                                        
                                        $CenterID = $Row["JoinCenterID"];
                                        $CenterName = $Row["JoinCenterName"];
                                        $BranchID = $Row["JoinBranchID"];
                                        $BranchName = $Row["JoinBranchName"];
                                        $BranchGroupID = $Row["JoinBranchGroupID"];
                                        $BranchGroupName = $Row["JoinBranchGroupName"];
                                        $CompanyID = $Row["JoinCompanyID"];
                                        $CompanyName = $Row["JoinCompanyName"];
                                        $FranchiseName = $Row["FranchiseName"];


										if ($ClassState==0){
											$StrClassState = $미등록[$LangID];
										}else if ($ClassState==1){
											$StrClassState = $등록[$LangID];
										}else if ($ClassState==2){
											$StrClassState = $완료[$LangID];
										}

										if ($ClassAttendState==4){
											$StrClassAttendState = $연기된수업[$LangID]."(S)";//학생연기
										}else if ($ClassAttendState==5){
											$StrClassAttendState = $연기된수업[$LangID]."(T)";//강사연기
										}else if ($ClassAttendState==6){
											$StrClassAttendState = $취소된수업[$LangID]."(S)";//학생취소
										}else if ($ClassAttendState==7){
											$StrClassAttendState = $취소된수업[$LangID]."(T)";//강사취소
										}else if ($ClassAttendState==8){
											$StrClassAttendState = $교사변경수업[$LangID];//교사변경수업
										}else{

										}
										
										if ($CommonCiTelephoneStudent==""){
											$CommonCiTelephoneStudent = $MemberCiTelephone;
										}

										if ($CommonCiTelephoneTeacher==""){
											$CommonCiTelephoneTeacher = $TeacherCiTelephone;
										}

                            
                                    ?>
                                    <tr>
                                        <td class="uk-text-nowrap uk-table-td-center"><?=$ListCount?></td>
                                        <td class="uk-text-nowrap uk-table-td-center">
											<?=$StartYear?>-<?=substr("0".$StartMonth,-2)?>-<?=substr("0".$StartDay,-2)?>
											<span style="color:#006BD7;"><?=substr("0".$StartHour,-2)?>:<?=substr("0".$StartMinute,-2)?></span>
										</td>
                                        <td class="uk-text-nowrap uk-table-td-center">
											<?=$EndYear?>-<?=substr("0".$EndMonth,-2)?>-<?=substr("0".$EndDay,-2)?>
											<span style="color:#006BD7;"><?=substr("0".$EndHour,-2)?>:<?=substr("0".$EndMinute,-2)?></span>
										</td>
                                        <td class="uk-text-nowrap uk-table-td-center"><?=$MemberName?> <span style="color:#006BD7;"><?=$MemberLoginID?></span><!-- <a href="javascript:OpenStudentForm(<?=$MemberID?>);"><i class="material-icons">account_box</i></a>--></td>
                                        <td class="uk-text-nowrap uk-table-td-center"><?=$TeacherName?></td>
                                        <td class="uk-text-nowrap uk-table-td-center">
											<a href="javascript:OpenClassQnaForm(1, <?=$ClassID?>);"><i class="material-icons">new_releases</i></a>
										</td>
                                                                                
										<td class="uk-text-nowrap uk-table-td-center">
											<?if ($ClassAttendState==4 || $ClassAttendState==5 || $ClassAttendState==6 || $ClassAttendState==7 || $ClassAttendState==8){?>	
												<?=$StrClassAttendState?>
											<?}else{?>
												<?=$StrClassState?>
											<?}?>
										</td>										
										<td class="uk-text-nowrap uk-table-td-center">
											<?if ($ClassAttendState==4 || $ClassAttendState==5 || $ClassAttendState==6 || $ClassAttendState==7 || $ClassAttendState==8){?>
												-
											<?}else{?>
												<a class="md-btn md-btn-warning md-btn-mini md-btn-wave-light" href="javascript:OpenClassSetup(1, <?=$ClassID?>);"><?=$수업설정[$LangID]?></a>
											<?}?>
										</td>
										<td class="uk-text-nowrap uk-table-td-center">
											<?if ($ClassAttendState==4 || $ClassAttendState==5 || $ClassAttendState==6 || $ClassAttendState==7 || $ClassAttendState==7){?>
												-
											<?}else{?>
												<?if ($ClassState==1 || $ClassState==0){ //0:미등록 전 1:등록완료 2:수업완료?>
												<a class="md-btn md-btn-gray md-btn-mini md-btn-wave-light" href="javascript:OpenResetDateForm(<?=$ClassID?>, <?=$ClassStudyType?>);"><?=$수업연기[$LangID]?></a>
												<?}else{?>
												-
												<?}?>
											<?}?>
										</td>
                                    </tr>
                                    <?php
                                        $ListCount ++;
                                    }
                                    $Stmt = null;
                                    ?>


                                </tbody>
                            </table>
                        </div>
                        

                        <?php            
                        //include_once('./inc_pagination.php');
                        ?>
						
						<br><br><br>

                        <div class="uk-form-row" style="text-align:center;display:none;">
                            <a type="button" href="leveltest_apply_form.php?ListParam=<?=$ListParam?>" class="md-btn md-btn-primary"><?=$신규등록[$LangID]?></a>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!--- api post form -->
<div style="display:none;">
<!--    <form name="ShClassForm" id="ShClassForm" action="http://180.150.230.195/sso/type1.do" method="POST">-->
    <form name="ShClassForm" id="ShClassForm" action="http://121.170.164.231/sso/type1.do" method="POST">
        <input type="text" name="userid" value="" />
        <input type="text" name="username" value="" />
        <input type="text" name="usertype" value="" />
        <input type="text" name="remote" value="1" />
        <input type="text" name="confcode" value="" />
        <input type="text" name="conftype" value="2" />
    </form>

    <form name="CiClassForm" id="CiClassForm" method="POST">
        <input type="text" name="ClassID" id="ClassID" value="">
        <input type="text" name="ClassName" id="ClassName" value="">
        <input type="text" name="MemberType" id="MemberType" value="">
    </form>
</div>
<?
include_once('./inc_common_list_js.php');

?>

<!-- ============== only this page js ============== -->

<!-- moment & moment timezone library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.26/moment-timezone.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.26/moment-timezone-with-data.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/locale/ko.js"></script>
<script>

function OpenResetDateForm(ClassID, ClassStudyType){
	var OpenUrl = "../pop_class_reset_date_form.php?ClassID="+ClassID+"&ClassStudyType="+ClassStudyType;

	$.colorbox({	
		href:OpenUrl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "900"
		,maxHeight: "800"
		,title:""
		,iframe:true 
		,scrolling:true
		,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}


function OpenClassSetup(ClassStudyType, ClassID){

    openurl = "class_setup_form.php?ClassStudyType="+ClassStudyType+"&ClassID="+ClassID;
    $.colorbox({    
        href:openurl
        ,width:"95%"
        ,height:"95%"
        ,maxWidth: "850"
        ,maxHeight: "750"
        ,title:""
        ,iframe:true 
        ,scrolling:true
        ,onClosed:function(){location.reload(true);}
        //,onComplete:function(){alert(1);}
    });

}



function OpenClassQnaForm(ClassStudyType, ClassID){
    openurl = "class_qna_form.php?ClassStudyType="+ClassStudyType+"&ClassID="+ClassID;
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


</script>



<!-- ============== only this page js ============== -->
<!-- ============== common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ============== common.js ============== -->
<!-- =================== moment library ================= -->
<!--
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.26/moment-timezone.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.26/moment-timezone-with-data.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/locale/ko.js"></script>
-->
<!-- =================== moment library ================= -->

<script>
function SearchSubmit(){
	document.SearchForm.action = "class_detail_list.php";
    document.SearchForm.submit();
}
</script>

<?php
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>
