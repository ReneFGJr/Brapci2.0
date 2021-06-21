<?php
$version = '0.1';
$attr = $argv;
define("BASEPATH", "LOCAL");

if (isset($_SERVER['HTTP_ACCEPT'])) {
    $web = TRUE;
} else {
    $web = FALSE;
}

require("ws_class_helper.php");
require("../../application/helpers/form_sisdoc_helper.php");

$dir = '../indexes';
$acro = array();
$acro['ago.'] = 'Agosto';
$acro['v.'] = 'Volume';
$acro['n.'] = 'Numero';
$acro['created'] = date("Y-m-d") . 'T' . date("H:i:s");
$json = json_encode($acro);
file_put_contents($dir.'/acronico.json', $json);

check_dir($dir);
exit;
$ws = new wsc;
$ws->dir = '../source/';
$force = TRUE; /* Força gravação, se já existe dados */

$d = dir($ws->dir);
echo "Handle: " . $d->handle . "\n";
echo "Caminho: " . $d->path . "\n";
$wd = array();
while (false !== ($dir = $d->read())) {
    if ((!is_dir($dir)) and (!is_file($dir)))
    {
        /**************** Diretorio */
        $sd = dir($ws->dir.'/'.$dir);
        while (false !== ($sdir = $sd->read())) {
            if (($sdir != '..') and ($sdir != '.'))
            {
            array_push($wd,strzero(strlen($sdir),3).'-'.$sdir);
            }
        }
        $sd->close();
    }
}
$d->close();
arsort($wd);
echo '<pre>';
print_r($wd);
exit;

