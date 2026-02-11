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
?>

<?
include_once('./inc_common_list_css.php');
?>
<!-- ============== only this page css ============== -->

<!-- ============== only this page css ============== -->
<!-- ============== common.css ============== -->
<link rel="stylesheet" type="text/css" href="./css/common.css" />
<!-- ============== common.css ============== -->
</head>

<body class="disable_transitions sidebar_main_open sidebar_main_swipe">
<?php
$ListType = isset($_REQUEST["ListType"]) ? $_REQUEST["ListType"] : "";

$MainMenuID = 29;
if ($ListType=="100"){
	$SubMenuID = 2925;
}else{
	$SubMenuID = 2922;
}
include_once('./inc_top.php');
include_once('./inc_menu_left.php');
include_once('./inc_document_common.php');
?>


 
<?php
#-----------------------------------------------------------------------------------------------------------------------------------------#
$MemberLoginID     = isset($_COOKIE["LoginMemberID"   ]) ? $_COOKIE["LoginMemberID"   ] : "";
#-----------------------------------------------------------------------------------------------------------------------------------------#
# 회원 고유아이디(번호, 조직아이디) 찾기
#-----------------------------------------------------------------------------------------------------------------------------------------#
$Sql = "SELECT T.*,M.* from Members as M 
			left join Hr_OrganLevelTaskMembers T on T.MemberID=M.MemberID
				where M.MemberLoginID=:MemberLoginID";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':MemberLoginID', $MemberLoginID);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$My_MemberID     = $Row["MemberID"];

$AddSqlWhere = "1=1";
$ListParam = "1=1";
$PaginationParam = "1=1";


$CurrentPage = isset($_REQUEST["CurrentPage"]) ? $_REQUEST["CurrentPage"] : "";
$PageListNum = isset($_REQUEST["PageListNum"]) ? $_REQUEST["PageListNum"] : "";
$SearchText = isset($_REQUEST["SearchText"]) ? $_REQUEST["SearchText"] : "";
$SearchText2 = isset($_REQUEST["SearchText2"]) ? $_REQUEST["SearchText2"] : "";
$SearchState = isset($_REQUEST["SearchState"]) ? $_REQUEST["SearchState"] : "";
$SearchDocumentID = isset($_REQUEST["SearchDocumentID"]) ? $_REQUEST["SearchDocumentID"] : "";

$SearchStartYear = isset($_REQUEST["SearchStartYear"]) ? $_REQUEST["SearchStartYear"] : "";
$SearchStartMonth = isset($_REQUEST["SearchStartMonth"]) ? $_REQUEST["SearchStartMonth"] : "";
$SearchStartDay = isset($_REQUEST["SearchStartDay"]) ? $_REQUEST["SearchStartDay"] : "";

$SearchEndYear = isset($_REQUEST["SearchEndYear"]) ? $_REQUEST["SearchEndYear"] : "";
$SearchEndMonth = isset($_REQUEST["SearchEndMonth"]) ? $_REQUEST["SearchEndMonth"] : "";
$SearchEndDay = isset($_REQUEST["SearchEndDay"]) ? $_REQUEST["SearchEndDay"] : "";


$Auth_Document_99 = 0;
//if ($_LINK_ADMIN_ID_==22050 || $_LINK_ADMIN_ID_==22054 ||  $_LINK_ADMIN_ID_==1){
if ($_LINK_ADMIN_ID_==22050 ||$_LINK_ADMIN_ID_==22054 ||  $_LINK_ADMIN_ID_==1 ){//정우영
	$Auth_Document_99 = 1;
}


if ($SearchStartYear==""){
	$SearchStartYear = date("Y");
}
if ($SearchStartMonth==""){
	$SearchStartMonth = date("m");
}
if ($SearchStartDay==""){
	$SearchStartDay = date("d");
}

if ($SearchEndYear==""){
	$SearchEndYear = date("Y");
}
if ($SearchEndMonth==""){
	$SearchEndMonth = date("m");
}
if ($SearchEndDay==""){
	$SearchEndDay = date("d");
}

