<?php
$top_menu_id = 4;
$left_menu_id = 4;
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
		<h2>서브페이지설정</h2>
		<?php
		$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
		$PageID = isset($_REQUEST["PageID"]) ? $_REQUEST["PageID"] : "";



		if ($PageID!=""){
			$Sql = "select * from Pages where PageID=:PageID";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':PageID', $PageID);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);
			$Row = $Stmt->fetch();
			$Stmt = null;

			$UseMain = $Row["UseMain"];
			$UseSub = $Row["UseSub"];
			$PageLevel = $Row["PageLevel"];
			$SubID = $Row["SubID"];
			$PageID = $Row["PageID"];
			$PageCode = $Row["PageCode"];			
			$PageName = $Row["PageName"];
			$PageContent = $Row["PageContent"];
			$PageContentCss = $Row["PageContentCss"];
			$PageContentJavascript = $Row["PageContentJavascript"];
			$PageState = $Row["PageState"];

			$CheckedCode = "1";
			$NewData = "0";
		}else{


			$UseMain = 0;
			$UseSub = 1;
			$PageLevel = 20;
			$SubID = "";
			$PageCode = "";
			$PageName = "";
			$PageContent = "";
			$PageContentCss = "";
			$PageContentJavascript = "";
			$PageState = 1; 

			$CheckedCode = "0";
			$NewData = "1";

		}
		
		?>
		<div class="box">
			<form id="form" name="RegForm" method="post" enctype="multipart/form-data" accept-charset="UTF-8">
			<input type="hidden" name="PageID" value="<?=$PageID?>">
			<input type="hidden" name="ListParam" value="<?=$ListParam?>">
			<input type="hidden" id="CheckedCode" name="CheckedCode" value="<?=$CheckedCode?>">
			<input type="hidden" name="NewData" value="<?=$NewData?>">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table3">		  
			  
			  
			  <tr>
				<th width="15%">소속서브</th>
				<td>
					<select name="SubID">
					<option value="">::서브선택::</option>
					<?php
					$Sql = "select * from Subs order by SubName asc";
					$Stmt = $DbConn->prepare($Sql);
					$Stmt->execute();
					$Stmt->setFetchMode(PDO::FETCH_ASSOC);

					while($Row = $Stmt->fetch()) {
					?>
					<option value="<?=$Row["SubID"]?>" <?php if ($SubID==$Row["SubID"]) { echo ("selected"); }?>><?=$Row["SubName"]?></option>
					<?php
					}
					$Stmt = null;
					?>
					</select>
				</td>
			  </tr>

			  <tr>
				<th width="15%">페이지코드</th>
				<td>
					<?php
					if ($NewData=="1"){
					?>
					<input type="text" id="PageCode" name="PageCode"  value="<?=$PageCode?>" onkeyup="EnNewCode()" style="ime-mode:disabled;"/>
					<input id="BtnCodeCheck" type="button" name="button" value="중복확인" onclick="CheckCode()" class="btn_input" style="display:none;">
					<?php
					}else{
					?>
					<!--<?=$PageCode?>-->
					<input type="text" id="PageCode" name="PageCode"  value="<?=$PageCode?>"/>
					<?php
					}
					?>
				</td>
			  </tr>

			  <tr>
				<th width="15%">페이지권한</th>
				<td>
					<select name="PageLevel">
					<option value="20">비회원</option>
					<?php
					$Sql = "select * from MemberLevels where UseMemberForm=1 order by MemberLevelID desc";
					$Stmt = $DbConn->prepare($Sql);
					$Stmt->execute();
					$Stmt->setFetchMode(PDO::FETCH_ASSOC);

					while($Row = $Stmt->fetch()) {
					?>
					<option value="<?=$Row["MemberLevelID"]?>" <?php if ($PageLevel==$Row["MemberLevelID"]) { echo ("selected"); }?>><?=$Row["MemberLevelName"]?></option>
					<?php
					}
					$Stmt = null;
					?>
					</select>
				</td>
			  </tr>

			  <tr>
				<th width="15%">페이지명</th>
				<td><input type="text" id="PageName" name="PageName" value="<?=$PageName?>"/></td>
			  </tr>

			  <tr>
				<th width="15%">메인레이아웃</th>
				<td>
					<input type="radio" name="UseMain" value="1" <?php if ($UseMain==1) {echo ("checked");}?>><label>사용</label> 
					<input type="radio" name="UseMain" value="0" <?php if ($UseMain==0) {echo ("checked");}?>><label>미사용</label>
				</td>
			  </tr>
			  <tr>
				<th width="15%">서브레이아웃</th>
				<td>
					<input type="radio" name="UseSub" value="1" <?php if ($UseSub==1) {echo ("checked");}?>><label>사용</label> 
					<input type="radio" name="UseSub" value="0" <?php if ($UseSub==0) {echo ("checked");}?>><label>미사용</label>
				</td>
			  </tr>
			  

			  <tr>
				<th width="15%">페이지 HTML</th>
				<td>
				    <textarea id="PageContent" name="PageContent" cols="50" rows="12" class="editor"><?=$PageContent?></textarea>
					<script>
					var editor = CodeMirror.fromTextArea(document.getElementById("PageContent"), {
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
					<textarea id="PageContentCss" name="PageContentCss" cols="50" rows="12" class="editor"><?=$PageContentCss?></textarea>
					&lt;/style&gt;
					<script>
					var editor = CodeMirror.fromTextArea(document.getElementById("PageContentCss"), {
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
					<textarea id="PageContentJavascript" name="PageContentJavascript" cols="50" rows="12" class="editor"><?=$PageContentJavascript?></textarea>
					&lt;/script&gt;
					<script>
					var editor = CodeMirror.fromTextArea(document.getElementById("PageContentJavascript"), {
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
					<input type="radio" name="PageState" value="1" <?php if ($PageState==1) {echo ("checked");}?>><label>승인</label> 
					<input type="radio" name="PageState" value="0" <?php if ($PageState==0) {echo ("checked");}?>><label>미승인</label>
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

    var NewCode = $.trim($('#PageCode').val());

    if (NewCode == "") {
        alert('페이지코드를 입력하세요.');
        document.TheForm.CheckedCode.value = "0";
    } else {
        url = "ajax_check_page_code.php";
        
		//location.href = url + "?NewCode="+NewCode;
		
		$.ajax(url, {
            data: {
                NewCode: NewCode
            },
            success: function (data) {
                //alert(data);
				
				if (data == "1") {
                    alert('사용 가능한 페이지코드 입니다.');
                    document.RegForm.CheckedCode.value = "1";
					document.getElementById("BtnCodeCheck").style.display = "none";
                }
                else {
                    alert('이미 사용중인 페이지코드 입니다.');
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

	obj = document.RegForm.SubID;
	if (obj.value==""){
		alert('소속서브를 선택하세요.');
		obj.focus();
		return;
	}	

	obj = document.RegForm.PageCode;
	if (obj.value==""){
		alert('페이지코드를 입력하세요.');
		obj.focus();
		return;
	}


	<?php
	if ($NewData=="1"){
	?>

	obj = document.RegForm.CheckedCode;
	if (obj.value=="0"){
		alert('페이지코드 중복확인을 진행하세요.');
		obj.focus();
		return;
	}

	<?
	}
	?>
	

	obj = document.RegForm.PageName;
	if (obj.value==""){
		alert('페이지명을 입력하세요.');
		obj.focus();
		return;
	}

	document.RegForm.action = "sub_page_action.php";
	document.RegForm.submit();
}
</script>
<script>
$(function(){
	$(document).on("keyup", "input:text[numberOnly]", function() {$(this).val( $(this).val().replace(/[^0-9]/gi,"") );});
	$(document).on("keyup", "input:text[datetimeOnly]", function() {$(this).val( $(this).val().replace(/[^0-9:\-]/gi,"") );});
});
</script>



<?php
include_once('./_bottom.php');
?>
</body>
<?php
include_once('./_footer.php');
include_once('../includes/dbclose.php');
?>







