<!--
//===========================================================================================================================//
//---------------------------------------------------- 검색 자동완성기능(Ajax) ----------------------------------------------//
//===========================================================================================================================//
var queryInit = false;

function clearInit() 
{
    if (queryInit) 
        return;
    document.SendPayForm.query.style.backgroundImage="";
    document.SendPayForm.query.value="";
    document.SendPayForm.keywordAd.value = "";
    SendPayForm.query.style.color="black";
    queryInit=true;
}

function checkSearch(gcode){
    if(document.SendPayForm.query.value == "") {
        alert("검색어를 입력하세요.");
        document.SendPayForm.query.focus();
        return false;
    }
	if (gcode) {
		document.SendPayForm.goodscode.value = gcode;
	}
}

//실제검색
function search_item(gcode){
    checkSearch(gcode);
    document.SendPayForm.submit();
}           
//------------------------------------------------------------------//
// 자동완성 시작함수....
//------------------------------------------------------------------//
function get_nav() {
     if (document.getElementById) {
        return 1;
     } else if (document.layers) return 2;
            else return 0;
}
function chk_rt(nav_type) {
        if (nav_type!=1) return 0;
        try { 
            var Td=top.document;
            var search_box=Td.SendPayForm.query;
        } catch (e) { 
            return 0;
        } 
        return 1;
} 
var nav_type=get_nav();
var dom_type=chk_rt(nav_type);

var Td = document;
var search_form=document.SendPayForm;
 
var search_box    = search_form.query;
var search_option = search_form.qdomain;
var m_on=0,m_now=0,s_now=0,shl=0,a_now=0,a_on=0,arr_on=0,frm_on=0;
var cn_use        = "use_ac";
var wi_int        = 500;
var B="block",I="inline",N="none",UD="undefined";
var bak="",old="";
var qs_ac_list="",qs_ac_id="",qs_q="",qs_m=0,qs_ac_len=0;
bak=old=search_box.value;
var acuse=1;    //자동완성사용여부. 1=>사용, 0=>미사용
var cc= new Object();
var ac_layer_visibility=0;
 
var goGoodsNo = "";
var keystatus = 1;
//------------------------------------------------------------------//
//기능끄기 버튼을 눌렀을때
//------------------------------------------------------------------//
function dmp_acoff() {
    if (search_box.value == "") {
           popup_ac(0);
    }else{
           ac_hide();
    }
    acuse = 0;
    //자동완성기능 하루동안사용안함
    setCookie("AC_USE","N",1000*60*60*24);
}

//기능켜기 버튼을 눌렀을때
function dmp_acon() {
    acuse = 1;
    setCookie("AC_USE","Y",-1000);
    popup_ac(1);
    
    if (search_box.value != "") 
        wd();
        
    setTimeout("wi()", wi_int);
    search_box.focus();
    //폼들을 감춘다.
    hide_behind_forms();
}
//------------------------------------------------------------------//
//인풋박스의 세모 버튼을 눌렀을때 자동완성창을 보여준다.
//------------------------------------------------------------------//
function show_ac() {   
		if (acuse == 0) {
			 if(off_ac_body.style.display == "block") popup_ac(0);  
			 else popup_ac(2);

		}else{
			 if (search_box.value == "") {
				  if(noquery_ac_body.style.display == "block") popup_ac(0);   
				  else popup_ac(3);
			 } else {
				  req_ipc();
			 }
		}
}
function wd() { 
    
        search_box.onclick = req_ipc;
        Td.body.onclick = dis_p;
}
    
var dnc=0;
function req_ipc() { 
        dnc=1;
        frm_on=0;
        req_ac2(1);
}

function dis_p() {
         if (dnc) { 
             dnc=0;
            return;
        } 
        
        if (arr_on) {
            return;
        }
        if (frm_on) { 
            return;
        } 
        alw=0;
        ac_hide();
        
} 

function req_ac2(me) { 
        
         if (search_box.value == "" || acuse==0 ) return;
        
         if (a_on && dnc) { 
            ac_hide();
            return;
        } 
        var o = get_cc(me);
         if (o && o[1][0] != "" ) ac_show(o[0], o[1], o[2], me);
         else reqAC(me);
} 

