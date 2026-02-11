<!doctype html>
<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
?>

<!--[if lte IE 9]> <html class="lte-ie9" lang="ko"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="ko"> <!--<![endif]-->
<head>
    <?
    include_once('./includes/common_meta_tag.php');
    include_once('./inc_header.php');
    include_once('./inc_common_form_css.php');
    ?>
    <!-- ============== common.css ============== -->
    <link rel="stylesheet" type="text/css" href="css/common.css" />
    <!-- ============== common.css ============== -->

    <!-- ===========================================   froala_editor   =========================================== -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../editors/froala_editor_3/css/froala_editor.css">
    <link rel="stylesheet" href="../editors/froala_editor_3/css/froala_style.css">
    <link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/code_view.css">
    <link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/draggable.css">
    <link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/colors.css">
    <link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/emoticons.css">
    <link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/image_manager.css">
    <link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/image.css">
    <link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/line_breaker.css">
    <link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/table.css">
    <link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/char_counter.css">
    <link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/video.css">
    <link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/fullscreen.css">
    <link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/file.css">
    <link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/quick_insert.css">
    <link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/help.css">
    <link rel="stylesheet" href="../editors/froala_editor_3/css/third_party/spell_checker.css">
    <link rel="stylesheet" href="../editors/froala_editor_3/css/plugins/special_characters.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.3.0/codemirror.min.css">
    <!-- ===========================================   froala_editor   =========================================== -->
</head>
<?
#-----------------------------------------------------------------------------------------------------------------------------------------#
$MemberLoginID     = isset($_COOKIE["LinkLoginMemberID" ]) ? $_COOKIE["LinkLoginMemberID" ] : "";
$SearchYear        = isset($_REQUEST["SearchYear"   ]) ? $_REQUEST["SearchYear"   ] : 0;  // 선택한 "휴가연도"
$HolidayType       = isset($_REQUEST["HolidayType" ]) ? $_REQUEST["HolidayType" ] : 0;
#-----------------------------------------------------------------------------------------------------------------------------------------#
# 회원 고유아이디(번호, 조직아이디) 찾기
#-----------------------------------------------------------------------------------------------------------------------------------------#
$Sql = "SELECT T.*,M.*, O.* 
          FROM Members as M 
     LEFT JOIN Hr_OrganLevelTaskMembers T on T.MemberID=M.MemberID
     LEFT JOIN Hr_OrganLevels          O on T.Hr_OrganLevelID = O.Hr_OrganLevelID
         WHERE M.MemberLoginID=:MemberLoginID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberLoginID', $MemberLoginID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$My_MemberID     = $Row["MemberID"];
$My_MemberName   = $Row["MemberName"];
$My_OrganLevel   = $Row["Hr_OrganLevel"];
$My_OrganName    = $Row["Hr_OrganLevelName"];
$My_OrganLevelID = $Row["Hr_OrganLevelID"];
$My_OrganTask2ID = $Row["Hr_OrganTask2ID"];
$StaffID         = $Row["StaffID"];
$Document_OrganName   = $My_OrganName;
$Document_MemberName  = $My_MemberName;
#-----------------------------------------------------------------------------------------------------------------------------------------#
?>

<body class="disable_transitions sidebar_main_open sidebar_main_swipe">
<?php
$MainMenuID = 29;
$SubMenuID = 2921;
include_once('./inc_top.php');
include_once('./inc_menu_left.php');

// 문서 파라미터
$ListParam         = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$DocumentReportID  = isset($_REQUEST["DocumentReportID"]) ? $_REQUEST["DocumentReportID"] : null;
$DocumentID        = isset($_REQUEST["DocumentID"]) ? $_REQUEST["DocumentID"] : "";
$CopyMode          = isset($_REQUEST["CopyMode"]) ? $_REQUEST["CopyMode"] : false;
$PageTabID         = isset($_REQUEST["PageTabID"]) ? $_REQUEST["PageTabID"] : "";
if ($PageTabID == ""){
    $PageTabID = "1";
}

