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
$ClassID = isset($_REQUEST["ClassID"]) ? $_REQUEST["ClassID"] : "";


$StrTitle = $수업[$LangID];

$Sql = "
		select 
				A.ClassOrderID,
				A.ClassID,
				A.MemberID,
				A.TeacherID,
				A.ClassLevel,
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
				A.BookSystemType,
				A.BookWebookUnitID,
				A.BookWebookUnitName,
				A.BookVideoID,
				A.BookQuizID,
				A.BookScanID,
				A.BookRegForReason, 
				A.ClassRegDateTime,
				A.ClassModiDateTime,

				B.MemberName,
				B.MemberLoginID,
				B.MemberCiTelephone,
				C.TeacherName,
				D.MemberLoginID as TeacherLoginID,
				D.MemberCiTelephone as TeacherCiTelephone,

				G.ClassProductID,

				F.BookGroupID as BookVideoBookGroupID,
				E.BookID as BookVideoBookID,
				FF.BookGroupID as BookQuizBookGroupID,
				EE.BookID as BookQuizBookID,
				FFF.BookGroupID as BookScanBookGroupID,
				EEE.BookID as BookScanBookID

		from Classes A 
			inner join Members B on A.MemberID=B.MemberID 
			inner join Teachers C on A.TeacherID=C.TeacherID 
			inner join Members D on C.TeacherID=D.TeacherID 

			inner join ClassOrders G on A.ClassOrderID=G.ClassOrderID 
			
			left outer join BookVideos E on A.BookVideoID=E.BookVideoID 
			left outer join Books F on E.BookID=F.BookID 

			left outer join BookQuizs EE on A.BookQuizID=EE.BookQuizID 
			left outer join Books FF on EE.BookID=FF.BookID 

			left outer join BookScans EEE on A.BookScanID=EEE.BookScanID 
			left outer join Books FFF on EEE.BookID=FFF.BookID 
		where A.ClassID=:ClassID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':ClassID', $ClassID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;


$ClassOrderID = $Row["ClassOrderID"];
$ClassID = $Row["ClassID"];
$ClassLinkType = $Row["ClassLinkType"];
$TeacherID = $Row["TeacherID"];
$ClassLevel = $Row["ClassLevel"];
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
$EndDateTime = $Row["EndDateTime"];

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
$BookRegForReason = $Row["BookRegForReason"];

$ClassRegDateTime = $Row["ClassRegDateTime"];
$ClassModiDateTime = $Row["ClassModiDateTime"];

$MemberName = $Row["MemberName"];
$MemberLoginID = $Row["MemberLoginID"];
$MemberCiTelephone = $Row["MemberCiTelephone"];

$TeacherName = $Row["TeacherName"];
$TeacherLoginID = $Row["TeacherLoginID"];
$TeacherCiTelephone = $Row["TeacherCiTelephone"];


$ClassProductID = $Row["ClassProductID"];

$BookSystemType = $Row["BookSystemType"];
$BookWebookUnitID = $Row["BookWebookUnitID"];
$BookWebookUnitName = $Row["BookWebookUnitName"];

$BookVideoBookGroupID = $Row["BookVideoBookGroupID"];
$BookVideoBookID = $Row["BookVideoBookID"];
$BookQuizBookGroupID = $Row["BookQuizBookGroupID"];
$BookQuizBookID = $Row["BookQuizBookID"];
$BookScanBookGroupID = $Row["BookScanBookGroupID"];
$BookScanBookID = $Row["BookScanBookID"];


if ($ClassAttendState==0){//기본으로 0 으로 등록 되지만 설정할때 기본은 1(출석)이다.
	//$ClassAttendState = 1;
}

if ($BookWebookUnitID=="0"){
	$BookWebookUnitID = "";
}

if ($CommonCiTelephoneStudent==""){
	$CommonCiTelephoneStudent = $MemberCiTelephone;
}

if ($CommonCiTelephoneTeacher==""){
	$CommonCiTelephoneTeacher = $TeacherCiTelephone;
}


//이전 교재 가져오기
if ($BookScanID==0){

	$Sql2 = "
			select 
				A.BookSystemType,
				A.BookScanID, 
				ifnull(FFF.BookGroupID,0) as BookScanBookGroupID, 
				ifnull(EEE.BookID,0) as BookScanBookID 
			from Classes A 
				left outer join BookScans EEE on A.BookScanID=EEE.BookScanID 
				left outer join Books FFF on EEE.BookID=FFF.BookID 
			where 
				A.ClassOrderID=$ClassOrderID 
				and (A.ClassAttendState=0 or A.ClassAttendState=1 or A.ClassAttendState=2) 
				and A.ClassState=2 
				and A.StartDateTimeStamp<".$StartDateTimeStamp." 
			order by A.StartDateTimeStamp desc limit 0,1";

	$Stmt2 = $DbConn->prepare($Sql2);
	$Stmt2->execute();
	$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
	$Row2 = $Stmt2->fetch();

	if ($Row2["BookSystemType"]==0){
		$BookScanID = $Row2["BookScanID"]; 
		$BookScanBookGroupID = $Row2["BookScanBookGroupID"]; 
		$BookScanBookID = $Row2["BookScanBookID"]; 
	}
	$Stmt2 = null;

}
//이전 교재 가져오기