var _req = null;
function get_req() {
         if(_req && _req.readyState!=0) { 
             _req.abort();
         } 
         try {
             _req = new ActiveXObject("Msxml2.XMLHTTP");
         } catch (e) { 
             try { 
                 _req = new ActiveXObject("Microsoft.XMLHTTP");
             } catch (e) { 
                 _req = false;
             } 
        }
        if (!_req && typeof XMLHttpRequest!=UD) _req = new XMLHttpRequest();
        return _req;
} 
//--------------------------------------------------------------------------//
// 데이터 받기....
//--------------------------------------------------------------------------//
function showAC() {

         if (_req.readyState==4 && _req.status==200 ) {
			   
			   var results    = _req.responseText.evalJSON();

			   var qs_q       = results.header.qs_q;
			   var qs_ac_list = results.data.qs_ac_list;
			   var qs_m       = results.header.qs_m;
			   var qs_ac_id   = results.header.qs_ac_id;
			   
			   set_cc(qs_q, qs_ac_list, qs_ac_id, qs_m);
               ac_show(qs_q, qs_ac_list, qs_ac_id, qs_m);

         }

} 
//--------------------------------------------------------------------------//
// 검색단어 및 조건 보내기....
//--------------------------------------------------------------------------//
function reqAC(me) { 
         var sv      = ''; 
		 var t       =0;
         var ke      = trim_space(search_box.value, me);

         ke = ke.replace(/ /g, "%20");
         if (ke == "") { 
                ac_hide();
                return;
         }

         sv = "book_autosearch.php?shquery=" + ke;
         _req = get_req();
         if (_req) { 
                _req.open("GET", sv, true);
                _req.onreadystatechange = showAC;
         } 
         try {
                _req.send();
         } catch (e) { 
                return 0;
         } 
} 
//--------------------------------------------------------------------------//
// 검색리스트 보여주기....
//--------------------------------------------------------------------------//
function ac_show(aq, al, ai, am) {
        
         if (aq && aq!="" && aq!=trim_space(search_box.value, am)) return;
         qs_q       = aq;
         qs_m       = am;
         qs_ac_list = al;
         qs_ac_id   = ai;
         qs_ac_len  = qs_ac_list.length;
         var h = (qs_ac_len > 9) ? 10 : qs_ac_len;
         h = h * 20;
         
		 // 검색리스트 보여주는 함수
         print_ac();

		 if (qs_ac_len > 0 && h < 120) {
			   h = 120;
		 }
         scrol.style.height = h;

         if (qs_ac_len) { 
               h+=38;
         }
         a_on=1;
         ac_body.width = 430;
         ac_body.height = h;

         popup_ac(1);
         if (a_on) { 
             set_acpos(0,0);
             scrol.scrollTop=0;
             search_box.onkeydown = ackhl;
         } 
         
         //밑의 폼들이 자동완성 레이어를 가리므로 잠시 폼들을 안보이게 한다.
         hide_behind_forms();
} 

function set_acpos(v, bookImgsrc) { 
        a_now = v;
        setTimeout('set_ahl();', 10);
        
         var g;
         
         if ( v > 0 && bookImgsrc) {
               document.all.bookImg.style.display =  "block";
               document.all.bookImg.src = bookImgsrc;
			   document.all.bookImgAlt.innerHTML = "<a href='javascript:goods_display()' onfocus='this.blur()'><font  style='font-size:8pt; color:#F76D1A;'>[구매정보]</a>";
               g = eval('document.all.gNo' + a_now);
               goGoodsNo = g.value;    
        }

}

