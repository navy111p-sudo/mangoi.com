<!DOCTYPE html>
<html>
<head>
    <title>test_main</title>
    <?php 
    $CookieMemberSocketName = isset($_COOKIE['MemberSocketName']) ? $_COOKIE['MemberSocketName'] : "";
    $CookieComputerID = isset($_COOKIE['ComputerID']) ? $_COOKIE['ComputerID'] : 0;
    $CookieMemberID = isset($_COOKIE['MemberID']) ? $_COOKIE['MemberID'] : 0;
    $CookieLinkMemberID = isset($_COOKIE['LinkMemberID']) ? $_COOKIE['LinkMemberID'] : 0;
     ?>
    <!-- jquery 버전 틀리면 수정할것 ~ -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
</head>
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
        컴퓨터 번호 : <input type="text" name="ComputerID" value="<?=$CookieComputerID?>" >
    </div>
</p>
<hr>
<p>바꿀 값
    <form name="RegForm" method="POST">
        <div>
            컴퓨터 : <input type="text" name="ComputerID" value="0" >
            링크 : <input type="text" name="LinkMemberID" value="0" >
            <button onclick="javascript:ChangeLinkMemberID(ComputerID.value, LinkMemberID.value);">전환하기
        </div>
    </form>
</p>
<script language="javascript" type="text/javascript">
  function init()
  {
    var Url = "ws://211.117.60.181:8090/"

    websocket = new WebSocket(Url);
    websocket.onopen = function(evt) { onOpen(evt) };
    websocket.onclose = function(evt) { onClose(evt) };
    websocket.onmessage = function(evt) { onMessage(evt) };
    websocket.onerror = function(evt) { onError(evt) };
  }

  function onOpen(evt)
  {
    writeToScreen("connected\n");
      // document.myform.connectButton.disabled = true;
      // document.myform.disconnectButton.disabled = false;
  }

  function onClose(evt)
  {
    writeToScreen("disconnected\n");
      // document.myform.connectButton.disabled = false;
      // document.myform.disconnectButton.disabled = true;
  }

  function onMessage(evt)
  {
    //console.log(evt.data);
    //var json = JSON.parse(evt.data); // 아이디, 내용 구별하기 위한 json
    //writeToScreen("onMessage : " + json.id + " : " + json.msg + '\n');

    // evt is object, and have 2 attr : id, msg
    //console.log(evt.data);

     /* default  ajax
    var ChangeLinkMemberCheck = evt.data.indexOf("|||");
    // alert(ChangeLinkMemberCheck);

    if (ChangeLinkMemberCheck == -1) {
        writeToScreen("onMessage : " + evt.data + '\n');
    } else {
        var Result = evt.data.split(" ||| ");
        var TempComputerID = Result[0];
        var TempLinkMemberID = Result[1];

        $.ajax({
            url: "ajax_set_student_cookie.php",
            data: {
                TempComputerID: TempComputerID,
                TempLinkMemberID: TempLinkMemberID
            }
        });
        var MyComputerID = getCookie('ComputerID');
        alert("Computer : " + TempComputerID + " MyComputerID : " + MyComputerID);
        if (TempComputerID == MyComputerID) {
            alert("setcookie " + TempLinkMemberID);
            setCookie('LinkMemberID', TempLinkMemberID);
            location.reload(true);
        }
    }
     */


    // /* default  원본
    var ChangeLinkMemberCheck = evt.data.indexOf("|||");
    // alert(ChangeLinkMemberCheck);

    if (ChangeLinkMemberCheck == -1) {
        writeToScreen("onMessage : " + evt.data + '\n');
    } else {
        var Result = evt.data.split(" ||| ");
        var TempComputerID = Result[0];
        var TempLinkMemberID = Result[1];

        var MyComputerID = getCookie('ComputerID');
        alert("Computer : " + TempComputerID + " MyComputerID : " + MyComputerID);
        if (TempComputerID == MyComputerID) {
            alert("setcookie " + TempLinkMemberID);
            setCookie('LinkMemberID', TempLinkMemberID);
            location.reload(true);
        }
    }
    // */
  }

  function onError(evt)
  {
    writeToScreen('error: ' + evt.data + '\n');

      websocket.close();

      document.myform.connectButton.disabled = false;
      document.myform.disconnectButton.disabled = true;

  }

  function doSend(message)
  {
    var WriteToScreen = JSON.parse(message);  // json 용
    //console.log("doSend : " + message);
    writeToScreen(WriteToScreen.id + " : " + WriteToScreen.msg + '\n'); 
    //websocket.send(WriteToScreen.id + " : " + WriteToScreen.msg + '\n');
    websocket.send(message)
    websocket.send(WriteToScreen.id + " : " + WriteToScreen.msg + '\n');  // json
  }

  function writeToScreen(message)
  {
    document.myform.outputtext.value += message
    document.myform.outputtext.scrollTop = document.myform.outputtext.scrollHeight;

  }

  window.addEventListener("load", init, false);


  function ChangeLinkMemberID(ComputerID, LinkMemberID) {
      writeToScreen(ComputerID + " ||| " + LinkMemberID);
      websocket.send(ComputerID + " ||| " + LinkMemberID);
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