// 웹북 API 조건 중 처음 스케쥴링 이거나 장기 연장일(14일)을 넘겼다면
// 새로히 진로정보를 전달하기 위함. ( submit js 함수로 체크 )
$Sql2 = "
		select 
				count(*) as Count,
				datediff(now(), :EndDateTime) as DateDiff
		from Classes A 

		where A.ClassID=:ClassID";
$Stmt2 = $DbConn->prepare($Sql2);
$Stmt2->bindParam(':ClassID', $ClassID);
$Stmt2->bindParam(':EndDateTime', $EndDateTime);
$Stmt2->execute();
$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
$Row2 = $Stmt2->fetch();
$Count = $Row2["Count"];
$DateDiff = $Row2["DateDiff"];

$Stmt2 = null;



if ($ClassLevel==0){

	$Sql2 = "
	select 
			ClassLevel
	from Classes A 
	where A.ClassOrderID=$ClassOrderID 
		and (A.ClassAttendState=0 or A.ClassAttendState=1 or A.ClassAttendState=2) 
		and A.ClassState=2 
		and A.StartDateTimeStamp<".$StartDateTimeStamp."
	order by A.StartDateTimeStamp desc limit 0, 1
	";

	$Stmt2 = $DbConn->prepare($Sql2);
	$Stmt2->execute();
	$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
	$Row2 = $Stmt2->fetch();
	$LastClassLevel = $Row2["ClassLevel"];
	$Stmt2 = null;

	if ($LastClassLevel){
		$ClassLevel = $LastClassLevel;
	}

}

if ($ClassLevel==0 || $ClassProductID==2 || $ClassProductID==3){
	$ClassLevel = 1;
}
?>


