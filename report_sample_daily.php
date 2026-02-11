<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
include_once('./includes/member_check.php');

$ClassID = 1426;

$Sql = "select 
                A.*,
				D.MemberID,
				D.MemberLoginID,
				D.MemberName
        from AssmtStudentDailyScores A 
			inner join Classes B on A.ClassID=B.ClassID 
			inner join ClassOrders C on B.ClassOrderID=C.ClassOrderID 
			inner join Members D on B.MemberID=D.MemberID 
		where A.ClassID=$ClassID ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$AssmtStudentDailyScore1 = $Row["AssmtStudentDailyScore1"];
$AssmtStudentDailyScore2 = $Row["AssmtStudentDailyScore2"];
$AssmtStudentDailyScore3 = $Row["AssmtStudentDailyScore3"];
$AssmtStudentDailyScore4 = $Row["AssmtStudentDailyScore4"];
$AssmtStudentDailyScore5 = $Row["AssmtStudentDailyScore5"];
$AssmtStudentDailyComment = $Row["AssmtStudentDailyComment"];

$MemberLoginID = $Row["MemberLoginID"];
$MemberName = $Row["MemberName"];



$Sql = "
	select 
		count(*) as VideoCount
	from ClassVideoPlayLogs A
	where A.ClassID=$ClassID  
";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$VideoCount = round($Row["VideoCount"],0);

$Sql = "
	select 
		count(*) as QuizCount
	from BookQuizResults A 
		
	where A.ClassID=$ClassID and A.BookQuizResultState=2 
";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;
$QuizCount = round($Row["QuizCount"],0);
?>
<html>
<head>
    <meta charset="utf-8">
    <title>주저없는 선택 망고아이</title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" id="viewport" />
    <link href="css/common.css" rel="stylesheet" type="text/css" />
    <link href="css/sub_style.css" rel="stylesheet" type="text/css" />
    <title></title>

<!-- Styles -->
<style>
#chartdiv {
  width: 100%;
  height: 500px;
}

</style>

<!-- Resources -->
<script src="https://www.amcharts.com/lib/4/core.js"></script>
<script src="https://www.amcharts.com/lib/4/charts.js"></script>
<script src="https://www.amcharts.com/lib/4/themes/kelly.js"></script>
<script src="https://www.amcharts.com/lib/4/themes/animated.js"></script>
</head>
<body>
<?
include_once('./includes/common_body_top.php');
?>
    <div class="report_wrap">
        <h1 class="report_month_caption">
            <a href="javascript:PrintPage()" class="report_print_btn"><img src="images/icon_print_black.png" class="icon">Print</a>
            평가보고서
        </h1>
        <div class="report_studen_name">Student : <b>yerim (김예림)</b></div>


        
        <div class="report_top">

            <div class="report_top_right" style="width:100%;">


                <h3 class="report_caption_left">점수</h3>
                <table class="report_table">
                    <col width="20%">
                    <col width="20%">
                    <col width="20%">
                    <col width="20%">
					<col width="20%">
                    <tr>
                        <th class="bg_green_1">Pronunciation</th>
                        <th class="bg_green_1">Grammar</th>
                        <th class="bg_green_1">Vocabulary</th>
						<th class="bg_green_1">Attitude</th>
						<th class="bg_green_1">Fluency</th>
                    </tr>
                    <tr>
                        <td class="bg_green_2"><b><?=$AssmtStudentDailyScore1?></b></td>
                        <td class="bg_green_2"><b><?=$AssmtStudentDailyScore2?></b></td>
                        <td class="bg_green_2"><b><?=$AssmtStudentDailyScore3?></b></td>
						<td class="bg_green_2"><b><?=$AssmtStudentDailyScore4?></b></td>
						<td class="bg_green_2"><b><?=$AssmtStudentDailyScore5?></b></td>
                    </tr>
                </table>
            </div>

            <div class="report_top_left" style="width:100%;">


                <h3 class="report_caption_left">학습현황</h3>
                <table class="report_table">
                    <col width="50%">
                    <col width="50%">
                    <tr>
                        <th class="bg_pink_1">레슨비디오</th>
                        <th class="bg_pink_1">리뷰퀴즈</th>
                    </tr>
                    <tr>
                        <td class="bg_pink_2"><b>6 회 시청</b></td>
                        <td class="bg_pink_2"><b>4 회 응시</b></td>
                    </tr>
                </table>

            </div>


            <div class="report_top_left" style="width:100%;">


                <h3 class="report_caption_left">리뷰퀴즈결과</h3>
                <table class="report_table">
                    <col width="30%">
                    <col width="70%">
                    <tr>
                        <th class="bg_yellow_1">회차</th>
                        <th class="bg_yellow_1">점수</th>
                    </tr>

                    <tr>
                        <td class="bg_yellow_2"><b>1 회</b></td>
                        <td class="bg_yellow_2"><b>68 점</b></td>
                    </tr>
					<tr>
                        <td class="bg_yellow_2"><b>2 회</b></td>
                        <td class="bg_yellow_2"><b>75 점</b></td>
                    </tr>
					<tr>
                        <td class="bg_yellow_2"><b>3 회</b></td>
                        <td class="bg_yellow_2"><b>96 점</b></td>
                    </tr>
				</table>

            </div>

			<div class="report_top_right" style="width:100%;">


                <h3 class="report_caption_left">Comment</h3>
                <table class="report_table" style="min-height:200px;">
                    <tr>
                        <td  class="bg_yellow_3" style="padding:20px;text-align:left;line-height:1.5;" valign="top"><?=str_replace("\n","<br>",$AssmtStudentDailyComment)?></td>
                    </tr>
                </table>
            </div>
        </div>
		


    </div>



<script>
function PrintPage(){
	print();
}
</script>

</body>
</html>
<?php
include_once('./includes/dbclose.php');
?>