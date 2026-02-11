<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');


$ListNum = isset($_REQUEST["ListNum"]) ? $_REQUEST["ListNum"] : "";

for ($ii=1;$ii<=$ListNum;$ii++){

    $MemberID_ = isset($_REQUEST["MemberID_".$ii]) ? $_REQUEST["MemberID_".$ii] : "";
    $ClassProductID_ = isset($_REQUEST["ClassProductID_".$ii]) ? $_REQUEST["ClassProductID_".$ii] : "";
    $ClassMemberType_ = isset($_REQUEST["ClassMemberType_".$ii]) ? $_REQUEST["ClassMemberType_".$ii] : "";
    $ClassOrderTimeTypeID_ = isset($_REQUEST["ClassOrderTimeTypeID_".$ii]) ? $_REQUEST["ClassOrderTimeTypeID_".$ii] : "";
    $ClassOrderWeekCountID_ = isset($_REQUEST["ClassOrderWeekCountID_".$ii]) ? $_REQUEST["ClassOrderWeekCountID_".$ii] : "";
    $ClassOrderStartDate_ = isset($_REQUEST["ClassOrderStartDate_".$ii]) ? $_REQUEST["ClassOrderStartDate_".$ii] : "";
    $ClassOrderLeveltestApplyLevel_ = isset($_REQUEST["ClassOrderLeveltestApplyLevel_".$ii]) ? $_REQUEST["ClassOrderLeveltestApplyLevel_".$ii] : "";
    $SelectMasterSlotCode_ = isset($_REQUEST["SelectMasterSlotCode_".$ii]) ? $_REQUEST["SelectMasterSlotCode_".$ii] : "";

    $LineRegOk_ = isset($_REQUEST["LineRegOk_".$ii]) ? $_REQUEST["LineRegOk_".$ii] : "";
    if ($LineRegOk_!="0"){
        $LineRegOk_ = "1";
    }

    //echo $ii . "-" . $LineRegOk_ . "<br>";

    if ($LineRegOk_=="1"){

        $ArrMemberID = explode(",", $MemberID_);

        for ($iii=1;$iii<=count($ArrMemberID)-2;$iii++){

            $MemberID = $ArrMemberID[$iii];
            $ClassProductID = $ClassProductID_;
            $SelectMasterSlotCode = $SelectMasterSlotCode_;
            $ClassOrderStartDate = $ClassOrderStartDate_;
            $ClassOrderSlotDate = $ClassOrderStartDate_;
            $ClassMemberType = $ClassMemberType_;
            $ClassOrderTimeTypeID = $ClassOrderTimeTypeID_;
            $ClassOrderWeekCountID = $ClassOrderWeekCountID_;
            $ClassOrderRequestText = "";
            $ClassOrderState = 1;
            $ClassProgress = 1;
            $ClassOrderEndDate = null; // 수업 종료일 변수 초기화

            // MemberLevelID를 기준으로 B2C 학생(Level 4 이상)의 정규 수업(ClassProductID=1)에 대해서만 종료일 계산
            $SqlMem = "SELECT MemberLevelID FROM Members WHERE MemberID = :MemberID";
            $StmtMem = $DbConn->prepare($SqlMem);
            $StmtMem->bindParam(':MemberID', $MemberID, PDO::PARAM_INT);
            $StmtMem->execute();
            $RowMem = $StmtMem->fetch(PDO::FETCH_ASSOC);
            $StmtMem = null;

            if ($ClassProductID == 1 && isset($RowMem['MemberLevelID']) && $RowMem['MemberLevelID'] >= 4) {
                // 4주 과정으로 계산
                $classEndDateObj = new DateTime($ClassOrderStartDate);
                $classEndDateObj->add(new DateInterval('P4W'));
                $ClassOrderEndDate = $classEndDateObj->format('Y-m-d');
            }


            //레벨테스트 기본값 ====
            $ClassOrderLeveltestApplyTypeID = 1;
            $ClassOrderLeveltestApplyLevel = $ClassOrderLeveltestApplyLevel_;
            $ClassOrderLeveltestApplyOverseaTypeID = 1;
            $ClassOrderLeveltestApplyText = "";
            //레벨테스트 기본값 ===

            $Sql = " insert into ClassOrders ( ";
            $Sql .= " ClassProductID, ";

            $Sql .= " ClassOrderLeveltestApplyTypeID, ";
            $Sql .= " ClassOrderLeveltestApplyLevel, ";
            $Sql .= " ClassOrderLeveltestApplyOverseaTypeID, ";
            $Sql .= " ClassOrderLeveltestApplyText, ";

            $Sql .= " ClassOrderTimeTypeID, ";
            $Sql .= " ClassOrderWeekCountID, ";
            $Sql .= " ClassOrderStartDate, ";
            $Sql .= " MemberID, ";
            $Sql .= " ClassOrderRequestText, ";
            $Sql .= " ClassOrderState, ";
            $Sql .= " ClassMemberType, ";
            $Sql .= " ClassProgress, ";
            $Sql .= " ClassOrderEndDate, ";
            $Sql .= " ClassOrderRegDateTime, ";
            $Sql .= " ClassOrderModiDateTime ";
            $Sql .= " ) values ( ";
            $Sql .= " :ClassProductID, ";

            $Sql .= " :ClassOrderLeveltestApplyTypeID, ";
            $Sql .= " :ClassOrderLeveltestApplyLevel, ";
            $Sql .= " :ClassOrderLeveltestApplyOverseaTypeID, ";
            $Sql .= " :ClassOrderLeveltestApplyText, ";

            $Sql .= " :ClassOrderTimeTypeID, ";
            $Sql .= " :ClassOrderWeekCountID, ";
            $Sql .= " :ClassOrderStartDate, ";
            $Sql .= " :MemberID, ";
            $Sql .= " :ClassOrderRequestText, ";
            $Sql .= " :ClassOrderState, ";
            $Sql .= " :ClassMemberType, ";
            $Sql .= " :ClassProgress, ";
            $Sql .= " :ClassOrderEndDate, ";
            $Sql .= " now(), ";
            $Sql .= " now() ";
            $Sql .= " ) ";


            $Stmt = $DbConn->prepare($Sql);
            $Stmt->bindParam(':ClassProductID', $ClassProductID);

            $Stmt->bindParam(':ClassOrderLeveltestApplyTypeID', $ClassOrderLeveltestApplyTypeID);
            $Stmt->bindParam(':ClassOrderLeveltestApplyLevel', $ClassOrderLeveltestApplyLevel);
            $Stmt->bindParam(':ClassOrderLeveltestApplyOverseaTypeID', $ClassOrderLeveltestApplyOverseaTypeID);
            $Stmt->bindParam(':ClassOrderLeveltestApplyText', $ClassOrderLeveltestApplyText);

            $Stmt->bindParam(':ClassOrderTimeTypeID', $ClassOrderTimeTypeID);
            $Stmt->bindParam(':ClassOrderWeekCountID', $ClassOrderWeekCountID);
            $Stmt->bindParam(':ClassOrderStartDate', $ClassOrderStartDate);
            $Stmt->bindParam(':MemberID', $MemberID);
            $Stmt->bindParam(':ClassOrderRequestText', $ClassOrderRequestText);
            $Stmt->bindParam(':ClassOrderState', $ClassOrderState);
            $Stmt->bindParam(':ClassMemberType', $ClassMemberType);
            $Stmt->bindParam(':ClassProgress', $ClassProgress);
            $Stmt->bindParam(':ClassOrderEndDate', $ClassOrderEndDate);

            $Stmt->execute();
            $ClassOrderID = $DbConn->lastInsertId();
            $Stmt = null;


            if ($ClassProductID==2 || $ClassProductID==3){
                $ClassOrderSlotType = 2;//임시
            }else{
                $ClassOrderSlotType = 1;//정규
            }

            $ArrSelectMasterSlotCode = explode("|",$SelectMasterSlotCode);//주 1회 이상일 경우 루프가 필요하다.


            for ($iiii=1;$iiii<=count($ArrSelectMasterSlotCode)-2;$iiii++){//교사 수업 시간 기록

                //이슬랏은 첫번째 마스터 슬랏이다.
                $ArrArrSelectMasterSlotCode  = explode("_",$ArrSelectMasterSlotCode[$iiii]);

                $TeacherID = $ArrArrSelectMasterSlotCode[0];
                $StudyTimeWeekOrDate = $ArrArrSelectMasterSlotCode[1];
                $StudyTimeHour = $ArrArrSelectMasterSlotCode[2];
                $StudyTimeMinute = $ArrArrSelectMasterSlotCode[3];

                // JS에서 날짜 기반으로 유니크 슬롯을 생성하므로, 받은 날짜에서 요일(w)을 추출합니다.
                $StudyTimeWeek = date('w', strtotime($StudyTimeWeekOrDate));

                // 레벨/체험 수업의 경우, 슬롯 날짜를 명시적으로 사용합니다.
                if ($ClassProductID == 2 || $ClassProductID == 3) {
                    $ClassOrderSlotDate = $StudyTimeWeekOrDate;
                }


                //시수 만큼 슬랏을 넣어준다. 20분은 2회, 40분은 4회
                for ($iiiii=0;$iiiii<=$ClassOrderTimeTypeID-1;$iiiii++){

                    if ($iiiii==0){//마스터
                        $ClassOrderSlotMaster = 1;

                        $Sql = "select ifnull(Max(ClassOrderSlotGroupID),0) as ClassOrderSlotGroupID from ClassOrderSlots";
                        $Stmt = $DbConn->prepare($Sql);
                        $Stmt->execute();
                        $Stmt->setFetchMode(PDO::FETCH_ASSOC);
                        $Row = $Stmt->fetch();
                        $Stmt = null;
                        $ClassOrderSlotGroupID = $Row["ClassOrderSlotGroupID"]+1;

                    }else{//슬레이브
                        $ClassOrderSlotMaster = 0;
                    }

                    $InStudyTimeHour = $StudyTimeHour;
                    $InStudyTimeMinute = $StudyTimeMinute + ($iiiii*10);
                    if ($InStudyTimeMinute>=60){
                        $InStudyTimeHour = $InStudyTimeHour + 1;
                        $InStudyTimeMinute = $InStudyTimeMinute -  60;
                    }

                    $Sql = " insert into ClassOrderSlots ( ";
                    $Sql .= " ClassOrderSlotGroupID, ";
                    $Sql .= " ClassMemberType, ";
                    $Sql .= " ClassOrderSlotType, ";
                    if ($ClassProductID==2 || $ClassProductID==3){
                        $Sql .= " ClassOrderSlotDate, ";
                    }
                    $Sql .= " TeacherID, ";
                    $Sql .= " ClassOrderID, ";
                    $Sql .= " ClassOrderSlotMaster, ";
                    $Sql .= " StudyTimeWeek, ";
                    $Sql .= " StudyTimeHour, ";
                    $Sql .= " StudyTimeMinute, ";
                    $Sql .= " ClassOrderSlotState, ";
                    $Sql .= " ClassOrderSlotRegDateTime ";
                    $Sql .= " ) values ( ";
                    $Sql .= " :ClassOrderSlotGroupID, ";
                    $Sql .= " :ClassMemberType, ";
                    $Sql .= " :ClassOrderSlotType, ";
                    if ($ClassProductID==2 || $ClassProductID==3){
                        $Sql .= " :ClassOrderSlotDate, ";
                    }
                    $Sql .= " :TeacherID, ";
                    $Sql .= " :ClassOrderID, ";
                    $Sql .= " :ClassOrderSlotMaster, ";
                    $Sql .= " :StudyTimeWeek, ";
                    $Sql .= " :StudyTimeHour, ";
                    $Sql .= " :StudyTimeMinute, ";
                    $Sql .= " 1, ";
                    $Sql .= " now() ";
                    $Sql .= " ) ";

                    $Stmt = $DbConn->prepare($Sql);
                    $Stmt->bindParam(':ClassOrderSlotGroupID', $ClassOrderSlotGroupID);
                    $Stmt->bindParam(':ClassMemberType', $ClassMemberType);
                    $Stmt->bindParam(':ClassOrderSlotType', $ClassOrderSlotType);
                    if ($ClassProductID==2 || $ClassProductID==3){
                        $Stmt->bindParam(':ClassOrderSlotDate', $ClassOrderSlotDate);
                    }
                    $Stmt->bindParam(':TeacherID', $TeacherID);
                    $Stmt->bindParam(':ClassOrderID', $ClassOrderID);
                    $Stmt->bindParam(':ClassOrderSlotMaster', $ClassOrderSlotMaster);
                    $Stmt->bindParam(':StudyTimeWeek', $StudyTimeWeek);
                    $Stmt->bindParam(':StudyTimeHour', $InStudyTimeHour);
                    $Stmt->bindParam(':StudyTimeMinute', $InStudyTimeMinute);
                    $Stmt->execute();
                    $Stmt = null;
                }
                //시수 만큼 슬랏을 넣어준다.

            }



            $Sql = "update ClassOrders set 
								ClassProgress=11, 
								ClassOrderModiDateTime=now() 
							where ClassOrderID=:ClassOrderID ";
            $Stmt = $DbConn->prepare($Sql);
            $Stmt->bindParam(':ClassOrderID', $ClassOrderID);
            $Stmt->execute();
            $Stmt = null;

        }

    }
}

include_once('../includes/dbclose.php');
?>
<script>
    parent.location.href = "class_order_list.php?type=21";
</script>