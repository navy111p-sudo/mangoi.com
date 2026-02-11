<?php
$top_menu_id = 2;
$left_menu_id = 2;
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
include_once('./_header.php');
?>

<style>
.content{width:100%; padding:0; margin:0;}
.table3{background:#fff; margin-top:20px; border-left:1px solid #ddd; border-top:1px solid #ddd;}
</style>

<body>

	<div class="content">
		<h2>팝업설정</h2>
		<?php
		
		$BoardID = isset($_REQUEST["BoardID"]) ? $_REQUEST["BoardID"] : "";
		$BoardCategoryID = isset($_REQUEST["BoardCategoryID"]) ? $_REQUEST["BoardCategoryID"] : "";

		if ($BoardCategoryID!=""){
			$Sql = "select * from BoardCategories where BoardCategoryID=:BoardCategoryID";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':BoardCategoryID', $BoardCategoryID);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);
			$Row = $Stmt->fetch();
			$Stmt = null;

			$BoardID = $Row["BoardID"];
			$BoardCategoryID = $Row["BoardCategoryID"];
			$BoardCategoryName = $Row["BoardCategoryName"];

		}
		
		?>
		<div class="box">
			<form id="form" name="RegForm" method="post" accept-charset="UTF-8">
			<input type="hidden" name="BoardCategoryID" value="<?=$BoardCategoryID?>">
			<input type="hidden" name="BoardID" value="<?=$BoardID?>">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table3">
			  <tr>
				<th width="22%">카테고리명</th>
				<td><input type="text" id="BoardCategoryName" name="BoardCategoryName" value="<?=$BoardCategoryName?>" style="width:98%;"/></td>
			  </tr>	  

			</table>
			</form>

			<div class="button" style="margin-top:15px;">
				<a href="javascript:FormSubmit();">등록하기</a>
			</div>
			
		</div>
	</div>



<script language="javascript">
function FormSubmit(){
	obj = document.RegForm.BoardCategoryName;
	if (obj.value==""){
		alert('카테고리명을 입력하세요.');
		obj.focus();
		return;
	}



	document.RegForm.action = "board_category_action.php";
	document.RegForm.submit();
}

</script>





</body>
<?php
include_once('./_footer.php');
include_once('../includes/dbclose.php');
?>







