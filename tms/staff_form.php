<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
include_once('./inc_header.php');
?>
</head>
<body>
<?php
$MainCode = 7;
$SubCode = 4;
include_once('./inc_top.php');
?>


<?php
$ListParam = isset($_REQUEST["ListParam"]) ? $_REQUEST["ListParam"] : "";
$MemberID = isset($_REQUEST["MemberID"]) ? $_REQUEST["MemberID"] : "";

if ($MemberID!=""){

	$Sql = "
			select 
					A.*,
					AES_DECRYPT(UNHEX(A.MemberPhone1),:EncryptionKey) as DecMemberPhone1,
					AES_DECRYPT(UNHEX(A.MemberPhone2),:EncryptionKey) as DecMemberPhone2,
					AES_DECRYPT(UNHEX(A.MemberPhone3),:EncryptionKey) as DecMemberPhone3,
					AES_DECRYPT(UNHEX(A.MemberEmail),:EncryptionKey) as DecMemberEmail
			from Members A 
			where A.MemberID=:MemberID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':MemberID', $MemberID);
	$Stmt->bindParam(':EncryptionKey', $EncryptionKey);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$CenterID = $Row["CenterID"];
	$MemberID = $Row["MemberID"];
	$MemberLoginID = $Row["MemberLoginID"];
	$MemberLoginPW = $Row["MemberLoginPW"];
	$MemberName = $Row["MemberName"];
	$MemberNumber = $Row["MemberNumber"];
	$MemberPositionName = $Row["MemberPositionName"];
	$SchoolName = $Row["SchoolName"];
	$SchoolGrade = $Row["SchoolGrade"];
	$MemberPhone1 = $Row["DecMemberPhone1"];
	$MemberPhone2 = $Row["DecMemberPhone2"];
	$MemberPhone3 = $Row["DecMemberPhone3"];
	$MemberEmail = $Row["DecMemberEmail"];
	$MemberZipCode = $Row["MemberZipCode"];
	$MemberAddr1 = $Row["MemberAddr1"];
	$MemberAddr2 = $Row["MemberAddr2"];
	$MemberIntroText = $Row["MemberIntroText"];
	$MemberSex = $Row["MemberSex"];

	$MemberBirthday = $Row["MemberBirthday"];
	$ArrMemberBirthday = explode("-",$MemberBirthday);
	$MemberBirthday_1 = $ArrMemberBirthday[0];
	$MemberBirthday_2 = $ArrMemberBirthday[1];
	$MemberBirthday_3 = $ArrMemberBirthday[2]; 

	$ArrMemberPhone1 = explode("-",$MemberPhone1);
	$MemberPhone1_1 = $ArrMemberPhone1[0];
	$MemberPhone1_2 = $ArrMemberPhone1[1];
	$MemberPhone1_3 = $ArrMemberPhone1[2];

	$ArrMemberPhone2 = explode("-",$MemberPhone2);
	$MemberPhone2_1 = $ArrMemberPhone2[0];
	$MemberPhone2_2 = $ArrMemberPhone2[1];
	$MemberPhone2_3 = $ArrMemberPhone2[2];

	$ArrMemberPhone3 = explode("-",$MemberPhone3);
	$MemberPhone3_1 = $ArrMemberPhone3[0];
	$MemberPhone3_2 = $ArrMemberPhone3[1];
	$MemberPhone3_3 = $ArrMemberPhone3[2];

	$ArrMemberEmail = explode("@",$MemberEmail);
	$MemberEmail_1 = $ArrMemberEmail[0];
	$MemberEmail_2 = $ArrMemberEmail[1];

	$MemberState = $Row["MemberState"];
	$MemberView = $Row["MemberView"];
	
	$CheckedID = 1;
	$CheckedEmail = 1;

}else{
	$CenterID = 0;
	$MemberID = "";
	$MemberLoginID = "";
	$MemberLoginPW = "";
	$MemberName = "";
	$MemberNumber = "";
	$MemberPositionName = "";
	$SchoolName = "";
	$SchoolGrade = "";
	$MemberZipCode = "";
	$MemberAddr1 = "";
	$MemberAddr2 = "";
	$MemberIntroText = "";
	$MemberSex = 1;

	$MemberBirthday_1 = "";
	$MemberBirthday_2 = "";
	$MemberBirthday_3 = "";

	$MemberPhone1_1 = "";
	$MemberPhone1_2 = "";
	$MemberPhone1_3 = "";

	$MemberPhone2_1 = "";
	$MemberPhone2_2 = "";
	$MemberPhone2_3 = "";

	$MemberPhone3_1 = "";
	$MemberPhone3_2 = "";
	$MemberPhone3_3 = "";

	$MemberEmail_1 = "";
	$MemberEmail_2 = "";

	$MemberState = 1;
	$MemberView = 1;
}

