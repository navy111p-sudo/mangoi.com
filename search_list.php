<?php
include_once('./includes/dbopen.php');
include_once('./includes/common.php');
include_once('./includes/member_check.php');

$UseMain = 0;
$UseSub = 1;
$SubCode = "Sub8";

?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $_SITE_TITLE_;?></title>
<link rel="icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>">
<link rel="shortcut icon" href="uploads/favicons/<?php echo $_SITE_FAVICON_;?>" />
<link href="css/style.css" rel="stylesheet" type="text/css">
<script src="js/javascript.js"></script>
<?php
include_once('./includes/common_header.php');

$Sql = "select SubID from Subs where SubCode=:SubCode";
$Stmt = $DbConn->prepare($Sql);
$Stmt->bindParam(':SubCode', $SubCode);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$SubID = $Row["SubID"];

if ($UseMain==1){
	$Sql = "select * from Main limit 0,1";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();

	$MainLayout = $Row["MainLayout"];
	$MainLayoutCss = $Row["MainLayoutCss"];
	$MainLayoutJavascript = $Row["MainLayoutJavascript"];
	list($MainLayoutTop, $MainLayoutBottom) = explode("{{Page}}", $MainLayout);
}else{
	$MainLayoutTop = "";
	$MainLayoutBottom = "";
	$MainLayoutCss = "";
	$MainLayoutJavascript = "";
}


if ($UseSub==1){
	$Sql = "select * from Subs where SubID=:SubID";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':SubID', $SubID);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;

	$SubLayout = $Row["SubLayout"];
	$SubLayoutCss = $Row["SubLayoutCss"];
	$SubLayoutJavascript = $Row["SubLayoutJavascript"];
	list($SubLayoutTop, $SubLayoutBottom) = explode("{{Page}}", $SubLayout);
}else{
	$SubLayoutTop = "";
	$SubLayoutBottom = "";
	$SubLayoutCss = "";
	$SubLayoutJavascript = "";
}


if (trim($MainLayoutCss)!=""){
	echo "\n";
	echo "<style>";
	echo "\n";
	echo $MainLayoutCss;
	echo "\n";
	echo "</style>";
	echo "\n";
}

if (trim($SubLayoutCss)!=""){
	echo "\n";
	echo "<style>";
	echo "\n";
	echo $SubLayoutCss;
	echo "\n";
	echo "</style>";
	echo "\n";
}

if (trim($PageContentCss)!=""){
	echo "\n";
	echo "<style>";
	echo "\n";
	echo $PageContentCss;
	echo "\n";
	echo "</style>";
	echo "\n";
}
?>
</head>
<body>
<?
include_once('./includes/common_body_top.php');
?>
<?php
// $MainLayoutTop = convertHTML(trim($MainLayoutTop));
// $SubLayoutTop = convertHTML(trim($SubLayoutTop));
// $PageContent = convertHTML(trim($PageContent));
// $SubLayoutBottom = convertHTML(trim($SubLayoutBottom));
// $MainLayoutBottom = convertHTML(trim($MainLayoutBottom));

if($DomainSiteID==7){
    $PageContent = convertHTML(trim($PageContent));

} else if($DomainSiteID==8){ //engliseed.kr
    $PageContent = convertHTML(trim($PageContent));

} else if($DomainSiteID==9){ //live.engedu.kr
    $PageContent = convertHTML(trim($PageContent));

} else {
    $MainLayoutTop = convertHTML(trim($MainLayoutTop));
    $SubLayoutTop = convertHTML(trim($SubLayoutTop));
    $PageContent = convertHTML(trim($PageContent));
	$SubLayoutBottom = convertHTML(trim($SubLayoutBottom));
    $MainLayoutBottom = convertHTML(trim($MainLayoutBottom));
  }
echo "\n";
echo $MainLayoutTop;
echo "\n";
echo $SubLayoutTop;
echo "\n";
?>



<?
$AddSqlWhere = " 1=1 ";

$MainSearchText = isset($_REQUEST["MainSearchText"]) ? $_REQUEST["MainSearchText"] : "";
$SearchText = isset($_REQUEST["SearchText"]) ? $_REQUEST["SearchText"] : "";
$SearchBoardID = isset($_REQUEST["SearchBoardID"]) ? $_REQUEST["SearchBoardID"] : "";


if ($MainSearchText!=""){
	$SearchText = $MainSearchText;
}

if ($SearchText!=""){
	$AddSqlWhere = $AddSqlWhere . " and ( AAA.BoardContentSubject like '%".$SearchText."%' or AAA.BoardContent like '%".$SearchText."%' ) ";
}

