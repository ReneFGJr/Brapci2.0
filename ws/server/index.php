<?php
$version = '0.1';
$attr = $argv;
define("BASEPATH", "LOCAL");

if (isset($_SERVER['HTTP_ACCEPT']))
{ $web = TRUE; } else { $web = FALSE; }

require("ws_class_helper.php");
require("../../application/helpers/form_sisdoc_helper.php");

if (isset($attr[1])) {
    $verb = $attr[1];
} else {
    $verb = 'Verb: ?';
}

switch ($verb) {
    case 'source':
        $ws = new wsc;
        echo $ws->source();
        break;
    case 'csv':
        $ws = new wsc;
        echo $ws->readcsv();
        break;        
    case 'v':
        $ws = new wsc;
        echo $ws->terms($attr[2]);
        break;
    default:
        help();
        echo $verb;
}


function help()
{
    global $version, $web;
    $webs = 'API';
    if ($web) { echo '<pre>'; $webs = 'Browser'; }
    echo "HELP - WEBSEMANTIC" . cr();
    echo 'Version: ' . $version.cr();
    echo 'Cliente: '.$webs.cr();
}
