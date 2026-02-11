<?php
include_once('./includes/dbopen.php');
$SsoNoRedirct = "1";//SSO 사용하는 사이트(englishtell, thomas) common 에서 무한 루프 방지용
include_once('./includes/common.php');
include_once('./includes/member_check.php');
?>
<html>
<head>
    <meta charset="utf-8">
    <title>주저없는 선택 망고아이</title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=no" id="viewport" />
    <link href="css/common.css" rel="stylesheet" type="text/css" />
    <link href="css/sub_style.css" rel="stylesheet" type="text/css" />
</head>
<?
$ClassOrderID = isset($_REQUEST["ClassOrderID"]) ? $_REQUEST["ClassOrderID"] : "";
?>
<body>
<?
include_once('./includes/common_body_top.php');
?>
    <div class="summary_wrap">
        <h1 class="summary_title"><b class="TrnTag">수업변경로그</b></h1>  
        
        <section class="summary_section">
            <h3 class="summary_caption_left TrnTag">수업변경로그</h3> 
            <table class="summary_table small">
                <tr>
                    <th style="width:20%;" class="TrnTag">날짜</th>
                    <th style="width:20%;" class="TrnTag">이름</th>
                    <th style="width:60%;" class="TrnTag">변경내용</th>
                </tr>
				<?
				$Sql = "
					select 
						A.*,
						B.MemberName
					from ClassOrderSlotLogs A 
						inner join Members B on A.MemberID=B.MemberID 
					where A.ClassOrderID=".$ClassOrderID."
					order by A.ClassOrderSlotLogRegDateTime desc 
				";
				?>

				<?

				$Stmt = $DbConn->prepare($Sql);
				$Stmt->execute();
				$Stmt->setFetchMode(PDO::FETCH_ASSOC);
				$ListCount = 1;

				while($Row = $Stmt->fetch()) {
					$ClassOrderID = $Row["ClassOrderID"];
					$MemberID = $Row["MemberID"];
					$ClassOrderSlotLogMemo = $Row["ClassOrderSlotLogMemo"];
					$ClassOrderSlotLogRegDateTime = $Row["ClassOrderSlotLogRegDateTime"];

					$MemberName = $Row["MemberName"];
					
				?>

				<tr>
					<td><?=$ClassOrderSlotLogRegDateTime?></td>
                    <td><?=$MemberName?></td>
                    <td><?=$ClassOrderSlotLogMemo?></td>
                </tr>
				<?
					$ListCount++;
				}
				$Stmt = null;

				if ($ListCount==1){
				?>
				<tr>
                    <td colspan="7" class="TrnTag"> 수업 변경 기록이 없습니다. </td>
                </tr>
				<?
				}
				?>


            </table>
        </section>       

        

    </div>



</body>
</html>
<?php
include_once('./includes/dbclose.php');
?>