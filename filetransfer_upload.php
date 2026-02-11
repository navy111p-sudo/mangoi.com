<?

$file = isset($_REQUEST["file"]) ? $_REQUEST["file"] : "";

$UpPath = "./uploads/app_upload_direct_qna/";


$TempFile = $_FILES['file']['tmp_name'];
if ($TempFile){

    $MyFile         = $_FILES['file']['name'];
    $MyFileSize     = $_FILES['file']['size'];
    $MyFileMimeType = $_FILES['file']['type'];
    $MyFileName     = (iconv('utf-8','euc-kr',$MyFile));
    $MyFileRealName = $MyFileName;

    $FileTypeCheck = explode('.',$MyFileName);
    $FileType       = $FileTypeCheck[count($FileTypeCheck)-1];
    $i = 0;
    
    $RealFileName = "";
    while($i < count($FileTypeCheck)-1){
        $RealFileName .= $FileTypeCheck[$i];
        $i++;
    }
    
    $RealFileName = md5($RealFileName);
    $RealFileNameResize = $RealFileName."_rs";
    $RealFileNameCrop = $RealFileName."_cp";

    $ExistFlag = 0;
    if(file_exists($UpPath.$RealFileName.'.'.$FileType)){
        $i = 1;
        while($ExistFlag != 1){
            if(!file_exists($UpPath.$RealFileName.'['.$i.'].'.$FileType)){
                $ExistFlag = 1;
                $MyFileName = $RealFileName.'['.$i.'].'.$FileType;
                $MyFileNameResize = $RealFileNameResize.'['.$i.'].'.$FileType;
                $MyFileNameCrop = $RealFileNameCrop.'['.$i.'].'.$FileType;
            }
            $i++;

        } 
    }else{
        $MyFileName = $RealFileName.'.'.$FileType;
        $MyFileNameResize = $RealFileNameResize.'.'.$FileType;
        $MyFileNameCrop = $RealFileNameCrop.'.'.$FileType;
    }

    if ($FileType=="php" || $FileType=="php3" || $FileType=="html"){
        $MyFileName = $MyFileName."_";
    }

    if(!@copy($TempFile, $UpPath.$MyFileName)) { echo("error"); }

    $DbMyFileName      = (iconv('euc-kr','utf-8',$MyFileName));
    $DbMyFileRealName  = (iconv('euc-kr','utf-8',$MyFileRealName));
    $DbMyFileSize      = $MyFileSize;
    $DbMyFileExtension = $FileType;
    $DbMyFileMimeType  = $MyFileMimeType;

}

echo $MyFileName;
/*
$ArrValue["TempFileName"] = $MyFileName;
$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 
*/
function my_json_encode($arr){
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

?>