<!-- main sidebar -->
<aside id="sidebar_main" class="accordion_mode">
	
	<div class="sidebar_main_header" style="height:48px;">
		<div class="sidebar_logo">
			<span style="display:block;line-height:20px;text-align:left;padding-top:4px;padding-left:20px;">
				<span style="display:block;"><i class="material-icons">access_time</i> KR TIME : <span id="DivKrTime" style="display:inline-block;"></span></span>
				<span style="display:block;"><i class="material-icons">access_time</i> PH TIME : <span id="DivPhTime" style="display:inline-block;"></span></span>
			</span>
			<a href="index.php" class="sSidebar_hide sidebar_logo_large">
				<img class="logo_regular" src="assets/img/logo_main.png" alt="" height="15" width="71" style="display:none;"/>
				<img class="logo_light" src="assets/img/logo_main_white.png" alt="" height="15" width="71" style="display:none;"/>
			</a>
			<a href="index.php" class="sSidebar_show sidebar_logo_small">
				<img class="logo_regular" src="assets/img/logo_main_small.png" alt="" height="32" width="32" style="display:none;"/>
				<img class="logo_light" src="assets/img/logo_main_small_light.png" alt="" height="32" width="32" style="display:none;"/>
			</a>
		</div>
		<div class="sidebar_actions" style="display:none;">
			<select id="lang_switcher" name="lang_switcher">
				<option value="gb" selected>English</option>
			</select>
		</div>
	</div>

	<div class="menu_section">
		<ul>

			<li title="Dashboard" <?if ($MainMenuID==0){?>class="current_section"<?}?>>
				<a href="index.php">
					<span class="menu_icon"><i class="material-icons">&#xE871;</i></span>
					<span class="menu_title">Dashboard</span>
				</a>
			</li>
			
			<?
			$Sql = "SELECT  
							A.*
					from Favorites A 
					where 
						A.MemberID=".$_LINK_ADMIN_ID_." and FavoriteState=1
					order by A.FavoriteOrder desc
					";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);
			
			while ($Row = $Stmt->fetch()){
				$FavoriteName = $Row["FavoriteName"];
				$FavoriteUrl = $Row["FavoriteUrl"];
			?>
			<li title="Dashboard" <?if ($MainMenuID==0){?>class="current_section"<?}?>>
				<a href="<?=$FavoriteUrl?>" style="color:#1D76CE;font-weight:bold;">
					<span class="menu_icon"><i class="material-icons">stars</i></span>
					<span class="menu_title"><?=$FavoriteName?></span>
				</a>
			</li>
			<?
			}
			$Stmt = null;


			// 즐겨찾기 등록여부
			$Sql2 = "SELECT  
					A.FavoriteLmsMenuSubMenuID
				from FavoriteLmsMenus A 
				where
					A.MemberID=:MemberID
					and
					A.FavoriteLmsMenuState=1
			";
			$Stmt2 = $DbConn->prepare($Sql2);
			$Stmt2->bindParam(':MemberID', $_LINK_ADMIN_ID_);
			$Stmt2->execute();
			$Stmt2->setFetchMode(PDO::FETCH_ASSOC);

			$ArrFavoriteLmsMenuSubMenuID = array();
			while ($Row2 = $Stmt2->fetch()){
				$FavoriteLmsMenuSubMenuID = $Row2["FavoriteLmsMenuSubMenuID"];
				$ArrFavoriteLmsMenuSubMenuID[$FavoriteLmsMenuSubMenuID] = 1;
			}
			$Stmt2 = null;
			?>

			<li title="<?=$가맹점관리[$LangID]?>" <?if ($MainMenuID==12){?>class="current_section"<?}?> style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>13){?>none<?}?>;">
				<a href="#">
					<span class="menu_icon"><i class="material-icons">account_balance</i></span>
					<span class="menu_title"><?=$가맹점관리[$LangID]?></span>
				</a>
				<ul class="lms_left_menu">
					<li <?if ($SubMenuID==1201){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1201, '<?=$대리점[$LangID]?>', 'center_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1201, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1201"></a>
						<a href="center_list.php"><?=$대리점[$LangID]?></a></li>

					<li <?if ($SubMenuID==1202){?>class="act_item"<?}?> style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>10){?>none<?}?>;">
						<a onclick="InsertFavoriteMenu(1202, '<?=$지사[$LangID]?>', 'branch_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1202, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1202"></a>
						<a href="branch_list.php"><?=$지사[$LangID]?></a></li>
					<li <?if ($SubMenuID==1203){?>class="act_item"<?}?> style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>7){?>none<?}?>;">
						<a onclick="InsertFavoriteMenu(1203, '<?=$대표지사[$LangID]?>', 'branch_group_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1203, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1203"></a>
						<a href="branch_group_list.php"><?=$대표지사[$LangID]?></a></li>
					<li <?if ($SubMenuID==1204){?>class="act_item"<?}?> style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>4){?>none<?}?>;">
						<a onclick="InsertFavoriteMenu(1204, '<?=$영업본부[$LangID]?>', 'manager_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1204, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1204"></a>
						<a href="manager_list.php"><?=$영업본부[$LangID]?></a></li>
					<li <?if ($SubMenuID==1206){?>class="act_item"<?}?> style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>4){?>none<?}?>;">
						<a onclick="InsertFavoriteMenu(1206, '<?=$본사관리[$LangID]?>', 'company_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1206, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1206"></a>
						<a href="company_list.php"><?=$본사관리[$LangID]?></a></li>
					<li <?if ($SubMenuID==1205){?>class="act_item"<?}?> style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>4){?>none<?}?>;">
						<a onclick="InsertFavoriteMenu(1205, '<?=$독립사이트[$LangID]?>', 'online_site_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1205, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1205"></a>
						<a href="online_site_list.php"><?=$독립사이트[$LangID]?></a></li>
					<li <?if ($SubMenuID==1207){?>class="act_item"<?}?> style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>1){?>none<?}?>;">
						<a onclick="InsertFavoriteMenu(1207, '<?=$프랜차이즈[$LangID]?>', 'franchise_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1207, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1207"></a>
						<a href="franchise_list.php"><?=$프랜차이즈[$LangID]?></a></li>
					<li <?if ($SubMenuID==1255){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1255, '<?=$충전금내역[$LangID]?>', 'center_saved_money_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1255, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1255"></a>
						<a href="center_saved_money_list.php"><?=$충전금내역[$LangID]?></a></li>
					<li <?if ($SubMenuID==1244){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1244, '<?=$포인트내역[$LangID]?>', 'member_point_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1244, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1244"></a>
						<a href="member_point_list.php"><?=$포인트내역[$LangID]?></a></li>	

					<li <?if ($SubMenuID==1299){?>class="act_item"<?}?> style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;">
						<a onclick="InsertFavoriteMenu(1299, '<?=$대리점_B2B결제[$LangID]?>', 'b2b_payment_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1299, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1299"></a>
						<a href="b2b_payment_list.php"><?=$대리점_B2B결제[$LangID]?></a></li>
				</ul>
			</li>

			<li title="<?=$교육센터[$LangID]?>" <?if ($MainMenuID==13){?>class="current_section"<?}?> style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>4 && $_LINK_ADMIN_LEVEL_ID_!=15){?>none<?}?>;">
				<a href="#">
					<span class="menu_icon"><i class="material-icons">location_city</i></span>
					<span class="menu_title"><?=$교육센터[$LangID]?></span>
				</a>
				<ul class="lms_left_menu">
					<li <?if ($SubMenuID==1301){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1301, '<?=$강사관리[$LangID]?>', 'teacher_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1301, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1301"></a>
						<a href="teacher_list.php"><?=$강사관리[$LangID]?></a></li>
					<li <?if ($SubMenuID==1304){?>class="act_item"<?}?>  style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>4){?>none<?}?>;">
						<a onclick="InsertFavoriteMenu(1304, '<?=$강사소개영상[$LangID]?>', 'teacher_video_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1304, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1304"></a>
						<a href="teacher_video_list.php"><?=$강사소개영상[$LangID]?></a></li>
					
					<li <?if ($SubMenuID==1305){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1305, '강사 리뷰', 'teacher_review_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1301, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1305"></a>
						<a href="teacher_review_list.php">강사 리뷰</a></li>	
		
					<li <?if ($SubMenuID==1302){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1302, '<?=$강사그룹[$LangID]?>', 'teacher_group_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1302, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1302"></a>
						<a href="teacher_group_list.php" style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>4){?>none<?}?>;"><?=$강사그룹[$LangID]?></a></li>
					<!--<li <?if ($SubMenuID==1306){?>class="act_item"<?}?>><a href="class_type_list.php" style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>4){?>none<?}?>;">수업구조</a></li>-->
					<li <?if ($SubMenuID==1303){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1303, '<?=$교육센터[$LangID]?>', 'edu_center_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1303, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1303"></a>
						<a href="edu_center_list.php" style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>4){?>none<?}?>;"><?=$교육센터[$LangID]?></a></li>
					<li <?if ($SubMenuID==1307){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1307, '<?=$출신지역관리[$LangID]?>', 'teacher_pay_type_item_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1307, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1307"></a>
						<a href="teacher_pay_type_item_list.php"><?=$출신지역관리[$LangID]?></a></li>	
				<li <?if ($SubMenuID==1308){?>class="act_item"<?}?>>
					<a onclick="InsertFavoriteMenu(1308, '수업료 계산기', '../salary-calculator.html');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1308, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1308"></a>
					<a href="../salary-calculator.html">수업료 계산기</a></li>
				</ul>
			</li>

			<li title="<?=$학생관리[$LangID]?>" <?if ($MainMenuID==14){?>class="current_section"<?}?> style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>13){?>none<?}?>;">
				<a href="#">
					<span class="menu_icon"><i class="material-icons">face</i></span>
					<span class="menu_title"><?=$학생관리[$LangID]?></span>
				</a>
				<ul class="lms_left_menu">
					<li <?if ($SubMenuID==1402){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1402, '<?=$학생목록[$LangID]?>', 'student_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1402, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1402"></a>
						<a href="student_list.php"><?=$학생목록[$LangID]?></a></li>
					<li <?if ($SubMenuID==1404){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1404, '<?=$종료일자확인[$LangID]?>', 'student_crisis_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1404, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1404"></a>
						<a href="student_crisis_list.php"><?=$종료일자확인[$LangID]?></a></li>	

					<?if ($_LINK_ADMIN_LEVEL_ID_<2 ) {?>
					<li <?if ($SubMenuID==1409){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1409, '<?=$학생상세목록[$LangID]?>', 'student_detail_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1409, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1409"></a>
						<a href="student_detail_list.php"><?=$학생상세목록[$LangID]?></a></li>
					<?}?>
					
					<?if ($_LINK_ADMIN_LEVEL_ID_<12 || (($_LINK_ADMIN_LEVEL_ID_==12 || $_LINK_ADMIN_LEVEL_ID_==13 ) && $_LINK_ADMIN_CENTER_PAY_TYPE_==1) ){?>
					<li <?if ($SubMenuID==1405){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1405, '<?=$수강연장[$LangID]?>', 'class_order_renew_center_form.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1405, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1405"></a>
						<a href="class_order_renew_center_form.php"><?=$수강연장[$LangID]?></a></li>

                    <li <?if ($SubMenuID==1416){?>class="act_item"<?}?>>
                        <a onclick="InsertFavoriteMenu(1416, '<?=$수강연장_요약[$LangID]?>', 'class_order_renew_center_form_simple.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1416, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1416"></a>
                        <a href="class_order_renew_center_form_simple.php"><?=$수강연장_요약[$LangID]?></a></li>
					<?}?>
					
					<li <?if ($SubMenuID==1408){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1408, '<?=$수업현황[$LangID]?>', 'account_center_class_status.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1408, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1408"></a>
						<a href="account_center_class_status.php"><?=$수업현황[$LangID]?></a></li>
					
					<li <?if ($SubMenuID==1420){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1408, '<?=$월별평가서[$LangID]?>', 'monthy_report_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1420, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1420"></a>
						<a href="monthy_report_list.php"><?=$월별평가서[$LangID]?></a></li>
					
					<li <?if ($SubMenuID==1406){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1406, '<?=$출결현황[$LangID]?>', 'attend_status_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1406, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1406"></a>
						<a href="attend_status_list.php"><?=$출결현황[$LangID]?></a></li>
					<li <?if ($SubMenuID==1407){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1407, '<?=$연속결석[$LangID]?>', 'absent_status_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1407, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1407"></a>
						<a href="absent_status_list.php"><?=$연속결석[$LangID]?></a></li>
					
					<li <?if ($SubMenuID==1403){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1403, '<?=$상담내역[$LangID]?>', 'counsel_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1403, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1403"></a>
						<a href="counsel_list.php"><?=$상담내역[$LangID]?></a></li>
					<li <?if ($SubMenuID==1410){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1410, '<?=$탈락률[$LangID]?>', 'leaving_out.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1410, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1410"></a>
						<a href="leaving_out.php"><?=$탈락률[$LangID]?></a></li>
				</ul>
			</li>

			<li title="<?=$레벨테스트[$LangID]?>" <?if ($MainMenuID==15){?>class="current_section"<?}?> style="display:<?if ( $_LINK_ADMIN_LEVEL_ID_>4 && $_LINK_ADMIN_LEVEL_ID_!=12 && $_LINK_ADMIN_LEVEL_ID_!=13 ){?>none<?}?>;">
				<a href="#">
					<span class="menu_icon"><i class="material-icons">multiline_chart</i></span>
					<span class="menu_title"><?=$레벨테스트[$LangID]?></span>
				</a>
				<ul class="lms_left_menu">
					<li <?if ($SubMenuID==1501){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1501, '<?=$스케줄링대상[$LangID]?>', 'leveltest_apply_list.php?type=11');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1501, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1501"></a>
						<a href="leveltest_apply_list.php?type=11"><?=$스케줄링대상[$LangID]?></a></li>
					<li <?if ($SubMenuID==1502){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1502, '<?=$스케줄링완료[$LangID]?>', 'leveltest_apply_list.php?type=21');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1502, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1502"></a>
						<a href="leveltest_apply_list.php?type=21"><?=$스케줄링완료[$LangID]?></a></li>
					<li <?if ($SubMenuID==1505){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1505, '<?=$테스트완료[$LangID]?>', 'leveltest_apply_list.php?type=51');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1505, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1505"></a>
						<a href="leveltest_apply_list.php?type=51"><?=$테스트완료[$LangID]?></a></li>
					<li <?if ($SubMenuID==1506){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1506, '<?=$결석_미응시[$LangID]?>', 'leveltest_apply_list.php?type=61');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1506, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1506"></a>
						<a href="leveltest_apply_list.php?type=61"><?=$결석_미응시[$LangID]?></a></li>
				</ul>
			</li>

<!--            // 아이디가 snickerkoki 인 경우, 수강신청관리 메뉴가 보이지 않게 처리-->

            <?
            // 현재 로그인한 사용자의 아이디를 가져옵니다.
            $currentUserId = $_LINK_ADMIN_ID_;

            // alert msg : $currentUserId
//             echo "<script>alert('$currentUserId');</script>";

            // 아이디가 snickerkoki 인 경우, 수강신청관리 메뉴가 보이지 않게 처리
//            if ($currentUserId == 'snickerkoki') {
            if ($currentUserId == '91552') {
            ?>
            <!-- 수강신청관리 메뉴를 숨깁니다. -->

                <?php
            } else {
            ?>

            <li title="<?=$수강신청관리[$LangID]?>" <?if ($MainMenuID==16){?>class="current_section"<?}?> style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>13){?>none<?}?>;">
				<a href="#">
					<span class="menu_icon"><i class="material-icons">school</i></span>
					<span class="menu_title"><?=$수강신청관리[$LangID]?></span>
				</a>
				<ul class="lms_left_menu">
					<!--
					<li class="menu_subtitle">스케줄관리 기준으로 보기</li>
					-->


					<li <?if ($SubMenuID==1611){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1611, '<?=$스케줄링대상[$LangID]?>', 'class_order_list.php?type=11');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1611, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1611"></a>
						<a href="class_order_list.php?type=11"><?=$스케줄링대상[$LangID]?></a></li>
					<li <?if ($SubMenuID==1621){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1621, '<?=$스케줄링완료[$LangID]?>', 'class_order_list.php?type=21');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1621, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1621"></a>
						<a href="class_order_list.php?type=21"><?=$스케줄링완료[$LangID]?></a></li>
					<li <?if ($SubMenuID==1631){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1631, '<?=$수업종료대상[$LangID]?>', 'class_order_list.php?type=31');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1631, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1631"></a>
						<a href="class_order_list.php?type=31"><?=$수업종료대상[$LangID]?></a></li>
					<li <?if ($SubMenuID==1641){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1641, '<?=$수업종료완료[$LangID]?>', 'class_order_list.php?type=41');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1641, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1641"></a>
						<a href="class_order_list.php?type=41"><?=$수업종료완료[$LangID]?></a></li>
					<li <?if ($SubMenuID==1699){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1699, '<?=$장기홀드목록[$LangID]?>', 'class_order_list.php?type=99');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1699, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1699"></a>
						<a href="class_order_list.php?type=99"><?=$장기홀드목록[$LangID]?></a></li>

                    <?//if ($_LINK_ADMIN_LEVEL_ID_<=4){//검수기간 동안 숨기기?>
                    <li <?if ($SubMenuID==1688){?>class="act_item"<?}?>>
                        <a onclick="InsertFavoriteMenu(1688, '<?=$단체수강신청통합[$LangID]?>', 'javascript:OpenClassOrderBulkMerge()', 2);" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1688, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1688"></a>
                        <a href="javascript:OpenClassOrderBulkMerge()"><?=$단체수강신청통합[$LangID]?></a></li>
                    <?//}?>

				</ul>
			</li>

                <?php
            }
            ?>
			
			<li title="교재구매관리" <?if ($MainMenuID==33){?>class="current_section"<?}?> style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>=4){?>none<?}?>;">
				<a href="#">
					<span class="menu_icon"><i class="material-icons">shopping_cart</i></span>
					<span class="menu_title"><?=$교재구매관리[$LangID]?></span>
				</a>
				<ul class="lms_left_menu">
					<li <?if ($SubMenuID==3301){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(3301, '<?=$결제완료[$LangID]?>', 'product_order_list.php?type=11');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(3301, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_3301"></a>
						<a href="product_order_list.php?type=11"><?=$결제완료[$LangID]?></a></li>
					<li <?if ($SubMenuID==3302){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(3302, '<?=$발송완료[$LangID]?>', 'product_order_list.php?type=21');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(3302, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_3302"></a>
						<a href="product_order_list.php?type=21"><?=$발송완료[$LangID]?></a></li>
					<li <?if ($SubMenuID==3303){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(3303, '<?=$취소완료[$LangID]?>', 'product_order_list.php?type=31');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(3303, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_3303"></a>
						<a href="product_order_list.php?type=31"><?=$취소완료[$LangID]?></a></li>
				</ul>
			</li>

			<li title="<?=$수업관리[$LangID]?>" <?if ($MainMenuID==17){?>class="current_section"<?}?> style="display:<?if ( ($_LINK_ADMIN_LEVEL_ID_>13 && $_LINK_ADMIN_LEVEL_ID_!=15)){?>none<?}?>;">
				<a href="#">
					<span class="menu_icon"><i class="material-icons">laptop_mac</i></span>
					<span class="menu_title"><?=$수업관리[$LangID]?></span>
				</a>
				<ul class="lms_left_menu">
					<li <?if ($SubMenuID==1721){?>class="act_item"<?}?> style="display:<?if ($_LINK_ADMIN_LEVEL_ID_!=15){?>none<?}?>;">
						<a onclick="InsertFavoriteMenu(1721, '<?=$알림메시지[$LangID]?>', 'teacher_message_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1721, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1721"></a>
						<a href="teacher_message_list.php"><?=$알림메시지[$LangID]?></a></li>
					<li <?if ($SubMenuID==1701){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1701, '<?=$오늘수업[$LangID]?>', 'class_list.php?type=1');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1701, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1701"></a>
						<a href="class_list.php?type=1"><?=$오늘수업[$LangID]?></a></li>
					
					<li <?if ($SubMenuID==1718){?>class="act_item"<?}?> style="display:<?if ($_LINK_ADMIN_LEVEL_ID_!=15){?>none<?}?>;">
						<a onclick="InsertFavoriteMenu(1718, 'Class Attendance', 'teacher_enter_excel_form.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1718, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1718"></a>
						<a href="teacher_enter_excel_form.php">Class Attendance</a></li>
					
					<li <?if ($SubMenuID==1706){?>class="act_item"<?}?> style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>4){?>none<?}?>;">
						<a onclick="InsertFavoriteMenu(1706, '수업현황<?=$수업현황[$LangID]?>', 'teacher_class_count.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1706, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1706"></a>
						<a href="teacher_class_count.php"><?=$수업현황[$LangID]?></a></li>

					<li <?if ($SubMenuID==1709){?>class="act_item"<?}?> style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>4){?>none<?}?>;">
						<a onclick="InsertFavoriteMenu(1709, '<?=$연기_취소_변경[$LangID]?>', 'class_list.php?type=9');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1709, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1709"></a>
						<a href="class_list.php?type=9"><?=$연기_취소_변경[$LangID]?></a></li>
					<li <?if ($SubMenuID==1704){?>class="act_item"<?}?> style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>13){?>none<?}?>;">
						<a onclick="InsertFavoriteMenu(1704, '<?=$전체스케쥴[$LangID]?>(Date)', 'javascript:OpenClassSchedule()', 2);" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1704, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1704"></a>
						<a href="javascript:OpenClassSchedule()"><?=$전체스케쥴[$LangID]?>(Date)</a></li>
					<li <?if ($SubMenuID==1705){?>class="act_item"<?}?> style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>13){?>none<?}?>;">
						<a onclick="InsertFavoriteMenu(1705, '<?=$전체스케쥴[$LangID]?>(Teacher)', 'javascript:OpenClassScheduleByTeacher()', 2);" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1705, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1705"></a>
						<a href="javascript:OpenClassScheduleByTeacher()"><?=$전체스케쥴[$LangID]?>(Teacher)</a></li>


					<li class="menu_subtitle" style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>4){?>none<?}?>;"><?=$관리자처리대상[$LangID]?></li>
					<li <?if ($SubMenuID==1711){?>class="act_item"<?}?> style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>4){?>none<?}?>;">
						<a onclick="InsertFavoriteMenu(1711, '<?=$신규[$LangID]?>', 'class_qna_list.php?type=1');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1711, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1711"></a>
						<a href="class_qna_list.php?type=1"><?=$신규[$LangID]?></a></li>
					<li <?if ($SubMenuID==1712){?>class="act_item"<?}?> style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>4){?>none<?}?>;">
						<a onclick="InsertFavoriteMenu(1712, '<?=$진행중[$LangID]?>', 'class_qna_list.php?type=2');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1712, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1712"></a>
						<a href="class_qna_list.php?type=2"><?=$진행중[$LangID]?></a></li>
					<li <?if ($SubMenuID==1713){?>class="act_item"<?}?> style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>4){?>none<?}?>;">
						<a onclick="InsertFavoriteMenu(1713, '<?=$위임[$LangID]?>', 'class_qna_list.php?type=3');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1713, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1713"></a>
						<a href="class_qna_list.php?type=3"><?=$위임[$LangID]?></a></li>
					<li <?if ($SubMenuID==1714){?>class="act_item"<?}?> style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>4){?>none<?}?>;">
						<a onclick="InsertFavoriteMenu(1714, '<?=$완료_강사미확인[$LangID]?>', 'class_qna_list.php?type=4');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1714, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1714"></a>
						<a href="class_qna_list.php?type=4"><?=$완료_강사미확인[$LangID]?></a></li>
					<li <?if ($SubMenuID==1715){?>class="act_item"<?}?> style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>4){?>none<?}?>;">
						<a onclick="InsertFavoriteMenu(1715, '<?=$완료_강사확인[$LangID]?>', 'class_qna_list.php?type=5');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1715, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1715"></a>
						<a href="class_qna_list.php?type=5"><?=$완료_강사확인[$LangID]?></a></li>
					
				</ul>
			</li>



			<!--
			<li title="보고서관리" <?if ($MainMenuID==18){?>class="current_section"<?}?> style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>4){?>none<?}?>;">
				<a href="#">
					<span class="menu_icon"><i class="material-icons">insert_chart_outlined</i></span>
					<span class="menu_title">보고서관리</span>
				</a>
				<ul class="lms_left_menu">
					<li <?if ($SubMenuID==1801){?>class="act_item"<?}?>><a href="#monthy_attend_list.php">월별출결현황</a></li>
					<li <?if ($SubMenuID==1802){?>class="act_item"<?}?>><a href="#monthy_report_list.php">월별평가서</a></li>

					<li class="menu_subtitle">※ 월별평가</li>
					<li <?if ($SubMenuID==1802){?>class="act_item"<?}?>><a href="#report_eng_list.php">영어수업</a></li>
					<li <?if ($SubMenuID==1803){?>class="act_item"<?}?>><a href="#report_jpn_list.php">일본어수업</a></li>
					<li <?if ($SubMenuID==1804){?>class="act_item"<?}?>><a href="#report_chn_list.php">중국어수업</a></li>
				</ul>
			</li>
			-->


			<!-- 7월 31일까지 -->
			<!--
			<li title="그룹웨어" <?if ($MainMenuID==23){?>class="current_section"<?}?>>
				<a href="#">
					<span class="menu_icon"><i class="material-icons">supervisor_account</i></span>
					<span class="menu_title">그룹웨어</span>
				</a>
				<ul class="lms_left_menu">
					<li <?if ($SubMenuID==2301){?>class="act_item"<?}?>><a href="suggestion_list.php">소원수리</a></li>
					<li <?if ($SubMenuID==2302){?>class="act_item"<?}?>><a href="project_draft_list.php">그룹웨어</a></li>
				</ul>
			</li>
			-->
			<!-- 7월 31일까지 -->

			<li title="<?=$커뮤니티[$LangID]?>" <?if ($MainMenuID==24){?>class="current_section"<?}?> style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>4){?>none<?}?>;">
				<a href="#">
					<span class="menu_icon"><i class="material-icons">forum</i></span>
					<span class="menu_title"><?=$커뮤니티[$LangID]?></span>
				</a>
				<ul class="lms_left_menu">
					<li <?if ($SubMenuID==2403){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(2403, '<?=$공지사항[$LangID]?>', 'board_list.php?BoardCode=notice');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(2403, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_2403"></a>
						<a href="board_list.php?BoardCode=notice"><?=$공지사항[$LangID]?></a></li>
					<li <?if ($SubMenuID==2406){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(2406, '<?=$질문답변[$LangID]?>', 'board_list.php?BoardCode=qna');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(2406, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_2406"></a>
						<a href="board_list.php?BoardCode=qna"><?=$질문답변[$LangID]?></a></li>
					<!--<li <?if ($SubMenuID==2401){?>class="act_item"<?}?>><a href="direct_qna_no_member_list.php"><?=$비회원문의[$LangID]?></a></li>-->
					<li <?if ($SubMenuID==2402){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(2402, '<?=$일대일문의[$LangID]?>', 'direct_qna_member_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(2402, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_2402"></a>
						<a href="direct_qna_member_list.php"><?=$일대일문의[$LangID]?></a></li>
					<li <?if ($SubMenuID==2411){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(2411, '<?=$건의사항[$LangID]?>', 'suggestion_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(2411, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_2411"></a>
						<a href="suggestion_list.php"><?=$건의사항[$LangID]?></a></li>
					<li <?if ($SubMenuID==2405){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(2405, '<?=$이벤트[$LangID]?>', 'event_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(2405, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_2405"></a>
						<a href="event_list.php"><?=$이벤트[$LangID]?></a></li>
					<li <?if ($SubMenuID==2407){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(2407, '<?=$FAQ[$LangID]?>', 'faq_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(2407, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_2407"></a>
						<a href="faq_list.php"><?=$FAQ[$LangID]?></a></li>
					<li <?if ($SubMenuID==2412){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(2412, '<?=$수강후기[$LangID]?>', 'mypage_review_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(2412, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_2412"></a>
						<a href="mypage_review_list.php"><?=$수강후기[$LangID]?></a></li>
					<li <?if ($SubMenuID==2409){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(2409, '<?=$원격지원목록[$LangID]?>', 'remote_support_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(2409, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_2409"></a>
						<a href="remote_support_list.php"><?=$원격지원목록[$LangID]?></a></li>
				</ul>
			</li>

			<li title="자료실" <?if ($MainMenuID==28){?>class="current_section"<?}?> style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>4){?>none<?}?>;">
				<a href="#">
					<span class="menu_icon"><i class="material-icons">business_center</i></span>
					<span class="menu_title"><?=$자료실[$LangID]?></span>
				</a>
				<ul class="lms_left_menu">
					<li <?if ($SubMenuID==2808){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(2808, '<?=$학습자료실[$LangID]?>', 'board_list.php?BoardCode=reference');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(2808, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_2808"></a>
						<a href="board_list.php?BoardCode=reference"><?=$학습자료실[$LangID]?></a></li>
					<li <?if ($SubMenuID==28081){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(28081, '<?=$지사자료실[$LangID]?>', 'board_list.php?BoardCode=branch');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(28081, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_28081"></a>
						<a href="board_list.php?BoardCode=branch"><?=$지사자료실[$LangID]?></a></li>
					<li <?if ($SubMenuID==28082){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(28082, '<?=$대리점자료실[$LangID]?>', 'board_list.php?BoardCode=center');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(28082, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_28082"></a>
						<a href="board_list.php?BoardCode=center"><?=$대리점자료실[$LangID]?></a></li>
					<li <?if ($SubMenuID==28083){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(28083, '<?=$기타자료실[$LangID]?>', 'board_list.php?BoardCode=etc');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(28083, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_28083"></a>
						<a href="board_list.php?BoardCode=etc"><?=$기타자료실[$LangID]?></a></li>
					<li <?if ($SubMenuID==2804){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(2804, '<?=$자료교환[$LangID]?>', 'teacher_data_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(2804, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_2804"></a>
						<a href="teacher_data_list.php"><?=$자료교환[$LangID]?></a></li>
				</ul>
			</li>

			<li title="<?=$교재콘텐츠관리[$LangID]?>" <?if ($MainMenuID==25){?>class="current_section"<?}?> style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>4){?>none<?}?>;">
				<a href="#">
					<span class="menu_icon"><i class="material-icons">create_new_folder</i></span>
					<span class="menu_title"><?=$교재콘텐츠관리[$LangID]?></span>
				</a>
				<ul class="lms_left_menu">
					<li class="menu_subtitle"><?=$콘텐츠_교재_관리[$LangID]?></li>
					
					<li <?if ($SubMenuID==2501){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(2501, '<?=$교재관리[$LangID]?>', 'book_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(2501, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_2501"></a>
						<a href="book_list.php"><?=$교재관리[$LangID]?></a></li>
					<li <?if ($SubMenuID==2502){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(2502, '<?=$교재그룹관리[$LangID]?>', 'book_gourp_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(2502, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_2502"></a>
						<a href="book_gourp_list.php"><?=$교재그룹관리[$LangID]?></a></li>

					<li class="menu_subtitle"><?=$판매_교재_관리[$LangID]?></li>

					<li <?if ($SubMenuID==2511){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(2511, '<?=$판매교재관리[$LangID]?>', 'product_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(2511, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_2511"></a>
						<a href="product_list.php"><?=$판매교재관리[$LangID]?></a></li>
					<li <?if ($SubMenuID==2512){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(2512, '<?=$판매교재그룹관리[$LangID]?>', 'product_category_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(2512, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_2512"></a>
						<a href="product_category_list.php"><?=$판매교재그룹관리[$LangID]?></a></li>
					<li <?if ($SubMenuID==2513){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(2513, '<?=$판매구분관리[$LangID]?>', 'product_seller_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(2513, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_2512"></a>
						<a href="product_seller_list.php"><?=$판매구분관리[$LangID]?></a></li>
				</ul>
			</li>
			
			<li title="<?=$정산통계관리[$LangID]?>" <?if ($MainMenuID==21){?>class="current_section"<?}?> style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>10){?>none<?}?>;">
				<a href="#">
					<span class="menu_icon"><i class="material-icons">exposure</i></span>
					<span class="menu_title"><?=$정산통계관리[$LangID]?></span>
				</a>
				<ul class="lms_left_menu">
					<li <?if ($SubMenuID==2101){?>class="act_item"<?}?> style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>4){?>none<?}?>;">
						<a onclick="InsertFavoriteMenu(2101, '<?=$지사별정산[$LangID]?>', 'account_branch.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(2101, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_2101"></a>
						<a href="account_branch.php"><?=$지사별정산[$LangID]?></a></li>
					
					<?if (($_LINK_ADMIN_LEVEL_ID_==6 || $_LINK_ADMIN_LEVEL_ID_==7) && ($_LINK_ADMIN_BRANCH_GROUP_ID_==26)){?>

					<?}else{?>
						<li <?if ($SubMenuID==2105){?>class="act_item"<?}?>>
							<a onclick="InsertFavoriteMenu(2105, '<?=$대리점별정산[$LangID]?>', 'account_center.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(2105, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_2105"></a>
							<a href="account_center.php"><?=$대리점별정산[$LangID]?></a></li>
					<?}?>

						

					<?if ( (($_LINK_ADMIN_LEVEL_ID_==6 || $_LINK_ADMIN_LEVEL_ID_==7) && ($_LINK_ADMIN_BRANCH_GROUP_ID_==26 )) || $_LINK_ADMIN_LEVEL_ID_<=4){//SLP 지사?>
						<li <?if ($SubMenuID==21051){?>class="act_item"<?}?>>
							<a onclick="InsertFavoriteMenu(21051, '<?=$SLP_정산_상세[$LangID]?>', 'account_center_slp.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(21051, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_21051"></a>
							<a href="account_center_slp.php"><?=$SLP_정산_상세[$LangID]?></a></li>
						
						<li <?if ($SubMenuID==21052){?>class="act_item"<?}?>>
							<a onclick="InsertFavoriteMenu(21052, '<?=$SLP_정산_학당지원금[$LangID]?>', 'account_center_slpmangoi.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(21052, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_21052"></a>
							<a href="account_center_slpmangoi.php"><?=$SLP_정산_학당지원금[$LangID]?></a></li>
						<li <?if ($SubMenuID==21053){?>class="act_item"<?}?>>
							<a onclick="InsertFavoriteMenu(21053, '<?=$SLP_정산_본사로얄티[$LangID]?>', 'account_center_slpmangoi_2.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(21053, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_21053"></a>
							<a href="account_center_slpmangoi_2.php"><?=$SLP_정산_본사로얄티[$LangID]?></a></li>
						<li <?if ($SubMenuID==21054){?>class="act_item"<?}?>>
							<a onclick="InsertFavoriteMenu(21054, '<?=$SLP_수업현황[$LangID]?>', 'account_center_slpmangoi_class_status.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(21054, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_21054"></a>
							<a href="account_center_slpmangoi_class_status.php"><?=$SLP_수업현황[$LangID]?></a></li>
					<?}?>

					<li <?if ($SubMenuID==2104){?>class="act_item"<?}?> style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>4){?>none<?}?>;">
						<a onclick="InsertFavoriteMenu(2104, '<?=$강사별정산[$LangID]?>', 'account_teacher.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(2104, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_2104"></a><a href="account_teacher.php"><?=$강사별정산[$LangID]?></a></li>
					<li <?if ($SubMenuID==2102){?>class="act_item"<?}?> style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>4){?>none<?}?>;">
						<a onclick="InsertFavoriteMenu(2102, '<?=$본사매출통계[$LangID]?>', 'account_total.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(2102, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_2102"></a><a href="account_total.php"><?=$본사매출통계[$LangID]?></a></li>
					<li <?if ($SubMenuID==2106){?>class="act_item"<?}?> style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>4){?>none<?}?>;">
						<a onclick="InsertFavoriteMenu(2106, '<?=$학생수업통계[$LangID]?>', 'account_study_total.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(2106, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_2106"></a><a href="account_study_total.php"><?=$학생수업통계[$LangID]?></a></li>
					<li <?if ($SubMenuID==2107){?>class="act_item"<?}?> style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>4){?>none<?}?>;">
						<a onclick="InsertFavoriteMenu(2107, '<?=$지사수업통계[$LangID]?>', 'account_branch_study.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(2107, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_2107"></a><a href="account_branch_study.php"><?=$지사수업통계[$LangID]?></a></li>
					<!--	
					<li <?if ($SubMenuID==2109){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(2109, '<?=$대리점수업통계[$LangID]?>', 'account_center_study.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(2109, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_2109"></a><a href="account_center_study.php"><?=$대리점수업통계[$LangID]?></a></li>
					-->	
					<li <?if ($SubMenuID==2108){?>class="act_item"<?}?> style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>4){?>none<?}?>;">
						<a onclick="InsertFavoriteMenu(2108, '<?=$교사수업통계[$LangID]?>', 'account_teacher_study.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(2108, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_2108"></a><a href="account_teacher_study.php"><?=$교사수업통계[$LangID]?></a></li>
	<?
	if ($_LINK_ADMIN_LEVEL_ID_==0 || $_LINK_ADMIN_LEVEL_ID_==1){    //마스터
	?>
					<li <?if ($SubMenuID==2103){?>class="act_item"<?}?> style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>4){?>none<?}?>;">
						<a onclick="InsertFavoriteMenu(2103, '<?=$통계그래프[$LangID]?>', 'account_graph_total.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(2103, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_2103"></a><a href="account_graph_total.php"><?=$통계그래프[$LangID]?></a></li>
	<?
	}else if ($_LINK_ADMIN_LEVEL_ID_==9 || $_LINK_ADMIN_LEVEL_ID_==10){    //지사 관리자
    ?>
					<!--
					<li <?if ($SubMenuID==2104){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(2104, '<?=$통계그래프[$LangID]?>', 'account_graph_branch.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(2103, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_2104"></a><a href="account_graph_branch.php"><?=$통계그래프[$LangID]?></a></li>
					-->	
					<li <?if ($SubMenuID==21041){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(21041, '학생수 데이터', 'number_of_student_branch.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(21041, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_21041"></a><a href="number_of_student_branch.php">학생수 데이터</a></li>
					<li <?if ($SubMenuID==21042){?>class="act_item"<?}?>>
                        <a onclick="InsertFavoriteMenu(21042, '커미션 데이터', 'commision_branch.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(21042, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_21042"></a><a href="commision_branch.php">커미션 데이터</a></li
    <?
    }
	?>

    <?
    if ($_LINK_ADMIN_LEVEL_ID_==0 || $_LINK_ADMIN_LEVEL_ID_==1 || $_LINK_ADMIN_LEVEL_ID_==6 || $_LINK_ADMIN_LEVEL_ID_==7) {
    // 마스터와 대표지사 관리자 모두에게 두 개의 추가 메뉴를 노출
    ?>
                    <li <?if ($SubMenuID==21044){?>class="act_item"<?}?>>
                        <a onclick="InsertFavoriteMenu(21044, '커미션 데이터 (상세보기)', 'commision_branch_detail.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(21044, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_21044"></a><a href="commision_branch_detail.php">커미션 데이터 (상세보기)</a></li>
                    <li <?if ($SubMenuID==21043){?>class="act_item"<?}?>>
                        <a onclick="InsertFavoriteMenu(21043, '커미션 데이터 (지사별 합계)', 'commision_branch_summary.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(21043, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_21042"></a><a href="commision_branch_summary.php">커미션 데이터 (지사별 합계)</a></li>

    <?
    }
    ?>
            </li>
            </ul>
        </li>

			<li title="<?=$그룹웨어_마이룸[$LangID]?>" <?if ($MainMenuID==29){?>class="current_section"<?}?> style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>4 AND $_LINK_ADMIN_LEVEL_ID_<13){?>none<?}?>;">
				<a href="#">
					<span class="menu_icon"><i class="material-icons">group_work</i></span>
					<span class="menu_title"><?=$그룹웨어_마이룸[$LangID]?></span>
				</a>
				<ul class="lms_left_menu">
					<li <?if ($SubMenuID==2921){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(2921, '<?=$휴가_및_병가원[$LangID]?>', 'my_document_holiday_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(2921, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_2921"></a>
						<a href="my_document_holiday_list.php"><?=$휴가_및_병가원[$LangID]?></a></li>
					<li <?if ($SubMenuID==2923){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(2923, '<?=$기안_및_지출서[$LangID]?>', 'my_document_draft_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(2923, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_2923"></a>
						<a href="my_document_draft_list.php"><?=$기안_및_지출서[$LangID]?></a></li>
					
									
					<li <?if ($SubMenuID==2922){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(2922, '<?=$확인할문서[$LangID]?>', 'my_document_comfirm_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(2922, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_2922"></a>
						<a href="my_document_comfirm_list.php"><?=$확인할문서[$LangID]?></a></li>
					<?if ($_LINK_ADMIN_LEVEL_ID_<13) {?>	
					<li <?if ($SubMenuID==2912){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(2912, '<?=$관리메시지[$LangID]?>', 'master_message_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>=13 ){?>none<?}?>;"><img src="<?if(array_key_exists(2912, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_2912"></a>
						<a href="master_message_list.php"><?=$관리메시지[$LangID]?></a></li>
					<li <?if ($SubMenuID==2913){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(2913, '<?=$즐겨찾기[$LangID]?>', 'favorite_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(2913, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_2913"></a>
						<a href="favorite_list.php"><?=$즐겨찾기[$LangID]?></a></li>
					<?}?>	
				</ul>
			</li>
			
			<?if ($_LINK_ADMIN_LEVEL_ID_>=0 && $_LINK_ADMIN_LEVEL_ID_<=15 ){

			#-----------------------------------------------------------------------------------------------------------------------------------------#
			# 회원 고유아이디(번호, 조직아이디) 찾기
			#-----------------------------------------------------------------------------------------------------------------------------------------#
			$Sql = "SELECT O.* from Members as M 
						left join Hr_OrganLevelTaskMembers T on T.MemberID=M.MemberID
						LEFT JOIN Hr_OrganLevels O on T.Hr_OrganLevelID = O.Hr_OrganLevelID
							where M.MemberLoginID=:MemberLoginID";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':MemberLoginID', $_LINK_ADMIN_LOGIN_ID_);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);
			$Row = $Stmt->fetch();
			$My_OrganLevelID  = $Row["Hr_OrganLevelID"];    
			$My_OrganLevel   = $Row["Hr_OrganLevel"];  
			if ($My_OrganLevel=="") $My_OrganLevel = 100; //만약 조직에 소속이 안된 사람이라면 임의로 100을 입력  

			#-----------------------------------------------------------------------------------------------------------------------------------------#
			?>	
			<li title="성과평가 시스템" <?if ($MainMenuID==88){?>class="current_section"<?}?> style="display:<?if ($_LINK_ADMIN_LEVEL_ID_==4 || $_LINK_ADMIN_LEVEL_ID_==0 || $_LINK_ADMIN_LEVEL_ID_>=13){?><?}else{?>none<?}?>;">
				<a href="#">
					<span class="menu_icon"><i class="material-icons">nature_people</i></span>
					<span class="menu_title"><?=$성과평가_시스템[$LangID]?></span>
				</a>
				<ul class="lms_left_menu">
					
					<?if ($_LINK_ADMIN_LEVEL_ID_==0){?>
						<li class="menu_subtitle"><?=$조직_인원_관리[$LangID]?></li>
						
						<li <?if ($SubMenuID==8802){?>class="act_item"<?}?>>
							<a onclick="InsertFavoriteMenu(8802, '<?=$조직관리[$LangID]?>', 'hr_organ_level_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(8802, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_8802"></a>
							<a href="hr_organ_level_list.php"><?=$조직관리[$LangID]?></a></li>
						<li <?if ($SubMenuID==8803){?>class="act_item"<?}?>>
							<a onclick="InsertFavoriteMenu(8803, '<?=$직무관리[$LangID]?>', 'hr_organ_task_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(8803, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_8803"></a>
							<a href="hr_organ_task_list.php"><?=$직무관리[$LangID]?></a></li>

						<li <?if ($SubMenuID==8804){?>class="act_item"<?}?>>
							<a onclick="InsertFavoriteMenu(8804, '<?=$업적평가_조직도[$LangID]?>', 'hr_evaluation_organ_table.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(8804, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_8804"></a>
							<a href="hr_evaluation_organ_table.php"><?=$업적평가_조직도[$LangID]?></a></li>

						<li <?if ($SubMenuID==8808){?>class="act_item"<?}?>>
							<a onclick="InsertFavoriteMenu(8808, '<?=$역량평가_조직도[$LangID]?>', 'hr_evaluation_competency_table.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(8808, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_8808"></a>
							<a href="hr_evaluation_competency_table.php"><?=$역량평가_조직도[$LangID]?></a></li>

						<li <?if ($SubMenuID==8809){?>class="act_item"<?}?>>
							<a onclick="InsertFavoriteMenu(8809, '<?=$직무별_역량관리[$LangID]?>', 'hr_competency_indicator_task.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(8809, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_8809"></a>
							<a href="hr_competency_indicator_task.php"><?=$직무별_역량관리[$LangID]?></a></li>

						<li <?if ($SubMenuID==8805){?>class="act_item"<?}?>>
							<a onclick="InsertFavoriteMenu(8805, '<?=$역량평가_문항관리[$LangID]?>', 'hr_competency_indicator_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(8805, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_8805"></a>
							<a href="hr_competency_indicator_list.php"><?=$역량평가_문항관리[$LangID]?></a></li>

						<li <?if ($SubMenuID==8806){?>class="act_item"<?}?>>
							<a onclick="InsertFavoriteMenu(8806, '<?=$KPI_문항관리[$LangID]?>', 'hr_kpi_indicator_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(8806, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_8806"></a>
							<a href="hr_kpi_indicator_list.php"><?=$KPI_문항관리[$LangID]?></a></li>

						<li <?if ($SubMenuID==8811){?>class="act_item"<?}?>>
							<a onclick="InsertFavoriteMenu(8807, '<?=$평가등록[$LangID]?>', 'hr_evaluation_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(8807, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_8807"></a>
							<a href="hr_evaluation_list.php"><?=$평가등록[$LangID]?></a></li>

					
						<li class="menu_subtitle"><?=$평가_진행관리[$LangID]?></li>

						<li <?if ($SubMenuID==8821){?>class="act_item"<?}?>>
							<a onclick="InsertFavoriteMenu(8821, '<?=$목표설정현황[$LangID]?>', 'hr_staffall_target_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(8821, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_8821"></a>
							<a href="hr_staffall_target_list.php"><?=$목표설정현황[$LangID]?></a></li>
						<li <?if ($SubMenuID==8822){?>class="act_item"<?}?>>
							<a onclick="InsertFavoriteMenu(8822, '<?=$업적평가현황[$LangID]?>', 'hr_staffall_evaluation_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(8822, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_8822"></a>
							<a href="hr_staffall_evaluation_list.php"><?=$업적평가현황[$LangID]?></a></li>
						<li <?if ($SubMenuID==8825){?>class="act_item"<?}?>>
							<a onclick="InsertFavoriteMenu(8825, '<?=$역량평가현황[$LangID]?>', 'hr_book_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(8825, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_8825"></a>
							<a href="hr_staffall_evaluation_competency_list.php"><?=$역량평가현황[$LangID]?></a></li>
						<li <?if ($SubMenuID==8826){?>class="act_item"<?}?>>
							<a onclick="InsertFavoriteMenu(8826, '성과평가마감', 'hr_book_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(8826, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_8826"></a>
							<a href="hr_evaluation_finishing.php">성과평가마감</a></li>	



						<li class="menu_subtitle"><?=$평가_결과관리[$LangID]?></li>

						<!----li <?if ($SubMenuID==8831){?>class="act_item"<?}?>>
							<a onclick="InsertFavoriteMenu(8831, '등급기준', 'hr_book_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(8831, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_8831"></a>
							<a href="#hr_book_list.php">등급기준</a></li---->
						<li <?if ($SubMenuID==8833){?>class="act_item"<?}?>>
							<a onclick="InsertFavoriteMenu(8833, '<?=$결과관리[$LangID]?>', 'hr_book_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(8833, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_8833"></a>
							<a href="hr_staffall_indicator_list.php"><?=$결과관리[$LangID]?></a></li>
						<li <?if ($SubMenuID==8834){?>class="act_item"<?}?>>
							<a onclick="InsertFavoriteMenu(8834, '<?=$DB다운로드[$LangID]?>', 'hr_book_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(8834, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_8834"></a>
							<a href="hr_staffall_dbdownload.php"><?=$DB다운로드[$LangID]?></a></li>
						<!-- <li <?if ($SubMenuID==8835){?>class="act_item"<?}?>>
							<a onclick="InsertFavoriteMenu(8835, '<?=$인센티브계산[$LangID]?>', 'hr_book_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(8835, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_8835"></a>
							<a href="#hr_book_list.php"><?=$인센티브계산[$LangID]?></a></li> -->
					
					<?}else{?>

						<li <?if ($SubMenuID==8851){?>class="act_item"<?}?>>
							<a onclick="InsertFavoriteMenu(8851, '<?=$목표설정[$LangID]?>', 'hr_staff_target_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(8851, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_8851"></a>
							<a href="hr_staff_target_list.php"><?=$목표설정[$LangID]?></a></li>

						<li <?if ($SubMenuID==8861){?>class="act_item"<?}?>>
							<a onclick="InsertFavoriteMenu(8861, '<?=$업적평가실시[$LangID]?>', 'hr_staff_evaluation_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(8861, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_8861"></a>
							<a href="hr_staff_evaluation_list.php"><?=$업적평가실시[$LangID]?></a></li>

						<li <?if ($SubMenuID==8862){?>class="act_item"<?}?>>
							<a onclick="InsertFavoriteMenu(8862, '<?=$역량평가실시[$LangID]?>', 'hr_staff_competency_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(8862, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_8862"></a>
							<a href="hr_staff_competency_list.php"><?=$역량평가실시[$LangID]?></a></li>

						<li <?if ($SubMenuID==8863){?>class="act_item"<?}?>>
							<a onclick="InsertFavoriteMenu(8863, '<?=$평가결과[$LangID]?>', 'hr_staff_indicator_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(8863, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_8863"></a>
							<a href="hr_staff_indicator_list.php"><?=$평가결과[$LangID]?></a></li>
						<? if ($My_OrganLevel < 4) { ?>	
						<li <?if ($SubMenuID==8864){?>class="act_item"<?}?>>
							<a onclick="InsertFavoriteMenu(8864, '<?=$부서원평가결과[$LangID]?>', 'hr_staffteam_indicator_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(8863, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_8864"></a>
							<a href="hr_staffteam_indicator_list.php"><?=$부서원평가결과[$LangID]?></a></li>	
						<? } ?>	

					<?}?>

					<li class="menu_subtitle"><?=$인사평가자료실[$LangID]?></li>
					<li <?if ($SubMenuID==8841){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(8841, '<?=$인사평가자료실[$LangID]?>', 'board_list.php?BoardCode=hrfile');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(8841, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_8841"></a>
						<a href="board_list.php?BoardCode=hrfile"><?=$인사평가자료실[$LangID]?></a></li>
				</ul>
			</li>

			
			
			<li title="회계/급여 관리" <?if ($MainMenuID==77){?>class="current_section"<?}?> style="display:<?if ($_LINK_ADMIN_LEVEL_ID_==4 || $_LINK_ADMIN_LEVEL_ID_==0){?><?}else{?>none<?}?>;">
				<a href="#">
					<span class="menu_icon"><i class="material-icons">attach_money</i></span>
					<span class="menu_title">회계/급여 관리</span>
					
				</a>
				<ul class="lms_left_menu">
					
					<?if ($My_OrganLevelID == 1 || $My_OrganLevelID == 18 || $My_OrganLevelID == 19 || $_LINK_ADMIN_LEVEL_ID_ == 0){?>
						<li <?if ($SubMenuID==7710){?>class="act_item"<?}?>>
							<a onclick="InsertFavoriteMenu(7710, '<?=$회계관리[$LangID]?>', 'account_book.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(7710, $ArrFavoriteLmsMenuSubMenuID)) { ?> http://mangoidev.hihome.kr/lms/images/star_click.pngstar_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_7710"></a>
							<a href="account_book.php"><?=$회계관리[$LangID]?></a></li>	
						<li <?if ($SubMenuID==7701){?>class="act_item"<?}?>>
							<a onclick="InsertFavoriteMenu(7701, '<?=$급여기본정보관리[$LangID]?>', 'pay_info.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(7701, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_7701"></a>
							<a href="pay_info.php"><?=$급여기본정보관리[$LangID]?></a></li>
						<li <?if ($SubMenuID==7702){?>class="act_item"<?}?>>
							<a onclick="InsertFavoriteMenu(7702, '<?=$급여관리[$LangID]?>', 'pay.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(7702, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_7702"></a>
							<a href="pay.php"><?=$급여관리[$LangID]?></a></li>
						<li <?if ($SubMenuID==7708){?>class="act_item"<?}?>>
							<a onclick="InsertFavoriteMenu(7708, '<?=$사대보험요율관리[$LangID]?>', 'pay_insurance_rate_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(7708, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_7708"></a>
							<a href="pay_insurance_rate_list.php"><?=$사대보험요율관리[$LangID]?></a></li>	
						<li <?if ($SubMenuID==7721){?>class="act_item"<?}?>>
							<a onclick="InsertFavoriteMenu(7721, '카드비용 날짜관리', 'card_money_date_form.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(7721, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_7721"></a>
							<a href="card_money_date_form.php">카드비용 날짜관리</a></li>	
						<li <?if ($SubMenuID==7709){?>class="act_item"<?}?>>
							<a onclick="InsertFavoriteMenu(7709, '<?=$과세항목설정[$LangID]?>', 'pay_tax_info_form.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(7709, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_7709"></a>
							<a href="pay_tax_info_form.php"><?=$과세항목설정[$LangID]?></a></li>		
						<li <?if ($SubMenuID==7731){?>class="act_item"<?}?>>	
							<a onclick="InsertFavoriteMenu(7731, '공제항목설정', 'pay_deduction_info_form.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(7731, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_7731"></a>
							<a href="pay_deduction_info_form.php">공제항목설정</a></li>	
					<?} ?>
					<? if ($My_OrganLevel == 1 || $My_OrganLevelID == 18 || $My_OrganLevelID == 19) { ?>
						<li <?if ($SubMenuID==7703){?>class="act_item"<?}?>>
							<a onclick="InsertFavoriteMenu(7703, '<?=$급여결재하기[$LangID]?>', 'pay_confirm_form.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(7703, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_7703"></a>
							<a href="pay_confirm_form.php"><?=$급여결재하기[$LangID]?></a></li>
					<?}?>
					<? if ($My_OrganLevel == 4) { ?>
						<li <?if ($SubMenuID==7704){?>class="act_item"<?}?>>
							<a onclick="InsertFavoriteMenu(7704, '<?=$초과근무수당작성[$LangID]?>', 'overtimepay_form.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(7704, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_7704"></a>
							<a href="overtimepay_form.php"><?=$초과근무수당작성[$LangID]?></a></li>
					<?}?>		
					<? if ($My_OrganLevel == 3) { ?>
						<li <?if ($SubMenuID==7705){?>class="act_item"<?}?>>
							<a onclick="InsertFavoriteMenu(7705, '<?=$초과근무수당결재[$LangID]?>', 'overtimepay_confirm.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(7705, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_7704"></a>
							<a href="overtimepay_confirm.php"><?=$초과근무수당결재[$LangID]?></a></li>
					<?}?>		
					<?if ($_LINK_ADMIN_LEVEL_ID_ == 0 || $_LINK_ADMIN_LEVEL_ID_ == 1 ) {?>
						<li <?if ($SubMenuID==7706){?>class="act_item"<?}?>>
							<a onclick="InsertFavoriteMenu(7706, '<?=$급여열람권한[$LangID]?>', 'pay_auth.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;" ><img src="<?if(array_key_exists(7702, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_7706"></a>
							<a href="pay_auth.php"><?=$급여열람권한[$LangID]?></a></li>
					<?} ?>	
					<?if ($_LINK_ADMIN_LEVEL_ID_ == 0 || $My_OrganLevel <= 4) {?>
						<li <?if ($SubMenuID==7707){?>class="act_item"<?}?>>
							<a onclick="InsertFavoriteMenu(7707, '<?=$급여열람[$LangID]?>', 'pay_view.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;" ><img src="<?if(array_key_exists(7702, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_7707"></a>
							<a href="pay_view.php"><?=$급여열람[$LangID]?></a></li>
						<li <?if ($SubMenuID==7712){?>class="act_item"<?}?>>
							<a onclick="InsertFavoriteMenu(7712, '<?=$본인급여열람[$LangID]?>', 'pay_self_view.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;" ><img src="<?if(array_key_exists(7712, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_7712"></a>
							<a href="pay_self_view.php"><?=$본인급여열람[$LangID]?></a></li>	
					<?} ?>	

				</ul>
			</li>
			<?}?>


            <? if ($_LINK_ADMIN_LEVEL_ID_ <= 4) { ?>
            <li title="<?=$운영관리[$LangID]?>" <?if ($MainMenuID==11){?>class="current_section"<?}?>>

				<a href="#">
					<span class="menu_icon"><i class="material-icons">settings</i></span>
					<span class="menu_title"><?=$운영관리[$LangID]?></span>
				</a>
				<ul class="lms_left_menu">
					<?if ($_LINK_ADMIN_LEVEL_ID_<=2){?>
						<li <?if ($SubMenuID==1122){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1101, '<?=$내정보관리[$LangID]?>', 'member_form.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1101, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1101"></a>
						<a href="member_form.php"><?=$내정보관리[$LangID]?></a></li>
					<?}?>
	
					<?if ($_LINK_ADMIN_LEVEL_ID_==4){?>
						<li <?if ($SubMenuID==1101){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1101, '<?=$내정보관리[$LangID]?>', 'staff_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1101, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1101"></a>
						<a href="staff_list.php"><?=$내정보관리[$LangID]?></a></li>
					<?}else{?>
						<li <?if ($SubMenuID==1101){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1101, '<?=$직원관리[$LangID]?>', 'staff_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1101, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1101"></a>
						<a href="staff_list.php"><?=$직원관리[$LangID]?></a></li>
					<?}?>
					<?if ($_ADMIN_LEVEL_ID_==0 || $_ADMIN_LEVEL_ID_==1) {?>
						<li <?if ($SubMenuID==1102){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1102, '<?=$직원휴가관리[$LangID]?>', 'staff_holiday_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1102, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1102"></a>
						<a href="staff_holiday_list.php"><?=$직원휴가관리[$LangID]?></a></li>
					<?}?>
					<?if ($_ADMIN_LEVEL_ID_==0 || $_ADMIN_LEVEL_ID_==1) {?>
						<li <?if ($SubMenuID==1188){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1188, '<?=$부서관리[$LangID]?>', 'departments_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1102, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1188"></a>
						<a href="departments_list.php"><?=$부서관리[$LangID]?></a></li>
						<li <?if ($SubMenuID==1177){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1177, '환율관리', 'currency_form.php?CountryCode=PH');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1102, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1177"></a>
						<a href="currency_form.php?CountryCode=PH">환율관리</a></li>
						<li <?if ($SubMenuID==1199){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1199, '결재라인관리', 'approval_line_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1102, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1199"></a>
						<a href="approval_line_list.php">결재라인관리</a></li>
					<?}?>


					<li <?if ($SubMenuID==1121){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1121, '<?=$보고서양식[$LangID]?>', 'document_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1121, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1121"></a>
						<a href="document_list.php"><?=$보고서양식[$LangID]?></a></li>
					<li <?if ($SubMenuID==1111){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1111, '<?=$메시지내역[$LangID]?>', 'send_message_log_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1111, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1111"></a>
						<a href="send_message_log_list.php"><?=$메시지내역[$LangID]?></a></li>
					<li <?if ($SubMenuID==1122){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1122, '<?=$베스트강사[$LangID]?>', 'teacher_best_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1122, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1122"></a>
						<a href="teacher_best_list.php"><?=$베스트강사[$LangID]?></a></li>
					<li <?if ($SubMenuID==1123){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1123, '<?=$쿠폰관리[$LangID]?>', 'coupon_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1123, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1123"></a>
						<a href="coupon_list.php"><?=$쿠폰관리[$LangID]?></a></li>
					<li <?if ($SubMenuID==1124){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1124, '<?=$지사미수관리[$LangID]?>', 'branch_account_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1124, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1124"></a>
						<a href="branch_account_list.php"><?=$지사미수관리[$LangID]?></a></li>
					<li <?if ($SubMenuID==1131){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1131, '<?=$B2C_결제관리[$LangID]?>', 'b2c_payment_list.php');" style="display: <?if (!in_array($_LINK_ADMIN_LEVEL_ID_, [9,10,12,13])){?>none<?}?>;"><img src="<?if(array_key_exists(1131, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1131"></a>
						<a href="b2c_payment_list.php"><?=$B2C_결제관리[$LangID]?></a></li>
					<li <?if ($SubMenuID==1132){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1132, '<?=$B2B_결제관리[$LangID]?>', 'b2b_payment_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1132, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1132"></a>
						<a href="b2b_payment_list.php"><?=$B2B_결제관리[$LangID]?></a></li>
					<li <?if ($SubMenuID==1135){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1135, '<?=$교재결제관리[$LangID]?>', 'product_payment_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1135, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1135"></a>
						<a href="product_payment_list.php"><?=$교재결제관리[$LangID]?></a></li>
					<li <?if ($SubMenuID==1133){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1133, '<?=$대리점_수강연장_현황[$LangID]?>', 'center_class_renew_status.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1133, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1133"></a>
						<a href="center_class_renew_status.php"><?=$대리점_수강연장_현황[$LangID]?></a></li>
					<li <?if ($SubMenuID==1107){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1107, '<?=$강사출근현황[$LangID]?>', 'teacher_attend_excel_form.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1107, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1107"></a>
						<a href="teacher_attend_excel_form.php"><?=$강사출근현황[$LangID]?></a></li>
					<li <?if ($SubMenuID==1108){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1108, '<?=$수업출석현황[$LangID]?>', 'teacher_enter_excel_form.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1108, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1108"></a>
						<a href="teacher_enter_excel_form.php"><?=$수업출석현황[$LangID]?></a></li>
					<li <?if ($SubMenuID==1109){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1109, '<?=$강사별수업종료현황[$LangID]?>', 'student_secession_by_teacher.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1109, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1109"></a>
						<a href="student_secession_by_teacher.php"><?=$강사별수업종료현황[$LangID]?></a></li>
					<li <?if ($SubMenuID==1134){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1134, '<?=$포인트_항목관리[$LangID]?>', 'point_type_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1134, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1134"></a>
						<a href="point_type_list.php"><?=$포인트_항목관리[$LangID]?></a></li>
					<li <?if ($SubMenuID==1103){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(1103, '<?=$팝업관리[$LangID]?>', 'popup_list.php');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(1103, $ArrFavoriteLmsMenuSubMenuID)) { ?> http://mangoidev.hihome.kr/lms/images/star_click.pngstar_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1103"></a>
						<a href="popup_list.php"><?=$팝업관리[$LangID]?></a></li>
	
				</ul>
			</li>
            <? } ?>

            <? if (in_array($_LINK_ADMIN_LEVEL_ID_, [6, 7, 9,10,12,13])) { ?>
                <li title="<?=$운영관리[$LangID]?>" <?if ($MainMenuID==11){?>class="current_section"<?}?>>
                    <a href="#">
                        <span class="menu_icon"><i class="material-icons">settings</i></span>
                        <span class="menu_title"><?=$운영관리[$LangID]?></span>
                    </a>
                    <ul class="lms_left_menu">
                        <!-- 지사/학원은 B2C/B2B 결제관리만 노출 -->
                        <li <?if ($SubMenuID==1131){?>class="act_item"<?}?>>
                            <a onclick="InsertFavoriteMenu(1131, '<?=$B2C_결제관리[$LangID]?>', 'b2c_payment_list.php');"
                               style="display:block;">
                                <img src="<?if(array_key_exists(1131, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1131">
                            </a>
                            <a href="b2c_payment_list.php"><?=$B2C_결제관리[$LangID]?></a>
                        </li>
                        <li <?if ($SubMenuID==1132){?>class="act_item"<?}?>>
                            <a onclick="InsertFavoriteMenu(1132, '<?=$B2B_결제관리[$LangID]?>', 'b2b_payment_list.php');"
                               style="display:block;">
                                <img src="<?if(array_key_exists(1132, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_1132">
                            </a>
                            <a href="b2b_payment_list.php"><?=$B2B_결제관리[$LangID]?></a>
                        </li>
                    </ul>
                </li>
            <? } ?>

			<li title="스케줄 관리" <?if ($MainMenuID==44){?>class="current_section"<?}?> style="display:<?if ($_LINK_ADMIN_LEVEL_ID_>4){?>none<?}?>;">
				<a href="#">
					<span class="menu_icon"><i class="material-icons">schedule</i></span>
					<span class="menu_title">스케줄 관리</span>
				</a>
				<ul class="lms_left_menu">
					<li <?if ($SubMenuID==4401){?>class="act_item"<?}?>>
						<a onclick="InsertFavoriteMenu(4401, '스케줄 관리', 'calendar_view.php?BoardCode=reference');" style="display: <?if ( $_LINK_ADMIN_LEVEL_ID_<9  || $_LINK_ADMIN_LEVEL_ID_>13 ){?>none<?}?>;"><img src="<?if(array_key_exists(4401, $ArrFavoriteLmsMenuSubMenuID)) { ?> images/star_clicked.png<?} else {?>images/star_click.png<?}?>" id="FvIcon_4401"></a>
						<a href="calendar_view.php">스케줄 관리</a></li>
				</ul>
			</li>
			<li style="height:100px;"></li>

		</ul>

	</div>
</aside>
<!-- main sidebar end -->

<style>
    .lms_left_menu li{position:relative; padding-left:100px;}
    .lms_left_menu img{width:20px; position:absolute; left:41px; top:41%;}
</style>

<script>

function InsertFavoriteMenu(SubMenuID, MenuName, MenuUrl, TempMenuType) {
	var url = "ajax_set_favorite_lms_menu.php";
	var MemberID = "<?=$_LINK_ADMIN_ID_?>";
	var Url = window.location.href;
	var ArrUrl = Url.split('/');
	var CurrentPage = ArrUrl[ArrUrl.length-1];

	// 대다수가 1인 케이스라 중복입력하지않기위함
	if(TempMenuType==2) {
		var MenuType = TempMenuType;
	} else {
		var MenuType = 1;
	}
	//location.href = url + "?MemberID="+MemberID+"&SubMenuID="+SubMenuID+"&MenuName="+MenuName+"&MenuUrl="+MenuUrl+"&MenuType="+MenuType;
	// /*
	$.ajax(url, {
		data: {
			MemberID: MemberID,
			SubMenuID: SubMenuID,
			MenuName: MenuName,
			MenuUrl: MenuUrl,
			MenuType: MenuType
		},
		success: function (data) {
			var result = data.Result;
			var rank = data.Rank;

			if(result==0) {
				UIkit.modal.alert( "<?=$메뉴가_즐겨찾기에서_제거되었습니다[$LangID]?>");
				document.getElementById("FvIcon_"+SubMenuID).src = "images/star_click.png";
			} else if(result==1) {
				UIkit.modal.alert("<?=$메뉴가_즐겨찾기에서_추가되었습니다[$LangID]?>");
				document.getElementById("FvIcon_"+SubMenuID).src = "images/star_clicked.png";
			}
			if(CurrentPage=="index.php") {
				// 대쉬보드 화면이라면
				if(result==0) {
					// 해당 되는 구성을 지운다
					var Element = document.getElementById("Idx_FvIcon_"+SubMenuID);
					Element.parentNode.removeChild(Element);
				} else if(result==1) {
					console.log("순서 : "+rank);
					// 구성을 추가한다
					var ParentElement = document.getElementById("Idx_FvIcons_list");
					var Elements = document.getElementsByName("Idx_FvIcons");
					var SpecificElement = Elements[rank-1];
					// 추가하고자하는 구성
					var AddElement = document.createElement('a');
					AddElement.setAttribute("id", "Idx_FvIcon_"+SubMenuID);
					AddElement.setAttribute("href", MenuUrl);
					AddElement.setAttribute("name", "Idx_FvIcons");
					AddElement.innerHTML = MenuName;
					
					ParentElement.insertBefore(AddElement, SpecificElement);
				}
			}
		},
		error: function () {
			alert('Error while contacting server, please try again');
		}
	});
	// */
}

function OpenClassOrderBulk(){
	openurl = "class_order_bulk_form.php";
	$.colorbox({	
		href:openurl
		,width:"95%" 
		,height:"95%"
		,maxWidth: "750"
		,maxHeight: "650"
		,title:""
		,iframe:true 
		,scrolling:true
		//,onClosed:function(){location.reload(true);}
		//,onComplete:function(){alert(1);}
	});
}

function OpenClassOrderBulkMerge(){
    openurl = "class_order_bulk_form_merge.php";
    $.colorbox({
        href:openurl
        ,width:"95%"
        ,height:"95%"
        ,maxWidth: "750"
        ,maxHeight: "650"
        ,title:""
        ,iframe:true
        ,scrolling:true
        //,onClosed:function(){location.reload(true);}
        //,onComplete:function(){alert(1);}
    });
}

function OpenClassSchedule(){
	openurl = "class_schedule.php";
	//cordova_iab.InAppOpenBrowser(openurl);
	window.open(openurl, "class_schedule", "toolbar=yes,scrollbars=yes,resizable=yes,top=50,left=50,width=1080,height=800");
}

function OpenClassScheduleByTeacher(){
	openurl = "class_schedule_by_teacher.php";
	window.open(openurl, "class_schedule_by_teacher", "toolbar=yes,scrollbars=yes,resizable=yes,top=50,left=50,width=1080,height=800");
}

//세계시간 =======================================

function GetTime() { 
	var dt = new Date();
	var def = dt.getTimezoneOffset()/60;
	var gmt = (dt.getHours() + def);
	var ending = ":" + IfZero(dt.getMinutes()) + ":" + IfZero(dt.getSeconds());
	
	var kr =check24(((gmt + 9) > 24) ? ((gmt + 9) - 24) : (gmt + 9));
	var kr_time = (IfZero(kr) + ending);
	var ph =check24(((gmt + 8) > 24) ? ((gmt + 8) - 24) : (gmt + 8));
	var ph_time = (IfZero(ph) + ending);

	document.getElementById("DivKrTime").innerHTML = kr_time;
	document.getElementById("DivPhTime").innerHTML = ph_time;

	/* 이하는 그리니치 기준 왼쪽 
	var _GMT =check24(((gmt) > 24) ? ((gmt) - 24) : (gmt));

	document.clock._GMT.value = (IfZero(_GMT) + ":" + IfZero(dt.getMinutes()) + ":" + IfZero(dt.getSeconds()));
	var eniw =check24(((gmt + (24-12)) > 24) ? ((gmt + (24-12)) - 24) : (gmt + (24-12)));
	document.clock.eniw.value = (IfZero(eniw) + ending);
	var sam =check24(((gmt + (24-11)) > 24) ? ((gmt + (24-11)) - 24) : (gmt + (24-11)));
	document.clock.sam.value = (IfZero(sam) + ending);
	var haw =check24(((gmt + (24-10)) > 24) ? ((gmt + (24-10)) - 24) : (gmt + (24-10)));
	document.clock.Hawaii.value = (IfZero(haw) + ending);
	*/
	
	setTimeout("GetTime()", 1000);
}
function IfZero(num) {
	return ((num <= 9) ? ("0" + num) : num);
}
function check24(hour) {
	return (hour >= 24) ? hour - 24 : hour;
}
window.onload=GetTime;
// End -->

//세계시간 =======================================

</script>