?>

<h1 class="Title" style="margin-bottom:20px;">직원 정보</h1>

<form id="RegForm" name="RegForm" method="post" enctype="multipart/form-data" accept-charset="UTF-8" autocomplete="off">
<input type="hidden" name="MemberID" value="<?=$MemberID?>">
<input type="hidden" name="ListParam" value="<?=$ListParam?>">

<input type="hidden" name="CheckedID" value="<?=$CheckedID?>">
<input type="hidden" name="CheckedEmail" value="<?=$CheckedEmail?>">
<input type="hidden" name="MemberLoginPW" value="<?=$MemberLoginPW?>">

<input type="hidden" name="MemberView" value="<?=$MemberView?>">
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_form" style="margin-bottom:15px;">
  <tr>
	<th>이름<span></span></th>
	<td class="radio">
		<input type="text" id="MemberName" name="MemberName" value="<?=$MemberName?>" placeholder="이름을 입력하세요." style="width:200px;"/>
		<span  style="display:none;">
			&nbsp; &nbsp; &nbsp;
			<input type="radio" name="MemberSex" id="MemberSex1" value="1" <?php if ($MemberSex==1) {echo ("checked");}?>> <label for="MemberSex1"><span></span>남자</label>
			<input type="radio" name="MemberSex" id="MemberSex2" value="2" <?php if ($MemberSex==2) {echo ("checked");}?>> <label for="MemberSex2"><span></span>여자</label>
		</span>
	</td>
  </tr>
  <tr>
	<th>직책<span></span></th>
	<td class="radio">
		<input type="text" id="MemberPositionName" name="MemberPositionName" value="<?=$MemberPositionName?>" placeholder="직책을 입력하세요." style="width:200px;"/>
	</td>
  </tr>

  <tr>
	<th>아이디<span></span></th>
	<td>
	<?if ($MemberID!="") {?>
		<span style="margin-left:10px;"><?=$MemberLoginID?></span>
		<input type="hidden" name="MemberLoginID" id="MemberLoginID" placeholder="아이디" value="<?=$MemberLoginID?>" style="width:200px;">
	<?}else{?>
		<input type="text" name="MemberLoginID" id="MemberLoginID" placeholder="아이디" onkeyup="EnNewID()" style="width:200px;ime-mode:disabled;">
		<a id="BtnCheckID" href="javascript:CheckID();" class="btn gray small" style="height:32px; line-height:32px;">중복확인</a>
		<label>※ 4자 이상 영문 또는 영문/숫자 조합</label>
	<?}?>
	</td>
  </tr>
  <tr>
	<th>비밀번호<span></span></th>
	<td>
		<input type="password" name="MemberLoginNewPW" placeholder="비밀번호" style="width:200px;margin-bottom:5px;border:0px;">
		<div>
		<?if ($MemberID=="") {?>
		* 4자 이상 영문 또는 영문/숫자 조합으로 입력해 주시기 바랍니다.
		<?}else{?>
		* 비밀번호 수정을 원하시면 입력하세요.
		<?}?>
		</div>
	</td>
  </tr>
  <tr>
	<th>비밀번호<span></span></th>
	<td>
		<input type="password" name="MemberLoginNewPW2" placeholder="비밀번호 확인" style="width:200px;margin-bottom:5px;border:0px;">
		<div>
		<?if ($MemberID=="") {?>
		* 비밀번호를 한번 더 입력하세요.
		<?}else{?>
		* 비밀번호 수정을 원하시면 입력하세요.
		<?}?>
		</div>
	</td>
  </tr>
  <tr style="display:none;">
	<th>생년월일<span></span></th>
	<td class="Birth">
		<select class="form-control" name="MemberBirthday_1" id="years" style="width:100px; display:inline;"></select>
		<select class="form-control" name="MemberBirthday_2" id="months" style="width:100px; display:inline;"></select>
		<select class="form-control" name="MemberBirthday_3" id="days" style="width:100px; display:inline;"></select>
	</td>
  </tr>
  <tr style="display:none;">
	<th>휴대폰번호<span></span></th>
	<td>
		<select name="MemberPhone1_1" class="Select" style="width:15%">
			<option value="010" <?If ($MemberPhone1_1=="010") {?>selected<?}?>>010</option>
			<option value="011" <?If ($MemberPhone1_1=="011") {?>selected<?}?>>011</option>
			<option value="016" <?If ($MemberPhone1_1=="016") {?>selected<?}?>>016</option>
			<option value="017" <?If ($MemberPhone1_1=="017") {?>selected<?}?>>017</option>
			<option value="018" <?If ($MemberPhone1_1=="018") {?>selected<?}?>>018</option>
			<option value="019" <?If ($MemberPhone1_1=="019") {?>selected<?}?>>019</option>
		</select>
		<input type="text" name="MemberPhone1_2" style="width:15%" placeholder="" value="<?=$MemberPhone1_2?>" class="allownumericwithoutdecimal" maxlength="4"> <input type="text" name="MemberPhone1_3" style="width:15%"  placeholder="" value="<?=$MemberPhone1_3?>" class="allownumericwithoutdecimal" maxlength="4"> 
	</td>
  </tr>
  <tr style="display:none;">
	<th>전화번호<span></span></th>
	<td>
		<select name="MemberPhone2_1" class="Select" style="width:15%">
			<option value="010" <?If ($MemberPhone2_1=="010") {?>selected<?}?>>010</option>
			<option value="011" <?If ($MemberPhone2_1=="011") {?>selected<?}?>>011</option>
			<option value="016" <?If ($MemberPhone2_1=="016") {?>selected<?}?>>016</option>
			<option value="017" <?If ($MemberPhone2_1=="017") {?>selected<?}?>>017</option>
			<option value="018" <?If ($MemberPhone2_1=="018") {?>selected<?}?>>018</option>
			<option value="019" <?If ($MemberPhone2_1=="019") {?>selected<?}?>>019</option>
		</select>
		<input type="text" name="MemberPhone2_2" style="width:15%" placeholder="" value="<?=$MemberPhone2_2?>" class="allownumericwithoutdecimal" maxlength="4"> <input type="text" name="MemberPhone2_3" style="width:15%"  placeholder="" value="<?=$MemberPhone2_3?>" class="allownumericwithoutdecimal" maxlength="4"> ※ 부모님 인증과 관련된 번호이니 정확히 작성해 주세요.
	</td>
  </tr>

  <tr style="display:none;">
	<th>전화번호<span></span></th>
	<td>
		<select name="MemberPhone3_1" class="Select" style="width:15%">
			<option value="010" <?If ($MemberPhone3_1=="010") {?>selected<?}?>>010</option>
			<option value="011" <?If ($MemberPhone3_1=="011") {?>selected<?}?>>011</option>
			<option value="016" <?If ($MemberPhone3_1=="016") {?>selected<?}?>>016</option>
			<option value="017" <?If ($MemberPhone3_1=="017") {?>selected<?}?>>017</option>
			<option value="018" <?If ($MemberPhone3_1=="018") {?>selected<?}?>>018</option>
			<option value="019" <?If ($MemberPhone3_1=="019") {?>selected<?}?>>019</option>
		</select>
		<input type="text" name="MemberPhone3_2" style="width:15%" placeholder="" value="<?=$MemberPhone3_2?>" class="allownumericwithoutdecimal" maxlength="4"> <input type="text" name="MemberPhone3_3" style="width:15%"  placeholder="" value="<?=$MemberPhone3_3?>" class="allownumericwithoutdecimal" maxlength="4">
	</td>

  <tr style="display:none;">
	<th>이메일<span></span></th>
	<td>
		<input type="text" name="MemberEmail_1" id="MemberEmail_1" style="width:20%" value="<?=$MemberEmail_1?>" onkeyup="EnNewEmail()"> 
		<div style="display:inline-block;width:20px;margin-top:8px;">@</div> 
		<input type="text" name="MemberEmail_2" id="MemberEmail_2" style="width:20%" value="<?=$MemberEmail_2?>" onkeyup="EnNewEmail()">
		<select name="MemberEmail_3" class="Select" style="width:22%" onchange="SetEmailName()">
			<option value="">직접입력</option>
			<option value="naver.com">naver.com</option>
			<option value="empal.com">empal.com</option>
			<option value="empas.com">empas.com</option>
			<option value="daum.net">daum.net</option>
			<option value="hanmail.net">hanmail.net</option>
			<option value="hotmail.com">hotmail.com</option>
			<option value="dreamwiz.com">dreamwiz.com</option>				
			<option value="korea.com">korea.com</option>
			<option value="paran.com">paran.com</option>
			<option value="nate.com">nate.com</option>
			<option value="lycos.co.kr">lycos.co.kr</option>
			<option value="yahoo.co.kr">yahoo.co.kr</option>
		</select>

		<a id="BtnCheckID2" href="javascript:CheckEmail();" class="btn gray small" style="height:32px; line-height:32px; display:<?if ($MemberID=="") {?><?}else{?>none<?}?>">중복확인</a>
	</td>
  </tr>

  <tr style="display:none;">
	<th>주소<span></span></th>
	<td>
		<input type="text" name="MemberZipCode" id="MemberZipCode" value="<?=$MemberZipCode?>" style="width:110px; margin-right:4px;">
		<a id="BtnCheckID2" href="javascript:ExecDaumPostcode();" class="btn gray small" style="height:32px; line-height:32px; width:100px;">우편번호검색</a>
		<br>
		<input type="text" name="MemberAddr1" id="MemberAddr1" value="<?=$MemberAddr1?>" style="width:500px;margin:3px 0;" placeholder="주소">
		<input type="text" name="MemberAddr2" id="MemberAddr2" value="<?=$MemberAddr2?>" style="width:500px;margin:3px 0;" placeholder="나머지 주소">
	</td>
  </tr>

  <tr>
	<th>상태<span></span></th>
	<td class="radio">
		<input type="radio" name="MemberState" id="MemberState1" value="1" <?php if ($MemberState==1) {echo ("checked");}?>> <label for="MemberState1"><span></span>승인</label>
		<input type="radio" name="MemberState" id="MemberState2" value="2" <?php if ($MemberState==2) {echo ("checked");}?>> <label for="MemberState2"><span></span>미승인</label>
	</td>
  </tr>

