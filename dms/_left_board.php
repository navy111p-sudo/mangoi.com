<div class="left">
	<ul class="left_nav">
		<li>게시판관리</li>
		<?php
		$BoardCode = isset($_REQUEST["BoardCode"]) ? $_REQUEST["BoardCode"] : "";


		$Sql = "select A.* from Boards A inner join Subs B on A.SubID=B.SubID where BoardState=1 order by BoardRegDateTime desc";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);

		while($Row = $Stmt->fetch()) {
		?>
		<li><a href="boardset_form.php?BoardCode=<?=$Row["BoardCode"]?>&BoardID=<?=$Row["BoardID"]?>" <?php if ($BoardCode==$Row["BoardCode"]){ echo ("class='active'"); }?>>> <?=$Row["BoardName"]?></a></li>
		<?php
		}
		$Stmt = null;
		?>

	</ul>
</div>