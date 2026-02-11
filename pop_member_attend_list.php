<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
include_once('./includes/member_check.php');
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
<?php
include_once('./includes/common_header.php');
?>
</head>
<body>
<?
include_once('./includes/common_body_top.php');
?>

        <!-------------------------- 컨텐츠영역 시작 -------------------------->
        <div class="ContentArea" style="padding:20px;">

			<?
			$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";
			$today = isset($_REQUEST["today"]) ? $_REQUEST["today"] : "";
			$year = isset($_REQUEST["year"]) ? $_REQUEST["year"] : "";
			$month = isset($_REQUEST["month"]) ? $_REQUEST["month"] : "";


			if ($year==""){
				$year = date('Y');
			}
			if ($month==""){
				$month = (int)date('m'); 
			}

			$p_month = $month - 1;
			$n_month = $month + 1;

			if ($p_month==0){
				$p_month = 12;
				$p_year = $year-1;
			}else{
				$p_year = $year;
			}

			if ($n_month==13){
				$n_month = 1;
				$n_year = $year+1;
			}else{
				$n_year = $year;
			}

			$time = strtotime($year.'-'.$month.'-01'); 
			list($tday, $sweek) = explode('-', date('t-w', $time));  // 총 일수, 시작요일 
			$tweek = ceil(($tday + $sweek) / 7);  // 총 주차 
			$lweek = date('w', strtotime($year.'-'.$month.'-'.$tday));  // 마지막요일 



			for ($ii=1;$ii<=31;$ii++){
				$Attend[$ii][1] = "-";
				$Attend[$ii][2] = "-";
			}

			$Sql = "select * from AttendRecords where AttendYear=$year and AttendMonth=$month and MemberID=$MemberID ";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);

			while($Row = $Stmt->fetch()) {
				$Attend[$Row["AttendDay"]][$Row["AttendType"]] = $Row["AttendHour"]."시 ".$Row["AttendMinute"]."분";
			}
			$Stmt = null;
			?> 

			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="CalTop">
				<col width="36%">
				<col width="">
				<col width="36%">
			  <tr>
				<td><a href="pop_member_attend_list.php?year=<?=$p_year?>&month=<?=$p_month?>&MemberID=<?=$MemberID?>">◀</a> <?=$year?>년 <?=$month?>월 <a href="pop_member_attend_list.php?year=<?=$n_year?>&month=<?=$n_month?>&MemberID=<?=$MemberID?>">▶</a></td>
				<th><b><?=$month?></b>월</th>
				<td></td>
			  </tr>
			</table>          

			<div style="text-align:right;padding-right:5px;">
				<img src="images1/attend_on.png"> <trn class="TrnTag">등원</trn> 
				<img src="images1/attend_off.png"> <trn class="TrnTag">하원</trn> 
			</div>		 
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="Cal">
			  <tr>
				<th class="Sun">SUN</th>
				<th>MON</th>
				<th>TUE</th>
				<th>WED</th>
				<th>THU</th>
				<th>FRI</th>
				<th class="Sat">SAT</th>
			  </tr>

			<?
			for ($nn=1,$ii=0; $ii<$tweek; $ii++){
			?> 
			<tr> 
				<?
				for ($kk=0; $kk<7; $kk++){
					
				?> 
				 <td <?if ($kk==0){?>class="Sun"<?}else if ($kk==6){?>class="Sat"<?}?> style="height:80px;">
					<? 
					if (($ii == 0 && $kk < $sweek) || ($ii == $tweek-1 && $kk > $lweek)) {
						echo "</td>\n";
						continue;
					}
					$day = $nn++;
					$NowDate = strtotime($year . "-" . substr("0".$month,-2) . "-" . substr("0".$day,-2));

					if ($today-$NowDate==0){
						echo "<div style=\"padding-top:6px;font-weight:bold;text-align:center;background-color:#5E97F7;color:#FFFFFF;width:30px;height:30px;border-radius:15px;\">".$day."</div>";
					}else{
						echo $day;
					}
					?>
					<div style="margin-top:10px;">
						<?if ($Attend[$day][1]!="-" || $Attend[$day][2]!="-") {?>
						<div><img src="images1/attend_on.png" style="border:1px solid #cccccc;"> <?=$Attend[$day][1]?></div>
						<div><img src="images1/attend_off.png" style="border:1px solid #cccccc;"> <?=$Attend[$day][2]?></div>
						<?}?>
					</div>
				 </td> 
				<?
				}
				?> 
			</tr> 
			<?
			}
			?> 

			</table>
			

            
        </div><!-- 컨텐츠영역 끝 -->
        
<?
include_once('./includes/common_analytics.php');
?>
</body>

<?php
include_once('./includes/common_footer.php');
?>
</html>

