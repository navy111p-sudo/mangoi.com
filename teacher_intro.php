<?php
include_once('./includes/dbopen.php');
include_once('./includes/common.php');
include_once('./includes/member_check.php');

$UseMain = 0;
$UseSub = 1;
$SubCode = "sub_01";

//강사 이름 검색
$SearchName = isset($_REQUEST["SearchName"]) ? $_REQUEST["SearchName"] : "";

//자동으로 강사 소개까지 스크롤하기 위해
$teacherIntro = isset($_REQUEST["teacherIntro"]) ? $_REQUEST["teacherIntro"] : "";


?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
<link href="css/sub_style.css?ver=8" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<link rel="stylesheet" href="./js/colorbox/example2/colorbox.css" />
<script src="./js/colorbox/jquery.colorbox.js"></script>
<?php
include_once('./includes/nojquery_header.php');


$MemberID = $_LINK_MEMBER_ID_;

$Sql = "SELECT SubID from Subs where SubCode=:SubCode";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':SubCode', $SubCode);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$SubID = $Row["SubID"];


if ($UseMain==1){
	$Sql = "SELECT * from Main limit 0,1";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$MainLayout = $Row["MainLayout"];
	$MainLayoutCss = $Row["MainLayoutCss"];
	$MainLayoutJavascript = $Row["MainLayoutJavascript"];
	list($MainLayoutTop, $MainLayoutBottom) = explode("{{Page}}", $MainLayout);
}else{
	$MainLayoutTop = "";
	$MainLayoutBottom = "";
	$MainLayoutCss = "";
	$MainLayoutJavascript = "";
}


if ($UseSub==1){
	$Sql = "SELECT * from Subs where SubID=:SubID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':SubID', $SubID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$SubLayout = $Row["SubLayout"];
	$SubLayoutCss = $Row["SubLayoutCss"];
	$SubLayoutJavascript = $Row["SubLayoutJavascript"];
	list($SubLayoutTop, $SubLayoutBottom) = explode("{{Page}}", $SubLayout);
}else{
	$SubLayoutTop = "";
	$SubLayoutBottom = "";
	$SubLayoutCss = "";
	$SubLayoutJavascript = "";
}


if (trim($MainLayoutCss)!=""){
	echo "\n";
	echo "<style>";
	echo "\n";
	echo $MainLayoutCss;
	echo "\n";
	echo "</style>";
	echo "\n";
}

if (trim($SubLayoutCss)!=""){
	echo "\n";
	echo "<style>";
	echo "\n";
	echo $SubLayoutCss;
	echo "\n";
	echo "</style>";
	echo "\n";
}



?>


</head>
<body>
<?
include_once('./includes/common_body_top.php');
?>
<?php
// $MainLayoutTop = convertHTML(trim($MainLayoutTop));
// $SubLayoutTop = convertHTML(trim($SubLayoutTop));
// $SubLayoutBottom = convertHTML(trim($SubLayoutBottom));
// $MainLayoutBottom = convertHTML(trim($MainLayoutBottom));

if($DomainSiteID==7){
    $MainLayoutTop = convertHTML(trim("{{Piece(header_gumiivyleague)}}"));
    $SubLayoutTop = convertHTML(trim("{{Piece(sub_01_gumiivyleague)}}"));
    $SubLayoutBottom = convertHTML(trim("{{Piece(sub_01_gumiivyleague_bottom)}}"));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_gumiivyleague)}}"));

} else if($DomainSiteID==8){ //engliseed.kr
    $MainLayoutTop = convertHTML(trim("{{Piece(header_engliseed)}}"));
    $SubLayoutTop = convertHTML(trim("{{Piece(sub_06_engliseed)}}"));
    $SubLayoutBottom = convertHTML(trim("{{Piece(sub_01_engliseed_bottom)}}"));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_engliseed)}}"));

} else if($DomainSiteID==9){ //live.engedu.kr
    $MainLayoutTop = convertHTML(trim("{{Piece(header_engedu)}}"));
    $SubLayoutTop = convertHTML(trim("{{Piece(sub_06_engedu)}}"));
    $SubLayoutBottom = convertHTML(trim("{{Piece(sub_01_engedu_bottom)}}"));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_engedu2)}}"));


} else {
    $MainLayoutTop = convertHTML(trim($MainLayoutTop));
    $SubLayoutTop = convertHTML(trim($SubLayoutTop));
    $SubLayoutBottom = convertHTML(trim($SubLayoutBottom));
    $MainLayoutBottom = convertHTML(trim($MainLayoutBottom));
  }


echo "\n";
echo $MainLayoutTop;
echo "\n";
echo $SubLayoutTop;
echo "\n";