// 문서가 이미 존재하는 경우(수정, 복사)
if ($DocumentReportID != null && $DocumentReportID != 0){
    $Sql = "SELECT A.*,
                   date_format(A.DocumentReportRegDateTime, '%Y-%m-%d')   as StrDocumentReportRegDateTime,
                   date_format(A.DocumentReportRegDateTime, '%Y년 %m월 %d일') as StrDocumentReportRegDateTime2,
                   B.MemberName
              FROM DocumentReports A
        INNER JOIN Members B on A.MemberID=B.MemberID
             WHERE A.DocumentReportID=:DocumentReportID";
    $Stmt = $DbConn->prepare($Sql);
    $Stmt->bindParam(':DocumentReportID', $DocumentReportID);
    $Stmt->execute();
    $Stmt->setFetchMode(PDO::FETCH_ASSOC);
    $Row = $Stmt->fetch();
    $Stmt = null;

    $DocumentReportMemberID   = $Row["MemberID"];
    $DocumentReportMemberName = $Row["MemberName"];
    $DocumentID               = $Row["DocumentID"];
    $DocumentReportID         = $Row["DocumentReportID"];
    $DocumentReportName       = $Row["DocumentReportName"];
    $DocumentReportContent    = $Row["DocumentReportContent"];

    $PayDate       = $Row["PayDate"];
    $AccCode       = $Row["AccCode"];
    $FileName      = $Row["FileName"];
    $FileRealName  = $Row["FileRealName"];
    $OrganName     = $Row["OrganName"];
    $OrganPhone    = $Row["OrganPhone"];
    $OrganManagerName = $Row["OrganManagerName"];
    $PayMethod     = $Row["PayMethod"];
    $RequestPayDate= $Row["RequestPayDate"];
    $PayMemo       = $Row["PayMemo"];
    $DocumentReportState = $Row["DocumentReportState"];

    $StrDocumentReportRegDateTime  = $Row["StrDocumentReportRegDateTime"];
    $StrDocumentReportRegDateTime2 = $Row["StrDocumentReportRegDateTime2"];

    // 결재 정보
    $Sql = "SELECT A.*,
                   date_format(A.DocumentReportMemberRegDateTime, '%Y-%m-%d') as StrDocumentReportMemberRegDateTime
              FROM DocumentReportMembers A
             WHERE A.DocumentReportID=:DocumentReportID
          ORDER BY A.DocumentReportMemberOrder desc 
             LIMIT 0,1";
    $Stmt = $DbConn->prepare($Sql);
    $Stmt->bindParam(':DocumentReportID', $DocumentReportID);
    $Stmt->execute();
    $Stmt->setFetchMode(PDO::FETCH_ASSOC);
    $Row = $Stmt->fetch();
    $Stmt = null;

    $DocumentReportMemberState          = $Row["DocumentReportMemberState"];
    $StrDocumentReportMemberRegDateTime = $Row["StrDocumentReportMemberRegDateTime"];

    if ($DocumentReportMemberState != 1){
        $StrDocumentReportMemberRegDateTime = "-";
    }

    // 복사 모드(원본을 그대로 카피하여 새로 작성)
    if($CopyMode) {
        $CopyDocumentReportID = $DocumentReportID;  // 원본 문서 ID
        $DocumentReportID = 0;

        $DocumentReportState    = 0;
        $FileName               = "";
        $FileRealName           = "";
        $StrDocumentReportRegDateTime  = date("Y-m-d");
        $RequestPayDate         = date("Y-m-d");
        $StrDocumentReportRegDateTime2 = date("Y년 m월 d일");

        $StrDocumentReportMemberRegDateTime = "-";
        $DocumentID = 2;
    }

} else {
    // 신규 작성
    $DocumentReportID         = 0;
    $DocumentReportMemberID   = $My_MemberID;
    $DocumentReportMemberName = $My_MemberName;
    $DocumentReportName       = "";
    $DocumentReportContent    = "";

    $PayDate       = date("Y-m-d");
    $AccCode       = "";
    $FileName      = "";
    $FileRealName  = "";
    $OrganName     = "";
    $OrganPhone    = "";
    $OrganManagerName = "";
    $PayMethod     = "";
    $RequestPayDate= date("Y-m-d");
    $PayMemo       = "";

    $DocumentReportState = 0;
    $StrDocumentReportRegDateTime  = date("Y-m-d");
    $StrDocumentReportRegDateTime2 = date("Y년 m월 d일");
    $StrDocumentReportMemberRegDateTime = "-";
    $DocumentID = 2;
}

// ▼▼▼ 휴가연도($SearchYear)가 0이면 현재 연도로 지정
if ($SearchYear == 0) {
    $SearchYear = date("Y");
}

/*
    * [중요] 휴가적용기간 계산 로직
    * 예: 2025년 휴가 -> 2025-03-01 ~ 2026-02-28
    *     2026년 휴가 -> 2026-03-01 ~ 2027-02-28
    *     ...
*/
$VacationStartDate = $SearchYear . "-03-01";
$VacationEndDate   = ($SearchYear + 1) . "-02-28";

// 실제 출력용 텍스트
$VacationPeriodText = $VacationStartDate . " ~ " . $VacationEndDate;
?>

