<!DOCTYPE HTML>
<html>
<head>
<?
include_once('./includes/common_meta_tag.php');
?>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="layout" content="main"/>
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<link rel="stylesheet" href="css/common.css">
<!------------------------- font-awesome ------------------------->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<!------------------------- font-awesome ------------------------->

<!------------------------- clipboard ------------------------->
<script src="../js/clipboard/clipboard.min.js"></script>
<!------------------------- clipboard ------------------------->
<script>
var filter = "win16|win32|win64|mac|macintel";
var DeviceType = 1;
if ( navigator.platform ) { 
	if ( filter.indexOf( navigator.platform.toLowerCase() ) < 0 ) { 
		DeviceType = 2;
	} else { 
		DeviceType = 1;
	} 
}
</script>