$StartDate = $SearchStartYear . "-" . substr("0".$SearchStartMonth,-2) . "-" . substr("0".$SearchStartDay,-2);
$EndDate = $SearchEndYear . "-" . substr("0".$SearchEndMonth,-2) . "-" . substr("0".$SearchEndDay,-2);



if (!$CurrentPage){
	$CurrentPage = 1;	
}
if (!$PageListNum){
	$PageListNum = 30;
}


if ($PageListNum!=""){
	$ListParam = $ListParam . "&PageListNum=" . $PageListNum;
}

if ($SearchState==""){
	$SearchState = "1";
}	

if ($SearchDocumentID==""){
	$SearchDocumentID = "100";
}	


$ListParam = $ListParam . "&ListType=" . $ListType;

$ListParam = $ListParam . "&SearchStartYear=" . $SearchStartYear;
$ListParam = $ListParam . "&SearchStartMonth=" . $SearchStartMonth;
$ListParam = $ListParam . "&SearchStartDay=" . $SearchStartDay;
$ListParam = $ListParam . "&SearchEndYear=" . $SearchEndYear;
$ListParam = $ListParam . "&SearchEndMonth=" . $SearchEndMonth;
$ListParam = $ListParam . "&SearchEndDay=" . $SearchEndDay;
$AddSqlWhere = $AddSqlWhere . " and datediff(A.DocumentReportRegDateTime, '$StartDate')>=0 ";
$AddSqlWhere = $AddSqlWhere . " and datediff(A.DocumentReportRegDateTime, '$EndDate')<=0 ";



if ($SearchState!="100"){
	$ListParam = $ListParam . "&SearchState=" . $SearchState;
	$AddSqlWhere = $AddSqlWhere . " and A.DocumentReportState=$SearchState ";
}
$AddSqlWhere = $AddSqlWhere . " and A.DocumentReportState=1 ";

if ($ListType!="100"){

	// 관리자가 아닐 경우는 자기 자신에게 해당하는 문서만 가지고 온다.
	if ( $_LINK_ADMIN_ID_!=1 ){
		$AddSqlWhere = $AddSqlWhere . " and A.DocumentReportID in (select DocumentReportID from DocumentReportMembers where MemberID=".$_LINK_ADMIN_ID_.") ";
	} 

}


if ($SearchText!=""){
	$ListParam = $ListParam . "&SearchText=" . $SearchText;
	$AddSqlWhere = $AddSqlWhere . " and A.DocumentReportName like '%".$SearchText."%' ";
}

if ($SearchText2!=""){
	$ListParam = $ListParam . "&SearchText2=" . $SearchText2;
	$AddSqlWhere = $AddSqlWhere . " and C.MemberName like '%".$SearchText2."%' ";
}

if ($SearchDocumentID!=""){
	$ListParam = $ListParam . "&SearchDocumentID=" . $SearchDocumentID;
	if ($SearchDocumentID=="99"){
		$AddSqlWhere = $AddSqlWhere . " and A.DocumentID=99 ";
	}else if ($SearchDocumentID=="88"){
		$AddSqlWhere = $AddSqlWhere . " and A.DocumentID<>99 ";
	}else{

	}
}




/*
if ($Auth_Document_99==0){
	$AddSqlWhere = $AddSqlWhere . " and A.DocumentID<>99 ";
}
*/


$PaginationParam = $ListParam;
if ($CurrentPage!=""){
	$ListParam = $ListParam . "&CurrentPage=" . $CurrentPage;
}

$ListParam = str_replace("&", "^^", $ListParam);

$Sql = "SELECT 
				count(*) TotalRowCount 
		from DocumentReports A 
			left outer join Documents B on A.DocumentID=B.DocumentID 
			inner join Members C on A.MemberID=C.MemberID 
		where ".$AddSqlWhere." ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TotalRowCount = $Row["TotalRowCount"];

$TotalPageCount = ceil($TotalRowCount / $PageListNum);
$StartRowNum = $PageListNum * ($CurrentPage - 1 );



