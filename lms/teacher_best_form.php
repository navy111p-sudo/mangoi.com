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
$TeacherListBestID = isset($_REQUEST["TeacherListBestID"]) ? $_REQUEST["TeacherListBestID"] : "";
$TeacherListBestYear = isset($_REQUEST["TeacherListBestYear"]) ? $_REQUEST["TeacherListBestYear"] : "";
$TeacherListBestMonth = isset($_REQUEST["TeacherListBestMonth"]) ? $_REQUEST["TeacherListBestMonth"] : "";

if ($TeacherListBestYear==""){
	$TeacherListBestYear = date("Y");
}
if ($TeacherListBestMonth==""){
	$TeacherListBestMonth = date("n");
}

$SearchStartDate = $TeacherListBestYear."-".substr("0".$TeacherListBestMonth,-2)."-01";
$SearchEndDate = $TeacherListBestYear."-".substr("0".$TeacherListBestMonth,-2)."-".date('t', strtotime($SearchStartDate));


if ($TeacherListBestID!=""){
	$Sql = "
			select 
				A.*,
				ifnull(A1.TeacherName, '-') as TeacherListBestTeacherName1,
				ifnull(A2.TeacherName, '-') as TeacherListBestTeacherName2,
				ifnull(A3.TeacherName, '-') as TeacherListBestTeacherName3,
				ifnull(A4.TeacherName, '-') as TeacherListBestTeacherName4,
				ifnull(A5.TeacherName, '-') as TeacherListBestTeacherName5,
				ifnull(A6.TeacherName, '-') as TeacherListBestTeacherName6,
				ifnull(A7.TeacherName, '-') as TeacherListBestTeacherName7,
				ifnull(A8.TeacherName, '-') as TeacherListBestTeacherName8,
				ifnull(A9.TeacherName, '-') as TeacherListBestTeacherName9,
				ifnull(A10.TeacherName, '-') as TeacherListBestTeacherName10
			from TeacherListBests A 
				left outer join Teachers A1 on A.TeacherListBestTeacherID1=A1.TeacherID 
				left outer join Teachers A2 on A.TeacherListBestTeacherID2=A2.TeacherID 
				left outer join Teachers A3 on A.TeacherListBestTeacherID3=A3.TeacherID 
				left outer join Teachers A4 on A.TeacherListBestTeacherID4=A4.TeacherID 
				left outer join Teachers A5 on A.TeacherListBestTeacherID5=A5.TeacherID 
				left outer join Teachers A6 on A.TeacherListBestTeacherID6=A6.TeacherID 
				left outer join Teachers A7 on A.TeacherListBestTeacherID7=A7.TeacherID 
				left outer join Teachers A8 on A.TeacherListBestTeacherID8=A8.TeacherID 
				left outer join Teachers A9 on A.TeacherListBestTeacherID9=A8.TeacherID 
				left outer join Teachers A10 on A.TeacherListBestTeacherID10=A10.TeacherID 
			where A.TeacherListBestID=:TeacherListBestID ";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TeacherListBestID', $TeacherListBestID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();

	$TeacherListBestName = $Row["TeacherListBestName"];
	
	$TeacherListBestTeacherID[1] = $Row["TeacherListBestTeacherID1"];
	$TeacherListBestTeacherID[2] = $Row["TeacherListBestTeacherID2"];
	$TeacherListBestTeacherID[3] = $Row["TeacherListBestTeacherID3"];
	$TeacherListBestTeacherID[4] = $Row["TeacherListBestTeacherID4"];
	$TeacherListBestTeacherID[5] = $Row["TeacherListBestTeacherID5"];
	$TeacherListBestTeacherID[6] = $Row["TeacherListBestTeacherID6"];
	$TeacherListBestTeacherID[7] = $Row["TeacherListBestTeacherID7"];
	$TeacherListBestTeacherID[8] = $Row["TeacherListBestTeacherID8"];
	$TeacherListBestTeacherID[9] = $Row["TeacherListBestTeacherID9"];
	$TeacherListBestTeacherID[10] = $Row["TeacherListBestTeacherID10"];

	$TeacherListBestState = $Row["TeacherListBestState"];

}else{
	$TeacherListBestName = $TeacherListBestMonth."<?=$월의_베스트_강사[$LangID]?>";
	
	$TeacherListBestTeacherID[1] = 0;
	$TeacherListBestTeacherID[2] = 0;
	$TeacherListBestTeacherID[3] = 0;
	$TeacherListBestTeacherID[4] = 0;
	$TeacherListBestTeacherID[5] = 0;
	$TeacherListBestTeacherID[6] = 0;
	$TeacherListBestTeacherID[7] = 0;
	$TeacherListBestTeacherID[8] = 0;
	$TeacherListBestTeacherID[9] = 0;
	$TeacherListBestTeacherID[10] = 0;

	$TeacherListBestState = 1;
}



for ($ii=1;$ii<=10;$ii++){
	$AutoBestTeacherID[$ii] = "";
	$AutoBestTeacherName[$ii] = "";
	$AutoBestTeacherScore[$ii] = "";
}


