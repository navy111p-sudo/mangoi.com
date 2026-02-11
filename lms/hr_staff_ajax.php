<?
$work_sw = isset($_REQUEST["work_sw"]) ? $_REQUEST["work_sw"] : "";

if ($work_sw == 1) {
		
		$ret_val  = "9＾<table class='uk-table uk-table-align-vertical'>";
	    $ret_val .= "＾<tbody>";
		$ret_val .= "＾<tr>";
		$ret_val .= "＾<td class='uk-text-wrap uk-table-td-center' rowspan='5' width='5%'><input type='checkbox' id='' name=''></td>";
		$ret_val .= "＾<td class='uk-text-wrap uk-table-td-center'>업적목표</td>";
		$ret_val .= "＾<td class='uk-text-wrap uk-table-td-center' colspan='3'>";
		$ret_val .= "＾<input type='text' id='' name='' style='height:25px;width:95%;border:1px solid #cccccc;padding-left:10px;padding-right:10px;'/>";
		$ret_val .= "＾</td>";
		$ret_val .= "＾</tr>";
		$ret_val .= "＾<tr>";
		$ret_val .= "＾<td class='uk-text-wrap uk-table-td-center'>KPI(핵심성과지표)</td>";
		$ret_val .= "＾<td class='uk-text-wrap uk-table-td-center' colspan='3'>";
		$ret_val .= "＾<input type='text' id='' name='' style='width:95%;border:1px solid #cccccc;padding:10px;'>";
		$ret_val .= "＾</td>";
		$ret_val .= "＾</tr>";
		$ret_val .= "＾<tr>";
		$ret_val .= "＾<td class='uk-text-wrap uk-table-td-center'>측정산식</td>";
		$ret_val .= "＾<td class='uk-text-wrap uk-table-td-center' colspan='3'>";
		$ret_val .= "＾<textarea id='' name='' value='' style='height:100px;width:95%;border:1px solid #cccccc;padding:10px;'></textarea>";
		$ret_val .= "＾</td>";
		$ret_val .= "＾</tr>";
		$ret_val .= "＾<tr>";
		$ret_val .= "＾<td class='uk-text-wrap uk-table-td-center'>평가척도</td>";
		$ret_val .= "＾<td class='uk-text-wrap uk-table-td-center' colspan='3'>";
		$ret_val .= "＾<textarea id='' name='' value='' style='height:100px;width:95%;border:1px solid #cccccc;padding:10px;'></textarea>";
		$ret_val .= "＾</td>";
		$ret_val .= "＾</tr>";
		$ret_val .= "＾<tr>";
		$ret_val .= "＾<td class='uk-text-wrap uk-table-td-center' width='15%'>가중치</td>";
		$ret_val .= "＾<td class='uk-text-wrap uk-table-td-center' width='35%'>";
		$ret_val .= "＾<select id='' name='' class='uk-width-1-1' class='Select' style='width:92%;height:30px;'/>";
		$ret_val .= "＾<option value='0'>선택</option>";
		for ($p=1; $p <= 100; $p++) {
		      $ret_val .= "＾<option value='".$p."'>".$p."%</option>";
		}
		$ret_val .= "＾</select>";
		$ret_val .= "＾</td>";
		$ret_val .= "＾<td class='uk-text-wrap uk-table-td-center' width='15%'>평가부서(부문)</td>";
		$ret_val .= "＾<td class='uk-text-wrap uk-table-td-center' width='30%'>";

		$ret_val .= "＾</td>";
		$ret_val .= "＾</tr>";
		$ret_val .= "＾</tbody>";
		$ret_val .= "＾</table>";

}
echo $ret_val;
?>
