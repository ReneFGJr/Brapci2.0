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
$ddiUrl = $siteUrl.'/api/datasets/export?exporter=ddi&persistentId='.(1).'';
$sx = file_get_contents($fileUrl);
/*
echo '<h1>HTML - '.$fileUrl.'</h1>';
echo '<pre>';
print_r($_GET);
print_r($_SERVER);
//http://cedapdados.ufrgs.br/api/datasets/export?exporter=ddi&persistentId=hdl%3A20.500.11959/CedapDados/3
//echo $sx;
//http://cedapdados.ufrgs.br/api/files/17/metadata
//{"label":"country_portugues_brasileiro.tab","directoryLabel":"paises/csv","description":"Lista de paises em portugues","restricted":false,"id":17}
echo '<hr>';
exit;
*/

$row = 0;
$cols = array();

echo '<div style="position: fixed; right:0px; text-align: right;"><img src="img/logo_cedapdados.png" align="right"></div>';
echo '<table class="table" width="100%">';
if (($handle = fopen($fileUrl, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, "\t")) !== FALSE) {
        $num = count($data);
        if ($row < 100)
        {
  	if ($row ==0)
	{
               echo '<tr>';
               echo '<th>&nbsp;</th>';
               for ($c=0; $c < $num; $c++)
                        {
                        echo '<th>';
                        echo $data[$c];
                        echo '</th>';
                        }
                echo '</tr>';
	} else {
	       echo '<tr>';
	       echo '<td>'.$row.'</td>';
	       for ($c=0; $c < $num; $c++)
			{
	                echo '<td>';
	//                echo utf8_decode($data[$c]);
			echo $data[$c];
			$sz = strlen($data[$c]);
			if (isset($cols[$c+1]))
			{
				if ($cols[$c+1] < $sz)
					{ $cols[$c+1] = $sz; }
			} else {
				$cols[$c+1] = $sz;
			}
	                echo '</td>';
			}
	        }
        	echo '</tr>';
	}
	$row++;
    }
    fclose($handle);
}

$tot = 0;
foreach($cols as $cs=>$size)
	{
		$tot = $tot + $size;
	}
echo '<tr>';
echo chr(13).chr(10);
echo '<th width="2%">';
foreach($cols as $cs=>$size)
        {
		$sz = sround($size/$tot*100);
		echo '<th width="'.$sz.'%">&nbsp;</th>';
        }
echo '</tr>';
echo chr(13).chr(10);
echo '<tr><th colspan=10>Total de registros '.$row.'</th></tr>';
echo '</table>';
