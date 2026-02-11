function OpenWebview(LinkUrl){
	webview.Show(LinkUrl);
}

function OpenRemoteUrl(LinkUrl){
	var close_listen_loop;
	var NewWin=window.open( AppDomain+AppPath+"/"+LinkUrl, "_blank", "location=no");

	NewWin.addEventListener( "loadstop", function(){
		   close_listen_loop = window.setInterval(function(){
			   NewWin.executeScript({
					   code: "window.Exit"
				   },
				   function(values){
					   if(values[0]){
						 NewWin.close();
					   }
				   }
			   );
		   },2000);
	});

	NewWin.addEventListener( "exit", function(){
		window.clearInterval(close_listen_loop);
		NewWindowClosed();
	});
}


function OpenLocalUrl(LinkUrl){
	var close_listen_loop;
	var NewWin=window.open( LinkUrl, "_blank", "location=no");

	NewWin.addEventListener( "loadstop", function(){
		   close_listen_loop = window.setInterval(function(){
			   NewWin.executeScript({
					   code: "window.Exit"
				   },
				   function(values){
					   if(values[0]){
						 NewWin.close();
					   }
				   }
			   );
		   },2000);
	});

	NewWin.addEventListener( "exit", function(){
		window.clearInterval(close_listen_loop);
		NewWindowClosed();
	});
}