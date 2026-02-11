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

<body class="disable_transitions sidebar_main_open sidebar_main_swipe" style="padding-top:0px;">

<?php
$BookID = isset($_REQUEST["BookID"]) ? $_REQUEST["BookID"] : "";
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$BookQuizID = isset($_REQUEST["BookQuizID"]) ? $_REQUEST["BookQuizID"] : "";
$BookQuizDetailID = isset($_REQUEST["BookQuizDetailID"]) ? $_REQUEST["BookQuizDetailID"] : "";

if ($BookQuizDetailID!=""){

    $Sql = "
            select 
                    A.*,
                    B.BookQuizName 
            from BookQuizDetails A 
                inner join BookQuizs B on A.BookQuizID=B.BookQuizID 
            where A.BookQuizDetailID=:BookQuizDetailID";
    $Stmt = $DbConn->prepare($Sql);
    $Stmt->bindParam(':BookQuizDetailID', $BookQuizDetailID);
    $Stmt->execute();
    $Stmt->setFetchMode(PDO::FETCH_ASSOC);
    $Row = $Stmt->fetch();
    $Stmt = null;

	$BookQuizDetailQuizType = $Row["BookQuizDetailQuizType"];
	$BookQuizDetailQuestionType = $Row["BookQuizDetailQuestionType"];
	$BookQuizDetailAnswerType = $Row["BookQuizDetailAnswerType"];
    $BookQuizDetailText = $Row["BookQuizDetailText"];
	$BookQuizDetailTextQuestion = $Row["BookQuizDetailTextQuestion"];
	$BookQuizDetailVideoCode = $Row["BookQuizDetailVideoCode"];
	$BookQuizDetailSoundFileName = $Row["BookQuizDetailSoundFileName"];
	$BookQuizDetailSoundFileRealName = $Row["BookQuizDetailSoundFileRealName"];
    $BookQuizDetailImageFileName = $Row["BookQuizDetailImageFileName"];
    $BookQuizDetailChoice1 = $Row["BookQuizDetailChoice1"];
    $BookQuizDetailChoice2 = $Row["BookQuizDetailChoice2"];
    $BookQuizDetailChoice3 = $Row["BookQuizDetailChoice3"];
    $BookQuizDetailChoice4 = $Row["BookQuizDetailChoice4"];
	$BookQuizDetailChoiceImage1 = $Row["BookQuizDetailChoiceImage1"];
	$BookQuizDetailChoiceImage2 = $Row["BookQuizDetailChoiceImage2"];
	$BookQuizDetailChoiceImage3 = $Row["BookQuizDetailChoiceImage3"];
	$BookQuizDetailChoiceImage4 = $Row["BookQuizDetailChoiceImage4"];
    $BookQuizDetailCorrectAnswer = $Row["BookQuizDetailCorrectAnswer"];
    $BookQuizDetailView = $Row["BookQuizDetailView"];
    $BookQuizDetailState = $Row["BookQuizDetailState"];

    $BookQuizName = $Row["BookQuizName"];

}else{
	$BookQuizDetailQuizType = "1";
	$BookQuizDetailQuestionType = "1";
	$BookQuizDetailAnswerType = "1";
	$BookQuizDetailTextQuestion = "";
	$BookQuizDetailVideoCode = "";
	$BookQuizDetailSoundFileName = "";
    $BookQuizDetailText = "";
    $BookQuizDetailImageFileName = "";
    $BookQuizDetailChoice1 = "";
    $BookQuizDetailChoice2 = "";
    $BookQuizDetailChoice3 = "";
    $BookQuizDetailChoice4 = "";
	$BookQuizDetailChoiceImage1 = "";
	$BookQuizDetailChoiceImage2 = "";
	$BookQuizDetailChoiceImage3 = "";
	$BookQuizDetailChoiceImage4 = "";
    $BookQuizDetailCorrectAnswer = 1;
    $BookQuizDetailView = 1;
    $BookQuizDetailState = 1;

    $Sql = "
            select 
                    A.*
            from BookQuizs A 
            where A.BookQuizID=:BookQuizID";
    $Stmt = $DbConn->prepare($Sql);
    $Stmt->bindParam(':BookQuizID', $BookQuizID);
    $Stmt->execute();
    $Stmt->setFetchMode(PDO::FETCH_ASSOC);
    $Row = $Stmt->fetch();
    $Stmt = null;    
    
    $BookQuizName = $Row["BookQuizName"];
}


