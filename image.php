<?php
$args = $_GET;
$file = '';
foreach($args as $file=>$name)
    {
        switch($file)
            {
                case 'image':
                    $file = $name;
                    break;
                case 'file':
                    $file = $name;
                    break;
            }
    }
if ($file != '')
    {
        $dir = '/var/www/html/temp/';
        $file = $dir.$file;
    } else {
        $file = 'img/no_image.png';
    }

    if (file_exists($file))
    {
        header('Content-type:image/png');
        readfile($file);
    } else {
        echo "ERRO NA CARGA DO ARQUIVO";
        echo '<br>'.$file;
    }
    