$Sql = "SELECT 
			A.*,
			ifnull(B.DocumentName, '-') as DocumentName, 
			C.MemberName,
			C.MemberDprtName
		from DocumentReports A 
			left outer join Documents B on A.DocumentID=B.DocumentID 
			inner join Members C on A.MemberID=C.MemberID 
		where ".$AddSqlWhere." 
		order by A.DocumentReportRegDateTime desc limit $StartRowNum, $PageListNum";


$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
?>


<div id="page_content">
	<div id="page_content_inner">

		<h3 class="heading_b uk-margin-bottom">보고서 목록</h3>

		<form name="SearchForm" method="get">
		<input type="hidden" name="ListType" value="<?=$ListType?>">
		<div class="md-card" style="margin-bottom:10px;">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin="">




					<!--
					<div class="uk-width-medium-1-10">
						<label for="product_search_price">Price</label>
						<input type="text" class="md-input" id="product_search_price">
					</div>
					

					<div class="uk-width-medium-1-10">
						<div class="uk-margin-small-top">
							<select id="SearchState" name="SearchState" onchange="SearchSubmit()" data-md-selectize  data-md-selectize-bottom>
								<option value="100" <?if ($SearchState=="100"){?>selected<?}?>>전체</option>
								<option value="1" <?if ($SearchState=="1"){?>selected<?}?>>활성</option>
								<option value="2" <?if ($SearchState=="2"){?>selected<?}?>>미활성</option>
							</select>
						</div>
					</div>
					-->

					<!--
					<div class="uk-width-medium-1-10">
						<div class="uk-margin-top uk-text-nowrap">
							<input type="checkbox" name="product_search_active" id="product_search_active" data-md-icheck/>
							<label for="product_search_active" class="inline-label">Active</label>
						</div>
					</div>
					-->

					<div class="uk-width-medium-1-10" style="padding-top:7px;">
						<select id="SearchStartYear" name="SearchStartYear" class="uk-width-1-1" data-placeholder="년도선택" style="width:100%;height:40px;"/>
							<option value="">년도선택</option>
							<?
							for ($iiii=2019;$iiii<=$SearchStartYear+1;$iiii++) {
							?>
							<option value="<?=$iiii?>" <?if ($SearchStartYear==$iiii){?>selected<?}?>><?=$iiii?> 년</option>
							<?
							}
							?>
						</select>
					</div>
					<div class="uk-width-medium-1-10" style="padding-top:7px;">
						<select id="SearchStartMonth" name="SearchStartMonth" class="uk-width-1-1" onchange="ChSearchStartMonth(1, this.value);" data-placeholder="<?=$월선택[$LangID]?>" style="width:100%;height:40px;"/>
							<option value="">월선택</option>
							<?
							for ($iiii=1;$iiii<=12;$iiii++) {
							?>
							<option value="<?=$iiii?>" <?if ($SearchStartMonth==$iiii){?>selected<?}?>><?=$iiii?> <?=$월월[$LangID]?></option>
							<?
							}
							?>
						</select>
					</div>
					<div class="uk-width-medium-1-10" style="padding-top:7px;">
						<select id="SearchStartDay" name="SearchStartDay" class="uk-width-1-1" data-placeholder="일선택" style="width:100%;height:40px;"/>
							<option value="">일선택</option>
						</select>
					</div>

					<!-- <div class="uk-width-medium-5-10"></div> --> <span style="padding-top: 15px; ">~</span>
					<div class="uk-width-medium-1-10" style="padding-top:7px; ">
						<select id="SearchEndYear" name="SearchEndYear" class="uk-width-1-1" data-placeholder="년도선택" style="width:100%;height:40px;"/>
							<option value="">년도선택</option>
							<?
							for ($iiii=2019;$iiii<=$SearchEndYear+1;$iiii++) {
							?>
							<option value="<?=$iiii?>" <?if ($SearchEndYear==$iiii){?>selected<?}?>><?=$iiii?> 년</option>
							<?
							}
							?>
						</select>
					</div>
					<div class="uk-width-medium-1-10" style="padding-top:7px;">
						<select id="SearchEndMonth" name="SearchEndMonth" class="uk-width-1-1" onchange="ChSearchStartMonth(2, this.value);" data-placeholder="<?=$월선택[$LangID]?>" style="width:100%;height:40px;"/>
							<option value="">월선택</option>
							<?
							for ($iiii=1;$iiii<=12;$iiii++) {
							?>
							<option value="<?=$iiii?>" <?if ($SearchEndMonth==$iiii){?>selected<?}?>><?=$iiii?> <?=$월월[$LangID]?></option>
							<?
							}
							?>
						</select>
					</div>
					<div class="uk-width-medium-1-10" style="padding-top:7px;">
						<select id="SearchEndDay" name="SearchEndDay" class="uk-width-1-1" data-placeholder="일선택" style="width:100%;height:40px;"/>
							<option value="">일선택</option>
						</select>
					</div>



				</div>
				<div class="uk-grid" data-uk-grid-margin="">	

					<div class="uk-width-medium-3-10">
						<label for="SearchText">보고서명</label>
						<input type="text" class="md-input" id="SearchText" name="SearchText" value="<?=$SearchText?>">
					</div>

					<div class="uk-width-medium-2-10">
						<label for="SearchText2">작성자</label>
						<input type="text" class="md-input"  id="SearchText2" name="SearchText2" value="<?=$SearchText2?>">
					</div>

					<div class="uk-width-medium-1-10" style="display:<?if ($Auth_Document_99==0){?>none<?}?>;">
						<div class="uk-margin-small-top">
							<select id="SearchDocumentID" name="SearchDocumentID" onchange="SearchSubmit()" data-md-selectize  data-md-selectize-bottom>
								<option value="100" <?if ($SearchDocumentID=="100"){?>selected<?}?>><?=$전체[$LangID]?></option>
								<option value="99" <?if ($SearchDocumentID=="99"){?>selected<?}?>><?=$기안_및_지출서[$LangID]?></option>
								<option value="88" <?if ($SearchDocumentID=="88"){?>selected<?}?>><?=$휴가_및_병가원[$LangID]?></option>
							</select>
						</div>
					</div>

					<div class="uk-width-medium-1-10 uk-text-center">
						<a href="javascript:SearchSubmit();" class="md-btn md-btn-primary uk-margin-small-top"><?=$검색[$LangID]?></a>
					</div>
					
					<?if ($SearchDocumentID=="99" && $Auth_Document_99==1){?>
					<div class="uk-width-medium-2-10 uk-text-center">
						<a type="button" href="javascript:DownloadPerDate()" class="md-btn md-btn-primary uk-margin-small-top two" style="display: inline-block"><?=$엑셀_다운로드[$LangID]?></a>
					</div>
					<?}?>
				</div>
			</div>
		</div>
		</form>


		<div class="md-card">
			<div class="md-card-content">
				<div class="uk-grid" data-uk-grid-margin>
					<div class="uk-width-1-1">
						<div class="uk-overflow-container">
							<table class="uk-table uk-table-align-vertical">
								<thead>
									<tr>
										<th nowrap>No</th>
										<th nowrap><?=$부서[$LangID]?></th>
										<th nowrap><?=$작성자[$LangID]?></th>
										<th nowrap><?=$사용양식[$LangID]?></th>
										<th nowrap><?=$보고서명[$LangID]?></th>
										<th nowrap><?=$작성일[$LangID]?></th>
										<th nowrap><?=$상태[$LangID]?></th>
									</tr>
								</thead>
								<tbody>
									
									<?php
									$ListCount = 1;
									while($Row = $Stmt->fetch()) {
										$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;

										$DocumentID = $Row["DocumentID"];
										$DocumentReportID = $Row["DocumentReportID"];
										$DocumentReportName = $Row["DocumentReportName"];
										$DocumentReportState = $Row["DocumentReportState"];
										$DocumentReportRegDateTime = $Row["DocumentReportRegDateTime"];

										if ($DocumentID==99){
											$DocumentName = "기안 및 지출서";
										}else{
											$DocumentName = $Row["DocumentName"];
										}

										$MemberName = $Row["MemberName"];
										$MemberDprtName = $Row["MemberDprtName"];
										
										if ($DocumentReportState==1){
											$StrDocumentReportState = "<span class=\"ListState_1\">활성</span>";
										}else if ($DocumentReportState==2){
											$StrDocumentReportState = "<span class=\"ListState_2\">미활성</span>";
										}
										//결재해야 할 사람 수와 이미 결재완료한 사람 수가 일치하면 전체메시지를 '승인'으로 다르면 '진행중'으로 표시
										if (compareApprovalMemberCount($DocumentReportID)) $ApporovalMessage = $승인[$LangID];
										else $ApporovalMessage = $진행중[$LangID];
										// 나 이전에 승인해야 할 단계가 남았으면 [이전단계 승인대기중] 표시 아니면 [결재 대기중]
										if (isPrevApproval($DocumentReportID,$My_MemberID)) {
											$StrDocumentReportMemberState = "<font color='grey'>이전단계 승인대기중</font>";
										} else {
											$Sql3 = "SELECT A.* from DocumentReportMembers A  where A.DocumentReportID=".$DocumentReportID." and A.MemberID=".$_LINK_ADMIN_ID_." ";
											$Stmt3 = $DbConn->prepare($Sql3);
											$Stmt3->execute();
											$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
											$Row3 = $Stmt3->fetch();
											$DocumentReportMemberState = $Row3["DocumentReportMemberState"];
											if ($DocumentReportMemberState==0){
												$StrDocumentReportMemberState = "<font color='green'>본인결재 대기중</font>";
											}else if ($DocumentReportMemberState==1){
												$StrDocumentReportMemberState = "<font color='blue'>승인</font>";
											}else if ($DocumentReportMemberState==2){
												$StrDocumentReportMemberState = "<font color='red'>반려</font>";
											}
										}
									?>

									<tr>
										<td class="uk-text-nowrap uk-table-td-center"><?=$ListNumber?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$MemberDprtName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$MemberName?></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=$DocumentName?></td>
										<td class="uk-text-nowrap"><a href="my_document_comfirm_form.php?ListParam=<?=$ListParam?>&DocumentReportID=<?=$DocumentReportID?>"><?=$DocumentReportName?></a></td>
										<td class="uk-text-nowrap uk-table-td-center"><?=substr($DocumentReportRegDateTime,0,10)?></td>
										<td class="uk-text-nowrap uk-table-td-center">
											본인 : <?=$StrDocumentReportMemberState?><br>
											전체 : <?=$ApporovalMessage?>
										</td>
									</tr>
									<?php
										$ListCount ++;
									}
									$Stmt = null;
									?>


								</tbody>
							</table>
						</div>
						

						<?php			
						include_once('./inc_pagination.php');
						?>

						<!--
						<div class="uk-form-row" style="text-align:center;">
							<a type="button" href="my_document_comfirm_form.php?ListParam=<?=$ListParam?>" class="md-btn md-btn-primary">신규등록</a>
						</div>
						-->

					</div>
				</div>
			</div>
		</div>

	</div>