$Sql2 = "SELECT * from TeacherPayTypeItems A WHERE TeacherPayTypeItemState = 1 
				Order By TeacherPayTypeItemOrder";
$Stmt2 = $DbConn->prepare($Sql2);
$Stmt2->execute();
$Stmt2->setFetchMode(PDO::FETCH_ASSOC);


?>

<? if($DomainSiteID == 7){ //gumiivyleage?>
	<div class="sub_wrap">

<div  class="sub_title_common_wrap"><h2 class="sub_title_common"><b>강사</b> 소개</h2></div>

<!-- 강사 선발 -->
<section class="tea_select_wrap">
	<div class="tea_select_area">
		<div class="tea_select_inner">
			<h2 class="caption_sub left TrnTag">강사선발 <span class="normal">과정</span></h2>
			<div class="tea_select_text TrnTag">5단계 채용과정과 5개 평가항목을 적용시켜 200점 만점 중 160점 이상의 우수한 강사 확보</div>
			<h3 class="caption_tea_select TrnTag">5단계 채용과정</h3>
			<img src="images/img_recruit_process.png" class="img_recruit_process" alt="5단계 채용과정">
			<h3 class="caption_tea_select TrnTag">5개 평가항목서</h3>
		</div>
		<div class="tea_select_form">
			<h4>Candidate Evaluation Form</h4>
			<table class="tea_select_table">
				<tr>
					<th>Criteria</th>
					<th>Check Lists</th>
					<th class="cell_bg_blue">Full Socore</th>
					<th>Interviewer</th>
					<th style="width:11%;">etc</th>
				</tr>
				<tr>
					<td rowspan="3">Appearance</td>
					<td>Visual Apperance</td>
					<td class="cell_bg_blue">10</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>Attitude</td>
					<td class="cell_bg_blue">10</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>Voice Sound</td>
					<td class="cell_bg_blue">10</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td rowspan="3">Character</td>
					<td>Cooperation</td>
					<td class="cell_bg_blue">10</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>Diligence</td>
					<td class="cell_bg_blue">10</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>Health Check</td>
					<td class="cell_bg_blue">10</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td rowspan="3">Teaching Quality</td>
					<td>Pronunciation</td>
					<td class="cell_bg_blue">30</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>Sample Class Demo</td>
					<td class="cell_bg_blue">30</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>College Graduation</td>
					<td class="cell_bg_blue">10</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td rowspan="3">Experience</td>
					<td>Teacher suitable for non-face-to-face (online) classes</td>
					<td class="cell_bg_blue">20</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>Teaching experience</td>
					<td class="cell_bg_blue">20</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>Commuting time</td>
					<td class="cell_bg_blue">20</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>Potential</td>
					<td>Potential bearing by Traning</td>
					<td class="cell_bg_blue">10</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<th colspan="2">Total Score</th>
					<th class="cell_bg_blue">200</th>
					<th></th>
					<th></th>
				</tr>
			</table>
		</div>
	</div>
</section>

<!-- 강사 프로필 -->
<section class="tea_profile_wrap">
	<div id="teacherIntro" class="tea_profile_area">
		<h2 class="caption_sub">아이비리그 강사프로필</h2>
		<h4 class="caption_tea_profile"><span class="normal">강사 검색</span>
			<input type="text" id="SearchName" name="SearchName" value="<?=$SearchName?>" class="md-input label-fixed"  style="height:30px;"/>
			<button onClick="javascript:searchTeacher();" style="height:33px;width:80px;">검색</button>
		</h4>
		<?php
		$flagCount = 1;
		while($Row2 = $Stmt2->fetch()) {
			$TeacherPayTypeItemTitle2 = $Row2["TeacherPayTypeItemTitle2"];
			/*
			if($TeacherPayTypeItemTitle2=="필리핀") {
				$flag = "<img src=\"images/flag_phi.png\" alt=\"필리핀국기\">";
			} else if($TeacherPayTypeItemTitle2=="미국/캐나다") {
				$flag = "<img src=\"images/flag_usa.png\" alt=\"미국국기\">";
			} else {
				$flag = "<img src='images/".$Row2["NationalFlagFile"]."' >";	
			}
			*/
			$flag = "<img src='images/".$Row2["NationalFlagFile"]."' >";
		?>
		
			<h4 class="caption_tea_profile"><?=$flag?> <?=$Row2["TeacherPayTypeItemTitle2"]?> <span class="normal">강사</span></h4>
			<ul class="tea_profile_list" id="teacher_table<?=$flagCount?>">
				
			</ul>
		<?php 
			$flagCount ++;
		} 
		?>
<!--
		<h4 class="caption_tea_profile"><img src="images/flag_usa.png" alt="미국국기"> 미국 <span class="normal">강사</span></h4>
		<ul class="tea_profile_list usa">
			<li>
				<div class="profile_left"><img src="images/photo_teacher_ed.jpg" class="photo_profile" alt="ED"></div>
				<div class="profile_right">
					<table class="profile_table">
						<tr>
							<th>Name</th>
							<td><b class="bold_name">ED</b></td>
						</tr>
						<tr>
							<th>Education</th>
							<td>Ed Deluna is a graduate from New York University in Hospitality Management and Pedagogy. He had worked as a member of the management team of Affinia . He handled training and development of staff in addition to the requisite management duties . In line with his focus on training he had done extensive training of New York Public School Teachers in Pedagogy Instructional Skills. He also taught English and Mathematics in New York City public schools to Elementary Middle Scholl and High School Students .Currently he works as a Management Consultant and Trainer for PPM Informatics as an English Teacher for Mangoi teaching English to Korean students.</td>
						</tr>
						<tr>
							<th>Comment</th>
							<td>A journey of a thousand miles must begin with the first step.</td>
						</tr>
					</table>
				</div>
			</li>
		</ul>
-->
	</div>
</section>
</div>
<?} else if($DomainSiteID == 8){ //engliseed?>
	<div class="sub_wrap">

<div  class="sub_title_common_wrap"><h2 class="sub_title_common"><b>강사</b> 소개</h2></div>

<!-- 강사 선발 -->
<section class="tea_select_wrap">
	<div class="tea_select_area">
		<div class="tea_select_inner">
			<h2 class="caption_sub left TrnTag">강사선발 <span class="normal">과정</span></h2>
			<div class="tea_select_text TrnTag">5단계 채용과정과 5개 평가항목을 적용시켜 200점 만점 중 160점 이상의 우수한 강사 확보</div>
			<h3 class="caption_tea_select TrnTag">5단계 채용과정</h3>
			<img src="images/img_recruit_process.png" class="img_recruit_process" alt="5단계 채용과정">
			<h3 class="caption_tea_select TrnTag">5개 평가항목서</h3>
		</div>
		<div class="tea_select_form">
			<h4>Candidate Evaluation Form</h4>
			<table class="tea_select_table">
				<tr>
					<th>Criteria</th>
					<th>Check Lists</th>
					<th class="cell_bg_blue">Full Socore</th>
					<th>Interviewer</th>
					<th style="width:11%;">etc</th>
				</tr>
				<tr>
					<td rowspan="3">Appearance</td>
					<td>Visual Apperance</td>
					<td class="cell_bg_blue">10</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>Attitude</td>
					<td class="cell_bg_blue">10</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>Voice Sound</td>
					<td class="cell_bg_blue">10</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td rowspan="3">Character</td>
					<td>Cooperation</td>
					<td class="cell_bg_blue">10</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>Diligence</td>
					<td class="cell_bg_blue">10</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>Health Check</td>
					<td class="cell_bg_blue">10</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td rowspan="3">Teaching Quality</td>
					<td>Pronunciation</td>
					<td class="cell_bg_blue">30</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>Sample Class Demo</td>
					<td class="cell_bg_blue">30</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>College Graduation</td>
					<td class="cell_bg_blue">10</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td rowspan="3">Experience</td>
					<td>Teacher suitable for non-face-to-face (online) classes</td>
					<td class="cell_bg_blue">20</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>Teaching experience</td>
					<td class="cell_bg_blue">20</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>Commuting time</td>
					<td class="cell_bg_blue">20</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>Potential</td>
					<td>Potential bearing by Traning</td>
					<td class="cell_bg_blue">10</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<th colspan="2">Total Score</th>
					<th class="cell_bg_blue">200</th>
					<th></th>
					<th></th>
				</tr>
			</table>
		</div>
	</div>
</section>

<!-- 강사 프로필 -->
<section class="tea_profile_wrap">
	<div id="teacherIntro" class="tea_profile_area">
		<h2 class="caption_sub">잉글리씨드 강사프로필</h2>
		<h4 class="caption_tea_profile"><span class="normal">강사 검색</span>
			<input type="text" id="SearchName" name="SearchName" value="<?=$SearchName?>" class="md-input label-fixed"  style="height:30px;"/>
			<button onClick="javascript:searchTeacher();" style="height:33px;width:80px;">검색</button>
		</h4>
		<?php
		$flagCount = 1;
		while($Row2 = $Stmt2->fetch()) {
			$TeacherPayTypeItemTitle2 = $Row2["TeacherPayTypeItemTitle2"];
			/*
			if($TeacherPayTypeItemTitle2=="필리핀") {
				$flag = "<img src=\"images/flag_phi.png\" alt=\"필리핀국기\">";
			} else if($TeacherPayTypeItemTitle2=="미국/캐나다") {
				$flag = "<img src=\"images/flag_usa.png\" alt=\"미국국기\">";
			} else {
				$flag = "<img src='images/".$Row2["NationalFlagFile"]."' >";
			}
			*/
			$flag = "<img src='images/".$Row2["NationalFlagFile"]."' >";
		?>

			<h4 class="caption_tea_profile"><?=$flag?> <?=$Row2["TeacherPayTypeItemTitle2"]?> <span class="normal">강사</span></h4>
			<ul class="tea_profile_list" id="teacher_table<?=$flagCount?>">

			</ul>
		<?php
			$flagCount ++;
		}
		?>
<!--
		<h4 class="caption_tea_profile"><img src="images/flag_usa.png" alt="미국국기"> 미국 <span class="normal">강사</span></h4>
		<ul class="tea_profile_list usa">
			<li>
				<div class="profile_left"><img src="images/photo_teacher_ed.jpg" class="photo_profile" alt="ED"></div>
				<div class="profile_right">
					<table class="profile_table">
						<tr>
							<th>Name</th>
							<td><b class="bold_name">ED</b></td>
						</tr>
						<tr>
							<th>Education</th>
							<td>Ed Deluna is a graduate from New York University in Hospitality Management and Pedagogy. He had worked as a member of the management team of Affinia . He handled training and development of staff in addition to the requisite management duties . In line with his focus on training he had done extensive training of New York Public School Teachers in Pedagogy Instructional Skills. He also taught English and Mathematics in New York City public schools to Elementary Middle Scholl and High School Students .Currently he works as a Management Consultant and Trainer for PPM Informatics as an English Teacher for Mangoi teaching English to Korean students.</td>
						</tr>
						<tr>
							<th>Comment</th>
							<td>A journey of a thousand miles must begin with the first step.</td>
						</tr>
					</table>
				</div>
			</li>
		</ul>
-->
	</div>
</section>
</div>
<?} else if($DomainSiteID == 9){ // live.engedu.kr ?>
    <div class="sub_wrap">

        <div  class="sub_title_common_wrap"><h2 class="sub_title_common"><b>강사</b> 소개</h2></div>

        <!-- 강사 선발 -->
        <section class="tea_select_wrap">
            <div class="tea_select_area">
                <div class="tea_select_inner">
                    <h2 class="caption_sub left TrnTag">강사선발 <span class="normal">과정</span></h2>
                    <div class="tea_select_text TrnTag">5단계 채용과정과 5개 평가항목을 적용시켜 200점 만점 중 160점 이상의 우수한 강사 확보</div>
                    <h3 class="caption_tea_select TrnTag">5단계 채용과정</h3>
                    <img src="images/img_recruit_process.png" class="img_recruit_process" alt="5단계 채용과정">
                    <h3 class="caption_tea_select TrnTag">5개 평가항목서</h3>
                </div>
                <div class="tea_select_form">
                    <h4>Candidate Evaluation Form</h4>
                    <table class="tea_select_table">
                        <tr>
                            <th>Criteria</th>
                            <th>Check Lists</th>
                            <th class="cell_bg_blue">Full Socore</th>
                            <th>Interviewer</th>
                            <th style="width:11%;">etc</th>
                        </tr>
                        <tr>
                            <td rowspan="3">Appearance</td>
                            <td>Visual Apperance</td>
                            <td class="cell_bg_blue">10</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Attitude</td>
                            <td class="cell_bg_blue">10</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Voice Sound</td>
                            <td class="cell_bg_blue">10</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td rowspan="3">Character</td>
                            <td>Cooperation</td>
                            <td class="cell_bg_blue">10</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Diligence</td>
                            <td class="cell_bg_blue">10</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Health Check</td>
                            <td class="cell_bg_blue">10</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td rowspan="3">Teaching Quality</td>
                            <td>Pronunciation</td>
                            <td class="cell_bg_blue">30</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Sample Class Demo</td>
                            <td class="cell_bg_blue">30</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>College Graduation</td>
                            <td class="cell_bg_blue">10</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td rowspan="3">Experience</td>
                            <td>Teacher suitable for non-face-to-face (online) classes</td>
                            <td class="cell_bg_blue">20</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Teaching experience</td>
                            <td class="cell_bg_blue">20</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Commuting time</td>
                            <td class="cell_bg_blue">20</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Potential</td>
                            <td>Potential bearing by Traning</td>
                            <td class="cell_bg_blue">10</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <th colspan="2">Total Score</th>
                            <th class="cell_bg_blue">200</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </table>
                </div>
            </div>
        </section>

        <!-- 강사 프로필 -->
        <section class="tea_profile_wrap">
            <div id="teacherIntro" class="tea_profile_area">
                <h2 class="caption_sub">이엔지 화상영어 강사프로필</h2>
                <h4 class="caption_tea_profile"><span class="normal">강사 검색</span>
                    <input type="text" id="SearchName" name="SearchName" value="<?=$SearchName?>" class="md-input label-fixed"  style="height:30px;"/>
                    <button onClick="javascript:searchTeacher();" style="height:33px;width:80px;">검색</button>
                </h4>
                <?php
                $flagCount = 1;
                while($Row2 = $Stmt2->fetch()) {
                    $TeacherPayTypeItemTitle2 = $Row2["TeacherPayTypeItemTitle2"];
                    /*
                    if($TeacherPayTypeItemTitle2=="필리핀") {
                        $flag = "<img src=\"images/flag_phi.png\" alt=\"필리핀국기\">";
                    } else if($TeacherPayTypeItemTitle2=="미국/캐나다") {
                        $flag = "<img src=\"images/flag_usa.png\" alt=\"미국국기\">";
                    } else {
                        $flag = "<img src='images/".$Row2["NationalFlagFile"]."' >";
                    }
                    */


                    $flag = "<img src='images/".$Row2["NationalFlagFile"]."' >";
                    if($TeacherPayTypeItemTitle2=="필리핀") {
                    ?>


                    <h4 class="caption_tea_profile"><?=$flag?> <?=$Row2["TeacherPayTypeItemTitle2"]?> <span class="normal">강사</span></h4>
                    <ul class="tea_profile_list" id="teacher_table<?=$flagCount?>">

                    </ul>
                    <?php
                    }
                    $flagCount ++;
                }
                ?>
                <!--
                        <h4 class="caption_tea_profile"><img src="images/flag_usa.png" alt="미국국기"> 미국 <span class="normal">강사</span></h4>
                        <ul class="tea_profile_list usa">
                            <li>
                                <div class="profile_left"><img src="images/photo_teacher_ed.jpg" class="photo_profile" alt="ED"></div>
                                <div class="profile_right">
                                    <table class="profile_table">
                                        <tr>
                                            <th>Name</th>
                                            <td><b class="bold_name">ED</b></td>
                                        </tr>
                                        <tr>
                                            <th>Education</th>
                                            <td>Ed Deluna is a graduate from New York University in Hospitality Management and Pedagogy. He had worked as a member of the management team of Affinia . He handled training and development of staff in addition to the requisite management duties . In line with his focus on training he had done extensive training of New York Public School Teachers in Pedagogy Instructional Skills. He also taught English and Mathematics in New York City public schools to Elementary Middle Scholl and High School Students .Currently he works as a Management Consultant and Trainer for PPM Informatics as an English Teacher for Mangoi teaching English to Korean students.</td>
                                        </tr>
                                        <tr>
                                            <th>Comment</th>
                                            <td>A journey of a thousand miles must begin with the first step.</td>
                                        </tr>
                                    </table>
                                </div>
                            </li>
                        </ul>
                -->
            </div>
        </section>
    </div>
<?}else{?>
<div class="sub_wrap">

    <div  class="sub_title_common_wrap"><h2 class="sub_title_common"><b>강사</b> 소개</h2></div>

    <!-- 강사 선발 -->
    <section class="tea_select_wrap">
        <div class="tea_select_area">
            <div class="tea_select_inner">
                <h2 class="caption_sub left TrnTag">강사선발 <span class="normal">과정</span></h2>
                <div class="tea_select_text TrnTag">5단계 채용과정과 5개 평가항목을 적용시켜 200점 만점 중 160점 이상의 우수한 강사 확보</div>
                <h3 class="caption_tea_select TrnTag">5단계 채용과정</h3>
                <img src="images/img_recruit_process.png" class="img_recruit_process" alt="5단계 채용과정">
                <h3 class="caption_tea_select TrnTag">5개 평가항목서</h3>
            </div>
            <div class="tea_select_form">
                <h4>Candidate Evaluation Form</h4>
                <table class="tea_select_table">
                    <tr>
                        <th>Criteria</th>
                        <th>Check Lists</th>
                        <th class="cell_bg_blue">Full Socore</th>
                        <th>Interviewer</th>
                        <th style="width:11%;">etc</th>
                    </tr>
                    <tr>
                        <td rowspan="3">Appearance</td>
                        <td>Visual Apperance</td>
                        <td class="cell_bg_blue">10</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Attitude</td>
                        <td class="cell_bg_blue">10</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Voice Sound</td>
                        <td class="cell_bg_blue">10</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td rowspan="3">Character</td>
                        <td>Cooperation</td>
                        <td class="cell_bg_blue">10</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Diligence</td>
                        <td class="cell_bg_blue">10</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Health Check</td>
                        <td class="cell_bg_blue">10</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td rowspan="3">Teaching Quality</td>
                        <td>Pronunciation</td>
                        <td class="cell_bg_blue">30</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Sample Class Demo</td>
                        <td class="cell_bg_blue">30</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>College Graduation</td>
                        <td class="cell_bg_blue">10</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td rowspan="3">Experience</td>
                        <td>Teacher suitable for non-face-to-face (online) classes</td>
                        <td class="cell_bg_blue">20</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Teaching experience</td>
                        <td class="cell_bg_blue">20</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Commuting time</td>
                        <td class="cell_bg_blue">20</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Potential</td>
                        <td>Potential bearing by Traning</td>
                        <td class="cell_bg_blue">10</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th colspan="2">Total Score</th>
                        <th class="cell_bg_blue">200</th>
                        <th></th>
                        <th></th>
                    </tr>
                </table>
            </div>
        </div>
    </section>

    <!-- 강사 프로필 -->
    <section class="tea_profile_wrap">
        <div id="teacherIntro" class="tea_profile_area">
            <h2 class="caption_sub">망고아이 강사프로필</h2>
			<h4 class="caption_tea_profile"><span class="normal">강사 검색</span>
				<input type="text" id="SearchName" name="SearchName" value="<?=$SearchName?>" class="md-input label-fixed"  style="height:30px;"/>
				<button onClick="javascript:searchTeacher();" style="height:33px;width:80px;">검색</button>
			</h4>
			<?php
			$flagCount = 1;
			while($Row2 = $Stmt2->fetch()) {
				$TeacherPayTypeItemTitle2 = $Row2["TeacherPayTypeItemTitle2"];
				/*
				if($TeacherPayTypeItemTitle2=="필리핀") {
					$flag = "<img src=\"images/flag_phi.png\" alt=\"필리핀국기\">";
				} else if($TeacherPayTypeItemTitle2=="미국/캐나다") {
					$flag = "<img src=\"images/flag_usa.png\" alt=\"미국국기\">";
				} else {
					$flag = "<img src='images/".$Row2["NationalFlagFile"]."' >";	
				}
				*/
				$flag = "<img src='images/".$Row2["NationalFlagFile"]."' >";
			?>
			
				<h4 class="caption_tea_profile"><?=$flag?> <?=$Row2["TeacherPayTypeItemTitle2"]?> <span class="normal">강사</span></h4>
				<ul class="tea_profile_list" id="teacher_table<?=$flagCount?>">
					
				</ul>
			<?php 
				$flagCount ++;
			} 
			?>
<!--
            <h4 class="caption_tea_profile"><img src="images/flag_usa.png" alt="미국국기"> 미국 <span class="normal">강사</span></h4>
            <ul class="tea_profile_list usa">
                <li>
                    <div class="profile_left"><img src="images/photo_teacher_ed.jpg" class="photo_profile" alt="ED"></div>
                    <div class="profile_right">
                        <table class="profile_table">
                            <tr>
                                <th>Name</th>
                                <td><b class="bold_name">ED</b></td>
                            </tr>
                            <tr>
                                <th>Education</th>
                                <td>Ed Deluna is a graduate from New York University in Hospitality Management and Pedagogy. He had worked as a member of the management team of Affinia . He handled training and development of staff in addition to the requisite management duties . In line with his focus on training he had done extensive training of New York Public School Teachers in Pedagogy Instructional Skills. He also taught English and Mathematics in New York City public schools to Elementary Middle Scholl and High School Students .Currently he works as a Management Consultant and Trainer for PPM Informatics as an English Teacher for Mangoi teaching English to Korean students.</td>
                            </tr>
                            <tr>
                                <th>Comment</th>
                                <td>A journey of a thousand miles must begin with the first step.</td>
                            </tr>
                        </table>
                    </div>
                </li>
            </ul>
-->
        </div>
    </section>
</div>
<?}?>


