<!DOCTYPE html>
<html>
<head>
    <title>Index File</title>

</head>
<body>
    <div>
        <form name="RegForm">
            ComputerID : <input type="number" name="ComputerID"><br>
            MemberID : <input type="number" name="MemberID"><br>
            LinkMemberID : <input type="number" name="LinkMemberID"><br>
            이름 : <input type="text" name="MemberSocketName">
            <a onclick="javascript:submitData();">submit</a>
        </form>
    </div>
    <script>
        function submitData() {
            var Url = "ajax_set_into_database.php";
            var ComputerID = document.RegForm.ComputerID.value;
            var MemberSocketName = document.RegForm.MemberSocketName.value;
            var MemberID = document.RegForm.MemberID.value;
            var LinkMemberID = document.RegForm.LinkMemberID.value;

            //location.href = Url + "?ComputerID=" + ComputerID + "&MemberSocketName="+MemberSocketName + "&MemberID="+MemberID + "&LinkMemberID="+LinkMemberID;
            $.ajax({
                url: Url,
                data: {
                    ComputerID: ComputerID,
                    MemberID: MemberID,
                    LinkMemberID : LinkMemberID,
                    MemberSocketName: MemberSocketName
                },
                success: function(res) {
                    if(res.Result==1) {
                        location.href = "websocket.php";
                    } else {
                        alert("정보 추가 실패 : 관리자에게 문의해주세요.");
                    }
                },
                fail: function(err) {
                    console.log(err);
                }
            });
        }
    </script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
</body>
</html>