if ($BookQuizDetailImageFileName==""){
    $StrBookQuizDetailImageFileName = "images/logo_mangoi.png";
}else{
    $StrBookQuizDetailImageFileName = "../uploads/book_quiz_images/".$BookQuizDetailImageFileName;
}

if($BookQuizDetailChoiceImage1=="") {
	$StrBookQuizDetailChoiceImage1 = "";
} else {
	$StrBookQuizDetailChoiceImage1 = "../uploads/book_quiz_images/".$BookQuizDetailChoiceImage1;
}

if($BookQuizDetailChoiceImage2=="") {
	$StrBookQuizDetailChoiceImage2 = "";
} else {
	$StrBookQuizDetailChoiceImage2 = "../uploads/book_quiz_images/".$BookQuizDetailChoiceImage2;
}

if($BookQuizDetailChoiceImage3=="") {
	$StrBookQuizDetailChoiceImage3 = "";
} else {
	$StrBookQuizDetailChoiceImage3 = "../uploads/book_quiz_images/".$BookQuizDetailChoiceImage3;
}

if($BookQuizDetailChoiceImage4=="") {
	$StrBookQuizDetailChoiceImage4 = "";
} else {
	$StrBookQuizDetailChoiceImage4 = "../uploads/book_quiz_images/".$BookQuizDetailChoiceImage4;
}

$AudioFileName = $BookQuizDetailSoundFileName;
?>


