<?php

header("Location: setup_form.php");


$top_menu_id = 1;
$left_menu_id = 0;
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
		<h2>타이틀</h2>
		<div class="box">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table1">
			  <tr>
				<th width="6%">No</th>
				<th>제목</th>
				<th width="12%">작성자</th>
				<th width="12%">작성일</th>
				<th width="8%">조회</th>
			  </tr>
			  <tr>
				<td>2</td>
				<td class="subject"><a href="#">관리자 템플릿 경축드립니다.</a></td>
				<td>홍길동</td>
				<td>2014.08.30</td>
				<td>100</td>
			  </tr>
			  <tr>
				<td>1</td>
				<td class="subject"><a href="#">관리자 템플릿 경축드립니다.</a></td>
				<td>홍길동</td>
				<td>2014.08.30</td>
				<td>100</td>
			  </tr>
			  <tr>
				<td>2</td>
				<td class="subject"><a href="#">관리자 템플릿 경축드립니다.</a></td>
				<td>홍길동</td>
				<td>2014.08.30</td>
				<td>100</td>
			  </tr>
			  <tr>
				<td>1</td>
				<td class="subject"><a href="#">관리자 템플릿 경축드립니다.</a></td>
				<td>홍길동</td>
				<td>2014.08.30</td>
				<td>100</td>
			  </tr>
			</table>
			<!------------------------------------ 번호영역 ------------------------------------->
			<div class="number">
				<ul>
					<li class="disabled">&laquo; prev</li>
					<li class="current">1</li>
					<li><a href="">2</a></li>
					<li><a href="">next &raquo;</a></li>
				</ul>
			</div>
			<!------------------------------------ 번호영역 ------------------------------------->

		</div>
	</div>
</div>
<?php
include_once('./_bottom.php');
?>
</body>
<?php
include_once('./_footer.php');
include_once('../includes/dbclose.php');
?>













