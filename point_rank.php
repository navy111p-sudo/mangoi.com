<?php
include_once('./includes/dbopen.php');
include_once('./includes/common.php');
include_once('./includes/member_check.php');

$UseMain = 0;
$UseSub = 1;
$SubCode = "sub_06";
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
    $SubLayoutTop = convertHTML(trim("{{Piece(sub_06_gumiivyleague)}}"));
    $SubLayoutBottom = convertHTML(trim("{{Piece(sub_01_gumiivyleague_bottom)}}"));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_gumiivyleague)}}"));

} else if($DomainSiteID==8){ //engliseed.kr
    $MainLayoutTop = convertHTML(trim("{{Piece(header_engliseed)}}"));
    $SubLayoutTop = convertHTML(trim("{{Piece(sub_06_engliseed)}}"));
    $SubLayoutBottom = convertHTML(trim("{{Piece(sub_01_engliseed_bottom)}}"));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_engliseed)}}"));

    $MainLayoutTop = str_replace("망고아이", "잉글리씨드", $MainLayoutTop);
    $SubLayoutTop = str_replace("망고아이", "잉글리씨드", $SubLayoutTop);
    $SubLayoutBottom = str_replace("망고아이", "잉글리씨드", $SubLayoutBottom);
    $MainLayoutBottom = str_replace("망고아이", "잉글리씨드", $MainLayoutBottom);


} else if($DomainSiteID==9){ //live.engedu.kr
    $MainLayoutTop = convertHTML(trim("{{Piece(header_engedu)}}"));
    $SubLayoutTop = convertHTML(trim("{{Piece(sub_06_engedu)}}"));
    $SubLayoutBottom = convertHTML(trim("{{Piece(sub_01_engedu_bottom)}}"));
    $MainLayoutBottom = convertHTML(trim("{{Piece(footer_engedu)}}"));

    $MainLayoutTop = str_replace("망고아이", "이엔지 화상영어", $MainLayoutTop);
    $SubLayoutTop = str_replace("망고아이", "이엔지 화상영어", $SubLayoutTop);
    $SubLayoutBottom = str_replace("망고아이", "이엔지 화상영어", $SubLayoutBottom);
    $MainLayoutBottom = str_replace("망고아이", "이엔지 화상영어", $MainLayoutBottom);


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

$MemberID = $_LINK_MEMBER_ID_;

//주간 포인트
$TodayWeek = date('w', strtotime(date("Y-m-d")));
$PointStartDate = date("Y-m-d", strtotime("-".$TodayWeek." day", strtotime(date("Y-m-d"))));
$PointEndDate = date("Y-m-d", strtotime((6-$TodayWeek)." day", strtotime(date("Y-m-d"))));

$ViewTable1 = "
	select 
		A.MemberID, 
		sum(A.MemberPoint) as MemberTotalPoint 
	from MemberPoints A 
	where 
		A.MemberPointState=1 
		and datediff(A.MemberPointRegDateTime, '".$PointStartDate."')>=0 and datediff(A.MemberPointRegDateTime, '".$PointEndDate."')<=0 
	group by A.MemberID 
";


//월간 포인트
$PointStartDate = date("Y-m-01");
$PointEndDate = date("Y-m-").date('t', strtotime(date("Y-m-01")));

$ViewTable2 = "
	select 
		A.MemberID, 
		sum(A.MemberPoint) as MemberTotalPoint 
	from MemberPoints A 
	where 
		A.MemberPointState=1 
		and datediff(A.MemberPointRegDateTime, '".$PointStartDate."')>=0 and datediff(A.MemberPointRegDateTime, '".$PointEndDate."')<=0 
	group by A.MemberID 
";


//전체 포인트
$ViewTable3 = "
	select 
		A.MemberID, 
		sum(A.MemberPoint) as MemberTotalPoint 
	from MemberPoints A 
	where 
		A.MemberPointState=1 
	group by A.MemberID 
";
?>

<?if($DomainSiteID==7){?>
    <div class="sub_wrap">
    <div class="sub_title_common_wrap"><h2 class="sub_title_common TrnTag">아이비리그 <b>포인트</b> 순위</h2></div>  
    <section class="point_rank_wrap">
        <div class="point_rank_area">
            <img src="images/img_point_rank.png" class="img_point_rank" alt="">
            <div class="point_rank_box">
                <div class="point_rank_1">
                    <h3 class="point_rank_caption TrnTag"><img src="images/icon_point_1.png" class="icon" alt=""><b>주간</b> 포인트 순위</h3>
                    <?
					$Sql = "
						select 
							A.MemberID,
							A.MemberTotalPoint,
							B.MemberLoginID,
							B.MemberName,
							C.CenterName
						from (".$ViewTable1.") A 
							inner join Members B on A.MemberID=B.MemberID 
							inner join Centers C on B.CenterID=C.CenterID 
						where B.MemberLevelID=19 
						order by A.MemberTotalPoint desc limit 0, 10
					";

					$Stmt = $DbConn->prepare($Sql);
					$Stmt->execute();
					$Stmt->setFetchMode(PDO::FETCH_ASSOC);
					?>
					<table class="point_rank_table one">
                        <col width="30%">
                        <col width="35%">
                        <col width="35%">
                        <tr>
                            <th class="TrnTag">순위</th>
                            <th class="TrnTag">ID/성명</th>
                            <th class="TrnTag">포인트</th>
                        </tr>
						<?
						$ListCount = 1;
						while($Row = $Stmt->fetch()) {
							$MemberID = $Row["MemberID"];
							$MemberLoginID = $Row["MemberLoginID"];
							$MemberName = $Row["MemberName"];
							$MemberTotalPoint = $Row["MemberTotalPoint"];
							$CenterName = $Row["CenterName"];
							
						?>
                        <tr>
                            <td><span class="point_rank_number"><?=$ListCount?></span></td>
                            <td>
								<small class="point_rank_academy"><?=$CenterName?></small>							
								<?=$MemberName?>
							</td>
                            <td><?=number_format($MemberTotalPoint,0)?></td>
                        </tr>
						<?
							$ListCount++;
						}
						$Stmt = null;
						?>
                    </table>
                </div>
                <div class="point_rank_2">
                    <h3 class="point_rank_caption TrnTag"><img src="images/icon_point_2.png" class="icon" alt=""><b>월간</b> 포인트 순위</h3>
                    <?
					$Sql = "
						select 
							A.MemberID,
							A.MemberTotalPoint,
							B.MemberLoginID,
							B.MemberName,
							C.CenterName
						from (".$ViewTable2.") A 
							inner join Members B on A.MemberID=B.MemberID 
							inner join Centers C on B.CenterID=C.CenterID 
						where B.MemberLevelID=19 
						order by A.MemberTotalPoint desc limit 0, 10
					";

					$Stmt = $DbConn->prepare($Sql);
					$Stmt->execute();
					$Stmt->setFetchMode(PDO::FETCH_ASSOC);
					?>
                    <table class="point_rank_table two">
                        <col width="30%">
                        <col width="35%">
                        <col width="35%">
                        <tr>
                            <th class="TrnTag">순위</th>
                            <th class="TrnTag">ID/성명</th>
                            <th class="TrnTag">포인트</th>
                        </tr>
						<?
						$ListCount = 1;
						while($Row = $Stmt->fetch()) {
							$MemberID = $Row["MemberID"];
							$MemberLoginID = $Row["MemberLoginID"];
							$MemberName = $Row["MemberName"];
							$MemberTotalPoint = $Row["MemberTotalPoint"];
							$CenterName = $Row["CenterName"];
							
						?>
                        <tr>
                            <td><span class="point_rank_number"><?=$ListCount?></span></td>
                            <td>
								<small class="point_rank_academy"><?=$CenterName?></small>
								<?=$MemberName?>
							</td>
                            <td><?=number_format($MemberTotalPoint,0)?></td>
                        </tr>
						<?
							$ListCount++;
						}
						$Stmt = null;
						?>
                    </table>
                </div>
                <div class="point_rank_3">
                    <h3 class="point_rank_caption TrnTag"><img src="images/icon_point_3.png" class="icon" alt=""><b>전체</b> 포인트 순위</h3>
                    <?
					$Sql = "
						select 
							A.MemberID,
							A.MemberTotalPoint,
							B.MemberLoginID,
							B.MemberName,
							C.CenterName
						from (".$ViewTable3.") A 
							inner join Members B on A.MemberID=B.MemberID 
							inner join Centers C on B.CenterID=C.CenterID 
						where B.MemberLevelID=19 
						order by A.MemberTotalPoint desc limit 0, 10
					";

					$Stmt = $DbConn->prepare($Sql);
					$Stmt->execute();
					$Stmt->setFetchMode(PDO::FETCH_ASSOC);
					?>
                    <table class="point_rank_table three">
                        <col width="30%">
                        <col width="35%">
                        <col width="35%">
                        <tr>
                            <th class="TrnTag">순위</th>
                            <th class="TrnTag">ID/성명</th>
                            <th class="TrnTag">포인트</th>
                        </tr>
						<?
						$ListCount = 1;
						while($Row = $Stmt->fetch()) {
							$MemberID = $Row["MemberID"];
							$MemberLoginID = $Row["MemberLoginID"];
							$MemberName = $Row["MemberName"];
							$MemberTotalPoint = $Row["MemberTotalPoint"];
							$CenterName = $Row["CenterName"];
							
						?>
                        <tr>
                            <td><span class="point_rank_number"><?=$ListCount?></span></td>
                            <td>
								<small class="point_rank_academy"><?=$CenterName?></small>
								<?=$MemberName?>
							</td>
                            <td><?=number_format($MemberTotalPoint,0)?></td>
                        </tr>
						<?
							$ListCount++;
						}
						$Stmt = null;
						?>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
<?} else if($DomainSiteID==8){?>
    <div class="sub_wrap">
    <div class="sub_title_common_wrap"><h2 class="sub_title_common TrnTag">잉글리씨드 <b>포인트</b> 순위</h2></div>
    <section class="point_rank_wrap">
        <div class="point_rank_area">
            <img src="images/img_point_rank.png" class="img_point_rank" alt="">
            <div class="point_rank_box">
                <div class="point_rank_1">
                    <h3 class="point_rank_caption TrnTag"><img src="images/icon_point_1.png" class="icon" alt=""><b>주간</b> 포인트 순위</h3>
                    <?
					$Sql = "
						select 
							A.MemberID,
							A.MemberTotalPoint,
							B.MemberLoginID,
							B.MemberName,
							C.CenterName
						from (".$ViewTable1.") A 
							inner join Members B on A.MemberID=B.MemberID 
							inner join Centers C on B.CenterID=C.CenterID 
						where B.MemberLevelID=19 
						order by A.MemberTotalPoint desc limit 0, 10
					";

					$Stmt = $DbConn->prepare($Sql);
					$Stmt->execute();
					$Stmt->setFetchMode(PDO::FETCH_ASSOC);
					?>
					<table class="point_rank_table one">
                        <col width="30%">
                        <col width="35%">
                        <col width="35%">
                        <tr>
                            <th class="TrnTag">순위</th>
                            <th class="TrnTag">ID/성명</th>
                            <th class="TrnTag">포인트</th>
                        </tr>
						<?
						$ListCount = 1;
						while($Row = $Stmt->fetch()) {
							$MemberID = $Row["MemberID"];
							$MemberLoginID = $Row["MemberLoginID"];
							$MemberName = $Row["MemberName"];
							$MemberTotalPoint = $Row["MemberTotalPoint"];
							$CenterName = $Row["CenterName"];

						?>
                        <tr>
                            <td><span class="point_rank_number"><?=$ListCount?></span></td>
                            <td>
								<small class="point_rank_academy"><?=$CenterName?></small>
								<?=$MemberName?>
							</td>
                            <td><?=number_format($MemberTotalPoint,0)?></td>
                        </tr>
						<?
							$ListCount++;
						}
						$Stmt = null;
						?>
                    </table>
                </div>
                <div class="point_rank_2">
                    <h3 class="point_rank_caption TrnTag"><img src="images/icon_point_2.png" class="icon" alt=""><b>월간</b> 포인트 순위</h3>
                    <?
					$Sql = "
						select 
							A.MemberID,
							A.MemberTotalPoint,
							B.MemberLoginID,
							B.MemberName,
							C.CenterName
						from (".$ViewTable2.") A 
							inner join Members B on A.MemberID=B.MemberID 
							inner join Centers C on B.CenterID=C.CenterID 
						where B.MemberLevelID=19 
						order by A.MemberTotalPoint desc limit 0, 10
					";

					$Stmt = $DbConn->prepare($Sql);
					$Stmt->execute();
					$Stmt->setFetchMode(PDO::FETCH_ASSOC);
					?>
                    <table class="point_rank_table two">
                        <col width="30%">
                        <col width="35%">
                        <col width="35%">
                        <tr>
                            <th class="TrnTag">순위</th>
                            <th class="TrnTag">ID/성명</th>
                            <th class="TrnTag">포인트</th>
                        </tr>
						<?
						$ListCount = 1;
						while($Row = $Stmt->fetch()) {
							$MemberID = $Row["MemberID"];
							$MemberLoginID = $Row["MemberLoginID"];
							$MemberName = $Row["MemberName"];
							$MemberTotalPoint = $Row["MemberTotalPoint"];
							$CenterName = $Row["CenterName"];

						?>
                        <tr>
                            <td><span class="point_rank_number"><?=$ListCount?></span></td>
                            <td>
								<small class="point_rank_academy"><?=$CenterName?></small>
								<?=$MemberName?>
							</td>
                            <td><?=number_format($MemberTotalPoint,0)?></td>
                        </tr>
						<?
							$ListCount++;
						}
						$Stmt = null;
						?>
                    </table>
                </div>
                <div class="point_rank_3">
                    <h3 class="point_rank_caption TrnTag"><img src="images/icon_point_3.png" class="icon" alt=""><b>전체</b> 포인트 순위</h3>
                    <?
					$Sql = "
						select 
							A.MemberID,
							A.MemberTotalPoint,
							B.MemberLoginID,
							B.MemberName,
							C.CenterName
						from (".$ViewTable3.") A 
							inner join Members B on A.MemberID=B.MemberID 
							inner join Centers C on B.CenterID=C.CenterID 
						where B.MemberLevelID=19 
						order by A.MemberTotalPoint desc limit 0, 10
					";

					$Stmt = $DbConn->prepare($Sql);
					$Stmt->execute();
					$Stmt->setFetchMode(PDO::FETCH_ASSOC);
					?>
                    <table class="point_rank_table three">
                        <col width="30%">
                        <col width="35%">
                        <col width="35%">
                        <tr>
                            <th class="TrnTag">순위</th>
                            <th class="TrnTag">ID/성명</th>
                            <th class="TrnTag">포인트</th>
                        </tr>
						<?
						$ListCount = 1;
						while($Row = $Stmt->fetch()) {
							$MemberID = $Row["MemberID"];
							$MemberLoginID = $Row["MemberLoginID"];
							$MemberName = $Row["MemberName"];
							$MemberTotalPoint = $Row["MemberTotalPoint"];
							$CenterName = $Row["CenterName"];

						?>
                        <tr>
                            <td><span class="point_rank_number"><?=$ListCount?></span></td>
                            <td>
								<small class="point_rank_academy"><?=$CenterName?></small>
								<?=$MemberName?>
							</td>
                            <td><?=number_format($MemberTotalPoint,0)?></td>
                        </tr>
						<?
							$ListCount++;
						}
						$Stmt = null;
						?>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
<?} else {?>
<div class="sub_wrap">
    <div class="sub_title_common_wrap"><h2 class="sub_title_common TrnTag">망고아이 <b>포인트</b> 순위</h2></div>  
    <section class="point_rank_wrap">
        <div class="point_rank_area">
            <img src="images/img_point_rank.png" class="img_point_rank" alt="">
            <div class="point_rank_box">
                <div class="point_rank_1">
                    <h3 class="point_rank_caption TrnTag"><img src="images/icon_point_1.png" class="icon" alt=""><b>주간</b> 포인트 순위</h3>
                    <?
					$Sql = "
						select 
							A.MemberID,
							A.MemberTotalPoint,
							B.MemberLoginID,
							B.MemberName,
							C.CenterName
						from (".$ViewTable1.") A 
							inner join Members B on A.MemberID=B.MemberID 
							inner join Centers C on B.CenterID=C.CenterID 
						where B.MemberLevelID=19 
						order by A.MemberTotalPoint desc limit 0, 10
					";

					$Stmt = $DbConn->prepare($Sql);
					$Stmt->execute();
					$Stmt->setFetchMode(PDO::FETCH_ASSOC);
					?>
					<table class="point_rank_table one">
                        <col width="30%">
                        <col width="35%">
                        <col width="35%">
                        <tr>
                            <th class="TrnTag">순위</th>
                            <th class="TrnTag">ID/성명</th>
                            <th class="TrnTag">포인트</th>
                        </tr>
						<?
						$ListCount = 1;
						while($Row = $Stmt->fetch()) {
							$MemberID = $Row["MemberID"];
							$MemberLoginID = $Row["MemberLoginID"];
							$MemberName = $Row["MemberName"];
							$MemberTotalPoint = $Row["MemberTotalPoint"];
							$CenterName = $Row["CenterName"];
							
						?>
                        <tr>
                            <td><span class="point_rank_number"><?=$ListCount?></span></td>
                            <td>
								<small class="point_rank_academy"><?=$CenterName?></small>							
								<?=$MemberName?>
							</td>
                            <td><?=number_format($MemberTotalPoint,0)?></td>
                        </tr>
						<?
							$ListCount++;
						}
						$Stmt = null;
						?>
                    </table>
                </div>
                <div class="point_rank_2">
                    <h3 class="point_rank_caption TrnTag"><img src="images/icon_point_2.png" class="icon" alt=""><b>월간</b> 포인트 순위</h3>
                    <?
					$Sql = "
						select 
							A.MemberID,
							A.MemberTotalPoint,
							B.MemberLoginID,
							B.MemberName,
							C.CenterName
						from (".$ViewTable2.") A 
							inner join Members B on A.MemberID=B.MemberID 
							inner join Centers C on B.CenterID=C.CenterID 
						where B.MemberLevelID=19 
						order by A.MemberTotalPoint desc limit 0, 10
					";

					$Stmt = $DbConn->prepare($Sql);
					$Stmt->execute();
					$Stmt->setFetchMode(PDO::FETCH_ASSOC);
					?>
                    <table class="point_rank_table two">
                        <col width="30%">
                        <col width="35%">
                        <col width="35%">
                        <tr>
                            <th class="TrnTag">순위</th>
                            <th class="TrnTag">ID/성명</th>
                            <th class="TrnTag">포인트</th>
                        </tr>
						<?
						$ListCount = 1;
						while($Row = $Stmt->fetch()) {
							$MemberID = $Row["MemberID"];
							$MemberLoginID = $Row["MemberLoginID"];
							$MemberName = $Row["MemberName"];
							$MemberTotalPoint = $Row["MemberTotalPoint"];
							$CenterName = $Row["CenterName"];
							
						?>
                        <tr>
                            <td><span class="point_rank_number"><?=$ListCount?></span></td>
                            <td>
								<small class="point_rank_academy"><?=$CenterName?></small>
								<?=$MemberName?>
							</td>
                            <td><?=number_format($MemberTotalPoint,0)?></td>
                        </tr>
						<?
							$ListCount++;
						}
						$Stmt = null;
						?>
                    </table>
                </div>
                <div class="point_rank_3">
                    <h3 class="point_rank_caption TrnTag"><img src="images/icon_point_3.png" class="icon" alt=""><b>전체</b> 포인트 순위</h3>
                    <?
					$Sql = "
						select 
							A.MemberID,
							A.MemberTotalPoint,
							B.MemberLoginID,
							B.MemberName,
							C.CenterName
						from (".$ViewTable3.") A 
							inner join Members B on A.MemberID=B.MemberID 
							inner join Centers C on B.CenterID=C.CenterID 
						where B.MemberLevelID=19 
						order by A.MemberTotalPoint desc limit 0, 10
					";

					$Stmt = $DbConn->prepare($Sql);
					$Stmt->execute();
					$Stmt->setFetchMode(PDO::FETCH_ASSOC);
					?>
                    <table class="point_rank_table three">
                        <col width="30%">
                        <col width="35%">
                        <col width="35%">
                        <tr>
                            <th class="TrnTag">순위</th>
                            <th class="TrnTag">ID/성명</th>
                            <th class="TrnTag">포인트</th>
                        </tr>
						<?
						$ListCount = 1;
						while($Row = $Stmt->fetch()) {
							$MemberID = $Row["MemberID"];
							$MemberLoginID = $Row["MemberLoginID"];
							$MemberName = $Row["MemberName"];
							$MemberTotalPoint = $Row["MemberTotalPoint"];
							$CenterName = $Row["CenterName"];
							
						?>
                        <tr>
                            <td><span class="point_rank_number"><?=$ListCount?></span></td>
                            <td>
								<small class="point_rank_academy"><?=$CenterName?></small>
								<?=$MemberName?>
							</td>
                            <td><?=number_format($MemberTotalPoint,0)?></td>
                        </tr>
						<?
							$ListCount++;
						}
						$Stmt = null;
						?>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
<?}?>


<script language="javascript">
$('.toggle_navi.six .seven').addClass('active');
$('.sub_visual_navi .seven').addClass('active');
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