<div id="page_content">
	<div id="page_content_inner">
		<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
		
		<input type="hidden" name="ClassID" value="<?=$ClassID?>">
		<input type="hidden" name="BookWebookUnitID" value="<?=$BookWebookUnitID?>">
		<input type="hidden" name="BookWebookUnitName" value="<?=$BookWebookUnitName?>">
		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-large-7-10">
				<div class="md-card">
					<div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
						<div class="user_heading_content">
							<h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$StartYear?>.<?=$StartMonth?>.<?=$StartDay?> <?=$StrTitle?></span><span class="sub-heading" id="user_edit_position"><?=$설정[$LangID]?></span></h2>
						</div>
					</div>
					<div class="user_content">
						
						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-2">
									<label for="TeacherName"><?=$강사명[$LangID]?></label>
									&nbsp; &nbsp; <?=$TeacherName?>
								</div>
								<div class="uk-width-medium-1-2">
									<label for="MemberName"><?=$학생명[$LangID]?></label>
									&nbsp; &nbsp; <?=$MemberName?>
								</div>
							</div>
						</div>
						<hr>

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-2-10">
								<?=$강의도구[$LangID]?>
								</div>
								<div class="uk-width-medium-8-10">
									<span class="icheck-inline" style="width:18%;">
										<input type="radio" name="ClassLinkType" id="ClassLinkType1" value="1" <?if ($ClassLinkType==1){?>checked<?}?> data-md-icheck />
										<label for="ClassLinkType1" class="inline-label">Sae Ha</label>
									</span>
									<span class="icheck-inline" style="width:18%;">
										<input type="radio" name="ClassLinkType" id="ClassLinkType2" value="2" <?if ($ClassLinkType==2){?>checked<?}?> data-md-icheck />
										<label for="ClassLinkType2" class="inline-label">Class In</label>
									</span>
								</div>
							</div>
						</div>
						<hr>

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-1-2">
									<label for="CommonCiTelephoneTeacher">ClassIn Teacher Number</label>
									<input type="text" id="CommonCiTelephoneTeacher" name="CommonCiTelephoneTeacher" value="<?=$CommonCiTelephoneTeacher?>" class="md-input label-fixed"/>
								</div>
								<div class="uk-width-medium-1-2">
									<label for="CommonCiTelephoneStudent">ClassIn Student Number</label>
									<input type="text" id="CommonCiTelephoneStudent" name="CommonCiTelephoneStudent" value="<?=$CommonCiTelephoneStudent?>" class="md-input label-fixed"/>
								</div>
							</div>
						</div>
						<hr>

						<?php

							$student_id = $MemberLoginID;
							if ($DomainSiteID==1) { // SLP
								$cid =  "_eduvision_mangoi";
								$secret_key = "88f9c4204bddf494f349859f59a29cb5";
							} else if ($DomainSiteID==5) { // ENGLISHTELL
								$cid = "_eduvision_englishtell";
								$secret_key = "0bddcf68e3b724e022067b0784e48778";
							} else {
								$cid = "_eduvision_mangopie";
								$secret_key = "f32ebf51cd3324689a35c8ea02a3aed4";
							}
							
							include_once('../webook/_config.php');

							// ============================ Delete Soon
							{
								$option = array();
								
								// [공통정보(Head)]
								/*
								$option['보안코드'] = ''; // class에서 설정됨.
								$option['고객사도메인'] = ''; // class에서 설정됨.
								$option['접속단말종류'] = ''; // class에서 설정됨.
								$option['실행일시'] = ''; // class에서 설정됨.
								$option["학생아이디"] = ''; // class에서 설정됨.
								*/

								// [API 정보(Content)]	
								$option["언어"] = 'en';	// 'en', 'jp', 'cn' 설정	
								$option['타입'] = "대분류"; // "대분류", "과정", "교재", "유닛"	
								$option['상위타입아이디'] = ""; // 선택된 타입의 상위타입 아이디를 입력 (type='대분류'인 경우에는 '' 설정)

								// [공통정보(Tail)]
								$option['code복호값출력'] = 'No'; // code 복호값을 출력하려면 'Yes'를 설정한다
								$option['고객사정의필드'] = "index.php - 대분류 조회 (".$member["id"].")"; // 사용자정의 문자열로 고객사 시스템 파일 등을 지정할 수 있다 (디버깅 용도 등)	
								$option['API확장필드'] = ""; // API 확장 기능을 위한 필드
								$data = $JTW->query( "커리큘럼조회 API", $option );
							}														

							$curriculum = array();
							$curriculum["대분류"] = explode( "#", $data );

							// ================================= //
						?>

						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>

								<div class="uk-width-medium-2-10">
								<?=$교재시스템분류[$LangID]?>
								</div>
								<div class="uk-width-medium-8-10">

									<span class="icheck-inline" style="width:25%;">
                                        <input type="radio" id="BookSystemType0" name="BookSystemType" class="radio_input" <?php if ($BookSystemType==0) { echo "checked";}?> value="0" />
                                        <label for="BookSystemType0" class="radio_label" onclick="ChangeMatchBookItems(0);"><span class="radio_bullet"></span>Mangoi Book</label>
                                    </span>
                                    <span class="icheck-inline" style="width:25%;">
                                        <input type="radio" id="BookSystemType1" name="BookSystemType" class="radio_input" <?php if ($BookSystemType==1) { echo "checked";}?> value="1" />
                                        <label for="BookSystemType1" class="radio_label" onclick="ChangeMatchBookItems(1);"><span class="radio_bullet"></span>JT Book</label>
                                    </span>
								</div>

								<div class="uk-width-medium-2-10">
								<?=$학습교재[$LangID]?>
								</div>
								<!-- JT-webook -->
								<div class="uk-width-medium-8-10" id="BookSystemType_JT" style="display: <?if($BookSystemType==0){?>none<?}else{?>''<?}?>" >
								<!-- name 은 우리양식, id는 웹북 양식을 따르다보니 다름 -->
									
									<?if ($BookWebookUnitID!=""){?>
									<div style="margin-bottom:10px;color:#0080C0;">현재 션택된 교재 : <b><?=$BookWebookUnitName?></b></div>
									<div style="margin-bottom:10px;color:#9F0000;">※ JT 웹북을 변경하시려면 [대분류]부터 선택해 주세요.</div>
									<div style="margin-bottom:10px;color:#9F0000;">※ 유닛까지 모두 선택하셔야 변경됩니다.</div>
									<?}?>
									
									<select name="group" id="group" style="height:30px;width:90%;" onchange="">
										<option value="">대분류를 선택하세요.</option>
										<?
										foreach( $curriculum["대분류"] as $key => $value ) {
											$arr = explode("|", $value);
											{
												$fields["영문이름"] = $arr[0];
												$fields["한글이름"] = $arr[1];
												$fields["타입아이디"] = $arr[2];
											}
											?>
											<option value="<?=$fields["타입아이디"]?>"><?=$fields["영문이름"]?>(<?=$fields["한글이름"]?>)</option>
										<?
										}
										?>
									</select>
									<div id="category_div"></div>
									<div id="book_div"></div>
									<div id="unit_div"></div>

								</div>
								<!-- mangoi -->
								<div class="uk-width-medium-8-10" id="BookSystemType_mangoi" style="display: <?if($BookSystemType==1){?>none<?}else{?>''<?}?>" >
									<select name="BookScanBookGroupID" id="BookScanBookGroupID" style="height:30px;" onchange="ChBookScanBookGroupID()">
										<option value="0"><?=$그룹선택[$LangID]?></option>
										<?
										$Sql2 = "select A.* from BookGroups A where A.BookGroupView=1 and A.BookGroupState=1 order by A.BookGroupOrder asc";
										$Stmt2 = $DbConn->prepare($Sql2);
										$Stmt2->execute();
										$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
										while($Row2 = $Stmt2->fetch()) {
										?>
										<option value="<?=$Row2["BookGroupID"]?>" <?if ($BookScanBookGroupID==$Row2["BookGroupID"]){?>selected<?}?>><?=$Row2["BookGroupName"]?></option>
										<?
										}
										$Stmt2 = null;
										?>
									</select>
									<select name="BookScanBookID" id="BookScanBookID" style="height:30px;" onchange="ChBookScanBookID()">
										<option value="0"><?=$교재선택[$LangID]?></option>
									</select>
									<select name="BookScanID" id="BookScanID" style="height:30px;">
										<option value="0"><?=$학습교재[$LangID]?></option>
									</select>
								</div>
								
								<div class="uk-width-medium-2-10">
								<?=$레슨비디오[$LangID]?>
								</div>
								<div class="uk-width-medium-8-10">
									<select name="BookVideoBookGroupID" id="BookVideoBookGroupID" style="height:30px;" onchange="ChBookVideoBookGroupID()">
										<option value="0"><?=$그룹선택[$LangID]?></option>
										<?
										$Sql2 = "select A.* from BookGroups A where A.BookGroupView=1 and A.BookGroupState=1 order by A.BookGroupOrder asc";
										$Stmt2 = $DbConn->prepare($Sql2);
										$Stmt2->execute();
										$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
										while($Row2 = $Stmt2->fetch()) {
										?>
										<option value="<?=$Row2["BookGroupID"]?>" <?if ($BookVideoBookGroupID==$Row2["BookGroupID"]){?>selected<?}?>><?=$Row2["BookGroupName"]?></option>
										<?
										}
										$Stmt2 = null;
										?>
									</select>
									<select name="BookVideoBookID" id="BookVideoBookID" style="height:30px;" onchange="ChBookVideoBookID()">
										<option value="0"><?=$교재선택[$LangID]?></option>
									</select>
									<select name="BookVideoID" id="BookVideoID" style="height:30px;">
										<option value="0"><?=$비디오선택[$LangID]?></option>
									</select>
								</div>
							</div>

							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-2-10">
								<?=$리뷰퀴즈[$LangID]?>
								</div>
								<div class="uk-width-medium-8-10">
									<select name="BookQuizBookGroupID" id="BookQuizBookGroupID" style="height:30px;" onchange="ChBookQuizBookGroupID()">
										<option value="0"><?=$그룹선택[$LangID]?></option>
										<?
										$Sql2 = "select A.* from BookGroups A where A.BookGroupView=1 and A.BookGroupState=1 order by A.BookGroupOrder asc";
										$Stmt2 = $DbConn->prepare($Sql2);
										$Stmt2->execute();
										$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
										while($Row2 = $Stmt2->fetch()) {
										?>
										<option value="<?=$Row2["BookGroupID"]?>" <?if ($BookQuizBookGroupID==$Row2["BookGroupID"]){?>selected<?}?>><?=$Row2["BookGroupName"]?></option>
										<?
										}
										$Stmt2 = null;
										?>
									</select>
									<select name="BookQuizBookID" id="BookQuizBookID" style="height:30px;" onchange="ChBookQuizBookID()">
										<option value="0"><?=$교재선택[$LangID]?></option>
									</select>
									<select name="BookQuizID" id="BookQuizID" style="height:30px;">
										<option value="0"><?=$퀴즈선택[$LangID]?></option>
									</select>
								</div>
							</div>

							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-2-10">
									자료 미등록
								</div>
								<div class="uk-width-medium-8-10">
									<input type="checkbox" name="BookRegForReason" value="1" <?if ($BookRegForReason==1){?>checked<?}?> data-md-icheck/><span style="color: #FF0000; font-size: 14px;">&nbsp;&nbsp;&nbsp;// Other Books</span>
									<!-- // verified unregistered Book/Video/Quiz -->
								</div>
							</div>
						</div>
						<hr>

						<div class="uk-margin-top" style="display:<?if ($ClassProductID!=1){?>none<?}?>;">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-2-10">
								LEVEL
								</div>
								<div class="uk-width-medium-8-10">
									<span class="icheck-inline" style="width:18%;">
										<input type="radio" name="ClassLevel" id="ClassLevel1" value="1" <?if ($ClassLevel==1){?>checked<?}?> data-md-icheck />
										<label for="ClassLevel1" class="inline-label">L-1</label>
									</span>
									<span class="icheck-inline" style="width:18%;">
										<input type="radio" name="ClassLevel" id="ClassLevel2" value="2" <?if ($ClassLevel==2){?>checked<?}?> data-md-icheck />
										<label for="ClassLevel2" class="inline-label">L-2</label>
									</span>
									<span class="icheck-inline" style="width:18%;">
										<input type="radio" name="ClassLevel" id="ClassLevel3" value="3" <?if ($ClassLevel==3){?>checked<?}?> data-md-icheck />
										<label for="ClassLevel3" class="inline-label">L-3</label>
									</span>
									<span class="icheck-inline" style="width:18%;">
										<input type="radio" name="ClassLevel" id="ClassLevel4" value="4" <?if ($ClassLevel==4){?>checked<?}?> data-md-icheck />
										<label for="ClassLevel4" class="inline-label">L-4</label>
									</span>
									<span class="icheck-inline" style="width:18%;">
										<input type="radio" name="ClassLevel" id="ClassLevel5" value="5" <?if ($ClassLevel==5){?>checked<?}?> data-md-icheck />
										<label for="ClassLevel5" class="inline-label">L-5</label>
									</span>
									<span class="icheck-inline" style="width:18%;">
										<input type="radio" name="ClassLevel" id="ClassLevel6" value="6" <?if ($ClassLevel==6){?>checked<?}?> data-md-icheck />
										<label for="ClassLevel6" class="inline-label">L-6</label>
									</span>
									<span class="icheck-inline" style="width:18%;">
										<input type="radio" name="ClassLevel" id="ClassLevel7" value="7" <?if ($ClassLevel==7){?>checked<?}?> data-md-icheck />
										<label for="ClassLevel7" class="inline-label">L-7</label>
									</span>
									<span class="icheck-inline" style="width:18%;">
										<input type="radio" name="ClassLevel" id="ClassLevel8" value="8" <?if ($ClassLevel==8){?>checked<?}?> data-md-icheck />
										<label for="ClassLevel8" class="inline-label">L-8</label>
									</span>
								</div>
							</div>
						</div>
						<hr style="display:<?if ($ClassProductID!=1){?>none<?}?>;">


						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-2-10">
								<?=$상태[$LangID]?>
								</div>
								<div class="uk-width-medium-8-10">
									<span class="icheck-inline" style="width:25%;">
										<input type="radio" name="ClassState" id="ClassState1" value="1" <?if ($ClassState==1){?>checked<?}?> data-md-icheck />
										<label for="ClassState1" class="inline-label"><?=$등록[$LangID]?></label>
									</span>
									<span class="icheck-inline" style="width:25%;">
										<input type="radio" name="ClassState" id="ClassState2" value="2" <?if ($ClassState==2){?>checked<?}?> data-md-icheck />
										<label for="ClassState2" class="inline-label"><?=$수업종료[$LangID]?></label>
									</span>
								</div>
							</div>
						</div>
						<hr>




						<div class="uk-margin-top">
							<div class="uk-grid" data-uk-grid-margin>
								<div class="uk-width-medium-2-10">
								<?=$수업상태[$LangID]?>
								</div>
								<div class="uk-width-medium-8-10">

									<span class="icheck-inline" style="width:18%;">
										<input type="radio" name="ClassAttendState" id="ClassAttendState0" value="0" <?if ($ClassAttendState==0){?>checked<?}?> data-md-icheck />
										<label for="ClassAttendState0" class="inline-label"><?=$미설정[$LangID]?></label>
									</span>
									<span class="icheck-inline" style="width:18%;">
										<input type="radio" name="ClassAttendState" id="ClassAttendState1" value="1" <?if ($ClassAttendState==1){?>checked<?}?> data-md-icheck />
										<label for="ClassAttendState1" class="inline-label"><?=$출석[$LangID]?></label>
									</span>
									<span class="icheck-inline" style="width:18%;">
										<input type="radio" name="ClassAttendState" id="ClassAttendState2" value="2" <?if ($ClassAttendState==2){?>checked<?}?> data-md-icheck />
										<label for="ClassAttendState2" class="inline-label"><?=$지각[$LangID]?></label>
									</span>
									<span class="icheck-inline" style="width:18%;">
										<input type="radio" name="ClassAttendState" id="ClassAttendState3" value="3" <?if ($ClassAttendState==3){?>checked<?}?> data-md-icheck />
										<label for="ClassAttendState3" class="inline-label"><?=$결석[$LangID]?></label>
									</span>

									<!--
									<br>
									
									<span class="icheck-inline" style="width:18%;">
										<input type="radio" name="ClassAttendState" id="ClassAttendState4" value="4" <?if ($ClassAttendState==4){?>checked<?}?> data-md-icheck />
										<label for="ClassAttendState4" class="inline-label">학생연기</label>
									</span>
									<span class="icheck-inline" style="width:18%;">
										<input type="radio" name="ClassAttendState" id="ClassAttendState5" value="5" <?if ($ClassAttendState==5){?>checked<?}?> data-md-icheck />
										<label for="ClassAttendState5" class="inline-label">강사연기</label>
									</span>
									<span class="icheck-inline" style="width:18%;">
										<input type="radio" name="ClassAttendState" id="ClassAttendState6" value="6" <?if ($ClassAttendState==6){?>checked<?}?> data-md-icheck />
										<label for="ClassAttendState6" class="inline-label">학생취소</label>
									</span>
									<span class="icheck-inline" style="width:18%;">
										<input type="radio" name="ClassAttendState" id="ClassAttendState7" value="7" <?if ($ClassAttendState==7){?>checked<?}?> data-md-icheck />
										<label for="ClassAttendState7" class="inline-label">강사취소</label>
									</span>
									-->
								</div>
							</div>
						</div>
						<hr>

						<div class="uk-margin-top" style="text-align:center;padding-top:30px;">
							<a type="button" href="javascript:FormSubmit();" class="md-btn md-btn-primary">SUBMIT</a>
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