</div>


<?
include_once('./inc_common_list_js.php');
?>
<!-- ============== only this page js ============== -->

<!-- ============== only this page js ============== -->
<!-- ============== common.js ============== -->
<script type="text/javascript" src="js/common.js"></script>
<!-- ============== common.js ============== -->


<script>
function DownloadPerDate() {
	
	SearchStartYear = document.SearchForm.SearchStartYear.value;
	SearchStartMonth = document.SearchForm.SearchStartMonth.value;
	SearchStartDay = document.SearchForm.SearchStartDay.value;
	SearchEndYear = document.SearchForm.SearchEndYear.value;
	SearchEndMonth = document.SearchForm.SearchEndMonth.value;
	SearchEndDay = document.SearchForm.SearchEndDay.value;

	SearchText = document.SearchForm.SearchText.value;
	SearchText2 = document.SearchForm.SearchText2.value;
	SearchDocumentID = document.SearchForm.SearchDocumentID.value;
	
	location.href = "my_document_comfirm_list_excel.php?SearchStartYear="+SearchStartYear+"&SearchStartMonth="+SearchStartMonth+"&SearchStartDay="+SearchStartDay+"&SearchEndYear="+SearchEndYear+"&SearchEndMonth="+SearchEndMonth+"&SearchEndDay="+SearchEndDay+"&SearchText="+SearchText+"&SearchText2="+SearchText2+"&SearchDocumentID="+SearchDocumentID+"&ListType=<?=$ListType?>";
}

