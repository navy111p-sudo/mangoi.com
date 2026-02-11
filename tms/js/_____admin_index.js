//===========================================================================================================================//
//                                                       관리자 javascript
//===========================================================================================================================//
//  커서 다음필드로 이동함수
//---------------------------------------------------------------------------------------------------------------------------//
function NextField(oTa,formname) {

     var form       = eval("document."+formname);
     var fieldname  = new Array();
     var check_nums = form.elements.length;
     var c          = 0;
     for(var i = 0; i < check_nums; i++) {                // 폼안에 들어있는 요소(필드명)들을 읽어들인다.
            var fieldname_obj = eval("document."+formname+".elements[" + i + "]");
            if (fieldname_obj.type == 'text') {
                    fieldname[c]  = fieldname_obj.name;
                    var lastfiled = fieldname_obj.name;
                    c = c + 1;
            }
     }
     for(var i = 0; i < c; i++) {
            if(fieldname[i] == oTa.name) {
                  if (i < check_nums) {
                        if (oTa.name == lastfiled) {     // 더이상 커서가 못가게 마지막 필드명을 비교한다.
                               oTa.focus();
                        } else {
                               var f = i + 1;
                               var fieldname_obj = eval("document."+formname+"." + fieldname[f]);
                               fieldname_obj.focus();
                        }
                  }
            }
      }

}
//---------------------------------------------------------------------------------------------------------------------------//
//   Input type="Text"를 핸드폰 전화번호 내용으로 사용
//---------------------------------------------------------------------------------------------------------------------------//
function formattedHpno(oTa) {
      
      var vm     = oTa.value;
      var a      = removeFormat(vm,'-');
      var money  = reverse(a);
      var format = "";
      for(var i = money.length-1; i > -1; i--) {
            if((i+1)%4 == 0 && money.length-1 != i) format += "-";
            format += money.charAt(i);
      }
      if (format.length >= 13) {
            var format = removeFormat(format,'-');
            format = format.substr(0,3) + '-' + format.substr(3,4) + '-' + format.substr(7,4);
      }  

      oTa.value = format;
      oTa.style.backgroundColor='#ffffff';

}
//---------------------------------------------------------------------------------------------------------------------------//
//   Input type="Text"를 일반전화번호 내용으로 사용
//---------------------------------------------------------------------------------------------------------------------------//
function formattedTelno(oTa) {

      var telno = oTa.value;
      if (telno.substr(0,2) == '02') {
            
           if (telno.length==2) {
                 oTa.value = telno + "-";
           }
           if (telno.length==6) {
                 oTa.value = telno + "-";
           }
           if (telno.length >= 12) {
                 var telno = removeFormat(telno,'-');
                 oTa.value = telno.substr(0,2) + '-' + telno.substr(2,4) + '-' + telno.substr(6,4);
           }  

      } else if(telno.substr(0,2) != '02') {

           if (telno.length==3) {
                 oTa.value = telno + "-";
           }
           if (telno.length==7) {
                 oTa.value = telno + "-";
           }
           if (telno.length >= 13) {
                 var telno = removeFormat(telno,'-');
                 oTa.value = telno.substr(0,3) + '-' + telno.substr(3,4) + '-' + telno.substr(7,4);
           }  

      } 


}
//---------------------------------------------------------------------------------------------------------------------------//
//   Input type="Text"를 사업자번호 내용으로 사용
//---------------------------------------------------------------------------------------------------------------------------//
function formattedSaupjano(oTa) {

      var saupjano = oTa.value;
      
      if (saupjano.length==3) {
             oTa.value = saupjano + "-";
      }
      if (saupjano.length==6) {
             oTa.value = saupjano + "-";
      }
      if (saupjano.length >= 12) {
             var saupjano = removeFormat(saupjano,'-');
             oTa.value = saupjano.substr(0,3) + '-' + saupjano.substr(3,2) + '-' + saupjano.substr(5,5);
      }  

}
//---------------------------------------------------------------------------------------------------------------------------//
//   Input type="Text"를 주민등록번호 혹은 법인번호 내용으로 사용
//---------------------------------------------------------------------------------------------------------------------------//
function formattedJuminno(oTa) {

      var saupjano = oTa.value;
      
      if (saupjano.length==6) {
             oTa.value = saupjano + "-";
      }
      if (saupjano.length >= 11) {
             var saupjano = removeFormat(saupjano,'-');
             oTa.value = saupjano.substr(0,6) + '-' + saupjano.substr(6,7);
      }  

}