var OldBookWebookUnitID = document.RegForm.BookWebookUnitID.value;

function FormSubmit(){

	obj1 = document.RegForm.CommonCiTelephoneTeacher;
	obj2 = document.RegForm.CommonCiTelephoneStudent;
	
	
	if (document.RegForm.BookSystemType[0].checked){
		BookSystemType = 0;
	}else if (document.RegForm.BookSystemType[1].checked){
		BookSystemType = 1;
	}
	
	
	if (document.RegForm.ClassLinkType[1].checked && (obj1.value=="" || obj2.value=="")){
		UIkit.modal.alert("클래스인 아이디를 모두 입력하세요.");
		return;
	}

	var JtResult = true;
	var BookWebookUnitName = "";
	if(BookSystemType==1) { // jt webook 일때만.
		// 첫 스케쥴링이거나, 마지막 수업과 현재의 날짜가 14일 이상 차이가 난다면..
		JtResult = SendStudentClassInfo();
		// 그외에는 따로 진로정보를 보내지않는다.
		
		if (JtResult==true){
			BookWebookUnitName = $("#unit option:checked").text();
			document.RegForm.BookWebookUnitName.value = BookWebookUnitName;
		}
			
	}

	if (OldBookWebookUnitID!=""){
		JtResult=true;
	}

	if (JtResult==true){

		UIkit.modal.confirm(
			'저장 하시겠습니까?', 
			function(){ 
				document.RegForm.action = "class_setup_action.php";
				document.RegForm.submit();
			}
		);

	}


}

