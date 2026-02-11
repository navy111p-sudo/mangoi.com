<!doctype html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>Math</title>

    <link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Waiting+for+the+Sunrise">
    <link type="text/css" rel="stylesheet" href="./math_libs/myscript/assets/css/web.css"/>
    <link type="text/css" rel="stylesheet" href="./math_libs/myscript/assets/css/math.css"/>
    <link type="text/css" rel="stylesheet" href="./math_libs/myscript/assets/css/help.css"/>
    <link type="text/css" rel="stylesheet" href="./math_libs/myscript/assets/css/devportaltoast.css"/>
    <link type="text/css" rel="stylesheet" href="./math_libs/myscript/third_party/google-code-prettify/prettify.css"/>

    <!--Configuration file used to store identifiers and some parameters-->
    <script type="text/javascript" src="./math_libs/myscript/assets/configuration.js"></script>
    <!--A WebComponent polyfill is needed to use the web component, has it's not yet well implemented by all browsers-->
    <script type="text/javascript" src="./math_libs/myscript/components/webcomponentsjs/webcomponents-lite.min.js"></script>

    <link rel="import" href="./math_libs/myscript/components/iron-icons/iron-icons.html">
    <link rel="import" href="./math_libs/myscript/components/iron-image/iron-image.html">
    <link rel="import" href="./math_libs/myscript/components/paper-fab/paper-fab.html">
    <link rel="import" href="./math_libs/myscript/components/paper-dialog/paper-dialog.html">
    <link rel="import" href="./math_libs/myscript/components/paper-icon-button/paper-icon-button.html">
    <link rel="import" href="./math_libs/myscript/components/paper-toast/paper-toast.html">

    <!-- Importing the myscript-math-web webcomponent. -->
    <link rel="import" href="./math_libs/myscript/components/myscript-math-web/myscript-math-web.html">

    <script type="text/javascript" src="./math_libs/myscript/third_party/google-code-prettify/prettify.js"></script>

</head>
<body touch-action="none" onload="prettyPrint();" >
<div id="content" hidden>
    <paper-toast  class="helpHidden" id="helpToast" duration="0" auto-fit-on-attach backdrop-element with-backdrop vertical-align="top">
        <h2>Math</h2>
        <a class="close" href="#" onclick="closeHelpToaster();">×</a>
        <div class="helpcontent">
            Write your calculations, equations, chemical formulas, and get instant result.
        </div>
        <iron-image src="../assets/img/demo/math_loop.gif" style="max-width: 600px" preload fade alt="Math capture"></iron-image>
    </paper-toast>
    <header class="ooo-header">
        <a id="index-header-link" class="ooo-link-back" href="../index.html" onclick="loading()"></a>
        <h1 class="ooo-math">Math</h1>
        <a id="info-header-link" class="ooo-link-info" href="#description" onclick="showHelpToaster()"></a>
    </header>
    <script>
        function showHelpToaster(){
            var helpToast = document.querySelector('#helpToast');
            helpToast.classList.remove('helpHidden');
            helpToast.open();
        }
        function closeHelpToaster(){
            var helpToast = document.querySelector('#helpToast');
            helpToast.classList.add('helpHidden');
            helpToast.toggle();
        }
    </script>
    <section class="ooo-section ooo-row">
        <div class="write-here math-write-here">Write here</div>
        <myscript-math-web id="math-input"
                           class="ooo-canvas"
                           strokecolor="#000000">
        </myscript-math-web>
        <aside class="ooo-aside" touch-action="auto">
            <ul class="ooo-result-nav">
                <li id="katex-result-link" class="ooo-result-link"><a href="#" onclick="displayResult('KATEX')">Math</a>
                </li>
                <li id="latex-result-link" class="ooo-result-link"><a href="#"
                                                                      onclick="displayResult('LATEX')">LaTeX</a></li>
                <li id="mathml-result-link" class="ooo-result-link"><a href="#"
                                                                       onclick="displayResult('MATHML')">MathML</a></li>
                <li id="symboltree-result-link" class="ooo-result-link"><a href="#"
                                                                           onclick="displayResult('SYMBOLTREE')">SymbolTree</a>
                </li>
            </ul>
            <div id="results-container" class="ooo-results">
                <div id="KATEX" class="ooo-result"></div>
                <div id="LATEX" class="ooo-result ooo-latex"></div>
                <pre id="MATHML" class="ooo-result prettyprint lang-xml"></pre>
                <pre id="SYMBOLTREE" class="ooo-result prettyprint lang-js"></pre>
            </div>
            <div class="ooo-action-bar">
                <paper-fab mini id="copyToClipboard" title="copy" icon="icons:content-copy" on-tap="copy"
                           disabled="true"></paper-fab>
            </div>
            <div id="graph" class="ooo-graph">
            </div>
        </aside>
    </section>
    <footer class="ooo-footer">
        <a id="myscript-footer-link" href="http://www.myscript.com" target="_blank" title="MyScript">
            Copyright © MyScript® All Rights Reserved
        </a>
        <a id="legal-footer-link" href="http://www.myscript.com/legal-notice/" target="_blank" title="Legal notice">
            Legal notice
        </a>
    </footer>
    <!-- devportal totaster section -->
    <style is="custom-style">
        #devportalToaster {
            --paper-toast-background-color: #1A9FFF;
            --paper-toast-color: #ffffff;
        }
    </style>
    <paper-toast id="devportalToaster" duration="0">
        <div class="block">
            <p class="devportal-link inner">Developer? Build your own integration using <a
                href="https://developer.myscript.com/get-started/web" target="_blank">MyScript APIs</a></p>
            <paper-button class="paper-close inner" onclick="document.querySelector('#devportalToaster').toggle()">×</paper-button>
        </div>
    </paper-toast>
    <script>window.addEventListener('WebComponentsReady', function () {
        setTimeout(function () {
            var deportalToaster = document.querySelector('#devportalToaster');
            deportalToaster.open();
        }, 200);
    })</script>
