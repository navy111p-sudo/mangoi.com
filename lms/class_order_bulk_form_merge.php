<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');

$MemberID   = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
$SearchText = isset($_REQUEST["SearchText"]) ? $_REQUEST["SearchText"] : "";

include_once('./inc_common_list_css.php');
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $_SITE_TITLE_; ?></title>
    <link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_; ?>">
    <link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_; ?>">
    <link rel="stylesheet" type="text/css" href="./css/common.css">
</head>
<body>
<style>
    body { background:#fff; }
    .ContentPopup { padding:30px 30px; text-align:center; }
    .ContentPopup h2 {
        border-bottom:1px solid #ccc;
        padding-bottom:10px;
        font-size:16px;
        color:#444;
        text-align:left;
        margin-bottom:50px;
    }
</style>

<br/>
<div class="ContentPopup" style="text-align:center;margin-top:-30px;">
    <!-- 메뉴 제목 -->
    <h2 class="Font1">단체 수강 신청 (통합·레벨체험) - 엑셀파일 업로드</h2> <!-- [레벨체험] 제목 살짝 수정 -->

    <form name="RegForm" method="post" enctype="multipart/form-data" accept-charset="UTF-8">
        <div style="margin-bottom:20px;line-height:1.5;">
            <span style="color:#ff0000;">
                업로드 시간이 오래 걸릴 수 있습니다.<br>
                업로드 후 창이 닫힐 때까지 창이 닫힐 때까지 기다려 주세요.<br><br>
            </span>
            <span>
                <!-- 통합·레벨체험 전용 양식 다운로드 -->
                <a href="javascript:window.open('../excel_sample/class_order_bulk_form_merge.xls', '_system');"
                   style="margin:0 auto;display:block; background-color:#888888; color:#ffffff; text-align:center; width:120px; line-height:32px; font-size:14px;"
                   download>양식다운로드</a>
                <br><br>
                반드시 위 양식을 다운받아 작성 후 업로드 해주세요.<br><br>
                <strong>수업구분</strong> 컬럼에 다음 값 중 <u>하나</u>를 입력하면 됩니다.<br>
                <span style="color:#0066cc;">정규</span> / <span style="color:#0066cc;">체험</span> / <span style="color:#0066cc;">레벨</span> / <span style="color:#0066cc;">통합</span> / <span style="color:#0066cc;">레벨체험</span><br>
                <em>(통합&nbsp;: 레벨&nbsp;→ 체험&nbsp;→ 정규)<br>(레벨체험&nbsp;: 레벨&nbsp;→ 체험)</em> <!-- [레벨체험] 설명 추가 -->
                <br><br><br><br><br><br>
            </span>
        </div>
        <!-- merge 모드 -->
        <input type="hidden" name="mode" value="merge">
        <input type="file" name="UpFile" id="UpFile" style="width:200px; height:32px; line-height:32px; margin-bottom:20px;">
    </form>

    <div class="BtnJoin" style="margin-bottom:100px;text-align:center;" id="BtnUpload">
        <a href="javascript:FormSubmit();"
           style="margin:0 auto;display:block; background-color:#556BAC; color:#ffffff; text-align:center; width:110px; line-height:32px; font-size:14px;"
           download>업로드</a>
    </div>
</div>

<script>
    function FormSubmit(){
        if (document.RegForm.UpFile.value=="") {
            alert("엑셀 파일을 선택해 주세요.");
        } else {
            document.getElementById("BtnUpload").innerHTML =
                "<img src='images/uploading_ing.gif'><br><br>수강신청서 분석 중입니다.";
            document.RegForm.action = "class_order_bulk_value_check_merge.php";
            document.RegForm.submit();
        }
    }
    parent.$.colorbox.resize({width:"95%", height:"95%", maxWidth: "750", maxHeight: "650"});
</script>
</body>
</html>
<?php
include_once('../includes/dbclose.php');
?>
