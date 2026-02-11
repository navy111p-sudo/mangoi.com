<?php
$top_menu_id = 4;
$left_menu_id = 3;
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
		<h2>서브설정</h2>
		<?php
		$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
		$SubID = isset($_REQUEST["SubID"]) ? $_REQUEST["SubID"] : "";


		if ($SubID!=""){
			$Sql = "select * from Subs where SubID=:SubID";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':SubID', $SubID);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);
			$Row = $Stmt->fetch();
			$Stmt = null;

			$SubID = $Row["SubID"];
			$SubCode = $Row["SubCode"];
			$SubName = $Row["SubName"];
			$SubLayout = $Row["SubLayout"];
			$SubLayoutCss = $Row["SubLayoutCss"];
			$SubLayoutJavascript = $Row["SubLayoutJavascript"];
			$SubState = $Row["SubState"];

			$CheckedCode = "1";
			$NewData = "0";
		}else{
			$SubCode = "";
			$SubName = "";
			$SubLayout = "{{Page}}";
			$SubLayoutCss = "";
			$SubLayoutJavascript = "";
			$SubState = 1;
			$CheckedCode = "0";
			$NewData = "1";

		}
		
		?>
		<div class="box">
			<form id="form" name="RegForm" method="post" enctype="multipart/form-data" accept-charset="UTF-8">
			<input type="hidden" name="SubID" value="<?=$SubID?>">
			<input type="hidden" name="ListParam" value="<?=$ListParam?>">
			<input type="hidden" id="CheckedCode" name="CheckedCode" value="<?=$CheckedCode?>">
			<input type="hidden" name="NewData" value="<?=$NewData?>">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table3">		  
			  

			  <tr>
				<th width="15%">서브코드</th>
				<td>
					<?php
					if ($NewData=="1"){
					?>
					<input type="text" id="SubCode" name="SubCode"  value="<?=$SubCode?>" onkeyup="EnNewCode()" style="ime-mode:disabled;"/>
					<input id="BtnCodeCheck" type="button" name="button" value="중복확인" onclick="CheckCode()" class="btn_input" style="display:none;">
					<?php
					}else{
					?>
					<?=$SubCode?>
					<input type="hidden" id="SubCode" name="SubCode"  value="<?=$SubCode?>"/>
					<?php
					}
					?>
				</td>
			  </tr>

			  <tr>
				<th width="15%">서브명</th>
				<td><input type="text" id="SubName" name="SubName" value="<?=$SubName?>"/></td>
			  </tr>
			  

			  <tr>
				<th width="15%">서브레이아웃</th>
				<td>
				    <textarea id="SubLayout" name="SubLayout" cols="50" rows="12" class="editor"><?=$SubLayout?></textarea>
					{{page}} 코드 위치에 페이지가 삽입됩니다. {{page}} 코드는 반드시 1개가 있어야 합니다.
					<script>
					var editor = CodeMirror.fromTextArea(document.getElementById("SubLayout"), {
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
					<textarea id="SubLayoutCss" name="SubLayoutCss" cols="50" rows="12" class="editor"><?=$SubLayoutCss?></textarea>
					&lt;/style&gt;
					<script>
					var editor = CodeMirror.fromTextArea(document.getElementById("SubLayoutCss"), {
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
					<textarea id="SubLayoutJavascript" name="SubLayoutJavascript" cols="50" rows="12" class="editor"><?=$SubLayoutJavascript?></textarea>
					&lt;/script&gt;
					<script>
					var editor = CodeMirror.fromTextArea(document.getElementById("SubLayoutJavascript"), {
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
				<th width="15%">상태</th>
				<td>
					<input type="radio" name="SubState" value="1" <?php if ($SubState==1) {echo ("checked");}?>><label>승인</label> 
					<input type="radio" name="SubState" value="0" <?php if ($SubState==0) {echo ("checked");}?>><label>미승인</label>
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


function EnNewCode(){
	document.RegForm.CheckedCode.value = "0";
	document.getElementById("BtnCodeCheck").style.display = "inline";
}


function CheckCode() {

    var NewCode = $.trim($('#SubCode').val());

    if (NewCode == "") {
        alert('서브코드를 입력하세요.');
        document.TheForm.CheckedCode.value = "0";
    } else {
        url = "ajax_check_sub_code.php";
        $.ajax(url, {
            data: {
                NewCode: NewCode
            },
            success: function (data) {
                if (data == "1") {
                    alert('사용 가능한 서브코드 입니다.');
                    document.RegForm.CheckedCode.value = "1";
					document.getElementById("BtnCodeCheck").style.display = "none";
                }
                else {
                    alert('이미 사용중인 서브코드 입니다.');
                    document.RegForm.CheckedCode.value = "0";
					document.getElementById("BtnCodeCheck").style.display = "inline";
                }
            },
            error: function () {
                alert('Error while contacting server, please try again');
                document.RegForm.CheckedCode.value = "0";
				document.getElementById("BtnCodeCheck").style.display = "inline";
            }
        });

    }

}




function FormSubmit(){
	
	obj = document.RegForm.SubCode;
	if (obj.value==""){
		alert('서브코드를 입력하세요.');
		obj.focus();
		return;
	}


	<?php
	if ($NewData=="1"){
	?>

	obj = document.RegForm.CheckedCode;
	if (obj.value=="0"){
		alert('서브코드 중복확인을 진행하세요.');
		obj.focus();
		return;
	}

	<?
	}
	?>
	

	obj = document.RegForm.SubName;
	if (obj.value==""){
		alert('서브명을 입력하세요.');
		obj.focus();
		return;
	}


	document.RegForm.action = "sub_layout_action.php";
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







