<?php
$top_menu_id = 2;
$left_menu_id = 1;
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
include_once('./_header.php');
?>
<body>
<?php
include_once('./_top.php');
include_once('./_left.php');
?>

<div class="right">
	<div class="content">
		<h2>기본설정</h2>
		<?php
		$Sql = "select SiteName, SiteTitle, SiteFavicon from SiteSetup where Seq=1";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;
		?>
		<div class="box">
			<form id="form" name="RegForm" method="post" enctype="multipart/form-data" accept-charset="UTF-8">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table3">
			  <tr>
				<th width="15%">사이트명</th>
				<td><input type="text" id="SiteName" name="SiteName" value="<?=$Row["SiteName"]?>" style="width:300px;"></td>
			  </tr>
			  <tr>
				<th width="15%">사이트타이틀</th>
				<td><input type="text" id="SiteTitle" name="SiteTitle" value="<?=$Row["SiteTitle"]?>" style="width:300px;"></td>
			  </tr>
			  <tr>
				<th>파비콘</th>
				<td>
					<input type="file" id="file" name="SiteFavicon" style="width:300px;"> 
					<?php
					if ($Row["SiteFavicon"]!="" && $Row["SiteFavicon"]!=NULL){
					?>
					<input type="checkbox" name="DelSiteFavicon" value="1"> 삭제
					<?php
					}
					?>
				</td>
			  </tr>
			</table>
			</form>

			<div class="button">
				<a href="javascript:FormSubmit();">등록</a>
			</div>

		</div>
	</div>
</div>	
	
	
	



<script language="javascript">
function FormSubmit(){
	obj = document.RegForm.SiteName;
	if (obj.value==""){
		alert('사이트명을 입력하세요.');
		obj.focus();
		return;
	}

	obj = document.RegForm.SiteTitle;
	if (obj.value==""){
		alert('사이트타이틀을 입력하세요.');
		obj.focus();
		return;
	}


	document.RegForm.action = "setup_action.php";
	document.RegForm.submit();
}

</script>




<?php
include_once('./_bottom.php');
?>
</body>
<?php
include_once('./_footer.php');
include_once('../includes/dbclose.php');
?>







