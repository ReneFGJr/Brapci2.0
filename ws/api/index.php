<?php
$version = '0.1';
$attr = $argv;
define("BASEPATH", "LOCAL");

require("../server/ws_class_helper.php");
require("../../application/helpers/form_sisdoc_helper.php");

if (isset($_SERVER['HTTP_ACCEPT']))
{ $web = TRUE; } else { $web = FALSE; }

$verb=$_GET["verb"];
$token = $_GET["token"];
$q = $_GET["q"];

switch($verb)
    {
        case 'genere':
        $ws = new wsc;  
        $ws->dir = '../'.$ws->dir;      
        $rst = $ws->genere($q);
        break;

        case 'lattes':
        $ws = new wsc;  
        $ws->dir = '../'.$ws->dir;      
        $rst = $ws->lattesXML($q);
        break;        

        default: 
        $rst = array();
        $rst['erro'] = 'verb not informed';
        break;
    }
$rst['date'] = date("Y-m-d").'T'.date("H:i:s");
$rst['verb'] = $verb;
$web = false;
if ($web == TRUE)
    {
        echo '<h2>Browser version</h2>';
        echo '<pre>';
        print_r($rst);
        echo '</pre>';
        echo '<div><h4>Json</h4>';
        echo json_encode($rst);
        echo '</div>';
    } else {
        echo json_encode($rst);
    }