</div>
<div class="overlay">
    <div class="loader"></div>
</div>

<?
include_once('./includes/common_analytics.php');
?>

<script>
    var calculator;
    var desmos = document.createElement('script');
    desmos.src = './math_libs/myscript/third_party/desmos/calculator.js';
    desmos.onload = function () {
        calculator = Desmos.Calculator(document.getElementById('graph'), {
            keypad: false,
            graphpaper: true,
            expressions: false,
            settingsMenu: false,
            zoomButtons: false,
            expressionsTopbar: false,
            solutions: false,
            border: false,
            lockViewport: false,
            expressionsCollapsed: true
        });
    };

    function load() {
        document.body.querySelector('#content').removeAttribute('hidden');
        document.body.querySelector('.overlay').style.display = "none";
        loadDesmos();
    }

    function loading() {
        document.body.querySelector('#content').setAttribute('hidden', true);
        document.body.querySelector('.overlay').style.display = "flex";
    }

    function loadDesmos() {
        var width = window.innerWidth || document.body.clientWidth;
        var height = window.innerHeight || document.body.clientHeight;

        // Load only for desktop
        if (calculator === undefined && width > 700 && height > 500) {
            document.body.appendChild(desmos);
        }
    }
    //Setting dynamically the attributes of our webcomponents
    function copyToClipboard(elem) {
        // create hidden text element, if it doesn't already exist
        var targetId = "_hiddenCopyText_";
        var isInput = elem.tagName === "INPUT" || elem.tagName === "TEXTAREA";
        var origSelectionStart, origSelectionEnd;
        if (isInput) {
            // can just use the original source element for the selection and copy
            target = elem;
            origSelectionStart = elem.selectionStart;
            origSelectionEnd = elem.selectionEnd;
        } else {
            // must use a temporary form element for the selection and copy
            target = document.getElementById(targetId);
            if (!target) {
                var target = document.createElement("textarea");
                target.style.position = "absolute";
                target.style.left = "-9999px";
                target.style.top = "0";
                target.id = targetId;
                document.body.appendChild(target);
            }
            target.textContent = elem.textContent;
        }
        // select the content
        var currentFocus = document.activeElement;
        target.focus();
        target.setSelectionRange(0, target.value.length);

        // copy the selection
        var succeed;
        try {
            succeed = document.execCommand("copy");
        } catch (e) {
            succeed = false;
        }
        // restore original focus
        if (currentFocus && typeof currentFocus.focus === "function") {
            currentFocus.focus();
        }

        if (isInput) {
            // restore prior selection
            elem.setSelectionRange(origSelectionStart, origSelectionEnd);
        } else {
            // clear temporary content
            target.textContent = "";
        }
        return succeed;
    }

    function extractExpressions(latex) {

        if (!latex) return [""];

        if (latex.indexOf("\\begin{align*}") == 0) {

            var steps = latex.replace("\\begin{align*} & ", "")
                .replace("\\end{align*} ", "")
                .replace(new RegExp("& ", "g"), "");
            return steps.split("\\\\ ");

        } else if (latex.indexOf("\\begin{cases}") == 0) {

            var steps = latex.replace("\\begin{cases}", "")
                .replace("\\end{cases} ", "")
                .replace(new RegExp("& ", "g"), "");
            return steps.split("\\\\ ");
        }
        return [latex];
    }

    function displayResult(resultType) {
        var i;
        var x = document.getElementsByClassName('ooo-result');
        for (i = 0; i < x.length; i++) {
            x[i].classList.remove('ooo-selected');
        }
        var y = document.getElementsByClassName('ooo-result-link');
        for (i = 0; i < y.length; i++) {
            y[i].classList.remove('ooo-selected');
        }
        document.getElementById(resultType).classList.add('ooo-selected');
        document.getElementById(resultType.toLowerCase() + '-result-link').classList.add('ooo-selected');

        if (document.getElementById(resultType).innerText != '') {
            document.getElementById('copyToClipboard').removeAttribute('disabled');
        } else {
            document.getElementById('copyToClipboard').setAttribute('disabled', 'true');
        }

        // Hack to display proper mobile view
        if (resultType === 'KATEX') {
            document.querySelector('#math-input').classList.add('result-visible');
        } else {
            document.querySelector('#math-input').classList.remove('result-visible');
        }
    }

    displayResult('LATEX');

    var mathInput = document.getElementById('math-input');
    mathInput.host = configuration.host;
    mathInput.ssl = configuration.ssl;
    mathInput.applicationkey = configuration.math.applicationKey;
    mathInput.hmackey = configuration.math.hmacKey;

    document.getElementById("copyToClipboard").addEventListener("click", function () {
        var results = document.getElementsByClassName("ooo-selected");
        for (var i = 0; i < results.length; i++) {
            copyToClipboard(results[i]);
        }
    });

    // Small piece of code to hide the write here message
    mathInput.addEventListener('pointerdown', function () {
        var writeHere = document.querySelector('.write-here');
        if (writeHere) {
            writeHere.remove();
        }
    });
    mathInput.addEventListener('myscript-math-web-result', function () {

        document.getElementById('copyToClipboard').setAttribute('disabled', 'true');
        var elements = document.getElementsByClassName('ooo-result');
        for (var i in elements) {
            elements[i].innerText = '';
        }

        for (var key in mathInput.result) {
            if (!mathInput.result.hasOwnProperty(key)) {
                continue;
            }
            document.getElementById(key).innerText = (mathInput.result[key] !== undefined) ? mathInput.result[key] : '';
        }

        var selectedResults = document.getElementsByClassName('ooo-selected');
        for (var j in selectedResults) {
            var id = selectedResults[j].id;
            if (id && !id.endsWith('-result-link')) {
                displayResult(id)
            }
        }

        var colors = ['#0060A0', '#09E05C', '#E06A09'];
        if (calculator) {
            calculator.removeExpressions([{ id: 1 }, { id: 2 }, { id: 3 }]);
            var expressions = extractExpressions(mathInput.result['LATEX']);
            for (var e in expressions) {
                calculator.setExpression({ id: e, latex: expressions[e], color: colors[e] })
            }
        }
    });

    // See https://github.com/Polymer/polymer/issues/1381
    window.addEventListener('WebComponentsReady', function () {
        load();
    });

    var timer;
    window.addEventListener('resize', function (event) {
        clearTimeout(timer);
        timer = setTimeout(function () {
            loadDesmos();
        }, 100);
    });

</script>

</body>
</html>
