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

$ws = new wsc;
$ws->dir = '../source/';
$force = TRUE; /* Força gravação, se já existe dados */

#require("inport_stopwords.php");
#require("inport_simbolos.php");
#require("inport_meses.php");
#require("inport_adjetivos.php");
#require("inport_paises.php");
#require("inport_genere.php");
#require("inport_years.php");
require("inport_meses.php");
exit;

$th = 64; /* Ciencia da Informação */
#require("inport_thesa.php");

$th = 8; /* Instituicoes */
#require("inport_thesa.php");

$th = 373; /* Questions */
//require("inport_thesa.php");




/********************************** UNIVERSIDADE */
if (1 == 2) {
    $file = 'universidades.csv';
    $handle = fopen($file, "r");
    if ($handle) {
        while (($line = fgets($handle)) !== false) {
            $line = utf8_encode($line);
            echo $line . '<hr>';
            $ln = splitx(';', $line);
            $class = 'University';
            $dt = array();
            $dt['prefLabel'] = $ln[2];
            $dt['hasPlaceState'] = 'brapci:' . $ws->trata($ln[1]);
            $dt['hasPlaceRegion'] = 'brapci:' . $ws->trata($ln[0]);
            $dt['hasAcronyms'] = $ln[3];
            $dt['hasFundation'] = $ln[4];
            $dt['Class'] = $class;
            $name = $ws->trata($ln[2]);
            $ws->save($dt, $name);

            /* ANACRONICO */
            $name = ascii($ln[3]);
            $class = 'UniversityAcronyms';
            $dt = array();
            $dt['prefLabel'] = $ln[3];
            $dt['hasAbbreviationsOf'] = 'brapci:' . $ws->trata($ln[2]);
            $dt['Class'] = $class;
            echo '<pre>';
            echo '<h1>' . $name . '</h1>';
            print_r($dt);
            $ws->save($dt, $name);
        }
        fclose($handle);
    }
}
exit;


/* Continente */
$dt = array();
$dt['prefLabel'] = 'Brazil@en';
$dt['prefLabel'] = 'América do Sul@pt_br';
$dt['Class'] = 'Continent';
$name = 'america_do_sul';
$ws->save($dt, $name, '../');

/* Pais */
$dt = array();
$dt['prefLabel'] = 'Brazil@en';
$dt['prefLabel'] = 'Brasil@pt_br';
$dt['skos:broader'] = 'brapci:america_do_sul';
$dt['Class'] = 'Country';
$name = 'brasil';
$ws->save($dt, $name);

/* Pais */
$dt = array();
$dt['prefLabel'] = 'Espirito Santo@pt_br';
$dt['skos:broader'] = 'brapci:brasil';
$dt['Class'] = 'State';
$name = 'espirito_santo';
$ws->save($dt, $name);

echo '<h1>Inport</h1>';
$file = 'cidade-es.csv';
$handle = fopen($file, "r");
if ($handle) {
    while (($line = fgets($handle)) !== false) {
        $line = utf8_encode($line);
        $ln = splitx(';', $line);
        $name = ascii($ln[0]);

        $class = 'City';
        $dt = array();
        $dt['prefLabel'] = $ln[0];
        $dt['hasPlaceState'] = 'brapci:espirito_santo';
        $dt['hasCodeIBGE'] = $ln[1];
        $dt['Class'] = 'City';
        $name = $ln[0];
        $name = ascii($name);
        $name = strtolower($name);
        $name = troca($name, ' ', '_');
        $force_save = 1;
        $ws->save($dt, $name, $force_save);
    }

    fclose($handle);
} else {
    // error opening the file.
}