//---------------------------------------------------------------------------------------------------------------------------//
//   Input type="Text"를 Money 에 관련된 내용으로 사용
//---------------------------------------------------------------------------------------------------------------------------//
function formattedMoney(oTa) {

      var vm     = oTa.value;
      var a      = removeFormat(vm,',');
      var money  = reverse(a);
      var format = "";
      for(var i = money.length-1; i > -1; i--) {
            if((i+1)%3 == 0 && money.length-1 != i) format += ",";
            format += money.charAt(i);
      }
      oTa.value = format;
      oTa.style.backgroundColor='#ffffff';

}
//---------------------------------------------------------------------------------------------------------------------------//
//  String을 꺼꾸로 만들어 준다.
//---------------------------------------------------------------------------------------------------------------------------//
function reverse(s) {

      var rev = "";

      for(var i = s.length-1; i >= 0 ; i--) {
            rev += s.charAt(i);
      }

      return rev;

}
//---------------------------------------------------------------------------------------------------------------------------//
//   형식화된 내용의 심볼들을 없애고 원래의 내용만을 보여준다.
//---------------------------------------------------------------------------------------------------------------------------//
function removeFormat(content, sep) {

      var real = "";
      var contents = content.split(sep);

      for(var i = 0; i < contents.length; i++) {
              real += contents[i];
      }
      var real_val = "";
      for(var i = 0; i < real.length; i++) {
            var chr = real.substr(i,1);
            if(chr >= '0' && chr <= '9') {
                   real_val += chr;
            }
      }

      return real_val;

}
//---------------------------------------------------------------------------------------------------------------------------//
// 화폐단위
//---------------------------------------------------------------------------------------------------------------------------//
function numberWithCommas(x) {

      return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

}
//---------------------------------------------------------------------------------------------------------------------------//
// 숫자인지 체크
//---------------------------------------------------------------------------------------------------------------------------//
function IsNumberData(sMsg) {

      for(var i = 0; i < sMsg.length; i++) {
             var chr = sMsg.substr(i,1);
             if(chr < '0' || chr > '9') {
                  return false;
             }
      }
      return true;

}
//-------------------------------------------------------------------------------------------------------------------------//
// 단문비교함수
//-------------------------------------------------------------------------------------------------------------------------//
function jviif( sw, a, b ) {

      if (sw) {
            return a;
      } else {
            return b;
      }

}
//-------------------------------------------------------------------------------------------------------------------------//
// 문자다시조합하기
//-------------------------------------------------------------------------------------------------------------------------//
function RemoveDash2(sNo) {
       var reNo = ""
       for(var i=0; i<sNo.length; i++) {
             reNo += sNo.charAt(i)
       }
       return reNo
}
//-------------------------------------------------------------------------------------------------------------------------//
// (-)빼고 숫자만 다시조합하기
//-------------------------------------------------------------------------------------------------------------------------//
function RemoveDash3(sNo) { 
    var reNo = "" 
    for(var i=0; i<sNo.length; i++) { 
        if ( sNo.charAt(i) != "/" && sNo.charAt(i) != "-" && sNo.charAt(i) != ":" && sNo.charAt(i) != "*" && sNo.charAt(i) != ";" ) { 
            reNo += sNo.charAt(i) 
        } 
    } 
    return reNo 
} 
//-------------------------------------------------------------------------------------------------------------------------//
// 문자열 길이체크
//-------------------------------------------------------------------------------------------------------------------------//
function GetMsgLen(sMsg) { // 0-127 1byte, 128~ 2byte
      var count = 0
      for(var i=0; i<sMsg.length; i++) {
              if ( sMsg.charCodeAt(i) > 127 ) {
                      count += 2
              } else {
                      count++
              }
      }
      return count
}
//-------------------------------------------------------------------------------------------------------------------------//
// 날짜 숫자인지 체크
//-------------------------------------------------------------------------------------------------------------------------//
function IsNumberDate(sMsg) {
      for(var i = 0; i < sMsg.length; i++) {
         var chr = sMsg.substr(i,1);
         if(chr < '0' || chr > '9') {            
              return false;
         }
      }
      return true;   
}
//---------------------------------------------------------------------------------------------------------------------------//
// SELECT 객체 생성
//---------------------------------------------------------------------------------------------------------------------------//
function createOption( text, value, selected ) {
        var oOption = document.createElement("OPTION"); // Option 객체를 생성
        oOption.text = text;                            // Text(Keyword)를 입력
        oOption.value = value;                          // Value를 입력
        if (selected=="selected"){
            oOption.selected = true;
        }
        return oOption;
}
// SelectBox의 Option을 초기화
function initOption( objId ) {
        var selectObj = document.getElementById( objId );
        if ( selectObj == null ) return;               // 객체가 존재하지 않으면 취소

        selectObj.options.length = 0;                  // 길이를 0으로 하면 초기화
}

