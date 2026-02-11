<?php
include_once('./includes/dbopen.php');
include_once('./includes/common.php');
$DenyGuest = true;
include_once('./includes/member_check.php');

$UseMain = 0;
$UseSub = 1;
$SubCode = "sub_07";
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
<link href="css/sub_style.css" rel="stylesheet" type="text/css" />
<?php
include_once('./includes/common_header.php');

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
include_once('./includes/common_body_top.php');
?>
<?php
// $MainLayoutTop = convertHTML(trim($MainLayoutTop));
// $SubLayoutTop = convertHTML(trim($SubLayoutTop));
// $SubLayoutBottom = convertHTML(trim($SubLayoutBottom));
// $MainLayoutBottom = convertHTML(trim($MainLayoutBottom));

if($DomainSiteID==7){
    $MainLayoutTop = convertHTML(trim("{{Piece(header_gumiivyleague)}}"));
    $SubLayoutTop = convertHTML(trim("{{Piece(sub_07_gumiivyleague)}}"));
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
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_engedu)}}"));


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
?>


<div class="sub_wrap">       
    <div class="sub_title_common_wrap"><h2 class="sub_title_common TrnTag"><b>평가</b>보고서</h2></div>

    <section class="mypage_wrap">
        <div class="mypage_area">

            <ul class="mypage_report_tab">
                <li><a href="mypage_monthly_report.php" class="TrnTag">정기 평가서</a></li>
                <li><a href="mypage_leveltest_report.php" class="active TrnTag">레벨 테스트</a></li>
            </ul>

            <div class="mypage_inner">

				<?
				$MemberID = $_LINK_MEMBER_ID_;

				$Sql = "
						select 
								count(*) as TotalCount
						from AssmtStudentLeveltestScores A 

						where A.ClassID in (select ClassID from Classes where MemberID=$MemberID)  
						
						
						";
				$Stmt = $DbConn->prepare($Sql);
				$Stmt->execute();
				$Stmt->setFetchMode(PDO::FETCH_ASSOC);
				$Row = $Stmt->fetch();
				$Stmt = null;
				$TotalCount = $Row["TotalCount"];


				?>

                <h3 class="caption_left_br TrnTag"><b>레벨</b> 테스트<span>Total : <b><?=$TotalCount?></b></span></h3>
                <div class="overflow_table">
                    <table class="mypage_report_table">
                        <col width="18%">
                        <col width="">
                        <col width="26%">
                        <col width="26%">
                        <tr>
                            <th class="TrnTag">번호</th>
                            <th class="TrnTag">레벨테스트</th>
                            <th class="TrnTag">평가일</th>
                            <th class="TrnTag">평가서보기</th>
                        </tr>
                        
						<?
						if ($TotalCount==0){
						?>
						<tr>
                            <td>-</td>
                            <td class="TrnTag">망고아이 레벨테스트(샘플)</td>
                            <td>2019.09.28</td>
                            <td><a href="javascript:OpenStudentLeveltestReportSample()"><img src="images/btn_report.png" class="mypage_report_btn"></a></td>
                        </tr>
						<?
						}else{

							$Sql = "select 
										A.*
									from AssmtStudentLeveltestScores A 
									where A.ClassID in (select ClassID from Classes where MemberID=$MemberID)  
									order by A.AssmtStudentLeveltestScoreRegDateTime desc";

							$Stmt = $DbConn->prepare($Sql);
							$Stmt->execute();
							$Stmt->setFetchMode(PDO::FETCH_ASSOC);

							$ii=1;
							while($Row = $Stmt->fetch()) {
								$AssmtStudentLeveltestScoreID = $Row["AssmtStudentLeveltestScoreID"];
								$AssmtStudentLeveltestScoreYear = $Row["AssmtStudentLeveltestScoreYear"];
								$AssmtStudentLeveltestScoreMonth = $Row["AssmtStudentLeveltestScoreMonth"];
								$AssmtStudentLeveltestScoreDay = $Row["AssmtStudentLeveltestScoreDay"];
								$AssmtStudentLeveltestScoreLevel = $Row["AssmtStudentLeveltestScoreLevel"];
						?>
                        <tr>
                            <td><?=$TotalCount-$ii+1?></td>
                            <td><trn class="TrnTag">레벨테스트</trn> (LEVEL <?=$AssmtStudentLeveltestScoreLevel?>)</td>
                            <td><?=$AssmtStudentLeveltestScoreYear?>. <?=substr("0".$AssmtStudentLeveltestScoreMonth,-2)?>. <?=substr("0".$AssmtStudentLeveltestScoreDay,-2)?></td>
                            <td><a href="javascript:OpenStudentScoreLeveltestReport(<?=$AssmtStudentLeveltestScoreID?>);"><img src="images/btn_report.png" class="mypage_report_btn"></a></td>
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









<script language="javascript">
$('.sub_visual_navi .three').addClass('active');

function OpenStudentScoreLeveltestReport(AssmtStudentLeveltestScoreID){
	var OpenUrl = "./report_leveltest.php?AssmtStudentLeveltestScoreID="+AssmtStudentLeveltestScoreID;

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


function OpenStudentLeveltestReportSample(ReportID){

	var OpenUrl = "./report_sample_leveltest.php";

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


</script>


<?php
echo "\n";
echo $SubLayoutBottom;
echo "\n";
echo $MainLayoutBottom;
echo "\n";
?>

<?
include_once('./includes/common_analytics.php');
?>


<?php
include_once('./includes/common_footer.php');

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
include_once('./includes/dbclose.php');
?>