function set_ahl() { 
         if (!a_on) return;
         
         var o1, o2;
         for (i=0;  i<qs_ac_len; i++) { 
            o1 = eval('ac' + (i+1));
            if ((i+1) == a_now) {
                o1.style.backgroundColor = '#F5FFEC';
                g = eval('document.all.gNo' + a_now);
                document.SendPayForm.goodscode.value = g.value;
            } else {
                o1.style.backgroundColor = '';
            }
        }
} 
//------------------------------------------------------------------//
// 선택사진을 클릭하여 상세 구매정보 알아보기
//------------------------------------------------------------------//
function goods_display() {
	    // 자동완성창 보이지 않게 하기...
		ac_hide();
        // 팝업창 띄우기
		//var PopUp_Obj = eval(document.all['PopUp_JavaShow']);
		var PopUp_Obj = document.getElementById("PopUp_JavaShow");
		PopUp_Obj.style.display = '';
		//var popupimg_Obj = eval(document.all['popup_title']);
		var popupimg_Obj = document.getElementById("popup_title");
		popupimg_Obj.src = 'images/popup-blank-title.png';
		var moveX        = parseInt((parent.document.body.clientWidth-500)/2);
		var moveY        = 137;
        PopUp_Obj.style.left = moveX;
        PopUp_Obj.style.top  = moveY;
        document.popupform.popup_common_sw.value = 10;
        document.popupform.popup_sh_id.value = document.SendPayForm.goodscode.value;
        document.popupform.submit();
}
//------------------------------------------------------------------//
//키를 누를때 이벤트 검사하는 함수
//------------------------------------------------------------------//
var max_row=4;
function ackhl() { 
         var e=top.window.event;
         var o1, o2;
         var img;
         if (e.keyCode==39) { 
             req_ac2(1);
         }
         if (e.keyCode==13) {
            
         }
         if (e.keyCode==40 || (e.keyCode==9 && !e.shiftKey)) { 
             if (m_on) return;
             if (!a_on) {
                 req_ac2(1);
                 return;
            }
            if (a_now < qs_ac_len) { 
                if (a_now == 0) bak = search_box.value;
                a_now++;
                if (a_now > max_row) scrol.scrollTop = parseInt((a_now-1)/max_row)*max_row*19;
                 o1 = eval('ac' + a_now);
                 o2 = eval('acq' + a_now);
                 img = eval('img' + a_now);
                 old = search_box.value = o2.outerText;
                 set_acpos(a_now, img.outerText);
                 search_box.focus();
                 set_ahl();
                 e.returnValue = false;
             } 
         }
         if (a_on && (e.keyCode==38 || (e.keyCode==9 && e.shiftKey))) {
             if (!a_on) return;
             if (a_now <= 1) { 
                 ac_hide();
                 old = search_box.value = bak;
             } 
             else {
                 a_now--;
                 if ((qs_ac_len-a_now)+1 > max_row) scrol.scrollTop = (qs_ac_len-(parseInt((qs_ac_len-a_now)/max_row)+1)*4)*19;
                 o1 = eval('ac'+ a_now);
                 o2 = eval('acq' + a_now);
                 old = search_box.value = o2.outerText;
                 img = eval('img' + a_now);
                 set_acpos(a_now, img.outerText);
                  search_box.focus();
                 set_ahl();
                 e.returnValue = false;
             }
         }
} 

function print_ac() { 
         scrol.innerHTML = get_aclist();
         popup_ac(1); //자동완성창 보여줌.
         setTimeout('set_ahl();', 10);
} 
//--------------------------------------------------------------------------//
// 검색리스트 표시방법....
//--------------------------------------------------------------------------//
function get_aclist() { 
         var d="", ds="",l=0, s="", cnt=0, pos=0, qlen=0, gunit="", img="", photo_url="";
		 s += "<table width=100% cellpadding=0 cellspacing=0 border=0>";
		 s += "<tr><td width=70% valign=top>";
		 s += "<table width=100% cellpadding=0 cellspacing=0 border=0>";
		 for (i=0; i<qs_ac_len; i++) { 
				 var query = qs_ac_list[i].split("^");
				 age_lmt_yn = query[0];    // 상품분류(처방/일반/의약외품/건강식품/생활건강,미용/건강기기
				 goodsNo    = query[1];    // 상품코드
				 ds = d     = query[2];    // 상품명
				 gunit      = query[3];    // 단위
				 photo_url  = query[4];    // 사진url
				 lmt_age    = query[5];    // 보험코드

				 if ( !photo_url ) {
						 img = "http://www.damoapharm.com/images/noimage-l.gif";
				 } else {
						 img = "http://www.damoapharm.com/upload/" + photo_url; // 상품사진URL 
				 }
				 l = js_strlen(d);
				 
				 if (l > 32) ds = js_substring(d, 0, 32) + "...";
				 
				 pos = d.indexOf(search_box.value);
				
				 if (pos >= 0) {
						if (pos == 0) {
							ds = js_highlight (ds, search_box.value,  0);
						}
						else if (pos == d.length - 1)
						{
							ds = js_highlight (ds, search_box.value,  -1);
						}else 
						{
							ds = js_highlight (ds, search_box.value,  pos);
						}
						ds = "[" + age_lmt_yn + "]" + ds;
				 }
				 s += "<input type='hidden' name='gNo"+(i+1)+"' value='"+goodsNo+"'>";
				 s += "<a href=\"javascript:search_item('"+goodsNo+"');\">";
				 s += "<tr id='ac" + (i+1) + "' onmouseover=\"set_acpos('" + (i+1) + "', '"+img + "');\" onmouseout=\"set_acpos(0,0); \" onclick=\"set_acinput('" + (i+1) + "')\" style=\"this.style.backgroundColor=''\" style='CURSOR:hand'>";
				 s += "<td height=20 align=left>" + ds;
				 //if (gunit) {
				 //	    s += " (" + gunit + ")";   // 단위 나타내기
				 //}
				 s += "</td>";
				 s += "<td height=20 align=right></td>";
				 s += "</tr></a>";
				 s += "<span id='acq" + (i+1) + "' style='display:none'>" + d + "</span>";
				 s += "<span id='img" + (i+1) + "' style='display:none'>" + img + "</span>";
		 } 
		 s += "</table>";
		 s += "</td><td width=30% style='padding:5 0 0 0'>";
		 s += "<table width=100% cellpadding=0 cellspacing=0 border=0>";
		 s += "<tr><td align=center><img border=0 id='bookImg' width=88 height=88 style='display:none; CURSOR:hand' onclick=\"goods_display()\"></td></tr>";
		 s += "<tr><td  id='bookImgAlt' align=center></td></tr>";
		 s += "</table></td></tr></table>"
         if (i == 0) { 
			 s = "<table width=100% cellpadding=0 cellspacing=0 border=0>";
			 s += "<tr><td height=20 style='padding : 5 5 5 2; color:#F76D1A;'>해당 검색어와 관련된 상품을 찾을 수 없습니다!</td></tr>";
			 s += "</table>";
		 }

         return s;
} 