<div id="page_content">
    <div id="page_content_inner">
        <form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" class="uk-form-stacked" accept-charset="UTF-8" autocomplete="off">
        <input type="hidden" name="BookID" value="<?=$BookID?>">
        <input type="hidden" name="BookQuizID" value="<?=$BookQuizID?>">
        <input type="hidden" name="BookQuizDetailID" value="<?=$BookQuizDetailID?>">
        <input type="hidden" name="ListParam" value="<?=$ListParam?>">
		<input type="hidden" name="UpFileVal" value="<?=$BookQuizDetailImageFileName?>">
		<input type="hidden" name="BookQuizDetailChoiceImageVal1" value="<?=$BookQuizDetailChoiceImage1?>">
		<input type="hidden" name="BookQuizDetailChoiceImageVal2" value="<?=$BookQuizDetailChoiceImage2?>">
		<input type="hidden" name="BookQuizDetailChoiceImageVal3" value="<?=$BookQuizDetailChoiceImage3?>">
		<input type="hidden" name="BookQuizDetailChoiceImageVal4" value="<?=$BookQuizDetailChoiceImage4?>">
        <div class="uk-grid" data-uk-grid-margin>
            <div class="uk-width-large-7-10">
                <div class="md-card">
                    <div class="user_heading" data-uk-sticky="{ top: 48, media: 960 }">
                        <div class="user_heading_content">
                            <h2 class="heading_b"><span class="uk-text-truncate" id="user_edit_uname"><?=$BookQuizName?></span><span class="sub-heading" id="user_edit_position"><?=$퀴즈관리[$LangID]?></span></h2>
                        </div>
                    </div>
                    <div class="user_content">
						<!-- 고정값 -->
                        <h3 class="full_width_in_card heading_c"> 
                            문제 관리
                        </h3>
                        <div class="uk-margin-top">
                            <div class="uk-grid" data-uk-grid-margin>
                                <div class="uk-width-medium-1-1">
                                    <label for="BookQuizDetailText">질문내용</label>
                                    <input type="text" id="BookQuizDetailText" name="BookQuizDetailText" value="<?=$BookQuizDetailText?>" class="md-input label-fixed"/>
                                </div>
                            </div>
                        </div>

                        <div class="uk-margin-top">
                            <div class="uk-grid" data-uk-grid-margin>
                                <div class="uk-width-medium-1-1 uk-input-group" style="padding-top:10px;">
								<label>퀴즈타입 :  </label>
                                    <span class="icheck-inline">
                                        <input type="radio" id="BookQuizDetailQuizType1" name="BookQuizDetailQuizType" class="radio_input" <?php if ($BookQuizDetailQuizType==1) { echo "checked";}?> value="1" />
                                        <label for="BookQuizDetailQuizType1" class="radio_label" onclick="BookQuizDetailQuizType('1');"><span class="radio_bullet"></span>일반문제</label>
                                    </span>
                                    <span class="icheck-inline">
                                        <input type="radio" id="BookQuizDetailQuizType2" name="BookQuizDetailQuizType" class="radio_input" <?php if ($BookQuizDetailQuizType==2) { echo "checked";}?> value="2" />
                                        <label for="BookQuizDetailQuizType2" class="radio_label" onclick="BookQuizDetailQuizType('2');"><span class="radio_bullet"></span>듣기평가</label>
                                    </span>
                                </div>
                            </div>
                        </div>

						<!-- 음원 라인 -->
                        <div class="uk-margin-top" id="UpSoundFileVisible" style="display: <?if($BookQuizDetailQuizType==1){?>none<?}?>">
                            <div class="uk-grid" data-uk-grid-margin>
                                <div class="uk-width-medium-1-1">
									<audio controls id="AudioFileName" style="height:50px;">
										<source src="../uploads/book_quiz_audio/<?=$AudioFileName?>" type="audio/mpeg">
									</audio>

									<input type="hidden" name="AudioFileName" value="<?=$AudioFileName?>" style="width:200px;margin-top:-7px;" >
									<a href="javascript:PopupAddSound('AudioFileName','RegForm.AudioFileName','../uploads/book_quiz_audio');" class="md-btn md-btn-primary md-btn-wave-light waves-effect waves-button waves-light" >음원업로드</a>
                                </div>
                            </div>
                        </div>
						<!-- 음원 라인 // -->

                        <!-- 이미지 나 텍스트로 지정 부분 -->
                        <div class="uk-margin-top">
                            <div class="uk-grid" data-uk-grid-margin>
                                <div class="uk-width-medium-1-1 uk-input-group" style="padding-top:10px;">
								<label>문제형식 :  </label>
                                    <span class="icheck-inline">
                                        <input type="radio" id="BookQuizDetailQuestionType1" name="BookQuizDetailQuestionType" class="radio_input" <?php if ($BookQuizDetailQuestionType==1) { echo "checked";}?> value="1" />
                                        <label for="BookQuizDetailQuestionType1" class="radio_label" onclick="BookQuizDetailQuestionType('1');"><span class="radio_bullet"></span>이미지</label>
                                    </span>
                                    <span class="icheck-inline">
                                        <input type="radio" id="BookQuizDetailQuestionType2" name="BookQuizDetailQuestionType" class="radio_input" <?php if ($BookQuizDetailQuestionType==2) { echo "checked";}?> value="2" />
                                        <label for="BookQuizDetailQuestionType2" class="radio_label" onclick="BookQuizDetailQuestionType('2');"><span class="radio_bullet"></span>텍스트</label>
                                    </span>
									<span class="icheck-inline">
                                        <input type="radio" id="BookQuizDetailQuestionType4" name="BookQuizDetailQuestionType" class="radio_input" <?php if ($BookQuizDetailQuestionType==4) { echo "checked";}?> value="4" />
                                        <label for="BookQuizDetailQuestionType4" class="radio_label" onclick="BookQuizDetailQuestionType('4');"><span class="radio_bullet"></span>동영상</label>
                                    </span>
                                    <span class="icheck-inline">
                                        <input type="radio" id="BookQuizDetailQuestionType3" name="BookQuizDetailQuestionType" class="radio_input" <?php if ($BookQuizDetailQuestionType==3) { echo "checked";}?> value="3" />
                                        <label for="BookQuizDetailQuestionType3" class="radio_label" onclick="BookQuizDetailQuestionType('3');"><span class="radio_bullet"></span>없음</label>
                                    </span>
                                </div>
                            </div>
                        </div>

						<div class="uk-width-medium-1-1 uk-input-group" id="BookQuizDetailVideoCodeText" style="display: <?if($BookQuizDetailQuestionType!=4){?>none<?}?>">
							※ Youtube 코드를 입력하세요.(아래 예제의 <span style='color:#ff0000;'>빨간색</span> 코드 부분)<br>
							※ 예) https://www.youtube.com/watch?v=<span style='color:#ff0000;'>LDPt7XLrbks</span>
						</div>


						<!-- 이미지, 텍스트영역 라인 -->
                        <div class="uk-margin-top" id="UpFileVisible" style="display: <?if($BookQuizDetailQuestionType!=1){?>none<?}?>" >
                            <div class="uk-grid" data-uk-grid-margin>
                                <div class="uk-width-medium-1-1">
                                    <input type="file" name="UpFile" id="UpFile" class="dropify" data-default-file="<?=$StrBookQuizDetailImageFileName?>" />
                                </div>
                            </div>
                        </div>

                        <div class="uk-margin-top" id="BookQuizDetailTextQuestionVisible" style="display: <?if($BookQuizDetailQuestionType!=2){?>none<?}?>" >
                            <div class="uk-grid" data-uk-grid-margin>
                                <div class="uk-width-medium-1-1">
                                    <textarea form="RegForm" rows="5" style="width: 717px; " name="BookQuizDetailTextQuestion" id="BookQuizDetailTextQuestion" ><?=$BookQuizDetailTextQuestion?></textarea>
									<!-- <input type="text" id="BookQuizDetailText" name="BookQuizDetailText" value="<?=$BookQuizDetailText?>" class="md-input label-fixed"/> -->
                                </div>
                            </div>
                        </div>

                        <div class="uk-margin-top" id="BookQuizDetailVideoCodeVisible" style="display: <?if($BookQuizDetailQuestionType!=4){?>none<?}?>" >
                            <div class="uk-grid" data-uk-grid-margin>
                                <div class="uk-width-medium-1-2" >
									<label for="BookQuizDetailVideoCode"><?=$영상코드[$LangID]?></label><input type="text" id="BookQuizDetailVideoCode" name="BookQuizDetailVideoCode" value="<?=$BookQuizDetailVideoCode?>" class="md-input label-fixed" />
                                </div>
								<span class="" style="margin-top:7px; padding-left: 10px;" ><a class="md-btn" href="javascript:OpenVideoPlayer();"><?=$영상확인[$LangID]?></a></span>
                            </div>
                        </div>
						<!-- 이미지, 텍스트영역 라인 // -->

                        <div class="uk-margin-top">
                            <div class="uk-grid" data-uk-grid-margin>
                                <div class="uk-width-medium-1-1 uk-input-group" style="padding-top:10px;">
								<label>보기형식 :  </label>
                                    <span class="icheck-inline">
                                        <input type="radio" id="BookQuizDetailAnswerType1" name="BookQuizDetailAnswerType" class="radio_input" <?php if ($BookQuizDetailAnswerType==1) { echo "checked";}?> value="1" />
                                        <label for="BookQuizDetailAnswerType1" class="radio_label" onclick="BookQuizDetailAnswerType('1');"><span class="radio_bullet"></span>텍스트</label>
                                    </span>
                                    <span class="icheck-inline">
                                        <input type="radio" id="BookQuizDetailAnswerType2" name="BookQuizDetailAnswerType" class="radio_input" <?php if ($BookQuizDetailAnswerType==2) { echo "checked";}?> value="2" />
                                        <label for="BookQuizDetailAnswerType2" class="radio_label" onclick="BookQuizDetailAnswerType('2');"><span class="radio_bullet"></span>이미지</label>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="uk-margin-top">
                            <div class="uk-grid" data-uk-grid-margin>
                                <div class="uk-width-medium-1-1 uk-input-group">
                                    ※ 아래 보기는 순서대로 입력하되 1, 2번은 반드시 입력해 주세요. 
                                </div>
                            </div>
                        </div>

                        <div class="uk-margin-top" id="BookQuizDetailChoiceVisible" style="display: <?if($BookQuizDetailAnswerType==2){?>none<?}?>" >
                            <div class="uk-grid" data-uk-grid-margin>
                                <div class="uk-width-medium-1-1">
                                    <label for="BookQuizDetailText">보기 1)</label>
                                    <input type="text" id="BookQuizDetailChoice1" name="BookQuizDetailChoice1" value="<?=$BookQuizDetailChoice1?>" class="md-input label-fixed"/>
                                </div>
                                <div class="uk-width-medium-1-1">
                                    <label for="BookQuizDetailText">보기 2)</label>
                                    <input type="text" id="BookQuizDetailChoice2" name="BookQuizDetailChoice2" value="<?=$BookQuizDetailChoice2?>" class="md-input label-fixed"/>
                                </div>
                                <div class="uk-width-medium-1-1">
                                    <label for="BookQuizDetailText">보기 3)</label>
                                    <input type="text" id="BookQuizDetailChoice3" name="BookQuizDetailChoice3" value="<?=$BookQuizDetailChoice3?>" class="md-input label-fixed"/>
                                </div>
                                <div class="uk-width-medium-1-1">
                                    <label for="BookQuizDetailText">보기 4)</label>
                                    <input type="text" id="BookQuizDetailChoice4" name="BookQuizDetailChoice4" value="<?=$BookQuizDetailChoice4?>" class="md-input label-fixed"/>
                                </div>
                            </div>
                        </div>

                        <div class="uk-margin-top" id="BookQuizDetailChoiceImageVisible" style="display: <?if($BookQuizDetailAnswerType==1){?>none<?}?>" >
                            <div class="uk-grid" data-uk-grid-margin>
                                <div class="uk-width-medium-1-1">
                                    <label for="BookQuizDetailText">보기 1)</label>
                                    <input type="file" id="BookQuizDetailChoiceImage1" name="BookQuizDetailChoiceImage1" class="dropify" data-default-file="<?=$StrBookQuizDetailChoiceImage1?>" />
                                </div>
                                <div class="uk-width-medium-1-1">
                                    <label for="BookQuizDetailText">보기 2)</label>
                                    <input type="file" id="BookQuizDetailChoiceImage2" name="BookQuizDetailChoiceImage2" class="dropify" data-default-file="<?=$StrBookQuizDetailChoiceImage2?>" />
                                </div>
                                <div class="uk-width-medium-1-1">
                                    <label for="BookQuizDetailText">보기 3)</label>
                                    <input type="file" id="BookQuizDetailChoiceImage3" name="BookQuizDetailChoiceImage3" class="dropify" data-default-file="<?=$StrBookQuizDetailChoiceImage3?>" />
                                </div>
                                <div class="uk-width-medium-1-1">
                                    <label for="BookQuizDetailText">보기 4)</label>
                                    <input type="file" id="BookQuizDetailChoiceImage4" name="BookQuizDetailChoiceImage4" class="dropify" data-default-file="<?=$StrBookQuizDetailChoiceImage4?>" />
                                </div>
                            </div>
                        </div>

                        <h3 class="full_width_in_card heading_c"> 
                            정답
                        </h3>
                        <div class="uk-margin-top">
                            <div class="uk-grid" data-uk-grid-margin>
                                <div class="uk-width-medium-1-1 uk-input-group" style="padding-top:10px;">
                                    <span class="icheck-inline">
                                        <input type="radio" id="BookQuizDetailCorrectAnswer1" name="BookQuizDetailCorrectAnswer" <?php if ($BookQuizDetailCorrectAnswer==1) { echo "checked";}?> value="1" data-md-icheck/>
                                        <label for="BookQuizDetailCorrectAnswer1" class="inline-label">1번</label>
                                    </span>
                                    <span class="icheck-inline">
                                        <input type="radio" id="BookQuizDetailCorrectAnswer2" name="BookQuizDetailCorrectAnswer" <?php if ($BookQuizDetailCorrectAnswer==2) { echo "checked";}?> value="2" data-md-icheck/>
                                        <label for="BookQuizDetailCorrectAnswer2" class="inline-label">2번</label>
                                    </span>
                                    <span class="icheck-inline">
                                        <input type="radio" id="BookQuizDetailCorrectAnswer3" name="BookQuizDetailCorrectAnswer" <?php if ($BookQuizDetailCorrectAnswer==3) { echo "checked";}?> value="3" data-md-icheck/>
                                        <label for="BookQuizDetailCorrectAnswer3" class="inline-label">3번</label>
                                    </span>
                                    <span class="icheck-inline">
                                        <input type="radio" id="BookQuizDetailCorrectAnswer4" name="BookQuizDetailCorrectAnswer" <?php if ($BookQuizDetailCorrectAnswer==4) { echo "checked";}?> value="4" data-md-icheck/>
                                        <label for="BookQuizDetailCorrectAnswer4" class="inline-label">4번</label>
                                    </span>
                                </div>
                            </div>
                        </div>


                        <hr>
                        <div class="uk-margin-top">
                            <div class="uk-grid" data-uk-grid-margin>
                                <div class="uk-width-medium-1-2 uk-input-group">
                                    <input type="checkbox" id="BookQuizDetailState" name="BookQuizDetailState" value="1" <?php if ($BookQuizDetailState==1) { echo "checked";}?> data-switchery/>
                                    <label for="BookQuizDetailState" class="inline-label">사용</label>
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

