<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./class_order_bulk_time_check_fnc.php');

//버퍼 켜기 ===============================
ob_start();
//버퍼 켜기 ===============================


//================================================================
// 7일 이하동안 유지되는 슬랏 중에 그 기간내에 수업이 없는 슬랏을 삭제 처리
$Sql = "SELECT 
					distinct ClassOrderSlotID 
				FROM View_ClassOrderSlotDelTargets 
				WHERE 
					ClassOrderSlotID NOT in (SELECT ClassOrderSlotID FROM View_ClassOrderSlotDelTargets WHERE ClassOrderSlotWeek=StudyWeek)";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

while($Row = $Stmt->fetch()) {

    $DelClassOrderSlotID = $Row["ClassOrderSlotID"];

    $Sql2 = "
		update ClassOrderSlots set 
			ClassOrderSlotState=0,
			DelAdminUnder7Day=1,
			DelAdminUnder7DayDateTime=now(),
			ClassOrderSlotDateModiDateTime=now()
		where ClassOrderSlotID=$DelClassOrderSlotID
	";
    $Stmt2 = $DbConn->prepare($Sql2);
    $Stmt2->execute();
    $Stmt2 = null;

}
$Stmt = null;
//================================================================



$ErrNum = 0;
$ErrMsg = "";
$UploadFileName = isset($_REQUEST["UploadFileName"]) ? $_REQUEST["UploadFileName"] : "";

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $_SITE_TITLE_;?></title>
    <link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
    <link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
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
<body>

