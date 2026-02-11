<div id="header">
	<div class="header2">
		<h1 style="padding:0 0 0 5px;">
			<img src="./images/logo.png" style="height:42px;">
		</h1>
        <ul class="top_nav">
       		<li style="margin-right:40px; color:#fff; font-weight:bold;"><?php echo $_ADMIN_NAME_;?>님 반갑습니다.</li>
        	<!--li><a href="#">내정보</a></li-->
            <li><a href="logout.php" class="active">로그아웃</a></li>
        </ul>
    </div>
    <div id="nav">
    	<h3><a href="index.php"><img src="images/icon_home.png"></a></h3>
        <div id="menu">
            <ul class="menu">
                
				
				<li><a href="setup_form.php" <?php if ($top_menu_id==2){echo ("class='parent active'");}?>><span>운영관리</span></a>
                    <ul>
						<li><a href="setup_form.php"><span>기본설정</span></a></li>
						<li><a href="popup_list.php"><span>팝업관리</span></a></li>
						<!--li><a href="calendar.php" class="last"><span>일정관리</span></a></li-->
					</ul>
                </li>
                <!--li><a href="member_list.php" <?php if ($top_menu_id==3){echo ("class='parent active'");}?>><span>회원관리</span></a>
					<ul>
						<li><a href="member_list.php"><span>전체회원</span></a></li>
						<!li><a href="member_level_form.php" class="last"><span>레벨관리</span></a></li>
					</ul>
                </li-->
				


				<li><a href="main_layout_form.php" <?php if ($top_menu_id==4){echo ("class='parent active'");}?>><span>홈페이지관리</span></a>
					<ul>
						<li><a href="main_layout_form.php"><span>메인템플릿</span></a></li>
						<li><a href="main_page_form.php"><span>메인페이지</span></a></li>
						<li><a href="sub_layout_list.php"><span>서브템플릿</span></a></li>
						<li><a href="sub_page_list.php"><span>서브페이지</span></a></li>
						<li><a href="boardset_list.php"><span>게시판설정</span></a></li>
						<li><a href="piece_list.php"><span>피스목록</span></a></li>
						<!--
						<li><a href="recent_list.php" class="last"><span>최근목록</span></a></li>
						-->
					</ul>				
				</li>

                <li><a href="board.php" <?php if ($top_menu_id==5){echo ("class='parent active'");}?>><span>게시판관리</span></a>
					<ul>
						<?php
						$Sql = "select A.* from Boards A inner join Subs B on A.SubID=B.SubID where BoardState=1 order by BoardRegDateTime desc";
						$Stmt = $DbConn->prepare($Sql);
						$Stmt->execute();
						$Stmt->setFetchMode(PDO::FETCH_ASSOC);

						while($Row = $Stmt->fetch()) {
						?>
						<li><a href="board_list.php?BoardCode=<?=$Row["BoardCode"]?>"><span><?=$Row["BoardName"]?></span></a></li>
						<?php
						}
						$Stmt = null;
						?>
					</ul>				
				</li>
				<!--
				<li><a href="image_list.php" <?php if ($top_menu_id==6){echo ("class='parent active'");}?>><span>멀티미디어관리</span></a>
					<ul>
						<li><a href="image_list.php"><span>이미지관리</span></a></li>
						<li><a href="sound_list.php"><span>사운드관리</span></a></li>
						<li><a href="movie_list.php"><span>동영상관리</span></a></li>
						<li><a href="flash_list.php" class="last"><span>플래시관리</span></a></li>
					</ul>
                </li>
				-->
            </ul>
        </div>
          
    </div>
</div>
<div class="main">