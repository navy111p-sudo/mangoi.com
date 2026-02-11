<!doctype html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>Math</title>

    <link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Waiting+for+the+Sunrise">
    <link type="text/css" rel="stylesheet" href="./math_libs/myscript/assets/css/web.css"/>
    <link type="text/css" rel="stylesheet" href="./math_libs/myscript/assets/css/math.css"/>
    <script type="text/javascript" src="./math_libs/myscript/assets/configuration.js"></script>
    <script type="text/javascript" src="./math_libs/myscript/components/webcomponentsjs/webcomponents-lite.min.js"></script>
    <link rel="import" href="./math_libs/myscript/components/myscript-math-web/myscript-math-web.html">

</head>
<body touch-action="none" >

<div id="content" hidden>
    <section class="ooo-section ooo-row">
        <div class="write-here math-write-here">Write here</div>
        <myscript-math-web id="math-input"
                           class="ooo-canvas"
                           strokecolor="#000000"
						   style="height:500px;">
        </myscript-math-web>
        <aside class="ooo-aside" touch-action="auto">
            <textarea id="MATHML" style="width:100%;height:200px;"></textarea>
        </aside>
    </section>
	<a href="javascript:GetMATHML();">가져오기</a>


</div>

<?
include_once('./includes/common_analytics.php');
?>

<script>

function GetMATHML(){
	alert(document.getElementById('MATHML').value)
}

function load() {
	document.body.querySelector('#content').removeAttribute('hidden');
}

function displayResult(resultType) {
	document.getElementById(resultType).classList.add('ooo-selected');
}

displayResult('MATHML');

var mathInput = document.getElementById('math-input');


mathInput.host = configuration.host;
mathInput.ssl = configuration.ssl;
mathInput.applicationkey = configuration.math.applicationKey;
mathInput.hmackey = configuration.math.hmacKey;

mathInput.addEventListener('pointerdown', function () {
	var writeHere = document.querySelector('.write-here');
	if (writeHere) {
		writeHere.remove();
	}
});
mathInput.addEventListener('myscript-math-web-result', function () {

	for (var key in mathInput.result) {
		if (!mathInput.result.hasOwnProperty(key)) {
			continue;
		}
		if (key=="MATHML"){
			document.getElementById(key).value = (mathInput.result[key] !== undefined) ? mathInput.result[key] : '';
		}
		
	}

});

window.addEventListener('WebComponentsReady', function () {
	load();
});



</script>

</body>
</html>