$ViewTable2 = "
	select 
		B.TeacherID, 
		(A.AssmtTeacherScore1+A.AssmtTeacherScore2+A.AssmtTeacherScore3+A.AssmtTeacherScore4+A.AssmtTeacherScore5) as AssmtTeacherScore 
	from AssmtTeacherScores A 
		inner join Classes B on A.ClassID=B.ClassID 
	where 
		datediff(A.AssmtTeacherScoreRegDateTime, '".$SearchStartDate."')>=0 and datediff(A.AssmtTeacherScoreRegDateTime, '".$SearchEndDate."')<=0 
	group by B.TeacherID 
";



$Sql = "
	select 
		AA.TeacherID,
		AA.AssmtTeacherScore,
		BB.TeacherName
	from (".$ViewTable2.") AA 
		inner join Teachers BB on AA.TeacherID=BB.TeacherID 
	order by AA.AssmtTeacherScore desc limit 0, 10
";

$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

$ListCount = 1;
while($Row = $Stmt->fetch()) {
	$AutoBestTeacherID[$ListCount] = $Row["TeacherID"];
	$AutoBestTeacherName[$ListCount] = $Row["TeacherName"];
	$AutoBestTeacherScore[$ListCount] = $Row["AssmtTeacherScore"];
	$ListCount++;
}
$Stmt = null;
?>


<div id="page_content">
	<div id="page_content_inner">
		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		<input type="hidden" name="TeacherListBestID" value="<?=$TeacherListBestID?>">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$베스트강사[$LangID]?></span><span class="sub-heading" id="user_edit_position"><!--베스트강사--></span></h2>
						</div>
					</div>
					<div class="user_content">
						
						<div class="uk-margin-top">
							<label style="font-weight:bold;color:#AE0000;"><?=$강사_평가_점수를_가져올_연도와_월을_선택해_주세요[$LangID]?></label>
							<div class="uk-grid" data-uk-grid-margin style="margin-top:10px;">
								
								<div class="uk-width-medium-1-2">
									<select name="TeacherListBestYear" id="TeacherListBestYear" onchange="SearchSubmit();" style="width:100%;height:30px;">
										<?for ($ii=date("Y")-1;$ii<=date("Y")+1;$ii++){?>
										<option value="<?=$ii?>" <?if ($ii==$TeacherListBestYear){?>selected<?}?>><?=$ii?></option>
										<?}?>
									</select>
								</div>
								<div class="uk-width-medium-1-2">
									<select name="TeacherListBestMonth" id="TeacherListBestMonth" onchange="SearchSubmit();" style="width:100%;height:30px;">
										<?for ($ii=1;$ii<=12;$ii++){?>
										<option value="<?=$ii?>" <?if ($ii==$TeacherListBestMonth){?>selected<?}?>><?=$ii?></option>
										<?}?>
									</select>
								</div>
							</div>
						</div>

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin style="margin-top:30px;">
								<div class="uk-width-medium-1-1">
									<label for="TeacherListBestName"><?=$제목[$LangID]?></label>
									<input type="text" id="TeacherListBestName" name="TeacherListBestName" value="<?=$TeacherListBestName?>" class="md-input label-fixed"/>
								</div>
							</div>
						</div>

						<?
						for ($ii=1;$ii<=10;$ii++){
						?>
						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-1">
									<label for="TeacherListBestTeacherID<?=$ii?>" style="font-weight:bold;color:#AE0000;"><?=$ii?>위 <?if ($AutoBestTeacherName[$ii]!=""){?> - <?=$AutoBestTeacherName[$ii]?> ( <?=number_format($AutoBestTeacherScore[$ii],0)?>점 )<?}?></label>
									<select name="TeacherListBestTeacherID<?=$ii?>" style="width:100%;height:30px;margin-top:5px;">
										<option value="0"><?=$선택하세요[$LangID]?></option>
										<?
										$Sql2 = "select A.* from Teachers A where A.TeacherState=1 or A.TeacherID=".$TeacherListBestTeacherID[$ii]." order by A.TeacherName asc ";
										$Stmt2 = $DbConn->prepare($Sql2);
										$Stmt2->execute();
										$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
										while($Row2 = $Stmt2->fetch()) {
										?>
										<option value="<?=$Row2["TeacherID"]?>" <?if ($TeacherListBestTeacherID[$ii]==$Row2["TeacherID"]){?>selected<?}?>><?=$Row2["TeacherName"]?></option>
										<?
										}
										$Stmt2 = null;
										?>
									</select>
								</div>
							</div>
						</div>
						<?
						}
						?>

						<hr>
						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-2 uk-input-group">
									<input type="checkbox" id="TeacherListBestState" name="TeacherListBestState" value="1" <?php if ($TeacherListBestState==1) { echo "checked";}?> data-switchery/>
									<label for="TeacherListBestState" class="inline-label"><?=$사용[$LangID]?></label>
								</div>
							</div>
						</div>

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

function FormSubmit(){

	obj = document.RegForm.TeacherListBestName;
	if (obj.value==""){
		UIkit.modal.alert("<?=$제목을_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}

	UIkit.modal.confirm(
		'<?=$저장_하시겠습니까[$LangID]?>?', 
		function(){ 
			document.RegForm.action = "teacher_best_action.php";
			document.RegForm.submit();
		}
	);

}


function SearchSubmit(){
	document.RegForm.action = "teacher_best_form.php";
	document.RegForm.submit();
}
</script>




<?php
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>