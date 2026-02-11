<?php
$top_menu_id = 4;
$left_menu_id = 6;
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
		<h2>피스설정</h2>
		<?php
		$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
		$PieceID = isset($_REQUEST["PieceID"]) ? $_REQUEST["PieceID"] : "";


		if ($PieceID!=""){
			$Sql = "select * from Pieces where PieceID=:PieceID";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':PieceID', $PieceID);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);
			$Row = $Stmt->fetch();
			$Stmt = null;

			$PieceID = $Row["PieceID"];
			$PieceCode = $Row["PieceCode"];
			$PieceName = $Row["PieceName"];
			$PieceLayout = $Row["PieceLayout"];
			$PieceState = $Row["PieceState"];

			$CheckedCode = "1";
			$NewData = "0";
		}else{

			$PieceCode = "";
			$PieceName = "";
			$PieceLayout = "";
			$PieceState = 1;
			$CheckedCode = "0";
			$NewData = "1";

		}
		
		?>
		<div class="box">
			<form id="form" name="RegForm" method="post" enctype="multipart/form-data" accept-charset="UTF-8">
			<input type="hidden" name="PieceID" value="<?=$PieceID?>">
			<input type="hidden" name="ListParam" value="<?=$ListParam?>">
			<input type="hidden" id="CheckedCode" name="CheckedCode" value="<?=$CheckedCode?>">
			<input type="hidden" name="NewData" value="<?=$NewData?>">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table3">		  
			  

			  <tr>
				<th width="15%">피스코드</th>
				<td>
					<?php
					if ($NewData=="1"){
					?>
					<input type="text" id="PieceCode" name="PieceCode"  value="<?=$PieceCode?>" onkeyup="EnNewCode()" style="ime-mode:disabled;"/>
					<input id="BtnCodeCheck" type="button" name="button" value="중복확인" onclick="CheckCode()" class="btn_input" style="display:none;">
					<?php
					}else{
					?>
					<?=$PieceCode?>
					<input type="hidden" id="PieceCode" name="PieceCode"  value="<?=$PieceCode?>"/>
					<?php
					}
					?>
				</td>
			  </tr>

			  <tr>
				<th width="15%">피스명</th>
				<td><input type="text" id="PieceName" name="PieceName" value="<?=$PieceName?>"/></td>
			  </tr>
			  

			  <tr>
				<th width="15%">피스레이아웃</th>
				<td>
				    <textarea id="PieceLayout" name="PieceLayout" cols="50" rows="12" class="editor"><?=$PieceLayout?></textarea>
					<script>
					var editor = CodeMirror.fromTextArea(document.getElementById("PieceLayout"), {
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

					<style>

					</style>

				</td>
			  </tr>
			  		  
			  
			  <tr>
				<th width="15%">상태</th>
				<td>
					<input type="radio" name="PieceState" value="1" <?php if ($PieceState==1) {echo ("checked");}?>><label>승인</label> 
					<input type="radio" name="PieceState" value="0" <?php if ($PieceState==0) {echo ("checked");}?>><label>미승인</label>
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

    var NewCode = $.trim($('#PieceCode').val());

    if (NewCode == "") {
        alert('피스코드를 입력하세요.');
        document.TheForm.CheckedCode.value = "0";
    } else {
        url = "ajax_check_piece_code.php";

		//location.href = url +"?NewCode="+NewCode;
        $.ajax(url, {
            data: {
                NewCode: NewCode
            },
            success: function (data) {
                if (data == "1") {
                    alert('사용 가능한 피스코드 입니다.');
                    document.RegForm.CheckedCode.value = "1";
					document.getElementById("BtnCodeCheck").style.display = "none";
                }
                else {
                    alert('이미 사용중인 피스코드 입니다.');
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
	
	obj = document.RegForm.PieceCode;
	if (obj.value==""){
		alert('피스코드를 입력하세요.');
		obj.focus();
		return;
	}


	<?php
	if ($NewData=="1"){
	?>

	obj = document.RegForm.CheckedCode;
	if (obj.value=="0"){
		alert('피스코드 중복확인을 진행하세요.');
		obj.focus();
		return;
	}

	<?
	}
	?>
	

	obj = document.RegForm.PieceName;
	if (obj.value==""){
		alert('피스명을 입력하세요.');
		obj.focus();
		return;
	}


	document.RegForm.action = "piece_action.php";
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