if ($SearchBoardID!=""){
	$AddSqlWhere = $AddSqlWhere . " and ( AAA.BoardID='".$SearchBoardID."' ) ";
}


$ViewTable = "
				(select 
					A.BoardID,

					CASE A.BoardID
						WHEN 9  THEN A.BoardContentSubject
						WHEN 10 THEN A.MajorBiz
						WHEN 13 THEN A.BizName
						WHEN 2  THEN A.BoardContentSubject
						WHEN 3  THEN A.BoardContentSubject
						WHEN 14 THEN A.FacilityProduct
						WHEN 15 THEN A.ResearchCompany
						WHEN 4  THEN A.BoardContentSubject
						WHEN 5  THEN A.BoardContentSubject
						WHEN 6  THEN A.BoardContentSubject
						WHEN 7  THEN A.BoardContentSubject
						WHEN 8  THEN A.BoardContentSubject
					END AS BoardContentSubject,

					CASE A.BoardID
						WHEN 9  THEN A.BoardContent
						WHEN 10 THEN '.............'
						WHEN 13 THEN '.............'
						WHEN 2  THEN A.BoardContent
						WHEN 3  THEN A.BoardContent
						WHEN 14 THEN '.............'
						WHEN 15 THEN '.............'
						WHEN 4  THEN A.BoardContent
						WHEN 5  THEN A.BoardContent
						WHEN 6  THEN A.BoardContent
						WHEN 7  THEN A.BoardContent
						WHEN 8  THEN A.BoardContent
					END AS BoardContent,

					CASE A.BoardID
						WHEN 9  THEN 1
						WHEN 10 THEN 2
						WHEN 13 THEN 3
						WHEN 2  THEN 4
						WHEN 3  THEN 5
						WHEN 14 THEN 6
						WHEN 15 THEN 7
						WHEN 4  THEN 8
						WHEN 5  THEN 9
						WHEN 6  THEN 10
						WHEN 7  THEN 11
						WHEN 8  THEN 12
					END AS BoardSearchOrder,

					CASE A.BoardID
						WHEN 9  THEN CONCAT('board_read.php?BoardCode=supporting&BoardContentID=',A.BoardContentID)
						WHEN 10 THEN CONCAT('board_read.php?BoardCode=example&BoardContentID=',A.BoardContentID)
						WHEN 13 THEN 'certification_biz_list.php'
						WHEN 2  THEN CONCAT('board_read.php?BoardCode=news&BoardContentID=',A.BoardContentID)
						WHEN 3  THEN CONCAT('board_read.php?BoardCode=data&BoardContentID=',A.BoardContentID)
						WHEN 14 THEN 'facilities_list.php'
						WHEN 15 THEN 'research_list.php'
						WHEN 4  THEN CONCAT('board_read.php?BoardCode=video&BoardContentID=',A.BoardContentID)
						WHEN 5  THEN CONCAT('board_read.php?BoardCode=album&BoardContentID=',A.BoardContentID)
						WHEN 6  THEN CONCAT('board_read.php?BoardCode=notice&BoardContentID=',A.BoardContentID)
						WHEN 7  THEN CONCAT('board_read.php?BoardCode=free&BoardContentID=',A.BoardContentID)
						WHEN 8  THEN CONCAT('board_read.php?BoardCode=qna&BoardContentID=',A.BoardContentID)
					END AS LinkUrl,

					A.BoardContentRegDateTime,
					B.BoardCode,
					B.BoardTitle				
					
					from BoardContents A inner join Boards B on A.BoardID=B.BoardID where
						A.BoardContentSecret=0 and 
						(A.BoardID=9 or A.BoardID=10 or A.BoardID=13 or A.BoardID=2 or A.BoardID=3 or A.BoardID=14 or A.BoardID=15 or A.BoardID=4 or A.BoardID=5 or A.BoardID=6 or A.BoardID=7 or A.BoardID=8)
				) 
				
				";

$Sql = "select count(*) as TotalRowCount from ".$ViewTable." AAA where ".$AddSqlWhere."  ";


$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);
$Row = $Stmt->fetch();
$Stmt = null;

$TotalRowCount = $Row["TotalRowCount"];

$Sql = "select AAA.* from ".$ViewTable." AAA where ".$AddSqlWhere." order by AAA.BoardSearchOrder asc ";
$Stmt = $DbConn->prepare($Sql);
$Stmt->execute();
$Stmt->setFetchMode(PDO::FETCH_ASSOC);