<?php
echo "\n";
echo $SubLayoutBottom;
echo "\n";
echo $MainLayoutBottom;
echo "\n";

//include_once('./includes/common_analytics.php');
//include_once('./includes/common_footer.php');

if (trim($SubLayoutJavascript)!=""){
	echo "\n";
	echo "<script>";
	echo "\n";
	echo $SubLayoutJavascript;
	echo "\n";
	echo "</script>";
	echo "\n";
}

if (trim($MainLayoutJavascript)!=""){
	echo "\n";
	echo "<script>";
	echo "\n";
	echo $MainLayoutJavascript;
	echo "\n";
	echo "</script>";
	echo "\n";
}
?>



<script language="javascript">
$('.toggle_navi.one .three').addClass('active');
$('.sub_visual_navi .three').addClass('active');


function OpenTeacherVideo(TeacherID, TeacherVideoType, TeacherVideoCode) {
	if (TeacherVideoCode==""){
		alert('소개영상 준비중 입니다.');
	}else{
	
		var OpenUrl = "pop_video_player.php?TeacherID="+TeacherID+"&TeacherVideoType="+TeacherVideoType+"&TeacherVideoCode="+TeacherVideoCode;

		$.colorbox({	
			href:OpenUrl
			,width:"95%" 
			,height:"95%"
			,maxWidth: "850"
			,maxHeight: "536"
			,title:""
			,iframe:true 
			,scrolling:false
			//,onClosed:function(){location.reload(true);}
			//,onComplete:function(){alert(1);}
		}); 

	}
}