function ChSearchStartMonth(MonthType, MonthNumber){
	
	if (MonthType==1){
		YearNumber = document.SearchForm.SearchStartYear.value;
	}else{
		YearNumber = document.SearchForm.SearchEndYear.value;
	}
	url = "ajax_get_month_last_day.php";

	//location.href = url + "?YearNumber="+YearNumber+"&MonthNumber"+MonthNumber;
	$.ajax(url, {
		data: {
			YearNumber: YearNumber,
			MonthNumber: MonthNumber
		},
		success: function (data) {

			if (MonthType==1){

				SelBoxInitOption('SearchStartDay');

				SelBoxAddOption( 'SearchStartDay', '일선택', "", "");
				for (ii=1 ; ii<=data.LastDay ; ii++ ){
					ArrOptionText     = ii + "일";
					ArrOptionValue    = ii;

					ArrOptionSelected = "";
					if (ii==<?=(int)$SearchStartDay?>){
						ArrOptionSelected = "selected";
					}

					SelBoxAddOption( 'SearchStartDay', ArrOptionText, ArrOptionValue, ArrOptionSelected );
				}

			}else{

				SelBoxInitOption('SearchEndDay');

				SelBoxAddOption( 'SearchEndDay', '일선택', "", "");
				for (ii=1 ; ii<=data.LastDay ; ii++ ){
					ArrOptionText     = ii + "일";
					ArrOptionValue    = ii;
					
					ArrOptionSelected = "";
					if (ii==<?=(int)$SearchEndDay?>){
						ArrOptionSelected = "selected";
					}
						
					SelBoxAddOption( 'SearchEndDay', ArrOptionText, ArrOptionValue, ArrOptionSelected );
				}

			}

		},
		error: function () {

		}
	});
}