</table>
</form>

<div class="btn_center" style="padding-top:25px;">
	<?
	if ($MemberID==""){ 
	?>
	<a href="javascript:FormSubmit();" class="btn red">등록하기</a>
	<?
	}else{
	?>
	<a href="javascript:FormSubmit();" class="btn red">수정하기</a>
	<?
	}
	?>
	<a href="javascript:history.go(-1);" class="btn gray">목록으로</a>
</div>



<div id="layer" style="padding-top:20px;display:none;position:fixed;overflow:hidden;z-index:100000;-webkit-overflow-scrolling:touch;background-color:#ffffff;">
<img src="//i1.daumcdn.net/localimg/localimages/07/postcode/320/close.png" id="btnCloseLayer" style="cursor:pointer;position:absolute;right:-3px;top:-3px;z-index:1" onclick="closeDaumPostcode()" alt="닫기 버튼">
</div>
<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
<script>
    // 우편번호 찾기 화면을 넣을 element
    var element_layer = document.getElementById('layer');

    function closeDaumPostcode() {
        // iframe을 넣은 element를 안보이게 한다.
        element_layer.style.display = 'none';
    }

    function ExecDaumPostcode() {
        new daum.Postcode({
            oncomplete: function(data) {
                // 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

                // 각 주소의 노출 규칙에 따라 주소를 조합한다.
                // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
                var fullAddr = data.address; // 최종 주소 변수
                var extraAddr = ''; // 조합형 주소 변수

                // 기본 주소가 도로명 타입일때 조합한다.
                if(data.addressType === 'R'){
                    //법정동명이 있을 경우 추가한다.
                    if(data.bname !== ''){
                        extraAddr += data.bname;
                    }
                    // 건물명이 있을 경우 추가한다.
                    if(data.buildingName !== ''){
                        extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                    }
                    // 조합형주소의 유무에 따라 양쪽에 괄호를 추가하여 최종 주소를 만든다.
                    fullAddr += (extraAddr !== '' ? ' ('+ extraAddr +')' : '');
                }

                // 우편번호와 주소 정보를 해당 필드에 넣는다.
                document.getElementById('MemberZipCode').value = data.zonecode; //5자리 새우편번호 사용
                document.getElementById('MemberAddr1').value = fullAddr;
                //document.getElementById('sample2_addressEnglish').value = data.addressEnglish;

                // iframe을 넣은 element를 안보이게 한다.
                // (autoClose:false 기능을 이용한다면, 아래 코드를 제거해야 화면에서 사라지지 않는다.)
                element_layer.style.display = 'none';
            },
            width : '100%',
            height : '100%'
        }).embed(element_layer);

        // iframe을 넣은 element를 보이게 한다.
        element_layer.style.display = 'block';

        // iframe을 넣은 element의 위치를 화면의 가운데로 이동시킨다.
        initLayerPosition();
    }

    // 브라우저의 크기 변경에 따라 레이어를 가운데로 이동시키고자 하실때에는
    // resize이벤트나, orientationchange이벤트를 이용하여 값이 변경될때마다 아래 함수를 실행 시켜 주시거나,
    // 직접 element_layer의 top,left값을 수정해 주시면 됩니다.
    function initLayerPosition(){
        var width = 500; //우편번호서비스가 들어갈 element의 width
        var height = 600; //우편번호서비스가 들어갈 element의 height
        var borderWidth = 5; //샘플에서 사용하는 border의 두께

        // 위에서 선언한 값들을 실제 element에 넣는다.
        element_layer.style.width = width + 'px';
        element_layer.style.height = height + 'px';
        element_layer.style.border = borderWidth + 'px solid';
        // 실행되는 순간의 화면 너비와 높이 값을 가져와서 중앙에 뜰 수 있도록 위치를 계산한다.
        element_layer.style.left = (((window.innerWidth || document.documentElement.clientWidth) - width)/2 - borderWidth) + 'px';
        element_layer.style.top = (((window.innerHeight || document.documentElement.clientHeight) - height)/2 - borderWidth) + 'px';
    }