<script>

function BookQuizDetailQuestionType(Type) {
    if(Type==1) {
        document.getElementById("UpFileVisible").style.display = "";
        document.getElementById("BookQuizDetailTextQuestionVisible").style.display = "none";
		document.getElementById("BookQuizDetailVideoCodeVisible").style.display = "none";
		document.getElementById("BookQuizDetailVideoCodeText").style.display = "none";
    } else if(Type==2) {
        document.getElementById("UpFileVisible").style.display = "none";
        document.getElementById("BookQuizDetailTextQuestionVisible").style.display = "";
		document.getElementById("BookQuizDetailVideoCodeVisible").style.display = "none";
		document.getElementById("BookQuizDetailVideoCodeText").style.display = "none";
    } else if(Type==4) {
        document.getElementById("UpFileVisible").style.display = "none";
        document.getElementById("BookQuizDetailTextQuestionVisible").style.display = "none";
		document.getElementById("BookQuizDetailVideoCodeVisible").style.display = "";
		document.getElementById("BookQuizDetailVideoCodeText").style.display = "";
    } else {
        document.getElementById("UpFileVisible").style.display = "none";
        document.getElementById("BookQuizDetailTextQuestionVisible").style.display = "none";
		document.getElementById("BookQuizDetailVideoCodeVisible").style.display = "none";
		document.getElementById("BookQuizDetailVideoCodeText").style.display = "none";
	}
}

