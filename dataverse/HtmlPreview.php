<!DOCTYPE html>
<html >
<head>
  <meta charset="UTF-8">
  <title>:: Dataverse :: VIEW ::</title>
  <link href="https://fonts.googleapis.com/css?family=Open+Sans+Condensed:300|Titillium+Web" rel="stylesheet">
  <link rel="stylesheet" href="https://www.ufrgs.br/redd/css/bootstrap.css#v4.4.1">
  <link rel="stylesheet" href="https://www.ufrgs.br/redd/css/style.css">
  
  <script type="text/javascript" src="https://www.ufrgs.br/redd/js/jquery-3.1.1.js"></script>
  <script type="text/javascript" src="https://www.ufrgs.br/redd/js/bootstrap.js#v4.4.1"></script>
</head><!--- content--->
<?php
header('Content-Type: text/html; charset=utf-8');

$siteUrl = $_GET['siteUrl'];
$fileid = $_GET['fileid'];
$datasetid = $_GET['datasetid'];
$apiKey  = $_GET['key'];
$datasetid = $_GET['datasetid'];
$locale = $_GET['locale'];
$siteUrl = 'https://cedapdados.ufrgs.br';
$fileUrl = $siteUrl.'/api/access/datafile/'.$fileid.'?gbrecs=true';
$sx = file_get_contents($fileUrl);
//echo '<h1>HTML - '.$fileUrl.'</h1>';
//echo $sx;
//echo '<hr>';

$row = 1;
echo '<table>';
if (($handle = fopen($fileUrl, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, "\t")) !== FALSE) {
        $num = count($data);
        if ($row < 100)
        {
            echo '<tr>';
            echo '<td>'.$row.'</td>';
            $row++;
            for ($c=0; $c < $num; $c++) {
                echo '<td>';
                echo utf8_decode($data[$c]);
                echo '</td>';
            }
        }
        echo '</tr>';
    }
    fclose($handle);
}
echo '</table>';
