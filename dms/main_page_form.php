<?php
$top_menu_id = 4;
$left_menu_id = 2;
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
		<h2>메인페이지설정</h2>
		<?php
		$Sql = "select * from Main limit 0,1";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;

		$MainContent = $Row["MainContent"];
		$MainContentCss = $Row["MainContentCss"];
		$MainContentJavascript = $Row["MainContentJavascript"];
		?>
		<div class="box">
			<form id="form" name="RegForm" method="post" enctype="multipart/form-data" accept-charset="UTF-8">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table3">		  
			  
			  
			  <tr>
				<th width="15%">메인페이지</th>
				<td>
				    <textarea id="MainContent" name="MainContent" cols="50" rows="12" class="editor"><?=$MainContent?></textarea>
					<script>
					var editor = CodeMirror.fromTextArea(document.getElementById("MainContent"), {
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
					<textarea id="MainContentCss" name="MainContentCss" cols="50" rows="12" class="editor"><?=$MainContentCss?></textarea>
					&lt;/style&gt;
					<script>
					var editor = CodeMirror.fromTextArea(document.getElementById("MainContentCss"), {
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
					<textarea id="MainContentJavascript" name="MainContentJavascript" cols="50" rows="12" class="editor"><?=$MainContentJavascript?></textarea>
					&lt;/script&gt;
					<script>
					var editor = CodeMirror.fromTextArea(document.getElementById("MainContentJavascript"), {
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
	
	document.RegForm.action = "main_page_action.php";
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







