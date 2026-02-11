<?php
include_once('../includes/dbopen.php');
include_once('../includes/common.php');
include_once('./includes/admin_check.php');
include_once('./includes/common.php');
?>
<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="ko"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="ko"> <!--<![endif]-->
<head>
<?
include_once('./includes/common_meta_tag.php');
include_once('./inc_header.php');
include_once('./inc_common_form_css.php');
?>
<!-- ============== only this page css ============== -->

<!-- ============== only this page css ============== -->
<!-- ============== common.css ============== -->
<link rel="stylesheet" type="text/css" href="css/common.css" />
<!-- ============== common.css ============== -->

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>

<body class="disable_transitions sidebar_main_open sidebar_main_swipe">
<?php
$MainMenuID = 11;
$SubMenuID = 1122; 
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
include('./inc_departments.php');
$departments = getDepartments($LangID);
?>
<body>
<?

if ($_LINK_ADMIN_ID_!="") {

	$Sql = "SELECT 
		A.MemberID,
		A.MemberLoginType,
		A.MemberLevelID,
		A.MemberLoginPW,
		A.MemberLoginID,
		A.MemberName,
		A.MemberLoginInit 	
		from Members A  where A.MemberID=:_MEMBER_ID_";
	
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':_MEMBER_ID_', $_LINK_ADMIN_ID_);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$MemberID = $Row["MemberID"];
	$MemberLoginType = $Row["MemberLoginType"];
	$MemberLevelID = $Row["MemberLevelID"];
	$MemberLoginPW = $Row["MemberLoginPW"];
	$MemberLoginID = $Row["MemberLoginID"];
	$MemberName = $Row["MemberName"];
	
	
	$CheckedID = 1;
	$CheckedEmail = 1;
}
?>
<!-- Section: inner-header -->
<div id="page_content">
	<div id="page_content_inner">

	<section class="inner-header divider parallax layer-overlay overlay-white-2" data-bg-img="images/Sub_Visual_2.jpg" style="display:none;">
  <div class="container pt-60 pb-60">
	<!-- Section Content -->
	<div class="section-content">
	  <div class="row">
	  
		<?if ($_LINK_ADMIN_ID_!="") {?>
		<div class="col-md-12 text-center">
		  <h2 class="title TrnTag">내정보 수정</h2>
		  <ol class="breadcrumb text-center text-black mt-10">
			<li><a href="#">Home</a></li>
			<li class="active text-theme-colored TrnTag">내정보 수정</li>
		  </ol>
		</div>
		<?}?>
	  </div>
	</div>
  </div>
</section>


<div class="sub_wrap">       

    <section class="member_wrap">
        <div class="member_area">           
			 <h2 class="member_caption TrnTag">내 정보<span class="normal">수정</span></h2>
			<form name="RegForm" method="post" class="pt-30 pb-40" autocomplete="off">
				<input type="hidden" name="MemberID" value="<?=$MemberID?>">
                

				<input type="hidden" name="CheckedID" value="<?=$CheckedID?>">
				<input type="hidden" name="CheckedEmail" value="<?=$CheckedEmail?>">
				
				<input type="hidden" name="MemberLevelID" value="<?=$MemberLevelID?>">
				<input type="hidden" name="MemberLoginPW" value="<?=$MemberLoginPW?>">
				<input type="hidden" name="MemberLoginInit" value="<?=$MemberLoginInit?>">
				
				<table class="member_table">
					<?
					if ($MemberID=="") {
					?>
					<?
					}else{
					?>
					<tr>
						<th>아이디 <b class="member_red">★</b></th>
						<td>
							<?=$MemberLoginID?>
							<input type="hidden" name="MemberLoginID" id="MemberLoginID" value="<?=$MemberLoginID?>">
						</td>
					</tr>
					<?
					}
					?>
					<tr>
						<th>비밀번호 <b class="member_red">★</b></th>
						<td class="Join1">
							<input type="password" name="MemberLoginNewPW" class="member_common">
							<?if ($MemberID=="") {?>
							<div class="member_idpw_text TrnTag">4자 이상 영문 또는 영문/숫자 조합으로 입력해 주시기 바랍니다.</div>
							<?}else{?>
							<div class="member_idpw_text TrnTag">비밀번호 수정을 원하시면 입력하세요.</div>
							<?}?>
						</td>
					  </tr>
					  <tr>
						<th>비밀번호 확인 <b class="member_red">★</b></th>
						<td class="Join1">
							<input type="password" name="MemberLoginNewPW2" class="member_common">
							<?if ($MemberID=="") {?>
							<div class="member_idpw_text TrnTag">비밀번호를 한번 더 입력하세요.</div>
							<?}else{?>
							<div class="member_idpw_text TrnTag">비밀번호 수정을 원하시면 입력하세요.</div>
							<?}?>
						</td>
					  </tr>
					</table>

			<a href="javascript:FormSubmit();" class="md-btn md-btn-primary TrnTag">정보수정</a>
			</form>

		</div>
	</section>
</div>

	</div>
</div>
<?
include_once('./inc_category_change.php');

include_once('./inc_common_form_js.php');
?>
<!-- ==============  common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ==============  common.js ============== -->




<script>

function SearchCenter() {
		openurl = "pop_search_center_form.php";
		$.colorbox({	
			href:openurl
			,width:"95%"
			,height:"95%"
			,maxWidth:"800" 
			,maxHeight:"710"
			,title:""
			,iframe:true 
			,scrolling:true
			//,onClosed:function(){location.reload(true);}   
		});
}

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
        url = "ajax_check_id.php";

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



</script>



<script language="javascript">
$('.sub_visual_navi .two').addClass('active');

function FormSubmit(){
	MemberLevelID = document.RegForm.MemberLevelID.value;

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

	<?if ($_LINK_ADMIN_ID_ != ""){?>
		AlertMsg = "회원정보를 수정하시겠습니까?";
	<?}else{?>
		AlertMsg = "회원가입을 진행하시겠습니까?";
	<?}?>

	if (confirm(AlertMsg)){
		document.RegForm.action = "member_action.php"
		document.RegForm.submit();
	}
}

function FormSubmitEn(){
	if (event.keyCode == 13){
		FormSubmit();
	}
}


</script>



<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>