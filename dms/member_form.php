<?php
$top_menu_id = 3;
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
		<h2>팝업설정</h2>
		<?php
		$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
		$MemberID = isset($_REQUEST["memberid"]) ? $_REQUEST["memberid"] : "";



		if ($MemberID!=""){
			$Sql = "select * from Members where MemberID=:MemberID";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':MemberID', $MemberID);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);
			$Row = $Stmt->fetch();
			$Stmt = null;

			$MemberLoginID = $Row["MemberLoginID"];
			$MemberLoginPW = $Row["MemberLoginPW"];
			$MemberLevelID = $Row["MemberLevelID"];
			$MemberName = $Row["MemberName"];
			$MemberNickName = $Row["MemberNickName"];			
			$MemberBirthday = $Row["MemberBirthday"];
			$MemberPhone1 = $Row["MemberPhone1"];
			$MemberPhone2 = $Row["MemberPhone2"];
			$MemberPhone3 = $Row["MemberPhone3"];
			$MemberZip = $Row["MemberZip"];
			$MemberAddr = $Row["MemberAddr"];
			$MemberAddrDetail = $Row["MemberAddrDetail"];
			$MemberOldAddr1 = $Row["MemberOldAddr1"];
			$MemberOldAddr2 = $Row["MemberOldAddr2"];
			$MemberExp01 = $Row["MemberExp01"];
			$MemberExp02 = $Row["MemberExp02"];
			$MemberExp03 = $Row["MemberExp03"];
			$MemberExp04 = $Row["MemberExp04"];
			$MemberExp05 = $Row["MemberExp05"];
			$MemberExp06 = $Row["MemberExp06"];
			$MemberExp07 = $Row["MemberExp07"];
			$MemberExp08 = $Row["MemberExp08"];
			$MemberExp09 = $Row["MemberExp09"];
			$MemberExp10 = $Row["MemberExp10"];
			$MemberState = $Row["MemberState"];			
			$MemberRegDateTime = $Row["MemberRegDateTime"];

			$NewData = "0";
			$CheckedID = "1";

		}else{

			$MemberLevelID = "";
			$MemberState = 1;
			$NewData = "1";
			$CheckedID = "0";

		}
		
		?>
		<div class="box">
			<form id="form" name="RegForm" method="post" enctype="multipart/form-data" accept-charset="UTF-8">
			<input type="hidden" name="MemberID" value="<?=$MemberID?>">
			<input type="hidden" name="ListParam" value="<?=$ListParam?>">
			<input type="hidden" name="NewData" value="<?=$NewData?>">
			<input type="hidden" name="MemberLoginPW" value="<?=$MemberLoginPW?>">
			<input type="hidden" id="CheckedID" name="CheckedID" value="<?=$CheckedID?>">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table3">		  
			  
			  <tr>
				<th width="15%">회원레벨</th>
				<td>
					<select name="MemberLevelID">
					<option value="">선택하세요</option>
					<?php
					$Sql = "select * from MemberLevels where UseMemberForm=1 order by MemberLevelID asc";
					$Stmt = $DbConn->prepare($Sql);
					$Stmt->execute();
					$Stmt->setFetchMode(PDO::FETCH_ASSOC);

					while($Row = $Stmt->fetch()) {
					?>
					<option value="<?=$Row["MemberLevelID"]?>" <?php if ($MemberLevelID==$Row["MemberLevelID"]) { echo ("selected"); }?>>[<?=$Row["MemberLevelID"]?>]<?=$Row["MemberLevelName"]?></option>
					<?php
					}
					$Stmt = null;
					?>
					</select>
				</td>
			  </tr>
			  <tr>
				<th width="15%">아이디</th>
				<td>
					<?php
					if ($NewData=="1"){
					?>
					<input type="text" id="MemberLoginID" name="MemberLoginID"  value="<?=$MemberLoginID?>" onkeyup="EnNewID()" style="ime-mode:disabled;"/>
					<input id="BtnIDCheck" type="button" name="button" value="중복확인" onclick="CheckID()" class="btn_input" style="display:none;">
					<?php
					}else{
					?>
					<?=$MemberLoginID?>
					<input type="hidden" id="MemberLoginID" name="MemberLoginID"  value="<?=$MemberLoginID?>"/>
					<?php
					}
					?>
				</td>
			  </tr>
			  <tr>
				<th width="15%">비밀번호</th>
				<td><input type="password" id="NewMemberLoginPW1" name="NewMemberLoginPW1"  value=""/></td>
			  </tr>
			  <tr>
				<th width="15%">비밀번호 확인</th>
				<td><input type="password" id="NewMemberLoginPW2" name="NewMemberLoginPW2"  value=""/></td>
			  </tr>
			  <tr>
				<th width="15%">회원명</th>
				<td><input type="text" id="MemberName" name="MemberName" value="<?=$MemberName?>"/></td>
			  </tr>
			  <tr style="display:none;">
				<th width="15%">닉네임</th>
				<td><input type="text" id="MemberNickName" name="MemberNickName"  value="<?=$MemberNickName?>"/></td>
			  </tr>
			  <tr>
				<th width="15%">생년월일</th>
				<td>
					<input type="text" id="MemberBirthday" name="MemberBirthday"  value="<?=$MemberBirthday?>" style="width:100px;"/>
					<script>
						$(document).ready(function() {
							$("#MemberBirthday").kendoDatePicker({
								format: "yyyy-MM-dd",
								culture: "ko-KR"
							});
						});
					</script>
				</td>
			  </tr>
			  <tr>
				<th width="15%">전화번호(1)</th>
				<td><input type="text" id="MemberPhone1" name="MemberPhone1"  value="<?=$MemberPhone1?>"/></td>
			  </tr>
			  <tr>
				<th width="15%">전화번호(2)</th>
				<td><input type="text" id="MemberPhone2" name="MemberPhone2"  value="<?=$MemberPhone2?>"/></td>
			  </tr>
			  <tr>
				<th width="15%">전화번호(3)</th>
				<td><input type="text" id="MemberPhone3" name="MemberPhone3"  value="<?=$MemberPhone3?>"/></td>
			  </tr>
			  <tr>
				<th width="15%">우편번호</th>
				<td><input type="text" id="ZipCode" name="ZipCode"  value="<?=$MemberZip?>" style="width:100px;"/>
				
				<a href="javascript:OpenZipCode();">우편번호찾기</a>
				
				</td>
			  </tr>
			  <tr>
				<th width="15%">구주소</th>
				<td>
				<input type="text" id="OldAddr1" name="OldAddr1"  value="<?=$MemberOldAddr1?>" style="width:400px;margin-bottom:5px;"/>
				<input type="text" id="OldAddr2" name="OldAddr2"  value="<?=$MemberOldAddr2?>" style="width:400px;margin-bottom:5px;"/>
				</td>
			  </tr>
			  
			  <tr>
				<th width="15%">새주소</th>
				<td><input type="text" id="Addr" name="Addr"  value="<?=$MemberAddr?>" style="width:400px;"/></td>
			  </tr>
			  <tr>
				<th width="15%">상세주소</th>
				<td><input type="text" id="AddrDetail" name="AddrDetail"  value="<?=$MemberAddrDetail?>" style="width:400px;"/></td>
			  </tr>
			  
		  
			  <tr>
				<th width="15%">상태</th>
				<td>
					<input type="radio" name="MemberState" value="1" <?php if ($MemberState==1) {echo ("checked");}?>><label>승인</label> 
					<input type="radio" name="MemberState" value="0" <?php if ($MemberState==0) {echo ("checked");}?>><label>미승인</label>
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

