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

<body class="disable_transitions sidebar_main_open sidebar_main_swipe">
<?php
$MainMenuID = 88;
$SubMenuID = 8864;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
include('./inc_departments.php');
$departments = getDepartments($LangID);



#-----------------------------------------------------------------------------------------------------------------------------------------#
$MemberLoginID     = isset($_COOKIE["LoginMemberID"   ]) ? $_COOKIE["LoginMemberID"   ] : "";
#-----------------------------------------------------------------------------------------------------------------------------------------#
# 회원 고유아이디(번호, 조직아이디) 찾기
#-----------------------------------------------------------------------------------------------------------------------------------------#
$Sql = "SELECT T.*,M.* from Members as M 
			  left join Hr_OrganLevelTaskMembers T on T.MemberID=M.MemberID
			      where M.MemberLoginID=:MemberLoginID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberLoginID', $MemberLoginID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$My_MemberID     = $Row["MemberID"];
$My_MemberName   = $Row["MemberName"];
$My_StaffID   = $Row["StaffID"];
$My_TeacherID   = $Row["TeacherID"];
$My_OrganLevel   = $Row["Hr_OrganLevel"];    
$My_OrganLevelID = $Row["Hr_OrganLevelID"];
$My_OrganTask2ID = $Row["Hr_OrganTask2ID"];
#-----------------------------------------------------------------------------------------------------------------------------------------#


$Sql = "SELECT 
			A.* ,
			AES_DECRYPT(UNHEX(A.StaffPhone1),:EncryptionKey) as DecStaffPhone1,
			AES_DECRYPT(UNHEX(A.StaffPhone2),:EncryptionKey) as DecStaffPhone2,
			B.FranchiseName,
			C.MemberLoginID,
			ifnull(D.MemberID,0) as Hr_MemberID,
			ifnull(D.Hr_OrganLevel,0) as Hr_OrganLevel

		from Staffs A 
			inner join Franchises B on A.FranchiseID=B.FranchiseID 
			inner join Members C on A.StaffID=C.StaffID and (C.MemberLevelID=4 OR C.MemberLevelID=15)
			left outer join Hr_OrganLevelTaskMembers D on C.MemberID=D.MemberID 

		where A.StaffState = 1 AND C.MemberID IN (SELECT K.MemberID FROM Hr_OrganLevelTaskMembers K 
								LEFT JOIN Hr_OrganLevels L ON K.Hr_OrganLevelID = L.Hr_OrganLevelID
								WHERE L.Hr_OrganLevel".$My_OrganLevel."ID = ".$My_OrganLevelID." 
										AND K.MemberID <> ".$My_MemberID."	AND L.Hr_OrganLevelState=1)";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom"><?=$부서원평가결과[$LangID]?></h3>

		<div class="md-card">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin>
					<div class="uk-width-1-1">
						<div class="uk-overflow-container">
							<table class="uk-table uk-table-align-vertical">
								<thead>
									<tr>
										<th nowrap>No</th>
										<th nowrap><?=$부서[$LangID]?></th>
										<th nowrap><?=$교사_및_직원명[$LangID]?></th>
										<th nowrap><?=$아이디[$LangID]?></th>
										<th nowrap><?=$닉네임[$LangID]?></th>
										<th nowrap><?=$전화번호[$LangID]?></th>
										<th nowrap><?=$휴대폰[$LangID]?></th>
										<th nowrap><?=$프랜차이즈[$LangID]?></th>
										<th nowrap><?=$상태[$LangID]?></th>
										<?if ($_LINK_ADMIN_LEVEL_ID_==0){?>
										<th nowrap><?=$인사평가셋팅[$LangID]?></th>
										<th nowrap><?=$권한[$LangID]?></th>
										<?}?>
									</tr>
								</thead>
								<tbody>
									
									<?php
									$ListCount = 1;
									while($Row = $Stmt->fetch()) {
										//$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

										$StaffID = $Row["StaffID"];
										$StaffManageMent = $Row["StaffManageMent"];
										$StaffName = $Row["StaffName"];
										$StaffNickName = $Row["StaffNickName"];
										$StaffPhone1 = $Row["DecStaffPhone1"];
										$StaffPhone2 = $Row["DecStaffPhone2"];
										$StaffState = $Row["StaffState"];
										$FranchiseName = $Row["FranchiseName"];
										$MemberLoginID = $Row["MemberLoginID"];

										$Hr_MemberID = $Row["Hr_MemberID"];
										$Hr_OrganLevel = $Row["Hr_OrganLevel"];
										
										if ($StaffState==1){
											$StrStaffState = "<span class=\"ListState_1\">".$활동중[$LangID]."</span>";
										}else if ($StaffState==2){
											$StrStaffState = "<span class=\"ListState_2\">".$미활동[$LangID]."</span>";
										}

										if ($Hr_MemberID!=0){
											$Str_Hr_MemberID = "<span class=\"ListState_1\">".$완료[$LangID]."</span>";
										}else{
											$Str_Hr_MemberID = "<span class=\"ListState_2\">".$미완료[$LangID]."</span>";
										}

										$StrStaffManageMent = $departments[$StaffManageMent];

										if ($Hr_OrganLevel==1) {
											$Str_Hr_OrganLevel = "LEVEL 1(".$경영진[$LangID].")";
										} else if ($Hr_OrganLevel==2) {
											$Str_Hr_OrganLevel = "LEVEL 2(".$부문[$LangID].")";
										} else if ($Hr_OrganLevel==3) {
											$Str_Hr_OrganLevel = "LEVEL 3(".$부서[$LangID].")";
										} else if ($Hr_OrganLevel==4) {
											$Str_Hr_OrganLevel = "LEVEL 4(".$파트[$LangID].")";
										}else{
											$Str_Hr_OrganLevel = "-";
										}
									?>
									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ListCount?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrStaffManageMent?></td>
										<td class="uk-text-nowrap uk-table-td-center"><a href="hr_staff_indicator_list.php?MemberID=<?=$MemberLoginID?>"><?=$StaffName?></a></td>
										<td class="uk-text-nowrap uk-table-td-center"><a href="hr_staff_indicator_list.php?MemberID=<?=$MemberLoginID?>"><?=$MemberLoginID?></a></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StaffNickName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StaffPhone1?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StaffPhone2?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$FranchiseName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$StrStaffState?></td>
										<?if ($_LINK_ADMIN_LEVEL_ID_==0){?>
										<td class="uk-text-nowrap uk-table-td-center"><?=$Str_Hr_MemberID?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$Str_Hr_OrganLevel?></td>
										<?}?>
									</tr>
									<?php
										$ListCount ++;
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

	</div>
</div>


<?
include_once('./inc_common_list_js.php');
?>
<!-- ============== only this page js ============== -->

<!-- ============== only this page js ============== -->
<!-- ============== common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ============== common.js ============== -->


<script>
function SearchSubmit(){
	document.SearchForm.action = "staff_list.php";
	document.SearchForm.submit();
}
</script>

<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>