</script>



<script>
function EnNewID(){
	document.RegForm.CheckedID.value = "0";
	document.getElementById('BtnCheckID').style.display = "";
}

function CheckID(){
    var NewID = $.trim($('#MemberLoginID').val());

    if (NewID == "") {
        alert('아이디를 입력하세요.');
        document.RegForm.CheckedID.value = "0";
    } else if (NewID.length<4)  {
		alert('아이디는 4자 이상 입력하세요.');
        document.RegForm.CheckedID.value = "0";
	} else {
        url = "../ajax_check_id.php";

		//location.href = url + "?NewID="+NewID;
        $.ajax(url, {
            data: {
                NewID: NewID
            },
            success: function (data) {
				json_data = data;
				CheckResult = json_data.CheckResult;
                if (CheckResult == 1) {
                    alert('사용 가능한 아이디 입니다.');
                    document.RegForm.CheckedID.value = "1";
					document.getElementById('BtnCheckID').style.display = "none";
                }
                else {
                    alert('이미 사용중인 아이디 입니다.');
                    document.RegForm.CheckedID.value = "0";
					document.getElementById('BtnCheckID').style.display = "";
                }
            },
            error: function () {
                alert('Error while contacting server, please try again');
                document.RegForm.CheckedID.value = "0";
				document.getElementById('BtnCheckID').style.display = "";
            }
        });

    }

}