function OpenZipCode(){
window.open("../juso_new.php","findzipcode","width=511,height=575,scrollbars=0");
}


function EnNewID(){
	document.RegForm.CheckedID.value = "0";
	document.getElementById("BtnIDCheck").style.display = "inline";
}


function CheckID() {

    var NewID = $.trim($('#MemberLoginID').val());

    if (NewID == "") {
        alert('아이디를 입력하세요.');
        document.TheForm.CheckedID.value = "0";
    } else {
        url = "ajax_check_id.php";
        $.ajax(url, {
            data: {
                NewID: NewID
            },
            success: function (data) {
                if (data == "1") {
                    alert('사용 가능한 아이디 입니다.');
                    document.RegForm.CheckedID.value = "1";
					document.getElementById("BtnIDCheck").style.display = "none";
                }
                else {
                    alert('이미 사용중인 아이디 입니다.');
                    document.RegForm.CheckedID.value = "0";
					document.getElementById("BtnIDCheck").style.display = "inline";
                }
            },
            error: function () {
                alert('Error while contacting server, please try again');
                document.RegForm.CheckedID.value = "0";
				document.getElementById("BtnIDCheck").style.display = "inline";
            }
        });

    }

}




function FormSubmit(){
	

	obj = document.RegForm.MemberLevelID;
	if (obj.value==""){
		alert('회원레벨을 선택하세요.');
		obj.focus();
		return;
	}

	obj = document.RegForm.MemberLoginID;
	if (obj.value==""){
		alert('아이디를 입력하세요.');
		obj.focus();
		return;
	}


	<?php
	if ($NewData=="1"){
	?>

	obj = document.RegForm.CheckedID;
	if (obj.value=="0"){
		alert('아이디 중복확인을 진행하세요.');
		obj.focus();
		return;
	}

	obj = document.RegForm.NewMemberLoginPW1;
	if (obj.value==""){
		alert('비밀번호를 입력하세요.');
		obj.focus();
		return;
	}

	obj = document.RegForm.NewMemberLoginPW2;
	if (obj.value==""){
		alert('비밀번호 확인을 입력하세요.');
		obj.focus();
		return;
	}

	<?
	}
	?>
	

	obj1 = document.RegForm.NewMemberLoginPW1;
	obj2 = document.RegForm.NewMemberLoginPW2;
	if (obj1.value!=obj2.value){
		alert('비밀번호가 일치하지 않습니다.');
		obj2.focus();
		return;
	}

	obj = document.RegForm.MemberName;
	if (obj.value==""){
		alert('회원명을 입력하세요.');
		obj.focus();
		return;
	}

	obj = document.RegForm.MemberBirthday;
	if (obj.value==""){
		alert('생년월일을 입력하세요.');
		obj.focus();
		return;
	}


	document.RegForm.action = "member_action.php";
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