// Option을 추가
function addOption( objId, text, value, selected ) {
        var selectObj = document.getElementById( objId );

        selectObj.add( createOption( text , value, selected ) );
        text     = "";
        value    = "";
        selected = "";
}
//---------------------------------------------------------------------------------------------------------------------------//
// jQuery Select box 기본 값 남기고 삭제 하기
//---------------------------------------------------------------------------------------------------------------------------//
function fnResetSelectBox(formName,objName ) {
      $("form[name='"+formName+"'] select[name='"+ objName +"'] option").not("[value='']").remove();
}
//===========================================================================================================================//
// 아이디 검증(한글 입력여부 체크)
//---------------------------------------------------------------------------------------------------------------------------//
function HAN_IsID(formname,textname) {
      var form = eval("document."+formname+"." + textname);      
      for(var i = 0; i < form.value.length; i++) {
             var chr = form.value.substr(i,1);         
             if((chr < '0' || chr > '9') && (chr < 'a' || chr > 'z') && (chr < 'A' || chr > 'Z')) {
                     return false;
             }
      }
      return true;   
}
//---------------------------------------------------------------------------------------------------------------------------//
// 비밀번호 검증(한글 입력여부 체크)
//---------------------------------------------------------------------------------------------------------------------------//
function HAN_IsPW(formname,textname) {
      var form = eval("document."+formname+"." + textname);      
      for(var i = 0; i < form.value.length; i++) {
             var chr = form.value.substr(i,1);         
             if((chr < '0' || chr > '9') && (chr < 'a' || chr > 'z') && (chr < 'A' || chr > 'Z')) {
                    return false;
             }
      }
      return true;   
}
//---------------------------------------------------------------------------------------------------------------------------//
// 자료 입력시 사용할 날짜입력폼(0000/00/00)
//---------------------------------------------------------------------------------------------------------------------------//
function OnCheckDate(oTa,formname) { 
    var form    = eval("document."+formname);  
    var sMsg    = oTa.value; 
    var onlynum = "" ; 
    onlynum = RemoveDash3(sMsg); 
    if (!IsNumberDate(onlynum)) {
         alert("숫자만 입력 하세요!");
         oTa.value = "";
         oTa.focus();   
         return;
    }
    if (GetMsgLen(onlynum) == 4 && (parseInt(onlynum.substring(0,4)) < 2018 || parseInt(onlynum.substring(0,4)) > 2100)) {
         alert("년도 입력이 2018 ~ 2100 년도를 벗어 났습니다!");
         oTa.value = "";
         oTa.focus();   
         return;
    }
    if (GetMsgLen(onlynum) == 6 && (parseInt(onlynum.substring(4,6)) > 12)) {
         alert("입력범위를 벗어 났습니다!");
         oTa.value = onlynum.substring(0,4) + "-";
         oTa.focus();   
         return;
    }
    if (GetMsgLen(onlynum) == 8 && (parseInt(onlynum.substring(6,8)) > 31)) {
         alert("입력범위를 벗어 났습니다!");
         oTa.value = onlynum.substring(0,4) + "-" + onlynum.substring(4,6) + "/";
         oTa.focus();   
         return;
    }
    if(event.keyCode != 8 ) { 

        if (GetMsgLen(onlynum) <= 3) oTa.value = onlynum; 
        if (GetMsgLen(onlynum) == 4) oTa.value = onlynum.substring(0,4) + "-";
        if (GetMsgLen(onlynum) == 5) oTa.value = onlynum.substring(0,4) + "-" + onlynum.substring(4,5);
        if (GetMsgLen(onlynum) == 6) oTa.value = onlynum.substring(0,4) + "-" + onlynum.substring(4,6) + "-";
        if (GetMsgLen(onlynum) == 7) oTa.value = onlynum.substring(0,4) + "-" + onlynum.substring(4,6) + "-" + onlynum.substring(6,7);
        if (GetMsgLen(onlynum) >= 8) {
             oTa.value = onlynum.substring(0,4) + "-" + onlynum.substring(4,6) + "-" + onlynum.substring(6,8);

             // 다음 필드명 찾아서 포커스 맞추기
             var fieldname = new Array();
             var check_nums = form.elements.length;
             for(var i = 0; i < check_nums; i++) {          //폼안에 들어있는 요소(필드명)들을 읽어들인다.
                    var fieldname_obj = eval("document."+formname+".elements[" + i + "]");
                    fieldname[i]  = fieldname_obj.name;  
                    var lastfield = fieldname_obj.name;  
             }
             for(var i = 0; i < check_nums; i++) {
                    if(fieldname[i] == oTa.name) {
                          if (i < check_nums) {
                                if (oTa.name == lastfield) {  //더이상 커서가 못가게 마지막 필드명을 비교한다.
                                        oTa.focus();
                                } else {
                                        var f = i + 1;
                                        var fieldname_obj = eval("document."+formname+".elements[" + f + "]");
                                        fieldname_obj.focus(); 
                                }
                          }
                    }
             }
              
        }
    }
} 
//---------------------------------------------------------------------------------------------------------------------------//
// 자료 입력시 사용할 시간입력폼(00:00)
//---------------------------------------------------------------------------------------------------------------------------//
function OnCheckTime(oTa,formname) { 
    var form    = eval("document."+formname);      
    var sMsg    = oTa.value; 
    var onlynum = "" ; 
    onlynum = RemoveDash3(sMsg); 
    if (GetMsgLen(onlynum) == 2 && (parseInt(onlynum.substring(0,2)) < 0 || parseInt(onlynum.substring(0,2)) > 24)) {
         alert("시간 입력이 1 ~ 24 시간을 벗어 났습니다!");
         oTa.value = "";
         oTa.focus();   
         return;
    }
    if(event.keyCode != 8 ) { 

        if (GetMsgLen(onlynum) <= 1) oTa.value = onlynum; 
        if (GetMsgLen(onlynum) == 2) oTa.value = onlynum.substring(0,2) + ":";
        if (GetMsgLen(onlynum) == 3) oTa.value = onlynum.substring(0,2) + ":" + onlynum.substring(2,3);
        if (GetMsgLen(onlynum) >= 4) {
             oTa.value = onlynum.substring(0,2) + ":" + onlynum.substring(2,4);

             // 다음 필드명 찾아서 포커스 맞추기
             var fieldname = new Array();
             var check_nums = form.elements.length;
             for(var i = 0; i < check_nums; i++) {          //폼안에 들어있는 요소(필드명)들을 읽어들인다.
                    var fieldname_obj = eval("document."+formname+".elements[" + i + "]");
                    fieldname[i]  = fieldname_obj.name;  
                    var lastfield = fieldname_obj.name;  
             }
             for(var i = 0; i < check_nums; i++) {
                    if(fieldname[i] == oTa.name) {
                          if (i < check_nums) {
                                if (oTa.name == lastfield) {  //더이상 커서가 못가게 마지막 필드명을 비교한다.
                                        oTa.focus();
                                } else {
                                        var f = i + 1;
                                        var fieldname_obj = eval("document."+formname+".elements[" + f + "]");
                                        fieldname_obj.focus(); 
                                }
                          }
                    }
             }
             
        }
    }
} 
//---------------------------------------------------------------------------------------------------------------------------//
// Page Excange LoadingBar Open....
//---------------------------------------------------------------------------------------------------------------------------//
function pageLoadingBar() {
     $(function(){
          $('.dark_bg').fadeIn();
          $('.wrap-loading').fadeIn();
     });
}
//===========================================================================================================================//
//
//---------------------------------------------------------------------------------------------------------------------------//
// 자료등록 오픈
//---------------------------------------------------------------------------------------------------------------------------//
function BoardWrite_Open() {
      
      pageLoadingBar();

      $('#EditID').val('');
      $('#WriteSW').val('1');

      $('#postform').submit(); 

}
//---------------------------------------------------------------------------------------------------------------------------//
// 자료수정 오픈
//---------------------------------------------------------------------------------------------------------------------------//
function BoardEdit_Open(uqcode, page) {
      
      pageLoadingBar();

      $('#EditID').val(uqcode);
      $('#WriteSW').val('2');
      $('#CurrentPage').val(page);

      $('#postform').submit(); 

}
//---------------------------------------------------------------------------------------------------------------------------//
// 자료보기
//---------------------------------------------------------------------------------------------------------------------------//
function BoardView_Open(uqcode, page) {
      
      pageLoadingBar();

      $('#EditID').val(uqcode);
      $('#WriteSW').val('5');
      $('#CurrentPage').val(page);

      $('#postform').submit(); 

}
//---------------------------------------------------------------------------------------------------------------------------//
// 리스트 목록으로....
//---------------------------------------------------------------------------------------------------------------------------//
function BoardList_Return(page) {

      pageLoadingBar();

      $('#CurrentPage').val(page);
      $('#EditID').val('');
      $('#WriteSW').val('');
      $('#key').val('');

      $('#postform').submit(); 

}
//---------------------------------------------------------------------------------------------------------------------------//
// 자료삭제
//---------------------------------------------------------------------------------------------------------------------------//
function BoardData_Delete(uqcode,page) {

      rabbit=confirm("정보를 삭제 하시겠습니까?");
      if(!rabbit) {
              return;
      }
      
      pageLoadingBar();
      
      $('#EditID').val(uqcode);
      $('#WriteSW').val('1544');
      $('#CurrentPage').val(page);

      $('#postform').submit(); 

}