function EnNewEmail(){
	document.RegForm.CheckedEmail.value = "0";
	document.getElementById('BtnCheckID2').style.display = "";
}


 
function CheckEmail(){
    var MemberEmail_1 = $.trim($('#MemberEmail_1').val());
	var MemberEmail_2 = $.trim($('#MemberEmail_2').val());

    if (MemberEmail_1 == "" || MemberEmail_2 == "") {
        alert('이메일을 입력하세요.');
        document.RegForm.CheckedEmail.value = "0";
	} else {
        url = "../ajax_check_email.php";

		//location.href = url + "?MemberEmail_1="+MemberEmail_1+"&MemberEmail_2="+MemberEmail_2+"&MemberID=<?=$MemberID?>";
        $.ajax(url, {
            data: {
                MemberEmail_1: MemberEmail_1,
				MemberEmail_2: MemberEmail_2,
				MemberID: "<?=$MemberID?>"
            },
            success: function (data) {
				json_data = data;
				CheckResult = json_data.CheckResult;
                if (CheckResult == 1) {
                    alert('사용 가능한 이메일 입니다.');
                    document.RegForm.CheckedEmail.value = "1";
					document.getElementById('BtnCheckID2').style.display = "none";
                }
                else {
                    alert('이미 등록된 이메일 입니다.');
                    document.RegForm.CheckedEmail.value = "0";
					document.getElementById('BtnCheckID2').style.display = "";
                }
            },
            error: function () {
                alert('Error while contacting server, please try again');
                document.RegForm.CheckedEmail.value = "0";
				document.getElementById('BtnCheckID2').style.display = "";
            }
        });

    }

}