function js_makehigh_pre(s, t) { 
         var d="";
         var s1=s.replace(/ /g, "");
         var t1=t.replace(/ /g, "");
         t1=t1.toLowerCase();
         if (t1==s1.substring(0, t1.length)) {
            d="<font color=#F76D1A><b>";
            for (var i=0,j=0; j<t1.length; i++) {
                if (s.substring(i, i+1)!=" ") j++;
                d+=s.substring(i, i+1)
             }
             d+="</b></font>"+s.substring(i, s.length)
         } 
         return d;
} 
     
function js_makehigh_suf(s, t) { 
         var d="";
         var s1=s.replace(/ /g, "");
         var t1=t.replace(/ /g, "");
         t1=t1.toLowerCase();
         if (t1==s1.substring(s1.length-t1.length))  { 
                for (var i=0,j=0; j<s1.length-t1.length;  i++) { 
                    if (s.substring(i, i+1)!=" ") j++;
                    d+=s.substring(i, i+1);
                } 
                d+="<font color=#F76D1A><b>";
                for (var k=i,l=0;  l<t1.length;  k++) {
                    if (s.substring(k, k+1)!=" ") l++;
                     d+=s.substring(k, k+1);
                }
                d+="</b></font>";
         }
         return d;
} 

function js_makehigh_mid(s, t, pos) { 
         var d="";
         var s1=s.replace(/ /g, "");
         var t1=t.replace(/ /g, "");
         t1=t1.toLowerCase();

        d=s.substring(0, pos);
        d+="<font color=#F76D1A><b>"; 
        for (var i=pos,j=0; j < t1.length; i++) 
        {
            if (s.substring(i, i+1)!=" ") j++;
            d+=s.substring(i, i+1);
        }
        d+="</b></font>"+s.substring(i, s.length);
         return d;
} 


function js_highlight(s, d, is_suf) {
         var ret="";
         if (is_suf == 0) { 
            ret=js_makehigh_pre(s, d);
         } 
         else if (is_suf == -1) {
            ret=js_makehigh_suf(s, d);
         }
         else {
            ret=js_makehigh_mid(s, d, is_suf);
         }

         if (ret=="") return s;
         else return ret;
} 

function set_acinput(v) { 
         if (!a_on) return;
         var o = eval('acq' + a_now);
         old = search_box.value = o.outerText;
         search_box.focus();
         ac_hide();
} 

function js_strlen(s) { 
         var i,l=0;
         for (i=0; i<s.length; i++) 
             if (s.charCodeAt(i) > 127) l+=2;
             else l++;
         return l;
}

function js_substring(s, start, len) { 
         var i,l=0;d="";
         for (i=start; i<s.length && l<len; i++) {
             if (s.charCodeAt(i) > 127) l+=2;
             else l++;
             d+=s.substr(i, 1);
         } 
         return d;
} 

function trim_space(ke, me) { 
        if (me!=2) {
            ke = ke.replace(/^ +/g, "");
            ke = ke.replace(/ +$/g, " ");
        } else { 
            ke = ke.replace(/^ +/g, " ");
            ke = ke.replace(/ +$/g, "");
        } 
        ke = ke.replace(/ +/g, " ");
        return ke;
} 

function get_cc(me) { 
         var ke=trim_space(search_box.value, me) + me;
         return typeof(cc[ke])==UD ? null : cc[ke];
} 

function set_cc(aq, al, ai, me) { 
         cc[aq+me] = new Array(aq, al, ai);
} 

function ac_hide() {   
        if (ac_body.style.display == N) return;
        popup_ac(0); //hide all
        a_on = a_now = 0;
        //폼들을 다시보이게 한다.
        show_behind_forms();
} 
    
