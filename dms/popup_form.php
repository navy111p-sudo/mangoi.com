<?php
$top_menu_id = 2;
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
		<h2>팝업설정</h2>
		<?php
		$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
		$PopupID = isset($_REQUEST["popupid"]) ? $_REQUEST["popupid"] : "";



		if ($PopupID!=""){
			$Sql = "select * from Popups where PopupID=:PopupID";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':PopupID', $PopupID);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);
			$Row = $Stmt->fetch();
			$Stmt = null;

			$PopupName = $Row["PopupName"];
			$PopupTitle = $Row["PopupTitle"];
			$PopupStartDateNum = $Row["PopupStartDateNum"];
			$PopupEndDateNum = $Row["PopupEndDateNum"];
			$PopupWidth = $Row["PopupWidth"];
			$PopupHeight = $Row["PopupHeight"];
			$PopupTop = $Row["PopupTop"];
			$PopupLeft = $Row["PopupLeft"];
			$PopupType = $Row["PopupType"];
			$PopupContent = $Row["PopupContent"];
			$PopupImage = $Row["PopupImage"];
			$PopupState = $Row["PopupState"];
			$NewData = "0";

			$PopupName = str_replace('"', "&#34;", $PopupName);
			$PopupTitle = str_replace('"', "&#34;", $PopupTitle);

		}else{
			$PopupName = "";
			$PopupTitle = "";
			$PopupStartDateNum = date("Y-m-d");
			$PopupEndDateNum = date("Y-m-d");
			$PopupWidth = 500;
			$PopupHeight = 500;
			$PopupTop = 0;
			$PopupLeft = 0;
			$PopupType = 1;
			$PopupContent = "";
			$PopupImage = "";
			$PopupState = 0;
			$NewData = "1";

		}
		
		?>
		<div class="box">
			<form id="form" name="RegForm" method="post" enctype="multipart/form-data" accept-charset="UTF-8">
			<input type="hidden" name="PopupID" value="<?=$PopupID?>">
			<input type="hidden" name="ListParam" value="<?=$ListParam?>">
			<input type="hidden" name="NewData" value="<?=$NewData?>">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table3">
			  <tr>
				<th width="15%">팝업명</th>
				<td><input type="text" id="PopupName" name="PopupName" value="<?=$PopupName?>" style="width:300px;"/></td>
			  </tr>
			  <tr>
				<th width="15%">팝업타이틀</th>
				<td><input type="text" id="PopupTitle" name="PopupTitle"  value="<?=$PopupTitle?>" style="width:300px;"/></td>
			  </tr>
			  <tr>
				<th width="15%">기간</th>
				<td>
					<label>시작날짜 :</label> <input type="text" id="PopupStartDateNum" name="PopupStartDateNum"  value="<?=$PopupStartDateNum?>" style="width:100px;"/>
					<label>종료날짜 :</label> <input type="text" id="PopupEndDateNum" name="PopupEndDateNum"  value="<?=$PopupEndDateNum?>" style="width:100px;"/>
					<script>
						$(document).ready(function() {
							$("#PopupStartDateNum").kendoDatePicker({
								format: "yyyy-MM-dd",
								culture: "ko-KR"
							});
							$("#PopupEndDateNum").kendoDatePicker({
								format: "yyyy-MM-dd",
								culture: "ko-KR"
							});
						});
					</script>

				</td>
			  </tr>
			  <tr>
				<th width="15%">사이즈</th>
				<td>
					<label>가로사이즈 :</label> <input type="text" id="PopupWidth" name="PopupWidth"  value="<?=$PopupWidth?>" style="width:30px;text-align:center;"/>
					<label>세로사이즈 :</label> <input type="text" id="PopupHeight" name="PopupHeight"  value="<?=$PopupHeight?>" style="width:30px;text-align:center;"/>
				</td>
			  </tr>
			  <tr>
				<th width="15%">위치</th>
				<td>
					<label>상단위치 :</label> <input type="text" id="PopupTop" name="PopupTop"  value="<?=$PopupTop?>" style="width:30px;text-align:center;"/>
					<label>좌측위치 :</label> <input type="text" id="PopupLeft" name="PopupLeft"  value="<?=$PopupLeft?>" style="width:30px;text-align:center;"/>
				</td>
			  </tr>
			  <tr>
				<th width="15%">팝업타입</th>
				<td>
					<input type="radio" name="PopupType" value="1" <?php if ($PopupType==1) { echo "checked";}?> onclick="CheckPopupType(1);">이미지 
					<input type="radio" name="PopupType" value="2" <?php if ($PopupType==2) { echo "checked";}?> onclick="CheckPopupType(2);">텍스트 
				</td>
			  </tr>
			  <tr>
				<th width="15%">내용</th>
				<td>
					
					<div id="DivPopupType1" style="display:<?php if ($PopupType==1) { echo "inline;";} else{ echo "none;";}?>">
					<input type="file" name="PopupImage">
					</div>
					<div id="DivPopupType2" style="display:<?php if ($PopupType==2) { echo "inline;";} else{ echo "none;";}?>">
					<textarea id="PopupContent" name="PopupContent" cols="100" rows="12" style="margin-top:5px;margin-bottom:5px;"><?=$PopupContent?></textarea>
					
					</div>
					
					<script>
					function CheckPopupType(type){
						if (type==1){
							document.getElementById('DivPopupType2').style.display = "none";
							document.getElementById('DivPopupType1').style.display = "inline";
						}else{
							document.getElementById('DivPopupType1').style.display = "none";
							document.getElementById('DivPopupType2').style.display = "inline";
						}
					}
					</script>
					
					
				</td>
			  </tr>
			  <tr>
				<th width="15%">상태</th>
				<td>
					<input type="radio" name="PopupState" value="1" <?php if ($PopupState==1) {echo ("checked");}?>><label>승인</label> 
					<input type="radio" name="PopupState" value="0" <?php if ($PopupState==0) {echo ("checked");}?>><label>미승인</label>
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
	obj = document.RegForm.PopupName;
	if (obj.value==""){
		alert('팝업명을 입력하세요.');
		obj.focus();
		return;
	}

	obj = document.RegForm.PopupTitle;
	if (obj.value==""){
		alert('팝업타이틀을 입력하세요.');
		obj.focus();
		return;
	}

	document.RegForm.action = "popup_action.php";
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







