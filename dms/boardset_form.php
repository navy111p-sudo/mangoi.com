<?php
$top_menu_id = 5;
$left_menu_id = 5;
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
		<h2>게시판설정</h2>
		<?php
		$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
		$BoardID = isset($_REQUEST["BoardID"]) ? $_REQUEST["BoardID"] : "";


		if ($BoardID!=""){
			$Sql = "select * from Boards where BoardID=:BoardID";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':BoardID', $BoardID);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);
			$Row = $Stmt->fetch();
			$Stmt = null;


			$UseMain = $Row["UseMain"];
			$UseSub = $Row["UseSub"];
			$SubID = $Row["SubID"];
			$BoardID = $Row["BoardID"];
			$BoardCode = $Row["BoardCode"];			
			$BoardLayout = $Row["BoardLayout"];
			$BoardCss = $Row["BoardCss"];
			$BoardJavascript = $Row["BoardJavascript"];
			$BoardName = $Row["BoardName"];
			$BoardTitle = $Row["BoardTitle"];
			$BoardListRowNum = $Row["BoardListRowNum"];
			$BoardEnableCategory = $Row["BoardEnableCategory"];
			$BoardEnableReplay = $Row["BoardEnableReplay"];
			$BoardEnableComment = $Row["BoardEnableComment"];
			$BoardEnableSecret = $Row["BoardEnableSecret"];
			$BoardFileCount = $Row["BoardFileCount"];
			$BoardListLevel = $Row["BoardListLevel"];
			$BoardReadLevel = $Row["BoardReadLevel"];
			$BoardWriteLevel = $Row["BoardWriteLevel"];
			$BoardReplyLevel = $Row["BoardReplyLevel"];
			$BoardNoticeLevel = $Row["BoardNoticeLevel"];
			$BoardCommentLevel = $Row["BoardCommentLevel"];
			$BoardSecretReadLevel = $Row["BoardSecretReadLevel"];
			$BoardModifyLevel = $Row["BoardModifyLevel"];
			$BoardRegDateTime = $Row["BoardRegDateTime"];
			$BoardState = $Row["BoardState"];

			$CheckedCode = "1";
			$NewData = "0";
		}else{


			$UseMain = 0;
			$UseSub = 1;
			$SubID = "";
			$BoardCode = "";
			$BoardLayout = "{{Board}}";
			$BoardCss = "";
			$BoardJavascript = "";
			$BoardName = "";
			$BoardTitle = "";
			$BoardListRowNum = 10;
			$BoardEnableCategory = 0;
			$BoardEnableReplay = 1;
			$BoardEnableComment = 0;
			$BoardEnableSecret = 0;
			$BoardFileCount = 0;
			$BoardListLevel = 20;
			$BoardReadLevel = 20;
			$BoardWriteLevel = 20;
			$BoardReplyLevel = 20;
			$BoardNoticeLevel = 0;
			$BoardCommentLevel = 20;
			$BoardSecretReadLevel = 0;
			$BoardModifyLevel = 0;
			$BoardState = 1;

			$CheckedCode = "0";
			$NewData = "1";

		}
		
		?>
		<div class="box">
			<form id="form" name="RegForm" method="post" enctype="multipart/form-data" accept-charset="UTF-8">
			<input type="hidden" name="BoardID" value="<?=$BoardID?>">
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
				<th width="15%">게시판코드</th>
				<td>
					<?php
					if ($NewData=="1"){
					?>
					<input type="text" id="BoardCode" name="BoardCode"  value="<?=$BoardCode?>" onkeyup="EnNewCode()" style="ime-mode:disabled;"/>
					<input id="BtnCodeCheck" type="button" name="button" value="중복확인" onclick="CheckCode()" class="btn_input" style="display:none;">
					<?php
					}else{
					?>
					<?=$BoardCode?>
					<input type="hidden" id="BoardCode" name="BoardCode"  value="<?=$BoardCode?>"/>
					<?php
					}
					?>
				</td>
			  </tr>

			  <tr>
				<th width="15%">게시판명</th>
				<td><input type="text" id="BoardName" name="BoardName" value="<?=$BoardName?>"/></td>
			  </tr>
			  <tr>
				<th width="15%">게시판타이틀</th>
				<td><input type="text" id="BoardTitle" name="BoardTitle"  value="<?=$BoardTitle?>"/></td>
			  </tr>
			  <tr>
				<th width="15%">리스트수</th>
				<td><input type="text" id="BoardListRowNum" name="BoardListRowNum"  value="<?=$BoardListRowNum?>" style="width:30px;" numberonly="true"/>줄</td>
			  </tr>
			  <tr>
				<th width="15%">카테고리기능</th>
				<td>
					<input type="radio" name="BoardEnableCategory" value="1" <?php if ($BoardEnableCategory==1) {echo ("checked");}?>><label>사용</label> 
					<input type="radio" name="BoardEnableCategory" value="0" <?php if ($BoardEnableCategory==0) {echo ("checked");}?>><label>미사용</label>

					<?php 
					if ($BoardEnableCategory==1) {
					?>
					<a href="javascript:OpenBoardCategoryList(<?=$BoardID?>);"><img src="images/btn_category.png" style="vertical-align:middle;"></a>
					<?
					}
					?>
				</td>
			  </tr>
			  <tr>
				<th width="15%">답변기능</th>
				<td>
					<input type="radio" name="BoardEnableReplay" value="1" <?php if ($BoardEnableReplay==1) {echo ("checked");}?>><label>사용</label> 
					<input type="radio" name="BoardEnableReplay" value="0" <?php if ($BoardEnableReplay==0) {echo ("checked");}?>><label>미사용</label>
				</td>
			  </tr>
			  <tr>
				<th width="15%">코멘트기능</th>
				<td>
					<input type="radio" name="BoardEnableComment" value="1" <?php if ($BoardEnableComment==1) {echo ("checked");}?>><label>사용</label> 
					<input type="radio" name="BoardEnableComment" value="0" <?php if ($BoardEnableComment==0) {echo ("checked");}?>><label>미사용</label>
				</td>
			  </tr>
			  <tr>
				<th width="15%">비밀글기능</th>
				<td>
					<input type="radio" name="BoardEnableSecret" value="1" <?php if ($BoardEnableSecret==1) {echo ("checked");}?>><label>사용</label> 
					<input type="radio" name="BoardEnableSecret" value="0" <?php if ($BoardEnableSecret==0) {echo ("checked");}?>><label>미사용</label>
				</td>
			  </tr>

			  <tr>
				<th width="15%">첨부파일수</th>
				<td>
					<select name="BoardFileCount">
					<option value="0">없음</option>
					<?php
					for ($i=1;$i<=10;$i++){
					?>
					<option value="<?=$i?>" <?php if ($BoardFileCount==$i) { echo ("selected"); }?>><?=$i?></option>
					<?php
					}
					?>
					</select>
				</td>
			  </tr>

			  
			  <tr>
				<th width="15%">목록권한</th>
				<td>
					<select name="BoardListLevel">
					<option value="20">비회원</option>
					<?php
					$Sql = "select * from MemberLevels where UseMemberForm=1 order by MemberLevelID desc";
					$Stmt = $DbConn->prepare($Sql);
					$Stmt->execute();
					$Stmt->setFetchMode(PDO::FETCH_ASSOC);

					while($Row = $Stmt->fetch()) {
					?>
					<option value="<?=$Row["MemberLevelID"]?>" <?php if ($BoardListLevel==$Row["MemberLevelID"]) { echo ("selected"); }?>><?=$Row["MemberLevelName"]?></option>
					<?php
					}
					$Stmt = null;
					?>
					</select>
				</td>
			  </tr>

			  <tr>
				<th width="15%">읽기권한</th>
				<td>
					<select name="BoardReadLevel">
					<option value="20">비회원</option>
					<?php
					$Sql = "select * from MemberLevels where UseMemberForm=1 order by MemberLevelID desc";
					$Stmt = $DbConn->prepare($Sql);
					$Stmt->execute();
					$Stmt->setFetchMode(PDO::FETCH_ASSOC);

					while($Row = $Stmt->fetch()) {
					?>
					<option value="<?=$Row["MemberLevelID"]?>" <?php if ($BoardReadLevel==$Row["MemberLevelID"]) { echo ("selected"); }?>><?=$Row["MemberLevelName"]?></option>
					<?php
					}
					$Stmt = null;
					?>
					</select>
				</td>
			  </tr>

			  <tr>
				<th width="15%">쓰기권한</th>
				<td>
					<select name="BoardWriteLevel">
					<option value="20">비회원</option>
					<?php
					$Sql = "select * from MemberLevels where UseMemberForm=1 order by MemberLevelID desc";
					$Stmt = $DbConn->prepare($Sql);
					$Stmt->execute();
					$Stmt->setFetchMode(PDO::FETCH_ASSOC);

					while($Row = $Stmt->fetch()) {
					?>
					<option value="<?=$Row["MemberLevelID"]?>" <?php if ($BoardWriteLevel==$Row["MemberLevelID"]) { echo ("selected"); }?>><?=$Row["MemberLevelName"]?></option>
					<?php
					}
					$Stmt = null;
					?>
					</select>
				</td>
			  </tr>

			  <tr>
				<th width="15%">답변권한</th>
				<td>
					<select name="BoardReplyLevel">
					<option value="20">비회원</option>
					<?php
					$Sql = "select * from MemberLevels where UseMemberForm=1 order by MemberLevelID desc";
					$Stmt = $DbConn->prepare($Sql);
					$Stmt->execute();
					$Stmt->setFetchMode(PDO::FETCH_ASSOC);

					while($Row = $Stmt->fetch()) {
					?>
					<option value="<?=$Row["MemberLevelID"]?>" <?php if ($BoardReplyLevel==$Row["MemberLevelID"]) { echo ("selected"); }?>><?=$Row["MemberLevelName"]?></option>
					<?php
					}
					$Stmt = null;
					?>
					</select>
				</td>
			  </tr>

			  <tr>
				<th width="15%">공지글권한</th>
				<td>
					<select name="BoardNoticeLevel">
					<option value="20">비회원</option>
					<?php
					$Sql = "select * from MemberLevels where UseMemberForm=1 order by MemberLevelID desc";
					$Stmt = $DbConn->prepare($Sql);
					$Stmt->execute();
					$Stmt->setFetchMode(PDO::FETCH_ASSOC);

					while($Row = $Stmt->fetch()) {
					?>
					<option value="<?=$Row["MemberLevelID"]?>" <?php if ($BoardNoticeLevel==$Row["MemberLevelID"]) { echo ("selected"); }?>><?=$Row["MemberLevelName"]?></option>
					<?php
					}
					$Stmt = null;
					?>
					</select>
				</td>
			  </tr>
			  
			  <tr>
				<th width="15%">코멘트권한</th>
				<td>
					<select name="BoardCommentLevel">
					<option value="20">비회원</option>
					<?php
					$Sql = "select * from MemberLevels where UseMemberForm=1 order by MemberLevelID desc";
					$Stmt = $DbConn->prepare($Sql);
					$Stmt->execute();
					$Stmt->setFetchMode(PDO::FETCH_ASSOC);

					while($Row = $Stmt->fetch()) {
					?>
					<option value="<?=$Row["MemberLevelID"]?>" <?php if ($BoardCommentLevel==$Row["MemberLevelID"]) { echo ("selected"); }?>><?=$Row["MemberLevelName"]?></option>
					<?php
					}
					$Stmt = null;
					?>
					</select>
				</td>
			  </tr>

			  <tr>
				<th width="15%">비밀글읽기권한</th>
				<td>
					<select name="BoardSecretReadLevel">
					<option value="20">비회원</option>
					<?php
					$Sql = "select * from MemberLevels where UseMemberForm=1 order by MemberLevelID desc";
					$Stmt = $DbConn->prepare($Sql);
					$Stmt->execute();
					$Stmt->setFetchMode(PDO::FETCH_ASSOC);

					while($Row = $Stmt->fetch()) {
					?>
					<option value="<?=$Row["MemberLevelID"]?>" <?php if ($BoardSecretReadLevel==$Row["MemberLevelID"]) { echo ("selected"); }?>><?=$Row["MemberLevelName"]?></option>
					<?php
					}
					$Stmt = null;
					?>
					</select>
				</td>
			  </tr>
			  <tr>
				<th width="15%">게시글수정/삭제권한</th>
				<td>
					<select name="BoardModifyLevel">
					<option value="20">비회원</option>
					<?php
					$Sql = "select * from MemberLevels where UseMemberForm=1 order by MemberLevelID desc";
					$Stmt = $DbConn->prepare($Sql);
					$Stmt->execute();
					$Stmt->setFetchMode(PDO::FETCH_ASSOC);

					while($Row = $Stmt->fetch()) {
					?>
					<option value="<?=$Row["MemberLevelID"]?>" <?php if ($BoardModifyLevel==$Row["MemberLevelID"]) { echo ("selected"); }?>><?=$Row["MemberLevelName"]?></option>
					<?php
					}
					$Stmt = null;
					?>
					</select>
				</td>
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
				<th width="15%">게시판레이아웃</th>
				<td>
				    <textarea id="BoardLayout" name="BoardLayout" cols="50" rows="12" class="editor"><?=$BoardLayout?></textarea>
					{{board}} 코드 위치에 게시판이 삽입됩니다. {{Board}} 코드는 반드시 1개가 있어야 합니다.
					<script>
					var editor = CodeMirror.fromTextArea(document.getElementById("BoardLayout"), {
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
				
					</script>

				</td>
			  </tr>
			  <tr>
				<th width="15%">CSS</th>
				<td>
					&lt;style&gt;
					<textarea id="BoardCss" name="BoardCss" cols="50" rows="12" class="editor"><?=$BoardCss?></textarea>
					&lt;/style&gt;
					<script>
					var editor = CodeMirror.fromTextArea(document.getElementById("BoardCss"), {
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

					editor.setSize(null, 150);
				
					</script>

				</td>
			  </tr>
			  <tr>
				<th width="15%">javascript</th>
				<td>
				    &lt;script&gt;
					<textarea id="BoardJavascript" name="BoardJavascript" cols="50" rows="12" class="editor"><?=$BoardJavascript?></textarea>
					&lt;/script&gt;
					<script>
					var editor = CodeMirror.fromTextArea(document.getElementById("BoardJavascript"), {
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
				
					editor.setSize(null, 150);

					</script>

				</td>
			  </tr>
			  
			  
			  <tr>
				<th width="15%">상태</th>
				<td>
					<input type="radio" name="BoardState" value="1" <?php if ($BoardState==1) {echo ("checked");}?>><label>승인</label> 
					<input type="radio" name="BoardState" value="0" <?php if ($BoardState==0) {echo ("checked");}?>><label>미승인</label>
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


function OpenBoardCategoryList(BoardID){
	window.open("board_category_list.php?BoardID="+BoardID,'pop_board_category','width=500,height=500,toolbar=no,top=0,left=0, scrollbars=yes');
}



function EnNewCode(){
	document.RegForm.CheckedCode.value = "0";
	document.getElementById("BtnCodeCheck").style.display = "inline";
}


function CheckCode() {

    var NewCode = $.trim($('#BoardCode').val());

    if (NewCode == "") {
        alert('게시판코드를 입력하세요.');
        document.TheForm.CheckedCode.value = "0";
    } else {
        url = "ajax_check_board_code.php";
        $.ajax(url, {
            data: {
                NewCode: NewCode
            },
            success: function (data) {
                if (data == "1") {
                    alert('사용 가능한 게시판코드 입니다.');
                    document.RegForm.CheckedCode.value = "1";
					document.getElementById("BtnCodeCheck").style.display = "none";
                }
                else {
                    alert('이미 사용중인 게시판코드 입니다.');
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
	
	obj = document.RegForm.BoardCode;
	if (obj.value==""){
		alert('게시판코드를 입력하세요.');
		obj.focus();
		return;
	}


	<?php
	if ($NewData=="1"){
	?>

	obj = document.RegForm.CheckedCode;
	if (obj.value=="0"){
		alert('게시판코드 중복확인을 진행하세요.');
		obj.focus();
		return;
	}

	<?
	}
	?>
	

	obj = document.RegForm.BoardName;
	if (obj.value==""){
		alert('게시판명을 입력하세요.');
		obj.focus();
		return;
	}

	obj = document.RegForm.BoardTitle;
	if (obj.value==""){
		alert('게시판 타이틀을 입력하세요.');
		obj.focus();
		return;
	}


	document.RegForm.action = "boardset_action.php";
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







