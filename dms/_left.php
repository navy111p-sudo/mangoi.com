<?php
switch ($top_menu_id) {
   case 1:
     include_once('./_left_index.php');
     break;
   case 2:
      include_once('./_left_setup.php');
      break;
   case 3:
      include_once('./_left_member.php');
      break;
   case 4:
      include_once('./_left_page.php');
      break;
   case 5:
      include_once('./_left_board.php');
      break;
   case 6:
      include_once('./_left_multimedia.php');
      break;
   default:
      include_once('./_left_index.php');
 }
?>