//---------------------------------------------------------------------------------------------------------------------------//
// 지사정보 저장, 수정
//---------------------------------------------------------------------------------------------------------------------------//
function Branch_Save(s, uqcode, page) {

      if (!$('#BranchSnNum').val()) {
            alert("관리번호를 입력 하세요!");
            $('#BranchSnNum').focus();
            return;
      }
      if (!$('#BranchName').val()) {
            alert("상호를 입력 하세요!");
            $('#BranchName').focus();
            return;
      }
      

      if (s==1) {
            rabbit=confirm("지사를 등록 하시겠습니까?");
            if(!rabbit) {
                   return;
            }
            $('#EditID').val('');
            $('#WriteSW').val('1588');
      } else {
            rabbit=confirm("지사정보를 수정 하시겠습니까?");
            if(!rabbit) {
                   return;
            }
            $('#EditID').val(uqcode);
            $('#WriteSW').val('1577');
      }
 
      pageLoadingBar();

      $('#CurrentPage').val(page);

      $('#postform').submit(); 

}
//---------------------------------------------------------------------------------------------------------------------------//
// 가맹점정보 저장, 수정
//---------------------------------------------------------------------------------------------------------------------------//
function Center_Save(s, uqcode, page) {

      if (!$('#CenterSnNum').val()) {
            alert("관리번호를 입력 하세요!");
            $('#CenterSnNum').focus();
            return;
      }
      if (!$('#CenterName').val()) {
            alert("가맹점 상호를 입력 하세요!");
            $('#CenterName').focus();
            return;
      }
      

      if (s==1) {
            rabbit=confirm("가맹점을 등록 하시겠습니까?");
            if(!rabbit) {
                   return;
            }
            $('#EditID').val('');
            $('#WriteSW').val('1588');
      } else {
            rabbit=confirm("가맹점 정보를 수정 하시겠습니까?");
            if(!rabbit) {
                   return;
            }
            $('#EditID').val(uqcode);
            $('#WriteSW').val('1577');
      }
 
      pageLoadingBar();

      $('#CurrentPage').val(page);

      $('#postform').submit(); 

}
//---------------------------------------------------------------------------------------------------------------------------//
// 가맹점정보 소속지사 선택
//---------------------------------------------------------------------------------------------------------------------------//
function Branch_Cho(ws) {

      pageLoadingBar();

      $('#CenterSnNum').val('');
      $('#WriteSW').val(ws);

      $('#postform').submit(); 

}
//---------------------------------------------------------------------------------------------------------------------------//
// 교재정보 저장, 수정
//---------------------------------------------------------------------------------------------------------------------------//
function Book_Save(s, uqcode, page) {

      if (!$('#BookSnNum').val()) {
            alert("교재 관리번호를 입력 하세요!");
            $('#BookSnNum').focus();
            return;
      }
      if (!$('#BookName').val()) {
            alert("교재명을 입력 하세요!");
            $('#BookName').focus();
            return;
      }

      if (s==1) {
            rabbit=confirm("교재를 등록 하시겠습니까?");
            if(!rabbit) {
                   return;
            }
            $('#EditID').val('');
            $('#WriteSW').val('1588');
      } else {
            rabbit=confirm("교재정보를 수정 하시겠습니까?");
            if(!rabbit) {
                   return;
            }
            $('#EditID').val(uqcode);
            $('#WriteSW').val('1577');
      }
 
      pageLoadingBar();

      $('#CurrentPage').val(page);

      $('#postform').submit(); 

}
//---------------------------------------------------------------------------------------------------------------------------//
// 교재등록에서 상품연결 카테고리 추가
//---------------------------------------------------------------------------------------------------------------------------//
function Add_BookGoods(k) {
        
        var c = k;
        k = k + 1;

        var row_obj = "<table id='add_booksub_table_"+k+"' class='admin_subtable' style='margin:0;'>";
        row_obj = row_obj + "<colgroup>";
        row_obj = row_obj + "<col width='19.3%'>";
        row_obj = row_obj + "<col width='80%'>";
        row_obj = row_obj + "</colgroup>";
        row_obj = row_obj + "<tr>";
        row_obj = row_obj + "<th style='background:#F5F5F5;'>상품세트연결-"+k+"</th>";
        row_obj = row_obj + "<td style='border:0px;'>";
        row_obj = row_obj + "<table class='admin_subtable2'>";
        row_obj = row_obj + "<colgroup>";
        row_obj = row_obj + "<col width='30%'>";
        row_obj = row_obj + "<col width='70%'>";
        row_obj = row_obj + "</colgroup>";
        row_obj = row_obj + "<tr>";
        row_obj = row_obj + "<td>관리번호 : <input type='text' id='add_BookSubSnNum_"+k+"' name='add_BookSubSnNum_"+k+"' onkeydown=\"if (event.keyCode == 13) NextField(this,'postform');\" style='width:200px;'></td>";
        row_obj = row_obj + "<td>상품명  : <input type='text' id='add_BookSubName_"+k+"'  name='add_BookSubName_"+k+"' onkeydown=\"if (event.keyCode == 13) NextField(this,'postform');\" style='width:70%;'></td>";
        row_obj = row_obj + "</tr>";
        row_obj = row_obj + "</table>";
        row_obj = row_obj + "</td>";
        row_obj = row_obj + "</tr>";   
        row_obj = row_obj + "<tr id='add_but_"+k+"'>";
        row_obj = row_obj + "<td colspan=2 style='padding:10px; border-top:1px solid #d6d3d3;'>";
        row_obj = row_obj + "<span><a href=\"javascript:Add_BookGoods("+k+")\" style='width:40px; height:40px; line-height:40px; background:#646464; color:#ffffff; border-radius:20px;'>＋</a></span>";
        row_obj = row_obj + "&nbsp; <span><a href=\"javascript:Del_BookGoods("+k+")\" style='width:40px; height:40px; line-height:40px; background:#646464; color:#ffffff; border-radius:20px;'>－</a></span>";
        row_obj = row_obj + "</td>";
        row_obj = row_obj + "</tr>";
        row_obj = row_obj + "</table>";
        
        $("#add_bookgoods").append(row_obj);
        
        if (c==0) {
                $("#add_but_0").hide();
        } else {
                $("#add_but_"+c).hide();
        }
        $("#addbookgoods_cnt").val(k);

}
//---------------------------------------------------------------------------------------------------------------------------//
// 교재등록에서 상품연결 카테고리 삭제
//---------------------------------------------------------------------------------------------------------------------------//
function Del_BookGoods(k) {

        $("#add_booksub_table_"+k).remove();
         
        var c = k-1;
        if (k > 0) {
                $("#add_but_"+c).show();
                $("#addbookgoods_cnt").val(c);
        }

}
//---------------------------------------------------------------------------------------------------------------------------//
// 컨텐츠등록에서 아웃라인 카테고리 추가
//---------------------------------------------------------------------------------------------------------------------------//
function Add_ContentsLine(k) {
        
        var c = k;
        k = k + 1;

        var row_obj = "<table id='add_contents_table_"+k+"' class='admin_subtable' style='margin:0;'>";
        row_obj = row_obj + "<colgroup>";
        row_obj = row_obj + "<col width='20%'>";
        row_obj = row_obj + "<col width='80%'>";
        row_obj = row_obj + "</colgroup>";
        row_obj = row_obj + "<tr>";
        row_obj = row_obj + "<th style='background:#F5F5F5;'>아웃라인-"+k+"</th>";
        row_obj = row_obj + "<td style='border:0px;'>";
        row_obj = row_obj + "<table class='admin_subtable2'>";
        row_obj = row_obj + "<colgroup>";
        row_obj = row_obj + "<col width='30%'>";
        row_obj = row_obj + "<col width='70%'>";
        row_obj = row_obj + "</colgroup>";
        row_obj = row_obj + "<tr>";
        row_obj = row_obj + "<td>관리번호 : <input type='text' id='add_ContentLineSnNum_"+k+"' name='add_ContentLineSnNum_"+k+"' onkeydown=\"if (event.keyCode == 13) NextField(this,'postform');\" style='width:200px;'></td>";
        row_obj = row_obj + "<td>라인명  : <input type='text' id='add_ContentLineName_"+k+"'  name='add_ContentLineName_"+k+"' onkeydown=\"if (event.keyCode == 13) NextField(this,'postform');\" style='width:70%;'></td>";
        row_obj = row_obj + "</tr>";
        row_obj = row_obj + "</table>";
        row_obj = row_obj + "</td>";
        row_obj = row_obj + "</tr>";   
        row_obj = row_obj + "<tr id='add_but_"+k+"'>";
        row_obj = row_obj + "<td colspan=2 style='padding:10px; border-top:1px solid #d6d3d3;'>";
        row_obj = row_obj + "<span><a href=\"javascript:Add_ContentsLine("+k+")\" style='width:40px; height:40px; line-height:40px; background:#646464; color:#ffffff; border-radius:20px;'>＋</a></span>";
        row_obj = row_obj + "&nbsp; <span><a href=\"javascript:Del_ContentsLine("+k+")\" style='width:40px; height:40px; line-height:40px; background:#646464; color:#ffffff; border-radius:20px;'>－</a></span>";
        row_obj = row_obj + "</td>";
        row_obj = row_obj + "</tr>";
        row_obj = row_obj + "</table>";
        
        $("#add_contents").append(row_obj);
        
        if (c==0) {
                $("#add_but_0").hide();
        } else {
                $("#add_but_"+c).hide();
        }
        $("#addcontents_cnt").val(k);

}
//---------------------------------------------------------------------------------------------------------------------------//
// 컨텐츠등록에서 아웃라인 카테고리 삭제
//---------------------------------------------------------------------------------------------------------------------------//
function Del_ContentsLine(k) {

        $("#add_contents_table_"+k).remove();
         
        var c = k-1;
        if (k > 0) {
                $("#add_but_"+c).show();
                $("#addcontents_cnt").val(c);
        }

}
//---------------------------------------------------------------------------------------------------------------------------//
// 컨텐츠등록에서 아웃라인별 코스 추가
//---------------------------------------------------------------------------------------------------------------------------//
function Add_ContentsLineCours(k,Path,Title) {

	  openurl = "admin_popup_contentcours_upload_form.php?Path="+Path;

      var width  = 700;
      var height = 300;
      var borderWidth = 3;

      // 위에서 선언한 값들을 실제 element에 넣는다.
      element_layer.innerHTML        = "";  
      element_layer.style.width      = width + 'px';
      element_layer.style.height     = height + 'px';
      element_layer.style.border     = borderWidth + 'px solid #000000';
      element_layer.style.background = '#E8F5FF';
      element_layer.style.overflow   = 'auto';
      // 실행되는 순간의 화면 너비와 높이 값을 가져와서 중앙에 뜰 수 있도록 위치를 계산한다.
      element_layer.style.left = (((window.innerWidth || document.documentElement.clientWidth) - width)/2 - borderWidth) + 'px';
      element_layer.style.top  = (((window.innerHeight || document.documentElement.clientHeight) - height)/2 - borderWidth) + 'px';

      $(".dark_bg").delay(100).fadeIn();
      element_layer.style.display = 'block';

	  var s = "<table width='100%' cellpadding=0 cellspacing=0 border=0>";
	  s += "<tr><td style='text-align:center; height:40px; background:#009EFF; color:#fff;' id='linecourstitledis'></td></tr>";
	  s += "<tr>";
	  s += "<td style='height:250px;text-align:center; background:#fff;'>";
      s += "<IFRAME NAME='Popup_AddFrame' SRC='"+openurl+"' FRAMEBORDER=0 FRAMESPACING=0 scrolling='no' width='100%' height='100%'></IFRAME>";
      s += "</td>";
      s += "</tr>";
	  s += "</table>";

	  element_layer.innerHTML = s;

	  var shtitle = Title + " 코스등록 <img src='//i1.daumcdn.net/localimg/localimages/07/postcode/320/close.png' id='btnCloseLayer' style='cursor:pointer;position:absolute; right:0px; top:0px; z-index:+9999' onclick='closeDaumPostcode()'>";
	  document.getElementById("linecourstitledis").innerHTML = shtitle;


}
//---------------------------------------------------------------------------------------------------------------------------//
// 결제요청 엑셀자료 추가
//---------------------------------------------------------------------------------------------------------------------------//
function ExcelData_BookUpload() {
      
      if ( !$('#book_excelldata').val() ) {
            alert("교재 엑셀파일을 선택 하세요!");
            $('#book_excelldata').focus();
            return;
      }
      rabbit=confirm("교재 엑셀파일을 업로드 하시겠습니까?");
      if(!rabbit) {
             return;
      }

      $('#EditID').val('');
      $('#WriteSW').val('1599');

      $('#postform').submit(); 

}
//---------------------------------------------------------------------------------------------------------------------------//
// 컨텐츠정보 저장, 수정
//---------------------------------------------------------------------------------------------------------------------------//
function Content_Save(s, uqcode, page) {

      if (!$('#ContentIntroImg').val()) {
            alert("컨텐츠 표지파일을 선택 하세요!");
            return;
      }
      if (s==1) {
			  if (!$('#ContentFileName').val()) {
					alert("컨텐츠 파일을 선택 하세요!");
					return;
			  }
      }
      if (!$('#ContentName').val()) {
            alert("컨텐츠 제목을 입력 하세요!");
            $('#ContentName').focus();
            return;
      }

      if (s==1) {
            rabbit=confirm("컨텐츠를 등록 하시겠습니까?");
            if(!rabbit) {
                   return;
            }
            $('#EditID').val('');
            $('#WriteSW').val('1588');
      } else {
            rabbit=confirm("컨텐츠정보를 수정 하시겠습니까?");
            if(!rabbit) {
                   return;
            }
            $('#EditID').val(uqcode);
            $('#WriteSW').val('1577');
      }
 
      pageLoadingBar();

      $('#CurrentPage').val(page);

      $('#postform').submit(); 

}