<div id="page_content">
    <div id="page_content_inner">

        <form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
            <input type="hidden" name="DocumentReportID" value="<?=$DocumentReportID?>">
            <input type="hidden" name="ListParam" value="<?=$ListParam?>">
            <input type="hidden" id="DocumentID" name="DocumentID" value="<?=$DocumentID?>">
            <input type="hidden" id="PayDate" name="PayDate" value="<?=$PayDate?>">

            <div class="uk-grid" data-uk-grid-margin>
                <div class="uk-width-large-7-10">
                    <div class="md-card">
                        <div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
                            <div class="user_heading_content">
                                <h2 class="heading_b">
                                    <span class="uk-text-truncate" id="user_edit_uname">휴가 및 병가원</span>
                                    <span class="sub-heading" id="user_edit_position"></span>
                                </h2>
                            </div>
                        </div>
                        <div class="user_content">

                            <!-- 휴가적용기간을 화면 상단에 표시 -->
<!--                            <div style="margin-bottom:20px;">-->
<!--                                <strong>휴가적용기간 (--><?php //=$SearchYear?><!--년 휴가) :</strong>-->
<!--                                <span style="color:blue;">--><?php //=$VacationPeriodText?><!--</span>-->
<!--                            </div>-->

                            <!-- inc_holiday_form.php 에서도 동일하게 $VacationStartDate, $VacationEndDate 등을 이용하여 출력할 수 있음 -->
                            <?php
                            // 예: inc_holiday_form.php 내부에서 이 변수들을 활용
                            // $VacationStartDate, $VacationEndDate, $VacationPeriodText
                            // 아래는 예시로 실제 휴가 신청 폼을 include
                            include("./inc_holiday_form.php");
                            ?>
                        </div>
                    </div>
                </div>

                <div class="uk-width-large-3-10" >
                    <div class="md-card">
                        <div class="md-card-content">
                            <h3 class="heading_c uk-margin-medium-bottom">저장 설정</h3>

                            <div class="uk-form-row">
                                <input type="hidden" name="DocumentReportState" id="DocumentReportState" value="<?=$DocumentReportState?>">
                                <? if ($DocumentReportState!=1) {?>
                                    <a type="button" href="javascript:FormSubmit(2);" class="md-btn md-btn-worning">저장하기</a>
                                <?}?>
                                <? if ($DocumentReportState==1 && !$DocumentPermited) {?>
                                    <a type="button" href="javascript:FormSubmit(1);" class="md-btn md-btn-worning">수정하기</a>
                                <?}?>
                                <? if ($DocumentReportState!=1) {?>
                                    <a type="button" href="javascript:FormSubmit(1);" class="md-btn md-btn-primary">제출하기</a>
                                <?}?>
                                <? if (($DocumentReportState==1 || $DocumentReportState==2) && !$DocumentPermited){?>
                                    <a type="button" href="javascript:FormSubmit(0);" class="md-btn md-btn-danger">삭제하기</a>
                                <?}?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>

<?php
include_once('./inc_common_form_js.php');
?>
<!-- ==============  only this page js ============== -->

<!-- ==============  common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ==============  common.js ============== -->

<!-- 멀티 파일업로드 JQuery -->
<link href="/js/fileuploadjs/fileup.min.css" rel="stylesheet">
<script src="/js/fileuploadjs/fileup.min.js"></script>

<script>
    var fileIndex=0;
    var fileNumberArr = [];   // 서버에 업로드된 파일 순서
    var fileNameArr = [];     // 서버에 업로드된 실제 파일 이름
    var originNameArr = [];   // 원래 파일 이름
    var jsonData;

    // 이전에 저장된 파일들 세팅
    if (document.RegForm.FileName && document.RegForm.FileName.value != '') {
        fileNameArr   = document.RegForm.FileName.value.split(',');
        originNameArr = document.RegForm.FileRealName.value.split(',');
        for (var i=0; i<fileNameArr.length; i++) {
            fileNumberArr.push(i);
        }
        var objectList = [];
        for(var i=0; i<fileNameArr.length; i++){
            var data = {};
            data.id          = i;
            data.name        = originNameArr[i];
            data.size        = 1024;
            data.downloadUrl = "../uploads/document_files/" + fileNameArr[i];
            objectList.push(data);
        }
        jsonData = JSON.stringify(objectList);
    }

    $.fileup({
        url: 'jquery_file_upload.php',
        inputID: 'upload-2',
        dropzoneID: 'upload-2-dropzone',
        queueID: 'upload-2-queue',
        autostart: true,
        files: objectList,
        onSelect: function(file) {
            $('#multiple .control-button').show();
        },
        onRemove: function(file, total) {
            $.ajax({
                type: 'POST',
                url: 'jquery_file_delete.php',
                data: { filename : fileNameArr[fileNumberArr.indexOf(file.file_number)] }
            });
            fileNameArr.splice(fileNumberArr.indexOf(file.file_number),1);
            originNameArr.splice(fileNumberArr.indexOf(file.file_number),1);
            fileNumberArr.splice(fileNumberArr.indexOf(file.file_number),1);
            if (file === '*' || total === 1) {
                $('#multiple .control-button').hide();
            }
        },
        onSuccess: function(response, file_number, file) {
            originNameArr.push(file.name);
            var imsi = response.split(',');
            fileNameArr.push(imsi[1]);
            fileNumberArr.push(file_number);
        },
        onError: function(event, file, file_number) {
            // 오류 처리
        },
        templateFile: "<div id='fileup-[INPUT_ID]-[FILE_NUM]' class='fileup-file [TYPE]'>\
			<div class='fileup-preview'>\
				<img src='[PREVIEW_SRC]' alt='[NAME]'/>\
			</div>\
			<div class='fileup-container'>\
				<div class='fileup-description'>\
					<span class='fileup-name'>[NAME]</span>\
				</div>\
				<div class='fileup-controls'>\
					<span class='fileup-remove' onclick=\"$.fileup('[INPUT_ID]', 'remove', '[FILE_NUM]');\" title='[REMOVE]'></span>\
				</div>\
				<div class='fileup-result'></div>\
				<div class='fileup-progress'>\
					<div class='fileup-progress-bar'></div>\
				</div>\
			</div>\
			<div class='fileup-clear'></div>\
		</div>"
    });

    // 문서 인쇄
    function DocPrint(DocumentReportID){
        window.open("./my_document_draft_print.php?DocumentReportID="+DocumentReportID, "_blank",
            "toolbar=yes,scrollbars=yes,resizable=yes,top=100,left=100,width=1000,height=900");
    }

    // 파일 업로드 팝업
    function PopupFileUpForm(FormName1,FormName2,FormName3,UpPath){
        var openurl = "./popup_doc_file_upload_form.php?FormName1="+FormName1+"&FormName2="+FormName2+"&FormName3="+FormName3+"&UpPath="+UpPath;
        $.colorbox({
            href:openurl,
            width:"500",
            height:"300",
            title:"",
            iframe:true,
            scrolling:false
        });
    }

    // 폼 전송
    function FormSubmit(DocumentReportState){
        document.RegForm.DocumentReportState.value = DocumentReportState;

        // 자바스크립트 변수를 숨긴 input에 담아 전송
        if (document.RegForm.FileName) {
            document.RegForm.FileName.value      = fileNameArr.join();
            document.RegForm.FileRealName.value  = originNameArr.join();
        }

        let submitMsg = "";
        if (DocumentReportState == 1) {
            submitMsg = "제출하시겠습니까?";
        } else if (DocumentReportState == 0) {
            submitMsg = "삭제하시겠습니까?";
        }

        // 저장(2)일 때는 확인창 없이 바로 전송
        if (DocumentReportState == 2) {
            document.RegForm.action = "my_document_holiday_action.php";
            document.RegForm.submit();
            return;
        }

        // 그 외(제출, 삭제) 시 확인 후 전송
        if (DocumentReportState == 1 || DocumentReportState == 0) {
            UIkit.modal.confirm(submitMsg, function(){
                document.RegForm.action = "my_document_holiday_action.php";
                document.RegForm.submit();
            });
        }
    }
</script>

<!-- ===========================================   froala_editor   =========================================== -->
<style>
    #_____DocumentReportContent {
        width: 81%;
        margin: auto;
        text-align: left;
    }
