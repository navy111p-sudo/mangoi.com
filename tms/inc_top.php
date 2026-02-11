<a id="PageTop"></a>
<div class="wrap_sub">
    <div class="page_sub">
    
        <ul class="navi_top">
			<li <?if ($MainCode==1){?>class="active"<?}?>>
				<a href="collect_url_list.php">번역관리</a>
			</li>
			<li <?if ($MainCode==2){?>class="active"<?}?>>
				<a href="language_list.php">번역언어관리</a>
			</li>
			<li <?if ($MainCode==3){?>class="active"<?}?>>
				<a href="browser_loc_code_list.php">브라우져코드</a>
			</li>
			<li <?if ($MainCode==7){?>class="active"<?}?>>
				<a href="trn_setup_form.php">기본관리</a>
			</li>


			<li class="login_info">
				<span><b><?=$_LINK_ADMIN_NAME_?></b></span>
				<!--<a href="" class="btn red">계정정보</a>-->
				<a href="logout.php" class="btn gray">로그아웃</a>
			</li>
        </ul>
        
        <div class="page_content">
            <div class="left_box">

				<!--
				<div class="logo_box" style="padding:30px 0px;">
                    <img src="./images/logo.png" class="logo">
                </div>
				-->
                
                <div id="accordian">
                    <ul>

						<?if ($MainCode==1){//오늘의급식?>
							<li <?if ($SubCode==1){?>class="active"<?}?>>
								<h3>
									<span class="icon-tasks"></span><a href="collect_url_list.php?SearchTrnCollectUrlDviceType=1">WEB 페이지</a>
								</h3>
							</li>
							<li <?if ($SubCode==2){?>class="active"<?}?>>
								<h3>
									<span class="icon-tasks"></span><a href="collect_url_list.php?SearchTrnCollectUrlDviceType=2">APP 페이지</a>
								</h3>
							</li>
						<?}else if ($MainCode==2){//번역언어관리?>
							<li <?if ($SubCode==1){?>class="active"<?}?>>
								<h3>
									<span class="icon-tasks"></span><a href="language_list.php">번역언어관리</a>
								</h3>
							</li>
						<?}else if ($MainCode==3){//사용브라우져?>
							<li <?if ($SubCode==1){?>class="active"<?}?>>
								<h3>
									<span class="icon-tasks"></span><a href="browser_loc_code_list.php">사용브라우져</a>
								</h3>
							</li>
						<?}else if ($MainCode==7){//기본관리?>
							<li <?if ($SubCode==3){?>class="active"<?}?>>
								<h3>
									<span class="icon-tasks"></span><a href="setup_form.php">기본관리</a>
								</h3>
							</li>
						<?}?>


                    </ul>
                </div>
            </div>
            <div class="right_box">


