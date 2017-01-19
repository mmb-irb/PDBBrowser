<?php

function headerDBW($title) {
    return "<html>
<head>
<title>$title</title>
<link rel=\"stylesheet\" type=\"text/css\" href=\"estil.css\">
        <link rel=\"stylesheet\" href=\"DataTable/jquery.dataTables.min.css\"/>
        <script type=\"text/javascript\" src=\"DataTable/jquery-2.2.0.min.js\"></script>
        <script type=\"text/javascript\" src=\"DataTable/jquery.dataTables.min.js\"></script>

</head>
<body bgcolor=\"#ffffff\">
<h1>DBW - $title</h1>
";
}

function footerDBW() {
    return '
</body>
</html>';
}

function errorPage($title, $text) {
    return headerDBW($title) . $text . footerDBW();
}