function ChangeMatchBookItems(BookSystemType) {
//	alert("hi");
	//alert(BookSystemType);
	var	BookSystemType_mangoi = document.getElementById("BookSystemType_mangoi");
	var	BookSystemType_JT = document.getElementById("BookSystemType_JT");

	if(BookSystemType==0) {
		BookSystemType_mangoi.style.display = "";
		BookSystemType_JT.style.display = "none";
	} else {
		BookSystemType_mangoi.style.display = "none";
		BookSystemType_JT.style.display = "";
	}
}

function ChBookVideoBookGroupID(){

	BookVideoBookGroupID = document.RegForm.BookVideoBookGroupID.value;
	
	url = "ajax_get_book_video_book_id.php";
	//window.open(url + "?BookVideoBookGroupID="+BookVideoBookGroupID);
	$.ajax(url, {
		data: {
			BookVideoBookGroupID: BookVideoBookGroupID,
			SelectedBookID: "<?=$BookVideoBookID?>"
		},
		success: function (data) {

			ArrOption = data.BookVideoBookIDs.split("{{|}}");
			SelBoxInitOption('BookVideoBookID');

			SelBoxAddOption( 'BookVideoBookID', '<?=$교재선택[$LangID]?>', "0", "");
			for (ii=2 ; ii<=ArrOption.length-1 ; ii++ ){
				ArrOptionText     = ArrOption[ii].split("{|}")[0];
				ArrOptionValue    = ArrOption[ii].split("{|}")[1];
				ArrOptionSelected = ArrOption[ii].split("{|}")[2];
				SelBoxAddOption( 'BookVideoBookID', ArrOptionText, ArrOptionValue, ArrOptionSelected );
			}

			ChBookVideoBookID();
		},
		error: function () {
			alert('오류가 발생했습니다. 다시 시도해 주세요.');
		}
	});	

}
function ChBookVideoBookID(){ 

	BookVideoBookID = document.RegForm.BookVideoBookID.value;
	
	url = "ajax_get_book_video_book_video_id.php";
	//window.open(url + "?BookVideoBookID="+BookVideoBookID);
	$.ajax(url, {
		data: {
			BookVideoBookID: BookVideoBookID,
			SelectedBookVideoID: "<?=$BookVideoID?>"
		},
		success: function (data) {

			ArrOption = data.BookVideoBookVideoIDs.split("{{|}}");
			SelBoxInitOption('BookVideoID');

			SelBoxAddOption( 'BookVideoID', '<?=$비디오선택[$LangID]?>', "0", "");
			for (ii=2 ; ii<=ArrOption.length-1 ; ii++ ){
				ArrOptionText     = ArrOption[ii].split("{|}")[0];
				ArrOptionValue    = ArrOption[ii].split("{|}")[1];
				ArrOptionSelected = ArrOption[ii].split("{|}")[2];
				SelBoxAddOption( 'BookVideoID', ArrOptionText, ArrOptionValue, ArrOptionSelected );
			}
		},
		error: function () {
			//alert('오류가 발생했습니다. 다시 시도해 주세요.');
		}
	});	

}
function ChBookQuizBookGroupID(){
	BookQuizBookGroupID = document.RegForm.BookQuizBookGroupID.value;
	
	url = "ajax_get_book_quiz_book_id.php";
	//window.open(url + "?BookVideoBookGroupID="+BookVideoBookGroupID);
	$.ajax(url, {
		data: {
			BookQuizBookGroupID: BookQuizBookGroupID,
			SelectedBookID: "<?=$BookQuizBookID?>"
		},
		success: function (data) {

			ArrOption = data.BookQuizBookIDs.split("{{|}}");
			SelBoxInitOption('BookQuizBookID');

			SelBoxAddOption( 'BookQuizBookID', '<?=$교재선택[$LangID]?>', "0", "");
			for (ii=2 ; ii<=ArrOption.length-1 ; ii++ ){
				ArrOptionText     = ArrOption[ii].split("{|}")[0];
				ArrOptionValue    = ArrOption[ii].split("{|}")[1];
				ArrOptionSelected = ArrOption[ii].split("{|}")[2];
				SelBoxAddOption( 'BookQuizBookID', ArrOptionText, ArrOptionValue, ArrOptionSelected );
			}

			ChBookQuizBookID();
		},
		error: function () {
			//alert('오류가 발생했습니다. 다시 시도해 주세요.');
		}
	});	
}
function ChBookQuizBookID(){
	BookQuizBookID = document.RegForm.BookQuizBookID.value;
	
	url = "ajax_get_book_quiz_book_quiz_id.php";
	//window.open(url + "?BookQuizBookID="+BookQuizBookID);
	$.ajax(url, {
		data: {
			BookQuizBookID: BookQuizBookID,
			SelectedBookQuizID: "<?=$BookQuizID?>"
		},
		success: function (data) {

			ArrOption = data.BookQuizBookQuizIDs.split("{{|}}");
			SelBoxInitOption('BookQuizID');

			SelBoxAddOption( 'BookQuizID', '<?=$퀴즈선택[$LangID]?>', "0", "");
			for (ii=2 ; ii<=ArrOption.length-1 ; ii++ ){
				ArrOptionText     = ArrOption[ii].split("{|}")[0];
				ArrOptionValue    = ArrOption[ii].split("{|}")[1];
				ArrOptionSelected = ArrOption[ii].split("{|}")[2];
				SelBoxAddOption( 'BookQuizID', ArrOptionText, ArrOptionValue, ArrOptionSelected );
			}
		},
		error: function () {
			//alert('오류가 발생했습니다. 다시 시도해 주세요.');
		}
	});	
}
function ChBookScanBookGroupID(){
	BookScanBookGroupID = document.RegForm.BookScanBookGroupID.value;
	
	url = "ajax_get_book_scan_book_id.php";
	//window.open(url + "?BookVideoBookGroupID="+BookVideoBookGroupID);
	$.ajax(url, {
		data: {
			BookScanBookGroupID: BookScanBookGroupID,
			SelectedBookID: "<?=$BookScanBookID?>"
		},
		success: function (data) {

			ArrOption = data.BookScanBookIDs.split("{{|}}");
			SelBoxInitOption('BookScanBookID');

			SelBoxAddOption( 'BookScanBookID', '<?=$교재선택[$LangID]?>', "0", "");
			for (ii=2 ; ii<=ArrOption.length-1 ; ii++ ){
				ArrOptionText     = ArrOption[ii].split("{|}")[0];
				ArrOptionValue    = ArrOption[ii].split("{|}")[1];
				ArrOptionSelected = ArrOption[ii].split("{|}")[2];
				SelBoxAddOption( 'BookScanBookID', ArrOptionText, ArrOptionValue, ArrOptionSelected );
			}

			ChBookScanBookID();
		},
		error: function () {
			//alert('오류가 발생했습니다. 다시 시도해 주세요.');
		}
	});	
}
function ChBookScanBookID(){
	BookScanBookID = document.RegForm.BookScanBookID.value;
	
	url = "ajax_get_book_scan_book_scan_id.php";

	//window.open(url + "?BookScanBookID="+BookScanBookID+"&SelectedBookScanID=<?=$BookScanID?>&TeacherID=<?=$TeacherID?>");
	$.ajax(url, {
		data: {
			BookScanBookID: BookScanBookID,
			SelectedBookScanID: "<?=$BookScanID?>",
			TeacherID: "<?=$TeacherID?>"
		},
		success: function (data) {
			
			ArrOption = data.BookScanBookScanIDs.split("{{|}}");
			SelBoxInitOption('BookScanID');

			SelBoxAddOption( 'BookScanID', '<?=$학습교재[$LangID]?>', "0", "");
			for (ii=2 ; ii<=ArrOption.length-1 ; ii++ ){
				ArrOptionText     = ArrOption[ii].split("{|}")[0];
				ArrOptionValue    = ArrOption[ii].split("{|}")[1];
				ArrOptionSelected = ArrOption[ii].split("{|}")[2];
				SelBoxAddOption( 'BookScanID', ArrOptionText, ArrOptionValue, ArrOptionSelected );
			}
		},
		error: function () {
			//alert('오류가 발생했습니다. 다시 시도해 주세요.');
		}
	});	
}

