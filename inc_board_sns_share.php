<div class="SnsArea">
    <a id="kakao-link-btn" href="javascript:;" class="IconSns1"><img src="images1/IconSns1.png" style="cursor:pointer;"></a>
    <a href="javascript:ShareKakaoStory()"><img src="images1/IconSns2.png"></a>
    <a href="javascript:ShareFacebook();"><img src="images1/IconSns3.png"></a>
    <a href="javascript:ShareTwitter()"><img src="images1/IconSns4.png"></a>
	<a href="javascript:ShareBand()"><img src="images1/IconSns5.png"></a>
</div>
<div id="kakaostory-follow-button" style="display:none;"></div>



<!--- 페이스북 --->
<script>

function ShareFacebook(){
    var fullUrl;
    var url = "http://gsnd6.com/board_read.php?BoardContentID=<?=$BoardContentID?>&BoardCode=<?=$BoardCode?>";
    var image = "";
    var title = "[경남6차산업지원센터] <?=$BoardContentSubject?>";
    var summary = "<?=strip_tags($BoardContent)?>";

    var pWidth = 640;
    var pHeight = 380;
    var pLeft = (screen.width - pWidth) / 2;
    var pTop = (screen.height - pHeight) / 2;

    fullUrl = "http://www.facebook.com/share.php?s=100&p[url]="+ url 
               +"&p[images][0]="+ image 
               +"&p[title]="+ title 
               +"&p[summary]="+ summary;
    fullUrl = fullUrl.split("#").join("%23");
    fullUrl = encodeURI(fullUrl);
    window.open(fullUrl,"","width="+ pWidth +",height="+ pHeight +",left="+ pLeft +",top="+ pTop + ",location=no,menubar=no,status=no,scrollbars=no,resizable=no,titlebar=no,toolbar=no");
}


</script>

<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/ko_KR/sdk.js#xfbml=1&version=v2.5";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<!--- 페이스북 --->

<!-- 카카오 스토리 공유하기 플러그인 -->
<script src="//developers.kakao.com/sdk/js/kakao.min.js"></script>
<script>
Kakao.init('2309b1f1394b617130d31270ccc0e7d5');

function ShareKakaoStory(){
	Kakao.Story.share({
		url: 'http://gsnd6.com/board_read.php?BoardContentID=<?=$BoardContentID?>&BoardCode=<?=$BoardCode?>',
		text: '[경남6차산업지원센터] <?=$BoardContentSubject?>'
	});
}

Kakao.Story.createFollowButton({
	container: '#kakaostory-follow-button',
	id: ''
});


Kakao.Link.createTalkLinkButton({
      container: '#kakao-link-btn',
      label: '[경남6차산업지원센터] <?=$BoardContentSubject?>',
      image: {
        src: 'http://gsnd6.com/images1/Logo1.png',
        width: '300',
        height: '200'
      },
      webButton: {
        text: '[경남6차산업지원센터] <?=$BoardContentSubject?>',
		url: 'http://gsnd6.com/board_read.php?BoardContentID=<?=$BoardContentID?>&BoardCode=<?=$BoardCode?>' 
      }
    });


</script>
<!-- 카카오 스토리 공유하기 플러그인 -->

<!-- 트위터 공유하기 플러그인 -->
<script>
function ShareTwitter(){
  var content = "[경남6차산업지원센터] <?=$BoardContentSubject?>";
  var link = "http://gsnd6.com/board_read.php?BoardContentID=<?=$BoardContentID?>&BoardCode=<?=$BoardCode?>";
  var popOption = "width=370, height=360, resizable=no, scrollbars=no, status=no;";
  var wp = window.open("http://twitter.com/share?url=" + encodeURIComponent(link) + "&text=" + encodeURIComponent(content), 'twitter', popOption); 
  if ( wp ) {
    wp.focus();
  }     
}
</script>
<!-- 트위터 공유하기 플러그인 -->


<!-- 밴드 공유하기 플러그인 -->
<script>
function ShareBand(){
     var shareUrl = "http://www.band.us/plugin/share?body="+encodeURIComponent("[경남6차산업지원센터] <?=$BoardContentSubject?>")+"&route="+encodeURIComponent("http://gsnd6.com/board_read.php?BoardContentID=<?=$BoardContentID?>&BoardCode=<?=$BoardCode?>");
     window.open(shareUrl, "share_band", "width=410, height=540, resizable=no"); 
 } 

 </script>
<!-- 트위터 밴드 플러그인 -->