function OpenVideoPlayer() {
	var Code = document.getElementById("BookQuizDetailVideoCode").value;

	if (Code==""){
		UIkit.modal.alert("<?=$동영상_코드를_입력하세요[$LangID]?>");
	}else{
		openurl = "video_player.php?VideoCode="+Code+"&VideoType=1";

		$.colorbox({	
			href:openurl
			,width:"95%" 
			,height:"95%"
			,maxWidth: "850"
			,maxHeight: "750"
			,title:""
			,iframe:true 
			,scrolling:false
			//,onClosed:function(){location.reload(true);}
			//,onComplete:function(){alert(1);}
		}); 
	}
}

function BookQuizDetailQuizType(Type) {
	if(Type==1) {
		document.getElementById("UpSoundFileVisible").style.display = "none";
	} else {
		document.getElementById("UpSoundFileVisible").style.display = "";
	}



}

function BookQuizDetailAnswerType(Type) {
    if(Type==1) {
        document.getElementById("BookQuizDetailChoiceImageVisible").style.display = "none";
        document.getElementById("BookQuizDetailChoiceVisible").style.display = "";
    } else {
        document.getElementById("BookQuizDetailChoiceImageVisible").style.display = "";
        document.getElementById("BookQuizDetailChoiceVisible").style.display = "none";
    }
}