window.onload = function(){
	ChBookVideoBookGroupID();
	ChBookQuizBookGroupID();
	ChBookScanBookGroupID();
}
</script>

<script>
/** ===================================== 기본함수 ===================================== **/
// Option객체를 생성해서 Return
function SelBoxCreateOption( text, value, selected )
{
	var oOption = document.createElement("OPTION"); // Option 객체를 생성
	oOption.text = text; // Text(Keyword)를 입력
	oOption.value = value; // Value를 입력
	if (selected=="selected"){
		oOption.selected = true;
	}
	return oOption;
}

// SelectBox의 Option을 초기화
function SelBoxInitOption( ObjId ){
	var SelectObj = document.getElementById( ObjId );
	if ( SelectObj == null ) return; // 객체가 존재하지 않으면 취소

	SelectObj.options.length = 0; // 길이를 0으로 하면 초기화
}

// Option을 추가
function SelBoxAddOption( ObjId, text, value, selected ){
	var SelectObj = document.getElementById( ObjId );

	SelectObj.add( SelBoxCreateOption( text , value, selected ) );
	text     = "";
	value    = "";
	selected = "";
}
/** ===================================== 기본함수 ===================================== **/
</script>

<script>
var path = "../webook/";

$("#group").change( function() {
	init_category();

	$.post(path+"_get_curri.php", { type:'과정', type_id:$(this).val(), MemberLoginID: "<?=$MemberLoginID?>", secret_key: "<?=$secret_key?>", cid: "<?=$cid?>" }, function( data ) {

		$("#category_div").html( data ); 

		$("#category").change( function() {
			
			init_book(); 

			$.post(path+ "_get_curri.php", { type:'교재', type_id:$(this).val(), MemberLoginID: "<?=$MemberLoginID?>", secret_key: "<?=$secret_key?>", cid: "<?=$cid?>" }, function( data ) {
				
				$("#book_div").html( data ); 				

				$("#book").change( function() {
					
					init_unit();

					// max_chapter_unit은 '학생아이디'별 열람가능한 최대 유닛정보를 의미한다. (_get_curri.php 파일 구현 참고)
					$.post(path+ "_get_curri.php", { type:'유닛', type_id:$(this).val(), MemberLoginID: "<?=$MemberLoginID?>", secret_key: "<?=$secret_key?>", cid: "<?=$cid?>", max_chapter_unit:$("#book option:selected").attr("max-chapter-unit"), current_chapter_unit:$("#book option:selected").attr("current-chapter-unit") }, function( data ) {
						
						$('#unit_div').html( data );
						
						$("#unit").change( function() {
							init_content();
							$.post( path+"_get_unit_content.php", { content_type:"학생", unit_id:$("#unit").val(), api_extension:'', width:"100%", height: "100%", unit_contents_type:"일반교재", secret_key: "<?=$secret_key?>", cid: "<?=$cid?>" })
							.done(function( data ) {

							});
						} );	
						
						$("#content_type").change( function() {
							$("#unit").change();
						} );

						$("#unit_content_type").change( function() {
							$("#unit").change();
						} );
					} );
				} );
			} );
		} );
	} );
} );