<div id="page_content">
    <div id="page_content_inner">
        <h3 class="heading_b uk-margin-bottom" style="text-align:center;margin-top:-30px;">강사 선택</h3>
        <form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
            <div style="text-align:right; ">

            </div>
            <div class="md-card">
                <div class="md-card-content">
                    <div class="uk-grid" data-uk-grid-margin>
                        <div class="uk-width-1-1">
                            <div class="uk-overflow-container">
                                <span style="float:right; font-size:12px;">※ 강사를 선택해 주세요.</span>

                                <!-- ===========================================  table ================================================= -->
                                <table class="uk-table uk-table-align-vertical" style="width:100%;margin-top:20px;">
                                    <thead>
                                    <tr style="background-color:gray">
                                        <th style="border: 1px solid black"><?=$대리점[$LangID]?>/<?=$학생명[$LangID]?>/<?=$아이디[$LangID]?></th>
                                        <th style="border: 1px solid black"><?=$수업구분[$LangID]?></th>
                                        <th style="border: 1px solid black"><?=$테스트레벨[$LangID]?></th>
                                        <th style="border: 1px solid black"><?=$수업시간[$LangID]?></th>
                                        <th style="border: 1px solid black"><?=$시작일[$LangID]?></th>
                                        <th style="border: 1px solid black"><?=$체험[$LangID]?>/<?=$레벨시간[$LangID]?></th>
                                        <th style="border: 1px solid black"><?=$월[$LangID]?></th>
                                        <th style="border: 1px solid black"><?=$화[$LangID]?></th>
                                        <th style="border: 1px solid black"><?=$수[$LangID]?></th>
                                        <th style="border: 1px solid black"><?=$목[$LangID]?></th>
                                        <th style="border: 1px solid black"><?=$금[$LangID]?></th>
                                        <th style="border: 1px solid black"><?=$등록여부[$LangID]?></th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    <?
                                    $LinkAdminLevelID = $_LINK_ADMIN_LEVEL_ID_;
                                    $EduCenterID = 1;
                                    $ArrWeekName = explode("|", "일요일|월요일|화요일|수요일|목요일|금요일|토요일");


                                    include_once("../PHPExcel-1.8/Classes/PHPExcel.php");

                                    libxml_use_internal_errors(true); // 일반적인 경고문을 안보여주는...https://codeday.me/ko/qa/20190325/149807.html also stackoverflow too,
                                    $objPHPExcel = new PHPExcel();
                                    $filename = $UploadFileName; // 읽어들일 엑셀 파일의 경로와 파일명을 지정한다.

                                    try {

                                        // 업로드 된 엑셀 형식에 맞는 Reader객체를 만든다.
                                        $objReader = PHPExcel_IOFactory::createReaderForFile($filename);
                                        // 읽기전용으로 설정
                                        $objReader->setReadDataOnly(true);
                                        // 엑셀파일을 읽는다
                                        $objExcel = $objReader->load($filename);
                                        // 첫번째 시트를 선택
                                        $objExcel->setActiveSheetIndex(0);
                                        $objWorksheet = $objExcel->getActiveSheet();
                                        $rowIterator = $objWorksheet->getRowIterator();

                                        foreach ($rowIterator as $row) { // 모든 행에 대해서
                                            $cellIterator = $row->getCellIterator();
                                            $cellIterator->setIterateOnlyExistingCells(false);
                                        }

                                        $maxRow = $objWorksheet->getHighestRow();



                                        $ListNum = 1;
                                        $RegOk = 1;
                                        $SelectForms = ",";
                                        for ($i = 11 ; $i <= $maxRow ; $i++) {

                                            $LineRegOk = 1;

                                            include("./class_order_bulk_excel_check_inc.php");


                                            if ($DataOk[1]==1 && $DataOk[2]==1 && $DataOk[3]==1 && $DataOk[4]==1 && $DataOk[5]==1 && $DataOk[6]==1 && $DataOk[7]==1 && $DataOk[8]==1 && $DataOk[9]==1 && $DataOk[10]==1){


                                                $InputClassOrderStartDate = $TempClassStartDate;

                                                //1: 강좌 2:레벨테스트 3:체험수업
                                                if ($TempClassType=="정규"){
                                                    $InputClassProductID = 1;
                                                    $InputClassOrderLeveltestApplyLevel = 1;//기본값 1
                                                    $StrClassOrderLeveltestApplyLevel = "";
                                                }else if ($TempClassType=="레벨"){
                                                    $InputClassProductID = 2;
                                                    $InputClassOrderWeekCountID = 1;
                                                    $InputClassOrderLeveltestApplyLevel = $TempClassOrderLeveltestApplyLevel;
                                                    $StrClassOrderLeveltestApplyLevel = "LEVEL ".$TempClassOrderLeveltestApplyLevel;
                                                }else if ($TempClassType=="체험"){
                                                    $InputClassProductID = 3;
                                                    $InputClassOrderWeekCountID = 1;
                                                    $InputClassOrderLeveltestApplyLevel = 1;//기본값 1
                                                    $StrClassOrderLeveltestApplyLevel = "";
                                                }
                                                $ClassProductID = $InputClassProductID;


                                                $TempClassStartDateWeekDay = date("w", strtotime($TempClassStartDate));
                                                $date=date_create($TempClassStartDate);
                                                date_add($date, date_interval_create_from_date_string("-".$TempClassStartDateWeekDay." days"));
                                                $TempClassStartDateWeekStartDate = date_format($date, "Y-m-d");//선택한날 기준으로 이전 일요일


                                                ?>


                                                <tr>
                                                    <td style="border: 1px solid black;text-align:left;padding-left:20px;line-height:1.5;width:350px;">
						<span style="display:none;">
						아이디 : <input type="text" name="MemberID_<?=$ListNum?>" id="MemberID_<?=$ListNum?>" value="<?=$InputMemberID?>"><br>
						상품 : <input type="text" name="ClassProductID_<?=$ListNum?>" id="ClassProductID_<?=$ListNum?>" value="<?=$InputClassProductID?>"><br>
						인원 : <input type="text" name="ClassMemberType_<?=$ListNum?>" id="ClassMemberType_<?=$ListNum?>" value="<?=$InputClassMemberType?>"><br>
						시수 : <input type="text" name="ClassOrderTimeTypeID_<?=$ListNum?>" id="ClassOrderTimeTypeID_<?=$ListNum?>" value="<?=$InputClassOrderTimeTypeID?>"><br>
						회수 : <input type="text" name="ClassOrderWeekCountID_<?=$ListNum?>" id="ClassOrderWeekCountID_<?=$ListNum?>" value="<?=$InputClassOrderWeekCountID?>"><br>
						시작 : <input type="text" name="ClassOrderStartDate_<?=$ListNum?>" id="ClassOrderStartDate_<?=$ListNum?>" value="<?=$InputClassOrderStartDate?>"><br>
						레벨 : <input type="text" name="ClassOrderLeveltestApplyLevel_<?=$ListNum?>" id="ClassOrderLeveltestApplyLevel_<?=$ListNum?>" value="<?=$InputClassOrderLeveltestApplyLevel?>"><br>
						선택 : <input type="text" name="SelectMasterSlotCode_<?=$ListNum?>" id="SelectMasterSlotCode_<?=$ListNum?>" value="|"><br>
						</span>
                                                        <?=$StrMemberLoginID?>
                                                    </td>
                                                    <td style="border: 1px solid black;text-align:center;"><?=$StrClassType?></td>
                                                    <td style="border: 1px solid black;text-align:center;"><?=$StrClassOrderLeveltestApplyLevel?></td>
                                                    <td style="border: 1px solid black;text-align:center;"><?=$StrClassOrderTimeTypeID?></td>
                                                    <td style="border: 1px solid black;text-align:center;"><?=$StrClassStartDate?></td>
                                                    <td style="border: 1px solid black;text-align:center;width:120px;<?if ($StrStartTimeLevel!=""){?>background-color:#E1E6F0;<?}?>">
                                                        <?=$StrStartTimeLevel?>
                                                        <?

                                                        if ($StrStartTimeLevel!="" && $TempClassStartDate!=""){//체험 레벨
                                                            $WeekNum = date("w", strtotime($TempClassStartDate));
                                                            $ClassOrderTimeTypeID = 2;

                                                            $ArrStrStartTimeLevel = explode(":", $StrStartTimeLevel);
                                                            $StudyTimeHour = (int)$ArrStrStartTimeLevel[0];
                                                            $StudyTimeMinute = (int)$ArrStrStartTimeLevel[1];
                                                            $SelectOptions = CheckStudyTime($EduCenterID, $TempClassStartDate, $StudyTimeHour, $StudyTimeMinute, $ClassOrderTimeTypeID, $ClassProductID, $LinkAdminLevelID);

                                                            if ($SelectOptions==""){

                                                                $RegOk = 0;
                                                                $LineRegOk = 0;
                                                                ?>
                                                                <div style="color:#ff0000;margin-top:20px;">강사없음</div>
                                                                <?
                                                            }else{
                                                                ?>
                                                                <div style="margin-top:20px;">
                                                                    <select name="LevelTeacherID_<?=$ListNum?>" id="LevelTeacherID_<?=$ListNum?>" style="width:100px;height:30px;" onchange="ChTeacher('LevelTeacherID_<?=$ListNum?>' , 'OldLevelTeacherID_<?=$ListNum?>', 'OldLevelSlotTeacherID_<?=$ListNum?>', 'OldLevelSlotAllTime_<?=$ListNum?>', <?=$ListNum?>, '<?=$InputClassOrderStartDate?>', <?=$WeekNum?>, <?=$StudyTimeHour?>, <?=$StudyTimeMinute?>, <?=$InputClassOrderTimeTypeID?>, this.value)">
                                                                        <option value=""><?=$강사선택[$LangID]?></option>
                                                                        <?=$SelectOptions?>
                                                                    </select>
                                                                    <span style="display:none;">
									<input type="text" name="OldLevelTeacherID_<?=$ListNum?>" id="OldLevelTeacherID_<?=$ListNum?>" value="">
									<input type="text" name="OldLevelSlotTeacherID_<?=$ListNum?>" id="OldLevelSlotTeacherID_<?=$ListNum?>" value="">
									<input type="text" name="OldLevelSlotAllTime_<?=$ListNum?>" id="OldLevelSlotAllTime_<?=$ListNum?>" value="|" style="background-color:#cccccc;">
								</span>
                                                                </div>
                                                                <?
                                                                $SelectForms = $SelectForms . "LevelTeacherID_".$ListNum.",";
                                                            }
                                                        }
                                                        ?>
                                                    </td>

                                                    <td style="border: 1px solid black;text-align:center;width:120px;<?if ($StrStartTimeWeek1!=""){?>background-color:#E1E6F0;<?}?>">
                                                        <?=$StrStartTimeWeek1?>
                                                        <?
                                                        if ($StrStartTimeWeek1!="" && $TempClassStartDate!=""){//정규-월
                                                            $WeekNum = 1;
                                                            $ClassOrderTimeTypeID = $StrClassOrderTimeTypeID/10;

                                                            $ArrStrStartTimeWeek1 = explode(":", $StrStartTimeWeek1);
                                                            $StudyTimeHour = (int)$ArrStrStartTimeWeek1[0];
                                                            $StudyTimeMinute = (int)$ArrStrStartTimeWeek1[1];

                                                            $date=date_create($TempClassStartDateWeekStartDate);
                                                            date_add($date, date_interval_create_from_date_string("1 days"));
                                                            $TempClassStartDate = date_format($date, "Y-m-d");

                                                            //echo "<br>(".$TempClassStartDate.")<br>";
                                                            $SelectOptions = CheckStudyTime($EduCenterID, $TempClassStartDate, $StudyTimeHour, $StudyTimeMinute, $ClassOrderTimeTypeID, $ClassProductID, $LinkAdminLevelID);
                                                            if ($SelectOptions==""){

                                                                $RegOk = 0;
                                                                $LineRegOk = 0;
                                                                ?>
                                                                <div style="color:#ff0000;margin-top:20px;"><?=$강사없음[$LangID]?></div>
                                                                <?
                                                            }else{
                                                                ?>
                                                                <div style="margin-top:20px;">
                                                                    <select name="Week1TeacherID_<?=$ListNum?>" id="Week1TeacherID_<?=$ListNum?>" style="width:100px;height:30px;" onchange="ChTeacher('Week1TeacherID_<?=$ListNum?>', 'OldWeek1TeacherID_<?=$ListNum?>', 'OldWeek1SlotTeacherID_<?=$ListNum?>', 'OldWeek1SlotAllTime_<?=$ListNum?>', <?=$ListNum?>, <?=$WeekNum?>, <?=$StudyTimeHour?>, <?=$StudyTimeMinute?>, <?=$InputClassOrderTimeTypeID?>, this.value)">
                                                                        <option value=""><?=$강사선택[$LangID]?></option>
                                                                        <?=$SelectOptions?>
                                                                    </select>
                                                                    <span style="display:none;">
									<input type="text" name="OldWeek1TeacherID_<?=$ListNum?>" id="OldWeek1TeacherID_<?=$ListNum?>" value="">
									<input type="text" name="OldWeek1SlotTeacherID_<?=$ListNum?>" id="OldWeek1SlotTeacherID_<?=$ListNum?>" value="">
									<input type="text" name="OldWeek1SlotAllTime_<?=$ListNum?>" id="OldWeek1SlotAllTime_<?=$ListNum?>" value="|" style="background-color:#cccccc;">
								</span>
                                                                </div>
                                                                <?
                                                                $SelectForms = $SelectForms . "Week1TeacherID_".$ListNum.",";
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <td style="border: 1px solid black;text-align:center;width:120px;<?if ($StrStartTimeWeek2!=""){?>background-color:#E1E6F0;<?}?>">
                                                        <?=$StrStartTimeWeek2?>
                                                        <?
                                                        if ($StrStartTimeWeek2!="" && $TempClassStartDate!=""){//정규-화
                                                            $WeekNum = 2;
                                                            $ClassOrderTimeTypeID = $StrClassOrderTimeTypeID/10;

                                                            $ArrStrStartTimeWeek2 = explode(":", $StrStartTimeWeek2);
                                                            $StudyTimeHour = (int)$ArrStrStartTimeWeek2[0];
                                                            $StudyTimeMinute = (int)$ArrStrStartTimeWeek2[1];

                                                            $date=date_create($TempClassStartDateWeekStartDate);
                                                            date_add($date, date_interval_create_from_date_string("2 days"));
                                                            $TempClassStartDate = date_format($date, "Y-m-d");

                                                            //echo "<br>(".$TempClassStartDate.")<br>";
                                                            $SelectOptions = CheckStudyTime($EduCenterID, $TempClassStartDate, $StudyTimeHour, $StudyTimeMinute, $ClassOrderTimeTypeID, $ClassProductID, $LinkAdminLevelID);
                                                            if ($SelectOptions==""){

                                                                $RegOk = 0;
                                                                $LineRegOk = 0;
                                                                ?>
                                                                <div style="color:#ff0000;margin-top:20px;"><?=$강사없음[$LangID]?></div>
                                                                <?
                                                            }else{
                                                                ?>
                                                                <div style="margin-top:20px;">
                                                                    <select name="Week2TeacherID_<?=$ListNum?>" id="Week2TeacherID_<?=$ListNum?>" style="width:100px;height:30px;" onchange="ChTeacher('Week2TeacherID_<?=$ListNum?>', 'OldWeek2TeacherID_<?=$ListNum?>', 'OldWeek2SlotTeacherID_<?=$ListNum?>', 'OldWeek2SlotAllTime_<?=$ListNum?>', <?=$ListNum?>, <?=$WeekNum?>, <?=$StudyTimeHour?>, <?=$StudyTimeMinute?>, <?=$InputClassOrderTimeTypeID?>, this.value)">
                                                                        <option value="">강사선택</option>
                                                                        <?=$SelectOptions?>
                                                                    </select>
                                                                    <span style="display:none;">
									<input type="text" name="OldWeek2TeacherID_<?=$ListNum?>" id="OldWeek2TeacherID_<?=$ListNum?>" value="">
									<input type="text" name="OldWeek2SlotTeacherID_<?=$ListNum?>" id="OldWeek2SlotTeacherID_<?=$ListNum?>" value="">
									<input type="text" name="OldWeek2SlotAllTime_<?=$ListNum?>" id="OldWeek2SlotAllTime_<?=$ListNum?>" value="|" style="background-color:#cccccc;">
								</span>
                                                                </div>
                                                                <?
                                                                $SelectForms = $SelectForms . "Week2TeacherID_".$ListNum.",";
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <td style="border: 1px solid black;text-align:center;width:120px;<?if ($StrStartTimeWeek3!=""){?>background-color:#E1E6F0;<?}?>">
                                                        <?=$StrStartTimeWeek3?>
                                                        <?
                                                        if ($StrStartTimeWeek3!="" && $TempClassStartDate!=""){//정규-수
                                                            $WeekNum = 3;
                                                            $ClassOrderTimeTypeID = $StrClassOrderTimeTypeID/10;

                                                            $ArrStrStartTimeWeek3 = explode(":", $StrStartTimeWeek3);
                                                            $StudyTimeHour = (int)$ArrStrStartTimeWeek3[0];
                                                            $StudyTimeMinute = (int)$ArrStrStartTimeWeek3[1];

                                                            $date=date_create($TempClassStartDateWeekStartDate);
                                                            date_add($date, date_interval_create_from_date_string("3 days"));
                                                            $TempClassStartDate = date_format($date, "Y-m-d");

                                                            //echo "<br>(".$TempClassStartDate.")<br>";
                                                            $SelectOptions = CheckStudyTime($EduCenterID, $TempClassStartDate, $StudyTimeHour, $StudyTimeMinute, $ClassOrderTimeTypeID, $ClassProductID, $LinkAdminLevelID);
                                                            if ($SelectOptions==""){

                                                                $RegOk = 0;
                                                                $LineRegOk = 0;
                                                                ?>
                                                                <div style="color:#ff0000;margin-top:20px;"><?=$강사없음[$LangID]?></div>
                                                                <?
                                                            }else{
                                                                ?>
                                                                <div style="margin-top:20px;">
                                                                    <select name="Week3TeacherID_<?=$ListNum?>" id="Week3TeacherID_<?=$ListNum?>" style="width:100px;height:30px;" onchange="ChTeacher('Week3TeacherID_<?=$ListNum?>', 'OldWeek3TeacherID_<?=$ListNum?>', 'OldWeek3SlotTeacherID_<?=$ListNum?>', 'OldWeek3SlotAllTime_<?=$ListNum?>', <?=$ListNum?>, <?=$WeekNum?>, <?=$StudyTimeHour?>, <?=$StudyTimeMinute?>, <?=$InputClassOrderTimeTypeID?>, this.value)">
                                                                        <option value=""><?=$강사선택[$LangID]?></option>
                                                                        <?=$SelectOptions?>
                                                                    </select>
                                                                    <span style="display:none;">
									<input type="text" name="OldWeek3TeacherID_<?=$ListNum?>" id="OldWeek3TeacherID_<?=$ListNum?>" value="">
									<input type="text" name="OldWeek3SlotTeacherID_<?=$ListNum?>" id="OldWeek3SlotTeacherID_<?=$ListNum?>" value="">
									<input type="text" name="OldWeek3SlotAllTime_<?=$ListNum?>" id="OldWeek3SlotAllTime_<?=$ListNum?>" value="|" style="background-color:#cccccc;">
								</span>
                                                                </div>
                                                                <?
                                                                $SelectForms = $SelectForms . "Week3TeacherID_".$ListNum.",";
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <td style="border: 1px solid black;text-align:center;width:120px;<?if ($StrStartTimeWeek4!=""){?>background-color:#E1E6F0;<?}?>">
                                                        <?=$StrStartTimeWeek4?>
                                                        <?
                                                        if ($StrStartTimeWeek4!="" && $TempClassStartDate!=""){//정규-목
                                                            $WeekNum = 4;
                                                            $ClassOrderTimeTypeID = $StrClassOrderTimeTypeID/10;

                                                            $ArrStrStartTimeWeek4 = explode(":", $StrStartTimeWeek4);
                                                            $StudyTimeHour = (int)$ArrStrStartTimeWeek4[0];
                                                            $StudyTimeMinute = (int)$ArrStrStartTimeWeek4[1];

                                                            $date=date_create($TempClassStartDateWeekStartDate);
                                                            date_add($date, date_interval_create_from_date_string("4 days"));
                                                            $TempClassStartDate = date_format($date, "Y-m-d");

                                                            //echo "<br>(".$TempClassStartDate.")<br>";
                                                            $SelectOptions = CheckStudyTime($EduCenterID, $TempClassStartDate, $StudyTimeHour, $StudyTimeMinute, $ClassOrderTimeTypeID, $ClassProductID, $LinkAdminLevelID);
                                                            if ($SelectOptions==""){

                                                                $RegOk = 0;
                                                                $LineRegOk = 0;
                                                                ?>
                                                                <div style="color:#ff0000;margin-top:20px;"><?=$강사없음[$LangID]?></div>
                                                                <?
                                                            }else{
                                                                ?>
                                                                <div style="margin-top:20px;">
                                                                    <select name="Week4TeacherID_<?=$ListNum?>" id="Week4TeacherID_<?=$ListNum?>" style="width:100px;height:30px;" onchange="ChTeacher('Week4TeacherID_<?=$ListNum?>', 'OldWeek4TeacherID_<?=$ListNum?>', 'OldWeek4SlotTeacherID_<?=$ListNum?>', 'OldWeek4SlotAllTime_<?=$ListNum?>', <?=$ListNum?>, <?=$WeekNum?>, <?=$StudyTimeHour?>, <?=$StudyTimeMinute?>, <?=$InputClassOrderTimeTypeID?>, this.value)">
                                                                        <option value=""><?=$강사선택[$LangID]?></option>
                                                                        <?=$SelectOptions?>
                                                                    </select>
                                                                    <span style="display:none;">
									<input type="text" name="OldWeek4TeacherID_<?=$ListNum?>" id="OldWeek4TeacherID_<?=$ListNum?>" value="">
									<input type="text" name="OldWeek4SlotTeacherID_<?=$ListNum?>" id="OldWeek4SlotTeacherID_<?=$ListNum?>" value="">
									<input type="text" name="OldWeek4SlotAllTime_<?=$ListNum?>" id="OldWeek4SlotAllTime_<?=$ListNum?>" value="|" style="background-color:#cccccc;">
								</span>
                                                                </div>
                                                                <?
                                                                $SelectForms = $SelectForms . "Week4TeacherID_".$ListNum.",";
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <td style="border: 1px solid black;text-align:center;width:120px;<?if ($StrStartTimeWeek5!=""){?>background-color:#E1E6F0;<?}?>">
                                                        <?=$StrStartTimeWeek5?>
                                                        <?
                                                        if ($StrStartTimeWeek5!="" && $TempClassStartDate!=""){//정규-금
                                                            $WeekNum = 5;
                                                            $ClassOrderTimeTypeID = $StrClassOrderTimeTypeID/10;

                                                            $ArrStrStartTimeWeek5 = explode(":", $StrStartTimeWeek5);
                                                            $StudyTimeHour = (int)$ArrStrStartTimeWeek5[0];
                                                            $StudyTimeMinute = (int)$ArrStrStartTimeWeek5[1];

                                                            $date=date_create($TempClassStartDateWeekStartDate);
                                                            date_add($date, date_interval_create_from_date_string("5 days"));
                                                            $TempClassStartDate = date_format($date, "Y-m-d");

                                                            //echo "<br>(".$TempClassStartDate.")<br>";
                                                            $SelectOptions = CheckStudyTime($EduCenterID, $TempClassStartDate, $StudyTimeHour, $StudyTimeMinute, $ClassOrderTimeTypeID, $ClassProductID, $LinkAdminLevelID);
                                                            if ($SelectOptions==""){

                                                                $RegOk = 0;
                                                                $LineRegOk = 0;
                                                                ?>
                                                                <div style="color:#ff0000;margin-top:20px;"><?=$강사없음[$LangID]?></div>
                                                                <?
                                                            }else{
                                                                ?>
                                                                <div style="margin-top:20px;">
                                                                    <select name="Week5TeacherID_<?=$ListNum?>" id="Week5TeacherID_<?=$ListNum?>" style="width:100px;height:30px;" onchange="ChTeacher('Week5TeacherID_<?=$ListNum?>', 'OldWeek5TeacherID_<?=$ListNum?>', 'OldWeek5SlotTeacherID_<?=$ListNum?>', 'OldWeek5SlotAllTime_<?=$ListNum?>', <?=$ListNum?>, <?=$WeekNum?>, <?=$StudyTimeHour?>, <?=$StudyTimeMinute?>, <?=$InputClassOrderTimeTypeID?>, this.value)">
                                                                        <option value=""><?=$강사선택[$LangID]?></option>
                                                                        <?=$SelectOptions?>
                                                                    </select>
                                                                    <span style="display:none;">
									<input type="text" name="OldWeek5TeacherID_<?=$ListNum?>" id="OldWeek5TeacherID_<?=$ListNum?>" value="">
									<input type="text" name="OldWeek5SlotTeacherID_<?=$ListNum?>" id="OldWeek5SlotTeacherID_<?=$ListNum?>" value="">
									<input type="text" name="OldWeek5SlotAllTime_<?=$ListNum?>" id="OldWeek5SlotAllTime_<?=$ListNum?>" value="|" style="background-color:#cccccc;">
								</span>
                                                                </div>
                                                                <?
                                                                $SelectForms = $SelectForms . "Week5TeacherID_".$ListNum.",";
                                                            }
                                                        }
                                                        ?>

                                                    </td>
                                                    <td style="border: 1px solid black;text-align:center;width:120px;">
                                                        <?if ($LineRegOk==0){?>
                                                            <div style="color:#ff0000;margin-top:20px;"><?=$등록불가[$LangID]?></div>
                                                            <span style="display:none;">
						<input type="text" name="LineRegOk_<?=$ListNum?>" id="LineRegOk_<?=$ListNum?>" value="0"><br>
						</span>
                                                        <?}else{?>
                                                            <input type="checkbox" name="LineRegOk_<?=$ListNum?>" id="LineRegOk_<?=$ListNum?>" value="0"><?=$등록안함[$LangID]?>
                                                        <?}?>

                                                    </td>
                                                </tr>

                                                <?
                                                $ListNum++;
                                            }
                                        }

                                    } catch (exception $e) {
                                        echo '엑셀파일을 읽는도중 오류가 발생하였습니다.';
                                    }

                                    ?>
                                    </tbody>
                                </table>
                                <span style="display:none;">
	<input type="text" name="ListNum" id="ListNum" value="<?=$ListNum-1?>" style="width:100%">
	<input type="text" name="SelectForms" id="SelectForms" value="<?=$SelectForms?>" style="width:100%">
	<input type="text" name="ClassOrderSlotAllTime" id="ClassOrderSlotAllTime" value="|" style="width:100%">
</span>
                                <!-- ===========================================  table ================================================= -->

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?if ($RegOk==0){?>
                <div style="width:100%;color:#880000;margin-bottom:20px;margin-top:20px;text-align:center;">선택하신 시간중에 수업 가능한 강사가 없는 시간이 있습니다. 신청서를 재작성 또는 강사 선택이 가능한 학생만 등록 할 수 있습니다.</div>

                <script>alert("선택하신 시간중에 수업 가능한 강사가 없는 시간이 있습니다. 신청서를 재작성 또는 강사 선택이 가능한 학생만 등록 할 수 있습니다.");</script>
            <?}?>

            <div style="margin-top: 20px; text-align:center;">
                <a style="margin:0 auto;display:inline-block; background-color:#888888; color:#ffffff; text-align:center; width:110px; line-height:32px; font-size:14px;" href="javascript:GoPrev();"><?=$이전으로[$LangID]?></a>
                <?if ($RegOk==0){?>
                    <a style="margin:0 auto;display:inline-block; background-color:#556BAC; color:#ffffff; text-align:center; width:240px; line-height:32px; font-size:14px;" href="javascript:FormSubmit();"><?=$강사_선택이_가능한_학생만_등록[$LangID]?></a>
                <?}else{?>
                    <a style="margin:0 auto;display:inline-block; background-color:#556BAC; color:#ffffff; text-align:center; width:110px;  line-height:32px; font-size:14px;" href="javascript:FormSubmit();"><?=$등록하기[$LangID]?></a>
                <?}?>
            </div>
    </div>
    </form>
</div>
</div>

<script>
    function ChTeacher(SelectFormID, OldSelectFormID, OldSlotTeacherIDFormID, OldSlotAllTimeFormID, ListNum, WeekNum, StudyTimeHour, StudyTimeMinute, ClassOrderTimeTypeID, TeacherID){

        ExistSlot = 0;
        ClassOrderSlotAllTime_ = document.getElementById("ClassOrderSlotAllTime").value;
        OldSlotTeacherIDFormID_ = document.getElementById(OldSlotTeacherIDFormID).value;
        for (jj=0;jj<=ClassOrderTimeTypeID-1;jj++){
            TempStudyTimeHour = StudyTimeHour;
            TempStudyTimeMinute = StudyTimeMinute+(jj*10);

            if (TempStudyTimeMinute>=60){
                TempStudyTimeHour = TempStudyTimeHour + 1;
                TempStudyTimeMinute = TempStudyTimeMinute - 60;
            }

            CheckSlot_ = "|" + TeacherID +"_" + WeekNum +"_" + TempStudyTimeHour +"_" + TempStudyTimeMinute + "|";

            if (ClassOrderSlotAllTime_.indexOf(CheckSlot_) != -1) {
                ExistSlot = 1;
            }
        }

        if (ExistSlot==1){
            alert("<?=$먼저_선택한_수업_중에_동일한_강사와_시간이_중복되는_신청이_있습니다[$LangID]?>");
            document.getElementById(SelectFormID).value = OldSlotTeacherIDFormID_;
        }else{

            //============================ 전체 관련 ======================================
            OldSlotAllTimeFormID_ = document.getElementById(OldSlotAllTimeFormID).value;//현재 선택한 시간 전체 슬랏
            ClassOrderSlotAllTime_ = document.getElementById("ClassOrderSlotAllTime").value;//모든 선택한 시간 전체 슬랏
            ClassOrderSlotAllTime_ = ClassOrderSlotAllTime_.replace(OldSlotAllTimeFormID_, "|");
            document.getElementById("ClassOrderSlotAllTime").value = ClassOrderSlotAllTime_;


            //현재 선택한 박스의 전체 슬랏
            if (TeacherID!=""){

                OldSlotAllTimeFormID_ = "";
                for (jj=0;jj<=ClassOrderTimeTypeID-1;jj++){
                    TempStudyTimeHour = StudyTimeHour;
                    TempStudyTimeMinute = StudyTimeMinute+(jj*10);

                    if (TempStudyTimeMinute>=60){
                        TempStudyTimeHour = TempStudyTimeHour + 1;
                        TempStudyTimeMinute = TempStudyTimeMinute - 60;
                    }

                    OldSlotAllTimeFormID_ = OldSlotAllTimeFormID_ + TeacherID +"_" + WeekNum +"_" + TempStudyTimeHour +"_" + TempStudyTimeMinute + "|";
                }

                ClassOrderSlotAllTime_ = document.getElementById("ClassOrderSlotAllTime").value;
                document.getElementById(OldSlotAllTimeFormID).value = "|"+OldSlotAllTimeFormID_;
                document.getElementById("ClassOrderSlotAllTime").value = ClassOrderSlotAllTime_ + OldSlotAllTimeFormID_;
            }else{
                document.getElementById(OldSlotAllTimeFormID).value = "|";
            }
            //현재 선택한 박스의 전체 슬랏

            //============================ 전체 관련 ======================================


            //============================ 라인 관련 ======================================
            SelectMasterSlotCode_ = document.getElementById("SelectMasterSlotCode_"+ListNum).value;
            OldSelectMasterSlotCode_ = document.getElementById(OldSelectFormID).value;
            if (OldSelectMasterSlotCode_!=""){
                SelectMasterSlotCode_ = SelectMasterSlotCode_.replace(OldSelectMasterSlotCode_, "|");
            }

            if (TeacherID!=""){
                document.getElementById(OldSelectFormID).value = "|" + TeacherID +"_" + WeekNum +"_" + StudyTimeHour +"_" + StudyTimeMinute + "|";
                SelectMasterSlotCode_ = SelectMasterSlotCode_ + TeacherID +"_" + WeekNum +"_" + StudyTimeHour +"_" + StudyTimeMinute + "|";
            }else{
                document.getElementById(OldSelectFormID).value = "";
            }
            document.getElementById("SelectMasterSlotCode_"+ListNum).value = SelectMasterSlotCode_;
            document.getElementById(OldSlotTeacherIDFormID).value = TeacherID;
            //============================ 라인 관련 ======================================

        }
    }


    function FormSubmit() {

        SelectForms = document.getElementById("SelectForms").value;
        ArrSelectForm = SelectForms.split(',');
        AllSelect = 1;
        for (ii=1;ii<=ArrSelectForm.length-2;ii++){
            SelectForm = ArrSelectForm[ii];
            if (document.getElementById(SelectForm).value==""){

                ArrLineNum = SelectForm.split("_");
                LineNum = ArrLineNum[1];
                if (document.getElementById("LineRegOk_"+LineNum).checked==false){
                    //alert("LineRegOk_"+LineNum);
                    AllSelect = 0;
                }

            }
        }

        if (AllSelect==0){
            alert("<?=$모든_시간의_강사를_선택해_주세요[$LangID]?>");
            return;
        }



        if (confirm("<?=$등록_하시겠습니까[$LangID]?>?")){
            document.RegForm.action = "class_order_bulk_action.php";
            document.RegForm.submit();
        }

    }

    function FormErr(){
        alert("등록할 수 없습니다. 안내를 참고하세요.");
    }

    function CloseThisWinodw() {
        parent.$.fn.colorbox.close();
    }

    // function GoPrev(){
    // 	location.href = "class_order_bulk_form.php";
    // }

    // '이전으로' 버튼 클릭 시 -> 통합 배정 페이지로 돌아가기
    function GoPrev(){
        location.href = "class_order_bulk_form_merge.php";
    }

    parent.$.colorbox.resize({width:"95%", height:"95%", maxWidth: "750", maxHeight: "650"});
</script>


<?
function validateDate($date, $format = 'Y-m-d H:i:s'){
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}
?>


</body>
</html>


<?

//버퍼 끄고 출력 ===============================
ob_end_flush();
//버퍼 끄고 출력 ===============================


include_once('../includes/dbclose.php');
?>