//---------------------------------------------------------------------------------------------------------------------------//
// 표지 이미지 업로드 화면
//---------------------------------------------------------------------------------------------------------------------------//
function PopupAddImage(ImgID,FormName,Path,Title){

	  openurl = "admin_popup_image_upload_form.php?ImgID="+ImgID+"&FormName="+FormName+"&Path="+Path;

      var width  = 500;
      var height = 200;
      var borderWidth = 3;

      // 위에서 선언한 값들을 실제 element에 넣는다.
      element_layer.innerHTML        = "";  
      element_layer.style.width      = width + 'px';
      element_layer.style.height     = height + 'px';
      element_layer.style.border     = borderWidth + 'px solid #000000';
      element_layer.style.background = '#E8F5FF';
      element_layer.style.overflow   = 'auto';
      // 실행되는 순간의 화면 너비와 높이 값을 가져와서 중앙에 뜰 수 있도록 위치를 계산한다.
      element_layer.style.left = (((window.innerWidth || document.documentElement.clientWidth) - width)/2 - borderWidth) + 'px';
      element_layer.style.top  = (((window.innerHeight || document.documentElement.clientHeight) - height)/2 - borderWidth) + 'px';

      $(".dark_bg").delay(100).fadeIn();
      element_layer.style.display = 'block';

	  var s = "<table width='100%' cellpadding=0 cellspacing=0 border=0>";
	  s += "<tr><td style='text-align:center; height:40px; background:#009EFF; color:#fff;' id='shtitledis'></td></tr>";
	  s += "<tr>";
	  s += "<td style='height:155px;text-align:center; background:#fff;'>";
      s += "<IFRAME NAME='Popup_AddFrame' SRC='"+openurl+"' FRAMEBORDER=0 FRAMESPACING=0 scrolling='no' width='100%' height='100%'></IFRAME>";
      s += "</td>";
      s += "</tr>";
	  s += "</table>";

	  element_layer.innerHTML = s;

	  var shtitle = Title + " <img src='//i1.daumcdn.net/localimg/localimages/07/postcode/320/close.png' id='btnCloseLayer' style='cursor:pointer;position:absolute; right:0px; top:0px; z-index:+9999' onclick='closeDaumPostcode()'>";
	  document.getElementById("shtitledis").innerHTML = shtitle;


}