// 각 선택박스 영역을 초기화 하는 함수 (매겨변후 이하의 선택박스를 초기화 한다, '대분류','과정','교재',유닛')
function init_content()
{
	$("#content").empty();
}

function init_audio()
{
	$("#audio_div").hide();
}

function init_unit()
{
	init_content();
	init_audio();
	$("#unit_div").empty();
}

function init_book()
{
	init_unit();
	$("#book_div").empty();
}

function init_category()
{
	init_book();
	$("#category_div").empty();
}


function init_group()
{
	init_category();
	$("#group_div").empty();
}

// 학생최종진도 모듈 조회
$(".btn_get_user_usage").on( "click", function() {

	$type = $(this).attr("type");
	$type_id = "";
	if ( $type == "대분류" ) {
		$type_id = $("#group").val();
	} else if ( $type == "과정" ) {
		$type_id = $("#category").val();
	} else if ( $type == "교재" ) {
		$type_id = $("#book").val();
	}

	if ( $type != "" ) {	
		if ( $type_id == "" && OldBookWebookUnitID=="" ) {
			alert( "먼저 '" + $type + "'을 선택해 주세요" );
			return;
		}
	}

	$.post(path+ "_get_user_usage.php", { type: $(this).attr("type"), type_id: $type_id, MemberLoginID: "<?=$MemberLoginID?>", secret_key: "<?=$secret_key?>", cid: "<?=$cid?>" }, function( data ) {
		alert( data );
	} );
} );