function SetEmailName(){
	MemberEmail_3 = document.RegForm.MemberEmail_3.value;
	if (MemberEmail_3==""){
		document.RegForm.MemberEmail_2.value = "";
		document.RegForm.MemberEmail_2.readOnly = false;
	}else{
		document.RegForm.MemberEmail_2.value = MemberEmail_3;
		document.RegForm.MemberEmail_2.readOnly = true;
	}

	EnNewEmail();
}


function PopupAddImage(ImgID,FormName,Path){
	openurl = "../pop_image_upload_form.php?ImgID="+ImgID+"&FormName="+FormName+"&Path="+Path+"&PopupType=2";
	$.colorbox({	
		href:openurl
		,width:"500" 
		,height:"300"
		,title:""
		,iframe:true 
		,scrolling:false
		//,onClosed:function(){location.reload(true);}   
	}); 

	//openurl = "../pop_image_upload_form.php?ImgID="+ImgID+"&FormName="+FormName+"&Path="+Path+"&PopupType=1";
	//window.open(openurl,'pop_image_upload','width=500,height=280,toolbar=no,top=100,left=100');
}
</script>



<script language="javascript">
function FormSubmit(){

	obj = document.RegForm.MemberName;
	if (obj.value==""){
		alert('이름을 입력하세요.');
		obj.focus();
		return;
	}

	obj = document.RegForm.MemberPositionName;
	if (obj.value==""){
		alert('직책을 입력하세요.');
		obj.focus();
		return;
	}


	obj = document.RegForm.MemberLoginID;
	if (obj.value==""){
		alert('아이디를 입력하세요.');
		obj.focus();
		return;
	}

	obj = document.RegForm.MemberLoginID;
	if (obj.value.length<4){
		alert('아이디는 4자 이상 입력하세요.');
		obj.focus();
		return;
	}


	obj = document.RegForm.CheckedID;
	if (obj.value=="0"){
		alert('아이디 중복확인 버튼을 클릭하세요.');
		return;
	}



	<?
	if ($MemberID!=""){ 
	?>	
		
		obj = document.RegForm.MemberLoginNewPW;
		obj2 = document.RegForm.MemberLoginNewPW2;

		if (obj.value!="" || obj2.value!=""){
			
			if (obj.value.length<4){
				alert('비밀번호는 4자 이상 입력하세요.');
				obj.focus();
				return;
			}			
			
			if (obj.value!=obj2.value){
				alert('비밀번호와 비밀번호 확인이 일치하지 않습니다.');
				obj.focus();
				return;
			}
		}
	<?
	}else{
	?>
		obj = document.RegForm.MemberLoginNewPW;
		obj2 = document.RegForm.MemberLoginNewPW2;
		if (obj.value==""){
			alert('비밀번호를 입력하세요.');
			obj.focus();
			return;
		}

		if (obj.value.length<4){
			alert('비밀번호는 4자 이상 입력하세요.');
			obj.focus();
			return;
		}	

		if (obj.value!=obj2.value){
			alert('비밀번호와 비밀번호 확인이 일치하지 않습니다.');
			obj.focus();
			return;
		}
	<?
	}
	?>


	/*
	obj = document.RegForm.MemberBirthday_1;
	if (obj.value==""){
		alert('생년월일을 입력하세요.');
		obj.focus();
		return;
	}

	obj = document.RegForm.MemberBirthday_2;
	if (obj.value==""){
		alert('생년월일을 입력하세요.');
		obj.focus();
		return;
	}

	obj = document.RegForm.MemberBirthday_3;
	if (obj.value==""){
		alert('생년월일을 입력하세요.');
		obj.focus();
		return;
	}


	obj = document.RegForm.MemberPhone1_2;
	if (obj.value==""){
		alert('휴대폰 번호를 입력하세요.');
		obj.focus();
		return;
	}

	obj = document.RegForm.MemberPhone1_3;
	if (obj.value==""){
		alert('휴대폰 번호를 입력하세요.');
		obj.focus();
		return;
	}





	obj = document.RegForm.MemberEmail_1;
	if (obj.value==""){
		alert('이메일을 입력하세요.');
		obj.focus();
		return;
	}


	obj = document.RegForm.MemberEmail_2;
	if (obj.value==""){
		alert('이메일을 입력하세요.');
		obj.focus();
		return;
	}



	obj = document.RegForm.CheckedEmail;
	if (obj.value=="0"){
		alert('이메일 중복확인 버튼을 클릭하세요.');
		return;
	}
	*/


	<?if ($MemberID==""){?>
	ConfrimMsg = "등록 하시겠습니까?";
	<?}else{?>
	ConfrimMsg = "수정 하시겠습니까?";
	<?}?>

	
	if (confirm(ConfrimMsg)){
		document.RegForm.action = "./staff_action.php"
		document.RegForm.submit();
	}
}

