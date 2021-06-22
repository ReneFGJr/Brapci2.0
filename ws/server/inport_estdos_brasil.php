<?php
/********************************** Ciencia da Informação - thesa */
$class = 'StateCountry';
echo '<h1>'.$class.'</h1>'.cr();
$file2 = '../.csv/estados_brasil_pt.csv';

$handle = fopen($file2, "r");
$row = 0;

if ($handle) {
    while (($line = fgets($handle)) !== false) {
        $line = troca($line,'"','');
        $line = troca($line,';',',');
        //$line = utf8_decode($line);
        //$line = utf8_decode($line);
        $fd = splitx(',',$line);

        if ($row == 0)
            {
                $hd = array();
                for ($r=1;$r < count($fd);$r++)
                    {
                        $hd[$r] = $fd[$r];
                    }
            } else {                
                $dt = array();
                $name = $ws->trata($fd[0]);
                $dt['skos:prefLabel'] = trim($fd[0]);
                $dt['Class'] = $class;               
                for ($r=1;$r < count($fd);$r++)
                    {
                        $dt[$hd[$r]] = $fd[$r];
                    }
                echo '<br>=>'.$name;
                echo '<pre>';
                print_r($dt);
                echo '</pre>';
                $ws->save($dt, $name,$force);
                
            }
            $row++;
    }
    fclose($handle);
} else {
    echo "Erro ao ler o arquivo ".$file2;
}
echo "Importado ".$row." linhas ";