//echo $Sql;
?>




            	<h2 class="Font1 Title">통합검색 <span class="Font3">HOME > <b>통합검색</b></span></h2>
				
                <div class="TbSearch">
					<form name="SearchForm" action="get">
					<img src="images1/IconSearch.gif">
                    <select name="SearchBoardID" class="Select">
                        <option value="">통합검색</option>
                        <option value="9" <?if ($SearchBoardID=="9"){?>selected<?}?>>6차산업 지원정책</option>
                        <option value="10" <?if ($SearchBoardID=="10"){?>selected<?}?>>우수사례</option>
                        <option value="13" <?if ($SearchBoardID=="13"){?>selected<?}?>>6차산업 인증사업자</option>
                        <option value="2" <?if ($SearchBoardID=="2"){?>selected<?}?>>6차산업 뉴스</option>
                        <option value="3" <?if ($SearchBoardID=="3"){?>selected<?}?>>자료실</option>
                        <option value="14" <?if ($SearchBoardID=="14"){?>selected<?}?>>제조·가공시설 현황</option>
                        <option value="15" <?if ($SearchBoardID=="15"){?>selected<?}?>>기초실태조사 현황</option>
                        <option value="4" <?if ($SearchBoardID=="4"){?>selected<?}?>>센터활동 스크랩</option>
                        <option value="5" <?if ($SearchBoardID=="5"){?>selected<?}?>>센터앨범</option>
                        <option value="6" <?if ($SearchBoardID=="6"){?>selected<?}?>>공지사항</option>
                        <option value="7" <?if ($SearchBoardID=="7"){?>selected<?}?>>자유게시판</option>
                        <option value="8" <?if ($SearchBoardID=="8"){?>selected<?}?>>묻고 답하기</option>
                    </select>
                    <input type="text" name="SearchText" class="Input" placeholder="검색어를 입력해 주세요." value="<?=$SearchText?>"><a href="javascript:SearchSubmit()" class="BtnSearch">검색</a>
					</form>
				</div>
                <p class="SearchTxt">통합검색 ‘<b><?=$SearchText?></b>’에 관하여 전체 <b><?=$TotalRowCount?>건</b>이 검색되었습니다.</p>

                <div class="SearchResult">           
					<h3><img src="images1/Bullet1.gif"> 통합검색결과</h3>
				<?php
				$ListCount = 1;
				while($Row = $Stmt->fetch()) {
				$ListNumber = $TotalRowCount - $PageListNum * ($CurrentPage - 1) - $ListCount + 1;
				
					$BoardContentSubject = $Row["BoardContentSubject"];
					$BoardContentSubject = mb_substr($BoardContentSubject, 0, 40, "UTF-8")."...";
					
					$BoardContent = $Row["BoardContent"];
					$BoardContent = mb_substr($BoardContent, 0, 50, "UTF-8")."...";
				?> 					
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td style="width:85%;"><b><?=$BoardContentSubject?></b> (<?=substr($Row["BoardContentRegDateTime"],0,10)?>)</td>
                        <th style="width:15%;" rowspan="2"><a href="<?=$Row["LinkUrl"]?>" class="Btn5">바로가기</a></th>
                      </tr>
                      <tr>
                        <td><?=strip_tags($BoardContent)?></td>
                      </tr>
                    </table>
				<?php
					$ListCount ++;
				}
				$Stmt = null;
				?>


                </div>



<script>
function SearchSubmit(){
	if (document.SearchForm.SearchText.value==""){
		alert('검색어를 입력하세요.');
	}else{
		document.SearchForm.action = "search_list.php";
		document.SearchForm.submit();
	}
}
</script>




<?php
echo "\n";
echo $SubLayoutBottom;
echo "\n";
echo $MainLayoutBottom;
echo "\n";
?>

<?
include_once('./includes/common_analytics.php');
?>


<?php
include_once('./includes/common_footer.php');

if (trim($PageContentJavascript)!=""){
	echo "\n";
	echo "<script>";
	echo "\n";
	echo $PageContentJavascript;
	echo "\n";
	echo "</script>";
	echo "\n";
}

if (trim($SubLayoutJavascript)!=""){
	echo "\n";
	echo "<script>";
	echo "\n";
	echo $SubLayoutJavascript;
	echo "\n";
	echo "</script>";
	echo "\n";
}

if (trim($MainLayoutJavascript)!=""){
	echo "\n";
	echo "<script>";
	echo "\n";
	echo $MainLayoutJavascript;
	echo "\n";
	echo "</script>";
	echo "\n";
}
?>

</body>
</html>
<?php
include_once('./includes/dbclose.php');
?>