</style>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.3.0/codemirror.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.3.0/mode/xml/xml.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/froala_editor.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/align.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/char_counter.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/code_beautifier.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/code_view.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/colors.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/draggable.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/emoticons.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/entities.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/file.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/font_size.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/font_family.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/fullscreen.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/image.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/image_manager.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/line_breaker.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/inline_style.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/link.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/lists.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/paragraph_format.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/paragraph_style.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/quick_insert.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/quote.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/table.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/save.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/url.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/video.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/help.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/print.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/third_party/spell_checker.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/special_characters.min.js"></script>
<script type="text/javascript" src="../editors/froala_editor_3/js/plugins/word_paste.min.js"></script>

<script>
    (function () {
        const editorInstance = new FroalaEditor('#_____DocumentReportContent', {
            key: "xGE6oB4B3C3A6D6E5fLUQZf1ASFb1EFRNh1Hb1BCCQDUHnA8B6E5B4B1C3I3A1B8A6==",
            enter: FroalaEditor.ENTER_BR,
            heightMin: 300,
            fileUploadURL: '../froala_editor_file_upload.php',
            imageUploadURL: '../froala_editor_image_upload.php',
            placeholderText: null,
            events: {
                initialized: function () {
                    const editor = this;
                    this.el.closest('form').addEventListener('submit', function (e) {
                        console.log(editor.$oel.val());
                        e.preventDefault();
                    });
                }
            }
        });
    })();
</script>
<!-- ===========================================   froala_editor   =========================================== -->

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
      integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
      crossorigin="anonymous">

<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>