function PopupAddSound(SoundID,FormName,UpPath){
	openurl = "./popup_audio_upload_form.php?SoundID="+SoundID+"&FormName="+FormName+"&UpPath="+UpPath;
	$.colorbox({	
		href:openurl
		,width:"500" 
		,height:"300"
		,title:""
		,iframe:true 
		,scrolling:false
		//,onClosed:function(){location.reload(true);}   
	}); 
}
</script>

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

	var QuizType = document.RegForm.BookQuizDetailQuizType.value;
	var QuestionType = document.RegForm.BookQuizDetailQuestionType.value;
	var AnswerType = document.RegForm.BookQuizDetailAnswerType.value;

    obj = document.RegForm.BookQuizDetailText;
    if (obj.value==""){
        UIkit.modal.alert("<?=$질문내용을_입력하세요[$LangID]?>");
        obj.focus();
        return;
    }

	// 질문 타입에 따른 이미지 또는 내용 확인설정
	if (QuestionType==1) {
		obj = document.RegForm.UpFile;
		var obj2 = document.RegForm.UpFileVal;
		if (obj.value=="" && obj2.value==""){
			UIkit.modal.alert("<?=$문제이미지를_선택하세요[$LangID]?>");
			obj.focus();
			return;
		}
	} else if(QuestionType==2) {
		obj = document.RegForm.BookQuizDetailTextQuestion;
		if (obj.value==""){
			UIkit.modal.alert("<?=$문제예문을_입력하세요[$LangID]?>");
			obj.focus();
			return;
		}
	} else if(QuestionType==4) {
		obj = document.RegForm.BookQuizDetailVideoCode;
		if (obj.value==""){
			UIkit.modal.alert("<?=$유튜브_코드를_입력하세요[$LangID]?>");
			obj.focus();
			return;
		}
	}

	// 퀴즈 타입에 따른 확인설정
	if (QuizType==2) {
		obj = document.RegForm.AudioFileName;
		if (obj.value=="") {
			UIkit.modal.alert("<?=$음원을_업로드하세요[$LangID]?>");
			obj.focus();
			return
		}
	}

	/*
	// 보기 타입에 따른 이미지 또는 내용 확인설정
	if (AnswerType==1) {
		obj = document.RegForm.BookQuizDetailChoice1;
		if (obj.value==""){
			UIkit.modal.alert("1번 보기를 입력하세요.");
			obj.focus();
			return;
		}

		obj = document.RegForm.BookQuizDetailChoice2;
		if (obj.value==""){
			UIkit.modal.alert("2번 보기를 입력하세요.");
			obj.focus();
			return;
		}
	} else if(AnswerType==2) {
		obj = document.RegForm.BookQuizDetailChoiceImage1;
		var obj2 = document.RegForm.BookQuizDetailChoiceImageVal1;
		if (obj.value=="" && obj2.value==""){
			UIkit.modal.alert("1번 보기(이미지)를 업로드하세요.");
			obj.focus();
			return;
		}

		obj = document.RegForm.BookQuizDetailChoiceImage2;
		var obj2 = document.RegForm.BookQuizDetailChoiceImageVal2;
		if (obj.value=="" && obj2.value==""){
			UIkit.modal.alert("2번 보기(이미지)를 업로드하세요.");
			obj.focus();
			return;
		}
	}
	*/


    UIkit.modal.confirm(
        '<?=$저장_하시겠습니까[$LangID]?>?', 
        function(){ 
            document.RegForm.action = "book_quiz_detail_action.php";
            document.RegForm.submit();
        }
    );

}

</script>



<?php
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>