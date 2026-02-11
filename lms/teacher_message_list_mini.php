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

<!-- ============== only this page css ============== -->
<!-- ============== common.css ============== -->
<link rel="stylesheet" type="text/css" href="css/common.css" />
<!-- ============== common.css ============== -->


</head>

<body class="disable_transitions sidebar_main_open sidebar_main_swipe" style="padding-top:0px;">

<?php
$TeacherID = isset($_REQUEST["TeacherID"]) ? $_REQUEST["TeacherID"] : "";
$TeacherMessageType = 1;


$Sql = "select 
				B.MemberID 
		from Teachers A 
			inner join Members B on A.TeacherID=B.TeacherID and B.MemberLevelID=15 
		where A.TeacherID=$TeacherID ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$MemberID = $Row["MemberID"];

?>


<div id="page_content">
	<div id="page_content_inner">
		
		<ul id="user_edit_tabs" class="uk-tab">
			<li style="background-color:#ffffff;border-radius:5px 5px 0px 0px;"><a href="teacher_message_form.php?TeacherID=<?=$TeacherID?>" style="color:#1D76CE;"><?=$메시지전송[$LangID]?></a></li>
			<li style="background-color:#1D76CE;border-radius:5px 5px 0px 0px;"><a href="teacher_message_list_mini.php?TeacherID=<?=$TeacherID?>" style="color:#ffffff;"><?=$전송목록[$LangID]?></a></li>
		</ul>


		<div class="uk-grid" data-uk-grid-margin style="padding-left:25px;">


			<?php

			$AddSqlWhere = "1=1";
			$ListParam = "1=1";
			$PaginationParam = "1=1";


			$CurrentPage = isset($_REQUEST["CurrentPage"]) ? $_REQUEST["CurrentPage"] : "";
			$PageListNum = isset($_REQUEST["PageListNum"]) ? $_REQUEST["PageListNum"] : "";
			$SearchText = isset($_REQUEST["SearchText"]) ? $_REQUEST["SearchText"] : "";
			$SearchState = isset($_REQUEST["SearchState"]) ? $_REQUEST["SearchState"] : "";





			if (!$CurrentPage){
				$CurrentPage = 1;	
			}
			if (!$PageListNum){
				$PageListNum = 10;
			}

			if ($PageListNum!=""){
				$ListParam = $ListParam . "&PageListNum=" . $PageListNum;
			}

			$ListParam = $ListParam . "&TeacherID=" . $TeacherID;


			$AddSqlWhere = $AddSqlWhere . " and A.MemberID=".$MemberID."";
			$AddSqlWhere = $AddSqlWhere . " and A.TeacherMessageType=1 ";
			$AddSqlWhere = $AddSqlWhere . " and (A.TeacherMessageID in (select TeacherMessageID from TeacherMessageReads where MemberID=".$MemberID.") or datediff(A.TeacherMessageRegDateTime, now())=0) ";// 읽었거나 오늘 메시지

			$PaginationParam = $ListParam;
			if ($CurrentPage!=""){
				$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
			}

			$ListParam = str_replace("&", "^^", $ListParam);

			$Sql = "select 
							count(*) TotalRowCount 
					from TeacherMessages A 
					where ".$AddSqlWhere." ";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);
			$Row = $Stmt->fetch();
			$Stmt = null;

			$TotalRowCount = $Row["TotalRowCount"];

			$TotalPageCount = ceil($TotalRowCount / $PageListNum);
			$StartRowNum = $PageListNum * ($CurrentPage - 1 );



			$Sql = "
					select 
						A.*,
						ifnull((select TeacherMessageReadID from TeacherMessageReads where TeacherMessageID=A.TeacherMessageID and MemberID=".$MemberID."),0) as TeacherMessageReadID,
						ifnull((select TeacherMessageReadDateTime from TeacherMessageReads where TeacherMessageID=A.TeacherMessageID and MemberID=".$MemberID."),'<?=$확인전[$LangID]?>') as TeacherMessageReadDateTime
					from TeacherMessages A
					where ".$AddSqlWhere." 
					order by A.TeacherMessageRegDateTime desc limit $StartRowNum, $PageListNum";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);

			?>

			<div class="md-card uk-width-1-1">
				<div class="md-card-content">
					<div class="uk-grid" data-uk-grid-margin>
						<div class="uk-width-1-1">
							<div class="uk-overflow-container">
								<table class="uk-table uk-table-align-vertical">
									<thead>
										<tr>
											<th nowrap style="width:8%;">No</th>
											<th nowrap style="width:15%;"><?=$등록일[$LangID]?></th>
											<th nowrap><?=$메시지[$LangID]?></th>
											<th nowrap style="width:15%;"><?=$확인시간[$LangID]?></th>
										</tr>
									</thead>
									<tbody>
										
										<?php
										$ListCount = 1;
										while($Row = $Stmt->fetch()) {
											$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

											$TeacherMessageID = $Row["TeacherMessageID"];

											$TeacherMessageType = $Row["TeacherMessageType"];
											$TeacherMessageText = $Row["TeacherMessageText"];
											$TeacherMessageRegDateTime = $Row["TeacherMessageRegDateTime"];

											$TeacherMessageReadID = $Row["TeacherMessageReadID"];
											$TeacherMessageReadDateTime = $Row["TeacherMessageReadDateTime"];

								
										?>
										<tr>
											<td class="uk-text-nowrap uk-table-td-center"><?=$ListNumber?></td>
											<td class="uk-text-nowrap uk-table-td-center"><?=$TeacherMessageRegDateTime?></td>
											<td class="uk-text-nowrap"><b><?=$TeacherMessageText?></b></td>
											<td class="uk-text-nowrap uk-table-td-center" id="DivTeacherMessageReadDateTime_<?=$TeacherMessageID?>"><?=$TeacherMessageReadDateTime?></td>
										</tr>
										<?php
											$ListCount ++;
										}
										$Stmt = null;
										?>


									</tbody>
								</table>
							</div>
							

							<?php			
							include_once('./inc_pagination.php');
							?>


						</div>
					</div>
				</div>
			</div>

		</div>


	</div>
</div>


<?
include_once('./inc_common_form_js.php');
?>
<!-- ==============  only this page js ============== -->

<!-- ==============  only this page js ============== -->
<!-- ==============  common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ==============  common.js ============== -->


<script language="javascript">

function FormSubmit(){


	obj = document.RegForm.TeacherMessageText;
	if (obj.value==""){
		UIkit.modal.alert("<?=$내용을_입력하세요[$LangID]?>");
		obj.focus();
		return;
	}
	

	UIkit.modal.confirm(
		'<?=$저장_하시겠습니까[$LangID]?>?', 
		function(){ 
			document.RegForm.action = "teacher_message_action.php";
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