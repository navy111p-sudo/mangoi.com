<script type="text/javascript" src="bower_components/jquery/dist/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery.alertable.js"></script>

<?
//include_once('./tms/includes/tms_engine.php');//상대경로
include $_SERVER["DOCUMENT_ROOT"]."/tms/includes/tms_engine.php";//절대경로
?>

<script>
//float
$(".allownumericwithdecimal").on("keypress keyup blur",function (event) {
	//this.value = this.value.replace(/[^0-9\.]/g,'');
	$(this).val($(this).val().replace(/[^0-9\.]/g,''));
	if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
		event.preventDefault();
	}
});

//int
$(".allownumericwithoutdecimal").on("keypress keyup blur",function (event) {    
	$(this).val($(this).val().replace(/[^\d].+/, ""));
	if ((event.which < 48 || event.which > 57)) {
		event.preventDefault();
	}
});


// numberOnly="true" 와 같이 사용
$(function(){
	$(document).on("keyup", "input:text[numberOnly]", function() {$(this).val( $(this).val().replace(/[^0-9]/gi,"") );});
	$(document).on("keyup", "input:text[datetimeOnly]", function() {$(this).val( $(this).val().replace(/[^0-9:\-]/gi,"") );});
});


// class="numeric-only" 와 같이 사용
$(document).on('keyup', '.numeric-only', function(event) {
   var v = this.value;
   if($.isNumeric(v) === false) {
        //chop off the last char entered
        this.value = this.value.slice(0,-1);
   }
});
</script>

<!-- ====    kendo -->
<link href="../kendo/styles/kendo.common.min.css" rel="stylesheet">
<link href="../kendo/styles/kendo.default.min.css" rel="stylesheet">
<script src="../kendo/js/kendo.web.min.js"></script>
<!-- ====    kendo   === -->


<!-- ====   Color Box -->
<?
$ColorBox = isset($ColorBox) ? $ColorBox : "";
if ($ColorBox==""){
	$ColorBox = "example2";
}
?>
<link rel="stylesheet" href="../js/colorbox/<?=$ColorBox?>/colorbox.css" />
<script src="../js/colorbox/jquery.colorbox.js"></script>
<script>
jQuery(document).ready(function(){
$(document).bind('cbox_open', function() {
    $('html').css({ overflow: 'hidden' });
}).bind('cbox_closed', function() {
    $('html').css({ overflow: '' });
});
});
</script>
<!-- ====   Color Box   === -->