function FormSubmitEn(){
	if (event.keyCode == 13){
		FormSubmit();
	}
}


</script>


<script>

//============== birthday picker ==============//
 $(function () {
	for (i = new Date().getFullYear() ; i > 1900; i--) {
		$('#years').append($('<option />').val(i).html(i));
	}

	for (i = 1; i < 13; i++) {
		$('#months').append($('<option />').val(i).html(i));
	}
	updateNumberOfDays();

	$('#years, #months').change(function () {

		updateNumberOfDays();

	});

});

function updateNumberOfDays() {
	$('#days').html('');
	month = $('#months').val();
	year = $('#years').val();
	days = daysInMonth(month, year);

	for (i = 1; i < days + 1 ; i++) {
		$('#days').append($('<option />').val(i).html(i));
	}

}

function daysInMonth(month, year) {
	return new Date(year, month, 0).getDate();
}


$(document).ready(function(){
	<?if ($MemberBirthday_1!=""){?>
	document.RegForm.MemberBirthday_1.value = "<?=$MemberBirthday_1?>";
	document.RegForm.MemberBirthday_2.value = "<?=$MemberBirthday_2?>";
	document.RegForm.MemberBirthday_3.value = "<?=$MemberBirthday_3?>";
	<?}?>
});

//============== birthday picker ==============//
</script>

<?php
include_once('./inc_bottom.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>