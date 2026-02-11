<div class="left">
	<ul class="left_nav">
		<li>홈페이지관리</li>
		<li><a href="main_layout_form.php" <?php if ($left_menu_id==1){echo ("class='active'");}?>>> 메인레이아웃</a></li>
		<li><a href="main_page_form.php" <?php if ($left_menu_id==2){echo ("class='active'");}?>>> 메인페이지</a></li>
		<li><a href="sub_layout_list.php" <?php if ($left_menu_id==3){echo ("class='active'");}?>>> 서브레이아웃</a></li>
		<li><a href="sub_page_list.php" <?php if ($left_menu_id==4){echo ("class='active'");}?>>> 서브페이지</a></li>
		<li><a href="boardset_list.php" <?php if ($left_menu_id==5){echo ("class='active'");}?>>> 게시판설정</a></li>
		<li><a href="piece_list.php" <?php if ($left_menu_id==6){echo ("class='active'");}?>>> 피스목록</a></li>
		<!--
		<li><a href="recent_list.php" <?php if ($left_menu_id==7){echo ("class='active'");}?>>> 최근목록</a></li>
		-->
	</ul>
</div>