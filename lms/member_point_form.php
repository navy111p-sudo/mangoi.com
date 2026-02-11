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

<!-- ============== only this page css ============== -->
<!-- ============== common.css ============== -->
<link rel="stylesheet" type="text/css" href="css/common.css" />
<!-- ============== common.css ============== -->


</head>

<body class="disable_transitions sidebar_main_open sidebar_main_swipe" style="padding-top:0px;">

<?php
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$MemberPointID = isset($_REQUEST["MemberPointID"]) ? $_REQUEST["MemberPointID"] : "";
$SearchMemberPointTypeID = isset($_REQUEST["SearchMemberPointTypeID"]) ? $_REQUEST["SearchMemberPointTypeID"] : "";

if ($MemberPointID!=""){

	$Sql = "
			select 
					A.*,
					B.MemberName,
					B.MemberLevelID,
					C.MemberName as RegMemberName,
					C.MemberLevelID as RegMemberLevelID
			from MemberPoints A 
				inner join Members B on A.MemberID=B.MemberID 
				left outer join Members C on A.RegMemberID=C.MemberID 
			where A.MemberPointID=:MemberPointID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':MemberPointID', $MemberPointID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$MemberPointTypeID = $Row["MemberPointTypeID"];
	$RegMemberID = $Row["RegMemberID"];
	$RegMemberLevelID = $Row["RegMemberLevelID"];
	$MemberID = $Row["MemberID"];
	$MemberPointName = $Row["MemberPointName"];
	$MemberPointText = $Row["MemberPointText"];
	$MemberPoint = $Row["MemberPoint"];
	$MemberName = $Row["MemberName"];
	$MemberLevelID = $Row["MemberLevelID"];
	$RegMemberName = $Row["RegMemberName"];


}else{
	$MemberPointTypeID = 0;
	$RegMemberID = $_LINK_ADMIN_ID_;
	$RegMemberLevelID = $_LINK_ADMIN_LEVEL_ID_;
	$MemberPointName = "";
	$MemberPointText = "";
	$MemberPoint = 0;

	$Sql = "
			select 
					A.*
			from Members A 
			where A.MemberID=:MemberID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$MemberName = $Row["MemberName"];
	$RegMemberName = $MemberName;
	$MemberLevelID = $Row["MemberLevelID"];
}

if($SearchMemberPointTypeID!="") {
	$Sql2 = "
		select 
			*
		from MemberPointNewTypes A 
		where 
			A.MemberPointTypeID=:MemberPointTypeID
	";
	$Stmt2 = $DbConn->prepare($Sql2);
	$Stmt2->bindParam(':MemberPointTypeID', $SearchMemberPointTypeID);
	$Stmt2->execute();
	$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
	$Row2 = $Stmt2->fetch();
	$Stmt2 = null;
	$MemberPointName = $Row2["MemberPointTypeName"];
	$MemberPointText = $Row2["MemberPointTypeText"];
	$MemberPointTypeID = $Row2["MemberPointTypeID"];
}
?>


