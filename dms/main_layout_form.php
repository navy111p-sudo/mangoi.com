<?php
$top_menu_id = 4;
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
		<h2>메인레이이웃설정</h2>
		<?php
		$Sql = "select * from Main limit 0,1";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;

		$MainLayout = $Row["MainLayout"];
		$MainLayoutCss = $Row["MainLayoutCss"];
		$MainLayoutJavascript = $Row["MainLayoutJavascript"];
		?>
		<div class="box">
			<form id="form" name="RegForm" method="post" enctype="multipart/form-data" accept-charset="UTF-8">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table3">		  
			  
			  
			  <tr>
				<th width="15%">레이아웃</th>
				<td>
				    <textarea id="MainLayout" name="MainLayout" cols="50" rows="12" class="editor"><?=$MainLayout?></textarea>
					{{page}} 코드 위치에 메인페이지가 삽입됩니다. {{page}} 코드는 반드시 1개가 있어야 합니다.
					<script>
					var editor = CodeMirror.fromTextArea(document.getElementById("MainLayout"), {
						theme: "mdn-like",
						lineNumbers: true,
						styleActiveLine: true,
						matchBrackets: true,
						extraKeys: {
							"F11": function(cm) {
							cm.setOption("fullScreen", !cm.getOption("fullScreen"));
							},
							"Esc": function(cm) {
								if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
							}
						}
					});

					editor.setSize(800, 600);
				
					</script>

				</td>
			  </tr>
			  <tr>
				<th width="15%">CSS</th>
				<td>
					&lt;style&gt;
					<textarea id="MainLayoutCss" name="MainLayoutCss" cols="50" rows="12" class="editor"><?=$MainLayoutCss?></textarea>
					&lt;/style&gt;
					<script>
					var editor = CodeMirror.fromTextArea(document.getElementById("MainLayoutCss"), {
						theme: "mdn-like",
						lineNumbers: true,
						styleActiveLine: true,
						matchBrackets: true,
						extraKeys: {
							"F11": function(cm) {
							cm.setOption("fullScreen", !cm.getOption("fullScreen"));
							},
							"Esc": function(cm) {
								if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
							}
						}
					});

					editor.setSize(800, 150);
				
					</script>

				</td>
			  </tr>
			  <tr>
				<th width="15%">javascript</th>
				<td>
				    &lt;script&gt;
					<textarea id="MainLayoutJavascript" name="MainLayoutJavascript" cols="50" rows="12" class="editor"><?=$MainLayoutJavascript?></textarea>
					&lt;/script&gt;
					<script>
					var editor = CodeMirror.fromTextArea(document.getElementById("MainLayoutJavascript"), {
						theme: "mdn-like",
						lineNumbers: true,
						styleActiveLine: true,
						matchBrackets: true,
						extraKeys: {
							"F11": function(cm) {
							cm.setOption("fullScreen", !cm.getOption("fullScreen"));
							},
							"Esc": function(cm) {
								if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
							}
						}
					});
				
					editor.setSize(800, 150);

					</script>

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
	
	document.RegForm.action = "main_layout_action.php";
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