function wi() {
     
         if (acuse==0) return;
         if (m_on) { 
             setTimeout("wi()", wi_int);
            return;
        } 
        var now = search_box.value;
         if (now == "" && now != old) ac_hide();
        if (now != "" && now != old && keystatus!=1) { 
            var o=null, me=1;
            o = get_cc(me);
            if (o && o[1][0] != "") ac_show(o[0], o[1], o[2], me);
            else reqAC(me);
        } 
        old = now;
        setTimeout("wi()", wi_int);
        
} 

function set_mouseon(f) { 
         if (f==1) arr_on = 1;
         else if (f==2) frm_on = 1;
}

function set_mouseoff(f) {
         if (f==1) arr_on = 0;
         else if (f==2) frm_on = 0;
} 

function req_pf() {
         frm_on=1;
         req_ac2(1);
         search_box.focus();
         cursor_end();
}

function req_sf() {
         frm_on=1;
         req_ac2(2);
         search_box.focus();
         cursor_end();
} 

function cursor_end() { 
         if (nav_type==1 && dom_type==1) { 
             var rng=search_box.createTextRange();
             if (rng!=null) { 
                 rng.move("textedit");
                 rng.select();
             }
         }
}
     
//type=0 : 모두 감춘준다.
//type=1 : 검색어가 있을때 자동완성창 보이기
//type=2 : 기능이 꺼져있을때 자동완성창 보이기
//type=3 : '검색어를 입력해달라'는 자동완성창 보이기
function popup_ac(type){
        if(type==0){
            ac_body.style.display = "none";
            off_ac_body.style.display = "none";
            noquery_ac_body.style.display = "none";
            //검색창내 세모 이미지변경
            switch_image(0);
        }else if(type==1){
            ac_body.style.display = "block";
            off_ac_body.style.display = "none";
            noquery_ac_body.style.display = "none";
            switch_image(1);
        }else if(type==2){
            ac_body.style.display = "none";
            off_ac_body.style.display = "block";
            noquery_ac_body.style.display = "none";
            switch_image(1);
        }else if(type==3){
            ac_body.style.display = "none";
            off_ac_body.style.display = "none";
            noquery_ac_body.style.display = "block";
            switch_image(1);
        }
}
     
//자동완성 레이어뒤의 폼들을 감춘다.
function hide_behind_forms(){
}
     
//자동완성 레이어뒤의 폼들을 다시 보여준다.
function show_behind_forms(){
}
     
//검색어입력창의 자동완성 화살표를 위, 아래로 변경한다.
//type 0 : 창이 닫혔을때 화살표 아래로.
//type 1 : 창이 펼처졌을때 위로
function switch_image(type){
        var imgIntro0 = document.getElementById("imgIntro0");
        var former_part = imgIntro0.src.substring(0, imgIntro0.src.length-6);
        if(type==0){
            imgIntro0.src = former_part + "Dn.gif";
        }else if(type==1){
            imgIntro0.src = former_part + "Up.gif";
        }
}
    
function debug(msg){
        window.status=msg;
}

 

function setTextBox(flag) {
        var textbox = search_box; 
        var _event; 
        
        switch ( getNavigatorType() ) {
        
            case 1 : // IE
                _event = window.event;
                nodeName = _event.srcElement.nodeName;
                break;
            case 2 : // Netscape
                _event = event;
                nodeName = _event.target.nodeName;
                break;
            default :
                nodeName = "None"; 
                break;
        }
        key = _event.keyCode;
        
        if ( keystatus == 1 && flag && key != 13) {
            //textbox.value = "";
            keystatus = 2;
        }
}

function getNavigatorType() {
        if ( navigator.appName == "Microsoft Internet Explorer" )
            return 1;  
        else if ( navigator.appName == "Netscape" )
            return 2;   
        else 
            return 0;
}
    
function setCookie(name,value,expire) {
        var today=new Date();
        today.setDate(today.getDate()+parseInt(expire));
        document.cookie=name+"="+escape(value)+"; path=/; expires="+today.toGMTString()+";";
}
function getCookie(name) {
        var cookieName=name+"=";
        var x=0;
        while(x<=document.cookie.length) {
            var y=(x+cookieName.length);
            if(document.cookie.substring(x,y)==cookieName) {
                if((endOfCookie=document.cookie.indexOf(";",y))==-1) endOfCookie=document.cookie.length;
                return unescape(document.cookie.substring(y,endOfCookie));
            }
            x=document.cookie.indexOf(" ",x)+1;
            if(x == 0) break;
        }
        return "";
}
//-->