<div id="page_content">
	<div id="page_content_inner">
		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="MemberPointID" value="<?=$MemberPointID?>">
		<input type="hidden" name="RegMemberID" value="<?=$RegMemberID?>">
		<input type="hidden" name="MemberID" value="<?=$MemberID?>">
		<input type="hidden" name="MemberPointTypeID" value="<?=$MemberPointTypeID?>">

		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$MemberName?></span><span class="sub-heading" id="user_edit_position"><?=$포인트관리[$LangID]?></span></h2>
						</div>
					</div>
					<div class="user_content">
						

						<div class="uk-width-medium-2-10" style="padding-top:7px; margin-bottom: 20px; width:30%;">
							<select id="SearchMemberPointTypeID" name="SearchMemberPointTypeID" class="uk-width-1-1" onchange="PreSubmit()" data-md-select2 data-allow-clear="true" data-placeholder="포인트 타입" style="width:100%;"/>
								<option value=""></option>
								<?
								$AddSqlWhere = "";
								if($RegMemberLevelID==19) {
									$AddSqlWhere = " and A.MemberPointTypeType=1 ";
								} else if($RegMemberLevelID==12 or $RegMemberLevelID==13){
									$AddSqlWhere = " and A.MemberPointTypeType=3 ";
								}

								$Sql2 = "select 
												A.* 
										from MemberPointNewTypes A 
										where A.MemberPointTypeMethod=2 ".$AddSqlWhere." 
								";
								$Stmt2 = $DbConn->prepare($Sql2);
								$Stmt2->execute();
								$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
								
								while($Row2 = $Stmt2->fetch()) {
									$MemberPointTypeID = $Row2["MemberPointTypeID"];
									$MemberPointTypeName = $Row2["MemberPointTypeName"];
								
								?>

								<option value="<?=$MemberPointTypeID?>" <?if ($SearchMemberPointTypeID==$MemberPointTypeID){?>selected<?}?>><?=$MemberPointTypeName?></option>
								<?
								}
								$Stmt2 = null;
								?>
							</select>
						</div>

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-5-10">
									<label for="MemberPointName"><?=$포인트제목[$LangID]?></label>
									<input type="text" id="MemberPointName" name="MemberPointName" value="<?=$MemberPointName?>" class="md-input label-fixed"/>
								</div>
								<div class="uk-width-medium-5-10 uk-input-group">
									<label for="MemberPoint"><?=$포인트[$LangID]?></label>
									<input type="number" id="MemberPoint" name="MemberPoint" value="<?=$MemberPoint?>" class="md-input label-fixed allownumericwithoutdecimal" <?if($SearchMemberPointTypeID==15) {?>onkeyup="CheckPoint(<?=$RegMemberID?>, this.value)"<?}?> />
									<span class="uk-input-group-addon">P</span>
								</div>
							</div>
						</div>

						
						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<label for="MemberPointText"><?=$포인트내용[$LangID]?></label>
									<textarea class="md-input" name="MemberPointText" id="MemberPointText" cols="30" rows="4"><?=$MemberPointText?></textarea>
								</div>
							</div>
						</div>
						<?if($SearchMemberPointTypeID!="") { ?>
							// {{}} 안의 데이터는 자동적으로 변환됩니다.
						<?}?>

						<?if ($MemberPointID!=""){?>
						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-2 uk-input-group">
									<input type="checkbox" name="DelMemberPoint" id="DelMemberPoint" value="1" data-md-icheck/>
									<label for="DelMemberPoint" class="inline-label"><?=$삭제[$LangID]?></label>
								</div>
								<div class="uk-width-medium-1-2 uk-input-group">
	
								</div>
							</div>
						</div>
						<?}?>

						<div class="uk-margin-top" style="text-align:center;padding-top:30px;">
							<a type="button" href="javascript:FormSubmit();" class="md-btn md-btn-primary"><?=$저장하기[$LangID]?></a>
						</div>

					</div>
				</div>
			</div>

		</div>
		</form>

	</div>
</div>


<?
include_once('./inc_common_form_js.php');
?>
<!-- ==============  only this page js ============== -->

<!-- ==============  only this page js ============== -->
<!-- ==============  common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ==============  common.js ============== -->


<script language="javascript">

function CheckPoint(RegMemberID, value){
	url = "ajax_get_max_point.php";
	//location.href = url + "?TeacherCharacterItemID="+TeacherCharacterItemID+"&OrderType="+OrderType;
    $.ajax(url, {
        data: {
			RegMemberID: RegMemberID,
			value: value
        },
        success: function (data) {
			var result = data.result;
			var code = data.code;

			if(code==0) {
				alert("보유하신 포인트는 "+result+" 이며, 그 이상의 포인트를 전달할 수 없습니다.");
				document.RegForm.MemberPoint.value = result;
			}
        },
        error: function () {
            alert('Error while contacting server, please try again');
        }
    });
}

function FormSubmit(){
	obj = document.RegForm.MemberPointName;
	if (obj.value==""){
		UIkit.modal.alert("포인트 제목을 입력하세요.");
		obj.focus();
		return;
	}


	UIkit.modal.confirm(
		'저장 하시겠습니까?', 
		function(){ 
			document.RegForm.action = "member_point_action.php";
			document.RegForm.submit();
		}
	);

}

function PreSubmit() {
	document.RegForm.submit();
}
</script>




<?php
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>