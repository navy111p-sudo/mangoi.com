<!DOCTYPE html>

<meta charset="utf-8" />
<?php 
    $CookieMemberSocketName = isset($_COOKIE['MemberSocketName']) ? $_COOKIE['MemberSocketName'] : "";
    $CookieComputerID = isset($_COOKIE['ComputerID']) ? $_COOKIE['ComputerID'] : 0;
    $CookieMemberID = isset($_COOKIE['MemberID']) ? $_COOKIE['MemberID'] : 0;
    $CookieLinkMemberID = isset($_COOKIE['LinkMemberID']) ? $_COOKIE['LinkMemberID'] : 0;
 ?>
<title>WebSocket Test</title>

<body>
<div id="output"></div>

<form name="myform">
<p>
<textarea name="outputtext" rows="20" cols="50"></textarea>
</p>
<p>
  <div id="inputarea">
    계정 : <input type="text" name="id"><br>
    내용 : <textarea name="inputtext" cols="50"></textarea>
  </div>
</p>
<p>
<textarea name="url" cols="50"></textarea>
</p>
<p>
<input type="button" name=sendButton value="Send" onClick="sendText();">
<input type="button" name=clearButton value="Clear" onClick="clearText();">
<input type="button" name=disconnectButton value="Disconnect" onClick="doDisconnect();">
<input type="button" name=connectButton value="Connect" onClick="doConnect();">
</p>


<p>현재 값
    <div>
        학생 이름 : <input type="text" name="CookieMemberSocketName" value="<?=$CookieMemberSocketName?>" >
        멤버 ID : <input type="text" name="CookieMemberID" value="<?=$CookieMemberID?>" >
        링크 ID : <input type="text" name="CookieLinkMemberID" value="<?=$CookieLinkMemberID?>" >
        컴퓨터 번호 : <input type="text" name="CookieComputerID" value="<?=$CookieComputerID?>" >
    </div>
</p>
<?php if ($CookieMemberID==1): ?>
<hr>
<p>바꿀 값
    <form name="RegForm" method="POST">
        <div>
            컴퓨터 : <input type="text" name="ComputerID" value="0" >
            링크 : <input type="text" name="LinkMemberID" value="0" >
            <a href="javascript:ChangeLinkMemberID();" >전환하기</a>
        </div>
    </form>
</p>
<?php endif ?>
</form>


<script language="javascript" type="text/javascript">


  function init()
  {
  document.myform.url.value = "ws://211.117.60.181:8090/"
  document.myform.inputtext.value = "Hello World!"
  document.myform.disconnectButton.disabled = true;
  }

  function doConnect()
  {
    websocket = new WebSocket(document.myform.url.value);
    websocket.onopen = function(Evt) { onOpen(Evt) };
    websocket.onclose = function(Evt) { onClose(Evt) };
    websocket.onmessage = function(Evt) { onMessage(Evt) };
    websocket.onerror = function(Evt) { onError(Evt) };
  }

  function onOpen(Evt)
  {
    writeToScreen("connected\n");
    document.myform.connectButton.disabled = true;
    document.myform.disconnectButton.disabled = false;
  }

  function onClose(Evt)
  {
    writeToScreen("disconnected\n");
    document.myform.connectButton.disabled = false;
    document.myform.disconnectButton.disabled = true;
  }

  function onMessage(Evt)
  {
    // 현재의 컴터값과 링크값
    var CookieComputerID = document.myform.CookieComputerID.value;
    var CookieLinkMemberID = document.myform.CookieLinkMemberID.value;
    var json = JSON.parse(Evt.data);

    if(json.type=="text") {
      writeToScreen(json.msg + '\n');
    } else if(json.type=="Message") {
      writeToScreen(json.msg+'\n');
    } else if(json.type=="linked") {
      if(json.id == CookieComputerID) {
        // alert(CookieComputerID);
        // alert(CookieLinkMemberID);
        // alert(json.link);

        alert("기존컴터쿠키 : " + CookieComputerID + "\n기존링크 : " + CookieLinkMemberID + "\n바꾸려는 링크 : " + json.link);
        setCookie('LinkMemberID', json.link);
        location.reload(true);
      }
    }
    //console.log(json.data);
    //var json = JSON.parse(json.data); // 아이디, 내용 구별하기 위한 json
    //writeToScreen("onMessage : " + json.id + " : " + json.msg + '\n');
    // Evt is object, and have 2 attr : id, msg
  }

  function onError(Evt)
  {
    writeToScreen('error: ' + Evt.data + '\n');

    websocket.close();

    document.myform.connectButton.disabled = false;
    document.myform.disconnectButton.disabled = true;

  }

  // json object 를 가져와 웹소켓으로 발송
  function doSend(Message)
  {
    var StrMessage = JSON.stringify(Message);
    alert("doSend : " + StrMessage);
    writeToScreen(StrMessage + "\n"); 

    websocket.send(StrMessage);  // string
  }

  function writeToScreen(Message)
  {
    document.myform.outputtext.value += Message
    document.myform.outputtext.scrollTop = document.myform.outputtext.scrollHeight;

  }

  window.addEventListener("load", init, false);


    // 아이디와 내용을 가져와 json 으로 만들어 발송함수 호출 ( json object )
   function sendText() {
    var id = document.myform.id.value;
    var Message = document.myform.inputtext.value;
    var json = { "type": "text", "id": id, "msg": Message };

    doSend( json );
   }

  function clearText() {
    document.myform.outputtext.value = "";
   }

   function doDisconnect() {
    websocket.close();
   }

  function ChangeLinkMemberID() {
    var LinkMemberID = document.myform.LinkMemberID.value;
    var ComputerID = document.myform.ComputerID.value;
    var json = { "type": "linked", "id": ComputerID, "link": LinkMemberID };
    // json = JSON.parse(json);
    writeToScreen(json.id + " 컴터의 링크번호 " + LinkMemberID + " 를 " + json.link + " 로 변환요청 수신");
    // alert("ChangeLinkMemberID : " + json);
    doSend(json);

  }

  // 쿠키 함수 !
  function setCookie(name, value) {
      //date.setTime(date.getTime() + exp*24*60*60*1000);
      //document.cookie = namae + '=' + value + ';expires=' + date.toUTCString() + ';path=/';
      document.cookie = name + '=' + value;
  }

  function getCookie(name) {
      var value = document.cookie.match('(^|;) ?' + name + '=([^;]*)(;|$)');
      return value? value[2] : null;
  };


</script>
</body>
</html> 

