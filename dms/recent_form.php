<?php
$top_menu_id = 4;
$left_menu_id = 7;
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
		<h2>최근목록설정</h2>
		<?php
		$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
		$RecentID = isset($_REQUEST["RecentID"]) ? $_REQUEST["RecentID"] : "";



		if ($RecentID!=""){
			$Sql = "select * from Recents where RecentID=:RecentID";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':RecentID', $RecentID);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);
			$Row = $Stmt->fetch();
			$Stmt = null;

			$RecentID = $Row["RecentID"];
			$RecentCode = $Row["RecentCode"];
			$RecentName = $Row["RecentName"];
			$RecentLayout = $Row["RecentLayout"];
			$RecentState = $Row["RecentState"];

			$CheckedCode = "1";
			$NewData = "0";
		}else{

			$RecentState = 1;
			$CheckedCode = "0";
			$NewData = "1";

		}
		
		?>
		<div class="box">
			<form id="form" name="RegForm" method="post" enctype="multipart/form-data" accept-charset="UTF-8">
			<input type="hidden" name="RecentID" value="<?=$RecentID?>">
			<input type="hidden" name="ListParam" value="<?=$ListParam?>">
			<input type="hidden" id="CheckedCode" name="CheckedCode" value="<?=$CheckedCode?>">
			<input type="hidden" name="NewData" value="<?=$NewData?>">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table3">		  
			  

			  <tr>
				<th width="15%">최근목록코드</th>
				<td>
					<?php
					if ($NewData=="1"){
					?>
					<input type="text" id="RecentCode" name="RecentCode"  value="<?=$RecentCode?>" onkeyup="EnNewCode()" style="ime-mode:disabled;"/>
					<input id="BtnCodeCheck" type="button" name="button" value="중복확인" onclick="CheckCode()" class="btn_input" style="display:none;">
					<?php
					}else{
					?>
					<?=$RecentCode?>
					<input type="hidden" id="RecentCode" name="RecentCode"  value="<?=$RecentCode?>"/>
					<?php
					}
					?>
				</td>
			  </tr>

			  <tr>
				<th width="15%">최근목록명</th>
				<td><input type="text" id="RecentName" name="RecentName" value="<?=$RecentName?>"/></td>
			  </tr>
			  

			  <tr>
				<th width="15%">최근목록레이아웃</th>
				<td>
				    <textarea id="RecentLayout" name="RecentLayout" cols="50" rows="12" class="editor"><?=$RecentLayout?></textarea>
					<script>
					var editor = CodeMirror.fromTextArea(document.getElementById("RecentLayout"), {
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
				<th width="15%">상태</th>
				<td>
					<input type="radio" name="RecentState" value="1" <?php if ($RecentState==1) {echo ("checked");}?>><label>승인</label> 
					<input type="radio" name="RecentState" value="0" <?php if ($RecentState==0) {echo ("checked");}?>><label>미승인</label>
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

    var NewCode = $.trim($('#RecentCode').val());

    if (NewCode == "") {
        alert('최근목록코드를 입력하세요.');
        document.TheForm.CheckedCode.value = "0";
    } else {
        url = "ajax_check_recent_code.php";
        $.ajax(url, {
            data: {
                NewCode: NewCode
            },
            success: function (data) {
                if (data == "1") {
                    alert('사용 가능한 최근목록코드 입니다.');
                    document.RegForm.CheckedCode.value = "1";
					document.getElementById("BtnCodeCheck").style.display = "none";
                }
                else {
                    alert('이미 사용중인 최근목록코드 입니다.');
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
	
	obj = document.RegForm.RecentCode;
	if (obj.value==""){
		alert('최근목록코드를 입력하세요.');
		obj.focus();
		return;
	}


	<?php
	if ($NewData=="1"){
	?>

	obj = document.RegForm.CheckedCode;
	if (obj.value=="0"){
		alert('최근목록코드 중복확인을 진행하세요.');
		obj.focus();
		return;
	}

	<?
	}
	?>
	

	obj = document.RegForm.RecentName;
	if (obj.value==""){
		alert('최근목록명을 입력하세요.');
		obj.focus();
		return;
	}


	document.RegForm.action = "recent_action.php";
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