function OpenReview(TeacherID) {

	var OpenUrl = "review_list_view.php?TeacherID="+TeacherID;
	$.colorbox({	
		href:OpenUrl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "850"
		,maxHeight: "536"
		,title:""
		,iframe:true 
		,scrolling:true

		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 
}

function OpenReviewWrite(TeacherID) {

var OpenUrl = "review_write_form.php?TeacherID="+TeacherID;
$.colorbox({	
	href:OpenUrl
	,width:"95%" 
	,height:"95%"
	,maxWidth: "850"
	,maxHeight: "536"
	,title:""
	,iframe:true 
	,scrolling:false
	//,onClosed:function(){location.reload(true);}
	//,onComplete:function(){alert(1);}
}); 
}


/*
function SearchSubmit(){
	document.SearchForm.action = "teacher_intro.php";
	document.SearchForm.submit();
}
*/


var teacherArray = [];  //자바스크립트 선생님 오브젝트 배열



<?php

	// 먼저 필리핀/미국 등 지역종류로 선생님 불러오기 
	$Stmt2 = $DbConn->prepare($Sql2);
	$Stmt2->execute();
	$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
	while($Row2 = $Stmt2->fetch()){

		$TeacherPayTypeItemID =$Row2["TeacherPayTypeItemID"];
		$TeacherPayTypeItemOrder =$Row2["TeacherPayTypeItemOrder"];

		// 선생님들의 데이터를 읽어와서 자바스크립트 객체 배열로 생성
		$Sql3 = "SELECT *, 
					(SELECT AVG(rating) AS overall_rating FROM reviews WHERE teacherID = A.TeacherID) as overall_rating, 
					(SELECT COUNT(*) AS total_reviews FROM reviews WHERE teacherID = A.TeacherID) as total_reviews,
					(SELECT COUNT(*) FROM Classes C WHERE A.TeacherID = C.TeacherID AND C.MemberID = :MemberID AND C.ClassState = 2) as class_count
					from Teachers A 
					inner join TeacherPayTypeItems B 
					on A.TeacherPayTypeItemID = B.TeacherPayTypeItemID 
					where A.TeacherState=1 and A.TeacherPayTypeItemID=:TeacherPayTypeItemID   and A.TeacherGroupID <> 5
					ORDER BY RAND()";
		$Stmt3 = $DbConn->prepare($Sql3);
		$Stmt3->bindParam(':TeacherPayTypeItemID', $TeacherPayTypeItemID);
		$Stmt3->bindParam(':MemberID', $MemberID);
		$Stmt3->execute();
		$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
		

		while($Row3 = $Stmt3->fetch()) {
			if ($Row3['TeacherImageFileName']==""){
				$StrTeacherImageFileName = "images/no_photo_2.png";
			}else{
				$StrTeacherImageFileName = "uploads/teacher_images/".$Row3['TeacherImageFileName'];
			}
			$TeacherID = $Row3["TeacherID"];
			$TeacherVideoType = $Row3["TeacherVideoType"];
			$TeacherVideoCode = $Row3["TeacherVideoCode"];
			$TeacherName = $Row3["TeacherName"];
			$TeacherIntroEdu = $Row3["TeacherIntroEdu"];
			$TeacherIntroSpec = $Row3["TeacherIntroSpec"];
			$TeacherIntroText = nl2br($Row3["TeacherIntroText"]);
			$Overall_rating = $Row3["overall_rating"];
			$Total_reviews = $Row3["total_reviews"];
			$Class_count = $Row3["class_count"];

			if ($Class_count < 4){
				$reviewWriteMsg = "alert('강사와 4회이상의 수업을 진행해야만 리뷰를 쓸 수 있습니다!')";
			} else {
				$reviewWriteMsg = "OpenReviewWrite($TeacherID);";
			}
	?>





	var viewString = `<li>
					<div class='profile_left'><img src='<?=$StrTeacherImageFileName?>' class='photo_profile' ></div>
					<div class='profile_right'>
						<a href="javascript:OpenTeacherVideo(<?=$TeacherID?>, <?=$TeacherVideoType?>, '<?=$TeacherVideoCode?>');" class='btn_tea_profile'>인사영상 <img src='images/arrow_big_right.png'></a>
						<a href="javascript:OpenReview(<?=$TeacherID?>);" class='btn_review'>리뷰보기 <img src='images/arrow_big_right.png'></a>
						<a href="javascript:<?=$reviewWriteMsg?>" class='btn_review_write'>리뷰작성 <img src='images/arrow_big_right.png'></a>
						
						<table class='profile_table'>
							<tr>
								<th>Name</th>
								<td><b class='bold_name'><?=$TeacherName?></b></td>
							</tr>
							<tr>
								<th>Review</th>
								<td><b class='bold_name'>
								<div class='reviews'>
									<div class='overall_rating'>
										<span class='num' style='font-size:14px'><?=number_format($Overall_rating, 1)?></span>
										<span class='stars' style='font-size:14px'><?=str_repeat('&#9733;', round($Overall_rating))?></span>
										<span class='total' style='font-size:14px'><?=$Total_reviews?>  reviews</span>
									</div>
								</div>	
								</b></td>
							</tr>
							<tr>
								<th>Education</th>
								<td><?=$TeacherIntroSpec?></td>
							</tr>
							<tr>
								<th>Comment</th>
								<td><?=$TeacherIntroText?></td>
							</tr>
						</table>
					</div>
				</li>`;


	//자바스크립 선생님 오브젝트
	var teacher = {
		TeacherPayTypeItemID: <?=$TeacherPayTypeItemID?>,
		TeacherPayTypeItemOrder: <?=$TeacherPayTypeItemOrder?>,
		TeacherID: <?=$TeacherID?>,
		TeacherName: '<?=$TeacherName?>',
		viewString: viewString,
		isView: true
	}

	teacherArray.push(teacher);

	<?php	}	


	}  
?>	

function showTeacher(){
	//먼저 기존에 있던 li들을 제거한다.
<?php

	for ($i=1;$i <$flagCount;$i++){
	
		echo "
			var tea_profile_list".$i." = $('#teacher_table".$i."'); 
			tea_profile_list".$i.".children().remove();
		";
	}

?>

	// 선생님 배열을 돌면서 li에 추가한다.
	teacherArray.forEach((teacher, index) => {
		if (teacher.isView){
			eval("tea_profile_list"+teacher.TeacherPayTypeItemOrder).append(teacher.viewString); //안쪽에 li추가	
		}
	});
	
}


function searchTeacher(){
	var SearchName = $("#SearchName").val();
	
	//만약 검색창이 비어 있으면 모든 선생님을 보여준다. 
	if (SearchName == ""){
		teacherArray.forEach((teacher, index) => {
			teacher.isView = true;
		});	
	} else {
		// 검색창에 있는 문자열을 포함한 선생님만 보여준다.
		teacherArray.forEach((teacher, index) => {
			if (teacher.TeacherName.toLowerCase().indexOf(SearchName.toLowerCase()) == -1) {
				teacher.isView = false;	
			} else {
				teacher.isView = true;
			}
			
		});	
	}


	showTeacher(); //실제 선생님 리스트를 추가한다.
}

// 자동완성 기능 : 배열을 선언하여 사용하는 방식
var autoTeacher = [];
teacherArray.forEach((tea,index) => {

	autoTeacher.push(tea.TeacherName);
})
   

$('#SearchName').autocomplete({
			source: autoTeacher,
			focus: function (event, ui) {
				return false;
			},
			select: function (event, ui) {},
			minLength: 1,
			delay: 100,
			autoFocus: true,
});


showTeacher();






</script>

<?php 
if ($teacherIntro==1){
?>
<script>
	
	document.getElementById('teacherIntro').scrollIntoView();

</script>
<? } ?>


</body>
</html>
<?php
include_once('./includes/dbclose.php');
?>





