<?
session_start();
include_once('./includes/dbopen.php');
include_once('./includes/common.php');
include_once('./includes/password_hash.php');
 
define('NAVER_CLIENT_ID', 'V20F4sbnagaPauTDXKMh');
define('NAVER_CLIENT_SECRET', 'E6jacbQ1Wf');
define('NAVER_CALLBACK_URL', 'http://www.mangoi.co.kr/callback_member.php');
 
 
$naver_curl = "https://nid.naver.com/oauth2.0/token?grant_type=authorization_code&client_id=".NAVER_CLIENT_ID."&client_secret=".NAVER_CLIENT_SECRET."&redirect_uri=".urlencode(NAVER_CALLBACK_URL)."&code=".$_REQUEST['code']."&state=".$_REQUEST['state'];
 
// 토큰값 가져오기 
$is_post = false; 
$ch = curl_init(); 
curl_setopt($ch, CURLOPT_URL, $naver_curl); 
curl_setopt($ch, CURLOPT_POST, $is_post); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
 
$response = curl_exec ($ch); 
$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
curl_close ($ch); 
 
if($status_code == 200){ 
    $responseArr = json_decode($response, true); 
 
      // 토큰값으로 네이버 회원정보 가져오기 
      $headers = array( 'Content-Type: application/json', sprintf('Authorization: Bearer %s', $responseArr['access_token']) ); 
      $is_post = false; 
      $me_ch = curl_init(); 
      curl_setopt($me_ch, CURLOPT_URL, "https://openapi.naver.com/v1/nid/me"); 
      curl_setopt($me_ch, CURLOPT_POST, $is_post ); 
      curl_setopt($me_ch, CURLOPT_HTTPHEADER, $headers); 
      curl_setopt($me_ch, CURLOPT_RETURNTRANSFER, true); 
      $res = curl_exec ($me_ch); 
      curl_close ($me_ch); 
      $res_data = json_decode($res , true); 
       
    
    /*
    {
      "resultcode": "00",
      "message": "success",
      "response": {
        "email": "openapi@naver.com",
        "nickname": "OpenAPI",
        "profile_image": "https://ssl.pstatic.net/static/pwe/address/nodata_33x33.gif",
        "age": "40-49",
        "gender": "F",
        "id": "32742776",
        "name": "오픈 API",
        "birthday": "10-01"
      }
    }
    */
      
      if ($res_data['response']['email']) { 
      //해당 아이디값을 정상적으로 가져온다면 디비에 해당 아이디로 회원가입 여부 확인 하여 회원 가입을 하였으면 자동 로그인 구현.
 
            $Sql = "SELECT * from Members where MemberLoginID='". $res_data['response']['email'] ."'";
            $Stmt = $DbConn->prepare($Sql);
            $Stmt->execute();
            $Stmt->setFetchMode(PDO::FETCH_ASSOC);
            $Row = $Stmt->fetch();
            $Stmt = null;
            
            if($Row["MemberID"]){ // 이미 가입된 회원이면 자동로그인한다.
                $ApplyMemberLoginID = $Row['MemberLoginID'];
                setcookie("LoginMemberID", $ApplyMemberLoginID, 0, false, NULL);
                setcookie("LinkLoginMemberID", $ApplyMemberLoginID, 0, false, NULL);
                $Sql = "UPDATE Members set LastLoginDateTime=now() where MemberLoginID=:ApplyMemberLoginID";
                $Stmt = $DbConn->prepare($Sql);
                $Stmt->bindParam(':ApplyMemberLoginID', $ApplyMemberLoginID);
                $Stmt->execute();
                $Stmt = null;
                
                echo "<script>
                  //팝업창에서 부모창을 다른페이지로 이동합니다.
                  window.opener.location.href='index.php';
                  self.close();
                  </script>";
                // header("Location: index.php");
 
                exit;
            } else {        // 새로 회원가입을 하고 자동로그인추가한다.
                $Email = $res_data['response']['email'];
                $MemberName = isset($res_data['response']['name'])?$res_data['response']['name']:"";
                $NickName = isset($res_data['response']['nickname'])?$res_data['response']['nickname']:"";
                $Phone = isset($res_data['response']['phone'])?$res_data['response']['phone']:"";
                if ($res_data['response']['gender']=='M') $MemberSex = 1;
                    else $MemberSex = 2;
                
                $Sql = " INSERT into Members ( ";
                    $Sql .= " MemberLoginID, ";
                    $Sql .= " MemberName, ";
                    $Sql .= " MemberNickName, ";
                    $Sql .= " MemberPhone1, ";
                    $Sql .= " MemberEmail, ";
                //    $Sql .= " MemberBirthday, ";
                    $Sql .= " MemberSex, ";
                    $Sql .= " MemberRegDateTime, ";
                    $Sql .= " MemberModiDateTime, ";
                    $Sql .= " MemberState ";
                $Sql .= " ) values ( ";
                    $Sql .= " '".$Email."', ";
                    $Sql .= " '".$MemberName."', ";
                    $Sql .= " '".$MemberNickName."', ";
                    $Sql .= " HEX(AES_ENCRYPT('".$MemberPhone1."', '".$EncryptionKey."')), ";
                    $Sql .= " HEX(AES_ENCRYPT('".$MemberEmail."', '".$EncryptionKey."')), ";
                    $Sql .= " ".$MemberSex.", ";
                    $Sql .= " now(), ";
                    $Sql .= " now(), ";
                    $Sql .= " 1 ";
                $Sql .= " )";

                
                $Stmt = $DbConn->prepare($Sql);
                //echo $Sql;
                $Stmt->execute();
                //echo $Email."test2";
                
                $Stmt = null;

                echo "redirect2";
                $Sql = "SELECT * from Members where MemberLoginID='". $res_data['response']['email'] ."'";
                $Stmt = $DbConn->prepare($Sql);
                $Stmt->execute();
                $Stmt->setFetchMode(PDO::FETCH_ASSOC);
                $Row = $Stmt->fetch();

                $MemberID = $Row["MemberID"];

                InsertPoint(1, 0, $MemberID, "회원가입(웹)", "회원가입(웹)" ,$OnlineSiteMemberRegPoint);
                SendSmsWelcome($MemberID, $EncryptionKey);
                setcookie("LoginMemberID", $res_data['response']['email'], 0, "/");
                setcookie("LinkLoginMemberID", $res_data['response']['email'], 0, "/");
                echo "redirect3";

                echo "<script>
                  //팝업창에서 부모창을 다른페이지로 이동합니다.
                  window.opener.location.href='member_form_sns.php';
                  self.close();
                  </script>";
                //header("Location: member_form_sns.php");
 
                exit;      
            }
        
 
      }
}
include_once('./includes/dbclose.php');

?>