function SearchSubmit(){
	document.SearchForm.action = "my_document_comfirm_list.php";
	document.SearchForm.submit();
}
</script>


<script>
/** ===================================== 기본함수 ===================================== **/
// Option객체를 생성해서 Return
function SelBoxCreateOption( text, value, selected )
{
	var oOption = document.createElement("OPTION"); // Option 객체를 생성
	oOption.text = text; // Text(Keyword)를 입력
	oOption.value = value; // Value를 입력
	if (selected=="selected"){
		oOption.selected = true;
	}
	return oOption;
}

// SelectBox의 Option을 초기화
function SelBoxInitOption( ObjId ){
	var SelectObj = document.getElementById( ObjId );
	if ( SelectObj == null ) return; // 객체가 존재하지 않으면 취소

	SelectObj.options.length = 0; // 길이를 0으로 하면 초기화
}

// Option을 추가
function SelBoxAddOption( ObjId, text, value, selected ){
	var SelectObj = document.getElementById( ObjId );

	SelectObj.add( SelBoxCreateOption( text , value, selected ) );
	text     = "";
	value    = "";
	selected = "";
}
/** ===================================== 기본함수 ===================================== **/
</script>


<script>
window.onload = function(){
	ChSearchStartMonth(1, <?=(int)$SearchStartMonth?>);
	ChSearchStartMonth(2, <?=(int)$SearchEndMonth?>);
}
</script>

<?php
include_once('./inc_menu_right.php');
include_once('./inc_footer.php');
include_once('../includes/dbclose.php');
?>
</body>
</html>