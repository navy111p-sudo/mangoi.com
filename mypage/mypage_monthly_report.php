<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
$DenyGuest = true;
include_once('../includes/member_check.php');

$MemberLevelID = $_LINK_MEMBER_LEVEL_ID_;

if($MemberLevelID==12 || $MemberLevelID==13) {
	header("Location: mypage_teacher_mode.php");
}		

$UseMain = 0;
$UseSub = 1;
$SubCode = "sub_englishtell";
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="google-signin-client_id" content="950462494416-92ppoda203fvs2ghu0qjr2q592epuqsk.apps.googleusercontent.com">
<?if ($DomainSiteID==5){?>
<title>(주)englishtell</title>
<?}else{?>
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="../uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="../uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
<?}?>
<link href="css/sub_style.css" rel="stylesheet" type="text/css" />
<?php
include_once('../includes/common_header.php');

$Sql = "select SubID from Subs where SubCode=:SubCode";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':SubCode', $SubCode);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$SubID = $Row["SubID"];


if ($UseMain==1){
	$Sql = "select * from Main limit 0,1";
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
	$Sql = "select * from Subs where SubID=:SubID";
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
include_once('../includes/common_body_top.php');
?>
<?php
$MainLayoutTop = convertHTML(trim($MainLayoutTop));
$SubLayoutTop = convertHTML(trim($SubLayoutTop));
$SubLayoutBottom = convertHTML(trim($SubLayoutBottom));
$MainLayoutBottom = convertHTML(trim($MainLayoutBottom));

echo "\n";
echo $MainLayoutTop;
echo "\n";
echo $SubLayoutTop;
echo "\n";
?>


<div class="sub_wrap">       
    <div class="sub_title_common_wrap"><h2 class="sub_title_common"><b>평가</b>보고서</h2></div>

    <section class="mypage_wrap">
        <div class="mypage_area">


            <ul class="mypage_report_tab">
                <li><a href="mypage_monthly_report.php" class="active TrnTag">정기 평가서</a></li>
                <li><a href="mypage_leveltest_report.php" class="TrnTag">레벨 테스트</a></li>
            </ul>

            <!-- 나의 학습 이력 -->
            <div class="mypage_inner">


				<?
				$MemberID = $_LINK_MEMBER_ID_;

				$Sql = "
						select 
								count(*) as TotalCount
						from AssmtStudentMonthlyScores A 

						where A.ClassID in (select ClassID from Classes where MemberID=$MemberID)  
						
						
						";
				$Stmt = $DbConn->prepare($Sql);
				$Stmt->execute();
				$Stmt->setFetchMode(PDO::FETCH_ASSOC);
				$Row = $Stmt->fetch();
				$Stmt = null;
				$TotalCount = $Row["TotalCount"];


				?>

                <h3 class="caption_left_br">정기 <b>평가서</b><span>Total : <b><?=$TotalCount?></b></span></h3>
                <div class="overflow_table">
                    <table class="mypage_report_table">
                        <col width="30%">
                        <col width="40%">
                        <col width="30%">
                        <tr>
                            <th class="TrnTag">번호</th>
                            <th class="TrnTag">평가서</th>
                            <th class="TrnTag">보기</th>
                        </tr>
                        
						<?
						if ($TotalCount==0){
						?>
						<tr>
                            <td>-</td>
                            <td>2019년 09월 평가서(샘플)</td>
                            <td><a href="javascript:OpenStudentScoreMonthlyReportSample();"><img src="images/btn_report.png" class="mypage_report_btn "></a></td>
                        </tr>
						<?
						}else{

							$Sql = "select 
										A.*
									from AssmtStudentMonthlyScores A 
									where A.ClassID in (select ClassID from Classes where MemberID=$MemberID)  
									order by A.AssmtStudentMonthlyScoreRegDateTime desc";

							$Stmt = $DbConn->prepare($Sql);
							$Stmt->execute();
							$Stmt->setFetchMode(PDO::FETCH_ASSOC);


							$ii=1;
							while($Row = $Stmt->fetch()) {
								
								$AssmtStudentMonthlyScoreID = $Row["AssmtStudentMonthlyScoreID"];
								$AssmtStudentMonthlyScoreSubject = $Row["AssmtStudentMonthlyScoreSubject"];
								$AssmtStudentMonthlyScoreYear = $Row["AssmtStudentMonthlyScoreYear"];
								$AssmtStudentMonthlyScoreMonth = $Row["AssmtStudentMonthlyScoreMonth"];
						?>
							<tr>
								<td><?=$TotalCount-$ii+1?></td>
								<td><?=$AssmtStudentMonthlyScoreSubject?>(<?=$AssmtStudentMonthlyScoreYear?>. <?=substr("0".$AssmtStudentMonthlyScoreMonth,-2)?>)</td>
								<td><a href="javascript:OpenStudentScoreMonthlyReport(<?=$AssmtStudentMonthlyScoreID?>);"><img src="images/btn_report.png" class="mypage_report_btn "></a></td>
							</tr>
						<?
								$ii++;	
							}
							$Stmt = null;
						}
						?>
						
                    </table>

					<!--
                    <div class="bbs_page">
                        <span class="arrow_left_2"><img src="images/arrow_bbs_left_2.png"></span>
                        <span class="arrow_left_1"><img src="images/arrow_bbs_left_1.png"></span>
                        <span class="active">1</span>
                        <span class="arrow_right_1"><img src="images/arrow_bbs_right_1.png"></span>
                        <span class="arrow_right_2"><img src="images/arrow_bbs_right_2.png"></span>
                    </div>
					-->

                </div>
            </div>

        </div>
    </section>

</div>

<!-- 평가보고서 -->
<div class="light_box_wrap">
    <div class="light_box_area">
        <a href="#"><img src="images/btn_close_white.png" alt="닫기" class="light_box_close"></a>
        <div class="light_box_box">            
            <iframe src="report_sample.html" class="light_box_iframe"></iframe>
        </div>
    </div>
</div>
<!-- 평가보고서 -->






<script language="javascript">
$(document).ready(function () {
    $('.navi_lnb .two').addClass('active');
});

function OpenStudentScoreMonthlyReport(AssmtStudentMonthlyScoreID){

	var OpenUrl = "../report_monthly.php?AssmtStudentMonthlyScoreID="+AssmtStudentMonthlyScoreID;

	$.colorbox({	
		href:OpenUrl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "1200"
		,maxHeight: "700"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 


}


function OpenStudentScoreMonthlyReportSample(){

	var OpenUrl = "../report_sample_monthly.php";

	$.colorbox({	
		href:OpenUrl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "1200"
		,maxHeight: "700"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	}); 

}

</script>


<?php
echo "\n";
echo $SubLayoutBottom;
echo "\n";
echo $MainLayoutBottom;
echo "\n";
?>

<?
include_once('../includes/common_analytics.php');
?>


<?php
include_once('../includes/common_footer.php');

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

</body>
</html>
<?php
include_once('../includes/dbclose.php');
?>





