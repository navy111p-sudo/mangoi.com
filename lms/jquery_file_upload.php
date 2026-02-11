<?php
        // 파일 타입을 체크해서 php, php3, html, htm 등 위험한 파일인 경우 제한    
        $MyFileName  = $_FILES['filedata']['name'];
        $FileTypeCheck = explode('.',$MyFileName);
	    $FileType       = $FileTypeCheck[count($FileTypeCheck)-1];
	    $i = 0;

        $RealFileName = "";
        while($i < count($FileTypeCheck)-1){
            $RealFileName .= $FileTypeCheck[$i];
            $i++;
        }
        
        // 파일이름을 변환
        $RealFileName = md5($RealFileName);

        if ($FileType=="php" || $FileType=="php3" || $FileType=="html" || $FileType=="htm"){
            $RealFileName .= "_";
        } else {
            $RealFileName .= ".".$FileType;
        }

        $uploadfile = "../uploads/document_files/".$RealFileName;

 
        if(move_uploaded_file($_FILES['filedata']['tmp_name'],$uploadfile)){
             echo ",".$RealFileName;
        } else {
             echo "파일 업로드 실패 !! 다시 시도해주세요.<br />";
        }
        
?>