// 특정 타입(대분류, 과정, 교재, 유닛)의 상세 정보 조회
$(".btn_get_type_info").on( "click", function() {

	$type = $(this).attr( "type" );
	$type_id = "";
	{
		if ( $type == "대분류" ) {
			$type_id = $("#group").val();
		} else if ( $type == "과정" ) {
			$type_id = $("#category").val();
		} else if ( $type == "교재" ) {
			$type_id = $("#book").val();			
		} else if ( $type == "유닛" ) {
			$type_id = $("#unit").val();			
		}
	}

	if ( $type_id ) { 
		$.post(path+ "_get_type_info.php", { type:$type, type_id:$type_id, MemberLoginID: "<?=$MemberLoginID?>", secret_key: "<?=$secret_key?>", cid: "<?=$cid?>" }, function( data ) { 
			//alert( data );	
			console.log(data);
		} );
	} else {
		alert( $type+"을 선택해 주세요." );	
	}
} );


// JT-Webbook 서버로 학생진도 정보를 전송
function SendStudentClassInfo() {	
	$unit_id = $('#unit').val();
	if ( $unit_id ) {
		$.post(path+ "_put_user_usage.php", { type:'유닛', unit_id:$unit_id, MemberLoginID: "<?=$MemberLoginID?>", secret_key: "<?=$secret_key?>", cid: "<?=$cid?>" }, function( data ) { 
			console.log( data );
			//alert(data);
		} );
		return true;
	} else {
		
		if (OldBookWebookUnitID==""){
			alert( 'JT-Webook 이용 시, 유닛까지 선택해주세요.' );
			return false;
		}
	}
}


// JT-Webbook 학생웹북을 바로가기 기능으로 실행하는 경우 (유닛아이디에 대한 바로가기만 구현한 예시임)
$(".student_webbook_shortcut").on( "click", function() { 	

	$type = $(this).attr( "type" );
	$device = $(this).attr( "device" );

	$type_id = $("#"+$type).val();
	if ( $type_id ) {
		if ( $device == "mobile" ) {
			url = $("#student_webbook_mobile").attr("href") + "&shortcut=" + $type_id + "@" + $type;
		} else {
			url = $("#student_webbook").attr("href") + "&shortcut=" + $type_id + "@" + $type;			
		}
		$(this).attr( "href", url ).click();
	} else {
		if (OldBookWebookUnitID==""){

			if ( $type == 'unit' ) {
				alert( "먼저 유닛을 선택해 주세요." );
			} else if ( $type == 'book' ) {
				alert( "먼저 교재를 선택해 주세요." );	
			} else if ( $type == 'category' ) {
				alert( "먼저 과정을 선택해 주세요." );	
			}
			return false;
		}
	}
} );
</script>

<?php
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>