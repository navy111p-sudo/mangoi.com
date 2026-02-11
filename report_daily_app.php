<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
include_once('./includes/member_check.php');

$ClassID = isset($_REQUEST["ClassID"]) ? $_REQUEST["ClassID"] : "";
$Sql = "select 
                A.*,
				B.BookSystemType,
				B.BookScanID,
				E.BookScanName,
				B.BookWebookUnitID,
				B.BookWebookUnitName,
				E.BookScanImageFileName,
				D.MemberID,
				D.MemberLoginID,
				D.MemberName
        from AssmtStudentDailyScores A 
			inner join Classes B on A.ClassID=B.ClassID 
			inner join ClassOrders C on B.ClassOrderID=C.ClassOrderID 
			inner join Members D on B.MemberID=D.MemberID 
			left outer join BookScans E on B.BookScanID=E.BookScanID 
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

$BookSystemType = $Row["BookSystemType"];
$BookScanID = $Row["BookScanID"];
$BookScanName = $Row["BookScanName"];
$BookWebookUnitID = $Row["BookWebookUnitID"];
$BookWebookUnitName = $Row["BookWebookUnitName"];
$BookScanImageFileName = $Row["BookScanImageFileName"];



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


$LinkScan = "-";
if ($BookSystemType==0){//망고교재
	if ($BookScanID==0){
		$LinkScan = "-";
	}else{
		$LinkScan = "<a href=\"javascript:OpenBookScan('".$BookScanImageFileName."',".$ClassID.")\" style=\"color:#000000;\">".$BookScanName."</a>";
	}
}else{//JT교재
	if ($BookWebookUnitID==""){
		$LinkScan = "-";
	}else{
		$LinkScan = "<a href=\"javascript:OpenWebook('".$BookWebookUnitID."',".$ClassID.")\" style=\"color:#000000;\">".$BookWebookUnitName."</a>";
	}
}
?>
<html>
<head>
    <meta charset="utf-8">
    <title>주저없는 선택 망고아이</title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" id="viewport" />
    <link href="css/common.css" rel="stylesheet" type="text/css" />
    <link href="css/sub_style.css" rel="stylesheet" type="text/css" />
    <title></title>
	<script src="js/jquery-2.2.4.min.js"></script>
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
            <trn class="TrnTag">평가보고서</trn>
        </h1>
        <div class="report_studen_name">Student : <b><?=$MemberLoginID?> (<?=$MemberName?>)</b></div>


        
        <div class="report_top">

            <div class="report_top_right" style="width:100%;">


                <h3 class="report_caption_left TrnTag">점수</h3>
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


                <h3 class="report_caption_left TrnTag">학습현황</h3>
                <table class="report_table">
                    <col width="50%">
                    <col width="25%">
					<col width="25%">
                    <tr>
						<th class="bg_pink_1 TrnTag">학습교재</th>
                        <th class="bg_pink_1 TrnTag">레슨비디오</th>
                        <th class="bg_pink_1 TrnTag">리뷰퀴즈</th>
                    </tr>
                    <tr>
						<td class="bg_pink_2"><b><?=$LinkScan?></b></td>
                        <td class="bg_pink_2"><b><?=$VideoCount?> <trn class="TrnTag">회 시청</trn></b></td>
                        <td class="bg_pink_2"><b><?=$QuizCount?> <trn class="TrnTag">회 응시</trn></b></td>
                    </tr>
                </table>

            </div>


            <div class="report_top_left" style="width:100%;">


                <h3 class="report_caption_left TrnTag">리뷰퀴즈결과</h3>
                <table class="report_table">
                    <col width="30%">
                    <col width="70%">
                    <tr>
                        <th class="bg_yellow_1 TrnTag">회차</th>
                        <th class="bg_yellow_1 TrnTag">점수</th>
                    </tr>
					<?
					$Sql2 = "select 
									A.*,
									ifnull((select avg(MyScore) from BookQuizResultDetails where BookQuizResultID=A.BookQuizResultID),0) as AvgMyScore
							from BookQuizResults A 
							where 
								A.ClassID=$ClassID 
								and A.BookQuizResultState=2 
							order by A.QuizStudyNumber asc";

					$Stmt2 = $DbConn->prepare($Sql2);
					$Stmt2->execute();
					$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

					$ii2=1;
					$QuizStudyNumber=0;
					$BookQuizResultState = 2;
					while($Row2 = $Stmt2->fetch()) {
						$BookQuizResultID = $Row2["BookQuizResultID"];
						$QuizStudyNumber = $Row2["QuizStudyNumber"];
						$BookQuizResultState = $Row2["BookQuizResultState"];
						$AvgMyScore = round($Row2["AvgMyScore"],0);

					?>
                    <tr>
                        <td class="bg_yellow_2"><b><?=$QuizStudyNumber?> <trn class="TrnTag">회</trn></b></td>
                        <td class="bg_yellow_2"><b><?=$AvgMyScore?> <trn class="TrnTag">점</trn></b></td>
                    </tr>
					<?
						$ii2++;
					}
					$Stmt2 = null;

					if ($ii2==1){
					?>
                    <tr>
                        <td class="bg_yellow_2 TrnTag" colspan="2"><b>리뷰퀴즈에 응시하지 않았습니다.</b></td>
                    </tr>
					<?
					}
					?>
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
<script>
function OpenBookScan(BookScanImageFileName,ClassID) {

	var iframe = "<html><head><style>body, html {width: 100%; height: 100%; margin: 0; padding: 0}</style></head><body><iframe src='../ViewerJS/?zoom=page-width#/uploads/book_pdf_uploads/"+BookScanImageFileName+"' frameborder='0' style='height:calc(100% - 4px);width:calc(100% - 4px)'></iframe><input type='hidden' id='filename' value='"+BookScanImageFileName+"'></div></body></html>";

	var win = window.open("","coursebookcontent","frameborder=0,width="+screen.width+",height="+screen.height+",top=0,left=0,directories=no,location=no,channelmode=yes,fullscreen=yes,menubar=no, resizable=no,status=no,toolbar=no,history=no,scrollbars=yes");

	win.document.write(iframe);

}


function OpenWebook(Unit, ClassID) {

	var StrContentType = "일반교재";

	$.post( "./webook/_get_unit_content.php", { content_type:"학생", MemberLoginID: "<?=$_LINK_MEMBER_LOGIN_ID_?>", unit_id:Unit, api_extension:'', width:"100%", height: "100%", unit_contents_type:StrContentType })
	.done(function( data ) {

		var iframe = data;

		var win = window.open("","jtwebook","frameborder=0,width="+screen.width+",height="+screen.height+",top=0,left=0,directories=no,location=no,channelmode=yes,fullscreen=yes,menubar=no, resizable=no,status=no,toolbar=no,history=no,scrollbars=yes");

		win.document.write(iframe);
	});
}

function PrintPage(){
	print();
}
</script>

</body>
</html>
<?php
include_once('./includes/dbclose.php');
?>